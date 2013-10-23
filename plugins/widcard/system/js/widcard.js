$(document).ready(function() {

    checkFields();
    $("#idCard input, #idCard textarea").keyup(function(){ //#idCard select,
        if(this.value == ''){
            $('.wic_'+this.name).css({'display':'none'});
        }
        else {
            $('.wic_'+this.name).removeAttr('style');
        }
    });
    
    $("#idCard>select").change(function(){
        if(this.value == ''){
            $('.wic_'+this.name).css({'display':'none'});
        }
        else {
            $('.wic_'+this.name).removeAttr('style');
        }
    });

    var selectedIndusrtries = $('select[name="industry_type[]"] option:selected')
                                    if(selectedIndusrtries.length != 0) {
                                        $.each(selectedIndusrtries, function(k, v){
                                            var liElement = $('<li>').attr('value', $(v).attr('value')).text($(v).attr('label'));
                                            $('#sel_industry').append(liElement);
                                            $(liElement).append($('<span>').addClass('ui-icon ui-icon-closethick del-selected-industry').text('Delete'));
                                            $('select[name="industry_type[]"] option[value='+$(v).attr('value')+']').addClass('selected-industry');
                                        });
                                        $('select[name="industry_type[]"] option:selected').removeAttr('selected');
                                    }


    $('select[name="organization_country"]').on('change',function() {
        var stateField = $('select[name="country_state"]');
        if ($(this).val() == 'US' || $(this).val() == 'CA' || $(this).val() == 'AU') {
            $(".wic_country_state").removeAttr('style')
            stateField.removeAttr('disabled')
            stateField.empty();
            i = 0;
            $.getJSON($('input[name="toasterUrl"]').val()+'plugin/widcard/run/getStates', {'countryCode': $(this).val()}, function(response){
                $.each(response.states, function(code, name) {
//                    stateField.append($('<option>').val(code).text(name));
                    i++ == 0 ? stateField.append($('<option selected>').val(code).text(name)) : stateField.append($('<option>').val(code).text(name));
                    
                });
                if( $('#state-typed').length ) {
                    $('#state-typed').text($('select[name="country_state"] option:selected').val());
                     $('.span_wic_country_state').text($('select[name="country_state"] option:selected').html());
                }
            });
        }
        else{
            stateField.empty();
            stateField.attr('disabled',true);
            $(".wic_country_state").css({'display':'none'})
        }
        if( $('#state-typed').length ) {
            $('#state-typed').text('');
        }
    });

    $('select[name="industry_type[]"]').change(function() {
        if($('select[name="industry_type[]"] option:selected').length >= 5) {
            $('select[name="industry_type[]"] option:selected:gt(4)').removeAttr("selected");
        }
    });



    $('select[name="industry_type[]"] option').click(function() {
        if( ($('#sel_industry li').length < 5) && ($(this).hasClass('selected-industry') != true) ) {
            $(this).addClass('selected-industry');
            var liElement = $('<li>').attr('value', $(this).val()).text($(this).text());
            $('#sel_industry').append(liElement);
            $(liElement).append($('<span>').addClass('ui-icon ui-icon-closethick del-selected-industry').text('Delete'));
        }
    });

    $('.del-selected-industry').on('click', function() {
        $('select[name="industry_type[]"] option[value='+$(this).parent().attr('value')+']').removeAttr('selected').removeClass('selected-industry');
        $(this).parent().remove();
    });

    $('input.payway-check:checked').parent().addClass('checkboxOn');

    $('a#get-token-link').on('click', function(){
        if($('#registration-frame').css('display') == 'none') {
            $('#registration-frame').slideDown('200', function(){
                $('#scroll').animate({
                    scrollTop: $("#scroll").get(0).scrollHeight+'px'
                },
            200);});
        }
        else {
            $('#registration-frame').slideUp('200');
        }
    });

if($('#teaser_text').length) {
    var maxDescCharacters = 200;
    $('#characters-limit').text(maxDescCharacters - $('#teaser_text').val().length);
    $('#teaser_text').keyup(function(e) {
        var letterCountDown = 0;
        var descLetters = $('#teaser_text').val().length;
        if( descLetters > maxDescCharacters ) {
            var descString = $('#teaser_text').val();
            var cutDesc = descString.substr(0, maxDescCharacters);
            $('#teaser_text').val(cutDesc);
        }
        else {
            letterCountDown = maxDescCharacters - descLetters;
        }
        $('#characters-limit').text(letterCountDown);
    });
}

    $('#idCard').on('change', 'input[name="analytics"]', function(){
        if($(this).attr('value') == 'WA') {
            $('div.webAnalyticsCodeRadio').addClass('showIt');
            $('div.seosambaAnalyticsRadio, div#noAnalyticsText').removeClass('showIt');
            $('div.sambaToken').appendTo($('div.webAnalyticsCodeRadio')).addClass('showIt');
            $('#agreement').show();
        }
        else if($(this).attr('value') == 'SA') {
            $('div.seosambaAnalyticsRadio').addClass('showIt');
            $('div.webAnalyticsCodeRadio, div#noAnalyticsText').removeClass('showIt');
            $('div.sambaToken').appendTo($('div.seosambaAnalyticsRadio')).addClass('showIt');
            $('#agreement').show();
        }
        else {
            $('div#noAnalyticsText').addClass('showIt');
            $('div.webAnalyticsCodeRadio, div.seosambaAnalyticsRadio').removeClass('showIt');
            $('div.sambaToken').removeClass('showIt');
            $('#agreement').hide();
        }
    });
    $('div.webAnalyticsCodeRadio').on('change', 'input[name="useGA"]', function() {
        if($(this).is(':checked')) {
            if( $('input[name="useGA"]').data('usetext') == undefined ) {
                $('input[name="useGA"]').data('usetext' ,$('#useWAText').text());
            }
            $('#useWAText>label').text($('#useGAText').text());
        }
        else {
            $('#useGAText').removeClass('showIt');
            $('#useWAText>label').text($('input[name="useGA"]').data('usetext'));
        }
    });

    if($('input#widcardInfo').data('usega') != '') {
        $('input[name="useGA"]').attr('checked', 'checked');
        $('input[name="useGA"]').change();
    }

    if($('input#widcardInfo').data('analyticstype') != '') {
        $('input[name="analytics"][value="'+$('input#widcardInfo').data('analyticstype')+'"]').attr('checked', 'checked').change();
    }

    $('#saveWebsite').on('click', function(e) {
        e.preventDefault();
        $('select[name="industry_type[]"] option').removeAttr('selected');
        var industries = $('#sel_industry li');
        if(industries.length) {
                $.each(industries, function() {
                    $('select[name="industry_type[]"] option[value='+$(this).attr('value')+']').attr('selected','selected');
                });
        }

        var description = $('#teaser_text');
        if($(description).val().length > 200) {
            var posXY = $(description).position();
            $(description).addClass('warning');
            $('#scroll').animate({
                scrollTop: posXY.top-30 +'px'
            }, 100);
            return false;
        }
        else {
            $(description).removeClass('warning');
        }
	if( ($('input[name="agreement"]').length) && ($('input[name="analytics"]:checked').val() != 'NA') ) {
            if(!$('input[name="agreement"]').is(':checked') || (!$('input[name="analytics"]').is(':checked')) ) {
                if(!$('input[name="analytics"]').is(':checked')){
                    showMessage('Please choose an analytics.', 1);
                    return false;
                }
                if(!$('input[name="agreement"]').is(':checked')) {
                    showMessage("Don't forget to accept the terms & conditions.", 1);
                    return false;
                }
            }
        }
        var imageName = $('#page-preview-image').attr('src');
        imageName = imageName.substr(imageName.lastIndexOf('/')+1);
        var uploadTrigger = $('div#page-teaser-uploader-filelist').has('div.ui-widget').length;
        if( (imageName != 'noimage.png') && (uploadTrigger == true) ) {
            $('input[name="imageName"]').val(imageName);
        }

        $('#idCard').submit();
    });

    $('div.payway-box').click(function(){
        var $checkbox = $('input:checkbox',this);
        $checkbox.attr('checked', !$checkbox.attr('checked'));	
		    $(this).toggleClass('checkboxOn');
    });

    $("span.wicInlineElement").hover(function(){
        $("span",$(this)).show("fast");
    }
    ,
    function(){
        $("span",$(this)).hide("fast");
    });

    $('body').on('click', 'span.wicInlineElement', function() {
        var link = $(this);
        var inputWidth = $(this).width()+'px';
        var fontSize = $(this).css('font-size');
        var key = $(this).attr('id');
        if( !$('.wicInlineInput').length ) {
            $(this).replaceWith($('<input>')
                .removeClass()
                .val($(link).text())
                .css({'width': inputWidth, 'font-size': fontSize})
                .attr({'type': 'text'}).addClass('wicInlineInput').blur(function() {
                if(key == 'wicPhone') {
                    var phoneMatch = /^(\+\d)*\s*(\(\d{3}\)\s*)*\d{3}(-{0,1}|\s{0,1})\d{2}(-{0,1}|\s{0,1})\d{2}$/;
                    /*
                    xxx-xxxx
                    xxx-xx-xx
                    xxx xx xx
                    xxx xxxx
                    xxxxxxx
                    (xxx) #phone#
                    (xxx)#phone#
                    +x (xxx) #phone#
                    +x (xxx)#phone#
                    +x(xxx) #phone#
                    +x(xxx)#phone#*/
                    var phoneStr = $(this).val();
                    if(! phoneMatch.test(phoneStr)) {
                        //$('.wicInlineInput').focus();
                        $(this).css('border','1px solid red');
                        return false;
                    }
                    else {
                        $(this).css('border','none');
                    }
                }

                if( $(this).val() == '' ) {
                    $('.wicInlineInput').replaceWith($(link));
                    return false;
                }

                var spinImg = $(this).next();
                $(this).hide();
                $(spinImg).show();
                $.ajax({
                            type: 'POST',
                            url: $(link).data('url'),
                            dataType: 'json',
                            data: {'key': key, 'value': $(this).val()},
                            success: function (answer) {
                                var input = $('.wicInlineInput');
                                $(link).attr('data-default',false);
                                $('.wicInlineInput').replaceWith($(link).text($(input).val()));
                                $(spinImg).hide();
                            },
                            error: function (){}
                        });
            }));
        }
        $('.wicInlineInput').focus();
        if($(link).data('default') == true) {
            $('.wicInlineInput').val('');
            $('.wicInlineInput').css('width', $('.wicInlineInput').width()*2+'px');
        }
        return false;
    });


    if($('input[name="organization_name"]').val() == '') {
        $('#organization-name-typed').text($('#organization-name-typed').data('deforgname'));
    }
    else {
        $('#organization-name-typed').text($('input[name="organization_name"]').val());
         $('.span_wic_organization_name').text($('input[name="organization_name"]').val())
    }
    $('input[name="organization_name"]').keyup(function(e) {
        if($('input[name="organization_name"]').val() == '') {
            $('#organization-name-typed').text($('#organization-name-typed').data('deforgname'));
        }
        else {
            $('#organization-name-typed').text($('input[name="organization_name"]').val());
            $('.span_wic_organization_name').text($('input[name="organization_name"]').val())
        }
    });

    if($('textarea[name="organization_description"]').val() == '') {
        $('#organization-description-typed').text($('#organization-description-typed').data('deforgdesc'));
    }
    else {
        $('#organization-description-typed').text($('textarea[name="organization_description"]').val());
        $('.span_wic_organization_description').text($('textarea[name="organization_description"]').val())

    }
    $('textarea[name="organization_description"]').keyup(function(e) {
        if($('textarea[name="organization_description"]').val() == '') {
            $('#organization-description-typed').text($('#organization-description-typed').data('deforgdesc'));
        }
        else {
            $('#organization-description-typed').text($('textarea[name="organization_description"]').val());
            $('.span_wic_organization_description').text($('textarea[name="organization_description"]').val())
        }
    });


    if($('input[name="address1"]').val() == '') {
        $('#address1-typed').text($('#address1-typed').data('defaddr1'));
    }
    else {
        $('#address1-typed').text($('input[name="address1"]').val());
         $('.span_wic_address1').text($('input[name="address1"]').val())
    }
    $('input[name="address1"]').keyup(function(e) {
        if($('input[name="address1"]').val() == '') {
            $('#address1-typed').text($('#address1-typed').data('defaddr1'));
        }
        else {
            $('#address1-typed').text($('input[name="address1"]').val());
             $('.span_wic_address1').text($('input[name="address1"]').val())
        }
    });
    if($('input[name="email"]').val() != '') {
         $('.span_wic_email').text($('input[name="email"]').val())
    }
    $('input[name="email"]').keyup(function(e) {
        if($('input[name="email"]').val() == '') {
             $('.span_wic_email').text($('input[name="email"]').val())
        }
    });
    if($('input[name="address2"]').val() == '') {
        $('#address2-typed').text($('#address2-typed').data('defaddr2'));
    }
    else {
        $('#address2-typed').text($('input[name="address2"]').val());
         $('.span_wic_address2').text($('input[name="address2"]').val())
    }
    $('input[name="address2"]').keyup(function(e) {
        if($('input[name="address2"]').val() == '') {
            $('#address2-typed').text($('#address2-typed').data('defaddr2'));
        }
        else {
            $('#address2-typed').text($('input[name="address2"]').val());
             $('.span_wic_address2').text($('input[name="address2"]').val())
        }
    });

    
    if($('input[name="city"]').val() == '') {
        $('#city-typed').text($('#city-typed').data('defcity'));
    }
    else {
        $('#city-typed').text($('input[name="city"]').val());
         $('.span_wic_city').text($('input[name="city"]').val())
    }
    $('input[name="city"]').keyup(function(e) {
        if($('input[name="city"]').val() == '') {
            $('#city-typed').text($('#city-typed').data('defcity'));
        }
        else {
            $('#city-typed').text($('input[name="city"]').val());
            $('.span_wic_city').text($('input[name="city"]').val())
        }
    });


    if($('select[name="country_state"] option:selected').val() == undefined) {
        $('#state-typed').text('');
        $('.wic_country_state').css({'display':'none'})
    }
    else {
        $('#state-typed').text($('select[name="country_state"] option:selected').val());
        $('.span_wic_country_state').text($('select[name="country_state"] option:selected').html())
    }

    $('body').on('click', 'select[name="country_state"]', function() {
        if($('select[name="country_state"] option:selected').val() == undefined) {
            $('#state-typed').text('');
        }
        else {
            $('#state-typed').text($('select[name="country_state"] option:selected').val());
            $('.wic_country_state').removeAttr('style')
            $('.span_wic_country_state').text($('select[name="country_state"] option:selected').html())
        }
    });
    

    
     if($('select[name="organization_country"] option:selected').val() != undefined) {
        $('.span_wic_organization_country').text($('select[name="organization_country"] option:selected').html())
         $('#country-typed').text(', '+$('select[name="organization_country"] option:selected').text());

    }
    else {
        $('#country-typed').text('');
    }

    $('body').on('change', 'select[name="organization_country"]', function() {
            $('.span_wic_organization_country').text($('select[name="organization_country"] option:selected').html())
             $('#country-typed').text(', '+$('select[name="organization_country"] option:selected').val());
            
    });


    if($('input[name="zip"]').val() == '') {
        $('#zip-typed').text($('#zip-typed').data('defzip'));
    }
    else {
        $('#zip-typed').text($('input[name="zip"]').val());
        $('.span_wic_zip').text($('input[name="zip"]').val())
        
    }
    $('input[name="zip"]').keyup(function(e) {
        if($('input[name="zip"]').val() == '') {
            $('#zip-typed').text($('#zip-typed').data('defzip'));
        }
        else {
            $('#zip-typed').text($('input[name="zip"]').val());
            $('.span_wic_zip').text($('input[name="zip"]').val())
        }
    });

    if($('input[name="phone"]').val() == '') {
        $('#phone-typed').text($('#phone-typed').data('defphone'));
    }
    else {
        $('#phone-typed').text($('input[name="phone"]').val());
        $('.span_wic_phone').text($('input[name="phone"]').val())
    }
    $('input[name="phone"]').keyup(function(e) {
        if($('input[name="phone"]').val() == '') {
            $('#phone-typed').text($('#phone-typed').data('defphone'));
        }
        else {
            $('#phone-typed').text($('input[name="phone"]').val());
            $('.span_wic_phone').text($('input[name="phone"]').val())
        }
    });
    
    if($('input[name="h1"]').val() == '') {
        $('#website-url-typed').text($('#website-url-typed').data('defurl'));
    }
    else {
        $('#website-url-typed').text($('input[name="h1"]').val());
         $('.span_wic_h1').text($('input[name="h1"]').val())
    }
    $('input[name="h1"]').keyup(function(e) {
        if($('input[name="h1"]').val() == '') {
            $('#website-url-typed').text($('#website-url-typed').data('defurl'));
        }
        else {
            $('#website-url-typed').text($('input[name="h1"]').val());
            $('.span_wic_h1').text($('input[name="h1"]').val())
        }
    });

    setGooleStaticMap();

    $(function() {
        if(typeof(plupload) != "undefined" && $('#page-teaser-uploader-pickfiles').length > 0){
            $('#page-teaser-uploader-pickfiles').button();
            var uploader = new plupload.Uploader({
                runtimes : 'html5,flash,html4',
                browse_button : 'page-teaser-uploader-pickfiles',
                container : 'page-teaser-uploader-filelist',
                max_file_size : '10mb',
                max_file_count: 10,
                //resize : {width : <?php echo $maxWidth; ?>, height : <?php echo $maxHeight; ?>, quality : <?php echo $this->config['imgQuality']; ?>},
                url : $('#website_url').val()+'plugin/widcard/run/uploadLogo',
                filters : [{title : "image",extensions : "png,jpg,jpeg,gif"}]
        });
        uploader.init();
        uploader.bind('FilesAdded', function(up, files) {
                $.each(files, function(i, file) {
                        $('#page-teaser-uploader-filelist').prepend(
                                '<div class="ui-widget"><div class="ui-state-highlight ui-corner-all" id="' + file.id + '"><p>' +
                                        file.name + ' (' + plupload.formatSize(file.size) + ')</p><div class="pbar"></div>' +
                                        '</div></div>');
                        $('#' + file.id + " .pbar").progressbar({value: 0});
                });
                up.refresh();
                up.start();
        });

        uploader.bind('UploadProgress', function(up, file) {
                $('#' + file.id + " .pbar").progressbar({value: file.percent});
        });


        uploader.bind('FileUploaded', function(up, file, info) {
                var response = jQuery.parseJSON(info.response);
                if (response.error == false && response.responseText.hasOwnProperty('src')){
                        $('#page-preview-image').attr('src', response.responseText.src);
                }
                else {
                        var errMsg = '';
                        $.each(response.responseText.data, function(k, v){
                                errMsg += '<p>' + v + '</p>';
                        });
                        smoke.alert(errMsg, {'classname':'errors'});
                }
        });
        }
    });
    
});
function checkFields(){
    var wList  = '';
    var fields = ['organization_name','address1','address2','city','country_state','zip','organization_country','h1','phone','email','organization_description','logo'];
    var widgets = ['BizOrgName','BizAddress1','BizAddress2','BizCity','BizState','BizZip','BizCountry','{$website:url}','BizTelephone','BizEmail','BizOrgDesc','bizLogo'];
    var fieldsLength = fields.length;
    for(i=0 ; i < fieldsLength; i++){
        if(i == 7 ){
             $("#"+fields[i]).val() != '' ? wList += '<li class="wic_'+fields[i]+'">'+widgets[i]+' => <span class="span_wic_'+fields[i]+'"></span></li>' : '';
        }
        else if( i == 11 ){
            wList += '<li class="wic_'+fields[i]+'">{$plugin:widcard:'+widgets[i]+'} => <span class=span_wic_'+fields[i]+'><img src="'+$('.img-padding').attr('src')+'" alt="company logo" class="inlineLogo"/></span></li>';
        }
        else {
            $('input[name="'+fields[i]+'"]').val() != '' || $('textarea[name="'+fields[i]+'"]').val() != undefined || $('select[name="'+fields[i]+'"]').val() != null? wList += '<li class="wic_'+fields[i]+'">{$plugin:widcard:'+widgets[i]+'} => <span class=span_wic_'+fields[i]+'></span></li>' : wList += '<li style="display:none" class="wic_'+fields[i]+'">{$plugin:widcard:'+widgets[i]+'} => <span class=span_wic_'+fields[i]+'></span></li>';
        }
    }
    $("#wic_widgets").html('<ul>'+wList+'</ul>')
}

