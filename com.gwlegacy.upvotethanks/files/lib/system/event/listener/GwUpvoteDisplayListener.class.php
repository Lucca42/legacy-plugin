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

        $firstPostID = $this->getFirstPostID((int)$thread->threadID);

        $counts = [];
        $mine = [];
        $thanked = [];
        if (!empty($postIDs)) {
            $counts = $this->loadCounts($postIDs);
            $thanked = $this->loadThanked($postIDs);
            $userID = WCF::getUser()->userID;
            if ($userID) {
                $mine = $this->loadMine($postIDs, $userID);
            }
        }

        // Edit button to hide on the locked first post (regular users only).
        $lockedEdit = [];
        if ($board && (int)$board->gwLockFirstPost && $firstPostID
            && !WCF::getSession()->getPermission('mod.board.canEditPost')
            && !WCF::getSession()->getPermission('admin.general.canUseAcp')
        ) {
            $lockedEdit = [$firstPostID];
        }

        WCF::getTPL()->assign([
            'gwUpvoteBoardEnabled' => $enabled,
            'gwUpvoteFirstPostID' => $firstPostID,
            'gwUpvoteThreadAuthor' => (int)$thread->userID,
            'gwUpvoteCount' => $counts,
            'gwUpvoteMine' => $mine,
            'gwUpvoteGuard' => new RenderGuard(),
            'gwUpvoteLockedEdit' => $lockedEdit,
            'gwThanked' => $thanked,
            'gwThanksGuard' => new RenderGuard(),
        ]);
    }

    /**
     * @param int[] $postIDs
     * @return array<int, bool>
     */
    private function loadThanked(array $postIDs): array
    {
        $conditions = new PreparedStatementConditionBuilder();
        $conditions->add('postID IN (?)', [$postIDs]);

        $sql = "SELECT postID FROM wcf1_gw_post_thanks " . $conditions;
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditions->getParameters());

        $thanked = [];
        while ($row = $statement->fetchArray()) {
            $thanked[(int)$row['postID']] = true;
        }

        return $thanked;
    }

    /**
     * The thread's first post is the lowest post id; this is reliable even on older
     * threads where the cached firstPostID column is missing.
     */
    private function getFirstPostID(int $threadID): int
    {
        $sql = "SELECT MIN(postID) FROM wbb1_post WHERE threadID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$threadID]);

        return (int)$statement->fetchSingleColumn();
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
