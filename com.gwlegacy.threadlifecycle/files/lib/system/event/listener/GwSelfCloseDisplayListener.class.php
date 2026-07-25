<?php

namespace wcf\system\event\listener;

use wcf\system\WCF;

/**
 * Exposes the self-close button state to the thread template. The button is shown to the
 * thread author on an enabled board, when self-close is configured, the thread is still
 * open, and it has reached the minimum age.
 *
 *   {$gwCanSelfClose}       the author may close the thread now
 *   {$gwSelfCloseThreadID}  thread id for the request
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class GwSelfCloseDisplayListener implements IParameterizedEventListener
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

        if ((int)$thread->userID !== $userID || (int)$thread->isClosed) {
            return;
        }

        $board = $thread->getBoard();
        if (!$board || !(int)$board->gwLifecycleEnabled) {
            return;
        }

        $delay = (int)$board->gwSelfCloseDelay;
        if ($delay <= 0 || TIME_NOW < (int)$thread->time + $delay) {
            return;
        }

        WCF::getTPL()->assign([
            'gwCanSelfClose' => true,
            'gwSelfCloseThreadID' => (int)$thread->threadID,
        ]);
    }
}
