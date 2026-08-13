# GWLegacy Favor of the Gods

A WoltLab Suite box that shows the live "Favor of the Gods" status (from the gwtoolbox
Kamadan feed), with a button to get a notification when favor becomes active.

## How it works

- A cron job polls `https://kamadan.gwtoolbox.com/favor` every couple of minutes, stores
  the state, and detects the transition from inactive to active.
- The **box** displays the status; the countdown ticks client-side and the state refreshes
  from our own endpoint (no cross-origin call from the browser).
- Logged-in users can **Register notification**; when favor next becomes active, every
  subscriber gets a bell notification.

## Requirements

- WoltLab Suite Core >= 6.0

## Installation

```bash
./build.sh   # produces dist/com.gwlegacy.favor.tar
```

Install via **ACP -> Configuration -> Packages -> Install Package**, then place the
**Favor of the Gods** box where you want it under **ACP -> Appearance -> Boxes**.

## Layout

```
package.xml                 metadata + install instructions
objectType.xml              box controller + notification object type
box.xml                     the placeable box
userNotificationEvent.xml   the "favor active" notification
cronjob.xml                 the polling schedule
files/acp/database/         schema (subscribers + activations)
files/lib/data/favor/       favor activation object + subscription action
files/lib/system/box/       box controller
files/lib/system/cronjob/   the poller
files/lib/system/gwfavor/   favor state helper
files/lib/system/user/notification/  notification object/type/event
templates/boxFavor.tpl      box content + client script
build.sh                    builds the installable package
```

## License

GNU Lesser General Public License.
