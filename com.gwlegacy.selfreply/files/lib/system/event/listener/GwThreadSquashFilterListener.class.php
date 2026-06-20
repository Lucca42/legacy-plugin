<?php

namespace wcf\system\event\listener;

/**
 * Hides squashed replies from the thread view.
 *
 * The condition is added before the post list is counted and read, so pagination stays
 * consistent. Visible after a squash: the opening post plus any later reply. The
 * "Show previous replies" button reloads with ?showSquashed=1 to bypass the filter.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class GwThreadSquashFilterListener implements IParameterizedEventListener
{
    public function execute($eventObj, $className, $eventName, array &$parameters): void
    {
        if (!empty($_GET['showSquashed'])) {
            return;
        }

        if (!isset($eventObj->thread) || !\is_object($eventObj->thread)) {
            return;
        }
        $thread = $eventObj->thread;

        $cutoff = (int)$thread->gwSquashPostID;
        if ($cutoff <= 0) {
            return;
        }

        $eventObj->objectList->getConditionBuilder()->add(
            '(post.postID = ? OR post.postID > ?)',
            [(int)$thread->firstPostID, $cutoff]
        );
    }
}
