"use strict";(self.webpackChunkkdn_ozg=self.webpackChunkkdn_ozg||[]).push([[180],{86180:function(t,s,n){n.r(s),n.d(s,{default:function(){return e}});var a=class{constructor(t){const s=$(t);this.$element=s,this.$modal=null,this.config=null,s.on("click",(t=>{t.preventDefault(),this.load()}))}_getConfig(){return null===this.config&&(this.config=this.$element.data("modal")),this.config}_getModal(){if(null===this.$modal){const t=this._getConfig();this.$modal=$(`\n                <div class="modal fade" role="dialog">\n                    <div class="modal-dialog">\n                        <div class="modal-content">\n                            <div class="modal-header">\n                                <button type="button" class="close" data-dismiss="modal">\n                                    <i class="fa fa-times"></i>\n                                </button>\n                                <h4 class="modal-title">${t.title}</h4>\n                            </div>\n                            <div class="modal-body">\n                                <p>${t.message}</p>\n                            </div>\n                            <div class="modal-footer">\n                                <button type="button" class="js-cancel btn btn-default" data-dismiss="modal">${t.cancel}</button>\n                                <button type="button" class="js-submit btn btn-primary">${t.submit}</button>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n            `).modal({show:!1})}return this.$modal}_closed(){this.$modal.remove(),this.$modal=null}_submit(){}_handleResponse(t){switch(t.type){case"reload":window.location.reload(!0);break;case"redirect":window.location.href=t.url;break;case"new-tab":window.open(t.url,"_blank"),this.$modal.modal("hide");break;case"content":const s=this.$modal,n=$(t.content),a=[".modal-header",".modal-body",".modal-footer"];if(n.filter(a.join(", ")).length>0)for(const t of a){const a=n.filter(t).first();a&&s.find(t).html(a.html())}else s.find(".modal-body").html($(t.content))}}load(){const t=this._getModal();t.on("click",".js-submit",(t=>{t.preventDefault(),$(t.currentTarget).addClass("disabled"),$(t.currentTarget).html('<i class="fa fa-spin fa-spinner"></i>'),this._submit()})),t.on("hidden.bs.modal",this._closed.bind(this)),t.modal("show")}};var e=class extends a{constructor(t){super(t),this.submitUrl=null,this.$form=null}_getConfig(){return null===this.config&&(super._getConfig(),this.config.message='<div class="text-center"><i class="fa fa-spinner fa-spin fa-4x"></i></div>',(!this.config.cancel||this.config.cancel.length<=0)&&(this.config.cancel='<i class="fa fa-times"></i>')),this.config}_getModalLoadUrl(){return this._getConfig().loadUrl}_getModalSubmitUrl(){return this.submitUrl||this._getConfig().submitUrl}_getModal(){if(null===this.$modal){const t=this._getConfig();this.$modal=$(`\n                <div class="modal fade" role="dialog">\n                    <div class="modal-dialog">\n                        <div class="modal-content">\n                            <div class="modal-header">\n                                <button type="button" class="close" data-dismiss="modal">\n                                    <i class="fa fa-times"></i>\n                                </button>\n                                <h4 class="modal-title">${t.title}</h4>\n                            </div>\n                            <div class="modal-body">\n                                <p>${t.message}</p>\n                            </div>\n                            <div class="modal-footer">\n                                <button type="button" class="js-cancel btn btn-default" data-dismiss="modal">${t.cancel}</button>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n            `).modal({show:!1})}return this.$modal}_submit(){$.ajax({type:"POST",url:this._getModalSubmitUrl(),data:this.$form.serialize(),headers:{"X-Requested-With":"XMLHttpRequest"}}).then((t=>{this._handleResponse(t)}))}_handleResponse(t){if(super._handleResponse(t),"content"===t.type){const t=this.$modal.find("form").first();this.$form=t,t&&t.attr("action")&&(this.submitUrl=t.attr("action"));const s=new CustomEvent("mb-form-update",{detail:{container:t}});document.dispatchEvent(s)}}load(){super.load();let t=this._getModalLoadUrl();t&&$.ajax({url:t,headers:{"X-Requested-With":"XMLHttpRequest"}}).then((t=>{this._handleResponse(t)}))}}}}]);