(window.webpackJsonp=window.webpackJsonp||[]).push([[4],{ABzb:function(e,t,a){var o,i;function r(e){return(r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}void 0===(i="function"==typeof(o=function(){"use strict";return{setUpList:function(e){for(var t=0,a=e.length;t<a;t++)this.initFormContainer(e[t])},initFormContainer:function(e){var t=e.querySelectorAll(".js-copy-row-values");if(t.length>0)for(var a=0,o=t.length;a<o;a++)this.initCopyRowContainer(t[a]);this.initFormMeta(e)},initFormMeta:function(e){var t=e.querySelector(".app-form-meta");if(t){var a=JSON.parse(t.dataset.meta);if("object"===r(a)){var o=t.dataset.formId;Object.keys(a).forEach((function(e){var t=o+"_"+a[e].property,i=document.getElementById("sonata-ba-field-container-"+t),r=null;if(i&&(r=i.querySelector(".control-label")),r){var n=a[e].description.replace('"',"'").replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g,"$1<br>"),s='\n<span id="meta-help-'+t+'" class="has-popover"><span class="field-help js-form-label-popover" data-toggle="popover" title="'+r.textContent.replace(/(<([^>]+)>)/gi,"")+'" data-content="'+n+'" data-html="1" data-trigger="hover" data-placement="top" data-container="#meta-help-'+t+'"> <i class="fa fa-question-circle" aria-hidden="true"></i></span></span>';r.innerHTML=r.innerHTML+s}}))}jQuery(".js-form-label-popover").popover()}},initCopyRowContainer:function(e){var t=this,a=e.querySelectorAll(".sonata-collection-row");if(a.length>0)for(var o=0,i=a.length;o<i;o++){var r=a[o];r.innerHTML='<div class="block-copy-paste"><i class="fa fa-files-o action-copy inactive js-row-copy" aria-hidden="true"></i><i class="fa fa-clipboard action-paste inactive disabled js-row-paste" aria-hidden="true"></i></div>'+r.innerHTML,e.addEventListener("click",(function(a){a.target.matches(".js-row-copy")&&(a.preventDefault(),a.stopPropagation(),t.resetCopyPasteContainer(e,a.target)),a.target.matches(".js-row-paste")&&(a.preventDefault(),a.stopPropagation(),t.pasteValues(e,a.target),t.resetCopyPasteContainer(e,null))}),!1)}},resetCopyPasteContainer:function(e,t){for(var a=e.querySelectorAll(".js-row-copy"),o=0,i=a.length;o<i;o++)a[o].classList.contains("active")&&(a[o].classList.remove("active"),a[o].classList.add("inactive")),a[o].classList.contains("disabled")&&a[o].classList.remove("disabled"),t&&(t&&t===a[o]?(a[o].classList.remove("inactive"),a[o].classList.add("active")):a[o].classList.add("disabled"));for(var r=e.querySelectorAll(".js-row-paste"),n=0,s=r.length;n<s;n++)r[n].classList.contains("active")&&(r[n].classList.remove("active"),r[n].classList.add("inactive")),r[n].classList.contains("disabled")&&r[n].classList.remove("disabled"),t&&(t.nextSibling!==r[n]?(r[n].classList.remove("inactive"),r[n].classList.add("active")):r[n].classList.add("disabled"))},pasteValues:function(e,t){var a=e.querySelector(".action-copy.active");if(a){var o=a.closest(".sonata-collection-row"),i=t.closest(".sonata-collection-row");if(o&&i)for(var r=o.querySelectorAll(".form-control"),n=i.querySelectorAll(".form-control"),s=0,l=r.length;s<l;s++)r[s].disabled||n[s].disabled||(n[s].value=r[s].value)}}}})?o.call(t,a,t,e):o)||(e.exports=i)}}]);