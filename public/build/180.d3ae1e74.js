(self.webpackChunkkdn_ozg=self.webpackChunkkdn_ozg||[]).push([[180],{6180:function(t,n,e){"use strict";function o(t,n){var e="undefined"!=typeof Symbol&&t[Symbol.iterator]||t["@@iterator"];if(!e){if(Array.isArray(t)||(e=function(t,n){if(!t)return;if("string"==typeof t)return a(t,n);var e=Object.prototype.toString.call(t).slice(8,-1);"Object"===e&&t.constructor&&(e=t.constructor.name);if("Map"===e||"Set"===e)return Array.from(t);if("Arguments"===e||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(e))return a(t,n)}(t))||n&&t&&"number"==typeof t.length){e&&(t=e);var o=0,i=function(){};return{s:i,n:function(){return o>=t.length?{done:!0}:{done:!1,value:t[o++]}},e:function(t){throw t},f:i}}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}var r,l=!0,s=!1;return{s:function(){e=e.call(t)},n:function(){var t=e.next();return l=t.done,t},e:function(t){s=!0,r=t},f:function(){try{l||null==e.return||e.return()}finally{if(s)throw r}}}}function a(t,n){(null==n||n>t.length)&&(n=t.length);for(var e=0,o=new Array(n);e<n;e++)o[e]=t[e];return o}function i(t,n){for(var e=0;e<n.length;e++){var o=n[e];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(t,o.key,o)}}function r(t){return(r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(t)}function l(t,n){for(var e=0;e<n.length;e++){var o=n[e];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(t,o.key,o)}}function s(t,n,e){return(s="undefined"!=typeof Reflect&&Reflect.get?Reflect.get:function(t,n,e){var o=function(t,n){for(;!Object.prototype.hasOwnProperty.call(t,n)&&null!==(t=d(t)););return t}(t,n);if(o){var a=Object.getOwnPropertyDescriptor(o,n);return a.get?a.get.call(e):a.value}})(t,n,e||t)}function c(t,n){return(c=Object.setPrototypeOf||function(t,n){return t.__proto__=n,t})(t,n)}function u(t){var n=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){}))),!0}catch(t){return!1}}();return function(){var e,o=d(t);if(n){var a=d(this).constructor;e=Reflect.construct(o,arguments,a)}else e=o.apply(this,arguments);return f(this,e)}}function f(t,n){return!n||"object"!==r(n)&&"function"!=typeof n?function(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t}(t):n}function d(t){return(d=Object.setPrototypeOf?Object.getPrototypeOf:function(t){return t.__proto__||Object.getPrototypeOf(t)})(t)}e.r(n),e.d(n,{default:function(){return h}});var h=function(t){!function(t,n){if("function"!=typeof n&&null!==n)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(n&&n.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),n&&c(t,n)}(i,t);var n,e,o,a=u(i);function i(t){var n;return function(t,n){if(!(t instanceof n))throw new TypeError("Cannot call a class as a function")}(this,i),(n=a.call(this,t)).submitUrl=null,n.$form=null,n}return n=i,(e=[{key:"_getConfig",value:function(){return null===this.config&&(s(d(i.prototype),"_getConfig",this).call(this),this.config.message='<div class="text-center"><i class="fa fa-spinner fa-spin fa-4x"></i></div>',(!this.config.cancel||this.config.cancel.length<=0)&&(this.config.cancel='<i class="fa fa-times"></i>')),this.config}},{key:"_getModalLoadUrl",value:function(){return this._getConfig().loadUrl}},{key:"_getModalSubmitUrl",value:function(){return this.submitUrl||this._getConfig().submitUrl}},{key:"_getModal",value:function(){if(null===this.$modal){var t=this._getConfig();this.$modal=$('\n                <div class="modal fade" role="dialog">\n                    <div class="modal-dialog">\n                        <div class="modal-content">\n                            <div class="modal-header">\n                                <button type="button" class="close" data-dismiss="modal">\n                                    <i class="fa fa-times"></i>\n                                </button>\n                                <h4 class="modal-title">'.concat(t.title,'</h4>\n                            </div>\n                            <div class="modal-body">\n                                <p>').concat(t.message,'</p>\n                            </div>\n                            <div class="modal-footer">\n                                <button type="button" class="js-cancel btn btn-default" data-dismiss="modal">').concat(t.cancel,"</button>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n            ")).modal({show:!1})}return this.$modal}},{key:"_submit",value:function(){var t=this;$.ajax({type:"POST",url:this._getModalSubmitUrl(),data:this.$form.serialize(),headers:{"X-Requested-With":"XMLHttpRequest"}}).then((function(n){t._handleResponse(n)}))}},{key:"_handleResponse",value:function(t){if(s(d(i.prototype),"_handleResponse",this).call(this,t),"content"===t.type){var n=this.$modal.find("form").first();this.$form=n,n&&n.attr("action")&&(this.submitUrl=n.attr("action"));var e=new CustomEvent("mb-form-update",{detail:{container:n}});document.dispatchEvent(e)}}},{key:"load",value:function(){var t=this;s(d(i.prototype),"load",this).call(this);var n=this._getModalLoadUrl();n&&$.ajax({url:n,headers:{"X-Requested-With":"XMLHttpRequest"}}).then((function(n){t._handleResponse(n)}))}}])&&l(n.prototype,e),o&&l(n,o),i}(function(){function t(n){var e=this;!function(t,n){if(!(t instanceof n))throw new TypeError("Cannot call a class as a function")}(this,t);var o=$(n);this.$element=o,this.$modal=null,this.config=null,o.on("click",(function(t){t.preventDefault(),e.load()}))}var n,e,a;return n=t,(e=[{key:"_getConfig",value:function(){return null===this.config&&(this.config=this.$element.data("modal")),this.config}},{key:"_getModal",value:function(){if(null===this.$modal){var t=this._getConfig();this.$modal=$('\n                <div class="modal fade" role="dialog">\n                    <div class="modal-dialog">\n                        <div class="modal-content">\n                            <div class="modal-header">\n                                <button type="button" class="close" data-dismiss="modal">\n                                    <i class="fa fa-times"></i>\n                                </button>\n                                <h4 class="modal-title">'.concat(t.title,'</h4>\n                            </div>\n                            <div class="modal-body">\n                                <p>').concat(t.message,'</p>\n                            </div>\n                            <div class="modal-footer">\n                                <button type="button" class="js-cancel btn btn-default" data-dismiss="modal">').concat(t.cancel,'</button>\n                                <button type="button" class="js-submit btn btn-primary">').concat(t.submit,"</button>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n            ")).modal({show:!1})}return this.$modal}},{key:"_closed",value:function(){this.$modal.remove(),this.$modal=null}},{key:"_submit",value:function(){}},{key:"_handleResponse",value:function(t){switch(t.type){case"reload":window.location.reload(!0);break;case"redirect":window.location.href=t.url;break;case"new-tab":window.open(t.url,"_blank"),this.$modal.modal("hide");break;case"content":var n=this.$modal,e=$(t.content),a=[".modal-header",".modal-body",".modal-footer"];if(e.filter(a.join(", ")).length>0){var i,r=o(a);try{for(r.s();!(i=r.n()).done;){var l=i.value,s=e.filter(l).first();s&&n.find(l).html(s.html())}}catch(t){r.e(t)}finally{r.f()}}else n.find(".modal-body").html($(t.content))}}},{key:"load",value:function(){var t=this,n=this._getModal();n.on("click",".js-submit",(function(n){n.preventDefault(),$(n.currentTarget).addClass("disabled"),$(n.currentTarget).html('<i class="fa fa-spin fa-spinner"></i>'),t._submit()})),n.on("hidden.bs.modal",this._closed.bind(this)),n.modal("show")}}])&&i(n.prototype,e),a&&i(n,a),t}())}}]);