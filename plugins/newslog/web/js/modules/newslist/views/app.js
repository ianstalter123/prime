define([
    'underscore',
    'backbone',
    '../../news/collections/news',
    './newslist',
    '../../news/models/news'
], function(_, Backbone, NewsCollection, NewsListView, NewsModel) {

    var AppView = Backbone.View.extend({

        el: $('.news-list'),

        events: {
            'click .news-item-tag-filter': 'filterByTag',
            'click .news-item-remove': 'removeNewsEntry'
        },

        newsList: null,
        filtered: false,
        cachedList: null,

        initialize: function() {
            this.newsList = new NewsCollection();

            var limit           = $(this.el).data('limit');
            this.newsList.limit = (limit) ? limit : null;
            this.newsList.order = $(this.el).data('order');

            var filterTag       = $(this.el).data('tags');
            this.newsList.tags  = (filterTag) ? filterTag : null;

            this.newsList.on('filter', this.renderNews, this);
            this.newsList.on('filter:reset', this.renderNews, this);
            this.newsList.on('destroy', this.renderNews, this);
            this.newsList.on('reset', this.renderNews, this);
        },

        filterByTag: function(e) {
            if(!this.filtered) {
                this.cachedList = this.newsList.models;
            }
            var filterTag = this.$(e.target).data('filter');
            if(filterTag == 'all') {
                if(this.filtered == true) {
                    console.log(this.cachedList);
                    this.filtered = false;
                    var self = this;
                    this.newsList.models = this.cachedList;
                    this.newsList.trigger('filter:reset');
                }
            } else {
                this.filtered        = true;
                this.newsList.models = this.newsList.filter(function(news) {
                    var tagsNames = _.pluck(news.get('tags'), 'name');
                    if(!tagsNames.length) {
                        return false;
                    }
                    return (_.indexOf(tagsNames, filterTag) != -1);

                });
            }
            this.newsList.trigger('filter');
        },

        removeNewsEntry: function(e) {
            e.preventDefault();
            var id = this.$(e.target).parent().data('nid');
            self = this;
            showConfirm(this.$(e.target).parents('div').data('confirm'), function() {
                showSpinner();
                var newsEntry = self.newsList.get(id);
                newsEntry.destroy({
                    success: function() {
                        hideSpinner();
                    },
                    error: function(model, response) {
                        hideSpinner();
                        console.log(response);
                        //showMessage(response, true);
                    }
                });
            });
        },

        renderNews: function() {
            $(this.el).empty();
            if(this.newsList.isEmpty()) {
                $(this.el).append('<li class="news-list-item">There are no news, yet.</li>');
            }
            this.newsList.each(function(newsItem) {
                var newsListView = new NewsListView({model: newsItem});
                $(this.el).append(newsListView.render().el)
            }, this);
            return this;
        },
        render: function() {
            return this;
        }

    });

    return AppView;

});