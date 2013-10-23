define([
    'underscore',
    'backbone'
], function(_, Backbone){

    var NewsView = Backbone.View.extend({
        tagName: 'div',
        className: 'news-item grid_2',
        template: $('#newsItemTemplate').template(),
        events: {
            'click h4' : 'editNewsItem'
        },
        initialize: function() {
            this.model.view = this;
        },
        editNewsItem: function() {
            appRouter.navigate('edit/' + this.model.get('id'), {trigger: true});
        },
        render: function(){
            $(this.el).html($.tmpl(this.template, this.model.toJSON()));
            return this;
        }
    });

    return NewsView;
});