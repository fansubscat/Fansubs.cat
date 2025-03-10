(function($) {

'use strict';

$(function() {

	var type		= $('#flair_type'),
		color		= $('#flair_color'),
		icon		= $('#flair_icon'),
		iconColor	= $('#flair_icon_color'),
		fontColor	= $('#flair_font_color'),
		image		= $('#flair_img'),
		preview		= $('#flair_preview');

	var getColorValue = (function() {
		var colorRegEx = new RegExp('^[0-9A-F]{6}$');

		/**
		 * Get the value from an input field only if it is a valid color hex value. If the field is
		 * not empty and the value is invalid, this function will also apply the error class to the
		 * field.
		 *
		 * @param {JQuery}	field	The field from which to get the value
		 *
		 * @return {String} The value or an empty string if it is invalid
		 */
		return function(field) {
			var value = field.val();

			field.removeClass('error');

			if (value) {
				if (colorRegEx.test(value)) {
					return value;
				}

				field.addClass('error');
			}

			return '';
		};
	}());

	/**
	 * Get the HTML for a flair preview.
	 *
	 * @param {String}	colorVal		The background color
	 * @param {String}	iconVal			The icon name
	 * @param {String}	iconColorVal	The icon color
	 * @param {String}	fontColorVal	The font color
	 * @param {Boolean}	large			Get the larger preview
	 *
	 * @return {String} The HTML
	 */
	var getPreviewHtml = function(colorVal, iconVal, iconColorVal, fontColorVal, large) {
		var html = '<span class="flair-icon fa-stack';

		if (large) {
			html += ' fa-2x flair-lg';
		}

		html += '">';

		if (colorVal) {
			html += '<i class="fa fa-square fa-stack-2x" style="color: #' + colorVal + '" aria-hidden="true"></i>';

			if (iconVal) {
				html += '<i class="fa ' + iconVal + ' fa-stack-1x"';

				if (iconColorVal) {
					html += ' style="color: #' + iconColorVal + '"';
				}

				html += ' aria-hidden="true"></i>';
			}
		} else if (iconVal) {
			html += '<i class="fa ' + iconVal + ' fa-stack-2x"';

			if (iconColorVal) {
				html += ' style="color: #' + iconColorVal + '"';
			}

			html += ' aria-hidden="true"></i>';
		}

		if (fontColorVal) {
			html += '<b class="flair-count" style="color: #' + fontColorVal + '" aria-hidden="true">2</b>';
		}

		html += '</span>';

		return html;
	};

	/**
	 * Get the HTML for an image flair preview.
	 *
	 * @param {String}	imgVal			The image path
	 * @param {String}	fontColorVal	The font color
	 * @param {Boolean}	large			Get the larger preview
	 *
	 * @return {String} The HTML
	 */
	var getImgPreviewHtml = function(imgVal, fontColorVal, large) {
		var name = imgVal.substr(0, imgVal.lastIndexOf('.')),
			ext = imgVal.substr(imgVal.lastIndexOf('.')),
			svg = ext.toLowerCase() === '.svg',
			html = '<span class="flair-image';

		if (large) {
			html += ' flair-lg';
		}

		html += '">';

		if (fontColorVal) {
			html += '<b class="flair-count" style="color: #' + fontColorVal + '" aria-hidden="true">2</b>';
		}

		html += '<img src="' + flair.imgPath + name;

		if (large) {
			if (!svg) {
				html += '-x2';
			}
			html += ext + '" height="44"';
		} else {
			if (!svg) {
				html += '-x1';
			}
			html += ext + '" height="22"';
		}

		html += ' aria-hidden="true" /></span>';

		return html;
	}

	/**
	 * Update the flair preview based on the current values of the form fields.
	 */
	var updatePreview = function() {
		var html = [];

		if (type.val() === '0') {
			var colorVal		= getColorValue(color),
				iconVal			= icon.val(),
				iconColorVal	= getColorValue(iconColor),
				fontColorVal	= getColorValue(fontColor);

			if (!colorVal && !iconVal) {
				preview.html('');
				return;
			}

			html.push(getPreviewHtml(colorVal, iconVal, iconColorVal, false, true));
			html.push(getPreviewHtml(colorVal, iconVal, iconColorVal));

			if (fontColorVal) {
				html.push(getPreviewHtml(colorVal, iconVal, iconColorVal, fontColorVal, true));
				html.push(getPreviewHtml(colorVal, iconVal, iconColorVal, fontColorVal));
			}
		} else {
			var imgVal			= image.val(),
				fontColorVal	= getColorValue(fontColor);

			if (!imgVal) {
				preview.html('');
				return;
			}

			html.push(getImgPreviewHtml(imgVal, false, true));
			html.push(getImgPreviewHtml(imgVal));

			if (fontColorVal) {
				html.push(getImgPreviewHtml(imgVal, fontColorVal, true));
				html.push(getImgPreviewHtml(imgVal, fontColorVal));
			}
		}

		preview.html(html.join('&nbsp;'));
	};

	$('#color_palette_toggle1').click(function(e) {
		$('#color_palette_placeholder').toggle();
		e.preventDefault();
	});

	var palette2 = $('#color_palette_placeholder2');
	phpbb.registerPalette(palette2);
	$('#color_palette_toggle2').click(function(e) {
		palette2.toggle();
		e.preventDefault();
	});

	var palette3 = $('#color_palette_placeholder3');
	phpbb.registerPalette(palette3);
	$('#color_palette_toggle3').click(function(e) {
		palette3.toggle();
		e.preventDefault();
	});

	$('.colour-palette a').click(function() {
		var colorVal = $(this).data('color'),
			target = $($(this).parents('.color_palette_placeholder').data('target'));
		target.val(colorVal);
		updatePreview();
	});

	$('#flair_icon').change(function() {
		var value = $(this).val().trim();
		if (value !== '') {
			value = value.toLowerCase();
			if (value.substr(0, 3) !== 'fa-') {
				value = 'fa-' + value;
			}
			$(this).val(value);
		}
	});

	$('#flair_type, #flair_color, #flair_icon, #flair_icon_color, #flair_font_color, #flair_img, #flair_img_width, #flair_img_height').change(function() {
		updatePreview();
	});

	$('#flair_type').change(function() {
		if ($(this).val() === '0') {
			$('.type_img').hide();
			$('.type_fa').show();
		} else {
			$('.type_fa').hide();
			$('.type_img').show();
		}
	})
	.trigger('change');

});

}(jQuery));
