(self.webpackChunkkdn_ozg=self.webpackChunkkdn_ozg||[]).push([[311],{9311:function(e,t,a){var o,r;function i(e){return(i="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}void 0===(r="function"==typeof(o=function(){"use strict";return{setUpList:function(e){for(var t=this,a=0,o=e.length;a<o;a++)t.initFormContainer(e[a])},initFormContainer:function(e){var t=this,a=e.querySelectorAll(".js-copy-row-values");if(a.length>0)for(var o=0,r=a.length;o<r;o++)t.initCopyRowContainer(a[o]);t.initFormMeta(e)},initFormMeta:function(e){var t=this,a=e.querySelector(".app-form-meta");if(a){var o=JSON.parse(a.dataset.meta);if("object"===i(o)){var r=a.dataset.formId;Object.keys(o).forEach((function(e){var a=r+"_"+o[e].property;t.addFormElementMeta(a,e,o)}))}jQuery(".js-form-label-popover:not(.initialized)").each((function(){$(this).addClass("initialized"),$(this).popover()}))}},addFormElementMeta:function(e,t,a){var o=this,r=document.getElementById("sonata-ba-field-container-"+e),n=null;if(r){if((n=r.querySelector(".control-label"))||(n=r.querySelector(".control-label__text")),n&&null===n.querySelector(".field-help")){var s="";a[t].description&&(s=a[t].description.replace('"',"'").replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g,"$1<br>"));var l=n.textContent.replace(/(<([^>]+)>)/gi,""),c='\n<span id="meta-help-'+e+'" class="has-popover"><span class="field-help js-form-label-popover" data-toggle="popover" title="'+l+'" data-content="'+s+'" data-html="1" data-trigger="hover" data-placement="top" data-container="#meta-help-'+e+'"> <i class="fa fa-question-circle" aria-hidden="true"></i></span></span>';n.innerHTML=n.innerHTML+c}if("object"===i(a[t].subMeta)&&null!==a[t].subMeta){var d=a[t].subMeta,p=Object.keys(d),f=-1;p.forEach((function(t){var a=d[t].property;if(f<0){var r=null;do{++f;var i=e+"_"+f+"_"+a;null===(r=document.getElementById("sonata-ba-field-container-"+i))&&--f}while(null!==r)}for(var n=0;n<=f;n++){var s=e+"_"+n+"_"+a;o.addFormElementMeta(s,t,d)}}))}return!0}return!1},initCopyRowContainer:function(e){var t=this,a=e.querySelectorAll(".sonata-collection-row");if(a.length>0)for(var o=0,r=a.length;o<r;o++){var i=a[o];i.innerHTML='<div class="block-copy-paste"><i class="fa fa-files-o action-copy inactive js-row-copy" aria-hidden="true"></i><i class="fa fa-clipboard action-paste inactive disabled js-row-paste" aria-hidden="true"></i></div>'+i.innerHTML,e.addEventListener("click",(function(a){a.target.matches(".js-row-copy")&&(a.preventDefault(),a.stopPropagation(),t.resetCopyPasteContainer(e,a.target)),a.target.matches(".js-row-paste")&&(a.preventDefault(),a.stopPropagation(),t.pasteValues(e,a.target),t.resetCopyPasteContainer(e,null))}),!1)}},resetCopyPasteContainer:function(e,t){for(var a=e.querySelectorAll(".js-row-copy"),o=0,r=a.length;o<r;o++)a[o].classList.contains("active")&&(a[o].classList.remove("active"),a[o].classList.add("inactive")),a[o].classList.contains("disabled")&&a[o].classList.remove("disabled"),t&&(t&&t===a[o]?(a[o].classList.remove("inactive"),a[o].classList.add("active")):a[o].classList.add("disabled"));for(var i=e.querySelectorAll(".js-row-paste"),n=0,s=i.length;n<s;n++)i[n].classList.contains("active")&&(i[n].classList.remove("active"),i[n].classList.add("inactive")),i[n].classList.contains("disabled")&&i[n].classList.remove("disabled"),t&&(t.nextSibling!==i[n]?(i[n].classList.remove("inactive"),i[n].classList.add("active")):i[n].classList.add("disabled"))},pasteValues:function(e,t){var a=e.querySelector(".action-copy.active");if(a){var o=a.closest(".sonata-collection-row"),r=t.closest(".sonata-collection-row");if(o&&r)for(var i=o.querySelectorAll(".form-control"),n=r.querySelectorAll(".form-control"),s=0,l=i.length;s<l;s++)i[s].disabled||n[s].disabled||(n[s].value=i[s].value)}}}})?o.call(t,a,t,e):o)||(e.exports=r)}}]);