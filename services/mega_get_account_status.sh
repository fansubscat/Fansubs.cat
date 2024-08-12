#!/bin/bash
SESSION_ID=$1

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

mega-df | grep "USED STORAGE" | awk -F ' ' '{print $3":::"$6}'
if [ $? -ne 0 ]
then
	exit 7
fi

mega-logout --keep-session > /dev/null 2> /dev/null
if [ $? -ne 0 ]
then
	exit 5
fi

exit 0

