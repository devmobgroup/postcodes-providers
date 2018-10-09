#!/usr/bin/env bash

set -e

if (( "$#" != 1 ))
then
    echo "Tag has to be provided"

    exit 1
fi

CURRENT_BRANCH="master"

for REMOTE in "postcodes-api-postcode" "postcodes-postcode-api-nu"
do
    echo ""
    echo ""
    echo "Releasing $REMOTE"

    TMP_DIR="/tmp/postcodes-providers-split"
    REMOTE_URL="git@github.com:devmobgroup/$REMOTE.git"

    rm -rf $TMP_DIR
    mkdir $TMP_DIR

    (
        cd $TMP_DIR

        git clone $REMOTE_URL .
        git checkout "$CURRENT_BRANCH"

        git tag $1 -m "Release $1"
        git push origin --tags
    )
done
