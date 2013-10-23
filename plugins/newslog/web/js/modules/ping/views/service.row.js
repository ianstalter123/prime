define([
    'underscore',
    'backbone',
    'text!../templates/ping.newslog.html'
], function(_, Backbone, PingServiceTmpl) {

    var serviceRow = Backbone.View.extend({
        tagName    : 'tr',
        className  : 'ping-service-row',
        template   : _.template(PingServiceTmpl),
        events     : {
            'click .remove': 'removeAction'
        },
        initialize : function() {
            this.model.view = this;
            this.model.on('change', this.render, this);
        },
        toggleAction: function(e) {
            this.model.set('checked', e.currentTarget.checked);
        },
        removeAction: function(e) {
            var self = this;
            showConfirm('You are about to remove the ping service. Are you sure?', function() {
                showSpinner();
                self.model.destroy({wait: true, success: function(model, response) {
                    hideSpinner();
                    if(!response) {
                        showMessage('Cannot remove the news item!', true);
                    }
                }});
            })
        },
        render: function(){
            $(this.el).html(this.template(this.model.toJSON()));
            return this;
        }
    })
    return serviceRow;
})