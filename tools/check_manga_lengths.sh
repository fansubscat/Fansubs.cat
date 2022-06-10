#!/bin/bash
storage_dir="/srv/websites/static.fansubs.cat/storage/"
token="YOUR_TOKEN"

json=`curl https://api.fansubs.cat/internal/get_manga_files/?token=$token 2> /dev/null`
if [ $? -eq 0 ]
then
	array=`echo $json | jq -c '.result []'`
	IFS=$'\n'
	for element in $array
	do
		unset IFS
		file_id=`echo $element | jq -r '.file_id'`
		series=`echo $element | jq -r '.series'`
		original_filename=`echo $element | jq -r '.original_filename'`
		expected_length=`echo $element | jq -r '.length'`
		real_length=`ls -1 $storage_dir/$file_id | wc -l`

		if [ ! "$expected_length" == "$real_length" ]
		then
			echo "Length DOES NOT MATCH for file '$original_filename' (id $file_id, $series): WEB:$expected_length!=HDD:$real_length";
		fi
		
		IFS=$'\n'
	done
	unset IFS
else
	echo "Error fetching"
fi

exit 0
