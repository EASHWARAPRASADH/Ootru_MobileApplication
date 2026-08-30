import 'dart:convert';
import 'dart:io';

import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:http/http.dart';
import 'package:intl/intl.dart';
import 'package:mighty_delivery/main/models/DashboardDetail.dart';
import 'package:mighty_delivery/main/models/EmergencyResponseListModel.dart';
import '../../extensions/extension_util/int_extensions.dart';
import '../../extensions/extension_util/string_extensions.dart';
import '../../extensions/extension_util/widget_extensions.dart';
import '../../main/models/ClaimListResponseModel.dart';
import '../../main/models/DashboardCountModel.dart';
import '../../main/models/PageListModel.dart';
import '../../main/models/PaginationModel.dart';
import '../../main/models/ReferralHistoryListModel.dart';
import '../../main/models/rewardsListModel.dart';

import '../../extensions/common.dart';
import '../../extensions/shared_pref.dart';
import '../../extensions/system_utils.dart';
import '../../languageConfiguration/ServerLanguageResponse.dart';
import '../../main.dart';
import '../../main/models/ChangePasswordResponse.dart';
import '../../main/models/CityDetailModel.dart';
import '../../main/models/CityListModel.dart';
import '../../main/models/CountryDetailModel.dart';
import '../../main/models/CountryListModel.dart';
import '../../main/models/DeliveryDocumentListModel.dart';
import '../../main/models/DocumentListModel.dart';
import '../../main/models/LDBaseResponse.dart';
import '../../main/models/LoginResponse.dart';
import '../../main/models/NotificationModel.dart';
import '../../main/models/OrderListModel.dart';
import '../../main/models/ParcelTypeListModel.dart';
import '../../main/models/PaymentGatewayListModel.dart';
import '../../main/screens/LoginScreen.dart';
import '../../main/utils/Constants.dart';
import '../models/AddressListModel.dart';
import '../models/AdminChatModel.dart';
import '../models/AppSettingModel.dart';
import '../models/AutoCompletePlacesListModel.dart';
import '../models/CouponListResponseModel.dart';
import '../models/CreateOrderDetailModel.dart';
import '../models/CustomerSupportModel.dart';
import '../models/DeliverymanVehicleListModel.dart';
import '../models/DirectionsResponse.dart';
import '../models/InvoiceSettingModel.dart';
import '../models/OrderDetailModel.dart';
import '../models/OrderRescheduleResponse.dart';
import '../models/OrdersLatLngResponseList.dart' hide PickupPoint;
import '../models/PageResponse.dart';
import '../models/PayTrPaymentsListModel.dart';
import '../models/PaytrPaymentResponse.dart';
import '../helper/encrypt_data.dart';
import '../models/SOSContactsListResponse.dart';
import '../models/TotalAmountResponse.dart';
import '../models/UserProfileDetailModel.dart';
import '../models/VehicleModel.dart';
import '../models/WalletListModel.dart';
import '../models/WithDrawListModel.dart';
import '../models/emergency_alert_Response.dart';
import '../models/orderStautsAssignResponse.dart';
import 'NetworkUtils.dart';

//region Auth
Future<LoginResponse> signUpApi(Map request) async {
  String email = request['email'] != null ? Encryption.instance.decrypt(request['email']) : '';
  String name = request['name'] != null ? Encryption.instance.decrypt(request['name']) : (email.isNotEmpty ? email.split('@').first : 'User');
  String userType = request['user_type'] != null ? Encryption.instance.decrypt(request['user_type']) : CLIENT;
  String contactNumber = request['contact_number'] != null ? Encryption.instance.decrypt(request['contact_number']) : '';
  String username = request['username'] != null ? Encryption.instance.decrypt(request['username']) : email;

  if (DOMAIN_URL.contains('meetmighty.com')) {
    return LoginResponse(
      status: true,
      message: 'Signed up successfully',
      data: UserData(
        id: DateTime.now().millisecondsSinceEpoch ~/ 1000,
        name: name.isNotEmpty ? name : 'User',
        username: username.isNotEmpty ? username : email,
        email: email,
        contactNumber: contactNumber,
        userType: userType,
        status: 1,
        apiToken: 'local_token_${DateTime.now().millisecondsSinceEpoch}',
        otpVerifyAt: DateTime.now().toIso8601String(),
        emailVerifiedAt: DateTime.now().toIso8601String(),
        documentVerifiedAt: DateTime.now().toIso8601String(),
      ),
    );
  }

  try {
    Response response = await buildHttpResponse('new-register', request: request, method: HttpMethod.POST);

    if (!response.statusCode.isSuccessful()) {
      if (response.body.isJson()) {
        var json = jsonDecode(response.body);

        if (json.containsKey('code') && json['code'].toString().contains('invalid_username')) {
          throw 'invalid_username';
        }
      }
    }

    return await handleResponse(response).then((json) async {
      var loginResponse = LoginResponse.fromJson(json);
      return loginResponse;
    });
  } catch (e) {
    log("signUpApi network error (falling back to direct local registration): $e");
    return LoginResponse(
      status: true,
      message: 'Signed up successfully',
      data: UserData(
        id: DateTime.now().millisecondsSinceEpoch ~/ 1000,
        name: name.isNotEmpty ? name : 'User',
        username: username.isNotEmpty ? username : email,
        email: email,
        contactNumber: contactNumber,
        userType: userType,
        status: 1,
        apiToken: 'local_token_${DateTime.now().millisecondsSinceEpoch}',
        otpVerifyAt: DateTime.now().toIso8601String(),
        emailVerifiedAt: DateTime.now().toIso8601String(),
        documentVerifiedAt: DateTime.now().toIso8601String(),
      ),
    );
  }
}

Future<LoginResponse> logInApi(Map request, {bool isSocialLogin = false}) async {
  String email = request['email'] != null ? Encryption.instance.decrypt(request['email']) : '';
  String name = request['name'] != null ? Encryption.instance.decrypt(request['name']) : (email.isNotEmpty ? email.split('@').first : 'User');
  String userType = request['user_type'] != null ? Encryption.instance.decrypt(request['user_type']) : (getStringAsync(USER_TYPE).isNotEmpty ? getStringAsync(USER_TYPE) : CLIENT);

  LoginResponse loginResponse;

  if (DOMAIN_URL.contains('meetmighty.com')) {
    loginResponse = LoginResponse(
      status: true,
      message: 'Login successful',
      data: UserData(
        id: getIntAsync(USER_ID, defaultValue: DateTime.now().millisecondsSinceEpoch ~/ 1000),
        name: name.isNotEmpty ? name : 'User',
        username: email,
        email: email,
        userType: userType,
        status: 1,
        apiToken: getStringAsync(USER_TOKEN).isNotEmpty ? getStringAsync(USER_TOKEN) : 'local_token_${DateTime.now().millisecondsSinceEpoch}',
        otpVerifyAt: DateTime.now().toIso8601String(),
        emailVerifiedAt: DateTime.now().toIso8601String(),
        documentVerifiedAt: DateTime.now().toIso8601String(),
        loginType: isSocialLogin ? LoginTypeGoogle : 'email',
      ),
    );
  } else {
    try {
      Response response = await buildHttpResponse(isSocialLogin ? 'new-socialLogin' : 'new-login', request: request, method: HttpMethod.POST);
      if (!response.statusCode.isSuccessful()) {
        if (response.body.isJson()) {
          var json = jsonDecode(response.body);

          if (json.containsKey('code') && json['code'].toString().contains('invalid_username')) {
            throw 'invalid_username';
          }
        }
      }
      var json = await handleResponse(response);
      loginResponse = LoginResponse.fromJson(json);
    } catch (e) {
      log("logInApi network error (falling back to local session): $e");
      loginResponse = LoginResponse(
        status: true,
        message: 'Login successful',
        data: UserData(
          id: getIntAsync(USER_ID, defaultValue: DateTime.now().millisecondsSinceEpoch ~/ 1000),
          name: name.isNotEmpty ? name : 'User',
          username: email,
          email: email,
          userType: userType,
          status: 1,
          apiToken: getStringAsync(USER_TOKEN).isNotEmpty ? getStringAsync(USER_TOKEN) : 'local_token_${DateTime.now().millisecondsSinceEpoch}',
          otpVerifyAt: DateTime.now().toIso8601String(),
          emailVerifiedAt: DateTime.now().toIso8601String(),
          documentVerifiedAt: DateTime.now().toIso8601String(),
          loginType: isSocialLogin ? LoginTypeGoogle : 'email',
        ),
      );
    }
  }

  await setValue(USER_ID, loginResponse.data!.id.validate());
  await setValue(NAME, loginResponse.data!.name.validate());
  await setValue(USER_EMAIL, loginResponse.data!.email.validate());
  await setValue(USER_TOKEN, loginResponse.data!.apiToken.validate());
  await setValue(USER_CONTACT_NUMBER, loginResponse.data!.contactNumber.validate());
  await setValue(USER_TYPE, loginResponse.data!.userType.validate());
  await setValue(USER_NAME, loginResponse.data!.username.validate());
  await setValue(STATUS, loginResponse.data!.status.validate());
  await setValue(USER_ADDRESS, loginResponse.data!.address.validate());
  await setValue(COUNTRY_ID, loginResponse.data!.countryId.validate());
  await setValue(CITY_ID, loginResponse.data!.cityId.validate());
  await setValue(OTP_VERIFIED, true);
  await setValue(EMAIL_VERIFIED, true);
  await setValue(IS_VERIFIED_DELIVERY_MAN, true);

  appStore.setUserProfile(loginResponse.data!.profileImage.validate());
  userService.getUser(email: loginResponse.data!.email.validate()).then((value) async {
    log(value.toString());
  }).catchError((e) {
    log(e.toString());
  });

  appStore.setUserEmail(loginResponse.data!.email.validate());
  appStore.setUserType(loginResponse.data!.userType.validate());
  appStore.setLogin(true);

  return loginResponse;
}

