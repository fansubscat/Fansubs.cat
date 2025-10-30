var linkUrlPattern = "(https:\/\/mega(?:\.co)?\.nz\/(?:#!|embed#!|file\/|embed\/)?([a-zA-Z0-9]{0,8})[!#]([a-zA-Z0-9_\-]+)|storage:\/\/.*)";

function lang(string) {
	if (window.LANGUAGE_STRINGS[string]===undefined) {
		alert('Missing string: '+string);
		return string;
	}
	return window.LANGUAGE_STRINGS[string];
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
			var msg = successful ? lang('js.admin.generic.copied_to_clipboard') : lang('js.admin.generic.copy_to_clipboard_error');
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
		window.prompt(lang('js.admin.generic.copy_to_clipboard_alternate'), text);
	}
}

function populateMalData(response, staffResponse) {
	if ($('#form-series').length>0) {
		//Version page
		$("#form-synopsis").val(response.data.synopsis);
		synopsisChanged=false;

		var url = (response.data.images && response.data.images.jpg && response.data.images.jpg.large_image_url) ? response.data.images.jpg.large_image_url : null;
		if (document.getElementById('form-image').files.length>0) {
			resetFileInput($("#form-image"));
		}
		$("#form-image_url").val(url);
		$('#form-image-preview').prop('src', url);
		$('#form-image-preview-link').prop('href', url);
		syncDivisionImages();
		
		return;
	}
	
	//Series page
	if ($("#form-name-with-autocomplete").val()=='') {
		$("#form-name-with-autocomplete").val(response.data.title);
		$("#form-name-with-autocomplete").attr('data-old-value', response.data.title);
	}
	if ($("#form-alternate_names").val()=='' && response.data.title_english && response.data.title_english!=response.data.title) {
		$("#form-alternate_names").val(response.data.title_english);
	}
	if ($("#form-division-list-name-1").val()=='') {
		$("#form-division-list-name-1").val(response.data.title);
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
	if ($('#form-series').length>0) {
		//Version page
		$("#form-synopsis").val(response.data.synopsis);
		synopsisChanged=false;

		var url = (response.data.images && response.data.images.jpg && response.data.images.jpg.large_image_url) ? response.data.images.jpg.large_image_url : null;
		if (document.getElementById('form-image').files.length>0) {
			resetFileInput($("#form-image"));
		}
		$("#form-image_url").val(url);
		$('#form-image-preview').prop('src', url);
		$('#form-image-preview-link').prop('href', url);
		syncDivisionImages();
		return;
	}
	
	//Series page
	if ($("#form-name-with-autocomplete").val()=='') {
		$("#form-name-with-autocomplete").val(response.data.title);
		$("#form-name-with-autocomplete").attr('data-old-value', response.data.title);
	}
	if ($("#form-alternate_names").val()=='' && response.data.title_english && response.data.title_english!=response.data.title) {
		$("#form-alternate_names").val(response.data.title_english);
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
	} else if (response.data.type=='Light Novel') {
		$("#form-comic_type").val('novel');
	} else {
		$("#form-comic_type").val('manga');
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
			var howMany = prompt(lang('js.admin.series_edit.manga_import_chapters_decide').replaceAll('%1$d', response.data.chapters).replaceAll('%2$d', response.data.volumes).replaceAll('%3$d', Math.floor(response.data.chapters/response.data.volumes)));
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

function populateMdlData(response) {
	var mdlData = $.parseHTML(response.response);
	if ($('#form-series').length>0) {
		//Version page
		$("#form-synopsis").val($(mdlData).find('.show-synopsis p').text());
		synopsisChanged=false;

		var url = $(mdlData).find('.film-cover .img-responsive').attr('data-cfsrc');
		if (document.getElementById('form-image').files.length>0) {
			resetFileInput($("#form-image"));
		}
		$("#form-image_url").val(url);
		$('#form-image-preview').prop('src', url);
		$('#form-image-preview-link').prop('href', url);
		syncDivisionImages();
		return;
	}
	
	//Series page
	if ($("#form-name-with-autocomplete").val()=='') {
		$("#form-name-with-autocomplete").val($(mdlData).find('.film-title a').text());
		$("#form-name-with-autocomplete").attr('data-old-value', $(mdlData).find('.film-title a').text());
	}
	if ($("#form-alternate_names").val()=='') {
		$("#form-alternate_names").val($(mdlData).find('.film-title a').text());
	}
	if ($("#form-division-list-name-1").val()=='') {
		$("#form-division-list-name-1").val($(mdlData).find('.film-title a').text());
	}
	if ($("#form-score").val()=='') {
		$("#form-score").val($(mdlData).find('.col-film-rating div').text()!='N/A' ? $(mdlData).find('.col-film-rating div').text() : '');
	}
	if ($("#form-division-list-external_id-1").val()=='') {
		$("#form-division-list-external_id-1").val($("#form-external_id").val());
	}
}

//Taken from: https://gist.github.com/codeguy/6684588
function string_to_slug(str) {
	str = str.replace(/^\s+|\s+$/g, ''); // trim
	str = str.toLowerCase();

	// remove accents, swap ñ for n, etc
	var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;'’";
	var to   = "aaaaeeeeiiiioooouuuunc--------";
	for (var i=0, l=from.length ; i<l ; i++) {
		str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
	}

	str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
		.replace(/\s+/g, '-') // collapse whitespace and replace by -
		.replace(/-+/g, '-'); // collapse dashes

	return str;
}

function addEpisodeRow(special, linked) {
	var i = parseInt($('#episode-list-table').attr('data-count'))+1;
	if (!linked) {
		$('#episode-list-table').append('<tr id="form-episode-list-row-'+i+'"><td><input id="form-episode-list-division-'+i+'" name="form-episode-list-division-'+i+'" type="number" class="form-control" value="" step="any" required/></td><td><input id="form-episode-list-num-'+i+'" oninput="checkEpisodeRow('+i+');" name="form-episode-list-num-'+i+'" type="number" class="form-control" value="" placeholder="'+lang('js.admin.series_edit.episode.number_placeholder')+'" step="any"/><input id="form-episode-list-id-'+i+'" name="form-episode-list-id-'+i+'" type="hidden" value="-1"/><input id="form-episode-list-has_version-'+i+'" type="hidden" value="0"/></td><td><input id="form-episode-list-description-'+i+'" name="form-episode-list-description-'+i+'" type="text" class="form-control" value="" placeholder="'+lang('js.admin.series_edit.episode.description_placeholder')+'" maxlength="500"/></td><td class="text-center align-middle"><button id="form-episode-list-delete-'+i+'" onclick="deleteEpìsodeRow('+i+');" type="button" class="btn fa fa-trash p-1 fa-width-auto text-danger"></button></td></tr>');
	} else {
		var htmlAcc = $('#form-episode-list-linked_episode_id-XXX').prop('outerHTML').replace(/XXX/g, i).replace(' d-none">','" required>');

		$('#episode-list-table').append('<tr id="form-episode-list-row-'+i+'"><td><input id="form-episode-list-division-'+i+'" name="form-episode-list-division-'+i+'" type="number" class="form-control" value="" step="any" required/></td><td><input id="form-episode-list-num-'+i+'" oninput="checkEpisodeRow('+i+');" name="form-episode-list-num-'+i+'" type="number" class="form-control" value="" placeholder="'+lang('js.admin.series_edit.episode.number_placeholder')+'" step="any"/><input id="form-episode-list-id-'+i+'" name="form-episode-list-id-'+i+'" type="hidden" value="-1"/><input id="form-episode-list-has_version-'+i+'" type="hidden" value="0"/></td><td>'+htmlAcc+'</td><td class="text-center align-middle"><button id="form-episode-list-delete-'+i+'" onclick="deleteEpìsodeRow('+i+');" type="button" class="btn fa fa-trash p-1 fa-width-auto text-danger"></button></td></tr>');
	}
	$('#episode-list-table').attr('data-count', i);
	$('#form-episode-list-division-'+i).val($('#form-episode-list-division-'+(i-1)).val()!='' ? $('#form-episode-list-division-'+(i-1)).val() : 1);

	if (!special) {
		$('#form-episode-list-description-'+i).val('');
		$('#form-episode-list-description-'+i).addClass('d-none');
		$('#form-episode-list-num-'+i).val($('#form-episode-list-num-'+(i-1)).val()!='' ? parseInt($('#form-episode-list-num-'+(i-1)).val())+1 : 1);
	}
}

function checkEpisodeRow(id) {
	if ($('#form-episode-list-num-'+id).val()=='') {
		$("#form-episode-list-description-"+id).removeClass('d-none');
	} else {
		$("#form-episode-list-description-"+id).addClass('d-none');
		$("#form-episode-list-description-"+id).val('');
	}
}

function deleteEpìsodeRow(id) {
	var i = parseInt($('#episode-list-table').attr('data-count'));
	if(i==1) {
		alert(lang('js.admin.series_edit.error.at_least_one_episode'));
	}
	else if ($('#form-episode-list-has_version-'+id).val()==1) {
		alert(lang('js.admin.series_edit.error.cannot_delete_episode'));
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
			$("#form-episode-list-num-"+j).attr('oninput','checkEpìsodeRow('+(j-1)+');');
			$("#form-episode-list-description-"+j).attr('class',$('form-episode-list-description-'+(j-1)).attr('class'));
			if ($("#form-episode-list-description-"+j).length>0) {
				$("#form-episode-list-description-"+j).attr('name','form-episode-list-description-'+(j-1));
				$("#form-episode-list-description-"+j).attr('id','form-episode-list-description-'+(j-1));
			} else if ($("#form-episode-list-linked_episode_id-"+j).length>0) {
				$("#form-episode-list-linked_episode_id-"+j).attr('name','form-episode-list-linked_episode_id-'+(j-1));
				$("#form-episode-list-linked_episode_id-"+j).attr('id','form-episode-list-linked_episode_id-'+(j-1));
			}
			$("#form-episode-list-delete-"+j).attr('onclick','deleteEpìsodeRow('+(j-1)+');');
			$("#form-episode-list-delete-"+j).attr('id','form-episode-list-delete-'+(j-1));
			$("#form-episode-list-delete-"+j).attr('class',$('form-episode-list-delete-'+(j-1)).attr('class'));
		}
		$('#episode-list-table').attr('data-count', i-1);
	}
}

function addDivisionRow() {
	var i = parseInt($('#division-list-table').attr('data-count'))+1;
	$('#division-list-table').append('<tr id="form-division-list-row-'+i+'"><td><input id="form-division-list-number-'+i+'" name="form-division-list-number-'+i+'" type="number" class="form-control" value="'+(parseInt($('#form-division-list-number-'+(i-1)).val())+1)+'" step="any" required/><input id="form-division-list-id-'+i+'" name="form-division-list-id-'+i+'" type="hidden" value="-1"/></td><td><input id="form-division-list-name-'+i+'" name="form-division-list-name-'+i+'" type="text" class="form-control" value="'+($('#type').val()=='manga' ? lang('js.admin.generic.volume_prefix')+i : '')+'" placeholder="'+lang('js.admin.series_edit.division.name_placeholder')+'" required/></td><td><input id="form-division-list-number_of_episodes-'+i+'" name="form-division-list-number_of_episodes-'+i+'" type="number" class="form-control" value="" required/></td><td><input id="form-division-list-external_id-'+i+'" name="form-division-list-external_id-'+i+'"'+($('#type').val()!='liveaction' ? ' type="number"' : '')+' class="form-control" value=""/></td><td class="text-center" style="padding-top: .75rem;"><input id="form-division-list-is_real-'+i+'" name="form-division-list-is_real-'+i+'" type="checkbox" value="1" checked/><td class="text-center align-middle"><button id="form-division-list-delete-'+i+'" onclick="deleteDivisionRow('+i+');" type="button" class="btn fa fa-trash p-1 text-danger"></button></td></tr>');
	$('#division-list-table').attr('data-count', i);
}

function deleteDivisionRow(id) {
	var i = parseInt($('#division-list-table').attr('data-count'));
	if(i==1) {
		alert(lang('js.admin.series_edit.error.at_least_one_division'));
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
	$('#links-list-table-'+episode_id+'-'+variant_number+' tbody').append('<tr id="form-links-list-'+episode_id+'-row-'+variant_number+'-'+i+'" style="background: none;"><td class="ps-0 pt-0 pb-0 border-0"><input id="form-files-list-'+episode_id+'-file-'+variant_number+'-link-'+i+'-url" name="form-files-list-'+episode_id+'-file-'+variant_number+'-link-'+i+'-url" type="text" pattern="'+linkUrlPattern+'" class="form-control" value="" maxlength="2048" placeholder="'+lang('js.admin.version_edit.episode.link_placeholder')+'" oninput="$(this).attr(\'value\',$(this).val());"/><input id="form-files-list-'+episode_id+'-file-'+variant_number+'-link-'+i+'-id" name="form-files-list-'+episode_id+'-file-'+variant_number+'-link-'+i+'-id" type="hidden" value="-1"/></td><td class="pt-0 pb-0 border-0" style="width: 22%;"><input id="form-files-list-'+episode_id+'-file-'+variant_number+'-link-'+i+'-resolution" name="form-files-list-'+episode_id+'-file-'+variant_number+'-link-'+i+'-resolution" type="text" class="form-control" list="resolution-options" value="" maxlength="200" placeholder="'+lang('js.admin.version_edit.episode.resolution_placeholder')+'"/></td><td class="pt-0 pb-0 border-0 text-center align-middle" style="width: 5%;"><button id="form-files-list-'+episode_id+'-file-'+variant_number+'-link-'+i+'-delete" onclick="deleteLinkRow('+episode_id+','+variant_number+','+i+');" type="button" class="btn fa fa-fw fa-times p-1 fa-width-auto text-danger" title="'+lang('js.admin.version_edit.episode.delete_link_title')+'"></button></td></tr>');
	$('#links-list-table-'+episode_id+'-'+variant_number).attr('data-count', i);
}

function addExtraLinkRow(extra_number) {
	var i = parseInt($('#extras-links-list-table-'+extra_number).attr('data-count'))+1;
	$('#extras-links-list-table-'+extra_number+' tbody').append('<tr id="form-links-extras-list-row-'+extra_number+'-'+i+'" style="background: none;"><td class="ps-0 pt-0 pb-0 border-0"><input id="form-extras-list-'+extra_number+'-link-'+i+'-url" name="form-extras-list-'+extra_number+'-link-'+i+'-url" type="text" pattern="'+linkUrlPattern+'" class="form-control" value="" maxlength="2048" placeholder="'+lang('js.admin.version_edit.episode.link_placeholder_extra')+'" oninput="$(this).attr(\'value\',$(this).val());" required/><input id="form-extras-list-'+extra_number+'-link-'+i+'-id" name="form-extras-list-'+extra_number+'-link-'+i+'-id" type="hidden" value="-1"/></td><td class="pt-0 pb-0 border-0" style="width: 22%;"><input id="form-extras-list-'+extra_number+'-link-'+i+'-resolution" name="form-extras-list-'+extra_number+'-link-'+i+'-resolution" type="text" class="form-control" list="resolution-options" value="" maxlength="200" placeholder="'+lang('js.admin.version_edit.episode.resolution_placeholder')+'" required/></td><td class="pt-0 pb-0 border-0 text-center align-middle" style="width: 5%;"><button id="form-extras-list-'+extra_number+'-link-'+i+'-delete" onclick="deleteExtraLinkRow('+extra_number+','+i+');" type="button" class="btn fa fa-fw fa-times p-1 fa-width-auto text-danger" title="'+lang('js.admin.version_edit.episode.delete_link_title')+'"></button></td></tr>');
	$('#extras-links-list-table-'+extra_number).attr('data-count', i);
}

function addVersionRow(episode_id) {
	if (isAutoFetchActive()){
		alert(lang('js.admin.version_edit.error.cannot_add_variants_if_autofetching'));
		return;
	}

	var i = parseInt($('#files-list-table-'+episode_id).attr('data-count'))+1;

	var contents = '<tr id="form-files-list-'+episode_id+'-row-'+i+'"><td><input id="form-files-list-'+episode_id+'-variant_name-'+i+'" name="form-files-list-'+episode_id+'-variant_name-'+i+'" type="text" class="form-control" value="" maxlength="200" placeholder="'+lang('js.admin.version_edit.episode.variant_placeholder')+'" required/><input id="form-files-list-'+episode_id+'-id-'+i+'" name="form-files-list-'+episode_id+'-id-'+i+'" type="hidden" value="-1"/></td>';
	if ($('#type').val()=='manga') {
		contents += '<td class="align-middle"><div id="form-files-list-'+episode_id+'-file_details-'+i+'" class="small"><span style="color: gray;"><span class="fa fa-times fa-fw"></span> '+lang('js.admin.generic.no_file_uploaded')+'</span></div></td><td class="align-middle"><label style="margin-bottom: 0;" for="form-files-list-'+episode_id+'-file-'+i+'" class="btn btn-sm btn-primary w-100"><span class="fa fa-upload pe-2"></span>'+lang('js.admin.generic.upload_file')+'</label><input id="form-files-list-'+episode_id+'-file-'+i+'" name="form-files-list-'+episode_id+'-file-'+i+'" type="file" accept=".zip,.rar,.cbz,.cbr" class="form-control d-none" onchange="uncompressFile(this);"/><input id="form-files-list-'+episode_id+'-length-'+i+'" name="form-files-list-'+episode_id+'-length-'+i+'" type="hidden" value="0"/></td>';
	} else {
		contents += '<td><table class="w-100" id="links-list-table-'+episode_id+'-'+i+'" data-count="1"><tbody><tr id="form-links-list-'+episode_id+'-row-'+i+'-1" style="background: none;"><td class="ps-0 pt-0 pb-0 border-0"><input id="form-files-list-'+episode_id+'-file-'+i+'-link-1-url" name="form-files-list-'+episode_id+'-file-'+i+'-link-1-url" type="text" pattern="'+linkUrlPattern+'" class="form-control" value="" maxlength="2048" placeholder="'+lang('js.admin.version_edit.episode.link_placeholder')+'" oninput="$(this).attr(\'value\',$(this).val());"/><input id="form-files-list-'+episode_id+'-file-'+i+'-link-1-id" name="form-files-list-'+episode_id+'-file-'+i+'-link-1-id" type="hidden" value="-1"/></td><td class="pt-0 pb-0 border-0" style="width: 22%;"><input id="form-files-list-'+episode_id+'-file-'+i+'-link-1-resolution" name="form-files-list-'+episode_id+'-file-'+i+'-link-1-resolution" type="text" class="form-control" list="resolution-options" value="" maxlength="200" placeholder="'+lang('js.admin.version_edit.episode.resolution_placeholder')+'"/></td><td class="pt-0 pb-0 border-0 text-center align-middle" style="width: 5%;"><button id="form-files-list-'+episode_id+'-file-'+i+'-link-1-delete" onclick="deleteLinkRow('+episode_id+','+i+',1);" type="button" class="btn fa fa-fw fa-times p-1 text-danger" title="'+lang('js.admin.version_edit.episode.delete_link_title')+'"></button></td></tr></tbody></table></td><td><input id="form-files-list-'+episode_id+'-length-'+i+'" name="form-files-list-'+episode_id+'-length-'+i+'" type="time" step="1" class="form-control"/>';
	}

	contents += '<td><input id="form-files-list-'+episode_id+'-comments-'+i+'" name="form-files-list-'+episode_id+'-comments-'+i+'" type="text" class="form-control" value="" maxlength="200"/></td><td class="text-center" style="padding-top: .75rem;"><input id="form-files-list-'+episode_id+'-is_lost-'+i+'" name="form-files-list-'+episode_id+'-is_lost-'+i+'" type="checkbox" value="1"/></td><td class="text-center pt-2"><button onclick="addVersionRow('+episode_id+');" type="button" class="btn text-primary btn-sm fa p-1 fa-width-auto fa-arrows-split-up-and-left fa-rotate-180" title="'+lang('js.admin.version_edit.episode.add_file_variant_title')+'"></button>'+(($('#type').val()!='manga') ? '<button id="form-files-list-'+episode_id+'-add_link-'+i+'" onclick="addLinkRow('+episode_id+','+i+');" type="button" class="btn text-success btn-sm fa p-1 fa-width-auto fa-link" title="'+lang('js.admin.version_edit.episode.add_file_link_title')+'"></button>' : '')+'<button id="form-files-list-'+episode_id+'-delete-'+i+'" onclick="deleteVersionRow('+episode_id+','+i+');" type="button" class="btn fa fa-trash p-1 fa-width-auto text-danger" title="'+lang('js.admin.version_edit.episode.delete_file_title')+'"></button></td></tr>';

	$('#files-list-table-'+episode_id).append(contents);
	$('#files-list-table-'+episode_id).attr('data-count', i);
}

function addVersionExtraRow() {
	var i = parseInt($('#extras-list-table').attr('data-count'))+1;

	var contents = '<tr id="form-extras-list-row-'+i+'"><td><input id="form-extras-list-name-'+i+'" name="form-extras-list-name-'+i+'" type="text" class="form-control" value="" maxlength="200" required placeholder="'+lang('js.admin.version_edit.episode.extra_title_placeholder')+'"/><input id="form-extras-list-id-'+i+'" name="form-extras-list-id-'+i+'" type="hidden" value="-1"/></td>';
	
	if ($('#type').val()=='manga') {
		contents += '<td class="align-middle"><div id="form-extras-list-file_details-'+i+'" class="small"><span style="color: gray;"><span class="fa fa-times fa-fw"></span> '+lang('js.admin.generic.no_file_uploaded')+'</span></div></td><td class="align-middle"><label style="margin-bottom: 0;" for="form-extras-list-file-'+i+'" class="btn btn-sm btn-primary w-100"><span class="fa fa-upload pe-2"></span>'+lang('js.admin.generic.upload_file')+'</label><input id="form-extras-list-file-'+i+'" name="form-extras-list-file-'+i+'" type="file" accept=".zip,.rar,.cbz,.cbr" class="form-control d-none" onchange="uncompressFile(this);" required/><input id="form-extras-list-length-'+i+'" name="form-extras-list-length-'+i+'" type="hidden" value="-1"/></td>';
	} else {
		contents += '<td><table class="w-100" id="extras-links-list-table-'+i+'" data-count="1"><tbody><tr id="form-links-extras-list-row-'+i+'-1" style="background: none;"><td class="ps-0 pt-0 pb-0 border-0"><input id="form-extras-list-'+i+'-link-1-url" name="form-extras-list-'+i+'-link-1-url" type="text" pattern="'+linkUrlPattern+'" class="form-control" value="" maxlength="2048" placeholder="'+lang('js.admin.version_edit.episode.link_placeholder_extra')+'" oninput="$(this).attr(\'value\',$(this).val());" required/><input id="form-extras-list-'+i+'-link-1-id" name="form-extras-list-'+i+'-link-1-id" type="hidden" value="-1"/></td><td class="pt-0 pb-0 border-0" style="width: 22%;"><input id="form-extras-list-'+i+'-link-1-resolution" name="form-extras-list-'+i+'-link-1-resolution" type="text" class="form-control" list="resolution-options" value="" maxlength="200" placeholder="'+lang('js.admin.version_edit.episode.resolution_placeholder')+'" required/></td><td class="pt-0 pb-0 border-0 text-center align-middle" style="width: 5%;"><button id="form-extras-list-'+i+'-link-1-delete" onclick="deleteExtraLinkRow('+i+',1);" type="button" class="btn fa fa-fw fa-times p-1 fa-width-auto text-danger" title="'+lang('js.admin.version_edit.episode.delete_link_title')+'"></button></td></tr></tbody></table></td><td><input id="form-extras-list-length-'+i+'" name="form-extras-list-length-'+i+'" type="time" step="1" class="form-control" required/></td>';
	}

	contents += '<td><input id="form-extras-list-comments-'+i+'" name="form-extras-list-comments-'+i+'" type="text" class="form-control" value="" maxlength="200"/></td><td class="text-center pt-2">'+(($('#type').val()!='manga') ? '<button id="form-extras-list-add_link-'+i+'" onclick="addExtraLinkRow('+i+');" type="button" class="btn text-success btn-sm fa p-1 fa-width-auto fa-link" title="'+lang('js.admin.version_edit.episode.add_file_link_title')+'"></button>' : '')+'<button id="form-extras-list-delete-'+i+'" onclick="deleteVersionExtraRow('+i+');" type="button" class="btn fa fa-trash p-1 fa-width-auto text-danger" title="'+lang('js.admin.version_edit.episode.delete_file_title')+'"></button></td></tr>';

	$('#extras-list-table').append(contents);
	$('#extras-list-table').attr('data-count', i);
	$('#extras-list-table-empty').addClass('d-none');
}

function uncompressFile(fileInput) {
	var detailsElement=$(document.getElementById(fileInput.id.replace("file-", "file_details-")));
	var numberOfPagesElement=$(document.getElementById(fileInput.id.replace("file-", "length-")));
	// Just return if there is no file selected
	if (fileInput.files.length === 0) {
		detailsElement.html("<span style=\"color: gray;\"><span class=\"fa fa-times fa-fw\"></span> "+lang('js.admin.generic.no_file_selected_no_changes')+"</span>");
		numberOfPagesElement.val(0);
		$('label[for="'+fileInput.id+'"]').removeClass("btn-danger");
		$('label[for="'+fileInput.id+'"]').removeClass("btn-primary");
		$('label[for="'+fileInput.id+'"]').removeClass("btn-success");
		$('label[for="'+fileInput.id+'"]').removeClass("btn-warning");
		$('label[for="'+fileInput.id+'"]').addClass("btn-secondary");
		$('label[for="'+fileInput.id+'"]').html('<span class="fa fa-times pe-2"></span>'+lang('js.admin.generic.no_changes'));
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
			var countMusic=0;
			var countRepeated=0;
			var foundNames = [];
			archive.entries.forEach(function(entry) {
				if (entry.is_file) {
					if (!entry.name.includes('__MACOSX') ) {
						if (entry.name.toLowerCase().endsWith(".jpg") || entry.name.toLowerCase().endsWith(".png") || entry.name.toLowerCase().endsWith(".jpeg")) {
							countImages++;
						}
						if (entry.name.toLowerCase().endsWith(".mp3") || entry.name.toLowerCase().endsWith(".ogg")) {
							countMusic++;
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

			if (countMusic>1) {
				detailsElement.html("<span style=\"color: #bd2130;\"><span class=\"fa fa-times fa-fw\"></span> "+lang('js.admin.generic.file_cannot_be_uploaded').replaceAll('%s', file.name)+"<br /><span class=\"fa fa-exclamation-triangle fa-fw\"></span> "+lang('js.admin.generic.file_contains_multiple_audio').replaceAll('%d', countMusic)+"</span>");
				fileInput.value="";
				$('label[for="'+fileInput.id+'"]').removeClass("btn-warning");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-primary");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-secondary");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-success");
				$('label[for="'+fileInput.id+'"]').addClass("btn-danger");
				$('label[for="'+fileInput.id+'"]').html('<span class="fa fa-upload pe-2"></span>'+lang('js.admin.generic.upload_file'));
				numberOfPagesElement.val(0);
			} else if ((countImages+countMusic)==count && countRepeated==0) {
				detailsElement.html("<span style=\"color: #1e7e34;\"><span class=\"fa fa-check fa-fw\"></span> "+lang('js.admin.generic.file_will_be_uploaded').replaceAll('%s', file.name)+"<br /><span class=\"fa fa-file-archive fa-fw\"></span> "+(countMusic>0 ? lang('js.admin.generic.file_contains_files_audio').replaceAll('%d', countImages) : lang('js.admin.generic.file_contains_files').replaceAll('%d', countImages))+"</span>");
				numberOfPagesElement.val(countImages);
				$('label[for="'+fileInput.id+'"]').removeClass("btn-danger");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-warning");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-primary");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-secondary");
				$('label[for="'+fileInput.id+'"]').addClass("btn-success");
				$('label[for="'+fileInput.id+'"]').html('<span class="fa fa-check pe-2"></span>'+lang('js.admin.generic.will_upload'));
			} else if (countRepeated>0) {
				detailsElement.html("<span style=\"color: #bd2130;\"><span class=\"fa fa-times fa-fw\"></span> "+lang('js.admin.generic.file_cannot_be_uploaded').replaceAll('%s', file.name)+"<br /><span class=\"fa fa-exclamation-triangle fa-fw\"></span> "+lang('js.admin.generic.file_contains_repeats').replaceAll('%d', countRepeated)+"</span>");
				fileInput.value="";
				$('label[for="'+fileInput.id+'"]').removeClass("btn-warning");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-primary");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-secondary");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-success");
				$('label[for="'+fileInput.id+'"]').addClass("btn-danger");
				$('label[for="'+fileInput.id+'"]').html('<span class="fa fa-upload pe-2"></span>'+lang('js.admin.generic.upload_file'));
				numberOfPagesElement.val(0);
			} else if (countImages==0) {
				detailsElement.html("<span style=\"color: #bd2130;\"><span class=\"fa fa-times fa-fw\"></span> "+lang('js.admin.generic.file_cannot_be_uploaded').replaceAll('%s', file.name)+"<br /><span class=\"fa fa-exclamation-triangle fa-fw\"></span> "+lang('js.admin.generic.file_has_no_images')+"</span>");
				fileInput.value="";
				numberOfPagesElement.val(0);
				$('label[for="'+fileInput.id+'"]').removeClass("btn-warning");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-primary");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-secondary");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-success");
				$('label[for="'+fileInput.id+'"]').addClass("btn-danger");
				$('label[for="'+fileInput.id+'"]').html('<span class="fa fa-upload pe-2"></span>'+lang('js.admin.generic.upload_file'));
			} else {
				detailsElement.html("<span style=\"color: #d39e00;\"><span class=\"fa fa-check fa-fw\"></span> "+lang('js.admin.generic.file_will_be_uploaded').replaceAll('%s', file.name)+"<br /><span class=\"fa fa-file-archive fa-fw\"></span> "+(countMusic>0 ? lang('js.admin.generic.file_contains_files_audio').replaceAll('%d', countImages) : lang('js.admin.generic.file_contains_files').replaceAll('%d', countImages))+"<br /><span class=\"fa fa-exclamation-triangle fa-fw\"></span> "+lang('js.admin.generic.file_contains_unsupported_files').replaceAll('%d', count-countImages-countMusic)+"</span>");
				numberOfPagesElement.val(countImages);
				$('label[for="'+fileInput.id+'"]').removeClass("btn-danger");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-primary");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-secondary");
				$('label[for="'+fileInput.id+'"]').removeClass("btn-success");
				$('label[for="'+fileInput.id+'"]').addClass("btn-warning");
				$('label[for="'+fileInput.id+'"]').html('<span class="fa fa-exclamation-triangle pe-2"></span>'+lang('js.admin.generic.will_upload'));
			}
		} else {
			detailsElement.html("<span style=\"color: #bd2130;\"><span class=\"fa fa-times fa-fw\"></span> "+lang('js.admin.generic.file_must_be_archive')+"</span>");
			fileInput.value="";
			numberOfPagesElement.val(0);
			$('label[for="'+fileInput.id+'"]').removeClass("btn-warning");
			$('label[for="'+fileInput.id+'"]').removeClass("btn-primary");
			$('label[for="'+fileInput.id+'"]').removeClass("btn-secondary");
			$('label[for="'+fileInput.id+'"]').removeClass("btn-success");
			$('label[for="'+fileInput.id+'"]').addClass("btn-danger");
			$('label[for="'+fileInput.id+'"]').html('<span class="fa fa-upload pe-2"></span>'+lang('js.admin.generic.upload_file'));
		}
	});
}

function addRelatedSeriesRow(seriesId, seriesName) {
	var i = parseInt($('#related-list-table').attr('data-count'))+1;
	$('#related-list-table').append('<tr id="form-related-list-row-'+i+'"><td><input type="hidden" id="form-related-list-related_series_id-'+i+'" name="form-related-list-related_series_id-'+i+'" value="'+seriesId+'"/><b>'+seriesName+'</b></td><td class="text-center align-middle"><button id="form-related-list-delete-'+i+'" onclick="deleteRelatedSeriesRow('+i+');" type="button" class="btn fa fa-trash p-1 fa-width-auto text-danger"></button></td></tr>');
	$('#related-list-table').attr('data-count', i);
	$('#related-list-table-empty').addClass('d-none');
	$('#add-related-series-modal').modal('hide');
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

	$('#remote_folders-list-table').append('<tr id="form-remote_folders-list-row-'+i+'"><td>'+htmlAcc+'<input id="form-remote_folders-list-id-'+i+'" name="form-remote_folders-list-id-'+i+'" type="hidden" value="-1"/></td><td><input id="form-remote_folders-list-folder-'+i+'" name="form-remote_folders-list-folder-'+i+'" class="form-control" value="" maxlength="200" required/></td><td><input id="form-remote_folders-list-default_resolution-'+i+'" name="form-remote_folders-list-default_resolution-'+i+'" class="form-control" value="" maxlength="200" required placeholder="'+lang('js.admin.version_edit.episode.resolution_placeholder')+'" list="resolution-options"/></td><td><input id="form-remote_folders-list-default_duration-'+i+'" name="form-remote_folders-list-default_duration-'+i+'" type="time" step="1" class="form-control" value="" required/></td><td>'+htmlSea+'</td><td class="text-center align-middle"><input id="form-remote_folders-list-is_active-'+i+'" name="form-remote_folders-list-is_active-'+i+'" type="checkbox" value="1"/></td><td class="text-center align-middle"><button id="form-remote_folders-list-delete-'+i+'" onclick="deleteVersionRemoteFolderRow('+i+');" type="button" class="btn fa fa-trash p-1 fa-width-auto text-danger"></button></td></tr>');
	$('#remote_folders-list-table').attr('data-count', i);
	$('#remote_folders-list-table-empty').addClass('d-none');
}

function deleteVersionRow(episode_id, id) {
	if ($("#form-files-list-"+episode_id+"-id-"+id).val()=='-1' || ($("#form-files-list-"+episode_id+"-id-"+id).val()!='-1' && confirm($('#type').val()=='manga' ? lang('js.admin.version_edit.warning.delete_file.manga') : lang('js.admin.version_edit.warning.delete_file')))) {
		var i = parseInt($('#files-list-table-'+episode_id).attr('data-count'));
		if(i==1) {
			$("#form-files-list-"+episode_id+"-id-1").val("-1");
			$("#form-files-list-"+episode_id+"-comments-1").val("");
			$("#form-files-list-"+episode_id+"-is_lost-1").prop('checked',false);
			if ($('#type').val()=='manga') {
				$("#form-files-list-"+episode_id+"-file-1").val("");
				$('label[for="form-files-list-'+episode_id+'-file-1"]').removeClass("btn-warning");
				$('label[for="form-files-list-'+episode_id+'-file-1"]').addClass("btn-primary");
				$('label[for="form-files-list-'+episode_id+'-file-1"]').html('<span class="fa fa-upload pe-2"></span> '+lang('js.admin.generic.upload_file'));
				$("#form-files-list-"+episode_id+"-file_details-1").html('<span style="color: gray;"><span class="fa fa-times fa-fw"></span> '+lang('js.admin.generic.no_file_uploaded')+'</span>');
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
	if ($("#form-extras-list-id-"+id).val()=='-1' || ($("#form-extras-list-id-"+id).val()!='-1' && confirm($('#type').val()=='manga' ? lang('js.admin.version_edit.warning.delete_extra.manga') : lang('js.admin.version_edit.warning.delete_extra')))) {
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
		$("#form-remote_folders-list-remote_account_id-"+j).attr('name','form-remote_folders-list-remote_account_id-'+(j-1));
		$("#form-remote_folders-list-remote_account_id-"+j).attr('id','form-remote_folders-list-remote_account_id-'+(j-1));
		$("#form-remote_folders-list-default_resolution-"+j).attr('name','form-remote_folders-list-default_resolution-'+(j-1));
		$("#form-remote_folders-list-default_resolution-"+j).attr('id','form-remote_folders-list-default_resolution-'+(j-1));
		$("#form-remote_folders-list-default_duration-"+j).attr('name','form-remote_folders-list-default_duration-'+(j-1));
		$("#form-remote_folders-list-default_duration-"+j).attr('id','form-remote_folders-list-default_duration-'+(j-1));
		$("#form-remote_folders-list-folder-"+j).attr('name','form-remote_folders-list-folder-'+(j-1));
		$("#form-remote_folders-list-folder-"+j).attr('id','form-remote_folders-list-folder-'+(j-1));
		$("#form-remote_folders-list-division_id-"+j).attr('name','form-remote_folders-list-division_id-'+(j-1));
		$("#form-remote_folders-list-division_id-"+j).attr('id','form-remote_folders-list-division_id-'+(j-1));
		$("#form-remote_folders-list-is_active-"+j).attr('name','form-remote_folders-list-is_active-'+(j-1));
		$("#form-remote_folders-list-is_active-"+j).attr('id','form-remote_folders-list-is_active-'+(j-1));
		$("#form-remote_folders-list-delete-"+j).attr('onclick','deleteVersionRemoteFolderRow('+(j-1)+');');
		$("#form-remote_folders-list-delete-"+j).attr('id','form-remote_folders-list-delete-'+(j-1));
	}
	$('#remote_folders-list-table').attr('data-count', i-1);

	if (i-1==0) {
		$('#remote_folders-list-table-empty').removeClass('d-none');
	}
}

function recalculateDivisionNames() {
	if ($('#type').val()=='manga' && ($("#form-division-list-name-1").val()==lang('js.admin.generic.volume_prefix')+'1' || $("#form-division-list-name-1").val()==lang('js.admin.generic.oneshot') || $("#form-division-list-name-1").val()==lang('js.admin.generic.light_novel'))) {
		if ($('#form-subtype').val()=='oneshot' && $('#form-comic_type').val()=='novel') {
			$("#form-division-list-name-1").val(lang('js.admin.generic.light_novel'));
		}
		else if ($('#form-subtype').val()=='oneshot' && $('#form-comic_type').val()!='novel') {
			$("#form-division-list-name-1").val(lang('js.admin.generic.oneshot'));
		}
		else {
			$("#form-division-list-name-1").val(lang('js.admin.generic.volume_prefix')+'1');
		}
	}
}

function checkNumberOfEpisodes() {
	var episodeCount = parseInt($('#episode-list-table').attr('data-count'));
	var divisionsEpisodes = $('[id^=form-division-list-number_of_episodes-]');
	var divisionsNumbers = $('[id^=form-division-list-number-]');
	var divisionsTitles = $('[id^=form-division-list-name-]');
	for (var i=0;i<divisionsEpisodes.length;i++){
		if ($(divisionsEpisodes[i]).val()!='') {
			var divisionEpisodeCount=parseInt($(divisionsEpisodes[i]).val());
			var divisionNumber=parseInt($(divisionsNumbers[i]).val());
			var realDivisionEpisodeCount = 0;
			for (var j=1;j<=episodeCount;j++){
				if ($('#form-episode-list-division-'+j).val()==divisionNumber){
					realDivisionEpisodeCount++;
				}
			}
			if (divisionEpisodeCount!=realDivisionEpisodeCount){
				alert(lang('js.admin.series_edit.error.division_has_unmatching_episodes').replaceAll('%1$d', divisionNumber).replaceAll('%2$d', divisionEpisodeCount).replaceAll('%3$d', realDivisionEpisodeCount));
				return false;
			}
		}
	}
	if ($('#type').val()!='manga') {
		for (var i=0;i<divisionsTitles.length;i++){
			var title = $(divisionsTitles[i]).val();
			if (title.toLowerCase().startsWith(lang('js.admin.series_edit.validation.season'))) {
				alert(lang('js.admin.series_edit.error.division_cannot_start_with_season'));
				return false;
			}
			if (title.toLowerCase().startsWith(lang('js.admin.series_edit.validation.special'))) {
				alert(lang('js.admin.series_edit.error.division_cannot_start_with_special'));
				return false;
			}
			if (title.toLowerCase().startsWith(lang('js.admin.series_edit.validation.ova'))) {
				alert(lang('js.admin.series_edit.error.division_cannot_start_with_ova'));
				return false;
			}
		}
	}
	for (var i=0;i<divisionsTitles.length;i++){
		for (var j=0;j<divisionsTitles.length;j++){
			if ($(divisionsTitles[j]).val()==$(divisionsTitles[i]).val() && i!=j) {
				alert(lang('js.admin.series_edit.error.division_is_repeated'));
				return false;
			}
		}
	}
	for (var i=1;i<=episodeCount;i++){
		if ($('#form-episode-list-num-'+i).val()=='' && $('#form-episode-list-description-'+i).val()==''){
			alert(lang('js.admin.series_edit.error.episodes_with_no_number_or_specials_without_name'));
			return false;
		}
	}

	var divisionNumbers = [];
	for (var i=0;i<divisionsEpisodes.length;i++){
		divisionNumbers.push($('#form-division-list-number-'+(i+1)).val());
	}
	var higherThanCount=false;
	for (var i=1;i<=episodeCount;i++){
		if ($('#form-episode-list-division-'+i).val()!='' && !divisionNumbers.includes($('#form-episode-list-division-'+i).val())){
			alert(lang('js.admin.series_edit.error.episodes_with_invalid_division'));
			return false;
		}
		if ($('#form-episode-list-num-'+i).val()>episodeCount){
			higherThanCount=true;
		}
	}

	if ($('#form-demographics input:checked').length>1 && !confirm(lang('js.admin.series_edit.warning.more_than_one_demography'))) {
		return false;
	}

	if ($('#type').val()=='manga' && $('[id^=form-division-list-name-]').length==1 && $($('[id^=form-division-list-name-]')[0]).val()==lang('js.admin.generic.volume_prefix')+'1' && !confirm(lang('js.admin.series_edit.warning.division_with_volume_1_instead_of_unique'))){
		return false;
	}

	if ($('#type').val()=='manga' && $('#form-subtype').val()=='oneshot' && $('#form-comic_type').val()!='novel' && $($('[id^=form-division-list-name-]')[0]).val()!=lang('js.admin.generic.oneshot') && !confirm(lang('js.admin.series_edit.warning.division_with_name_instead_of_oneshot'))){
		return false;
	}

	if ($('#type').val()=='manga' && $('#form-subtype').val()=='oneshot' && $('#form-comic_type').val()=='novel' && $($('[id^=form-division-list-name-]')[0]).val()!=lang('js.admin.generic.light_novel') && !confirm(lang('js.admin.series_edit.warning.division_with_name_instead_of_light_novel'))){
		return false;
	}

	if ($('#type').val()=='manga' && $('#form-subtype').val()=='oneshot' && episodeCount>1) {
		alert(lang('js.admin.series_edit.error.oneshot_with_more_than_one_episode'));
		return false;
	}

	if ($('#type').val()!='manga' && $('#form-subtype').val()=='series' && episodeCount<2 && !confirm(lang('js.admin.series_edit.warning.series_with_one_episode'))) {
		return false;
	}

	if ($('#type').val()=='manga' && $('#form-subtype').val()=='serialized' && episodeCount<2 && !confirm(lang('js.admin.series_edit.warning.serialized_with_one_episode'))) {
		return false;
	}

	if (higherThanCount && !confirm(lang('js.admin.series_edit.warning.episode_numbers_higher_than_total'))){
		return false;
	}

	if ($('#form-name-with-autocomplete').val()==$('#form-alternate_names').val()) {
		alert(lang('js.admin.series_edit.error.title_and_alternate_titles_are_equal'));
		return false;
	}

	if ($('#form-alternate_names').val().startsWith($('#form-name-with-autocomplete').val()+',')) {
		alert(lang('js.admin.series_edit.error.alternate_titles_contains_title'));
		return false;
	}

	//List of supported characters extracted from Lexend Deca using: fc-match --format='%{charset}\n' "Lexend Deca"
	var supportedCharsRegex = /[^\u0020-\u007e\u00a0-\u017e\u018f\u0192\u019d\u01a0-\u01a1\u01af-\u01b0\u01c4-\u01d4\u01e6-\u01e7\u01ea-\u01eb\u01f1-\u01f2\u01fa-\u021b\u022a-\u022d\u0230-\u0233\u0237\u0259\u0272\u02bb-\u02bc\u02be-\u02bf\u02c6-\u02c8\u02cc\u02d8-\u02dd\u0300-\u0304\u0306-\u030c\u030f\u0311-\u0312\u031b\u0323-\u0324\u0326-\u0328\u032e\u0331\u0335\u0394\u03a9\u03bc\u03c0\u1e08-\u1e09\u1e0c-\u1e0f\u1e14-\u1e17\u1e1c-\u1e1d\u1e20-\u1e21\u1e24-\u1e25\u1e2a-\u1e2b\u1e2e-\u1e2f\u1e36-\u1e37\u1e3a-\u1e3b\u1e42-\u1e49\u1e4c-\u1e53\u1e5a-\u1e5b\u1e5e-\u1e69\u1e6c-\u1e6f\u1e78-\u1e7b\u1e80-\u1e85\u1e8e-\u1e8f\u1e92-\u1e93\u1e97\u1e9e\u1ea0-\u1ef9\u2007-\u200b\u2010\u2012-\u2015\u2018-\u201a\u201c-\u201e\u2020-\u2022\u2026\u2030\u2033\u2039-\u203a\u2044\u2070\u2074-\u2079\u2080-\u2089\u20a1\u20a3-\u20a4\u20a6-\u20a7\u20a9\u20ab-\u20ad\u20b1-\u20b2\u20b5\u20b9-\u20ba\u20bc-\u20bd\u2113\u2116\u2122\u2126\u212e\u215b-\u215e\u2202\u2205-\u2206\u220f\u2211-\u2212\u2215\u2219-\u221a\u221e\u222b\u2248\u2260\u2264-\u2265\u25ca\ufb01-\ufb02]/g;
	if ($('#form-name-with-autocomplete').val().match(supportedCharsRegex)) {
		alert(lang('js.admin.series_edit.error.title_contains_unsupported_chars').replaceAll('%s', $('#form-name-with-autocomplete').val().match(supportedCharsRegex).join('')));
		return false;
	}
	if ($('#form-alternate_names').val().match(supportedCharsRegex)) {
		alert(lang('js.admin.series_edit.error.alternate_titles_contains_unsupported_chars').replaceAll('%s', $('#form-alternate_names').val().match(supportedCharsRegex).join('')));
		return false;
	}
	if ($('#form-keywords').val().indexOf(',')>=0) {
		alert(lang('js.admin.series_edit.error.keywords_with_spaces'));
		return false;
	}

	//Disable form
	$('form').addClass('form-submitted');
	$('form').find('[type=submit]').html('<span class="fa fa-circle-notch fa-spin"></span>&nbsp;&nbsp;'+lang('js.admin.generic.processing'));

	return true;
}

function checkNewsPost() {
	if (!$('#form-fansub_id').val() && !confirm(lang('js.admin.news_edit.warning.using_main_site'))) {
		return false;
	}
	return true;
}

function checkFansub() {
	if (!$('#form-id').val() && !$('#form-icon').val()) {
		alert(lang('js.admin.fansub_edit.error.must_upload_icon'));
		return false;
	}

	return true;
}

function checkCommunity() {
	if (!$('#form-id').val() && !$('#form-logo').val()) {
		alert(lang('js.admin.link_edit.error.must_upload_logo'));
		return false;
	}

	return true;
}

function checkNumberOfLinks() {
	if (!$('#id').val() && !$('#form-image').val() && !$('#form-image_url').val()) {
		alert(lang('js.admin.version_edit.error.must_upload_cover_image'));
		return false;
	}

	if (!$('#id').val() && !$('#form-featured_image').val()) {
		alert(lang('js.admin.version_edit.error.must_upload_header_image'));
		return false;
	}

	if (!synopsisChanged) {
		alert(lang('js.admin.version_edit.error.synopsis_unchanged'));
		return false;
	}

	if ($('#form-title-with-autocomplete').val()==$('#form-alternate_titles').val()) {
		alert(lang('js.admin.version_edit.error.localized_title_and_alternate_titles_are_equal'));
		return false;
	}

	if ($('#form-series').val()==$('#form-alternate_titles').val()) {
		alert(lang('js.admin.version_edit.error.series_title_and_alternate_titles_are_equal'));
		return false;
	}
	
	if (document.getElementById('form-image-preview').naturalWidth<300 || document.getElementById('form-image-preview').naturalHeight<400) {
		alert(lang('js.admin.version_edit.error.cover_image_too_small'));
		return false;
	}

	if ($('#form-old_slug').val()!='' && $('#form-old_slug').val()!=$('#form-slug').val()) {
		return confirm(lang('js.admin.version_edit.warning.slug_changed').replaceAll('%1$s', $('#form-old_slug').val()).replaceAll('%2$s', $('#form-slug').val()));
	}
	
	var divisionsTitles = $('[id^=form-division-title-]');
	if ($('#type').val()!='manga') {
		for (var i=0;i<divisionsTitles.length;i++){
			var title = $(divisionsTitles[i]).val();
			if (title.toLowerCase().startsWith(lang('js.admin.series_edit.validation.season'))) {
				alert(lang('js.admin.series_edit.error.division_cannot_start_with_season'));
				return false;
			}
			if (title.toLowerCase().startsWith(lang('js.admin.series_edit.validation.special'))) {
				alert(lang('js.admin.series_edit.error.division_cannot_start_with_special'));
				return false;
			}
			if (title.toLowerCase().startsWith(lang('js.admin.series_edit.validation.ova'))) {
				alert(lang('js.admin.series_edit.error.division_cannot_start_with_ova'));
				return false;
			}
		}
	}
	for (var i=0;i<divisionsTitles.length;i++){
		for (var j=0;j<divisionsTitles.length;j++){
			if ($(divisionsTitles[j]).val()==$(divisionsTitles[i]).val() && i!=j) {
				alert(lang('js.admin.series_edit.error.division_is_repeated'));
				return false;
			}
		}
	}

	var downloadsUrls = $('[id^=form-downloads_url_]');
	for (var i=0;i<downloadsUrls.length;i++) {
		if (downloadsUrls[i].value.match(/.*https:\/\/mega(?:\.co)?\.nz\/fm\/.*/)) {
			alert(lang('js.admin.version_edit.error.download_url_is_wrong_mega_link'));
			return false;
		}
	}

	if (isAutoFetchActive()){
		var linkTables = $('[id^=files-list-table-]');
		var multipleLinks = false;
		for (var i=0;i<linkTables.length;i++) {
			if ($(linkTables[i]).attr('data-count')>1){
				multipleLinks = true;
				break;
			}
		}

		if (multipleLinks) {
			alert(lang('js.admin.version_edit.error.cannot_add_variants_if_sync_is_enabled'));
			return false;
		}
	}

	var folders = $('[id^=form-remote_folders-list-folder-]');
	for (var i=0;i<folders.length;i++) {
		if (folders[i].value.match(/.*https?:\/\/.*/)) {
			alert(lang('js.admin.version_edit.error.remote_folder_is_a_link'));
			return false;
		}
		if (folders[i].value.match(/\\/)) {
			alert(lang('js.admin.version_edit.error.remote_folder_has_backslash'));
			return false;
		}
	}

	var urls = $('[id$=-url]');
	for (var i=0;i<urls.length;i++) {
		if (urls[i].value!='') {
			var resolution = $('#'+urls[i].id.replace('-url','-resolution'));
			if (resolution.val()=='' || resolution.val()=='null' || (!resolution.val().includes('p') && !resolution.val().includes('x'))) {
				alert(lang('js.admin.version_edit.error.resolution_empty_of_invalid'));
				return false;
			}
			if (urls[i].value.startsWith("https://mega.nz/") && !urls[i].value.match(/https:\/\/mega(?:\.co)?\.nz\/(?:#!|embed#!|file\/|embed\/)?([a-zA-Z0-9]{0,8})[!#]([a-zA-Z0-9_-]+)/)) {
				alert(lang('js.admin.version_edit.error.invalid_mega_url').replaceAll('%s', urls[i].value));
				return false;
			}
			if (urls[i].value.match(/https:\/\/.*https:\/\//)) {
				alert(lang('js.admin.version_edit.error.invalid_generic_url').replaceAll('%s', urls[i].value));
				return false;
			}
			if ($(urls[i]).closest('.episode-container').find('.episode-title-input').attr('placeholder')==lang('js.admin.version_edit.episode.extra_title_placeholder') && $(urls[i]).closest('.episode-container').find('.episode-title-input').val()=='') {
				$(urls[i]).closest('.accordion-collapse').collapse('show');
				$(urls[i]).closest('.episode-container').find('.episode-title-input').focus();
				alert(lang('js.admin.version_edit.error.must_provide_episode_title'));
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
						alert(lang('js.admin.version_edit.error.must_provide_duration'));
						return false;
					}
				}
			}
		}
	} else { //Manga only
		var validFiles = $('.episode-container span .fa-check');
		for (var i=0;i<validFiles.length;i++) {
			if ($(validFiles[i]).closest('.episode-container').find('.episode-title-input').attr('placeholder')==lang('js.admin.version_edit.episode.extra_title_placeholder') && $(validFiles[i]).closest('.episode-container').find('.episode-title-input').val()=='') {
				$(urls[i]).closest('.accordion-collapse').collapse('show');
				$(validFiles[i]).closest('.episode-container').find('.episode-title-input').focus();
				alert(lang('js.admin.version_edit.error.must_provide_episode_title'));
				return false;
			}
		}
	}

	var files = $("[type=file]");
	var totalBytes = 0;
	for (var i=0;i<files.length;i++){
		if (files[i].files && files[i].files.length>0) {
			totalBytes+=files[i].files[0].size;
			if (files[i].files[0].size>524288000) {
				alert(lang('js.admin.version_edit.error.file_too_big'));
				return false;
			}
		}
	}

	if (totalBytes>524288000) {
		alert(lang('js.admin.version_edit.error.total_file_size_too_big'));
		return false;
	}

	//Check for inconsistent states
	var status = $('#form-status').val();
	var totalEpisodes = $('.episode-container:not(.linked-episode-container)').length;
	var validFiles = 0;
	if ($('#type').val()=='manga') {
		validFiles = $('.episode-container span .fa-check').length;
	} else {
		var episodes = $('.episode-container');
		for (var i=0;i<episodes.length;i++){
			var possibleUrls = $(episodes[i]).find('[id$=-url]');
			for (var j=0;j<possibleUrls.length;j++){
				if ($(possibleUrls[j]).val()!='') {
					validFiles++;
					break;
				}
			}
		}
	}
	if (status!=1 && status!=3 && validFiles>=totalEpisodes) {
		alert(lang('js.admin.version_edit.error.all_episodes_done_but_not_completed'));
		return false;
	} else if (status==1 && validFiles<totalEpisodes) {
		alert(lang('js.admin.version_edit.error.completed_but_not_all_episodes_done'));
		return false;
	} else if (validFiles==0 && status!=2) {
		alert(lang('js.admin.version_edit.error.no_episodes_with_content_and_not_in_progress'));
		return false;
	}

	//Disable form
	$('form').addClass('form-submitted');
	$('form').find('[type=submit]').html('<span class="fa fa-circle-notch fa-spin"></span>&nbsp;&nbsp;'+lang('js.admin.generic.processing'));

	return true;
}

function addEpisodeFromVersion() {
	var seriesId = $("[name='series_id']").val();
	var divisionId = $('#add-episode-from-version-division-id').val();
	var number = $('#add-episode-from-version-number').val();
	var description = $('#add-episode-from-version-description').val();
	if (number=='' && description=='') {
		alert(lang('js.admin.version_edit.error.add_episode_no_special_title'));
		return false;
	}
	
	//Call the server
	$.post("add_episode_from_version.php", {series_id: seriesId, division_id: divisionId, number: number, description: description}, function(data, status){
		var json = JSON.parse(data);
		var insertedId = json.inserted_id;
		var formattedNumber = json.formatted_number;
		var episodeTitle = json.episode_title;
		var divisionId = json.division_id;
		
		$($('#add-episode-from-version-template').html()
			.replaceAll('{template_id}',insertedId)
			.replaceAll('{formatted_number}',formattedNumber)
			.replaceAll('{episode_title}',episodeTitle)).appendTo($('#division-collapse-'+divisionId+' .accordion-body'));
		
		if (number=='') {
			$('#form-files-list-'+insertedId+'-title').removeClass('episode-title-input-numbered');
			$('#form-files-list-'+insertedId+'-title').attr('placeholder', lang('js.admin.version_edit.episode.title_placeholder'));
		} else if ($("#form-show_episode_numbers").val()==1) {
			$('#form-files-list-'+insertedId+'-title').attr('placeholder', lang('js.admin.generic.episode_prefix')+$('#form-files-list-'+insertedId+'-title').attr('data-episode-number'));
		} else {
			$('#form-files-list-'+insertedId+'-title').attr('placeholder', lang('js.admin.version_edit.episode.title_placeholder'));
		}
		
		$('#add-episode-from-version-modal').modal('hide');
	}).fail(function() {
		alert(lang('js.admin.version_edit.error.add_episode_generic_error'));
	});
}

function searchSeries() {
	var query = $('#add-related-series-query').val();
	
	//Call the server
	$.post("search_series.php", {text: query}, function(data, status){
		var json = JSON.parse(data);
		var html = '';
		for(var i=0;i<json.results.length;i++) {
			var seriesName = $('<div/>').text(json.results[i].name).html();
			html+='<a class="text-black mt-2 d-block" style="cursor: pointer; font-weight: bold;" onclick="addRelatedSeriesRow('+json.results[i].id+', \''+seriesName+'\')">'+seriesName+'</a>';
		}
		
		if (json.results.length==0) {
			html=lang('js.admin.series_edit.error.add_related_series_no_results');
		}
		
		$('#add-related-series-results').html(html);
	}).fail(function() {
		$('#add-related-series-results').html(lang('js.admin.series_edit.error.add_related_series_generic_error'));
	});
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
		$('#link-verifier-progress')[0].innerHTML=lang('js.admin.link_verifier.process_completed');
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

function isAutoFetchListPopulated() {
	return $('[id^=form-remote_folders-list-remote_account_id-]').length>1;
}

function isAutoFetchActive() {
	return $('[id^=form-remote_folders-list-is_active-]:checked').length>0;
}

function checkImageUpload(fileInput, maxBytes, fileMimeType, minResX, minResY, maxResX, maxResY, previewImageId, previewLinkId, optionalUrlId) {
	if (fileInput.files && fileInput.files[0]) {
		if (maxBytes!=-1 && fileInput.files[0].size>maxBytes) {
			alert(lang('js.admin.file_uploader.error_file_too_big').replaceAll('%d', maxBytes/1024));
		} else if (fileMimeType=='image/*' && !fileInput.files[0].type.startsWith('image/')) {
			alert(lang('js.admin.file_uploader.error_wrong_type'));
		} else if (fileMimeType!='image/*' && fileInput.files[0].type!=fileMimeType) {
			alert(lang('js.admin.file_uploader.error_wrong_type'));
		} else {
			var reader = new FileReader();
			reader.onload = function(e) {
				//Get image dimensions
				var img = new Image();
				img.src = e.target.result;
				img.onload = function(e2) {
					var width = img.naturalWidth;
					var height = img.naturalHeight;
					if (width<minResX || height<minResY) {
						alert(lang('js.admin.file_uploader.error_image_too_small').replaceAll('%1$d', minResX).replaceAll('%2$d', minResY));
						resetOptionalUrl(optionalUrlId, previewImageId, previewLinkId);
						resetFileInput($(fileInput));
					} else if (width>maxResX || height>maxResY) {
						alert(lang('js.admin.file_uploader.error_image_too_big').replaceAll('%1$d', maxResX).replaceAll('%2$d', maxResY));
						resetOptionalUrl(optionalUrlId, previewImageId, previewLinkId);
						resetFileInput($(fileInput));
					} else {
						$('#'+previewImageId).attr('src',e.target.result);
						if (previewLinkId) {
							$('#'+previewLinkId).attr('href',e.target.result);
						}
						if (optionalUrlId) {
							$('#'+optionalUrlId).val('');
						}
						$('label[for="'+fileInput.id+'"]').removeClass("btn-warning");
						$('label[for="'+fileInput.id+'"]').removeClass("btn-secondary");
						$('label[for="'+fileInput.id+'"]').removeClass("btn-primary");
						$('label[for="'+fileInput.id+'"]').addClass("btn-success");
						$('label[for="'+fileInput.id+'"]').html('<span class="fa fa-check pe-2"></span>'+lang('js.admin.generic.will_upload'));
					}
					//This is only done for the version page, but it's harmless anywhere else since the elements don't exist
					syncDivisionImages();
				}
			};
			reader.readAsDataURL(fileInput.files[0]);
			return;
		}
	}
	
	//Non-success cases: reset input
	resetOptionalUrl(optionalUrlId, previewImageId, previewLinkId);
	resetFileInput($(fileInput));
	
	//This is only done for the version page, but it's harmless anywhere else since the elements don't exist
	syncDivisionImages();
}

function syncDivisionImages() {
	var divisionCovers = $('[id^=form-division_cover_][id$=_preview]');
	var divisionCoverFiles = $('[id^=form-division_cover_]:not([id$=_preview])');
	
	for (var i=0;i<divisionCovers.length;i++){
		if ($(divisionCoverFiles[i]).val()=='' && !$(divisionCovers[i]).attr('data-original')) {
			$(divisionCovers[i]).attr('src', $('#form-image-preview').attr('src'));
		}
	}
}

function resetOptionalUrl(optionalUrlId, previewImageId, previewLinkId) {
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
}

function resetFileInput(fileInput) {
	fileInput.val('');
	$('label[for="'+fileInput.attr('id')+'"]').removeClass("btn-warning");
	$('label[for="'+fileInput.attr('id')+'"]').removeClass("btn-primary");
	$('label[for="'+fileInput.attr('id')+'"]').removeClass("btn-success");
	$('label[for="'+fileInput.attr('id')+'"]').addClass("btn-secondary");
	$('label[for="'+fileInput.attr('id')+'"]').html('<span class="fa fa-times pe-2"></span>'+lang('js.admin.generic.no_changes'));
}

function generateStorageFolder() {
	if ($('#form-fansub-1').val()>0) {
		$('#form-downloads_url_1').prop("disabled", false);
	} else {
		$('#form-downloads_url_1').prop("disabled", true);
	}
	if ($('#form-fansub-2').val()>0) {
		$('#form-downloads_url_2').prop("disabled", false);
	} else {
		$('#form-downloads_url_2').prop("disabled", true);
	}
	if ($('#form-fansub-3').val()>0) {
		$('#form-downloads_url_3').prop("disabled", false);
	} else {
		$('#form-downloads_url_3').prop("disabled", true);
	}

	if ($('#form-storage_folder').length==0 || $('#form-storage_folder').attr('data-is-set')) {
		return;
	}
	
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
	string+=$('#form-title-with-autocomplete').val().replaceAll('/','-').replaceAll(':',' -').replaceAll('?','').replaceAll('*','').replaceAll('♡',' ').replaceAll(';',' ').replaceAll('★',' ').replaceAll('"','');
	string = string.replaceAll('/.','/');
	if (string.startsWith('.')) {
		string = string.substring(1, string.length);
	}
	if (string.endsWith('.')) {
		string = string.substring(0, string.length-1);
	}
	$('#form-storage_folder').val(string);
}

function checkAnimeGenres(currentElement, currentMalIdElement, currentGenres) {
	if (currentElement==0 && currentMalIdElement==0) {
		$('#output').text(lang('js.admin.maintenance.check_anime.start'));
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
						$('#output').append("<br />"+lang('js.admin.maintenance.check_anime.missing_gender').replaceAll('%1$s', animes[currentElement].name).replaceAll('%2$s', getAnimeGenreName(difference[i])));
					}

					difference = $(animes[currentElement].genres).not(currentGenres).get();
					for (var i = 0; i < difference.length; i++) {
						$('#output').append("<br />"+lang('js.admin.maintenance.check_anime.extra_gender').replaceAll('%1$s', animes[currentElement].name).replaceAll('%2$s', getAnimeGenreName(difference[i])));
					}
		
					if (currentElement<animes.length-1) {
						setTimeout(function() {
							checkAnimeGenres(currentElement+1, 0, []);
						}, 4000);
					} else {
						$('#output').append("<br />"+lang('js.admin.maintenance.process_finished'));
					}
				}
			} else {
				$('#output').append("<br />"+lang('js.admin.maintenance.check_anime.could_not_get').replaceAll('%s', animes[currentElement].name));
		
				if (currentElement<animes.length-1) {
					setTimeout(function() {
						checkAnimeGenres(currentElement+1, 0, []);
					}, 4000);
				} else {
					$('#output').append("<br />"+lang('js.admin.maintenance.process_finished'));
				}
			}
		}
	};
	xmlhttp.open("GET", url, true);
	xmlhttp.send();
}

function checkMangaGenres(currentElement, currentMalIdElement, currentGenres) {
	if (currentElement==0 && currentMalIdElement==0) {
		$('#output').text(lang('js.admin.maintenance.check_manga.start'));
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
						$('#output').append("<br />"+lang('js.admin.maintenance.check_manga.missing_gender').replaceAll('%1$s', mangas[currentElement].name).replaceAll('%2$s', getMangaGenreName(difference[i])));
					}

					difference = $(mangas[currentElement].genres).not(currentGenres).get();
					for (var i = 0; i < difference.length; i++) {
						$('#output').append("<br />"+lang('js.admin.maintenance.check_manga.extra_gender').replaceAll('%1$s', mangas[currentElement].name).replaceAll('%2$s', getMangaGenreName(difference[i])));
					}
		
					if (currentElement<mangas.length-1) {
						setTimeout(function() {
							checkMangaGenres(currentElement+1, 0, []);
						}, 4000);
					} else {
						$('#output').append("<br />"+lang('js.admin.maintenance.process_finished'));
					}
				}
			} else {
				$('#output').append("<br />"+lang('js.admin.maintenance.check_manga.could_not_get').replaceAll('%s', mangas[currentElement].name));
		
				if (currentElement<mangas.length-1) {
					setTimeout(function() {
						checkMangaGenres(currentElement+1, 0, []);
					}, 4000);
				} else {
					$('#output').append("<br />"+lang('js.admin.maintenance.process_finished'));
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
	return lang('js.admin.maintenance.check_anime.unknown_gender').replaceAll('%d', id);
}

function getMangaGenreName(id) {
	for (var i = 0; i < mangaGenres.length; i++) {
		if (mangaGenres[i].mal_id==id) {
			return mangaGenres[i].name;
		}
	}
	return lang('js.admin.maintenance.check_manga.unknown_gender').replaceAll('%d', id);
}

function showAnimeWithNoMal() {
	$('#output').text(lang('js.admin.maintenance.animes_with_no_mal'));
	for (var i = 0; i < noMalAnime.length; i++) {
		$('#output').append("<br />«"+noMalAnime[i]+"»");
	}
}

function showMangaWithNoMal() {
	$('#output').text(lang('js.admin.maintenance.mangas_with_no_mal'));
	for (var i = 0; i < noMalManga.length; i++) {
		$('#output').append("<br />«"+noMalManga[i]+"»");
	}
}

function showLiveActionWithNoMdl() {
	$('#output').text(lang('js.admin.maintenance.liveaction_with_no_mdl'));
	for (var i = 0; i < noMdlLiveAction.length; i++) {
		$('#output').append("<br />«"+noMdlLiveAction[i]+"»");
	}
}

function recalculateVersionSlug() {
	var fansub1 = $('#form-fansub-1').find(":selected").attr('data-slug');
	var fansub2 = $('#form-fansub-2').find(":selected").attr('data-slug');
	var fansub3 = $('#form-fansub-3').find(":selected").attr('data-slug');
	
	var fansubs = [];
	if (fansub1) {
		fansubs.push(fansub1);
	}
	if (fansub2) {
		fansubs.push(fansub2);
	}
	if (fansub3) {
		fansubs.push(fansub3);
	}
	
	fansubs.sort();
	
	$("#form-slug").val(string_to_slug($("#form-title-with-autocomplete").val())+'/'+fansubs.join('+'));
}

function toggleWelcomeView() {
	$('#welcome-refresh').toggleClass('d-none');
	$('#welcome-view').toggleClass('d-none');
	$('#latest-view').toggleClass('d-none');
	$.post("save_user_default_view.php", {view: ($('#welcome-view').hasClass('d-none') ? 2 : 1)});
}

var malData;
var malDataStaff;
var malDataDivisionsEpisodes;
var malDataEpisodes;
var malDataMessages;
var uncompressReady = false;
var synopsisChanged = false;

$(document).ready(function() {
	loadArchiveFormats(['rar', 'zip'], function() {
		uncompressReady = true;
	});

	var genericModal = document.getElementById('generic-modal');
	genericModal.addEventListener('show.bs.modal', function (event) {
		// Button that triggered the modal
		var button = event.relatedTarget;
		// Extract info from data-bs-* attributes
		var title = button.getAttribute('data-bs-title');
		var contents = button.getAttribute('data-bs-contents');
		// Update the modal's content.
		var modalTitle = genericModal.querySelector('.modal-title');
		var modalBody = genericModal.querySelector('.modal-body');

		modalTitle.textContent = title;
		modalBody.innerHTML = contents.replaceAll('\\n','<br>');
	});
	
	$('#add-related-series-modal').on('show.bs.modal', function () {
		$('#add-related-series-results').html('');
		$('#add-related-series-query').val('');
	})
	
	$('#add-related-series-modal').on('shown.bs.modal', function () {
		$('#add-related-series-query').focus();
	})

	if ($('#form-fansub-1').length==1) {
		$('#form-fansub-1').on('change', generateStorageFolder);
		$('#form-fansub-2').on('change', generateStorageFolder);
		$('#form-fansub-3').on('change', generateStorageFolder);
		$('#form-fansub-1').on('change', recalculateVersionSlug);
		$('#form-fansub-2').on('change', recalculateVersionSlug);
		$('#form-fansub-3').on('change', recalculateVersionSlug);
		generateStorageFolder();
		if ($('#form-slug').val()=='') {
			recalculateVersionSlug();
		}
		syncDivisionImages();
		
		var addEpisodeFromVersionModal = document.getElementById('add-episode-from-version-modal');
		addEpisodeFromVersionModal.addEventListener('show.bs.modal', function (event) {
			var lastDivision = $('[id^=form-division-title-]').last().attr('id');
			var parts = lastDivision.split("-");
			var lastDivisionId = parts[parts.length - 1];
			var lastEpisodeNumber = 0;
			var episodes = $(".episode-title-input-numbered");
			for(var i=0;i<episodes.length;i++) {
				if (parseInt($(episodes[i]).attr('data-episode-number'))>lastEpisodeNumber) {
					lastEpisodeNumber = parseInt($(episodes[i]).attr('data-episode-number'));
				}
			}
			lastEpisodeNumber++;
			$('#add-episode-from-version-division-id').val(lastDivisionId);
			$('#add-episode-from-version-number').val(lastEpisodeNumber);
		});
	}

	$("#form-name-with-autocomplete").on('input', function() {
		if ($('#type').val()!='manga' && $("#form-name-with-autocomplete").attr('data-old-value')==$("#form-division-list-name-1").val()) {
			$("#form-division-list-name-1").val($("#form-name-with-autocomplete").val());
		}
		$("#form-name-with-autocomplete").attr('data-old-value', $("#form-name-with-autocomplete").val());
	});

	$("#form-title-with-autocomplete").on('input', function() {
		generateStorageFolder();
		recalculateVersionSlug();
		
		if ($("#form-title-with-autocomplete").attr('data-old-value')==$($('[id^=form-division-title-]')[0]).val()) {
			$($('[id^=form-division-title-]')[0])
			$($('[id^=form-division-title-]')[0]).val($("#form-title-with-autocomplete").val());
		}
		$("#form-title-with-autocomplete").attr('data-old-value', $("#form-title-with-autocomplete").val());
	});

	$("#import-from-mal").click(function() {
		if ($("#form-external_id").val()=='') {
			if ($('#type').val()=='manga') {
				var result = prompt(lang('js.admin.series_edit.add_from_mal_manga_prompt'));
				if (!result) {
					return;
				} else if (result.match(/https?:\/\/.*myanimelist.net\/manga\/(\d+)\//i)) {
					$("#form-external_id").val(result.match(/https?:\/\/.*myanimelist.net\/manga\/(\d+)\//i)[1]);
				} else {
					alert(lang('js.admin.series_edit.add_from_mal_mdl_invalid_url'));
					return;
				}
			} else if ($('#type').val()=='liveaction') {
				var result = prompt(lang('js.admin.series_edit.add_from_mdl_liveaction_prompt'));
				if (!result) {
					return;
				} else if (result.match(/https?:\/\/.*mydramalist.com\/(\d+.*)/i)) {
					$("#form-external_id").val(result.match(/https?:\/\/.*mydramalist.com\/(\d+.*)/i)[1]);
				} else {
					alert(lang('js.admin.series_edit.add_from_mal_mdl_invalid_url'));
					return;
				}
			} else {
				var result = prompt(lang('js.admin.series_edit.add_from_mal_anime_prompt'));
				if (!result) {
					return;
				} else if (result.match(/https?:\/\/.*myanimelist.net\/anime\/(\d+)\//i)) {
					$("#form-external_id").val(result.match(/https?:\/\/.*myanimelist.net\/anime\/(\d+)\//i)[1]);
				} else {
					alert(lang('js.admin.series_edit.add_from_mal_mdl_invalid_url'));
					return;
				}
			}
		}
		if ((($('#form-series').length>0 && $("#form-synopsis").val()!='') || ($('#form-series').length==0 && $("#form-name-with-autocomplete").val()!='')) && !confirm(lang('js.admin.series_edit.warning.overwrite_data'))) {
			return;
		}
		$("#import-from-mal").prop('disabled', true);
		$("#import-from-mal-episodes").prop('disabled', true);
		$("#import-from-mal-loading").removeClass("d-none");
		$("#import-from-mal-not-loading").addClass("d-none");
		var xmlhttp = new XMLHttpRequest();
		var url;
		if ($('#type').val()=='manga') {
			url = "https://api.jikan.moe/v4/manga/"+$("#form-external_id").val();
		} else if ($('#type').val()=='liveaction') {
			url = "/get_mdl_response.php?id="+$("#form-external_id").val();
		} else {
			url = "https://api.jikan.moe/v4/anime/"+$("#form-external_id").val();
		}

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
				} else if ($('#type').val()=='liveaction') {
					populateMdlData(malData);
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
								alert(lang('js.admin.series_edit.error.mal_mdl_generic_error'));
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
				alert(lang('js.admin.series_edit.error.mal_mdl_generic_error'));
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

	$("#generate-episodes").click(function() {
		if ($("#generate-episodes").hasClass('disabled')) {
			if ($('#type').val()=='manga') {
				alert(lang('js.admin.series_edit.error.cannot_generate_episodes.manga'));
			} else if ($('#type').val()=='liveaction') {
				alert(lang('js.admin.series_edit.error.cannot_generate_episodes.liveaction'));
			} else {
				alert(lang('js.admin.series_edit.error.cannot_generate_episodes.anime'));
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
			alert(lang('js.admin.series_edit.error.please_input_number_of_episodes'));
			return;
		}

		if ((parseInt($('#episode-list-table').attr('data-count'))>1 || $('#form-episode-list-description-1').val()!='') && !confirm(lang('js.admin.series_edit.warning.recreate_existing_episodes'))) {
			return;
		}

		var restart = (divisions.length==1 || confirm(lang('js.admin.series_edit.prompt.restart_numbering_at_new_division')));

		var i = parseInt($('#episode-list-table').attr('data-count'));
		for (var id=1;id<i+1;id++) {
			$("#form-episode-list-row-"+id).remove();
		}
		$('#episode-list-table').attr('data-count', 0);

		var rowNumber=1;

		for (var i=0;i<divisions.length;i++) {
			for (var j=0;j<$(divisions[i]).val();j++) {
				addEpisodeRow(false, false);
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
			alert(lang('js.admin.version_edit.error.mega_import_must_setup_folder'));
			return;
		}

		$("#import-from-mega-loading").removeClass("d-none");
		$("#import-from-mega-not-loading").addClass("d-none");
		$("#import-from-mega").prop('disabled', true);
		$('#import-failed-results-table tbody').empty();
		$('#import-failed-results').addClass('d-none');

		var account_ids = [];
		var folders = [];
		var default_resolutions = [];
		var default_durations = [];
		var division_ids = [];
		for (var i=1;i<=count;i++){
			if ($('#import-type').val()!='sync' || $('#form-remote_folders-list-is_active-'+i).prop('checked')) {
				account_ids.push(encodeURIComponent($('#form-remote_folders-list-remote_account_id-'+i).val()));
				folders.push(encodeURIComponent($('#form-remote_folders-list-folder-'+i).val()));
				default_resolutions.push(encodeURIComponent($('#form-remote_folders-list-default_resolution-'+i).val()));
				default_durations.push(encodeURIComponent($('#form-remote_folders-list-default_duration-'+i).val()));
				division_ids.push(encodeURIComponent($('#form-remote_folders-list-division_id-'+i).val()!='' ? $('#form-remote_folders-list-division_id-'+i).val() : -1));
			}
		}

		var xmlhttp = new XMLHttpRequest();
		var url = "fetch_storage_links.php?series_id="+$('[name="series_id"]').val()+"&import_type="+$('#import-type').val()+"&remote_account_ids[]="+account_ids.join("&remote_account_ids[]=")+"&remote_folders[]="+folders.join("&remote_folders[]=")+"&default_resolutions[]="+default_resolutions.join("&default_resolutions[]=")+"&default_durations[]="+default_durations.join("&default_durations[]=")+"&division_ids[]="+division_ids.join("&division_ids[]=");

		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var data = JSON.parse(this.responseText);
				if (data.status=='ko') {
					alert(lang('js.admin.version_edit.error.mega_import_server_error').replaceAll('%s', data.error));
				} else {
					var moreThanOne = false;
					for (var i = 0; i < data.results.length; i++) {
						var splitted = data.results[i].link.split('/');
						start = splitted[0]+"//"+splitted[2]+"/";
						var found = false;
						$("[id^=form-files-list-"+data.results[i].id+"-file-][id$=url]").each(function (pos,e) {
							//If a file exists with this storage method, replace it
							if ($(e).val().startsWith(start)) {
								if (found) {
									moreThanOne=true;
									return;
								}
								if ($(e).parent().parent().find("[id$=resolution]").val()=='') {
									$(e).parent().parent().find("[id$=resolution]").val(data.results[i].resolution);
								}
								if ($(e).parent().parent().parent().parent().parent().parent().find("[id$=length-1]").val()=='') {
									$(e).parent().parent().parent().parent().parent().parent().find("[id$=length-1]").val(data.results[i].duration);
								}
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
								//If a file exists with an empty value, replace it
								if ($(e).val()=='') {
									$(e).parent().parent().find("[id$=resolution]").val(data.results[i].resolution);
									$(e).parent().parent().parent().parent().parent().parent().find("[id$=length-1]").val(data.results[i].duration);
									$(e).val(data.results[i].link);
									$(e).attr('value', data.results[i].link);
									found = true;
								}
							});
							if (!found) {
								//If NO file exists with this storage method, add a new link and re-scan to find it
								addLinkRow(data.results[i].id,1);
								$("[id^=form-files-list-"+data.results[i].id+"-file-][id$=url]").each(function (pos,e) {
									if (found) {
										return;
									}
									if ($(e).val()=='') {
										$(e).parent().parent().find("[id$=resolution]").val(data.results[i].resolution);
										$(e).parent().parent().parent().parent().parent().parent().find("[id$=length-1]").val(data.results[i].duration);
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
							$('#import-failed-results-table').append('<tr><td>'+data.unmatched_results[i].file+'</td><td>'+data.unmatched_results[i].link+'</td><td title="'+data.unmatched_results[i].reason_description+'" style="white-space: nowrap;">'+data.unmatched_results[i].reason+'<span class="fa fa-question-circle ms-1"></span></td></tr>');
						}
					}

					if (moreThanOne) {
						alert(lang('js.admin.version_edit.warning.mega_import_multiple_links'));
					}
				}
				$("#import-from-mega-loading").addClass("d-none");
				$("#import-from-mega-not-loading").removeClass("d-none");
				$("#import-from-mega").prop('disabled', false);
			} else if (this.readyState == 4) {
				alert(lang('js.admin.version_edit.error.mega_import_generic_error'));
				$("#import-from-mega-loading").addClass("d-none");
				$("#import-from-mega-not-loading").removeClass("d-none");
				$("#import-from-mega").prop('disabled', false);
			}
		};
		xmlhttp.open("GET", url, true);
		xmlhttp.send();
	});
	$("#form-licensed_status").change(function() {
		if ($(this).val()==1) {
			alert(lang('js.admin.series_edit.warning.licensed_parts_selected'));
		}
	});
	$('#form-reader_type').change(function() {
		if ($(this).val()=='strip') {
			alert(lang('js.admin.series_edit.warning.long_strip_selected'));
		}
		else if ($(this).val()=='ltr' && $('#form-comic_type').val()=='manga') {
			alert(lang('js.admin.series_edit.warning.ltr_selected_for_manga'));
		}
		else if ($(this).val()=='rtl' && $('#form-comic_type').val()=='novel') {
			alert(lang('js.admin.series_edit.warning.rtl_selected_for_light_novel'));
		}
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
	$("#form-show_episode_numbers").change(function() {
		if ($(this).val()==1) {
			$("#warning-no-numbers").addClass('d-none');
			var episodes = $(".episode-title-input-numbered");
			for(var i=0;i<episodes.length;i++) {
				$(episodes[i]).attr('placeholder', lang('js.admin.generic.episode_prefix')+$(episodes[i]).attr('data-episode-number'));
			}
		} else {
			$("#warning-no-numbers").removeClass('d-none');
			$(".episode-title-input-numbered").attr('placeholder', lang('js.admin.version_edit.episode.title_placeholder'));
		}
	});
	
	if ($('#id').val()) {
		synopsisChanged=true;
	}
});
