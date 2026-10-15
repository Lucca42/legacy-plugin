<?php

namespace wcf\data\hom\badge;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\hom\HomLookup;
use wcf\system\hom\HomProfile;
use wcf\system\WCF;

/**
 * Publishes or removes the current user's Hall of Monuments badge. The code is fetched
 * again server side, so the stored points are never client supplied.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class HomBadgeAction extends AbstractDatabaseObjectAction
{
    /**
     * The action works on the current user only; a valid editor class is still required.
     * @var string
     */
    protected $className = 'wcf\\data\\user\\UserEditor';

    private string $characterName = '';

    public function validatePublish(): void
    {
        if (!WCF::getUser()->userID || !WCF::getSession()->getPermission('user.gwHom.canView')) {
            throw new PermissionDeniedException();
        }

        $name = HomLookup::normalizeName((string)($this->parameters['character'] ?? ''));
        if ($name === null) {
            throw new UserInputException('character');
        }
        $this->characterName = $name;
    }

    /**
     * @return array{ok: bool, error?: string, characterName?: string, points?: int, title?: ?string}
     */
    public function publish(): array
    {
        $result = HomLookup::fetch($this->characterName);
        if ($result['status'] === HomLookup::STATUS_NOT_FOUND) {
            return ['ok' => false, 'error' => 'No character of that name.'];
        }
        if ($result['status'] !== HomLookup::STATUS_OK) {
            return ['ok' => false, 'error' => 'The Hall of Monuments service did not answer. Try again in a minute.'];
        }
        $this->characterName = $result['name'];

        $profile = HomProfile::fromCode($result['code']);
        if ($profile->empty) {
            return ['ok' => false, 'error' => 'This character has no statue dedicated yet.'];
        }

        $sql = "INSERT INTO wcf1_gw_hom_badge (userID, characterName, legacyBits, points, title, time)
                VALUES (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE characterName = VALUES(characterName), legacyBits = VALUES(legacyBits),
                    points = VALUES(points), title = VALUES(title), time = VALUES(time)";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([
            WCF::getUser()->userID,
            $this->characterName,
            $profile->code,
            $profile->total,
            $profile->rank ?? '',
            TIME_NOW,
        ]);

        return ['ok' => true, 'characterName' => $this->characterName, 'points' => $profile->total, 'title' => $profile->rank];
    }

    public function validateUnpublish(): void
    {
        if (!WCF::getUser()->userID) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * @return array{ok: bool}
     */
    public function unpublish(): array
    {
        $sql = "DELETE FROM wcf1_gw_hom_badge WHERE userID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([WCF::getUser()->userID]);

        return ['ok' => true];
    }
}
