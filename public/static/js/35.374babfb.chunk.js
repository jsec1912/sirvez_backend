(this["webpackJsonpjumbo-hooks"]=this["webpackJsonpjumbo-hooks"]||[]).push([[35],{1338:function(e,a,t){"use strict";var n=t(1),i=t(6),o=t(0),l=(t(2),t(5)),r=t(8),c=o.forwardRef((function(e,a){var t=e.classes,r=e.className,c=e.disableSpacing,s=void 0!==c&&c,d=Object(i.a)(e,["classes","className","disableSpacing"]);return o.createElement("div",Object(n.a)({className:Object(l.a)(t.root,r,!s&&t.spacing),ref:a},d))}));a.a=Object(r.a)({root:{display:"flex",alignItems:"center",padding:8,justifyContent:"flex-end"},spacing:{"& > :not(:first-child)":{marginLeft:8}}},{name:"MuiAccordionActions"})(c)},1339:function(e,a,t){"use strict";t.r(a);var n=t(9),i=t(0),o=t.n(i),l=t(469),r=t(480),c=t(3),s=t(1370),d=t(1374),u=t(1373),m=t(1375),f=t(1371),p=t(1393),g=t(1372),b=t(1392),h=t(450),v=t(255),E=t(404),N=t(1391),j=t(532),x=t.n(j),O=t(851),y=t(67),w=t(14),_=t(27),S=t(446),C=t(29),R=t(1390),k=t(1376),I=t(572),P=t.n(I),D=t(467),M=t(445),B=t(447),F=t(448),A=t(490),z=t.n(A),J=t(892),L=t(869),T=t(870),$=t(1338),U=t(894),W=t(175),H=t.n(W),V=t(50),q=[{id:"id",align:!1,disablePadding:!0,label:"ID"},{id:"floor_name",align:!0,disablePadding:!1,label:"Floor"},{id:"rooms_count",align:!0,disablePadding:!1,label:"Rooms"},{id:"actions",align:!0,disablePadding:!1,numeric:"right",label:"Actions"}],G=[{id:"id",align:!1,disablePadding:!0,label:"ID"},{id:"room_number",align:!0,disablePadding:!1,label:"Name"},{id:"department_name",align:!0,disablePadding:!1,label:"Department"},{id:"floor_name",align:!0,disablePadding:!1,label:"Floor"},{id:"actions",align:!0,disablePadding:!1,numeric:"right",label:"Actions"}];a.default=function(e){var a=e.location.state?e.location.state.id:"",t=e.location.state?e.location.state.site_id:"",j=Object(i.useState)(!1),I=Object(n.a)(j,2),A=I[0],W=I[1],Y=Object(i.useState)(!1),K=Object(n.a)(Y,2),Q=K[0],X=K[1],Z=Object(i.useState)(!1),ee=Object(n.a)(Z,2),ae=ee[0],te=ee[1],ne=Object(i.useState)("asc"),ie=Object(n.a)(ne,2),oe=ie[0],le=ie[1],re=Object(i.useState)("id"),ce=Object(n.a)(re,2),se=ce[0],de=ce[1],ue=Object(i.useState)(!1),me=Object(n.a)(ue,2),fe=me[0],pe=me[1],ge=Object(w.e)((function(e){return e.settings})).width,be=Object(_.g)(),he=Object(i.useState)(0),ve=Object(n.a)(he,2),Ee=ve[0],Ne=ve[1],je=Object(i.useState)(10),xe=Object(n.a)(je,2),Oe=xe[0],ye=xe[1],we=Object(i.useState)(0),_e=Object(n.a)(we,2),Se=_e[0],Ce=_e[1],Re=Object(i.useState)(10),ke=Object(n.a)(Re,2),Ie=ke[0],Pe=ke[1],De=Object(i.useState)([]),Me=Object(n.a)(De,2),Be=Me[0],Fe=Me[1],Ae=Object(i.useState)([]),ze=Object(n.a)(Ae,2),Je=ze[0],Le=ze[1],Te=Object(i.useState)(""),$e=Object(n.a)(Te,2),Ue=$e[0],We=$e[1],He=Object(i.useState)(""),Ve=Object(n.a)(He,2),qe=Ve[0],Ge=Ve[1],Ye=Object(i.useState)([]),Ke=Object(n.a)(Ye,2),Qe=Ke[0],Xe=Ke[1],Ze=Object(i.useState)(""),ea=Object(n.a)(Ze,2),aa=ea[0],ta=ea[1],na=Object(i.useState)("https://via.placeholder.com/300x300"),ia=Object(n.a)(na,2),oa=ia[0],la=ia[1],ra=Object(i.useState)([]),ca=Object(n.a)(ra,2),sa=ca[0],da=ca[1],ua=Object(i.useState)(""),ma=Object(n.a)(ua,2),fa=ma[0],pa=ma[1],ga=Object(i.useState)(""),ba=Object(n.a)(ga,2),ha=ba[0],va=ba[1],Ea=Object(i.useState)("https://via.placeholder.com/300x300"),Na=Object(n.a)(Ea,2),ja=Na[0],xa=Na[1],Oa=Object(i.useState)([]),ya=Object(n.a)(Oa,2),wa=ya[0],_a=ya[1],Sa=Object(i.useState)(""),Ca=Object(n.a)(Sa,2),Ra=Ca[0],ka=Ca[1],Ia=Object(i.useState)(""),Pa=Object(n.a)(Ia,2),Da=Pa[0],Ma=Pa[1],Ba=Object(i.useState)([]),Fa=Object(n.a)(Ba,2),Aa=Fa[0],za=Fa[1],Ja=Object(i.useState)(""),La=Object(n.a)(Ja,2),Ta=La[0],$a=La[1],Ua=Object(i.useState)(""),Wa=Object(n.a)(Ua,2),Ha=Wa[0],Va=Wa[1],qa=Object(i.useState)(!1),Ga=Object(n.a)(qa,2),Ya=Ga[0],Ka=Ga[1];Object(i.useEffect)((function(){C.a.get(C.b+"building/buildingInfo",{params:{id:a}}).then((function(e){Fe(e.data.floors),Xe(e.data.floors),Le(e.data.rooms),_a(e.data.rooms),da(e.data.building),pe(!0)})).catch((function(e){Ka(!0)}))}),[a]);var Qa=function(){C.a.get(C.b+"building/buildingInfo",{params:{id:a}}).then((function(e){Fe(e.data.floors),Xe(e.data.floors),Le(e.data.rooms),_a(e.data.rooms),da(e.data.building),pe(!0)})).catch((function(e){Ka(!0)}))},Xa=function(e,a){var t;"desc"===(t=se===a&&"asc"===oe?"desc":"asc")?Qe.sort((function(e,t){return t[a]<e[a]?-1:1})):Qe.sort((function(e,t){return e[a]<t[a]?-1:1})),le(t),de(a)},Za=function(e,a){var t;"desc"===(t=se===a&&"asc"===oe?"desc":"asc")?wa.sort((function(e,t){return t[a]<e[a]?-1:1})):wa.sort((function(e,t){return e[a]<t[a]?-1:1})),le(t),de(a)},et=function(e){be.push({pathname:"/app/sites/mysite/floor",state:{id:e,site_id:t,building_id:a}})},at=function(e,n){if(0===n)be.push({pathname:"/app/sites/mysite/floor",state:{id:e,site_id:t,building_id:a}});else if(1===n)tt(e);else if(2===n){navigator.onLine||caches.open("SirvezApp").then((function(t){t.match("/api/building/buildingInfo?id="+a).then((function(e){return e?e.json():null})).then((function(n){if(null!=n){n.floors=n.floors.filter((function(a){return a.id!==e}));var i=new Response(JSON.stringify(n),{headers:{"content-type":"application/json"}});t.put("/api/building/buildingInfo?id="+a,i.clone()).then((function(){Qa()}))}}))}));var i=new FormData;i.append("id",e),C.a.post(C.b+"floor/deleteFloor",i).then((function(e){Qa(),V.NotificationManager.info("You removed selected Floor")})).catch((function(e){V.NotificationManager.error(e,"Error!",1e3,(function(){}))}))}},tt=function(e){We(e),C.a.get(C.b+"floor/getFloorInfo",{params:{id:e}}).then((function(a){e&&(Ge(a.data.floor.floor_name),la(a.data.floor.upload_img?C.c+"upload/img/"+a.data.floor.upload_img:"https://via.placeholder.com/300x300")),W(!0)})).catch((function(e){Ka(!0)}))},nt=function(){W(!1)},it=function(){X(!1)},ot=function(e,t){if(0===t)lt(e.id);else if(1===t)lt(e.id);else if(2===t){navigator.onLine||caches.open("SirvezApp").then((function(t){t.match("/api/building/buildingInfo?id="+a).then((function(e){return e?e.json():null})).then((function(n){if(null!=n){n.rooms=n.rooms.filter((function(a){return a.id!==e.id}));var i=new Response(JSON.stringify(n),{headers:{"content-type":"application/json"}});t.put("/api/building/buildingInfo?id="+a,i.clone()).then((function(){Qa()}))}})),t.match("/api/floor/floorInfo?id="+e.floor_id).then((function(e){return e?e.json():null})).then((function(a){if(null!==a){a.rooms=a.rooms.filter((function(a){return a.id!==e.id}));var n=new Response(JSON.stringify(a),{headers:{"content-type":"application/json"}});t.put("/api/floor/floorInfo?id="+Ue,n.clone()).then((function(){Qa()}))}}))}));var n=new FormData;n.append("id",e.id),C.a.post(C.b+"siteroom/deleteRoom",n).then((function(e){"success"===e.data.status?(Qa(),V.NotificationManager.info("You removed selected Room")):"error"===e.data.status&&V.NotificationManager.error(e.data.msg,"Error!",1e3,(function(){}))})).catch((function(e){V.NotificationManager.error(e,"Error!",1e3,(function(){}))}))}},lt=function(e){ka(e),C.a.get(C.b+"siteroom/roomInfo",{params:{id:e}}).then((function(a){za(a.data.departments),e&&($a(a.data.room.department_id),Ma(a.data.room.room_number)),te(!0)})).catch((function(e){Ka(!0)}))},rt=function(){te(!1)};return o.a.createElement("div",{className:"app-wrapper"},o.a.createElement(r.a,{match:e.match,title:o.a.createElement(c.a,{id:"sidebar.sites.buildingInfo"})}),fe?o.a.createElement("div",{className:"row"},o.a.createElement("div",{className:"col-12 mb-4"},o.a.createElement(J.a,null,o.a.createElement(L.a,{expandIcon:o.a.createElement(H.a,null)},o.a.createElement("div",{className:"DetailedExpansionPanel-column-24"},o.a.createElement("div",{className:"row"},o.a.createElement(E.a,{className:"icon-btn",onClick:function(){return be.go(-1)}},o.a.createElement("i",{className:"zmdi zmdi-arrow-left"})),o.a.createElement("div",{className:"col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6 text-left d-flex align-items-center"},o.a.createElement("h2",{className:"mb-0"},sa.building_name))))),o.a.createElement(T.a,{className:"DetailedExpansionPanel-details-23"},o.a.createElement("div",{className:"DetailedExpansionPanel-column-24 container"},o.a.createElement("div",{className:"row"},o.a.createElement("div",{className:"col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12"},o.a.createElement(l.a,{className:"size-120 m-auto",alt:"Remy Sharp",src:C.c+"upload/img/"+sa.upload_img})),o.a.createElement("div",{className:"col-xl-9 col-lg-9 col-md-6 col-sm-6 col-12"},o.a.createElement("div",{className:"row"},o.a.createElement("div",{className:"col-xl-4 col-lg-4 col-md-6 col-sm-4 col-4 text-left"},o.a.createElement("h2",null,o.a.createElement("strong",null,"building Name"))),o.a.createElement("div",{className:"col-xl-8 col-lg-8 col-md-6 col-sm-8 col-8 text-left"},o.a.createElement("h2",null,sa.building_name))))))),o.a.createElement(U.a,null),o.a.createElement($.a,null,o.a.createElement("ul",{className:"list-inline d-sm-flex flex-sm-row d-flex flex-row jr-mbtn-list mb-0 jr-featured-content-right"},o.a.createElement("li",null,o.a.createElement(M.a,{color:"primary",onClick:function(){return e=a,void C.a.get(C.b+"building/getBuildingInfo",{params:{id:e}}).then((function(a){e&&(pa(a.data.building.building_name),xa(a.data.building.upload_img?C.c+"upload/img/"+a.data.building.upload_img:"https://via.placeholder.com/300x300")),X(!0)})).catch((function(e){Ka(!0)}));var e},variant:"contained",className:"jr-btn text-white"},o.a.createElement(c.a,{id:"button.modify"}))),o.a.createElement("li",null,o.a.createElement(M.a,{color:"secondary",onClick:function(){return function(e){navigator.onLine||caches.open("SirvezApp").then((function(a){a.match("/api/site/siteInfo").then((function(e){return e?e.json():null})).then((function(t){if(null!=t){t.buildings=t.buildings.filter((function(a){return a.id!==e}));var n=new Response(JSON.stringify(t),{headers:{"content-type":"application/json"}});a.put("/api/site/siteInfo",n.clone()).then((function(){be.push({pathname:"/app/sites/mysite/floor"})}))}}))}));var a=new FormData;a.append("id",e),C.a.post(C.b+"building/deleteBuilding",a).then((function(e){"success"===e.data.status?be.push({pathname:"/app/sites/mysite/Info",state:{}}):"error"===e.data.status&&V.NotificationManager.error(e.data.msg,"Error!",1e3,(function(){}))})).catch((function(e){V.NotificationManager.error(e,"Error!",1e3,(function(){}))}))}()},variant:"contained",className:"jr-btn text-white"},o.a.createElement(c.a,{id:"button.delete"}))))))),o.a.createElement("div",{className:"col-12"},o.a.createElement("div",{className:"jr-card"},o.a.createElement("div",null,o.a.createElement(h.a,{className:"table-header"},o.a.createElement("div",{className:"title"},o.a.createElement(v.a,{variant:"h6"},"Floors")),o.a.createElement("div",{className:"col-md-3 col-lg-3 col-sx-6 col-6 ml-auto"},o.a.createElement(y.a,{placeholder:"Search ...",onChange:function(e){return a=e.target.value,void Xe(Be.filter((function(e){return e.floor_name.toLowerCase().includes(a.toLowerCase())})));var a}})),o.a.createElement("div",{className:"actions"},o.a.createElement(N.a,{title:"New Floor"},o.a.createElement(E.a,{"aria-label":"New Floor",onClick:function(){return tt("")}},o.a.createElement(x.a,null))))),fe?o.a.createElement("div",{className:"flex-auto"},o.a.createElement("div",{className:"table-responsive-material"},o.a.createElement(s.a,{className:""},o.a.createElement(f.a,null,o.a.createElement(g.a,null,q.map((function(e){return o.a.createElement(u.a,{key:e.id,align:e.numeric},o.a.createElement(N.a,{title:"Sort",placement:e.numeric?"bottom-end":"bottom-start",enterDelay:300},o.a.createElement(b.a,{active:se===e.id,direction:oe,onClick:(a=e.id,function(e){Xa(e,a)})},e.label)));var a})))),o.a.createElement(d.a,null,Qe.length>0?Qe.slice(Ee*Oe,Ee*Oe+Oe).map((function(e){return o.a.createElement(g.a,{hover:!0,key:e.id,tabIndex:-1},o.a.createElement(u.a,{onClick:function(){return et(e.id)}},e.id),o.a.createElement(u.a,{onClick:function(){return et(e.id)}},o.a.createElement("div",{className:"user-profile d-flex flex-row align-items-center"},o.a.createElement(l.a,{alt:e.floor_name,src:C.c+"upload/img/"+e.upload_img,className:"user-avatar"}),o.a.createElement("div",{className:"user-detail"},o.a.createElement("h5",{className:"user-name"},e.floor_name)))),o.a.createElement(u.a,{onClick:function(){return et(e.id)}},e.rooms_count),o.a.createElement(u.a,{className:"text-right"},o.a.createElement(O.a,{key:e.id,selectedData:e.id,onSelectRow:at})))})):o.a.createElement("tr",null,o.a.createElement("td",{className:"text-danger",colSpan:"4"},o.a.createElement(c.a,{id:"table.noData"})))),o.a.createElement(m.a,null,o.a.createElement(g.a,null,o.a.createElement(p.a,{count:Qe.length,rowsPerPage:Oe,page:Ee,onChangePage:function(e,a){Ne(a)},onChangeRowsPerPage:function(e){ye(e.target.value)}}))))),o.a.createElement(V.NotificationContainer,null)):o.a.createElement("div",{className:"loader-view",style:{height:ge>=1200?"calc(100vh - 259px)":"calc(100vh - 238px)"}},o.a.createElement(S.a,null))))),o.a.createElement("div",{className:"col-12"},o.a.createElement("div",{className:"jr-card"},o.a.createElement("div",null,o.a.createElement(h.a,{className:"table-header"},o.a.createElement("div",{className:"title"},o.a.createElement(v.a,{variant:"h6"},"Rooms")),o.a.createElement("div",{className:"col-md-3 col-lg-3 col-sx-6 col-6 ml-auto"},o.a.createElement(y.a,{placeholder:"Search ...",onChange:function(e){return a=e.target.value,void _a(Je.filter((function(e){return e.room_number.toLowerCase().includes(a.toLowerCase())})));var a}})),o.a.createElement("div",{className:"actions"},o.a.createElement(N.a,{title:"New Room"},o.a.createElement(E.a,{"aria-label":"New Room",onClick:function(){return lt("")}},o.a.createElement(x.a,null))))),fe?o.a.createElement("div",{className:"flex-auto"},o.a.createElement("div",{className:"table-responsive-material"},o.a.createElement(s.a,{className:""},o.a.createElement(f.a,null,o.a.createElement(g.a,null,G.map((function(e){return o.a.createElement(u.a,{key:e.id,align:e.numeric},o.a.createElement(N.a,{title:"Sort",placement:e.numeric?"bottom-end":"bottom-start",enterDelay:300},o.a.createElement(b.a,{active:se===e.id,direction:oe,onClick:(a=e.id,function(e){Za(e,a)})},e.label)));var a})))),o.a.createElement(d.a,null,wa.length>0?wa.slice(Se*Ie,Se*Ie+Ie).map((function(e){return o.a.createElement(g.a,{hover:!0,key:e.id,tabIndex:-1},o.a.createElement(u.a,null,e.id),o.a.createElement(u.a,null,e.room_number),o.a.createElement(u.a,null,e.department_name),o.a.createElement(u.a,null,e.floor_name),o.a.createElement(u.a,{className:"text-right"},o.a.createElement(O.a,{key:e.id,selectedData:e,onSelectRow:ot})))})):o.a.createElement("tr",null,o.a.createElement("td",{className:"text-danger",colSpan:"4"},o.a.createElement(c.a,{id:"table.noData"})))),o.a.createElement(m.a,null,o.a.createElement(g.a,null,o.a.createElement(p.a,{count:wa.length,rowsPerPage:Ie,page:Se,onChangePage:function(e,a){Ce(a)},onChangeRowsPerPage:function(e){Pe(e.target.value)}}))))),o.a.createElement(V.NotificationContainer,null)):o.a.createElement("div",{className:"loader-view",style:{height:ge>=1200?"calc(100vh - 259px)":"calc(100vh - 238px)"}},o.a.createElement(S.a,null))))),o.a.createElement(R.a,{key:"room-save",className:"modal-box",toggle:rt,isOpen:ae},o.a.createElement(k.a,null,Ra?"Modify Room":"Add Room",o.a.createElement(E.a,{className:"text-grey",onClick:rt},o.a.createElement(P.a,null))),o.a.createElement("div",{className:"modal-box-content"},o.a.createElement("div",{className:"row"},o.a.createElement("div",{className:"col-md-6 col-12"},o.a.createElement(D.a,{id:"room_department",select:!0,label:"department",value:Ta,onChange:function(e){return $a(e.target.value)},helperText:"Please select Department",margin:"normal",fullWidth:!0},Aa.map((function(e){return o.a.createElement(F.a,{key:e.id,value:e.id},e.department_name)})))),o.a.createElement("div",{className:"col-md-6 col-12"},o.a.createElement(D.a,{id:"room_floor",select:!0,label:"floor",value:Ha,onChange:function(e){return Va(e.target.value)},helperText:"Please select Floor",margin:"normal",fullWidth:!0},Qe.map((function(e){return o.a.createElement(F.a,{key:e.id,value:e.id},e.floor_name)})))),o.a.createElement("div",{className:"col-md-6 col-12"},o.a.createElement(D.a,{id:"room_number",label:"Room Number",value:Da,onChange:function(e){return Ma(e.target.value)},margin:"normal",helperText:"Please Enter Room Number",fullWidth:!0})))),o.a.createElement("div",{className:"modal-box-footer d-flex flex-row jr-featured-content-right"},o.a.createElement(M.a,{onClick:rt,color:"secondary",variant:"contained",className:"jr-btn text-white"},"Cancel"),o.a.createElement(M.a,{onClick:function(){rt();var e=Ra||JSON.parse(localStorage.getItem("user")).id+"_"+(new Date).getTime(),n=new FormData;n.append("id",e),n.append("site_id",t),n.append("department_id",Ta),n.append("building_id",a),n.append("floor_id",Ha),n.append("room_number",Da),C.a.post(C.b+"siteroom/updateRoom",n).then((function(e){"success"===e.data.status?(V.NotificationManager.success("Updated room!","Success!",1e3,(function(){})),Qa()):"error"===e.data.status&&V.NotificationManager.error(e.data.msg,"Error!",1e3,(function(){}))})).catch((function(e){V.NotificationManager.error(e,"Error!",1e3,(function(){}))})),navigator.onLine||caches.open("SirvezApp").then((function(n){var i={id:e,site_id:t,department_id:Ta,building_id:a,floor_id:Ha,room_number:Da,department_name:Aa.filter((function(e){return e.id===Ta}))[0].department_name,floor_name:Qe.filter((function(e){return e.id===Ha}))[0].floor_name},o={room:i,status:"success"},l=new Response(JSON.stringify(o),{headers:{"content-type":"application/json"}});n.put("/api/room/getRoomInfo?id="+e,l.clone()),n.match("/api/building/buildingInfo?id="+a).then((function(e){return e?e.json():null})).then((function(t){if(null!==t){var o=t.rooms.find((function(a){return a.id===e}));o?o=i:t.rooms.unshift(i);var l=new Response(JSON.stringify(t),{headers:{"content-type":"application/json"}});n.put("/api/building/buildingInfo?id="+a,l.clone())}})),n.match("/api/floor/floorInfo?id="+Ha).then((function(e){return e?e.json():null})).then((function(a){if(null!==a){var t=a.rooms.find((function(a){return a.id===e}));t?t=i:a.rooms.unshift(i);var o=new Response(JSON.stringify(a),{headers:{"content-type":"application/json"}});n.put("/api/floor/floorInfo?id="+Ha,o.clone()).then((function(){Qa()}))}}))}))},color:"primary",variant:"contained",className:"jr-btn text-white"},"Save"))),o.a.createElement(R.a,{key:"building-save",className:"modal-box",toggle:it,isOpen:Q,fullwidth:"true"},o.a.createElement(k.a,null,a?"Modify Building":"Add Building",o.a.createElement(E.a,{className:"text-grey",onClick:it},o.a.createElement(P.a,null))),o.a.createElement("div",{className:"modal-box-content"},o.a.createElement("div",{className:"row"},o.a.createElement("div",{className:"col-xl-3 col-lg-4 col-md-5 col-12"},o.a.createElement("div",{className:"jr-card pb-2"},o.a.createElement("div",{className:"jr-card-thumb"},o.a.createElement("img",{className:"card-img-top img-fluid",alt:"products",src:ja})),o.a.createElement("input",{type:"file",id:"upload_img",accept:"image/*",name:"upload_img",style:{display:"none"},onChange:function(e){return function(e){e.preventDefault();var a=new FileReader,t=e.target.files[0];a.onloadend=function(){va(t),xa(a.result)},a.readAsDataURL(t)}(e)}}),o.a.createElement("div",{className:"jr-card-social text-right"},o.a.createElement(B.a,{className:"jr-fab-btn bg-secondary text-white jr-btn-fab-xs mb-3",onClick:function(e){document.getElementById("upload_img").click()}},o.a.createElement("i",{className:"zmdi zmdi-cloud-upload zmdi-hc-1x"}))))),o.a.createElement("div",{className:"col-xl-9 col-lg-8 col-md-7 col-12"},o.a.createElement("div",{className:"row"},o.a.createElement("div",{className:"col-md-12 col-12"},o.a.createElement(D.a,{id:"building_name",label:"Building Name",value:fa,onChange:function(e){return pa(e.target.value)},margin:"normal",helperText:"Please Enter Building Name",fullWidth:!0})))))),o.a.createElement("div",{className:"modal-box-footer d-flex flex-row jr-featured-content-right"},o.a.createElement(M.a,{onClick:it,color:"secondary",variant:"contained",className:"jr-btn text-white"},"Cancel"),o.a.createElement(M.a,{onClick:function(){it();var e=new FormData;e.append("id",a),e.append("site_id",t),e.append("building_name",fa),e.append("upload_img",ha),C.a.post(C.b+"building/updateBuilding",e).then((function(e){"success"===e.data.status?(V.NotificationManager.success("Updated building","Success!",1e3,(function(){})),Qa()):"error"===e.data.status&&V.NotificationManager.error(e.data.msg,"Error!",1e3,(function(){}))})).catch((function(e){V.NotificationManager.error(e,"Error!",1e3,(function(){}))})),navigator.onLine||caches.open("SirvezApp").then((function(e){var n="";ha&&(n=(new Date).getTime()+"_new.jpeg",e.put("/upload/img/"+n,new Response(ha)));var i={id:a,site_id:t,building_name:fa};""!==n&&(i.upload_img=n);var o={building:i,status:"success"},l=new Response(JSON.stringify(o),{headers:{"content-type":"application/json"}});e.put("/api/building/getbuildingInfo?id="+a,l.clone()),e.match("/api/building/buildingInfo?id="+a).then((function(e){return e?e.json():null})).then((function(t){if(null==t){t={building:i,status:"success",floors:[],rooms:[]};var n=new Response(JSON.stringify(t),{headers:{"content-type":"application/json"}});e.put("/api/building/buildingInfo?id="+a,n.clone())}else{t.building=i;var o=new Response(JSON.stringify(t),{headers:{"content-type":"application/json"}});e.put("/api/building/buildingInfo?id="+a,o.clone())}})),e.match("/api/site/siteInfo?id="+t).then((function(e){return e?e.json():null})).then((function(o){if(null!=o){var l=o.buildings.find((function(e){return e.id==a}));l?(l.site_id=t,l.building_name=fa,""!==n&&(l.upload_img=n)):o.buildings.unshift(i);var r=new Response(JSON.stringify(o),{headers:{"content-type":"application/json"}});e.put("/api/site/siteInfo?id="+t,r.clone()).then((function(){Qa()}))}}))}))},color:"primary",variant:"contained",className:"jr-btn text-white"},"Save"))),o.a.createElement(R.a,{key:"floor-save",className:"modal-box",toggle:nt,isOpen:A},o.a.createElement(k.a,null,Ue?"Modify floor":"Add Floor",o.a.createElement(E.a,{className:"text-grey",onClick:nt},o.a.createElement(P.a,null))),o.a.createElement("div",{className:"modal-box-content"},o.a.createElement("div",{className:"row"},o.a.createElement("div",{className:"col-xl-3 col-lg-4 col-md-5 col-12"},o.a.createElement("div",{className:"jr-card pb-2"},o.a.createElement("div",{className:"jr-card-thumb"},o.a.createElement("img",{className:"card-img-top img-fluid",alt:"products",src:oa})),o.a.createElement("input",{type:"file",id:"upload_img",accept:"image/*",name:"upload_img",style:{display:"none"},onChange:function(e){return function(e){e.preventDefault();var a=new FileReader,t=e.target.files[0];a.onloadend=function(){ta(t),la(a.result)},a.readAsDataURL(t)}(e)}}),o.a.createElement("div",{className:"jr-card-social text-right"},o.a.createElement(B.a,{className:"jr-fab-btn bg-secondary text-white jr-btn-fab-xs mb-3",onClick:function(e){document.getElementById("upload_img").click()}},o.a.createElement("i",{className:"zmdi zmdi-cloud-upload zmdi-hc-1x"}))))),o.a.createElement("div",{className:"col-xl-9 col-lg-8 col-md-7 col-12"},o.a.createElement("div",{className:"row"},o.a.createElement("div",{className:"col-md-6 col-12"},o.a.createElement(D.a,{id:"floor_name",label:"Floor",value:qe,onChange:function(e){return Ge(e.target.value)},margin:"normal",helperText:"Please Enter Floor",fullWidth:!0})),o.a.createElement("div",{className:"modal-box-footer d-flex flex-row jr-featured-content-right"},o.a.createElement(M.a,{onClick:nt,color:"secondary",variant:"contained",className:"jr-btn text-white"},"Cancel"),o.a.createElement(M.a,{onClick:function(){nt();var e=Ue||JSON.parse(localStorage.getItem("user")).id+"_"+(new Date).getTime(),n=new FormData;n.append("id",e),n.append("site_id",t),n.append("building_id",a),n.append("floor_name",qe),n.append("upload_img",aa),C.a.post(C.b+"floor/updateFloor",n).then((function(e){"success"===e.data.status?(V.NotificationManager.success("Updated floor","Success!",1e3,(function(){})),Qa()):"error"===e.data.status&&V.NotificationManager.error(e.data.msg,"Error!",1e3,(function(){}))})).catch((function(e){V.NotificationManager.error(e,"Error!",1e3,(function(){}))})),navigator.onLine||caches.open("SirvezApp").then((function(n){var i="";aa&&(i=(new Date).getTime()+"_new.jpeg",n.put("/upload/img/"+i,new Response(aa)));var o={id:e,site_id:t,building_id:a,floor_name:qe,rooms_count:0};""!==i&&(o.upload_img=i);var l={floor:o,status:"success"},r=new Response(JSON.stringify(l),{headers:{"content-type":"application/json"}});n.put("/api/floor/getFloorInfo?id="+e,r.clone()),n.match("/api/floor/floorInfo?id="+e).then((function(e){return e?e.json():null})).then((function(a){if(null==a){a={floor:o,status:"success",rooms:[]};var t=new Response(JSON.stringify(a),{headers:{"content-type":"application/json"}});n.put("/api/floor/floorInfo?id="+e,t.clone())}else{a.floor=o;var i=new Response(JSON.stringify(a),{headers:{"content-type":"application/json"}});n.put("/api/floor/floorInfo?id="+e,i.clone())}})),n.match("/api/building/buildingInfo?id="+a).then((function(e){return e?e.json():null})).then((function(l){if(null!=l){var r=l.floors.find((function(a){return a.id==e}));r?(r.site_id=t,r.building_id=a,r.floor_name=qe,""!==i&&(r.upload_img=i)):l.floors.unshift(o);var c=new Response(JSON.stringify(l),{headers:{"content-type":"application/json"}});n.put("/api/building/buildingInfo?id="+a,c.clone()).then((function(){Qa()}))}}))}))},color:"primary",variant:"contained",className:"jr-btn text-white"},"Save")))))))):o.a.createElement("div",{className:"loader-view",style:{height:ge>=1200?"calc(100vh - 259px)":"calc(100vh - 238px)"}},o.a.createElement(S.a,null)),o.a.createElement(z.a,{show:Ya,warning:!0,confirmBtnText:"Go Back",confirmBtnBsStyle:"danger",cancelBtnBsStyle:"default",title:"Warning!",onConfirm:function(){return be.go(-1)}},"Data not cached, so you can not view this page."))}},480:function(e,a,t){"use strict";var n=t(0),i=t.n(n),o=t(529),l=t(530),r=function(e,a,t){return 0===t?"/":"/"+e.split(a)[0]+a};a.a=function(e){var a=e.title,t=e.match,n=e.project_name,c=e.room_number,s=[],d=t.url.substr(1),u=d.split("/");return u.map((function(e,a){""===e&&u.splice(a,1),"live"===u[a-1]?s[a]=n||"":"live"===u[a-2]?s[a]=c||"":s[a]=e})),i.a.createElement("div",{className:"page-heading d-sm-flex justify-content-sm-between align-items-sm-center"},i.a.createElement("h2",{className:"title mb-3 mb-sm-0"},a),i.a.createElement(o.a,{className:"mb-0",tag:"nav"},u.map((function(e,a){if(0!=a)return i.a.createElement(l.a,{active:u.length===a+1,tag:u.length===a+1?"span":"a",key:a,href:r(d,e,a)},function(e){var a=e.split("-");return a.length>1?a[0].charAt(0).toUpperCase()+a[0].slice(1)+" "+a[1].charAt(0).toUpperCase()+a[1].slice(1):e.charAt(0).toUpperCase()+e.slice(1)}(s[a]))}))))}},531:function(e,a,t){"use strict";var n=t(0),i=n.createContext({});a.a=i},532:function(e,a,t){"use strict";var n=t(41);Object.defineProperty(a,"__esModule",{value:!0}),a.default=void 0;var i=n(t(0)),o=(0,n(t(173)).default)(i.default.createElement("path",{d:"M14 10H2v2h12v-2zm0-4H2v2h12V6zm4 8v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM2 16h8v-2H2v2z"}),"PlaylistAdd");a.default=o},851:function(e,a,t){"use strict";var n=t(9),i=t(0),o=t.n(i),l=t(252),r=t(448),c=t(404);a.a=function(e){var a=Object(i.useState)(!1),t=Object(n.a)(a,2),s=t[0],d=t[1],u=Object(i.useState)(void 0),m=Object(n.a)(u,2),f=m[0],p=m[1],g=e.selectedData,b=e.onSelectRow,h=e.menu_rows,v=h||[{content:"View",id:0},{content:"Modify",id:1},{content:"Delete",id:2}],E=function(){d(!1)};return o.a.createElement("div",null,o.a.createElement(c.a,{onClick:function(e){d(!0),p(e.currentTarget)}},o.a.createElement("i",{className:"zmdi zmdi-more-vert"})),o.a.createElement(l.a,{anchorEl:f,open:s,onClose:E,key:g.id,MenuListProps:{style:{width:150,paddingTop:0,paddingBottom:0}}},v.map((function(e){return o.a.createElement(r.a,{key:g+"-"+e.content,onClick:function(){E(),b(g,e.id)}},e.content)}))))}},869:function(e,a,t){"use strict";var n=t(1),i=t(6),o=t(0),l=(t(2),t(5)),r=t(151),c=t(404),s=t(8),d=t(531),u=o.forwardRef((function(e,a){var t=e.children,s=e.classes,u=e.className,m=e.expandIcon,f=e.IconButtonProps,p=e.onBlur,g=e.onClick,b=e.onFocusVisible,h=Object(i.a)(e,["children","classes","className","expandIcon","IconButtonProps","onBlur","onClick","onFocusVisible"]),v=o.useState(!1),E=v[0],N=v[1],j=o.useContext(d.a),x=j.disabled,O=void 0!==x&&x,y=j.expanded,w=j.toggle;return o.createElement(r.a,Object(n.a)({focusRipple:!1,disableRipple:!0,disabled:O,component:"div","aria-expanded":y,className:Object(l.a)(s.root,u,O&&s.disabled,y&&s.expanded,E&&s.focused),onFocusVisible:function(e){N(!0),b&&b(e)},onBlur:function(e){N(!1),p&&p(e)},onClick:function(e){w&&w(e),g&&g(e)},ref:a},h),o.createElement("div",{className:Object(l.a)(s.content,y&&s.expanded)},t),m&&o.createElement(c.a,Object(n.a)({className:Object(l.a)(s.expandIcon,y&&s.expanded),edge:"end",component:"div",tabIndex:null,role:null,"aria-hidden":!0},f),m))}));a.a=Object(s.a)((function(e){var a={duration:e.transitions.duration.shortest};return{root:{display:"flex",minHeight:48,transition:e.transitions.create(["min-height","background-color"],a),padding:e.spacing(0,2),"&:hover:not($disabled)":{cursor:"pointer"},"&$expanded":{minHeight:64},"&$focused":{backgroundColor:e.palette.action.focus},"&$disabled":{opacity:e.palette.action.disabledOpacity}},expanded:{},focused:{},disabled:{},content:{display:"flex",flexGrow:1,transition:e.transitions.create(["margin"],a),margin:"12px 0","&$expanded":{margin:"20px 0"}},expandIcon:{transform:"rotate(0deg)",transition:e.transitions.create("transform",a),"&:hover":{backgroundColor:"transparent"},"&$expanded":{transform:"rotate(180deg)"}}}}),{name:"MuiAccordionSummary"})(u)},870:function(e,a,t){"use strict";var n=t(1),i=t(6),o=t(0),l=(t(2),t(5)),r=t(8),c=o.forwardRef((function(e,a){var t=e.classes,r=e.className,c=Object(i.a)(e,["classes","className"]);return o.createElement("div",Object(n.a)({className:Object(l.a)(t.root,r),ref:a},c))}));a.a=Object(r.a)((function(e){return{root:{display:"flex",padding:e.spacing(1,2,2)}}}),{name:"MuiAccordionDetails"})(c)},892:function(e,a,t){"use strict";var n=t(1),i=t(182),o=t(181),l=t(126),r=t(183);var c=t(44),s=t(6),d=t(0),u=(t(35),t(2),t(5)),m=t(454),f=t(171),p=t(8),g=t(531),b=t(92),h=d.forwardRef((function(e,a){var t,p=e.children,h=e.classes,v=e.className,E=e.defaultExpanded,N=void 0!==E&&E,j=e.disabled,x=void 0!==j&&j,O=e.expanded,y=e.onChange,w=e.square,_=void 0!==w&&w,S=e.TransitionComponent,C=void 0===S?m.a:S,R=e.TransitionProps,k=Object(s.a)(e,["children","classes","className","defaultExpanded","disabled","expanded","onChange","square","TransitionComponent","TransitionProps"]),I=Object(b.a)({controlled:O,default:N,name:"Accordion",state:"expanded"}),P=Object(c.a)(I,2),D=P[0],M=P[1],B=d.useCallback((function(e){M(!D),y&&y(e,!D)}),[D,y,M]),F=d.Children.toArray(p),A=(t=F,Object(i.a)(t)||Object(o.a)(t)||Object(l.a)(t)||Object(r.a)()),z=A[0],J=A.slice(1),L=d.useMemo((function(){return{expanded:D,disabled:x,toggle:B}}),[D,x,B]);return d.createElement(f.a,Object(n.a)({className:Object(u.a)(h.root,v,D&&h.expanded,x&&h.disabled,!_&&h.rounded),ref:a,square:_},k),d.createElement(g.a.Provider,{value:L},z),d.createElement(C,Object(n.a)({in:D,timeout:"auto"},R),d.createElement("div",{"aria-labelledby":z.props.id,id:z.props["aria-controls"],role:"region"},J)))}));a.a=Object(p.a)((function(e){var a={duration:e.transitions.duration.shortest};return{root:{position:"relative",transition:e.transitions.create(["margin"],a),"&:before":{position:"absolute",left:0,top:-1,right:0,height:1,content:'""',opacity:1,backgroundColor:e.palette.divider,transition:e.transitions.create(["opacity","background-color"],a)},"&:first-child":{"&:before":{display:"none"}},"&$expanded":{margin:"16px 0","&:first-child":{marginTop:0},"&:last-child":{marginBottom:0},"&:before":{opacity:0}},"&$expanded + &":{"&:before":{display:"none"}},"&$disabled":{backgroundColor:e.palette.action.disabledBackground}},rounded:{borderRadius:0,"&:first-child":{borderTopLeftRadius:e.shape.borderRadius,borderTopRightRadius:e.shape.borderRadius},"&:last-child":{borderBottomLeftRadius:e.shape.borderRadius,borderBottomRightRadius:e.shape.borderRadius,"@supports (-ms-ime-align: auto)":{borderBottomLeftRadius:0,borderBottomRightRadius:0}}},expanded:{},disabled:{}}}),{name:"MuiAccordion"})(h)},894:function(e,a,t){"use strict";var n=t(1),i=t(6),o=t(0),l=(t(2),t(5)),r=t(8),c=t(31),s=o.forwardRef((function(e,a){var t=e.absolute,r=void 0!==t&&t,c=e.classes,s=e.className,d=e.component,u=void 0===d?"hr":d,m=e.flexItem,f=void 0!==m&&m,p=e.light,g=void 0!==p&&p,b=e.orientation,h=void 0===b?"horizontal":b,v=e.role,E=void 0===v?"hr"!==u?"separator":void 0:v,N=e.variant,j=void 0===N?"fullWidth":N,x=Object(i.a)(e,["absolute","classes","className","component","flexItem","light","orientation","role","variant"]);return o.createElement(u,Object(n.a)({className:Object(l.a)(c.root,s,"fullWidth"!==j&&c[j],r&&c.absolute,f&&c.flexItem,g&&c.light,"vertical"===h&&c.vertical),role:E,ref:a},x))}));a.a=Object(r.a)((function(e){return{root:{height:1,margin:0,border:"none",flexShrink:0,backgroundColor:e.palette.divider},absolute:{position:"absolute",bottom:0,left:0,width:"100%"},inset:{marginLeft:72},light:{backgroundColor:Object(c.c)(e.palette.divider,.08)},middle:{marginLeft:e.spacing(2),marginRight:e.spacing(2)},vertical:{height:"100%",width:1},flexItem:{alignSelf:"stretch",height:"auto"}}}),{name:"MuiDivider"})(s)}}]);
//# sourceMappingURL=35.374babfb.chunk.js.map