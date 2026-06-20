#!/usr/bin/env bash
#
# Builds the installable WoltLab package archive for com.gwlegacy.selfreply.
#
# WoltLab package layout (plain tar, NOT gzipped):
#   com.gwlegacy.selfreply.tar
#   ├── package.xml
#   ├── eventListener.xml  templateListener.xml
#   ├── files.tar          (<- contents of ./files)
#   ├── templates.tar      (<- contents of ./templates)
#   └── acptemplates.tar   (<- contents of ./acptemplates)
#
# Usage:  ./build.sh        # output -> dist/com.gwlegacy.selfreply.tar
set -euo pipefail

cd "$(dirname "$0")"
PKG="com.gwlegacy.selfreply"
OUT="dist"
rm -rf "$OUT"
mkdir -p "$OUT"

# 1. component tars (paths relative to their source dir, so they extract at root)
tar -cf "$OUT/files.tar"        -C files        .
tar -cf "$OUT/templates.tar"    -C templates    .
tar -cf "$OUT/acptemplates.tar" -C acptemplates .

# 2. stage the archive contents
cp package.xml eventListener.xml templateListener.xml "$OUT/"

# 3. assemble the package archive
( cd "$OUT" && tar -cf "$PKG.tar" \
    package.xml \
    eventListener.xml templateListener.xml \
    files.tar templates.tar acptemplates.tar )

echo "Built $OUT/$PKG.tar"
echo "Install via ACP -> Configuration -> Packages -> Install Package."
