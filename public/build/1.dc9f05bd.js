(window.webpackJsonp=window.webpackJsonp||[]).push([[1],{"3C8r":function(t,e,a){var n,i;void 0===(i="function"==typeof(n=function(){"use strict";return{setUpList:function(t){for(var e=0,a=t.length;e<a;e++)this.initSelect2(t.item(e))},initSelect2:function(t){var e=this,a=jQuery(t),n=!1,i=a.data("popover"),r=null,o=10;a.removeClass("form-control"),a.find('option[value=""]').length||"true"===a.attr("data-sonata-select2-allow-clear")?n=!0:"false"===a.attr("data-sonata-select2-allow-clear")&&(n=!1),a.attr("data-sonata-select2-maximumSelectionSize")&&(r=a.attr("data-sonata-select2-maximumSelectionSize")),a.attr("data-sonata-select2-minimumResultsForSearch")&&(o=a.attr("data-sonata-select2-minimumResultsForSearch"));var d={width:function(){return e.getSelect2Width(window.Select2?this.element:a)},dropdownAutoWidth:!0,minimumResultsForSearch:o,allowClear:n,maximumSelectionSize:r};a.select2(d),void 0!==i&&a.select2("container").popover(i.options),a.attr("data-reload-selector")&&a.on("change",(function(t){var n=$(a.attr("data-reload-selector")).first();e.updateSelectChoices(n,t)}))},updateSelectChoices:function(t,e){var a,n,i=this;e.added?(a="added",n=e.added.id):(a="removed",n=e.removed.id);var r={changeData:{entityId:t.attr("data-entity-id"),groupValues:e.val,type:a,groupId:n}},o=new XMLHttpRequest;o.onreadystatechange=function(){if(4===this.readyState&&200===this.status){var e=JSON.parse(this.responseText);t.select2("destroy");for(var a=t.val(),n=t.get(0),r=n.options.length-1;r>=0;r--)n.options[r]=null;for(var o=0,d=e.data.serviceList.length;o<d;o++){var l=document.createElement("option"),s=e.data.serviceList[o];l.value=s.id,l.innerHTML=s.text,n.appendChild(l)}a||(a=[]);var c=[];if(e.data.removed&&e.data.removed.length>0)for(var u=0,p=a.length;u<p;u++){for(var h=parseInt(a[u]),m=!1,v=0,f=e.data.removed.length;v<f;v++)if(h===e.data.removed[v].id){m=!0;break}m||c.push(h)}else if(e.data.added&&e.data.added.length>0){c=a;for(var S=0,g=e.data.added.length;S<g;S++){var w=e.data.added[S];a.indexOf(w.id)<0&&c.push(w.id)}}$(n).val(c),$(n).select2({width:function(){return i.getSelect2Width(window.Select2?this.element:select)},dropdownAutoWidth:!0,minimumResultsForSearch:10,allowClear:!0,maximumSelectionSize:null})}};var d=t.attr("data-url");d||(d=$(t.data("select2").select).attr("data-url")),o.open("POST",d,!0),o.setRequestHeader("Content-Type","application/json"),o.send(JSON.stringify(r))},getSelect2Width:function(t){var e=/width:(auto|(([-+]?([0-9]*\.)?[0-9]+)(px|em|ex|%|in|cm|mm|pt|pc)))/i,a=t.attr("style");if(void 0!==a)for(var n=a.split(";"),i=0,r=n.length;i<r;i+=1){var o=n[i].replace(/\s/g,"").match(e);if(null!==o&&o.length>=1)return o[1]}return(a=t.css("width")).indexOf("%")>0?a:"100%"},addLoader:function(t){t.innerHTML='<div class="chart-loader"><i class="fa fa-cog fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span></div>'}}})?n.call(e,a,e,t):n)||(t.exports=i)}}]);