define([
    'underscore',
    'backbone',
    '../models/service'
], function(_, Backbone, ServiceModel){
    var servicesCollection = Backbone.Collection.extend({
        model : ServiceModel,
        url   : $('#website_url').val() + 'api/newslog/services/'
    });
    return servicesCollection;
});