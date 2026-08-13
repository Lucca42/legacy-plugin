<?php

namespace wcf\system\cronjob;

use GuzzleHttp\Psr7\Request;
use wcf\data\cronjob\Cronjob;
use wcf\data\favor\activation\FavorActivationEditor;
use wcf\system\gwfavor\Favor;
use wcf\system\io\HttpFactory;
use wcf\system\registry\RegistryHandler;
use wcf\system\user\notification\object\FavorUserNotificationObject;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\system\WCF;

/**
 * Polls the favor feed, stores the current state for the box, and notifies subscribers
 * when favor transitions from inactive to active.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class GwFavorCronjob extends AbstractCronjob
{
    private const PACKAGE = 'com.gwlegacy.favor';

    private const FEED_URL = 'https://kamadan.gwtoolbox.com/favor';

    public function execute(Cronjob $cronjob): void
    {
        parent::execute($cronjob);

        try {
            $response = HttpFactory::makeClientWithTimeout(5)->send(new Request('GET', self::FEED_URL));
            if ($response->getStatusCode() !== 200) {
                return;
            }
            $data = \json_decode((string)$response->getBody(), true);
        } catch (\Throwable $e) {
            return;
        }

        $favor = Favor::fromArray($data);
        if ($favor === null) {
            return;
        }

        RegistryHandler::getInstance()->set(self::PACKAGE, 'favor', \json_encode([
            'minutesRemaining' => $favor->minutesRemaining,
            'achievementsNeeded' => $favor->achievementsNeeded,
            'updatedAt' => $favor->updatedAt,
        ]));

        $wasActive = RegistryHandler::getInstance()->get(self::PACKAGE, 'active') === '1';
        $isActive = $favor->isActive();
        RegistryHandler::getInstance()->set(self::PACKAGE, 'active', $isActive ? '1' : '0');

        if ($isActive && !$wasActive) {
            $this->notifySubscribers();
        }
    }

    private function notifySubscribers(): void
    {
        $sql = "SELECT userID FROM wcf1_gw_favor_subscriber";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute();
        $recipientIDs = \array_map('intval', $statement->fetchAll(\PDO::FETCH_COLUMN));
        if (empty($recipientIDs)) {
            return;
        }

        $activation = FavorActivationEditor::create(['time' => TIME_NOW]);

        UserNotificationHandler::getInstance()->fireEvent(
            'favorActive',
            self::PACKAGE . '.notification',
            new FavorUserNotificationObject($activation),
            $recipientIDs
        );
    }
}
