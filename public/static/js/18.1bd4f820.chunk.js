(this["webpackJsonpjumbo-hooks"]=this["webpackJsonpjumbo-hooks"]||[]).push([[18],{466:function(e,a,t){"use strict";var r=t(0),c=t.n(r),n=t(482),l=t(483),i=function(e,a,t){return 0===t?"#/":"#/"+e.split(a)[0]+a};a.a=function(e){var a=e.title,t=e.match.path.substr(1),r=t.split("/");return c.a.createElement("div",{className:"page-heading d-sm-flex justify-content-sm-between align-items-sm-center"},c.a.createElement("h2",{className:"title mb-3 mb-sm-0"},a),c.a.createElement(n.a,{className:"mb-0",tag:"nav"},r.map((function(e,a){return c.a.createElement(l.a,{active:r.length===a+1,tag:r.length===a+1?"span":"a",key:a,href:i(t,e,a)},function(e){var a=e.split("-");return a.length>1?a[0].charAt(0).toUpperCase()+a[0].slice(1)+" "+a[1].charAt(0).toUpperCase()+a[1].slice(1):e.charAt(0).toUpperCase()+e.slice(1)}(e))}))))}},467:function(e,a,t){"use strict";var r=t(0),c=t.n(r),n=function(e){var a=e.heading,t=e.children,r=e.styleName,n=e.cardStyle,l=e.childrenStyle,i=e.headerOutside,o=e.headingStyle;return c.a.createElement("div",{className:"".concat(r)},i&&c.a.createElement("div",{className:"jr-entry-header"},c.a.createElement("h3",{className:"entry-heading"},a),t.length>1&&c.a.createElement("div",{className:"entry-description"},t[0])),c.a.createElement("div",{className:"jr-card ".concat(n)},!i&&a&&c.a.createElement("div",{className:"jr-card-header ".concat(o)},c.a.createElement("h3",{className:"card-heading"},a),t.length>1&&c.a.createElement("div",{className:"sub-heading"},t[0])),c.a.createElement("div",{className:"jr-card-body ".concat(l)},t.length>1?t[1]:t)))};a.a=n,n.defaultProps={cardStyle:"",headingStyle:"",childrenStyle:"",styleName:"col-lg-6 col-sm-12"}},481:function(e,a,t){"use strict";var r=t(6),c=t(1),n=t(0),l=(t(2),t(5)),i=t(8),o=t(169),s=t(13),d=n.forwardRef((function(e,a){var t=e.children,i=e.classes,d=e.className,m=e.color,u=void 0===m?"default":m,p=e.component,b=void 0===p?"button":p,g=e.disabled,h=void 0!==g&&g,v=e.disableFocusRipple,f=void 0!==v&&v,y=e.focusVisibleClassName,N=e.size,j=void 0===N?"large":N,E=e.variant,C=void 0===E?"round":E,k=Object(r.a)(e,["children","classes","className","color","component","disabled","disableFocusRipple","focusVisibleClassName","size","variant"]);return n.createElement(o.a,Object(c.a)({className:Object(l.a)(i.root,d,"round"!==C&&i.extended,"large"!==j&&i["size".concat(Object(s.a)(j))],h&&i.disabled,{primary:i.primary,secondary:i.secondary,inherit:i.colorInherit}[u]),component:b,disabled:h,focusRipple:!f,focusVisibleClassName:Object(l.a)(i.focusVisible,y),ref:a},k),n.createElement("span",{className:i.label},t))}));a.a=Object(i.a)((function(e){return{root:Object(c.a)({},e.typography.button,{boxSizing:"border-box",minHeight:36,transition:e.transitions.create(["background-color","box-shadow","border"],{duration:e.transitions.duration.short}),borderRadius:"50%",padding:0,minWidth:0,width:56,height:56,boxShadow:e.shadows[6],"&:active":{boxShadow:e.shadows[12]},color:e.palette.getContrastText(e.palette.grey[300]),backgroundColor:e.palette.grey[300],"&:hover":{backgroundColor:e.palette.grey.A100,"@media (hover: none)":{backgroundColor:e.palette.grey[300]},"&$disabled":{backgroundColor:e.palette.action.disabledBackground},textDecoration:"none"},"&$focusVisible":{boxShadow:e.shadows[6]},"&$disabled":{color:e.palette.action.disabled,boxShadow:e.shadows[0],backgroundColor:e.palette.action.disabledBackground}}),label:{width:"100%",display:"inherit",alignItems:"inherit",justifyContent:"inherit"},primary:{color:e.palette.primary.contrastText,backgroundColor:e.palette.primary.main,"&:hover":{backgroundColor:e.palette.primary.dark,"@media (hover: none)":{backgroundColor:e.palette.primary.main}}},secondary:{color:e.palette.secondary.contrastText,backgroundColor:e.palette.secondary.main,"&:hover":{backgroundColor:e.palette.secondary.dark,"@media (hover: none)":{backgroundColor:e.palette.secondary.main}}},extended:{borderRadius:24,padding:"0 16px",width:"auto",minHeight:"auto",minWidth:48,height:48,"&$sizeSmall":{width:"auto",padding:"0 8px",borderRadius:17,minWidth:34,height:34},"&$sizeMedium":{width:"auto",padding:"0 16px",borderRadius:20,minWidth:40,height:40}},focusVisible:{},disabled:{},colorInherit:{color:"inherit"},sizeSmall:{width:40,height:40},sizeMedium:{width:48,height:48}}}),{name:"MuiFab"})(d)},482:function(e,a,t){"use strict";var r=t(1),c=t(21),n=t(0),l=t.n(n),i=t(2),o=t.n(i),s=t(52),d=t.n(s),m=t(17),u={tag:m.m,listTag:m.m,className:o.a.string,listClassName:o.a.string,cssModule:o.a.object,children:o.a.node,"aria-label":o.a.string},p=function(e){var a=e.className,t=e.listClassName,n=e.cssModule,i=e.children,o=e.tag,s=e.listTag,u=e["aria-label"],p=Object(c.a)(e,["className","listClassName","cssModule","children","tag","listTag","aria-label"]),b=Object(m.i)(d()(a),n),g=Object(m.i)(d()("breadcrumb",t),n);return l.a.createElement(o,Object(r.a)({},p,{className:b,"aria-label":u}),l.a.createElement(s,{className:g},i))};p.propTypes=u,p.defaultProps={tag:"nav",listTag:"ol","aria-label":"breadcrumb"},a.a=p},483:function(e,a,t){"use strict";var r=t(1),c=t(21),n=t(0),l=t.n(n),i=t(2),o=t.n(i),s=t(52),d=t.n(s),m=t(17),u={tag:m.m,active:o.a.bool,className:o.a.string,cssModule:o.a.object},p=function(e){var a=e.className,t=e.cssModule,n=e.active,i=e.tag,o=Object(c.a)(e,["className","cssModule","active","tag"]),s=Object(m.i)(d()(a,!!n&&"active","breadcrumb-item"),t);return l.a.createElement(i,Object(r.a)({},o,{className:s,"aria-current":n?"page":void 0}))};p.propTypes=u,p.defaultProps={tag:"li"},a.a=p},570:function(e,a,t){"use strict";t.r(a);var r=t(10),c=t(0),n=t.n(c),l=t(466),i=t(74),o=t(3),s=t(467),d=t(458),m=t(439),u=t(441),p=t(99),b=t(26),g=t(481);a.default=function(e){var a=e.location.state?e.location.state.id:null,t=Object(c.useState)(""),h=Object(r.a)(t,2),v=h[0],f=h[1],y=Object(c.useState)(0),N=Object(r.a)(y,2),j=N[0],E=N[1],C=Object(c.useState)(null),k=Object(r.a)(C,2),O=k[0],x=k[1],w=Object(c.useState)("https://via.placeholder.com/300x300"),S=Object(r.a)(w,2),T=S[0],z=S[1],M=Object(c.useState)(1),R=Object(r.a)(M,2),P=R[0],W=R[1],_=Object(c.useState)([]),A=Object(r.a)(_,2),V=A[0],D=A[1],F=Object(b.g)();Object(c.useEffect)((function(){p.a.get(p.b+"category/categoryList",{params:{id:a}}).then((function(e){D(e.data.category),a>0&&(E(e.data.project.category_id),W(e.data.project.status),f(e.data.project.name))}))}),[a]);return n.a.createElement("div",{className:"app-wrapper"},n.a.createElement(l.a,{match:e.match,title:n.a.createElement(o.a,{id:"sidebar.stickers.addnew"})}),n.a.createElement("div",{className:"row"},n.a.createElement("div",{className:"col-xl-3 col-lg-5 col-md-6 col-12"},n.a.createElement("div",{className:"jr-card pb-2"},n.a.createElement("div",{className:"jr-card-thumb"},n.a.createElement("img",{className:"card-img-top img-fluid",alt:"products",src:T})),n.a.createElement("input",{type:"file",id:"add_img",accept:".png",name:"add_img",style:{display:"none"},onChange:function(e){return function(e){e.preventDefault();var a=new FileReader,t=e.target.files[0];a.onloadend=function(){x(t),z(a.result)},a.readAsDataURL(t)}(e)}}),n.a.createElement("div",{className:"jr-card-social text-right"},n.a.createElement(g.a,{className:"jr-fab-btn bg-secondary text-white jr-btn-fab-xs mb-3",onClick:function(e){document.getElementById("add_img").click()}},n.a.createElement("i",{className:"zmdi zmdi-cloud-upload zmdi-hc-1x"}))))),n.a.createElement(s.a,{styleName:"col-xl-9 col-lg-7 col-md-6 col-12"},n.a.createElement("form",{className:"row",autoComplete:"off"},n.a.createElement("div",{className:"col-md-6 col-12"},n.a.createElement(d.a,{id:"sticker_name",label:"Name",value:v,onChange:function(e){return f(e.target.value)},margin:"normal",helperText:"Please Enter Name",fullWidth:!0})),n.a.createElement("div",{className:"col-md-6 col-12"},n.a.createElement(d.a,{id:"category",select:!0,label:"Category",value:j,onChange:function(e){return E(e.target.value)},SelectProps:{},helperText:"Please select Category",margin:"normal",fullWidth:!0},V.map((function(e){return n.a.createElement(u.a,{key:e.id,value:e.id},e.name)})))),n.a.createElement("div",{className:"col-md-6 col-12"},n.a.createElement(d.a,{id:"status",select:!0,label:"Status",value:P,onChange:function(e){W(e.target.value)},SelectProps:{},helperText:"Please select Status",margin:"normal",fullWidth:!0},[{id:1,content:"Active"},{id:0,content:"Deactive"}].map((function(e){return n.a.createElement(u.a,{key:e.id,value:e.id},e.content)})))),n.a.createElement("div",{className:"col-md-12 col-12 text-right"},n.a.createElement(m.a,{variant:"contained",color:"primary",className:"jr-btn",onClick:function(){return function(){var e=new FormData;e.append("id",a),e.append("name",v),e.append("category_id",j),e.append("stiker_img",O),e.append("status",P),p.a.post(p.b+"sticker/updateSticker",e).then((function(e){console.log(e.data),"success"===e.data.status?F.push({pathname:"/app/stickers/Categories",state:{id:e.data.id}}):"error"===e.data.status&&i.NotificationManager.error(e.data.msg,"Error!",5e3,(function(){}))})).catch((function(e){console.log("error:",e)}))}()}},"Save"))))),n.a.createElement(i.NotificationContainer,null))}}}]);
//# sourceMappingURL=18.1bd4f820.chunk.js.map