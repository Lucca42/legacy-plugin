<?php

/**
 * Schema for com.gwlegacy.upvotethanks.
 *
 *   wcf1_gw_post_upvote      one row per (post, user) upvote
 *   wcf1_gw_post_thanks      one row per reply thanked by the thread author
 *   wbb1_board.gwUpvoteEnabled   enables the upvote + thanks buttons on this board
 *   wbb1_board.gwLockFirstPost   locks the first post against editing on this board
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
    DatabaseTable::create('wcf1_gw_post_upvote')
        ->columns([
            IntDatabaseTableColumn::create('postID')
                ->length(10)
                ->notNull(),
            IntDatabaseTableColumn::create('userID')
                ->length(10)
                ->notNull(),
            IntDatabaseTableColumn::create('time')
                ->length(10)
                ->notNull()
                ->defaultValue(0),
        ])
        ->indices([
            DatabaseTableIndex::create('postID_userID')
                ->type(DatabaseTableIndex::UNIQUE_TYPE)
                ->columns(['postID', 'userID']),
        ]),

    DatabaseTable::create('wcf1_gw_post_thanks')
        ->columns([
            IntDatabaseTableColumn::create('postID')
                ->length(10)
                ->notNull(),
            IntDatabaseTableColumn::create('time')
                ->length(10)
                ->notNull()
                ->defaultValue(0),
        ])
        ->indices([
            DatabaseTableIndex::create('postID')
                ->type(DatabaseTableIndex::UNIQUE_TYPE)
                ->columns(['postID']),
        ]),

    PartialDatabaseTable::create('wbb1_board')
        ->columns([
            TinyintDatabaseTableColumn::create('gwUpvoteEnabled')
                ->length(1)
                ->notNull()
                ->defaultValue(0),
            TinyintDatabaseTableColumn::create('gwLockFirstPost')
                ->length(1)
                ->notNull()
                ->defaultValue(0),
        ]),
];
