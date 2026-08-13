<?php

/**
 * Schema for com.gwlegacy.favor.
 *
 *   wcf1_gw_favor_subscriber   users opted in to the "favor is active" notification
 *   wcf1_gw_favor_activation   one row per favor activation (backs the notification)
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */

use wcf\system\database\table\column\IntDatabaseTableColumn;
use wcf\system\database\table\column\ObjectIdDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\index\DatabaseTableForeignKey;
use wcf\system\database\table\index\DatabaseTableIndex;
use wcf\system\database\table\index\DatabaseTablePrimaryIndex;

return [
    DatabaseTable::create('wcf1_gw_favor_subscriber')
        ->columns([
            IntDatabaseTableColumn::create('userID')
                ->length(10)
                ->notNull(),
            IntDatabaseTableColumn::create('time')
                ->length(10)
                ->notNull()
                ->defaultValue(0),
        ])
        ->indices([
            DatabaseTableIndex::create('userID')
                ->type(DatabaseTableIndex::UNIQUE_TYPE)
                ->columns(['userID']),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['userID'])
                ->referencedTable('wcf1_user')
                ->referencedColumns(['userID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('wcf1_gw_favor_activation')
        ->columns([
            ObjectIdDatabaseTableColumn::create('activationID'),
            IntDatabaseTableColumn::create('time')
                ->length(10)
                ->notNull()
                ->defaultValue(0),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['activationID']),
        ]),
];
