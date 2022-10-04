"use strict";
(self["webpackChunkkdn_ozg"] = self["webpackChunkkdn_ozg"] || []).push([["assets_js_modules_modal-form_js"],{

/***/ "./assets/js/modules/_modal-abstract.js":
/*!**********************************************!*\
  !*** ./assets/js/modules/_modal-abstract.js ***!
  \**********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
// import 'bootstrap3/js/modal';
class AbstractModal {
  constructor(element) {
    const $element = $(element);
    this.$element = $element;
    this.$modal = null;
    this.config = null;
    $element.on('click', e => {
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

      this.$modal = $("\n                <div class=\"modal fade\" role=\"dialog\">\n                    <div class=\"modal-dialog\">\n                        <div class=\"modal-content\">\n                            <div class=\"modal-header\">\n                                <button type=\"button\" class=\"close\" data-dismiss=\"modal\">\n                                    <i class=\"fa fa-times\"></i>\n                                </button>\n                                <h4 class=\"modal-title\">".concat(config.title, "</h4>\n                            </div>\n                            <div class=\"modal-body\">\n                                <p>").concat(config.message, "</p>\n                            </div>\n                            <div class=\"modal-footer\">\n                                <button type=\"button\" class=\"js-cancel btn btn-default\" data-dismiss=\"modal\">").concat(config.cancel, "</button>\n                                <button type=\"button\" class=\"js-submit btn btn-primary\">").concat(config.submit, "</button>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n            ")).modal({
        show: false
      });
    }

    return this.$modal;
  }

  _closed() {
    this.$modal.remove();
    this.$modal = null;
  }

  _submit() {}

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
        window.open(response.url, '_blank');
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

    $modal.on('click', '.js-submit', e => {
      e.preventDefault();
      $(e.currentTarget).addClass('disabled');
      $(e.currentTarget).html('<i class="fa fa-spin fa-spinner"></i>');

      this._submit();
    });
    $modal.on('hidden.bs.modal', this._closed.bind(this));
    $modal.modal('show');
  }

}

/* harmony default export */ __webpack_exports__["default"] = (AbstractModal);

/***/ }),

/***/ "./assets/js/modules/modal-form.js":
/*!*****************************************!*\
  !*** ./assets/js/modules/modal-form.js ***!
  \*****************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _modal_abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_modal-abstract */ "./assets/js/modules/_modal-abstract.js");


class ModalForm extends _modal_abstract__WEBPACK_IMPORTED_MODULE_0__["default"] {
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

      this.$modal = $("\n                <div class=\"modal fade\" role=\"dialog\">\n                    <div class=\"modal-dialog\">\n                        <div class=\"modal-content\">\n                            <div class=\"modal-header\">\n                                <button type=\"button\" class=\"close\" data-dismiss=\"modal\">\n                                    <i class=\"fa fa-times\"></i>\n                                </button>\n                                <h4 class=\"modal-title\">".concat(config.title, "</h4>\n                            </div>\n                            <div class=\"modal-body\">\n                                <p>").concat(config.message, "</p>\n                            </div>\n                            <div class=\"modal-footer\">\n                                <button type=\"button\" class=\"js-cancel btn btn-default\" data-dismiss=\"modal\">").concat(config.cancel, "</button>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n            ")).modal({
        show: false
      });
    }

    return this.$modal;
  }

  _submit() {
    $.ajax({
      type: 'POST',
      url: this._getModalSubmitUrl(),
      data: this.$form.serialize(),
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    }).then(response => {
      this._handleResponse(response);
    });
  }

  _handleResponse(response) {
    super._handleResponse(response);

    if (response.type === 'content') {
      const $form = this.$modal.find('form').first();
      this.$form = $form;

      if ($form && $form.attr('action')) {
        this.submitUrl = $form.attr('action');
      }

      const event = new CustomEvent('mb-form-update', {
        detail: {
          container: $form
        }
      });
      document.dispatchEvent(event);
    }
  }

  load() {
    super.load();

    let loadUrl = this._getModalLoadUrl();

    if (loadUrl) {
      $.ajax({
        url: loadUrl,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      }).then(response => {
        this._handleResponse(response);
      });
    }
  }

}

