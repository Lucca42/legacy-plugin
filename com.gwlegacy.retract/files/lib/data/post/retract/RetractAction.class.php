<?php

namespace wcf\data\post\retract;

use wbb\data\post\Post;
use wbb\data\post\PostAction;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\html\input\HtmlInputProcessor;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Retracts a reply: posts a new reply that quotes the original and marks it "Retracted".
 * Restricted to the reply's own author on an enabled board; the first post cannot be
 * retracted.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class RetractAction extends AbstractDatabaseObjectAction
{
    /**
     * Editor class required by the action constructor; we never load the object through it.
     * @var string
     */
    protected $className = 'wbb\\data\\post\\PostEditor';

    private int $postID = 0;

    private int $threadID = 0;

    public function validateRetract(): void
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
                            board.gwRetractEnabled
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
        if (!(int)$row['gwRetractEnabled']) {
            throw new PermissionDeniedException();
        }
        if ($this->getFirstPostID((int)$row['threadID']) === $postID) {
            throw new PermissionDeniedException(); // replies only
        }
        if ((int)$row['userID'] !== $userID) {
            throw new PermissionDeniedException(); // author only
        }
        if ($this->isRetractPost($postID)) {
            throw new PermissionDeniedException(); // a retract cannot itself be retracted
        }

        $this->postID = $postID;
        $this->threadID = (int)$row['threadID'];
    }

    private function getFirstPostID(int $threadID): int
    {
        $sql = "SELECT MIN(postID) FROM wbb1_post WHERE threadID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$threadID]);

        return (int)$statement->fetchSingleColumn();
    }

    public function retract(): void
    {
        $post = new Post($this->postID);

        $html = '<p><b><span style="color: #cc0000">Retracted</span></b></p>'
            . '<woltlab-quote data-author="' . StringUtil::encodeHTML($post->username)
            . '" data-link="' . StringUtil::encodeHTML($post->getLink()) . '">'
            . $post->message
            . '</woltlab-quote>';

        $htmlInputProcessor = new HtmlInputProcessor();
        $htmlInputProcessor->process($html, 'com.woltlab.wbb.post');

        $data = [
            'threadID' => $this->threadID,
            'message' => $htmlInputProcessor->getHtml(),
            'time' => TIME_NOW,
            'userID' => WCF::getUser()->userID,
            'username' => WCF::getUser()->username,
        ];

        $returnValues = (new PostAction([], 'create', ['data' => $data]))->executeAction();

        // Prefer the id returned by the action; fall back to the newest post by this user
        // in the thread (immune to concurrent posts from other users).
        $newPostID = $this->extractPostID($returnValues);
        if (!$newPostID) {
            $sql = "SELECT MAX(postID) FROM wbb1_post WHERE threadID = ? AND userID = ?";
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute([$this->threadID, WCF::getUser()->userID]);
            $newPostID = (int)$statement->fetchSingleColumn();
        }

        // Both the retracted original and the new retract post become non-retractable:
        // the original so it cannot be retracted twice (spam), the new one so a retract
        // cannot itself be retracted.
        $sql = "INSERT IGNORE INTO wcf1_gw_retract_post (postID) VALUES (?)";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$this->postID]);
        if ($newPostID) {
            $statement->execute([$newPostID]);
        }
    }

    /**
     * Extracts the created post id from a PostAction 'create' result, if present.
     */
    private function extractPostID(array $returnValues): int
    {
        $object = $returnValues['returnValues'] ?? null;
        if (\is_object($object)) {
            if (isset($object->postID)) {
                return (int)$object->postID;
            }
            if (\method_exists($object, 'getObjectID')) {
                return (int)$object->getObjectID();
            }
        }

        return 0;
    }

    private function isRetractPost(int $postID): bool
    {
        $sql = "SELECT COUNT(*) FROM wcf1_gw_retract_post WHERE postID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$postID]);

        return (int)$statement->fetchSingleColumn() > 0;
    }
}
