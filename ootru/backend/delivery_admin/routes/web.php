<?php

use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ClaimsController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DeliverymanController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ExtraChargeController;
use App\Http\Controllers\StaticDataController;
use App\Http\Controllers\ClientReviewController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DeliveryManDocumentController;
use App\Http\Controllers\DeliveryPartnerController;
use App\Http\Controllers\Frontendwebsite\FronthomeController;
use App\Http\Controllers\WalkThroughController;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WhyDeliveryController;
use App\Http\Controllers\WithdrawRequestController;
use App\Http\Controllers\CourierCompaniesController;
use App\Http\Controllers\CustomerSupportController;
use App\Http\Controllers\DefaultkeywordController;
use App\Http\Controllers\EmergencyController;
use App\Http\Controllers\LanguageListController;
use App\Http\Controllers\LanguageWithKeywordListController;
use App\Http\Controllers\OrderMailController;
use App\Http\Controllers\OrderSMSController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RestApiController;
use App\Http\Controllers\ScreenController;
use App\Http\Controllers\SubAdminController;
use App\Http\Controllers\ClientTestimonialController;
use App\Http\Controllers\DeliveryManSectionController;
use App\Http\Controllers\DeliverYourWayController;
use App\Http\Controllers\DocumentVerificationController;
use App\Http\Controllers\SupportchatHistoryController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\WebSiteSectionController;

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

require __DIR__ . '/auth.php';

use Illuminate\Support\Facades\Schema;
Route::get('migrate', function () {
    try {
        // check user table exist or not
        $schema = Schema::hasTable('users');
        // Run migrations
        Artisan::call('migrate', ['--force' => true]);

        // if users table not exit than run seeder command
        if( !$schema ) {
            // Run seeders
            Artisan::call('db:seed', ['--force' => true]);
        }

        return redirect()->route('frontend-section');
    } catch (\Exception $e) {
        return 'Migration failed: ' . $e->getMessage();
    }
})->withoutMiddleware(['web']);

Route::get('storage-link', function () {
    try {
        Artisan::call('storage:link', ['--force' => true]);
        return 'Storage Link successfully';
    } catch (\Exception $e) {
        return 'Storage Link failed: ' . $e->getMessage();
    }
});

// Route to run optimize
Route::get('optimize', function () {
    Artisan::call('optimize:clear');
    return 'Optimization completed';
});

Route::get('logs/{date}', function ($date) {
    $logPath = storage_path('logs/laravel-' . $date . '.log');
    return response()->file($logPath);
});
//Auth pages Routs
Route::group(['prefix' => 'auth'], function () {
    Route::get('login', [HomeController::class, 'authLogin'])->name('auth.login');
    Route::get('register', [HomeController::class, 'authRegister'])->name('auth.register');
    Route::get('recover-password', [HomeController::class, 'authRecoverPassword'])->name('auth.recover-password');
    Route::get('confirm-email', [HomeController::class, 'authConfirmEmail'])->name('auth.confirm-email');
    Route::get('lock-screen', [HomeController::class, 'authlockScreen'])->name('auth.lock-screen');
});

