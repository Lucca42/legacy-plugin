#!/usr/bin/env bash
#
# Builds the installable WoltLab package archive for com.gwlegacy.upvotethanks.
#
#   com.gwlegacy.upvotethanks.tar
#   ├── package.xml
#   ├── eventListener.xml  templateListener.xml
#   ├── files.tar          (<- contents of ./files)
#   ├── templates.tar      (<- contents of ./templates)
#   └── acptemplates.tar   (<- contents of ./acptemplates)
#
# Usage:  ./build.sh        # output -> dist/com.gwlegacy.upvotethanks.tar
set -euo pipefail

cd "$(dirname "$0")"
PKG="com.gwlegacy.upvotethanks"
OUT="dist"
rm -rf "$OUT"
mkdir -p "$OUT"

tar -cf "$OUT/files.tar"        -C files        .
tar -cf "$OUT/templates.tar"    -C templates    .
tar -cf "$OUT/acptemplates.tar" -C acptemplates .

cp package.xml eventListener.xml templateListener.xml "$OUT/"

( cd "$OUT" && tar -cf "$PKG.tar" \
    package.xml \
    eventListener.xml templateListener.xml \
    files.tar templates.tar acptemplates.tar )

echo "Built $OUT/$PKG.tar"
echo "Install via ACP -> Configuration -> Packages -> Install Package."
