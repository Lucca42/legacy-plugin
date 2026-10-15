# Hall of Monuments calculator: integration brief

Working notes on turning the standalone HoM calculator PoC into a gwlegacy plugin.
The PoC itself is in a shared Claude conversation that cannot be fetched from here
(Cloudflare challenge), so this brief is built from the official calculator and the site.
Reference copies of the official data and point logic are in `research/hom/` (gitignored,
ArenaNet code, kept for study only).

## How the official calculator works (verified 15 Oct 2026)

- Lookup: `GET https://hom.guildwars2.com/character/<Character Name>` (URL encoded,
  no auth, no key) returns `{"legacy_bits": "<base64>"}`. A character with an empty
  HoM returns the all-zero code; a name that does not exist still returns 200 with the
  zero code, so "not found" and "empty HoM" look the same.
- No CORS header on the endpoint, so the browser cannot call it from guildwarslegacy.com. The lookup must go through our server (PHP, same pattern as the favor cron).
- `legacy_bits` is a bitfield read in this order (see `research/hom/points.js`):
  resilience 20 flags padded to 32 bits, fellowship 30 flags padded to 32, honor 54
  flags padded to 64, valor 33 flags padded to 64, then devotion as four 7-bit counts
  (common, uncommon, rare, unique miniatures). The flag order is the key order of the
  catalog in `research/hom/character_catalog.json`; it must be reproduced exactly.
- Points (50 total) per monument:
  - Devotion 8: any mini 1, rare 1, unique 1, 20 minis 2, 30 1, 40 1, 50 1.
  - Fellowship 8: any hero 1, any pet 1, rare pet 1, 5 statues 2, 10 1, 20 1, 30 1.
  - Honor 18: accounts linked 3 (always granted), any statue 2, PvP statue 3, 5 statues 3,
    then 10/15/20/25/30/35/40 one each. Codex and Commander titles together count as a
    single honor statue, the PvP set is Champion, Codex, Commander, Gladiator, Hero,
    Zaishen.
  - Resilience 8: any armor 1, 3 sets 1, 5 sets 2, 7 sets 1, Kurzick or Luxon 1,
    Vabbian 1, Obsidian 1.
  - Valor 8: any weapon 1, Destroyer 1, Tormented 1, Oppressor 1, 5 weapons 1, 11 2, 15 1.
- Rewards: 30 items at 1 to 30 points, titles at 5, 10, 15, 20, 25, 30, 35, 40, 45, 50.
  Full list with descriptions in `research/hom/homdata.js` under `rewards`.
- Data freshness: the game writes the HoM state when the character logs out. Points are
  account wide (every character of the account returns the same bits).

## Where it fits on the site

The header already has **Tools & Downloads** with Kamadan Trade, GWToolbox, GW Market,
DirectSong, Build Creator, Party search. DirectSong is the only one hosted on the site, as
a plain WoltLab page. A **Hall of Monuments** entry there is the natural home: a WoltLab
page (page PIP + controller + template) with its own menu item, same chrome as the rest of
the site, mobile friendly, English hardcoded like the other plugins.

Why host it on legacy rather than link to the official one: the official site is a 2012
YUI app that ArenaNet barely maintains (CSS bug, hero/pet miscount, Steam account caveats),
and a legacy version can tie the HoM to what the forum is about: trading and collecting.

## Features, by phase

### Phase 1, the calculator (MVP, no database)
- Character name field, lookup through our server, result cached per name for 10 minutes.
- Points per monument with progress bars, total out of 50, next reward tier.
- Reward list with what is unlocked and what the next points unlock.
- Per monument detail: owned and missing statues (minis by rarity count only, the game
  does not expose which minis; heroes, pets, titles, armors, weapons by name).
- Share link: the page URL with the character name, or with the raw code so it also works
  for a character that no longer loads.
- Manual mode: toggle statues by hand to plan ("what if I add Vabbian"), encoded back to
  the same code format so it can be shared.
- Graceful failure: endpoint down or empty code shows a clear message, never an error page.

### Phase 2, member link
- "This is my character": store one or more character names per member
  (`wcf1_gw_hom_character`), refresh on demand, cache the last result.
- HoM points in the member profile statistics, next to the upvotes and thanks counters
  planned for upvotethanks 1.5.0.
- Public top of members by HoM points, same box or page as the upvote top.
- Optional: a small "50/50" style badge under the avatar in posts.

### Phase 3, trading and museum hooks
- "What I still need" list with a search link into the Selling board (and later the
  market plugin) for each missing weapon, armor or mini category. This is the feature only
  legacy can offer: the HoM drives demand for minis, Tormented and Oppressor weapons.
- A `[hom]code[/hom]` BBCode that renders a compact HoM summary inside a post, for the
  Collections board and signatures.

## Constraints

- The only data source is an undocumented ArenaNet endpoint. It has been stable since
  2012 but can change or disappear without notice. The plugin must snapshot the catalog
  and rules locally, and degrade to manual mode if the lookup fails.
- Keep the lookup server side with a cache and a per-user rate limit, so 12k members do
  not hammer the endpoint. The favor plugin already does outbound HTTPS from the server,
  so the hosting allows it.
- Name lookups are public and need no login, but saving a character requires one.
- No images from the official site (ArenaNet assets). Use the five monument icons and the
  wiki item icons if needed, with credit, or text only for phase 1.
- Usual plugin rules: tar installable from the ACP, no server-side build, no language or
  option PIP, no JS framework, WoltLab `require` only, author Calin.
- The PoC is still worth recovering: it may already contain the decoder, the catalog as a
  JS object, and UI decisions. Paste its final code into `research/hom/poc/` when
  available.

## Open questions for Calin

1. Phase 1 only first, or phase 1 + member link in the same release?
2. Menu placement: under Tools & Downloads, or a top-level entry?
3. Manual mode in phase 1 or later?
4. Should the lookup also work for guests, or members only (helps rate limiting)?
