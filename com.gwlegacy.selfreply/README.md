# GWLegacy Trading Board Tools

A WoltLab Suite (Burning Board) plugin that adds a small set of per-board tools for
trading boards. Every feature is opt-in per board, so boards that are not enabled are
left untouched.

## Features

Enabled per board (a checkbox in the board's ACP edit form):

- **Bump rate limit.** A thread author may resurface their own thread only once per a
  configurable per-board delay (default 24h). This applies to both ways of resurfacing:
  posting an update reply, or pressing the bump button. Both share the same window, so an
  author cannot post and then immediately bump on top of it. Replies from other members
  never count against the author, and other members reply normally.
- **Bump button.** Instead of posting, the author can press a *Bump thread* button that
  refreshes the thread's activity without adding a message. It is disabled until the
  interval has elapsed.
- **Squash.** The author can hide all current replies in one click (the opening post
  stays visible); only later replies are shown afterwards. Anyone, including guests, can
  reveal the hidden replies with a *Show previous replies* button. Nothing is deleted.

## Requirements

- WoltLab Suite Core ≥ 6.0
- WoltLab Burning Board ≥ 6.0

(Developed and tested against WBB 6.2.)

## Installation

Build the package, then install it via **ACP → Configuration → Packages → Install
Package**.

```bash
./build.sh   # produces dist/com.gwlegacy.selfreply.tar
```

## Configuration

Edit a board in the ACP and enable **No self-reply & daily bump**, then set the
**Minimum delay between bumps**. The squash and bump buttons appear on threads of that
board automatically.

## Layout

```
package.xml                 package metadata + install instructions
eventListener.xml           behaviour hooks
templateListener.xml        UI injection points
files/acp/database/         schema (adds columns to the board/thread tables)
files/lib/data/thread/      bump + squash AJAX actions
files/lib/system/event/     event listeners
templates/                  thread-page UI fragments
acptemplates/               board ACP form fragment
build.sh                    builds the installable package
```

## License

GNU Lesser General Public License.
