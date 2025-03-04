#!/bin/bash

echo "MEGA Worker starting up" >> mega_worker.log

while true
do
	if [ -f "/srv/fansubscat/temporary/mega.request" ]
	then
		SESSION_ID=`cat /srv/fansubscat/temporary/mega.request | awk -F':::' '{print $1}'`
		FOLDER=`cat /srv/fansubscat/temporary/mega.request | awk -F':::' '{print $2}'`

		echo "Got request for session id '$SESSION_ID' and folder '$FOLDER'" >> /srv/fansubscat/temporary/mega_worker.log
		
		rm /srv/fansubscat/temporary/mega.temp

		mega-whoami > /dev/null 2> /dev/null
		if [ $? -ne 57 ]
		then
			mega-logout --keep-session > /dev/null 2> /dev/null
			echo "ERROR 1" > /srv/fansubscat/temporary/mega.response
			rm /srv/fansubscat/temporary/mega.request
			echo "Request served with error 1" >> /srv/fansubscat/temporary/mega_worker.log
			continue;
		fi

		mega-login $SESSION_ID > /dev/null 2> /dev/null
		if [ $? -ne 0 ]
		then
			echo "ERROR 2" > /srv/fansubscat/temporary/mega.response
			rm /srv/fansubscat/temporary/mega.request
			echo "Request served with error 2" >> /srv/fansubscat/temporary/mega_worker.log
			continue;
		fi


		mega-reload > /dev/null 2> /dev/null
		if [ $? -ne 0 ]
		then
			echo "ERROR 6" > /srv/fansubscat/temporary/mega.response
			rm /srv/fansubscat/temporary/mega.request
			echo "Request served with error 6" >> /srv/fansubscat/temporary/mega_worker.log
			continue;
		fi

		mega-cd "$FOLDER" > /dev/null 2> /dev/null
		if [ $? -ne 0 ] 
		then
			mega-logout --keep-session > /dev/null 2> /dev/null
			echo "ERROR 3" > /srv/fansubscat/temporary/mega.response
			rm /srv/fansubscat/temporary/mega.request
			echo "Request served with error 3" >> /srv/fansubscat/temporary/mega_worker.log
			continue;
		fi

		mega-export "*.mp4" 2> /dev/null | grep "shared as exported" | awk -F'.mp4 ' '{n=split($2,a,")"); print $1 ".mp4:::" a[1]}' | awk -F': ' '{n=split($1,a,"/"); print a[n] ":::" $2}' | awk -F' AuthKey' '{print $1}' | awk -F':::' '{print $1 ":::" $3}' | sort > /srv/fansubscat/temporary/mega.temp
		if [ $? -ne 0 ] 
		then
			mega-logout --keep-session > /dev/null 2> /dev/null
			echo "ERROR 4" > /srv/fansubscat/temporary/mega.response
			rm /srv/fansubscat/temporary/mega.request
			echo "Request served with error 4" >> /srv/fansubscat/temporary/mega_worker.log
			continue;
		fi

		mega-export -f -a "*.mp4" 2> /dev/null | grep "Exported " | awk -F': ' '{n=split($1,a,"/"); print a[n] ":::" $2}' | sort >> /srv/fansubscat/temporary/mega.temp
		if [ $? -ne 0 ] 
		then
			mega-logout --keep-session > /dev/null 2> /dev/null
			echo "ERROR 4" > /srv/fansubscat/temporary/mega.response
			rm /srv/fansubscat/temporary/mega.request
			echo "Request served with error 4" >> /srv/fansubscat/temporary/mega_worker.log
			continue;
		fi

		mega-logout --keep-session > /dev/null 2> /dev/null
		if [ $? -ne 0 ] 
		then
			echo "ERROR 4" > /srv/fansubscat/temporary/mega.response
			rm /srv/fansubscat/temporary/mega.request
			echo "Request served with error 5" >> /srv/fansubscat/temporary/mega_worker.log
			continue;
		fi

		cat /srv/fansubscat/temporary/mega.temp | sort > /srv/fansubscat/temporary/mega.response
		rm /srv/fansubscat/temporary/mega.temp
		rm /srv/fansubscat/temporary/mega.request
		echo "Request served with success" >> /srv/fansubscat/temporary/mega_worker.log
	fi

	sleep 5
done

exit 0

