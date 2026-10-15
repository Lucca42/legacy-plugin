# GWLegacy Hall of Monuments

A WoltLab Suite plugin that adds a Hall of Monuments reward calculator to the site: type a
Guild Wars character name, see the dedicated statues, the points per monument and the
Guild Wars 2 rewards they unlock. Members can publish the result as a badge shown in
their profile and next to their posts.

## How it works

- The page asks the official calculator (`hom.guildwars2.com/character/<name>`) for the
  character's `legacy_bits` code, server side, with a 10 minute cache per name.
- The code is decoded locally (`HomBitReader`, `HomProfile`) with the same catalog and
  point rules as the official calculator (`HomCatalog`), so nothing else is fetched.
- The page shows the title reached, the points, each monument with its dedicated
  statues, point rules and a popup of what is still missing, and the reward track.
- **Share on my Legacy profile** stores the character, its title and its points for the
  current member. The code is fetched again server side, the client never supplies them.
- Badges appear in the post author sidebar and among the profile header statistics
  (title on one line, "50 / 50" on the next), both linking back to the hall.

## Access

The page and its menu entry are limited to groups with the **user.gwHom.canView**
permission (administrators by default). Grant it to a test group first, then to
Registered Users when the page goes public.

## Requirements

- WoltLab Suite Core >= 6.0, Burning Board >= 6.0
- Outbound HTTPS from the server to hom.guildwars2.com

## Installation

```bash
./build.sh   # produces dist/com.gwlegacy.hom.tar
```

Install via **ACP -> Configuration -> Packages -> Install Package**. The menu entry
**Hall of Monuments** is added at the top level; move it under Tools & Downloads from
**ACP -> Appearance -> Menus** if preferred.

## Layout

```
package.xml                 metadata + install instructions
page.xml                    the calculator page (controller wcf\page\HomPage)
userGroupOption.xml         the user.gwHom.canView permission
menuItem.xml                header menu entry
eventListener.xml           badge preload on thread pages, profile stat item
templateListener.xml        badge markup in the post sidebar
files/acp/database/         schema (lookup cache + badges)
files/lib/system/hom/       catalog, bit reader, profile (points), lookup
files/lib/page/             the page
files/lib/data/hom/badge/   publish / unpublish AJAX action
files/lib/system/event/listener/  thread badge preload, profile stat item
templates/hom.tpl           the page
templates/__gwHomSidebarBadge.tpl  sidebar badge snippet
build.sh                    builds the installable package
```

## License

GNU Lesser General Public License. Hall of Monuments data and rules belong to ArenaNet.
