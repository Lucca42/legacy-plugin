#!/usr/bin/env bash
#
# Builds the installable WoltLab package archive for com.gwlegacy.favor.
#
#   com.gwlegacy.favor.tar
#   ├── package.xml
#   ├── objectType.xml  box.xml  userNotificationEvent.xml  cronjob.xml
#   ├── files.tar          (<- contents of ./files)
#   └── templates.tar      (<- contents of ./templates)
#
# Usage:  ./build.sh        # output -> dist/com.gwlegacy.favor.tar
set -euo pipefail

cd "$(dirname "$0")"
PKG="com.gwlegacy.favor"
OUT="dist"
rm -rf "$OUT"
mkdir -p "$OUT"

tar -cf "$OUT/files.tar"     -C files     .
tar -cf "$OUT/templates.tar" -C templates .

cp package.xml objectType.xml box.xml userNotificationEvent.xml cronjob.xml "$OUT/"

( cd "$OUT" && tar -cf "$PKG.tar" \
    package.xml \
    objectType.xml box.xml userNotificationEvent.xml cronjob.xml \
    files.tar templates.tar )

echo "Built $OUT/$PKG.tar"
echo "Install via ACP -> Configuration -> Packages -> Install Package."
