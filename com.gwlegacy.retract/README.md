# GWLegacy Retract

A WoltLab Suite (Burning Board) plugin that lets a reply's author retract it, and can
lock replies against editing. Every feature is opt-in per board.

## Features

Enabled per board (checkboxes in the board's ACP edit form):

- **Retract button on replies.** The reply's own author gets a *Retract* button. Clicking
  it posts a new reply that quotes the original and marks it "Retracted" in bold red.
  Nothing is deleted; the original stays in place, now clearly retracted. The first post
  cannot be retracted.
- **Lock replies against editing.** Once posted, replies on this board can no longer be
  edited, moderators excepted. Because edits are locked, authors are expected to reread
  before posting; retracting is the supported way to walk something back.

## Requirements

- WoltLab Suite Core >= 6.0
- WoltLab Burning Board >= 6.0

(Developed and tested against WBB 6.2.)

## Installation

```bash
./build.sh   # produces dist/com.gwlegacy.retract.tar
```

Install via **ACP -> Configuration -> Packages -> Install Package**.

## Layout

```
package.xml                 package metadata + install instructions
eventListener.xml           behaviour hooks
templateListener.xml        UI injection points
files/acp/database/         schema (board columns)
files/lib/data/post/retract/ retract AJAX action (posts the quoting reply)
files/lib/system/event/     event listeners
templates/                  reply retract button
acptemplates/               board ACP form fragment
build.sh                    builds the installable package
```

## License

GNU Lesser General Public License.
