<?php

/**
 * Schema for com.gwlegacy.retract.
 *
 *   wcf1_gw_retract_post     ids of non-retractable posts (retracted originals + retracts)
 *   wbb1_board.gwRetractEnabled  shows the retract button on replies
 *   wbb1_board.gwLockReplies     locks replies against editing on this board
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */

use wcf\system\database\table\column\IntDatabaseTableColumn;
use wcf\system\database\table\column\TinyintDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\index\DatabaseTableIndex;
use wcf\system\database\table\PartialDatabaseTable;

return [
    DatabaseTable::create('wcf1_gw_retract_post')
        ->columns([
            IntDatabaseTableColumn::create('postID')
                ->length(10)
                ->notNull(),
        ])
        ->indices([
            DatabaseTableIndex::create('postID')
                ->type(DatabaseTableIndex::UNIQUE_TYPE)
                ->columns(['postID']),
        ]),

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
