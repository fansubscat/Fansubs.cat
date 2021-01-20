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

	var url = data.image_url ? data.image_url.replace(".jpg","l.jpg") : data.image_url;
	if (document.getElementById('form-image').files.length>0) {
		resetFileInput($("#form-image"));
	}
	$("#form-image_url").val(url);
	$('#form-image-preview').prop('src', url);
	$('#form-image-preview-link').prop('href', url);

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

function populateMalDataManga(data) {
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
		$("#form-type").val(data.type=='One-shot' ? 'oneshot' : 'serialized');
	}
	if ($("#form-publish_date").val()=='') {
		$("#form-publish_date").val(data.published.from.substr(0, 10));
	}
	if ($("#form-synopsis").val()=='') {
		$("#form-synopsis").val(data.synopsis);
	}

	var url = data.image_url ? data.image_url.replace(".jpg","l.jpg") : data.image_url;
	if (document.getElementById('form-image').files.length>0) {
		resetFileInput($("#form-image"));
	}
	$("#form-image_url").val(url);
	$('#form-image-preview').prop('src', url);
	$('#form-image-preview-link').prop('href', url);

	if (data.chapters==1) {
		//One-shot, populate first chapter and uncheck show chapter numbers
		$('#form-show_chapter_numbers').prop('checked', false);
		if ($('#form-chapter-list-name-1').val()=='') {
			$('#form-chapter-list-name-1').val($("#form-name-with-autocomplete").val());
		}
	}

	var textAuthors = "";
	for (var i = 0; i < data.authors.length; i++) {
		var authorName;
		if (data.authors[i].name.includes(', ')) {
			authorName=data.authors[i].name.split(', ')[1]+" "+data.authors[i].name.split(', ')[0];
		} else {
			authorName=data.authors[i].name;
		}

		if (textAuthors!='') {
			textAuthors+=', ';
		}
		textAuthors+=authorName;
	}

	$("#form-author").val(textAuthors);

	$("[name='genres[]']").each(function() {
		$(this).prop('checked', false);
	});
	
	for (var i = 0; i < data.genres.length; i++) {
		$("[data-myanimelist-id='"+data.genres[i].mal_id+"']").prop('checked', true);
	}

	if (data.volumes && data.volumes>1) {
		for (var i=0;i<data.volumes;i++){
			if ((i+1)>$('#volume-list-table').attr('data-count')){
				addVolumeRow();
			}
		}
		if (data.chapters) {
			var howMany = prompt("S'importaran els volums, però no sabem quants capítols té cadascun. Segons MyAnimeList, en total n'hi ha "+data.chapters+" repartits en "+data.volumes+" volums (aprox. uns "+Math.floor(data.chapters/data.volumes)+" capítols per volum). Amb l'objectiu de facilitar introduir les dades, podem assignar-ne una quantitat fixa a cada volum: introdueix-la. Si ho cancel·les o no introdueixes res, s'assignaran tots al primer volum. En qualsevol cas, assegura't de revisar que tot sigui correcte abans d'afegir el manga.");
			if (!howMany || !howMany.match(/^-?[0-9]+$/)) {
				$("#form-volume-list-chapters-1").val(data.chapters);
			} else {
				for (var i=1;i<=data.volumes;i++){
					$("#form-volume-list-chapters-"+i).val(howMany);
				}
			}
		}
	}

	if ($("#form-volume-list-chapters-1").val()=='') {
		if (data.chapters) {
			$("#form-volume-list-chapters-1").val(data.chapters);
		}
	}

	if ($("#form-volume-list-myanimelist_id-1").val()=='') {
		$("#form-volume-list-myanimelist_id-1").val(data.mal_id);
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
	$('#episode-list-table').append('<tr id="form-episode-list-row-'+i+'"><td><input id="form-episode-list-season-'+i+'" name="form-episode-list-season-'+i+'" type="number" class="form-control" value="" placeholder="(Altres)"/></td><td><input id="form-episode-list-num-'+i+'" name="form-episode-list-num-'+i+'" type="number" class="form-control" value="" placeholder="(Esp.)" step="any"/><input id="form-episode-list-id-'+i+'" name="form-episode-list-id-'+i+'" type="hidden" value="-1"/></td><td><input id="form-episode-list-name-'+i+'" name="form-episode-list-name-'+i+'" type="text" class="form-control" value="" placeholder="(Sense títol)"/></td><td><input id="form-episode-list-duration-'+i+'" name="form-episode-list-duration-'+i+'" type="number" class="form-control" value="" required/></td><td class="text-center align-middle"><button id="form-episode-list-delete-'+i+'" onclick="deleteRow('+i+');" type="button" class="btn fa fa-trash p-1 text-danger"></button></td></tr>');
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
		alert('L\'anime ha de tenir un capítol, com a mínim!');
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

function addChapterRow(extra) {
	var i = parseInt($('#chapter-list-table').attr('data-count'))+1;
	$('#chapter-list-table').append('<tr id="form-chapter-list-row-'+i+'"><td><input id="form-chapter-list-volume-'+i+'" name="form-chapter-list-volume-'+i+'" type="number" class="form-control" value="" placeholder="(Altres)"/></td><td><input id="form-chapter-list-num-'+i+'" name="form-chapter-list-num-'+i+'" type="number" class="form-control" value="" placeholder="(Esp.)" step="any"/><input id="form-chapter-list-id-'+i+'" name="form-chapter-list-id-'+i+'" type="hidden" value="-1"/></td><td><input id="form-chapter-list-name-'+i+'" name="form-chapter-list-name-'+i+'" type="text" class="form-control" value="" placeholder="(Sense títol)"/></td><td class="text-center align-middle"><button id="form-chapter-list-delete-'+i+'" onclick="deleteChapterRow('+i+');" type="button" class="btn fa fa-trash p-1 text-danger"></button></td></tr>');
	$('#chapter-list-table').attr('data-count', i);

	if (!extra) {
		$('#form-chapter-list-volume-'+i).val($('#form-chapter-list-volume-'+(i-1)).val()!='' ? $('#form-chapter-list-volume-'+(i-1)).val() : 1);
		$('#form-chapter-list-num-'+i).val($('#form-chapter-list-num-'+(i-1)).val()!='' ? parseInt($('#form-chapter-list-num-'+(i-1)).val())+1 : 1);
	}
}

function deleteChapterRow(id) {
	var i = parseInt($('#chapter-list-table').attr('data-count'));
	if(i==1) {
		alert('El manga ha de tenir un capítol, com a mínim!');
	}
	else {
		$("#form-chapter-list-row-"+id).remove();
		for (var j=id+1;j<i+1;j++) {
			$("#form-chapter-list-row-"+j).attr('id','form-chapter-list-row-'+(j-1));
			$("#form-chapter-list-id-"+j).attr('name','form-chapter-list-id-'+(j-1));
			$("#form-chapter-list-id-"+j).attr('id','form-chapter-list-id-'+(j-1));
			$("#form-chapter-list-volume-"+j).attr('name','form-chapter-list-volume-'+(j-1));
			$("#form-chapter-list-volume-"+j).attr('id','form-chapter-list-volume-'+(j-1));
			$("#form-chapter-list-num-"+j).attr('name','form-chapter-list-num-'+(j-1));
			$("#form-chapter-list-num-"+j).attr('id','form-chapter-list-num-'+(j-1));
			$("#form-chapter-list-name-"+j).attr('name','form-chapter-list-name-'+(j-1));
			$("#form-chapter-list-name-"+j).attr('id','form-chapter-list-name-'+(j-1));
			$("#form-chapter-list-delete-"+j).attr('onclick','deleteChapterRow('+(j-1)+');');
			$("#form-chapter-list-delete-"+j).attr('id','form-chapter-list-delete-'+(j-1));
		}
		$('#chapter-list-table').attr('data-count', i-1);
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
		alert('L\'anime ha de tenir una temporada, com a mínim!');
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

function addVolumeRow() {
	var i = parseInt($('#volume-list-table').attr('data-count'))+1;
	$('#volume-list-table').append('<tr id="form-volume-list-row-'+i+'"><td><input id="form-volume-list-number-'+i+'" name="form-volume-list-number-'+i+'" type="number" class="form-control" value="'+(parseInt($('#form-volume-list-number-'+(i-1)).val())+1)+'" required/><input id="form-volume-list-id-'+i+'" name="form-volume-list-id-'+i+'" type="hidden" value="-1"/></td><td><input id="form-volume-list-name-'+i+'" name="form-volume-list-name-'+i+'" type="text" class="form-control" value="" placeholder="(Sense nom)"/></td><td><input id="form-volume-list-chapters-'+i+'" name="form-volume-list-chapters-'+i+'" type="number" class="form-control" value="" required/></td><td><input id="form-volume-list-myanimelist_id-'+i+'" name="form-volume-list-myanimelist_id-'+i+'" type="number" class="form-control" value=""/></td><td class="text-center align-middle"><button id="form-volume-list-delete-'+i+'" onclick="deleteVolumeRow('+i+');" type="button" class="btn fa fa-trash p-1 text-danger"></button></td></tr>');
	$('#volume-list-table').attr('data-count', i);
}

function deleteVolumeRow(id) {
	var i = parseInt($('#volume-list-table').attr('data-count'));
	if(i==1) {
		alert('El manga ha de tenir un volum, com a mínim!');
	}
	else {
		$("#form-volume-list-row-"+id).remove();
		for (var j=id+1;j<i+1;j++) {
			$("#form-volume-list-row-"+j).attr('id','form-volume-list-row-'+(j-1));
			$("#form-volume-list-id-"+j).attr('name','form-volume-list-id-'+(j-1));
			$("#form-volume-list-id-"+j).attr('id','form-volume-list-id-'+(j-1));
			$("#form-volume-list-number-"+j).attr('name','form-volume-list-number-'+(j-1));
			$("#form-volume-list-number-"+j).attr('id','form-volume-list-number-'+(j-1));
			$("#form-volume-list-name-"+j).attr('name','form-volume-list-name-'+(j-1));
			$("#form-volume-list-name-"+j).attr('id','form-volume-list-name-'+(j-1));
			$("#form-volume-list-chapters-"+j).attr('name','form-volume-list-chapters-'+(j-1));
			$("#form-volume-list-chapters-"+j).attr('id','form-volume-list-chapters-'+(j-1));
			$("#form-volume-list-myanimelist_id-"+j).attr('name','form-volume-list-myanimelist_id-'+(j-1));
			$("#form-volume-list-myanimelist_id-"+j).attr('id','form-volume-list-myanimelist_id-'+(j-1));
			$("#form-volume-list-delete-"+j).attr('onclick','deleteVolumeRow('+(j-1)+');');
			$("#form-volume-list-delete-"+j).attr('id','form-volume-list-delete-'+(j-1));
		}
		$('#volume-list-table').attr('data-count', i-1);
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

function addFileRow(chapter_id) {
	var i = parseInt($('#files-list-table-'+chapter_id).attr('data-count'))+1;
	$('#files-list-table-'+chapter_id).append('<tr id="form-files-list-'+chapter_id+'-row-'+i+'"><td class="align-middle"><div id="form-files-list-'+chapter_id+'-file_details-'+i+'" class="small"><span style="color: gray;"><span class="fa fa-times fa-fw"></span> No hi ha cap fitxer pujat.</span></div></td><td class="align-middle"><label style="margin-bottom: 0;" for="form-files-list-'+chapter_id+'-file-'+i+'" class="btn btn-sm btn-info w-100"><span class="fa fa-upload pr-2"></span>Puja un fitxer...</label><input id="form-files-list-'+chapter_id+'-file-'+i+'" name="form-files-list-'+chapter_id+'-file-'+i+'" type="file" accept=".zip,.rar,.cbz" class="form-control d-none" onchange="uncompressFile(this);"/><input id="form-files-list-'+chapter_id+'-id-'+i+'" name="form-files-list-'+chapter_id+'-id-'+i+'" type="hidden" value="-1"/><input id="form-files-list-'+chapter_id+'-number_of_files-'+i+'" name="form-files-list-'+chapter_id+'-number_of_files-'+i+'" type="hidden" value="0"/></td><td class="align-middle"><input id="form-files-list-'+chapter_id+'-comments-'+i+'" name="form-files-list-'+chapter_id+'-comments-'+i+'" type="text" class="form-control" value="" maxlength="200"/></td><td class="text-center align-middle"><input id="form-files-list-'+chapter_id+'-lost-'+i+'" name="form-files-list-'+chapter_id+'-lost-'+i+'" type="checkbox" value="1"/></td><td class="text-center align-middle"><button id="form-files-list-'+chapter_id+'-delete-'+i+'" onclick="deleteFileRow('+chapter_id+','+i+');" type="button" class="btn fa fa-trash p-1 text-danger"></button></td></tr>');
	$('#files-list-table-'+chapter_id).attr('data-count', i);
}

function addFileExtraRow() {
	var i = parseInt($('#extras-list-table').attr('data-count'))+1;
	$('#extras-list-table').append('<tr id="form-extras-list-row-'+i+'"><td class="align-middle"><input id="form-extras-list-name-'+i+'" name="form-extras-list-name-'+i+'" type="text" class="form-control" value="" maxlength="200" required placeholder="- Introdueix un nom -"/><input id="form-extras-list-id-'+i+'" name="form-extras-list-id-'+i+'" type="hidden" value="-1"/><input id="form-extras-list-number_of_files-'+i+'" name="form-extras-list-number_of_files-'+i+'" type="hidden" value="-1"/></td><td class="align-middle"><div id="form-extras-list-file_details-'+i+'" class="small"><span style="color: gray;"><span class="fa fa-times fa-fw"></span> No hi ha cap fitxer pujat.</span></div></td><td class="align-middle"><label style="margin-bottom: 0;" for="form-extras-list-file-'+i+'" class="btn btn-sm btn-info w-100"><span class="fa fa-upload pr-2"></span>Puja un fitxer...</label><input id="form-extras-list-file-'+i+'" name="form-extras-list-file-'+i+'" type="file" accept=".zip,.rar,.cbz" class="form-control d-none" onchange="uncompressFile(this);" required/></td><td class="align-middle"><input id="form-extras-list-comments-'+i+'" name="form-extras-list-comments-'+i+'" type="text" class="form-control" value="" maxlength="200"/></td><td class="text-center align-middle"><button id="form-extras-list-delete-'+i+'" onclick="deleteFileExtraRow('+i+');" type="button" class="btn fa fa-trash p-1 text-danger"></button></td></tr>');
	$('#extras-list-table').attr('data-count', i);
	$('#extras-list-table-empty').addClass('d-none');
}

function uncompressFile(fileInput) {
	var detailsElement=$(document.getElementById(fileInput.id.replace("file-", "file_details-")));
	var numberOfPagesElement=$(document.getElementById(fileInput.id.replace("file-", "number_of_pages-")));
	// Just return if there is no file selected
	if (fileInput.files.length === 0) {
		detailsElement.html("<span style=\"color: gray;\"><span class=\"fa fa-times fa-fw\"></span> No s'ha seleccionat cap fitxer, no es faran canvis.</span>");
		numberOfPagesElement.val(0);
		$('label[for="'+fileInput.id+'"]').removeClass("btn-danger");
		$('label[for="'+fileInput.id+'"]').removeClass("btn-info");
		$('label[for="'+fileInput.id+'"]').removeClass("btn-success");
		$('label[for="'+fileInput.id+'"]').removeClass("btn-warning");
		$('label[for="'+fileInput.id+'"]').addClass("btn-secondary");
		$('label[for="'+fileInput.id+'"]').html('<span class="fa fa-times pr-2"></span>Sense canvis');
		return;
	}

	// Get the selected file
	let file = fileInput.files[0];

	// Open the file as an archive
	archiveOpenFile(file, '', function(archive, err) {
		if (archive) {
			console.info('Uncompressing ' + archive.archive_type + ' ...');
			var count=0;
			var countImages=0;
			var countRepeated=0;
			var foundNames = [];
			archive.entries.forEach(function(entry) {
				if (entry.is_file) {
					if (entry.name.toLowerCase().endsWith(".jpg") || entry.name.toLowerCase().endsWith(".png") || entry.name.toLowerCase().endsWith(".jpeg")) {
						countImages++;
					}
					var shortName = entry.name.split('/').pop().replace(/[^0-9a-zA-Z_\.]/g,'_');
					if (foundNames.includes(shortName)){
						countRepeated++;
					}
					foundNames.push(shortName);
					count++;
				}
			});

			if (countImages==count && countRepeated==0) {
				detailsElement.html("<span style=\"color: #1e7e34;\"><span class=\"fa fa-check fa-fw\"></span> <strong>Es pujarà</strong> el fitxer <strong>"+file.name+"</strong>.<br /><span class=\"fa fa-file-archive fa-fw\"></span> L'arxiu consta de <strong>"+countImages+" imatges</strong> en total.</span>");
				numberOfPagesElement.val(countImages);
				$('label[for="'+fileInput.id+'"]').removeClass("btn-danger");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-warning");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-info");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-secondary");
				$('label[for="'+fileInput.id+'"]').addClass("btn-success");
				$('label[for="'+fileInput.id+'"]').html('<span class="fa fa-check pr-2"></span>Es pujarà');
			} else if (countRepeated>0) {
				detailsElement.html("<span style=\"color: #bd2130;\"><span class=\"fa fa-times fa-fw\"></span> <strong>No es pot pujar</strong> el fitxer <strong>"+file.name+"</strong>.<br /><span class=\"fa fa-exclamation-triangle fa-fw\"></span> <strong>L'arxiu conté "+countRepeated+" fitxers amb noms repetits. Recorda que no hi poden haver subcarpetes.</span>");
				fileInput.value="";
				$('label[for="'+fileInput.id+'"]').removeClass("btn-warning");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-info");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-secondary");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-success");
				$('label[for="'+fileInput.id+'"]').addClass("btn-danger");
				$('label[for="'+fileInput.id+'"]').html('<span class="fa fa-upload pr-2"></span>Puja un fitxer...');
				numberOfPagesElement.val(0);
			} else if (countImages==0) {
				detailsElement.html("<span style=\"color: #bd2130;\"><span class=\"fa fa-times fa-fw\"></span> <strong>No es pot pujar</strong> el fitxer <strong>"+file.name+"</strong>.<br /><span class=\"fa fa-exclamation-triangle fa-fw\"></span> <strong>L'arxiu no conté cap fitxer d'imatge JPEG ni PNG.</span>");
				fileInput.value="";
				numberOfPagesElement.val(0);
				$('label[for="'+fileInput.id+'"]').removeClass("btn-warning");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-info");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-secondary");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-success");
				$('label[for="'+fileInput.id+'"]').addClass("btn-danger");
				$('label[for="'+fileInput.id+'"]').html('<span class="fa fa-upload pr-2"></span>Puja un fitxer...');
			} else {
				detailsElement.html("<span style=\"color: #d39e00;\"><span class=\"fa fa-check fa-fw\"></span> <strong>Es pujarà</strong> el fitxer <strong>"+file.name+"</strong>.<br /><span class=\"fa fa-file-archive fa-fw\"></span> L'arxiu consta de <strong>"+countImages+" imatges</strong> en total.<br /><span class=\"fa fa-exclamation-triangle fa-fw\"></span> <strong>Compte: L'arxiu conté "+(count-countImages)+" fitxers que no són imatges i es descartaran.</span>");
				numberOfPagesElement.val(countImages);
				$('label[for="'+fileInput.id+'"]').removeClass("btn-danger");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-info");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-secondary");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-success");
				$('label[for="'+fileInput.id+'"]').addClass("btn-warning");
				$('label[for="'+fileInput.id+'"]').html('<span class="fa fa-exclamation-triangle pr-2"></span>Es pujarà');
			}
		} else {
			detailsElement.html("<span style=\"color: #bd2130;\"><span class=\"fa fa-times fa-fw\"></span> <strong>El fitxer no és vàlid</strong> (ha de ser un arxiu ZIP o RAR).</span>");
			fileInput.value="";
			numberOfPagesElement.val(0);
			$('label[for="'+fileInput.id+'"]').removeClass("btn-warning");
			$('label[for="'+fileInput.id+'"]').removeClass("btn-info");
			$('label[for="'+fileInput.id+'"]').removeClass("btn-secondary");
			$('label[for="'+fileInput.id+'"]').removeClass("btn-success");
			$('label[for="'+fileInput.id+'"]').addClass("btn-danger");
			$('label[for="'+fileInput.id+'"]').html('<span class="fa fa-upload pr-2"></span>Puja un fitxer...');
		}
	});
}

function addRelatedSeriesRow() {
	var i = parseInt($('#related-list-table').attr('data-count'))+1;

	var htmlAcc = $('#form-related-list-related_series_id-XXX').prop('outerHTML').replace(/XXX/g, i).replace(' d-none">','" required>');

	$('#related-list-table').append('<tr id="form-related-list-row-'+i+'"><td>'+htmlAcc+'</td><td class="text-center align-middle"><button id="form-related-list-delete-'+i+'" onclick="deleteRelatedSeriesRow('+i+');" type="button" class="btn fa fa-trash p-1 text-danger"></button></td></tr>');
	$('#related-list-table').attr('data-count', i);
	$('#related-list-table-empty').addClass('d-none');
}

function deleteRelatedSeriesRow(id) {
	var i = parseInt($('#related-list-table').attr('data-count'));
	$("#form-related-list-row-"+id).remove();
	for (var j=id+1;j<i+1;j++) {
		$("#form-related-list-row-"+j).attr('id','form-related-list-row-'+(j-1));
		$("#form-related-list-related_series_id-"+j).attr('name','form-related-list-related_series_id-'+(j-1));
		$("#form-related-list-related_series_id-"+j).attr('id','form-related-list-related_series_id-'+(j-1));
		$("#form-related-list-delete-"+j).attr('onclick','deleteRelatedSeriesRow('+(j-1)+');');
		$("#form-related-list-delete-"+j).attr('id','form-related-list-delete-'+(j-1));
	}
	$('#related-list-table').attr('data-count', i-1);

	if (i-1==0) {
		$('#related-list-table-empty').removeClass('d-none');
	}
}

function addRelatedMangaRow() {
	var i = parseInt($('#relatedmanga-list-table').attr('data-count'))+1;

	var htmlAcc = $('#form-relatedmanga-list-related_manga_id-XXX').prop('outerHTML').replace(/XXX/g, i).replace(' d-none">','" required>');

	$('#relatedmanga-list-table').append('<tr id="form-relatedmanga-list-row-'+i+'"><td>'+htmlAcc+'</td><td class="text-center align-middle"><button id="form-relatedmanga-list-delete-'+i+'" onclick="deleteRelatedMangaRow('+i+');" type="button" class="btn fa fa-trash p-1 text-danger"></button></td></tr>');
	$('#relatedmanga-list-table').attr('data-count', i);
	$('#relatedmanga-list-table-empty').addClass('d-none');
}

function deleteRelatedMangaRow(id) {
	var i = parseInt($('#relatedmanga-list-table').attr('data-count'));
	$("#form-relatedmanga-list-row-"+id).remove();
	for (var j=id+1;j<i+1;j++) {
		$("#form-relatedmanga-list-row-"+j).attr('id','form-relatedmanga-list-row-'+(j-1));
		$("#form-relatedmanga-list-related_manga_id-"+j).attr('name','form-relatedmanga-list-related_manga_id-'+(j-1));
		$("#form-relatedmanga-list-related_manga_id-"+j).attr('id','form-relatedmanga-list-related_manga_id-'+(j-1));
		$("#form-relatedmanga-list-delete-"+j).attr('onclick','deleteRelatedMangaRow('+(j-1)+');');
		$("#form-relatedmanga-list-delete-"+j).attr('id','form-relatedmanga-list-delete-'+(j-1));
	}
	$('#relatedmanga-list-table').attr('data-count', i-1);

	if (i-1==0) {
		$('#relatedmanga-list-table-empty').removeClass('d-none');
	}
}

function addRelatedMangaMangaRow() {
	var i = parseInt($('#relatedmangamanga-list-table').attr('data-count'))+1;

	var htmlAcc = $('#form-relatedmangamanga-list-related_manga_id-XXX').prop('outerHTML').replace(/XXX/g, i).replace(' d-none">','" required>');

	$('#relatedmangamanga-list-table').append('<tr id="form-relatedmangamanga-list-row-'+i+'"><td>'+htmlAcc+'</td><td class="text-center align-middle"><button id="form-relatedmangamanga-list-delete-'+i+'" onclick="deleteRelatedMangaMangaRow('+i+');" type="button" class="btn fa fa-trash p-1 text-danger"></button></td></tr>');
	$('#relatedmangamanga-list-table').attr('data-count', i);
	$('#relatedmangamanga-list-table-empty').addClass('d-none');
}

function deleteRelatedMangaMangaRow(id) {
	var i = parseInt($('#relatedmangamanga-list-table').attr('data-count'));
	$("#form-relatedmangamanga-list-row-"+id).remove();
	for (var j=id+1;j<i+1;j++) {
		$("#form-relatedmangamanga-list-row-"+j).attr('id','form-relatedmangamanga-list-row-'+(j-1));
		$("#form-relatedmangamanga-list-related_manga_id-"+j).attr('name','form-relatedmangamanga-list-related_manga_id-'+(j-1));
		$("#form-relatedmangamanga-list-related_manga_id-"+j).attr('id','form-relatedmangamanga-list-related_manga_id-'+(j-1));
		$("#form-relatedmangamanga-list-delete-"+j).attr('onclick','deleteRelatedMangaMangaRow('+(j-1)+');');
		$("#form-relatedmangamanga-list-delete-"+j).attr('id','form-relatedmangamanga-list-delete-'+(j-1));
	}
	$('#relatedmangamanga-list-table').attr('data-count', i-1);

	if (i-1==0) {
		$('#relatedmangamanga-list-table-empty').removeClass('d-none');
	}
}

function addRelatedMangaAnimeRow() {
	var i = parseInt($('#relatedmangaanime-list-table').attr('data-count'))+1;

	var htmlAcc = $('#form-relatedmangaanime-list-related_anime_id-XXX').prop('outerHTML').replace(/XXX/g, i).replace(' d-none">','" required>');

	$('#relatedmangaanime-list-table').append('<tr id="form-relatedmangaanime-list-row-'+i+'"><td>'+htmlAcc+'</td><td class="text-center align-middle"><button id="form-relatedmangaanime-list-delete-'+i+'" onclick="deleteRelatedMangaAnimeRow('+i+');" type="button" class="btn fa fa-trash p-1 text-danger"></button></td></tr>');
	$('#relatedmangaanime-list-table').attr('data-count', i);
	$('#relatedmangaanime-list-table-empty').addClass('d-none');
}

function deleteRelatedMangaAnimeRow(id) {
	var i = parseInt($('#relatedmangaanime-list-table').attr('data-count'));
	$("#form-relatedmangaanime-list-row-"+id).remove();
	for (var j=id+1;j<i+1;j++) {
		$("#form-relatedmangaanime-list-row-"+j).attr('id','form-relatedmangaanime-list-row-'+(j-1));
		$("#form-relatedmangaanime-list-related_manga_id-"+j).attr('name','form-relatedmangaanime-list-related_anime_id-'+(j-1));
		$("#form-relatedmangaanime-list-related_manga_id-"+j).attr('id','form-relatedmangaanime-list-related_anime_id-'+(j-1));
		$("#form-relatedmangaanime-list-delete-"+j).attr('onclick','deleteRelatedMangaAnimeRow('+(j-1)+');');
		$("#form-relatedmangaanime-list-delete-"+j).attr('id','form-relatedmangaanime-list-delete-'+(j-1));
	}
	$('#relatedmangaanime-list-table').attr('data-count', i-1);

	if (i-1==0) {
		$('#relatedmangaanime-list-table-empty').removeClass('d-none');
	}
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
		$("#form-extras-list-delete-"+j).attr('onclick','deleteVersionExtraRow('+(j-1)+');');
		$("#form-extras-list-delete-"+j).attr('id','form-extras-list-delete-'+(j-1));
	}
	$('#extras-list-table').attr('data-count', i-1);

	if (i-1==0) {
		$('#extras-list-table-empty').removeClass('d-none');
	}
}

