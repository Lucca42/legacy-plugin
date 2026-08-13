<?php

namespace wcf\system\box;

use wcf\system\WCF;

/**
 * Renders the Favor of the Gods box. The live status and countdown are computed
 * client-side from our own status endpoint (fed by the cron).
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class FavorBoxController extends AbstractBoxController
{
    protected static $supportedPositions = ['sidebarLeft', 'sidebarRight', 'contentTop', 'contentBottom'];

    protected function loadContent()
    {
        $this->content = WCF::getTPL()->render('wcf', 'boxFavor', [
            'loggedIn' => WCF::getUser()->userID > 0,
        ]);
    }
}
