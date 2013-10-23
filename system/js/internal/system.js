$(function() {

    var currentUrl = decodeURI(window.location.href);
    if (currentUrl && typeof currentUrl != 'undefined') {
        $("a[href='" + currentUrl + "']").addClass('current');
        if (currentUrl == $('#website_url').val()){
            $("a[href='" +  $('#website_url').val() + "index.html']").addClass('current');
        }
    }

	/**
	 * Seotoaster popup dialog
	 */
	$(document).on('click', 'a.tpopup', function(e) {
		if(!loginCheck()) {
			return;
		}
		e.preventDefault();
		var link    = $(this);
		var pwidth  = link.data('pwidth') || 960;
		var pheight = link.data('pheight') || 580;
		var popup = $(document.createElement('iframe')).attr({'scrolling' : 'no', 'frameborder' : 'no', 'allowTransparency' : 'allowTransparency', 'id' : 'toasterPopup'}).addClass('__tpopup rounded3px');
		popup.parent().css({background: 'none'});

		popup.dialog({
			width: pwidth,
			height: pheight,
			resizable : false,
			draggable : true,
			modal: true,
			open: function() {
                this.onload = function(){
                    $(this).contents().find('.close, .save-and-close').on('click', function(){
                        var restored = localStorage.getItem(generateStorageKey());
                        if(restored !== null) {
                            showConfirm('Hey, you did not save your work? Are you sure you want discard all changes?', function() {
                                localStorage.removeItem(generateStorageKey());
                                closePopup(popup);
                            });
                        } else {
                            closePopup(popup);
                        }
                    });
                }
				$(this).attr('src', link.data('url')).css({
						width    : pwidth + 'px',
						height   : pheight + 'px',
						padding  : '0px',
						margin   : '0px',
						overflow : 'hidden'
				});
				$('.ui-dialog-titlebar').remove();
			},
			close: function() {
				$(this).remove();
			}
		});
	});


	//seotoaster delete item link
	$(document).on('click', 'a._tdelete', function() {
		var url      = $(this).attr('href');
		var callback = $(this).data('callback');
		var elId     =  $(this).data('eid');
		if((typeof url == 'undefined') || !url || url == 'javascript:;') {
			url = $(this).data('url');
		}
		smoke.confirm('You are about to remove an item. Are you sure?', function(e) {
			if(e) {
				$.post(url, {id : elId}, function(response) {
					var responseText = (response.hasOwnProperty(responseText)) ? response.responseText : 'Removed.';
					showMessage(responseText, (!(typeof response.error == 'undefined' || !response.error)));
                    if(typeof callback != 'undefined') {
						eval(callback + '()');
					}
				})
			} else {
				$('.smoke-base').remove();
			}
		}, {classname:"errors", 'ok':'Yes', 'cancel':'No'});
	});

	//seotoaster ajax form submiting
	$(document).on('submit', 'form._fajax', function(e) {
		e.preventDefault();
		var donotCleanInputs = [
			'#h1',
			'#header-title',
			'#url',
			'#nav-name',
			'#meta-description',
			'#meta-keywords',
			'#teaser-text'
		];
		var form        = $(this);
        var callback    = $(form).data('callback');
		$.ajax({
			url        : form.attr('action'),
			type       : 'post',
			dataType   : 'json',
			data       : form.serialize(),
			beforeSend : function() {
				showSpinner();
			},
			success : function(response) {
				if(!response.error) {
					if(form.hasClass('_reload')) {
						if(typeof response.responseText.redirectTo != 'undefined') {
							top.location.href = $('#website_url').val() + response.responseText.redirectTo;
							return;
						}
						top.location.reload();
						return;
					}
					//processing callback
					if(typeof callback != 'undefined' && callback != null) {
						eval(callback + '()');
					}
					hideSpinner();
					showMessage(response.responseText);
				}
				else {
					if(!$(form).data('norefresh')) {
						$(form).find('input:text').not(donotCleanInputs.join(',')).val('');
					}
					hideSpinner();
					smoke.alert(response.responseText, {classname:"errors"}, function() {
                        if(typeof callback != 'undefined' && callback != null) {
                            eval(callback + '()');
                        }
                    });
				}
			},
			error: function(err) {
				$('.smoke-base').remove();
				showMessage('Oops! sorry but something fishy is going on - try again or call for support.', true);
			}
		})
	})

	//seotoaster edit item link
	$(document).on('click', 'a._tedit', function(e) {
		e.preventDefault();
		var handleUrl = $(this).data('url');
		if(!handleUrl || handleUrl == 'undefined') {
			handleUrl = $(this).attr('href');
		}
		var eid = $(this).data('eid');
		$.post(handleUrl, {id: eid}, function(response) {
			//console.log(response.responseText.data);
			var formToLoad = $('#' + response.responseText.formId);
			for(var i in response.responseText.data) {
				$('[name=' + i + ']').val(response.responseText.data[i]);
				if(i == 'password') {
					$('[name=' + i + ']').val('');
				}
			}
		})

	});
	//seotoaster gallery links
	if(jQuery.fancybox) {
		$('a._lbox').fancybox({
            'transitionIn'		: 'none',
            'transitionOut'		: 'none',
            'titlePosition' 	: 'over'
        });
	}
	//publishPages();

});


