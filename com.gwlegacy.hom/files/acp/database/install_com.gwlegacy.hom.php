<?php

/**
 * Schema for com.gwlegacy.hom.
 *
 *   wcf1_gw_hom_lookup   short cache of the official calculator answers, one row per name
 *   wcf1_gw_hom_badge    the badge a member chose to publish, one row per user
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */

use wcf\system\database\table\column\IntDatabaseTableColumn;
use wcf\system\database\table\column\TinyintDatabaseTableColumn;
use wcf\system\database\table\column\VarcharDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\index\DatabaseTableIndex;

return [
    DatabaseTable::create('wcf1_gw_hom_lookup')
        ->columns([
            VarcharDatabaseTableColumn::create('characterName')
                ->length(32)
                ->notNull(),
            VarcharDatabaseTableColumn::create('legacyBits')
                ->length(255)
                ->notNull()
                ->defaultValue(''),
            IntDatabaseTableColumn::create('time')
                ->length(10)
                ->notNull()
                ->defaultValue(0),
        ])
        ->indices([
            DatabaseTableIndex::create('characterName')
                ->type(DatabaseTableIndex::UNIQUE_TYPE)
                ->columns(['characterName']),
        ]),
    DatabaseTable::create('wcf1_gw_hom_badge')
        ->columns([
            IntDatabaseTableColumn::create('userID')
                ->length(10)
                ->notNull(),
            VarcharDatabaseTableColumn::create('characterName')
                ->length(32)
                ->notNull(),
            VarcharDatabaseTableColumn::create('legacyBits')
                ->length(255)
                ->notNull()
                ->defaultValue(''),
            TinyintDatabaseTableColumn::create('points')
                ->length(3)
                ->notNull()
                ->defaultValue(0),
            VarcharDatabaseTableColumn::create('title')
                ->length(40)
                ->notNull()
                ->defaultValue(''),
            IntDatabaseTableColumn::create('time')
                ->length(10)
                ->notNull()
                ->defaultValue(0),
        ])
        ->indices([
            DatabaseTableIndex::create('userID')
                ->type(DatabaseTableIndex::UNIQUE_TYPE)
                ->columns(['userID']),
        ]),
];
