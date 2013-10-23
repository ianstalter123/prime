$(function() {
    $(document).on('click', 'a.netcontent-list-link', function() {
        var tabs        = $('#tabs');
        var widgetsList = $('#list-of-widgets');
        if(tabs.hasClass('grid_9')) {
            tabs.switchClass('grid_9', 'grid_4').tabs('select', 0);
            $('.above-editor-links').switchClass('grid_5', 'grid_8');
            $('tr.mceFirst').show();
        }
        if(!widgetsList.length) {
            getNetContentList();
            $('.netcontent-list-link').text('HIDE NETCONTENT');
            $('<div>').attr('id','list-of-widgets').addClass('grid_8').css({'marginTop':'41px'}).text('Loading...').insertAfter('#links');
        } else {
            widgetsList.remove();
            $('.netcontent-list-link').text('NETCONTENT');
        }
    })

    $('.ui-tabs-nav-item a').click(function() {
        var children = $(this).find('span#products');
        var widgetsList = $('#list-of-widgets');
        if(!children.length) {
            widgetsList.switchClass('grid_3', 'grid_8');
        } else {
            widgetsList.switchClass('grid_8', 'grid_3');
        }
    })


    $(document).on('click', 'li.widget-list-item', function() {
        $('textarea.tinymce').tinymce().execCommand('mceInsertContent',false, '{$plugin:netcontent:' + $(this).data('netcontentName') + '}');
        $('a.netcontent-list-link').click();
    });

});



function getNetContentList() {
    $.ajax({
        type: 'GET',
	url: $('#website_url').val() + 'plugin/netcontent/run/widgetlist/',
	dataType: 'json',
	success: function (response) {
            if(!response.error) {
                $('#list-of-widgets').empty();
                getSync();
                $('<div>').attr('id', 'netcontent-hint-block').appendTo($('#list-of-widgets'));
                var netContentList = $('<ul>').addClass('netcontent-widget-list').appendTo($('#list-of-widgets'));
                $.each(response.responseText, function(){
                    var widget  = this;
                    var netItem = $('<li>').text(widget.widgetName).appendTo(netContentList);
                    var p2pState = (widget.p2p == true) ? ':p2p' :'';
                    $(netItem).data('netcontentName', widget.widgetName + p2pState);
                    (widget.publish == true) ? $(netItem).addClass('widget-list-item') : $(netItem).addClass('widget-list-item widget-list-item-empty') ;
                    var netContentHint = $('#netcontent-hint-block');
                    $(netItem).mouseover(function(){
                        netContentHint
                                        .empty()
                                        .append( $('<img id="tooltip-load" src="'+$('#website_url').val()+'plugins/netcontent/web/images/load.gif'+'" style="display:block; margin-left:20px;" width="50px" heigth="15px;" alt="load" />') )
                                        .stop(true, true).fadeIn(400)
                            .empty().html($('<div />').text(widget.content).text()); //.html(widget.content).text()
                    }).mousemove(function(e) {
                        var borderTop   = $(window).scrollTop();
                        var borderRight = $(window).width();
                        var leftPos     = 0;
                        var topPos      = 0;
                        var offset      = 30;
                        if(borderRight - (offset *2) >= netContentHint.width() + e.pageX) {
                            leftPos = e.pageX+offset;
                        }
                        else {
                            leftPos = borderRight - netContentHint.width() - offset;
                        }
                        if(borderTop + (offset * 2) >= e.pageY - netContentHint.height()){
                            topPos = borderTop + offset;
                        }
                        else {
                                topPos = e.pageY-netContentHint.height() - offset;
                        }
                        netContentHint.css({left : leftPos, top : topPos});
                    }).mouseout(function() {
                        netContentHint.empty();
                        netContentHint.stop(true, true).fadeOut(200);
                    });
                });
            }
            else {
                $('#list-of-widgets').empty();
                $('#list-of-widgets').css({'fontWeight': 'bold'}).text(response.responseText);
                if(typeof response.responseText.notConected == 'undefined' || !response.responseText.notConected) {
                    getSync();
                }
                else {
                    $('#list-of-widgets').empty().append($('<img class="connectImage" src="' + $('#website_url').val() + 'plugins/netcontent/web/images/sambaConnect.jpg">'));
                    $('img.connectImage').wrap($('<a>').attr({'href':'http://mojo.seosamba.com', 'target':'_blank'}));
                }
            }
	},
        error: function (response){
            $('#list-of-widgets').empty().html(response.responseText);
        }
    });
}

function getSync() {
    $('<a>').attr({'id': 'widgetSync'}).text('CHECK FOR UPDATES').appendTo($('#list-of-widgets')).css({'cursor':'pointer'});
    $('#widgetSync').click(function() {
        $('#list-of-widgets').empty().text('Loading...');
        $.getJSON($('#website_url').val() + 'plugin/netcontent/run/syncNetContent/', function(response) {
            if(response.error == true) {
                $('#list-of-widgets').empty().append($('<img class="connectImage" src="' + $('#website_url').val() + 'plugins/netcontent/web/images/sambaConnect.jpg">'));
                $('img.connectImage').wrap($('<a>').attr({'href':'http://mojo.seosamba.com', 'target':'_blank'}));
            }
            else {
                getNetContentList();
            }
        });
    });
}