function loginCheck() {
	if($.cookie('PHPSESSID') === null) {
		showModalMessage('Session expired', 'Your session is expired! Please, login again', function() {
			top.location.href = $('#website_url').val();
		})
		return false;
	}
	return true;
}

function showMessage(msg, err, delay) {
	if(err) {
		smoke.alert(msg, {classname:"errors"});
		return;
	}
	smoke.signal(msg);
    delay = (typeof(delay) == 'undefined') ? 1300 : delay;
	$('.smoke-base').delay(delay).slideUp();
}

function showConfirm(msg, yesCallback, noCallback) {
	smoke.confirm(msg, function(e) {
		if(e) {
			if(typeof yesCallback != 'undefined') {
				yesCallback();
			}
		} else {
		    if(typeof noCallback != 'undefined') {
			    noCallback();
		    }
		}
	}, {classname : 'errors', ok : 'Yes', cancel : 'No'});
}

function showSpinner() {
	smoke.signal('<img src="' + $('#website_url').val() + 'system/images/loading.gif" alt="working..." />', 30000);
}

function hideSpinner() {
	$('.smoke-base').hide();
}

function publishPages() {
	if(!top.$('#__tpopup').length) {
		$.get($('#website_url').val() + 'backend/backend_page/publishpages/');
	}
}

function closePopup(frame) {
    if(frame.contents().find('div.seotoaster').hasClass('refreshOnClose')) {
		window.parent.location.reload();
	}

    if(typeof frame.dialog != 'undefined') {
        frame.dialog('close');
	} else {
		console.log('Alarm! Something went wrong!');
	}
}

function generateStorageKey() {
	if($('#frm_content').length) {
		var actionUrlComponents = $('#frm_content').prop('action').split('/');
		return actionUrlComponents[5] + actionUrlComponents[7] + (typeof actionUrlComponents[9] == 'undefined' ? $('#page_id').val() : actionUrlComponents[9]);
	}
	return null;
}

function showMailMessageEdit(trigger, callback) {
    $.getJSON($('#website_url').val() + 'backend/backend_config/mailmessage/', {
        'trigger' : trigger
    }, function(response) {
        $(msgEditScreen).remove();
        var msgEditScreen = $('<div class="msg-edit-screen"></div>').append($('<textarea id="trigger-msg"></textarea>').val(response.responseText).css({
            width  : '555px',
            height : '155px',
            resizable: "none"
        }));
        $('#trigger-msg').val(response.responseText);
        msgEditScreen.dialog({
            modal: true,
            title: 'Edit mail message before sending',
            width: 600,
            height: 300,
            resizable: false,
            show: 'clip',
            hide: 'clip',
            draggable: false,
            buttons: [
                {
                    text: "Okay",
                    click: function(e) {
                        msgEditScreen.dialog('close');
                        callback($('#trigger-msg').val());
                    }
                }
            ]
        }).parent().css({
            background: '#DAE8ED'
        });
    }, 'json');
}
