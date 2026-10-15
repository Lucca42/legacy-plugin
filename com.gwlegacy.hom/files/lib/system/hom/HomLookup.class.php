<?php

namespace wcf\system\hom;

use GuzzleHttp\Psr7\Request;
use wcf\system\io\HttpFactory;
use wcf\system\WCF;

/**
 * Fetches a character's legacy_bits code from the official calculator, through a short
 * cache so repeated lookups and page reloads do not hit ArenaNet.
 *
 * The official endpoint answers 200 with the code when the character exists (the code
 * is account wide, every character of the account returns the same one), and 400
 * "ErrCharacterNotFound" otherwise. Names are case sensitive there.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class HomLookup
{
    private const ENDPOINT = 'https://hom.guildwars2.com/character/';

    public const CACHE_TTL = 600;

    public const STATUS_OK = 'ok';

    public const STATUS_NOT_FOUND = 'notFound';

    public const STATUS_FAILED = 'failed';

    /**
     * Cleans a typed character name: single spaces, letters only, 3 to 20 characters.
     * The case is kept, the endpoint is case sensitive. Null when it cannot be a name.
     */
    public static function normalizeName(string $name): ?string
    {
        $name = \trim(\preg_replace('/\s+/', ' ', $name));
        if (\strlen($name) < 3 || \strlen($name) > 20 || !\preg_match('/^[A-Za-z]+( [A-Za-z]+)*$/', $name)) {
            return null;
        }

        return $name;
    }

    /**
     * Looks the name up as typed, then with each word capitalized when the typed form is
     * unknown, so a lowercase entry still finds "Petit Calin Toudoux".
     *
     * @return array{status: string, name: string, code: string, time: int}
     */
    public static function fetch(string $name): array
    {
        $result = self::lookup($name);
        if ($result['status'] === self::STATUS_NOT_FOUND) {
            $capitalized = \ucwords(\strtolower($name));
            if ($capitalized !== $name) {
                $retry = self::lookup($capitalized);
                if ($retry['status'] !== self::STATUS_NOT_FOUND) {
                    return $retry;
                }
            }
        }

        return $result;
    }

    /**
     * @return array{status: string, name: string, code: string, time: int}
     */
    private static function lookup(string $name): array
    {
        $sql = "SELECT legacyBits, time FROM wcf1_gw_hom_lookup WHERE characterName = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$name]);
        $cached = $statement->fetchArray() ?: null;

        if ($cached && TIME_NOW - (int)$cached['time'] < self::CACHE_TTL) {
            return self::fromCache($name, $cached);
        }

        $fresh = self::request($name);
        if ($fresh === null) {
            return $cached
                ? self::fromCache($name, $cached)
                : ['status' => self::STATUS_FAILED, 'name' => $name, 'code' => '', 'time' => 0];
        }

        // An unknown name is cached as an empty code, so retries stay cheap.
        $sql = "INSERT INTO wcf1_gw_hom_lookup (characterName, legacyBits, time)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE legacyBits = VALUES(legacyBits), time = VALUES(time)";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$name, $fresh, TIME_NOW]);

        return self::fromCache($name, ['legacyBits' => $fresh, 'time' => TIME_NOW]);
    }

    private static function fromCache(string $name, array $row): array
    {
        return [
            'status' => $row['legacyBits'] === '' ? self::STATUS_NOT_FOUND : self::STATUS_OK,
            'name' => $name,
            'code' => $row['legacyBits'],
            'time' => (int)$row['time'],
        ];
    }

    /**
     * The code, an empty string when the character does not exist, null when the
     * service did not answer properly.
     */
    private static function request(string $name): ?string
    {
        try {
            $response = HttpFactory::makeClientWithTimeout(5)
                ->send(new Request('GET', self::ENDPOINT . \rawurlencode($name)), ['http_errors' => false]);
            $status = $response->getStatusCode();
            $data = \json_decode((string)$response->getBody(), true);
        } catch (\Throwable $e) {
            return null;
        }

        if ($status === 400 && ($data['reason'] ?? '') === 'ErrCharacterNotFound') {
            return '';
        }
        if ($status !== 200) {
            return null;
        }

        $code = $data['legacy_bits'] ?? null;
        if (!\is_string($code) || !HomBitReader::isValidCode($code)) {
            return null;
        }

        return $code;
    }
}