function deleteFileRow(chapter_id, id) {
	var i = parseInt($('#files-list-table-'+chapter_id).attr('data-count'));
	if(i==1) {
		$("#form-files-list-"+chapter_id+"-id-1").val("-1");
		$("#form-files-list-"+chapter_id+"-file-1").val("");
		$('label[for="form-files-list-'+chapter_id+'-file-1"]').removeClass("btn-warning");
		$('label[for="form-files-list-'+chapter_id+'-file-1"]').addClass("btn-info");
		$('label[for="form-files-list-'+chapter_id+'-file-1"]').html('<span class="fa fa-upload pr-2"></span> Puja un fitxer...');
		$("#form-files-list-"+chapter_id+"-file_details-1").html('<span style="color: gray;"><span class="fa fa-times fa-fw"></span> No hi ha cap fitxer pujat.</span>');
		$("#form-files-list-"+chapter_id+"-comments-1").val("");
		$("#form-files-list-"+chapter_id+"-lost-1").prop('checked',false);
	}
	else {
		$("#form-files-list-"+chapter_id+"-row-"+id).remove();
		for (var j=id+1;j<i+1;j++) {
			$("#form-files-list-"+chapter_id+"-row-"+j).attr('id','form-files-list-'+chapter_id+'-row-'+(j-1));
			$("#form-files-list-"+chapter_id+"-id-"+j).attr('name','form-files-list-'+chapter_id+'-id-'+(j-1));
			$("#form-files-list-"+chapter_id+"-id-"+j).attr('id','form-files-list-'+chapter_id+'-id-'+(j-1));
			$("#form-files-list-"+chapter_id+"-file-"+j).attr('name','form-files-list-'+chapter_id+'-file-'+(j-1));
			$("#form-files-list-"+chapter_id+"-file-"+j).attr('id','form-files-list-'+chapter_id+'-file-'+(j-1));
			$("#form-files-list-"+chapter_id+"-file_details-"+j).attr('name','form-files-list-'+chapter_id+'-file_details-'+(j-1));
			$("#form-files-list-"+chapter_id+"-file_details-"+j).attr('id','form-files-list-'+chapter_id+'-file_details-'+(j-1));
			$("#form-files-list-"+chapter_id+"-comments-"+j).attr('name','form-files-list-'+chapter_id+'-comments-'+(j-1));
			$("#form-files-list-"+chapter_id+"-comments-"+j).attr('id','form-files-list-'+chapter_id+'-comments-'+(j-1));
			$("#form-files-list-"+chapter_id+"-lost-"+j).attr('name','form-files-list-'+chapter_id+'-lost-'+(j-1));
			$("#form-files-list-"+chapter_id+"-lost-"+j).attr('id','form-files-list-'+chapter_id+'-lost-'+(j-1));
			$("#form-files-list-"+chapter_id+"-delete-"+j).attr('onclick','deleteFileRow('+chapter_id+','+(j-1)+');');
			$("#form-files-list-"+chapter_id+"-delete-"+j).attr('id','form-files-list-'+chapter_id+'-delete-'+(j-1));
		}
		$('#files-list-table-'+chapter_id).attr('data-count', i-1);
	}
}