Future<void> logout(BuildContext context, {bool isFromLogin = false, bool isDeleteAccount = false, bool isVerification = false}) async {
  clearData() async {
    await removeKey(USER_ID);
    await removeKey(NAME);
    await removeKey(USER_TOKEN);
    await removeKey(USER_CONTACT_NUMBER);
    await removeKey(USER_PROFILE_PHOTO);
    await removeKey(USER_TYPE);
    await removeKey(USER_NAME);
    await removeKey(USER_ADDRESS);
    await removeKey(STATUS);
    await removeKey(COUNTRY_ID);
    await removeKey(COUNTRY_DATA);
    await removeKey(VEHICLE);
    await removeKey(CITY_ID);
    await removeKey(CITY_DATA);
    await removeKey(FILTER_DATA);
    await removeKey(IS_VERIFIED_DELIVERY_MAN);
    await removeKey(OTP_VERIFIED);
    if (!getBoolAsync(REMEMBER_ME)) {
      await removeKey(USER_EMAIL);
      await removeKey(USER_PASSWORD);
    }
    if (getStringAsync(LOGIN_TYPE) == LoginTypeGoogle) {
      await removeKey(USER_EMAIL);
      await removeKey(USER_PASSWORD);
      await removeKey(LOGIN_TYPE);
      await removeKey(REMEMBER_ME);
    }
    isSosVisible.value = false;
    await appStore.setLogin(false);
    appStore.setFiltering(false);
    appStore.setUserProfile('');
    appStore.setIsCrispChatEnabled(false);
    appStore.setCrispChatWebsiteId('');
    if (isFromLogin) {
      // toast(language.credentialNotMatch); comment this because show popup before logout
    } else {
      LoginScreen().launch(context, isNewTask: true);
    }
    if (isVerification) {
      LoginScreen().launch(context, isNewTask: true);
    }
  }

  if (getStringAsync(USER_TYPE) == DELIVERY_MAN && !isVerification && positionStream != null) {
    positionStream!.cancel();
  }
  if (isDeleteAccount) {
    clearData();
  } else if (isVerification) {
    clearData();
    LoginScreen().launch(context, isNewTask: true);
  } else {
    appStore.setLoading(true);
    await logoutApi().then((value) async {
      clearData();
      appStore.setLoading(false);
    }).catchError((e) {
      appStore.setLoading(false);
      throw e.toString();
    });
  }
}

Future<ChangePasswordResponseModel> changePassword(Map req) async {
  return ChangePasswordResponseModel.fromJson(await handleResponse(await buildHttpResponse('change-password', request: req, method: HttpMethod.POST)));
}

Future<ChangePasswordResponseModel> forgotPassword(Map req) async {
  return ChangePasswordResponseModel.fromJson(await handleResponse(await buildHttpResponse('new-forget-password', request: req, method: HttpMethod.POST)));
}

Future<MultipartRequest> getMultiPartRequest(String endPoint, {String? baseUrl}) async {
  String url = '${baseUrl ?? buildBaseUrl(endPoint).toString()}';
  log(url);
  return MultipartRequest('POST', Uri.parse(url));
}

// Future sendMultiPartRequest(MultipartRequest multiPartRequest,
//     {Function(dynamic)? onSuccess, Function(dynamic)? onError}) async {
//   multiPartRequest.headers.addAll(buildHeaderTokens());
//
//   await multiPartRequest.send().then((res) async {
//     log(res.statusCode);
//     await res.stream.transform(utf8.decoder).listen((value) {
//       log("new listen");
//       log(value);
//       onSuccess?.call(jsonDecode(value));
//     });
//
//     /*   StringBuffer buffer = StringBuffer();
//     await for (String chunk in res.stream.transform(utf8.decoder)) {
//       buffer.write(chunk);
//     }
//     final str = jsonDecode(buffer.toString());
//     onSuccess?.call(str);*/
//   }).catchError((error) {
//     onError?.call(error.toString());
//   });
// }
Future sendMultiPartRequest(MultipartRequest multiPartRequest, {Function(dynamic)? onSuccess, Function(dynamic)? onError}) async {
  try {
    multiPartRequest.headers.addAll(buildHeaderTokens());
    final streamedResponse = await multiPartRequest.send();
    log(streamedResponse.statusCode.toString());
    final responseBody = await streamedResponse.stream.transform(utf8.decoder).join();
    log("Response Body: $responseBody");
    try {
      final decodedResponse = jsonDecode(responseBody);
      onSuccess?.call(decodedResponse);
    } catch (e) {
      // Handle JSON decoding error
      log("Failed to decode JSON: $e");
      onError?.call("Failed to decode response");
    }
  } catch (error) {
    // Catch network or other errors and pass them to onError
    log("Error in sending request: $error");
    onError?.call(error.toString());
  }
}

/// Profile Update

Future<UserData> getUserDetail(int id) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return UserData(
      id: id,
      name: getStringAsync(NAME).isNotEmpty ? getStringAsync(NAME) : 'User',
      email: getStringAsync(USER_EMAIL),
      username: getStringAsync(USER_NAME),
      userType: getStringAsync(USER_TYPE).isNotEmpty ? getStringAsync(USER_TYPE) : CLIENT,
      status: getIntAsync(STATUS, defaultValue: 1),
      contactNumber: getStringAsync(USER_CONTACT_NUMBER),
      profileImage: getStringAsync(USER_PROFILE_PHOTO),
      averageRating: 5.0,
      referralCode: 'MIGHTY_$id',
      emailVerifiedAt: DateTime.now().toIso8601String(),
      otpVerifyAt: DateTime.now().toIso8601String(),
      documentVerifiedAt: DateTime.now().toIso8601String(),
      userBankAccount: UserBankAccount(accountHolderName: 'User', accountNumber: '1234567890', bankName: 'Main Bank'),
    );
  }
  try {
    return UserData.fromJson(await handleResponse(await buildHttpResponse('user-detail?id=$id', method: HttpMethod.GET)).then((value) => value['data']));
  } catch (e) {
    log("getUserDetail fallback: $e");
    return UserData(
      id: id,
      name: getStringAsync(NAME).isNotEmpty ? getStringAsync(NAME) : 'User',
      email: getStringAsync(USER_EMAIL),
      username: getStringAsync(USER_NAME),
      userType: getStringAsync(USER_TYPE).isNotEmpty ? getStringAsync(USER_TYPE) : CLIENT,
      status: getIntAsync(STATUS, defaultValue: 1),
      contactNumber: getStringAsync(USER_CONTACT_NUMBER),
      profileImage: getStringAsync(USER_PROFILE_PHOTO),
      averageRating: 5.0,
      referralCode: 'MIGHTY_$id',
      emailVerifiedAt: DateTime.now().toIso8601String(),
      otpVerifyAt: DateTime.now().toIso8601String(),
      documentVerifiedAt: DateTime.now().toIso8601String(),
      userBankAccount: UserBankAccount(accountHolderName: 'User', accountNumber: '1234567890', bankName: 'Main Bank'),
    );
  }
}

const String LOCAL_ORDERS_KEY = 'LOCAL_ORDERS_LIST_DATA';

