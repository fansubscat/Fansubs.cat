#!/bin/bash
storage_dir="/YOUR/MANGA/STORAGE/DIR"
token="YOUR_TOKEN"

json=`curl https://api.fansubs.cat/internal/get_manga_files/?token=$token 2> /dev/null`
files=("Nothing")
if [ $? -eq 0 ]
then
	array=`echo $json | jq -c '.result []'`
	IFS=$'\n'
	for element in $array
	do
		unset IFS
		files+=(`echo $element | jq -r '.file_id'`)
		IFS=$'\n'
	done
	unset IFS
else
	echo "Error fetching"
fi

#Check for directories that are not in the site
for f in `ls -1 "$storage_dir/"`
do
        found=0
        for g in ${files[@]}
	do
                if [ "$f" = "$g" ]
		then
			found=1
		fi
        done
        if [ $found -eq 0 ]
	then
		echo "$f does not exist in site, should be removed"
	fi
done 

exit 0
