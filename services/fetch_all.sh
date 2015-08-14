#!/bin/bash

# Change this to your website directory if you install it on another server
ROUTE_TO_WEBSITE_DIRECTORY=/srv/tourian_data/websites/www.fansubs.cat

# CatSub
echo `date +"%Y-%m-%d %H:%M:%S"` "- Fetching CatSub..."
/usr/bin/php fetchers/catsub.php > last_fetch/catsub

if [ $? -ne 0 ]
then
	echo `date +"%Y-%m-%d %H:%M:%S"` "- Error found, aborting!"
	exit 1
fi

# Ippantekina
echo `date +"%Y-%m-%d %H:%M:%S"` "- Fetching Ippantekina..."
/usr/bin/php fetchers/ippantekina.php > last_fetch/ippantekina

if [ $? -ne 0 ]
then
        echo `date +"%Y-%m-%d %H:%M:%S"` "- Error found, aborting!"
	exit 1
fi

# LlunaPlena
echo `date +"%Y-%m-%d %H:%M:%S"` "- Fetching LlunaPlena..."
/usr/bin/php fetchers/llunaplena.php > last_fetch/llunaplena

if [ $? -ne 0 ]
then
        echo `date +"%Y-%m-%d %H:%M:%S"` "- Error found, aborting!"
	exit 1
fi

# Seireitei
echo `date +"%Y-%m-%d %H:%M:%S"` "- Fetching Seireitei..."
/usr/bin/php fetchers/seireitei.php > last_fetch/seireitei

if [ $? -ne 0 ]
then
        echo `date +"%Y-%m-%d %H:%M:%S"` "- Error found, aborting!"
	exit 1
fi

# XOP
echo `date +"%Y-%m-%d %H:%M:%S"` "- Fetching XOP..."
/usr/bin/php fetchers/xop.php > last_fetch/xop

if [ $? -ne 0 ]
then
        echo `date +"%Y-%m-%d %H:%M:%S"` "- Error found, aborting!"
	exit 1
fi

# Copy to final destination
echo `date +"%Y-%m-%d %H:%M:%S"` "- Copying to MoonMoon directory..."
cp last_fetch/* $ROUTE_TO_WEBSITE_DIRECTORY/fansubs/

# Update data
echo `date +"%Y-%m-%d %H:%M:%S"` "- Updating MoonMoon cache..."
/usr/bin/php $ROUTE_TO_WEBSITE_DIRECTORY/cron.php

exit 0

