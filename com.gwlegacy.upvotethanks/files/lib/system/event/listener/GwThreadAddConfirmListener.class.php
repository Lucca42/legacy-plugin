<?php

namespace wcf\system\event\listener;

use wcf\system\WCF;

/**
 * Flags the thread-add page for a submit-time confirmation when the target board locks
 * the first post, so the author is warned it will not be editable. Moderators (who can
 * edit anyway) are not warned.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class GwThreadAddConfirmListener implements IParameterizedEventListener
{
    public function execute($eventObj, $className, $eventName, array &$parameters): void
    {
        if (WCF::getSession()->getPermission('mod.board.canEditPost')
            || WCF::getSession()->getPermission('admin.general.canUseAcp')
        ) {
            return;
        }

        if (!isset($eventObj->board) || !\is_object($eventObj->board)) {
            return;
        }

        if ((int)$eventObj->board->gwLockFirstPost) {
            WCF::getTPL()->assign('gwConfirmThreadLock', true);
        }
    }
}
