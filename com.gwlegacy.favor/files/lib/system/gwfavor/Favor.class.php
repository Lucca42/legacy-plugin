<?php

namespace wcf\system\gwfavor;

/**
 * "Favor of the Gods" state from the gwtoolbox feed. Data older than 15 minutes counts
 * as unknown; favor is active while minutes_remaining (minus the data age) is positive.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class Favor
{
    public const STALE_AFTER = 900;

    public const MAX_ACHIEVEMENTS = 20;

    public int $minutesRemaining;

    public int $achievementsNeeded;

    public int $updatedAt;

    public function __construct(int $minutesRemaining, int $achievementsNeeded, int $updatedAt)
    {
        $this->minutesRemaining = $minutesRemaining;
        $this->achievementsNeeded = $achievementsNeeded;
        $this->updatedAt = $updatedAt;
    }

    /**
     * Builds a Favor from the decoded JSON payload, or null if it is malformed.
     */
    public static function fromArray(?array $data): ?self
    {
        if (!$data || !isset($data['updated_at'])) {
            return null;
        }

        return new self(
            (int)($data['minutes_remaining'] ?? 0),
            (int)($data['achievements_needed'] ?? 0),
            (int)$data['updated_at']
        );
    }

    public function age(): int
    {
        return TIME_NOW - $this->updatedAt;
    }

    public function isStale(): bool
    {
        return $this->age() > self::STALE_AFTER;
    }

    public function secondsRemaining(): int
    {
        return $this->minutesRemaining * 60 - $this->age();
    }

    /**
     * True while Favor of the Gods is running.
     */
    public function isActive(): bool
    {
        return !$this->isStale() && $this->secondsRemaining() > 0;
    }
}
