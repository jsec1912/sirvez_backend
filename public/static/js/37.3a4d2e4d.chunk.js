/*! For license information please see 37.3a4d2e4d.chunk.js.LICENSE.txt */
(this["webpackJsonpjumbo-hooks"]=this["webpackJsonpjumbo-hooks"]||[]).push([[37],{1341:function(e,t,a){"use strict";var n=a(41);Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var r=n(a(0)),i=(0,n(a(173)).default)(r.default.createElement("path",{d:"M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"}),"Check");t.default=i},1355:function(e,t,a){"use strict";a.r(t);var n=a(63),r=a(64),i=a(66),c=a(65),o=a(0),s=a.n(o),l=a(480),d=a(3),u=a(123),m=a(11),p=a(8),f=a(54),g=a.n(f),h=a(470),v=a(449),E=a(450),b=a(444),y=a(255),S=a(894),x=a(404),_=a(556),N=a.n(_),j=a(852),w=a.n(j),k=a(889),z=a.n(k),C=a(1341),O=a.n(C),P=a(880),F=a.n(P),D=a(879),L=a.n(D),M=a(9),B=(a(552),a(1370)),A=a(1374),I=a(1373),R=a(1375),T=a(1371),H=a(1393),U=a(1372),W=a(1392),q=a(1391),J=a(670),G=(a(67),a(14)),Q=a(27),V=a(446),Y=a(29),K=a(469),X=a(50),Z=a(490),$=a.n(Z),ee=a(659),te=[{id:"customer",align:!1,disablePadding:!0,label:"Customer"},{id:"project_title",align:!0,disablePadding:!1,label:"Project Title"},{id:"account_manager",align:!0,disablePadding:!1,label:"Account Manager"},{id:"room_count",align:!0,disablePadding:!1,label:"Rooms"},{id:"site_count",align:!0,disablePadding:!1,label:"Sites"},{id:"survey_start_date",align:!0,disablePadding:!1,label:"Survey Date"},{id:"signed_off",align:!0,disablePadding:!1,label:"Signed Off"},{id:"created_by",align:!0,disablePadding:!1,label:"User"},{id:"actions",align:!0,disablePadding:!1,numeric:"right",label:"Actions"}],ae=function(e){var t=e.order,a=e.orderBy;return s.a.createElement(T.a,null,s.a.createElement(U.a,null,te.map((function(n){return s.a.createElement(I.a,{key:n.id,align:n.numeric},s.a.createElement(q.a,{title:"Sort",placement:n.numeric?"bottom-end":"bottom-start",enterDelay:300},s.a.createElement(W.a,{active:a===n.id,direction:t,onClick:(r=n.id,function(t){e.onRequestSort(t,r)})},n.label)));var r}))))},ne=function(e){e.filterKind;var t=e.SiteSurveys,a=e.loader,n=e.goback,r=e.init_Data,i=JSON.parse(localStorage.getItem("user")),c=Object(o.useState)("asc"),l=Object(M.a)(c,2),u=l[0],m=l[1],p=Object(o.useState)("id"),f=Object(M.a)(p,2),g=f[0],h=f[1],v=Object(G.e)((function(e){return e.settings})).width,E=Object(Q.g)(),b=Object(o.useState)(0),y=Object(M.a)(b,2),S=y[0],_=y[1],N=Object(o.useState)(10),j=Object(M.a)(N,2),w=j[0],k=j[1],z=function(e){E.push({pathname:"/app/project/live/".concat(e.id)})},C=function(e,t){if(0===t)E.push({pathname:"/app/project/live/".concat(e)});else if(1===t)E.push({pathname:"/app/project/add-new",state:{id:e}});else if(3===t)!function(e){Y.a.get(Y.b+"project/projectInfo",{params:{project_id:e}}).then((function(e){var t=new ee.a("p","pt","letter"),a=i.is_upload>=0?i.is_upload:1,n=i.front_cover,r=i.back_cover,c=e.data.project,o=e.data.rooms,s=e.data.products,l=e.data.sites,d=[{id:0,content:"New Product",icon:"zmdi zmdi-assignment-check zmdi-hc-fw"},{id:1,content:"Dispose",icon:"zmdi zmdi-close-circle-o zmdi-hc-fw"},{id:2,content:"Move To Room",icon:"zmdi zmdi-arrows zmdi-hc-fw"}],u=(t=new ee.a("p","pt","letter")).internal.pageSize.getWidth(),m=t.internal.pageSize.getHeight(),p=new Image,f="",g=0,h=80,v=80;a&&n&&(p.src=Y.c+"upload/img/"+n,t.addImage(p,"png",0,0,u,m),v=250,t.setFontSize(30),t.text("Scope of works:",250,v),p.src=Y.c+"upload/img/"+c.logo_img,f=c.logo_img.split(".").pop(),t.addImage(p,f,250,v+30,200,130),v+=200,t.setFontSize(12),t.text("Client: ",250,v),t.text(c.company_name+"",350,v),t.text("Project Title: ",250,v+30),t.text(c.project_name+"",350,v+30),t.text("Sirvey Date: ",250,v+60),t.text(c.survey_start_date+"",350,v+60),t.addPage()),o.map((function(e){var a=l.find((function(t){return t.id==e.site_id}));v=100,t.setFontSize(30),t.text("Room  "+e.room_number+"  Summary",70,v),v+=50,t.setFontSize(12),t.text("Customer User: ",50,v),t.setFontSize(11),t.text(c.customer_user+"",60+u/4,v),t.setFontSize(12),t.text("Address1: ",u/2,v),t.setFontSize(11),t.text(a.address+"",2*u/3,v),v+=20,t.setFontSize(12),t.text("Survey Date: ",50,v),t.setFontSize(11),t.text(c.survey_start_date+"",60+u/4,v),t.setFontSize(12),t.text("Address2: ",u/2,v),t.setFontSize(11),t.text(a.address1+"",2*u/3,v),v+=20,t.setFontSize(12),t.text("Number of rooms: ",50,v),t.setFontSize(11),t.text(c.room_count+"",60+u/4,v),t.setFontSize(12),t.text("City: ",u/2,v),t.setFontSize(11),t.text(a.city+"",2*u/3,v),v+=20,t.setFontSize(12),t.text("Out of hours Contat Number: ",50,v),t.setFontSize(11),t.text(c.contact_number+"",60+u/4,v),t.setFontSize(12),t.text("Postcode: ",u/2,v),t.setFontSize(11),t.text(a.postcode+"",2*u/3,v),v+=30,t.setFontSize(13),t.text("-   Products to be installed",50,v),v+=30,t.setFontSize(11),t.text("No",70,v),t.text("Name",u/5,v),t.text("Action",2*u/5,v),t.text("Qty",3*u/5,v),t.text("Description",7*u/10,v);var n=s.filter((function(t){return t.id==e.id}));n.filter((function(e){return 0==e.action})).map((function(e,a){(v+=20)>m-100&&(t.addPage(),v=80),t.text(a+1+"",70,v),t.text(e.product_name+"",u/5,v),t.text(d[e.action].content,2*u/5,v),t.text(e.qty+"",3*u/5,v),t.text(e.description+"",4*u/5,v)})),v+=30,t.setFontSize(13),t.text("-   Products to be Dispose of",50,v),v+=30,t.setFontSize(11),t.text("No",70,v),t.text("Name",u/5,v),t.text("Action",2*u/5,v),t.text("Qty",3*u/5,v),t.text("Description",7*u/10,v),n.filter((function(e){return 1==e.action})).map((function(e,a){(v+=20)>m-100&&(t.addPage(),v=80,g=a),t.text(a+1+"",70,v),t.text(e.product_name+"",u/5,v),t.text(d[e.action].content,2*u/5,v),t.text(e.qty+"",3*u/5,v),t.text(e.description+"",4*u/5,v)})),v+=30,t.setFontSize(13),t.text("-   Products to be Moved",50,v),v+=30,t.setFontSize(11),t.text("No",70,v),t.text("Name",u/5,v),t.text("Action",2*u/5,v),t.text("Qty",3*u/5,v),t.text("Description",7*u/10,v),n.filter((function(e){return 2==e.action})).map((function(e,a){(v+=20)>m-100&&(t.addPage(),v=80,g=a),t.text(a+1+"",70,v),t.text(e.product_name+"",u/5,v),t.text(d[e.action].content,2*u/5,v),t.text(e.qty+"",3*u/5,v),t.text(e.description+"",4*u/5,v)})),t.addPage(),t.setFontSize(15),t.text(e.room_number+"(Room Name)",70,50),v=80,h=80,g=0,e.img_files.map((function(e,a){h+200*(a-g)>m-200&&(t.addPage(),h=80,g=a),p.src=Y.c+"upload/img/"+e.img_name,f=e.img_name.split(".").pop(),t.addImage(p,f,100,h+200*(a-g),u-200,200),v=(h+=20)+200*(a-g+1)+30})),v>m-100&&(t.addPage(),v=80),t.setFontSize(12),t.text("-  Note: ",50,v),t.setFontSize(11);var r=e.notes.split(/\r\n|\r|\n/);v+=30,r.map((function(e,a){"null"==e&&(e=""),t.text(e+"",70,v+20*a)})),v+=20*r.length,t.addPage()})),a&&r&&(p.src=Y.c+"upload/img/"+r,t.addImage(p,"png",0,0,u,m)),t.save("Scope of Works(project).pdf")}))}(e);else if(2===t){var a=new FormData;a.append("id",e),Y.a.post(Y.b+"project/deleteProject",a).then((function(e){"success"===e.data.status?(r(),X.NotificationManager.info("You removed selected sticker")):"error"===e.data.status&&X.NotificationManager.error(e.data.msg,"Error!",1e3,(function(){}))})).catch((function(t){X.NotificationManager.error(t,"Error!",1e3,(function(){})),navigator.onLine||caches.open("SirvezApp").then((function(t){t.match("/api/project/projectList?archived=0").then((function(e){return e?e.json():null})).then((function(a){if(null!=a){a.projects=a.projects.filter((function(t){return t.id!=e}));var n=new Response(JSON.stringify(a),{headers:{"content-type":"application/json"}});t.put("/api/project/projectList?archived=0",n.clone()).then((function(){r()}))}}))}))}))}};return s.a.createElement("div",null,a?s.a.createElement("div",{className:"flex-auto"},s.a.createElement("div",{className:"table-responsive-material"},s.a.createElement(B.a,{className:""},s.a.createElement(ae,{order:u,orderBy:g,onRequestSort:function(e,a){var n;"desc"===(n=g===a&&"asc"===u?"desc":"asc")?t.sort((function(e,t){return t[g]<e[g]?-1:1})):t.sort((function(e,t){return e[g]<t[g]?-1:1})),m(n),h(a)}}),s.a.createElement(A.a,null,t.length>0?t.slice(S*w,S*w+w).map((function(e){return s.a.createElement(U.a,{hover:!0,key:e.id,tabIndex:-1},s.a.createElement(I.a,{onClick:function(){return z(e)}},s.a.createElement(x.a,{className:"icon-btn",onClick:function(){!function(e){var t;t="1"===e.favourite?0:1;var a=new FormData;a.append("id",e.id),a.append("favourite",t),Y.a.post(Y.b+"project/setFavourite",a).then((function(e){"success"===e.data.status?(X.NotificationManager.info("You set favourite!"),r()):"error"===e.data.status&&X.NotificationManager.error(e.data.msg,"Error!",1e3,(function(){}))})).catch((function(e){X.NotificationManager.error(e,"Error!",1e3,(function(){}))}))}(e)}},"1"===e.favourite?s.a.createElement("i",{className:"zmdi zmdi-star text-warning"}):s.a.createElement("i",{className:"zmdi zmdi-star-outline text-warning"})),e.customer),s.a.createElement(I.a,{onClick:function(){return z(e)}},e.project_name),s.a.createElement(I.a,{onClick:function(){return z(e)}},e.account_manager),s.a.createElement(I.a,{onClick:function(){return z(e)}},e.room_count),s.a.createElement(I.a,{onClick:function(){return z(e)}},e.site_count),s.a.createElement(I.a,{onClick:function(){return z(e)}},e.survey_start_date),s.a.createElement(I.a,{onClick:function(){return z(e)}},e.signed_off?"Yes":"No"),s.a.createElement(I.a,{onClick:function(){return z(e)}},s.a.createElement("div",{className:"user-profile d-flex flex-row align-items-center"},s.a.createElement(K.a,{alt:e.account_manager,src:Y.c+"upload/img/"+e.profile_pic,className:"user-avatar"}))),s.a.createElement(I.a,{className:"text-right"},s.a.createElement(J.a,{key:e.id,selectedData:e.id,onSelectRow:C})))})):s.a.createElement("tr",null,s.a.createElement("td",{className:"text-danger",colSpan:"10"},s.a.createElement(d.a,{id:"table.noData"})))),s.a.createElement(R.a,null,s.a.createElement(U.a,null,s.a.createElement(H.a,{count:t.length,rowsPerPage:w,page:S,onChangePage:function(e,t){_(t)},onChangeRowsPerPage:function(e){k(e.target.value)}}))))),s.a.createElement(X.NotificationContainer,null)):s.a.createElement("div",{className:"loader-view",style:{height:v>=1200?"calc(100vh - 259px)":"calc(100vh - 238px)"}},s.a.createElement(V.a,null)),s.a.createElement($.a,{show:n,warning:!0,confirmBtnText:"Go Back",confirmBtnBsStyle:"danger",cancelBtnBsStyle:"default",title:"Warning!",onConfirm:function(){return E.go(-1)}},"Data not cached, so you can not view this page."))},re=a(403),ie=a(968),ce=a(969),oe=a(467),se=a(448),le=function(e){Object(i.a)(a,e);var t=Object(c.a)(a);function a(){var e;Object(n.a)(this,a);for(var r=arguments.length,i=new Array(r),c=0;c<r;c++)i[c]=arguments[c];return(e=t.call.apply(t,[this].concat(i))).state={open:!1,filter_kind:0,SiteSurveys:[],loader:!1,goback:!1,alldata:[],users:[],user_id:0,customers:[],customer_id:0},e.handleDrawerOpen=function(){e.setState({open:!0})},e.handleDrawerClose=function(){e.setState({open:!1})},e.init_Data=function(){Y.a.get(Y.b+"project/projectList",{params:{archived:0}}).then((function(t){e.setState({SiteSurveys:t.data.projects,alldata:t.data.projects,loader:!0,users:t.data.users,customers:t.data.customers})})).catch((function(t){e.setState({goback:!0})}))},e.setCustomer=function(e){},e.setUser=function(t){e.setState({user_id:t})},e.filter=function(t,a,n){e.setState({filter_kind:t,customer_id:a,user_id:n,SiteSurveys:e.state.alldata.filter((function(e){return(0==t||1==t&&"1"==e.favourite||2==t&&"2"==e.signed_off)&&(0==a||a>0&&e.company_id==a)&&(0==n||n>0&&e.created_by==n)}))})},e.componentDidMount=function(){e.init_Data()},e}return Object(r.a)(a,[{key:"render",value:function(){var e=this,t=this.props,a=t.classes,n=t.theme,r=JSON.parse(localStorage.getItem("user"));return s.a.createElement("div",{className:a.root},s.a.createElement("div",{className:a.appFrame},s.a.createElement(v.a,{className:g()("bg-primary",a.appBar,this.state.open&&a.appBarShift)},s.a.createElement(E.a,{disableGutters:!this.state.open},s.a.createElement(x.a,{"aria-label":"open drawer",onClick:this.handleDrawerOpen,className:g()(a.menuButton,this.state.open&&a.hide)},s.a.createElement(N.a,{className:"text-white"})),s.a.createElement(y.a,{variant:"h6",color:"inherit",className:"text-white",noWrap:!0},"Projects"))),s.a.createElement(h.a,{variant:"persistent",classes:{paper:a.drawerPaper},open:this.state.open},s.a.createElement("div",{className:a.drawerInner},s.a.createElement("div",{className:a.drawerHeader},s.a.createElement(x.a,{onClick:this.handleDrawerClose},"rtl"===n.direction?s.a.createElement(L.a,null):s.a.createElement(F.a,null))),s.a.createElement(S.a,null),r.user_type<6?s.a.createElement(b.a,{className:a.list},s.a.createElement(re.a,null,s.a.createElement(oe.a,{id:"customer",select:!0,label:"Customer",value:this.state.customer_id,onChange:function(t){return e.filter(e.state.filter_kind,t.target.value,e.state.user_id)},margin:"normal",fullWidth:!0},s.a.createElement(se.a,{value:0},s.a.createElement(d.a,{id:"Task.all"})),this.state.customers.map((function(e){return s.a.createElement(se.a,{key:e.id,value:e.id},e.name)}))))):null,r.user_type<6?s.a.createElement(b.a,{className:a.list},s.a.createElement(re.a,null,s.a.createElement(oe.a,{id:"user",select:!0,label:"User",value:this.state.user_id,onChange:function(t){return e.filter(e.state.filter_kind,e.state.customer_id,t.target.value)},margin:"normal",fullWidth:!0},s.a.createElement(se.a,{value:0},s.a.createElement(d.a,{id:"Task.all"})),this.state.users.map((function(e){return s.a.createElement(se.a,{key:e.id,value:e.id},e.first_name)}))))):null,s.a.createElement(S.a,null),s.a.createElement(b.a,{className:a.list},s.a.createElement(re.a,{button:!0,className:"py-0",onClick:function(){return e.filter(0,e.state.customer_id,e.state.user_id)}},s.a.createElement(ie.a,null,s.a.createElement(x.a,{className:"".concat(0==this.state.filter_kind?"text-primary":"")},s.a.createElement(w.a,null))),s.a.createElement(ce.a,{primary:"All",className:"".concat(0==this.state.filter_kind?"text-primary":"")})),s.a.createElement(re.a,{button:!0,className:"py-0",onClick:function(){return e.filter(1,e.state.customer_id,e.state.user_id)}},s.a.createElement(ie.a,null,s.a.createElement(x.a,{className:"".concat(1==this.state.filter_kind?"text-primary":"")},s.a.createElement(z.a,null))),s.a.createElement(ce.a,{primary:"Star",className:"".concat(1==this.state.filter_kind?"text-primary":"")})),s.a.createElement(re.a,{button:!0,className:"py-0",onClick:function(){return e.filter(2,e.state.customer_id,e.state.user_id)}},s.a.createElement(ie.a,null,s.a.createElement(x.a,{className:"".concat(2==this.state.filter_kind?"text-primary":"")},s.a.createElement(O.a,null))),s.a.createElement(ce.a,{primary:"Signed Off",className:"".concat(2==this.state.filter_kind?"text-primary":"")}))))),s.a.createElement("main",{className:g()(a.content,this.state.open&&a.contentShift)},s.a.createElement(ne,{loader:this.state.loader,goback:this.state.goback,SiteSurveys:this.state.SiteSurveys,init_Data:this.init_Data}))))}}]),a}(s.a.Component),de=Object(p.a)((function(e){return{root:{width:"100%",height:"100%",zIndex:1,overflow:"hidden"},appFrame:{position:"relative",display:"flex",width:"100%",height:"100%"},appBar:{position:"absolute",transition:e.transitions.create(["margin","width"],{easing:e.transitions.easing.sharp,duration:e.transitions.duration.leavingScreen})},appBarShift:{marginLeft:240,width:"calc(100% - ".concat(240,"px)"),transition:e.transitions.create(["margin","width"],{easing:e.transitions.easing.easeOut,duration:e.transitions.duration.enteringScreen})},menuButton:{marginLeft:12,marginRight:20},hide:{display:"none"},drawerPaper:{position:"relative",height:"100%",width:240},drawerHeader:Object(m.a)({display:"flex",alignItems:"center",justifyContent:"flex-end",padding:"0 8px"},e.mixins.toolbar),content:Object(u.a)({width:"100%",marginLeft:-240,flexGrow:1,padding:e.spacing(3),transition:e.transitions.create("margin",{easing:e.transitions.easing.sharp,duration:e.transitions.duration.leavingScreen}),marginTop:56},e.breakpoints.up("sm"),{content:{height:"calc(100% - 64px)",marginTop:64}}),contentShift:{marginLeft:0,transition:e.transitions.create("margin",{easing:e.transitions.easing.easeOut,duration:e.transitions.duration.enteringScreen})}}}),{withTheme:!0})(le),ue=a(487),me=function(e){Object(i.a)(a,e);var t=Object(c.a)(a);function a(){return Object(n.a)(this,a),t.apply(this,arguments)}return Object(r.a)(a,[{key:"render",value:function(){return s.a.createElement("div",{className:"app-wrapper"},s.a.createElement(l.a,{match:this.props.match,title:s.a.createElement(d.a,{id:"sidebar.project"})}),s.a.createElement("div",{className:"row"},s.a.createElement(ue.a,{styleName:"col-12",cardStyle:"p-0",headerOutside:!0},s.a.createElement(de,null))))}}]),a}(s.a.Component);t.default=me},480:function(e,t,a){"use strict";var n=a(0),r=a.n(n),i=a(529),c=a(530),o=function(e,t,a){return 0===a?"/":"/"+e.split(t)[0]+t};t.a=function(e){var t=e.title,a=e.match,n=e.project_name,s=e.room_number,l=[],d=a.url.substr(1),u=d.split("/");return u.map((function(e,t){""===e&&u.splice(t,1),"live"===u[t-1]?l[t]=n||"":"live"===u[t-2]?l[t]=s||"":l[t]=e})),r.a.createElement("div",{className:"page-heading d-sm-flex justify-content-sm-between align-items-sm-center"},r.a.createElement("h2",{className:"title mb-3 mb-sm-0"},t),r.a.createElement(i.a,{className:"mb-0",tag:"nav"},u.map((function(e,t){if(0!=t)return r.a.createElement(c.a,{active:u.length===t+1,tag:u.length===t+1?"span":"a",key:t,href:o(d,e,t)},function(e){var t=e.split("-");return t.length>1?t[0].charAt(0).toUpperCase()+t[0].slice(1)+" "+t[1].charAt(0).toUpperCase()+t[1].slice(1):e.charAt(0).toUpperCase()+e.slice(1)}(l[t]))}))))}},487:function(e,t,a){"use strict";var n=a(0),r=a.n(n),i=function(e){var t=e.heading,a=e.children,n=e.styleName,i=e.cardStyle,c=e.childrenStyle,o=e.headerOutside,s=e.headingStyle;return r.a.createElement("div",{className:"".concat(n)},o&&r.a.createElement("div",{className:"jr-entry-header"},r.a.createElement("h3",{className:"entry-heading"},t),a.length>1&&r.a.createElement("div",{className:"entry-description"},a[0])),r.a.createElement("div",{className:"jr-card ".concat(i)},!o&&t&&r.a.createElement("div",{className:"jr-card-header ".concat(s)},r.a.createElement("h3",{className:"card-heading"},t),a.length>1&&r.a.createElement("div",{className:"sub-heading"},a[0])),r.a.createElement("div",{className:"jr-card-body ".concat(c)},a.length>1?a[1]:a)))};t.a=i,i.defaultProps={cardStyle:"",headingStyle:"",childrenStyle:"",styleName:"col-lg-6 col-sm-12"}},552:function(e,t){function a(e){if(e&&"object"===typeof e){var t=e.which||e.keyCode||e.charCode;t&&(e=t)}if("number"===typeof e)return c[e];var a,i=String(e);return(a=n[i.toLowerCase()])?a:(a=r[i.toLowerCase()])||(1===i.length?i.charCodeAt(0):void 0)}a.isEventKey=function(e,t){if(e&&"object"===typeof e){var a=e.which||e.keyCode||e.charCode;if(null===a||void 0===a)return!1;if("string"===typeof t){var i;if(i=n[t.toLowerCase()])return i===a;if(i=r[t.toLowerCase()])return i===a}else if("number"===typeof t)return t===a;return!1}};var n=(t=e.exports=a).code=t.codes={backspace:8,tab:9,enter:13,shift:16,ctrl:17,alt:18,"pause/break":19,"caps lock":20,esc:27,space:32,"page up":33,"page down":34,end:35,home:36,left:37,up:38,right:39,down:40,insert:45,delete:46,command:91,"left command":91,"right command":93,"numpad *":106,"numpad +":107,"numpad -":109,"numpad .":110,"numpad /":111,"num lock":144,"scroll lock":145,"my computer":182,"my calculator":183,";":186,"=":187,",":188,"-":189,".":190,"/":191,"`":192,"[":219,"\\":220,"]":221,"'":222},r=t.aliases={windows:91,"\u21e7":16,"\u2325":18,"\u2303":17,"\u2318":91,ctl:17,control:17,option:18,pause:19,break:19,caps:20,return:13,escape:27,spc:32,spacebar:32,pgup:33,pgdn:34,ins:45,del:46,cmd:91};for(i=97;i<123;i++)n[String.fromCharCode(i)]=i-32;for(var i=48;i<58;i++)n[i-48]=i;for(i=1;i<13;i++)n["f"+i]=i+111;for(i=0;i<10;i++)n["numpad "+i]=i+96;var c=t.names=t.title={};for(i in n)c[n[i]]=i;for(var o in r)n[o]=r[o]},670:function(e,t,a){"use strict";var n=a(9),r=a(0),i=a.n(r),c=a(252),o=a(448),s=a(404);t.a=function(e){var t=Object(r.useState)(!1),a=Object(n.a)(t,2),l=a[0],d=a[1],u=Object(r.useState)(void 0),m=Object(n.a)(u,2),p=m[0],f=m[1],g=e.selectedData,h=e.onSelectRow,v=e.menu_rows,E=v||[{content:"View",id:0},{content:"Modify",id:1},{content:"Print",id:3},{content:"Delete",id:2}],b=function(){d(!1)};return i.a.createElement("div",{className:"ml-auto"},i.a.createElement(s.a,{onClick:function(e){d(!0),f(e.currentTarget)}},i.a.createElement("i",{className:"zmdi zmdi-more-vert"})),i.a.createElement(c.a,{anchorEl:p,open:l,onClose:b,MenuListProps:{style:{width:150,paddingTop:0,paddingBottom:0}}},E.map((function(e){return i.a.createElement(o.a,{key:g+"-"+e.content,onClick:function(){b(),h(g,e.id)}},e.content)}))))}},852:function(e,t,a){"use strict";var n=a(41);Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var r=n(a(0)),i=(0,n(a(173)).default)(r.default.createElement("path",{d:"M19 3H4.99c-1.11 0-1.98.89-1.98 2L3 19c0 1.1.88 2 1.99 2H19c1.1 0 2-.9 2-2V5c0-1.11-.9-2-2-2zm0 12h-4c0 1.66-1.35 3-3 3s-3-1.34-3-3H4.99V5H19v10z"}),"Inbox");t.default=i},879:function(e,t,a){"use strict";var n=a(41);Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var r=n(a(0)),i=(0,n(a(173)).default)(r.default.createElement("path",{d:"M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"}),"ChevronRight");t.default=i},880:function(e,t,a){"use strict";var n=a(41);Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var r=n(a(0)),i=(0,n(a(173)).default)(r.default.createElement("path",{d:"M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"}),"ChevronLeft");t.default=i},889:function(e,t,a){"use strict";var n=a(41);Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var r=n(a(0)),i=(0,n(a(173)).default)(r.default.createElement("path",{d:"M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"}),"Star");t.default=i}}]);
//# sourceMappingURL=37.3a4d2e4d.chunk.js.map