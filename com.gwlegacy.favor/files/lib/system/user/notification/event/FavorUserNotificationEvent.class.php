<?php

namespace wcf\system\user\notification\event;

/**
 * Notification fired when Favor of the Gods becomes active. English text is hardcoded
 * (single-language community).
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 *
 * @method \wcf\system\user\notification\object\FavorUserNotificationObject getUserNotificationObject()
 */
final class FavorUserNotificationEvent extends AbstractUserNotificationEvent
{
    protected $stackable = false;

    public function getTitle(): string
    {
        return 'Favor of the Gods is active';
    }

    public function getMessage(): string
    {
        return 'Favor of the Gods is now active. Make the most of it.';
    }

    public function getLink(): string
    {
        return $this->getUserNotificationObject()->getURL();
    }
}
