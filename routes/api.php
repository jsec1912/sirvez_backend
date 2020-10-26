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
    Route::post('/room/saveimage','RoomController@saveimage');
    Route::post('user/checkValidate', 'UserController@checkValidate');
    Route::post('companyuser/register', 'UserController@companyUserRegister');


});

Route::get('/company/getCompanyImg','CompanyController@getCompanyImg');

Route::group(['middleware' => ['jwt-auth','api-header', 'cors']], function () {

    //dashboard
    Route::get('/dashboard','CompanyCustomerController@getDashboard');////
    //company modify
    Route::post('/company/saveCompany','CompanyController@saveCompany');
    Route::get('/company/getCompanyInfo','CompanyController@getCompanyInfo');
    //customer
    Route::Post('/customers/company/customer-edit','CompanyCustomerController@addCompanyCustomer');///
    Route::Post('/customers/DeleteCompanyCustomer','CompanyCustomerController@DeleteCompanyCustomer');///
    Route::get('/customers/company','CompanyCustomerController@getCompanyCustomer');///
    Route::get('/customers/companyInfo','CompanyCustomerController@CompanyCustomerInfo');///
    Route::get('/customers/company/customer-edit','CompanyCustomerController@getCustomerInfo');///
    Route::get('/customers/userList','CompanyCustomerController@userList');///
    Route::Post('/customers/pendingUser','CompanyCustomerController@pendingUser');

    Route::post('/project/updateProject','ProjectController@updateProject');///
    Route::post('/project/deleteProject','ProjectController@deleteProject');///
    Route::get('/project/projectList','ProjectController@projectList');///
    Route::get('/project/projectInfo','ProjectController@projectDetail');///
    Route::get('/project/getprojectInfo','ProjectController@getProjectInfo');///
    Route::post('/project/setFavourite','ProjectController@setFavourite');
    Route::post('/project/deleteAssignUser','ProjectController@deleteAssignUser');
    Route::post('/project/signOff','ProjectController@signOff');
    Route::post('/project/addAssignUser','ProjectController@addAssignUser');
    Route::post('/project/changeSummary','ProjectController@changeSummary');

    Route::post('/site/updateSite','SiteController@updateSite');///
    Route::post('/site/deleteSite','SiteController@deleteSite');///
    Route::get('/site/siteList','SiteController@siteList');
    Route::get('/site/siteInfo','SiteController@siteInfo');///
    Route::get('/site/getSiteInfo','SiteController@getSiteInfo');
    Route::post('/siteroom/updateRoom','SiteController@updateRoom');
    Route::post('/siteroom/deleteRoom','SiteController@deleteRoom');
    Route::get('/siteroom/roomInfo','SiteController@roomInfo');

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

    Route::post('/room/updateRoom','RoomController@updateRoom');///
    Route::post('/room/deleteRoom','RoomController@deleteRoom');///
    Route::get('/room/roomInfo','RoomController@roomInfo');///
    Route::get('/room/editPhoto','RoomController@editPhoto');///
    Route::post('/room/signoff','RoomController@signoff');///
    Route::post('/room/setFavourite','RoomController@setFavourite');
    Route::post('/room/changeNotes','RoomController@changeNotes');


    Route::post('/task/updateTask','TaskController@updateTask');///
    Route::post('/task/setCompleted','TaskController@setCompleted');///
    Route::get('/task/taskList','TaskController@taskList');///
    Route::get('/task/getTaskInfo','TaskController@getTaskInfo');///
    Route::post('/task/setFavourite','TaskController@setFavourite');
    Route::post('/task/commentSubmit','TaskController@commentSubmit');
    Route::get('/task/getComments','TaskController@getComments');

    Route::post('/user/updateUser','UserController@CustomerUpdateUser');///
    Route::post('/user/deleteUser','UserController@DeleteUser');///
    Route::get('/user/userInfo','UserController@userInfo');///
    Route::post('/user/saveUser','UserController@saveUser');///
    Route::get('/user/totalUserlist','UserController@totalUserlist');///

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




    // Route::get('/customer/list', 'CompanyController@Companylist');
    // Route::get('/customer/info', 'CompanyController@Companyinfo');
    // Route::post('/customer/UpdateCustomer', 'CompanyController@UpdateCustomer');
    // Route::post('/customer/deleteCompany', 'CompanyController@deleteCompany');

    // Route::get('/project/ProjectInfo','ProjectController@ProjectInfo');
    // Route::post('/project/addProject','ProjectController@AddProject');
    // Route::post('/project/updateProject','ProjectController@updateProject');
    // Route::post('/project/deleteProject','ProjectController@DeleteProject');

    // Route::get('/site/SiteInfo','SiteController@SiteInfo');
    // Route::post('/site/addSite','SiteController@AddSite');
    // Route::post('/site/updateSite','SiteController@updateSite');
    // Route::post('/site/deleteSite','SiteController@DeleteSite');

    // Route::get('/room/RoomInfo','RoomController@RoomInfo');
    // Route::post('/room/addRoom','RoomController@AddRoom');
    // Route::post('/room/updateRoom','RoomController@updateRoom');
    // Route::post('/room/deleteRoom','RoomController@DeleteRoom');

    // Route::post('/room/updateImg','RoomController@UpdateImg');
    // Route::post('/room/DeleteImg','RoomController@DeleteImg');

    // Route::post('/stiker/InsertStiker','StikerController@InsertStiker');
    // Route::post('/stiker/DeleteStiker','StikerController@DeleteStiker');
    // Route::post('/stiker/UpdateStiker','StikerController@UpdateStiker');

    // Route::get('/product/ProductInfo','ProductController@ProductInfo');
    // Route::post('/product/addProduct','ProductController@addProduct');
    // Route::post('/product/updateProduct','ProductController@updateProduct');
    // Route::post('/product/deleteProduct','ProductController@deleteProduct');

    // Route::get('/task/TaskInfo','TaskController@TaskInfo');
    // Route::post('/task/addTask','TaskController@addTask');
    // Route::post('/task/updateTask','TaskController@updateTask');
    // Route::post('/task/deleteTask','TaskController@deleteTask');

});
