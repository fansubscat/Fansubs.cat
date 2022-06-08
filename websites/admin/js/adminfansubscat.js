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

function copyToClipboard(text, el) {
	var copyTest = document.queryCommandSupported('copy');
	var elOriginalText = el.attr('data-original-title');

	if (copyTest === true) {
		var copyTextArea = document.createElement("textarea");
		copyTextArea.value = text;
		document.body.appendChild(copyTextArea);
		copyTextArea.select();
		try {
			var successful = document.execCommand('copy');
			var msg = successful ? 'Copiat!' : 'No s\'ha pogut copiar!';
			el.attr('data-original-title', msg).tooltip({trigger: 'manual'});
			el.attr('data-original-title', msg).tooltip('show');

			setTimeout(function(){
				el.tooltip('hide');
			}, 2000);
		} catch (err) {
			console.log('Oops, unable to copy');
		}
		document.body.removeChild(copyTextArea);
		el.attr('data-original-title', elOriginalText);
	} else {
		// Fallback if browser doesn't support .execCommand('copy')
		window.prompt("Copia al porta-retalls: Control+C i prem Intro.", text);
	}
}

function populateMalData(response, staffResponse) {
	if ($("#form-name-with-autocomplete").val()=='') {
		$("#form-name-with-autocomplete").val(response.data.title);
	}
	if ($("#form-slug").val()=='') {
		$("#form-slug").val(string_to_slug(response.data.title));
	}
	if ($("#form-alternate_names").val()=='') {
		if (response.data.title && response.data.title_english && response.data.title_english!=response.data.title) {
			$("#form-alternate_names").val(response.data.title+', '+response.data.title_english);
		} else if (response.data.title) {
			$("#form-alternate_names").val(response.data.title);
		} else if (response.data.title_english) {
			$("#form-alternate_names").val(response.data.title_english);
		}
	}
	if ($("#form-score").val()=='') {
		$("#form-score").val(response.data.score ? response.data.score : '');
	}
	if ($("#form-subtype").val()=='') {
		$("#form-subtype").val(response.data.episodes==1 ? 'movie' : 'series');
	}
	if ($("#form-publish_date").val()=='') {
		$("#form-publish_date").val(response.data.aired.from.substr(0, 10));
	}
	if (response.data.rating=='G - All Ages') {
		$("#form-rating").val('TP');
	} else if (response.data.rating=='PG - Children') {
		$("#form-rating").val('+7');
	} else if (response.data.rating=='PG-13 - Teens 13 or older') {
		$("#form-rating").val('+13');
	} else if (response.data.rating=='R - 17+ (violence & profanity)') {
		$("#form-rating").val('+16');
	} else if (response.data.rating=='R+ - Mild Nudity') {
		$("#form-rating").val('+18');
	} else if (response.data.rating=='Rx - Hentai') {
		$("#form-rating").val('XXX');
	} else {
		$("#form-rating").val('');
	}
	if ($("#form-synopsis").val()=='') {
		$("#form-synopsis").val(response.data.synopsis);
	}
	if ($("#form-duration").val()=='') {
		$("#form-duration").val(response.data.duration ? response.data.duration.replace('per ep','per capítol').replace('hr','h').replace('Unknown','') : null);
	}

	var url = (response.data.images && response.data.images.jpg && response.data.images.jpg.large_image_url) ? response.data.images.jpg.large_image_url : null;
	if (document.getElementById('form-image').files.length>0) {
		resetFileInput($("#form-image"));
	}
	$("#form-image_url").val(url);
	$('#form-image-preview').prop('src', url);
	$('#form-image-preview-link').prop('href', url);

	if (response.data.episodes==1) {
		//Movie, populate first episode
		if ($('#form-episode-list-description-1').val()=='') {
			$('#form-episode-list-description-1').val($("#form-name-with-autocomplete").val());
		}
	}

	var authors = staffResponse.data.filter(function(value, index, array) {
		return value.positions.includes("Original Creator");
	});

	var textAuthors = "";
	for (var i = 0; i < authors.length; i++) {
		var authorName;
		if (authors[i].person.name.includes(', ')) {
			authorName=authors[i].person.name.split(', ')[1]+" "+authors[i].person.name.split(', ')[0];
		} else {
			authorName=authors[i].person.name;
		}

		if (textAuthors!='') {
			textAuthors+=', ';
		}
		textAuthors+=authorName;
	}

	$("#form-author").val(textAuthors);

	var directors = staffResponse.data.filter(function(value, index, array) {
		return value.positions.includes("Director");
	});

	var textDirectors = "";
	for (var i = 0; i < directors.length; i++) {
		var directorName;
		if (directors[i].person.name.includes(', ')) {
			directorName=directors[i].person.name.split(', ')[1]+" "+directors[i].person.name.split(', ')[0];
		} else {
			directorName=directors[i].person.name;
		}

		if (textDirectors!='') {
			textDirectors+=', ';
		}
		textDirectors+=directorName;
	}

	$("#form-director").val(textDirectors);

	var textStudios = "";
	for (var i = 0; i < response.data.studios.length; i++) {
		if (textStudios!='') {
			textStudios+=', ';
		}
		textStudios+=response.data.studios[i].name;
	}

	$("#form-studio").val(textStudios);

	$("[name='genres[]']").each(function() {
		$(this).prop('checked', false);
	});
	
	for (var i = 0; i < response.data.genres.length; i++) {
		$("[data-external-id='"+response.data.genres[i].mal_id+"']").prop('checked', true);
	}
	
	for (var i = 0; i < response.data.explicit_genres.length; i++) {
		$("[data-external-id='"+response.data.explicit_genres[i].mal_id+"']").prop('checked', true);
	}
	
	for (var i = 0; i < response.data.themes.length; i++) {
		$("[data-external-id='"+response.data.themes[i].mal_id+"']").prop('checked', true);
	}
	
	for (var i = 0; i < response.data.demographics.length; i++) {
		$("[data-external-id='"+response.data.demographics[i].mal_id+"']").prop('checked', true);
	}

	if ($("#form-division-list-number_of_episodes-1").val()=='') {
		if (response.data.episodes) {
			$("#form-division-list-number_of_episodes-1").val(response.data.episodes);
		}
	}

	if ($("#form-division-list-external_id-1").val()=='') {
		$("#form-division-list-external_id-1").val(response.data.mal_id);
	}
}