/* harmony default export */ __webpack_exports__["default"] = (ModalForm);

/***/ })

}]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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QyxJQUFaLENBQWlCLE1BQWpCLEVBQXlCRCxLQUF6QixFQUFkO01BQ0EsS0FBS1EsS0FBTCxHQUFhQSxLQUFiOztNQUNBLElBQUdBLEtBQUssSUFBSUEsS0FBSyxDQUFDUSxJQUFOLENBQVcsUUFBWCxDQUFaLEVBQWtDO1FBQzlCLEtBQUtULFNBQUwsR0FBaUJDLEtBQUssQ0FBQ1EsSUFBTixDQUFXLFFBQVgsQ0FBakI7TUFDSDs7TUFDRCxNQUFNQyxLQUFLLEdBQUcsSUFBSUMsV0FBSixDQUFnQixnQkFBaEIsRUFBa0M7UUFBRUMsTUFBTSxFQUFFO1VBQUNDLFNBQVMsRUFBRVo7UUFBWjtNQUFWLENBQWxDLENBQWQ7TUFDQWEsUUFBUSxDQUFDQyxhQUFULENBQXVCTCxLQUF2QjtJQUNIO0VBQ0o7O0VBRUQvQyxJQUFJLEdBQUc7SUFDSCxNQUFNQSxJQUFOOztJQUVBLElBQUl3QyxPQUFPLEdBQUcsS0FBS0QsZ0JBQUwsRUFBZDs7SUFDQSxJQUFJQyxPQUFKLEVBQWE7TUFDVDlDLENBQUMsQ0FBQ2dELElBQUYsQ0FBTztRQUNIckIsR0FBRyxFQUFFbUIsT0FERjtRQUVISSxPQUFPLEVBQUU7VUFBQyxvQkFBb0I7UUFBckI7TUFGTixDQUFQLEVBR0dDLElBSEgsQ0FHUy9CLFFBQUQsSUFBYztRQUNsQixLQUFLRCxlQUFMLENBQXFCQyxRQUFyQjtNQUNILENBTEQ7SUFNSDtFQUNKOztBQWpHaUM7O0FBb0d0QywrREFBZXNCLFNBQWYiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9rZG5fb3pnLy4vYXNzZXRzL2pzL21vZHVsZXMvX21vZGFsLWFic3RyYWN0LmpzIiwid2VicGFjazovL2tkbl9vemcvLi9hc3NldHMvanMvbW9kdWxlcy9tb2RhbC1mb3JtLmpzIl0sInNvdXJjZXNDb250ZW50IjpbIi8vIGltcG9ydCAnYm9vdHN0cmFwMy9qcy9tb2RhbCc7XG5cbmNsYXNzIEFic3RyYWN0TW9kYWwge1xuXG4gICAgY29uc3RydWN0b3IoZWxlbWVudCkge1xuICAgICAgICBjb25zdCAkZWxlbWVudCA9ICQoZWxlbWVudCk7XG4gICAgICAgIHRoaXMuJGVsZW1lbnQgPSAkZWxlbWVudDtcbiAgICAgICAgdGhpcy4kbW9kYWwgPSBudWxsO1xuICAgICAgICB0aGlzLmNvbmZpZyA9IG51bGw7XG5cbiAgICAgICAgJGVsZW1lbnQub24oJ2NsaWNrJywgKGUpID0+IHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIHRoaXMubG9hZCgpO1xuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBfZ2V0Q29uZmlnKCkge1xuICAgICAgICBpZiAobnVsbCA9PT0gdGhpcy5jb25maWcpIHtcbiAgICAgICAgICAgIHRoaXMuY29uZmlnID0gdGhpcy4kZWxlbWVudC5kYXRhKCdtb2RhbCcpO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIHRoaXMuY29uZmlnO1xuICAgIH1cblxuICAgIF9nZXRNb2RhbCgpIHtcbiAgICAgICAgaWYgKG51bGwgPT09IHRoaXMuJG1vZGFsKSB7XG4gICAgICAgICAgICBjb25zdCBjb25maWcgPSB0aGlzLl9nZXRDb25maWcoKTtcbiAgICAgICAgICAgIHRoaXMuJG1vZGFsID0gJChgXG4gICAgICAgICAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsIGZhZGVcIiByb2xlPVwiZGlhbG9nXCI+XG4gICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJtb2RhbC1kaWFsb2dcIj5cbiAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJtb2RhbC1jb250ZW50XCI+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWhlYWRlclwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzcz1cImNsb3NlXCIgZGF0YS1kaXNtaXNzPVwibW9kYWxcIj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxpIGNsYXNzPVwiZmEgZmEtdGltZXNcIj48L2k+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvYnV0dG9uPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8aDQgY2xhc3M9XCJtb2RhbC10aXRsZVwiPiR7Y29uZmlnLnRpdGxlfTwvaDQ+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWJvZHlcIj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPHA+JHtjb25maWcubWVzc2FnZX08L3A+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWZvb3RlclwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzcz1cImpzLWNhbmNlbCBidG4gYnRuLWRlZmF1bHRcIiBkYXRhLWRpc21pc3M9XCJtb2RhbFwiPiR7Y29uZmlnLmNhbmNlbH08L2J1dHRvbj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJqcy1zdWJtaXQgYnRuIGJ0bi1wcmltYXJ5XCI+JHtjb25maWcuc3VibWl0fTwvYnV0dG9uPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgYCkubW9kYWwoe1xuICAgICAgICAgICAgICAgIHNob3c6IGZhbHNlLFxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIHRoaXMuJG1vZGFsO1xuICAgIH1cblxuICAgIF9jbG9zZWQoKSB7XG4gICAgICAgIHRoaXMuJG1vZGFsLnJlbW92ZSgpO1xuICAgICAgICB0aGlzLiRtb2RhbCA9IG51bGw7XG4gICAgfVxuXG4gICAgX3N1Ym1pdCgpIHtcbiAgICB9XG5cbiAgICBfaGFuZGxlUmVzcG9uc2UocmVzcG9uc2UpIHtcbiAgICAgICAgY29uc3QgcmVzcG9uc2VUeXBlID0gcmVzcG9uc2UudHlwZTtcbiAgICAgICAgc3dpdGNoIChyZXNwb25zZVR5cGUpIHtcbiAgICAgICAgICAgIGNhc2UgJ3JlbG9hZCc6XG4gICAgICAgICAgICAgICAgd2luZG93LmxvY2F0aW9uLnJlbG9hZCh0cnVlKTtcbiAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIGNhc2UgJ3JlZGlyZWN0JzpcbiAgICAgICAgICAgICAgICB3aW5kb3cubG9jYXRpb24uaHJlZiA9IHJlc3BvbnNlLnVybDtcbiAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIGNhc2UgJ25ldy10YWInOlxuICAgICAgICAgICAgICAgIHdpbmRvdy5vcGVuKHJlc3BvbnNlLnVybCwnX2JsYW5rJyk7XG4gICAgICAgICAgICAgICAgdGhpcy4kbW9kYWwubW9kYWwoJ2hpZGUnKTtcbiAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIGNhc2UgJ2NvbnRlbnQnOlxuICAgICAgICAgICAgICAgIGNvbnN0ICRtb2RhbCA9IHRoaXMuJG1vZGFsO1xuICAgICAgICAgICAgICAgIGNvbnN0ICRyZXNwb25zZSA9ICQocmVzcG9uc2UuY29udGVudCk7XG4gICAgICAgICAgICAgICAgY29uc3QgcGFydGlhbHMgPSBbJy5tb2RhbC1oZWFkZXInLCAnLm1vZGFsLWJvZHknLCAnLm1vZGFsLWZvb3RlciddO1xuICAgICAgICAgICAgICAgIGlmICgkcmVzcG9uc2UuZmlsdGVyKHBhcnRpYWxzLmpvaW4oJywgJykpLmxlbmd0aCA+IDApIHtcbiAgICAgICAgICAgICAgICAgICAgZm9yIChjb25zdCBzZWxlY3RvciBvZiBwYXJ0aWFscykge1xuICAgICAgICAgICAgICAgICAgICAgICAgY29uc3QgJGVsZW1lbnQgPSAkcmVzcG9uc2UuZmlsdGVyKHNlbGVjdG9yKS5maXJzdCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCRlbGVtZW50KSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJG1vZGFsLmZpbmQoc2VsZWN0b3IpLmh0bWwoJGVsZW1lbnQuaHRtbCgpKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICRtb2RhbC5maW5kKCcubW9kYWwtYm9keScpLmh0bWwoJChyZXNwb25zZS5jb250ZW50KSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgbG9hZCgpIHtcbiAgICAgICAgY29uc3QgJG1vZGFsID0gdGhpcy5fZ2V0TW9kYWwoKTtcbiAgICAgICAgJG1vZGFsLm9uKCdjbGljaycsICcuanMtc3VibWl0JywgKGUpID0+IHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICQoZS5jdXJyZW50VGFyZ2V0KS5hZGRDbGFzcygnZGlzYWJsZWQnKVxuICAgICAgICAgICAgJChlLmN1cnJlbnRUYXJnZXQpLmh0bWwoJzxpIGNsYXNzPVwiZmEgZmEtc3BpbiBmYS1zcGlubmVyXCI+PC9pPicpXG4gICAgICAgICAgICB0aGlzLl9zdWJtaXQoKTtcbiAgICAgICAgfSk7XG4gICAgICAgICRtb2RhbC5vbignaGlkZGVuLmJzLm1vZGFsJywgdGhpcy5fY2xvc2VkLmJpbmQodGhpcykpO1xuXG4gICAgICAgICRtb2RhbC5tb2RhbCgnc2hvdycpO1xuICAgIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgQWJzdHJhY3RNb2RhbDtcblxuIiwiaW1wb3J0IEFic3RyYWN0TW9kYWwgZnJvbSBcIi4vX21vZGFsLWFic3RyYWN0XCI7XG5cbmNsYXNzIE1vZGFsRm9ybSBleHRlbmRzIEFic3RyYWN0TW9kYWwge1xuXG4gICAgY29uc3RydWN0b3IoZWxlbWVudCkge1xuICAgICAgICBzdXBlcihlbGVtZW50KTtcblxuICAgICAgICB0aGlzLnN1Ym1pdFVybCA9IG51bGw7XG4gICAgICAgIHRoaXMuJGZvcm0gPSBudWxsO1xuICAgIH1cblxuICAgIF9nZXRDb25maWcoKSB7XG4gICAgICAgIGlmIChudWxsID09PSB0aGlzLmNvbmZpZykge1xuICAgICAgICAgICAgc3VwZXIuX2dldENvbmZpZygpO1xuXG4gICAgICAgICAgICB0aGlzLmNvbmZpZy5tZXNzYWdlID0gJzxkaXYgY2xhc3M9XCJ0ZXh0LWNlbnRlclwiPjxpIGNsYXNzPVwiZmEgZmEtc3Bpbm5lciBmYS1zcGluIGZhLTR4XCI+PC9pPjwvZGl2Pic7XG5cbiAgICAgICAgICAgIGlmICghdGhpcy5jb25maWcuY2FuY2VsIHx8IHRoaXMuY29uZmlnLmNhbmNlbC5sZW5ndGggPD0gMCkge1xuICAgICAgICAgICAgICAgIHRoaXMuY29uZmlnLmNhbmNlbCA9ICc8aSBjbGFzcz1cImZhIGZhLXRpbWVzXCI+PC9pPic7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gdGhpcy5jb25maWc7XG4gICAgfVxuXG4gICAgX2dldE1vZGFsTG9hZFVybCgpIHtcbiAgICAgICAgcmV0dXJuIHRoaXMuX2dldENvbmZpZygpLmxvYWRVcmw7XG4gICAgfVxuXG4gICAgX2dldE1vZGFsU3VibWl0VXJsKCkge1xuICAgICAgICByZXR1cm4gdGhpcy5zdWJtaXRVcmwgfHwgdGhpcy5fZ2V0Q29uZmlnKCkuc3VibWl0VXJsO1xuICAgIH1cblxuICAgIF9nZXRNb2RhbCgpIHtcbiAgICAgICAgaWYgKG51bGwgPT09IHRoaXMuJG1vZGFsKSB7XG4gICAgICAgICAgICBjb25zdCBjb25maWcgPSB0aGlzLl9nZXRDb25maWcoKTtcbiAgICAgICAgICAgIHRoaXMuJG1vZGFsID0gJChgXG4gICAgICAgICAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsIGZhZGVcIiByb2xlPVwiZGlhbG9nXCI+XG4gICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJtb2RhbC1kaWFsb2dcIj5cbiAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJtb2RhbC1jb250ZW50XCI+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWhlYWRlclwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzcz1cImNsb3NlXCIgZGF0YS1kaXNtaXNzPVwibW9kYWxcIj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxpIGNsYXNzPVwiZmEgZmEtdGltZXNcIj48L2k+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvYnV0dG9uPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8aDQgY2xhc3M9XCJtb2RhbC10aXRsZVwiPiR7Y29uZmlnLnRpdGxlfTwvaDQ+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWJvZHlcIj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPHA+JHtjb25maWcubWVzc2FnZX08L3A+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWZvb3RlclwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzcz1cImpzLWNhbmNlbCBidG4gYnRuLWRlZmF1bHRcIiBkYXRhLWRpc21pc3M9XCJtb2RhbFwiPiR7Y29uZmlnLmNhbmNlbH08L2J1dHRvbj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgIGApLm1vZGFsKHtcbiAgICAgICAgICAgICAgICBzaG93OiBmYWxzZSxcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIHRoaXMuJG1vZGFsO1xuICAgIH1cblxuICAgIF9zdWJtaXQoKSB7XG4gICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICB0eXBlOiAnUE9TVCcsXG4gICAgICAgICAgICB1cmw6IHRoaXMuX2dldE1vZGFsU3VibWl0VXJsKCksXG4gICAgICAgICAgICBkYXRhIDogdGhpcy4kZm9ybS5zZXJpYWxpemUoKSxcbiAgICAgICAgICAgIGhlYWRlcnM6IHsnWC1SZXF1ZXN0ZWQtV2l0aCc6ICdYTUxIdHRwUmVxdWVzdCd9XG4gICAgICAgIH0pLnRoZW4oKHJlc3BvbnNlKSA9PiB7XG4gICAgICAgICAgICB0aGlzLl9oYW5kbGVSZXNwb25zZShyZXNwb25zZSk7XG4gICAgICAgIH0pXG4gICAgfVxuXG4gICAgX2hhbmRsZVJlc3BvbnNlKHJlc3BvbnNlKSB7XG4gICAgICAgIHN1cGVyLl9oYW5kbGVSZXNwb25zZShyZXNwb25zZSk7XG4gICAgICAgIGlmIChyZXNwb25zZS50eXBlID09PSAgJ2NvbnRlbnQnKSB7XG4gICAgICAgICAgICBjb25zdCAkZm9ybSA9IHRoaXMuJG1vZGFsLmZpbmQoJ2Zvcm0nKS5maXJzdCgpO1xuICAgICAgICAgICAgdGhpcy4kZm9ybSA9ICRmb3JtO1xuICAgICAgICAgICAgaWYoJGZvcm0gJiYgJGZvcm0uYXR0cignYWN0aW9uJykpIHtcbiAgICAgICAgICAgICAgICB0aGlzLnN1Ym1pdFVybCA9ICRmb3JtLmF0dHIoJ2FjdGlvbicpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgY29uc3QgZXZlbnQgPSBuZXcgQ3VzdG9tRXZlbnQoJ21iLWZvcm0tdXBkYXRlJywgeyBkZXRhaWw6IHtjb250YWluZXI6ICRmb3JtfSB9KTtcbiAgICAgICAgICAgIGRvY3VtZW50LmRpc3BhdGNoRXZlbnQoZXZlbnQpO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgbG9hZCgpIHtcbiAgICAgICAgc3VwZXIubG9hZCgpO1xuXG4gICAgICAgIGxldCBsb2FkVXJsID0gdGhpcy5fZ2V0TW9kYWxMb2FkVXJsKCk7XG4gICAgICAgIGlmIChsb2FkVXJsKSB7XG4gICAgICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgICAgICAgIHVybDogbG9hZFVybCxcbiAgICAgICAgICAgICAgICBoZWFkZXJzOiB7J1gtUmVxdWVzdGVkLVdpdGgnOiAnWE1MSHR0cFJlcXVlc3QnfVxuICAgICAgICAgICAgfSkudGhlbigocmVzcG9uc2UpID0+IHtcbiAgICAgICAgICAgICAgICB0aGlzLl9oYW5kbGVSZXNwb25zZShyZXNwb25zZSk7XG4gICAgICAgICAgICB9KVxuICAgICAgICB9XG4gICAgfVxufVxuXG5leHBvcnQgZGVmYXVsdCBNb2RhbEZvcm07Il0sIm5hbWVzIjpbIkFic3RyYWN0TW9kYWwiLCJjb25zdHJ1Y3RvciIsImVsZW1lbnQiLCIkZWxlbWVudCIsIiQiLCIkbW9kYWwiLCJjb25maWciLCJvbiIsImUiLCJwcmV2ZW50RGVmYXVsdCIsImxvYWQiLCJfZ2V0Q29uZmlnIiwiZGF0YSIsIl9nZXRNb2RhbCIsInRpdGxlIiwibWVzc2FnZSIsImNhbmNlbCIsInN1Ym1pdCIsIm1vZGFsIiwic2hvdyIsIl9jbG9zZWQiLCJyZW1vdmUiLCJfc3VibWl0IiwiX2hhbmRsZVJlc3BvbnNlIiwicmVzcG9uc2UiLCJyZXNwb25zZVR5cGUiLCJ0eXBlIiwid2luZG93IiwibG9jYXRpb24iLCJyZWxvYWQiLCJocmVmIiwidXJsIiwib3BlbiIsIiRyZXNwb25zZSIsImNvbnRlbnQiLCJwYXJ0aWFscyIsImZpbHRlciIsImpvaW4iLCJsZW5ndGgiLCJzZWxlY3RvciIsImZpcnN0IiwiZmluZCIsImh0bWwiLCJjdXJyZW50VGFyZ2V0IiwiYWRkQ2xhc3MiLCJiaW5kIiwiTW9kYWxGb3JtIiwic3VibWl0VXJsIiwiJGZvcm0iLCJfZ2V0TW9kYWxMb2FkVXJsIiwibG9hZFVybCIsIl9nZXRNb2RhbFN1Ym1pdFVybCIsImFqYXgiLCJzZXJpYWxpemUiLCJoZWFkZXJzIiwidGhlbiIsImF0dHIiLCJldmVudCIsIkN1c3RvbUV2ZW50IiwiZGV0YWlsIiwiY29udGFpbmVyIiwiZG9jdW1lbnQiLCJkaXNwYXRjaEV2ZW50Il0sInNvdXJjZVJvb3QiOiIifQ==