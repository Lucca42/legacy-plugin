<?php

namespace wcf\system\event\listener;

use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * Blocks editing of replies (every post except the first) on boards where reply locking
 * is enabled, for everyone except moderators. Hooked on both the edit start and the edit
 * save of wbb\data\post\PostAction.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class GwLockRepliesListener implements IParameterizedEventListener
{
    public function execute($eventObj, $className, $eventName, array &$parameters): void
    {
        // Only the edit actions matter (open editor / save edit).
        if (!\method_exists($eventObj, 'getActionName')
            || !\in_array($eventObj->getActionName(), ['beginEdit', 'save'], true)
        ) {
            return;
        }

        if (WCF::getSession()->getPermission('mod.board.canEditPost')
            || WCF::getSession()->getPermission('admin.general.canUseAcp')
        ) {
            return;
        }

        // Inline editing passes the post id as parameters['objectID'], not objectIDs.
        $params = \method_exists($eventObj, 'getParameters') ? $eventObj->getParameters() : [];
        $postID = (int)($params['objectID'] ?? ($eventObj->objectIDs[0] ?? 0));
        if (!$postID) {
            return;
        }

        $sql = "SELECT      post.threadID,
                            board.gwLockReplies
                FROM        wbb1_post post
                INNER JOIN  wbb1_thread thread ON thread.threadID = post.threadID
                INNER JOIN  wbb1_board board ON board.boardID = thread.boardID
                WHERE       post.postID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$postID]);
        $row = $statement->fetchSingleRow();

        if (!$row || !(int)$row['gwLockReplies']) {
            return;
        }

        // Replies only (the first post is handled by the upvote plugin, if at all).
        if ($this->getFirstPostID((int)$row['threadID']) !== $postID) {
            throw new PermissionDeniedException();
        }
    }

    private function getFirstPostID(int $threadID): int
    {
        $sql = "SELECT MIN(postID) FROM wbb1_post WHERE threadID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$threadID]);

        return (int)$statement->fetchSingleColumn();
    }
}
