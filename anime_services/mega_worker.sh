#!/bin/bash

echo "MEGA Worker starting up" >> mega_worker.log

while true
do
	if [ -f "/tmp/mega.request" ]
	then
		SESSION_ID=`cat /tmp/mega.request | awk -F':::' '{print $1}'`
		FOLDER=`cat /tmp/mega.request | awk -F':::' '{print $2}'`

		echo "Got request for session id '$SESSION_ID' and folder '$FOLDER'" >> mega_worker.log

		mega-whoami > /dev/null 2> /dev/null
		if [ $? -ne 57 ]
		then
			mega-logout --keep-session > /dev/null 2> /dev/null
			echo "ERROR 1" > /tmp/mega.response
			rm /tmp/mega.request
			echo "Request served with error 1" >> mega_worker.log
			continue;
		fi

		mega-login $SESSION_ID > /dev/null 2> /dev/null
		if [ $? -ne 0 ]
		then
			echo "ERROR 2" > /tmp/mega.response
			rm /tmp/mega.request
			echo "Request served with error 2" >> mega_worker.log
			continue;
		fi

		mega-cd "$FOLDER" > /dev/null 2> /dev/null
		if [ $? -ne 0 ] 
		then
			mega-logout --keep-session > /dev/null 2> /dev/null
			echo "ERROR 3" > /tmp/mega.response
			rm /tmp/mega.request
			echo "Request served with error 3" >> mega_worker.log
			continue;
		fi

		mega-export -f -a "*.mp4" 2> /dev/null | grep "Exported " | awk -F': ' '{n=split($1,a,"/"); print a[n] ":::" $2}' > /tmp/mega.temp
		if [ $? -ne 0 ] 
		then
			mega-logout --keep-session > /dev/null 2> /dev/null
			echo "ERROR 4" > /tmp/mega.response
			rm /tmp/mega.request
			echo "Request served with error 4" >> mega_worker.log
			continue;
		fi

		mega-logout --keep-session > /dev/null 2> /dev/null
		if [ $? -ne 0 ] 
		then
			echo "ERROR 4" > /tmp/mega.response
			rm /tmp/mega.request
			echo "Request served with error 5" >> mega_worker.log
			continue;
		fi

		mv /tmp/mega.temp /tmp/mega.response
		rm /tmp/mega.request
		echo "Request served with success" >> mega_worker.log
	fi

	sleep 5
done

exit 0

