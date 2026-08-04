<?php

namespace wcf\system\event\listener;

use wcf\data\DatabaseObjectDecorator;
use wcf\system\WCF;

/**
 * Fixes the "stuck unread" state that bump and squash create.
 *
 * WBB only marks a thread as read on view when the newest displayed post matches the
 * thread's lastPostTime. A bump pushes lastPostTime past any real post, and a squash
 * hides the newest posts, so that condition never holds and the thread stays unread. On
 * the last page (where the reader has seen everything visible) we invoke WBB's own
 * mark-as-read for the current user, on the boards where these features apply.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class GwThreadReadFixListener implements IParameterizedEventListener
{
    public function execute($eventObj, $className, $eventName, array &$parameters): void
    {
        if (!WCF::getUser()->userID) {
            return;
        }

        if (!isset($eventObj->thread) || !\is_object($eventObj->thread)) {
            return;
        }
        $thread = $eventObj->thread;

        $board = $thread->getBoard();
        if (!$board || !(int)$board->gwSelfReplyRestricted) {
            return;
        }

        // Only on the last page, so we never mark read past pages the user has not seen.
        $pageNo = isset($eventObj->pageNo) ? (int)$eventObj->pageNo : 1;
        $pages = isset($eventObj->pages) ? (int)$eventObj->pages : 1;
        if ($pages > 1 && $pageNo < $pages) {
            return;
        }

        if (\method_exists($thread, 'isNew') && !$thread->isNew()) {
            return;
        }

        $threadObject = $thread instanceof DatabaseObjectDecorator ? $thread->getDecoratedObject() : $thread;
        (new \wbb\data\thread\ThreadAction([$threadObject], 'markAsRead'))->executeAction();
    }
}
