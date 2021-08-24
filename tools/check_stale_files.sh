#!/bin/bash
dest_dir="/YOUR/CONVERTED/FILES/DIR"
token="YOUR_TOKEN"

json=`curl https://api.fansubs.cat/internal/get_converted_links/?token=$token 2> /dev/null`
if [ $? -eq 0 ]
then
	array=`echo $json | jq -c '.result []'`
	IFS=$'\n'
	for element in $array
	do
		unset IFS
		url=`echo $element | jq -r '.url' | sed -E "s/^storage:\\\/\\\///"`
		echo "$dest_dir/$url" >> remote_urls_temp.txt
		IFS=$'\n'
	done
	unset IFS

	cat remote_urls_temp.txt | sort > remote_urls.txt
	rm remote_urls_temp.txt
	find "$dest_dir" -type f -printf "%p\n" | grep -v "AVÍS IMPORTANT - LLEGIU-ME" | sort > local_urls.txt

	diff remote_urls.txt local_urls.txt
	rm remote_urls.txt local_urls.txt
	exit 0
else
	echo "Error fetching"
	exit 1
fi
