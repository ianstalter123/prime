define([
    'underscore',
    'backbone',
    '../collections/services',
    '../views/service.row'
], function(_, Backbone, ServicesCollection, ServiceView) {

    var pingTabApplication = Backbone.View.extend({
        el: $('#ping-services-section'),
        events: {
            'keypress #ping-url': 'addAction',
            'click #add-service': 'addServiceAction',
            'change .status': 'toggleStatusAction'
        },
        services: null,
        initialize: function() {
            this.services = new ServicesCollection();
            this.services.on('reset', this.render, this);
            this.services.on('add', this.render, this);
            this.services.on('remove', this.render, this);
            this.services.fetch();
        },
        addAction: function(e) {
            var serviceUrl = $(e.currentTarget).val();
            if(e.keyCode == 13 && serviceUrl != '') {
                this._addService(serviceUrl);
            }
        },
        addServiceAction: function(e) {
            var serviceUrl = this.$('#ping-url').val();
            if(serviceUrl != '') {
                this._addService(serviceUrl);
            }
        },
        _addService: function(url) {
            showSpinner();
            this.services.create({url: url}, {
                wait: true,
                success: function() {
                    hideSpinner()
                    this.$('#ping-url').val('');
                }
            });
        },
        toggleStatusAction: function(e) {
            var modelId = this.$(e.currentTarget).data('sid');
            var status  = e.currentTarget.value;
            var model = this.services.get(modelId);
            model.set({status: status});
            showSpinner();
            model.save(null,
                {success: function() {
                    hideSpinner();
                    showMessage('Status updated');
                }
            });
        },
        render: function(e) {
            var container = $('#ping-services-list > tbody');
            container.empty();
            this.services.each(function(service) {
                var serviceView = new ServiceView({model: service});
                container.append(serviceView.render().$el)
            });


        }
    })

    return pingTabApplication;
});