#!/bin/bash

# --- FITXERS ---
# Nom del fitxer de vídeo original: el fitxer resultant tindrà el mateix nom, però es desarà a la subcarpeta "Recomprimit"
input_file="Nom del fitxer del vídeo original.mkv"
# Fitxer de subtítols: si és extern, el nom sencer del fitxer; si és intern, número de pista (normalment 0); -1 si el vídeo ja té subtítols cremats
input_subs="Nom del fitxer dels subtítols.ass"

# --- CONVERSIÓ ---
# Pista de vídeo que es processarà (normalment 0)
video_track=0
# Pista d'àudio que es processarà (normalment 0)
audio_track=0
# Acció a fer amb el vídeo: CONVERT per a recomprimir, COPY per a deixar-lo tal qual (en principi, sempre CONVERT)
video_action=CONVERT
# Acció a fer amb l'àudio: CONVERT per a recomprimir, COPY per a deixar-lo tal qual (en principi, sempre CONVERT)
audio_action=CONVERT

# --- EXECUCIÓ ---
# NO CANVIÏS RES D'AQUÍ EN AVALL!

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
	script_id="BaseScript-Linux-External"
	crf_fullhd="23"
	crf_hd="21"
	crf_sd="19"
	crf_ssd="17"
	max_bitrate_fullhd="8192k"
	max_bitrate_hd="4096k"
	max_bitrate_sd="2048k"
	max_bitrate_ssd="1536k"
	output_dir="Recomprimit"
	has_softsubs=0

	if [ ! -f "$original_file" ]
	then
		echo "ERROR: El fitxer de vídeo no existeix, verifica que sigui correcte."
		exit 1
	fi
	if [[ ! "$subtitle_stream" =~ ^-?[0-9]+$ ]]
	then
		if [ ! -f "$subtitle_stream" ]
		then
			echo "ERROR: El fitxer de subtítols no existeix, verifica que sigui correcte."
			exit 2
		fi
	fi

	if ! command -v ffmpeg &> /dev/null
	then
	    echo "ERROR: No s'ha trobat l'ordre 'ffmpeg'. Assegura't que tinguis el paquet ffmpeg correctament instal·lat."
	    exit 3
	fi

	if ! command -v ffprobe &> /dev/null
	then
	    echo "ERROR: No s'ha trobat l'ordre 'ffprobe'. Assegura't que tinguis el paquet ffmpeg correctament instal·lat."
	    exit 3
	fi

	resolution=`ffprobe -v error -select_streams v:$video_stream -show_entries stream=height -of csv=s=x:p=0 "$original_file"`

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

	if [[ "$subtitle_stream" =~ ^-?[0-9]+$ ]]
	then
		if [ $subtitle_stream -ne -1 ]
		then
			# Embedded subtitles: we cannot use the ass filter, so we use the subtitles one
			# Drawback: ligatures are not correctly applied when using subtitles instead of ass
			has_softsubs=1
			filter_opts="subtitles='$original_file:si=$subtitle_stream'"
		fi
	else
		has_softsubs=1
		if [[ "$subtitle_stream" =~ \.ass$ ]]
		then
			# ASS subtitles
			filter_opts="ass='$subtitle_stream'"
		else
			# SRT or other subtitles - will use Arial font
			filter_opts="subtitles='$subtitle_stream':force_style='Fontname=Arial,Fontsize=20'"
		fi
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

	mkdir $output_dir 2> /dev/null

	if [ "$action_video" = "COPY" ]
	then
		ffmpeg -y -i "$original_file" -map_metadata -1 -map_chapters -1 -map 0:v:$video_stream -map 0:a:$audio_stream $video_opts $audio_opts -metadata title="$title" -metadata artist="$author" -metadata comment="$comment" -movflags faststart "$output_dir/$output_file.mp4"
	else
		if [ $has_softsubs -eq 1 ]
		then
			echo "S'està comprovant que els tipus de lletra existeixin i s'identifiquin correctament (caldrà validació manual)..."
			ffmpeg -y -i "$original_file" -map_metadata -1 -map_chapters -1 -map 0:v:$video_stream -map 0:a:$audio_stream -vf "$filter_opts" -f null - 2>&1 | grep fontselect | sed -E "s/.* fontselect:/Tipus de lletra:/"
			read -p "Premeu Intro si tots els tipus de lletra estan correctament identificats o Control-C si no és així."
			ffmpeg -y -i "$original_file" -map_metadata -1 -map_chapters -1 -map 0:v:$video_stream -map 0:a:$audio_stream -pix_fmt yuv420p -vf "$filter_opts" $video_opts $audio_opts -metadata title="$title" -metadata artist="$author" -metadata comment="$comment" -movflags faststart "$output_dir/$output_file.mp4"
		else
			ffmpeg -y -i "$original_file" -map_metadata -1 -map_chapters -1 -map 0:v:$video_stream -map 0:a:$audio_stream -pix_fmt yuv420p $video_opts $audio_opts -metadata title="$title" -metadata artist="$author" -metadata comment="$comment" -movflags faststart "$output_dir/$output_file.mp4"
		fi
	fi

	echo "El procés ha finalitzat! Si no veus errors en pantalla, ja pots publicar el fitxer!"
}

# Usage: generate_streaming ORIGINAL_FILE VIDEO_STREAM AUDIO_STREAM SUBTITLE_STREAM_OR_FILE VIDEO_ACTION AUDIO_ACTION OUTPUT_FILENAME_WITHOUT_EXTENSION
# Normally, you will use video and audio streams 0, subtitle stream -1 for files with hardsubs and 0 for softsubs
# You can also use an external file (subtitles.ass or subtitles.srt) by setting the subtitle stream to the filename (surrounded by "quotes")
# Video and audio actions will always be CONVERT CONVERT unless you are absolutely sure of what you are doing (ask before using anything else)
generate_streaming "$input_file" $video_track $audio_track "$input_subs" $video_action $audio_action "`echo "$input_file" | sed s/\\.mp4// | sed s/\\.mkv// | sed s/\\.avi// | sed -E "s/ \[.*\]//"`"

exit 0

