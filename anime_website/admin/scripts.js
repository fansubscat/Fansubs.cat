function getDurationFromString(str) {
	var result = str.match(/(?:(\d*) h)? ?(?:(\d*) min)?(?: per capítol)?/);
	var duration = 0;
	if (result[1]!=undefined){
		duration+=parseInt(result[1])*60;
	}
	if (result[2]!=undefined){
		duration+=parseInt(result[2]);
	}
	if (duration>0) {
		return duration;
	} else {
		return "";
	}
}

function populateMalData(data, staff) {
	if ($("#form-name-with-autocomplete").val()=='') {
		$("#form-name-with-autocomplete").val(data.title);
	}
	if ($("#form-slug").val()=='') {
		$("#form-slug").val(string_to_slug(data.title));
	}
	if ($("#form-alternate_names").val()=='') {
		if (data.title && data.title_english && data.title_english!=data.title) {
			$("#form-alternate_names").val(data.title+', '+data.title_english);
		} else if (data.title) {
			$("#form-alternate_names").val(data.title);
		} else if (data.title_english) {
			$("#form-alternate_names").val(data.title_english);
		}
	}
	if ($("#form-score").val()=='') {
		$("#form-score").val(data.score ? data.score : '');
	}
	if ($("#form-type").val()=='') {
		$("#form-type").val(data.episodes==1 ? 'movie' : 'series');
	}
	if ($("#form-air_date").val()=='') {
		$("#form-air_date").val(data.aired.from.substr(0, 10));
	}
	if (data.rating=='G - All Ages') {
		$("#form-rating").val('TP');
	} else if (data.rating=='PG - Children') {
		$("#form-rating").val('+7');
	} else if (data.rating=='PG-13 - Teens 13 or older') {
		$("#form-rating").val('+13');
	} else if (data.rating=='R - 17+ (violence & profanity)') {
		$("#form-rating").val('+16');
	} else if (data.rating=='R+ - Mild Nudity') {
		$("#form-rating").val('+18');
	} else if (data.rating=='Rx - Hentai') {
		$("#form-rating").val('XXX');
	} else {
		$("#form-rating").val('');
	}
	if ($("#form-synopsis").val()=='') {
		$("#form-synopsis").val(data.synopsis);
	}
	if ($("#form-duration").val()=='') {
		$("#form-duration").val(data.duration ? data.duration.replace('per ep','per capítol').replace('hr','h') : data.duration);
	}
	if ($("#form-image").val()=='' || $("#form-image").val().startsWith("https://cdn.myanimelist.net")) {
		var url = data.image_url ? data.image_url.replace(".jpg","l.jpg") : data.image_url;
		$("#form-image").val(url);
		$('#form-image-preview').prop('src', url);
		$('#form-image-preview-link').prop('href', url);
	}
	if ($("#form-episodes").val()=='' || $("#form-episodes").val()=='0') {
		if (data.episodes) {
			$("#form-episodes").val(data.episodes);
			$("#form-is_open").prop('checked', false);
			$("#form-episodes").prop('disabled', false);
		} else {
			$("#form-episodes").val('');
			$("#form-is_open").prop('checked', true);
			$("#form-episodes").prop('disabled', true);
		}
	}

	if (data.episodes==1) {
		//Movie, populate first episode and uncheck show episode numbers
		$('#form-show_episode_numbers').prop('checked', false);
		if ($('#form-episode-list-name-1').val()=='') {
			$('#form-episode-list-name-1').val($("#form-name-with-autocomplete").val());
		}
	}
	//Populate first episode duration
	if ($('#form-episode-list-duration-1').val()=='') {
		$('#form-episode-list-duration-1').val(getDurationFromString($("#form-duration").val()));
	}

	var authors = staff.staff.filter(function(value, index, array) {
		return value.positions.includes("Original Creator");
	});

	var textAuthors = "";
	for (var i = 0; i < authors.length; i++) {
		var authorName;
		if (authors[i].name.includes(', ')) {
			authorName=authors[i].name.split(', ')[1]+" "+authors[i].name.split(', ')[0];
		} else {
			authorName=authors[i].name;
		}

		if (textAuthors!='') {
			textAuthors+=', ';
		}
		textAuthors+=authorName;
	}

	$("#form-author").val(textAuthors);

	var directors = staff.staff.filter(function(value, index, array) {
		return value.positions.includes("Director");
	});

	var textDirectors = "";
	for (var i = 0; i < directors.length; i++) {
		var directorName;
		if (directors[i].name.includes(', ')) {
			directorName=directors[i].name.split(', ')[1]+" "+directors[i].name.split(', ')[0];
		} else {
			directorName=directors[i].name;
		}

		if (textDirectors!='') {
			textDirectors+=', ';
		}
		textDirectors+=directorName;
	}

	$("#form-director").val(textDirectors);

	var textStudios = "";
	for (var i = 0; i < data.studios.length; i++) {
		if (textStudios!='') {
			textStudios+=', ';
		}
		textStudios+=data.studios[i].name;
	}

	$("#form-studio").val(textStudios);

	$("[name='genres[]']").each(function() {
		$(this).prop('checked', false);
	});
	
	for (var i = 0; i < data.genres.length; i++) {
		$("[data-myanimelist-id='"+data.genres[i].mal_id+"']").prop('checked', true);
	}

	if ($("#form-season-list-episodes-1").val()=='') {
		if (data.episodes) {
			$("#form-season-list-episodes-1").val(data.episodes);
		}
	}

	if ($("#form-season-list-myanimelist_id-1").val()=='') {
		$("#form-season-list-myanimelist_id-1").val(data.mal_id);
	}
}