function deleteFileExtraRow(id) {
	var i = parseInt($('#extras-list-table').attr('data-count'));
	$("#form-extras-list-row-"+id).remove();
	for (var j=id+1;j<i+1;j++) {
		$("#form-extras-list-row-"+j).attr('id','form-extras-list-row-'+(j-1));
		$("#form-extras-list-id-"+j).attr('name','form-extras-list-id-'+(j-1));
		$("#form-extras-list-id-"+j).attr('id','form-extras-list-id-'+(j-1));
		$("#form-extras-list-name-"+j).attr('name','form-extras-list-name-'+(j-1));
		$("#form-extras-list-name-"+j).attr('id','form-extras-list-name-'+(j-1));
		$("#form-extras-list-file-"+j).attr('name','form-extras-list-file-'+(j-1));
		$("#form-extras-list-file-"+j).attr('id','form-extras-list-file-'+(j-1));
		$("#form-extras-list-file_details-"+j).attr('name','form-extras-list-file_details-'+(j-1));
		$("#form-extras-list-file_details-"+j).attr('id','form-extras-list-file_details-'+(j-1));
		$("#form-extras-list-comments-"+j).attr('name','form-extras-list-comments-'+(j-1));
		$("#form-extras-list-comments-"+j).attr('id','form-extras-list-comments-'+(j-1));
		$("#form-extras-list-delete-"+j).attr('onclick','deleteFileExtraRow('+(j-1)+');');
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
		$("#form-folders-list-account_id-"+j).attr('name','form-folders-list-account_id-'+(j-1));
		$("#form-folders-list-account_id-"+j).attr('id','form-folders-list-account_id-'+(j-1));
		$("#form-folders-list-folder-"+j).attr('name','form-folders-list-folder-'+(j-1));
		$("#form-folders-list-folder-"+j).attr('id','form-folders-list-folder-'+(j-1));
		$("#form-folders-list-season_id-"+j).attr('name','form-folders-list-season_id-'+(j-1));
		$("#form-folders-list-season_id-"+j).attr('id','form-folders-list-season_id-'+(j-1));
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
	var higherThanCount=false;
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
			higherThanCount=true;
		}
	}

	if (higherThanCount && !confirm('Hi ha números de capítol més alts que el nombre total de capítols. Segur que és correcte? Si dubtes, cancel·la i revisa-ho.')){
		return false;
	}

	if (!$('#id').val() && !$('#form-image').val() && !$('#form-image_url').val()) {
		alert('Cal que pugis una imatge de portada.');
		return false;
	}

	if (!$('#id').val() && !$('#form-featured_image').val()) {
		alert('Cal que pugis una imatge de capçalera.');
		return false;
	}

	if ($('#form-name-with-autocomplete').val()==$('#form-alternate_names').val()) {
		alert('El nom i el camp "altres noms" no poden ser iguals. Si no se\'n tradueix el nom, el camp "altres noms" ha de romandre buit o amb altres noms diferents, si s\'escau (en anglès, per exemple).');
		return false;
	}

	if (document.getElementById('form-image-preview').naturalWidth>450 || document.getElementById('form-image-preview').naturalHeight>600) {
		alert('La imatge de portada té unes dimensions massa grosses. El màxim són 450x600 píxels.');
		return false;
	}

	if (document.getElementById('form-featured-image-preview').naturalWidth>1200 || document.getElementById('form-featured-image-preview').naturalHeight>400) {
		alert('La imatge de capçalera té unes dimensions massa grosses. El màxim són 1200x400 píxels.');
		return false;
	}

	return true;
}

