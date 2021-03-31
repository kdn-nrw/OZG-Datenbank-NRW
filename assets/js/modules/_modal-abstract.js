// import 'bootstrap3/js/modal';

class AbstractModal {

    constructor(element) {
        const $element = $(element);
        this.$element = $element;
        this.$modal = null;
        this.config = null;

        $element.on('click', (e) => {
            e.preventDefault();
            this.load();
        });
    }

    _getConfig() {
        if (null === this.config) {
            this.config = this.$element.data('modal');
        }

        return this.config;
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
                                <button type="button" class="js-submit btn btn-primary">${config.submit}</button>
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

    _closed() {
        this.$modal.remove();
        this.$modal = null;
    }

    _submit() {
    }

    _handleResponse(response) {
        const responseType = response.type;
        switch (responseType) {
            case 'reload':
                window.location.reload(true);
                break;
            case 'redirect':
                window.location.href = response.url;
                break;
            case 'new-tab':
                window.open(response.url,'_blank');
                this.$modal.modal('hide');
                break;
            case 'content':
                const $modal = this.$modal;
                const $response = $(response.content);
                const partials = ['.modal-header', '.modal-body', '.modal-footer'];
                if ($response.filter(partials.join(', ')).length > 0) {
                    for (const selector of partials) {
                        const $element = $response.filter(selector).first();
                        if ($element) {
                            $modal.find(selector).html($element.html());
                        }
                    }
                } else {
                    $modal.find('.modal-body').html($(response.content));
                }
                break;
        }
    }

    load() {
        const $modal = this._getModal();
        $modal.on('click', '.js-submit', (e) => {
            e.preventDefault();
            $(e.currentTarget).addClass('disabled')
            $(e.currentTarget).html('<i class="fa fa-spin fa-spinner"></i>')
            this._submit();
        });
        $modal.on('hidden.bs.modal', this._closed.bind(this));

        $modal.modal('show');
    }
}

export default AbstractModal;

