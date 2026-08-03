# GWLegacy Upvote and Thanks

A WoltLab Suite (Burning Board) plugin that adds an upvote button to thread replies and
an option to lock the first post of a thread. Every feature is opt-in per board.

## Features

Enabled per board (checkboxes in the board's ACP edit form):

- **Upvotes on replies.** A thumbs-up button appears at the bottom of each reply. Any
  logged-in user may upvote or remove their upvote by clicking again, except the thread
  author and the reply's own author. The first post is never upvotable. The count is
  visible to everyone, including guests.
- **Thanks on replies.** The thread author gets a thanks button on replies made to them
  (not their own, not the first post). The thanked state is shown to everyone.
- **Lock the first post.** Once posted, the first post of a thread on this board can no
  longer be edited, moderators excepted. Enforced both when opening the inline editor and
  when saving.

## Requirements

- WoltLab Suite Core >= 6.0
- WoltLab Burning Board >= 6.0

(Developed and tested against WBB 6.2.)

## Installation

```bash
./build.sh   # produces dist/com.gwlegacy.upvotethanks.tar
```

Install via **ACP -> Configuration -> Packages -> Install Package**.

## Layout

```
package.xml                 package metadata + install instructions
eventListener.xml           behaviour hooks
templateListener.xml        UI injection points
files/acp/database/         schema (upvote table + board columns)
files/lib/data/post/upvote/ upvote toggle AJAX action
files/lib/system/event/     event listeners
templates/                  reply upvote button
acptemplates/               board ACP form fragment
build.sh                    builds the installable package
```

## License

GNU Lesser General Public License.
