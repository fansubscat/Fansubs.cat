#!/bin/bash
dest_dir="/YOUR/CONVERTED/FILES/DI"
token="YOUR_TOKEN"

#This script requires having a "thumbnails" directory with all existing thumbnails

json=`curl https://api.fansubs.cat/internal/get_converted_links/?token=$token 2> /dev/null`
if [ $? -eq 0 ]
then
	array=`echo $json | jq -c '.result|sort_by(.url) []'`
	IFS=$'\n'
	cd thumbnails
	for element in $array
	do
		unset IFS
		file_id=`echo $element | jq -r '.file_id'`
		if [[ ! -f "$file_id.jpg" ]]
		then
			url=`echo $element | jq -r '.url' | sed -E "s/^storage:\\\/\\\///"`
			if [[ -f "$dest_dir/$url" ]]
			then
				duration=`../ffprobe -v error -select_streams v:0 -show_entries stream=duration -of csv=s=x:p=0 "$dest_dir/$url" | awk -F'.' '{print $1}'`
				echo "Regenerating thumbnail for file id $file_id ($dest_dir/$url)..."
				../ffmpeg -i "$dest_dir/$url" -ss $(((duration)/6)) -vframes 1 -filter:v scale="-1:240" $file_id.jpg 2> /dev/null
				curl -F "thumbnail=@$file_id.jpg" -F "file_id=$file_id" https://api.fansubs.cat/internal/change_file_thumbnail/?token=$token 2> /dev/null
				#rm $file_id.jpg
			else
				echo "Not regenerating thumbnail for file id $file_id ($dest_dir/$url) because it doesn't exist!"
			fi
		fi
		IFS=$'\n'
	done
	cd ..
	unset IFS
else
	echo "Error fetching"
fi

exit 0
