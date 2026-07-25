<?php

namespace wcf\system\cronjob;

use wbb\data\thread\Thread;
use wbb\data\thread\ThreadAction;
use wcf\data\cronjob\Cronjob;
use wcf\system\WCF;

/**
 * Closes open threads on enabled boards that have been inactive for longer than the
 * board's configured auto-close delay. A batch limit keeps a large backlog from being
 * processed in a single run; the next run picks up the rest.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class GwAutoCloseCronjob extends AbstractCronjob
{
    private const BATCH_LIMIT = 250;

    public function execute(Cronjob $cronjob): void
    {
        parent::execute($cronjob);

        $sql = "SELECT      thread.threadID
                FROM        wbb1_thread thread
                INNER JOIN  wbb1_board board ON board.boardID = thread.boardID
                WHERE       board.gwLifecycleEnabled = 1
                        AND board.gwAutoCloseAfter > 0
                        AND thread.isClosed = 0
                        AND thread.lastPostTime > 0
                        AND (? - thread.lastPostTime) >= board.gwAutoCloseAfter
                LIMIT       " . self::BATCH_LIMIT;
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([TIME_NOW]);
        $threadIDs = $statement->fetchAll(\PDO::FETCH_COLUMN);

        if (empty($threadIDs)) {
            return;
        }

        $threads = [];
        foreach ($threadIDs as $threadID) {
            $threads[] = new Thread($threadID);
        }

        (new ThreadAction($threads, 'close'))->executeAction();
    }
}
