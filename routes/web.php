<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::get('/test', function() {
    if(Auth::check()) {
        echo 'login';
    } else echo 'not login';
});

Route::middleware('auth:web')->group(function () {
	Route::get('/home', 'HomeController@index')->name('home');
	Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
//	Route::get('/master/users', 'Master\UsersController@index')->name('users');
	//
	Route::get('/','DashboardController@show');
    Route::get('/home', 'DashboardController@show')->name('home');
    Route::get('/home/getrange/{per}/{segment?}/{start?}/{end?}','HomeController@getRange');
    Route::get('/dashboard', 'DashboardController@show')->name('dashboard');
    Route::get('/dashboard/{type}', 'DashboardController@get');
    //Please do not remove this if you want adminlte:route and adminlte:link commands to works correctly.
    #adminlte_routes

    Route::get('/customerservice/request','CustomerService\RequestCSController@show')->name('requestcs');
    Route::post('/customerservice/request','CustomerService\RequestCSController@receive');
    Route::get('/customerservice/task','CustomerService\TaskCSController@show')->name('taskcs');
    Route::get('/customerservice/task/update/{id}','CustomerService\TaskCSController@showformupdate');
    Route::post('/customerservice/task/update/{id}','CustomerService\TaskCSController@updatestatus');
    Route::get('/customerservice/task/showdetail/{id}','CustomerService\TaskCSController@showdetail');
    Route::get('/customerservice/task/download/{id}','CustomerService\TaskCSController@download');

    Route::get('/customerservice/taskcomplete','CustomerService\TaskCSCompleteController@show')->name('taskcscomplete');
    Route::get('/customerservice/taskcomplete/showdetail/{id}','CustomerService\TaskCSCompleteController@showdetail');
    Route::get('/customerservice/taskcomplete/download/{id}','CustomerService\TaskCSCompleteController@download');


    Route::group(['prefix'=>'profile'], function(){
        Route::get('/changepassword','Profile\ChangePasswordController@show')->name('changepassword');
        Route::post('/changepassword','Profile\ChangePasswordController@postCredentials');
        Route::get('/profile', 'HomeController@index')->name('profile');
    });

    Route::group(['prefix'=>'master'], function(){
        // masters
        Route::get('/usermenu', 'Master\UserMenuController@index')->name('usermenu');
        Route::get('/users','Master\UsersController@show')->name('users');
        Route::get('/users/menu/{id}','Master\UsersController@showmenuform');
        // new action
        Route::post('/users/save/{id}', 'Master\UsersController@save');
        Route::post('/users/add', 'Master\UsersController@add');
        Route::post('/users/get/{id}', 'Master\UsersController@get');
        Route::post('/users/delete/{id}', 'Master\UsersController@delete');
        Route::post('/users/getmenu/{id}', 'Master\UsersController@getmenu');
        Route::post('/users/replacemenu/{id}', 'Master\UsersController@replacemenu');
        Route::get('/group','Master\GroupController@show')->name('profilegroup');
        Route::get('/customers', 'Master\CustomersController@index')->name('mastercustomer');
        Route::post('/customers/save/{id}', 'Master\CustomersController@save');
        Route::post('/customers/add', 'Master\CustomersController@add');
        Route::post('/customers/get/{id}', 'Master\CustomersController@get');
        Route::post('/customers/delete/{id}', 'Master\CustomersController@delete');
        Route::get('/projects', 'Master\ProjectsController@index')->name('masterproject');
        Route::get('/projects/add', 'Master\ProjectsController@add');
        Route::post('/projects/save/{id}', 'Master\ProjectsController@save');
        Route::post('/projects/add', 'Master\ProjectsController@add');
        Route::post('/projects/get/{id}', 'Master\ProjectsController@get');
        Route::post('/projects/delete/{id}', 'Master\ProjectsController@remove');
        Route::post('/projects/gettask/{id}', 'Master\ProjectsController@gettask');
        Route::post('/projects/savetask/{id}', 'Master\ProjectsController@savetask');
        Route::post('/projects/getcomponent/{id}', 'Master\ProjectsController@getcomponent');
        Route::post('/projects/savecomponent/{id}', 'Master\ProjectsController@savecomponent');
        Route::get('/taskstatus', 'Master\TaskStatusController@index')->name('mastertaskstatus');
        Route::post('/taskstatus/save/{id}', 'Master\TaskStatusController@save');
        Route::post('/taskstatus/add', 'Master\TaskStatusController@add');
        Route::post('/taskstatus/get/{id}', 'Master\TaskStatusController@get');
        Route::post('/taskstatus/delete/{id}', 'Master\TaskStatusController@delete');
        Route::post('/taskstatus/getresult/{id}', 'Master\TaskStatusController@getresult');
        Route::post('/taskstatus/saveresult/{id}', 'Master\TaskStatusController@saveresult');
        Route::get('/taskresult', 'Master\TaskResultController@index')->name('mastertaskresult');
        Route::post('/taskresult/save/{id}', 'Master\TaskResultController@save');
        Route::post('/taskresult/add', 'Master\TaskResultController@add');
        Route::post('/taskresult/get/{id}', 'Master\TaskResultController@get');
        Route::post('/taskresult/delete/{id}', 'Master\TaskResultController@delete');
        Route::get('/component', 'Master\ComponentController@index')->name('mastercomponent');
        Route::post('/component/save/{id}', 'Master\ComponentController@save');
        Route::post('/component/add', 'Master\ComponentController@add');
        Route::post('/component/get/{id}', 'Master\ComponentController@get');
        Route::post('/component/delete/{id}', 'Master\ComponentController@delete');    
    });

    Route::group(['prefix'=>'request'], function(){
        Route::get('/upload', 'Request\UploadController@index')->name('requestupload');
        Route::post('/upload', 'Request\UploadController@upload');    
        Route::get('/incoming', 'Request\IncomingController@index')->name('requestincomingdata');
        Route::post('/incoming/detail/{id}', 'Request\IncomingController@showdetail');        
        Route::get('/download', 'Request\DownloadController@index')->name('requestdownload');
        Route::get('/download/get/{id}', 'Request\DownloadController@download');
        Route::get('/uploadapproval', 'Request\UploadApprovalController@index')->name('requestuploadapproval');
        Route::post('/uploadapproval', 'Request\UploadApprovalController@upload');    
        Route::get('/approval', 'Request\ApprovalController@index')->name('requestapproval');
        Route::post('/approval', 'Request\ApprovalController@update');
        Route::get('/download/app/{id}', 'Request\ApprovalController@downloadapp')->name('downloadapp');    
        Route::get('/uploadjoblist', 'Request\UploadJobListController@index')->name('requestuploadjoblist');
        Route::post('/uploadjoblist', 'Request\UploadJobListController@upload');
        Route::get('/download/att/{id}', 'Request\UploadJobListController@downloadatt')->name('downloadatt');
    });
    
    Route::group(['prefix'=>'production'], function(){
        Route::get('/joblist', 'Production\JobListController@index')->name('prodjoblist');
        Route::get('/joblist/selectproject', 'Production\JobListController@selectproject');
        Route::post('/joblist', 'Production\JobListController@upload');
        Route::post('/joblist/detail/{id}', 'Production\JobListController@detail')->name('joblistdetail');
        Route::get('/joblist/download/{id}', 'Production\JobListController@download');
        Route::get('/approval', 'Production\PrintingController@index')->name('prodapproval');
        Route::get('/printing', 'Production\PrintingController@index')->name('prodprinting');
        Route::post('/printing', 'Production\PrintingController@update');
        Route::get('/printing/detail/{id}', 'Production\PrintingController@showdetail');
        Route::get('/printing/material/{id}', 'Production\PrintingController@printmaterial');    
        Route::get('/balancing', 'Production\BalancingController@index')->name('prodbalancing');
        Route::post('/balancing', 'Production\BalancingController@update');
        Route::get('/balancing/detail/{id}', 'Production\BalancingController@showdetail');    
        Route::get('/distribusi', 'Production\DistribusiController@index')->name('proddistribusi');
        Route::get('/distribusi/form/{no_manifest}', 'Production\DistribusiController@showform');
        Route::post('/distribusi/update', 'Production\DistribusiController@update');
        Route::post('/distribusi/detail/{no_manifest}', 'Production\DistribusiController@detail');
        Route::get('/distribusi/print/{no_manifest}', 'Production\DistribusiController@print');
        Route::get('/distribusi/download/{no_manifest}', 'Production\DistribusiController@download');
    });

    Route::group(['prefix'=>'gudang'], function(){
        Route::get('/approval','Gudang\ApprovalController@index')->name('approvalmaterial');
        Route::post('/approval','Gudang\ApprovalController@update')->name('updateappmaterial');
        Route::get('/inpo','Gudang\ListIncPOController@index')->name('incomingpo');
        Route::post('/upload','PO\POMaterialController@upload')->name('uploadincomingmaterial');
        Route::get('/detail/{nopo}','Gudang\ListIncPOController@detail')->name('detailincpo');   
        Route::post('/saveupdate','Gudang\ListIncPOController@UpdateIncPO')->name('saveupdate'); 
        Route::get('/createoutmaterial','Gudang\OutgoingMaterialController@index')->name('createoutmaterial');    
        Route::get('/nojob/{project_id}','Gudang\OutgoingMaterialController@generateNOPO')->name('generatenojob');    
        Route::post('/saveoutmaterial','Gudang\OutgoingMaterialController@upload')->name('saveout');   
        Route::post('/delete/{id}','Gudang\OutgoingMaterialController@delete')->name('deletedetailout'); 
        Route::post('/batal/{id}','Gudang\OutgoingMaterialController@batal')->name('batalout'); 
        Route::get('/listoutmaterial','Gudang\ListOutgoingController@index')->name('listoutmaterial'); 
        Route::get('/download/{id}','Gudang\ListIncPOController@downloadbukti')->name('downbukti');
        Route::post('/detail/{nojob}','Gudang\ListOutgoingController@detail')->name('lisgodetail'); 
        Route::post('/listdetail/{nopo}','Gudang\ListIncPOController@listdetail')->name('listdetail'); 
        
        Route::get('/so','Gudang\StockController@index')->name('stockopname');
        Route::post('/so','Gudang\StockController@index')->name('stockopname');
        Route::get('/liststock','Gudang\StockController@liststok')->name('liststock');
    });

    Route::group(['prefix'=>'po'], function(){
        Route::get('/createpo','PO\POMaterialController@index')->name('createpomaterial');
        Route::post('/upload','PO\POMaterialController@upload')->name('savepomaterial');
        Route::get('/nopo','PO\POMaterialController@generateNOPO')->name('generatenopo');
        Route::post('/delete/{id}','PO\POMaterialController@delete')->name('deletedetailpo');
        Route::get('/printpo/{nopo}','PO\ListPOController@printPO');
        Route::get('/listpomat','PO\ListPOController@index')->name('listpomaterial');
        Route::post('/delete/{id}','PO\ListPOController@delete');
        Route::post('/detail/{id}','PO\ListPOController@detail')->name('podetail');
    });

    Route::group(['prefix'=>'adm'], function(){
        Route::get('/generate-invoice','Adm\GenController@index')->name('geninv');
        Route::get('/list-invoice','Adm\ListInvController@index')->name('listinv');
        Route::get('/noinv','Adm\GenController@generateNOINV')->name('generatenoiv');
        Route::post('/save-invoice','Adm\GenController@saveinvoice')->name('saveinvoice');
        Route::get('/preview','Adm\GenController@preview');
        Route::get('/perincian','Adm\GenController@perincian');
    });

    Route::get('/report/detail', 'Report\DetailController@index')->name('reportdetail');
    Route::get('/report/detail/detail/{id}', 'Report\DetailController@showdetail');

    Route::get('/report/summary', 'Report\SummaryController@index')->name('reportsummary');

    Route::get('/report/distribusi', 'Report\DistribusiController@index')->name('reportdistribusi');

    Route::get('/report/material', 'Report\MaterialController@index')->name('reportmaterial');

    Route::get('/check/{project_id}', 'Controller@test');

    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register');

    Route::get('/util/getusers', 'Utility\ChatController@getusers');

    Route::get('setting/master/project/getJsonByCustomer/{customer_id}', 'Master\ProjectsController@getByCustomer');

    Route::get('logout', 'Auth\LoginController@logout');
	//
});


    //    Route::get('/master/customers/showdetail/{id}', 'Master\CustomersController@detail');
    //    Route::get('/master/customers/edit/{id}', 'Master\CustomersController@edit');
    //    Route::get('/master/customers/add', 'Master\CustomersController@add');
    //    Route::post('/master/customers/add', 'Master\CustomersController@save');
    //    Route::get('/master/customers/edit/{id}', 'Master\CustomersController@edit');
    //    Route::post('/master/customers/edit/{id}', 'Master\CustomersController@update');

    //    Route::post('/master/projects/add', 'Master\ProjectsController@save');
    /*    Route::get('/master/projects/showdetail/{id}', 'Master\ProjectsController@detail');
        Route::get('/master/projects/edit/{id}', 'Master\ProjectsController@edit');
        Route::get('/master/projects/task/{id}', 'Master\ProjectsController@showformtask');
        Route::post('/master/projects/task/{id}', 'Master\ProjectsController@replacetask');
        Route::get('/master/projects/component/{id}', 'Master\ProjectsController@showformcomponent');
        Route::post('/master/projects/component/{id}', 'Master\ProjectsController@replacecomponent');
    */        
            /*    Route::get('/setting/master/component/add', 'Master\ComponentController@add');
        Route::post('/setting/master/component/add', 'Master\ComponentController@save');
        Route::get('/setting/master/component/showdetail/{id}', 'Master\ComponentController@detail');
        Route::get('/setting/master/component/edit/{id}', 'Master\ComponentController@edit');
        Route::post('/setting/master/component/edit/{id}', 'Master\ComponentController@update');

    */
    /*    Route::get('/master/taskresult/add', 'Master\TaskResultController@add');
        Route::post('/master/taskresult/add', 'Master\TaskResultController@save');
        Route::get('/master/taskresult/showdetail/{id}', 'Master\TaskResultController@detail');
        Route::get('/master/taskresult/edit/{id}', 'Master\TaskResultController@edit');
        Route::get('/master/taskresult/result/{id}', 'Master\TaskResultController@showformresult');
    */    
    /*    Route::get('/master/taskstatus/add', 'Master\TaskStatusController@add');
        Route::post('/master/taskstatus/add', 'Master\TaskStatusController@save');
        Route::get('/master/taskstatus/showdetail/{id}', 'Master\TaskStatusController@detail');
        Route::get('/master/taskstatus/edit/{id}', 'Master\TaskStatusController@edit');
        Route::post('/master/taskstatus/edit/{id}', 'Master\TaskStatusController@update');
        Route::get('/master/taskstatus/result/{id}', 'Master\TaskStatusController@showformresult');
        Route::post('/master/taskstatus/result/{id}', 'Master\TaskStatusController@replaceresult');
    */   
    //    Route::post('/master/users/menu/{id}','Master\UsersController@replacemenu');
    //    Route::get('/master/users/info/{id}','Master\UsersController@showformInfo');
    //    Route::post('/master/users/info/{id}','Master\UsersController@replaceInfo');

    //    Route::get('/master/users/add','Master\UsersController@showform');
    //    Route::post('/master/users/add','Master\UsersController@save'); 