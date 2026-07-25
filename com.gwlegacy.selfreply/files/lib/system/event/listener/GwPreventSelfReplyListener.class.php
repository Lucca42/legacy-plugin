<?php

namespace wcf\system\event\listener;

use wcf\system\exception\UserInputException;
use wcf\system\gw\BumpSchedule;
use wcf\system\WCF;

/**
 * Rate-limits a thread author's own replies on an enabled board: the author may post an
 * update to their thread, but at most once per the board's interval. The reply and the
 * bump button share that interval, so an author cannot post and then bump on top of it.
 *
 * Hooked on the post action's validation, which covers both the quick reply
 * ("quickReply") and the full reply form ("create").
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class GwPreventSelfReplyListener implements IParameterizedEventListener
{
    public function execute($eventObj, $className, $eventName, array &$parameters): void
    {
        // The rule targets the thread author; a member replying to someone else's thread
        // is not the author and is never affected.
        $userID = WCF::getUser()->userID;
        if (!$userID) {
            return;
        }

        $threadID = $this->resolveThreadID($eventObj);
        if (!$threadID) {
            return;
        }

        $thread = $this->getRestrictedThread($threadID, $userID);
        if ($thread === null) {
            return;
        }

        $interval = (int)$thread['gwBumpInterval'] ?: 86400;
        $lastResurface = BumpSchedule::lastResurfaceTime($threadID, $userID, (int)$thread['gwLastBumpTime']);
        if (TIME_NOW < $lastResurface + $interval) {
            throw new UserInputException(
                'text',
                'You can only bump or update your own thread once every ' . BumpSchedule::describeInterval($interval)
                    . '. Please try again later, or use the "Bump" button.'
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

    /**
     * Returns [gwLastBumpTime, gwBumpInterval] when the thread is on an enabled board and
     * owned by $userID, otherwise null (the reply is then left untouched).
     */
    private function getRestrictedThread(int $threadID, int $userID): ?array
    {
        $sql = "SELECT      thread.userID,
                            thread.gwLastBumpTime,
                            board.gwSelfReplyRestricted,
                            board.gwBumpInterval
                FROM        wbb1_thread thread
                INNER JOIN  wbb1_board board ON board.boardID = thread.boardID
                WHERE       thread.threadID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$threadID]);
        $row = $statement->fetchSingleRow();

        if (!$row || !(int)$row['gwSelfReplyRestricted'] || (int)$row['userID'] !== $userID) {
            return null;
        }

        return $row;
    }
}
