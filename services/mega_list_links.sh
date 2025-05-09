#!/bin/bash
SESSION_ID=$1
FOLDER=$2

mega-whoami > /dev/null 2> /dev/null
if [ $? -ne 57 ]
then
	mega-logout --keep-session > /dev/null 2> /dev/null
	exit 1
fi

mega-login $SESSION_ID > /dev/null 2> /dev/null
if [ $? -ne 0 ]
then
	mega-logout --keep-session > /dev/null 2> /dev/null
	exit 2
fi

mega-reload > /dev/null 2> /dev/null
if [ $? -ne 0 ]
then
	exit 6
fi

mega-cd "$FOLDER" > /dev/null 2> /dev/null
if [ $? -ne 0 ]
then
	exit 3
fi

mega-export "*.mp4" 2> /dev/null | grep "shared as exported" | awk -F'.mp4 ' '{n=split($2,a,")"); print $1 ".mp4:::" a[1]}' | awk -F': ' '{n=split($1,a,"/"); print a[n] ":::" $2}' | awk -F' AuthKey' '{print $1}' | awk -F':::' '{print $1 ":::" $3}' | sort
if [ $? -ne 0 ]
then
	exit 4
fi

mega-export -f -a "*.mp4" 2> /dev/null | grep "Exported " | awk -F': ' '{n=split($1,a,"/"); print a[n] ":::" $2}' | sort
if [ $? -ne 0 ]
then
	exit 4
fi

mega-logout --keep-session > /dev/null 2> /dev/null
if [ $? -ne 0 ]
then
	exit 5
fi

exit 0

