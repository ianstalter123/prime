define([
    './views/app'
], function(AppView) {
    window.appView = new AppView();
    $(function(){
        $(document).trigger('list:loaded');
    });

});