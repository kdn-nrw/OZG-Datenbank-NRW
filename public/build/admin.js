(self["webpackChunkkdn_ozg"] = self["webpackChunkkdn_ozg"] || []).push([["admin"],{

/***/ "./assets/js/admin.js":
/*!****************************!*\
  !*** ./assets/js/admin.js ***!
  \****************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
// replacement of require("@babel/polyfill");
__webpack_require__.e(/*! AMD require */ "vendors-node_modules_core-js_stable_index_js-node_modules_regenerator-runtime_runtime_js").then(function() {[__webpack_require__(/*! core-js/stable */ "./node_modules/core-js/stable/index.js"), __webpack_require__(/*! regenerator-runtime/runtime */ "./node_modules/regenerator-runtime/runtime.js")];})['catch'](__webpack_require__.oe); // any CSS you require will output into a single css file (app.css in this case)


__webpack_require__(/*! ../css/admin.scss */ "./assets/css/admin.scss"); // jQuery is included globally outside of webpack!


 //global.$ = $;

const appOnReady = function () {
  let frontendBody = document.querySelector('.app-fe');

  if (frontendBody) {
    __webpack_require__.e(/*! import() */ "assets_js_modules_frontend_js").then(__webpack_require__.t.bind(__webpack_require__, /*! ./modules/frontend */ "./assets/js/modules/frontend.js", 23)).then(_ref => {
      let {
        default: appFrontend
      } = _ref;
      appFrontend.setUp();
    }).catch(error => {
      console.log('An error occurred while loading the frontend component', error);
    });

    __webpack_require__(/*! ./modules/bootstrap.offcanvas */ "./assets/js/modules/bootstrap.offcanvas.js");
  }

  let chartContainers = document.querySelectorAll('.mb-chart-container');

  if (chartContainers.length > 0) {
    Promise.all(/*! import() */[__webpack_require__.e("vendors-node_modules_chart_js_dist_Chart_js"), __webpack_require__.e("assets_js_modules_chart_js")]).then(__webpack_require__.t.bind(__webpack_require__, /*! ./modules/chart */ "./assets/js/modules/chart.js", 23)).then(_ref2 => {
      let {
        default: appChart
      } = _ref2;
      appChart.setUpList(chartContainers);
    }).catch(error => {
      console.log('An error occurred while loading the chart component', error);
    });
  }

  let advancedSelectElements = document.querySelectorAll('select.js-advanced-select');

  if (advancedSelectElements.length > 0) {
    __webpack_require__.e(/*! import() */ "assets_js_modules_advanced-select_js").then(__webpack_require__.t.bind(__webpack_require__, /*! ./modules/advanced-select */ "./assets/js/modules/advanced-select.js", 23)).then(_ref3 => {
      let {
        default: appAdvanceSelect
      } = _ref3;
      appAdvanceSelect.setUpList(advancedSelectElements);
    }).catch(error => {
      console.log('An error occurred while loading the advanced-select component', error);
    });
  }

  let filterAddLinks = document.querySelectorAll('.js-filter-add');
  let filterSelection = document.getElementById("navbar-filter-selection");

  if (filterAddLinks.length > 0 || filterSelection) {
    __webpack_require__.e(/*! import() */ "assets_js_modules_filter_js").then(__webpack_require__.t.bind(__webpack_require__, /*! ./modules/filter */ "./assets/js/modules/filter.js", 23)).then(_ref4 => {
      let {
        default: appFilter
      } = _ref4;
      appFilter.setUpAddLinksList(filterAddLinks);
      appFilter.setUpFilterSelectionList(filterSelection);
    }).catch(error => {
      console.log('An error occurred while loading the filter component', error);
    });
  }

  let modalForms = document.querySelectorAll('.js-modal-form');

  if (modalForms.length > 0) {
    __webpack_require__.e(/*! import() */ "assets_js_modules_modal-form_js").then(__webpack_require__.bind(__webpack_require__, /*! ./modules/modal-form */ "./assets/js/modules/modal-form.js")).then(_ref5 => {
      let {
        default: ModalForm
      } = _ref5;

      for (let i = 0, n = modalForms.length; i < n; i++) {
        new ModalForm(modalForms[i]);
      }
    }).catch(error => {
      console.log('An error occurred while loading the form component', error);
    });
  }

  let formContainers = document.querySelectorAll('.sonata-ba-form');

  if (formContainers.length > 0 && typeof Admin !== "undefined") {
    __webpack_require__.e(/*! import() */ "assets_js_modules_form_js").then(__webpack_require__.t.bind(__webpack_require__, /*! ./modules/form */ "./assets/js/modules/form.js", 23)).then(_ref6 => {
      let {
        default: appForm
      } = _ref6;
      appForm.setUpList(formContainers);
    }).catch(error => {
      console.log('An error occurred while loading the form component', error);
    });
  }

  __webpack_require__.e(/*! import() */ "assets_js_modules_common_js").then(__webpack_require__.t.bind(__webpack_require__, /*! ./modules/common */ "./assets/js/modules/common.js", 23)).then(_ref7 => {
    let {
      default: appCommon
    } = _ref7;
    appCommon.init();
  }).catch(error => {
    console.log('An error occurred while loading the common component', error);
  });
};

if (document.readyState === "complete" || document.readyState !== "loading" && !document.documentElement.doScroll) {
  appOnReady();
} else {
  document.addEventListener("DOMContentLoaded", appOnReady);
}

/***/ }),

/***/ "./assets/js/modules/bootstrap.offcanvas.js":
/*!**************************************************!*\
  !*** ./assets/js/modules/bootstrap.offcanvas.js ***!
  \**************************************************/
/***/ (function() {

(function () {
  var __bind = function (fn, me) {
    return function () {
      return fn.apply(me, arguments);
    };
  };

  (function ($, window) {
    var Offcanvas, OffcanvasDropdown, OffcanvasTouch;

    OffcanvasDropdown = function () {
      function OffcanvasDropdown(element) {
        this.element = element;
        this._clickEvent = __bind(this._clickEvent, this);
        this.element = $(this.element);
        this.nav = this.element.closest(".nav");
        this.dropdown = this.element.parent().find(".dropdown-menu");
        this.element.on('click', this._clickEvent);
        this.nav.closest('.navbar-offcanvas').on('click', function (_this) {
          return function () {
            if (_this.dropdown.is('.shown')) {
              return _this.dropdown.removeClass('shown').closest('.open').removeClass('open');
            }
          };
        }(this));
      }

      OffcanvasDropdown.prototype._clickEvent = function (e) {
        if (!this.dropdown.hasClass('shown')) {
          e.preventDefault();
        }

        e.stopPropagation();
        $('.dropdown-toggle').not(this.element).closest('.open').removeClass('open').find('.dropdown-menu').removeClass('shown');
        this.dropdown.toggleClass("shown");
        return this.element.parent().toggleClass('open');
      };

      return OffcanvasDropdown;
    }();

    OffcanvasTouch = function () {
      function OffcanvasTouch(button, element, location, offcanvas) {
        this.button = button;
        this.element = element;
        this.location = location;
        this.offcanvas = offcanvas;
        this._getFade = __bind(this._getFade, this);
        this._getCss = __bind(this._getCss, this);
        this._touchEnd = __bind(this._touchEnd, this);
        this._touchMove = __bind(this._touchMove, this);
        this._touchStart = __bind(this._touchStart, this);
        this.endThreshold = 130;
        this.startThreshold = this.element.hasClass('navbar-offcanvas-right') ? $("body").outerWidth() - 60 : 20;
        this.maxStartThreshold = this.element.hasClass('navbar-offcanvas-right') ? $("body").outerWidth() - 20 : 60;
        this.currentX = 0;
        this.fade = this.element.hasClass('navbar-offcanvas-fade') ? true : false;
        $(document).on("touchstart", this._touchStart);
        $(document).on("touchmove", this._touchMove);
        $(document).on("touchend", this._touchEnd);
      }

      OffcanvasTouch.prototype._touchStart = function (e) {
        this.startX = e.originalEvent.touches[0].pageX;

        if (this.element.is('.in')) {
          return this.element.height($(window).outerHeight());
        }
      };

      OffcanvasTouch.prototype._touchMove = function (e) {
        var x;

        if ($(e.target).parents('.navbar-offcanvas').length > 0) {
          return true;
        }

        if (this.startX > this.startThreshold && this.startX < this.maxStartThreshold) {
          e.preventDefault();
          x = e.originalEvent.touches[0].pageX - this.startX;
          x = this.element.hasClass('navbar-offcanvas-right') ? -x : x;

          if (Math.abs(x) < this.element.outerWidth()) {
            this.element.css(this._getCss(x));
            return this.element.css(this._getFade(x));
          }
        } else if (this.element.hasClass('in')) {
          e.preventDefault();
          x = e.originalEvent.touches[0].pageX + (this.currentX - this.startX);
          x = this.element.hasClass('navbar-offcanvas-right') ? -x : x;

          if (Math.abs(x) < this.element.outerWidth()) {
            this.element.css(this._getCss(x));
            return this.element.css(this._getFade(x));
          }
        }
      };

      OffcanvasTouch.prototype._touchEnd = function (e) {
        var end, sendEvents, x;

        if ($(e.target).parents('.navbar-offcanvas').length > 0) {
          return true;
        }

        sendEvents = false;
        x = e.originalEvent.changedTouches[0].pageX;

        if (Math.abs(x) === this.startX) {
          return;
        }

        end = this.element.hasClass('navbar-offcanvas-right') ? Math.abs(x) > this.endThreshold + 50 : x < this.endThreshold + 50;

        if (this.element.hasClass('in') && end) {
          this.currentX = 0;
          this.element.removeClass('in').css(this._clearCss());
          this.button.removeClass('is-open');
          sendEvents = true;
        } else if (Math.abs(x - this.startX) > this.endThreshold && this.startX > this.startThreshold && this.startX < this.maxStartThreshold) {
          this.currentX = this.element.hasClass('navbar-offcanvas-right') ? -this.element.outerWidth() : this.element.outerWidth();
          this.element.toggleClass('in').css(this._clearCss());
          this.button.toggleClass('is-open');
          sendEvents = true;
        } else {
          this.element.css(this._clearCss());
        }

        return this.offcanvas.bodyOverflow(sendEvents);
      };

      OffcanvasTouch.prototype._getCss = function (x) {
        x = this.element.hasClass('navbar-offcanvas-right') ? -x : x;
        return {
          "-webkit-transform": "translate3d(" + x + "px, 0px, 0px)",
          "-webkit-transition-duration": "0s",
          "-moz-transform": "translate3d(" + x + "px, 0px, 0px)",
          "-moz-transition": "0s",
          "-o-transform": "translate3d(" + x + "px, 0px, 0px)",
          "-o-transition": "0s",
          "transform": "translate3d(" + x + "px, 0px, 0px)",
          "transition": "0s"
        };
      };

      OffcanvasTouch.prototype._getFade = function (x) {
        if (this.fade) {
          return {
            "opacity": x / this.element.outerWidth()
          };
        } else {
          return {};
        }
      };

      OffcanvasTouch.prototype._clearCss = function () {
        return {
          "-webkit-transform": "",
          "-webkit-transition-duration": "",
          "-moz-transform": "",
          "-moz-transition": "",
          "-o-transform": "",
          "-o-transition": "",
          "transform": "",
          "transition": "",
          "opacity": ""
        };
      };

      return OffcanvasTouch;
    }();

    window.Offcanvas = Offcanvas = function () {
      function Offcanvas(element) {
        var t, target;
        this.element = element;
        this.bodyOverflow = __bind(this.bodyOverflow, this);
        this._sendEventsAfter = __bind(this._sendEventsAfter, this);
        this._sendEventsBefore = __bind(this._sendEventsBefore, this);
        this._documentClicked = __bind(this._documentClicked, this);
        this._close = __bind(this._close, this);
        this._open = __bind(this._open, this);
        this._clicked = __bind(this._clicked, this);
        this._navbarHeight = __bind(this._navbarHeight, this);
        target = this.element.attr('data-target') ? this.element.attr('data-target') : false;

        if (target) {
          this.target = $(target);

          if (this.target.length && !this.target.hasClass('js-offcanvas-done')) {
            this.element.addClass('js-offcanvas-has-events');
            this.location = this.target.hasClass("navbar-offcanvas-right") ? "right" : "left";
            this.target.addClass(this._transformSupported() ? "offcanvas-transform js-offcanvas-done" : "offcanvas-position js-offcanvas-done");
            this.target.data('offcanvas', this);
            this.element.on("click", this._clicked);
            this.target.on('transitionend', function (_this) {
              return function () {
                if (_this.target.is(':not(.in)')) {
                  return _this.target.height('');
                }
              };
            }(this));
            $(document).on("click", this._documentClicked);

            if (this.target.hasClass('navbar-offcanvas-touch')) {
              t = new OffcanvasTouch(this.element, this.target, this.location, this);
            }

            this.target.find(".dropdown-toggle").each(function () {
              var d;
              return d = new OffcanvasDropdown(this);
            });
            this.target.on('offcanvas.toggle', function (_this) {
              return function (e) {
                return _this._clicked(e);
              };
            }(this));
            this.target.on('offcanvas.close', function (_this) {
              return function (e) {
                return _this._close(e);
              };
            }(this));
            this.target.on('offcanvas.open', function (_this) {
              return function (e) {
                return _this._open(e);
              };
            }(this));
          }
        } else {
          console.warn('Offcanvas: `data-target` attribute must be present.');
        }
      }

      Offcanvas.prototype._navbarHeight = function () {
        if (this.target.is('.in')) {
          return this.target.height($(window).outerHeight());
        }
      };

      Offcanvas.prototype._clicked = function (e) {
        e.preventDefault();

        this._sendEventsBefore();

        $(".navbar-offcanvas").not(this.target).trigger('offcanvas.close');
        this.target.toggleClass('in');
        this.element.toggleClass('is-open');

        this._navbarHeight();

        return this.bodyOverflow();
      };

      Offcanvas.prototype._open = function (e) {
        e.preventDefault();

        if (this.target.is('.in')) {
          return;
        }

        this._sendEventsBefore();

        this.target.addClass('in');
        this.element.addClass('is-open');

        this._navbarHeight();

        return this.bodyOverflow();
      };

      Offcanvas.prototype._close = function (e) {
        e.preventDefault();

        if (this.target.is(':not(.in)')) {
          return;
        }

        this._sendEventsBefore();

        this.target.removeClass('in');
        this.element.removeClass('is-open');

        this._navbarHeight();

        return this.bodyOverflow();
      };

      Offcanvas.prototype._documentClicked = function (e) {
        var clickedEl;
        clickedEl = $(e.target);

        if (!clickedEl.hasClass('offcanvas-toggle') && clickedEl.parents('.offcanvas-toggle').length === 0 && clickedEl.parents('.navbar-offcanvas').length === 0 && !clickedEl.hasClass('navbar-offcanvas')) {
          if (this.target.hasClass('in')) {
            e.preventDefault();

            this._sendEventsBefore();

            this.target.removeClass('in');
            this.element.removeClass('is-open');

            this._navbarHeight();

            return this.bodyOverflow();
          }
        }
      };

      Offcanvas.prototype._sendEventsBefore = function () {
        if (this.target.hasClass('in')) {
          return this.target.trigger('hide.bs.offcanvas');
        } else {
          return this.target.trigger('show.bs.offcanvas');
        }
      };

      Offcanvas.prototype._sendEventsAfter = function () {
        if (this.target.hasClass('in')) {
          return this.target.trigger('shown.bs.offcanvas');
        } else {
          return this.target.trigger('hidden.bs.offcanvas');
        }
      };

      Offcanvas.prototype.bodyOverflow = function (events) {
        if (events == null) {
          events = true;
        }

        if (this.target.is('.in')) {
          $('body').addClass('offcanvas-stop-scrolling');
        } else {
          $('body').removeClass('offcanvas-stop-scrolling');
        }

        if (events) {
          return this._sendEventsAfter();
        }
      };

      Offcanvas.prototype._transformSupported = function () {
        var asSupport, el, regex, translate3D;
        el = document.createElement('div');
        translate3D = "translate3d(0px, 0px, 0px)";
        regex = /translate3d\(0px, 0px, 0px\)/g;
        el.style.cssText = "-webkit-transform: " + translate3D + "; -moz-transform: " + translate3D + "; -o-transform: " + translate3D + "; transform: " + translate3D;
        asSupport = el.style.cssText.match(regex);
        return asSupport.length != null;
      };

      return Offcanvas;
    }();

    $.fn.bsOffcanvas = function () {
      return this.each(function () {
        return new Offcanvas($(this));
      });
    };

    return $(function () {
      $('[data-toggle="offcanvas"]').each(function () {
        return $(this).bsOffcanvas();
      });
      $(window).on('resize', function () {
        $('.navbar-offcanvas.in').each(function () {
          return $(this).height('').removeClass('in');
        });
        $('.offcanvas-toggle').removeClass('is-open');
        return $('body').removeClass('offcanvas-stop-scrolling');
      });
      return $('.offcanvas-toggle').each(function () {
        return $(this).on('click', function (e) {
          var el, selector;

          if (!$(this).hasClass('js-offcanvas-has-events')) {
            selector = $(this).attr('data-target');
            el = $(selector);

            if (el) {
              el.height('');
              el.removeClass('in');
              return $('body').css({
                overflow: '',
                position: ''
              });
            }
          }
        });
      });
    });
  })(window.jQuery, window);
}).call(this);

/***/ }),