function checkNumberOfChapters() {
	var volumes = $('[id^=form-volume-list-chapters-]');
	var volumesChapterCount=0;
	for (var i=0;i<volumes.length;i++){
		if ($(volumes[i]).val()!='' && $(volumes[i]).val()>0) {
			volumesChapterCount+=parseInt($(volumes[i]).val());
		}
	}
	var chapterCount = parseInt($('#chapter-list-table').attr('data-count'));
	var normalChapterCount = 0;

	for (var i=1;i<=chapterCount;i++){
		if ($('#form-chapter-list-num-'+i).val()!=''){
			normalChapterCount++;
		}
	}
	if (normalChapterCount!=volumesChapterCount){
		alert('El nombre de capítols numerats de la llista ha de coincidir amb el nombre de capítols indicat als volums.');
		return false;
	}
	for (var i=1;i<=chapterCount;i++){
		if ($('#form-chapter-list-num-'+i).val()=='' && $('#form-chapter-list-name-'+i).val()==''){
			alert('Hi ha capítols sense número ni nom. Els capítols normals han de tenir com a mínim número, i els capítols especials han de tenir com a mínim nom.');
			return false;
		}
	}

	var volumeNumbers = [];
	for (var i=0;i<volumes.length;i++){
		volumeNumbers.push($('#form-volume-list-number-'+(i+1)).val());
	}

	var higherThanCount=false;
	for (var i=1;i<=chapterCount;i++){
		if ($('#form-chapter-list-volume-'+i).val()!='' && !volumeNumbers.includes($('#form-chapter-list-volume-'+i).val())){
			alert('Hi ha capítols de volums inexistents. Corregeix-ho.');
			return false;
		}
		if ($('#form-chapter-list-num-'+i).val()!='' && $('#form-chapter-list-num-'+i).val()==0){
			alert('0 no és un número de capítol vàlid. Corregeix-ho.');
			return false;
		}
		if ($('#form-chapter-list-num-'+i).val()>chapterCount){
			higherThanCount=true;
		}
	}

	if (higherThanCount && !confirm('Hi ha números de capítol més alts que el nombre total de capítols. Segur que és correcte? Si dubtes, cancel·la i revisa-ho.')){
		return false;
	}

	if (!$('#id').val() && !$('#form-image').val() && !$('#form-image_url').val()) {
		alert('Cal que pugis una imatge de portada.');
		return false;
	}

	if (!$('#id').val() && !$('#form-featured_image').val()) {
		alert('Cal que pugis una imatge de capçalera.');
		return false;
	}

	if ($('#form-name-with-autocomplete').val()==$('#form-alternate_names').val()) {
		alert('El nom i el camp "altres noms" no poden ser iguals. Si no se\'n tradueix el nom, el camp "altres noms" ha de romandre buit o amb altres noms diferents, si s\'escau (en anglès, per exemple).');
		return false;
	}

	if (document.getElementById('form-image-preview').naturalWidth>450 || document.getElementById('form-image-preview').naturalHeight>600) {
		alert('La imatge de portada té unes dimensions massa grosses. El màxim són 450x600 píxels.');
		return false;
	}

	if (document.getElementById('form-featured-image-preview').naturalWidth>1200 || document.getElementById('form-featured-image-preview').naturalHeight>400) {
		alert('La imatge de capçalera té unes dimensions massa grosses. El màxim són 1200x400 píxels.');
		return false;
	}

	return true;
}

