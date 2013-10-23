var p2pWidgetFlag = null;
$(document).ready(function(){
    //editWidget();
    isStatic();
    $('#p2p-content').change(function() {
        if( $('#p2p-content').is(':checked') == true ) {
            $('#btn-submit').unbind('click');
            saveWidget();
        }
        else {
            if(p2pWidgetFlag == true) {
                $('#btn-submit').unbind('click');
                saveWidget();
            }
            else {
                $('#btn-submit').bind('click', function(){
                    $('#frm_content').submit();
                });
            }
        }
        
    });

});

function isStatic() {
    var type = null;
    $('#p2p-content').data('pageurl', parent.window.location.pathname.toLocaleString().substr(1));
    if($('#container_name').val() == '') {
        var formUrl = $('#frm_content').attr('action');
        var typeRegExp = /containerType\/\d{1}/i;
        var containerType = typeRegExp.exec(formUrl);
        if(containerType != null) {
            type = containerType[0].substr(containerType[0].lastIndexOf('/')+1, containerType[0].length);
        }
    }
    else {
        type = $('#container_type').val();
    }
    if(type == 2) {
        $('div.p2p-check').css('display','block');
    }

    $.getJSON($('#website_url').val() + 'plugin/netcontent/run/isP2p/',
        {'widgetName':getContainerName()},
    function(response) {
        if(response != null) {
            if(response.responseText.exist == true) {
                $('#p2p-content').attr('checked', 'checked');
                p2pWidgetFlag = true;
                $('#btn-submit').unbind('click');
                saveWidget();
            }
            else {
                
            }
        }
    });

}

function getContainerName() {
    var name =null;
    if($('#container_name').val() == '') {
        var formUrl = $('#frm_content').attr('action');
        var nameRegExp = /containerName\/[-_\d\w]+/;
        var containerName = nameRegExp.exec(formUrl);
        if(containerName != null) {
            name = containerName[0].substr(containerName[0].lastIndexOf('/')+1, containerName[0].length);
        }
    }
    else {
        name = $('#container_name').val();
    }
    return name;
}

function saveWidget() {
    $('#btn-submit').bind('click', function() {
        //var ajaxMsgSuccess = $('#ajax_msg');
        var widgetData = null;
        var actionUrl = '';
        if( $('#p2p-content').is(':checked') == true ) {
            actionUrl = $('#website_url').val() + 'plugin/netcontent/run/saveNetContent';
            widgetData = {'widgetName': getContainerName(), 'widgetContent': $('#content').val(), 'pageUrl': $('#p2p-content').data('pageurl')};
            p2pWidgetFlag = true;
        }
        else if( ($('#p2p-content').is(':checked') != true) && (p2pWidgetFlag == true) ) {
            actionUrl = $('#website_url').val() + 'plugin/netcontent/run/deleteNetContent';
            widgetData = {'widgetName': getContainerName()};
        }
            $.ajax({
            type: 'POST',
            url: actionUrl,
            dataType: 'json',
            data: widgetData,
            beforeSend : function() {
		//ajaxMsgSuccess.fadeIn().text('Working...');
                showSpinner();
            },
            success: function (answer) {
                if(answer.error == 0) {
                    $('#frm_content').submit();
                }
                else {

                }
            },
            error: function (){}
        });

        return false;
    });
}

/*function editWidget() {
    $('.edit-widget').click(function(){
        
    });
}*/