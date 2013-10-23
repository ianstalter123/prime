define([
    'underscore',
    'backbone'
], function(_, Backbone){

    var TagModel = Backbone.Model.extend({
        urlRoot:  $('#website_url').val() + 'api/newslog/tags/id/',
        defaults: function(){
            return {
                name: ''
            };
        }
    });

    return TagModel;
});