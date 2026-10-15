<?php

namespace wcf\system\event\listener;

use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Preloads the Hall of Monuments badges of the authors on the current thread page.
 *
 *   {$gwHomBadges}  map userID => [characterName, points, title]
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class GwHomThreadBadgeListener implements IParameterizedEventListener
{
    public function execute($eventObj, $className, $eventName, array &$parameters): void
    {
        if (!isset($eventObj->objectList) || !\is_object($eventObj->objectList)) {
            return;
        }

        $userIDs = [];
        foreach ($eventObj->objectList->getObjects() as $post) {
            if ($post->userID) {
                $userIDs[(int)$post->userID] = true;
            }
        }
        if (empty($userIDs)) {
            return;
        }

        $conditions = new PreparedStatementConditionBuilder();
        $conditions->add('userID IN (?)', [\array_keys($userIDs)]);

        $sql = "SELECT userID, characterName, points, title FROM wcf1_gw_hom_badge " . $conditions;
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditions->getParameters());

        $badges = [];
        while ($row = $statement->fetchArray()) {
            $badges[(int)$row['userID']] = ['characterName' => $row['characterName'], 'points' => (int)$row['points'], 'title' => $row['title']];
        }

        WCF::getTPL()->assign('gwHomBadges', $badges);
    }
}
