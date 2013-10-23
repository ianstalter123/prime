require.config({
    paths: {
        'underscore'         : './libs/underscore/underscore.min',
        'backbone'           : './libs/backbone/backbone.min',
        'backbone.paginator' : './libs/backbone/backbone.paginator.min',
        'text'               : './libs/text/text'
    },
    shim: {
        'underscore': {exports: '_'},
        'backbone' : {
            deps: ['underscore'],
            exports: 'Backbone'
        },
        'backbone.paginator': ['backbone']
    }
});

require(['modules/ping/main'],
    function(App){
        App.initialize();
    }
);