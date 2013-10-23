define([
    'underscore',
    'backbone'
], function(_, Backbone){

    var appView = Backbone.View.extend({
        el: $('#new-post'),
        oldUrl: null,
        events: {
            'keypress input#news-tag'    : 'newTag',
            'click div.template_preview' : 'selectTemplate',
            'click #save-btn'            : 'savePost',
            'change #broadcast'          : 'toggleBroadcast',
            'change #published'          : 'togglePublished',
            'click input[name^=tag]'     : 'setTags',
            'click .show-posts'          : 'managePosts',
            'click .closebutton-list'    : 'closeManagePosts',
            'click .close-broadcast'     : 'closeBroadcast',
            'click #broadcast-btn'       : 'broadcast',
            'keyup #h1'               : 'populateAction'
            //'change [data-destination=property]' : 'setProperty'
        },
        initialize: function() {},
        setModel: function(model) {
            this.model = model;
            this.model.view = this;
            this.render();
        },
        newTag: function(e) {
            var tagName = this.$(e.target).val();
            if(e.keyCode == 13 && tagName != '') {
                if(appRouter.tags.exists(tagName)) {
                    $(e.target).val('');
                } else {
                    appRouter.tags.create({name: tagName}, {
                        success: function(model, response) {
                            $(e.target).val('').blur();
                        },
                        error: function(model, response) {
                            showMessage(response.responseText, true);
                        }
                    })
                }
            }
        },
        populateAction: function(e) {
            if(this.model.isNew()) {
                this.$('[data-destination=property]').val(e.currentTarget.value);
            }
        },
        selectTemplate: function(e) {
            var templateId = $(e.target).parents('div.template_preview').find('input[name="template-id"]').val();
            $('#template-id').val(templateId);
            $('#current-template').text(templateId);
            $('#templatelist').slideUp();
        },
        toggleBroadcast: function() {
            this.model.set({
                'broadcast': this.$('#broadcast').prop('checked') ? 1 : 0
            });
        },
        togglePublished : function() {
            this.model.set({
                'published': this.$('#published').prop('checked') ? 1 : 0
            });
        },
        setTags : function() {
            var tags = [];
            _.each($('input[name^=tag]:checked'), function(e) {
                tags.push(appRouter.tags.get(e.value).toJSON());
            });
            this.model.set({tags : tags});
        },
        managePosts: function() {
            this.$('#manage-posts-container').slideToggle();
            appRouter.posts.fetch();
        },
        closeManagePosts: function() {
            this.$('#manage-posts-container').hide('slide');
        },
        closeBroadcast: function() {
            this.$('#broadcast').prop('checked', false);
            this.$('#broadcast-list-container').hide('slide');
        },
        savePost: function() {
            var url = $('#url').val();
            this.model.set({
                title    : $('#h1').val(),
                teaser   : $('#teaser-text').val(),
                metaData : JSON.stringify({
                    h1           : $('#h1').val(),
                    title        : $('#title').val(),
                    navName      : $('#nav-name').val(),
                    url          : url,
                    oldUrl       : this.oldUrl,
                    teaserText   : $('#teaser-text').val(),
                    metaKeywords : $('#meta-keywords').val(),
                    template     : $('#template-id').val(),
                    image        : $('#page-preview-image').attr('src')
                })
            });

            if(!this.validatePost()) {
                showMessage('You are missing required fields!', true);
                return false;
            }
            showSpinner();
            this.model.save(null, {success: function(model, response) {
                var newsMeta = JSON.parse(response.metaData);
                hideSpinner();
                appRouter.navigate('edit/' + model.id, true);
                //showMessage('Gooooood! Now you can visit your seo-optimized news page <a target="_blank" href="' + $('#website_url').val() + newsMeta.url + '">' + $('#website_url').val() + newsMeta.url + '</a>');
                parent.window.location.href = this.$('#website_url').val() + newsMeta.url;
            }});
        },
        broadcast: function() {
            var websites = [];
            _.each($('.broadcast-site:checked'), function(item) {
                websites.push($(item).data('wid'));
            })

            $.ajax({
                url: $('#website_url').val() + 'api/newslog/broadcast/',
                type: 'post',
                beforeSend: showSpinner,
                data: {
                    nid: $('#broadcast-btn').data('nid'),
                    websites: websites
                },
                dataType: 'json'
            }).done(function(response) {
                hideSpinner();
                showMessage('Hooray! It looks like we did it!');
            });
        },
        validatePost: function() {
            var metaData = JSON.parse(this.model.get('metaData'));
            var error = false;
            for(var property in metaData) {
                if(metaData.hasOwnProperty(property)) {
                    if(property == 'teaserText' || property == 'metaKeywords' || property == 'image' || property == 'oldUrl') {
                        continue;
                    }
                    var elId = property;
                    if(property == 'navName') {
                        elId = 'nav-name';
                    }
                    if(property == 'template') {
                        elId = 'current-template';
                    }
                    if(!metaData[property]) {
                        this.$('#' + elId).addClass('error-highlight');
                        error = true || error;
                    } else {
                        this.$('#' + elId).removeClass('error-highlight');
                    }
                }
            }
            return !error;
        },
        render: function() {
            if(this.model.get('type') == 'external') {
                $('input').attr('readonly', true).addClass('grayout');
                $('textarea').attr('readonly', true).addClass('grayout');
                $('#page-teaser-uploader-pickfiles').attr('disabled', true);
                $('#current-template').off('click');
                $(':checkbox').attr('disabled', true);
                $('#save-btn').attr('disabled', true);
            }

            if(!this.model.isNew()) {

                //set main values
                this.$('#title').val(this.model.get('title'));

                //set other values
                var metaData = JSON.parse(this.model.get('metaData'));
                _.each(metaData, function(data, key) {
                    this.$('[name^=' + key + ']').val(data);
                }, this);

                //set template
                if(metaData.template) {
                    this.$('#current-template').text(metaData.template);
                    this.$('#template-id').val(metaData.template);
                }

                //set an image
                if(typeof (metaData.image) != 'undefined' && metaData.image) {
                    this.$('#page-preview-image').attr('src', $('#website_url').val() + 'previews/' + metaData.image);
                }

                //populate tags
                this.$('#news-tags').find('input:checkbox:checked').removeAttr('checked');
                _.each(this.model.get('tags'), function(tag) {
                    var el = appRouter.tags.get(tag.id).view.el;
                    $(el).find(':checkbox').attr('checked', 'checked');
                });
            }
            this.oldUrl = this.$('#url').val();
            return this;
        }
    });

    return appView;
});