(self.webpackChunkkdn_ozg=self.webpackChunkkdn_ozg||[]).push([[328],{38055:function(t,e,n){"use strict";jQuery;n.e(806).then((function(){n(28594),n(35666)})).catch(n.oe),n(96972);const s=function(){document.querySelector(".app-fe")&&(n.e(124).then(n.t.bind(n,35124,23)).then((t=>{let{default:e}=t;e.setUp()})).catch((t=>{console.log("An error occurred while loading the frontend component",t)})),n(47046));let t=document.querySelectorAll(".mb-chart-container");t.length>0&&Promise.all([n.e(757),n.e(314)]).then(n.t.bind(n,98314,23)).then((e=>{let{default:n}=e;n.setUpList(t)})).catch((t=>{console.log("An error occurred while loading the chart component",t)}));let e=document.querySelectorAll("select.js-advanced-select");e.length>0&&n.e(530).then(n.t.bind(n,28530,23)).then((t=>{let{default:n}=t;n.setUpList(e)})).catch((t=>{console.log("An error occurred while loading the advanced-select component",t)}));let s=document.querySelectorAll(".js-filter-add"),o=document.getElementById("navbar-filter-selection");(s.length>0||o)&&n.e(787).then(n.t.bind(n,31787,23)).then((t=>{let{default:e}=t;e.setUpAddLinksList(s),e.setUpFilterSelectionList(o)})).catch((t=>{console.log("An error occurred while loading the filter component",t)}));let a=document.querySelectorAll(".js-modal-form");a.length>0&&n.e(180).then(n.bind(n,86180)).then((t=>{let{default:e}=t;for(let t=0,n=a.length;t<n;t++)new e(a[t])})).catch((t=>{console.log("An error occurred while loading the form component",t)}));let i=document.querySelectorAll(".sonata-ba-form");i.length>0&&"undefined"!=typeof Admin&&n.e(311).then(n.t.bind(n,69311,23)).then((t=>{let{default:e}=t;e.setUpList(i)})).catch((t=>{console.log("An error occurred while loading the form component",t)})),n.e(885).then(n.t.bind(n,50885,23)).then((t=>{let{default:e}=t;e.init()})).catch((t=>{console.log("An error occurred while loading the common component",t)}))};"complete"===document.readyState||"loading"!==document.readyState&&!document.documentElement.doScroll?s():document.addEventListener("DOMContentLoaded",s)},47046:function(){(function(){var t=function(t,e){return function(){return t.apply(e,arguments)}};!function(e,n){var s,o,a;o=function(){function n(n){var s;this.element=n,this._clickEvent=t(this._clickEvent,this),this.element=e(this.element),this.nav=this.element.closest(".nav"),this.dropdown=this.element.parent().find(".dropdown-menu"),this.element.on("click",this._clickEvent),this.nav.closest(".navbar-offcanvas").on("click",(s=this,function(){if(s.dropdown.is(".shown"))return s.dropdown.removeClass("shown").closest(".open").removeClass("open")}))}return n.prototype._clickEvent=function(t){return this.dropdown.hasClass("shown")||t.preventDefault(),t.stopPropagation(),e(".dropdown-toggle").not(this.element).closest(".open").removeClass("open").find(".dropdown-menu").removeClass("shown"),this.dropdown.toggleClass("shown"),this.element.parent().toggleClass("open")},n}(),a=function(){function s(n,s,o,a){this.button=n,this.element=s,this.location=o,this.offcanvas=a,this._getFade=t(this._getFade,this),this._getCss=t(this._getCss,this),this._touchEnd=t(this._touchEnd,this),this._touchMove=t(this._touchMove,this),this._touchStart=t(this._touchStart,this),this.endThreshold=130,this.startThreshold=this.element.hasClass("navbar-offcanvas-right")?e("body").outerWidth()-60:20,this.maxStartThreshold=this.element.hasClass("navbar-offcanvas-right")?e("body").outerWidth()-20:60,this.currentX=0,this.fade=!!this.element.hasClass("navbar-offcanvas-fade"),e(document).on("touchstart",this._touchStart),e(document).on("touchmove",this._touchMove),e(document).on("touchend",this._touchEnd)}return s.prototype._touchStart=function(t){if(this.startX=t.originalEvent.touches[0].pageX,this.element.is(".in"))return this.element.height(e(n).outerHeight())},s.prototype._touchMove=function(t){var n;if(e(t.target).parents(".navbar-offcanvas").length>0)return!0;if(this.startX>this.startThreshold&&this.startX<this.maxStartThreshold){if(t.preventDefault(),n=t.originalEvent.touches[0].pageX-this.startX,n=this.element.hasClass("navbar-offcanvas-right")?-n:n,Math.abs(n)<this.element.outerWidth())return this.element.css(this._getCss(n)),this.element.css(this._getFade(n))}else if(this.element.hasClass("in")&&(t.preventDefault(),n=t.originalEvent.touches[0].pageX+(this.currentX-this.startX),n=this.element.hasClass("navbar-offcanvas-right")?-n:n,Math.abs(n)<this.element.outerWidth()))return this.element.css(this._getCss(n)),this.element.css(this._getFade(n))},s.prototype._touchEnd=function(t){var n,s,o;return e(t.target).parents(".navbar-offcanvas").length>0||(s=!1,o=t.originalEvent.changedTouches[0].pageX,Math.abs(o)!==this.startX?(n=this.element.hasClass("navbar-offcanvas-right")?Math.abs(o)>this.endThreshold+50:o<this.endThreshold+50,this.element.hasClass("in")&&n?(this.currentX=0,this.element.removeClass("in").css(this._clearCss()),this.button.removeClass("is-open"),s=!0):Math.abs(o-this.startX)>this.endThreshold&&this.startX>this.startThreshold&&this.startX<this.maxStartThreshold?(this.currentX=this.element.hasClass("navbar-offcanvas-right")?-this.element.outerWidth():this.element.outerWidth(),this.element.toggleClass("in").css(this._clearCss()),this.button.toggleClass("is-open"),s=!0):this.element.css(this._clearCss()),this.offcanvas.bodyOverflow(s)):void 0)},s.prototype._getCss=function(t){return{"-webkit-transform":"translate3d("+(t=this.element.hasClass("navbar-offcanvas-right")?-t:t)+"px, 0px, 0px)","-webkit-transition-duration":"0s","-moz-transform":"translate3d("+t+"px, 0px, 0px)","-moz-transition":"0s","-o-transform":"translate3d("+t+"px, 0px, 0px)","-o-transition":"0s",transform:"translate3d("+t+"px, 0px, 0px)",transition:"0s"}},s.prototype._getFade=function(t){return this.fade?{opacity:t/this.element.outerWidth()}:{}},s.prototype._clearCss=function(){return{"-webkit-transform":"","-webkit-transition-duration":"","-moz-transform":"","-moz-transition":"","-o-transform":"","-o-transition":"",transform:"",transition:"",opacity:""}},s}(),n.Offcanvas=s=function(){function s(n){var s,i;this.element=n,this.bodyOverflow=t(this.bodyOverflow,this),this._sendEventsAfter=t(this._sendEventsAfter,this),this._sendEventsBefore=t(this._sendEventsBefore,this),this._documentClicked=t(this._documentClicked,this),this._close=t(this._close,this),this._open=t(this._open,this),this._clicked=t(this._clicked,this),this._navbarHeight=t(this._navbarHeight,this),(s=!!this.element.attr("data-target")&&this.element.attr("data-target"))?(this.target=e(s),this.target.length&&!this.target.hasClass("js-offcanvas-done")&&(this.element.addClass("js-offcanvas-has-events"),this.location=this.target.hasClass("navbar-offcanvas-right")?"right":"left",this.target.addClass(this._transformSupported()?"offcanvas-transform js-offcanvas-done":"offcanvas-position js-offcanvas-done"),this.target.data("offcanvas",this),this.element.on("click",this._clicked),this.target.on("transitionend",(i=this,function(){if(i.target.is(":not(.in)"))return i.target.height("")})),e(document).on("click",this._documentClicked),this.target.hasClass("navbar-offcanvas-touch")&&new a(this.element,this.target,this.location,this),this.target.find(".dropdown-toggle").each((function(){return new o(this)})),this.target.on("offcanvas.toggle",function(t){return function(e){return t._clicked(e)}}(this)),this.target.on("offcanvas.close",function(t){return function(e){return t._close(e)}}(this)),this.target.on("offcanvas.open",function(t){return function(e){return t._open(e)}}(this)))):console.warn("Offcanvas: `data-target` attribute must be present.")}return s.prototype._navbarHeight=function(){if(this.target.is(".in"))return this.target.height(e(n).outerHeight())},s.prototype._clicked=function(t){return t.preventDefault(),this._sendEventsBefore(),e(".navbar-offcanvas").not(this.target).trigger("offcanvas.close"),this.target.toggleClass("in"),this.element.toggleClass("is-open"),this._navbarHeight(),this.bodyOverflow()},s.prototype._open=function(t){if(t.preventDefault(),!this.target.is(".in"))return this._sendEventsBefore(),this.target.addClass("in"),this.element.addClass("is-open"),this._navbarHeight(),this.bodyOverflow()},s.prototype._close=function(t){if(t.preventDefault(),!this.target.is(":not(.in)"))return this._sendEventsBefore(),this.target.removeClass("in"),this.element.removeClass("is-open"),this._navbarHeight(),this.bodyOverflow()},s.prototype._documentClicked=function(t){var n;if(!(n=e(t.target)).hasClass("offcanvas-toggle")&&0===n.parents(".offcanvas-toggle").length&&0===n.parents(".navbar-offcanvas").length&&!n.hasClass("navbar-offcanvas")&&this.target.hasClass("in"))return t.preventDefault(),this._sendEventsBefore(),this.target.removeClass("in"),this.element.removeClass("is-open"),this._navbarHeight(),this.bodyOverflow()},s.prototype._sendEventsBefore=function(){return this.target.hasClass("in")?this.target.trigger("hide.bs.offcanvas"):this.target.trigger("show.bs.offcanvas")},s.prototype._sendEventsAfter=function(){return this.target.hasClass("in")?this.target.trigger("shown.bs.offcanvas"):this.target.trigger("hidden.bs.offcanvas")},s.prototype.bodyOverflow=function(t){if(null==t&&(t=!0),this.target.is(".in")?e("body").addClass("offcanvas-stop-scrolling"):e("body").removeClass("offcanvas-stop-scrolling"),t)return this._sendEventsAfter()},s.prototype._transformSupported=function(){var t,e,n;return n="translate3d(0px, 0px, 0px)",e=/translate3d\(0px, 0px, 0px\)/g,(t=document.createElement("div")).style.cssText="-webkit-transform: "+n+"; -moz-transform: "+n+"; -o-transform: "+n+"; transform: "+n,null!=t.style.cssText.match(e).length},s}(),e.fn.bsOffcanvas=function(){return this.each((function(){return new s(e(this))}))},e((function(){return e('[data-toggle="offcanvas"]').each((function(){return e(this).bsOffcanvas()})),e(n).on("resize",(function(){return e(".navbar-offcanvas.in").each((function(){return e(this).height("").removeClass("in")})),e(".offcanvas-toggle").removeClass("is-open"),e("body").removeClass("offcanvas-stop-scrolling")})),e(".offcanvas-toggle").each((function(){return e(this).on("click",(function(t){var n,s;if(!e(this).hasClass("js-offcanvas-has-events")&&(s=e(this).attr("data-target"),n=e(s)))return n.height(""),n.removeClass("in"),e("body").css({overflow:"",position:""})}))}))}))}(window.jQuery,window)}).call(this)},96972:function(t,e,n){"use strict";n.r(e)},42802:function(t){"use strict";if(void 0===window.moment){var e=new Error("Cannot find module 'window.moment'");throw e.code="MODULE_NOT_FOUND",e}t.exports=window.moment}},function(t){var e;e=38055,t(t.s=e)}]);