import AbstractModal from "./_modal-abstract";

class ModalForm extends AbstractModal {

    constructor(element) {
        super(element);

        this.submitUrl = null;
        this.$form = null;
    }

    _getConfig() {
        if (null === this.config) {
            super._getConfig();

            this.config.message = '<div class="text-center"><i class="fa fa-spinner fa-spin fa-4x"></i></div>';

            if (!this.config.cancel || this.config.cancel.length <= 0) {
                this.config.cancel = '<i class="fa fa-times"></i>';
            }
        }

        return this.config;
    }

    _getModalLoadUrl() {
        return this._getConfig().loadUrl;
    }

    _getModalSubmitUrl() {
        return this.submitUrl || this._getConfig().submitUrl;
    }

    _getModal() {
        if (null === this.$modal) {
            const config = this._getConfig();
            this.$modal = $(`
                <div class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">
                                    <i class="fa fa-times"></i>
                                </button>
                                <h4 class="modal-title">${config.title}</h4>
                            </div>
                            <div class="modal-body">
                                <p>${config.message}</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="js-cancel btn btn-default" data-dismiss="modal">${config.cancel}</button>
                            </div>
                        </div>
                    </div>
                </div>
            `).modal({
                show: false,
            });

        }
        return this.$modal;
    }

    _submit() {
        $.ajax({
            type: 'POST',
            url: this._getModalSubmitUrl(),
            data : this.$form.serialize(),
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        }).then((response) => {
            this._handleResponse(response);
        })
    }

    _handleResponse(response) {
        super._handleResponse(response);
        if (response.type ===  'content') {
            const $form = this.$modal.find('form').first();
            this.$form = $form;
            if($form && $form.attr('action')) {
                this.submitUrl = $form.attr('action');
            }
            const event = new CustomEvent('mb-form-update', { detail: {container: $form} });
            document.dispatchEvent(event);
        }
    }

    load() {
        super.load();

        let loadUrl = this._getModalLoadUrl();
        if (loadUrl) {
            $.ajax({
                url: loadUrl,
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            }).then((response) => {
                this._handleResponse(response);
            })
        }
    }
}

export default ModalForm;