(this["webpackJsonpjumbo-hooks"]=this["webpackJsonpjumbo-hooks"]||[]).push([[15],{466:function(e,a,t){"use strict";var l=t(0),r=t.n(l),n=t(482),c=t(483),i=function(e,a,t){return 0===t?"#/":"#/"+e.split(a)[0]+a};a.a=function(e){var a=e.title,t=e.match.path.substr(1),l=t.split("/");return r.a.createElement("div",{className:"page-heading d-sm-flex justify-content-sm-between align-items-sm-center"},r.a.createElement("h2",{className:"title mb-3 mb-sm-0"},a),r.a.createElement(n.a,{className:"mb-0",tag:"nav"},l.map((function(e,a){return r.a.createElement(c.a,{active:l.length===a+1,tag:l.length===a+1?"span":"a",key:a,href:i(t,e,a)},function(e){var a=e.split("-");return a.length>1?a[0].charAt(0).toUpperCase()+a[0].slice(1)+" "+a[1].charAt(0).toUpperCase()+a[1].slice(1):e.charAt(0).toUpperCase()+e.slice(1)}(e))}))))}},467:function(e,a,t){"use strict";var l=t(0),r=t.n(l),n=function(e){var a=e.heading,t=e.children,l=e.styleName,n=e.cardStyle,c=e.childrenStyle,i=e.headerOutside,s=e.headingStyle;return r.a.createElement("div",{className:"".concat(l)},i&&r.a.createElement("div",{className:"jr-entry-header"},r.a.createElement("h3",{className:"entry-heading"},a),t.length>1&&r.a.createElement("div",{className:"entry-description"},t[0])),r.a.createElement("div",{className:"jr-card ".concat(n)},!i&&a&&r.a.createElement("div",{className:"jr-card-header ".concat(s)},r.a.createElement("h3",{className:"card-heading"},a),t.length>1&&r.a.createElement("div",{className:"sub-heading"},t[0])),r.a.createElement("div",{className:"jr-card-body ".concat(c)},t.length>1?t[1]:t)))};a.a=n,n.defaultProps={cardStyle:"",headingStyle:"",childrenStyle:"",styleName:"col-lg-6 col-sm-12"}},481:function(e,a,t){"use strict";var l=t(6),r=t(1),n=t(0),c=(t(2),t(5)),i=t(8),s=t(169),o=t(13),d=n.forwardRef((function(e,a){var t=e.children,i=e.classes,d=e.className,m=e.color,u=void 0===m?"default":m,b=e.component,p=void 0===b?"button":b,h=e.disabled,g=void 0!==h&&h,f=e.disableFocusRipple,v=void 0!==f&&f,N=e.focusVisibleClassName,E=e.size,j=void 0===E?"large":E,y=e.variant,O=void 0===y?"round":y,C=Object(l.a)(e,["children","classes","className","color","component","disabled","disableFocusRipple","focusVisibleClassName","size","variant"]);return n.createElement(s.a,Object(r.a)({className:Object(c.a)(i.root,d,"round"!==O&&i.extended,"large"!==j&&i["size".concat(Object(o.a)(j))],g&&i.disabled,{primary:i.primary,secondary:i.secondary,inherit:i.colorInherit}[u]),component:p,disabled:g,focusRipple:!v,focusVisibleClassName:Object(c.a)(i.focusVisible,N),ref:a},C),n.createElement("span",{className:i.label},t))}));a.a=Object(i.a)((function(e){return{root:Object(r.a)({},e.typography.button,{boxSizing:"border-box",minHeight:36,transition:e.transitions.create(["background-color","box-shadow","border"],{duration:e.transitions.duration.short}),borderRadius:"50%",padding:0,minWidth:0,width:56,height:56,boxShadow:e.shadows[6],"&:active":{boxShadow:e.shadows[12]},color:e.palette.getContrastText(e.palette.grey[300]),backgroundColor:e.palette.grey[300],"&:hover":{backgroundColor:e.palette.grey.A100,"@media (hover: none)":{backgroundColor:e.palette.grey[300]},"&$disabled":{backgroundColor:e.palette.action.disabledBackground},textDecoration:"none"},"&$focusVisible":{boxShadow:e.shadows[6]},"&$disabled":{color:e.palette.action.disabled,boxShadow:e.shadows[0],backgroundColor:e.palette.action.disabledBackground}}),label:{width:"100%",display:"inherit",alignItems:"inherit",justifyContent:"inherit"},primary:{color:e.palette.primary.contrastText,backgroundColor:e.palette.primary.main,"&:hover":{backgroundColor:e.palette.primary.dark,"@media (hover: none)":{backgroundColor:e.palette.primary.main}}},secondary:{color:e.palette.secondary.contrastText,backgroundColor:e.palette.secondary.main,"&:hover":{backgroundColor:e.palette.secondary.dark,"@media (hover: none)":{backgroundColor:e.palette.secondary.main}}},extended:{borderRadius:24,padding:"0 16px",width:"auto",minHeight:"auto",minWidth:48,height:48,"&$sizeSmall":{width:"auto",padding:"0 8px",borderRadius:17,minWidth:34,height:34},"&$sizeMedium":{width:"auto",padding:"0 16px",borderRadius:20,minWidth:40,height:40}},focusVisible:{},disabled:{},colorInherit:{color:"inherit"},sizeSmall:{width:40,height:40},sizeMedium:{width:48,height:48}}}),{name:"MuiFab"})(d)},482:function(e,a,t){"use strict";var l=t(1),r=t(21),n=t(0),c=t.n(n),i=t(2),s=t.n(i),o=t(52),d=t.n(o),m=t(17),u={tag:m.m,listTag:m.m,className:s.a.string,listClassName:s.a.string,cssModule:s.a.object,children:s.a.node,"aria-label":s.a.string},b=function(e){var a=e.className,t=e.listClassName,n=e.cssModule,i=e.children,s=e.tag,o=e.listTag,u=e["aria-label"],b=Object(r.a)(e,["className","listClassName","cssModule","children","tag","listTag","aria-label"]),p=Object(m.i)(d()(a),n),h=Object(m.i)(d()("breadcrumb",t),n);return c.a.createElement(s,Object(l.a)({},b,{className:p,"aria-label":u}),c.a.createElement(o,{className:h},i))};b.propTypes=u,b.defaultProps={tag:"nav",listTag:"ol","aria-label":"breadcrumb"},a.a=b},483:function(e,a,t){"use strict";var l=t(1),r=t(21),n=t(0),c=t.n(n),i=t(2),s=t.n(i),o=t(52),d=t.n(o),m=t(17),u={tag:m.m,active:s.a.bool,className:s.a.string,cssModule:s.a.object},b=function(e){var a=e.className,t=e.cssModule,n=e.active,i=e.tag,s=Object(r.a)(e,["className","cssModule","active","tag"]),o=Object(m.i)(d()(a,!!n&&"active","breadcrumb-item"),t);return c.a.createElement(i,Object(l.a)({},s,{className:o,"aria-current":n?"page":void 0}))};b.propTypes=u,b.defaultProps={tag:"li"},a.a=b},568:function(e,a,t){"use strict";t.r(a);var l=t(10),r=t(0),n=t.n(r),c=t(466),i=t(3),s=t(467),o=t(458),d=t(439),m=t(15),u=t(99),b=t(440),p=t(74),h=t(481);a.default=function(e){var a=Object(r.useState)(!1),t=Object(l.a)(a,2),g=t[0],f=t[1],v=Object(r.useState)(""),N=Object(l.a)(v,2),E=N[0],j=N[1],y=Object(r.useState)(""),O=Object(l.a)(y,2),C=O[0],w=O[1],x=Object(r.useState)(""),S=Object(l.a)(x,2),k=S[0],_=S[1],W=Object(r.useState)(null),z=Object(l.a)(W,2),M=z[0],R=z[1],T=Object(r.useState)(""),A=Object(l.a)(T,2),F=A[0],U=A[1],V=Object(r.useState)(""),I=Object(l.a)(V,2),$=I[0],D=I[1],P=Object(r.useState)(""),B=Object(l.a)(P,2),J=B[0],H=B[1],L=Object(r.useState)(""),q=Object(l.a)(L,2),G=q[0],K=q[1],Q=Object(r.useState)(""),X=Object(l.a)(Q,2),Y=X[0],Z=X[1],ee=Object(m.e)((function(e){return e.settings})).width,ae=Object(r.useState)("https://via.placeholder.com/300x300"),te=Object(l.a)(ae,2),le=te[0],re=te[1];Object(r.useEffect)((function(){u.a.get(u.b+"user/userInfo").then((function(e){j(e.data.user.first_name),re(e.data.user.profile_pic?u.c+"upload/img/"+e.data.user.profile_pic:"https://via.placeholder.com/300x300"),w(e.data.user.last_name),_(e.data.user.job_title),U(e.data.user.user_type),D(e.data.user.mobile),H(e.data.user.status),Z(e.data.user.email),f(!0)}))}),[]);return n.a.createElement("div",{className:"app-wrapper"},n.a.createElement(c.a,{match:e.match,title:n.a.createElement(i.a,{id:"sidebar.settings.profile"})}),g?n.a.createElement("div",{className:"row"},n.a.createElement("div",{className:"col-xl-3 col-lg-4 col-md-5 col-12"},n.a.createElement("div",{className:"jr-card pb-2"},n.a.createElement("div",{className:"jr-card-thumb"},n.a.createElement("img",{className:"card-img-top img-fluid",alt:"products",src:le})),n.a.createElement("input",{type:"file",id:"profile_img",accept:"image/*",name:"profile_img",style:{display:"none"},onChange:function(e){!function(e){e.preventDefault();var a=new FileReader,t=e.target.files[0];a.onloadend=function(){R(t),re(a.result)},a.readAsDataURL(t)}(e)}}),n.a.createElement("div",{className:"jr-card-social text-center"},n.a.createElement(h.a,{className:"jr-fab-btn bg-light-blue accent-2 text-white",onClick:function(e){document.getElementById("profile_img").click()}},n.a.createElement("i",{className:"zmdi zmdi-cloud-upload zmdi-hc-2x"}))))),n.a.createElement(s.a,{styleName:"col-xl-9 col-lg-8 col-md-7 col-12"},n.a.createElement("form",{className:"row",autoComplete:"off"},n.a.createElement("div",{className:"col-md-6 col-12"},n.a.createElement(o.a,{id:"first_name",label:"First Name",value:E,onChange:function(e){return j(e.target.value)},margin:"normal",fullWidth:!0})),n.a.createElement("div",{className:"col-md-6 col-12"},n.a.createElement(o.a,{id:"last_name",label:"Last Name",value:C,onChange:function(e){return w(e.target.value)},margin:"normal",fullWidth:!0})),n.a.createElement("div",{className:"col-md-6 col-12"},n.a.createElement(o.a,{id:"job_title",label:"Job Title",value:k,onChange:function(e){return _(e.target.value)},margin:"normal",fullWidth:!0})),n.a.createElement("div",{className:"col-md-6 col-12"},n.a.createElement(o.a,{id:"user_role",label:"User Role",value:F,onChange:function(e){return U(e.target.value)},margin:"normal",fullWidth:!0})),n.a.createElement("div",{className:"col-md-6 col-12"},n.a.createElement(o.a,{id:"contact_number",label:"Contact Number",value:$,onChange:function(e){return D(e.target.value)},margin:"normal",fullWidth:!0})),n.a.createElement("div",{className:"col-md-6 col-12"},n.a.createElement(o.a,{id:"status",label:"Status",value:J,onChange:function(e){return H(e.target.value)},margin:"normal",fullWidth:!0})),n.a.createElement("div",{className:"col-md-6 col-12"},n.a.createElement(o.a,{id:"password",label:"Password",value:G,onChange:function(e){return K(e.target.value)},margin:"normal",fullWidth:!0})),n.a.createElement("div",{className:"col-md-6 col-12"},n.a.createElement(o.a,{id:"email_address",label:"Email Address",value:Y,onChange:function(e){return Z(e.target.value)},margin:"normal",fullWidth:!0})),n.a.createElement("div",{className:"col-md-12 col-12 text-right"},n.a.createElement(d.a,{variant:"contained",color:"primary",className:"jr-btn",onClick:function(){return function(){var e=new FormData;e.append("first_name",E),e.append("last_name",C),e.append("job_title",k),e.append("user_type",F),e.append("mobile",$),e.append("status",J),e.append("email",Y),e.append("profile_pic",M),u.a.post(u.b+"user/saveUser",e).then((function(e){"success"===e.data.status?p.NotificationManager.success(e.data.msg,"Success!",5e3,(function(){})):"error"===e.data.status&&p.NotificationManager.error(e.data.msg,"Error!",5e3,(function(){}))}))}()}},"Save"))))):n.a.createElement("div",{className:"loader-view",style:{height:ee>=1200?"calc(100vh - 259px)":"calc(100vh - 238px)"}},n.a.createElement(b.a,null)),n.a.createElement(p.NotificationContainer,null))}}}]);
//# sourceMappingURL=15.3c45bf15.chunk.js.map