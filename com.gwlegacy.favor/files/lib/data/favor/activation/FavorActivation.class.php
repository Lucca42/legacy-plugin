<?php

namespace wcf\data\favor\activation;

use wcf\data\DatabaseObject;
use wcf\system\request\LinkHandler;

/**
 * A single "Favor of the Gods" activation. Exists so the notification has a reloadable
 * object; it carries no favor detail itself.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 *
 * @property-read int $activationID
 * @property-read int $time
 */
final class FavorActivation extends DatabaseObject
{
    protected static $databaseTableName = 'gw_favor_activation';

    protected static $databaseTableIndexName = 'activationID';

    public function getTitle(): string
    {
        return 'Favor of the Gods is active';
    }

    public function getLink(): string
    {
        return LinkHandler::getInstance()->getLink();
    }
}