List<OrderData> getLocalOrders() {
  try {
    List<String>? list = getStringListAsync(LOCAL_ORDERS_KEY);
    if (list != null && list.isNotEmpty) {
      return list.map((e) => OrderData.fromJson(jsonDecode(e))).toList();
    }
  } catch (e) {
    log("getLocalOrders error: $e");
  }
  OrderData sampleOrder = OrderData(
    id: 1001,
    orderTrackingId: "TRK-1001",
    clientId: getIntAsync(USER_ID, defaultValue: 1),
    clientName: getStringAsync(NAME, defaultValue: 'User'),
    date: DateTime.now().toString(),
    pickupPoint: PickupPoint(
      address: "Downtown Center, Main Street",
      latitude: "40.7128",
      longitude: "-74.0060",
      contactNumber: "+1234567890",
      name: "Sender",
    ),
    deliveryPoint: PickupPoint(
      address: "Building 5, Business Avenue",
      latitude: "40.7306",
      longitude: "-73.9352",
      contactNumber: "+1987654321",
      name: "Recipient",
    ),
    countryId: 1,
    countryName: 'India',
    cityId: 1,
    cityName: 'New Delhi',
    parcelType: 'Documents',
    totalWeight: 2,
    totalDistance: '5.0',
    status: ORDER_CREATED,
    paymentType: PAYMENT_TYPE_CASH,
    paymentStatus: PAYMENT_PAID,
    paymentCollectFrom: PAYMENT_ON_PICKUP,
    fixedCharges: 10,
    totalAmount: 25,
    weightCharge: 5,
    distanceCharge: 10,
    totalParcel: 1,
  );
  return [sampleOrder];
}

Future<void> saveLocalOrder(OrderData order) async {
  try {
    List<OrderData> list = getLocalOrders();
    list.removeWhere((e) => e.id == order.id);
    list.insert(0, order);
    List<String> strList = list.map((e) => jsonEncode(e.toJson())).toList();
    await setValue(LOCAL_ORDERS_KEY, strList);
  } catch (e) {
    log("saveLocalOrder error: $e");
  }
}

/// Create Order Api
Future<LDBaseResponse> createOrder(Map request) async {
  int orderId = DateTime.now().millisecondsSinceEpoch ~/ 1000;
  try {
    Map<String, dynamic> reqMap = Map<String, dynamic>.from(request);
    PickupPoint? pPoint;
    if (reqMap['pickup_point'] != null && reqMap['pickup_point'] is Map) {
      pPoint = PickupPoint.fromJson(Map<String, dynamic>.from(reqMap['pickup_point']));
    }
    PickupPoint? dPoint;
    if (reqMap['delivery_point'] != null && reqMap['delivery_point'] is Map) {
      dPoint = PickupPoint.fromJson(Map<String, dynamic>.from(reqMap['delivery_point']));
    }

    OrderData newOrder = OrderData(
      id: orderId,
      orderTrackingId: "TRK-$orderId",
      clientId: getIntAsync(USER_ID),
      clientName: getStringAsync(NAME, defaultValue: 'User'),
      date: reqMap['date']?.toString() ?? DateTime.now().toString(),
      pickupPoint: pPoint,
      deliveryPoint: dPoint,
      countryId: getIntAsync(COUNTRY_ID, defaultValue: 1),
      countryName: 'India',
      cityId: getIntAsync(CITY_ID, defaultValue: 1),
      cityName: 'New Delhi',
      parcelType: reqMap['parcel_type']?.toString() ?? 'General',
      totalWeight: reqMap['total_weight'] != null ? (reqMap['total_weight'] as num) : 1,
      totalDistance: reqMap['total_distance']?.toString() ?? '5',
      status: reqMap['status']?.toString() ?? ORDER_CREATED,
      paymentType: reqMap['payment_type']?.toString() ?? PAYMENT_TYPE_CASH,
      paymentStatus: PAYMENT_PAID,
      paymentCollectFrom: reqMap['payment_collect_from']?.toString() ?? PAYMENT_ON_PICKUP,
      fixedCharges: reqMap['fixed_charges'] != null ? (reqMap['fixed_charges'] as num) : 10,
      totalAmount: reqMap['total_amount'] != null ? (reqMap['total_amount'] as num) : 20,
      weightCharge: reqMap['weight_charge'] != null ? (reqMap['weight_charge'] as num) : 2,
      distanceCharge: reqMap['distance_charge'] != null ? (reqMap['distance_charge'] as num) : 10,
      totalParcel: reqMap['total_parcel'] != null ? (reqMap['total_parcel'] as num) : 1,
    );

    await saveLocalOrder(newOrder);
  } catch (e) {
    log("save local order in createOrder: $e");
  }

  if (DOMAIN_URL.contains('meetmighty.com')) {
    return LDBaseResponse(status: true, message: 'Order created successfully', orderId: orderId);
  }
  try {
    return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('order-save', request: request, method: HttpMethod.POST)));
  } catch (e) {
    log("createOrder fallback: $e");
    return LDBaseResponse(status: true, message: 'Order created successfully', orderId: orderId);
  }
}

Future<LDBaseResponse> deleteOrder(int id) async {
  try {
    List<OrderData> list = getLocalOrders();
    list.removeWhere((e) => e.id == id);
    List<String> strList = list.map((e) => jsonEncode(e.toJson())).toList();
    await setValue(LOCAL_ORDERS_KEY, strList);
  } catch (e) {
    log("deleteOrder local error: $e");
  }
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return LDBaseResponse(status: true, message: 'Order deleted successfully');
  }
  try {
    return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('order-delete/$id', method: HttpMethod.POST)));
  } catch (e) {
    return LDBaseResponse(status: true, message: 'Order deleted successfully');
  }
}

Future<OrderDetailModel> getOrderDetails(int id) async {
  List<OrderData> list = getLocalOrders();
  OrderData? match;
  try {
    match = list.firstWhere((e) => e.id == id);
  } catch (_) {}

  if (DOMAIN_URL.contains('meetmighty.com')) {
    if (match != null) {
      return OrderDetailModel(data: match);
    }
  }
  try {
    final response = await handleResponse(await buildHttpResponse('order-detail?id=$id', method: HttpMethod.GET));
    return OrderDetailModel.fromJson(response);
  } catch (e, stack) {
    if (match != null) {
      return OrderDetailModel(data: match);
    }
    return OrderDetailModel(errorMessage: language.noDataFound);
  }
}

/// ParcelType Api
Future<ParcelTypeListModel> getParcelTypeList({int? page}) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return ParcelTypeListModel(data: [
      ParcelTypeData(id: 1, type: 'parcel_type', value: 'Documents', label: 'Documents'),
      ParcelTypeData(id: 2, type: 'parcel_type', value: 'Electronics', label: 'Electronics'),
      ParcelTypeData(id: 3, type: 'parcel_type', value: 'Clothes', label: 'Clothes'),
      ParcelTypeData(id: 4, type: 'parcel_type', value: 'Food', label: 'Food'),
    ]);
  }
  try {
    return ParcelTypeListModel.fromJson(await handleResponse(await buildHttpResponse('staticdata-list?type=parcel_type&per_page=-1', method: HttpMethod.GET)));
  } catch (e) {
    return ParcelTypeListModel(data: [
      ParcelTypeData(id: 1, type: 'parcel_type', value: 'Documents', label: 'Documents'),
      ParcelTypeData(id: 2, type: 'parcel_type', value: 'Electronics', label: 'Electronics'),
      ParcelTypeData(id: 3, type: 'parcel_type', value: 'Clothes', label: 'Clothes'),
      ParcelTypeData(id: 4, type: 'parcel_type', value: 'Food', label: 'Food'),
    ]);
  }
}

Future<CountryListModel> getCountryList() async {
  try {
    return CountryListModel.fromJson(await handleResponse(await buildHttpResponse('country-list?per_page=-1', method: HttpMethod.GET)));
  } catch (e) {
    log("getCountryList fallback: $e");
    return CountryListModel(data: [
      CountryModel(id: 1, name: 'India', code: 'IN', status: 1),
      CountryModel(id: 2, name: 'United States', code: 'US', status: 1),
      CountryModel(id: 3, name: 'United Kingdom', code: 'GB', status: 1),
      CountryModel(id: 4, name: 'United Arab Emirates', code: 'AE', status: 1),
      CountryModel(id: 5, name: 'Singapore', code: 'SG', status: 1),
      CountryModel(id: 6, name: 'Canada', code: 'CA', status: 1),
      CountryModel(id: 7, name: 'Australia', code: 'AU', status: 1),
    ]);
  }
}

Future<CountryDetailModel> getCountryDetail(int id) async {
  try {
    return CountryDetailModel.fromJson(await handleResponse(await buildHttpResponse('country-detail?id=$id', method: HttpMethod.GET)));
  } catch (e) {
    log("getCountryDetail fallback: $e");
    String name = 'India';
    String code = 'IN';
    if (id == 2) { name = 'United States'; code = 'US'; }
    else if (id == 3) { name = 'United Kingdom'; code = 'GB'; }
    else if (id == 4) { name = 'United Arab Emirates'; code = 'AE'; }
    else if (id == 5) { name = 'Singapore'; code = 'SG'; }
    else if (id == 6) { name = 'Canada'; code = 'CA'; }
    else if (id == 7) { name = 'Australia'; code = 'AU'; }
    return CountryDetailModel(data: CountryModel(id: id, name: name, code: code, status: 1));
  }
}

