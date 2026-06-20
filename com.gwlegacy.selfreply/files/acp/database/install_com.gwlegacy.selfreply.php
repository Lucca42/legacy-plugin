<?php

/**
 * Adds the plugin's columns to the WBB board and thread tables.
 *
 *   wbb1_board.gwSelfReplyRestricted  the per-board on/off toggle
 *   wbb1_board.gwBumpInterval         per-board minimum delay between bumps (seconds)
 *   wbb1_thread.gwLastBumpTime        timestamp of the author's last bump
 *   wbb1_thread.gwSquashPostID        squash cutoff (posts up to here are hidden)
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
            TinyintDatabaseTableColumn::create('gwSelfReplyRestricted')
                ->length(1)
                ->notNull()
                ->defaultValue(0),
            IntDatabaseTableColumn::create('gwBumpInterval')
                ->length(10)
                ->notNull()
                ->defaultValue(86400),
        ]),

    PartialDatabaseTable::create('wbb1_thread')
        ->columns([
            IntDatabaseTableColumn::create('gwLastBumpTime')
                ->length(10)
                ->notNull()
                ->defaultValue(0),
            IntDatabaseTableColumn::create('gwSquashPostID')
                ->length(10)
                ->notNull()
                ->defaultValue(0),
        ]),
];
