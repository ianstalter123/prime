define([
    'underscore',
    'backbone'
], function(_, Backbone){

    var NewsModel = Backbone.Model.extend({
        urlRoot:  $('#website_url').val() + 'api/newslog/news/id',
        defaults: function(){
            return {
                title     : '',
                teaser    : '',
                content   : '',
                metaData  : {
                    h1: '',
                    title: '',
                    navName: '',
                    url: '',
                    teaserText: '',
                    metaKeywords: '',
                    image: '',
                    template: ''
                },
                broadcast : 0,
                published : 1,
                tags      : [],
                archived  : 0,
                featured  : 0,
                type      : 'internal'
            };
        }
    });

    return NewsModel;
});
