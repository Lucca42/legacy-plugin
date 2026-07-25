<?php

namespace wcf\system\event\listener;

use wcf\system\gwlifecycle\RenderGuard;
use wcf\system\WCF;

/**
 * Adds the per-board lifecycle settings (enable toggle, self-close delay, auto-close
 * delay) to the board ACP form and persists them through additionalFields.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class GwLifecycleBoardOptionListener implements IParameterizedEventListener
{
    private int $enabled = 0;

    private int $selfCloseDelay = 0;

    private int $autoCloseAfter = 0;

    public function execute($eventObj, $className, $eventName, array &$parameters): void
    {
        switch ($eventName) {
            case 'readFormParameters':
                $this->enabled = empty($_POST['gwLifecycleEnabled']) ? 0 : 1;
                $this->selfCloseDelay = isset($_POST['gwSelfCloseDelay']) ? \max(0, (int)$_POST['gwSelfCloseDelay']) : 0;
                $this->autoCloseAfter = isset($_POST['gwAutoCloseAfter']) ? \max(0, (int)$_POST['gwAutoCloseAfter']) : 0;
                break;

            case 'save':
                $eventObj->additionalFields['gwLifecycleEnabled'] = $this->enabled;
                $eventObj->additionalFields['gwSelfCloseDelay'] = $this->selfCloseDelay;
                $eventObj->additionalFields['gwAutoCloseAfter'] = $this->autoCloseAfter;
                break;

            case 'readData':
                if (empty($_POST) && isset($eventObj->board) && \is_object($eventObj->board)) {
                    $this->enabled = (int)$eventObj->board->gwLifecycleEnabled;
                    $this->selfCloseDelay = (int)$eventObj->board->gwSelfCloseDelay;
                    $this->autoCloseAfter = (int)$eventObj->board->gwAutoCloseAfter;
                }
                break;

            case 'assignVariables':
                WCF::getTPL()->assign([
                    'gwLifecycleEnabled' => $this->enabled,
                    'gwSelfCloseDelay' => $this->selfCloseDelay,
                    'gwAutoCloseAfter' => $this->autoCloseAfter,
                    'gwLifecycleGuard' => new RenderGuard(),
                ]);
                break;
        }
    }
}
