@echo off
chcp 65001
rem ^ NO HO TOQUIS ^

rem --- FITXERS ---
rem Nom del fitxer de vídeo original: el fitxer resultant tindrà el mateix nom, però es desarà a la subcarpeta "Recomprimit"
rem Si hi ha una exclamació al nom del fitxer, cal canviar "!" per "^^!"
rem Si vols fer servir la funció d'arrossegar, deixa això en blanc (per exemple: "set input_file=", sense les cometes)
set input_file=
rem Fitxer de subtítols: si és extern, el nom sencer del fitxer; si és intern, número de pista (normalment 0); -1 si el vídeo ja té subtítols cremats
rem Aquest nom de fitxer pot contenir espais, però no caràcters especials com ara accents (a causa d'un bug del filtre ass)
rem Si vols fer servir la funció d'arrossegar, deixa això en blanc (per exemple: "set input_subs=", sense les cometes)
set input_subs=

rem --- CONVERSIÓ ---
rem Pista de vídeo que es processarà (normalment 0)
set video_track=0
rem Pista d'àudio que es processarà (normalment 0)
set audio_track=0
rem Acció a fer amb el vídeo: CONVERT per a recomprimir, COPY per a deixar-lo tal qual (en principi, sempre CONVERT)
set video_action=CONVERT
rem Acció a fer amb l'àudio: CONVERT per a recomprimir, COPY per a deixar-lo tal qual (en principi, sempre CONVERT)
set audio_action=CONVERT

rem --- DRAG AND DROP ---
:: intentarem omplir input_file i input_subs a partir dels fitxers arrossegats damunt el batch
:: si aquestes variables ja tenien valor, no els tocarem

:: evidentment, no hem provat molts d'aquests formats
set "video_extensions=.mkv .mp4 .avi .flv .vob .ogv .ogg .gifv .mts .m2ts .wmv .mov .qt .m4p .m4v .webm .mpeg .mpg .ts"
set "sub_extensions=.srt .ass .ssa .sub .vtt"

:: recorrem els arguments
setlocal EnableDelayedExpansion
for %%a in (%*) do (
  if "%input_file%" == "" (
    for %%e in (%video_extensions%) do (
      :: %%~xa és l'extensió del fitxer
      if %%~xa == %%~e (
        set input_file=%%~a
        echo Fitxer principal detectat: !input_file!
      )
    )
  )
  if "%input_subs%" == "" (
    for %%e in (%sub_extensions%) do (
      if %%~xa == %%~e (
        set input_subs=%%~a
        echo Fitxer de subtítols detectat: !input_subs!
      )
    )
  )
)
setlocal DisableDelayedExpansion

rem --- EXECUCIÓ ---
rem NO CANVIÏS RES D'AQUÍ EN AVALL!
call :main
pause
exit /b 0

:generate_streaming

set original_file=%~1
set video_stream=%~2
set audio_stream=%~3
set subtitle_stream=%~4
set action_video=%~5
set action_audio=%~6
set output_file=%~7

:: el filtre dels subtítols la lia parda
:: substituïm les \ per / i : per \\:
set subtitle_stream_escaped=%subtitle_stream:\=/%
set subtitle_stream_escaped=%subtitle_stream_escaped::=\\:%
set subtitle_stream_escaped=%subtitle_stream_escaped:[=\[%
set subtitle_stream_escaped=%subtitle_stream_escaped:]=\]%

set author=Recompressió per a anime.fansubs.cat
set title=No baixeu aquest fitxer, baixeu l'original^^!
set script_id=BaseScript-Windows-External
set crf_fullhd=23
set crf_hd=21
set crf_sd=19
set crf_ssd=17
set max_bitrate_fullhd=8192k
set max_bitrate_hd=4096k
set max_bitrate_sd=2048k
set max_bitrate_ssd=1536k
set output_dir=Recomprimit
set has_softsubs=0

if not exist "%original_file%" (
	echo ERROR: El fitxer de vídeo no existeix, verifica que sigui correcte.
	exit /b 1
)

echo %subtitle_stream%| findstr /r "^-*[0-9][0-9]*$" > nul

if %errorlevel% neq 0 (
	if not exist "%subtitle_stream%" (
		echo ERROR: El fitxer de subtítols no existeix, verifica que sigui correcte.
		exit /b 2
	)
)

if not exist "ffmpeg\bin\ffmpeg.exe" (
	echo ERROR: No s'ha trobat l'ordre 'ffmpeg'. Assegura't que tinguis l'ffmpeg descomprimit dins la carpeta "ffmpeg".
	exit /b 3
)

if not exist "ffmpeg\bin\ffprobe.exe" (
	echo ERROR: No s'ha trobat l'ordre 'ffprobe'. Assegura't que tinguis l'ffmpeg descomprimit dins la carpeta "ffmpeg".
	exit /b 3
)

ffmpeg\bin\ffprobe.exe -v error -select_streams v:%video_stream% -show_entries stream=height -of csv=s=x:p=0 "%original_file%" > temp.txt
set /p resolution=<temp.txt
del temp.txt

if %resolution% leq 360 (
	set crf=%crf_ssd%
	set max_bitrate=%max_bitrate_ssd%
) else (
	if %resolution% leq 480 (
		set crf=%crf_sd%
		set max_bitrate=%max_bitrate_sd%
	) else (
		if %resolution% leq 720 (
			set crf=%crf_hd%
			set max_bitrate=%max_bitrate_hd%
		) else (
			set crf=%crf_fullhd%
			set max_bitrate=%max_bitrate_fullhd%
		)
	)
)

echo %subtitle_stream%| findstr /r "^-*[0-9][0-9]*$" > nul

if %errorlevel% equ 0 (
	if "%subtitle_stream%" neq "-1" (
		rem Embedded subtitles: we cannot use the ass filter, so we use the subtitles one
		rem Drawback: ligatures are not correctly applied when using subtitles instead of ass
		set has_softsubs=1
		set filter_opts=subtitles="%original_file%:si=%subtitle_stream%"
	)
) else (
	set has_softsubs=1
	if "%subtitle_stream:~-4%" == ".ass" (
		rem ASS subtitles
		set filter_opts=ass="%subtitle_stream_escaped%"
	) else (
		rem SRT or other subtitles - will use Arial font
		set filter_opts=subtitles="%subtitle_stream_escaped%":force_style='Fontname=Arial,Fontsize=20'
	)
)

if "%action_video%" == "COPY" (
	set video_opts=-c:v copy
) else (
	set video_opts=-c:v libx264 -preset slower -profile:v high -level 4.1 -crf %crf% -maxrate %max_bitrate% -bufsize %max_bitrate%
)

if "%action_audio%" == "COPY" (
	set audio_opts=-c:a copy
) else (
	set audio_opts=-ac 2 -c:a aac -b:a 128k
)

rem Newline black magic
for /f %%a in ('copy /Z "%~dpf0" nul') do set "CR=%%a"
(set LF=^
%=EMPTY=%
)

md %output_dir% 2> nul

if "%action_video%" == "COPY" (
	setlocal EnableDelayedExpansion
	ffmpeg\bin\ffmpeg.exe -y -i "%original_file%" -map_metadata -1 -map_chapters -1 -map 0:v:%video_stream% -map 0:a:%audio_stream% %video_opts% %audio_opts% -metadata title="%title%" -metadata artist="%author%" -metadata comment="Codificador: %script_id%!LF!Paràmetres: %video_opts% %audio_opts%" -movflags faststart "%output_dir%\%output_file%.mp4"
	setlocal DisableDelayedExpansion
) else (
	if %has_softsubs% equ 1 (
		echo S'està comprovant que els tipus de lletra existeixin i s'identifiquin correctament ^(caldrà validació manual^)...
		ffmpeg\bin\ffmpeg.exe -y -i "%original_file%" -map_metadata -1 -map_chapters -1 -map 0:v:%$video_stream% -map 0:a:%audio_stream% -vf %filter_opts% -f null - 2>&1| findstr fontselect 2> nul
		set /p=Premeu Intro si tots els tipus de lletra estan correctament identificats o tanqueu la finestra si no és així.
		setlocal EnableDelayedExpansion
		ffmpeg\bin\ffmpeg.exe -y -i "%original_file%" -map_metadata -1 -map_chapters -1 -map 0:v:%video_stream% -map 0:a:%audio_stream% -pix_fmt yuv420p -vf %filter_opts% %video_opts% %audio_opts% -metadata title="%title%" -metadata artist="%author%" -metadata comment="Codificador: %script_id%!LF!Paràmetres: %video_opts% %audio_opts%" -movflags faststart "%output_dir%\%output_file%.mp4"
		setlocal DisableDelayedExpansion
	) else (
		setlocal EnableDelayedExpansion
		ffmpeg\bin\ffmpeg.exe -y -i "%original_file%" -map_metadata -1 -map_chapters -1 -map 0:v:%video_stream% -map 0:a:%audio_stream% -pix_fmt yuv420p %video_opts% %audio_opts% -metadata title="%title%" -metadata artist="%author%" -metadata comment="Codificador: %script_id%!LF!Paràmetres: %video_opts% %audio_opts%" -movflags faststart "%output_dir%\%output_file%.mp4"
		setlocal DisableDelayedExpansion
	)
)

echo El procés ha finalitzat! Si no veus errors en pantalla, ja pots publicar el fitxer!
exit /b 0

:main
rem Usage: generate_streaming ORIGINAL_FILE VIDEO_STREAM AUDIO_STREAM SUBTITLE_STREAM_OR_FILE VIDEO_ACTION AUDIO_ACTION OUTPUT_FILENAME_WITHOUT_EXTENSION
rem Normally, you will use video and audio streams 0, subtitle stream -1 for files with hardsubs and 0 for softsubs
rem You can also use an external file (subtitles.ass or subtitles.srt) by setting the subtitle stream to the filename (surrounded by "quotes")
rem Video and audio actions will always be CONVERT CONVERT unless you are absolutely sure of what you are doing (ask before using anything else)
:: calculam el nom del fitxer de sortida
for %%a in ("%input_file%") do set basename=%%~na
call :generate_streaming "%input_file%" %video_track% %audio_track% "%input_subs%" %video_action% %audio_action% "%basename%"
exit /b 0