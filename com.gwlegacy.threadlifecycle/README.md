# GWLegacy Thread Lifecycle

A WoltLab Suite (Burning Board) plugin that manages the lifecycle of threads on trading
boards. Every feature is opt-in per board.

## Features

Enabled per board (a checkbox in the board's ACP edit form):

- **Self-close.** The thread author gets a *Close thread* button once the thread has
  reached a configurable minimum age. This gives sellers a clean way to end a listing
  without the delete-and-recreate churn. Set the delay to 0 to disable the button.
- **Auto-close.** A thread with no activity for longer than a configurable delay is
  closed automatically by an hourly cron job. Set the delay to 0 to disable it.

Closing a thread only sets it as closed (no new replies); nothing is deleted, and a
moderator can reopen it.

## Requirements

- WoltLab Suite Core >= 6.0
- WoltLab Burning Board >= 6.0

(Developed and tested against WBB 6.2.)

## Installation

```bash
./build.sh   # produces dist/com.gwlegacy.threadlifecycle.tar
```

Install via **ACP -> Configuration -> Packages -> Install Package**.

## Configuration

Edit a board in the ACP, enable **Thread lifecycle**, then set:

- **Self-close after (seconds)** the minimum thread age before the author may close it.
- **Auto-close after inactivity (seconds)** how long a thread may stay inactive before
  it is closed (e.g. 7776000 for 90 days).

## Layout

```
package.xml                 package metadata + install instructions
cronjob.xml                 hourly auto-close schedule
eventListener.xml           behaviour hooks
templateListener.xml        UI injection points
files/acp/database/         schema (adds columns to the board table)
files/lib/data/thread/      self-close AJAX action
files/lib/system/cronjob/   auto-close job
files/lib/system/event/     event listeners
templates/                  thread-page UI fragment
acptemplates/               board ACP form fragment
build.sh                    builds the installable package
```

## License

GNU Lesser General Public License.
