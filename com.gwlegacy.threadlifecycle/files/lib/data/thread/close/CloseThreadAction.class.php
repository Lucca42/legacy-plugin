<?php

namespace wcf\data\thread\close;

use wbb\data\thread\Thread;
use wbb\data\thread\ThreadAction;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * Lets a thread author close their own thread once it has reached the board's minimum
 * age. Restricted to the author on an enabled board.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class CloseThreadAction extends AbstractDatabaseObjectAction
{
    /**
     * The action constructor resolves the decorator's base class, so this must be the
     * editor class rather than the plain database object.
     * @var string
     */
    protected $className = 'wbb\\data\\thread\\ThreadEditor';

    private int $closeThreadID = 0;

    public function validateClose(): void
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
                            thread.time,
                            thread.isClosed,
                            board.gwLifecycleEnabled,
                            board.gwSelfCloseDelay
                FROM        wbb1_thread thread
                INNER JOIN  wbb1_board board ON board.boardID = thread.boardID
                WHERE       thread.threadID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$threadID]);
        $row = $statement->fetchSingleRow();

        if (!$row) {
            throw new IllegalLinkException();
        }
        if (!(int)$row['gwLifecycleEnabled'] || (int)$row['gwSelfCloseDelay'] <= 0) {
            throw new PermissionDeniedException();
        }
        if ((int)$row['userID'] !== $userID) {
            throw new PermissionDeniedException();
        }
        if ((int)$row['isClosed']) {
            throw new PermissionDeniedException();
        }
        if (TIME_NOW < (int)$row['time'] + (int)$row['gwSelfCloseDelay']) {
            throw new PermissionDeniedException();
        }

        $this->closeThreadID = $threadID;
    }

    public function close(): void
    {
        // Native WBB close (a Thread instance is required because close() does not call
        // readObjects()). We already authorised the request in validateClose().
        (new ThreadAction([new Thread($this->closeThreadID)], 'close'))->executeAction();
    }
}
