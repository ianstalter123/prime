$(function() {
	$('#addSilo-label').hide();
	loadSculptingData();
	$(document).on('change', '.silo-select', function() {
		var pid = $(this).attr('id');
		var sid = $(this).val();
		showSpinner();
		$.post($('#website_url').val() + 'backend/backend_seo/addsilotopage/', {
			pid : pid,
			sid : sid
		}, function(response) {
			hideSpinner();
			showMessage((typeof response.responseText != 'undefined') ? response.responseText : 'Added');
		});
	});

	$(document).on('click', '.silo-this-cat', function() {
		var cid    = $(this).val();
		var actUrl = '';
		showSpinner();
		if($(this).prop('checked')) {
			actUrl = $('#website_url').val() + 'backend/backend_seo/silocat/act/add/';
		}
		else {
			actUrl = $('#website_url').val() + 'backend/backend_seo/silocat/act/remove/'
		}
		$.post(actUrl, {
			cid : cid
		}, function() {
			//$('#ajax_msg').text('Done').fadeOut();
			loadSculptingData();
		});
	})
});

sculptingCallback = function() {
	$('#silo-name').val('');
	loadSculptingData();
};

loadSculptingData = function() {
	//$('#sculpting-list').addClass('ajaxspineer');
	showSpinner();
	$.getJSON($('#website_url').val() + 'backend/backend_seo/loadsculptingdata', function(response) {
		hideSpinner();
		$('#sculpting-list').html(response.sculptingList);
	})
};