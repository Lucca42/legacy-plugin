<?php

namespace wcf\system\event\listener;

use wcf\system\WCF;

/**
 * Exposes the bump button state to the thread template.
 *
 *   {$gwCanBump}            the author may bump right now
 *   {$gwBumpThreadID}       thread id for the request
 *   {$gwBumpAvailableTime}  timestamp of the next allowed bump (0 when available)
 *
 * The button is shown only to the thread author on an enabled board, and is disabled
 * until the board's interval has elapsed since the last bump or reply.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class GwThreadBumpDisplayListener implements IParameterizedEventListener
{
    public function execute($eventObj, $className, $eventName, array &$parameters): void
    {
        $userID = WCF::getUser()->userID;
        if (!$userID) {
            return;
        }

        if (!isset($eventObj->thread) || !\is_object($eventObj->thread)) {
            return;
        }
        $thread = $eventObj->thread;

        if ((int)$thread->userID !== $userID) {
            return;
        }

        $board = $thread->getBoard();
        if (!$board || !(int)$board->gwSelfReplyRestricted) {
            return;
        }

        $interval = (int)$board->gwBumpInterval ?: 86400;
        $reference = \max((int)$thread->gwLastBumpTime, (int)$thread->lastPostTime);
        $availableTime = $reference + $interval;
        $canBump = TIME_NOW >= $availableTime;

        WCF::getTPL()->assign([
            'gwCanBump' => $canBump,
            'gwBumpThreadID' => (int)$thread->threadID,
            'gwBumpAvailableTime' => $canBump ? 0 : $availableTime,
        ]);
    }
}
