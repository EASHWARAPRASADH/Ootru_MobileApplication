<?php

use App\Http\Controllers;
use App\Http\Controllers\API;
use App\Http\Controllers\CouponController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('save-external-order', [API\RestApiController::class, 'store']);
Route::get('get-code', [API\RestApiController::class, 'getCount']);
Route::post('new-register', [API\UserController::class, 'newregister']);
Route::post('driver-register', [API\UserController::class, 'driverRegister']);
Route::post('new-login', [API\UserController::class, 'newlogin']);
Route::post('new-forget-password', [API\UserController::class, 'newforgetPassword']);
Route::post('new-socialLogin', [API\UserController::class, 'newsocialLogin']);
Route::get('user-list', [API\UserController::class, 'userList']);
Route::get('user-detail', [API\UserController::class, 'userDetail']);
Route::post('paytr-callback', [API\PaymentController::class, 'callback']);

Route::get('appsetting', [API\DashboardController::class, 'appsetting']);
Route::get('language-table-list', [API\LanguageTableController::class, 'getList']);

Route::group(['middleware' => ['auth:sanctum', 'assign_user_role']], function () {
    Route::get('dashboard-detail', [API\UserController::class, 'dashboard']);
    Route::get('dashboard-chartdata', [API\UserController::class, 'dashboardChartData']);

    Route::post('notification-list', [API\NotificationController::class, 'getList']);

    Route::get('paymentgateway-list', [API\PaymentGatewayController::class, 'getList']);
    Route::post('paymentgateway-save', [Controllers\PaymentGatewayController::class, 'store']);

    Route::post('update-profile', [API\UserController::class, 'updateProfile']);
    Route::post('change-password', [API\UserController::class, 'changePassword']);
    Route::post('update-user-status', [API\UserController::class, 'updateUserStatus']);

    Route::post('delete-user-account', [API\UserController::class, 'deleteUserAccount']);

    Route::get('logout', [API\UserController::class, 'logout']);
    Route::get('get-dashboard-list', [API\DashboardController::class, 'getdashboardlist']);

    Route::get('country-list', [API\CountryController::class, 'getList']);
    Route::get('country-detail', [API\CountryController::class, 'getDetail']);
    Route::post('country-save', [Controllers\CountryController::class, 'store']);
    Route::post('country-update/{id}', [Controllers\CountryController::class, 'update']);
    Route::post('country-delete/{id}', [Controllers\CountryController::class, 'destroy']);
    Route::post('multiple-delete-country', [API\CountryController::class, 'multipleDeleteRecords']);
    Route::post('country-action', [Controllers\CountryController::class, 'action']);

    Route::get('city-detail', [API\CityController::class, 'getDetail']);
    Route::get('city-list', [API\CityController::class, 'getList']);
    Route::post('city-save', [Controllers\CityController::class, 'store']);
    Route::post('city-update/{id}', [Controllers\CityController::class, 'update']);
    Route::post('city-delete/{id}', [Controllers\CityController::class, 'destroy']);
    Route::post('multiple-delete-city', [API\CityController::class, 'multipleDeleteRecords']);
    Route::post('city-action', [Controllers\CityController::class, 'action']);

    Route::get('vehicle-list', [API\VehicleController::class, 'getList']);
    Route::post('vehicle-save', [Controllers\VehicleController::class, 'store']);
    Route::post('vehicle-update/{id}', [Controllers\VehicleController::class, 'update']);
    Route::post('vehicle-delete/{id}', [Controllers\VehicleController::class, 'destroy']);
    Route::post('multiple-delete-vehicle', [API\VehicleController::class, 'multipleDeleteRecords']);
    Route::post('vehicle-action', [Controllers\VehicleController::class, 'action']);

    Route::get('extracharge-list', [API\ExtraChargeController::class, 'getList']);
    Route::post('extracharge-save', [Controllers\ExtraChargeController::class, 'store']);
    Route::post('extracharge-update/{id}', [Controllers\ExtraChargeController::class, 'update']);
    Route::post('extracharge-delete/{id}', [Controllers\ExtraChargeController::class, 'destroy']);
    Route::post('extracharge-action', [Controllers\ExtraChargeController::class, 'action']);

    Route::get('staticdata-list', [API\StaticDataController::class, 'getList']);
    Route::post('staticdata-save', [Controllers\StaticDataController::class, 'store']);
    Route::post('staticdata-delete/{id}', [Controllers\StaticDataController::class, 'destroy']);

    Route::get('get-setting', [API\SettingController::class, 'getList']);
    Route::post('save-setting', [Controllers\SettingController::class, 'saveInvoiceSetting']);
    Route::post('setting-upload-invoice-image', [Controllers\SettingController::class, 'settingUploadInvoiceImage']);

    Route::get('notification-detail', [API\NotificationController::class, 'getNotificationDetail']);

    Route::get('user-list', [API\UserController::class, 'userList']);
    Route::get('reference-list', [API\ReferenceController::class, 'getList']);
    Route::get('user-detail', [API\UserController::class, 'userDetail']);
    Route::get('user-profile-detail', [API\UserController::class, 'commonUserDetail']);
    Route::post('user-save', [Controllers\ClientController::class, 'store']);
    Route::post('user-update/{id}', [Controllers\ClientController::class, 'update']);
    Route::post('user-delete/{id}', [Controllers\ClientController::class, 'destroy']);
    Route::post('delete-user', [API\UserController::class, 'deleteUser']);
    Route::post('update-appsetting', [Controllers\SettingController::class, 'updateAppSetting']);
    Route::get('get-appsetting', [API\UserController::class, 'getAppSetting']);

    Route::post('multiple-delete-user', [API\UserController::class, 'multipleDeleteRecords']);
    Route::post('user-action', [Controllers\ClientController::class, 'action']);
    Route::post('user-forceDelete', [Controllers\ClientController::class, 'userdelete']);
    Route::get('user-filter-list', [API\UserController::class, 'userFilterList']);

    Route::get('wallet-detail', [API\WalletController::class, 'getWallatDetail']);
    Route::post('save-wallet', [API\WalletController::class, 'saveWallet']);
    Route::get('wallet-list', [API\WalletController::class, 'getList']);
    Route::get('reward-list', [API\WalletController::class, 'rewardhistory']);

    Route::get('document-list', [API\DocumentController::class, 'getList']);
    Route::post('document-save', [Controllers\DocumentController::class, 'store']);
    Route::post('document-update/{id}', [Controllers\DocumentController::class, 'update']);
    Route::post('document-delete/{id}', [Controllers\DocumentController::class, 'destroy']);
    Route::post('document-action', [Controllers\DocumentController::class, 'action']);

    Route::get('delivery-man-document-list', [API\DeliveryManDocumentController::class, 'getList']);
    Route::post('multiple-delete-deliveryman-document', [API\DeliveryManDocumentController::class, 'multipleDeleteRecords']);
    Route::post('delivery-man-document-save', [Controllers\DeliveryManDocumentController::class, 'store']);
    Route::post('delivery-man-document-delete/{id}', [Controllers\DeliveryManDocumentController::class, 'destroy']);
    Route::post('delivery-man-document-action', [Controllers\DeliveryManDocumentController::class, 'action']);

    Route::get('order-list', [API\OrderController::class, 'getList']);
    Route::get('order-detail', [API\OrderController::class, 'getDetail']);
    Route::post('multiple-delete-order', [API\OrderController::class, 'multipleDeleteRecords']);
    Route::post('order-save', [Controllers\OrderController::class, 'store']);
    Route::post('order-update/{id}', [Controllers\OrderController::class, 'update']);
    Route::post('assign-order-update', [Controllers\OrderController::class, 'assignOrder']);
    Route::post('order-delete/{id}', [Controllers\OrderController::class, 'destroy']);
    Route::post('order-action', [Controllers\OrderController::class, 'action']);
    Route::post('order-auto-assign', [Controllers\OrderController::class, 'autoAssignCancelOrder']);
    Route::get('order-tracking', [API\OrderController::class, 'getOrderTrackingDetail']);
    Route::Post('calculatetotal-get', [API\OrderController::class, 'calculateTotal']);

    Route::get('useraddress-list', [API\UserAddressController::class, 'getList']);
    Route::get('useraddress-detail', [API\UserAddressController::class, 'getDetail']);
    Route::post('useraddress-save', [Controllers\UserAddressController::class, 'store']);
    Route::post('useraddress-delete/{id}', [Controllers\UserAddressController::class, 'destroy']);

    Route::get('place-autocomplete-api', [API\CommonController::class, 'placeAutoComplete']);
    Route::get('place-detail-api', [API\CommonController::class, 'placeDetail']);
    Route::get('distance-matrix-api', [API\CommonController::class, 'distanceMatrix']);

    Route::post('payment-save', [API\PaymentController::class, 'paymentSave']);
    Route::get('payment-list', [API\PaymentController::class, 'getList']);

    Route::get('deliveryman-earning-list', [API\PaymentController::class, 'getDeliveryManEarningList']);

    Route::get('withdrawrequest-list', [API\WithdrawRequestController::class, 'getList']);
    Route::post('save-withdrawrequest', [API\WithdrawRequestController::class, 'saveWithdrawrequest']);
    Route::post('approved-withdrawrequest', [API\WithdrawRequestController::class, 'approvedWithdrawRequest']);
    Route::post('decline-withdrawrequest', [API\WithdrawRequestController::class, 'declineWithdrawRequest']);

    Route::get('frontenddata-list', [API\FrontendDataController::class, 'getList']);
    Route::post('frontenddata-save', [Controllers\FrontendDataController::class, 'store']);
    Route::post('frontenddata-delete/{id}', [Controllers\FrontendDataController::class, 'destroy']);
    Route::get('frontend-website-data', [API\DashboardController::class, 'websiteData']);
    Route::get('deliveryman-dashboard-data', [API\DashboardController::class, 'deliverymandashboard']);

    Route::post('verify-otp-for-email', [API\UserController::class, 'verifyOTPForEmail']);
    Route::post('resend-otp-for-email', [API\UserController::class, 'resendOTPForEmail']);

    // pages
    Route::get('pages-list', [API\PagesController::class, 'getList']);
    Route::post('pages-save', [Controllers\PagesController::class, 'store']);
    Route::post('pages-delete/{id}', [Controllers\PagesController::class, 'destroy']);
    Route::post('pages-update/{id}', [Controllers\PagesController::class, 'update']);
    Route::get('page-detail', [API\PagesController::class, 'getDetail']);

    // customer support route
    // Route::get('customersupport-list', [API\SupportchatHistoryController::class, 'getList']);
    Route::get('customersupport-list', [API\CustomerSupportController::class, 'getList']);
    Route::post('customersupport-save', [Controllers\CustomerSupportController::class, 'store']);
    Route::post('customersupport-delete/{id}', [Controllers\CustomerSupportController::class, 'destroy']);
    Route::post('chatmessage-save', [Controllers\CustomerSupportController::class, 'chatMessage']);
    Route::post('status-save/{id}', [Controllers\CustomerSupportController::class, 'updateStatus']);

    // reports route
    Route::get('userreport-list', [API\UserReportController::class, 'getList']);
    Route::get('deliverymanearningreport-list', [API\DeliverymanReportController::class, 'getList']);
    Route::get('adminearningreport-list', [API\AdminEarningReportController::class, 'getList']);
    Route::get('orderreport-list', [API\OrderReportController::class, 'getList']);
    Route::get('deliverymanreport-list', [API\DeliverymanByReportController::class, 'getList']);
    Route::get('cityreport-list', [API\CityByReportController::class, 'getList']);
    Route::get('countryreport-list', [API\CountryByReportController::class, 'getList']);

    // order location
    Route::get('order-location-list', [API\OrderController::class, 'orderLocationList']);

    // push notification
    Route::get('pushnotification-list', [API\PushNotificationController::class, 'getList']);
    Route::post('pushnotification-save', [Controllers\PushNotificationController::class, 'store']);
    Route::post('pushnotification-delete/{id}', [Controllers\PushNotificationController::class, 'destroy']);

    // isReSchedule api
    Route::post('reschedule-save', [Controllers\OrderController::class, 'isReschedule']);

    // deliveryman vehicle history
    Route::post('deliverymanvehiclehistory-save', [Controllers\OrderController::class, 'deliveryManVehiclehistory']);
    Route::get('deliverymanvehiclehistory-list', [API\DeliverymanVehicleHistoryController::class, 'getList']);
    Route::post('deliverymanvehicle-status-save', [Controllers\DeliverymanController::class, 'updateVehicleStatus']);

    // CourierCompanies api
    Route::get('couriercompanies-list', [API\CourierCompaniesController::class, 'getList']);
    Route::post('couriercompanies-save', [Controllers\CourierCompaniesController::class, 'store']);
    Route::post('couriercompanies-update/{id}', [Controllers\CourierCompaniesController::class, 'update']);
    Route::post('couriercompanies-delete/{id}', [Controllers\CourierCompaniesController::class, 'destroy']);

    // SippedOrder in couriercomny add
    Route::post('shipped-save/{id}', [Controllers\OrderController::class, 'updateCourierCompany']);

    Route::get('permision-list', [API\UserController::class, 'permissionGetList']);

    // subadmin api
    Route::get('subadmin-list', [API\SubAdminController::class, 'getList']);
    Route::post('subadmin-save', [Controllers\SubAdminController::class, 'store']);
    Route::post('subadmin-update/{id}', [Controllers\SubAdminController::class, 'update']);
    Route::post('subadmin-delete/{id}', [Controllers\SubAdminController::class, 'destroy']);
    Route::post('subadmin-action', [Controllers\SubAdminController::class, 'action']);

    // merge api
    Route::get('multipledetails-list', [API\DashboardController::class, 'multipleDetails']);
    Route::get('order-print-list', [API\OrderController::class, 'orderPrintList']);

    // claims mangemant
    Route::post('claims-save', [Controllers\ClaimsController::class, 'store']);
    Route::get('claims-list', [Controllers\ClaimsController::class, 'getList']);
    Route::get('claimhistory-list', [Controllers\ClaimsController::class, 'claimhistorygetList']);
    Route::post('claimshistory-save', [Controllers\ClaimsController::class, 'claimhistorySave']);
    Route::post('reject-status/{id}', [Controllers\ClaimsController::class, 'rejectStatus']);
    Route::post('approved-status/{id}', [Controllers\ClaimsController::class, 'approvedStatus']);

    Route::post('status-details', [Controllers\ClaimsController::class, 'statusdetail']);
    // Bid
    Route::post('apply-bid', [Controllers\OrderController::class, 'applyBidOrder']);
    Route::get('get-bidding-order', [Controllers\OrderController::class, 'getBiddingDeliveryMan']);
    Route::post('order-bid-respond', [Controllers\OrderController::class, 'acceptBidRequest']);

    Route::get('orderbid-list', [API\OrderController::class, 'OrderBidList']);
    Route::get('profOfpicture-list', [API\ProfofPicturesController::class, 'profOfpictureList']);
    Route::post('profOfpicture-save', [API\ProfofPicturesController::class, 'profOfpictureSave']);

    // Coupon
    Route::get('coupon-list', [API\CouponController::class, 'getlist']);
    Route::post('coupon-save',[CouponController::class,'store']);
    Route::post('coupon-update/{id}',[CouponController::class,'update']);
    Route::post('coupon-delete/{id}',[CouponController::class,'destroy']);

    // RestApi
    Route::get('restapi-list', [API\RestApiController::class, 'getlist']);

    Route::post('order-status/{id}', [API\OrderController::class, 'orderStatusUpdate']);
    Route::post('order-trasfer-deliveryman/{id}', [API\OrderController::class, 'transferDeliverymanOrder']);

    // Emergency
    Route::get('emergency-list', [Controllers\EmergencyController::class, 'emergencyList']);
    Route::post('emergency-save', [Controllers\EmergencyController::class, 'store']);
    Route::post('emergency-update/{id}', [Controllers\EmergencyController::class, 'update']);

    // Sos
    Route::post('sos-save', [Controllers\EmergencyController::class, 'sosStore']);
    Route::get('sos-list', [Controllers\EmergencyController::class, 'sosList']);
    Route::post('sos-delete/{id}', [Controllers\EmergencyController::class, 'sosDestroy']);

    // Rating
    Route::post('rating-save', [Controllers\OrderController::class, 'rating']);
    // Paytr

    Route::post('paytr-save', [API\PaymentController::class, 'initiatePayment']);
});
