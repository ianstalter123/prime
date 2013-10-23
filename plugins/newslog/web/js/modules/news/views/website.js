define([
    'underscore',
    'backbone'
], function(_, Backbone){

    var WebsiteView = Backbone.View.extend({
        tagName: 'div',
        className: 'broadcast-item-website grid_2',
        template: $('#broadcastWebsiteTemplate').template(),
        initialize: function() {
            this.model.view = this;
        },
        render: function(){
            $(this.el).html($.tmpl(this.template, this.model));
            return this;
        }
    });

    return WebsiteView;
});