Route::get('api-invoice/{id}', [OrderController::class, 'ApiInvoicePdf'])->name('api-order-invoice');
Route::get('language/{locale}', [HomeController::class, 'changeLanguage'])->name('change.language');
Route::group(['middleware' => ['auth', 'verified', 'assign_user_role']], function () {
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/high_demanding_areas', [HomeController::class, 'highDemanding_areas'])->name('high_demanding_areas');
  

    Route::group(['namespace' => ''], function () {
        Route::resource('permission', PermissionController::class);
        Route::get('permission/add/{type}', [PermissionController::class, 'addPermission'])->name('permission.add');
        Route::post('permission/save', [PermissionController::class, 'savePermission'])->name('permission.save');
    });

    Route::resource('role', RoleController::class);

    Route::get('changeStatus', [HomeController::class, 'changeStatus'])->name('changeStatus');
    Route::get('changeVerify', [HomeController::class, 'changeVerify'])->name('changeVerify');

    Route::get('setting/{page?}', [SettingController::class, 'settings'])->name('setting.index');
    Route::post('/layout-page', [SettingController::class, 'layoutPage'])->name('layout_page');
    Route::post('settings/save', [SettingController::class, 'settingsUpdates'])->name('settingsUpdates');
    Route::post('mobile-config-save', [SettingController::class, 'settingUpdate'])->name('settingUpdate');
    Route::post('payment-settings/save', [SettingController::class, 'paymentSettingsUpdate'])->name('paymentSettingsUpdate');
    Route::post('sms-settings/save', [SettingController::class, 'smsSettingsUpdate'])->name('smsSettingsUpdate');

    Route::post('orders-setting-save', [SettingController::class, 'updateAppSetting'])->name('order-setting-save');

    Route::post('get-lang-file', [LanguageController::class, 'getFile'])->name('getLanguageFile');
    Route::post('save-lang-file', [LanguageController::class, 'saveFileContent'])->name('saveLangContent');

    Route::get('pages/term-condition', [SettingController::class, 'termAndCondition'])->name('term-condition');
    Route::post('term-condition-save', [SettingController::class, 'saveTermAndCondition'])->name('term-condition-save');

    Route::get('pages/invoice-setting', [SettingController::class, 'invoiceSetting'])->name('invoice-setting');
    Route::post('setting-save', [SettingController::class, 'saveInvoiceSetting'])->name('setting-save');

    Route::get('pages/privacy-policy', [SettingController::class, 'privacyPolicy'])->name('privacy-policy');
    Route::post('privacy-policy-save', [SettingController::class, 'savePrivacyPolicy'])->name('privacy-policy-save');

    Route::post('env-setting', [SettingController::class, 'envChanges'])->name('envSetting');
    Route::post('update-profile', [SettingController::class, 'updateProfile'])->name('updateProfile');
    Route::post('change-password', [SettingController::class, 'changePassword'])->name('changePassword');
    Route::post('change-email', [SettingController::class, 'changeEmail'])->name('changeEmail');
    Route::post('change-email-otp-verify', [SettingController::class, 'changeEmail_otpVerify'])->name('changeEmail_otpVerify');

    Route::get('notification-list', [NotificationController::class, 'notificationList'])->name('notification.list');
    Route::get('notification-counts', [NotificationController::class, 'notificationCounts'])->name('notification.counts');
    Route::get('notification', [NotificationController::class, 'index'])->name('notification.index');

    Route::post('remove-file', [HomeController::class, 'removeFile'])->name('remove.file');


    Route::resource('country', CountryController::class);
    Route::delete('country-force-delete/{id?}', [CountryController::class, 'action'])->name('country.force.delete');
    Route::get('country-restore/{id?}', [CountryController::class, 'action'])->name('country.restore');

    Route::delete('datatble/destroySelected', [HomeController::class, 'destroySelected'])->name('datatble.destroySelected');

    Route::resource('city', CityController::class);
    Route::delete('city-force-delete/{id?}', [CityController::class, 'action'])->name('city.force.delete');
    Route::get('city-restore/{id?}', [CityController::class, 'action'])->name('city.restore');

    Route::resource('vehicle', VehicleController::class);
    Route::delete('vehicle-force-delete/{id?}', [VehicleController::class, 'action'])->name('vehicle.force.delete');
    Route::get('vehicle-restore/{id?}', [VehicleController::class, 'action'])->name('vehicle.restore');

    Route::resource('extracharge', ExtraChargeController::class);
    Route::delete('extracharge-force-delete/{id?}', [ExtraChargeController::class, 'action'])->name('extracharge.force.delete');
    Route::get('extracharge-restore/{id?}', [ExtraChargeController::class, 'action'])->name('extracharge.restore');

    Route::resource('staticdata', StaticDataController::class);

    Route::resource('users', ClientController::class);
    Route::get('users-view/{id?}', [ClientController::class, 'show'])->name('users-view.show');
    Route::get('users-edit/{id?}', [ClientController::class, 'edit'])->name('users-edit.edit');
    Route::get('users-restore/{id?}', [ClientController::class, 'action'])->name('users.restore');
    Route::delete('users-force-delete/{id?}', [ClientController::class, 'action'])->name('users.force.delete');
    Route::get('user/list/{status?}', [ClientController::class, 'index'])->name('user.status');

    // sub admin
    Route::resource('sub-admin', SubAdminController::class);
    Route::get('sub-admin-restore/{id?}', [SubAdminController::class, 'action'])->name('sub-admin.restore');
    Route::delete('sub-admin-force-delete/{id?}', [SubAdminController::class, 'action'])->name('sub-admin.force.delete');

    Route::resource('deliveryman', DeliverymanController::class);
    Route::get('deliveryman-view/{id?}', [DeliverymanController::class, 'show'])->name('deliveryman-view.show');
    Route::get('deliverymean-restore/{id?}', [DeliverymanController::class, 'action'])->name('deliveryman.restore');
    Route::delete('deliveryman-force-delete/{id?}', [DeliverymanController::class, 'action'])->name('deliveryman.force.delete');

    Route::resource('document', DocumentController::class);
    Route::get('document-restore/{id?}', [DocumentController::class, 'action'])->name('document.restore');
    Route::delete('document-force-delete/{id?}', [DocumentController::class, 'action'])->name('document.force.delete');

    Route::resource('order', OrderController::class);
    Route::get('order-view/{id?}', [OrderController::class, 'show'])->name('order-view.show');
    Route::get('order-restore/{id?}', [OrderController::class, 'action'])->name('order.restore');
    Route::delete('order-force-delete/{id?}', [OrderController::class, 'action'])->name('order.force.delete');
    Route::get('assign/{id?}', [OrderController::class, 'assign'])->name('order-assign');
    Route::post('order-assign/{id?}', [OrderController::class, 'action'])->name('order.assign');
    Route::get('filter-order', [OrderController::class, 'filterOrder'])->name('filter.order.data');


    Route::get('deliveryman-location', [HomeController::class, 'deliverymanlocation'])->name('deliveryman-location');
    Route::get('ordermap', [HomeController::class, 'ordermaplocation'])->name('ordermap');

    Route::get('invoice/{id}', [OrderController::class, 'InvoicePdf'])->name('order-invoice');
    Route::get('previousinvoice', [SettingController::class, 'previousInvoice'])->name('previousinvoice');

    Route::resource('pushnotification', PushNotificationController::class);
    Route::get('resend-pushnotification/{id}', [PushNotificationController::class, 'edit'])->name('resend.pushnotification');

    Route::post('order-auto-assign', [App\Http\Controllers\OrderController::class, 'autoAssignCancelOrder']);

    Route::resource('deliverymandocument', DeliveryManDocumentController::class);
    Route::post('/deliverymandocument/{id}', [DeliveryManDocumentController::class, 'store'])->name('deliverymandocument.action');
    Route::get('deliverymandocument-restore/{id?}', [DocumentController::class, 'action'])->name('deliverymandocument.restore');
    Route::delete('deliverymandocument-force-delete/{id?}', [DocumentController::class, 'action'])->name('deliverymandocument.force.delete');
    Route::get('deliveryman/list/{status?}', [DeliverymanController::class, 'index'])->name('deliveryman.pending');

    Route::post('save-wallet/{user_id}', [HomeController::class, 'saveWalletHistory'])->name('savewallet-save');

    Route::get('website-section/{type}', [FronthomeController::class, 'websiteSettingForm'])->name('frontend.website.form');
    Route::post('update-website-information/{type}', [FronthomeController::class, 'websiteSettingUpdate'])->name('frontend.website.information.update');

    Route::resource('withdrawrequest', WithdrawRequestController::class);
    Route::any('approved-withdrawrequest', [App\Http\Controllers\API\WithdrawRequestController::class, 'approvedWithdrawRequest'])->name('approvedWithdrawRequest');
    Route::any('decline-withdrawrequest', [App\Http\Controllers\API\WithdrawRequestController::class, 'declineWithdrawRequest'])->name('declineWithdrawRequest');
    Route::get('withdraw-history/{id}', [WithdrawRequestController::class, 'withdrawhestory'])->name('withdraw-history');
    Route::get('withdraw-history-edit/{id}', [WithdrawRequestController::class, 'withdrawdetailedit'])->name('withdraw-history-edit');
    Route::post('withdraw-deatils', [WithdrawRequestController::class, 'withdrawdetailstore'])->name('withdraw-deatils');

    Route::resource('whydelivery', WhyDeliveryController::class);
    Route::resource('document-verification', DocumentVerificationController::class);
    Route::resource('deliver-your-way', DeliverYourWayController::class);
    Route::get('delete/{id}', [WhyDeliveryController::class, 'destroy'])->name('website_section-delete');
    Route::resource('clientreview', ClientReviewController::class);
    Route::resource('deliverypartner', DeliveryPartnerController::class);
    Route::resource('client-testimonial', ClientTestimonialController::class);
    Route::resource('app-overview',WebSiteSectionController::class);
    Route::resource('delivery-man-section',DeliveryManSectionController::class);
    Route::resource('walkthrough', WalkThroughController::class);

    Route::post('setting-upload-invoice-image', [SettingController::class, 'settingUploadInvoiceImage'])->name('image-save');
    Route::get('clientwallet', [HomeController::class, 'clientwallet'])->name('clientwallet');
    Route::get('draft-order', [OrderController::class, 'draftOrder'])->name('draft-order');
    Route::post('banks-deatils-save', [HomeController::class, 'bankDetailSave'])->name('banks-deatils-save');
    Route::post('passwordchnage', [HomeController::class, 'changePassword'])->name('passwordchnage');
    Route::get('passwordpage', [HomeController::class, 'changePasswordPage'])->name('passwordpage');
    Route::get('bankdeatils', [HomeController::class, 'bankDetails'])->name('bankdeatils');
    Route::get('appsetting', [HomeController::class, 'appsetting'])->name('appsetting');
    Route::resource('useraddress', UserAddressController::class);
    Route::get('clientwithdraw', [WithdrawRequestController::class, 'clientwithdraw'])->name('clientwithdraw');
    Route::get('client-addreses', [HomeController::class, 'bankDetails'])->name('client-addreses');

    Route::get('shipped-order', [OrderController::class, 'shippedOrder'])->name('shipped-order');
    Route::get('client-order', [OrderController::class, 'clientOrderdatatable'])->name('client-order');


    Route::resource('screen', ScreenController::class);
    Route::resource('defaultkeyword', DefaultkeywordController::class);
    Route::resource('languagelist', LanguageListController::class);
    Route::resource('languagewithkeyword', LanguageWithKeywordListController::class);
    Route::get('download-language-with-keyword-list', [LanguageWithKeywordListController::class, 'downloadLanguageWithKeywordList'])->name('download.language.with,keyword.list');

    Route::post('import-language-keyword', [LanguageWithKeywordListController::class, 'importlanguagewithkeyword'])->name('import.languagewithkeyword');
    Route::get('bulklanguagedata', [LanguageWithKeywordListController::class, 'bulklanguagedata'])->name('bulk.language.data');
    Route::get('help', [LanguageWithKeywordListController::class, 'help'])->name('help');
    Route::get('download-template', [LanguageWithKeywordListController::class, 'downloadtemplate'])->name('download.template');


    // Report Route all
    Route::resource('report', ReportController::class);
    Route::get('admin-earning-report', [ReportController::class, 'adminEarning'])->name('report-adminEarning');
    Route::get('deliveryman-earning-report', [ReportController::class, 'deliverymanEarning'])->name('report-deliverymanEarning');
    Route::get('report-of-deliveryman', [ReportController::class, 'reportOfDeliveryman'])->name('report-of-deliveryman');
    Route::get('report-of-user', [ReportController::class, 'reportOfuser'])->name('report-of-user');
    Route::get('report-of-city', [ReportController::class, 'reportOfCity'])->name('report-of-city');
    Route::get('report-of-country', [ReportController::class, 'reportOfCountry'])->name('report-of-country');
    Route::get('order-of-report', [ReportController::class, 'orderreport'])->name('order-of-report');

    // Report Excel Route
    Route::get('download-adminearning/{file_type}', [ReportController::class, 'downloadAdminEarning'])->name('download-adminearning');
    Route::get('download-deliveryman-earning/{file_type}', [ReportController::class, 'downloadDeliverymanEarning'])->name('download-deliveryman-earning');
    Route::get('report-of-deliverymanexcel/{file_type}', [ReportController::class, 'reportOfDeliverymanExcel'])->name('report-of-deliverymanexcel');
    Route::get('report-of-userexcel/{file_type}', [ReportController::class, 'reportOfuserExcel'])->name('report-of-userexcel');
    Route::get('report-of-cityexcel/{file_type}', [ReportController::class, 'reportOfcityExcel'])->name('report-of-cityexcel');
    Route::get('report-of-countryexcel/{file_type}', [ReportController::class, 'reportOfcountryExcel'])->name('report-of-countryexcel');
    Route::get('report-of-orderexcel/{file_type}', [ReportController::class, 'downloadOrderExcel'])->name('report-of-orderexcel');
    // Route::get('download-admin-earning', [ReportController::class, 'downloadAdminEarning'])->name('download-admin-earning');

    //Report Pdf Route
    Route::get('download-adminearningpdf', [ReportController::class, 'downloadAdminEarningPdf'])->name('download-adminearningpdf');
    Route::get('download-deliverymanearningpdf', [ReportController::class, 'downloadDeliverymanEarningPdf'])->name('download-deliverymanearningpdf');
    Route::get('report-of-deliverymanpdf', [ReportController::class, 'reportOfDeliverymanPdf'])->name('report-of-deliverymanpdf');
    Route::get('report-of-userpdf', [ReportController::class, 'reportOfuserPdf'])->name('report-of-userpdf');
    Route::get('report_of_citypdf', [ReportController::class, 'reportOfcityPdf'])->name('report_of_citypdf');
    Route::get('report-of-countrypdf', [ReportController::class, 'reportOfcountryPdf'])->name('report-of-countrypdf');
    Route::get('report-of-orderpdf', [ReportController::class, 'downloadOrderReportPdf'])->name('report-of-orderpdf');


    // Route::get('adminearning-export-model',[ReportController::class, 'adminEarningExportmodel'])->name('adminearning-exportmodel');
    // Route::get('report-of-admin-earningpdf', [ReportController::class, 'downloadAdminEarningPdf'])->name('download-adminearningpdf');



    //withdarwrequest pdf & excel
    Route::get('withdraw-requestexcel/{file_type}', [WithdrawRequestController::class, 'withdrawExcel'])->name('download-withdrawexcel');
    Route::get('withdraw-requestexcelmodel', [WithdrawRequestController::class, 'withdrawrequestmodel'])->name('withdraw-requestexcelmodel');
    Route::get('withdraw-requeste-approvedxcel/{file_type}', [WithdrawRequestController::class, 'withdrawApprovedExcel'])->name('download-withdrawapprovedexcel');
    Route::get('withdraw-approvedexcelmodel', [WithdrawRequestController::class, 'withdrawapprovedmodel'])->name('withdraw-approvedexcelmodel');


    Route::resource('couriercompanies', CourierCompaniesController::class);

    Route::get('filter-dashboard', [HomeController::class, 'dashboardfilter'])->name('dashboard.filter.data');

    //pages
    Route::resource('pages', PagesController::class);
    Route::get('pages-edit/{id?}', [PagesController::class, 'edit'])->name('Pages-edit.edit');

    //customer support
    Route::resource('customersupport', CustomerSupportController::class);
    Route::resource('supportchathistory', SupportchatHistoryController::class);
    Route::put('/support/{id}/status', [CustomerSupportController::class, 'updateStatus'])->name('support.updateStatus');

    Route::put('/courier/{id}/couriercompany_id', [OrderController::class, 'updateCourierCompany'])->name('courier.couriercompany');

    Route::post('update-verification/{user}', [DeliverymanController::class, 'updateVerification'])->name('update-verification');
    Route::get('reference-data', [ClientController::class, 'referenceindex'])->name('reference-list');

    Route::get('orderprint-datatable', [OrderController::class, 'orderprintindex'])->name('orderprint-datatable');
    Route::get('payment-datatable', [PaymentGatewayController::class, 'paymentIndex'])->name('payment-datatable');
    Route::get('labelprint/{id?}', [OrderController::class, 'labelprint'])->name('labelprint');
    Route::get('printorder/{id?}', [OrderController::class, 'printorder'])->name('printorder');
    Route::get('printbarcode/{id?}', [OrderController::class, 'printbarcode'])->name('printbarcode');
    Route::get('ebarcodeSingal/{id?}', [OrderController::class, 'printorderqrSingal'])->name('printorderbarcodeSingal');
    Route::get('printOrderMultiple', [OrderController::class, 'printOrderMultiple'])->name('printOrderMultiple');
    Route::get('multipleLabel', [OrderController::class, 'multipleLabel'])->name('multiple-Label');

    Route::post('import-order-data', [OrderController::class, 'importorderdata'])->name('import.orderdata');
    Route::get('bulkorderdata', [OrderController::class, 'bulkorderdata'])->name('bulk.order.data');
    Route::get('order-help', [OrderController::class, 'orderhelp'])->name('order-help');
    Route::get('order-download.template', [OrderController::class, 'orderdownloadtemplate'])->name('order-download.template');
    Route::get('ordertemplate.excel', [OrderController::class, 'ordertemplateExcel'])->name('ordertemplate.excel');

    Route::get('ordervehicleinfo-vehicle/{id?}', [DeliverymanController::class, 'vehicleInformationOrder'])->name('ordervehicleinfo-vehicle');
    Route::get('vehicleinfo/{id?}', [DeliverymanController::class, 'vehicleInformation'])->name('vehicleinfo-vehicle');

    //webSection in help routes
    Route::get('help-information', [FronthomeController::class, 'helpinfinformation'])->name('help-information');
    Route::get('help-downlaodapp', [FronthomeController::class, 'helpinfdownlaodapp'])->name('help-downlaodapp');
    Route::get('help-contact', [FronthomeController::class, 'helpcontact'])->name('help-contact');
    Route::get('help-about', [FronthomeController::class, 'helpabout'])->name('help-about');
    Route::get('help-courier-recruitment', [FronthomeController::class, 'helpCourierRecruitment'])->name('help.courier.recruitment');
    Route::get('delivery-job', [FronthomeController::class, 'deliveryJob'])->name('delivery.job');

    Route::get('help-whydelivery', [WhyDeliveryController::class, 'helpwhydelivery'])->name('help-whydelivery');
    Route::get('help-clientreview', [ClientReviewController::class, 'helpclientreview'])->name('help-clientreview');
    Route::get('help-deliverypartner', [DeliveryPartnerController::class, 'helpdeliverypartner'])->name('help-deliverypartner');
    Route::get('help-client-testimonial', [ClientTestimonialController::class, 'helpClientTestimonial'])->name('help.client.testimonial');
    Route::get('help-walkthrough', [WalkThroughController::class, 'helpwalkthrough'])->name('help-walkthrough');
    Route::get('help-app-overview', [WebSiteSectionController::class, 'helpAppOverview'])->name('help.app.overview');
    Route::get('help-document-verification', [DocumentVerificationController::class, 'helpDocumentVerification'])->name('help.document.verification');
    Route::get('help-delivery-man-section', [DeliveryManSectionController::class, 'helpDeliveryManSection'])->name('help.delivery.man.section');
    Route::get('help-deliver-your-way', [DeliverYourWayController::class, 'helpDeliverYourWay'])->name('help.deliver.your.way');

    //claims mangemant
    Route::resource('claims', ClaimsController::class);
    Route::get('claims-model/{id}', [ClaimsController::class, 'claimsmodel'])->name('claims-model');
    Route::any('approved-status/{id}', [ClaimsController::class, 'approvedStatus'])->name('approvedstatus');
    Route::post('claims-history', [ClaimsController::class, 'claimhistorySave'])->name('claims-history');

    Route::get('close-view/{id}', [ClaimsController::class, 'closeview'])->name('close-view');

    //Order Mail
    Route::resource('ordermail', OrderMailController::class);
    Route::get('otpVerify_template', [OrderMailController::class,'otpVerify_template'])->name('otpVerify_template');
    //Order SMS
    Route::resource('ordersms', OrderSMSController::class);

    // coupon 
    Route::resource('coupon', CouponController::class);
    Route::get('coupon-restore/{id?}', [CouponController::class, 'action'])->name('coupon.restore');
    Route::delete('coupon-force-delete/{id?}', [CouponController::class, 'action'])->name('coupon.force.delete');

    //RestApi
    Route::resource('rest-api', RestApiController::class);
    Route::get('rest-api.restore/{id?}', [RestApiController::class, 'action'])->name('rest-api.restore');
    Route::delete('rest-api-force-delete/{id?}', [RestApiController::class, 'action'])->name('rest-api.force.delete');

    Route::get('help-restapi', [RestApiController::class, 'helprestapi'])->name('help-restapi');

    Route::get('website-section-order-status', [FronthomeController::class, 'orderStatus'])->name('website.section.order.status');
    Route::get('get-frontend-order-status', [FronthomeController::class, 'getFrontendOrderStatusList'])->name('get.frontend.order.status.status');
    Route::post('store-frontend-order-status', [FronthomeController::class, 'storeFrontendOrderStatusData'])->name('store.frontend.order.status.data');
    Route::post('order-status-data-delete', [FronthomeController::class, 'frontendOrderStatusDataDestroy'])->name('delete.frontend.order.status.data');

    // Emergency 
    Route::resource('emergency',EmergencyController::class);
    Route::get('/emergency-location/{id?}', [EmergencyController::class, 'trackLocation'])->name('emergency.location');
    Route::post('/deliveryman/vehicle/status/update', [DeliverymanController::class, 'updateVehicleStatus'])->name('deliveryman.vehicle.status.update');

    Route::get('cancel-order{id?}',[OrderController::class, 'ordercancel'])->name('cance.order');
    Route::post('cancelorder-save',[OrderController::class,'saveCancelOrder'])->name('ordercancel.save');
    
});
Route::get('/map', [EmergencyController::class, 'showMap']);