function checkCoverListImages() {
	var covers = $('[id$=_preview]');
	var affectedCovers='';
	for (var i=0;i<covers.length;i++){
		if (covers[i].naturalWidth>300 || covers[i].naturalHeight>400) {
			affectedCovers+=("\n- "+$($(covers[i].parentElement).find('label')[0]).text());
		}
	}

	if (affectedCovers!='') {
		alert('Les imatges de portada següents tenen unes dimensions massa grosses (el màxim són 300x400 píxels):'+affectedCovers);
		return false;
	}

	var files = $("[type=file]");
	var totalBytes = 0;
	for (var i=0;i<files.length;i++){
		if (files[i].files && files[i].files.length>0) {
			totalBytes+=files[i].files[0].size;
		}
	}

	if (totalBytes>262144000) {
		alert('La mida total dels fitxers pujats no pot excedir de 250 MiB. Si us plau, puja\'ls en diverses tandes.');
		return false;
	}

	return true;
}

function checkFansub() {
	if (!$('#form-id').val() && !$('#form-icon').val()) {
		alert('Cal que pugis una icona.');
		return false;
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
	
	var matchesMega = links[i].link.match(/https:\/\/mega(?:\.co)?\.nz\/(?:#!|embed#!|file\/|embed\/)?([a-zA-Z0-9]{0,8})[!#]([a-zA-Z0-9_-]+)/);
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

function checkImageUpload(fileInput, maxBytes, previewImageId, previewLinkId, optionalUrlId) {
	if (fileInput.files && fileInput.files[0]) {
		if (maxBytes!=-1 && fileInput.files[0].size>maxBytes) {
			alert('El fitxer que has seleccionat és massa gros. Com a màxim ha de fer '+(maxBytes/1024)+' KiB.');
		} else {
			var reader = new FileReader();
			reader.onload = function(e) {
				$('#'+previewImageId).attr('src',e.target.result);
				if (previewLinkId) {
					$('#'+previewLinkId).attr('href',e.target.result);
				}
				if (optionalUrlId) {
					$('#'+optionalUrlId).val('');
				}
				$('label[for="'+fileInput.id+'"]').removeClass("btn-warning");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-secondary");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-info");
				$('label[for="'+fileInput.id+'"]').addClass("btn-success");
				$('label[for="'+fileInput.id+'"]').html('<span class="fa fa-check pr-2"></span>Es pujarà');
			};
			reader.readAsDataURL(fileInput.files[0]);
			return;
		}
	}
	//Non-success cases: reset input
	if (optionalUrlId && $('#'+optionalUrlId).val()) {
		$('#'+previewImageId).prop('src',$('#'+previewImageId).attr('data-original'));
	} else if ($('#'+previewImageId).attr('data-original')) {
		$('#'+previewImageId).prop('src',$('#'+previewImageId).attr('data-original'));
	} else {
		$('#'+previewImageId).prop('src','');
	}
	if (previewLinkId) {
		if (optionalUrlId && $('#'+optionalUrlId).val()) {
			$('#'+previewLinkId).attr('href',$('#'+previewImageId).attr('data-original'));
		} else if ($('#'+previewLinkId).attr('data-original')) {
			$('#'+previewLinkId).attr('href',$('#'+previewLinkId).attr('data-original'));
		} else {
			$('#'+previewLinkId).attr('href','');
		}
	}
	resetFileInput($(fileInput));
}

function resetFileInput(fileInput) {
	$('label[for="'+fileInput.attr('id')+'"]').removeClass("btn-warning");
	$('label[for="'+fileInput.attr('id')+'"]').removeClass("btn-info");
	$('label[for="'+fileInput.attr('id')+'"]').removeClass("btn-success");
	$('label[for="'+fileInput.attr('id')+'"]').addClass("btn-secondary");
	$('label[for="'+fileInput.attr('id')+'"]').html('<span class="fa fa-times pr-2"></span>Sense canvis');
}

var malData;
var malDataStaff;
var malDataSeasonsEpisodes;
var malDataEpisodes;
var malDataMessages;
var uncompressReady = false;

$(document).ready(function() {
	loadArchiveFormats(['rar', 'zip'], function() {
		uncompressReady = true;
	});

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
		if (($("#form-name-with-autocomplete").val()!='' || $("#form-synopsis").val()!='') && !confirm("ATENCIÓ! La fitxa ja conté dades. Si continues, se sobreescriuran les dades d'autor, director, estudi, valoració per edats, gèneres i imatge de portada, i també s'ompliran els camps que siguin buits.\nL'acció no es podrà desfer un cop hagis desat els canvis. Vols continuar?")) {
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
			alert("Hi ha temporades sense identificador de MyAnimeList. Cal que especifiquis l'identificador de totes.");
			return;
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

	$("#generate-chapters").click(function() {
		var volumes = $('[id^=form-volume-list-chapters-]');
		var with_chapters=0;
		for (var i=0;i<volumes.length;i++){
			if ($(volumes[i]).val()!='' && $(volumes[i]).val()>0) {
				with_chapters++;
			}
		}
		if (with_chapters!=volumes.length) {
			alert("Per a poder-los generar, cal que introdueixis el nombre de capítols de cada volum.");
			return;
		}

		if ((parseInt($('#chapter-list-table').attr('data-count'))>1 || $('#form-chapter-list-name-1').val()!='') && !confirm("ATENCIÓ! Ja hi ha dades de capítols. Si continues, se suprimiran tots i es tornaran a crear. Totes les versions que continguin aquests capítols i tots els fitxers d'aquests capítols desapareixeran i no es podrà desfer l'acció un cop hagis desat els canvis. Vols continuar?")) {
			return;
		}

		var restart = (volumes.length==1 || confirm("Vols reiniciar la numeració de capítols a cada volum? Si és així, prem 'D'acord', en cas contrari, prem 'Cancel·la'."));

		var i = parseInt($('#chapter-list-table').attr('data-count'));
		for (var id=1;id<i+1;id++) {
			$("#form-chapter-list-row-"+id).remove();
		}
		$('#chapter-list-table').attr('data-count', 0);

		var rowNumber=1;

		for (var i=0;i<volumes.length;i++) {
			for (var j=0;j<$(volumes[i]).val();j++) {
				addChapterRow(false);
				$("#form-chapter-list-volume-"+rowNumber).val($('#form-volume-list-number-'+(i+1)).val());
				$("#form-chapter-list-num-"+rowNumber).val(restart ? j+1 : rowNumber);
				$("#form-chapter-list-name-"+rowNumber).val('');
				rowNumber++;
			}
		}
	});

	$("#import-from-mal-manga").click(function() {
		if ($("#form-myanimelist_id").val()=='') {
			var result = prompt("Introdueix l'URL del manga a MyAnimeList per a importar-ne la fitxa.");
			if (!result) {
				return;
			} else if (result.match(/https?:\/\/.*myanimelist.net\/manga\/(\d+)\//i)) {
				$("#form-myanimelist_id").val(result.match(/https?:\/\/.*myanimelist.net\/manga\/(\d*)\//i)[1]);
			} else {
				alert("L'URL no és vàlida.");
				return;
			}
		}
		if (($("#form-name-with-autocomplete").val()!='' || $("#form-synopsis").val()!='') && !confirm("ATENCIÓ! La fitxa ja conté dades. Si continues, se sobreescriuran les dades d'autor, valoració per edats, gèneres i imatge de portada, i també s'ompliran els camps que siguin buits.\nL'acció no es podrà desfer un cop hagis desat els canvis. Vols continuar?")) {
			return;
		}
		$("#import-from-mal-manga").prop('disabled', true);
		$("#import-from-mal-loading").removeClass("d-none");
		$("#import-from-mal-not-loading").addClass("d-none");
		var xmlhttp = new XMLHttpRequest();
		var url = "https://api.jikan.moe/v3/manga/"+$("#form-myanimelist_id").val();

		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				malData = JSON.parse(this.responseText);
				populateMalDataManga(malData);
				$("#import-from-mal-loading").addClass("d-none");
				$("#import-from-mal-not-loading").removeClass("d-none");
				setTimeout(function() {
					$("#import-from-mal-manga").prop('disabled', false);
				}, 4000);
				$("#import-from-mal-done").removeClass("d-none");
			} else if (this.readyState == 4) {
				alert("S'ha produït un error en obtenir dades de MyAnimeList, torna-ho a provar més tard.");
				$("#import-from-mal-loading").addClass("d-none");
				$("#import-from-mal-not-loading").removeClass("d-none");
				setTimeout(function() {
					$("#import-from-mal-manga").prop('disabled', false);
				}, 4000);
			}
		};
		xmlhttp.open("GET", url, true);
		xmlhttp.send();
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
			if ($('#import-type').val()!='sync' || $('#form-folders-list-active-'+i).prop('checked')) {
				account_ids.push(encodeURIComponent($('#form-folders-list-account_id-'+i).val()));
				folders.push(encodeURIComponent($('#form-folders-list-folder-'+i).val()));
				season_ids.push(encodeURIComponent($('#form-folders-list-season_id-'+i).val()!='' ? $('#form-folders-list-season_id-'+i).val() : -1));
			}
		}

		var xmlhttp = new XMLHttpRequest();
		var url = "fetch_storage_links.php?series_id="+$('[name="series_id"]').val()+"&import_type="+$('#import-type').val()+"&account_ids[]="+account_ids.join("&account_ids[]=")+"&folders[]="+folders.join("&folders[]=")+"&season_ids[]="+season_ids.join("&season_ids[]=");

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
	$("#form-historical").change(function() {
		if ($(this).prop('checked')) {
			$("#form-archive_url").prop('disabled', false);
			$("#form-archive_url").prop('required', true);
		} else {
			$("#form-archive_url").val('');
			$("#form-archive_url").prop('disabled', true);
			$("#form-archive_url").prop('required', false);
		}
	});
});
