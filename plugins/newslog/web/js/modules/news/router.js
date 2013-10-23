define([
    'underscore',
    'backbone',
    './views/app',
    './collections/tags',
    './views/tag',
    './models/news',
    './collections/news',
    './views/news',
    './views/website'
], function(_, Backbone, AppView, TagsCollection, TagView, NewsModel, NewsCollection, NewsView, WebsiteView) {

    var NewsRouter = Backbone.Router.extend({

        routes: {
            ''         : 'createNewPost',
            'new'      : 'createNewPost',
            'edit/:id' : 'editPost',
            'manage'   : 'managePosts',
            'broadcast/:id' : 'broadcastPost'
        },

        app   : null,
        tags  : null,
        posts : null,
        broadcastSites: null,

        initialize: function() {

            this.app = new AppView();

            this.tags = new TagsCollection();
            this.tags.on('add', this.renderTag, this);
            this.tags.on('reset', this.renderTags, this);

            this.posts = new NewsCollection();
            this.posts.on('reset', this.renderPosts, this);
        },

        renderTag : function(tag, index) {
            var view = new TagView({model: tag});
            view.render();
            if (index instanceof Backbone.Collection){
                $('#news-tags').prepend(view.$el);
            } else {
                $('#news-tags').append(view.$el);
            }
        },
        renderTags: function() {
            $('#news-tags').empty();
            this.tags.each(this.renderTag, this);
        },
        renderPosts: function() {
            $('#manage-posts').empty();
            this.posts.each(function(newsModel) {
                var newsView = new NewsView({model: newsModel});
                //console.log(newsView.render().$el);
                $('#manage-posts').append(newsView.render().$el)
            }, this);

        },
        createNewPost: function() {
            $('#manage-posts-container:visible').hide('slide');
            this.app.setModel(new NewsModel());
        },
        editPost: function(id) {
            $('#manage-posts-container:visible').hide('slide');
            showSpinner();
            var post = new NewsModel();
            post.fetch({data: {id: id}}).done(function() {
                hideSpinner();
                appRouter.app.setModel(post);
            });
        },
        managePosts: function() {
           this.app.$('#manage-posts-container').slideToggle();
           this.posts.fetch();
        },
        broadcastPost: function(id) {
            var post = new NewsModel();
            showSpinner();
            console.log('pfff');
            post.fetch({data: {id: id}}).done(function() {
                appRouter.app.setModel(post);
                $.when(appRouter.app.$('#broadcast-list-container').slideToggle()).done(function() {
                    $.getJSON($('#website_url').val() + 'api/newslog/broadcast/').done(function(response) {
                        hideSpinner();
                        //console.log(response);
                        appRouter.broadcastSites = response;
                        appRouter.renderBroadcastSites(id);
                    });
                });
            });
        },
        renderBroadcastSites: function(id) {
            $('#broadcast-list').empty();
            _.each(this.broadcastSites, function(site) {
                //console.log(site);
                var view = new WebsiteView({model: site});
                $('#broadcast-list').append(view.render().$el);
            })
            $('<input type="submit" name="broadcast" id="broadcast-btn" value="Broadcast" data-nid="' + id + '" />').insertAfter('#broadcast-list');
        }
    });

    var initialize = function() {
        window.appRouter = new NewsRouter;
        $.when(
            appRouter.tags.fetch()
        ).then(function(){
            Backbone.history.start();
        });
    };

    return {
        initialize: initialize
    };
});