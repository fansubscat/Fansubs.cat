#!/bin/bash
orig_dir="/YOUR/ORIGINAL/FILES/DIR"
dest_dir="/YOUR/CONVERTED/FILES/DIR"
token="YOUR_TOKEN"
dest_host="your.host.xyz"

function notify_error {
	php -r "mail('YOUR_EMAIL', \"Notificació del procés d'importació de fitxers a Fansubs.cat\", \"$1\");"
}

function generate_streaming {
	original_file=$1
	video_stream=$2
	audio_stream=$3
	subtitle_stream=$4
	action_video=$5
	action_audio=$6
	output_file=$7
	
	author="Recompressió per a anime.fansubs.cat"
	title="No baixeu aquest fitxer, baixeu l'original!"
	crf_fullhd="23"
	crf_hd="21"
	crf_sd="19"
	crf_ssd="17"

	resolution=`../ffprobe -v error -select_streams v:$video_stream -show_entries stream=height -of csv=s=x:p=0 "$original_file"`

	if [ $resolution -le 360 ]
	then
		crf=$crf_ssd
	elif [ $resolution -le 480 ]
	then
		crf=$crf_sd
	elif [ $resolution -le 720 ]
	then
		crf=$crf_hd
	else
		crf=$crf_fullhd
	fi

	if [[ "$subtitle_stream" =~ ^-?[0-9]+$ ]]
	then
		if [ $subtitle_stream -eq -1 ]
		then
			filter_opts=""
		else
			filter_opts="subtitles='$original_file:si=$subtitle_stream',"
		fi
	else
		filter_opts="subtitles='$subtitle_stream',"
	fi

	if [ "$action_video" = "COPY" ]
	then
		video_opts="-c:v copy"
	else
		video_opts="-c:v libx264 -preset slower -profile:v high -level 4.1 -crf $crf"
	fi

	if [ "$action_audio" = "COPY" ]
	then
		audio_opts="-c:a copy"
	else
		audio_opts="-ac 2 -c:a aac -b:a 128k"
	fi

	if [ "$action_video" = "COPY" ]
	then
		../ffmpeg -y -i "$original_file" -map_metadata -1 -map_chapters -1 -map 0:v:$video_stream -map 0:a:$audio_stream $video_opts $audio_opts -metadata title="$title" -metadata artist="$author" -movflags faststart "$output_file"
	else
		../ffmpeg -y -i "$original_file" -map_metadata -1 -map_chapters -1 -map 0:v:$video_stream -map 0:a:$audio_stream -pix_fmt yuv420p -vf "${filter_opts}null" $video_opts $audio_opts -metadata title="$title" -metadata artist="$author" -movflags faststart "$output_file"
	fi
}

treated_link_ids=()

mega-whoami

while [ 1 ]
do
	json=`curl https://api.fansubs.cat/internal/get_unconverted_links/?token=$token 2> /dev/null`
	if [ $? -eq 0 ]
	then
		array=`echo $json | jq -c '.result []'`
		IFS=$'\n'
		for element in $array
		do
			unset IFS
			link_id=`echo $element | jq -r '.link_id'`
			url=`echo $element | jq -r '.url'`
			storage_folder=`echo $element | jq -r '.storage_folder'`
			storage_processing=`echo $element | jq -r '.storage_processing'`
			resolutionp=`echo $element | jq -r '.resolution'`
			is_extra=`echo $element | jq -r '.is_extra'`

			if [[ ! "$url" =~ ^https://mega\.nz.* ]]
			then
				if [[ ! " ${treated_link_ids[@]} " =~ " $link_id " ]]
				then
					#New - notify
					treated_link_ids+=($link_id)
					notify_error "Hi ha un enllaç que no és de MEGA pendent de conversió: id. $link_id, URL $url"
				fi
				continue
			fi

			if [ $is_extra = "true" ]
			then
				storage_folder="$storage_folder/Extres"
			fi
			
			echo "Processing link id $link_id, folder: $storage_folder, URL: $url"
			mkdir -p "$orig_dir/$storage_folder"
			mkdir -p "$dest_dir/$storage_folder"
			mkdir Temporal
			rm -rf Temporal/*
			cd Temporal
			ready=0

			while [ $ready -eq 0 ]
			do
				mega-get $url
				if [ $? -eq 0 ]
				then
					file=`ls *.mp4`
					output=`echo "$file" | sed -E "s/ \[.*\]//"`
					if [ -f "$orig_dir/$storage_folder/$file" ]
					then
						notify_error "S'ha sobreescrit el fitxer original $storage_folder/$file i se'n sobreescriurà també la versió recomprimida, si existeix."
					fi
					cp "$file" "$orig_dir/$storage_folder/"
					if [ $storage_processing -eq 0 ]
					then
						generate_streaming "$file" 0 0 -1 CONVERT COPY "$dest_dir/$storage_folder/$output"
					elif [ $storage_processing -eq 1 ]
					then
						generate_streaming "$file" 0 0 -1 CONVERT CONVERT "$dest_dir/$storage_folder/$output"
					elif [ $storage_processing -eq 2 ]
					then
						generate_streaming "$file" 0 0 -1 COPY CONVERT "$dest_dir/$storage_folder/$output"
					elif [ $storage_processing -eq 3 ]
					then
						generate_streaming "$file" 0 0 -1 COPY COPY "$dest_dir/$storage_folder/$output"
					else
						cp "$file" "$dest_dir/$storage_folder/$output"
					fi
					rsync -avzhW --chmod=u=rwX,go=rX "$dest_dir/" root@$dest_host:/home/storage/ --exclude "@eaDir" --delete
					curl --data-urlencode "original_url=$url" --data-urlencode "url=storage://$storage_folder/$output" --data-urlencode "link_id=$link_id" --data-urlencode "resolution=$resolutionp" https://api.fansubs.cat/internal/insert_converted_link/?token=$token 2> /dev/null
					ready=1
				else
					echo "Error downloading: $?, waiting 30 minutes... Now at `date -Iseconds`."
					sleep 1800
				fi
				mega-quit
				rm -rf ~/.megaCmd
			done
			cd ..
			rm -rf Temporal
			IFS=$'\n'
		done
		unset IFS
	else
		echo "Error fetching"
	fi
	sleep 60
done
