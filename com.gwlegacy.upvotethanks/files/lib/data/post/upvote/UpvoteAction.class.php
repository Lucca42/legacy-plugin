<?php

namespace wcf\data\post\upvote;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * Toggles the current user's upvote on a reply. Any logged-in user may upvote, except
 * the thread author and the post author, and never the first post. Restricted to boards
 * where upvotes are enabled.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class UpvoteAction extends AbstractDatabaseObjectAction
{
    /**
     * The action constructor resolves the decorator's base class, so this must be an
     * editor class; we never load the object ourselves.
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
        if ((int)$row['userID'] === $userID) {
            throw new PermissionDeniedException(); // not your own post
        }
        if ((int)$row['threadUserID'] === $userID) {
            throw new PermissionDeniedException(); // not the thread author
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
     * @return array{count: int, active: bool}
     */
    public function toggle(): array
    {
        $userID = WCF::getUser()->userID;

        $sql = "SELECT COUNT(*) FROM wcf1_gw_post_upvote WHERE postID = ? AND userID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$this->postID, $userID]);
        $active = (int)$statement->fetchSingleColumn() > 0;

        if ($active) {
            $sql = "DELETE FROM wcf1_gw_post_upvote WHERE postID = ? AND userID = ?";
            WCF::getDB()->prepare($sql)->execute([$this->postID, $userID]);
            $active = false;
        } else {
            $sql = "INSERT INTO wcf1_gw_post_upvote (postID, userID, time) VALUES (?, ?, ?)";
            WCF::getDB()->prepare($sql)->execute([$this->postID, $userID, TIME_NOW]);
            $active = true;
        }

        $sql = "SELECT COUNT(*) FROM wcf1_gw_post_upvote WHERE postID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$this->postID]);
        $count = (int)$statement->fetchSingleColumn();

        return ['count' => $count, 'active' => $active];
    }
}
