#!/bin/bash
dest_dir="/YOUR/CONVERTED/FILES/DIR"
token="YOUR_TOKEN"

json=`curl https://api.fansubs.cat/internal/get_converted_links/?token=$token\\&from_id=0 2> /dev/null`
segment_seconds=15 #Time to check for bitrate in segments
exceed_normal=0
if [ $? -eq 0 ]
then
	array=`echo $json | jq -c '.result|sort_by(.url) []'`
	IFS=$'\n'
	for element in $array
	do
		unset IFS
		url=`echo $element | jq -r '.url' | sed -E "s/^storage:\\\/\\\///"`
		#resolution=`./ffprobe -v error -select_streams v:0 -show_entries stream=height -of csv=s=x:p=0 "$dest_dir/$url"`
		echo "Analyzing file '$url'"

		#if [ $resolution -le 360 ]
		#then
		#	expected_bitrate=$((192*1024*segment_seconds)) #1536 kbps = 192 kBps -> 192x1024 x segment_seconds
		#elif [ $resolution -le 480 ]
		#then
		#	expected_bitrate=$((256*1024*segment_seconds)) #2048 kbps = 256 kBps -> 256x1024 x segment_seconds
		#elif [ $resolution -le 720 ]
		#then
		#	expected_bitrate=$((512*1024*segment_seconds)) #4096 kbps = 512 kBps -> 512x1024 x segment_seconds
		#else
		#	expected_bitrate=$((1024*1024*segment_seconds)) #8192 kbps = 1024 kBps -> 1024x1024 x segment_seconds
		#fi

		#Ignore resolutions or we will have to rebuild almost everything: just make it stay below 1024 kBps
		expected_bitrate=$((1024*1024*segment_seconds)) #8192 kbps = 1024 kBps -> 1024x1024 x segment_seconds

		mkdir tmpbitrateanalysis
		ffmpeg -i "$dest_dir/$url" -map 0:v -c copy -f segment -segment_time $segment_seconds -break_non_keyframes 1 tmpbitrateanalysis/seg%d.264 2>/dev/null >/dev/null
		max_bitrate=`ls -lS tmpbitrateanalysis | head -n2 | tail -n1 | awk -F' ' '{print $5}'`

		if [ $max_bitrate -gt $expected_bitrate ]
		then
			conv_expected_bitrate=$((expected_bitrate/1024/segment_seconds))
			conv_max_bitrate=$((max_bitrate/1024/segment_seconds))
			echo "WARNING: File has a bitrate greater than the max allowed bitrate for at least ${segment_seconds}s ($conv_max_bitrate>$conv_expected_bitrate): «$url»"
			exceed_normal=$((exceed_normal+1))
		fi
		rm -rf tmpbitrateanalysis
		
		IFS=$'\n'
	done
	unset IFS
else
	echo "Error fetching"
fi
echo "Total files to be fixed: $exceed_normal"
exit 0