/***/ "./assets/css/admin.scss":
/*!*******************************!*\
  !*** ./assets/css/admin.scss ***!
  \*******************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "jquery":
/*!*************************!*\
  !*** external "jQuery" ***!
  \*************************/
/***/ (function(module) {

"use strict";
module.exports = jQuery;

/***/ }),

/***/ "moment":
/*!********************************!*\
  !*** external "window.moment" ***!
  \********************************/
/***/ (function(module) {

"use strict";
if(typeof window.moment === 'undefined') { var e = new Error("Cannot find module 'window.moment'"); e.code = 'MODULE_NOT_FOUND'; throw e; }

module.exports = window.moment;

/***/ })

},
/******/ function(__webpack_require__) { // webpackRuntimeModules
/******/ var __webpack_exec__ = function(moduleId) { return __webpack_require__(__webpack_require__.s = moduleId); }
/******/ var __webpack_exports__ = (__webpack_exec__("./assets/js/admin.js"));
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiYWRtaW4uanMiLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7Ozs7O0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQUEsc0pBQVMsQ0FBQyxtRkFBRCxFQUFtQix1R0FBbkIsQ0FBRixvQ0FBUCxFQUVBOzs7QUFDQUEsbUJBQU8sQ0FBQyxrREFBRCxDQUFQLEVBRUE7OztDQUVBOztBQUNBLE1BQU1FLFVBQVUsR0FBRyxZQUFXO0VBQzFCLElBQUlDLFlBQVksR0FBR0MsUUFBUSxDQUFDQyxhQUFULENBQXVCLFNBQXZCLENBQW5COztFQUNBLElBQUlGLFlBQUosRUFBa0I7SUFDZCw4TEFBNkJHLElBQTdCLENBQWtDLFFBQThCO01BQUEsSUFBN0I7UUFBRUMsT0FBTyxFQUFFQztNQUFYLENBQTZCO01BQzVEQSxXQUFXLENBQUNDLEtBQVo7SUFFSCxDQUhELEVBR0dDLEtBSEgsQ0FHU0MsS0FBSyxJQUFJO01BQ2RDLE9BQU8sQ0FBQ0MsR0FBUixDQUFZLHdEQUFaLEVBQXNFRixLQUF0RTtJQUNILENBTEQ7O0lBTUFYLG1CQUFPLENBQUMsaUZBQUQsQ0FBUDtFQUNIOztFQUNELElBQUljLGVBQWUsR0FBR1YsUUFBUSxDQUFDVyxnQkFBVCxDQUEwQixxQkFBMUIsQ0FBdEI7O0VBQ0EsSUFBSUQsZUFBZSxDQUFDRSxNQUFoQixHQUF5QixDQUE3QixFQUFnQztJQUM1Qix5UUFBMEJWLElBQTFCLENBQStCLFNBQTJCO01BQUEsSUFBMUI7UUFBRUMsT0FBTyxFQUFFVTtNQUFYLENBQTBCO01BQ3REQSxRQUFRLENBQUNDLFNBQVQsQ0FBbUJKLGVBQW5CO0lBRUgsQ0FIRCxFQUdHSixLQUhILENBR1NDLEtBQUssSUFBSTtNQUNkQyxPQUFPLENBQUNDLEdBQVIsQ0FBWSxxREFBWixFQUFtRUYsS0FBbkU7SUFDSCxDQUxEO0VBTUg7O0VBQ0QsSUFBSVEsc0JBQXNCLEdBQUdmLFFBQVEsQ0FBQ1csZ0JBQVQsQ0FBMEIsMkJBQTFCLENBQTdCOztFQUNBLElBQUlJLHNCQUFzQixDQUFDSCxNQUF2QixHQUFnQyxDQUFwQyxFQUF1QztJQUNuQyxtTkFBb0NWLElBQXBDLENBQXlDLFNBQW1DO01BQUEsSUFBbEM7UUFBRUMsT0FBTyxFQUFFYTtNQUFYLENBQWtDO01BQ3hFQSxnQkFBZ0IsQ0FBQ0YsU0FBakIsQ0FBMkJDLHNCQUEzQjtJQUVILENBSEQsRUFHR1QsS0FISCxDQUdTQyxLQUFLLElBQUk7TUFDZEMsT0FBTyxDQUFDQyxHQUFSLENBQVksK0RBQVosRUFBNkVGLEtBQTdFO0lBQ0gsQ0FMRDtFQU1IOztFQUNELElBQUlVLGNBQWMsR0FBR2pCLFFBQVEsQ0FBQ1csZ0JBQVQsQ0FBMEIsZ0JBQTFCLENBQXJCO0VBQ0EsSUFBSU8sZUFBZSxHQUFHbEIsUUFBUSxDQUFDbUIsY0FBVCxDQUF3Qix5QkFBeEIsQ0FBdEI7O0VBQ0EsSUFBSUYsY0FBYyxDQUFDTCxNQUFmLEdBQXdCLENBQXhCLElBQTZCTSxlQUFqQyxFQUFrRDtJQUM5Qyx3TEFBMkJoQixJQUEzQixDQUFnQyxTQUE0QjtNQUFBLElBQTNCO1FBQUVDLE9BQU8sRUFBRWlCO01BQVgsQ0FBMkI7TUFDeERBLFNBQVMsQ0FBQ0MsaUJBQVYsQ0FBNEJKLGNBQTVCO01BQ0FHLFNBQVMsQ0FBQ0Usd0JBQVYsQ0FBbUNKLGVBQW5DO0lBQ0gsQ0FIRCxFQUdHWixLQUhILENBR1NDLEtBQUssSUFBSTtNQUNkQyxPQUFPLENBQUNDLEdBQVIsQ0FBWSxzREFBWixFQUFvRUYsS0FBcEU7SUFDSCxDQUxEO0VBTUg7O0VBQ0QsSUFBSWdCLFVBQVUsR0FBR3ZCLFFBQVEsQ0FBQ1csZ0JBQVQsQ0FBMEIsZ0JBQTFCLENBQWpCOztFQUNBLElBQUlZLFVBQVUsQ0FBQ1gsTUFBWCxHQUFvQixDQUF4QixFQUEyQjtJQUN2Qiw4TEFBK0JWLElBQS9CLENBQW9DLFNBQTRCO01BQUEsSUFBM0I7UUFBRUMsT0FBTyxFQUFFcUI7TUFBWCxDQUEyQjs7TUFDNUQsS0FBSyxJQUFJQyxDQUFDLEdBQUcsQ0FBUixFQUFXQyxDQUFDLEdBQUdILFVBQVUsQ0FBQ1gsTUFBL0IsRUFBdUNhLENBQUMsR0FBR0MsQ0FBM0MsRUFBOENELENBQUMsRUFBL0MsRUFBbUQ7UUFDL0MsSUFBSUQsU0FBSixDQUFjRCxVQUFVLENBQUNFLENBQUQsQ0FBeEI7TUFDSDtJQUVKLENBTEQsRUFLR25CLEtBTEgsQ0FLU0MsS0FBSyxJQUFJO01BQ2RDLE9BQU8sQ0FBQ0MsR0FBUixDQUFZLG9EQUFaLEVBQWtFRixLQUFsRTtJQUNILENBUEQ7RUFRSDs7RUFDRCxJQUFJb0IsY0FBYyxHQUFHM0IsUUFBUSxDQUFDVyxnQkFBVCxDQUEwQixpQkFBMUIsQ0FBckI7O0VBQ0EsSUFBSWdCLGNBQWMsQ0FBQ2YsTUFBZixHQUF3QixDQUF4QixJQUE2QixPQUFPZ0IsS0FBUCxLQUFpQixXQUFsRCxFQUErRDtJQUMzRCxrTEFBeUIxQixJQUF6QixDQUE4QixTQUEwQjtNQUFBLElBQXpCO1FBQUVDLE9BQU8sRUFBRTBCO01BQVgsQ0FBeUI7TUFDcERBLE9BQU8sQ0FBQ2YsU0FBUixDQUFrQmEsY0FBbEI7SUFFSCxDQUhELEVBR0dyQixLQUhILENBR1NDLEtBQUssSUFBSTtNQUNkQyxPQUFPLENBQUNDLEdBQVIsQ0FBWSxvREFBWixFQUFrRUYsS0FBbEU7SUFDSCxDQUxEO0VBTUg7O0VBQ0Qsd0xBQTJCTCxJQUEzQixDQUFnQyxTQUE0QjtJQUFBLElBQTNCO01BQUVDLE9BQU8sRUFBRTJCO0lBQVgsQ0FBMkI7SUFDeERBLFNBQVMsQ0FBQ0MsSUFBVjtFQUVILENBSEQsRUFHR3pCLEtBSEgsQ0FHU0MsS0FBSyxJQUFJO0lBQ2RDLE9BQU8sQ0FBQ0MsR0FBUixDQUFZLHNEQUFaLEVBQW9FRixLQUFwRTtFQUNILENBTEQ7QUFNSCxDQWpFRDs7QUFtRUEsSUFDSVAsUUFBUSxDQUFDZ0MsVUFBVCxLQUF3QixVQUF4QixJQUNDaEMsUUFBUSxDQUFDZ0MsVUFBVCxLQUF3QixTQUF4QixJQUFxQyxDQUFDaEMsUUFBUSxDQUFDaUMsZUFBVCxDQUF5QkMsUUFGcEUsRUFHRTtFQUNFcEMsVUFBVTtBQUNiLENBTEQsTUFLTztFQUNIRSxRQUFRLENBQUNtQyxnQkFBVCxDQUEwQixrQkFBMUIsRUFBOENyQyxVQUE5QztBQUNIOzs7Ozs7Ozs7O0FDNUZELENBQUMsWUFBVztFQUNWLElBQUlzQyxNQUFNLEdBQUcsVUFBU0MsRUFBVCxFQUFhQyxFQUFiLEVBQWdCO0lBQUUsT0FBTyxZQUFVO01BQUUsT0FBT0QsRUFBRSxDQUFDRSxLQUFILENBQVNELEVBQVQsRUFBYUUsU0FBYixDQUFQO0lBQWlDLENBQXBEO0VBQXVELENBQXRGOztFQUVBLENBQUMsVUFBUzNDLENBQVQsRUFBWTRDLE1BQVosRUFBb0I7SUFDbkIsSUFBSUMsU0FBSixFQUFlQyxpQkFBZixFQUFrQ0MsY0FBbEM7O0lBQ0FELGlCQUFpQixHQUFJLFlBQVc7TUFDOUIsU0FBU0EsaUJBQVQsQ0FBMkJFLE9BQTNCLEVBQW9DO1FBQ2xDLEtBQUtBLE9BQUwsR0FBZUEsT0FBZjtRQUNBLEtBQUtDLFdBQUwsR0FBbUJWLE1BQU0sQ0FBQyxLQUFLVSxXQUFOLEVBQW1CLElBQW5CLENBQXpCO1FBQ0EsS0FBS0QsT0FBTCxHQUFlaEQsQ0FBQyxDQUFDLEtBQUtnRCxPQUFOLENBQWhCO1FBQ0EsS0FBS0UsR0FBTCxHQUFXLEtBQUtGLE9BQUwsQ0FBYUcsT0FBYixDQUFxQixNQUFyQixDQUFYO1FBQ0EsS0FBS0MsUUFBTCxHQUFnQixLQUFLSixPQUFMLENBQWFLLE1BQWIsR0FBc0JDLElBQXRCLENBQTJCLGdCQUEzQixDQUFoQjtRQUNBLEtBQUtOLE9BQUwsQ0FBYU8sRUFBYixDQUFnQixPQUFoQixFQUF5QixLQUFLTixXQUE5QjtRQUNBLEtBQUtDLEdBQUwsQ0FBU0MsT0FBVCxDQUFpQixtQkFBakIsRUFBc0NJLEVBQXRDLENBQXlDLE9BQXpDLEVBQW1ELFVBQVNDLEtBQVQsRUFBZ0I7VUFDakUsT0FBTyxZQUFXO1lBQ2hCLElBQUlBLEtBQUssQ0FBQ0osUUFBTixDQUFlSyxFQUFmLENBQWtCLFFBQWxCLENBQUosRUFBaUM7Y0FDL0IsT0FBT0QsS0FBSyxDQUFDSixRQUFOLENBQWVNLFdBQWYsQ0FBMkIsT0FBM0IsRUFBb0NQLE9BQXBDLENBQTRDLE9BQTVDLEVBQXFETyxXQUFyRCxDQUFpRSxNQUFqRSxDQUFQO1lBQ0Q7VUFDRixDQUpEO1FBS0QsQ0FOaUQsQ0FNL0MsSUFOK0MsQ0FBbEQ7TUFPRDs7TUFFRFosaUJBQWlCLENBQUNhLFNBQWxCLENBQTRCVixXQUE1QixHQUEwQyxVQUFTVyxDQUFULEVBQVk7UUFDcEQsSUFBSSxDQUFDLEtBQUtSLFFBQUwsQ0FBY1MsUUFBZCxDQUF1QixPQUF2QixDQUFMLEVBQXNDO1VBQ3BDRCxDQUFDLENBQUNFLGNBQUY7UUFDRDs7UUFDREYsQ0FBQyxDQUFDRyxlQUFGO1FBQ0EvRCxDQUFDLENBQUMsa0JBQUQsQ0FBRCxDQUFzQmdFLEdBQXRCLENBQTBCLEtBQUtoQixPQUEvQixFQUF3Q0csT0FBeEMsQ0FBZ0QsT0FBaEQsRUFBeURPLFdBQXpELENBQXFFLE1BQXJFLEVBQTZFSixJQUE3RSxDQUFrRixnQkFBbEYsRUFBb0dJLFdBQXBHLENBQWdILE9BQWhIO1FBQ0EsS0FBS04sUUFBTCxDQUFjYSxXQUFkLENBQTBCLE9BQTFCO1FBQ0EsT0FBTyxLQUFLakIsT0FBTCxDQUFhSyxNQUFiLEdBQXNCWSxXQUF0QixDQUFrQyxNQUFsQyxDQUFQO01BQ0QsQ0FSRDs7TUFVQSxPQUFPbkIsaUJBQVA7SUFFRCxDQTdCbUIsRUFBcEI7O0lBOEJBQyxjQUFjLEdBQUksWUFBVztNQUMzQixTQUFTQSxjQUFULENBQXdCbUIsTUFBeEIsRUFBZ0NsQixPQUFoQyxFQUF5Q21CLFFBQXpDLEVBQW1EQyxTQUFuRCxFQUE4RDtRQUM1RCxLQUFLRixNQUFMLEdBQWNBLE1BQWQ7UUFDQSxLQUFLbEIsT0FBTCxHQUFlQSxPQUFmO1FBQ0EsS0FBS21CLFFBQUwsR0FBZ0JBLFFBQWhCO1FBQ0EsS0FBS0MsU0FBTCxHQUFpQkEsU0FBakI7UUFDQSxLQUFLQyxRQUFMLEdBQWdCOUIsTUFBTSxDQUFDLEtBQUs4QixRQUFOLEVBQWdCLElBQWhCLENBQXRCO1FBQ0EsS0FBS0MsT0FBTCxHQUFlL0IsTUFBTSxDQUFDLEtBQUsrQixPQUFOLEVBQWUsSUFBZixDQUFyQjtRQUNBLEtBQUtDLFNBQUwsR0FBaUJoQyxNQUFNLENBQUMsS0FBS2dDLFNBQU4sRUFBaUIsSUFBakIsQ0FBdkI7UUFDQSxLQUFLQyxVQUFMLEdBQWtCakMsTUFBTSxDQUFDLEtBQUtpQyxVQUFOLEVBQWtCLElBQWxCLENBQXhCO1FBQ0EsS0FBS0MsV0FBTCxHQUFtQmxDLE1BQU0sQ0FBQyxLQUFLa0MsV0FBTixFQUFtQixJQUFuQixDQUF6QjtRQUNBLEtBQUtDLFlBQUwsR0FBb0IsR0FBcEI7UUFDQSxLQUFLQyxjQUFMLEdBQXNCLEtBQUszQixPQUFMLENBQWFhLFFBQWIsQ0FBc0Isd0JBQXRCLElBQWtEN0QsQ0FBQyxDQUFDLE1BQUQsQ0FBRCxDQUFVNEUsVUFBVixLQUF5QixFQUEzRSxHQUFnRixFQUF0RztRQUNBLEtBQUtDLGlCQUFMLEdBQXlCLEtBQUs3QixPQUFMLENBQWFhLFFBQWIsQ0FBc0Isd0JBQXRCLElBQWtEN0QsQ0FBQyxDQUFDLE1BQUQsQ0FBRCxDQUFVNEUsVUFBVixLQUF5QixFQUEzRSxHQUFnRixFQUF6RztRQUNBLEtBQUtFLFFBQUwsR0FBZ0IsQ0FBaEI7UUFDQSxLQUFLQyxJQUFMLEdBQVksS0FBSy9CLE9BQUwsQ0FBYWEsUUFBYixDQUFzQix1QkFBdEIsSUFBaUQsSUFBakQsR0FBd0QsS0FBcEU7UUFDQTdELENBQUMsQ0FBQ0csUUFBRCxDQUFELENBQVlvRCxFQUFaLENBQWUsWUFBZixFQUE2QixLQUFLa0IsV0FBbEM7UUFDQXpFLENBQUMsQ0FBQ0csUUFBRCxDQUFELENBQVlvRCxFQUFaLENBQWUsV0FBZixFQUE0QixLQUFLaUIsVUFBakM7UUFDQXhFLENBQUMsQ0FBQ0csUUFBRCxDQUFELENBQVlvRCxFQUFaLENBQWUsVUFBZixFQUEyQixLQUFLZ0IsU0FBaEM7TUFDRDs7TUFFRHhCLGNBQWMsQ0FBQ1ksU0FBZixDQUF5QmMsV0FBekIsR0FBdUMsVUFBU2IsQ0FBVCxFQUFZO1FBQ2pELEtBQUtvQixNQUFMLEdBQWNwQixDQUFDLENBQUNxQixhQUFGLENBQWdCQyxPQUFoQixDQUF3QixDQUF4QixFQUEyQkMsS0FBekM7O1FBQ0EsSUFBSSxLQUFLbkMsT0FBTCxDQUFhUyxFQUFiLENBQWdCLEtBQWhCLENBQUosRUFBNEI7VUFDMUIsT0FBTyxLQUFLVCxPQUFMLENBQWFvQyxNQUFiLENBQW9CcEYsQ0FBQyxDQUFDNEMsTUFBRCxDQUFELENBQVV5QyxXQUFWLEVBQXBCLENBQVA7UUFDRDtNQUNGLENBTEQ7O01BT0F0QyxjQUFjLENBQUNZLFNBQWYsQ0FBeUJhLFVBQXpCLEdBQXNDLFVBQVNaLENBQVQsRUFBWTtRQUNoRCxJQUFJMEIsQ0FBSjs7UUFDQSxJQUFJdEYsQ0FBQyxDQUFDNEQsQ0FBQyxDQUFDMkIsTUFBSCxDQUFELENBQVlDLE9BQVosQ0FBb0IsbUJBQXBCLEVBQXlDekUsTUFBekMsR0FBa0QsQ0FBdEQsRUFBeUQ7VUFDdkQsT0FBTyxJQUFQO1FBQ0Q7O1FBQ0QsSUFBSSxLQUFLaUUsTUFBTCxHQUFjLEtBQUtMLGNBQW5CLElBQXFDLEtBQUtLLE1BQUwsR0FBYyxLQUFLSCxpQkFBNUQsRUFBK0U7VUFDN0VqQixDQUFDLENBQUNFLGNBQUY7VUFDQXdCLENBQUMsR0FBRzFCLENBQUMsQ0FBQ3FCLGFBQUYsQ0FBZ0JDLE9BQWhCLENBQXdCLENBQXhCLEVBQTJCQyxLQUEzQixHQUFtQyxLQUFLSCxNQUE1QztVQUNBTSxDQUFDLEdBQUcsS0FBS3RDLE9BQUwsQ0FBYWEsUUFBYixDQUFzQix3QkFBdEIsSUFBa0QsQ0FBQ3lCLENBQW5ELEdBQXVEQSxDQUEzRDs7VUFDQSxJQUFJRyxJQUFJLENBQUNDLEdBQUwsQ0FBU0osQ0FBVCxJQUFjLEtBQUt0QyxPQUFMLENBQWE0QixVQUFiLEVBQWxCLEVBQTZDO1lBQzNDLEtBQUs1QixPQUFMLENBQWEyQyxHQUFiLENBQWlCLEtBQUtyQixPQUFMLENBQWFnQixDQUFiLENBQWpCO1lBQ0EsT0FBTyxLQUFLdEMsT0FBTCxDQUFhMkMsR0FBYixDQUFpQixLQUFLdEIsUUFBTCxDQUFjaUIsQ0FBZCxDQUFqQixDQUFQO1VBQ0Q7UUFDRixDQVJELE1BUU8sSUFBSSxLQUFLdEMsT0FBTCxDQUFhYSxRQUFiLENBQXNCLElBQXRCLENBQUosRUFBaUM7VUFDdENELENBQUMsQ0FBQ0UsY0FBRjtVQUNBd0IsQ0FBQyxHQUFHMUIsQ0FBQyxDQUFDcUIsYUFBRixDQUFnQkMsT0FBaEIsQ0FBd0IsQ0FBeEIsRUFBMkJDLEtBQTNCLElBQW9DLEtBQUtMLFFBQUwsR0FBZ0IsS0FBS0UsTUFBekQsQ0FBSjtVQUNBTSxDQUFDLEdBQUcsS0FBS3RDLE9BQUwsQ0FBYWEsUUFBYixDQUFzQix3QkFBdEIsSUFBa0QsQ0FBQ3lCLENBQW5ELEdBQXVEQSxDQUEzRDs7VUFDQSxJQUFJRyxJQUFJLENBQUNDLEdBQUwsQ0FBU0osQ0FBVCxJQUFjLEtBQUt0QyxPQUFMLENBQWE0QixVQUFiLEVBQWxCLEVBQTZDO1lBQzNDLEtBQUs1QixPQUFMLENBQWEyQyxHQUFiLENBQWlCLEtBQUtyQixPQUFMLENBQWFnQixDQUFiLENBQWpCO1lBQ0EsT0FBTyxLQUFLdEMsT0FBTCxDQUFhMkMsR0FBYixDQUFpQixLQUFLdEIsUUFBTCxDQUFjaUIsQ0FBZCxDQUFqQixDQUFQO1VBQ0Q7UUFDRjtNQUNGLENBdEJEOztNQXdCQXZDLGNBQWMsQ0FBQ1ksU0FBZixDQUF5QlksU0FBekIsR0FBcUMsVUFBU1gsQ0FBVCxFQUFZO1FBQy9DLElBQUlnQyxHQUFKLEVBQVNDLFVBQVQsRUFBcUJQLENBQXJCOztRQUNBLElBQUl0RixDQUFDLENBQUM0RCxDQUFDLENBQUMyQixNQUFILENBQUQsQ0FBWUMsT0FBWixDQUFvQixtQkFBcEIsRUFBeUN6RSxNQUF6QyxHQUFrRCxDQUF0RCxFQUF5RDtVQUN2RCxPQUFPLElBQVA7UUFDRDs7UUFDRDhFLFVBQVUsR0FBRyxLQUFiO1FBQ0FQLENBQUMsR0FBRzFCLENBQUMsQ0FBQ3FCLGFBQUYsQ0FBZ0JhLGNBQWhCLENBQStCLENBQS9CLEVBQWtDWCxLQUF0Qzs7UUFDQSxJQUFJTSxJQUFJLENBQUNDLEdBQUwsQ0FBU0osQ0FBVCxNQUFnQixLQUFLTixNQUF6QixFQUFpQztVQUMvQjtRQUNEOztRQUNEWSxHQUFHLEdBQUcsS0FBSzVDLE9BQUwsQ0FBYWEsUUFBYixDQUFzQix3QkFBdEIsSUFBa0Q0QixJQUFJLENBQUNDLEdBQUwsQ0FBU0osQ0FBVCxJQUFlLEtBQUtaLFlBQUwsR0FBb0IsRUFBckYsR0FBMkZZLENBQUMsR0FBSSxLQUFLWixZQUFMLEdBQW9CLEVBQTFIOztRQUNBLElBQUksS0FBSzFCLE9BQUwsQ0FBYWEsUUFBYixDQUFzQixJQUF0QixLQUErQitCLEdBQW5DLEVBQXdDO1VBQ3RDLEtBQUtkLFFBQUwsR0FBZ0IsQ0FBaEI7VUFDQSxLQUFLOUIsT0FBTCxDQUFhVSxXQUFiLENBQXlCLElBQXpCLEVBQStCaUMsR0FBL0IsQ0FBbUMsS0FBS0ksU0FBTCxFQUFuQztVQUNBLEtBQUs3QixNQUFMLENBQVlSLFdBQVosQ0FBd0IsU0FBeEI7VUFDQW1DLFVBQVUsR0FBRyxJQUFiO1FBQ0QsQ0FMRCxNQUtPLElBQUlKLElBQUksQ0FBQ0MsR0FBTCxDQUFTSixDQUFDLEdBQUcsS0FBS04sTUFBbEIsSUFBNEIsS0FBS04sWUFBakMsSUFBaUQsS0FBS00sTUFBTCxHQUFjLEtBQUtMLGNBQXBFLElBQXNGLEtBQUtLLE1BQUwsR0FBYyxLQUFLSCxpQkFBN0csRUFBZ0k7VUFDckksS0FBS0MsUUFBTCxHQUFnQixLQUFLOUIsT0FBTCxDQUFhYSxRQUFiLENBQXNCLHdCQUF0QixJQUFrRCxDQUFDLEtBQUtiLE9BQUwsQ0FBYTRCLFVBQWIsRUFBbkQsR0FBK0UsS0FBSzVCLE9BQUwsQ0FBYTRCLFVBQWIsRUFBL0Y7VUFDQSxLQUFLNUIsT0FBTCxDQUFhaUIsV0FBYixDQUF5QixJQUF6QixFQUErQjBCLEdBQS9CLENBQW1DLEtBQUtJLFNBQUwsRUFBbkM7VUFDQSxLQUFLN0IsTUFBTCxDQUFZRCxXQUFaLENBQXdCLFNBQXhCO1VBQ0E0QixVQUFVLEdBQUcsSUFBYjtRQUNELENBTE0sTUFLQTtVQUNMLEtBQUs3QyxPQUFMLENBQWEyQyxHQUFiLENBQWlCLEtBQUtJLFNBQUwsRUFBakI7UUFDRDs7UUFDRCxPQUFPLEtBQUszQixTQUFMLENBQWU0QixZQUFmLENBQTRCSCxVQUE1QixDQUFQO01BQ0QsQ0F6QkQ7O01BMkJBOUMsY0FBYyxDQUFDWSxTQUFmLENBQXlCVyxPQUF6QixHQUFtQyxVQUFTZ0IsQ0FBVCxFQUFZO1FBQzdDQSxDQUFDLEdBQUcsS0FBS3RDLE9BQUwsQ0FBYWEsUUFBYixDQUFzQix3QkFBdEIsSUFBa0QsQ0FBQ3lCLENBQW5ELEdBQXVEQSxDQUEzRDtRQUNBLE9BQU87VUFDTCxxQkFBcUIsaUJBQWlCQSxDQUFqQixHQUFxQixlQURyQztVQUVMLCtCQUErQixJQUYxQjtVQUdMLGtCQUFrQixpQkFBaUJBLENBQWpCLEdBQXFCLGVBSGxDO1VBSUwsbUJBQW1CLElBSmQ7VUFLTCxnQkFBZ0IsaUJBQWlCQSxDQUFqQixHQUFxQixlQUxoQztVQU1MLGlCQUFpQixJQU5aO1VBT0wsYUFBYSxpQkFBaUJBLENBQWpCLEdBQXFCLGVBUDdCO1VBUUwsY0FBYztRQVJULENBQVA7TUFVRCxDQVpEOztNQWNBdkMsY0FBYyxDQUFDWSxTQUFmLENBQXlCVSxRQUF6QixHQUFvQyxVQUFTaUIsQ0FBVCxFQUFZO1FBQzlDLElBQUksS0FBS1AsSUFBVCxFQUFlO1VBQ2IsT0FBTztZQUNMLFdBQVdPLENBQUMsR0FBRyxLQUFLdEMsT0FBTCxDQUFhNEIsVUFBYjtVQURWLENBQVA7UUFHRCxDQUpELE1BSU87VUFDTCxPQUFPLEVBQVA7UUFDRDtNQUNGLENBUkQ7O01BVUE3QixjQUFjLENBQUNZLFNBQWYsQ0FBeUJvQyxTQUF6QixHQUFxQyxZQUFXO1FBQzlDLE9BQU87VUFDTCxxQkFBcUIsRUFEaEI7VUFFTCwrQkFBK0IsRUFGMUI7VUFHTCxrQkFBa0IsRUFIYjtVQUlMLG1CQUFtQixFQUpkO1VBS0wsZ0JBQWdCLEVBTFg7VUFNTCxpQkFBaUIsRUFOWjtVQU9MLGFBQWEsRUFQUjtVQVFMLGNBQWMsRUFSVDtVQVNMLFdBQVc7UUFUTixDQUFQO01BV0QsQ0FaRDs7TUFjQSxPQUFPaEQsY0FBUDtJQUVELENBdkhnQixFQUFqQjs7SUF3SEFILE1BQU0sQ0FBQ0MsU0FBUCxHQUFtQkEsU0FBUyxHQUFJLFlBQVc7TUFDekMsU0FBU0EsU0FBVCxDQUFtQkcsT0FBbkIsRUFBNEI7UUFDMUIsSUFBSWlELENBQUosRUFBT1YsTUFBUDtRQUNBLEtBQUt2QyxPQUFMLEdBQWVBLE9BQWY7UUFDQSxLQUFLZ0QsWUFBTCxHQUFvQnpELE1BQU0sQ0FBQyxLQUFLeUQsWUFBTixFQUFvQixJQUFwQixDQUExQjtRQUNBLEtBQUtFLGdCQUFMLEdBQXdCM0QsTUFBTSxDQUFDLEtBQUsyRCxnQkFBTixFQUF3QixJQUF4QixDQUE5QjtRQUNBLEtBQUtDLGlCQUFMLEdBQXlCNUQsTUFBTSxDQUFDLEtBQUs0RCxpQkFBTixFQUF5QixJQUF6QixDQUEvQjtRQUNBLEtBQUtDLGdCQUFMLEdBQXdCN0QsTUFBTSxDQUFDLEtBQUs2RCxnQkFBTixFQUF3QixJQUF4QixDQUE5QjtRQUNBLEtBQUtDLE1BQUwsR0FBYzlELE1BQU0sQ0FBQyxLQUFLOEQsTUFBTixFQUFjLElBQWQsQ0FBcEI7UUFDQSxLQUFLQyxLQUFMLEdBQWEvRCxNQUFNLENBQUMsS0FBSytELEtBQU4sRUFBYSxJQUFiLENBQW5CO1FBQ0EsS0FBS0MsUUFBTCxHQUFnQmhFLE1BQU0sQ0FBQyxLQUFLZ0UsUUFBTixFQUFnQixJQUFoQixDQUF0QjtRQUNBLEtBQUtDLGFBQUwsR0FBcUJqRSxNQUFNLENBQUMsS0FBS2lFLGFBQU4sRUFBcUIsSUFBckIsQ0FBM0I7UUFDQWpCLE1BQU0sR0FBRyxLQUFLdkMsT0FBTCxDQUFheUQsSUFBYixDQUFrQixhQUFsQixJQUFtQyxLQUFLekQsT0FBTCxDQUFheUQsSUFBYixDQUFrQixhQUFsQixDQUFuQyxHQUFzRSxLQUEvRTs7UUFDQSxJQUFJbEIsTUFBSixFQUFZO1VBQ1YsS0FBS0EsTUFBTCxHQUFjdkYsQ0FBQyxDQUFDdUYsTUFBRCxDQUFmOztVQUNBLElBQUksS0FBS0EsTUFBTCxDQUFZeEUsTUFBWixJQUFzQixDQUFDLEtBQUt3RSxNQUFMLENBQVkxQixRQUFaLENBQXFCLG1CQUFyQixDQUEzQixFQUFzRTtZQUNwRSxLQUFLYixPQUFMLENBQWEwRCxRQUFiLENBQXNCLHlCQUF0QjtZQUNBLEtBQUt2QyxRQUFMLEdBQWdCLEtBQUtvQixNQUFMLENBQVkxQixRQUFaLENBQXFCLHdCQUFyQixJQUFpRCxPQUFqRCxHQUEyRCxNQUEzRTtZQUNBLEtBQUswQixNQUFMLENBQVltQixRQUFaLENBQXFCLEtBQUtDLG1CQUFMLEtBQTZCLHVDQUE3QixHQUF1RSxzQ0FBNUY7WUFDQSxLQUFLcEIsTUFBTCxDQUFZcUIsSUFBWixDQUFpQixXQUFqQixFQUE4QixJQUE5QjtZQUNBLEtBQUs1RCxPQUFMLENBQWFPLEVBQWIsQ0FBZ0IsT0FBaEIsRUFBeUIsS0FBS2dELFFBQTlCO1lBQ0EsS0FBS2hCLE1BQUwsQ0FBWWhDLEVBQVosQ0FBZSxlQUFmLEVBQWlDLFVBQVNDLEtBQVQsRUFBZ0I7Y0FDL0MsT0FBTyxZQUFXO2dCQUNoQixJQUFJQSxLQUFLLENBQUMrQixNQUFOLENBQWE5QixFQUFiLENBQWdCLFdBQWhCLENBQUosRUFBa0M7a0JBQ2hDLE9BQU9ELEtBQUssQ0FBQytCLE1BQU4sQ0FBYUgsTUFBYixDQUFvQixFQUFwQixDQUFQO2dCQUNEO2NBQ0YsQ0FKRDtZQUtELENBTitCLENBTTdCLElBTjZCLENBQWhDO1lBT0FwRixDQUFDLENBQUNHLFFBQUQsQ0FBRCxDQUFZb0QsRUFBWixDQUFlLE9BQWYsRUFBd0IsS0FBSzZDLGdCQUE3Qjs7WUFDQSxJQUFJLEtBQUtiLE1BQUwsQ0FBWTFCLFFBQVosQ0FBcUIsd0JBQXJCLENBQUosRUFBb0Q7Y0FDbERvQyxDQUFDLEdBQUcsSUFBSWxELGNBQUosQ0FBbUIsS0FBS0MsT0FBeEIsRUFBaUMsS0FBS3VDLE1BQXRDLEVBQThDLEtBQUtwQixRQUFuRCxFQUE2RCxJQUE3RCxDQUFKO1lBQ0Q7O1lBQ0QsS0FBS29CLE1BQUwsQ0FBWWpDLElBQVosQ0FBaUIsa0JBQWpCLEVBQXFDdUQsSUFBckMsQ0FBMEMsWUFBVztjQUNuRCxJQUFJQyxDQUFKO2NBQ0EsT0FBT0EsQ0FBQyxHQUFHLElBQUloRSxpQkFBSixDQUFzQixJQUF0QixDQUFYO1lBQ0QsQ0FIRDtZQUlBLEtBQUt5QyxNQUFMLENBQVloQyxFQUFaLENBQWUsa0JBQWYsRUFBb0MsVUFBU0MsS0FBVCxFQUFnQjtjQUNsRCxPQUFPLFVBQVNJLENBQVQsRUFBWTtnQkFDakIsT0FBT0osS0FBSyxDQUFDK0MsUUFBTixDQUFlM0MsQ0FBZixDQUFQO2NBQ0QsQ0FGRDtZQUdELENBSmtDLENBSWhDLElBSmdDLENBQW5DO1lBS0EsS0FBSzJCLE1BQUwsQ0FBWWhDLEVBQVosQ0FBZSxpQkFBZixFQUFtQyxVQUFTQyxLQUFULEVBQWdCO2NBQ2pELE9BQU8sVUFBU0ksQ0FBVCxFQUFZO2dCQUNqQixPQUFPSixLQUFLLENBQUM2QyxNQUFOLENBQWF6QyxDQUFiLENBQVA7Y0FDRCxDQUZEO1lBR0QsQ0FKaUMsQ0FJL0IsSUFKK0IsQ0FBbEM7WUFLQSxLQUFLMkIsTUFBTCxDQUFZaEMsRUFBWixDQUFlLGdCQUFmLEVBQWtDLFVBQVNDLEtBQVQsRUFBZ0I7Y0FDaEQsT0FBTyxVQUFTSSxDQUFULEVBQVk7Z0JBQ2pCLE9BQU9KLEtBQUssQ0FBQzhDLEtBQU4sQ0FBWTFDLENBQVosQ0FBUDtjQUNELENBRkQ7WUFHRCxDQUpnQyxDQUk5QixJQUo4QixDQUFqQztVQUtEO1FBQ0YsQ0F2Q0QsTUF1Q087VUFDTGpELE9BQU8sQ0FBQ29HLElBQVIsQ0FBYSxxREFBYjtRQUNEO01BQ0Y7O01BRURsRSxTQUFTLENBQUNjLFNBQVYsQ0FBb0I2QyxhQUFwQixHQUFvQyxZQUFXO1FBQzdDLElBQUksS0FBS2pCLE1BQUwsQ0FBWTlCLEVBQVosQ0FBZSxLQUFmLENBQUosRUFBMkI7VUFDekIsT0FBTyxLQUFLOEIsTUFBTCxDQUFZSCxNQUFaLENBQW1CcEYsQ0FBQyxDQUFDNEMsTUFBRCxDQUFELENBQVV5QyxXQUFWLEVBQW5CLENBQVA7UUFDRDtNQUNGLENBSkQ7O01BTUF4QyxTQUFTLENBQUNjLFNBQVYsQ0FBb0I0QyxRQUFwQixHQUErQixVQUFTM0MsQ0FBVCxFQUFZO1FBQ3pDQSxDQUFDLENBQUNFLGNBQUY7O1FBQ0EsS0FBS3FDLGlCQUFMOztRQUNBbkcsQ0FBQyxDQUFDLG1CQUFELENBQUQsQ0FBdUJnRSxHQUF2QixDQUEyQixLQUFLdUIsTUFBaEMsRUFBd0N5QixPQUF4QyxDQUFnRCxpQkFBaEQ7UUFDQSxLQUFLekIsTUFBTCxDQUFZdEIsV0FBWixDQUF3QixJQUF4QjtRQUNBLEtBQUtqQixPQUFMLENBQWFpQixXQUFiLENBQXlCLFNBQXpCOztRQUNBLEtBQUt1QyxhQUFMOztRQUNBLE9BQU8sS0FBS1IsWUFBTCxFQUFQO01BQ0QsQ0FSRDs7TUFVQW5ELFNBQVMsQ0FBQ2MsU0FBVixDQUFvQjJDLEtBQXBCLEdBQTRCLFVBQVMxQyxDQUFULEVBQVk7UUFDdENBLENBQUMsQ0FBQ0UsY0FBRjs7UUFDQSxJQUFJLEtBQUt5QixNQUFMLENBQVk5QixFQUFaLENBQWUsS0FBZixDQUFKLEVBQTJCO1VBQ3pCO1FBQ0Q7O1FBQ0QsS0FBSzBDLGlCQUFMOztRQUNBLEtBQUtaLE1BQUwsQ0FBWW1CLFFBQVosQ0FBcUIsSUFBckI7UUFDQSxLQUFLMUQsT0FBTCxDQUFhMEQsUUFBYixDQUFzQixTQUF0Qjs7UUFDQSxLQUFLRixhQUFMOztRQUNBLE9BQU8sS0FBS1IsWUFBTCxFQUFQO01BQ0QsQ0FWRDs7TUFZQW5ELFNBQVMsQ0FBQ2MsU0FBVixDQUFvQjBDLE1BQXBCLEdBQTZCLFVBQVN6QyxDQUFULEVBQVk7UUFDdkNBLENBQUMsQ0FBQ0UsY0FBRjs7UUFDQSxJQUFJLEtBQUt5QixNQUFMLENBQVk5QixFQUFaLENBQWUsV0FBZixDQUFKLEVBQWlDO1VBQy9CO1FBQ0Q7O1FBQ0QsS0FBSzBDLGlCQUFMOztRQUNBLEtBQUtaLE1BQUwsQ0FBWTdCLFdBQVosQ0FBd0IsSUFBeEI7UUFDQSxLQUFLVixPQUFMLENBQWFVLFdBQWIsQ0FBeUIsU0FBekI7O1FBQ0EsS0FBSzhDLGFBQUw7O1FBQ0EsT0FBTyxLQUFLUixZQUFMLEVBQVA7TUFDRCxDQVZEOztNQVlBbkQsU0FBUyxDQUFDYyxTQUFWLENBQW9CeUMsZ0JBQXBCLEdBQXVDLFVBQVN4QyxDQUFULEVBQVk7UUFDakQsSUFBSXFELFNBQUo7UUFDQUEsU0FBUyxHQUFHakgsQ0FBQyxDQUFDNEQsQ0FBQyxDQUFDMkIsTUFBSCxDQUFiOztRQUNBLElBQUksQ0FBQzBCLFNBQVMsQ0FBQ3BELFFBQVYsQ0FBbUIsa0JBQW5CLENBQUQsSUFBMkNvRCxTQUFTLENBQUN6QixPQUFWLENBQWtCLG1CQUFsQixFQUF1Q3pFLE1BQXZDLEtBQWtELENBQTdGLElBQWtHa0csU0FBUyxDQUFDekIsT0FBVixDQUFrQixtQkFBbEIsRUFBdUN6RSxNQUF2QyxLQUFrRCxDQUFwSixJQUF5SixDQUFDa0csU0FBUyxDQUFDcEQsUUFBVixDQUFtQixrQkFBbkIsQ0FBOUosRUFBc007VUFDcE0sSUFBSSxLQUFLMEIsTUFBTCxDQUFZMUIsUUFBWixDQUFxQixJQUFyQixDQUFKLEVBQWdDO1lBQzlCRCxDQUFDLENBQUNFLGNBQUY7O1lBQ0EsS0FBS3FDLGlCQUFMOztZQUNBLEtBQUtaLE1BQUwsQ0FBWTdCLFdBQVosQ0FBd0IsSUFBeEI7WUFDQSxLQUFLVixPQUFMLENBQWFVLFdBQWIsQ0FBeUIsU0FBekI7O1lBQ0EsS0FBSzhDLGFBQUw7O1lBQ0EsT0FBTyxLQUFLUixZQUFMLEVBQVA7VUFDRDtRQUNGO01BQ0YsQ0FiRDs7TUFlQW5ELFNBQVMsQ0FBQ2MsU0FBVixDQUFvQndDLGlCQUFwQixHQUF3QyxZQUFXO1FBQ2pELElBQUksS0FBS1osTUFBTCxDQUFZMUIsUUFBWixDQUFxQixJQUFyQixDQUFKLEVBQWdDO1VBQzlCLE9BQU8sS0FBSzBCLE1BQUwsQ0FBWXlCLE9BQVosQ0FBb0IsbUJBQXBCLENBQVA7UUFDRCxDQUZELE1BRU87VUFDTCxPQUFPLEtBQUt6QixNQUFMLENBQVl5QixPQUFaLENBQW9CLG1CQUFwQixDQUFQO1FBQ0Q7TUFDRixDQU5EOztNQVFBbkUsU0FBUyxDQUFDYyxTQUFWLENBQW9CdUMsZ0JBQXBCLEdBQXVDLFlBQVc7UUFDaEQsSUFBSSxLQUFLWCxNQUFMLENBQVkxQixRQUFaLENBQXFCLElBQXJCLENBQUosRUFBZ0M7VUFDOUIsT0FBTyxLQUFLMEIsTUFBTCxDQUFZeUIsT0FBWixDQUFvQixvQkFBcEIsQ0FBUDtRQUNELENBRkQsTUFFTztVQUNMLE9BQU8sS0FBS3pCLE1BQUwsQ0FBWXlCLE9BQVosQ0FBb0IscUJBQXBCLENBQVA7UUFDRDtNQUNGLENBTkQ7O01BUUFuRSxTQUFTLENBQUNjLFNBQVYsQ0FBb0JxQyxZQUFwQixHQUFtQyxVQUFTa0IsTUFBVCxFQUFpQjtRQUNsRCxJQUFJQSxNQUFNLElBQUksSUFBZCxFQUFvQjtVQUNsQkEsTUFBTSxHQUFHLElBQVQ7UUFDRDs7UUFDRCxJQUFJLEtBQUszQixNQUFMLENBQVk5QixFQUFaLENBQWUsS0FBZixDQUFKLEVBQTJCO1VBQ3pCekQsQ0FBQyxDQUFDLE1BQUQsQ0FBRCxDQUFVMEcsUUFBVixDQUFtQiwwQkFBbkI7UUFDRCxDQUZELE1BRU87VUFDTDFHLENBQUMsQ0FBQyxNQUFELENBQUQsQ0FBVTBELFdBQVYsQ0FBc0IsMEJBQXRCO1FBQ0Q7O1FBQ0QsSUFBSXdELE1BQUosRUFBWTtVQUNWLE9BQU8sS0FBS2hCLGdCQUFMLEVBQVA7UUFDRDtNQUNGLENBWkQ7O01BY0FyRCxTQUFTLENBQUNjLFNBQVYsQ0FBb0JnRCxtQkFBcEIsR0FBMEMsWUFBVztRQUNuRCxJQUFJUSxTQUFKLEVBQWVDLEVBQWYsRUFBbUJDLEtBQW5CLEVBQTBCQyxXQUExQjtRQUNBRixFQUFFLEdBQUdqSCxRQUFRLENBQUNvSCxhQUFULENBQXVCLEtBQXZCLENBQUw7UUFDQUQsV0FBVyxHQUFHLDRCQUFkO1FBQ0FELEtBQUssR0FBRywrQkFBUjtRQUNBRCxFQUFFLENBQUNJLEtBQUgsQ0FBU0MsT0FBVCxHQUFtQix3QkFBd0JILFdBQXhCLEdBQXNDLG9CQUF0QyxHQUE2REEsV0FBN0QsR0FBMkUsa0JBQTNFLEdBQWdHQSxXQUFoRyxHQUE4RyxlQUE5RyxHQUFnSUEsV0FBbko7UUFDQUgsU0FBUyxHQUFHQyxFQUFFLENBQUNJLEtBQUgsQ0FBU0MsT0FBVCxDQUFpQkMsS0FBakIsQ0FBdUJMLEtBQXZCLENBQVo7UUFDQSxPQUFPRixTQUFTLENBQUNwRyxNQUFWLElBQW9CLElBQTNCO01BQ0QsQ0FSRDs7TUFVQSxPQUFPOEIsU0FBUDtJQUVELENBMUo4QixFQUEvQjs7SUEySkE3QyxDQUFDLENBQUN3QyxFQUFGLENBQUttRixXQUFMLEdBQW1CLFlBQVc7TUFDNUIsT0FBTyxLQUFLZCxJQUFMLENBQVUsWUFBVztRQUMxQixPQUFPLElBQUloRSxTQUFKLENBQWM3QyxDQUFDLENBQUMsSUFBRCxDQUFmLENBQVA7TUFDRCxDQUZNLENBQVA7SUFHRCxDQUpEOztJQUtBLE9BQU9BLENBQUMsQ0FBQyxZQUFXO01BQ2xCQSxDQUFDLENBQUMsMkJBQUQsQ0FBRCxDQUErQjZHLElBQS9CLENBQW9DLFlBQVc7UUFDN0MsT0FBTzdHLENBQUMsQ0FBQyxJQUFELENBQUQsQ0FBUTJILFdBQVIsRUFBUDtNQUNELENBRkQ7TUFHQTNILENBQUMsQ0FBQzRDLE1BQUQsQ0FBRCxDQUFVVyxFQUFWLENBQWEsUUFBYixFQUF1QixZQUFXO1FBQ2hDdkQsQ0FBQyxDQUFDLHNCQUFELENBQUQsQ0FBMEI2RyxJQUExQixDQUErQixZQUFXO1VBQ3hDLE9BQU83RyxDQUFDLENBQUMsSUFBRCxDQUFELENBQVFvRixNQUFSLENBQWUsRUFBZixFQUFtQjFCLFdBQW5CLENBQStCLElBQS9CLENBQVA7UUFDRCxDQUZEO1FBR0ExRCxDQUFDLENBQUMsbUJBQUQsQ0FBRCxDQUF1QjBELFdBQXZCLENBQW1DLFNBQW5DO1FBQ0EsT0FBTzFELENBQUMsQ0FBQyxNQUFELENBQUQsQ0FBVTBELFdBQVYsQ0FBc0IsMEJBQXRCLENBQVA7TUFDRCxDQU5EO01BT0EsT0FBTzFELENBQUMsQ0FBQyxtQkFBRCxDQUFELENBQXVCNkcsSUFBdkIsQ0FBNEIsWUFBVztRQUM1QyxPQUFPN0csQ0FBQyxDQUFDLElBQUQsQ0FBRCxDQUFRdUQsRUFBUixDQUFXLE9BQVgsRUFBb0IsVUFBU0ssQ0FBVCxFQUFZO1VBQ3JDLElBQUl3RCxFQUFKLEVBQVFRLFFBQVI7O1VBQ0EsSUFBSSxDQUFDNUgsQ0FBQyxDQUFDLElBQUQsQ0FBRCxDQUFRNkQsUUFBUixDQUFpQix5QkFBakIsQ0FBTCxFQUFrRDtZQUNoRCtELFFBQVEsR0FBRzVILENBQUMsQ0FBQyxJQUFELENBQUQsQ0FBUXlHLElBQVIsQ0FBYSxhQUFiLENBQVg7WUFDQVcsRUFBRSxHQUFHcEgsQ0FBQyxDQUFDNEgsUUFBRCxDQUFOOztZQUNBLElBQUlSLEVBQUosRUFBUTtjQUNOQSxFQUFFLENBQUNoQyxNQUFILENBQVUsRUFBVjtjQUNBZ0MsRUFBRSxDQUFDMUQsV0FBSCxDQUFlLElBQWY7Y0FDQSxPQUFPMUQsQ0FBQyxDQUFDLE1BQUQsQ0FBRCxDQUFVMkYsR0FBVixDQUFjO2dCQUNuQmtDLFFBQVEsRUFBRSxFQURTO2dCQUVuQkMsUUFBUSxFQUFFO2NBRlMsQ0FBZCxDQUFQO1lBSUQ7VUFDRjtRQUNGLENBZE0sQ0FBUDtNQWVELENBaEJNLENBQVA7SUFpQkQsQ0E1Qk8sQ0FBUjtFQTZCRCxDQXJWRCxFQXFWR2xGLE1BQU0sQ0FBQ21GLE1BclZWLEVBcVZrQm5GLE1BclZsQjtBQXVWRCxDQTFWRCxFQTBWR29GLElBMVZILENBMFZRLElBMVZSOzs7Ozs7Ozs7Ozs7QUNBQTs7Ozs7Ozs7Ozs7O0FDQUE7Ozs7Ozs7Ozs7O0FDQUEsMkNBQTJDLHlEQUF5RCw2QkFBNkI7O0FBRWpJIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8va2RuX296Zy8uL2Fzc2V0cy9qcy9hZG1pbi5qcyIsIndlYnBhY2s6Ly9rZG5fb3pnLy4vYXNzZXRzL2pzL21vZHVsZXMvYm9vdHN0cmFwLm9mZmNhbnZhcy5qcyIsIndlYnBhY2s6Ly9rZG5fb3pnLy4vYXNzZXRzL2Nzcy9hZG1pbi5zY3NzIiwid2VicGFjazovL2tkbl9vemcvZXh0ZXJuYWwgdmFyIFwialF1ZXJ5XCIiLCJ3ZWJwYWNrOi8va2RuX296Zy9leHRlcm5hbCB2YXIgXCJ3aW5kb3cubW9tZW50XCIiXSwic291cmNlc0NvbnRlbnQiOlsiLyoqXG4gKiBUaGlzIGZpbGUgaXMgcGFydCBvZiB0aGUgS0ROIE9aRyBwYWNrYWdlLlxuICpcbiAqIEBhdXRob3IgICAgR2VydCBIYW1tZXMgPGluZm9AZ2VydGhhbW1lcy5kZT5cbiAqIEBjb3B5cmlnaHQgMjAyMCBHZXJ0IEhhbW1lc1xuICpcbiAqIEZvciB0aGUgZnVsbCBjb3B5cmlnaHQgYW5kIGxpY2Vuc2UgaW5mb3JtYXRpb24sIHBsZWFzZSB2aWV3IHRoZSBMSUNFTlNFXG4gKiBmaWxlIHRoYXQgd2FzIGRpc3RyaWJ1dGVkIHdpdGggdGhpcyBzb3VyY2UgY29kZS5cbiAqL1xuLy8gcmVwbGFjZW1lbnQgb2YgcmVxdWlyZShcIkBiYWJlbC9wb2x5ZmlsbFwiKTtcbnJlcXVpcmUoIFtcImNvcmUtanMvc3RhYmxlXCIsICdyZWdlbmVyYXRvci1ydW50aW1lL3J1bnRpbWUnXSk7XG5cbi8vIGFueSBDU1MgeW91IHJlcXVpcmUgd2lsbCBvdXRwdXQgaW50byBhIHNpbmdsZSBjc3MgZmlsZSAoYXBwLmNzcyBpbiB0aGlzIGNhc2UpXG5yZXF1aXJlKCcuLi9jc3MvYWRtaW4uc2NzcycpO1xuXG4vLyBqUXVlcnkgaXMgaW5jbHVkZWQgZ2xvYmFsbHkgb3V0c2lkZSBvZiB3ZWJwYWNrIVxuaW1wb3J0ICQgZnJvbSAnanF1ZXJ5Jztcbi8vZ2xvYmFsLiQgPSAkO1xuY29uc3QgYXBwT25SZWFkeSA9IGZ1bmN0aW9uKCkge1xuICAgIGxldCBmcm9udGVuZEJvZHkgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcuYXBwLWZlJyk7XG4gICAgaWYgKGZyb250ZW5kQm9keSkge1xuICAgICAgICBpbXBvcnQoJy4vbW9kdWxlcy9mcm9udGVuZCcpLnRoZW4oKHsgZGVmYXVsdDogYXBwRnJvbnRlbmQgfSkgPT4ge1xuICAgICAgICAgICAgYXBwRnJvbnRlbmQuc2V0VXAoKTtcblxuICAgICAgICB9KS5jYXRjaChlcnJvciA9PiB7XG4gICAgICAgICAgICBjb25zb2xlLmxvZygnQW4gZXJyb3Igb2NjdXJyZWQgd2hpbGUgbG9hZGluZyB0aGUgZnJvbnRlbmQgY29tcG9uZW50JywgZXJyb3IpO1xuICAgICAgICB9KTtcbiAgICAgICAgcmVxdWlyZSgnLi9tb2R1bGVzL2Jvb3RzdHJhcC5vZmZjYW52YXMnKTtcbiAgICB9XG4gICAgbGV0IGNoYXJ0Q29udGFpbmVycyA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJy5tYi1jaGFydC1jb250YWluZXInKTtcbiAgICBpZiAoY2hhcnRDb250YWluZXJzLmxlbmd0aCA+IDApIHtcbiAgICAgICAgaW1wb3J0KCcuL21vZHVsZXMvY2hhcnQnKS50aGVuKCh7IGRlZmF1bHQ6IGFwcENoYXJ0IH0pID0+IHtcbiAgICAgICAgICAgIGFwcENoYXJ0LnNldFVwTGlzdChjaGFydENvbnRhaW5lcnMpO1xuXG4gICAgICAgIH0pLmNhdGNoKGVycm9yID0+IHtcbiAgICAgICAgICAgIGNvbnNvbGUubG9nKCdBbiBlcnJvciBvY2N1cnJlZCB3aGlsZSBsb2FkaW5nIHRoZSBjaGFydCBjb21wb25lbnQnLCBlcnJvcik7XG4gICAgICAgIH0pO1xuICAgIH1cbiAgICBsZXQgYWR2YW5jZWRTZWxlY3RFbGVtZW50cyA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJ3NlbGVjdC5qcy1hZHZhbmNlZC1zZWxlY3QnKTtcbiAgICBpZiAoYWR2YW5jZWRTZWxlY3RFbGVtZW50cy5sZW5ndGggPiAwKSB7XG4gICAgICAgIGltcG9ydCgnLi9tb2R1bGVzL2FkdmFuY2VkLXNlbGVjdCcpLnRoZW4oKHsgZGVmYXVsdDogYXBwQWR2YW5jZVNlbGVjdCB9KSA9PiB7XG4gICAgICAgICAgICBhcHBBZHZhbmNlU2VsZWN0LnNldFVwTGlzdChhZHZhbmNlZFNlbGVjdEVsZW1lbnRzKTtcblxuICAgICAgICB9KS5jYXRjaChlcnJvciA9PiB7XG4gICAgICAgICAgICBjb25zb2xlLmxvZygnQW4gZXJyb3Igb2NjdXJyZWQgd2hpbGUgbG9hZGluZyB0aGUgYWR2YW5jZWQtc2VsZWN0IGNvbXBvbmVudCcsIGVycm9yKTtcbiAgICAgICAgfSk7XG4gICAgfVxuICAgIGxldCBmaWx0ZXJBZGRMaW5rcyA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJy5qcy1maWx0ZXItYWRkJyk7XG4gICAgbGV0IGZpbHRlclNlbGVjdGlvbiA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKFwibmF2YmFyLWZpbHRlci1zZWxlY3Rpb25cIik7XG4gICAgaWYgKGZpbHRlckFkZExpbmtzLmxlbmd0aCA+IDAgfHwgZmlsdGVyU2VsZWN0aW9uKSB7XG4gICAgICAgIGltcG9ydCgnLi9tb2R1bGVzL2ZpbHRlcicpLnRoZW4oKHsgZGVmYXVsdDogYXBwRmlsdGVyIH0pID0+IHtcbiAgICAgICAgICAgIGFwcEZpbHRlci5zZXRVcEFkZExpbmtzTGlzdChmaWx0ZXJBZGRMaW5rcyk7XG4gICAgICAgICAgICBhcHBGaWx0ZXIuc2V0VXBGaWx0ZXJTZWxlY3Rpb25MaXN0KGZpbHRlclNlbGVjdGlvbik7XG4gICAgICAgIH0pLmNhdGNoKGVycm9yID0+IHtcbiAgICAgICAgICAgIGNvbnNvbGUubG9nKCdBbiBlcnJvciBvY2N1cnJlZCB3aGlsZSBsb2FkaW5nIHRoZSBmaWx0ZXIgY29tcG9uZW50JywgZXJyb3IpO1xuICAgICAgICB9KTtcbiAgICB9XG4gICAgbGV0IG1vZGFsRm9ybXMgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKCcuanMtbW9kYWwtZm9ybScpO1xuICAgIGlmIChtb2RhbEZvcm1zLmxlbmd0aCA+IDApIHtcbiAgICAgICAgaW1wb3J0KCcuL21vZHVsZXMvbW9kYWwtZm9ybScpLnRoZW4oKHsgZGVmYXVsdDogTW9kYWxGb3JtIH0pID0+IHtcbiAgICAgICAgICAgIGZvciAobGV0IGkgPSAwLCBuID0gbW9kYWxGb3Jtcy5sZW5ndGg7IGkgPCBuOyBpKyspIHtcbiAgICAgICAgICAgICAgICBuZXcgTW9kYWxGb3JtKG1vZGFsRm9ybXNbaV0pO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0pLmNhdGNoKGVycm9yID0+IHtcbiAgICAgICAgICAgIGNvbnNvbGUubG9nKCdBbiBlcnJvciBvY2N1cnJlZCB3aGlsZSBsb2FkaW5nIHRoZSBmb3JtIGNvbXBvbmVudCcsIGVycm9yKTtcbiAgICAgICAgfSk7XG4gICAgfVxuICAgIGxldCBmb3JtQ29udGFpbmVycyA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJy5zb25hdGEtYmEtZm9ybScpO1xuICAgIGlmIChmb3JtQ29udGFpbmVycy5sZW5ndGggPiAwICYmIHR5cGVvZiBBZG1pbiAhPT0gXCJ1bmRlZmluZWRcIikge1xuICAgICAgICBpbXBvcnQoJy4vbW9kdWxlcy9mb3JtJykudGhlbigoeyBkZWZhdWx0OiBhcHBGb3JtIH0pID0+IHtcbiAgICAgICAgICAgIGFwcEZvcm0uc2V0VXBMaXN0KGZvcm1Db250YWluZXJzKTtcblxuICAgICAgICB9KS5jYXRjaChlcnJvciA9PiB7XG4gICAgICAgICAgICBjb25zb2xlLmxvZygnQW4gZXJyb3Igb2NjdXJyZWQgd2hpbGUgbG9hZGluZyB0aGUgZm9ybSBjb21wb25lbnQnLCBlcnJvcik7XG4gICAgICAgIH0pO1xuICAgIH1cbiAgICBpbXBvcnQoJy4vbW9kdWxlcy9jb21tb24nKS50aGVuKCh7IGRlZmF1bHQ6IGFwcENvbW1vbiB9KSA9PiB7XG4gICAgICAgIGFwcENvbW1vbi5pbml0KCk7XG5cbiAgICB9KS5jYXRjaChlcnJvciA9PiB7XG4gICAgICAgIGNvbnNvbGUubG9nKCdBbiBlcnJvciBvY2N1cnJlZCB3aGlsZSBsb2FkaW5nIHRoZSBjb21tb24gY29tcG9uZW50JywgZXJyb3IpO1xuICAgIH0pO1xufTtcblxuaWYgKFxuICAgIGRvY3VtZW50LnJlYWR5U3RhdGUgPT09IFwiY29tcGxldGVcIiB8fFxuICAgIChkb2N1bWVudC5yZWFkeVN0YXRlICE9PSBcImxvYWRpbmdcIiAmJiAhZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50LmRvU2Nyb2xsKVxuKSB7XG4gICAgYXBwT25SZWFkeSgpO1xufSBlbHNlIHtcbiAgICBkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKFwiRE9NQ29udGVudExvYWRlZFwiLCBhcHBPblJlYWR5KTtcbn0iLCIoZnVuY3Rpb24oKSB7XG4gIHZhciBfX2JpbmQgPSBmdW5jdGlvbihmbiwgbWUpeyByZXR1cm4gZnVuY3Rpb24oKXsgcmV0dXJuIGZuLmFwcGx5KG1lLCBhcmd1bWVudHMpOyB9OyB9O1xuXG4gIChmdW5jdGlvbigkLCB3aW5kb3cpIHtcbiAgICB2YXIgT2ZmY2FudmFzLCBPZmZjYW52YXNEcm9wZG93biwgT2ZmY2FudmFzVG91Y2g7XG4gICAgT2ZmY2FudmFzRHJvcGRvd24gPSAoZnVuY3Rpb24oKSB7XG4gICAgICBmdW5jdGlvbiBPZmZjYW52YXNEcm9wZG93bihlbGVtZW50KSB7XG4gICAgICAgIHRoaXMuZWxlbWVudCA9IGVsZW1lbnQ7XG4gICAgICAgIHRoaXMuX2NsaWNrRXZlbnQgPSBfX2JpbmQodGhpcy5fY2xpY2tFdmVudCwgdGhpcyk7XG4gICAgICAgIHRoaXMuZWxlbWVudCA9ICQodGhpcy5lbGVtZW50KTtcbiAgICAgICAgdGhpcy5uYXYgPSB0aGlzLmVsZW1lbnQuY2xvc2VzdChcIi5uYXZcIik7XG4gICAgICAgIHRoaXMuZHJvcGRvd24gPSB0aGlzLmVsZW1lbnQucGFyZW50KCkuZmluZChcIi5kcm9wZG93bi1tZW51XCIpO1xuICAgICAgICB0aGlzLmVsZW1lbnQub24oJ2NsaWNrJywgdGhpcy5fY2xpY2tFdmVudCk7XG4gICAgICAgIHRoaXMubmF2LmNsb3Nlc3QoJy5uYXZiYXItb2ZmY2FudmFzJykub24oJ2NsaWNrJywgKGZ1bmN0aW9uKF90aGlzKSB7XG4gICAgICAgICAgcmV0dXJuIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgaWYgKF90aGlzLmRyb3Bkb3duLmlzKCcuc2hvd24nKSkge1xuICAgICAgICAgICAgICByZXR1cm4gX3RoaXMuZHJvcGRvd24ucmVtb3ZlQ2xhc3MoJ3Nob3duJykuY2xvc2VzdCgnLm9wZW4nKS5yZW1vdmVDbGFzcygnb3BlbicpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgIH07XG4gICAgICAgIH0pKHRoaXMpKTtcbiAgICAgIH1cblxuICAgICAgT2ZmY2FudmFzRHJvcGRvd24ucHJvdG90eXBlLl9jbGlja0V2ZW50ID0gZnVuY3Rpb24oZSkge1xuICAgICAgICBpZiAoIXRoaXMuZHJvcGRvd24uaGFzQ2xhc3MoJ3Nob3duJykpIHtcbiAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgIH1cbiAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcbiAgICAgICAgJCgnLmRyb3Bkb3duLXRvZ2dsZScpLm5vdCh0aGlzLmVsZW1lbnQpLmNsb3Nlc3QoJy5vcGVuJykucmVtb3ZlQ2xhc3MoJ29wZW4nKS5maW5kKCcuZHJvcGRvd24tbWVudScpLnJlbW92ZUNsYXNzKCdzaG93bicpO1xuICAgICAgICB0aGlzLmRyb3Bkb3duLnRvZ2dsZUNsYXNzKFwic2hvd25cIik7XG4gICAgICAgIHJldHVybiB0aGlzLmVsZW1lbnQucGFyZW50KCkudG9nZ2xlQ2xhc3MoJ29wZW4nKTtcbiAgICAgIH07XG5cbiAgICAgIHJldHVybiBPZmZjYW52YXNEcm9wZG93bjtcblxuICAgIH0pKCk7XG4gICAgT2ZmY2FudmFzVG91Y2ggPSAoZnVuY3Rpb24oKSB7XG4gICAgICBmdW5jdGlvbiBPZmZjYW52YXNUb3VjaChidXR0b24sIGVsZW1lbnQsIGxvY2F0aW9uLCBvZmZjYW52YXMpIHtcbiAgICAgICAgdGhpcy5idXR0b24gPSBidXR0b247XG4gICAgICAgIHRoaXMuZWxlbWVudCA9IGVsZW1lbnQ7XG4gICAgICAgIHRoaXMubG9jYXRpb24gPSBsb2NhdGlvbjtcbiAgICAgICAgdGhpcy5vZmZjYW52YXMgPSBvZmZjYW52YXM7XG4gICAgICAgIHRoaXMuX2dldEZhZGUgPSBfX2JpbmQodGhpcy5fZ2V0RmFkZSwgdGhpcyk7XG4gICAgICAgIHRoaXMuX2dldENzcyA9IF9fYmluZCh0aGlzLl9nZXRDc3MsIHRoaXMpO1xuICAgICAgICB0aGlzLl90b3VjaEVuZCA9IF9fYmluZCh0aGlzLl90b3VjaEVuZCwgdGhpcyk7XG4gICAgICAgIHRoaXMuX3RvdWNoTW92ZSA9IF9fYmluZCh0aGlzLl90b3VjaE1vdmUsIHRoaXMpO1xuICAgICAgICB0aGlzLl90b3VjaFN0YXJ0ID0gX19iaW5kKHRoaXMuX3RvdWNoU3RhcnQsIHRoaXMpO1xuICAgICAgICB0aGlzLmVuZFRocmVzaG9sZCA9IDEzMDtcbiAgICAgICAgdGhpcy5zdGFydFRocmVzaG9sZCA9IHRoaXMuZWxlbWVudC5oYXNDbGFzcygnbmF2YmFyLW9mZmNhbnZhcy1yaWdodCcpID8gJChcImJvZHlcIikub3V0ZXJXaWR0aCgpIC0gNjAgOiAyMDtcbiAgICAgICAgdGhpcy5tYXhTdGFydFRocmVzaG9sZCA9IHRoaXMuZWxlbWVudC5oYXNDbGFzcygnbmF2YmFyLW9mZmNhbnZhcy1yaWdodCcpID8gJChcImJvZHlcIikub3V0ZXJXaWR0aCgpIC0gMjAgOiA2MDtcbiAgICAgICAgdGhpcy5jdXJyZW50WCA9IDA7XG4gICAgICAgIHRoaXMuZmFkZSA9IHRoaXMuZWxlbWVudC5oYXNDbGFzcygnbmF2YmFyLW9mZmNhbnZhcy1mYWRlJykgPyB0cnVlIDogZmFsc2U7XG4gICAgICAgICQoZG9jdW1lbnQpLm9uKFwidG91Y2hzdGFydFwiLCB0aGlzLl90b3VjaFN0YXJ0KTtcbiAgICAgICAgJChkb2N1bWVudCkub24oXCJ0b3VjaG1vdmVcIiwgdGhpcy5fdG91Y2hNb3ZlKTtcbiAgICAgICAgJChkb2N1bWVudCkub24oXCJ0b3VjaGVuZFwiLCB0aGlzLl90b3VjaEVuZCk7XG4gICAgICB9XG5cbiAgICAgIE9mZmNhbnZhc1RvdWNoLnByb3RvdHlwZS5fdG91Y2hTdGFydCA9IGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgdGhpcy5zdGFydFggPSBlLm9yaWdpbmFsRXZlbnQudG91Y2hlc1swXS5wYWdlWDtcbiAgICAgICAgaWYgKHRoaXMuZWxlbWVudC5pcygnLmluJykpIHtcbiAgICAgICAgICByZXR1cm4gdGhpcy5lbGVtZW50LmhlaWdodCgkKHdpbmRvdykub3V0ZXJIZWlnaHQoKSk7XG4gICAgICAgIH1cbiAgICAgIH07XG5cbiAgICAgIE9mZmNhbnZhc1RvdWNoLnByb3RvdHlwZS5fdG91Y2hNb3ZlID0gZnVuY3Rpb24oZSkge1xuICAgICAgICB2YXIgeDtcbiAgICAgICAgaWYgKCQoZS50YXJnZXQpLnBhcmVudHMoJy5uYXZiYXItb2ZmY2FudmFzJykubGVuZ3RoID4gMCkge1xuICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICB9XG4gICAgICAgIGlmICh0aGlzLnN0YXJ0WCA+IHRoaXMuc3RhcnRUaHJlc2hvbGQgJiYgdGhpcy5zdGFydFggPCB0aGlzLm1heFN0YXJ0VGhyZXNob2xkKSB7XG4gICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgIHggPSBlLm9yaWdpbmFsRXZlbnQudG91Y2hlc1swXS5wYWdlWCAtIHRoaXMuc3RhcnRYO1xuICAgICAgICAgIHggPSB0aGlzLmVsZW1lbnQuaGFzQ2xhc3MoJ25hdmJhci1vZmZjYW52YXMtcmlnaHQnKSA/IC14IDogeDtcbiAgICAgICAgICBpZiAoTWF0aC5hYnMoeCkgPCB0aGlzLmVsZW1lbnQub3V0ZXJXaWR0aCgpKSB7XG4gICAgICAgICAgICB0aGlzLmVsZW1lbnQuY3NzKHRoaXMuX2dldENzcyh4KSk7XG4gICAgICAgICAgICByZXR1cm4gdGhpcy5lbGVtZW50LmNzcyh0aGlzLl9nZXRGYWRlKHgpKTtcbiAgICAgICAgICB9XG4gICAgICAgIH0gZWxzZSBpZiAodGhpcy5lbGVtZW50Lmhhc0NsYXNzKCdpbicpKSB7XG4gICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgIHggPSBlLm9yaWdpbmFsRXZlbnQudG91Y2hlc1swXS5wYWdlWCArICh0aGlzLmN1cnJlbnRYIC0gdGhpcy5zdGFydFgpO1xuICAgICAgICAgIHggPSB0aGlzLmVsZW1lbnQuaGFzQ2xhc3MoJ25hdmJhci1vZmZjYW52YXMtcmlnaHQnKSA/IC14IDogeDtcbiAgICAgICAgICBpZiAoTWF0aC5hYnMoeCkgPCB0aGlzLmVsZW1lbnQub3V0ZXJXaWR0aCgpKSB7XG4gICAgICAgICAgICB0aGlzLmVsZW1lbnQuY3NzKHRoaXMuX2dldENzcyh4KSk7XG4gICAgICAgICAgICByZXR1cm4gdGhpcy5lbGVtZW50LmNzcyh0aGlzLl9nZXRGYWRlKHgpKTtcbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgIH07XG5cbiAgICAgIE9mZmNhbnZhc1RvdWNoLnByb3RvdHlwZS5fdG91Y2hFbmQgPSBmdW5jdGlvbihlKSB7XG4gICAgICAgIHZhciBlbmQsIHNlbmRFdmVudHMsIHg7XG4gICAgICAgIGlmICgkKGUudGFyZ2V0KS5wYXJlbnRzKCcubmF2YmFyLW9mZmNhbnZhcycpLmxlbmd0aCA+IDApIHtcbiAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgfVxuICAgICAgICBzZW5kRXZlbnRzID0gZmFsc2U7XG4gICAgICAgIHggPSBlLm9yaWdpbmFsRXZlbnQuY2hhbmdlZFRvdWNoZXNbMF0ucGFnZVg7XG4gICAgICAgIGlmIChNYXRoLmFicyh4KSA9PT0gdGhpcy5zdGFydFgpIHtcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cbiAgICAgICAgZW5kID0gdGhpcy5lbGVtZW50Lmhhc0NsYXNzKCduYXZiYXItb2ZmY2FudmFzLXJpZ2h0JykgPyBNYXRoLmFicyh4KSA+ICh0aGlzLmVuZFRocmVzaG9sZCArIDUwKSA6IHggPCAodGhpcy5lbmRUaHJlc2hvbGQgKyA1MCk7XG4gICAgICAgIGlmICh0aGlzLmVsZW1lbnQuaGFzQ2xhc3MoJ2luJykgJiYgZW5kKSB7XG4gICAgICAgICAgdGhpcy5jdXJyZW50WCA9IDA7XG4gICAgICAgICAgdGhpcy5lbGVtZW50LnJlbW92ZUNsYXNzKCdpbicpLmNzcyh0aGlzLl9jbGVhckNzcygpKTtcbiAgICAgICAgICB0aGlzLmJ1dHRvbi5yZW1vdmVDbGFzcygnaXMtb3BlbicpO1xuICAgICAgICAgIHNlbmRFdmVudHMgPSB0cnVlO1xuICAgICAgICB9IGVsc2UgaWYgKE1hdGguYWJzKHggLSB0aGlzLnN0YXJ0WCkgPiB0aGlzLmVuZFRocmVzaG9sZCAmJiB0aGlzLnN0YXJ0WCA+IHRoaXMuc3RhcnRUaHJlc2hvbGQgJiYgdGhpcy5zdGFydFggPCB0aGlzLm1heFN0YXJ0VGhyZXNob2xkKSB7XG4gICAgICAgICAgdGhpcy5jdXJyZW50WCA9IHRoaXMuZWxlbWVudC5oYXNDbGFzcygnbmF2YmFyLW9mZmNhbnZhcy1yaWdodCcpID8gLXRoaXMuZWxlbWVudC5vdXRlcldpZHRoKCkgOiB0aGlzLmVsZW1lbnQub3V0ZXJXaWR0aCgpO1xuICAgICAgICAgIHRoaXMuZWxlbWVudC50b2dnbGVDbGFzcygnaW4nKS5jc3ModGhpcy5fY2xlYXJDc3MoKSk7XG4gICAgICAgICAgdGhpcy5idXR0b24udG9nZ2xlQ2xhc3MoJ2lzLW9wZW4nKTtcbiAgICAgICAgICBzZW5kRXZlbnRzID0gdHJ1ZTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICB0aGlzLmVsZW1lbnQuY3NzKHRoaXMuX2NsZWFyQ3NzKCkpO1xuICAgICAgICB9XG4gICAgICAgIHJldHVybiB0aGlzLm9mZmNhbnZhcy5ib2R5T3ZlcmZsb3coc2VuZEV2ZW50cyk7XG4gICAgICB9O1xuXG4gICAgICBPZmZjYW52YXNUb3VjaC5wcm90b3R5cGUuX2dldENzcyA9IGZ1bmN0aW9uKHgpIHtcbiAgICAgICAgeCA9IHRoaXMuZWxlbWVudC5oYXNDbGFzcygnbmF2YmFyLW9mZmNhbnZhcy1yaWdodCcpID8gLXggOiB4O1xuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgIFwiLXdlYmtpdC10cmFuc2Zvcm1cIjogXCJ0cmFuc2xhdGUzZChcIiArIHggKyBcInB4LCAwcHgsIDBweClcIixcbiAgICAgICAgICBcIi13ZWJraXQtdHJhbnNpdGlvbi1kdXJhdGlvblwiOiBcIjBzXCIsXG4gICAgICAgICAgXCItbW96LXRyYW5zZm9ybVwiOiBcInRyYW5zbGF0ZTNkKFwiICsgeCArIFwicHgsIDBweCwgMHB4KVwiLFxuICAgICAgICAgIFwiLW1vei10cmFuc2l0aW9uXCI6IFwiMHNcIixcbiAgICAgICAgICBcIi1vLXRyYW5zZm9ybVwiOiBcInRyYW5zbGF0ZTNkKFwiICsgeCArIFwicHgsIDBweCwgMHB4KVwiLFxuICAgICAgICAgIFwiLW8tdHJhbnNpdGlvblwiOiBcIjBzXCIsXG4gICAgICAgICAgXCJ0cmFuc2Zvcm1cIjogXCJ0cmFuc2xhdGUzZChcIiArIHggKyBcInB4LCAwcHgsIDBweClcIixcbiAgICAgICAgICBcInRyYW5zaXRpb25cIjogXCIwc1wiXG4gICAgICAgIH07XG4gICAgICB9O1xuXG4gICAgICBPZmZjYW52YXNUb3VjaC5wcm90b3R5cGUuX2dldEZhZGUgPSBmdW5jdGlvbih4KSB7XG4gICAgICAgIGlmICh0aGlzLmZhZGUpIHtcbiAgICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgXCJvcGFjaXR5XCI6IHggLyB0aGlzLmVsZW1lbnQub3V0ZXJXaWR0aCgpXG4gICAgICAgICAgfTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICByZXR1cm4ge307XG4gICAgICAgIH1cbiAgICAgIH07XG5cbiAgICAgIE9mZmNhbnZhc1RvdWNoLnByb3RvdHlwZS5fY2xlYXJDc3MgPSBmdW5jdGlvbigpIHtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICBcIi13ZWJraXQtdHJhbnNmb3JtXCI6IFwiXCIsXG4gICAgICAgICAgXCItd2Via2l0LXRyYW5zaXRpb24tZHVyYXRpb25cIjogXCJcIixcbiAgICAgICAgICBcIi1tb3otdHJhbnNmb3JtXCI6IFwiXCIsXG4gICAgICAgICAgXCItbW96LXRyYW5zaXRpb25cIjogXCJcIixcbiAgICAgICAgICBcIi1vLXRyYW5zZm9ybVwiOiBcIlwiLFxuICAgICAgICAgIFwiLW8tdHJhbnNpdGlvblwiOiBcIlwiLFxuICAgICAgICAgIFwidHJhbnNmb3JtXCI6IFwiXCIsXG4gICAgICAgICAgXCJ0cmFuc2l0aW9uXCI6IFwiXCIsXG4gICAgICAgICAgXCJvcGFjaXR5XCI6IFwiXCJcbiAgICAgICAgfTtcbiAgICAgIH07XG5cbiAgICAgIHJldHVybiBPZmZjYW52YXNUb3VjaDtcblxuICAgIH0pKCk7XG4gICAgd2luZG93Lk9mZmNhbnZhcyA9IE9mZmNhbnZhcyA9IChmdW5jdGlvbigpIHtcbiAgICAgIGZ1bmN0aW9uIE9mZmNhbnZhcyhlbGVtZW50KSB7XG4gICAgICAgIHZhciB0LCB0YXJnZXQ7XG4gICAgICAgIHRoaXMuZWxlbWVudCA9IGVsZW1lbnQ7XG4gICAgICAgIHRoaXMuYm9keU92ZXJmbG93ID0gX19iaW5kKHRoaXMuYm9keU92ZXJmbG93LCB0aGlzKTtcbiAgICAgICAgdGhpcy5fc2VuZEV2ZW50c0FmdGVyID0gX19iaW5kKHRoaXMuX3NlbmRFdmVudHNBZnRlciwgdGhpcyk7XG4gICAgICAgIHRoaXMuX3NlbmRFdmVudHNCZWZvcmUgPSBfX2JpbmQodGhpcy5fc2VuZEV2ZW50c0JlZm9yZSwgdGhpcyk7XG4gICAgICAgIHRoaXMuX2RvY3VtZW50Q2xpY2tlZCA9IF9fYmluZCh0aGlzLl9kb2N1bWVudENsaWNrZWQsIHRoaXMpO1xuICAgICAgICB0aGlzLl9jbG9zZSA9IF9fYmluZCh0aGlzLl9jbG9zZSwgdGhpcyk7XG4gICAgICAgIHRoaXMuX29wZW4gPSBfX2JpbmQodGhpcy5fb3BlbiwgdGhpcyk7XG4gICAgICAgIHRoaXMuX2NsaWNrZWQgPSBfX2JpbmQodGhpcy5fY2xpY2tlZCwgdGhpcyk7XG4gICAgICAgIHRoaXMuX25hdmJhckhlaWdodCA9IF9fYmluZCh0aGlzLl9uYXZiYXJIZWlnaHQsIHRoaXMpO1xuICAgICAgICB0YXJnZXQgPSB0aGlzLmVsZW1lbnQuYXR0cignZGF0YS10YXJnZXQnKSA/IHRoaXMuZWxlbWVudC5hdHRyKCdkYXRhLXRhcmdldCcpIDogZmFsc2U7XG4gICAgICAgIGlmICh0YXJnZXQpIHtcbiAgICAgICAgICB0aGlzLnRhcmdldCA9ICQodGFyZ2V0KTtcbiAgICAgICAgICBpZiAodGhpcy50YXJnZXQubGVuZ3RoICYmICF0aGlzLnRhcmdldC5oYXNDbGFzcygnanMtb2ZmY2FudmFzLWRvbmUnKSkge1xuICAgICAgICAgICAgdGhpcy5lbGVtZW50LmFkZENsYXNzKCdqcy1vZmZjYW52YXMtaGFzLWV2ZW50cycpO1xuICAgICAgICAgICAgdGhpcy5sb2NhdGlvbiA9IHRoaXMudGFyZ2V0Lmhhc0NsYXNzKFwibmF2YmFyLW9mZmNhbnZhcy1yaWdodFwiKSA/IFwicmlnaHRcIiA6IFwibGVmdFwiO1xuICAgICAgICAgICAgdGhpcy50YXJnZXQuYWRkQ2xhc3ModGhpcy5fdHJhbnNmb3JtU3VwcG9ydGVkKCkgPyBcIm9mZmNhbnZhcy10cmFuc2Zvcm0ganMtb2ZmY2FudmFzLWRvbmVcIiA6IFwib2ZmY2FudmFzLXBvc2l0aW9uIGpzLW9mZmNhbnZhcy1kb25lXCIpO1xuICAgICAgICAgICAgdGhpcy50YXJnZXQuZGF0YSgnb2ZmY2FudmFzJywgdGhpcyk7XG4gICAgICAgICAgICB0aGlzLmVsZW1lbnQub24oXCJjbGlja1wiLCB0aGlzLl9jbGlja2VkKTtcbiAgICAgICAgICAgIHRoaXMudGFyZ2V0Lm9uKCd0cmFuc2l0aW9uZW5kJywgKGZ1bmN0aW9uKF90aGlzKSB7XG4gICAgICAgICAgICAgIHJldHVybiBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBpZiAoX3RoaXMudGFyZ2V0LmlzKCc6bm90KC5pbiknKSkge1xuICAgICAgICAgICAgICAgICAgcmV0dXJuIF90aGlzLnRhcmdldC5oZWlnaHQoJycpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgfTtcbiAgICAgICAgICAgIH0pKHRoaXMpKTtcbiAgICAgICAgICAgICQoZG9jdW1lbnQpLm9uKFwiY2xpY2tcIiwgdGhpcy5fZG9jdW1lbnRDbGlja2VkKTtcbiAgICAgICAgICAgIGlmICh0aGlzLnRhcmdldC5oYXNDbGFzcygnbmF2YmFyLW9mZmNhbnZhcy10b3VjaCcpKSB7XG4gICAgICAgICAgICAgIHQgPSBuZXcgT2ZmY2FudmFzVG91Y2godGhpcy5lbGVtZW50LCB0aGlzLnRhcmdldCwgdGhpcy5sb2NhdGlvbiwgdGhpcyk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICB0aGlzLnRhcmdldC5maW5kKFwiLmRyb3Bkb3duLXRvZ2dsZVwiKS5lYWNoKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICB2YXIgZDtcbiAgICAgICAgICAgICAgcmV0dXJuIGQgPSBuZXcgT2ZmY2FudmFzRHJvcGRvd24odGhpcyk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIHRoaXMudGFyZ2V0Lm9uKCdvZmZjYW52YXMudG9nZ2xlJywgKGZ1bmN0aW9uKF90aGlzKSB7XG4gICAgICAgICAgICAgIHJldHVybiBmdW5jdGlvbihlKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIF90aGlzLl9jbGlja2VkKGUpO1xuICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgfSkodGhpcykpO1xuICAgICAgICAgICAgdGhpcy50YXJnZXQub24oJ29mZmNhbnZhcy5jbG9zZScsIChmdW5jdGlvbihfdGhpcykge1xuICAgICAgICAgICAgICByZXR1cm4gZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgICAgIHJldHVybiBfdGhpcy5fY2xvc2UoZSk7XG4gICAgICAgICAgICAgIH07XG4gICAgICAgICAgICB9KSh0aGlzKSk7XG4gICAgICAgICAgICB0aGlzLnRhcmdldC5vbignb2ZmY2FudmFzLm9wZW4nLCAoZnVuY3Rpb24oX3RoaXMpIHtcbiAgICAgICAgICAgICAgcmV0dXJuIGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gX3RoaXMuX29wZW4oZSk7XG4gICAgICAgICAgICAgIH07XG4gICAgICAgICAgICB9KSh0aGlzKSk7XG4gICAgICAgICAgfVxuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIGNvbnNvbGUud2FybignT2ZmY2FudmFzOiBgZGF0YS10YXJnZXRgIGF0dHJpYnV0ZSBtdXN0IGJlIHByZXNlbnQuJyk7XG4gICAgICAgIH1cbiAgICAgIH1cblxuICAgICAgT2ZmY2FudmFzLnByb3RvdHlwZS5fbmF2YmFySGVpZ2h0ID0gZnVuY3Rpb24oKSB7XG4gICAgICAgIGlmICh0aGlzLnRhcmdldC5pcygnLmluJykpIHtcbiAgICAgICAgICByZXR1cm4gdGhpcy50YXJnZXQuaGVpZ2h0KCQod2luZG93KS5vdXRlckhlaWdodCgpKTtcbiAgICAgICAgfVxuICAgICAgfTtcblxuICAgICAgT2ZmY2FudmFzLnByb3RvdHlwZS5fY2xpY2tlZCA9IGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICB0aGlzLl9zZW5kRXZlbnRzQmVmb3JlKCk7XG4gICAgICAgICQoXCIubmF2YmFyLW9mZmNhbnZhc1wiKS5ub3QodGhpcy50YXJnZXQpLnRyaWdnZXIoJ29mZmNhbnZhcy5jbG9zZScpO1xuICAgICAgICB0aGlzLnRhcmdldC50b2dnbGVDbGFzcygnaW4nKTtcbiAgICAgICAgdGhpcy5lbGVtZW50LnRvZ2dsZUNsYXNzKCdpcy1vcGVuJyk7XG4gICAgICAgIHRoaXMuX25hdmJhckhlaWdodCgpO1xuICAgICAgICByZXR1cm4gdGhpcy5ib2R5T3ZlcmZsb3coKTtcbiAgICAgIH07XG5cbiAgICAgIE9mZmNhbnZhcy5wcm90b3R5cGUuX29wZW4gPSBmdW5jdGlvbihlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgaWYgKHRoaXMudGFyZ2V0LmlzKCcuaW4nKSkge1xuICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuICAgICAgICB0aGlzLl9zZW5kRXZlbnRzQmVmb3JlKCk7XG4gICAgICAgIHRoaXMudGFyZ2V0LmFkZENsYXNzKCdpbicpO1xuICAgICAgICB0aGlzLmVsZW1lbnQuYWRkQ2xhc3MoJ2lzLW9wZW4nKTtcbiAgICAgICAgdGhpcy5fbmF2YmFySGVpZ2h0KCk7XG4gICAgICAgIHJldHVybiB0aGlzLmJvZHlPdmVyZmxvdygpO1xuICAgICAgfTtcblxuICAgICAgT2ZmY2FudmFzLnByb3RvdHlwZS5fY2xvc2UgPSBmdW5jdGlvbihlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgaWYgKHRoaXMudGFyZ2V0LmlzKCc6bm90KC5pbiknKSkge1xuICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuICAgICAgICB0aGlzLl9zZW5kRXZlbnRzQmVmb3JlKCk7XG4gICAgICAgIHRoaXMudGFyZ2V0LnJlbW92ZUNsYXNzKCdpbicpO1xuICAgICAgICB0aGlzLmVsZW1lbnQucmVtb3ZlQ2xhc3MoJ2lzLW9wZW4nKTtcbiAgICAgICAgdGhpcy5fbmF2YmFySGVpZ2h0KCk7XG4gICAgICAgIHJldHVybiB0aGlzLmJvZHlPdmVyZmxvdygpO1xuICAgICAgfTtcblxuICAgICAgT2ZmY2FudmFzLnByb3RvdHlwZS5fZG9jdW1lbnRDbGlja2VkID0gZnVuY3Rpb24oZSkge1xuICAgICAgICB2YXIgY2xpY2tlZEVsO1xuICAgICAgICBjbGlja2VkRWwgPSAkKGUudGFyZ2V0KTtcbiAgICAgICAgaWYgKCFjbGlja2VkRWwuaGFzQ2xhc3MoJ29mZmNhbnZhcy10b2dnbGUnKSAmJiBjbGlja2VkRWwucGFyZW50cygnLm9mZmNhbnZhcy10b2dnbGUnKS5sZW5ndGggPT09IDAgJiYgY2xpY2tlZEVsLnBhcmVudHMoJy5uYXZiYXItb2ZmY2FudmFzJykubGVuZ3RoID09PSAwICYmICFjbGlja2VkRWwuaGFzQ2xhc3MoJ25hdmJhci1vZmZjYW52YXMnKSkge1xuICAgICAgICAgIGlmICh0aGlzLnRhcmdldC5oYXNDbGFzcygnaW4nKSkge1xuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgdGhpcy5fc2VuZEV2ZW50c0JlZm9yZSgpO1xuICAgICAgICAgICAgdGhpcy50YXJnZXQucmVtb3ZlQ2xhc3MoJ2luJyk7XG4gICAgICAgICAgICB0aGlzLmVsZW1lbnQucmVtb3ZlQ2xhc3MoJ2lzLW9wZW4nKTtcbiAgICAgICAgICAgIHRoaXMuX25hdmJhckhlaWdodCgpO1xuICAgICAgICAgICAgcmV0dXJuIHRoaXMuYm9keU92ZXJmbG93KCk7XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9O1xuXG4gICAgICBPZmZjYW52YXMucHJvdG90eXBlLl9zZW5kRXZlbnRzQmVmb3JlID0gZnVuY3Rpb24oKSB7XG4gICAgICAgIGlmICh0aGlzLnRhcmdldC5oYXNDbGFzcygnaW4nKSkge1xuICAgICAgICAgIHJldHVybiB0aGlzLnRhcmdldC50cmlnZ2VyKCdoaWRlLmJzLm9mZmNhbnZhcycpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIHJldHVybiB0aGlzLnRhcmdldC50cmlnZ2VyKCdzaG93LmJzLm9mZmNhbnZhcycpO1xuICAgICAgICB9XG4gICAgICB9O1xuXG4gICAgICBPZmZjYW52YXMucHJvdG90eXBlLl9zZW5kRXZlbnRzQWZ0ZXIgPSBmdW5jdGlvbigpIHtcbiAgICAgICAgaWYgKHRoaXMudGFyZ2V0Lmhhc0NsYXNzKCdpbicpKSB7XG4gICAgICAgICAgcmV0dXJuIHRoaXMudGFyZ2V0LnRyaWdnZXIoJ3Nob3duLmJzLm9mZmNhbnZhcycpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIHJldHVybiB0aGlzLnRhcmdldC50cmlnZ2VyKCdoaWRkZW4uYnMub2ZmY2FudmFzJyk7XG4gICAgICAgIH1cbiAgICAgIH07XG5cbiAgICAgIE9mZmNhbnZhcy5wcm90b3R5cGUuYm9keU92ZXJmbG93ID0gZnVuY3Rpb24oZXZlbnRzKSB7XG4gICAgICAgIGlmIChldmVudHMgPT0gbnVsbCkge1xuICAgICAgICAgIGV2ZW50cyA9IHRydWU7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKHRoaXMudGFyZ2V0LmlzKCcuaW4nKSkge1xuICAgICAgICAgICQoJ2JvZHknKS5hZGRDbGFzcygnb2ZmY2FudmFzLXN0b3Atc2Nyb2xsaW5nJyk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgJCgnYm9keScpLnJlbW92ZUNsYXNzKCdvZmZjYW52YXMtc3RvcC1zY3JvbGxpbmcnKTtcbiAgICAgICAgfVxuICAgICAgICBpZiAoZXZlbnRzKSB7XG4gICAgICAgICAgcmV0dXJuIHRoaXMuX3NlbmRFdmVudHNBZnRlcigpO1xuICAgICAgICB9XG4gICAgICB9O1xuXG4gICAgICBPZmZjYW52YXMucHJvdG90eXBlLl90cmFuc2Zvcm1TdXBwb3J0ZWQgPSBmdW5jdGlvbigpIHtcbiAgICAgICAgdmFyIGFzU3VwcG9ydCwgZWwsIHJlZ2V4LCB0cmFuc2xhdGUzRDtcbiAgICAgICAgZWwgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKTtcbiAgICAgICAgdHJhbnNsYXRlM0QgPSBcInRyYW5zbGF0ZTNkKDBweCwgMHB4LCAwcHgpXCI7XG4gICAgICAgIHJlZ2V4ID0gL3RyYW5zbGF0ZTNkXFwoMHB4LCAwcHgsIDBweFxcKS9nO1xuICAgICAgICBlbC5zdHlsZS5jc3NUZXh0ID0gXCItd2Via2l0LXRyYW5zZm9ybTogXCIgKyB0cmFuc2xhdGUzRCArIFwiOyAtbW96LXRyYW5zZm9ybTogXCIgKyB0cmFuc2xhdGUzRCArIFwiOyAtby10cmFuc2Zvcm06IFwiICsgdHJhbnNsYXRlM0QgKyBcIjsgdHJhbnNmb3JtOiBcIiArIHRyYW5zbGF0ZTNEO1xuICAgICAgICBhc1N1cHBvcnQgPSBlbC5zdHlsZS5jc3NUZXh0Lm1hdGNoKHJlZ2V4KTtcbiAgICAgICAgcmV0dXJuIGFzU3VwcG9ydC5sZW5ndGggIT0gbnVsbDtcbiAgICAgIH07XG5cbiAgICAgIHJldHVybiBPZmZjYW52YXM7XG5cbiAgICB9KSgpO1xuICAgICQuZm4uYnNPZmZjYW52YXMgPSBmdW5jdGlvbigpIHtcbiAgICAgIHJldHVybiB0aGlzLmVhY2goZnVuY3Rpb24oKSB7XG4gICAgICAgIHJldHVybiBuZXcgT2ZmY2FudmFzKCQodGhpcykpO1xuICAgICAgfSk7XG4gICAgfTtcbiAgICByZXR1cm4gJChmdW5jdGlvbigpIHtcbiAgICAgICQoJ1tkYXRhLXRvZ2dsZT1cIm9mZmNhbnZhc1wiXScpLmVhY2goZnVuY3Rpb24oKSB7XG4gICAgICAgIHJldHVybiAkKHRoaXMpLmJzT2ZmY2FudmFzKCk7XG4gICAgICB9KTtcbiAgICAgICQod2luZG93KS5vbigncmVzaXplJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICQoJy5uYXZiYXItb2ZmY2FudmFzLmluJykuZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICAgICByZXR1cm4gJCh0aGlzKS5oZWlnaHQoJycpLnJlbW92ZUNsYXNzKCdpbicpO1xuICAgICAgICB9KTtcbiAgICAgICAgJCgnLm9mZmNhbnZhcy10b2dnbGUnKS5yZW1vdmVDbGFzcygnaXMtb3BlbicpO1xuICAgICAgICByZXR1cm4gJCgnYm9keScpLnJlbW92ZUNsYXNzKCdvZmZjYW52YXMtc3RvcC1zY3JvbGxpbmcnKTtcbiAgICAgIH0pO1xuICAgICAgcmV0dXJuICQoJy5vZmZjYW52YXMtdG9nZ2xlJykuZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICAgcmV0dXJuICQodGhpcykub24oJ2NsaWNrJywgZnVuY3Rpb24oZSkge1xuICAgICAgICAgIHZhciBlbCwgc2VsZWN0b3I7XG4gICAgICAgICAgaWYgKCEkKHRoaXMpLmhhc0NsYXNzKCdqcy1vZmZjYW52YXMtaGFzLWV2ZW50cycpKSB7XG4gICAgICAgICAgICBzZWxlY3RvciA9ICQodGhpcykuYXR0cignZGF0YS10YXJnZXQnKTtcbiAgICAgICAgICAgIGVsID0gJChzZWxlY3Rvcik7XG4gICAgICAgICAgICBpZiAoZWwpIHtcbiAgICAgICAgICAgICAgZWwuaGVpZ2h0KCcnKTtcbiAgICAgICAgICAgICAgZWwucmVtb3ZlQ2xhc3MoJ2luJyk7XG4gICAgICAgICAgICAgIHJldHVybiAkKCdib2R5JykuY3NzKHtcbiAgICAgICAgICAgICAgICBvdmVyZmxvdzogJycsXG4gICAgICAgICAgICAgICAgcG9zaXRpb246ICcnXG4gICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgICB9KTtcbiAgICB9KTtcbiAgfSkod2luZG93LmpRdWVyeSwgd2luZG93KTtcblxufSkuY2FsbCh0aGlzKTtcbiIsIi8vIGV4dHJhY3RlZCBieSBtaW5pLWNzcy1leHRyYWN0LXBsdWdpblxuZXhwb3J0IHt9OyIsIm1vZHVsZS5leHBvcnRzID0galF1ZXJ5OyIsImlmKHR5cGVvZiB3aW5kb3cubW9tZW50ID09PSAndW5kZWZpbmVkJykgeyB2YXIgZSA9IG5ldyBFcnJvcihcIkNhbm5vdCBmaW5kIG1vZHVsZSAnd2luZG93Lm1vbWVudCdcIik7IGUuY29kZSA9ICdNT0RVTEVfTk9UX0ZPVU5EJzsgdGhyb3cgZTsgfVxuXG5tb2R1bGUuZXhwb3J0cyA9IHdpbmRvdy5tb21lbnQ7Il0sIm5hbWVzIjpbInJlcXVpcmUiLCIkIiwiYXBwT25SZWFkeSIsImZyb250ZW5kQm9keSIsImRvY3VtZW50IiwicXVlcnlTZWxlY3RvciIsInRoZW4iLCJkZWZhdWx0IiwiYXBwRnJvbnRlbmQiLCJzZXRVcCIsImNhdGNoIiwiZXJyb3IiLCJjb25zb2xlIiwibG9nIiwiY2hhcnRDb250YWluZXJzIiwicXVlcnlTZWxlY3RvckFsbCIsImxlbmd0aCIsImFwcENoYXJ0Iiwic2V0VXBMaXN0IiwiYWR2YW5jZWRTZWxlY3RFbGVtZW50cyIsImFwcEFkdmFuY2VTZWxlY3QiLCJmaWx0ZXJBZGRMaW5rcyIsImZpbHRlclNlbGVjdGlvbiIsImdldEVsZW1lbnRCeUlkIiwiYXBwRmlsdGVyIiwic2V0VXBBZGRMaW5rc0xpc3QiLCJzZXRVcEZpbHRlclNlbGVjdGlvbkxpc3QiLCJtb2RhbEZvcm1zIiwiTW9kYWxGb3JtIiwiaSIsIm4iLCJmb3JtQ29udGFpbmVycyIsIkFkbWluIiwiYXBwRm9ybSIsImFwcENvbW1vbiIsImluaXQiLCJyZWFkeVN0YXRlIiwiZG9jdW1lbnRFbGVtZW50IiwiZG9TY3JvbGwiLCJhZGRFdmVudExpc3RlbmVyIiwiX19iaW5kIiwiZm4iLCJtZSIsImFwcGx5IiwiYXJndW1lbnRzIiwid2luZG93IiwiT2ZmY2FudmFzIiwiT2ZmY2FudmFzRHJvcGRvd24iLCJPZmZjYW52YXNUb3VjaCIsImVsZW1lbnQiLCJfY2xpY2tFdmVudCIsIm5hdiIsImNsb3Nlc3QiLCJkcm9wZG93biIsInBhcmVudCIsImZpbmQiLCJvbiIsIl90aGlzIiwiaXMiLCJyZW1vdmVDbGFzcyIsInByb3RvdHlwZSIsImUiLCJoYXNDbGFzcyIsInByZXZlbnREZWZhdWx0Iiwic3RvcFByb3BhZ2F0aW9uIiwibm90IiwidG9nZ2xlQ2xhc3MiLCJidXR0b24iLCJsb2NhdGlvbiIsIm9mZmNhbnZhcyIsIl9nZXRGYWRlIiwiX2dldENzcyIsIl90b3VjaEVuZCIsIl90b3VjaE1vdmUiLCJfdG91Y2hTdGFydCIsImVuZFRocmVzaG9sZCIsInN0YXJ0VGhyZXNob2xkIiwib3V0ZXJXaWR0aCIsIm1heFN0YXJ0VGhyZXNob2xkIiwiY3VycmVudFgiLCJmYWRlIiwic3RhcnRYIiwib3JpZ2luYWxFdmVudCIsInRvdWNoZXMiLCJwYWdlWCIsImhlaWdodCIsIm91dGVySGVpZ2h0IiwieCIsInRhcmdldCIsInBhcmVudHMiLCJNYXRoIiwiYWJzIiwiY3NzIiwiZW5kIiwic2VuZEV2ZW50cyIsImNoYW5nZWRUb3VjaGVzIiwiX2NsZWFyQ3NzIiwiYm9keU92ZXJmbG93IiwidCIsIl9zZW5kRXZlbnRzQWZ0ZXIiLCJfc2VuZEV2ZW50c0JlZm9yZSIsIl9kb2N1bWVudENsaWNrZWQiLCJfY2xvc2UiLCJfb3BlbiIsIl9jbGlja2VkIiwiX25hdmJhckhlaWdodCIsImF0dHIiLCJhZGRDbGFzcyIsIl90cmFuc2Zvcm1TdXBwb3J0ZWQiLCJkYXRhIiwiZWFjaCIsImQiLCJ3YXJuIiwidHJpZ2dlciIsImNsaWNrZWRFbCIsImV2ZW50cyIsImFzU3VwcG9ydCIsImVsIiwicmVnZXgiLCJ0cmFuc2xhdGUzRCIsImNyZWF0ZUVsZW1lbnQiLCJzdHlsZSIsImNzc1RleHQiLCJtYXRjaCIsImJzT2ZmY2FudmFzIiwic2VsZWN0b3IiLCJvdmVyZmxvdyIsInBvc2l0aW9uIiwialF1ZXJ5IiwiY2FsbCJdLCJzb3VyY2VSb290IjoiIn0=