#!/bin/bash
token="YOUR_TOKEN"
dest_host="your.host.xyz"
base_dest_dir="YOUR_DESTINATION_DIRECTORY"
sender_email="your@sender.email"

function notify_error {
	php -r "mail('YOUR_EMAIL', \"Notificació del procés d'importació de fitxers a Fansubs.cat\", \"$1\", \"From: $sender_email\");"
}

function generate_streaming {
	original_file=$1
	video_stream=$2
	audio_stream=$3
	subtitle_stream=$4 #Unused in this script
	action_video=$5
	action_audio=$6
	output_file=$7
	
	author="Recompressió per a Fansubs.cat"
	title="No baixeu aquest fitxer, baixeu l'original!"
	script_id="AutomaticBatchProcessor"
	crf_fullhd="23"
	crf_hd="21"
	crf_sd="19"
	crf_ssd="17"
	max_bitrate_fullhd="8192k"
	max_bitrate_hd="4096k"
	max_bitrate_sd="2048k"
	max_bitrate_ssd="1536k"

	resolution=`../ffprobe -v error -select_streams v:$video_stream -show_entries stream=height -of csv=s=x:p=0 "$original_file"`

	if [ $resolution -le 360 ]
	then
		crf=$crf_ssd
		max_bitrate=$max_bitrate_ssd
	elif [ $resolution -le 480 ]
	then
		crf=$crf_sd
		max_bitrate=$max_bitrate_sd
	elif [ $resolution -le 720 ]
	then
		crf=$crf_hd
		max_bitrate=$max_bitrate_hd
	else
		crf=$crf_fullhd
		max_bitrate=$max_bitrate_fullhd
	fi

	if [ "$action_video" = "COPY" ]
	then
		video_opts="-c:v copy"
	else
		video_opts="-c:v libx264 -preset slower -profile:v high -level 4.1 -crf $crf -maxrate $max_bitrate -bufsize $max_bitrate"
	fi

	if [ "$action_audio" = "COPY" ]
	then
		audio_opts="-c:a copy"
	else
		audio_opts="-ac 2 -c:a aac -b:a 128k"
	fi

	comment="Codificador: $script_id"$'\n'"Paràmetres: $video_opts $audio_opts"

	if [ "$action_video" = "COPY" ]
	then
		../ffmpeg -y -i "$original_file" -map_metadata -1 -map_chapters -1 -map 0:v:$video_stream -map 0:a:$audio_stream $video_opts $audio_opts -metadata title="$title" -metadata artist="$author" -metadata comment="$comment" -movflags faststart "$output_file"
	else
		../ffmpeg -y -i "$original_file" -map_metadata -1 -map_chapters -1 -map 0:v:$video_stream -map 0:a:$audio_stream -pix_fmt yuv420p $video_opts $audio_opts -metadata title="$title" -metadata artist="$author" -metadata comment="$comment" -movflags faststart "$output_file"
	fi
}

treated_file_ids=()

mega-whoami

while [ 1 ]
do
	json=`curl https://api.fansubs.cat/internal/get_unconverted_links/?token=$token 2> /dev/null`
	if [ $? -eq 0 ]
	then
		array=`echo $json | jq -c '.result|sort_by(.file_id) []'`
		IFS=$'\n'
		for element in $array
		do
			unset IFS
			file_id=`echo $element | jq -r '.file_id'`
			type=`echo $element | jq -r '.type'`
			url=`echo $element | jq -r '.url'`
			storage_folder=`echo $element | jq -r '.storage_folder'`
			storage_processing=`echo $element | jq -r '.storage_processing'`
			resolutionp=`echo $element | jq -r '.resolution'`
			is_extra=`echo $element | jq -r '.is_extra'`

			if [[ ! "$url" =~ ^https://mega\.nz.* ]]
			then
				if [[ ! " ${treated_file_ids[@]} " =~ " $file_id " ]]
				then
					#New - notify
					treated_file_ids+=($file_id)
					notify_error "Hi ha un enllaç que no és de MEGA pendent de conversió: id. $file_id, URL $url"
				fi
				continue
			fi

			folder_type="Anime"
			orig_dir="/volume1/Fansubs - Anime"
			dest_dir="$base_dest_dir/Anime"

			if [ $type = "liveaction" ]
			then
				folder_type="Acció real"
				orig_dir="/volume1/Fansubs - Acció real"
				dest_dir="$base_dest_dir/Acció real"
			fi

			if [ $is_extra = "true" ]
			then
				storage_folder="$storage_folder/Extres"
			fi
			
			echo "Processing file id $file_id, folder: $storage_folder, URL: $url"
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

					# Copy to original folder, unless the method is only copy to storage
					if [ $storage_processing -ne 5 ]
					then
						if [ -f "$orig_dir/$storage_folder/$file" ]
						then
							notify_error "S'ha sobreescrit el fitxer original $folder_type/$storage_folder/$file i se'n sobreescriurà també la versió recomprimida, si existeix."
						fi
						cp "$file" "$orig_dir/$storage_folder/"
					fi

					# Extract thumbnail
					duration=`../ffprobe -v error -select_streams v:0 -show_entries stream=duration -of csv=s=x:p=0 "$file" | awk -F'.' '{print $1}'`
					../ffmpeg -i "$file" -ss $(((duration)/6)) -vframes 1 -filter:v scale="-1:240" thumbnail_$file_id.jpg
					curl -F "thumbnail=@thumbnail_$file_id.jpg" -F "file_id=$file_id" https://api.fansubs.cat/internal/change_file_thumbnail/?token=$token 2> /dev/null

					# Update duration
					curl --data-urlencode "duration=$duration" --data-urlencode "file_id=$file_id" https://api.fansubs.cat/internal/change_file_duration/?token=$token 2> /dev/null

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
					rsync -avzhW --chmod=u=rwX,go=rX "$base_dest_dir/" root@$dest_host:/home/storage/ --exclude "@eaDir" --exclude "Manga" --exclude "ZZZ_INTERNAL" --delete

					# Insert converted file
					curl --data-urlencode "original_url=$url" --data-urlencode "url=storage://$folder_type/$storage_folder/$output" --data-urlencode "file_id=$file_id" --data-urlencode "resolution=$resolutionp" https://api.fansubs.cat/internal/insert_converted_link/?token=$token 2> /dev/null
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
