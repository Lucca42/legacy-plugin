<?php

namespace wcf\system\hom;

/**
 * A decoded Hall of Monuments: which statues are dedicated, the points per monument
 * (same rules as the official calculator), the title reached and the Guild Wars 2
 * rewards unlocked. Everything the page shows is computed here.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class HomProfile
{
    public string $code;

    /**
     * @var array<string, array<string, bool>> monument => statue name => dedicated
     */
    public array $flags = [];

    /**
     * @var array<string, int> miniature counts by rarity
     */
    public array $devotion = [];

    /**
     * A bit set on the honor slot whose title was removed from the game.
     */
    public bool $retiredHonor = false;

    /**
     * @var array<string, array> per monument: title, subtitle, possible, total, max,
     *      count, summary, rules, niches, owned, missing, missingGroups
     */
    public array $monuments = [];

    public int $total = 0;

    /**
     * Highest reward title reached, null below 5 points.
     */
    public ?string $rank = null;

    /**
     * Statues not dedicated across the four listable monuments.
     */
    public int $missingTotal = 0;

    /**
     * @var array<int, array{lit: bool, milestone: bool}> the 50 point meter
     */
    public array $meter = [];

    /**
     * @var array<int, array{points: int, items: array, names: string, descs: string, title: ?string, unlocked: bool}>
     */
    public array $track = [];

    public int $itemsUnlocked = 0;

    public bool $empty = true;

    public static function fromCode(string $code): self
    {
        $reader = new HomBitReader($code);
        $profile = new self();
        $profile->code = $code;

        foreach (['resilience', 'fellowship', 'honor', 'valor'] as $monument) {
            $names = \constant(HomCatalog::class . '::' . \strtoupper($monument));
            $profile->flags[$monument] = [];
            foreach ($names as $name) {
                $bit = $reader->get(1);
                if ($name !== '') {
                    $profile->flags[$monument][$name] = (bool)$bit;
                } elseif ($bit) {
                    $profile->retiredHonor = true;
                }
            }
            $reader->get(HomCatalog::BLOCK_BITS[$monument] - \count($names));
        }
        foreach (HomCatalog::DEVOTION_COUNTERS as $counter) {
            $profile->devotion[$counter] = $reader->get(7);
        }

        $profile->compute();

        return $profile;
    }

    private function compute(): void
    {
        $this->monuments = [
            'devotion' => $this->devotionPoints(),
            'fellowship' => $this->fellowshipPoints(),
            'honor' => $this->honorPoints(),
            'resilience' => $this->resiliencePoints(),
            'valor' => $this->valorPoints(),
        ];

        $this->total = 0;
        $this->missingTotal = 0;
        foreach ($this->monuments as $key => &$monument) {
            $monument = \array_merge(HomCatalog::MONUMENTS[$key], $monument);
            $monument['max'] = $monument['total'] >= $monument['possible'];
            $monument['pool'] = \count($monument['owned']) + \count($monument['missing']);
            $this->total += $monument['total'];
            $this->missingTotal += \count($monument['missing']);
            if ($monument['count'] > 0) {
                $this->empty = false;
            }
        }
        unset($monument);

        foreach (HomCatalog::TITLES as $points => $name) {
            if ($this->total >= $points) {
                $this->rank = $name;
            }
        }

        for ($p = 1; $p <= HomCatalog::MAX_POINTS; $p++) {
            $this->meter[] = ['lit' => $p <= $this->total, 'milestone' => $p % 5 === 0];
            $items = HomCatalog::REWARDS[$p - 1] ?? [];
            $title = HomCatalog::TITLES[$p] ?? null;
            if ($items || $title) {
                $this->track[] = [
                    'points' => $p,
                    'items' => $items,
                    'names' => \implode(' and ', \array_column($items, 0)),
                    'descs' => \implode(" \u{b7} ", \array_column($items, 1)),
                    'title' => $title,
                    'unlocked' => $p <= $this->total,
                ];
            }
        }
        $this->itemsUnlocked = \min($this->total, \count(HomCatalog::REWARDS));
    }

    /**
     * @return string[] dedicated statue names, in catalog order
     */
    private function owned(string $monument): array
    {
        return \array_keys(\array_filter($this->flags[$monument]));
    }

    /**
     * @return string[] statue names still missing, in catalog order
     */
    private function missing(string $monument): array
    {
        return \array_keys(\array_filter($this->flags[$monument], static fn($dedicated) => !$dedicated));
    }

    private function ownedIn(string $monument, array $names): int
    {
        $n = 0;
        foreach ($names as $name) {
            if (!empty($this->flags[$monument][$name])) {
                $n++;
            }
        }

        return $n;
    }

    /**
     * One niche per catalog slot, filled when dedicated. The retired honor slot is
     * marked so the display can dot it instead of leaving a hole.
     *
     * @return array<int, array{filled: bool, retired: bool}>
     */
    private function niches(string $monument): array
    {
        $niches = [];
        foreach (\constant(HomCatalog::class . '::' . \strtoupper($monument)) as $name) {
            $niches[] = [
                'filled' => $name !== '' && !empty($this->flags[$monument][$name]),
                'retired' => $name === '',
            ];
        }

        return $niches;
    }

    /**
     * Rule rows from the catalog labels: earned flag, points, and for threshold rules
     * the progress in percent and as "current / needed".
     *
     * @param bool[] $earned one per rule, catalog order
     * @param array<int, array{int, int}> $thresholds rule index => [current, needed]
     */
    private function rules(string $monument, array $earned, array $thresholds = []): array
    {
        $rows = [];
        $total = 0;
        foreach (HomCatalog::RULES[$monument] as $index => [$label, $points]) {
            $row = ['label' => $label, 'points' => $points, 'earned' => $earned[$index], 'percent' => 0, 'progress' => null];
            if (!$earned[$index] && isset($thresholds[$index])) {
                [$current, $needed] = $thresholds[$index];
                $row['percent'] = (int)\min(100, \floor(100 * $current / $needed));
                $row['progress'] = "$current / $needed";
            }
            $rows[] = $row;
            if ($earned[$index]) {
                $total += $points;
            }
        }

        return ['rules' => $rows, 'total' => $total];
    }

    private function devotionPoints(): array
    {
        $d = $this->devotion;
        $count = \array_sum($d);
        $rules = $this->rules('devotion', [
            $count >= 1, $d['rare'] >= 1, $d['unique'] >= 1,
            $count >= 20, $count >= 30, $count >= 40, $count >= 50,
        ], [3 => [$count, 20], 4 => [$count, 30], 5 => [$count, 40], 6 => [$count, 50]]);

        $niches = [];
        for ($i = 0; $i < 50; $i++) {
            $niches[] = ['filled' => $i < $count, 'retired' => false];
        }

        return $rules + [
            'count' => $count,
            'summary' => $count . ' miniature' . ($count === 1 ? '' : 's') . ' dedicated, counted by rarity.',
            'tally' => $d,
            'niches' => $niches,
            'owned' => [],
            'missing' => [],
            'missingGroups' => [],
        ];
    }

    private function fellowshipPoints(): array
    {
        $pets = $this->ownedIn('fellowship', [HomCatalog::FELLOWSHIP_PET]);
        $rare = $this->ownedIn('fellowship', HomCatalog::FELLOWSHIP_RARE);
        $pets += $rare;
        $count = \count($this->owned('fellowship'));
        $heroes = $count - $pets;
        $rules = $this->rules('fellowship', [
            $heroes >= 1, $pets >= 1, $rare >= 1,
            $count >= 5, $count >= 10, $count >= 20, $count >= 30,
        ], [3 => [$count, 5], 4 => [$count, 10], 5 => [$count, 20], 6 => [$count, 30]]);

        return $rules + [
            'count' => $count,
            'summary' => "$heroes hero statue" . ($heroes === 1 ? '' : 's') . " and $pets companion" . ($pets === 1 ? '' : 's')
                . ' across ' . \count(HomCatalog::FELLOWSHIP) . ' slots.',
            'niches' => $this->niches('fellowship'),
            'owned' => $this->owned('fellowship'),
            'missing' => $this->missing('fellowship'),
            'missingGroups' => [],
        ];
    }

    private function honorPoints(): array
    {
        $count = \count($this->owned('honor'));
        // Codex and Commander together count as one PvP statue.
        $pvp = $this->ownedIn('honor', HomCatalog::HONOR_PVP)
            + ($this->ownedIn('honor', HomCatalog::HONOR_CODEX_COMMANDER) ? 1 : 0);
        $rules = $this->rules('honor', [
            true, $count >= 1, $pvp >= 1, $count >= 5, $count >= 10, $count >= 15,
            $count >= 20, $count >= 25, $count >= 30, $count >= 35, $count >= 40,
        ], [
            3 => [$count, 5], 4 => [$count, 10], 5 => [$count, 15], 6 => [$count, 20],
            7 => [$count, 25], 8 => [$count, 30], 9 => [$count, 35], 10 => [$count, 40],
        ]);
        $pool = \count(\array_filter(HomCatalog::HONOR, static fn($n) => $n !== ''));

        return $rules + [
            'count' => $count,
            'summary' => "$count title" . ($count === 1 ? '' : 's') . " out of $pool, "
                . ($pvp ? 'at least one of them PvP.' : 'none of them PvP.'),
            'niches' => $this->niches('honor'),
            'owned' => $this->owned('honor'),
            'missing' => $this->missing('honor'),
            'missingGroups' => [],
        ];
    }

    private function resiliencePoints(): array
    {
        $count = \count($this->owned('resilience'));
        $kurzickLuxon = $this->ownedIn('resilience', HomCatalog::RESILIENCE_KURZICK_LUXON) > 0;
        $vabbian = $this->ownedIn('resilience', HomCatalog::RESILIENCE_VABBIAN) > 0;
        $obsidian = $this->ownedIn('resilience', HomCatalog::RESILIENCE_OBSIDIAN) > 0;
        $rules = $this->rules('resilience', [
            $count >= 1, $count >= 3, $count >= 5, $count >= 7, $kurzickLuxon, $vabbian, $obsidian,
        ], [1 => [$count, 3], 2 => [$count, 5], 3 => [$count, 7]]);

        return $rules + [
            'count' => $count,
            'summary' => "$count armor set" . ($count === 1 ? '' : 's') . ' out of ' . \count(HomCatalog::RESILIENCE) . '.',
            'niches' => $this->niches('resilience'),
            'owned' => $this->owned('resilience'),
            'missing' => $this->missing('resilience'),
            'missingGroups' => [],
        ];
    }

    private function valorPoints(): array
    {
        $destroyer = $this->ownedIn('valor', HomCatalog::VALOR_DESTROYER);
        $tormented = $this->ownedIn('valor', HomCatalog::VALOR_TORMENTED);
        $oppressor = $this->ownedIn('valor', HomCatalog::VALOR_OPPRESSOR);
        $count = $destroyer + $tormented + $oppressor;
        $rules = $this->rules('valor', [
            $count >= 1, $destroyer >= 1, $tormented >= 1, $oppressor >= 1,
            $count >= 5, $count >= 11, $count >= 15,
        ], [4 => [$count, 5], 5 => [$count, 11], 6 => [$count, 15]]);

        // Missing weapons grouped by family, without the repeated prefix.
        $groups = [];
        foreach ($this->missing('valor') as $name) {
            [$family, $type] = \explode(' ', $name, 2);
            $groups[$family][] = $type;
        }

        return $rules + [
            'count' => $count,
            'summary' => "$destroyer Destroyer, $tormented Tormented, $oppressor Oppressor, $count weapon"
                . ($count === 1 ? '' : 's') . ' in all.',
            'niches' => $this->niches('valor'),
            'owned' => $this->owned('valor'),
            'missing' => $this->missing('valor'),
            'missingGroups' => $groups,
        ];
    }
}
