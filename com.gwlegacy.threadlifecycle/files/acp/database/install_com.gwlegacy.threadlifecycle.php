<?php

/**
 * Adds the per-board lifecycle settings to the WBB board table.
 *
 *   gwLifecycleEnabled  master toggle for this board
 *   gwSelfCloseDelay    minimum thread age before the author may self-close (seconds; 0 = off)
 *   gwAutoCloseAfter    inactivity after which a thread is auto-closed (seconds; 0 = off)
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */

use wcf\system\database\table\column\IntDatabaseTableColumn;
use wcf\system\database\table\column\TinyintDatabaseTableColumn;
use wcf\system\database\table\PartialDatabaseTable;

return [
    PartialDatabaseTable::create('wbb1_board')
        ->columns([
            TinyintDatabaseTableColumn::create('gwLifecycleEnabled')
                ->length(1)
                ->notNull()
                ->defaultValue(0),
            IntDatabaseTableColumn::create('gwSelfCloseDelay')
                ->length(10)
                ->notNull()
                ->defaultValue(0),
            IntDatabaseTableColumn::create('gwAutoCloseAfter')
                ->length(10)
                ->notNull()
                ->defaultValue(0),
        ]),
];
