(self.webpackChunkkdn_ozg=self.webpackChunkkdn_ozg||[]).push([[311],{9311:function(e,t,a){var o,n;function r(e){return r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},r(e)}void 0===(n="function"==typeof(o=function(){"use strict";return{setUpList:function(e){for(var t=this,a=0,o=e.length;a<o;a++)t.initFormContainer(e[a])},initFormContainer:function(e){var t=this,a=e.querySelectorAll(".js-copy-row-values");if(a.length>0)for(var o=0,n=a.length;o<n;o++)t.initCopyRowContainer(a[o]);t.initFormMeta(e)},initFormMeta:function(e){var t=this,a=e.querySelectorAll(".app-form-meta");if(a.length>0)for(var o=function(e,o){var n=JSON.parse(a[e].dataset.meta);if("object"===r(n)){var i=a[e].dataset.formId;Object.keys(n).forEach((function(e){var a=i+"_"+n[e].property;t.addFormElementMeta(a,e,n)}))}jQuery(".js-form-label-popover:not(.initialized)").each((function(){$(this).addClass("initialized"),$(this).popover()}))},n=0,i=a.length;n<i;n++)o(n,i)},addFormElementMeta:function(e,t,a){var o=this,n=document.getElementById("sonata-ba-field-container-"+e),i=null;if(n){if((i=n.querySelector(".control-label"))||(i=n.querySelector(".control-label__text")),i&&null===i.querySelector(".field-help")){var s="";a[t].description&&(s=a[t].description.replace('"',"'").replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g,"$1<br>"));var l=i.textContent.replace(/(<([^>]+)>)/gi,""),c='\n<span id="meta-help-'+e+'" class="has-popover"><span class="field-help js-form-label-popover" data-toggle="popover" title="'+l+'" data-content="'+s+'" data-html="1" data-trigger="hover" data-placement="top" data-container="#meta-help-'+e+'"> <i class="fa fa-question-circle" aria-hidden="true"></i></span></span>';i.innerHTML=i.innerHTML+c}if("object"===r(a[t].subMeta)&&null!==a[t].subMeta){var d=a[t].subMeta,p=Object.keys(d),f=-1;p.forEach((function(t){var a=d[t].property;if(f<0){var n=null;do{++f;var r=e+"_"+f+"_"+a;null===(n=document.getElementById("sonata-ba-field-container-"+r))&&--f}while(null!==n)}for(var i=0;i<=f;i++){var s=e+"_"+i+"_"+a;o.addFormElementMeta(s,t,d)}}))}return!0}return!1},initCopyRowContainer:function(e){var t=this,a=e.querySelectorAll(".sonata-collection-row");if(a.length>0)for(var o=0,n=a.length;o<n;o++){var r=a[o];r.innerHTML='<div class="block-copy-paste"><i class="fa fa-files-o action-copy inactive js-row-copy" aria-hidden="true"></i><i class="fa fa-clipboard action-paste inactive disabled js-row-paste" aria-hidden="true"></i></div>'+r.innerHTML,e.addEventListener("click",(function(a){a.target.matches(".js-row-copy")&&(a.preventDefault(),a.stopPropagation(),t.resetCopyPasteContainer(e,a.target)),a.target.matches(".js-row-paste")&&(a.preventDefault(),a.stopPropagation(),t.pasteValues(e,a.target),t.resetCopyPasteContainer(e,null))}),!1)}},resetCopyPasteContainer:function(e,t){for(var a=e.querySelectorAll(".js-row-copy"),o=0,n=a.length;o<n;o++)a[o].classList.contains("active")&&(a[o].classList.remove("active"),a[o].classList.add("inactive")),a[o].classList.contains("disabled")&&a[o].classList.remove("disabled"),t&&(t&&t===a[o]?(a[o].classList.remove("inactive"),a[o].classList.add("active")):a[o].classList.add("disabled"));for(var r=e.querySelectorAll(".js-row-paste"),i=0,s=r.length;i<s;i++)r[i].classList.contains("active")&&(r[i].classList.remove("active"),r[i].classList.add("inactive")),r[i].classList.contains("disabled")&&r[i].classList.remove("disabled"),t&&(t.nextSibling!==r[i]?(r[i].classList.remove("inactive"),r[i].classList.add("active")):r[i].classList.add("disabled"))},pasteValues:function(e,t){var a=e.querySelector(".action-copy.active");if(a){var o=a.closest(".sonata-collection-row"),n=t.closest(".sonata-collection-row");if(o&&n)for(var r=o.querySelectorAll(".form-control"),i=n.querySelectorAll(".form-control"),s=0,l=r.length;s<l;s++)r[s].disabled||i[s].disabled||(i[s].value=r[s].value)}}}})?o.call(t,a,t,e):o)||(e.exports=n)}}]);