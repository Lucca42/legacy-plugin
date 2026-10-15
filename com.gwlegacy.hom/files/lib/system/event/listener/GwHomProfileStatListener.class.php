<?php

namespace wcf\system\event\listener;

use wcf\event\user\profile\UserProfileStatItemCollecting;
use wcf\page\HomPage;
use wcf\system\request\LinkHandler;
use wcf\system\view\user\profile\UserProfileStatItem;
use wcf\system\WCF;

/**
 * Adds the member's Hall of Monuments badge to the profile header statistics, next to
 * posts, reactions and points: the title reached on one line, the points on the next.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class GwHomProfileStatListener
{
    public function __invoke(UserProfileStatItemCollecting $event): void
    {
        $sql = "SELECT characterName, points, title FROM wcf1_gw_hom_badge WHERE userID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$event->user->userID]);
        $badge = $statement->fetchArray();
        if (!$badge) {
            return;
        }

        $value = ($badge['title'] !== '' ? \htmlspecialchars($badge['title'], \ENT_QUOTES) . '<br>' : '')
            . (int)$badge['points'] . ' / 50';
        $link = LinkHandler::getInstance()->getControllerLink(HomPage::class, ['character' => $badge['characterName']]);

        $event->register(UserProfileStatItem::forLink(
            'Hall of Monuments',
            $value,
            $link,
            '',
            'title="' . \htmlspecialchars($badge['characterName'], \ENT_QUOTES) . '"'
        ));
    }
}
