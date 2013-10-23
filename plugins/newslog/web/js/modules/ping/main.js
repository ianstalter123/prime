define(['./views/application'],
    function(ApplicationView) {
        return {
            initialize: function() {
                window.pingConfigTab = new ApplicationView();
            }
        };
    }
);