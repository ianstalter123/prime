window.onload = function() {
	var icons = {
			'icon-home' : '&#xe000;',
			'icon-location' : '&#xe001;',
			'icon-mail' : '&#xe002;',
			'icon-facebook' : '&#xe003;',
			'icon-twitter' : '&#xe004;',
			'icon-google-plus' : '&#xe005;',
			'icon-grid' : '&#xe006;',
			'icon-list' : '&#xe007;'
		};
	$('[class^="icon-"], [class*=" icon-"]').each(function(index, element) {
		var code = $(this).attr('class').match(/icon-[^\s'"]+/);
		$(this).prepend('<span class="icon-toaster">'+icons[code[0]]+'</span>');
	});
	$('select').each(function() {
		$(this).css('width', $(this).width()+24 );
	});
};