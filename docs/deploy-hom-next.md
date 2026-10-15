# Hall of Monuments: install on next

Package: `plugin_installation/com.gwlegacy.hom.tar` (GWLegacy Hall of Monuments 1.0.2).
If an older version is installed, upload the same file: the installer offers an update.
Everything is done from the ACP of next.guildwarslegacy.com. About 5 minutes.

## What it adds

- A page **Hall of Monuments** (`/hom/`): type a Guild Wars character name and read its
  hall: the title reached (for example "Champion of the Gods"), the points out of 50, a
  monument by monument view with the dedicated statues, the point rules and a popup
  listing what is still missing, and the Guild Wars 2 rewards and titles unlocked.
- A header menu entry **Hall of Monuments**, top level by default.
- For logged-in members, a **Share on my Legacy profile** button under the title. The
  badge (the title reached, then "50 / 50" on the next line) then shows in the author
  sidebar of their posts and among their profile statistics, linking back to the hall.
  Members can update or remove it from the same page.
- Two tables: `wcf1_gw_hom_lookup` (10 minute cache of the official answers) and
  `wcf1_gw_hom_badge` (one row per member who published a badge).

Data comes from the official calculator (hom.guildwars2.com), fetched by the server, so
the server needs outbound HTTPS to that host. Nothing is fetched by the browser.

## If the 1.0.1 update failed on next

The 1.0.1 package had a broken uninstall entry and stopped the installer with
"Undefined array key environment". Before installing 1.0.2:

1. ACP -> Configuration -> Packages. If a pending installation is listed at the top
   (queue 109, "GWLegacy Hall of Monuments"), cancel it.
2. Then install 1.0.2 as below. It re-applies every step, nothing is left half done.

## Install

1. ACP -> Configuration -> Packages -> Install Package.
2. Upload `com.gwlegacy.hom.tar`, follow the installer. No options to fill in.
3. Done. The page and the menu entry are live immediately.

## Who can see the page

The page is off for everyone except administrators until a group is allowed. The menu
entry only shows to members who can open the page. To let a few people test it:

1. ACP -> Users -> User Groups -> Add User Group. Name it **HoM testers**, leave the
   rest as is, save.
2. Edit that group. In the **User** tab, section **Profile**, tick
   **user.gwHom.canView** (the option shows under its internal name), save.
3. ACP -> Users -> List Users: edit each tester and add the **HoM testers** group.
   They see the Hall of Monuments entry in the header on their next page load.

To open it to everybody later, tick the same option on the **Registered Users** group
(and on **Everyone** if guests should see it too). The testers group can then be deleted.

## Configure (optional)

- ACP -> Appearance -> Menus -> Main Menu: drag **Hall of Monuments** under
  **Tools & Downloads** if you prefer it there, or reorder it.
- There are no other settings.

## Check

1. Open the **Hall of Monuments** page from the header. Type a character name that has
   statues dedicated, submit. The page shows the title and total out of 50, the five
   monuments with their statues and rules, and the reward track. Click "N missing" on a
   monument: a popup lists what is not dedicated.
2. Type a misspelled name: the page says no character of that name has a published hall.
3. Logged in, click **Share on my Legacy profile**: the page reloads with the button
   now reading "Update my Legacy profile". Open one of your posts: the sidebar shows
   "Hall of Monuments" with the title and points. Open your profile: a "Hall of Monuments"
   stat sits next to Posts and Reactions with the same title and points.
4. Click **Remove from my profile**: both disappear.

If the page shows "The Hall of Monuments service did not answer", the server cannot
reach hom.guildwars2.com (firewall or the service is down). Nothing else breaks.

## Rollback

ACP -> Configuration -> Packages -> Uninstall. The two tables, the page, the menu entry
and the listeners are removed. Posts and profiles are untouched.
