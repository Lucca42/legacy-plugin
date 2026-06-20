<?php

namespace wcf\system\event\listener;

use wcf\system\gw\RenderGuard;
use wcf\system\WCF;

/**
 * Adds the per-board settings (enable toggle + bump interval) to the board ACP form and
 * persists them through the standard additionalFields mechanism.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class GwBoardSelfReplyOptionListener implements IParameterizedEventListener
{
    /**
     * @var int
     */
    private int $restricted = 0;

    /**
     * @var int
     */
    private int $interval = 86400;

    public function execute($eventObj, $className, $eventName, array &$parameters): void
    {
        switch ($eventName) {
            case 'readFormParameters':
                $this->restricted = empty($_POST['gwSelfReplyRestricted']) ? 0 : 1;
                $this->interval = isset($_POST['gwBumpInterval'])
                    ? \max(60, (int)$_POST['gwBumpInterval'])
                    : 86400;
                break;

            case 'save':
                $eventObj->additionalFields['gwSelfReplyRestricted'] = $this->restricted;
                $eventObj->additionalFields['gwBumpInterval'] = $this->interval;
                break;

            case 'readData':
                if (empty($_POST) && isset($eventObj->board) && \is_object($eventObj->board)) {
                    $this->restricted = (int)$eventObj->board->gwSelfReplyRestricted;
                    $this->interval = (int)$eventObj->board->gwBumpInterval ?: 86400;
                }
                break;

            case 'assignVariables':
                WCF::getTPL()->assign([
                    'gwSelfReplyRestricted' => $this->restricted,
                    'gwBumpInterval' => $this->interval,
                    'gwBoardSettingsGuard' => new RenderGuard(),
                ]);
                break;
        }
    }
}
