#!/bin/bash
dest_dir="/YOUR/CONVERTED/FILES/DIR"
token="YOUR_TOKEN"

json=`curl https://api.fansubs.cat/internal/get_converted_links/?token=$token 2> /dev/null`
if [ $? -eq 0 ]
then
	array=`echo $json | jq -c '.result|sort_by(.url) []'`
	IFS=$'\n'
	for element in $array
	do
		unset IFS
		link_id=`echo $element | jq -r '.link_id'`
		url=`echo $element | jq -r '.url' | sed -E "s/^storage:\\\/\\\///"`
		resolutionp=`echo $element | jq -r '.resolution' | sed -E "s/720p/1x720/" | sed -E "s/480p/1x480/" | sed -E "s/1080p/1x1080/" | sed -E "s/360p/1x360/" | sed -E "s/540p/1x540/" | sed -E "s/576p/1x576/" | sed -E "s/240p/1x240/" | awk -F'x' '{print $2}'`
		durationp=`echo $element | jq -r '.duration_in_minutes'`
		is_extra=`echo $element | jq -r '.is_extra'`
		is_unique=`echo $element | jq -r '.is_unique'`
		resolutionr=`./ffprobe -v error -select_streams v:0 -show_entries stream=height -of csv=s=x:p=0 "$dest_dir/$url"`
		vcodec=`./ffprobe -v error -select_streams v:0 -show_entries stream=codec_name -of csv=s=x:p=0 "$dest_dir/$url"`
		acodec=`./ffprobe -v error -select_streams a:0 -show_entries stream=codec_name -of csv=s=x:p=0 "$dest_dir/$url"`
		artist=`./ffprobe -v error -show_entries format_tags=artist -of csv=s=x:p=0 "$dest_dir/$url"`

		if [ ! "$vcodec" == "h264" ]
		then
			echo "Video format is not H264 for file '$url': WEB:h264!=HDD:$vcodec";
		fi

		if [ ! "$acodec" == "aac" ]
		then
			echo "Audio format is not AAC for file '$url': WEB:aac!=HDD:$acodec";
		fi

		if [ ! "$artist" == "Recompressió per a anime.fansubs.cat" ]
		then
			echo "Metadata is not correct for file '$url': WEB:Recompressió per a anime.fansubs.cat!=HDD:$artist";
		fi

		if [ ! "$resolutionp" == "$resolutionr" ]
		then
			echo "Resolution DOES NOT MATCH for file '$url': WEB:$resolutionp!=HDD:$resolutionr";
		fi

		durationr=`./ffprobe -v error -select_streams v:0 -show_entries stream=duration -of csv=s=x:p=0 "$dest_dir/$url" | awk -F'.' '{print $1}'`
		durationr=$(((durationr+30)/60))

		if [ $is_extra = "false" ]
		then
			durationd=$((durationp-durationr))

			if [ $durationd -ne 0 ]
			then
				echo "Duration DOES NOT MATCH for file '$url': WEB:$durationp!=HDD:$durationr";
				if [ $is_unique = "true" ]
				then
					curl --data-urlencode "duration=$durationr" --data-urlencode "link_id=$link_id" https://api.fansubs.cat/internal/change_link_episode_duration/?token=$token 2> /dev/null
					echo "Setting duration to $durationr for link id $link_id"
				else
					echo "WARNING! Check duration for link id $link_id! It has multiple versions!"
				fi
			fi
		elif [ $durationr -ge 10 ]
		then
			echo "WARNING! Extra '$url' is more than 10 minutes long: are you sure this is an extra?"
		fi
		
		IFS=$'\n'
	done
	unset IFS
else
	echo "Error fetching"
fi

exit 0
