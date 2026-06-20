<?php

namespace wcf\system\event\listener;

use wcf\system\WCF;

/**
 * Exposes the squash UI state to the thread template.
 *
 *   {$gwSquashThreadID}    thread id
 *   {$gwIsSquashed}        the thread currently has hidden replies
 *   {$gwShowingSquashed}   the hidden replies are currently being shown
 *   {$gwCanSquash}         the current user may squash (author, enabled board)
 *
 * "Show previous replies" is available to everyone; the squash button only to the
 * thread author.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class GwThreadSquashDisplayListener implements IParameterizedEventListener
{
    public function execute($eventObj, $className, $eventName, array &$parameters): void
    {
        if (!isset($eventObj->thread) || !\is_object($eventObj->thread)) {
            return;
        }
        $thread = $eventObj->thread;

        $canSquash = false;
        $userID = WCF::getUser()->userID;
        if ($userID && (int)$thread->userID === $userID) {
            $board = $thread->getBoard();
            if ($board && (int)$board->gwSelfReplyRestricted) {
                $canSquash = true;
            }
        }

        WCF::getTPL()->assign([
            'gwSquashThreadID' => (int)$thread->threadID,
            'gwIsSquashed' => (int)$thread->gwSquashPostID > 0,
            'gwShowingSquashed' => !empty($_GET['showSquashed']),
            'gwCanSquash' => $canSquash,
        ]);
    }
}
