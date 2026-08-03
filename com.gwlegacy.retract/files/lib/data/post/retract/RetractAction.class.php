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
                            thread.firstPostID,
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
        if ((int)$row['firstPostID'] === $postID) {
            throw new PermissionDeniedException(); // replies only
        }
        if ((int)$row['userID'] !== $userID) {
            throw new PermissionDeniedException(); // author only
        }

        $this->postID = $postID;
        $this->threadID = (int)$row['threadID'];
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

        (new PostAction([], 'create', ['data' => $data]))->executeAction();
    }
}
