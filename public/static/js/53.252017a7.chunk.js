/*! For license information please see 53.252017a7.chunk.js.LICENSE.txt */
(this["webpackJsonpjumbo-hooks"]=this["webpackJsonpjumbo-hooks"]||[]).push([[53],{1360:function(e,t,a){"use strict";a.r(t);var n=a(0),c=a.n(n),r=a(480),i=a(3),o=a(487),l=a(9),s=a(552),m=a.n(s),d=a(1370),u=a(1374),f=a(1373),g=a(1375),p=a(1371),h=a(1393),v=a(1372),b=a(1392),E=a(450),y=a(255),k=a(404),N=a(1391),j=a(964),S=a(469),w=a(67),O=a(29),C=a(448),x=a(445),_=a(467),P=a(1390),I=a(1376),D=a(572),A=a.n(D),R=a(50),B=a(14),L=a(446),z=a(447),M=a(490),T=a.n(M),J=[{id:"idx",align:!1,disablePadding:!0,label:"ID"},{id:"image",align:!1,disablePadding:!0,label:"Image"},{id:"name",align:!0,disablePadding:!1,label:"Image Name"},{id:"status",align:!0,disablePadding:!1,label:"Status"},{id:"Actions",align:!0,disablePadding:!1,numeric:"right",label:"Actions"}],F=function(e){var t=e.order,a=e.orderBy;return c.a.createElement(p.a,null,c.a.createElement(v.a,null,J.map((function(n){return c.a.createElement(f.a,{key:n.id,align:n.numeric},c.a.createElement(N.a,{title:"Sort",placement:n.numeric?"bottom-end":"bottom-start",enterDelay:300},c.a.createElement(b.a,{active:a===n.id,direction:t,onClick:function(){return t=n.id,function(a){e.onRequestSort(a,t)};var t}},n.label)))}))))},U=function(e){var t=e.NewImage,a=e.category;return c.a.createElement(E.a,{className:"table-header"},c.a.createElement("div",{className:"title"},c.a.createElement(y.a,{variant:"h6"},a)),c.a.createElement("div",{className:"col-md-3 col-lg-3 col-sx-6 col-6 ml-auto"},c.a.createElement(w.a,{placeholder:"Search ..."})),c.a.createElement("div",{className:"actions"},c.a.createElement(N.a,{title:"Add Sticker",onClick:function(){return t()}},c.a.createElement(k.a,{className:"icon-btn"},c.a.createElement("i",{className:"zmdi zmdi-collection-image"})))))},W=function(e){var t=e.category_id,a=Object(n.useState)("asc"),r=Object(l.a)(a,2),i=r[0],o=r[1],s=Object(n.useState)("calories"),p=Object(l.a)(s,2),b=p[0],E=p[1],y=Object(n.useState)([]),N=Object(l.a)(y,2),w=N[0],D=N[1],M=Object(n.useState)([]),J=Object(l.a)(M,2),W=J[0],q=J[1],K=Object(n.useState)(!1),G=Object(l.a)(K,2),H=G[0],V=G[1],Y=Object(n.useState)(!1),Q=Object(l.a)(Y,2),X=Q[0],Z=Q[1],$=Object(n.useState)(null),ee=Object(l.a)($,2),te=ee[0],ae=ee[1],ne=Object(n.useState)(""),ce=Object(l.a)(ne,2),re=ce[0],ie=ce[1],oe=Object(n.useState)("https://via.placeholder.com/300x300"),le=Object(l.a)(oe,2),se=le[0],me=le[1],de=Object(n.useState)(0),ue=Object(l.a)(de,2),fe=ue[0],ge=ue[1],pe=Object(n.useState)(0),he=Object(l.a)(pe,2),ve=he[0],be=he[1],Ee=Object(n.useState)(0),ye=Object(l.a)(Ee,2),ke=ye[0],Ne=ye[1],je=Object(B.e)((function(e){return e.settings})).width,Se=Object(n.useState)(!1),we=Object(l.a)(Se,2),Oe=we[0],Ce=we[1];Object(n.useEffect)((function(){O.a.get(O.b+"category/getCategoryInfo",{params:{id:t}}).then((function(e){q(e.data.stickers),ge(e.data.category.name)})).catch((function(e){Ce(!0)})),Z(!0),Ne(0)}),[t]);var xe=function(){O.a.get(O.b+"category/getCategoryInfo",{params:{id:t}}).then((function(e){q(e.data.stickers),ge(e.data.category.name)})).catch((function(e){Ce(!0)})),Z(!0),Ne(0)},_e=Object(n.useState)(0),Pe=Object(l.a)(_e,2),Ie=Pe[0],De=Pe[1],Ae=Object(n.useState)(10),Re=Object(l.a)(Ae,2),Be=Re[0],Le=Re[1],ze=[{content:"Modify",id:1},{content:"Delete",id:2}],Me=function(e,t){var a=w.indexOf(t),n=[];-1===a?n=n.concat(w,t):0===a?n=n.concat(w.slice(1)):a===w.length-1?n=n.concat(w.slice(0,-1)):a>0&&(n=n.concat(w.slice(0,a),w.slice(a+1))),D(n)},Te=function(e,a){if(0===a);else if(1===a)Je(e);else if(2===a){var n=new FormData;n.append("id",e),O.a.post(O.b+"sticker/deleteSticker",n).then((function(e){"success"===e.data.status?(R.NotificationManager.info("You removed selected sticker.",1e3,(function(){})),navigator.onLine&&xe()):"error"===e.data.status&&R.NotificationManager.error(e.data.msg,"Error!",1e3,(function(){}))})).catch((function(e){R.NotificationManager.error(e,"Error!",1e3,(function(){}))})),navigator.onLine||caches.open("SirvezApp").then((function(a){a.match("/api/category/getCategoryInfo?id="+t).then((function(e){return e?e.json():null})).then((function(n){if(null!=n)for(var c=0;c<n.stickers.length;c++)if(n.stickers[c].id==e){n.stickers.splice(c,1);var r=new Response(JSON.stringify(n),{headers:{"content-type":"application/json"}});a.put("/api/category/getCategoryInfo?id="+t,r.clone()).then((function(e){xe()}));break}})),a.match("/api/room/editPhoto").then((function(e){return e?e.json():null})).then((function(e){if(null!=e)for(var n=0;n<e.categories.length;n++)if(e.category[n].id==t)for(var c=0;c<e.category[n].stickers.length;c++){e.category[n].stickers.splice(c,1);var r=new Response(JSON.stringify(e),{headers:{"content-type":"application/json"}});a.put("/api/room/editPhoto",r.clone());break}}))}))}},Je=function(e){e?O.a.get(O.b+"sticker/getStickerInfo",{params:{id:e}}).then((function(t){Ne(e),ie(t.data.stiker.name),be(t.data.stiker.status),me(t.data.stiker.stiker_img?O.d+fe+"/"+t.data.stiker.stiker_img:"https://via.placeholder.com/300x300")})).catch((function(e){Ce(!0)})):(me("https://via.placeholder.com/300x300"),Ne(0)),V(!0)},Fe=function(){V(!1)};return X?c.a.createElement("div",null,c.a.createElement(U,{NewImage:function(){return Je(0)},category:fe}),c.a.createElement("div",{className:"flex-auto"},c.a.createElement("div",{className:"table-responsive-material"},c.a.createElement(d.a,{className:""},c.a.createElement(F,{order:i,orderBy:b,onRequestSort:function(e,t){var a;"desc"===(a=b===t&&"asc"===i?"desc":"asc")?W.sort((function(e,a){return a[t]<e[t]?-1:1})):W.sort((function(e,a){return e[t]<a[t]?-1:1})),o(a),E(t)}}),c.a.createElement(u.a,null,W.slice(Ie*Be,Ie*Be+Be).map((function(e){return c.a.createElement(v.a,{hover:!0,onKeyDown:function(t){return function(e,t){"space"===m()(e)&&Me(e,t)}(t,e.id)},tabIndex:-1,key:e.id},c.a.createElement(f.a,null,e.id),c.a.createElement(f.a,null,c.a.createElement("div",{className:"user-profile d-flex flex-row align-items-center"},c.a.createElement(S.a,{alt:e.name,src:O.d+fe+"/"+e.stiker_img,className:"user-avatar"}))),c.a.createElement(f.a,null,e.name),c.a.createElement(f.a,null,e.status>0?"Active":"Deactive"),c.a.createElement(f.a,{className:"text-right"},c.a.createElement(j.a,{key:e.id,selectedData:e.id,onSelectRow:Te,menu_rows:ze})))}))),c.a.createElement(g.a,null,c.a.createElement(v.a,null,c.a.createElement(h.a,{count:W.length,rowsPerPage:Be,page:Ie,onChangePage:function(e,t){De(t)},onChangeRowsPerPage:function(e){Le(e.target.value)}})))))),c.a.createElement(P.a,{key:"add-img",className:"modal-box",toggle:Fe,isOpen:H},c.a.createElement(I.a,null,"Add Image",c.a.createElement(k.a,{className:"text-grey",onClick:Fe},c.a.createElement(A.a,null))),c.a.createElement("div",{className:"modal-box-content"},c.a.createElement("div",{className:"row"},c.a.createElement("div",{className:"col-xl-6 col-lg-6 col-md-6 col-12"},c.a.createElement("div",{className:"jr-card pb-2"},c.a.createElement("div",{className:"jr-card-thumb"},c.a.createElement("img",{className:"card-img-top img-fluid",alt:"products",src:se})),c.a.createElement("input",{type:"file",id:"add_img",accept:".svg",name:"add_img",style:{display:"none"},onChange:function(e){return function(e){e.preventDefault();var t=new FileReader,a=e.target.files[0];t.onloadend=function(){ae(a),me(t.result)},t.readAsDataURL(a)}(e)}}),c.a.createElement("div",{className:"jr-card-social text-right"},c.a.createElement(z.a,{className:"jr-fab-btn bg-secondary text-white jr-btn-fab-xs mb-3",onClick:function(e){document.getElementById("add_img").click()}},c.a.createElement("i",{className:"zmdi zmdi-cloud-upload zmdi-hc-1x"}))))),c.a.createElement("div",{className:"col-xl-6 col-lg-6 col-md-6 col-12"},c.a.createElement(_.a,{autoFocus:!0,margin:"dense",id:"category",value:fe,label:"Category Name",helperText:"Please Enter Category",fullWidth:!0,disabled:!0}),c.a.createElement(_.a,{autoFocus:!0,margin:"dense",id:"name",value:re,onChange:function(e){return ie(e.target.value)},label:"Name",helperText:"Please Enter Name",fullWidth:!0}),c.a.createElement(_.a,{id:"status",select:!0,label:"Status",value:ve,onChange:function(e){return be(e.target.value)},SelectProps:{},helperText:"Please select Status",fullWidth:!0},[{id:1,content:"Active"},{id:0,content:"Disable"}].map((function(e){return c.a.createElement(C.a,{key:e.id,value:e.id},e.content)})))))),c.a.createElement("div",{className:"modal-box-footer d-flex flex-row"},c.a.createElement(x.a,{onClick:Fe,color:"secondary"},"Cancel"),c.a.createElement(x.a,{onClick:function(){Fe();var e=ke>0?ke:JSON.parse(localStorage.getItem("user")).first_name+"_"+(new Date).getTime(),a=new FormData;a.append("id",e),a.append("stiker_img",te),a.append("name",re),a.append("category_id",t),a.append("category",fe),a.append("status",ve),O.a.post(O.b+"sticker/updateSticker",a).then((function(e){"success"===e.data.status?(R.NotificationManager.info("Sticker updated!","Success!",1e3,(function(){})),navigator.onLine&&xe()):"error"===e.data.status&&R.NotificationManager.error(e.data.msg,"Error!",1e3,(function(){}))})).catch((function(e){R.NotificationManager.error(e,"Error!",1e3,(function(){}))})),navigator.onLine||caches.open("SirvezApp").then((function(a){var n="";te&&(n=(new Date).getTime()+"_new.jpeg",a.put("/pixie/assets/images/stickers/"+fe+"/"+n,new Response(te)));var c={id:e,name:re,category_id:t,category:fe,stiker_img:n},r={stiker:c},i=new Response(JSON.stringify(r),{headers:{"content-type":"application/json"}});a.put("/api/category/sticker/getStickerInfo?id="+e,i.clone());var o="/api/category/getCategoryInfo?id="+t;a.match(o).then((function(e){return e.json()})).then((function(t){var r=t.stickers.find((function(t){return t.id===e}));r?(r.name=re,r.category=fe,""!=n&&(r.stiker_img=n)):t.stickers.unshift(c);var i=new Response(JSON.stringify(t),{headers:{"content-type":"application/json"}});a.put(o,i.clone())})),a.match("/api/room/editPhoto").then((function(e){return e?e.json():null})).then((function(r){if(null!=r){var i=r.categories.find((function(e){return e.id===t}));if(i){var o=i.stickers.find((function(t){return t.id===e}));o?(o.name=re,o.category=fe,""!=n&&(o.stiker_img=n)):i.stickers.unshift(c);var l=new Response(JSON.stringify(r),{headers:{"content-type":"application/json"}});a.put("/api/room/editPhoto",l.clone()).then((function(e){xe()}))}}}))})).catch((function(e){console.log("cashe_error",e)}))},color:"primary"},"Save"))),c.a.createElement(R.NotificationContainer,null)):c.a.createElement("div",{className:"loader-view",style:{height:je>=1200?"calc(100vh - 259px)":"calc(100vh - 238px)"}},c.a.createElement(L.a,null),c.a.createElement(T.a,{show:Oe,warning:!0,confirmBtnText:"Go Back",confirmBtnBsStyle:"danger",cancelBtnBsStyle:"default",title:"Warning!",onConfirm:function(){return History.go(-1)}},"Data not cached, so you can not view this page."))};t.default=function(e){var t=e.location.state?e.location.state.id:null;return c.a.createElement("div",{className:"app-wrapper"},c.a.createElement(r.a,{match:e.match,title:c.a.createElement(i.a,{id:"sidebar.stickers.categories"})}),c.a.createElement("div",{className:"row animated slideInUpTiny animation-duration-3"},c.a.createElement(o.a,{styleName:"col-12"},c.a.createElement(W,{category_id:t}))))}},480:function(e,t,a){"use strict";var n=a(0),c=a.n(n),r=a(529),i=a(530),o=function(e,t,a){return 0===a?"/":"/"+e.split(t)[0]+t};t.a=function(e){var t=e.title,a=e.match,n=e.project_name,l=e.room_number,s=[],m=a.url.substr(1),d=m.split("/");return d.map((function(e,t){""===e&&d.splice(t,1),"live"===d[t-1]?s[t]=n||"":"live"===d[t-2]?s[t]=l||"":s[t]=e})),c.a.createElement("div",{className:"page-heading d-sm-flex justify-content-sm-between align-items-sm-center"},c.a.createElement("h2",{className:"title mb-3 mb-sm-0"},t),c.a.createElement(r.a,{className:"mb-0",tag:"nav"},d.map((function(e,t){if(0!=t)return c.a.createElement(i.a,{active:d.length===t+1,tag:d.length===t+1?"span":"a",key:t,href:o(m,e,t)},function(e){var t=e.split("-");return t.length>1?t[0].charAt(0).toUpperCase()+t[0].slice(1)+" "+t[1].charAt(0).toUpperCase()+t[1].slice(1):e.charAt(0).toUpperCase()+e.slice(1)}(s[t]))}))))}},487:function(e,t,a){"use strict";var n=a(0),c=a.n(n),r=function(e){var t=e.heading,a=e.children,n=e.styleName,r=e.cardStyle,i=e.childrenStyle,o=e.headerOutside,l=e.headingStyle;return c.a.createElement("div",{className:"".concat(n)},o&&c.a.createElement("div",{className:"jr-entry-header"},c.a.createElement("h3",{className:"entry-heading"},t),a.length>1&&c.a.createElement("div",{className:"entry-description"},a[0])),c.a.createElement("div",{className:"jr-card ".concat(r)},!o&&t&&c.a.createElement("div",{className:"jr-card-header ".concat(l)},c.a.createElement("h3",{className:"card-heading"},t),a.length>1&&c.a.createElement("div",{className:"sub-heading"},a[0])),c.a.createElement("div",{className:"jr-card-body ".concat(i)},a.length>1?a[1]:a)))};t.a=r,r.defaultProps={cardStyle:"",headingStyle:"",childrenStyle:"",styleName:"col-lg-6 col-sm-12"}},552:function(e,t){function a(e){if(e&&"object"===typeof e){var t=e.which||e.keyCode||e.charCode;t&&(e=t)}if("number"===typeof e)return i[e];var a,r=String(e);return(a=n[r.toLowerCase()])?a:(a=c[r.toLowerCase()])||(1===r.length?r.charCodeAt(0):void 0)}a.isEventKey=function(e,t){if(e&&"object"===typeof e){var a=e.which||e.keyCode||e.charCode;if(null===a||void 0===a)return!1;if("string"===typeof t){var r;if(r=n[t.toLowerCase()])return r===a;if(r=c[t.toLowerCase()])return r===a}else if("number"===typeof t)return t===a;return!1}};var n=(t=e.exports=a).code=t.codes={backspace:8,tab:9,enter:13,shift:16,ctrl:17,alt:18,"pause/break":19,"caps lock":20,esc:27,space:32,"page up":33,"page down":34,end:35,home:36,left:37,up:38,right:39,down:40,insert:45,delete:46,command:91,"left command":91,"right command":93,"numpad *":106,"numpad +":107,"numpad -":109,"numpad .":110,"numpad /":111,"num lock":144,"scroll lock":145,"my computer":182,"my calculator":183,";":186,"=":187,",":188,"-":189,".":190,"/":191,"`":192,"[":219,"\\":220,"]":221,"'":222},c=t.aliases={windows:91,"\u21e7":16,"\u2325":18,"\u2303":17,"\u2318":91,ctl:17,control:17,option:18,pause:19,break:19,caps:20,return:13,escape:27,spc:32,spacebar:32,pgup:33,pgdn:34,ins:45,del:46,cmd:91};for(r=97;r<123;r++)n[String.fromCharCode(r)]=r-32;for(var r=48;r<58;r++)n[r-48]=r;for(r=1;r<13;r++)n["f"+r]=r+111;for(r=0;r<10;r++)n["numpad "+r]=r+96;var i=t.names=t.title={};for(r in n)i[n[r]]=r;for(var o in c)n[o]=c[o]},964:function(e,t,a){"use strict";var n=a(9),c=a(0),r=a.n(c),i=a(252),o=a(448),l=a(404);t.a=function(e){var t=Object(c.useState)(!1),a=Object(n.a)(t,2),s=a[0],m=a[1],d=Object(c.useState)(void 0),u=Object(n.a)(d,2),f=u[0],g=u[1],p=e.selectedData,h=e.onSelectRow,v=e.menu_rows,b=v||[{content:"View",id:0},{content:"Modify",id:1},{content:"Delete",id:2}],E=function(){m(!1)};return r.a.createElement("div",null,r.a.createElement(l.a,{onClick:function(e){m(!0),g(e.currentTarget)}},r.a.createElement("i",{className:"zmdi zmdi-more-vert"})),r.a.createElement(i.a,{anchorEl:f,open:s,onClose:E,key:p.id,MenuListProps:{style:{width:150,paddingTop:0,paddingBottom:0}}},b.map((function(e){return r.a.createElement(o.a,{key:p+"-"+e.content,onClick:function(){E(),h(p,e.id)}},e.content)}))))}}}]);
//# sourceMappingURL=53.252017a7.chunk.js.map