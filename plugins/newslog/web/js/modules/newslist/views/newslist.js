define([
    'underscore',
    'backbone'
], function(_, Backbone){

    var NewslistView = Backbone.View.extend({
        tagName: 'li',
        className: 'news-list-item',
        template: _.template($('#newsListItemTemplate').text()),
        initialize: function() {
            this.model.view = this;
        },
        render: function(){
            $(this.el).html(this.template(this.model.toJSON()));
            return this;
        }
    });

    return NewslistView;
});