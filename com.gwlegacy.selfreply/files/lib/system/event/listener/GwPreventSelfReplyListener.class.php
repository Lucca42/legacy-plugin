<?php

namespace wcf\system\event\listener;

use wcf\system\exception\UserInputException;
use wcf\system\WCF;

/**
 * Prevents a thread author from replying to their own thread on an enabled board.
 *
 * Hooked on the post action's validation, which covers both the quick reply
 * ("quickReply") and the full reply form ("create"). The reply UI is hidden via a
 * template listener; this is the server-side enforcement.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class GwPreventSelfReplyListener implements IParameterizedEventListener
{
    public function execute($eventObj, $className, $eventName, array &$parameters): void
    {
        // The rule targets the thread author regardless of rank; a moderator replying to
        // someone else's thread is not the author and is therefore never affected.
        $userID = WCF::getUser()->userID;
        if (!$userID) {
            return;
        }

        $threadID = $this->resolveThreadID($eventObj);
        if (!$threadID) {
            return;
        }

        if ($this->isOwnRestrictedThread($threadID, $userID)) {
            throw new UserInputException(
                'text',
                'You cannot reply to your own thread here. Use the "Bump" button to push it back up.'
            );
        }
    }

    private function resolveThreadID($eventObj): ?int
    {
        if (!\method_exists($eventObj, 'getActionName')) {
            return null;
        }
        if (!\in_array($eventObj->getActionName(), ['create', 'quickReply'], true)) {
            return null;
        }

        $params = $eventObj->getParameters();

        // The quick reply passes the target thread as the container objectID.
        if (!empty($params['objectID'])) {
            return (int)$params['objectID'];
        }
        if (isset($params['data']['threadID'])) {
            return (int)$params['data']['threadID'];
        }
        if (isset($params['thread']) && \is_object($params['thread'])) {
            return (int)$params['thread']->threadID;
        }
        if (isset($params['threadID'])) {
            return (int)$params['threadID'];
        }

        return null;
    }

    private function isOwnRestrictedThread(int $threadID, int $userID): bool
    {
        $sql = "SELECT      thread.userID,
                            board.gwSelfReplyRestricted
                FROM        wbb1_thread thread
                INNER JOIN  wbb1_board board ON board.boardID = thread.boardID
                WHERE       thread.threadID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$threadID]);
        $row = $statement->fetchSingleRow();

        if (!$row) {
            return false;
        }

        return (int)$row['gwSelfReplyRestricted'] === 1
            && (int)$row['userID'] === $userID;
    }
}
