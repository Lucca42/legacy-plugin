<?php

namespace wcf\system\gw;

use wcf\system\WCF;

/**
 * Shared cooldown for the two ways a thread author can resurface their own thread:
 * posting an update, or pressing the bump button. Both draw from the same delay, so an
 * author cannot post and then immediately bump on top of it.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class BumpSchedule
{
    /**
     * Returns the last time the author resurfaced the thread: the most recent of their
     * own posts and their last bump. Replies from other members do not count, so a
     * question from a buyer never consumes the author's daily allowance.
     */
    public static function lastResurfaceTime(int $threadID, int $authorUserID, int $lastBumpTime): int
    {
        $sql = "SELECT  MAX(post.time)
                FROM    wbb1_post post
                WHERE   post.threadID = ?
                    AND post.userID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$threadID, $authorUserID]);

        return \max($lastBumpTime, (int)$statement->fetchSingleColumn());
    }

    /**
     * Human-readable form of an interval, in the largest whole unit (hours preferred, as
     * bump intervals are typically set in hours): e.g. 86400 -> "24 hours", 60 -> "1 minute".
     */
    public static function describeInterval(int $seconds): string
    {
        if ($seconds >= 3600 && $seconds % 3600 === 0) {
            $hours = \intdiv($seconds, 3600);

            return $hours . ' hour' . ($hours > 1 ? 's' : '');
        }
        if ($seconds >= 60 && $seconds % 60 === 0) {
            $minutes = \intdiv($seconds, 60);

            return $minutes . ' minute' . ($minutes > 1 ? 's' : '');
        }

        return $seconds . ' second' . ($seconds !== 1 ? 's' : '');
    }
}
