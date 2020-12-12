/*! For license information please see 59.9cc741db.chunk.js.LICENSE.txt */
(this["webpackJsonpjumbo-hooks"]=this["webpackJsonpjumbo-hooks"]||[]).push([[59],{1389:function(e,t,a){"use strict";a.r(t);var n=a(0),c=a.n(n),r=a(480),i=a(3),l=a(9),o=a(552),s=a.n(o),d=a(1370),m=a(1374),u=a(1373),f=a(1375),p=a(1371),h=a(1393),g=a(1372),b=a(1392),E=a(450),v=a(255),y=a(1391),N=a(252),j=a(448),w=a(404),S=function(e){var t=Object(n.useState)(!1),a=Object(l.a)(t,2),r=a[0],i=a[1],o=Object(n.useState)(void 0),s=Object(l.a)(o,2),d=s[0],m=s[1],u=e.selectedData,f=e.onSelectRow,p=e.menu_rows,h=p||[{content:"Delete",id:2}],g=function(){i(!1)};return c.a.createElement("div",null,c.a.createElement(w.a,{onClick:function(e){i(!0),m(e.currentTarget)}},c.a.createElement("i",{className:"zmdi zmdi-more-vert"})),c.a.createElement(N.a,{anchorEl:d,open:r,onClose:g,key:u.id,MenuListProps:{style:{width:150,paddingTop:0,paddingBottom:0}}},h.map((function(e){return c.a.createElement(j.a,{key:u+"-"+e.content,onClick:function(){g(),f(u,e.id)}},e.content)}))))},C=a(67),O=a(14),k=a(27),P=a(446),x=a(29),B=a(50),D=a(490),_=a.n(D),L=[{id:"id",align:!1,disablePadding:!0,label:"ID"},{id:"notification",align:!0,disablePadding:!1,label:"Notification"},{id:"created_by",align:!0,disablePadding:!1,label:"Created By"},{id:"customer",align:!0,disablePadding:!1,label:"Customer"},{id:"created_date",align:!0,disablePadding:!1,label:"Created Date"},{id:"actions",align:!0,numeric:"right",disablePadding:!1,label:"Actions"}],A=function(e){var t=e.order,a=e.orderBy;return c.a.createElement(p.a,null,c.a.createElement(g.a,null,L.map((function(n){return c.a.createElement(u.a,{key:n.id,align:n.numeric},c.a.createElement(y.a,{title:"Sort",placement:n.numeric?"bottom-end":"bottom-start",enterDelay:300},c.a.createElement(b.a,{active:a===n.id,direction:t,onClick:(r=n.id,function(t){e.onRequestSort(t,r)})},n.label)));var r}))))},R=function(e){Object(k.g)();var t=e.SearchNotification;return c.a.createElement(E.a,{className:"table-header"},c.a.createElement("div",{className:"title"},c.a.createElement(v.a,{variant:"h6"},"Notifications")),c.a.createElement("div",{className:"col-md-3 col-lg-3 col-sx-6 col-6 ml-auto"},c.a.createElement(C.a,{placeholder:"Search ...",onChange:function(e){return t(e.target.value)}})))},M=function(){var e=Object(n.useState)("asc"),t=Object(l.a)(e,2),a=t[0],r=t[1],o=Object(n.useState)("id"),p=Object(l.a)(o,2),b=p[0],E=p[1],v=Object(n.useState)([]),y=Object(l.a)(v,2),N=y[0],j=y[1],w=Object(n.useState)(!1),C=Object(l.a)(w,2),k=C[0],D=C[1],L=Object(O.e)((function(e){return e.settings})).width,M=Object(n.useState)(0),T=Object(l.a)(M,2),U=T[0],I=T[1],q=Object(n.useState)(10),z=Object(l.a)(q,2),J=z[0],K=z[1],F=Object(n.useState)([]),G=Object(l.a)(F,2),H=G[0],W=G[1],Y=Object(n.useState)([]),Q=Object(l.a)(Y,2),V=Q[0],X=Q[1],Z=Object(n.useState)(!1),$=Object(l.a)(Z,2),ee=$[0],te=$[1];Object(n.useEffect)((function(){ae()}),[]);var ae=function(){x.a.get(x.b+"notification/getNotification").then((function(e){W(e.data.notifications),X(e.data.notifications),D(!0)})).catch((function(e){te(!0)}))},ne=function(e,t){var a=N.indexOf(t),n=[];-1===a?n=n.concat(N,t):0===a?n=n.concat(N.slice(1)):a===N.length-1?n=n.concat(N.slice(0,-1)):a>0&&(n=n.concat(N.slice(0,a),N.slice(a+1))),j(n)},ce=function(e,t){var a=new FormData;a.append("id",e),x.a.post(x.b+"notification/deleteNotification",a).then((function(e){"success"===e.data.status?(ae(),B.NotificationManager.info("You removed selected notification")):"error"===e.data.status&&B.NotificationManager.error(e.data.msg,"Error!",1e3,(function(){}))})).catch((function(e){B.NotificationManager.error(e,"Error!",1e3,(function(){}))}))};return c.a.createElement("div",null,c.a.createElement(R,{SearchNotification:function(e){X(H.filter((function(t){return t.notification.toLowerCase().includes(e.toLowerCase())})))}}),k?c.a.createElement("div",{className:"flex-auto"},c.a.createElement("div",{className:"table-responsive-material"},c.a.createElement(d.a,{className:""},c.a.createElement(A,{order:a,orderBy:b,onRequestSort:function(e,t){var n;"desc"===(n=b===t&&"asc"===a?"desc":"asc")?V.sort((function(e,a){return a[t]<e[t]?-1:1})):V.sort((function(e,a){return e[t]<a[t]?-1:1})),r(n),E(t)}}),c.a.createElement(m.a,null,V.length>0?V.slice(U*J,U*J+J).map((function(e){return c.a.createElement(g.a,{hover:!0,key:e.id,onKeyDown:function(t){return function(e,t){"space"===s()(e)&&ne(e,t)}(t,e.id)},tabIndex:-1},c.a.createElement(u.a,null,e.id),c.a.createElement(u.a,null,e.notification),c.a.createElement(u.a,null,e.first_name),c.a.createElement(u.a,null,e.company_name),c.a.createElement(u.a,null,e.created_at),c.a.createElement(u.a,{className:"text-right"},c.a.createElement(S,{key:e.id,selectedData:e.id,onSelectRow:ce})))})):c.a.createElement("tr",null,c.a.createElement("td",{className:"text-danger",colSpan:"6"},c.a.createElement(i.a,{id:"table.noData"})))),c.a.createElement(f.a,null,c.a.createElement(g.a,null,c.a.createElement(h.a,{count:V.length,rowsPerPage:J,page:U,onChangePage:function(e,t){I(t)},onChangeRowsPerPage:function(e){K(e.target.value)}}))))),c.a.createElement(B.NotificationContainer,null)):c.a.createElement("div",{className:"loader-view",style:{height:L>=1200?"calc(100vh - 259px)":"calc(100vh - 238px)"}},c.a.createElement(P.a,null)),c.a.createElement(_.a,{show:ee,warning:!0,confirmBtnText:"Go Back",confirmBtnBsStyle:"danger",cancelBtnBsStyle:"default",title:"Warning!",onConfirm:function(){return History.go(-1)}},"Data not cached, so you can not view this page."))},T=a(487);t.default=function(e){return c.a.createElement("div",{className:"app-wrapper"},c.a.createElement(r.a,{match:e.match,title:c.a.createElement(i.a,{id:"sidebar.settings.notifications"})}),c.a.createElement("div",{className:"row animated slideInUpTiny animation-duration-3"},c.a.createElement(T.a,{styleName:"col-12"},c.a.createElement(M,null))))}},480:function(e,t,a){"use strict";var n=a(0),c=a.n(n),r=a(529),i=a(530),l=function(e,t,a){return 0===a?"/":"/"+e.split(t)[0]+t};t.a=function(e){var t=e.title,a=e.match,n=e.project_name,o=e.room_number,s=[],d=a.url.substr(1),m=d.split("/");return m.map((function(e,t){""===e&&m.splice(t,1),"live"===m[t-1]?s[t]=n||"":"live"===m[t-2]?s[t]=o||"":s[t]=e})),c.a.createElement("div",{className:"page-heading d-sm-flex justify-content-sm-between align-items-sm-center"},c.a.createElement("h2",{className:"title mb-3 mb-sm-0"},t),c.a.createElement(r.a,{className:"mb-0",tag:"nav"},m.map((function(e,t){if(0!=t)return c.a.createElement(i.a,{active:m.length===t+1,tag:m.length===t+1?"span":"a",key:t,href:l(d,e,t)},function(e){var t=e.split("-");return t.length>1?t[0].charAt(0).toUpperCase()+t[0].slice(1)+" "+t[1].charAt(0).toUpperCase()+t[1].slice(1):e.charAt(0).toUpperCase()+e.slice(1)}(s[t]))}))))}},487:function(e,t,a){"use strict";var n=a(0),c=a.n(n),r=function(e){var t=e.heading,a=e.children,n=e.styleName,r=e.cardStyle,i=e.childrenStyle,l=e.headerOutside,o=e.headingStyle;return c.a.createElement("div",{className:"".concat(n)},l&&c.a.createElement("div",{className:"jr-entry-header"},c.a.createElement("h3",{className:"entry-heading"},t),a.length>1&&c.a.createElement("div",{className:"entry-description"},a[0])),c.a.createElement("div",{className:"jr-card ".concat(r)},!l&&t&&c.a.createElement("div",{className:"jr-card-header ".concat(o)},c.a.createElement("h3",{className:"card-heading"},t),a.length>1&&c.a.createElement("div",{className:"sub-heading"},a[0])),c.a.createElement("div",{className:"jr-card-body ".concat(i)},a.length>1?a[1]:a)))};t.a=r,r.defaultProps={cardStyle:"",headingStyle:"",childrenStyle:"",styleName:"col-lg-6 col-sm-12"}},552:function(e,t){function a(e){if(e&&"object"===typeof e){var t=e.which||e.keyCode||e.charCode;t&&(e=t)}if("number"===typeof e)return i[e];var a,r=String(e);return(a=n[r.toLowerCase()])?a:(a=c[r.toLowerCase()])||(1===r.length?r.charCodeAt(0):void 0)}a.isEventKey=function(e,t){if(e&&"object"===typeof e){var a=e.which||e.keyCode||e.charCode;if(null===a||void 0===a)return!1;if("string"===typeof t){var r;if(r=n[t.toLowerCase()])return r===a;if(r=c[t.toLowerCase()])return r===a}else if("number"===typeof t)return t===a;return!1}};var n=(t=e.exports=a).code=t.codes={backspace:8,tab:9,enter:13,shift:16,ctrl:17,alt:18,"pause/break":19,"caps lock":20,esc:27,space:32,"page up":33,"page down":34,end:35,home:36,left:37,up:38,right:39,down:40,insert:45,delete:46,command:91,"left command":91,"right command":93,"numpad *":106,"numpad +":107,"numpad -":109,"numpad .":110,"numpad /":111,"num lock":144,"scroll lock":145,"my computer":182,"my calculator":183,";":186,"=":187,",":188,"-":189,".":190,"/":191,"`":192,"[":219,"\\":220,"]":221,"'":222},c=t.aliases={windows:91,"\u21e7":16,"\u2325":18,"\u2303":17,"\u2318":91,ctl:17,control:17,option:18,pause:19,break:19,caps:20,return:13,escape:27,spc:32,spacebar:32,pgup:33,pgdn:34,ins:45,del:46,cmd:91};for(r=97;r<123;r++)n[String.fromCharCode(r)]=r-32;for(var r=48;r<58;r++)n[r-48]=r;for(r=1;r<13;r++)n["f"+r]=r+111;for(r=0;r<10;r++)n["numpad "+r]=r+96;var i=t.names=t.title={};for(r in n)i[n[r]]=r;for(var l in c)n[l]=c[l]}}]);
//# sourceMappingURL=59.9cc741db.chunk.js.map