function populateMalEpisodes(season_line, episodes) {
	if (season_line==1) {
		var i = parseInt($('#episode-list-table').attr('data-count'));
		for (var id=1;id<i+1;id++) {
			$("#form-episode-list-row-"+id).remove();
		}
		$('#episode-list-table').attr('data-count', 0);
	}

	initialNumber = parseInt($('#episode-list-table').attr('data-count'))+1;

	for (var i=0;i<episodes.episodes.length;i++) {
		addRow(false);
		$("#form-episode-list-season-"+(i+initialNumber)).val($('#form-season-list-number-'+season_line).val());
		$("#form-episode-list-num-"+(i+initialNumber)).val(episodes.episodes[i].episode_id);
		$("#form-episode-list-name-"+(i+initialNumber)).val(episodes.episodes[i].title);
		$("#form-episode-list-duration-"+(i+initialNumber)).val(getDurationFromString($("#form-duration").val()));
	}
}

//Taken from: https://gist.github.com/codeguy/6684588
function string_to_slug(str) {
	str = str.replace(/^\s+|\s+$/g, ''); // trim
	str = str.toLowerCase();

	// remove accents, swap ñ for n, etc
	var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;'";
	var to   = "aaaaeeeeiiiioooouuuunc-------";
	for (var i=0, l=from.length ; i<l ; i++) {
		str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
	}

	str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
		.replace(/\s+/g, '-') // collapse whitespace and replace by -
		.replace(/-+/g, '-'); // collapse dashes

	return str;
}

function addRow(extra) {
	var i = parseInt($('#episode-list-table').attr('data-count'))+1;
	$('#episode-list-table').append('<tr id="form-episode-list-row-'+i+'"><td><input id="form-episode-list-season-'+i+'" name="form-episode-list-season-'+i+'" type="number" class="form-control" value="" placeholder="(Altres)"/></td><td><input id="form-episode-list-num-'+i+'" name="form-episode-list-num-'+i+'" type="number" class="form-control" value="" placeholder="(Esp.)"/><input id="form-episode-list-id-'+i+'" name="form-episode-list-id-'+i+'" type="hidden" value="-1"/></td><td><input id="form-episode-list-name-'+i+'" name="form-episode-list-name-'+i+'" type="text" class="form-control" value="" placeholder="(Sense títol)"/></td><td><input id="form-episode-list-duration-'+i+'" name="form-episode-list-duration-'+i+'" type="number" class="form-control" value="" required/></td><td class="text-center align-middle"><button id="form-episode-list-delete-'+i+'" onclick="deleteRow('+i+');" type="button" class="btn fa fa-trash p-1 text-danger"></button></td></tr>');
	$('#episode-list-table').attr('data-count', i);

	if (!extra) {
		$('#form-episode-list-season-'+i).val($('#form-episode-list-season-'+(i-1)).val()!='' ? $('#form-episode-list-season-'+(i-1)).val() : 1);
		$('#form-episode-list-num-'+i).val($('#form-episode-list-num-'+(i-1)).val()!='' ? parseInt($('#form-episode-list-num-'+(i-1)).val())+1 : 1);
		$('#form-episode-list-duration-'+i).val(getDurationFromString($("#form-duration").val()));
	}
}

function deleteRow(id) {
	var i = parseInt($('#episode-list-table').attr('data-count'));
	if(i==1) {
		alert('La sèrie ha de tenir un capítol, com a mínim!');
	}
	else {
		$("#form-episode-list-row-"+id).remove();
		for (var j=id+1;j<i+1;j++) {
			$("#form-episode-list-row-"+j).attr('id','form-episode-list-row-'+(j-1));
			$("#form-episode-list-id-"+j).attr('name','form-episode-list-id-'+(j-1));
			$("#form-episode-list-id-"+j).attr('id','form-episode-list-id-'+(j-1));
			$("#form-episode-list-season-"+j).attr('name','form-episode-list-season-'+(j-1));
			$("#form-episode-list-season-"+j).attr('id','form-episode-list-season-'+(j-1));
			$("#form-episode-list-num-"+j).attr('name','form-episode-list-num-'+(j-1));
			$("#form-episode-list-num-"+j).attr('id','form-episode-list-num-'+(j-1));
			$("#form-episode-list-name-"+j).attr('name','form-episode-list-name-'+(j-1));
			$("#form-episode-list-name-"+j).attr('id','form-episode-list-name-'+(j-1));
			$("#form-episode-list-duration-"+j).attr('name','form-episode-list-duration-'+(j-1));
			$("#form-episode-list-duration-"+j).attr('id','form-episode-list-duration-'+(j-1));
			$("#form-episode-list-delete-"+j).attr('onclick','deleteRow('+(j-1)+');');
			$("#form-episode-list-delete-"+j).attr('id','form-episode-list-delete-'+(j-1));
		}
		$('#episode-list-table').attr('data-count', i-1);
	}
}

function addSeasonRow() {
	var i = parseInt($('#season-list-table').attr('data-count'))+1;
	$('#season-list-table').append('<tr id="form-season-list-row-'+i+'"><td><input id="form-season-list-number-'+i+'" name="form-season-list-number-'+i+'" type="number" class="form-control" value="'+(parseInt($('#form-season-list-number-'+(i-1)).val())+1)+'" required/><input id="form-season-list-id-'+i+'" name="form-season-list-id-'+i+'" type="hidden" value="-1"/></td><td><input id="form-season-list-name-'+i+'" name="form-season-list-name-'+i+'" type="text" class="form-control" value="" placeholder="(Sense nom)"/></td><td><input id="form-season-list-episodes-'+i+'" name="form-season-list-episodes-'+i+'" type="number" class="form-control" value="" required/></td><td><input id="form-season-list-myanimelist_id-'+i+'" name="form-season-list-myanimelist_id-'+i+'" type="number" class="form-control" value=""/></td><td class="text-center align-middle"><button id="form-season-list-delete-'+i+'" onclick="deleteSeasonRow('+i+');" type="button" class="btn fa fa-trash p-1 text-danger"></button></td></tr>');
	$('#season-list-table').attr('data-count', i);
}

