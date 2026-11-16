(function ($) {
	'use strict';

	// Repeaters (departures, itinerary, FAQs)
	$(document).on('click', '.kv-add-row', function () {
		var box = $(this).closest('.kv-repeater');
		var tpl = box.find('.kv-row-tpl').html();
		box.find('.kv-rows').append(tpl.replace(/__i__/g, 'n' + Date.now()));
	});
	$(document).on('click', '.kv-remove-row', function () {
		$(this).closest('.kv-row').remove();
	});

	// Single image pickers (settings page)
	$(document).on('click', '.kv-media-pick', function (e) {
		e.preventDefault();
		var wrap = $(this).closest('.kv-media');
		var frame = wp.media({ title: 'Choose image', multiple: false, library: { type: 'image' } });
		frame.on('select', function () {
			var a = frame.state().get('selection').first().toJSON();
			wrap.find('input[type=hidden]').val(a.id);
			var url = (a.sizes && a.sizes.medium) ? a.sizes.medium.url : a.url;
			wrap.find('.kv-media-prev').html('<img src="' + url + '" alt="">');
		});
		frame.open();
	});
	$(document).on('click', '.kv-media-clear', function (e) {
		e.preventDefault();
		var wrap = $(this).closest('.kv-media');
		wrap.find('input[type=hidden]').val('');
		wrap.find('.kv-media-prev').empty();
	});

	// Tour gallery picker (multiple)
	$(document).on('click', '.kv-gallery-pick', function (e) {
		e.preventDefault();
		var wrap = $(this).closest('.kv-gallery-wrap');
		var frame = wp.media({ title: 'Select gallery images', multiple: 'add', library: { type: 'image' } });
		frame.on('select', function () {
			var ids = [], html = '';
			frame.state().get('selection').each(function (m) {
				var a = m.toJSON();
				ids.push(a.id);
				var url = (a.sizes && a.sizes.thumbnail) ? a.sizes.thumbnail.url : a.url;
				html += '<img src="' + url + '" alt="">';
			});
			wrap.find('.kv-gallery-ids').val(ids.join(','));
			wrap.find('.kv-gallery-preview').html(html);
		});
		frame.open();
	});
	$(document).on('click', '.kv-gallery-clear', function (e) {
		e.preventDefault();
		var wrap = $(this).closest('.kv-gallery-wrap');
		wrap.find('.kv-gallery-ids').val('');
		wrap.find('.kv-gallery-preview').empty();
	});
})(jQuery);