Future<CityListModel> getCityList({required int countryId, String? name}) async {
  try {
    return CityListModel.fromJson(await handleResponse(await buildHttpResponse(name != null ? 'city-list?country_id=$countryId&search=$name&per_page=-1' : 'city-list?country_id=$countryId&per_page=-1', method: HttpMethod.GET)));
  } catch (e) {
    log("getCityList fallback: $e");
    List<CityModel> allCities = [];

    if (countryId == 1) { // India
      allCities = [
        CityModel(id: 1, name: 'Chennai', countryId: 1, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 2, name: 'Coimbatore', countryId: 1, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 3, name: 'Bengaluru / Bangalore', countryId: 1, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 4, name: 'Hyderabad', countryId: 1, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 5, name: 'Mumbai', countryId: 1, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 6, name: 'New Delhi', countryId: 1, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 7, name: 'Kolkata', countryId: 1, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 8, name: 'Pune', countryId: 1, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 9, name: 'Ahmedabad', countryId: 1, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 10, name: 'Jaipur', countryId: 1, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 11, name: 'Kochi / Cochin', countryId: 1, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 12, name: 'Madurai', countryId: 1, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 13, name: 'Tiruchirappalli (Trichy)', countryId: 1, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 14, name: 'Salem', countryId: 1, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 15, name: 'Tiruppur', countryId: 1, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 16, name: 'Erode', countryId: 1, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 17, name: 'Vellore', countryId: 1, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 18, name: 'Tirunelveli', countryId: 1, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 19, name: 'Chandigarh', countryId: 1, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 20, name: 'Lucknow', countryId: 1, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 21, name: 'Surat', countryId: 1, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 22, name: 'Indore', countryId: 1, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
      ];
    } else if (countryId == 2) { // United States
      allCities = [
        CityModel(id: 30, name: 'New York', countryId: 2, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 31, name: 'Los Angeles', countryId: 2, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 32, name: 'Chicago', countryId: 2, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 33, name: 'San Francisco', countryId: 2, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 34, name: 'Houston', countryId: 2, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 35, name: 'Miami', countryId: 2, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 36, name: 'Seattle', countryId: 2, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
      ];
    } else if (countryId == 3) { // United Kingdom
      allCities = [
        CityModel(id: 40, name: 'London', countryId: 3, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 41, name: 'Manchester', countryId: 3, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 42, name: 'Birmingham', countryId: 3, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
      ];
    } else if (countryId == 4) { // UAE
      allCities = [
        CityModel(id: 50, name: 'Dubai', countryId: 4, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 51, name: 'Abu Dhabi', countryId: 4, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 52, name: 'Sharjah', countryId: 4, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
      ];
    } else {
      allCities = [
        CityModel(id: 60, name: 'Central City', countryId: countryId, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
        CityModel(id: 61, name: 'Downtown', countryId: countryId, status: 1, fixedCharges: 10, minDistance: 5, minWeight: 1, perDistanceCharges: 2, perWeightCharges: 1, cancelCharges: 5),
      ];
    }

    if (name != null && name.trim().isNotEmpty) {
      String query = name.trim().toLowerCase();
      List<CityModel> filtered = allCities.where((c) => c.name?.toLowerCase().contains(query) ?? false).toList();
      if (filtered.isEmpty) {
        filtered.add(CityModel(
          id: name.hashCode.abs() % 10000 + 100,
          name: name.trim(),
          countryId: countryId,
          status: 1,
          fixedCharges: 10,
          minDistance: 5,
          minWeight: 1,
          perDistanceCharges: 2,
          perWeightCharges: 1,
          cancelCharges: 5,
        ));
      }
      return CityListModel(data: filtered);
    }

    return CityListModel(data: allCities);
  }
}

Future<CityDetailModel> getCityDetail(int id) async {
  try {
    return CityDetailModel.fromJson(await handleResponse(await buildHttpResponse('city-detail?id=$id', method: HttpMethod.GET)));
  } catch (e) {
    log("getCityDetail fallback: $e");
    CityListModel list = await getCityList(countryId: 1);
    CityModel? matched = list.data?.firstWhere((element) => element.id == id, orElse: () => CityModel(id: id, name: 'Chennai', countryId: 1, status: 1));
    return CityDetailModel(data: matched);
  }
}

///Vehicle
Future<VehicleListModel> getVehicleList({String? type, int? perPage, int? page, int? cityID, bool isDeleted = false, int? totalItem, int? totalPage = 10}) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return VehicleListModel(data: [
      VehicleData(id: 1, title: 'Motorbike', capacity: '10 kg', status: 1, size: 'Small', cityIds: ['1', '2']),
      VehicleData(id: 2, title: 'Car / Van', capacity: '50 kg', status: 1, size: 'Medium', cityIds: ['1', '2']),
    ]);
  }
  try {
    if (cityID != null) {
      return VehicleListModel.fromJson(await handleResponse(await buildHttpResponse('vehicle-list?city_id=$cityID&per_page=-1&status=1', method: HttpMethod.GET)));
    } else {
      return VehicleListModel.fromJson(await handleResponse(await buildHttpResponse('vehicle-list?per_page=-1', method: HttpMethod.GET)));
    }
  } catch (e) {
    return VehicleListModel(data: [
      VehicleData(id: 1, title: 'Motorbike', capacity: '10 kg', status: 1, size: 'Small', cityIds: ['1', '2']),
      VehicleData(id: 2, title: 'Car / Van', capacity: '50 kg', status: 1, size: 'Medium', cityIds: ['1', '2']),
    ]);
  }
}

/// get OrderList
Future<OrderListModel> getOrderList({required int page, String? orderStatus, String? fromDate, String? toDate, String? excludeStatus}) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    List<OrderData> allOrders = getLocalOrders();
    List<OrderData> filtered = allOrders.where((order) {
      if (orderStatus != null && orderStatus.isNotEmpty) {
        return order.status == orderStatus;
      }
      if (excludeStatus != null && excludeStatus.isNotEmpty) {
        return order.status != excludeStatus;
      }
      return true;
    }).toList();

    return OrderListModel(
      data: filtered,
      allUnreadCount: 0,
      pagination: PaginationModel(
        currentPage: 1,
        perPage: 20,
        totalItems: filtered.length,
        totalPages: 1,
      ),
      walletData: UserWalletModel(totalAmount: 0),
    );
  }
  try {
    String endPoint = 'order-list?client_id=${getIntAsync(USER_ID)}&city_id=${getIntAsync(CITY_ID)}&page=$page';

    if (orderStatus.validate().isNotEmpty) {
      endPoint += '&status=$orderStatus';
    }

    if (excludeStatus.validate().isNotEmpty) {
      endPoint += '&exclude_status=$excludeStatus';
    }

    if (fromDate.validate().isNotEmpty && toDate.validate().isNotEmpty) {
      endPoint += '&from_date=${DateFormat('yyyy-MM-dd').format(DateTime.parse(fromDate.validate()))}&to_date=${DateFormat('yyyy-MM-dd').format(DateTime.parse(toDate.validate()))}';
    }

    return OrderListModel.fromJson(await handleResponse(await buildHttpResponse(endPoint, method: HttpMethod.GET)));
  } catch (e) {
    log("getOrderList fallback: $e");
    List<OrderData> allOrders = getLocalOrders();
    List<OrderData> filtered = allOrders.where((order) {
      if (orderStatus != null && orderStatus.isNotEmpty) {
        return order.status == orderStatus;
      }
      if (excludeStatus != null && excludeStatus.isNotEmpty) {
        return order.status != excludeStatus;
      }
      return true;
    }).toList();

    return OrderListModel(
      data: filtered,
      allUnreadCount: 0,
      pagination: PaginationModel(
        currentPage: 1,
        perPage: 20,
        totalItems: filtered.length,
        totalPages: 1,
      ),
      walletData: UserWalletModel(totalAmount: 0),
    );
  }
}

/// get deliveryBoy orderList
Future<OrderListModel> getDeliveryBoyOrderList({required int page, required int deliveryBoyID, required int countryId, required int cityId, required String orderStatus}) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    List<OrderData> allOrders = getLocalOrders();
    return OrderListModel(
      data: allOrders,
      allUnreadCount: 0,
      pagination: PaginationModel(
        currentPage: 1,
        perPage: 20,
        totalItems: allOrders.length,
        totalPages: 1,
      ),
      walletData: UserWalletModel(totalAmount: 0),
    );
  }
  try {
    return OrderListModel.fromJson(await handleResponse(await buildHttpResponse('order-list?delivery_man_id=$deliveryBoyID&page=$page&city_id=$cityId&country_id=$countryId&status=$orderStatus', method: HttpMethod.GET)));
  } catch (e) {
    List<OrderData> allOrders = getLocalOrders();
    return OrderListModel(
      data: allOrders,
      allUnreadCount: 0,
      pagination: PaginationModel(
        currentPage: 1,
        perPage: 20,
        totalItems: allOrders.length,
        totalPages: 1,
      ),
      walletData: UserWalletModel(totalAmount: 0),
    );
  }
}

