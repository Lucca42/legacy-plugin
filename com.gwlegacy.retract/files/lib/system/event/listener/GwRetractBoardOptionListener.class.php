<?php

namespace wcf\system\event\listener;

use wcf\system\gwretract\RenderGuard;
use wcf\system\WCF;

/**
 * Adds the per-board options (enable retract, lock replies) to the board ACP form and
 * persists them through additionalFields.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class GwRetractBoardOptionListener implements IParameterizedEventListener
{
    private int $retractEnabled = 0;

    private int $lockReplies = 0;

    public function execute($eventObj, $className, $eventName, array &$parameters): void
    {
        switch ($eventName) {
            case 'readFormParameters':
                $this->retractEnabled = empty($_POST['gwRetractEnabled']) ? 0 : 1;
                $this->lockReplies = empty($_POST['gwLockReplies']) ? 0 : 1;
                break;

            case 'save':
                $eventObj->additionalFields['gwRetractEnabled'] = $this->retractEnabled;
                $eventObj->additionalFields['gwLockReplies'] = $this->lockReplies;
                break;

            case 'readData':
                if (empty($_POST) && isset($eventObj->board) && \is_object($eventObj->board)) {
                    $this->retractEnabled = (int)$eventObj->board->gwRetractEnabled;
                    $this->lockReplies = (int)$eventObj->board->gwLockReplies;
                }
                break;

            case 'assignVariables':
                WCF::getTPL()->assign([
                    'gwRetractEnabled' => $this->retractEnabled,
                    'gwLockReplies' => $this->lockReplies,
                    'gwRetractOptionGuard' => new RenderGuard(),
                ]);
                break;
        }
    }
}
