$(function() {
    $("body").on('click', '.poster_send', function() {
        var networks = [];
        $.each($('.poster_network'), function(k, v) {
            if(this.checked == true){
                networks.push(v.value);
            }
        });
        var postLinkNeeded = ['facebook', 'linkedin'];
        if(networks.length == 0) { showMessage('Choose a social network to post.', 1); return false;}
        for(var i=0; i<= postLinkNeeded.length; i++) {
            if($.inArray(postLinkNeeded[i], networks) != -1) {
                if($('.poster_link').val() == '') {
                    hideSpinner();
                    showMessage('When post to ' + postLinkNeeded[i].substring(0,1).toUpperCase()+postLinkNeeded[i].substring(1,postLinkNeeded[i].length) + ' "post link" field cannot be empty', 1);
                    return false;
                }
            }
        }
        showSpinner();
        $.post($('#website_url').val() + 'plugin/socialposter/run/postMessage/',
            {
             'post_description': $('.poster_title').val(),
             'post_link': $('.poster_link').val(),
             'post_message': $('#poster_message').val(),
             'networks': networks
            }, 
            function(resp) {
                hideSpinner();
                showMessage(resp.responseText, resp.error, 2500);
            }
        , 'json');
    });
    
});