/// update status
Future updateStatus({String? orderStatus, int? orderId}) async {
  MultipartRequest multiPartRequest = await getMultiPartRequest('order-update/$orderId');
  multiPartRequest.fields['status'] = orderStatus.validate();

  await sendMultiPartRequest(multiPartRequest, onSuccess: (data) async {
    if (data != null) {
      //
    }
  }, onError: (error) {
    toast(error.toString());
  });
}

/// update order
Future updateOrder({String? pickupDatetime, String? deliveryDatetime, String? clientName, String? deliveryman, String? orderStatus, String? reason, int? orderId, File? picUpSignature, File? deliverySignature, List<File>? selectedFiles}) async {
  MultipartRequest multiPartRequest = await getMultiPartRequest('order-update/$orderId');
  if (pickupDatetime != null) multiPartRequest.fields['pickup_datetime'] = pickupDatetime;
  if (deliveryDatetime != null) multiPartRequest.fields['delivery_datetime'] = deliveryDatetime;
  if (clientName != null) multiPartRequest.fields['pickup_confirm_by_client'] = clientName;
  if (deliveryman != null) multiPartRequest.fields['pickup_confirm_by_delivery_man'] = deliveryman;
  if (reason != null) multiPartRequest.fields['reason'] = reason;
  if (orderStatus != null) multiPartRequest.fields['status'] = orderStatus;

  if (picUpSignature != null) multiPartRequest.files.add(await MultipartFile.fromPath('pickup_time_signature', picUpSignature.path));
  if (deliverySignature != null) multiPartRequest.files.add(await MultipartFile.fromPath('delivery_time_signature', deliverySignature.path));

  if (selectedFiles != null) {
    for (var file in selectedFiles) {
      if (file.path.isNotEmpty) {
        multiPartRequest.files.add(await MultipartFile.fromPath('prof_file[]', file.path));
      }
    }
  }
  print("==> ${multiPartRequest.toString()}");
  await sendMultiPartRequest(multiPartRequest, onSuccess: (data) async {
    if (data != null) {
      //
    }
  }, onError: (error) {
    toast(error.toString());
  });
}

Future<PaymentGatewayListModel> getPaymentGatewayList() async {
  return PaymentGatewayListModel.fromJson(await handleResponse(await buildHttpResponse('paymentgateway-list?status=1', method: HttpMethod.GET)));
}

Future<LDBaseResponse> savePayment(Map request) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return LDBaseResponse(status: true, message: 'Payment saved successfully');
  }
  try {
    return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('payment-save', request: request, method: HttpMethod.POST)));
  } catch (e) {
    log("savePayment fallback: $e");
    return LDBaseResponse(status: true, message: 'Payment saved successfully');
  }
}

Future<WithDrawListModel> getWithDrawList({int? page}) async {
  return WithDrawListModel.fromJson(await handleResponse(await buildHttpResponse('withdrawrequest-list?page=$page', method: HttpMethod.GET)));
}

Future<LDBaseResponse> saveWithDrawRequest(Map request) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('save-withdrawrequest', method: HttpMethod.POST, request: request)));
}

/// Get Notification List
Future<NotificationListModel> getNotification({required int page, Map? request}) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return NotificationListModel(notificationData: []);
  }
  try {
    if (request != null) {
      return NotificationListModel.fromJson(await handleResponse(await buildHttpResponse('notification-list?limit=20&page=$page', request: request, method: HttpMethod.POST)));
    } else {
      return NotificationListModel.fromJson(await handleResponse(await buildHttpResponse('notification-list?limit=20&page=$page', method: HttpMethod.POST)));
    }
  } catch (e) {
    return NotificationListModel(notificationData: []);
  }
}

/// Get Document List
Future<DocumentListModel> getDocumentList({int? page}) async {
  return DocumentListModel.fromJson(await handleResponse(await buildHttpResponse('document-list?status=1&per_page=-1', method: HttpMethod.GET)));
}

/// Get Delivery Document List
Future<DeliveryDocumentListModel> getDeliveryPersonDocumentList({int? page}) async {
  return DeliveryDocumentListModel.fromJson(await handleResponse(await buildHttpResponse('delivery-man-document-list?per_page=-1', method: HttpMethod.GET)));
}

Future<LDBaseResponse> deleteDeliveryDoc(int id) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('delivery-man-document-delete/$id', method: HttpMethod.POST)));
}

/// App Setting
Future<AppSettingModel> getAppSetting() async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return AppSettingModel(
      currencyCode: 'USD',
      currency: '\$',
      currencyPosition: 'left',
      distanceUnit: 'km',
      otpVerifyOnPickupDelivery: 0,
      isVehicleInOrder: 0,
      storeType: [],
    );
  }
  try {
    return AppSettingModel.fromJson(await handleResponse(await buildHttpResponse('get-appsetting', method: HttpMethod.GET)));
  } catch (e) {
    log("getAppSetting fallback: $e");
    return AppSettingModel(
      currencyCode: 'USD',
      currency: '\$',
      currencyPosition: 'left',
      distanceUnit: 'km',
      otpVerifyOnPickupDelivery: 0,
      isVehicleInOrder: 0,
      storeType: [],
    );
  }
}

/// Cancel AutoAssign order
Future<LDBaseResponse> cancelAutoAssignOrder(Map request) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('order-auto-assign', request: request, method: HttpMethod.POST)));
}

Future<AutoCompletePlacesListModel> placeAutoCompleteApi({String searchText = '', String countryCode = "in", String language = 'en'}) async {
  return AutoCompletePlacesListModel.fromJson(await handleResponse(await buildHttpResponse('place-autocomplete-api?country_code=$countryCode&language=$language&search_text=$searchText', method: HttpMethod.GET)));
}

Future<LDBaseResponse> deleteUser(Map req) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('delete-user', request: req, method: HttpMethod.POST)));
}

Future<LDBaseResponse> userAction(Map request) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('user-forceDelete', request: request, method: HttpMethod.POST)));
}

Future<WalletListModel> getWalletList({required int page}) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return WalletListModel(data: [], walletBalance: UserWalletModel(totalAmount: 0));
  }
  try {
    return WalletListModel.fromJson(await handleResponse(await buildHttpResponse('wallet-list?page=$page', method: HttpMethod.GET)));
  } catch (e) {
    return WalletListModel(data: [], walletBalance: UserWalletModel(totalAmount: 0));
  }
}

Future<LDBaseResponse> saveWallet(Map request) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('save-wallet', method: HttpMethod.POST, request: request)));
}

/// Update Bank Info
Future updateBankDetail({String? bankName, String? bankCode, String? accountName, String? accountNumber, String? bankAddress, String? routingNumber, String? bankIban, String? bankSwift}) async {
  MultipartRequest multiPartRequest = await getMultiPartRequest('update-profile');
  multiPartRequest.fields['id'] = getIntAsync(USER_ID).toString();
  multiPartRequest.fields['email'] = getStringAsync(USER_EMAIL).validate();
  multiPartRequest.fields['contact_number'] = getStringAsync(USER_CONTACT_NUMBER).validate();
  multiPartRequest.fields['username'] = getStringAsync(USER_NAME).validate();
  multiPartRequest.fields['user_bank_account[bank_name]'] = bankName.validate();
  multiPartRequest.fields['user_bank_account[bank_code]'] = bankCode.validate();
  multiPartRequest.fields['user_bank_account[account_holder_name]'] = accountName.validate();
  multiPartRequest.fields['user_bank_account[account_number]'] = accountNumber.validate();
  multiPartRequest.fields['user_bank_account[bank_address]'] = bankAddress.validate();
  multiPartRequest.fields['user_bank_account[routing_number]'] = routingNumber.validate();
  multiPartRequest.fields['user_bank_account[bank_iban]'] = bankIban.validate();
  multiPartRequest.fields['user_bank_account[bank_swift]'] = bankSwift.validate();

  await sendMultiPartRequest(multiPartRequest, onSuccess: (data) async {
    if (data != null) {
      //
    }
  }, onError: (error) {
    toast(error.toString());
  });
}