function deleteSeasonRow(id) {
	var i = parseInt($('#season-list-table').attr('data-count'));
	if(i==1) {
		alert('La sèrie ha de tenir una temporada, com a mínim!');
	}
	else {
		$("#form-season-list-row-"+id).remove();
		for (var j=id+1;j<i+1;j++) {
			$("#form-season-list-row-"+j).attr('id','form-season-list-row-'+(j-1));
			$("#form-season-list-id-"+j).attr('name','form-season-list-id-'+(j-1));
			$("#form-season-list-id-"+j).attr('id','form-season-list-id-'+(j-1));
			$("#form-season-list-number-"+j).attr('name','form-season-list-number-'+(j-1));
			$("#form-season-list-number-"+j).attr('id','form-season-list-number-'+(j-1));
			$("#form-season-list-name-"+j).attr('name','form-season-list-name-'+(j-1));
			$("#form-season-list-name-"+j).attr('id','form-season-list-name-'+(j-1));
			$("#form-season-list-episodes-"+j).attr('name','form-season-list-episodes-'+(j-1));
			$("#form-season-list-episodes-"+j).attr('id','form-season-list-episodes-'+(j-1));
			$("#form-season-list-myanimelist_id-"+j).attr('name','form-season-list-myanimelist_id-'+(j-1));
			$("#form-season-list-myanimelist_id-"+j).attr('id','form-season-list-myanimelist_id-'+(j-1));
			$("#form-season-list-delete-"+j).attr('onclick','deleteSeasonRow('+(j-1)+');');
			$("#form-season-list-delete-"+j).attr('id','form-season-list-delete-'+(j-1));
		}
		$('#season-list-table').attr('data-count', i-1);
	}
}

function addVersionRow(episode_id) {
	if (isAutoFetchActive()){
		alert("Si hi ha activada la sincronització automàtica de carpetes de MEGA, no és possible afegir més d'un enllaç per capítol. Abans has de desactivar-la.");
		return;
	}
	var i = parseInt($('#links-list-table-'+episode_id).attr('data-count'))+1;
	$('#links-list-table-'+episode_id).append('<tr id="form-links-list-'+episode_id+'-row-'+i+'"><td><input id="form-links-list-'+episode_id+'-link-'+i+'" name="form-links-list-'+episode_id+'-link-'+i+'" type="url" class="form-control" value="" maxlength="200" placeholder="(Sense enllaç)"/><input id="form-links-list-'+episode_id+'-id-'+i+'" name="form-links-list-'+episode_id+'-id-'+i+'" type="hidden" value="-1"/></td><td><input id="form-links-list-'+episode_id+'-resolution-'+i+'" name="form-links-list-'+episode_id+'-resolution-'+i+'" type="text" class="form-control" list="resolution-options" value="" maxlength="200" placeholder="- Tria -"/></td><td><input id="form-links-list-'+episode_id+'-comments-'+i+'" name="form-links-list-'+episode_id+'-comments-'+i+'" type="text" class="form-control" value="" maxlength="200"/></td><td class="text-center align-middle"><input id="form-links-list-'+episode_id+'-lost-'+i+'" name="form-links-list-'+episode_id+'-lost-'+i+'" type="checkbox" value="1"/></td><td class="text-center align-middle"><button id="form-links-list-'+episode_id+'-delete-'+i+'" onclick="deleteVersionRow('+episode_id+','+i+');" type="button" class="btn fa fa-trash p-1 text-danger"></button></td></tr>');
	$('#links-list-table-'+episode_id).attr('data-count', i);
}

function addVersionExtraRow() {
	var i = parseInt($('#extras-list-table').attr('data-count'))+1;
	$('#extras-list-table').append('<tr id="form-extras-list-row-'+i+'"><td><input id="form-extras-list-name-'+i+'" name="form-extras-list-name-'+i+'" type="text" class="form-control" value="" maxlength="200" required placeholder="- Introdueix un nom -"/><input id="form-extras-list-id-'+i+'" name="form-extras-list-id-'+i+'" type="hidden" value="-1"/></td><td><input id="form-extras-list-link-'+i+'" name="form-extras-list-link-'+i+'" type="url" class="form-control" value="" maxlength="200" required placeholder="- Introdueix un enllaç -"/></td><td><input id="form-extras-list-resolution-'+i+'" name="form-extras-list-resolution-'+i+'" type="text" class="form-control" list="resolution-options" value="" maxlength="200" placeholder="- Tria -"/></td><td><input id="form-extras-list-comments-'+i+'" name="form-extras-list-comments-'+i+'" type="text" class="form-control" value="" maxlength="200"/></td><td class="text-center align-middle"><button id="form-extras-list-delete-'+i+'" onclick="deleteVersionExtraRow('+i+');" type="button" class="btn fa fa-trash p-1 text-danger"></button></td></tr>');
	$('#extras-list-table').attr('data-count', i);
	$('#extras-list-table-empty').addClass('d-none');
}

