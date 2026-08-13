<?php

namespace wcf\system\user\notification\object\type;

use wcf\data\favor\activation\FavorActivation;
use wcf\data\favor\activation\FavorActivationList;
use wcf\system\user\notification\object\FavorUserNotificationObject;

/**
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class FavorUserNotificationObjectType extends AbstractUserNotificationObjectType
{
    protected static $decoratorClassName = FavorUserNotificationObject::class;

    protected static $objectClassName = FavorActivation::class;

    protected static $objectListClassName = FavorActivationList::class;
}
