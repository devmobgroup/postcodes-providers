#!/usr/bin/env bash

set -e
set -x

CURRENT_BRANCH="master"

function remote()
{
    git remote add $1 "git@github.com:devmobgroup/$1.git" || true
}

function split()
{
    SHA1=`./bin/splitsh-lite --prefix=$1`
    git push $2 "$SHA1:refs/heads/$CURRENT_BRANCH" -f
}

git pull origin $CURRENT_BRANCH

remote "postcodes-api-postcode"
remote "postcodes-postcode-api-nu"

split 'src/ApiPostcode' "postcodes-api-postcode"
split 'src/PostcodeApiNu' "postcodes-postcode-api-nu"