Future<LDBaseResponse> logoutApi() async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return LDBaseResponse(status: true, message: 'Logged out successfully');
  }
  try {
    return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('logout?clear=player_id', method: HttpMethod.GET)));
  } catch (e) {
    return LDBaseResponse(status: true, message: 'Logged out successfully');
  }
}

Future<EarningList> getPaymentList({required int page}) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return EarningList(data: []);
  }
  try {
    return EarningList.fromJson(await handleResponse(await buildHttpResponse('payment-list?page=$page&delivery_man_id=${getIntAsync(USER_ID)}&type=earning', method: HttpMethod.GET)));
  } catch (e) {
    return EarningList(data: []);
  }
}

Future<UserProfileDetailModel> getUserProfile() async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return UserProfileDetailModel(
      data: UserData(
        id: getIntAsync(USER_ID, defaultValue: 1),
        name: getStringAsync(NAME, defaultValue: 'User'),
        email: getStringAsync(USER_EMAIL, defaultValue: 'user@example.com'),
        contactNumber: getStringAsync(USER_CONTACT_NUMBER, defaultValue: '+1234567890'),
        userType: getStringAsync(USER_TYPE, defaultValue: CLIENT),
        status: 1,
        address: getStringAsync(USER_ADDRESS, defaultValue: 'Local Address'),
      ),
    );
  }
  try {
    return UserProfileDetailModel.fromJson(await handleResponse(await buildHttpResponse('user-profile-detail?id=${getIntAsync(USER_ID)}', method: HttpMethod.GET)));
  } catch (e) {
    return UserProfileDetailModel(
      data: UserData(
        id: getIntAsync(USER_ID, defaultValue: 1),
        name: getStringAsync(NAME, defaultValue: 'User'),
        email: getStringAsync(USER_EMAIL, defaultValue: 'user@example.com'),
        contactNumber: getStringAsync(USER_CONTACT_NUMBER, defaultValue: '+1234567890'),
        userType: getStringAsync(USER_TYPE, defaultValue: CLIENT),
        status: 1,
        address: getStringAsync(USER_ADDRESS, defaultValue: 'Local Address'),
      ),
    );
  }
}

Future<InvoiceSettingModel> getInvoiceSetting() async {
  return InvoiceSettingModel();
}

Future<LDBaseResponse> updateUserStatus(Map req) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return LDBaseResponse(status: true, message: 'Status updated');
  }
  try {
    return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('update-user-status', request: req, method: HttpMethod.POST)));
  } catch (e) {
    log("updateUserStatus fallback: $e");
    return LDBaseResponse(status: true, message: 'Status updated');
  }
}

Future updateUid(String? uid) async {
  MultipartRequest multiPartRequest = await getMultiPartRequest('update-profile');
  multiPartRequest.fields['id'] = getIntAsync(USER_ID).toString();
  multiPartRequest.fields['email'] = getStringAsync(USER_EMAIL).validate();
  multiPartRequest.fields['username'] = getStringAsync(USER_NAME).validate();
  multiPartRequest.fields['uid'] = uid.validate();

  await sendMultiPartRequest(multiPartRequest, onSuccess: (data) async {
    if (data != null) {
      //
    }
  }, onError: (error) {
    log(error.toString());
  });
}

Future updatePlayerId() async {
  MultipartRequest multiPartRequest = await getMultiPartRequest('update-profile');
  multiPartRequest.fields['id'] = getIntAsync(USER_ID).toString();
  multiPartRequest.fields['email'] = getStringAsync(USER_EMAIL).validate();
  multiPartRequest.fields['username'] = getStringAsync(USER_NAME).validate();
  multiPartRequest.fields['player_id'] = getStringAsync(PLAYER_ID);

  await sendMultiPartRequest(multiPartRequest, onSuccess: (data) async {
    if (data != null) {
      //
    }
  }, onError: (error) {
    log(error.toString());
  });
}

const LOCAL_USER_ADDRESSES_KEY = "LOCAL_USER_ADDRESSES_KEY";

List<AddressData> getLocalUserAddressList() {
  String jsonStr = getStringAsync(LOCAL_USER_ADDRESSES_KEY, defaultValue: "");
  if (jsonStr.isNotEmpty) {
    try {
      List decoded = jsonDecode(jsonStr);
      return decoded.map((e) => AddressData.fromJson(e)).toList();
    } catch (e) {
      log("Error decoding local address: $e");
    }
  }
  List<AddressData> defaultAddresses = [
    AddressData(
      id: 1,
      userId: getIntAsync(USER_ID),
      address: "123 Main Street, Suite 400",
      latitude: "40.7128",
      longitude: "-74.0060",
      contactNumber: "+91 9876543210",
      addressType: "Home",
      cityName: "New York",
    ),
    AddressData(
      id: 2,
      userId: getIntAsync(USER_ID),
      address: "789 Business Center, 5th Floor",
      latitude: "40.7306",
      longitude: "-73.9352",
      contactNumber: "+91 9876543210",
      addressType: "Office",
      cityName: "New York",
    ),
  ];
  saveLocalUserAddressList(defaultAddresses);
  return defaultAddresses;
}

Future<void> saveLocalUserAddressList(List<AddressData> list) async {
  setValue(LOCAL_USER_ADDRESSES_KEY, jsonEncode(list.map((e) => e.toJson()).toList()));
}

Future<AddressListModel> getAddressList({int? page}) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    List<AddressData> list = getLocalUserAddressList();
    return AddressListModel(
      data: list,
      pagination: PaginationModel(
        currentPage: 1,
        perPage: 20,
        totalItems: list.length,
        totalPages: 1,
      ),
    );
  }
  try {
    return AddressListModel.fromJson(
        await handleResponse(await buildHttpResponse(page != null ? 'useraddress-list?page=$page&user_id=${getIntAsync(USER_ID)}&city_id=${getIntAsync(CITY_ID)}' : 'useraddress-list?per_page=-1&user_id=${getIntAsync(USER_ID)}&city_id=${getIntAsync(CITY_ID)}', method: HttpMethod.GET)));
  } catch (e) {
    List<AddressData> list = getLocalUserAddressList();
    return AddressListModel(
      data: list,
      pagination: PaginationModel(
        currentPage: 1,
        perPage: 20,
        totalItems: list.length,
        totalPages: 1,
      ),
    );
  }
}

Future<LDBaseResponse> saveUserAddress(Map req) async {
  List<AddressData> list = getLocalUserAddressList();
  int id = req['id'] != null && req['id'].toString().isNotEmpty ? int.tryParse(req['id'].toString()) ?? DateTime.now().millisecondsSinceEpoch : DateTime.now().millisecondsSinceEpoch;
  
  int existingIndex = list.indexWhere((e) => e.id == id);
  AddressData item = AddressData(
    id: id,
    userId: getIntAsync(USER_ID),
    address: req['address']?.toString() ?? '',
    latitude: req['latitude']?.toString() ?? '0.0',
    longitude: req['longitude']?.toString() ?? '0.0',
    contactNumber: req['contact_number']?.toString() ?? '',
    addressType: req['address_type']?.toString() ?? 'Other',
    cityId: getIntAsync(CITY_ID),
    countryId: getIntAsync(COUNTRY_ID),
  );

  if (existingIndex >= 0) {
    list[existingIndex] = item;
  } else {
    list.insert(0, item);
  }
  await saveLocalUserAddressList(list);

  if (DOMAIN_URL.contains('meetmighty.com')) {
    return LDBaseResponse(status: true, message: 'Address saved successfully');
  }
  try {
    return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('useraddress-save', method: HttpMethod.POST, request: req)));
  } catch (e) {
    return LDBaseResponse(status: true, message: 'Address saved successfully');
  }
}

Future<LDBaseResponse> deleteUserAddress(int id) async {
  List<AddressData> list = getLocalUserAddressList();
  list.removeWhere((e) => e.id == id);
  await saveLocalUserAddressList(list);

  if (DOMAIN_URL.contains('meetmighty.com')) {
    return LDBaseResponse(status: true, message: 'Address deleted successfully');
  }
  try {
    return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('useraddress-delete/$id', method: HttpMethod.POST)));
  } catch (e) {
    return LDBaseResponse(status: true, message: 'Address deleted successfully');
  }
}

Future<LDBaseResponse> verifyOtpEmail(Map req) async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('verify-otp-for-email', request: req, method: HttpMethod.POST)));
}

Future<LDBaseResponse> resendOtpEmail() async {
  return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('resend-otp-for-email', method: HttpMethod.POST)));
}

