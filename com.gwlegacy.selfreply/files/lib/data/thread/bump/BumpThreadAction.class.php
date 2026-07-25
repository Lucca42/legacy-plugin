<?php

namespace wcf\data\thread\bump;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\gw\BumpSchedule;
use wcf\system\WCF;

/**
 * Bumps a thread by refreshing its last activity, at most once per the board's
 * configured interval. Restricted to the thread author on an enabled board.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class BumpThreadAction extends AbstractDatabaseObjectAction
{
    /**
     * The action constructor resolves the decorator's base class, so this must be the
     * editor class rather than the plain database object.
     * @var string
     */
    protected $className = 'wbb\\data\\thread\\ThreadEditor';

    /**
     * @var array
     */
    private array $bumpData = [];

    public function validateBump(): void
    {
        $userID = WCF::getUser()->userID;
        if (!$userID) {
            throw new PermissionDeniedException();
        }

        $threadID = (int)($this->objectIDs[0] ?? 0);
        if (!$threadID) {
            throw new IllegalLinkException();
        }

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

        if (!$row) {
            throw new IllegalLinkException();
        }
        if (!(int)$row['gwSelfReplyRestricted']) {
            throw new PermissionDeniedException();
        }
        if ((int)$row['userID'] !== $userID) {
            throw new PermissionDeniedException();
        }

        $interval = (int)$row['gwBumpInterval'] ?: 86400;
        $reference = BumpSchedule::lastResurfaceTime($threadID, $userID, (int)$row['gwLastBumpTime']);
        if (TIME_NOW < $reference + $interval) {
            throw new UserInputException(
                'text',
                'You can only bump or update your own thread once every ' . BumpSchedule::describeInterval($interval) . '.'
            );
        }

        $this->bumpData = [
            'threadID' => $threadID,
            'userID' => $userID,
            'username' => WCF::getUser()->username,
            'interval' => $interval,
        ];
    }

    /**
     * @return array{time: int, availableTime: int}
     */
    public function bump(): array
    {
        $sql = "UPDATE  wbb1_thread
                SET     lastPostTime = ?,
                        lastPosterID = ?,
                        lastPoster = ?,
                        gwLastBumpTime = ?
                WHERE   threadID = ?";
        WCF::getDB()->prepare($sql)->execute([
            TIME_NOW,
            $this->bumpData['userID'],
            $this->bumpData['username'],
            TIME_NOW,
            $this->bumpData['threadID'],
        ]);

        return [
            'time' => TIME_NOW,
            'availableTime' => TIME_NOW + ($this->bumpData['interval'] ?? 86400),
        ];
    }
}
