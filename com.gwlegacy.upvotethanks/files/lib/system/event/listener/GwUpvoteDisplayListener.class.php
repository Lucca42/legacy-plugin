<?php

namespace wcf\system\event\listener;

use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\gwupvote\RenderGuard;
use wcf\system\WCF;

/**
 * Preloads the upvote data for the posts on the current thread page and exposes it to
 * the post template.
 *
 *   {$gwUpvoteBoardEnabled}  upvotes are enabled on this board
 *   {$gwUpvoteFirstPostID}   first post of the thread (excluded from upvoting)
 *   {$gwUpvoteThreadAuthor}  thread author user id (may not upvote)
 *   {$gwUpvoteCount}         map postID => upvote count
 *   {$gwUpvoteMine}          map postID => true when the current user upvoted it
 *   {$gwUpvoteGuard}         render-once guard for the shared script
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class GwUpvoteDisplayListener implements IParameterizedEventListener
{
    public function execute($eventObj, $className, $eventName, array &$parameters): void
    {
        if (!isset($eventObj->thread) || !\is_object($eventObj->thread)) {
            return;
        }
        $thread = $eventObj->thread;

        $board = $thread->getBoard();
        $enabled = $board && (int)$board->gwUpvoteEnabled;

        $postIDs = [];
        if ($enabled && isset($eventObj->objectList) && \is_object($eventObj->objectList)) {
            $postIDs = \array_map('intval', $eventObj->objectList->getObjectIDs());
        }

        $counts = [];
        $mine = [];
        if (!empty($postIDs)) {
            $counts = $this->loadCounts($postIDs);
            $userID = WCF::getUser()->userID;
            if ($userID) {
                $mine = $this->loadMine($postIDs, $userID);
            }
        }

        WCF::getTPL()->assign([
            'gwUpvoteBoardEnabled' => $enabled,
            'gwUpvoteFirstPostID' => (int)$thread->firstPostID,
            'gwUpvoteThreadAuthor' => (int)$thread->userID,
            'gwUpvoteCount' => $counts,
            'gwUpvoteMine' => $mine,
            'gwUpvoteGuard' => new RenderGuard(),
        ]);
    }

    /**
     * @param int[] $postIDs
     * @return array<int, int>
     */
    private function loadCounts(array $postIDs): array
    {
        $conditions = new PreparedStatementConditionBuilder();
        $conditions->add('postID IN (?)', [$postIDs]);

        $sql = "SELECT      postID, COUNT(*) AS upvotes
                FROM        wcf1_gw_post_upvote
                " . $conditions . "
                GROUP BY    postID";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditions->getParameters());

        $counts = [];
        while ($row = $statement->fetchArray()) {
            $counts[(int)$row['postID']] = (int)$row['upvotes'];
        }

        return $counts;
    }

    /**
     * @param int[] $postIDs
     * @return array<int, bool>
     */
    private function loadMine(array $postIDs, int $userID): array
    {
        $conditions = new PreparedStatementConditionBuilder();
        $conditions->add('postID IN (?)', [$postIDs]);
        $conditions->add('userID = ?', [$userID]);

        $sql = "SELECT postID FROM wcf1_gw_post_upvote " . $conditions;
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditions->getParameters());

        $mine = [];
        while ($row = $statement->fetchArray()) {
            $mine[(int)$row['postID']] = true;
        }

        return $mine;
    }
}