function addVersionFolderRow() {
	var i = parseInt($('#folders-list-table').attr('data-count'))+1;

	var htmlAcc = $('#form-folders-list-account_id-XXX').prop('outerHTML').replace(/XXX/g, i).replace(' d-none">','" required>');

	var htmlSea = $('#form-folders-list-season_id-XXX').prop('outerHTML').replace(/XXX/g, i).replace(' d-none">','">');

	$('#folders-list-table').append('<tr id="form-folders-list-row-'+i+'"><td>'+htmlAcc+'<input id="form-folders-list-id-'+i+'" name="form-folders-list-id-'+i+'" type="hidden" value="-1"/></td><td><input id="form-folders-list-folder-'+i+'" name="form-folders-list-folder-'+i+'" class="form-control" value="" maxlength="200" required/></td><td>'+htmlSea+'</td><td class="text-center align-middle"><input id="form-folders-list-active-'+i+'" name="form-folders-list-active-'+i+'" type="checkbox" value="1"/></td><td class="text-center align-middle"><button id="form-folders-list-delete-'+i+'" onclick="deleteVersionFolderRow('+i+');" type="button" class="btn fa fa-trash p-1 text-danger"></button></td></tr>');
	$('#folders-list-table').attr('data-count', i);
	$('#folders-list-table-empty').addClass('d-none');
}

function deleteVersionRow(episode_id, id) {
	var i = parseInt($('#links-list-table-'+episode_id).attr('data-count'));
	if(i==1) {
		$("#form-links-list-"+episode_id+"-id-1").val("-1");
		$("#form-links-list-"+episode_id+"-link-1").val("");
		$("#form-links-list-"+episode_id+"-resolution-1").val("");
		$("#form-links-list-"+episode_id+"-comments-1").val("");
		$("#form-links-list-"+episode_id+"-lost-1").prop('checked',false);
	}
	else {
		$("#form-links-list-"+episode_id+"-row-"+id).remove();
		for (var j=id+1;j<i+1;j++) {
			$("#form-links-list-"+episode_id+"-row-"+j).attr('id','form-links-list-'+episode_id+'-row-'+(j-1));
			$("#form-links-list-"+episode_id+"-id-"+j).attr('name','form-links-list-'+episode_id+'-id-'+(j-1));
			$("#form-links-list-"+episode_id+"-id-"+j).attr('id','form-links-list-'+episode_id+'-id-'+(j-1));
			$("#form-links-list-"+episode_id+"-link-"+j).attr('name','form-links-list-'+episode_id+'-link-'+(j-1));
			$("#form-links-list-"+episode_id+"-link-"+j).attr('id','form-links-list-'+episode_id+'-link-'+(j-1));
			$("#form-links-list-"+episode_id+"-resolution-"+j).attr('name','form-links-list-'+episode_id+'-resolution-'+(j-1));
			$("#form-links-list-"+episode_id+"-resolution-"+j).attr('id','form-links-list-'+episode_id+'-resolution-'+(j-1));
			$("#form-links-list-"+episode_id+"-comments-"+j).attr('name','form-links-list-'+episode_id+'-comments-'+(j-1));
			$("#form-links-list-"+episode_id+"-comments-"+j).attr('id','form-links-list-'+episode_id+'-comments-'+(j-1));
			$("#form-links-list-"+episode_id+"-lost-"+j).attr('name','form-links-list-'+episode_id+'-lost-'+(j-1));
			$("#form-links-list-"+episode_id+"-lost-"+j).attr('id','form-links-list-'+episode_id+'-lost-'+(j-1));
			$("#form-links-list-"+episode_id+"-delete-"+j).attr('onclick','deleteVersionRow('+episode_id+','+(j-1)+');');
			$("#form-links-list-"+episode_id+"-delete-"+j).attr('id','form-links-list-'+episode_id+'-delete-'+(j-1));
		}
		$('#links-list-table-'+episode_id).attr('data-count', i-1);
	}
}

function deleteVersionExtraRow(id) {
	var i = parseInt($('#extras-list-table').attr('data-count'));
	$("#form-extras-list-row-"+id).remove();
	for (var j=id+1;j<i+1;j++) {
		$("#form-extras-list-row-"+j).attr('id','form-extras-list-row-'+(j-1));
		$("#form-extras-list-id-"+j).attr('name','form-extras-list-id-'+(j-1));
		$("#form-extras-list-id-"+j).attr('id','form-extras-list-id-'+(j-1));
		$("#form-extras-list-name-"+j).attr('name','form-extras-list-name-'+(j-1));
		$("#form-extras-list-name-"+j).attr('id','form-extras-list-name-'+(j-1));
		$("#form-extras-list-link-"+j).attr('name','form-extras-list-link-'+(j-1));
		$("#form-extras-list-link-"+j).attr('id','form-extras-list-link-'+(j-1));
		$("#form-extras-list-resolution-"+j).attr('name','form-extras-list-resolution-'+(j-1));
		$("#form-extras-list-resolution-"+j).attr('id','form-extras-list-resolution-'+(j-1));
		$("#form-extras-list-comments-"+j).attr('name','form-extras-list-comments-'+(j-1));
		$("#form-extras-list-comments-"+j).attr('id','form-extras-list-comments-'+(j-1));
		$("#form-extras-list-delete-"+j).attr('onclick','deleteVersionRow('+(j-1)+');');
		$("#form-extras-list-delete-"+j).attr('id','form-extras-list-delete-'+(j-1));
	}
	$('#extras-list-table').attr('data-count', i-1);

	if (i-1==0) {
		$('#extras-list-table-empty').removeClass('d-none');
	}
}

