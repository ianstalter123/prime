define([
	'backbone'
], function(Backbone){
    var CouponFormView = Backbone.View.extend({
        el: $('#edit-coupon'),
        events: {
            'submit': 'submit',
            'change :input[data-validator]': 'validate',
            'change [data-action=render]': 'render',
            'change #enable-coupon-limit': function(e){
                if (e.currentTarget.checked){
                    $('#scope').removeAttr('disabled');
                } else {
                    $('#scope').attr('disabled', 'disabled');
                }
            },
            'click #genCoupon': 'genCoupon'
        },
        templates: {

        },
        initialize: function(){
            this.$el.attr('action', $('#website_url').val()+'api/store/coupons');
            $('#startDate').datepicker({
                showOtherMonths: true,
                selectOtherMonths: false,
                dateFormat: 'dd-M-yy',
                onClose: function(selectedDate){
                    $('#endDate').datepicker("option", "minDate", selectedDate);
                }
            });
            $('#endDate').datepicker({
                showOtherMonths: true,
                selectOtherMonths: false,
                dateFormat: 'dd-M-yy',
                onClose: function(selectedDate){
                    $('#startDate').datepicker("option", "maxDate", selectedDate);
                }
            });
        },
        render: function(){
            var couponActionTmpl = '',
                couponType       = $('#coupon-type').val();

            couponActionTmpl = _.template($('#actionDiscountFreeshippingTmpl').html(), {type: couponType});

            $('#coupon-action').html(couponActionTmpl);

            return this;
        },
        submit: function(e){
            e.preventDefault();
            var self = this,
                form = $(e.currentTarget),
                isValid = true;

            _.each(form.find('.required'), function(el){
                if (!$(el).val()){
                    isValid = false;
                }
            });

            if (!isValid){
                showMessage('Missing required field', true);
                return false;
            }
            showSpinner();
            $.ajax({
                url: this.$el.attr('action'),
                data: this.$el.serialize(),
                type: 'POST',
                dataType: 'json',
                success: function(response){
                    self.$el.trigger('reset');
                    self.$el.trigger('coupon:created');
                    hideSpinner();
                },
                error: function(response){
                    hideSpinner();
                    showMessage(response.responseText, true);
                }
            });
        },
        validate: function(e){
            var el = $(e.currentTarget);
            console.log(el.data());
        },
        genCoupon: function(e){
            e.preventDefault();
            var string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
                code = '';

            $('#code').val(code);

            for(var i=0; i<10; i++){
                code += string[Math.floor(Math.random()*string.length)];
            }

            $('#code').val(code);
        }
    });

    return CouponFormView;
});