function populateMalDataManga(response) {
	if ($("#form-name-with-autocomplete").val()=='') {
		$("#form-name-with-autocomplete").val(response.data.title);
	}
	if ($("#form-slug").val()=='') {
		$("#form-slug").val(string_to_slug(response.data.title));
	}
	if ($("#form-alternate_names").val()=='') {
		if (response.data.title && response.data.title_english && response.data.title_english!=response.data.title) {
			$("#form-alternate_names").val(response.data.title+', '+response.data.title_english);
		} else if (response.data.title) {
			$("#form-alternate_names").val(response.data.title);
		} else if (response.data.title_english) {
			$("#form-alternate_names").val(response.data.title_english);
		}
	}
	if ($("#form-score").val()=='') {
		$("#form-score").val(response.data.score ? response.data.score : '');
	}
	if ($("#form-subtype").val()=='') {
		$("#form-subtype").val(response.data.type=='One-shot' ? 'oneshot' : 'serialized');
	}
	if ($("#form-publish_date").val()=='') {
		$("#form-publish_date").val(response.data.published.from.substr(0, 10));
	}
	if (response.data.type=='Manhwa') {
		$("#form-comic_type").val('manhwa');
	} else if (response.data.type=='Manhua') {
		$("#form-comic_type").val('manhua');
	} else {
		$("#form-comic_type").val('manga');
	}
	if ($("#form-synopsis").val()=='') {
		$("#form-synopsis").val(response.data.synopsis);
	}

	var url = (response.data.images && response.data.images.jpg && response.data.images.jpg.large_image_url) ? response.data.images.jpg.large_image_url : null;
	if (document.getElementById('form-image').files.length>0) {
		resetFileInput($("#form-image"));
	}
	$("#form-image_url").val(url);
	$('#form-image-preview').prop('src', url);
	$('#form-image-preview-link').prop('href', url);

	if (response.data.chapters==1) {
		//One-shot, populate first chapter
		if ($('#form-episode-list-description-1').val()=='') {
			$('#form-episode-list-description-1').val($("#form-name-with-autocomplete").val());
		}
	}

	var textAuthors = "";
	for (var i = 0; i < response.data.authors.length; i++) {
		var authorName;
		if (response.data.authors[i].name.includes(', ')) {
			authorName=response.data.authors[i].name.split(', ')[1]+" "+response.data.authors[i].name.split(', ')[0];
		} else {
			authorName=response.data.authors[i].name;
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
	
	for (var i = 0; i < response.data.genres.length; i++) {
		$("[data-external-id='"+response.data.genres[i].mal_id+"']").prop('checked', true);
	}
	
	for (var i = 0; i < response.data.explicit_genres.length; i++) {
		$("[data-external-id='"+response.data.explicit_genres[i].mal_id+"']").prop('checked', true);
	}
	
	for (var i = 0; i < response.data.themes.length; i++) {
		$("[data-external-id='"+response.data.themes[i].mal_id+"']").prop('checked', true);
	}
	
	for (var i = 0; i < response.data.demographics.length; i++) {
		$("[data-external-id='"+response.data.demographics[i].mal_id+"']").prop('checked', true);
	}

	if (response.data.volumes && response.data.volumes>1) {
		for (var i=0;i<response.data.volumes;i++){
			if ((i+1)>$('#division-list-table').attr('data-count')){
				addDivisionRow();
			}
		}
		if (response.data.chapters) {
			var howMany = prompt("S'importaran els volums, però no sabem quants capítols té cadascun. Segons MyAnimeList, en total n'hi ha "+response.data.chapters+" repartits en "+response.data.volumes+" volums (aprox. uns "+Math.floor(response.data.chapters/response.data.volumes)+" capítols per volum). Amb l'objectiu de facilitar introduir les dades, podem assignar-ne una quantitat fixa a cada volum: introdueix-la. Si ho cancel·les o no introdueixes res, s'assignaran tots al primer volum. En qualsevol cas, assegura't de revisar que tot sigui correcte abans d'afegir el manga.");
			if (!howMany || !howMany.match(/^-?[0-9]+$/)) {
				$("#form-division-list-number_of_episodes-1").val(response.data.chapters);
			} else {
				for (var i=1;i<=response.data.volumes;i++){
					$("#form-division-list-number_of_episodes-"+i).val(howMany);
				}
			}
		}
	}

	if ($("#form-division-list-number_of_episodes-1").val()=='') {
		if (response.data.chapters) {
			$("#form-division-list-number_of_episodes-1").val(response.data.chapters);
		}
	}

	if ($("#form-division-list-external_id-1").val()=='') {
		$("#form-division-list-external_id-1").val(response.data.mal_id);
	}
}

function populateMalEpisodes(division_line, episodes) {
	if (division_line==1) {
		var i = parseInt($('#episode-list-table').attr('data-count'));
		for (var id=1;id<i+1;id++) {
			$("#form-episode-list-row-"+id).remove();
		}
		$('#episode-list-table').attr('data-count', 0);
	}

	initialNumber = parseInt($('#episode-list-table').attr('data-count'))+1;

	for (var i=0;i<episodes.length;i++) {
		addRow(false);
		$("#form-episode-list-division-"+(i+initialNumber)).val($('#form-division-list-number-'+division_line).val());
		$("#form-episode-list-num-"+(i+initialNumber)).val(episodes[i].mal_id);
		$("#form-episode-list-description-"+(i+initialNumber)).val(episodes[i].title);
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
	$('#episode-list-table').append('<tr id="form-episode-list-row-'+i+'"><td><input id="form-episode-list-division-'+i+'" name="form-episode-list-division-'+i+'" type="number" class="form-control" value="" placeholder="(Altres)"/></td><td><input id="form-episode-list-num-'+i+'" name="form-episode-list-num-'+i+'" type="number" class="form-control" value="" placeholder="(Esp.)" step="any"/><input id="form-episode-list-id-'+i+'" name="form-episode-list-id-'+i+'" type="hidden" value="-1"/><input id="form-episode-list-has_version-'+i+'" type="hidden" value="0"/></td><td><input id="form-episode-list-description-'+i+'" name="form-episode-list-description-'+i+'" type="text" class="form-control" value="" placeholder="(Sense descripció)"/></td><td class="text-center align-middle"><button id="form-episode-list-delete-'+i+'" onclick="deleteRow('+i+');" type="button" class="btn fa fa-trash p-1 text-danger"></button></td></tr>');
	$('#episode-list-table').attr('data-count', i);

	if (!extra) {
		$('#form-episode-list-division-'+i).val($('#form-episode-list-division-'+(i-1)).val()!='' ? $('#form-episode-list-division-'+(i-1)).val() : 1);
		$('#form-episode-list-num-'+i).val($('#form-episode-list-num-'+(i-1)).val()!='' ? parseInt($('#form-episode-list-num-'+(i-1)).val())+1 : 1);
	}
}

function deleteRow(id) {
	var i = parseInt($('#episode-list-table').attr('data-count'));
	if(i==1) {
		alert('La fitxa ha de tenir un capítol, com a mínim!');
	}
	else if ($('#form-episode-list-has_version-'+id).val()==1) {
		alert('Aquest capítol ja té fitxers en alguna versió d\'algun fansub. No es pot suprimir perquè els fitxers d\'aquella versió deixarien de funcionar. Si realment el vols suprimir, primer caldria que suprimissis els fitxers de la versió. Si tens dubtes, contacta amb un administrador.');
	} else {
		$("#form-episode-list-row-"+id).remove();
		for (var j=id+1;j<i+1;j++) {
			$("#form-episode-list-row-"+j).attr('id','form-episode-list-row-'+(j-1));
			$("#form-episode-list-id-"+j).attr('name','form-episode-list-id-'+(j-1));
			$("#form-episode-list-id-"+j).attr('id','form-episode-list-id-'+(j-1));
			$("#form-episode-list-has_version-"+j).attr('id','form-episode-list-has_version-'+(j-1));
			$("#form-episode-list-division-"+j).attr('name','form-episode-list-division-'+(j-1));
			$("#form-episode-list-division-"+j).attr('id','form-episode-list-division-'+(j-1));
			$("#form-episode-list-num-"+j).attr('name','form-episode-list-num-'+(j-1));
			$("#form-episode-list-num-"+j).attr('id','form-episode-list-num-'+(j-1));
			$("#form-episode-list-description-"+j).attr('name','form-episode-list-description-'+(j-1));
			$("#form-episode-list-description-"+j).attr('id','form-episode-list-description-'+(j-1));
			$("#form-episode-list-delete-"+j).attr('onclick','deleteRow('+(j-1)+');');
			$("#form-episode-list-delete-"+j).attr('id','form-episode-list-delete-'+(j-1));
			$("#form-episode-list-delete-"+j).attr('class',$('form-episode-list-delete-'+(j-1)).attr('class'));
		}
		$('#episode-list-table').attr('data-count', i-1);
	}
}

function addDivisionRow() {
	var i = parseInt($('#division-list-table').attr('data-count'))+1;
	$('#division-list-table').append('<tr id="form-division-list-row-'+i+'"><td><input id="form-division-list-number-'+i+'" name="form-division-list-number-'+i+'" type="number" class="form-control" value="'+(parseInt($('#form-division-list-number-'+(i-1)).val())+1)+'" required/><input id="form-division-list-id-'+i+'" name="form-division-list-id-'+i+'" type="hidden" value="-1"/></td><td><input id="form-division-list-name-'+i+'" name="form-division-list-name-'+i+'" type="text" class="form-control" value="" placeholder="(Sense nom)"/></td><td><input id="form-division-list-number_of_episodes-'+i+'" name="form-division-list-number_of_episodes-'+i+'" type="number" class="form-control" value="" required/></td><td><input id="form-division-list-external_id-'+i+'" name="form-division-list-external_id-'+i+'" type="number" class="form-control" value=""/></td><td class="text-center align-middle"><button id="form-division-list-delete-'+i+'" onclick="deleteDivisionRow('+i+');" type="button" class="btn fa fa-trash p-1 text-danger"></button></td></tr>');
	$('#division-list-table').attr('data-count', i);
}

function deleteDivisionRow(id) {
	var i = parseInt($('#division-list-table').attr('data-count'));
	if(i==1) {
		alert('La fitxa ha de tenir una temporada/volum, com a mínim!');
	}
	else {
		$("#form-division-list-row-"+id).remove();
		for (var j=id+1;j<i+1;j++) {
			$("#form-division-list-row-"+j).attr('id','form-division-list-row-'+(j-1));
			$("#form-division-list-id-"+j).attr('name','form-division-list-id-'+(j-1));
			$("#form-division-list-id-"+j).attr('id','form-division-list-id-'+(j-1));
			$("#form-division-list-number-"+j).attr('name','form-division-list-number-'+(j-1));
			$("#form-division-list-number-"+j).attr('id','form-division-list-number-'+(j-1));
			$("#form-division-list-name-"+j).attr('name','form-division-list-name-'+(j-1));
			$("#form-division-list-name-"+j).attr('id','form-division-list-name-'+(j-1));
			$("#form-division-list-number_of_episodes-"+j).attr('name','form-division-list-number_of_episodes-'+(j-1));
			$("#form-division-list-number_of_episodes-"+j).attr('id','form-division-list-number_of_episodes-'+(j-1));
			$("#form-division-list-external_id-"+j).attr('name','form-division-list-external_id-'+(j-1));
			$("#form-division-list-external_id-"+j).attr('id','form-division-list-external_id-'+(j-1));
			$("#form-division-list-delete-"+j).attr('onclick','deleteDivisionRow('+(j-1)+');');
			$("#form-division-list-delete-"+j).attr('id','form-division-list-delete-'+(j-1));
		}
		$('#division-list-table').attr('data-count', i-1);
	}
}

function addLinkRow(episode_id, variant_number) {
	var i = parseInt($('#links-list-table-'+episode_id+'-'+variant_number).attr('data-count'))+1;
	$('#links-list-table-'+episode_id+'-'+variant_number+' tbody').append('<tr id="form-links-list-'+episode_id+'-row-'+variant_number+'-'+i+'" style="background: none;"><td class="pl-0 pt-0 pb-0 border-0"><input id="form-files-list-'+episode_id+'-file-'+variant_number+'-link-'+i+'-url" name="form-files-list-'+episode_id+'-file-'+variant_number+'-link-'+i+'-url" type="url" class="form-control" value="" maxlength="2048" placeholder="(Sense enllaç)" oninput="$(this).attr(\'value\',$(this).val());"/><input id="form-files-list-'+episode_id+'-file-'+variant_number+'-link-'+i+'-id" name="form-files-list-'+episode_id+'-file-'+variant_number+'-link-'+i+'-id" type="hidden" value="-1"/></td><td class="pt-0 pb-0 border-0" style="width: 22%;"><input id="form-files-list-'+episode_id+'-file-'+variant_number+'-link-'+i+'-resolution" name="form-files-list-'+episode_id+'-file-'+variant_number+'-link-'+i+'-resolution" type="text" class="form-control" list="resolution-options" value="" maxlength="200" placeholder="- Tria -"/></td><td class="pt-0 pb-0 border-0 text-center align-middle" style="width: 5%;"><button id="form-files-list-'+episode_id+'-file-'+variant_number+'-link-'+i+'-delete" onclick="deleteLinkRow('+episode_id+','+variant_number+','+i+');" type="button" class="btn fa fa-fw fa-times p-1 text-danger" title="Suprimeix aquest enllaç"></button></td></tr>');
	$('#links-list-table-'+episode_id+'-'+variant_number).attr('data-count', i);
}

function addExtraLinkRow(extra_number) {
	var i = parseInt($('#extras-links-list-table-'+extra_number).attr('data-count'))+1;
	$('#extras-links-list-table-'+extra_number+' tbody').append('<tr id="form-links-extras-list-row-'+extra_number+'-'+i+'" style="background: none;"><td class="pl-0 pt-0 pb-0 border-0"><input id="form-extras-list-'+extra_number+'-link-'+i+'-url" name="form-extras-list-'+extra_number+'-link-'+i+'-url" type="url" class="form-control" value="" maxlength="2048" placeholder="- Introdueix un enllaç -" oninput="$(this).attr(\'value\',$(this).val());" required/><input id="form-extras-list-'+extra_number+'-link-'+i+'-id" name="form-extras-list-'+extra_number+'-link-'+i+'-id" type="hidden" value="-1"/></td><td class="pt-0 pb-0 border-0" style="width: 22%;"><input id="form-extras-list-'+extra_number+'-link-'+i+'-resolution" name="form-extras-list-'+extra_number+'-link-'+i+'-resolution" type="text" class="form-control" list="resolution-options" value="" maxlength="200" placeholder="- Tria -" required/></td><td class="pt-0 pb-0 border-0 text-center align-middle" style="width: 5%;"><button id="form-extras-list-'+extra_number+'-link-'+i+'-delete" onclick="deleteExtraLinkRow('+extra_number+','+i+');" type="button" class="btn fa fa-fw fa-times p-1 text-danger" title="Suprimeix aquest enllaç"></button></td></tr>');
	$('#extras-links-list-table-'+extra_number).attr('data-count', i);
}

function addVersionRow(episode_id) {
	if (isAutoFetchActive()){
		alert("Si hi ha activada la sincronització automàtica de carpetes, no és possible afegir més d'una variant per capítol. Abans has de desactivar-la.");
		return;
	}

	var i = parseInt($('#files-list-table-'+episode_id).attr('data-count'))+1;

	var contents = '<tr id="form-files-list-'+episode_id+'-row-'+i+'"><td><input id="form-files-list-'+episode_id+'-variant_name-'+i+'" name="form-files-list-'+episode_id+'-variant_name-'+i+'" type="text" class="form-control" value="" maxlength="200" placeholder="- Variant -" required/><input id="form-files-list-'+episode_id+'-id-'+i+'" name="form-files-list-'+episode_id+'-id-'+i+'" type="hidden" value="-1"/></td>';
	if ($('#type').val()=='manga') {
		contents += '<td class="align-middle"><div id="form-files-list-'+episode_id+'-file_details-'+i+'" class="small"><span style="color: gray;"><span class="fa fa-times fa-fw"></span> No hi ha cap fitxer pujat.</span></div></td><td class="align-middle"><label style="margin-bottom: 0;" for="form-files-list-'+episode_id+'-file-'+i+'" class="btn btn-sm btn-info w-100"><span class="fa fa-upload pr-2"></span>Puja un fitxer...</label><input id="form-files-list-'+episode_id+'-file-'+i+'" name="form-files-list-'+episode_id+'-file-'+i+'" type="file" accept=".zip,.rar,.cbz" class="form-control d-none" onchange="uncompressFile(this);"/><input id="form-files-list-'+episode_id+'-length-'+i+'" name="form-files-list-'+episode_id+'-length-'+i+'" type="hidden" value="0"/></td>';
	} else {
		contents += '<td><table class="w-100" id="links-list-table-'+episode_id+'-'+i+'" data-count="1"><tbody><tr id="form-links-list-'+episode_id+'-row-'+i+'-1" style="background: none;"><td class="pl-0 pt-0 pb-0 border-0"><input id="form-files-list-'+episode_id+'-file-'+i+'-link-1-url" name="form-files-list-'+episode_id+'-file-'+i+'-link-1-url" type="url" class="form-control" value="" maxlength="2048" placeholder="(Sense enllaç)" oninput="$(this).attr(\'value\',$(this).val());"/><input id="form-files-list-'+episode_id+'-file-'+i+'-link-1-id" name="form-files-list-'+episode_id+'-file-'+i+'-link-1-id" type="hidden" value="-1"/></td><td class="pt-0 pb-0 border-0" style="width: 22%;"><input id="form-files-list-'+episode_id+'-file-'+i+'-link-1-resolution" name="form-files-list-'+episode_id+'-file-'+i+'-link-1-resolution" type="text" class="form-control" list="resolution-options" value="" maxlength="200" placeholder="- Tria -"/></td><td class="pt-0 pb-0 border-0 text-center align-middle" style="width: 5%;"><button id="form-files-list-'+episode_id+'-file-'+i+'-link-1-delete" onclick="deleteLinkRow('+episode_id+','+i+',1);" type="button" class="btn fa fa-fw fa-times p-1 text-danger" title="Suprimeix aquest enllaç"></button></td></tr></tbody><tfoot><tr style="background: none;"><td colspan="3" class="text-center p-0 border-0"><button id="form-files-list-'+episode_id+'-add_link-'+i+'" onclick="addLinkRow('+episode_id+','+i+');" type="button" class="btn btn-success btn-sm" style="margin-top: 0.25em;"><span class="fa fa-fw fa-plus pr-2"></span>Afegeix un altre enllaç</button></td></tr></tfoot></table></td><td><input id="form-files-list-'+episode_id+'-length-'+i+'" name="form-files-list-'+episode_id+'-length-'+i+'" pattern="^\\d+:[0-5]\\d:[0-5]\\d$" class="form-control" required/>';
	}

	contents += '<td><input id="form-files-list-'+episode_id+'-comments-'+i+'" name="form-files-list-'+episode_id+'-comments-'+i+'" type="text" class="form-control" value="" maxlength="200"/></td><td class="text-center" style="padding-top: .75rem;"><input id="form-files-list-'+episode_id+'-is_lost-'+i+'" name="form-files-list-'+episode_id+'-is_lost-'+i+'" type="checkbox" value="1"/></td><td class="text-center pt-2"><button id="form-files-list-'+episode_id+'-delete-'+i+'" onclick="deleteVersionRow('+episode_id+','+i+');" type="button" class="btn fa fa-trash p-1 text-danger"></button></td></tr>';

	$('#files-list-table-'+episode_id).append(contents);
	$('#files-list-table-'+episode_id).attr('data-count', i);
}

function addVersionExtraRow() {
	var i = parseInt($('#extras-list-table').attr('data-count'))+1;

	var contents = '<tr id="form-extras-list-row-'+i+'"><td><input id="form-extras-list-name-'+i+'" name="form-extras-list-name-'+i+'" type="text" class="form-control" value="" maxlength="200" required placeholder="- Introdueix un nom -"/><input id="form-extras-list-id-'+i+'" name="form-extras-list-id-'+i+'" type="hidden" value="-1"/></td>';
	
	if ($('#type').val()=='manga') {
		contents += '<td class="align-middle"><div id="form-extras-list-file_details-'+i+'" class="small"><span style="color: gray;"><span class="fa fa-times fa-fw"></span> No hi ha cap fitxer pujat.</span></div></td><td class="align-middle"><label style="margin-bottom: 0;" for="form-extras-list-file-'+i+'" class="btn btn-sm btn-info w-100"><span class="fa fa-upload pr-2"></span>Puja un fitxer...</label><input id="form-extras-list-file-'+i+'" name="form-extras-list-file-'+i+'" type="file" accept=".zip,.rar,.cbz" class="form-control d-none" onchange="uncompressFile(this);" required/><input id="form-extras-list-length-'+i+'" name="form-extras-list-length-'+i+'" type="hidden" value="-1"/></td>';
	} else {
		contents += '<td><table class="w-100" id="extras-links-list-table-'+i+'" data-count="1"><tbody><tr id="form-links-extras-list-row-'+i+'-1" style="background: none;"><td class="pl-0 pt-0 pb-0 border-0"><input id="form-extras-list-'+i+'-link-1-url" name="form-extras-list-'+i+'-link-1-url" type="url" class="form-control" value="" maxlength="2048" placeholder="- Introdueix un enllaç -" oninput="$(this).attr(\'value\',$(this).val());" required/><input id="form-extras-list-'+i+'-link-1-id" name="form-extras-list-'+i+'-link-1-id" type="hidden" value="-1"/></td><td class="pt-0 pb-0 border-0" style="width: 22%;"><input id="form-extras-list-'+i+'-link-1-resolution" name="form-extras-list-'+i+'-link-1-resolution" type="text" class="form-control" list="resolution-options" value="" maxlength="200" placeholder="- Tria -" required/></td><td class="pt-0 pb-0 border-0 text-center align-middle" style="width: 5%;"><button id="form-extras-list-'+i+'-link-1-delete" onclick="deleteExtraLinkRow('+i+',1);" type="button" class="btn fa fa-fw fa-times p-1 text-danger" title="Suprimeix aquest enllaç"></button></td></tr></tbody><tfoot><tr style="background: none;"><td colspan="3" class="text-center p-0 border-0"><button id="form-extras-list-add_link-'+i+'" onclick="addExtraLinkRow('+i+');" type="button" class="btn btn-success btn-sm" style="margin-top: 0.25em;"><span class="fa fa-fw fa-plus pr-2"></span>Afegeix un altre enllaç</button></td></tr></tfoot></table></td><td><input id="form-extras-list-length-'+i+'" name="form-extras-list-length-'+i+'" pattern="^\\d+:[0-5]\\d:[0-5]\\d$" class="form-control" required/></td>';
	}

	contents += '<td><input id="form-extras-list-comments-'+i+'" name="form-extras-list-comments-'+i+'" type="text" class="form-control" value="" maxlength="200"/></td><td class="text-center pt-2"><button id="form-extras-list-delete-'+i+'" onclick="deleteVersionExtraRow('+i+');" type="button" class="btn fa fa-trash p-1 text-danger"></button></td></tr>';

	$('#extras-list-table').append(contents);
	$('#extras-list-table').attr('data-count', i);
	$('#extras-list-table-empty').addClass('d-none');
}

function uncompressFile(fileInput) {
	var detailsElement=$(document.getElementById(fileInput.id.replace("file-", "file_details-")));
	var numberOfPagesElement=$(document.getElementById(fileInput.id.replace("file-", "length-")));
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
					if (!entry.name.includes('__MACOSX') ) {
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

function addVersionRemoteFolderRow() {
	var i = parseInt($('#remote_folders-list-table').attr('data-count'))+1;

	var htmlAcc = $('#form-remote_folders-list-remote_account_id-XXX').prop('outerHTML').replace(/XXX/g, i).replace(' d-none">','" required>');

	var htmlSea = $('#form-remote_folders-list-division_id-XXX').prop('outerHTML').replace(/XXX/g, i).replace(' d-none">','">');

	$('#remote_folders-list-table').append('<tr id="form-remote_folders-list-row-'+i+'"><td>'+htmlAcc+'<input id="form-remote_folders-list-id-'+i+'" name="form-remote_folders-list-id-'+i+'" type="hidden" value="-1"/></td><td><input id="form-remote_folders-list-folder-'+i+'" name="form-remote_folders-list-folder-'+i+'" class="form-control" value="" maxlength="200" required/></td><td>'+htmlSea+'</td><td class="text-center align-middle"><input id="form-remote_folders-list-is_active-'+i+'" name="form-remote_folders-list-is_active-'+i+'" type="checkbox" value="1"/></td><td class="text-center align-middle"><button id="form-remote_folders-list-delete-'+i+'" onclick="deleteVersionRemoteFolderRow('+i+');" type="button" class="btn fa fa-trash p-1 text-danger"></button></td></tr>');
	$('#remote_folders-list-table').attr('data-count', i);
	$('#remote_folders-list-table-empty').addClass('d-none');
}

function deleteVersionRow(episode_id, id) {
	if ($("#form-files-list-"+episode_id+"-id-"+id).val()=='-1' || ($("#form-files-list-"+episode_id+"-id-"+id).val()!='-1' && confirm($('#type').val()=='manga' ? "Segur que vols esborrar aquest fitxer? Se'n perdran totes les estadístiques. Si vols pujar-ne una versió amb canvis, fes servir el botó 'Canvia el fitxer'." : "Segur que vols esborrar aquest fitxer? Se'n perdran totes les estadístiques. Si vols pujar-ne una versió amb canvis, simplement canvia'n els enllaços."))) {
		var i = parseInt($('#files-list-table-'+episode_id).attr('data-count'));
		if(i==1) {
			$("#form-files-list-"+episode_id+"-id-1").val("-1");
			$("#form-files-list-"+episode_id+"-comments-1").val("");
			$("#form-files-list-"+episode_id+"-is_lost-1").prop('checked',false);
			if ($('#type').val()=='manga') {
				$("#form-files-list-"+episode_id+"-file-1").val("");
				$('label[for="form-files-list-'+episode_id+'-file-1"]').removeClass("btn-warning");
				$('label[for="form-files-list-'+episode_id+'-file-1"]').addClass("btn-info");
				$('label[for="form-files-list-'+episode_id+'-file-1"]').html('<span class="fa fa-upload pr-2"></span> Puja un fitxer...');
				$("#form-files-list-"+episode_id+"-file_details-1").html('<span style="color: gray;"><span class="fa fa-times fa-fw"></span> No hi ha cap fitxer pujat.</span>');
			} else {
				$("#form-files-list-"+episode_id+"-length-1").val("");
			}
			var numInstances = parseInt($('#links-list-table-'+episode_id+'-1').attr('data-count'));
			for (var k=numInstances;k>0;k--) {
				deleteLinkRow(episode_id,1,k);
			}
		}
		else {
			$("#form-files-list-"+episode_id+"-row-"+id).remove();
			for (var j=id+1;j<i+1;j++) {
				$("#form-files-list-"+episode_id+"-row-"+j).attr('id','form-files-list-'+episode_id+'-row-'+(j-1));
				$("#form-files-list-"+episode_id+"-id-"+j).attr('name','form-files-list-'+episode_id+'-id-'+(j-1));
				$("#form-files-list-"+episode_id+"-id-"+j).attr('id','form-files-list-'+episode_id+'-id-'+(j-1));
				$("#form-files-list-"+episode_id+"-variant_name-"+j).attr('name','form-files-list-'+episode_id+'-variant_name-'+(j-1));
				$("#form-files-list-"+episode_id+"-variant_name-"+j).attr('id','form-files-list-'+episode_id+'-variant_name-'+(j-1));
				$("#form-files-list-"+episode_id+"-length-"+j).attr('name','form-files-list-'+episode_id+'-length-'+(j-1));
				$("#form-files-list-"+episode_id+"-length-"+j).attr('id','form-files-list-'+episode_id+'-length-'+(j-1));
				$("#form-files-list-"+episode_id+"-comments-"+j).attr('name','form-files-list-'+episode_id+'-comments-'+(j-1));
				$("#form-files-list-"+episode_id+"-comments-"+j).attr('id','form-files-list-'+episode_id+'-comments-'+(j-1));
				$("#form-files-list-"+episode_id+"-is_lost-"+j).attr('name','form-files-list-'+episode_id+'-is_lost-'+(j-1));
				$("#form-files-list-"+episode_id+"-is_lost-"+j).attr('id','form-files-list-'+episode_id+'-is_lost-'+(j-1));
				$("#form-files-list-"+episode_id+"-delete-"+j).attr('onclick','deleteVersionRow('+episode_id+','+(j-1)+');');
				$("#form-files-list-"+episode_id+"-delete-"+j).attr('id','form-files-list-'+episode_id+'-delete-'+(j-1));
				if ($('#type').val()=='manga') {
					$('label[for="form-files-list-'+episode_id+'-file-'+j+'"]').attr('for', 'form-files-list-'+episode_id+'-file-'+(j-1));
					$("#form-files-list-"+episode_id+"-file-"+j).attr('name','form-files-list-'+episode_id+'-file-'+(j-1));
					$("#form-files-list-"+episode_id+"-file-"+j).attr('id','form-files-list-'+episode_id+'-file-'+(j-1));
					$("#form-files-list-"+episode_id+"-file_details-"+j).attr('id','form-files-list-'+episode_id+'-file_details-'+(j-1));
				} else {
					$("#form-files-list-"+episode_id+"-add_link-"+j).attr('onclick','addLinkRow('+episode_id+','+(j-1)+');');
					$("#form-files-list-"+episode_id+"-add_link-"+j).attr('id','form-files-list-'+episode_id+'-add_link-'+(j-1));
					$("#links-list-table-"+episode_id+"-"+j).attr('id','links-list-table-'+episode_id+'-'+(j-1));
					var numInstances = parseInt($('#links-list-table-'+episode_id+'-'+(j-1)).attr('data-count'));
					for (var k=1;k<numInstances+1;k++) {
						$("#form-links-list-"+episode_id+"-row-"+j+"-"+k).attr('id','form-links-list-'+episode_id+'-row-'+(j-1)+'-'+k);
						$("#form-files-list-"+episode_id+"-file-"+j+"-link-"+k+"-url").attr('name', "form-files-list-"+episode_id+"-file-"+(j-1)+"-link-"+k+"-url");
						$("#form-files-list-"+episode_id+"-file-"+j+"-link-"+k+"-url").attr('id', "form-files-list-"+episode_id+"-file-"+(j-1)+"-link-"+k+"-url");
						$("#form-files-list-"+episode_id+"-file-"+j+"-link-"+k+"-id").attr('name', "form-files-list-"+episode_id+"-file-"+(j-1)+"-link-"+k+"-id");
						$("#form-files-list-"+episode_id+"-link-"+j+"-link-"+k+"-id").attr('id', "form-files-list-"+episode_id+"-file-"+(j-1)+"-link-"+k+"-id");
						$("#form-files-list-"+episode_id+"-file-"+j+"-link-"+k+"-resolution").attr('name', "form-files-list-"+episode_id+"-file-"+(j-1)+"-link-"+k+"-resolution");
						$("#form-files-list-"+episode_id+"-file-"+j+"-link-"+k+"-resolution").attr('id', "form-files-list-"+episode_id+"-file-"+(j-1)+"-link-"+k+"-resolution");
						$("#form-files-list-"+episode_id+"-file-"+j+"-link-"+k+"-delete").attr('onclick','deleteLinkRow('+episode_id+','+(j-1)+','+k+');');
						$("#form-files-list-"+episode_id+"-file-"+j+"-link-"+k+"-delete").attr('id','form-files-list-'+episode_id+"-file-"+(j-1)+"-link-"+k+"-delete");
					}
				}
			}
			$('#files-list-table-'+episode_id).attr('data-count', i-1);
		}
	}
}

function deleteLinkRow(episode_id, variant_number, id) {
	var i = parseInt($('#links-list-table-'+episode_id+'-'+variant_number).attr('data-count'));
	if(i==1) {
		$("#form-files-list-"+episode_id+"-file-"+variant_number+"-link-1-url").val("");
		$("#form-files-list-"+episode_id+"-file-"+variant_number+"-link-1-url").attr("value","");
		$("#form-files-list-"+episode_id+"-file-"+variant_number+"-link-1-id").val("-1");
		$("#form-files-list-"+episode_id+"-file-"+variant_number+"-link-1-resolution").val("");
	}
	else {
		$("#form-links-list-"+episode_id+"-row-"+variant_number+"-"+id).remove();
		for (var j=id+1;j<i+1;j++) {
			$("#form-links-list-"+episode_id+"-row-"+variant_number+"-"+j).attr('id','form-links-list-'+episode_id+'-row-'+variant_number+'-'+(j-1));
			$("#form-files-list-"+episode_id+"-file-"+variant_number+"-link-"+j+"-url").attr('name', "form-files-list-"+episode_id+"-file-"+variant_number+"-link-"+(j-1)+"-url");
			$("#form-files-list-"+episode_id+"-file-"+variant_number+"-link-"+j+"-url").attr('id', "form-files-list-"+episode_id+"-file-"+variant_number+"-link-"+(j-1)+"-url");
			$("#form-files-list-"+episode_id+"-file-"+variant_number+"-link-"+j+"-id").attr('name', "form-files-list-"+episode_id+"-file-"+variant_number+"-link-"+(j-1)+"-id");
			$("#form-files-list-"+episode_id+"-file-"+variant_number+"-link-"+j+"-id").attr('id', "form-files-list-"+episode_id+"-file-"+variant_number+"-link-"+(j-1)+"-id");
			$("#form-files-list-"+episode_id+"-file-"+variant_number+"-link-"+j+"-resolution").attr('name', "form-files-list-"+episode_id+"-file-"+variant_number+"-link-"+(j-1)+"-resolution");
			$("#form-files-list-"+episode_id+"-file-"+variant_number+"-link-"+j+"-resolution").attr('id', "form-files-list-"+episode_id+"-file-"+variant_number+"-link-"+(j-1)+"-resolution");
			$("#form-files-list-"+episode_id+"-file-"+variant_number+"-link-"+j+"-delete").attr('onclick','deleteLinkRow('+episode_id+','+variant_number+','+(j-1)+');');
			$("#form-files-list-"+episode_id+"-file-"+variant_number+"-link-"+j+"-delete").attr('id','form-files-list-'+episode_id+"-file-"+variant_number+"-link-"+(j-1)+"-delete");
		}
		$('#links-list-table-'+episode_id+'-'+variant_number).attr('data-count', i-1);
	}
}

function deleteExtraLinkRow(extra_number, id) {
	var i = parseInt($('#extras-links-list-table-'+extra_number).attr('data-count'));
	if(i==1) {
		$("#form-extras-list-"+extra_number+"-link-1-url").val("");
		$("#form-extras-list-"+extra_number+"-link-1-url").attr("value","");
		$("#form-extras-list-"+extra_number+"-link-1-id").val("-1");
		$("#form-extras-list-"+extra_number+"-link-1-resolution").val("");
	}
	else {
		$("#form-links-extras-list-row-"+extra_number+"-"+id).remove();
		for (var j=id+1;j<i+1;j++) {
			$("#form-links-extras-list-row-"+extra_number+"-"+j).attr('id','form-links-extras-list-row-'+extra_number+'-'+(j-1));
			$("#form-extras-list-"+extra_number+"-link-"+j+"-url").attr('name', "form-extras-list-"+extra_number+"-link-"+(j-1)+"-url");
			$("#form-extras-list-"+extra_number+"-link-"+j+"-url").attr('id', "form-extras-list-"+extra_number+"-link-"+(j-1)+"-url");
			$("#form-extras-list-"+extra_number+"-link-"+j+"-id").attr('name', "form-extras-list-"+extra_number+"-link-"+(j-1)+"-id");
			$("#form-extras-list-"+extra_number+"-link-"+j+"-id").attr('id', "form-extras-list-"+extra_number+"-link-"+(j-1)+"-id");
			$("#form-extras-list-"+extra_number+"-link-"+j+"-resolution").attr('name', "form-extras-list-"+extra_number+"-link-"+(j-1)+"-resolution");
			$("#form-extras-list-"+extra_number+"-link-"+j+"-resolution").attr('id', "form-extras-list-"+extra_number+"-link-"+(j-1)+"-resolution");
			$("#form-extras-list-"+extra_number+"-link-"+j+"-delete").attr('onclick','deleteExtraLinkRow('+extra_number+','+(j-1)+');');
			$("#form-extras-list-"+extra_number+"-link-"+j+"-delete").attr('id','form-extras-list-'+extra_number+"-link-"+(j-1)+"-delete");
		}
		$('#extras-links-list-table-'+extra_number).attr('data-count', i-1);
	}
}

function deleteVersionExtraRow(id) {
	if ($("#form-extras-list-id-"+id).val()=='-1' || ($("#form-extras-list-id-"+id).val()!='-1' && confirm("Segur que vols esborrar aquest extra? Se'n perdran totes les estadístiques. Si vols pujar-ne una versió amb canvis, simplement canvia'n l'enllaç."))) {
		var i = parseInt($('#extras-list-table').attr('data-count'));
		$("#form-extras-list-row-"+id).remove();
		for (var j=id+1;j<i+1;j++) {
			$("#form-extras-list-row-"+j).attr('id','form-extras-list-row-'+(j-1));
			$("#form-extras-list-id-"+j).attr('name','form-extras-list-id-'+(j-1));
			$("#form-extras-list-id-"+j).attr('id','form-extras-list-id-'+(j-1));
			$("#form-extras-list-name-"+j).attr('name','form-extras-list-name-'+(j-1));
			$("#form-extras-list-name-"+j).attr('id','form-extras-list-name-'+(j-1));
			$("#form-extras-list-length-"+j).attr('name','form-extras-list-length-'+(j-1));
			$("#form-extras-list-length-"+j).attr('id','form-extras-list-length-'+(j-1));
			$("#form-extras-list-comments-"+j).attr('name','form-extras-list-comments-'+(j-1));
			$("#form-extras-list-comments-"+j).attr('id','form-extras-list-comments-'+(j-1));
			$("#form-extras-list-delete-"+j).attr('onclick','deleteVersionExtraRow('+(j-1)+');');
			$("#form-extras-list-delete-"+j).attr('id','form-extras-list-delete-'+(j-1));
			if ($('#type').val()=='manga') {
				$('label[for="form-extras-list-file-'+j+'"]').attr('for', 'form-extras-list-file-'+(j-1));
				$("#form-extras-list-file-"+j).attr('name','form-extras-list-file-'+(j-1));
				$("#form-extras-list-file-"+j).attr('id','form-extras-list-file-'+(j-1));
				$("#form-extras-list-file_details-"+j).attr('id','form-extras-list-file_details-'+(j-1));
			} else {
				$("#form-extras-list-add_link-"+j).attr('onclick','addExtraLinkRow('+(j-1)+');');
				$("#form-extras-list-add_link-"+j).attr('id','form-extras-list-add_link-'+(j-1));
				$("#extras-links-list-table-"+j).attr('id','extras-links-list-table-'+(j-1));
				var numInstances = parseInt($('#extras-links-list-table-'+(j-1)).attr('data-count'));
				for (var k=1;k<numInstances+1;k++) {
					$("#form-links-extras-list-row-"+j+"-"+k).attr('id','form-links-extras-list-row-'+(j-1)+'-'+k);
					$("#form-extras-list-"+j+"-link-"+k+"-url").attr('name', "form-extras-list-"+(j-1)+"-link-"+k+"-url");
					$("#form-extras-list-"+j+"-link-"+k+"-url").attr('id', "form-extras-list-"+(j-1)+"-link-"+k+"-url");
					$("#form-extras-list-"+j+"-link-"+k+"-id").attr('name', "form-extras-list-"+(j-1)+"-link-"+k+"-id");
					$("#form-extras-list-"+j+"-link-"+k+"-id").attr('id', "form-extras-list-"+(j-1)+"-link-"+k+"-id");
					$("#form-extras-list-"+j+"-link-"+k+"-resolution").attr('name', "form-extras-list-"+(j-1)+"-link-"+k+"-resolution");
					$("#form-extras-list-"+j+"-link-"+k+"-resolution").attr('id', "form-extras-list-"+(j-1)+"-link-"+k+"-resolution");
					$("#form-extras-list-"+j+"-link-"+k+"-delete").attr('onclick','deleteExtraLinkRow('+(j-1)+','+k+');');
					$("#form-extras-list-"+j+"-link-"+k+"-delete").attr('id',"form-extras-list-"+(j-1)+"-link-"+k+"-delete");
				}
			}
		}
		$('#extras-list-table').attr('data-count', i-1);

		if (i-1==0) {
			$('#extras-list-table-empty').removeClass('d-none');
		}
	}
}

function deleteVersionRemoteFolderRow(id) {
	var i = parseInt($('#remote_folders-list-table').attr('data-count'));
	$("#form-remote_folders-list-row-"+id).remove();
	for (var j=id+1;j<i+1;j++) {
		$("#form-remote_folders-list-row-"+j).attr('id','form-remote_folders-list-row-'+(j-1));
		$("#form-remote_folders-list-id-"+j).attr('name','form-remote_folders-list-id-'+(j-1));
		$("#form-remote_folders-list-id-"+j).attr('id','form-remote_folders-list-id-'+(j-1));
		$("#form-remote_folders-list-account_id-"+j).attr('name','form-remote_folders-list-remote_account_id-'+(j-1));
		$("#form-remote_folders-list-account_id-"+j).attr('id','form-remote_folders-list-remote_account_id-'+(j-1));
		$("#form-remote_folders-list-folder-"+j).attr('name','form-remote_folders-list-folder-'+(j-1));
		$("#form-remote_folders-list-folder-"+j).attr('id','form-remote_folders-list-folder-'+(j-1));
		$("#form-remote_folders-list-division_id-"+j).attr('name','form-remote_folders-list-division_id-'+(j-1));
		$("#form-remote_folders-list-division_id-"+j).attr('id','form-remote_folders-list-division_id-'+(j-1));
		$("#form-remote_folders-list-active-"+j).attr('name','form-remote_folders-list-active-'+(j-1));
		$("#form-remote_folders-list-active-"+j).attr('id','form-remote_folders-list-active-'+(j-1));
		$("#form-remote_folders-list-delete-"+j).attr('onclick','deleteVersionRemoteFolderRow('+(j-1)+');');
		$("#form-remote_folders-list-delete-"+j).attr('id','form-remote_folders-list-delete-'+(j-1));
	}
	$('#remote_folders-list-table').attr('data-count', i-1);

	if (i-1==0) {
		$('#remote_folders-list-table-empty').removeClass('d-none');
	}
}

function fetchMalEpisodes(current_division, total_divisions, page) {
	if (current_division==1 && page==1) {
		malDataDivisionsEpisodes = [];
		malDataDivisionsEpisodesCount = 0;
		malDataMessages = "";
	}

	var xmlhttp = new XMLHttpRequest();
	var url = "https://api.jikan.moe/v4/anime/"+$("#form-division-list-external_id-"+current_division).val()+"/episodes?page="+page;

	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var malDataEpisodesPage = JSON.parse(this.responseText);
			if (page==1) {
				malDataEpisodes=malDataEpisodesPage.data;
			} else {
				malDataEpisodes=malDataEpisodes.concat(malDataEpisodesPage.data);
			}
			if (malDataEpisodesPage.pagination.has_next_page) {
				setTimeout(function() {
					fetchMalEpisodes(current_division, total_divisions, page+1);
				}, 4000);
			} else{
				if (malDataEpisodes.length>0) {
					malDataDivisionsEpisodes.push(malDataEpisodes);
					malDataDivisionsEpisodesCount+=malDataEpisodes.length;
				}
				else{
					malDataMessages+="\nLa temporada "+$("#form-division-list-number-"+current_division).val()+" no té capítols donats d'alta a MyAnimeList. Caldrà que els introdueixis a mà.";
					malDataDivisionsEpisodes.push({'episodes': []});
				}
				if (current_division<total_divisions) {
					setTimeout(function() {
						fetchMalEpisodes(current_division+1, total_divisions, 1);
					}, 4000);
				} else {
					if (malDataDivisionsEpisodesCount>0) {
						for (var i=0;i<malDataDivisionsEpisodes.length;i++) {
							populateMalEpisodes(i+1,malDataDivisionsEpisodes[i]);
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
	var divisions = $('[id^=form-division-list-number_of_episodes-]');
	var divisionsEpisodeCount=0;
	for (var i=0;i<divisions.length;i++){
		if ($(divisions[i]).val()!='' && $(divisions[i]).val()>0) {
			divisionsEpisodeCount+=parseInt($(divisions[i]).val());
		}
	}
	var episodeCount = parseInt($('#episode-list-table').attr('data-count'));
	var normalEpisodeCount = 0;

	for (var i=1;i<=episodeCount;i++){
		if ($('#form-episode-list-num-'+i).val()!=''){
			normalEpisodeCount++;
		}
	}
	if (normalEpisodeCount!=divisionsEpisodeCount){
		if ($('#type').val()=='manga') {
			alert('El nombre de capítols numerats de la llista ha de coincidir amb el nombre de capítols indicat als volums.');
		} else {
			alert('El nombre de capítols numerats de la llista ha de coincidir amb el nombre de capítols indicat a les temporades.');
		}
		return false;
	}
	for (var i=1;i<=episodeCount;i++){
		if ($('#form-episode-list-num-'+i).val()=='' && $('#form-episode-list-description-'+i).val()==''){
			alert('Hi ha capítols sense número ni descripció. Els capítols normals han de tenir com a mínim número, i els capítols especials han de tenir com a mínim descripció.');
			return false;
		}
	}

	var divisionNumbers = [];
	for (var i=0;i<divisions.length;i++){
		divisionNumbers.push($('#form-division-list-number-'+(i+1)).val());
	}
	var higherThanCount=false;
	for (var i=1;i<=episodeCount;i++){
		if ($('#form-episode-list-division-'+i).val()!='' && !divisionNumbers.includes($('#form-episode-list-division-'+i).val())){
			if ($('#type').val()=='manga') {
				alert('Hi ha capítols de volums inexistents. Corregeix-ho.');
			} else {
				alert('Hi ha capítols de temporades inexistents. Corregeix-ho.');
			}
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

	if ($('#form-old_slug').val()!='' && $('#form-old_slug').val()!=$('#form-slug').val()) {
		return confirm('Has canviat (o s\'ha canviat automàticament perquè has modificat el nom) l\'identificador de la fitxa de "'+$('#form-old_slug').val()+'" a "'+$('#form-slug').val()+'". Això farà que tots els enllaços externs que apuntin a la fitxa deixin de funcionar. Segur que és el que vols? Si no és el que vols, prem \'Cancel·la\' i torna a posar-hi el valor original.');
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
			alert("Si hi ha activada la sincronització automàtica de carpetes, no és possible afegir més d'una variant per capítol. Has de desactivar-la o bé eliminar els enllaços addicionals dels capítols.");
			return false;
		}
	}

	var urls = $('[id$=-url]');
	for (var i=0;i<urls.length;i++) {
		if (urls[i].value!='') {
			var resolution = $('#'+urls[i].id.replace('-url','-resolution'));
			if (resolution.val()=='' || resolution.val()=='null' || (!resolution.val().includes('p') && !resolution.val().includes('x'))) {
				alert("Si introdueixes una URL per a un capítol, cal que també n'especifiquis la resolució.\nLa resolució ha de tenir format '1234p' o '1234x1234'.");
				return false;
			}
		}
	}

	if ($('#type').val()!='manga') {
		var lengths = $('[id^=form-files-list-][id*=-length-]');
		for (var i=0;i<lengths.length;i++) {
			if (lengths[i].value=='') {
				var urls = $(lengths[i]).parent().parent().find('[id$=-url]');
				for (var j=0;j<urls.length;j++) {
					if (urls[j].value!='') {
						alert("Si introdueixes una URL per a un capítol, cal que també n'especifiquis la durada. Hi ha algun capítol en què no ho has fet.");
						return false;
					}
				}
			}
		}
	}

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

var validLinks=0;
var invalidLinks=0;
var failedLinks=0;
var unknownLinks=0;
var linkVerifyRetries=0;
var totalSize=0;

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
	var matchesYouTube = links[i].link.match(/(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((?:\w|-){11})?/);
	if (matchesMega && matchesMega.length>1 && matchesMega[1]!=''){
		//MEGA link
		$.post("https://eu.api.mega.co.nz/cs", "[{\"a\":\"g\", \"g\":1, \"ssl\":0, \"p\":\""+matchesMega[1]+"\"}]", function(data, status){
			if (Array.isArray(data) && data[0]<0) {
				//invalid
				$('#link-verifier-wrong-links-list').append('<div class="row w-100"><p class="col-sm-4 font-weight-bold">'+links[i].text+'</p><p class="col-sm-8"><a href="'+links[i].link+'">'+links[i].link+'</a></p></div>');
				invalidLinks++;
				updateVerifyLinksResult(i+1);
				linkVerifyRetries=0;
				verifyLinks(i+1);
			} else if (status=='success') {
				//valid
				validLinks++;
				totalSize+=data[0].s;
				console.debug("Total file size: " + totalSize);
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
					$('#link-verifier-failed-links-list').append('<div class="row w-100"><p class="col-sm-4 font-weight-bold">'+links[i].text+'</p><p class="col-sm-8"><a href="'+links[i].link+'">'+links[i].link+'</a></p></div>');
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
				$('#link-verifier-failed-links-list').append('<div class="row w-100"><p class="col-sm-4 font-weight-bold">'+links[i].text+'</p><p class="col-sm-8"><a href="'+links[i].link+'">'+links[i].link+'</a></p></div>');
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
				$('#link-verifier-wrong-links-list').append('<div class="row w-100"><p class="col-sm-4 font-weight-bold">'+links[i].text+'</p><p class="col-sm-8"><a href="'+links[i].link+'">'+links[i].link+'</a></p></div>');
				invalidLinks++;
				updateVerifyLinksResult(i+1);
				linkVerifyRetries=0;
				verifyLinks(i+1);
			} else {
				if (linkVerifyRetries<5){
					linkVerifyRetries++;
					verifyLinks(i);
				} else {
					$('#link-verifier-failed-links-list').append('<div class="row w-100"><p class="col-sm-4 font-weight-bold">'+links[i].text+'</p><p class="col-sm-8"><a href="'+links[i].link+'">'+links[i].link+'</a></p></div>');
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
				$('#link-verifier-failed-links-list').append('<div class="row w-100"><p class="col-sm-4 font-weight-bold">'+links[i].text+'</p><p class="col-sm-8"><a href="'+links[i].link+'">'+links[i].link+'</a></p></div>');
				failedLinks++;
				linkVerifyRetries=0;
				verifyLinks(i+1);
			}
		});
	} else if (matchesYouTube && matchesYouTube.length>1 && matchesYouTube[1]!='') {
		//YouTube not verifiable for now
		unknownLinks++;
		updateVerifyLinksResult(i+1);
		verifyLinks(i+1);
	} else {
		//Direct link
		$.post("check_direct_link.php?link="+encodeURIComponent(links[i].link.replaceAll('&amp;','&')), function(data, status){
			if (data.lastIndexOf('OK', 0)===0) {
				//valid
				validLinks++;
				totalSize+=parseInt(data.split(',')[1]);
				console.debug("Total file size: " + totalSize);
				updateVerifyLinksResult(i+1);
				linkVerifyRetries=0;
				verifyLinks(i+1);
			} else if (data.lastIndexOf('KO', 0)===0) {
				//invalid
				$('#link-verifier-wrong-links-list').append('<div class="row w-100"><p class="col-sm-4 font-weight-bold">'+links[i].text+'</p><p class="col-sm-8"><a href="'+links[i].link+'">'+links[i].link+'</a></p></div>');
				invalidLinks++;
				updateVerifyLinksResult(i+1);
				linkVerifyRetries=0;
				verifyLinks(i+1);
			} else {
				if (linkVerifyRetries<5){
					linkVerifyRetries++;
					verifyLinks(i);
				} else {
					$('#link-verifier-failed-links-list').append('<div class="row w-100"><p class="col-sm-4 font-weight-bold">'+links[i].text+'</p><p class="col-sm-8"><a href="'+links[i].link+'">'+links[i].link+'</a></p></div>');
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
				$('#link-verifier-failed-links-list').append('<div class="row w-100"><p class="col-sm-4 font-weight-bold">'+links[i].text+'</p><p class="col-sm-8"><a href="'+links[i].link+'">'+links[i].link+'</a></p></div>');
				failedLinks++;
				linkVerifyRetries=0;
				verifyLinks(i+1);
			}
		});
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
	return $('[id^=form-remote_folders-list-is_active-]:checked').length>0;
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

function generateStorageFolder() {
	var string = '';
	var fansubs = [];
	if ($('#form-fansub-1').val()>0) {
		fansubs.push($('#form-fansub-1').find('option:selected').text());
	}
	if ($('#form-fansub-2').val()>0) {
		fansubs.push($('#form-fansub-2').find('option:selected').text());
	}
	if ($('#form-fansub-3').val()>0) {
		fansubs.push($('#form-fansub-3').find('option:selected').text());
	}
	fansubs.sort();
	for(var i=0;i<fansubs.length;i++) {
		if (string!='') {
			string+=' + ';
		}
		string+=fansubs[i].replaceAll('/','-').replaceAll(' +','');
	}
	string+='/';
	if ($('#series_subtype').val()=='movie') {
		string+='Films';
	} else {
		string+=$('#form-series').text().replaceAll('/','-').replaceAll(':',' -').replaceAll('?','').replaceAll('♡',' ').replaceAll(';',' ').replaceAll('★',' ');
	}
	$('#form-storage_folder').val(string);
}

function checkAnimeGenres(currentElement, currentMalIdElement, currentGenres) {
	if (currentElement==0 && currentMalIdElement==0) {
		$('#output').text("S'ha iniciat la comprovació de gèneres dels animes. Tingues paciència...");
	}

	var xmlhttp = new XMLHttpRequest();
	var url = "https://api.jikan.moe/v4/anime/"+animes[currentElement].mal_ids[currentMalIdElement];

	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4) {
			if (this.status == 200) {
				response = JSON.parse(this.responseText);				
				for (var i = 0; i < response.data.genres.length; i++) {
					currentGenres.push(response.data.genres[i].mal_id);
				}
				for (var i = 0; i < response.data.explicit_genres.length; i++) {
					currentGenres.push(response.data.explicit_genres[i].mal_id);
				}
				for (var i = 0; i < response.data.themes.length; i++) {
					currentGenres.push(response.data.themes[i].mal_id);
				}
				for (var i = 0; i < response.data.demographics.length; i++) {
					currentGenres.push(response.data.demographics[i].mal_id);
				}

				if (currentMalIdElement<animes[currentElement].mal_ids.length-1) {
					setTimeout(function() {
						checkAnimeGenres(currentElement, currentMalIdElement+1, currentGenres);
					}, 4000);
				} else {
					//Finished elements, check genres and go to next
					var difference = $($.unique(currentGenres)).not(animes[currentElement].genres).get();
					for (var i = 0; i < difference.length; i++) {
						$('#output').append("<br />A l'anime «"+animes[currentElement].name+"» li manca el gènere "+getAnimeGenreName(difference[i]));
					}

					difference = $(animes[currentElement].genres).not(currentGenres).get();
					for (var i = 0; i < difference.length; i++) {
						$('#output').append("<br />A l'anime «"+animes[currentElement].name+"» li sobra el gènere "+getAnimeGenreName(difference[i]));
					}
		
					if (currentElement<animes.length-1) {
						setTimeout(function() {
							checkAnimeGenres(currentElement+1, 0, []);
						}, 4000);
					} else {
						$('#output').append("<br />El procés ha finalitzat!");
					}
				}
			} else {
				$('#output').append("<br />No s'han pogut obtenir les dades de l'anime «"+animes[currentElement].name+"»");
		
				if (currentElement<animes.length-1) {
					setTimeout(function() {
						checkAnimeGenres(currentElement+1, 0, []);
					}, 4000);
				} else {
					$('#output').append("<br />El procés ha finalitzat!");
				}
			}
		}
	};
	xmlhttp.open("GET", url, true);
	xmlhttp.send();
}

function checkMangaGenres(currentElement, currentMalIdElement, currentGenres) {
	if (currentElement==0 && currentMalIdElement==0) {
		$('#output').text("S'ha iniciat la comprovació de gèneres dels mangues. Tingues paciència...");
	}

	var xmlhttp = new XMLHttpRequest();
	var url = "https://api.jikan.moe/v4/manga/"+mangas[currentElement].mal_ids[currentMalIdElement];

	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4) {
			if (this.status == 200) {
				response = JSON.parse(this.responseText);				
				for (var i = 0; i < response.data.genres.length; i++) {
					currentGenres.push(response.data.genres[i].mal_id);
				}
				for (var i = 0; i < response.data.explicit_genres.length; i++) {
					currentGenres.push(response.data.explicit_genres[i].mal_id);
				}
				for (var i = 0; i < response.data.themes.length; i++) {
					currentGenres.push(response.data.themes[i].mal_id);
				}
				for (var i = 0; i < response.data.demographics.length; i++) {
					currentGenres.push(response.data.demographics[i].mal_id);
				}

				if (currentMalIdElement<mangas[currentElement].mal_ids.length-1) {
					setTimeout(function() {
						checkMangaGenres(currentElement, currentMalIdElement+1, currentGenres);
					}, 4000);
				} else {
					//Finished elements, check genres and go to next
					var difference = $($.unique(currentGenres)).not(mangas[currentElement].genres).get();
					for (var i = 0; i < difference.length; i++) {
						$('#output').append("<br />Al manga «"+mangas[currentElement].name+"» li manca el gènere "+getMangaGenreName(difference[i]));
					}

					difference = $(mangas[currentElement].genres).not(currentGenres).get();
					for (var i = 0; i < difference.length; i++) {
						$('#output').append("<br />Al manga «"+mangas[currentElement].name+"» li sobra el gènere "+getMangaGenreName(difference[i]));
					}
		
					if (currentElement<mangas.length-1) {
						setTimeout(function() {
							checkMangaGenres(currentElement+1, 0, []);
						}, 4000);
					} else {
						$('#output').append("<br />El procés ha finalitzat!");
					}
				}
			} else {
				$('#output').append("<br />No s'han pogut obtenir les dades del manga «"+mangas[currentElement].name+"»");
		
				if (currentElement<mangas.length-1) {
					setTimeout(function() {
						checkMangaGenres(currentElement+1, 0, []);
					}, 4000);
				} else {
					$('#output').append("<br />El procés ha finalitzat!");
				}
			}
		}
	};
	xmlhttp.open("GET", url, true);
	xmlhttp.send();
}

function getAnimeGenreName(id){
	for (var i = 0; i < animeGenres.length; i++) {
		if (animeGenres[i].mal_id==id) {
			return animeGenres[i].name;
		}
	}
	return "Gènere desconegut "+id;
}

function getMangaGenreName(id) {
	for (var i = 0; i < mangaGenres.length; i++) {
		if (mangaGenres[i].mal_id==id) {
			return mangaGenres[i].name;
		}
	}
	return "Gènere desconegut "+id;
}

function showAnimeWithNoMal() {
	$('#output').text("Animes sense enllaç a MAL:");
	for (var i = 0; i < noMalAnime.length; i++) {
		$('#output').append("<br />«"+noMalAnime[i]+"»");
	}
}

function showMangaWithNoMal() {
	$('#output').text("Mangues sense enllaç a MAL:");
	for (var i = 0; i < noMalManga.length; i++) {
		$('#output').append("<br />«"+noMalManga[i]+"»");
	}
}

var malData;
var malDataStaff;
var malDataDivisionsEpisodes;
var malDataEpisodes;
var malDataMessages;
var uncompressReady = false;

$(document).ready(function() {
	loadArchiveFormats(['rar', 'zip'], function() {
		uncompressReady = true;
	});

	if ($('#form-storage_folder').length==1) {
		$('#form-fansub-1').on('change', generateStorageFolder);
		$('#form-fansub-2').on('change', generateStorageFolder);
		$('#form-fansub-3').on('change', generateStorageFolder);
		if ($('#form-storage_folder').val()=='') {
			generateStorageFolder();
		}
	}

	$("#form-name-with-autocomplete").on('input', function() {
		$("#form-slug").val(string_to_slug($("#form-name-with-autocomplete").val()));
	});

	$("#import-from-mal").click(function() {
		if ($("#form-external_id").val()=='') {
			if ($('#type').val()=='manga') {
				var result = prompt("Introdueix l'URL del manga a MyAnimeList per a importar-ne la fitxa.");
				if (!result) {
					return;
				} else if (result.match(/https?:\/\/.*myanimelist.net\/manga\/(\d+)\//i)) {
					$("#form-external_id").val(result.match(/https?:\/\/.*myanimelist.net\/manga\/(\d*)\//i)[1]);
				} else {
					alert("L'URL no és vàlida.");
					return;
				}
			} else {
				var result = prompt("Introdueix l'URL de l'anime a MyAnimeList per a importar-ne la fitxa.");
				if (!result) {
					return;
				} else if (result.match(/https?:\/\/.*myanimelist.net\/anime\/(\d+)\//i)) {
					$("#form-external_id").val(result.match(/https?:\/\/.*myanimelist.net\/anime\/(\d*)\//i)[1]);
				} else {
					alert("L'URL no és vàlida.");
					return;
				}
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
		var url = "https://api.jikan.moe/v4/"+($('#type').val()=='manga' ? 'manga' : 'anime')+"/"+$("#form-external_id").val();

		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				malData = JSON.parse(this.responseText);

				if ($('#type').val()=='manga') {
					populateMalDataManga(malData);
					$("#import-from-mal-loading").addClass("d-none");
					$("#import-from-mal-not-loading").removeClass("d-none");
					setTimeout(function() {
						$("#import-from-mal").prop('disabled', false);
						$("#import-from-mal-episodes").prop('disabled', false);
					}, 4000);
					$("#import-from-mal-done").removeClass("d-none");
				} else {
					//The staff call is only needed for anime
					setTimeout(function() {
						var xmlhttp2 = new XMLHttpRequest();
						var url2 = "https://api.jikan.moe/v4/anime/"+$("#form-external_id").val()+"/staff";

						xmlhttp2.onreadystatechange = function() {
							if (this.readyState == 4 && this.status == 200) {
								malDataStaff = JSON.parse(this.responseText);
								populateMalData(malData, malDataStaff);
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
				}
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
		if ($("#import-from-mal-episodes").hasClass('disabled')) {
			if ($('#type').val()=='manga') {
				alert('Aquest manga ja té capítols amb fitxers en alguna versió d\'algun fansub. No es poden importar capítols de MyAnimeList perquè això implicaria suprimir els capítols antics, i els fitxers d\'aquella versió deixarien de funcionar. Si realment els vols suprimir i tornar a importar, primer caldria que suprimissis els fitxers de la versió. Si tens dubtes, contacta amb un administrador.');
			} else if ($('#type').val()=='liveaction') {
				alert('Aquest contingut d\'acció real ja té capítols amb fitxers en alguna versió d\'algun fansub. No es poden importar capítols de MyAnimeList perquè això implicaria suprimir els capítols antics, i els fitxers d\'aquella versió deixarien de funcionar. Si realment els vols suprimir i tornar a importar, primer caldria que suprimissis els fitxers de la versió. Si tens dubtes, contacta amb un administrador.');
			} else {
				alert('Aquest anime ja té capítols amb fitxers en alguna versió d\'algun fansub. No es poden importar capítols de MyAnimeList perquè això implicaria suprimir els capítols antics, i els fitxers d\'aquella versió deixarien de funcionar. Si realment els vols suprimir i tornar a importar, primer caldria que suprimissis els fitxers de la versió. Si tens dubtes, contacta amb un administrador.');
			}
			return false;
		}
		var divisions = $('[id^=form-division-list-external_id-]');
		var with_id=0;
		for (var i=0;i<divisions.length;i++){
			if ($(divisions[i]).val()!='') {
				with_id++;
			}
		}
		if (with_id==0) {
			if ($('#type').val()=='manga') {
				alert("Cal que introdueixis l'identificador de MyAnimeList d'almenys un dels volums.");
			} else {
				alert("Cal que introdueixis l'identificador de MyAnimeList d'almenys una de les temporades.");
			}
			return;
		} else if (with_id<divisions.length) {
			if ($('#type').val()=='manga') {
				alert("Hi ha volums sense identificador de MyAnimeList. Cal que especifiquis l'identificador de tots.");
			} else {
				alert("Hi ha temporades sense identificador de MyAnimeList. Cal que especifiquis l'identificador de totes.");
			}
			return;
		}
		if ((parseInt($('#episode-list-table').attr('data-count'))>1 || $('#form-episode-list-description-1').val()!='') && !confirm("ATENCIÓ! Ja hi ha dades de capítols. Si continues, se suprimiran tots els de la llista i es tornaran a crear. Vols continuar?")) {
			return;
		}

		$("#import-from-mal").prop('disabled', true);
		$("#import-from-mal-episodes").prop('disabled', true);
		$("#import-from-mal-episodes-loading").removeClass("d-none");
		$("#import-from-mal-episodes-not-loading").addClass("d-none");

		fetchMalEpisodes(1,divisions.length,1);
	});

	$("#generate-episodes").click(function() {
		if ($("#generate-episodes").hasClass('disabled')) {
			if ($('#type').val()=='manga') {
				alert('Aquest manga ja té capítols amb fitxers en alguna versió d\'algun fansub. No es poden generar capítols automàticament perquè això implicaria suprimir els capítols antics, i els fitxers d\'aquella versió deixarien de funcionar. Si realment els vols suprimir i tornar a generar, primer caldria que suprimissis els fitxers de la versió. Si tens dubtes, contacta amb un administrador.');
			} else if ($('#type').val()=='liveaction') {
				alert('Aquest contingut d\'acció real ja té capítols amb fitxers en alguna versió d\'algun fansub. No es poden generar capítols automàticament perquè això implicaria suprimir els capítols antics, i els fitxers d\'aquella versió deixarien de funcionar. Si realment els vols suprimir i tornar a generar, primer caldria que suprimissis els fitxers de la versió. Si tens dubtes, contacta amb un administrador.');
			} else {
				alert('Aquest anime ja té capítols amb fitxers en alguna versió d\'algun fansub. No es poden generar capítols automàticament perquè això implicaria suprimir els capítols antics, i els fitxers d\'aquella versió deixarien de funcionar. Si realment els vols suprimir i tornar a generar, primer caldria que suprimissis els fitxers de la versió. Si tens dubtes, contacta amb un administrador.');
			}
			return false;
		}
		var divisions = $('[id^=form-division-list-number_of_episodes-]');
		var with_episodes=0;
		for (var i=0;i<divisions.length;i++){
			if ($(divisions[i]).val()!='' && $(divisions[i]).val()>0) {
				with_episodes++;
			}
		}
		if (with_episodes!=divisions.length) {
			if ($('#type').val()=='manga') {
				alert("Per a poder-los generar, cal que introdueixis el nombre de capítols de cada volum.");
			} else {
				alert("Per a poder-los generar, cal que introdueixis el nombre de capítols de cada temporada.");
			}
			return;
		}

		if ((parseInt($('#episode-list-table').attr('data-count'))>1 || $('#form-episode-list-description-1').val()!='') && !confirm("ATENCIÓ! Ja hi ha dades de capítols. Si continues, se suprimiran tots els de la llista i es tornaran a crear. Vols continuar?")) {
			return;
		}

		var divisionName = ($('#type').val()=='manga' ? 'volum' : 'temporada');

		var restart = (divisions.length==1 || confirm("Vols reiniciar la numeració de capítols a cada "+divisionName+"? Si és així, prem 'D'acord', en cas contrari, prem 'Cancel·la'."));

		var i = parseInt($('#episode-list-table').attr('data-count'));
		for (var id=1;id<i+1;id++) {
			$("#form-episode-list-row-"+id).remove();
		}
		$('#episode-list-table').attr('data-count', 0);

		var rowNumber=1;

		for (var i=0;i<divisions.length;i++) {
			for (var j=0;j<$(divisions[i]).val();j++) {
				addRow(false);
				$("#form-episode-list-division-"+rowNumber).val($('#form-division-list-number-'+(i+1)).val());
				$("#form-episode-list-num-"+rowNumber).val(restart ? j+1 : rowNumber);
				$("#form-episode-list-description-"+rowNumber).val('');
				rowNumber++;
			}
		}
	});

	$("#import-from-mega").click(function() {
		var count = parseInt($('#remote_folders-list-table').attr('data-count'));
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
		var division_ids = [];
		for (var i=1;i<=count;i++){
			if ($('#import-type').val()!='sync' || $('#form-remote_folders-list-is_active-'+i).prop('checked')) {
				account_ids.push(encodeURIComponent($('#form-remote_folders-list-remote_account_id-'+i).val()));
				folders.push(encodeURIComponent($('#form-remote_folders-list-folder-'+i).val()));
				division_ids.push(encodeURIComponent($('#form-remote_folders-list-division_id-'+i).val()!='' ? $('#form-folders-list-division_id-'+i).val() : -1));
			}
		}

		var xmlhttp = new XMLHttpRequest();
		var url = "fetch_storage_links.php?series_id="+$('[name="series_id"]').val()+"&import_type="+$('#import-type').val()+"&remote_account_ids[]="+account_ids.join("&remote_account_ids[]=")+"&remote_folders[]="+folders.join("&remote_folders[]=")+"&division_ids[]="+division_ids.join("&division_ids[]=");

		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var data = JSON.parse(this.responseText);
				if (data.status=='ko') {
					alert("S'ha produït un error:\n"+data.error);
				} else {
					var moreThanOne = false;
					for (var i = 0; i < data.results.length; i++) {
						var splitted = data.results[i].link.split('/');
						start = splitted[0]+"//"+splitted[2]+"/";
						var found = false;
						$("[id^=form-files-list-"+data.results[i].id+"-file-][id$=url]").each(function (pos,e) {
							if ($(e).val().startsWith(start)) {
								if (found) {
									moreThanOne=true;
									return;
								}
								$(e).parent().parent().find("[id$=resolution]").val($('#form-default_resolution').val());
								$(e).val(data.results[i].link);
								$(e).attr('value', data.results[i].link);
								found = true;
							}
						});
						if (!found) {
							found = false;
							$("[id^=form-files-list-"+data.results[i].id+"-file-][id$=url]").each(function (pos,e) {
								if (found) {
									return;
								}
								if ($(e).val()=='') {
									$(e).parent().parent().find("[id$=resolution]").val($('#form-default_resolution').val());
									$(e).val(data.results[i].link);
									$(e).attr('value', data.results[i].link);
									found = true;
								}
							});
							if (!found) {
								addLinkRow(data.results[i].id,1);
								$("[id^=form-files-list-"+data.results[i].id+"-file-][id$=url]").each(function (pos,e) {
									if (found) {
										return;
									}
									if ($(e).val()=='') {
										$(e).parent().parent().find("[id$=resolution]").val($('#form-default_resolution').val());
										$(e).val(data.results[i].link);
										$(e).attr('value', data.results[i].link);
										found = true;
									}
								});
							}
						}
					}
					if (data.unmatched_results.length>0) {
						$('#import-failed-results').removeClass('d-none');
						for (var i = 0; i < data.unmatched_results.length; i++) {
							$('#import-failed-results-table').append('<tr><td>'+data.unmatched_results[i].file+'</td><td>'+data.unmatched_results[i].link+'</td><td title="'+data.unmatched_results[i].reason_description+'" style="white-space: nowrap;">'+data.unmatched_results[i].reason+'<span class="fa fa-question-circle ml-1"></span></td></tr>');
						}
					}

					if (moreThanOne) {
						alert("ALERTA! Hi havia més d'un enllaç del tipus importat en algun capítol. La importació només ha subtituït el primer de cada capítol.");
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
