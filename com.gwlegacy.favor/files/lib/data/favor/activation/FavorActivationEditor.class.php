<?php

namespace wcf\data\favor\activation;

use wcf\data\DatabaseObjectEditor;

/**
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 *
 * @method static FavorActivation create(array $parameters = [])
 */
final class FavorActivationEditor extends DatabaseObjectEditor
{
    protected static $baseClass = FavorActivation::class;
}
