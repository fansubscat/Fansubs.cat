#!/bin/bash
dest_dir="/YOUR/CONVERTED/FILES/DIR"
token="YOUR_TOKEN"

json=`curl https://api.fansubs.cat/internal/get_converted_links/?token=$token 2> /dev/null`
if [ $? -eq 0 ]
then
	array=`echo $json | jq -c '.result|sort_by(.url) []'`
	IFS=$'\n'
	mkdir thumbnails
	cd thumbnails
	for element in $array
	do
		unset IFS
		link_id=`echo $element | jq -r '.link_id'`
		url=`echo $element | jq -r '.url' | sed -E "s/^storage:\\\/\\\///"`
		duration=`../ffprobe -v error -select_streams v:0 -show_entries stream=duration -of csv=s=x:p=0 "$dest_dir/$url" | awk -F'.' '{print $1}'`
		echo "Regenerating thumbnail for link id $link_id ($dest_dir/$url)..."
		../ffmpeg -i "$dest_dir/$url" -ss $(((duration)/10)) -vframes 1 -filter:v scale="-1:240" thumbnail_$link_id.jpg 2> /dev/null
		curl -F "thumbnail=@thumbnail_$link_id.jpg" -F "link_id=$link_id" https://api.fansubs.cat/internal/change_link_thumbnail/?token=$token 2> /dev/null
		#rm thumbnail_$link_id.jpg
		IFS=$'\n'
	done
	cd ..
	unset IFS
else
	echo "Error fetching"
fi

exit 0
