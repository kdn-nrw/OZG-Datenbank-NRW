(self.webpackChunkkdn_ozg=self.webpackChunkkdn_ozg||[]).push([[885],{885:function(t,e,n){var o,a;void 0===(a="function"==typeof(o=function(){"use strict";return{init:function(){var t=this;t.initTabs(),t.initClickToggle(),t.initClickLoad(),t.initContent(document)},initTabs:function(){var t=this;if(document.getElementById("header-top")){var e=null;window.location.hash&&(e=window.location.hash,t.scrollTo(window.location.hash)),window.addEventListener("scroll",(function(n){window.location.hash&&e!==window.location.hash&&(e=window.location.hash,t.scrollTo(window.location.hash))}));var n=location.hash.replace(/^#/,"");if(n){var o=$('.nav-tabs a[href="#'+n+'"]');o.length>0&&(o.parents(".nav-tabs").find(".tab-item.js-init-load").removeClass("js-init-load").addClass("js-click-load"),o.parent().hasClass("js-click-load")&&o.parent().removeClass("js-click-load").addClass("js-init-load"),o.tab("show"))}$(".nav-tabs a").on("shown.bs.tab",(function(t){window.location.hash=t.target.hash}))}},initContent:function(t){var e=this;$(t).find('[data-toggle="popover"]').popover(),e.initSortableTables(t)},initSortableTables:function(t){var e=t.querySelectorAll('[data-sortable="true"]');e.length>0&&function(){for(var t=function(t,e){return t.children[e].innerText||t.children[e].textContent},n=function(e,n){return function(o,a){return s=t(n?o:a,e),i=t(n?a:o,e),""===s||""===i||isNaN(s)||isNaN(i)?s.toString().localeCompare(i):s-i;var s,i}},o=function(t,e,o){var a=t.tBodies[0],s=void 0===o.asc||!o.asc;e.forEach((function(t){t.classList.contains("sort-by-active")&&(t.classList.remove("sort-by-active"),t.classList.remove("sort-asc"),t.classList.remove("sort-desc"))})),o.classList.add("sort-by-active"),o.classList.add(s?"sort-asc":"sort-desc"),o.asc=s,Array.from(a.querySelectorAll("tr")).sort(n(Array.from(o.parentNode.children).indexOf(o),s)).forEach((function(t){return a.appendChild(t)}))},a=function(t,n){var a=e[t];if(!a.classList.contains("table-sortable")&&1===a.tBodies.length){a.classList.add("table-sortable");var s=a.querySelectorAll("th"),i=0;s.forEach((function(t){var e=t.innerHTML.trim();e.length>0?(t.sortable=!0,++i,t.querySelector(".sort-wrap")||(t.innerHTML='<span class="sort-wrap">'+e+"</span>")):t.sortable=!1})),i>0&&s.forEach((function(t,e){t.sortable&&(t.addEventListener("click",(function(){o(a,s,t)})),0===e&&o(a,s,t))}))}},s=0,i=e.length;s<i;s++)a(s,i)}()},initClickLoad:function(){var t=this,e=function(e){var n;e.classList.contains("js-loaded")||(e.classList.add("js-loaded"),n=e.dataset.target?document.getElementById(e.dataset.target):e,t.load(e.dataset.url,"GET",(function(e){n.innerHTML=e.content,t.initContent(n)})))},n=document.querySelectorAll(".js-init-load");if(n.length>0)if("IntersectionObserver"in window&&"IntersectionObserverEntry"in window&&"intersectionRatio"in window.IntersectionObserverEntry.prototype)for(var o=new IntersectionObserver((function(t){t.forEach((function(t){t.intersectionRatio>0&&(e(t.target),o.unobserve(t.target))}))})),a=0,s=n.length;a<s;a++)o.observe(n[a]);else for(var i=0,r=n.length;i<r;i++)t.loadContent(n[i]),e(n[i]);t.onClick(".js-click-load",e)},initClickToggle:function(){this.onClick(".js-click-toggle",(function(t){var e=document.getElementById(t.dataset.toggle);if(e)if(e.classList.remove("updating"),e.classList.contains("open"))e.classList.remove("open"),e.style.display="none",t.classList.remove("active");else{for(var n=document.querySelectorAll(".js-toggle-target"),o=0,a=n.length;o<a;o++)n[o]!==e&&(n[o].classList.remove("open"),n[o].style.display="none");e.classList.add("open"),e.removeAttribute("style");for(var s=document.querySelectorAll(".js-click-toggle"),i=0,r=s.length;i<r;i++)s[i]!==t&&s[i].classList.remove("active");t.classList.add("active")}}))},onClick:function(t,e){document.addEventListener("click",(function(n){var o;(o=n.target.matches(t)?n.target:n.target.closest(t))&&(n.preventDefault(),n.stopPropagation(),e(o))}),!1)},load:function(t,e,n){var o=new XMLHttpRequest;o.onreadystatechange=function(){if(o.readyState===XMLHttpRequest.DONE){e=null;var t=o.status;if(0===t||t>=200&&t<400)try{var e=JSON.parse(o.responseText);n(e)}catch(t){console.log(t.message+" in "+o.responseText)}}},o.open(e,t),o.send()},scrollTo:function(t,e){var n=t?document.querySelector(t):null;return null!==n&&(void 0===n.dataset.scrolling||"1"!==n.dataset.scrolling)&&(n.dataset.scrolling="1",setTimeout((function(){var o=$(n).offset().top;$(n).hasClass("tab-pane")&&(o=$(n).parent().parent().offset().top-10);var a=$("#header-top").height();void 0!==e&&e||(e=200),$("html, body").stop().animate({scrollTop:o-a},e,"swing",(function(){n.dataset.scrolling="0",$(n).offset().top,0===t.indexOf("#")&&(window.location.hash=t)}))}),10),!0)}}})?o.call(e,n,e,t):o)||(t.exports=a)}}]);