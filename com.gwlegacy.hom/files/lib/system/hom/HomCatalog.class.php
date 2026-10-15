<?php

namespace wcf\system\hom;

/**
 * Static Hall of Monuments data, mirrored from the official reward calculator
 * (hom.guildwars2.com). Item order matters: it is the bit order of the legacy_bits code.
 * Do not reorder.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class HomCatalog
{
    public const MAX_POINTS = 50;

    /**
     * Monuments in display order, with the points each one can give.
     */
    public const MONUMENTS = [
        'devotion' => ['title' => 'Devotion', 'subtitle' => 'Miniatures', 'possible' => 8],
        'fellowship' => ['title' => 'Fellowship', 'subtitle' => 'Heroes and pets', 'possible' => 8],
        'honor' => ['title' => 'Honor', 'subtitle' => 'Titles', 'possible' => 18],
        'resilience' => ['title' => 'Resilience', 'subtitle' => 'Armor', 'possible' => 8],
        'valor' => ['title' => 'Valor', 'subtitle' => 'Weapons', 'possible' => 8],
    ];

    /**
     * Bits reserved for each flag block in the code; unused bits are padding.
     */
    public const BLOCK_BITS = [
        'resilience' => 32,
        'fellowship' => 32,
        'honor' => 64,
        'valor' => 64,
    ];

    /**
     * Devotion is four 7-bit counters, in this order.
     */
    public const DEVOTION_COUNTERS = ['common', 'uncommon', 'rare', 'unique'];

    /**
     * Resilience statues, one flag each, in bit order. An empty name is an unused slot.
     */
    public const RESILIENCE = [
        'Elite Canthan Armor',
        'Elite Exotic Armor',
        'Elite Kurzick Armor',
        'Elite Luxon Armor',
        'Imperial Ascended Armor',
        'Ancient Armor',
        'Elite Sunspear Armor',
        'Vabbian Armor',
        'Primeval Armor',
        'Asuran Armor',
        'Norn Armor',
        'Silver Eagle Armor',
        'Monument Armor',
        'Obsidian Armor',
        'Granite Citadel Elite Armor',
        'Granite Citadel Exclusive Armor',
        'Granite Citadel Ascended Armor',
        'Marhan\'s Grotto Elite Armor',
        'Marhan\'s Grotto Exclusive Armor',
        'Marhan\'s Grotto Ascended Armor',
    ];

    /**
     * Fellowship statues, one flag each, in bit order. An empty name is an unused slot.
     */
    public const FELLOWSHIP = [
        'Zenmai',
        'Norgu',
        'Goren',
        'Zhed Shadowhoof',
        'General Morgahn',
        'Margrid the Sly',
        'Tahlkora',
        'Razah',
        'Master of Whispers',
        'Koss',
        'Dunkoro',
        'Melonni',
        'Acolyte Jin',
        'Acolyte Sousuke',
        'Vekk',
        'Livia',
        'Hayda',
        'Ogden Stonehealer',
        'Pyre Fierceshot',
        'Jora',
        'Kahmu',
        'Xandra',
        'Anton',
        'Gwen',
        'Animal Companion',
        'Black Moa',
        'Imperial Phoenix',
        'Black Widow Spider',
        'Olias',
        'M.O.X.',
    ];

    /**
     * Honor statues, one flag each, in bit order. An empty name is an unused slot.
     */
    public const HONOR = [
        'Eternal Champion',
        'Eternal Commander',
        'Eternal Skillz',
        'Eternal Gladiator',
        'Eternal Hero',
        'Eternal Lightbringer',
        'Eternal Bookah',
        'Eternal Delver',
        'Eternal Slayer',
        'Eternal Ebon Vanguard Agent',
        'Eternal Defender of Ascalon',
        'Eternal Tyrian Cartographer',
        'Eternal Guardian of Tyria',
        'Eternal Protector of Tyria',
        'Eternal Tyrian Skill Hunter',
        'Eternal Tyrian Vanquisher',
        'Eternal Canthan Cartographer',
        'Eternal Guardian of Cantha',
        'Eternal Protector of Cantha',
        'Eternal Canthan Skill Hunter',
        'Eternal Canthan Vanquisher',
        'Eternal Savior of the Kurzicks',
        'Eternal Savior of the Luxons',
        'Eternal Elonian Cartographer',
        'Eternal Guardian of Elona',
        'Eternal Protector of Elona',
        'Eternal Elonian Skill Hunter',
        'Eternal Elonian Vanquisher',
        'Eternal Ale-Hound',
        'Eternal Party Animal',
        'Eternal Master of the North',
        'Eternal Legendary Cartographer',
        'Eternal Legendary Guardian',
        'Eternal Legendary Skill Hunter',
        'Eternal Legendary Vanquisher',
        'Eternal Fortune',
        'Eternal Sweet Tooth',
        'Eternal Spearmarshal',
        'Eternal Survivor',
        '',
        'Eternal Treasure Hunter',
        'Eternal Misfortune',
        'Eternal Source of Wisdom',
        'Eternal Hero of Tyria',
        'Eternal Hero of Cantha',
        'Eternal Hero of Elona',
        'Eternal Conqueror of Sorrow\'s Furnace',
        'Eternal Conqueror of the Deep',
        'Eternal Conqueror of Urgoz\'s Warren',
        'Eternal Conqueror of the Fissure of Woe',
        'Eternal Conqueror of the Underworld',
        'Eternal Conqueror of the Domain of Anguish',
        'Eternal Zaishen Supporter',
        'Eternal Codex Disciple',
    ];

    /**
     * Valor statues, one flag each, in bit order. An empty name is an unused slot.
     */
    public const VALOR = [
        'Destroyer Axe',
        'Destroyer Bow',
        'Destroyer Daggers',
        'Destroyer Focus',
        'Destroyer Maul',
        'Destroyer Scepter',
        'Destroyer Scythe',
        'Destroyer Shield',
        'Destroyer Spear',
        'Destroyer Staff',
        'Destroyer Sword',
        'Tormented Axe',
        'Tormented Bow',
        'Tormented Daggers',
        'Tormented Focus',
        'Tormented Maul',
        'Tormented Scepter',
        'Tormented Scythe',
        'Tormented Shield',
        'Tormented Spear',
        'Tormented Staff',
        'Tormented Sword',
        'Oppressor Axe',
        'Oppressor Bow',
        'Oppressor Daggers',
        'Oppressor Focus',
        'Oppressor Maul',
        'Oppressor Scepter',
        'Oppressor Scythe',
        'Oppressor Shield',
        'Oppressor Spear',
        'Oppressor Staff',
        'Oppressor Sword',
    ];

    public const FELLOWSHIP_PET = 'Animal Companion';

    public const FELLOWSHIP_RARE = [
        'Black Moa',
        'Imperial Phoenix',
        'Black Widow Spider',
    ];

    public const HONOR_PVP = [
        'Eternal Hero',
        'Eternal Champion',
        'Eternal Gladiator',
        'Eternal Zaishen Supporter',
    ];

    /**
     * These two count as a single PvP statue together.
     */
    public const HONOR_CODEX_COMMANDER = [
        'Eternal Commander',
        'Eternal Codex Disciple',
    ];

    public const RESILIENCE_VABBIAN = [
        'Vabbian Armor',
    ];

    public const RESILIENCE_OBSIDIAN = [
        'Obsidian Armor',
    ];

    public const RESILIENCE_KURZICK_LUXON = [
        'Elite Luxon Armor',
        'Elite Kurzick Armor',
    ];

    public const VALOR_DESTROYER = [
        'Destroyer Axe',
        'Destroyer Bow',
        'Destroyer Daggers',
        'Destroyer Focus',
        'Destroyer Maul',
        'Destroyer Scepter',
        'Destroyer Scythe',
        'Destroyer Shield',
        'Destroyer Spear',
        'Destroyer Staff',
        'Destroyer Sword',
    ];

    public const VALOR_TORMENTED = [
        'Tormented Axe',
        'Tormented Bow',
        'Tormented Daggers',
        'Tormented Focus',
        'Tormented Maul',
        'Tormented Scepter',
        'Tormented Scythe',
        'Tormented Shield',
        'Tormented Spear',
        'Tormented Staff',
        'Tormented Sword',
    ];

    public const VALOR_OPPRESSOR = [
        'Oppressor Axe',
        'Oppressor Bow',
        'Oppressor Daggers',
        'Oppressor Focus',
        'Oppressor Maul',
        'Oppressor Scepter',
        'Oppressor Scythe',
        'Oppressor Shield',
        'Oppressor Spear',
        'Oppressor Staff',
        'Oppressor Sword',
    ];

    /**
     * Point rules per monument, in the official order: label and points.
     */
    public const RULES = [
        'devotion' => [
            ['Any Miniature Statue', 1],
            ['Rare Miniature Statue', 1],
            ['Unique Miniature Statue', 1],
            ['20 Miniature Statues', 2],
            ['30 Miniature Statues', 1],
            ['40 Miniature Statues', 1],
            ['50 Miniature Statues', 1],
        ],
        'fellowship' => [
            ['Any Hero Statue', 1],
            ['Any Pet Statue', 1],
            ['Any Rare Pet Statue', 1],
            ['5 Companion Statues (Full Display)', 2],
            ['10 Companion Statues', 1],
            ['20 Companion Statues', 1],
            ['30 Companion Statues', 1],
        ],
        'honor' => [
            ['Accounts Linked', 3],
            ['Any Statue', 2],
            ['1 PvP Statue', 3],
            ['5 Statues (Full Display)', 3],
            ['10 Statues', 1],
            ['15 Statues', 1],
            ['20 Statues', 1],
            ['25 Statues', 1],
            ['30 Statues', 1],
            ['35 Statues', 1],
            ['40 Statues', 1],
        ],
        'resilience' => [
            ['Any Armor Statue', 1],
            ['3 Armor Statues', 1],
            ['5 Armor Statues (Full display)', 2],
            ['7 Armor Statues', 1],
            ['Kurzick or Luxon Armor Statue', 1],
            ['Vabbian Armor Statue', 1],
            ['Obsidian Armor Statue', 1],
        ],
        'valor' => [
            ['Any Weapon Statue', 1],
            ['Destroyer Weapon Statue', 1],
            ['Tormented Weapon Statue', 1],
            ['Oppressor Weapon Statue', 1],
            ['5 Weapon Statues', 1],
            ['11 Weapon Statues (Full Display)', 2],
            ['15 Weapon Statues', 1],
        ],
    ];

    /**
     * Guild Wars 2 reward items per point tier, index 0 unlocks at 1 point. A tier may hold two items.
     */
    public const REWARDS = [
        [['Heritage Shoes, Boots, Warboots', 'Heritage footwear for light, medium, and heavy armor professions']],
        [['Heritage Pants, Legguards, Legplates', 'Heritage legwear for light, medium, and heavy armor professions']],
        [['Heritage Great Coat, Jerkin, Warplate', 'Heritage coats for light, medium, and heavy armor professions']],
        [['Heritage Gloves, Armguards, Gauntlets', 'Heritage handwear for light, medium, and heavy armor professions']],
        [['Heritage Mantle, Shoulderpads, Pauldrons', 'Heritage shoulder covering for light, medium, and heavy armor professions']],
        [['Heritage Masque, Bandana, Warhelm', 'Heritage headwear for light, medium, and heavy armor professions']],
        [['Gnarled Walking Stick', 'An ancient staff']],
        [['Living Shortbow', 'An organic bow']],
        [['Orange Tabby Cat', 'A feline mini-pet']],
        [['Fiery Dragon Sword', 'A flaming dragon blade']],
        [['Diamond Aegis', 'A brilliant diamond shield']],
        [['Baroque Mask', 'An elegant harlequin mask']],
        [['Centurion\'s Claw', 'A double-bladed dagger']],
        [['Wheelock Rifle', 'An antique rifle']],
        [['Orrian Baby Chicken', 'An oddly lovable mini-pet'], ['Black Moa', 'A dark-feathered moa for rangers']],
        [['Wayward Wand', 'A clawed scepter']],
        [['Seathunder Pistol', 'An aquatic themed pistol']],
        [['Heavenly Bracers', 'Gauntlets of Dwayna']],
        [['Deldrimor Mace', 'An ancient dwarven mace']],
        [['Chimeric Prism', 'A brilliant, colorful focus'], ['Rainbow Jellyfish', 'A spineless aquatic companion for your ranger']],
        [['Rockfur Raccoon', 'A woodland mini-pet']],
        [['Ithas Longbow', 'A legendary longbow']],
        [['Fellblade', 'An ominous greatsword']],
        [['Icelord\'s Diadem', 'An icy crown']],
        [['Ice Breaker', 'A mighty hammer of ice'], ['White Raven Skin', 'A white-feathered raven for your ranger']],
        [['Flaming Beacon', 'An eternally burning torch']],
        [['Red Servitor Golem', 'A loyal golem mini-pet']],
        [['Stygian Reaver', 'A long sought-after axe']],
        [['Mountaincall Warhorn', 'A great horn from antiquity']],
        [['Fire God\'s Vambraces', 'Fiery gloves of the human god Balthazar'], ['Black Widow Spider', 'A deadly spider companion for your ranger']],
    ];

    /**
     * Guild Wars 2 reward titles by point threshold.
     */
    public const TITLES = [
        5 => 'Traveler',
        10 => 'Guild Warrior',
        15 => 'Rift Warden',
        20 => 'Chosen',
        25 => 'Ascendant',
        30 => 'Closer to the Stars',
        35 => 'Ghostly Hero',
        40 => 'Flameseeker',
        45 => 'Legend of the Mists',
        50 => 'Champion of the Gods',
    ];
}
