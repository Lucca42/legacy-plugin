<?php

namespace wcf\data\post\thanks;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * Toggles the thread author's "thanks" on a reply. Only the thread author may thank, only
 * on replies (never the first post), and never on their own reply. Restricted to boards
 * where the feature is enabled.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class ThanksAction extends AbstractDatabaseObjectAction
{
    /**
     * Editor class required by the action constructor; we never load the object through it.
     * @var string
     */
    protected $className = 'wbb\\data\\post\\PostEditor';

    private int $postID = 0;

    public function validateToggle(): void
    {
        $userID = WCF::getUser()->userID;
        if (!$userID) {
            throw new PermissionDeniedException();
        }

        $postID = (int)($this->objectIDs[0] ?? 0);
        if (!$postID) {
            throw new IllegalLinkException();
        }

        $sql = "SELECT      post.userID,
                            post.threadID,
                            thread.userID AS threadUserID,
                            board.gwUpvoteEnabled
                FROM        wbb1_post post
                INNER JOIN  wbb1_thread thread ON thread.threadID = post.threadID
                INNER JOIN  wbb1_board board ON board.boardID = thread.boardID
                WHERE       post.postID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$postID]);
        $row = $statement->fetchSingleRow();

        if (!$row) {
            throw new IllegalLinkException();
        }
        if (!(int)$row['gwUpvoteEnabled']) {
            throw new PermissionDeniedException();
        }
        if ($this->getFirstPostID((int)$row['threadID']) === $postID) {
            throw new PermissionDeniedException(); // replies only
        }
        if ((int)$row['threadUserID'] !== $userID) {
            throw new PermissionDeniedException(); // only the thread author may thank
        }
        if ((int)$row['userID'] === $userID) {
            throw new PermissionDeniedException(); // not your own reply
        }

        $this->postID = $postID;
    }

    private function getFirstPostID(int $threadID): int
    {
        $sql = "SELECT MIN(postID) FROM wbb1_post WHERE threadID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$threadID]);

        return (int)$statement->fetchSingleColumn();
    }

    /**
     * @return array{active: bool}
     */
    public function toggle(): array
    {
        $sql = "SELECT COUNT(*) FROM wcf1_gw_post_thanks WHERE postID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$this->postID]);
        $active = (int)$statement->fetchSingleColumn() > 0;

        if ($active) {
            $sql = "DELETE FROM wcf1_gw_post_thanks WHERE postID = ?";
            WCF::getDB()->prepare($sql)->execute([$this->postID]);
            $active = false;
        } else {
            $sql = "INSERT IGNORE INTO wcf1_gw_post_thanks (postID, time) VALUES (?, ?)";
            WCF::getDB()->prepare($sql)->execute([$this->postID, TIME_NOW]);
            $active = true;
        }

        return ['active' => $active];
    }
}