function deleteVersionFolderRow(id) {
	var i = parseInt($('#folders-list-table').attr('data-count'));
	$("#form-folders-list-row-"+id).remove();
	for (var j=id+1;j<i+1;j++) {
		$("#form-folders-list-row-"+j).attr('id','form-folders-list-row-'+(j-1));
		$("#form-folders-list-id-"+j).attr('name','form-folders-list-id-'+(j-1));
		$("#form-folders-list-id-"+j).attr('id','form-folders-list-id-'+(j-1));
		$("#form-folders-list-account_id-"+j).attr('name','form-folders-account_id-link-'+(j-1));
		$("#form-folders-list-account_id-"+j).attr('id','form-folders-account_id-link-'+(j-1));
		$("#form-folders-list-folder-"+j).attr('name','form-folders-list-folder-'+(j-1));
		$("#form-folders-list-folder-"+j).attr('id','form-folders-list-folder-'+(j-1));
		$("#form-folders-list-delete-"+j).attr('onclick','deleteVersionFolderRow('+(j-1)+');');
		$("#form-folders-list-delete-"+j).attr('id','form-folders-list-delete-'+(j-1));
	}
	$('#folders-list-table').attr('data-count', i-1);

	if (i-1==0) {
		$('#folders-list-table-empty').removeClass('d-none');
	}
}

function fetchMalEpisodes(current_season, total_seasons, page) {
	if (current_season==1 && page==1) {
		malDataSeasonsEpisodes = [];
		malDataSeasonsEpisodesCount = 0;
		malDataMessages = "";
	}

	var xmlhttp = new XMLHttpRequest();
	var url = "https://api.jikan.moe/v3/anime/"+$("#form-season-list-myanimelist_id-"+current_season).val()+"/episodes/"+page;

	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var malDataEpisodesPage = JSON.parse(this.responseText);
			if (page==1) {
				malDataEpisodes=malDataEpisodesPage;
			} else {
				malDataEpisodes.episodes=malDataEpisodes.episodes.concat(malDataEpisodesPage.episodes);
			}
			if (page<malDataEpisodesPage.episodes_last_page) {
				setTimeout(function() {
					fetchMalEpisodes(current_season, total_seasons, page+1);
				}, 4000);
			} else{
				if (malDataEpisodes.episodes.length>0) {
					malDataSeasonsEpisodes.push(malDataEpisodes);
					malDataSeasonsEpisodesCount+=malDataEpisodes.episodes.length;
				}
				else{
					malDataMessages+="\nLa temporada "+$("#form-season-list-number-"+current_season).val()+" no té capítols donats d'alta a MyAnimeList. Caldrà que els introdueixis a mà.";
					malDataSeasonsEpisodes.push({'episodes': []});
				}
				if (current_season<total_seasons) {
					setTimeout(function() {
						fetchMalEpisodes(current_season+1, total_seasons, 1);
					}, 4000);
				} else {
					if (malDataSeasonsEpisodesCount>0) {
						for (var i=0;i<malDataSeasonsEpisodes.length;i++) {
							populateMalEpisodes(i+1,malDataSeasonsEpisodes[i]);
						}
						if (malDataMessages!='') {
							alert("S'han produït els següents errors:\n"+malDataMessages);
						}
						$("#import-from-mal-episodes-done").removeClass("d-none");
					} else {
						alert("No hi ha capítols donats d'alta a MyAnimeList. Caldrà que els introdueixis a mà.");
					}
					$("#import-from-mal-episodes-loading").addClass("d-none");
					$("#import-from-mal-episodes-not-loading").removeClass("d-none");
					setTimeout(function() {
						$("#import-from-mal").prop('disabled', false);
						$("#import-from-mal-episodes").prop('disabled', false);
					}, 4000);
				}
			}
		} else if (this.readyState == 4) {
			alert("S'ha produït un error en obtenir dades de MyAnimeList, torna-ho a provar més tard.");
			$("#import-from-mal-episodes-loading").addClass("d-none");
			$("#import-from-mal-episodes-not-loading").removeClass("d-none");
			setTimeout(function() {
				$("#import-from-mal").prop('disabled', false);
				$("#import-from-mal-episodes").prop('disabled', false);
			}, 4000);
		}
	};
	xmlhttp.open("GET", url, true);
	xmlhttp.send();
}

function checkNumberOfEpisodes() {
	var seasons = $('[id^=form-season-list-episodes-]');
	var seasonsEpisodeCount=0;
	for (var i=0;i<seasons.length;i++){
		if ($(seasons[i]).val()!='' && $(seasons[i]).val()>0) {
			seasonsEpisodeCount+=parseInt($(seasons[i]).val());
		}
	}
	var episodeCount = parseInt($('#episode-list-table').attr('data-count'));
	var normalEpisodeCount = 0;

	for (var i=1;i<=episodeCount;i++){
		if ($('#form-episode-list-num-'+i).val()!=''){
			normalEpisodeCount++;
		}
	}
	if (normalEpisodeCount!=seasonsEpisodeCount){
		alert('El nombre de capítols numerats de la llista ha de coincidir amb el nombre de capítols indicat a les temporades.');
		return false;
	}
	for (var i=1;i<=episodeCount;i++){
		if ($('#form-episode-list-num-'+i).val()=='' && $('#form-episode-list-name-'+i).val()==''){
			alert('Hi ha capítols sense número ni nom. Els capítols normals han de tenir com a mínim número, i els capítols especials han de tenir com a mínim nom.');
			return false;
		}
	}

	var seasonNumbers = [];
	for (var i=0;i<seasons.length;i++){
		seasonNumbers.push($('#form-season-list-number-'+(i+1)).val());
	}
	for (var i=1;i<=episodeCount;i++){
		if ($('#form-episode-list-season-'+i).val()!='' && !seasonNumbers.includes($('#form-episode-list-season-'+i).val())){
			alert('Hi ha capítols de temporades inexistents. Corregeix-ho.');
			return false;
		}
		if ($('#form-episode-list-num-'+i).val()!='' && $('#form-episode-list-num-'+i).val()==0){
			alert('0 no és un número de capítol vàlid. Corregeix-ho.');
			return false;
		}
		if ($('#form-episode-list-num-'+i).val()>episodeCount){
			alert('Hi ha números de capítol més alts que el nombre total de capítols. Corregeix-ho.');
			return false;
		}
	}

	return true;
}