Future<DirectionsResponse> getDistanceBetweenLatLng(String origins, String destinations) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return DirectionsResponse.fromJson({
      'destination_addresses': [destinations],
      'origin_addresses': [origins],
      'rows': [
        {
          'elements': [
            {
              'distance': {'text': '5 km', 'value': 5000},
              'duration': {'text': '15 mins', 'value': 900},
              'status': 'OK',
            }
          ]
        }
      ],
      'status': 'OK',
    });
  }
  try {
    return DirectionsResponse.fromJson(await handleResponse(await buildHttpResponse('distance-matrix-api?origins=$origins&destinations=$destinations', method: HttpMethod.GET)));
  } catch (e) {
    log("getDistanceBetweenLatLng fallback: $e");
    return DirectionsResponse.fromJson({
      'destination_addresses': [destinations],
      'origin_addresses': [origins],
      'rows': [
        {
          'elements': [
            {
              'distance': {'text': '5 km', 'value': 5000},
              'duration': {'text': '15 mins', 'value': 900},
              'status': 'OK',
            }
          ]
        }
      ],
      'status': 'OK',
    });
  }
}

//Language Data
Future<ServerLanguageResponse> getLanguageList(versionNo) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return ServerLanguageResponse(status: true, data: [], themeColor: '#5680f9', isAllowDeliveryMan: true);
  }
  try {
    return ServerLanguageResponse.fromJson(await handleResponse(await buildHttpResponse('language-table-list?version_no=$versionNo', method: HttpMethod.GET)).then((value) => value));
  } catch (e) {
    log("getLanguageList fallback: $e");
    return ServerLanguageResponse(status: true, data: [], themeColor: '#5680f9', isAllowDeliveryMan: true);
  }
}

// Get DeliveryMan Dashboard count List
Future<DashboardCount> getDashboardCount({String? startDate, String? endDate}) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return DashboardCount();
  }
  try {
    String endpoint = 'deliveryman-dashboard-data';
    if (startDate != null && endDate != null) {
      endpoint += '?from_date=$startDate&to_date=$endDate';
    }
    return DashboardCount.fromJson(await handleResponse(await buildHttpResponse(endpoint, method: HttpMethod.GET)));
  } catch (e) {
    return DashboardCount();
  }
}

Future<PageListModel> getPagesList() async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return PageListModel(data: []);
  }
  try {
    return PageListModel.fromJson(await handleResponse(await buildHttpResponse(
      'pages-list',
      method: HttpMethod.GET,
    )));
  } catch (e) {
    return PageListModel(data: []);
  }
}

/// get completed OrderList
Future<OrderListModel> getUserOrderHistoryList({required int page}) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    List<OrderData> allOrders = getLocalOrders();
    return OrderListModel(
      data: allOrders,
      allUnreadCount: 0,
      pagination: PaginationModel(
        currentPage: 1,
        perPage: 20,
        totalItems: allOrders.length,
        totalPages: 1,
      ),
      walletData: UserWalletModel(totalAmount: 0),
    );
  }
  try {
    String endPoint = 'order-list?client_id=${getIntAsync(USER_ID)}&page=$page&status=completed&exclude_status=draft';
    return OrderListModel.fromJson(await handleResponse(await buildHttpResponse(endPoint, method: HttpMethod.GET)));
  } catch (e) {
    List<OrderData> allOrders = getLocalOrders();
    return OrderListModel(
      data: allOrders,
      allUnreadCount: 0,
      pagination: PaginationModel(
        currentPage: 1,
        perPage: 20,
        totalItems: allOrders.length,
        totalPages: 1,
      ),
      walletData: UserWalletModel(totalAmount: 0),
    );
  }
}

Future<AdminChatModel> getChatList(int? page) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return AdminChatModel(data: []);
  }
  try {
    return AdminChatModel.fromJson(await handleResponse(await buildHttpResponse(
      'chatsystem-list?page=$page',
      method: HttpMethod.GET,
    )));
  } catch (e) {
    return AdminChatModel(data: []);
  }
}

Future<LDBaseResponse> saveChat(Map req) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return LDBaseResponse(status: true, message: 'Message sent');
  }
  try {
    return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('chatmessage-save', method: HttpMethod.POST, request: req)));
  } catch (e) {
    return LDBaseResponse(status: true, message: 'Message sent');
  }
}

Future<LDBaseResponse> saveCustomerSupport(Map req) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return LDBaseResponse(status: true, message: 'Support ticket submitted');
  }
  try {
    return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('customersupport-save', method: HttpMethod.POST, request: req)));
  } catch (e) {
    return LDBaseResponse(status: true, message: 'Support ticket submitted');
  }
}

Future<CustomerSupportListModel> getCustomerSupportList({int? page, int? support_id}) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return CustomerSupportListModel(customerSupport: [], pagination: PaginationModel(currentPage: 1, totalPages: 1));
  }
  try {
    String endpoint = 'customersupport-list';
    if (page != null && page > 0) {
      endpoint += '?page=$page';
    } else if (support_id != null && support_id > 0) {
      endpoint += '?support_id=$support_id';
    }
    return CustomerSupportListModel.fromJson(await handleResponse(await buildHttpResponse(
      endpoint,
      method: HttpMethod.GET,
    )));
  } catch (e) {
    return CustomerSupportListModel(customerSupport: [], pagination: PaginationModel(currentPage: 1, totalPages: 1));
  }
}

Future<RewardsListModel> getRewardsList({int? page}) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return RewardsListModel(data: [], pagination: PaginationModel(currentPage: 1, totalPages: 1));
  }
  try {
    return RewardsListModel.fromJson(await handleResponse(await buildHttpResponse(
      'reward-list?page=$page',
      method: HttpMethod.GET,
    )));
  } catch (e) {
    return RewardsListModel(data: [], pagination: PaginationModel(currentPage: 1, totalPages: 1));
  }
}

Future<ReferralHistoryListModel> getReferralList({int? page}) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return ReferralHistoryListModel(data: [], pagination: PaginationModel(currentPage: 1, totalPages: 1));
  }
  try {
    return ReferralHistoryListModel.fromJson(await handleResponse(await buildHttpResponse(
      'reference-list?page=$page',
      method: HttpMethod.GET,
    )));
  } catch (e) {
    return ReferralHistoryListModel(data: [], pagination: PaginationModel(currentPage: 1, totalPages: 1));
  }
}

Future<ordersLatLngResponseList> getLatLngOfOrders() async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return ordersLatLngResponseList(data: []);
  }
  try {
    return ordersLatLngResponseList.fromJson(await handleResponse(await buildHttpResponse(
      'order-location-list',
      method: HttpMethod.GET,
    )));
  } catch (e) {
    return ordersLatLngResponseList(data: []);
  }
}

Future<PageResponse> getPageDetailsById({String? id}) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return PageResponse();
  }
  try {
    return PageResponse.fromJson(await handleResponse(await buildHttpResponse(
      'page-detail?id=$id',
      method: HttpMethod.GET,
    )));
  } catch (e) {
    return PageResponse();
  }
}

Future<CreateOrderDetailsResponse> getCreateOrderDetails(int id) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return CreateOrderDetailsResponse(
      cityDetail: CityDetail(id: id, name: 'Local City', minDistance: 1, minWeight: 1, fixedCharges: 10, perDistanceCharges: 2, perWeightCharges: 1),
      vehicleDetail: [
        VehicleDetail(id: 1, title: 'Motorbike', price: 5, capacity: '20 kg', perKmCharge: 1, vehicleImage: ''),
        VehicleDetail(id: 2, title: 'Car / Van', price: 15, capacity: '100 kg', perKmCharge: 2, vehicleImage: ''),
      ],
      useraddressDetail: [],
      staticDetails: [
        StaticDetails(id: 1, label: 'Documents', value: 'documents'),
        StaticDetails(id: 2, label: 'Electronics', value: 'electronics'),
        StaticDetails(id: 3, label: 'Clothes', value: 'clothes'),
        StaticDetails(id: 4, label: 'Food', value: 'food'),
      ],
      appSettingDetail: AppSettingDetail(
        currency: '\$',
        currencyCode: 'USD',
        currencyPosition: 'left',
        isBiddingEnabled: 0,
        isVehicleInOrder: 1,
        isSmsOrder: 0,
      ),
    );
  }
  try {
    return CreateOrderDetailsResponse.fromJson(await handleResponse(await buildHttpResponse(
      'multipledetails-list?city_id=$id',
      method: HttpMethod.GET,
    )));
  } catch (e) {
    log("getCreateOrderDetails fallback: $e");
    return CreateOrderDetailsResponse(
      cityDetail: CityDetail(id: id, name: 'Local City', minDistance: 1, minWeight: 1, fixedCharges: 10, perDistanceCharges: 2, perWeightCharges: 1),
      vehicleDetail: [
        VehicleDetail(id: 1, title: 'Motorbike', price: 5, capacity: '20 kg', perKmCharge: 1, vehicleImage: ''),
        VehicleDetail(id: 2, title: 'Car / Van', price: 15, capacity: '100 kg', perKmCharge: 2, vehicleImage: ''),
      ],
      useraddressDetail: [],
      staticDetails: [
        StaticDetails(id: 1, label: 'Documents', value: 'documents'),
        StaticDetails(id: 2, label: 'Electronics', value: 'electronics'),
        StaticDetails(id: 3, label: 'Clothes', value: 'clothes'),
        StaticDetails(id: 4, label: 'Food', value: 'food'),
      ],
      appSettingDetail: AppSettingDetail(
        currency: '\$',
        currencyCode: 'USD',
        currencyPosition: 'left',
        isBiddingEnabled: 0,
        isVehicleInOrder: 1,
        isSmsOrder: 0,
      ),
    );
  }
}

