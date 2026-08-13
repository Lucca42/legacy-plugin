<?php

namespace wcf\data\favor\activation;

use wcf\data\DatabaseObjectList;

/**
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 *
 * @method FavorActivation current()
 * @method FavorActivation[] getObjects()
 */
final class FavorActivationList extends DatabaseObjectList
{
    public $className = FavorActivation::class;
}