function setGooleStaticMap() {
    var locationAddr = new Array();
    var mapZoom = 1;
    if( ( ($('input[name="address1"]').val() != '') || ($('input[name="address2"]').val() != '') ) && ( $('select[name="organization_country"]').val() != -1 ) ) {
        if($('select[name="organization_country"] option:selected').val() != -1) {
            locationAddr[locationAddr.length] = $('select[name="organization_country"] option:selected').text();
        }
        if($('select[name="country_state"] option').length) {
            locationAddr[locationAddr.length] = $('select[name="country_state"] option:selected').text();
        }
        if($('input[name="city"]').val() != '') {
            locationAddr[locationAddr.length] = $('input[name="city"]').val();
        }
        if($('input[name="address1"]').val() != '') {
            locationAddr[locationAddr.length] = $('input[name="address1"]').val();
        }
        if($('input[name="address2"]').val() != '') {
            locationAddr[locationAddr.length] = $('input[name="address2"]').val();
        }
        locationAddr = locationAddr.join(',');
        mapZoom = 14;
    }
    $('#gMapImg').attr('src', 'http://maps.googleapis.com/maps/api/staticmap?zoom='+mapZoom+'&size=380x255&maptype=roadmap&center='+locationAddr+'&markers=color:red|label:G|'+locationAddr+'&sensor=false');
}