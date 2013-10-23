$(document).ready(function(){
    var websiteUrl = $('#website_url').val();
    $('textarea.tinymce').tinymce({
        // general settings
        script_url             : websiteUrl + 'system/js/external/tinymce/tiny_mce_gzip.php',
        theme                  : 'advanced',
        width                  : 620,
        height                 : 510,
        plugins                : 'preview,spellchecker,fullscreen,media,paste,stw',
        convert_urls           : false,
        relative_urls          : false,
        entity_encoding        : 'raw',
        content_css            : websiteUrl + 'themes/' + $('#current_theme').val() + '/content.css',
        external_link_list_url : websiteUrl + 'backend/backend_page/linkslist/',
        forced_root_block      : 'p',
        valid_elements         : '*[*]',

        // buttons
        theme_advanced_buttons1 : 'bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist,styleselect,formatselect,fontsizeselect,forecolor,|,link,unlink,anchor,hr',
		theme_advanced_buttons2 : 'image,|,widgets,|,spellchecker,|,pastetext,removeformat,charmap',

        // theme advanced related settings
        theme_advanced_blockformats       : 'div,blockquote,p,address,pre,h2,h3,h4,h5,h6',
		theme_advanced_toolbar_location   : 'top',
		theme_advanced_toolbar_align      : 'left',
        theme_advanced_resizing           : false,
        theme_advanced_statusbar_location : 'none',

        // spell checker
        spellchecker_languages  : 'English=en,French=fr,German=de,Hebrew=iw,Italian=it,Polish=pl,Portuguese (Brazil)=pt-BR, Portuguese (Portugal)=pt-PT,Russian=ru,Spanish=es,Ukrainian=uk',
		spellchecker_rpc_url    : websiteUrl + 'system/js/external/tinymce/plugins/spellchecker/rpc.php',

        // setup hook
        setup : function(ed) {
			ed.keyUpTimer = null;
			ed.onKeyUp.add(function(ed, e) {
				//@see content.js for this function
				dispatchEditorKeyup(ed, e);
			});
		}
    });
});
