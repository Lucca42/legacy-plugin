<?php

namespace wcf\data\favor\subscription;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\registry\RegistryHandler;
use wcf\system\WCF;

/**
 * Favor box endpoint: toggles the current user's notification subscription and returns
 * the current favor state (stored by the cron) for the box.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class SubscriptionAction extends AbstractDatabaseObjectAction
{
    /**
     * A valid editor class is required by the action constructor; the action operates on
     * the current user only, so no object is loaded.
     * @var string
     */
    protected $className = 'wcf\\data\\user\\UserEditor';

    /**
     * @var string[]
     */
    protected $allowGuestAccess = ['getStatus'];

    public function validateToggle(): void
    {
        if (!WCF::getUser()->userID) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * @return array{subscribed: bool}
     */
    public function toggle(): array
    {
        $userID = WCF::getUser()->userID;

        $sql = "SELECT COUNT(*) FROM wcf1_gw_favor_subscriber WHERE userID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$userID]);
        $subscribed = (int)$statement->fetchSingleColumn() > 0;

        if ($subscribed) {
            $sql = "DELETE FROM wcf1_gw_favor_subscriber WHERE userID = ?";
            WCF::getDB()->prepare($sql)->execute([$userID]);
            $subscribed = false;
        } else {
            $sql = "INSERT IGNORE INTO wcf1_gw_favor_subscriber (userID, time) VALUES (?, ?)";
            WCF::getDB()->prepare($sql)->execute([$userID, TIME_NOW]);
            $subscribed = true;
        }

        return ['subscribed' => $subscribed];
    }

    public function validateGetStatus(): void
    {
        // public: no checks
    }

    /**
     * @return array{favor: ?array, subscribed: bool}
     */
    public function getStatus(): array
    {
        $favor = null;
        $raw = RegistryHandler::getInstance()->get('com.gwlegacy.favor', 'favor');
        if ($raw) {
            $favor = \json_decode($raw, true) ?: null;
        }

        $subscribed = false;
        if (WCF::getUser()->userID) {
            $sql = "SELECT COUNT(*) FROM wcf1_gw_favor_subscriber WHERE userID = ?";
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute([WCF::getUser()->userID]);
            $subscribed = (int)$statement->fetchSingleColumn() > 0;
        }

        return ['favor' => $favor, 'subscribed' => $subscribed];
    }
}
