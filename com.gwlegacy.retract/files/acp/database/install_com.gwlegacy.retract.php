<?php

/**
 * Adds the per-board retract settings to the WBB board table.
 *
 *   gwRetractEnabled  shows the retract button on replies
 *   gwLockReplies     locks replies against editing on this board
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */

use wcf\system\database\table\column\TinyintDatabaseTableColumn;
use wcf\system\database\table\PartialDatabaseTable;

return [
    PartialDatabaseTable::create('wbb1_board')
        ->columns([
            TinyintDatabaseTableColumn::create('gwRetractEnabled')
                ->length(1)
                ->notNull()
                ->defaultValue(0),
            TinyintDatabaseTableColumn::create('gwLockReplies')
                ->length(1)
                ->notNull()
                ->defaultValue(0),
        ]),
];
