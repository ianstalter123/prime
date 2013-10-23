define([
    'underscore',
    'backbone',
    '../models/news'
], function(_, Backbone, NewsModel){

    var NewsList = Backbone.Collection.extend({
        model: NewsModel,
        limit: null,
        offset: null,
        order: 'desc',
        tags: null,
        url: function() {
            var url = $('#website_url').val() + 'api/newslog/news/';
            if(this.limit) {
                url += 'limit/' + this.limit + '/';
            }
            if(this.offset) {
                url += 'offset/' + this.offset + '/';
            }
            if(this.tags) {
                url += 'tags/' + this.tags + '/';
            }
            return url += 'order/' + this.order + '/';
        }
    });

    return NewsList;
});