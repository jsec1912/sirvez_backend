(this["webpackJsonpjumbo-hooks"]=this["webpackJsonpjumbo-hooks"]||[]).push([[47],{1257:function(e,a,t){"use strict";t.r(a);var n=t(85),r=t(9),l=t(0),c=t.n(l),s=t(480),i=t(3),o=t(467),m=t(448),u=t(404),d=t(445),b=t(29),f=t(50),p=t(881),g=t.n(p),v=t(27);a.default=function(e){var a=Object(v.g)(),t=Object(l.useState)([{customer:e.location.state.customer_id,email:"",first_name:"",user_role:""}]),p=Object(r.a)(t,2),E=p[0],N=p[1],h=[{id:2,content:"Admin"},{id:6,content:"Normal"}],j=Object(l.useState)([]),O=Object(r.a)(j,2),_=O[0],C=O[1],k=function(e,a,t){var r=Object(n.a)(E);0===t?r[a].email=e:1===t?r[a].first_name=e:r[a].user_role=e,N(r)};return c.a.createElement("div",{className:"app-wrapper"},c.a.createElement(s.a,{match:e.match,title:c.a.createElement(i.a,{id:"sidebar.customers.inviteusers"})}),c.a.createElement("div",{className:"row animated slideInUpTiny animation-duration-3"},c.a.createElement("div",{className:"col-12"},c.a.createElement("div",{className:"jr-card"},E.map((function(a,t){return c.a.createElement("div",{className:"row d-flex align-items-center",key:t},c.a.createElement("div",{className:"col-xl-3 col-lg-3 col-md-3 col-12"},c.a.createElement(o.a,{id:"email",label:"Email",value:a.email,onChange:function(e){return k(e.target.value,t,0)},margin:"normal",helperText:"Please Enter Email",fullWidth:!0})),c.a.createElement("div",{className:"col-xl-3 col-lg-3 col-md-3 col-12"},c.a.createElement(o.a,{id:"firstname",label:"First Name",value:a.first_name,onChange:function(e){return k(e.target.value,t,1)},margin:"normal",helperText:"Please Enter First Name",fullWidth:!0})),c.a.createElement("div",{className:"col-xl-3 col-lg-3 col-md-3 col-12"},c.a.createElement(o.a,{id:"user_role",select:!0,label:"User Role",value:a.user_role,onChange:function(e){return k(e.target.value,t,2)},SelectProps:{},helperText:"Please select User Role",margin:"normal",fullWidth:!0},h.map((function(e){return c.a.createElement(m.a,{key:e.id,value:e.id},e.content)})))),t+1===E.length?c.a.createElement(u.a,{className:"icon-btn",onClick:function(){return function(a){var t=Object(n.a)(E);t.push({customer:e.location.state.customer_id,email:"",first_name:"",user_role:""}),N(t)}()}},c.a.createElement("i",{className:"zmdi zmdi-account-add"})):c.a.createElement(u.a,{className:"icon-btn",onClick:function(){return function(e){var a=Object(n.a)(E);a.splice(e,1),N(a)}(a)}},c.a.createElement("i",{className:"zmdi zmdi-delete"})),_[t]>=0?c.a.createElement("div",null,_[t]?c.a.createElement(u.a,{className:"border-2 size-30 text-success border-green"},c.a.createElement("i",{className:"zmdi zmdi-check"})):c.a.createElement(u.a,{className:"border-2 size-30 text-danger border-red"},c.a.createElement(g.a,null))):c.a.createElement("div",null))})),c.a.createElement("div",{className:"col-md-12 col-12 text-right"},c.a.createElement(d.a,{variant:"contained",color:"primary",className:"jr-btn",onClick:function(){return function(){var e=new FormData;e.append("pendingUser",E),b.a.post(b.b+"customers/pendingUser",e).then((function(e){"success"===e.data.status?C(e.data.success_key):"error"===e.data.status&&f.NotificationManager.error(e.data.msg,"Error!",1e3,(function(){})),f.NotificationManager.info("You sent email!")})).catch((function(e){f.NotificationManager.error(e,"Error!",1e3,(function(){}))}))}()}},c.a.createElement(i.a,{id:"sidebar.sendInvite"})),c.a.createElement(d.a,{variant:"contained",color:"primary",className:"jr-btn",onClick:function(){return a.go(-1)}},c.a.createElement(i.a,{id:"sidebar.back"}))),c.a.createElement(f.NotificationContainer,null)))))}},480:function(e,a,t){"use strict";var n=t(0),r=t.n(n),l=t(529),c=t(530),s=function(e,a,t){return 0===t?"/":"/"+e.split(a)[0]+a};a.a=function(e){var a=e.title,t=e.match,n=e.project_name,i=e.room_number,o=[],m=t.url.substr(1),u=m.split("/");return u.map((function(e,a){""===e&&u.splice(a,1),"live"===u[a-1]?o[a]=n||"":"live"===u[a-2]?o[a]=i||"":o[a]=e})),r.a.createElement("div",{className:"page-heading d-sm-flex justify-content-sm-between align-items-sm-center"},r.a.createElement("h2",{className:"title mb-3 mb-sm-0"},a),r.a.createElement(l.a,{className:"mb-0",tag:"nav"},u.map((function(e,a){if(0!=a)return r.a.createElement(c.a,{active:u.length===a+1,tag:u.length===a+1?"span":"a",key:a,href:s(m,e,a)},function(e){var a=e.split("-");return a.length>1?a[0].charAt(0).toUpperCase()+a[0].slice(1)+" "+a[1].charAt(0).toUpperCase()+a[1].slice(1):e.charAt(0).toUpperCase()+e.slice(1)}(o[a]))}))))}},529:function(e,a,t){"use strict";var n=t(1),r=t(22),l=t(0),c=t.n(l),s=t(2),i=t.n(s),o=t(54),m=t.n(o),u=t(18),d={tag:u.m,listTag:u.m,className:i.a.string,listClassName:i.a.string,cssModule:i.a.object,children:i.a.node,"aria-label":i.a.string},b=function(e){var a=e.className,t=e.listClassName,l=e.cssModule,s=e.children,i=e.tag,o=e.listTag,d=e["aria-label"],b=Object(r.a)(e,["className","listClassName","cssModule","children","tag","listTag","aria-label"]),f=Object(u.i)(m()(a),l),p=Object(u.i)(m()("breadcrumb",t),l);return c.a.createElement(i,Object(n.a)({},b,{className:f,"aria-label":d}),c.a.createElement(o,{className:p},s))};b.propTypes=d,b.defaultProps={tag:"nav",listTag:"ol","aria-label":"breadcrumb"},a.a=b},530:function(e,a,t){"use strict";var n=t(1),r=t(22),l=t(0),c=t.n(l),s=t(2),i=t.n(s),o=t(54),m=t.n(o),u=t(18),d={tag:u.m,active:i.a.bool,className:i.a.string,cssModule:i.a.object},b=function(e){var a=e.className,t=e.cssModule,l=e.active,s=e.tag,i=Object(r.a)(e,["className","cssModule","active","tag"]),o=Object(u.i)(m()(a,!!l&&"active","breadcrumb-item"),t);return c.a.createElement(s,Object(n.a)({},i,{className:o,"aria-current":l?"page":void 0}))};b.propTypes=d,b.defaultProps={tag:"li"},a.a=b},881:function(e,a,t){"use strict";var n=t(41);Object.defineProperty(a,"__esModule",{value:!0}),a.default=void 0;var r=n(t(0)),l=(0,n(t(173)).default)(r.default.createElement("path",{d:"M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"}),"Clear");a.default=l}}]);
//# sourceMappingURL=47.6f232e0e.chunk.js.map