<?php

namespace wcf\system\event\listener;

use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * Blocks editing of a thread's first post on boards where the lock is enabled, for
 * everyone except moderators. Hooked on both the edit start and the edit save of
 * wbb\data\post\PostAction.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class GwLockFirstPostListener implements IParameterizedEventListener
{
    public function execute($eventObj, $className, $eventName, array &$parameters): void
    {
        // Only the edit actions matter (open editor / save edit).
        if (!\method_exists($eventObj, 'getActionName')
            || !\in_array($eventObj->getActionName(), ['beginEdit', 'save'], true)
        ) {
            return;
        }

        // Moderators and admins are never blocked.
        if (WCF::getSession()->getPermission('mod.board.canEditPost')
            || WCF::getSession()->getPermission('admin.general.canUseAcp')
        ) {
            return;
        }

        $postID = (int)($eventObj->objectIDs[0] ?? 0);
        if (!$postID) {
            return;
        }

        $sql = "SELECT      thread.firstPostID,
                            board.gwLockFirstPost
                FROM        wbb1_post post
                INNER JOIN  wbb1_thread thread ON thread.threadID = post.threadID
                INNER JOIN  wbb1_board board ON board.boardID = thread.boardID
                WHERE       post.postID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$postID]);
        $row = $statement->fetchSingleRow();

        if (!$row) {
            return;
        }

        if ((int)$row['gwLockFirstPost'] && (int)$row['firstPostID'] === $postID) {
            throw new PermissionDeniedException();
        }
    }
}
