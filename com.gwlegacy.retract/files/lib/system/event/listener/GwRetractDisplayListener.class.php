<?php

namespace wcf\system\event\listener;

use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\gwretract\RenderGuard;
use wcf\system\WCF;

/**
 * Exposes the retract UI state to the post template.
 *
 *   {$gwRetractBoardEnabled}  the retract button is enabled on this board
 *   {$gwRetractFirstPostID}   first post of the thread (cannot be retracted)
 *   {$gwRetractPosts}         map postID => true for posts that are themselves retracts
 *   {$gwRetractGuard}         render-once guard for the shared script
 *   {$gwConfirmReplyLock}     warn on reply submit that replies cannot be edited
 *
 * The button itself is shown per post to the reply's own author only (checked in the
 * template against the post author).
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class GwRetractDisplayListener implements IParameterizedEventListener
{
    public function execute($eventObj, $className, $eventName, array &$parameters): void
    {
        if (!isset($eventObj->thread) || !\is_object($eventObj->thread)) {
            return;
        }
        $thread = $eventObj->thread;

        $board = $thread->getBoard();

        // Warn on reply submit when replies are locked, unless the user may moderate.
        $confirmReplyLock = false;
        if ($board && (int)$board->gwLockReplies
            && !WCF::getSession()->getPermission('mod.board.canEditPost')
            && !WCF::getSession()->getPermission('admin.general.canUseAcp')
        ) {
            $confirmReplyLock = true;
        }

        $firstPostID = $this->getFirstPostID((int)$thread->threadID);

        $postIDs = [];
        if (isset($eventObj->objectList) && \is_object($eventObj->objectList)) {
            $postIDs = \array_map('intval', $eventObj->objectList->getObjectIDs());
        }

        $retractPosts = [];
        if ($board && (int)$board->gwRetractEnabled && !empty($postIDs)) {
            $retractPosts = $this->loadRetractPosts($postIDs);
        }

        WCF::getTPL()->assign([
            'gwRetractBoardEnabled' => $board && (int)$board->gwRetractEnabled,
            'gwRetractFirstPostID' => $firstPostID,
            'gwRetractPosts' => $retractPosts,
            'gwRetractGuard' => new RenderGuard(),
            'gwConfirmReplyLock' => $confirmReplyLock,
        ]);
    }

    /**
     * @param int[] $postIDs
     * @return array<int, bool>
     */
    private function loadRetractPosts(array $postIDs): array
    {
        if (empty($postIDs)) {
            return [];
        }

        $conditions = new PreparedStatementConditionBuilder();
        $conditions->add('postID IN (?)', [$postIDs]);

        $sql = "SELECT postID FROM wcf1_gw_retract_post " . $conditions;
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditions->getParameters());

        $map = [];
        while ($row = $statement->fetchArray()) {
            $map[(int)$row['postID']] = true;
        }

        return $map;
    }

    /**
     * The thread's first post is the lowest post id; reliable even on older threads
     * where the cached firstPostID column is missing.
     */
    private function getFirstPostID(int $threadID): int
    {
        $sql = "SELECT MIN(postID) FROM wbb1_post WHERE threadID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$threadID]);

        return (int)$statement->fetchSingleColumn();
    }
}
