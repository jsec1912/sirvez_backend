<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

//login,register
Route::group(['middleware' => ['api-header', 'cors']], function () {

    // The registration and login requests doesn't come with tokens
    // as users at that point have not been authenticated yet
    // Therefore the jwtMiddleware will be exclusive of them

    Route::get('user/login', 'UserController@login');
    Route::post('user/register', 'UserController@register');
    Route::post('user/forgot', 'UserController@forgot');
    Route::get('user/checkToken', 'UserController@checkToken');
    Route::post('user/changePassword', 'UserController@changePassword');
    Route::post('/room/saveimage','RoomController@saveimage');
    Route::post('user/checkValidate', 'UserController@checkValidate');
    Route::post('companyuser/register', 'UserController@companyUserRegister');


});

Route::get('/company/getCompanyImg','CompanyController@getCompanyImg');

Route::group(['middleware' => ['jwt-auth','api-header','cors']], function () {

    //dashboard
    Route::get('/dashboard','CompanyCustomerController@getDashboard');////
    Route::get('/barcodeCheck','ProductController@barcodeCheck');////
    //company modify
    Route::post('/company/saveCompany','CompanyController@saveCompany');
    Route::get('/company/getCompanyInfo','CompanyController@getCompanyInfo');
    Route::post('/company/changeLogo','CompanyController@changeLogo');
    Route::post('/company/changeName', 'CompanyController@changeName');
    Route::get('/company/customerList','CompanyController@customerList');
    Route::post('/company/updatePartnerCompany', 'CompanyController@updatePartnerCompany');
    Route::get('/company/partnerlist', 'CompanyController@partnerlist');
    Route::post('/company/deletePartner', 'CompanyController@deletePartner');
    Route::post('/company/setAllowPartner', 'CompanyController@setAllowPartner');
    Route::post('/company/setAllowPartnerRequest', 'CompanyController@setAllowPartnerRequest');
    Route::post('/company/setPrimaryUser', 'CompanyController@setPrimaryUser');
    Route::post('/company/changeParentCompany', 'CompanyController@changeParentCompany');
    //customer
    Route::Post('/customers/company/customer-edit','CompanyCustomerController@addCompanyCustomer');///
    Route::Post('/customers/DeleteCompanyCustomer','CompanyCustomerController@DeleteCompanyCustomer');///
    Route::get('/customers/company','CompanyCustomerController@getCompanyCustomer');///
    Route::get('/customers/companyInfo','CompanyCustomerController@CompanyCustomerInfo');///
    Route::get('/customers/company/customer-edit','CompanyCustomerController@getCustomerInfo');///
    Route::get('/customers/userList','CompanyCustomerController@userList');///
    Route::Post('/customers/pendingUser','CompanyCustomerController@pendingUser');
    Route::Post('/customers/setFavourite','CompanyCustomerController@setFavourite');
    Route::get('/customers/partnerlist', 'CompanyCustomerController@partnerlist');
    Route::post('/customers/addPartner', 'CompanyCustomerController@addPartner');
    Route::post('/customers/deletePartner', 'CompanyCustomerController@deletePartner');

    Route::post('/project/updateProject','ProjectController@updateProject');///
    Route::post('/project/deleteProject','ProjectController@deleteProject');///
    Route::get('/project/projectList','ProjectController@projectList');///
    Route::get('/project/projectInfo','ProjectController@projectInfo');///
    //Route::get('/project/getprojectInfo','ProjectController@getProjectInfo');///
    Route::post('/project/setFavourite','ProjectController@setFavourite');
    Route::post('/project/deleteAssignUser','ProjectController@deleteAssignUser');
    Route::post('/project/signOff','ProjectController@signOff');
    Route::post('/project/finalSignOff','ProjectController@finalSignOff');
    Route::post('/project/addAssignUser','ProjectController@addAssignUser');
    Route::post('/project/addProjectUser', 'ProjectController@addProjectUser');
    // Route::post('/project/addCustomerUser','ProjectController@addCustomerUser');
    // Route::post('/project/deleteCustomerUser','ProjectController@deleteCustomerUser');
    Route::post('/project/deleteProjectUser', 'ProjectController@deleteProjectUser');
    Route::post('/project/addPartnerUser','ProjectController@addPartnerUser');
    Route::post('/project/deletePartnerUser','ProjectController@deletePartnerUser');
    Route::post('/project/changeSummary','ProjectController@changeSummary');
    Route::post('/project/changeProjectName','ProjectController@changeProjectName');
    Route::post('/project/changeProjectRef','ProjectController@changeProjectRef');
    Route::post('/project/changeProjectDate','ProjectController@changeProjectDate');
    Route::post('/project/changeContactNumber','ProjectController@changeContactNumber');
    Route::post('/project/changeLocationForm','ProjectController@changeLocationForm');
    Route::post('/project/changeSignoffForm','ProjectController@changeSignoffForm');
    Route::post('/project/changeFinalSignoffForm','ProjectController@changeFinalSignoffForm');
    Route::get('/project/getProjectModule','ProjectController@getProjectModule');
    Route::post('/project/updateProjectModule','ProjectController@updateProjectModule');
    Route::get('/project/getActivity','ProjectController@getActivity');
    Route::post('/project/addProjectPage','ProjectController@addProjectPage');
    Route::post('/project/removeProjectPage','ProjectController@removeProjectPage');
    Route::post('/project/changeOrderProjectPage', 'ProjectController@changeOrderProjectPage');
    Route::post('/project/changeOrderRoom', 'ProjectController@changeOrderRoom');
    Route::post('/project/addProjectTender','ProjectController@addProjectTender');
    Route::post('/project/removeProjectTender','ProjectController@removeProjectTender');
    Route::post('/project/changeOrderProjectTender', 'ProjectController@changeOrderProjectTender');
    Route::post('/project/addProjectHealthy','ProjectController@addProjectHealthy');
    Route::post('/project/removeProjectHealthy','ProjectController@removeProjectHealthy');
    Route::post('/project/changeOrderProjectHealthy', 'ProjectController@changeOrderProjectHealthy');    
    Route::post('/project/subscription', 'ProjectController@createSubscription');
    Route::post('/project/changeTopMenu', 'ProjectController@changeTopMenu');
    Route::post('/project/changeLockProjectPage', 'ProjectController@changeLockProjectPage');
    Route::post('/project/signOffAllLocaton', 'ProjectController@signOffAllLocaton');
    Route::post('/project/moveInstallStage', 'ProjectController@moveInstallStage');
    Route::post('/project/changePageFile', 'ProjectController@changePageFile');
    Route::post('/project/updateInstallFile', 'ProjectController@updateInstallFile');

    Route::post('/site/updateSite','SiteController@updateSite');///
    Route::post('/site/deleteSite','SiteController@deleteSite');///
    Route::get('/site/siteList','SiteController@siteList');
    Route::get('/site/siteInfo','SiteController@siteInfo');///
    Route::get('/site/getSiteInfo','SiteController@getSiteInfo');
    Route::post('/siteroom/updateRoom','SiteController@updateRoom');
    Route::post('/siteroom/deleteRoom','SiteController@deleteRoom');
    Route::get('/siteroom/roomInfo','SiteController@roomInfo');
    Route::post('/siteroom/savePolygon','SiteController@savePolygon');
    

    Route::post('/department/updateDepartment','DepartmentController@updateDepartment');
    Route::post('/department/deleteDepartment','DepartmentController@deleteDepartment');
    Route::get('/department/departmentList','DepartmentController@departmentList');
    Route::get('/department/departmentInfo','DepartmentController@departmentInfo');

    Route::post('/building/updateBuilding','BuildingController@updateBuilding');
    Route::post('/building/deleteBuilding','BuildingController@deleteBuilding');
    Route::get('/building/buildingList','BuildingController@buildingList');
    Route::get('/building/buildingInfo','BuildingController@buildingInfo');
    Route::get('/building/getBuildingInfo','BuildingController@getBuildingInfo');

    Route::post('/floor/updateFloor','FloorController@updateFloor');
    Route::post('/floor/deleteFloor','FloorController@deleteFloor');
    Route::get('/floor/floorList','FloorController@floorList');
    Route::get('/floor/floorInfo','FloorController@floorInfo');
    Route::get('/floor/getFloorInfo','FloorController@getFloorInfo');

    Route::post('/projectsite/updateSite','ProjectSiteController@updateSite');
    Route::post('/projectsite/deleteSite','ProjectSiteController@deleteSite');
    Route::get('/projectsite/siteList','ProjectSiteController@siteList');
    Route::get('/projectsite/siteInfo','ProjectSiteController@siteInfo');

    Route::post('/product/updateProduct','ProductController@updateProduct');
    Route::post('/product/deleteProduct','ProductController@deleteProduct');
    Route::get('/product/productList','ProductController@productList');
    Route::get('/product/productInfo','ProductController@productInfo');
    Route::get('/product/getProductInfo','ProductController@getProductInfo');
    Route::post('/product/import-product', 'ProductController@importProduct');
    Route::post('/product/import-list', 'ProductController@importList');
    Route::post('/product/signOff', 'ProductController@signOff');
    Route::post('/product/testSignOff', 'ProductController@testSignOff');
    Route::post('/product/comSignOff', 'ProductController@comSignOff');
    Route::post('/product/saveTestingForm', 'ProductController@saveTestingForm');
    Route::post('/product/savecommissioningForm', 'ProductController@savecommissioningForm');
    Route::post('/product/changeProductName', 'ProductController@changeProductName');
    Route::post('/product/changeProductDescription', 'ProductController@changeProductDescription');
    Route::post('/product/changeProductSupplier', 'ProductController@changeProductSupplier');
    Route::post('/product/changeModelNumber', 'ProductController@changeModelNumber');
    Route::post('/product/changeManufacturer', 'ProductController@changeManufacturer');
    Route::post('/product/changeTestingFormId', 'ProductController@changeTestingFormId');
    Route::post('/product/changeCommissioningFormId', 'ProductController@changeCommissioningFormId');
    Route::post('/product/removeTestingImg', 'ProductController@removeTestingImg');
    Route::post('/product/removeTestingVideo', 'ProductController@removeTestingVideo');
    Route::get('/product/getQrOption', 'ProductController@getQrOption');
    Route::post('/product/updateQrOption', 'ProductController@updateQrOption');
    Route::get('/product/getBarcodeApi', 'ProductController@getBarcodeApi');
    Route::post('/product/updateBarcodeApi', 'ProductController@updateBarcodeApi');
    Route::post('/product/insertBarcode', 'ProductController@insertBarcode');
    Route::post('/product/AssignProduct', 'ProductController@AssignProduct');
    Route::get('/product/getBarcodeInfo', 'ProductController@getBarcodeInfo');
    Route::post('/product/newBarcode', 'ProductController@newBarcode');
    Route::post('/product/qrScan', 'ProductController@qrScan');
    Route::get('/product/labelList', 'ProductController@labelList');
    Route::post('/product/addLabel', 'ProductController@addLabel');
    Route::post('/product/deleteLabel', 'ProductController@deleteLabel');
    Route::post('/product/setProductLabel', 'ProductController@setProductLabel');
    Route::post('/product/uploadTechPdf', 'ProductController@uploadTechPdf');
    Route::post('/product/uploadBrochuresPdf', 'ProductController@uploadBrochuresPdf');
    Route::post('/product/updateScanProduct','ProductController@updateScanProduct');
    Route::post('/product/changeWarrantyTime','ProductController@changeWarrantyTime');
    Route::post('/product/removeProductGroup','ProductController@removeProductGroup');
    Route::post('/product/makeProductGroup','ProductController@makeProductGroup');
    Route::post('/product/releaseProductGroup','ProductController@releaseProductGroup');
    Route::post('/product/madeDelivery','ProductController@madeDelivery');
    Route::post('/product/changeTopMenu','ProductController@changeTopMenu');
    Route::post('/product/addProductToGroup','ProductController@addProductToGroup');
    Route::post('/product/removeProductFromGroup','ProductController@removeProductFromGroup');

    Route::post('/room/updateRoom','RoomController@updateRoom');///
    Route::post('/room/deleteRoom','RoomController@deleteRoom');///
    Route::get('/room/roomInfo','RoomController@roomInfo');///
    Route::get('/room/editPhoto','RoomController@editPhoto');///
    Route::post('/room/signoff','RoomController@signoff');///
    Route::post('/room/finalSignOff','RoomController@finalSignOff');///
    Route::post('/room/changeRequest','RoomController@changeRequest');///
    Route::post('/room/setFavourite','RoomController@setFavourite');
    Route::post('/room/changeNotes','RoomController@changeNotes');
    Route::post('/room/changeRoomNumber','RoomController@changeRoomNumber');
    Route::post('/room/changeCeiling','RoomController@changeCeiling');
    Route::post('/room/changeWall','RoomController@changeWall');
    Route::post('/room/changeAsbestos','RoomController@changeAsbestos');
    Route::post('/room/changeInstall','RoomController@changeInstall');
    Route::post('/room/commentSubmit','RoomController@commentSubmit');
    Route::post('/room/changePhotoOutput','RoomController@changePhotoOutput');
    Route::post('/room/changePhotoPortrait','RoomController@changePhotoPortrait');
    Route::post('/room/removeComment','RoomController@removeComment');


    Route::post('/task/updateTask','TaskController@updateTask');///
    Route::post('/task/setCompleted','TaskController@setCompleted');///
    Route::get('/task/taskList','TaskController@taskList');///
    Route::get('/task/getTaskInfo','TaskController@getTaskInfo');///
    Route::post('/task/setFavourite','TaskController@setFavourite');
    Route::post('/task/commentSubmit','TaskController@commentSubmit');
    Route::get('/task/getComments','TaskController@getComments');
    Route::post('/task/saveImage','TaskController@saveImage');
    Route::get('/task/labelList', 'TaskController@labelList');
    Route::post('/task/addLabel', 'TaskController@addLabel');
    Route::post('/task/deleteLabel', 'TaskController@deleteLabel');
    Route::post('/task/setTaskLabel', 'TaskController@setTaskLabel');
    Route::post('/task/commentComplete', 'TaskController@commentComplete');
    Route::post('/task/deleteCommentUser', 'TaskController@deleteCommentUser');
    Route::post('/task/addCommentUser', 'TaskController@addCommentUser');
    Route::post('/task/setDueByDate', 'TaskController@setDueByDate');
    Route::post('/task/changeTopMenu', 'TaskController@changeTopMenu');
    Route::post('/task/modifyComment', 'TaskController@modifyComment');
    Route::post('/task/addNewBoard', 'TaskController@addNewBoard');
    Route::post('/task/addNewBucket', 'TaskController@addNewBucket');
    Route::post('/task/duplicateTask', 'TaskController@duplicateTask');
    Route::post('/task/setBucket', 'TaskController@setBucket');
    Route::post('/task/changeBucketOrder', 'TaskController@changeBucketOrder');
    Route::post('/task/changeTaskOrder', 'TaskController@changeTaskOrder');
    Route::post('/task/saveBucketName', 'TaskController@saveBucketName');
    Route::post('/task/saveBucketLock', 'TaskController@saveBucketLock');
    Route::post('/task/deleteBucket', 'TaskController@deleteBucket');
    Route::post('/task/deleteBoard', 'TaskController@deleteBoard');
    Route::post('/task/updateTaskCustomer', 'TaskController@updateTaskCustomer');
    Route::post('/task/updateTaskProject', 'TaskController@updateTaskProject');
    Route::post('/task/updateTaskRoom', 'TaskController@updateTaskRoom');
    Route::post('/task/removeComment', 'TaskController@removeComment');
    Route::post('/task/changeDeadline', 'TaskController@changeDeadline');
    Route::post('/task/addAttachFile', 'TaskController@addAttachFile');

    Route::post('/user/updateUser','UserController@CustomerUpdateUser');///
    Route::post('/user/deleteUser','UserController@DeleteUser');///
    Route::get('/user/userInfo','UserController@userInfo');///
    Route::post('/user/saveUser','UserController@saveUser');///
    Route::get('/user/totalUserlist','UserController@totalUserlist');///
    Route::post('/user/sendFeedback','UserController@sendFeedback');///
    Route::post('/user/deleteFeedback','UserController@deleteFeedback');///
    Route::get('/user/getFeedbackList','UserController@getFeedbackList');///
    Route::post('/user/setFeedback','UserController@setFeedback');///
    Route::get('/user/userOnlineStatus','UserController@userOnlineStatus');///
    Route::get('/user/getProjectTaskCount','UserController@getProjectTaskCount');///
    

    Route::post('/category/updateCategory','StickerCategoryController@updateCategory');///
    Route::post('/category/deleteCategory','StickerCategoryController@deleteCategory');///
    Route::get('/category/categoryList','StickerCategoryController@categoryList');///
    Route::get('/category/getCategoryInfo','StickerCategoryController@getCategoryInfo');///

    Route::post('/sticker/updateSticker','StickerController@updateStiker');///
    Route::post('/sticker/deleteSticker','StickerController@deleteStiker');///
    Route::get('/sticker/getStickerInfo','StickerController@getStikerInfo');///

    Route::get('/notification/getNotification','NotificationController@getNotification');
    Route::post('/notification/deleteNotification','NotificationController@deleteNotification');
    Route::get('/notification/readNotification','NotificationController@readNotification');

    Route::post('/schedule/updateSchedule','ScheduleController@updateSchedule');///
    Route::post('/schedule/deleteSchedule','ScheduleController@deleteSchedule');///
    Route::post('/schedule/changeStart','ScheduleController@changeStart');///

    Route::post('/form/saveFrom','NewFormController@saveForm');///
    Route::post('/form/saveFormPartner', 'NewFormController@saveFormPartner');
    Route::get('/form/infoForm', 'NewFormController@infoForm');///
    Route::post('/form/deleteForm', 'NewFormController@deleteForm');///
    Route::post('/form/duplicateForm', 'NewFormController@duplicateForm');///

    Route::post('/version/updateVersion','VersionControlController@updateVersion');///
    Route::post('/version/deleteVersion','VersionControlController@deleteVersion');///
    Route::post('/version/changeVersionName', 'VersionControlController@changeVersionName');
    Route::post('/version/changeVersionNumber', 'VersionControlController@changeVersionNumber');
    Route::post('/version/changeVersionTag', 'VersionControlController@changeVersionTag');
    Route::post('/version/changeVersionRoom', 'VersionControlController@changeVersionRoom');
    Route::post('/version/changeVersionDescription', 'VersionControlController@changeVersionDescription');

    Route::get('/calendar/getEvents','ProjectController@getEvents');///
    Route::post('/calendar/saveEvent','ProjectController@saveEvent');///
    Route::post('/calendar/saveNewEvent','ProjectController@saveNewEvent');///
    Route::post('/calendar/deleteEvent','ProjectController@deleteEvent');///
    Route::post('/calendar/saveEventID', 'ProjectController@saveEventID');
    Route::post('/calendar/removeEventID', 'ProjectController@removeEventID');
    Route::post('/calendar/addNewNotes', 'ProjectController@addNewNotes');

    Route::post('/chat/sendMessage','ProjectChatController@sendMessage');///
    Route::get('/chat/getChats','ProjectChatController@getChats');///
    Route::post('/chat/readMessage','ProjectChatController@readMessage');///
    Route::post('/chat/createGroup','ProjectChatController@createGroup');///
    Route::post('/chat/addUserToGroup','ProjectChatController@addUserToGroup');///
    Route::post('/chat/removeUserToGroup','ProjectChatController@removeUserToGroup');///
    Route::post('/chat/changeGroupName','ProjectChatController@changeGroupName');///

    Route::post('/page/updatePage','PageController@updatePage');///
    Route::get('/page/pageList','PageController@pageList');///
    Route::post('/page/duplicatePage','PageController@duplicatePage');///
    Route::post('/page/deletePage','PageController@deletePage');///
    Route::post('/page/uploadPdf','PageController@uploadPdf');///
    Route::get('/page/labelList', 'PageController@labelList');
    Route::post('/page/addLabel', 'PageController@addLabel');
    Route::post('/page/deleteLabel', 'PageController@deleteLabel');
    Route::post('/page/setPageLabel', 'PageController@setPageLabel');
    Route::post('/document/duplicateDocument','PageController@duplicateDocument');///
    Route::post('/document/deletedocument','PageController@deletedocument');///
    Route::get('/document/documentList','PageController@documentList');///
    Route::post('/document/updateDocument','PageController@updateDocument');///
    Route::post('/document/removeDocumentPage','PageController@removeDocumentPage');///
    Route::post('/document/changeOrderDocumentPage','PageController@changeOrderDocumentPage');///

});
