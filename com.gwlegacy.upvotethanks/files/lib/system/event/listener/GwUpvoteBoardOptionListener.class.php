<?php

namespace wcf\system\event\listener;

use wcf\system\gwupvote\RenderGuard;
use wcf\system\WCF;

/**
 * Adds the per-board options (enable upvotes, lock first post) to the board ACP form and
 * persists them through additionalFields.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class GwUpvoteBoardOptionListener implements IParameterizedEventListener
{
    private int $upvoteEnabled = 0;

    private int $lockFirstPost = 0;

    public function execute($eventObj, $className, $eventName, array &$parameters): void
    {
        switch ($eventName) {
            case 'readFormParameters':
                $this->upvoteEnabled = empty($_POST['gwUpvoteEnabled']) ? 0 : 1;
                $this->lockFirstPost = empty($_POST['gwLockFirstPost']) ? 0 : 1;
                break;

            case 'save':
                $eventObj->additionalFields['gwUpvoteEnabled'] = $this->upvoteEnabled;
                $eventObj->additionalFields['gwLockFirstPost'] = $this->lockFirstPost;
                break;

            case 'readData':
                if (empty($_POST) && isset($eventObj->board) && \is_object($eventObj->board)) {
                    $this->upvoteEnabled = (int)$eventObj->board->gwUpvoteEnabled;
                    $this->lockFirstPost = (int)$eventObj->board->gwLockFirstPost;
                }
                break;

            case 'assignVariables':
                WCF::getTPL()->assign([
                    'gwUpvoteEnabled' => $this->upvoteEnabled,
                    'gwLockFirstPost' => $this->lockFirstPost,
                    'gwUpvoteOptionGuard' => new RenderGuard(),
                ]);
                break;
        }
    }
}