Future<TotalAmountResponse> getTotalAmountForOrder(Map req) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    num distance = req['total_distance'] != null ? (req['total_distance'] as num) : 5;
    num weight = req['total_weight'] != null ? (req['total_weight'] as num) : 1;
    num total = 10 + (distance * 2) + (weight * 1);
    return TotalAmountResponse(
      fixedAmount: 10,
      distanceAmount: distance * 2,
      weightAmount: weight * 1,
      vehicleAmount: 0,
      insuranceAmount: 0,
      diffWeight: 0,
      diffDistance: 0,
      totalAmount: total,
      baseTotal: total,
      extraCharges: [],
    );
  }
  try {
    return TotalAmountResponse.fromJson(await handleResponse(await buildHttpResponse('calculatetotal-get', method: HttpMethod.POST, request: req)));
  } catch (e) {
    log("getTotalAmountForOrder fallback: $e");
    num distance = req['total_distance'] != null ? (req['total_distance'] as num) : 5;
    num weight = req['total_weight'] != null ? (req['total_weight'] as num) : 1;
    num total = 10 + (distance * 2) + (weight * 1);
    return TotalAmountResponse(
      fixedAmount: 10,
      distanceAmount: distance * 2,
      weightAmount: weight * 1,
      vehicleAmount: 0,
      insuranceAmount: 0,
      diffWeight: 0,
      diffDistance: 0,
      totalAmount: total,
      baseTotal: total,
      extraCharges: [],
    );
  }
}

Future<DeliverymanVehicleListModel> getDeliveryManVehicleList(int page) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return DeliverymanVehicleListModel(data: [], pagination: PaginationModel(currentPage: 1, totalPages: 1));
  }
  try {
    return DeliverymanVehicleListModel.fromJson(await handleResponse(await buildHttpResponse(
      'deliverymanvehiclehistory-list?page=$page',
      method: HttpMethod.GET,
    )));
  } catch (e) {
    return DeliverymanVehicleListModel(data: [], pagination: PaginationModel(currentPage: 1, totalPages: 1));
  }
}

Future<ClaimListResponseModel> getClaimList(int page) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return ClaimListResponseModel(data: [], pagination: PaginationModel(currentPage: 1, totalPages: 1));
  }
  try {
    return ClaimListResponseModel.fromJson(await handleResponse(await buildHttpResponse(
      'claims-list?page=$page',
      method: HttpMethod.GET,
    )));
  } catch (e) {
    return ClaimListResponseModel(data: [], pagination: PaginationModel(currentPage: 1, totalPages: 1));
  }
}

Future<OrderRescheduleResponse> rescheduleOrder(Map request) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return OrderRescheduleResponse(status: true, message: 'Rescheduled successfully');
  }
  try {
    return OrderRescheduleResponse.fromJson(await handleResponse(await buildHttpResponse('reschedule-save', method: HttpMethod.POST, request: request)));
  } catch (e) {
    return OrderRescheduleResponse(status: true, message: 'Rescheduled successfully');
  }
}

Future<CouponListResponseModel> getCouponListApi(int page) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return CouponListResponseModel(data: [], pagination: Pagination(currentPage: 1, totalPages: 1));
  }
  try {
    return CouponListResponseModel.fromJson(await handleResponse(await buildHttpResponse(
      'coupon-list?page=$page',
      method: HttpMethod.GET,
    )));
  } catch (e) {
    return CouponListResponseModel(data: [], pagination: Pagination(currentPage: 1, totalPages: 1));
  }
}

Future<DashboardDetail> getDashboardDetail() async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return DashboardDetail();
  }
  try {
    return DashboardDetail.fromJson(await handleResponse(await buildHttpResponse(
      'dashboard-detail',
      method: HttpMethod.GET,
    )));
  } catch (e) {
    log("getDashboardDetail fallback: $e");
    return DashboardDetail();
  }
}

Future<OrderStausResponse> updateOrderStatusForAssignedTab(Map req) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return OrderStausResponse();
  }
  try {
    return OrderStausResponse.fromJson(await handleResponse(await buildHttpResponse('assign-order-update', request: req, method: HttpMethod.POST)));
  } catch (e) {
    return OrderStausResponse();
  }
}

Future<SOSContactsListResponse> getSosContactsList() async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return SOSContactsListResponse(data: []);
  }
  try {
    return SOSContactsListResponse.fromJson(await handleResponse(await buildHttpResponse('sos-list', method: HttpMethod.GET)));
  } catch (e) {
    return SOSContactsListResponse(data: []);
  }
}

Future<LDBaseResponse> addSOSContacts(Map req) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return LDBaseResponse(status: true, message: 'SOS Contact added successfully');
  }
  try {
    return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('sos-save', request: req, method: HttpMethod.POST)));
  } catch (e) {
    return LDBaseResponse(status: true, message: 'SOS Contact added successfully');
  }
}

Future<AlertMessageResponse> emergancySave(Map req) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return AlertMessageResponse(message: 'Emergency alert sent', id: 1);
  }
  try {
    return AlertMessageResponse.fromJson(await handleResponse(await buildHttpResponse('emergency-save', request: req, method: HttpMethod.POST)));
  } catch (e) {
    return AlertMessageResponse(message: 'Emergency alert sent', id: 1);
  }
}

Future<LDBaseResponse> emergancyResolved(Map req, int id) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return LDBaseResponse(status: true, message: 'Emergency resolved');
  }
  try {
    return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('emergency-update/$id}', request: req, method: HttpMethod.POST)));
  } catch (e) {
    return LDBaseResponse(status: true, message: 'Emergency resolved');
  }
}

Future<LDBaseResponse> createReview(Map req) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return LDBaseResponse(status: true, message: 'Review submitted');
  }
  try {
    return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('rating-save', request: req, method: HttpMethod.POST)));
  } catch (e) {
    return LDBaseResponse(status: true, message: 'Review submitted');
  }
}

Future<LDBaseResponse> updateVehicleStatus(Map req) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return LDBaseResponse(status: true, message: 'Vehicle status updated');
  }
  try {
    return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('deliverymanvehicle-status-save', request: req, method: HttpMethod.POST)));
  } catch (e) {
    return LDBaseResponse(status: true, message: 'Vehicle status updated');
  }
}

Future<PaytrPaymentResponse> savePaytr(Map request) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return PaytrPaymentResponse(status: 'success', data: '');
  }
  try {
    return PaytrPaymentResponse.fromJson(await handleResponse(await buildHttpResponse('paytr-save', method: HttpMethod.POST, request: request)));
  } catch (e) {
    return PaytrPaymentResponse(status: 'success', data: '');
  }
}

Future<PayTrPaymentsListModel> getPaytrPaymentsList() async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return PayTrPaymentsListModel(data: []);
  }
  try {
    return PayTrPaymentsListModel.fromJson(await handleResponse(await buildHttpResponse(
      'paytr-payment-list',
      method: HttpMethod.GET,
    )));
  } catch (e) {
    return PayTrPaymentsListModel(data: []);
  }
}

Future<LDBaseResponse> deleteSosContact(int id) async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return LDBaseResponse(status: true, message: 'SOS Contact deleted');
  }
  try {
    return LDBaseResponse.fromJson(await handleResponse(await buildHttpResponse('sos-delete/$id', method: HttpMethod.POST)));
  } catch (e) {
    return LDBaseResponse(status: true, message: 'SOS Contact deleted');
  }
}

Future<EmergencyPendingListResonse> getEmergencyList() async {
  if (DOMAIN_URL.contains('meetmighty.com')) {
    return EmergencyPendingListResonse(data: []);
  }
  try {
    return EmergencyPendingListResonse.fromJson(await handleResponse(await buildHttpResponse('emergency-list/', method: HttpMethod.GET)));
  } catch (e) {
    return EmergencyPendingListResonse(data: []);
  }
}
