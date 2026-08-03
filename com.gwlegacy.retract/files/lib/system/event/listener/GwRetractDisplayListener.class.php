<?php

namespace wcf\system\event\listener;

use wcf\system\gwretract\RenderGuard;
use wcf\system\WCF;

/**
 * Exposes the retract UI state to the post template.
 *
 *   {$gwRetractBoardEnabled}  the retract button is enabled on this board
 *   {$gwRetractFirstPostID}   first post of the thread (cannot be retracted)
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

        WCF::getTPL()->assign([
            'gwRetractBoardEnabled' => $board && (int)$board->gwRetractEnabled,
            'gwRetractFirstPostID' => (int)$thread->firstPostID,
            'gwRetractGuard' => new RenderGuard(),
            'gwConfirmReplyLock' => $confirmReplyLock,
        ]);
    }
}
