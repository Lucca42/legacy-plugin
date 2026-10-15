<?php

namespace wcf\page;

use wcf\system\hom\HomLookup;
use wcf\system\hom\HomProfile;
use wcf\system\WCF;

/**
 * Hall of Monuments page: looks up a character and shows its monuments, points and
 * Guild Wars 2 rewards. Members can publish the result as a badge on their profile.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
class HomPage extends AbstractPage
{
    /**
     * @inheritDoc
     */
    public $neededPermissions = ['user.gwHom.canView'];

    public ?string $characterName = null;

    public bool $invalidName = false;

    public bool $lookupFailed = false;

    public bool $notFound = false;

    public ?HomProfile $profile = null;

    public int $fetchedAt = 0;

    /**
     * @var array{characterName: string, points: int, title: string}|null the current user's badge
     */
    public ?array $myBadge = null;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (isset($_GET['character']) && \is_string($_GET['character']) && \trim($_GET['character']) !== '') {
            $this->characterName = HomLookup::normalizeName($_GET['character']);
            if ($this->characterName === null) {
                $this->invalidName = true;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function readData()
    {
        parent::readData();

        if ($this->characterName !== null) {
            $result = HomLookup::fetch($this->characterName);
            if ($result['status'] === HomLookup::STATUS_OK) {
                $this->characterName = $result['name'];
                $this->profile = HomProfile::fromCode($result['code']);
                $this->fetchedAt = $result['time'];
            } elseif ($result['status'] === HomLookup::STATUS_NOT_FOUND) {
                $this->notFound = true;
            } else {
                $this->lookupFailed = true;
            }
        }

        if (WCF::getUser()->userID) {
            $sql = "SELECT characterName, points, title FROM wcf1_gw_hom_badge WHERE userID = ?";
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute([WCF::getUser()->userID]);
            $row = $statement->fetchArray();
            if ($row) {
                $this->myBadge = ['characterName' => $row['characterName'], 'points' => (int)$row['points'], 'title' => $row['title']];
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'characterName' => $this->characterName,
            'invalidName' => $this->invalidName,
            'lookupFailed' => $this->lookupFailed,
            'notFound' => $this->notFound,
            'profile' => $this->profile,
            'fetchedAt' => $this->fetchedAt,
            'myBadge' => $this->myBadge,
        ]);
    }
}
