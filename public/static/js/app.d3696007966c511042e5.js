webpackJsonp([1],{"+iN9":function(t,e,n){"use strict";var a=n("Xxa5"),s=n.n(a),r=n("o3nj"),i=n("+vOg"),o=n("VSB1");e.a={name:"Info",data:function(){return{title:"PHP Info 的信息"}},computed:{html:function(){if(""===this.$store.state.phpInfo){var t=this;Object(r.a)(s.a.mark(function t(){return s.a.wrap(function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,Object(i.d)();case 2:case"end":return t.stop()}},t,this)}),!1,s.a.mark(function e(n){return s.a.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:200==Object(o.b)(n,"status")&&"OK"==Object(o.b)(n,"statusText")?t.$store.commit("setPhpInfo",Object(o.b)(n,"data")):console.info("error",n);case 1:case"end":return e.stop()}},e,this)}))}return this.$store.state.phpInfo}}}},"+vOg":function(t,e,n){"use strict";n.d(e,"e",function(){return s}),n.d(e,"c",function(){return r}),n.d(e,"d",function(){return i}),n.d(e,"b",function(){return o}),n.d(e,"a",function(){return c});var a=n("vLgD"),s=function(){return Object(a.a)("/api/vue/php")},r=function(){return Object(a.a)("/api/vue/index")},i=function(){return Object(a.a)("/api/vue/info")},o=function(){return Object(a.a)("/api/api/index")},c=function(t){return Object(a.b)("/api/api/create",t)}},"/tMJ":function(t,e,n){"use strict";var a=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("select",{staticClass:"form-control",attrs:{name:t.name,id:t.id}},t._l(t.data,function(e){return n("option",{key:e.value,domProps:{value:e.value}},[t._v(t._s(e.label))])}),0)},s=[],r={render:a,staticRenderFns:s};e.a=r},"2uFj":function(t,e,n){"use strict";var a={serverHost:"/vue-api.php",resourceUrl:"/resource"};e.a=a},"4lxn":function(t,e,n){"use strict";e.a={name:"Select",props:{name:String,id:String,data:Array}}},"63LE":function(t,e,n){"use strict";var a=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",[n("div",{staticClass:"page-header"},[n("h1",{domProps:{innerHTML:t._s(t.title)}})]),t._v(" "),n("div",{staticClass:"panel-group",attrs:{id:"accordion",role:"tablist","aria-multiselectable":"true"}},t._l(t.lists,function(t,e){return n("item",{key:e,attrs:{data:t}})}),1)])},s=[],r={render:a,staticRenderFns:s};e.a=r},"96x2":function(t,e){},"AN/r":function(t,e,n){"use strict";function a(t){n("96x2")}var s=n("I4+v"),r=n("ayfc"),i=n("VU/8"),o=a,c=i(s.a,r.a,!1,o,"data-v-5b843415",null);e.a=c.exports},CXEK:function(t,e,n){"use strict";var a=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"col-md-3"},[n("p",{staticClass:"text-primary"},[n("a",{attrs:{href:t.item.href,target:t.target}},[t._v(t._s(t.item.name))])])])},s=[],r={render:a,staticRenderFns:s};e.a=r},"I4+v":function(t,e,n){"use strict";(function(t){var a=n("Xxa5"),s=n.n(a),r=n("wi0O"),i=n("o3nj"),o=n("VSB1"),c=n("+vOg");e.a={name:"Index",data:function(){return{type_list:[],method_list:[],input_list:[{}],html:""}},components:{Select:r.a},methods:{handleDelete:function(t){var e=this.input_list;e.splice(t,1),this.input_list=e},handleCreateInput:function(){this.input_list.push({})},handleCopy:function(){Object(o.a)(t("#code").get(0)),t("#myModal").modal("hide")},handleSubmit:function(){var e=this,n=t("#form").serialize();Object(i.a)(s.a.mark(function a(){var r;return s.a.wrap(function(a){for(;;)switch(a.prev=a.next){case 0:return a.next=2,Object(c.a)(n);case 2:r=a.sent,e.html=r,t("#myModal").modal({backdrop:"static"});case 5:case"end":return a.stop()}},a,this)}))}},created:function(){var t=this;Object(i.a)(s.a.mark(function e(){var n;return s.a.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,Object(c.b)();case 2:n=e.sent,t.type_list=Object(o.c)(Object(o.b)(n,"type_list",{})),t.method_list=Object(o.c)(Object(o.b)(n,"method_list",{}));case 5:case"end":return e.stop()}},e,this)}))}}}).call(e,n("7t+N"))},IcnI:function(t,e,n){"use strict";var a=n("7+uW"),s=n("NYxO");a.a.use(s.a),e.a=new s.a.Store({state:{php:{version:"",os:""},phpInfo:"",lists:[]},mutations:{setPhp:function(t,e){t.php=e},setPhpInfo:function(t,e){t.phpInfo=e},setLists:function(t,e){t.lists=e}},getters:{php:function(t){return t.php},phpInfo:function(t){return t.phpInfo}}})},NHnr:function(t,e,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var a=n("Xxa5"),s=n.n(a),r=n("7+uW"),i=n("YaEn"),o=n("2uFj"),c=n("IcnI"),u=n("qb6w"),l=(n.n(u),n("Bb4J")),d=(n.n(l),n("o3nj")),p=n("+vOg");r.a.config.productionTip=!1,r.a.prototype.Config=o.a,new r.a({el:"#app",router:i.a,store:c.a,data:function(){return{strAceUrl:this.Config.resourceUrl+"/ace"}},methods:{goHome:function(){this.$router.push({path:"/"})}},computed:{php:function(){if(""===this.$store.state.php.version){var t=this;Object(d.a)(s.a.mark(function e(){var n;return s.a.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,Object(p.e)();case 2:n=e.sent,t.$store.commit("setPhp",n);case 4:case"end":return e.stop()}},e,this)}))}return this.$store.state.php}}})},OtQl:function(t,e,n){"use strict";var a=n("jGb6"),s=n("63LE"),r=n("VU/8"),i=r(a.a,s.a,!1,null,null,null);e.a=i.exports},T0MJ:function(t,e,n){"use strict";e.a={name:"my-component",props:{item:{required:!0},target:{type:String,default:"_self"}}}},VSB1:function(t,e,n){"use strict";n.d(e,"b",function(){return o}),n.d(e,"c",function(){return c}),n.d(e,"a",function(){return u});var a=n("bOdI"),s=n.n(a),r=n("SeUI"),i=n.n(r),o=function(t,e,n){return i.a.get(t,e,n)},c=function(t){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"label",n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"value",a=[];for(var r in t){var i;a.push((i={},s()(i,e,r),s()(i,n,t[r]),i))}return a},u=function(t){var e,n,a="INPUT"===t.tagName||"TEXTAREA"===t.tagName;if(a)s=t,e=t.selectionStart,n=t.selectionEnd;else{if(!(s=document.getElementById("_hiddenCopyText_"))){var s=document.createElement("textarea");s.style.position="absolute",s.style.left="-9999px",s.style.top="0",s.id="_hiddenCopyText_",document.body.appendChild(s)}s.textContent=t.textContent}var r=document.activeElement;s.focus(),s.setSelectionRange(0,s.value.length);var i;try{i=document.execCommand("copy")}catch(t){i=!1}return r&&"function"==typeof r.focus&&r.focus(),a?t.setSelectionRange(e,n):s.textContent="",i}},YK89:function(t,e,n){"use strict";var a=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",[n("div",{staticClass:"page-header"},[n("h1",[t._v(t._s(t.title))])]),t._v(" "),n("div",{staticClass:"row",domProps:{innerHTML:t._s(t.html)}})])},s=[],r={render:a,staticRenderFns:s};e.a=r},YaEn:function(t,e,n){"use strict";var a=n("7+uW"),s=n("/ocq"),r=n("OtQl"),i=n("fQ+h"),o=n("AN/r");a.a.use(s.a),e.a=new s.a({routes:[{path:"/",name:"home",component:r.a},{path:"/info",name:"info",component:i.a},{path:"/api",component:o.a}]})},ayfc:function(t,e,n){"use strict";var a=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"row"},[n("div",{staticClass:"col-md-12"},[t._m(0),t._v(" "),n("div",{staticClass:"page-content"},[n("form",{attrs:{method:"POST",id:"form"},on:{submit:function(e){return e.preventDefault(),t.handleSubmit(e)}}},[t._m(1),t._v(" "),t._m(2),t._v(" "),n("div",{staticClass:"form-group"},[n("label",{attrs:{for:"method"}},[t._v(" 接口请求方式 ")]),t._v(" "),n("Select",{attrs:{name:"api[method]",id:"method",data:t.method_list}})],1),t._v(" "),n("div",{staticClass:"form-group"},[n("div",[n("label",{staticClass:"col-sm-2 control-label"},[t._v(" 接口请求参数 ")]),t._v(" "),n("div",{staticClass:"col-sm-10"},[t._l(t.input_list,function(e,a){return n("div",{key:a,staticClass:"form-inline params-div"},[n("div",{staticClass:"form-group"},[n("label",[t._v("参数")]),t._v(" "),n("input",{staticClass:"form-control",attrs:{type:"text",name:"api[params]["+a+"][name]",required:"required",placeholder:"参数名称"}})]),t._v(" "),n("div",{staticClass:"form-group"},[n("label",{staticClass:"sr-only"},[t._v("参数类型")]),t._v(" "),n("Select",{attrs:{name:"api[params]["+a+"][type]",data:t.type_list}})],1),t._v(" "),n("div",{staticClass:"form-group"},[n("label",{staticClass:"sr-only"},[t._v("是否必填")]),t._v(" "),n("Select",{attrs:{name:"api[params]["+a+"][required]",data:[{label:"必填",value:"必填"},{label:"非必填",value:"非必填"}]}})],1),t._v(" "),n("div",{staticClass:"form-group"},[n("label",{staticClass:"sr-only"},[t._v("参数说明")]),t._v(" "),n("input",{staticClass:"form-control",attrs:{type:"text",name:"api[params]["+a+"][desc]",placeholder:"参数说明"}})]),t._v(" "),n("div",{staticClass:"form-group"},[n("button",{staticClass:"btn btn-danger delete",attrs:{type:"button"},on:{click:function(e){return t.handleDelete(a)}}},[t._v("\n                    删除\n                  ")])])])}),t._v(" "),n("div",{staticStyle:{"margin-top":"15px"}},[n("button",{staticClass:"btn btn-info",attrs:{type:"button"},on:{click:t.handleCreateInput}},[t._v("\n                  添 加\n                ")])])],2)])]),t._v(" "),n("button",{staticClass:"btn btn-success",attrs:{type:"submit"}},[t._v("保存")]),t._v(" "),n("button",{staticClass:"btn btn-warning",attrs:{type:"reset"}},[t._v("重置")])])])]),t._v(" "),n("div",{staticClass:"modal fade",attrs:{id:"myModal",tabindex:"-1",role:"dialog","aria-labelledby":"myModalLabel"}},[n("div",{staticClass:"modal-dialog",attrs:{role:"document"}},[n("div",{staticClass:"modal-content"},[t._m(3),t._v(" "),n("div",{staticClass:"modal-body"},[n("pre",{attrs:{id:"code"},domProps:{innerHTML:t._s(t.html)}})]),t._v(" "),n("div",{staticClass:"modal-footer"},[n("button",{staticClass:"btn btn-default",attrs:{type:"button","data-dismiss":"modal"}},[t._v("关闭")]),t._v(" "),n("button",{staticClass:"btn btn-primary",attrs:{type:"button",id:"btn-copy"},on:{click:t.handleCopy}},[t._v(" 复制信息")])])])])])])},s=[function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"page-header"},[n("h1",[t._v(" API文档生成\n        "),n("small",[t._v(" 请认真填写表单数据信息")])])])},function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"form-group"},[n("label",{attrs:{for:"title"}},[t._v(" 接口名称 ")]),t._v(" "),n("input",{staticClass:"form-control",attrs:{type:"text",id:"title",name:"api[title]",required:"required",placeholder:"请输入接口名称"}})])},function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"form-group"},[n("label",{attrs:{for:"address"}},[t._v(" 接口地址 ")]),t._v(" "),n("input",{staticClass:"form-control",attrs:{type:"text",id:"address",name:"api[url]",required:"required",placeholder:"请输入接口地址"}})])},function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"modal-header"},[n("button",{staticClass:"close",attrs:{type:"button","data-dismiss":"modal","aria-label":"Close"}},[n("span",{attrs:{"aria-hidden":"true"}},[t._v("×")])]),t._v(" "),n("h4",{staticClass:"modal-title",attrs:{id:"myModalLabel"}},[t._v("API Markdown 文档")])])}],r={render:a,staticRenderFns:s};e.a=r},"fQ+h":function(t,e,n){"use strict";var a=n("+iN9"),s=n("YK89"),r=n("VU/8"),i=r(a.a,s.a,!1,null,null,null);e.a=i.exports},iVmO:function(t,e,n){"use strict";var a=function(){var t=this,e=t.$createElement,n=t._self._c||e;return t.data.title&&t.data.lists&&t.data.lists.length>0?n("div",{staticClass:"panel panel-default"},[n("div",{staticClass:"panel-heading",attrs:{role:"tab"}},[n("h4",{staticClass:"panel-title"},[n("span",{staticClass:"collapsed"},[t._v(" "+t._s(t.data.title)+" ")])])]),t._v(" "),n("div",{staticClass:"panel-collapse collapse in",attrs:{id:"collapseThree",role:"tabpanel","aria-labelledby":"headingTwo"}},[n("div",{staticClass:"panel-body"},[n("div",{staticClass:"row"},t._l(t.data.lists,function(e){return n("my-component",{key:e.name,attrs:{item:e,target:"本地站点"==t.data.title?"_blank":"_self"}})}),1)])])]):t._e()},s=[],r={render:a,staticRenderFns:s};e.a=r},jGb6:function(t,e,n){"use strict";var a=n("Xxa5"),s=n.n(a),r=n("w0UY"),i=n("o3nj"),o=n("+vOg");e.a={name:"index",data:function(){return{title:"我的Web服务器 <small> 选择需要进行的操作 </small>"}},components:{Item:r.a},computed:{lists:function(){if(0===this.$store.state.lists.length){var t=this;Object(i.a)(s.a.mark(function e(){var n;return s.a.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,Object(o.c)();case 2:n=e.sent,t.$store.commit("setLists",n);case 4:case"end":return e.stop()}},e,this)}))}return this.$store.state.lists}}}},n8qw:function(t,e,n){"use strict";n.d(e,"a",function(){return f});var a=n("Xxa5"),s=n.n(a),r=n("mvHQ"),i=n.n(r),o=n("Zrlr"),c=n.n(o),u=n("wxAW"),l=n.n(u),d=n("VSB1"),p=function(){function t(e){c()(this,t),this.info=e}return l()(t,null,[{key:"instance",value:function(e){return e instanceof t?e:new t(e)}}]),l()(t,[{key:"isIgnore",value:function(){return Object(d.b)(this.info,"ignore")}},{key:"getMsg",value:function(){return Object(d.b)(this.info,"msg")||i()(this.info)}}]),t}(),f=s.a.mark(function t(e){return s.a.wrap(function(t){for(;;)switch(t.prev=t.next){case 0:console.info("error handle:",e),e=p.instance(e),e.isIgnore()||alert(e.getMsg());case 3:case"end":return t.stop()}},t,this)})},o3nj:function(t,e,n){"use strict";n.d(e,"a",function(){return c});var a=n("Xxa5"),s=n.n(a),r=n("sqs/"),i=n.n(r),o=n("n8qw"),c=function(t){var e=(!(arguments.length>1&&void 0!==arguments[1])||arguments[1],arguments.length>2&&void 0!==arguments[2]?arguments[2]:o.a);window.errorHandle&&(e=window.errorHandle),i()(s.a.mark(function e(){return s.a.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,t();case 2:case"end":return e.stop()}},e,this)})).catch(function(t){i()(s.a.mark(function n(){return s.a.wrap(function(n){for(;;)switch(n.prev=n.next){case 0:if(!e){n.next=3;break}return n.next=3,e(t);case 3:case"end":return n.stop()}},n,this)}))})}},paAy:function(t,e,n){"use strict";var a=n("ssQg");e.a={props:["data"],components:{MyComponent:a.a}}},qb6w:function(t,e){},ssQg:function(t,e,n){"use strict";var a=n("T0MJ"),s=n("CXEK"),r=n("VU/8"),i=r(a.a,s.a,!1,null,null,null);e.a=i.exports},vLgD:function(t,e,n){"use strict";n.d(e,"a",function(){return d}),n.d(e,"b",function(){return p});var a=n("Dd8w"),s=n.n(a),r=n("//Fk"),i=n.n(r),o=n("mtWM"),c=n.n(o),u=n("VSB1"),l=function(t){return new i.a(function(e,n){c()(t).then(function(t){var a=Object(u.b)(t,"data");"success"==Object(u.b)(a,"status")&&200===Object(u.b)(a,"code")?e(Object(u.b)(a,"data")):n(t)}).catch(function(t){n(t)})})},d=function(t){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{},n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{},a=s()({url:t,data:e,method:"get"},n);return l(a)},p=function(t){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{},n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{},a=s()({url:t,data:e,method:"post"},n);return l(a)}},w0UY:function(t,e,n){"use strict";var a=n("paAy"),s=n("iVmO"),r=n("VU/8"),i=r(a.a,s.a,!1,null,null,null);e.a=i.exports},wi0O:function(t,e,n){"use strict";var a=n("4lxn"),s=n("/tMJ"),r=n("VU/8"),i=r(a.a,s.a,!1,null,null,null);e.a=i.exports}},["NHnr"]);
//# sourceMappingURL=app.d3696007966c511042e5.js.map