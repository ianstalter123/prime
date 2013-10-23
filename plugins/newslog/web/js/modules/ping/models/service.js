define([
    'underscore',
    'backbone'
], function(_, Backbone){

    var ServiceModel = Backbone.Model.extend({
        urlRoot:  function() {
            return $('#website_url').val() + 'api/newslog/services/id/';
        }
    });

    return ServiceModel;
});
