<?php

namespace wcf\system\event\listener;

use wcf\system\gwupvote\RenderGuard;
use wcf\system\WCF;

/**
 * Assigns the upvote/thanks template data when a reply is rendered through the quick
 * reply (an AJAX insert that does not go through ThreadPage), so the buttons appear on
 * the new post without a page reload. The maps are empty because the post is brand new.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class GwUpvoteQuickReplyListener implements IParameterizedEventListener
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

        $sql = "SELECT      thread.userID AS threadUserID,
                            board.gwUpvoteEnabled
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
            'gwUpvoteBoardEnabled' => (int)$row['gwUpvoteEnabled'],
            'gwUpvoteFirstPostID' => $this->getFirstPostID($threadID),
            'gwUpvoteThreadAuthor' => (int)$row['threadUserID'],
            'gwUpvoteCount' => [],
            'gwUpvoteMine' => [],
            'gwThanked' => [],
            'gwUpvoteGuard' => new RenderGuard(),
            'gwThanksGuard' => new RenderGuard(),
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
