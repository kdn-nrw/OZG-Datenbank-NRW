(self.webpackChunkkdn_ozg=self.webpackChunkkdn_ozg||[]).push([[311],{69311:function(e,t,l){var a,o;void 0===(o="function"==typeof(a=function(){"use strict";return{setUpList:function(e){let t=this;for(let l=0,a=e.length;l<a;l++)t.initFormContainer(e[l])},initFormContainer:function(e){let t=this,l=e.querySelectorAll(".js-copy-row-values");if(l.length>0)for(let e=0,a=l.length;e<a;e++)t.initCopyRowContainer(l[e]);t.initFormMeta(e);let a=e.querySelectorAll(".js-toggle-info");if(a.length>0){let t=function(t){let l=t.dataset.toggle,a=null;if("input"===t.nodeName.toLowerCase()){let e="radio"===t.getAttribute("type")||"checkbox"===t.getAttribute("type");(e&&t.checked||!e&&t.value)&&(a=t.dataset.toggle+"-show-"+t.dataset.show)}let o=e.querySelectorAll("."+l);for(let e=0,t=o.length;e<t;e++)o[e].setAttribute("style","display:none;"),a&&o[e].classList.contains(a)&&o[e].removeAttribute("style","display:none;")};for(let e=0,l=a.length;e<l;e++)t(a[e]);e.addEventListener("click",(function(e){e.target.matches(".js-toggle-info")&&t(e.target)}),!1)}},initFormMeta:function(e){let t=this,l=e.querySelectorAll(".app-form-meta");if(l.length>0)for(let e=0,a=l.length;e<a;e++){let a=JSON.parse(l[e].dataset.meta);if("object"==typeof a){let o=l[e].dataset.formId;Object.keys(a).forEach((function(e){const l=o+"_"+a[e].property;t.addFormElementMeta(l,e,a)}))}jQuery(".js-form-label-popover:not(.initialized)").each((function(){$(this).addClass("initialized"),$(this).popover()}))}},addFormElementMeta:function(e,t,l){let a=this;const o=document.getElementById("sonata-ba-field-container-"+e);let i=null;if(o){if(i=o.querySelector(".control-label"),i||(i=o.querySelector(".control-label__text")),i&&null===i.querySelector(".field-help")){let a="";l[t].description&&(a=l[t].description.replace('"',"'").replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g,"$1<br>"));let o=i.textContent.replace(/(<([^>]+)>)/gi,""),n='\n<span id="meta-help-'+e+'" class="has-popover"><span class="field-help js-form-label-popover" data-toggle="popover" title="'+o+'" data-content="'+a+'" data-html="1" data-trigger="hover" data-placement="top" data-container="#meta-help-'+e+'"> <i class="fa fa-question-circle" aria-hidden="true"></i></span></span>';i.innerHTML=i.innerHTML+n}if("object"==typeof l[t].subMeta&&null!==l[t].subMeta){let o=l[t].subMeta,i=Object.keys(o),n=-1;i.forEach((function(t){const l=o[t].property;if(n<0){let t=null;do{++n;let a=e+"_"+n+"_"+l;t=document.getElementById("sonata-ba-field-container-"+a),null===t&&--n}while(null!==t)}for(let i=0;i<=n;i++){const n=e+"_"+i+"_"+l;a.addFormElementMeta(n,t,o)}}))}return!0}return!1},initCopyRowContainer:function(e){let t=this,l=e.querySelectorAll(".sonata-collection-row");if(l.length>0)for(let a=0,o=l.length;a<o;a++){let o=l[a];o.innerHTML='<div class="block-copy-paste"><i class="fa fa-files-o action-copy inactive js-row-copy" aria-hidden="true"></i><i class="fa fa-clipboard action-paste inactive disabled js-row-paste" aria-hidden="true"></i></div>'+o.innerHTML,e.addEventListener("click",(function(l){l.target.matches(".js-row-copy")&&(l.preventDefault(),l.stopPropagation(),t.resetCopyPasteContainer(e,l.target)),l.target.matches(".js-row-paste")&&(l.preventDefault(),l.stopPropagation(),t.pasteValues(e,l.target),t.resetCopyPasteContainer(e,null))}),!1)}},resetCopyPasteContainer:function(e,t){let l=e.querySelectorAll(".js-row-copy");for(let e=0,a=l.length;e<a;e++)l[e].classList.contains("active")&&(l[e].classList.remove("active"),l[e].classList.add("inactive")),l[e].classList.contains("disabled")&&l[e].classList.remove("disabled"),t&&(t&&t===l[e]?(l[e].classList.remove("inactive"),l[e].classList.add("active")):l[e].classList.add("disabled"));let a=e.querySelectorAll(".js-row-paste");for(let e=0,l=a.length;e<l;e++)a[e].classList.contains("active")&&(a[e].classList.remove("active"),a[e].classList.add("inactive")),a[e].classList.contains("disabled")&&a[e].classList.remove("disabled"),t&&(t.nextSibling!==a[e]?(a[e].classList.remove("inactive"),a[e].classList.add("active")):a[e].classList.add("disabled"))},pasteValues:function(e,t){let l=e.querySelector(".action-copy.active");if(l){let e=l.closest(".sonata-collection-row"),a=t.closest(".sonata-collection-row");if(e&&a){let t=e.querySelectorAll(".form-control"),l=a.querySelectorAll(".form-control");for(let e=0,a=t.length;e<a;e++)t[e].disabled||l[e].disabled||(l[e].value=t[e].value)}}}}})?a.call(t,l,t,e):a)||(e.exports=o)}}]);