function checkNumberOfLinks() {
	if (isAutoFetchActive()){
		var linkTables = $('[id^=links-list-table-]');
		var multipleLinks = false;
		for (var i=0;i<linkTables.length;i++) {
			if ($(linkTables).attr('data-count')>1){
				multipleLinks = true;
				break;
			}
		}

		if (multipleLinks) {
			alert("Si hi ha activada la sincronització automàtica de carpetes de MEGA, no és possible afegir més d'un enllaç per capítol. Has de desactivar-la o bé eliminar els enllaços addicionals dels capítols.");
			return false;
		}
	}

	return true;
}

var validLinks=0;
var invalidLinks=0;
var failedLinks=0;
var unknownLinks=0;
var linkVerifyRetries=0;

function verifyLinks(i) {
	if (i==links.length){
		$('#link-verifier-button').prop('disabled', false);
		$('#link-verifier-loading').addClass('d-none');
		$('#link-verifier-progress')[0].innerHTML="Procés completat";
		return;
	}

	if (i==0){
		validLinks=0;
		invalidLinks=0;
		failedLinks=0;
		unknownLinks=0;
		linkVerifyRetries=0;
		updateVerifyLinksResult(0);
		$('#link-verifier-progress').removeClass('d-none');
		$('#link-verifier-wrong-links-list').addClass('d-none');
		$('#link-verifier-failed-links-list').addClass('d-none');
		$('#link-verifier-results').removeClass('d-none');
		$('#link-verifier-button').prop('disabled', true);
		$('#link-verifier-loading').removeClass('d-none');
	}
	
	var matchesMega = links[i].link.match(/https:\/\/mega\.nz\/(?:#!|embed#!|file\/|embed\/)?([a-zA-Z0-9]{0,8})[!#]([a-zA-Z0-9_-]+)/);
	var matchesGoogleDrive = links[i].link.match(/https:\/\/drive\.google\.com\/(?:file\/d\/|open\?id=)?([^\/]*)(?:preview|view)?/);
	if (matchesMega && matchesMega.length>1 && matchesMega[1]!=''){
		//MEGA link
		$.post("https://eu.api.mega.co.nz/cs", "[{\"a\":\"g\", \"g\":1, \"ssl\":0, \"p\":\""+matchesMega[1]+"\"}]", function(data, status){
			if (data=="-9") {
				//invalid
				$('#link-verifier-wrong-links-list').append('<div class="row w-100"><p class="col-sm-4 font-weight-bold">'+links[i].text+'</p><p class="col-sm-8">'+links[i].link+'</p></div>');
				invalidLinks++;
				updateVerifyLinksResult(i+1);
				linkVerifyRetries=0;
				verifyLinks(i+1);
			} else if (status=='success') {
				//valid
				validLinks++;
				updateVerifyLinksResult(i+1);
				linkVerifyRetries=0;
				verifyLinks(i+1);
			} else {
				if (linkVerifyRetries<5){
					linkVerifyRetries++;
					setTimeout(function(){
						verifyLinks(i);
					}, 5000);
				} else {
					$('#link-verifier-failed-links-list').append('<div class="row w-100"><p class="col-sm-4 font-weight-bold">'+links[i].text+'</p><p class="col-sm-8">'+links[i].link+'</p></div>');
					failedLinks++;
					linkVerifyRetries=0;
					verifyLinks(i+1);
				}
			}
		}).fail(function() {
			if (linkVerifyRetries<5){
				linkVerifyRetries++;
				setTimeout(function(){
					verifyLinks(i);
				}, 5000);
			} else {
				$('#link-verifier-failed-links-list').append('<div class="row w-100"><p class="col-sm-4 font-weight-bold">'+links[i].text+'</p><p class="col-sm-8">'+links[i].link+'</p></div>');
				failedLinks++;
				linkVerifyRetries=0;
				verifyLinks(i+1);
			}
		});
	} else if (matchesGoogleDrive && matchesGoogleDrive.length>1 && matchesGoogleDrive[1]!=''){
		//Google Drive link
		$.post("check_googledrive_link.php?link="+encodeURIComponent(matchesGoogleDrive[1]), function(data, status){
			if (data=='OK') {
				//valid
				validLinks++;
				updateVerifyLinksResult(i+1);
				linkVerifyRetries=0;
				verifyLinks(i+1);
			} else if (data=='KO') {
				//invalid
				$('#link-verifier-wrong-links-list').append('<div class="row w-100"><p class="col-sm-4 font-weight-bold">'+links[i].text+'</p><p class="col-sm-8">'+links[i].link+'</p></div>');
				invalidLinks++;
				updateVerifyLinksResult(i+1);
				linkVerifyRetries=0;
				verifyLinks(i+1);
			} else {
				if (linkVerifyRetries<5){
					linkVerifyRetries++;
					verifyLinks(i);
				} else {
					$('#link-verifier-failed-links-list').append('<div class="row w-100"><p class="col-sm-4 font-weight-bold">'+links[i].text+'</p><p class="col-sm-8">'+links[i].link+'</p></div>');
					failedLinks++;
					linkVerifyRetries=0;
					verifyLinks(i+1);
				}
			}
		}).fail(function() {
			if (linkVerifyRetries<5){
				linkVerifyRetries++;
				verifyLinks(i);
			} else {
				$('#link-verifier-failed-links-list').append('<div class="row w-100"><p class="col-sm-4 font-weight-bold">'+links[i].text+'</p><p class="col-sm-8">'+links[i].link+'</p></div>');
				failedLinks++;
				linkVerifyRetries=0;
				verifyLinks(i+1);
			}
		});
	} else {
		unknownLinks++;
		updateVerifyLinksResult(i+1);
		verifyLinks(i+1);
	}
}

function updateVerifyLinksResult(i) {
	$('#link-verifier-progress')[0].innerHTML=i+"/"+links.length;
	$('#link-verifier-good-links')[0].innerHTML=validLinks;
	$('#link-verifier-unknown-links')[0].innerHTML=unknownLinks;
	$('#link-verifier-failed-links')[0].innerHTML=failedLinks;
	$('#link-verifier-wrong-links')[0].innerHTML=invalidLinks;
	if (invalidLinks>0) {
		$('#link-verifier-wrong-links-list').removeClass('d-none');
	}
	if (failedLinks>0) {
		$('#link-verifier-failed-links-list').removeClass('d-none');
	}
}

function isAutoFetchActive() {
	return $('[id^=form-folders-list-active-]:checked').length>0;
}

var malData;
var malDataStaff;
var malDataSeasonsEpisodes;
var malDataEpisodes;
var malDataMessages;

$(document).ready(function() {
	$("#form-name-with-autocomplete").on('input', function() {
		$("#form-slug").val(string_to_slug($("#form-name-with-autocomplete").val()));
	});

	$("#import-from-mal").click(function() {
		if ($("#form-myanimelist_id").val()=='') {
			var result = prompt("Introdueix l'URL de l'anime a MyAnimeList per a importar-ne la fitxa.");
			if (!result) {
				return;
			} else if (result.match(/https?:\/\/.*myanimelist.net\/anime\/(\d+)\//i)) {
				$("#form-myanimelist_id").val(result.match(/https?:\/\/.*myanimelist.net\/anime\/(\d*)\//i)[1]);
			} else {
				alert("L'URL no és vàlida.");
				return;
			}
		}
		if (($("#form-name-with-autocomplete").val()!='' || $("#form-synopsis").val()!='') && !confirm("ATENCIÓ! La fitxa ja conté dades. Si continues, se sobreescriuran les dades d'autor, director, estudi, valoració per edats i gèneres, i també s'ompliran els camps que siguin buits.\nL'acció no es podrà desfer un cop hagis desat els canvis. Vols continuar?")) {
			return;
		}
		$("#import-from-mal").prop('disabled', true);
		$("#import-from-mal-episodes").prop('disabled', true);
		$("#import-from-mal-loading").removeClass("d-none");
		$("#import-from-mal-not-loading").addClass("d-none");
		var xmlhttp = new XMLHttpRequest();
		var url = "https://api.jikan.moe/v3/anime/"+$("#form-myanimelist_id").val();

		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				malData = JSON.parse(this.responseText);

				setTimeout(function() {
					var xmlhttp2 = new XMLHttpRequest();
					var url2 = "https://api.jikan.moe/v3/anime/"+$("#form-myanimelist_id").val()+"/characters_staff";

					xmlhttp2.onreadystatechange = function() {
						if (this.readyState == 4 && this.status == 200) {
							malDataStaff = JSON.parse(this.responseText);
							populateMalData(malData,malDataStaff);
							$("#import-from-mal-loading").addClass("d-none");
							$("#import-from-mal-not-loading").removeClass("d-none");
							setTimeout(function() {
								$("#import-from-mal").prop('disabled', false);
								$("#import-from-mal-episodes").prop('disabled', false);
							}, 4000);
							$("#import-from-mal-done").removeClass("d-none");
						} else if (this.readyState == 4) {
							$("#import-from-mal-loading").addClass("d-none");
							$("#import-from-mal-not-loading").removeClass("d-none");
							alert("S'ha produït un error en obtenir dades de MyAnimeList, torna-ho a provar més tard.");
							setTimeout(function() {
								$("#import-from-mal").prop('disabled', false);
								$("#import-from-mal-episodes").prop('disabled', false);
							}, 4000);
						}
					};
					xmlhttp2.open("GET", url2, true);
					xmlhttp2.send();
				}, 4000);
			} else if (this.readyState == 4) {
				alert("S'ha produït un error en obtenir dades de MyAnimeList, torna-ho a provar més tard.");
				$("#import-from-mal-loading").addClass("d-none");
				$("#import-from-mal-not-loading").removeClass("d-none");
				setTimeout(function() {
					$("#import-from-mal").prop('disabled', false);
					$("#import-from-mal-episodes").prop('disabled', false);
				}, 4000);
			}
		};
		xmlhttp.open("GET", url, true);
		xmlhttp.send();
	});

	$("#import-from-mal-episodes").click(function() {
		var seasons = $('[id^=form-season-list-myanimelist_id-]');
		var with_id=0;
		for (var i=0;i<seasons.length;i++){
			if ($(seasons[i]).val()!='') {
				with_id++;
			}
		}
		if (with_id==0) {
			alert("Cal que introdueixis l'identificador de MyAnimeList d'almenys una de les temporades.");
			return;
		} else if (with_id<seasons.length) {
			if (!confirm("Hi ha temporades sense identificador de MyAnimeList. Només s'importaran els capítols de les temporades que tinguin identificador.")){
				return;
			}
		}
		if ((parseInt($('#episode-list-table').attr('data-count'))>1 || $('#form-episode-list-name-1').val()!='') && !confirm("ATENCIÓ! Ja hi ha dades de capítols. Si continues, se suprimiran tots i es tornaran a crear. Totes les versions que continguin aquests capítols i tots els enllaços d'aquests capítols desapareixeran i no es podrà desfer l'acció un cop hagis desat els canvis. Vols continuar?")) {
			return;
		}

		$("#import-from-mal").prop('disabled', true);
		$("#import-from-mal-episodes").prop('disabled', true);
		$("#import-from-mal-episodes-loading").removeClass("d-none");
		$("#import-from-mal-episodes-not-loading").addClass("d-none");

		fetchMalEpisodes(1,seasons.length,1);
	});

	$("#generate-episodes").click(function() {
		var seasons = $('[id^=form-season-list-episodes-]');
		var with_episodes=0;
		for (var i=0;i<seasons.length;i++){
			if ($(seasons[i]).val()!='' && $(seasons[i]).val()>0) {
				with_episodes++;
			}
		}
		if (with_episodes!=seasons.length) {
			alert("Per a poder-los generar, cal que introdueixis el nombre de capítols de cada temporada.");
			return;
		}

		if ((parseInt($('#episode-list-table').attr('data-count'))>1 || $('#form-episode-list-name-1').val()!='') && !confirm("ATENCIÓ! Ja hi ha dades de capítols. Si continues, se suprimiran tots i es tornaran a crear. Totes les versions que continguin aquests capítols i tots els enllaços d'aquests capítols desapareixeran i no es podrà desfer l'acció un cop hagis desat els canvis. Vols continuar?")) {
			return;
		}

		var restart = (seasons.length==1 || confirm("Vols reiniciar la numeració de capítols a cada temporada? Si és així, prem 'D'acord', en cas contrari, prem 'Cancel·la'."));

		var i = parseInt($('#episode-list-table').attr('data-count'));
		for (var id=1;id<i+1;id++) {
			$("#form-episode-list-row-"+id).remove();
		}
		$('#episode-list-table').attr('data-count', 0);

		var rowNumber=1;

		for (var i=0;i<seasons.length;i++) {
			for (var j=0;j<$(seasons[i]).val();j++) {
				addRow(false);
				$("#form-episode-list-season-"+rowNumber).val($('#form-season-list-number-'+(i+1)).val());
				$("#form-episode-list-num-"+rowNumber).val(restart ? j+1 : rowNumber);
				$("#form-episode-list-name-"+rowNumber).val('');
				$("#form-episode-list-duration-"+rowNumber).val(getDurationFromString($("#form-duration").val()));
				rowNumber++;
			}
		}
	});

	$("#import-from-mega").click(function() {
		var count = parseInt($('#folders-list-table').attr('data-count'));
		if (count==0){
			alert('Per a poder importar, abans has de configurar una carpeta!');
			return;
		}

		$("#import-from-mega-loading").removeClass("d-none");
		$("#import-from-mega-not-loading").addClass("d-none");
		$("#import-from-mega").prop('disabled', true);
		$('#import-failed-results-table tbody').empty();
		$('#import-failed-results').addClass('d-none');

		var account_ids = [];
		var folders = [];
		var season_ids = [];
		for (var i=1;i<=count;i++){
			account_ids.push(encodeURIComponent($('#form-folders-list-account_id-'+i).val()));
			folders.push(encodeURIComponent($('#form-folders-list-folder-'+i).val()));
			season_ids.push(encodeURIComponent($('#form-folders-list-season_id-'+i).val()!='' ? $('#form-folders-list-season_id-'+i).val() : -1));
		}

		var xmlhttp = new XMLHttpRequest();
		var url = "fetch_mega_files.php?series_id="+$('[name="series_id"]').val()+"&account_ids[]="+account_ids.join("&account_ids[]=")+"&folders[]="+folders.join("&folders[]=")+"&season_ids[]="+season_ids.join("&season_ids[]=");

		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var data = JSON.parse(this.responseText);
				if (data.status=='ko') {
					alert("S'ha produït un error:\n"+data.error);
				} else {
					for (var i = 0; i < data.results.length; i++) {
						$("[id^=form-links-list-"+data.results[i].id+"-link-]").val(data.results[i].link);
						$("[id^=form-links-list-"+data.results[i].id+"-resolution-]").val($('#form-default_resolution').val());
					}
					if (data.unmatched_results.length>0) {
						$('#import-failed-results').removeClass('d-none');
						for (var i = 0; i < data.unmatched_results.length; i++) {
							$('#import-failed-results-table').append('<tr><td>'+data.unmatched_results[i].file+'</td><td>'+data.unmatched_results[i].link+'</td><td title="'+data.unmatched_results[i].reason_description+'" style="white-space: nowrap;">'+data.unmatched_results[i].reason+'<span class="fa fa-question-circle ml-1"></span></td></tr>');
						}
					}
				}
				$("#import-from-mega-loading").addClass("d-none");
				$("#import-from-mega-not-loading").removeClass("d-none");
				$("#import-from-mega").prop('disabled', false);
			} else if (this.readyState == 4) {
				alert("S'ha produït un error. Torna-ho a provar.");
				$("#import-from-mega-loading").addClass("d-none");
				$("#import-from-mega-not-loading").removeClass("d-none");
				$("#import-from-mega").prop('disabled', false);
			}
		};
		xmlhttp.open("GET", url, true);
		xmlhttp.send();
	});
	$("#form-is_open").change(function() {
		if ($(this).prop('checked')) {
			$("#form-episodes").val('');
			$("#form-episodes").prop('disabled', true);
		} else {
			$("#form-episodes").prop('disabled', false);
		}
	});
});
