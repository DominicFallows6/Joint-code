define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function($) {

    "use strict";

    //creating jquery widget
    $.widget('Limitless.modalForm', {
        options: {
            modalForm: '#modal-form',
            modalButton: '.open-contact-modal-form'
        },
        _create: function() {
            this.options.modalOption = this._getModalOptions();
            this._bind();
        },
        _getModalOptions: function() {
            /**
             * Modal options
             */
            var options = {
                type: 'popup',
                responsive: true,
                modalClass: 'contact-form',
                buttons: false
            };

            return options;
        },
        _bind: function(){
            var modalOption = this.options.modalOption;
            var modalForm = this.options.modalForm;

            $(document).on('click', this.options.modalButton,  function(){
                //Initialize modal
                $(modalForm).modal(modalOption);
                //open modal
                $(modalForm).trigger('openModal');
            });
        }
    });

    return $.Limitless.modalForm;

});