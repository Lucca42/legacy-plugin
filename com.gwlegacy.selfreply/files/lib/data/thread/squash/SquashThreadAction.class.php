<?php

namespace wcf\data\thread\squash;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * Squashes a thread: sets the cutoff to the current highest post so every existing
 * reply (except the opening post) is hidden from the normal thread view. Restricted to
 * the thread author on an enabled board.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class SquashThreadAction extends AbstractDatabaseObjectAction
{
    /**
     * @var string
     */
    protected $className = 'wbb\\data\\thread\\ThreadEditor';

    /**
     * @var int
     */
    private int $squashThreadID = 0;

    public function validateSquash(): void
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
                            board.gwSelfReplyRestricted
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

        $this->squashThreadID = $threadID;
    }

    /**
     * @return array{cutoff: int}
     */
    public function squash(): array
    {
        $sql = "SELECT MAX(postID) FROM wbb1_post WHERE threadID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$this->squashThreadID]);
        $cutoff = (int)$statement->fetchSingleColumn();

        $sql = "UPDATE wbb1_thread SET gwSquashPostID = ? WHERE threadID = ?";
        WCF::getDB()->prepare($sql)->execute([$cutoff, $this->squashThreadID]);

        return ['cutoff' => $cutoff];
    }
}