Route::get('/ajax-list', [HomeController::class, 'getAjaxList'])->name('ajax-list');

Route::get('/', [FronthomeController::class, 'index']);

$admin_routes = env('SECURE_ADMIN_ROUTE') ? env('SECURE_ADMIN_ROUTE') : 'adminHub';
Route::get($admin_routes, [FronthomeController::class, 'adminLogin'])->name('admin-login');

Route::get('admin/verify-otp', [FronthomeController::class, 'verifyOTP'])->name('verify-otp');
Route::post('admin/verify-otp-post', [FronthomeController::class, 'verifyOTP_post'])->name('verify-otp-post');
Route::get('frontend-section', [FronthomeController::class, 'index'])->name('frontend-section');
Route::get('ordertracking', [FronthomeController::class, 'ordertracking'])->name('ordertracking');
Route::get('email-order/{id}', [FronthomeController::class, 'emailOrder'])->name('email-order');
Route::post('orderhistory', [FronthomeController::class, 'orderhistory'])->name('orderhistory');
Route::get('aboutusdetail', [FronthomeController::class, 'about_us'])->name('about-us');
Route::get('contactus', [FronthomeController::class, 'contactus'])->name('contactus');
Route::get('privacypolicy', [FronthomeController::class, 'privacypolicy'])->name('privacypolicy');
Route::get('delivery-partner', [FronthomeController::class, 'deliverypartner'])->name('deliverypartner');
Route::get('termofservice', [FronthomeController::class, 'termofservice'])->name('termofservice');
Route::post('client-store', [ClientController::class, 'frontendclientstore'])->name('client.store');
Route::get('page/{slug}', [FronthomeController::class, 'page'])->name('pages');

Route::get('sms-orderhistory/{order_id}', [FronthomeController::class, 'smsorderhistory'])->name('sms-orderhistory');



// Route::get('/t/{encodedUrl}', function ($encodedUrl) {
//     $decodedUrl = base64_decode($encodedUrl);
//     return redirect($decodedUrl);
// });