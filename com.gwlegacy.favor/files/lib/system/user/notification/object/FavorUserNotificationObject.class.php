<?php

namespace wcf\system\user\notification\object;

use wcf\data\DatabaseObjectDecorator;
use wcf\data\favor\activation\FavorActivation;

/**
 * Notification object wrapping a favor activation.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 *
 * @method FavorActivation getDecoratedObject()
 * @mixin  FavorActivation
 */
final class FavorUserNotificationObject extends DatabaseObjectDecorator implements IUserNotificationObject
{
    protected static $baseClass = FavorActivation::class;

    public function getTitle(): string
    {
        return $this->getDecoratedObject()->getTitle();
    }

    public function getURL(): string
    {
        return $this->getDecoratedObject()->getLink();
    }

    public function getAuthorID(): int
    {
        return 0;
    }
}
