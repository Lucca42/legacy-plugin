#!/usr/bin/env bash
#
# Builds the installable WoltLab package archive for com.gwlegacy.hom.
#
#   com.gwlegacy.hom.tar
#   ├── package.xml
#   ├── page.xml  menuItem.xml  eventListener.xml  templateListener.xml  userGroupOption.xml
#   ├── files.tar          (<- contents of ./files)
#   └── templates.tar      (<- contents of ./templates)
#
# Usage:  ./build.sh        # output -> dist/com.gwlegacy.hom.tar
set -euo pipefail

cd "$(dirname "$0")"
PKG="com.gwlegacy.hom"
OUT="dist"
rm -rf "$OUT"
mkdir -p "$OUT"

tar -cf "$OUT/files.tar"     -C files     .
tar -cf "$OUT/templates.tar" -C templates .

cp package.xml page.xml menuItem.xml eventListener.xml templateListener.xml userGroupOption.xml "$OUT/"

( cd "$OUT" && tar -cf "$PKG.tar" \
    package.xml \
    page.xml menuItem.xml eventListener.xml templateListener.xml userGroupOption.xml \
    files.tar templates.tar )

echo "Built $OUT/$PKG.tar"
echo "Install via ACP -> Configuration -> Packages -> Install Package."
