<?php

namespace wcf\system\event\listener;

use wcf\system\gwretract\RenderGuard;
use wcf\system\WCF;

/**
 * Assigns the retract template data when a reply is rendered through the quick reply
 * (an AJAX insert that does not go through ThreadPage), so the retract button appears on
 * the new post without a page reload.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class GwRetractQuickReplyListener implements IParameterizedEventListener
{
    public function execute($eventObj, $className, $eventName, array &$parameters): void
    {
        $message = $parameters['message'] ?? null;
        if (!\is_object($message) || \strpos(\get_class($message), 'wbb\\data\\post\\') !== 0) {
            return;
        }

        $threadID = (int)$message->threadID;
        if (!$threadID) {
            return;
        }

        $sql = "SELECT board.gwRetractEnabled
                FROM        wbb1_thread thread
                INNER JOIN  wbb1_board board ON board.boardID = thread.boardID
                WHERE       thread.threadID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$threadID]);
        $row = $statement->fetchSingleRow();
        if (!$row) {
            return;
        }

        WCF::getTPL()->assign([
            'gwRetractBoardEnabled' => (int)$row['gwRetractEnabled'],
            'gwRetractFirstPostID' => $this->getFirstPostID($threadID),
            'gwRetractPosts' => [],
            'gwRetractGuard' => new RenderGuard(),
        ]);
    }

    private function getFirstPostID(int $threadID): int
    {
        $sql = "SELECT MIN(postID) FROM wbb1_post WHERE threadID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$threadID]);

        return (int)$statement->fetchSingleColumn();
    }
}
