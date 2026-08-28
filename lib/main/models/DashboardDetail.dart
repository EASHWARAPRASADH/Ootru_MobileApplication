import 'AppSettingModel.dart';

class DashboardDetail {
  // int? totalCity;
  // int? totalClient;
  // int? totalDeliveryMan;
  // int? totalOrder;
  // int? todayRegisterUser;
  // int? totalEarning;
  // int? totalCancelledOrder;
  // int? totalCreateOrder;
  // int? totalActiveOrder;
  // int? totalDelayedOrder;
  // int? totalCourierAssignedOrder;
  // int? totalCourierPickedUpOrder;
  // int? totalCourierDepartedOrder;
  // int? totalCourierArrivedOrder;
  // int? totalCompletedOrder;
  // int? totalFailedOrder;
  // int? todayCreateOrder;
  // int? todayActiveOrder;
  // int? todayDelayedOrder;
  // int? todayCancelledOrder;
  // int? todayCourierAssignedOrder;
  // int? todayCourierPickedUpOrder;
  // int? todayCourierDepartedOrder;
  // int? todayCourierArrivedOrder;
  // int? todayCompletedOrder;
  // int? todayFailedOrder;
  AppSetting? appSetting;
  // List<Null>? upcomingOrder;
  // List<Null>? recentOrder;
  // List<RecentClient>? recentClient;
  // List<RecentDeliveryMan>? recentDeliveryMan;
  // Week? week;
  // List<WeeklyOrderCount>? weeklyOrderCount;
  // List<UserWeeklyCount>? userWeeklyCount;
  // int? allUnreadCount;
  // List<WeeklyPaymentReport>? weeklyPaymentReport;
  // Month? month;
  // List<MonthlyOrderCount>? monthlyOrderCount;
  // List<MonthlyPaymentCompletedReport>? monthlyPaymentCompletedReport;
  // List<MonthlyPaymentCancelledReport>? monthlyPaymentCancelledReport;
  // List<CountryCityData>? countryCityData;
  DeliverManVersion? deliverManVersion;
  CrispData? crispData;

  DashboardDetail(
      {
      // this.totalCity,
      // this.totalClient,
      // this.totalDeliveryMan,
      // this.totalOrder,
      // this.todayRegisterUser,
      // this.totalEarning,
      // this.totalCancelledOrder,
      // this.totalCreateOrder,
      // this.totalActiveOrder,
      // this.totalDelayedOrder,
      // this.totalCourierAssignedOrder,
      // this.totalCourierPickedUpOrder,
      // this.totalCourierDepartedOrder,
      // this.totalCourierArrivedOrder,
      // this.totalCompletedOrder,
      // this.totalFailedOrder,
      // this.todayCreateOrder,
      // this.todayActiveOrder,
      // this.todayDelayedOrder,
      // this.todayCancelledOrder,
      // this.todayCourierAssignedOrder,
      // this.todayCourierPickedUpOrder,
      // this.todayCourierDepartedOrder,
      // this.todayCourierArrivedOrder,
      // this.todayCompletedOrder,
      // this.todayFailedOrder,
      this.appSetting,
      // this.upcomingOrder,
      // this.recentOrder,
      // this.recentClient,
      // this.recentDeliveryMan,
      // this.week,
      // this.weeklyOrderCount,
      // this.userWeeklyCount,
      // this.allUnreadCount,
      // this.weeklyPaymentReport,
      // this.month,
      // this.monthlyOrderCount,
      // this.monthlyPaymentCompletedReport,
      // this.monthlyPaymentCancelledReport,
      // this.countryCityData,
      this.deliverManVersion,
      this.crispData});

  DashboardDetail.fromJson(Map<String, dynamic> json) {
    // totalCity = json['total_city'];
    // totalClient = json['total_client'];
    // totalDeliveryMan = json['total_delivery_man'];
    // totalOrder = json['total_order'];
    // todayRegisterUser = json['today_register_user'];
    // totalEarning = json['total_earning'];
    // totalCancelledOrder = json['total_cancelled_order'];
    // totalCreateOrder = json['total_create_order'];
    // totalActiveOrder = json['total_active_order'];
    // totalDelayedOrder = json['total_delayed_order'];
    // totalCourierAssignedOrder = json['total_courier_assigned_order'];
    // totalCourierPickedUpOrder = json['total_courier_picked_up_order'];
    // totalCourierDepartedOrder = json['total_courier_departed_order'];
    // totalCourierArrivedOrder = json['total_courier_arrived_order'];
    // totalCompletedOrder = json['total_completed_order'];
    // totalFailedOrder = json['total_failed_order'];
    // todayCreateOrder = json['today_create_order'];
    // todayActiveOrder = json['today_active_order'];
    // todayDelayedOrder = json['today_delayed_order'];
    // todayCancelledOrder = json['today_cancelled_order'];
    // todayCourierAssignedOrder = json['today_courier_assigned_order'];
    // todayCourierPickedUpOrder = json['today_courier_picked_up_order'];
    // todayCourierDepartedOrder = json['today_courier_departed_order'];
    // todayCourierArrivedOrder = json['today_courier_arrived_order'];
    // todayCompletedOrder = json['today_completed_order'];
    // todayFailedOrder = json['today_failed_order'];
    appSetting = json['app_setting'] != null ? new AppSetting.fromJson(json['app_setting']) : null;
    // if (json['upcoming_order'] != null) {
    //   upcomingOrder = <Null>[];
    //   json['upcoming_order'].forEach((v) {
    //     upcomingOrder!.add(new Null.fromJson(v));
    //   });
    // }
    // if (json['recent_order'] != null) {
    //   recentOrder = <Null>[];
    //   json['recent_order'].forEach((v) {
    //     recentOrder!.add(new Null.fromJson(v));
    //   });
    // }
    // if (json['recent_client'] != null) {
    //   recentClient = <RecentClient>[];
    //   json['recent_client'].forEach((v) {
    //     recentClient!.add(new RecentClient.fromJson(v));
    //   });
    // }
    // if (json['recent_delivery_man'] != null) {
    //   recentDeliveryMan = <RecentDeliveryMan>[];
    //   json['recent_delivery_man'].forEach((v) {
    //     recentDeliveryMan!.add(new RecentDeliveryMan.fromJson(v));
    //   });
    // }
    // week = json['week'] != null ? new Week.fromJson(json['week']) : null;
    // if (json['weekly_order_count'] != null) {
    //   weeklyOrderCount = <WeeklyOrderCount>[];
    //   json['weekly_order_count'].forEach((v) {
    //     weeklyOrderCount!.add(new WeeklyOrderCount.fromJson(v));
    //   });
    // }
    // if (json['user_weekly_count'] != null) {
    //   userWeeklyCount = <UserWeeklyCount>[];
    //   json['user_weekly_count'].forEach((v) {
    //     userWeeklyCount!.add(new UserWeeklyCount.fromJson(v));
    //   });
    // }
    // allUnreadCount = json['all_unread_count'];
    // if (json['weekly_payment_report'] != null) {
    //   weeklyPaymentReport = <WeeklyPaymentReport>[];
    //   json['weekly_payment_report'].forEach((v) {
    //     weeklyPaymentReport!.add(new WeeklyPaymentReport.fromJson(v));
    //   });
    // }
    // month = json['month'] != null ? new Month.fromJson(json['month']) : null;
    // if (json['monthly_order_count'] != null) {
    //   monthlyOrderCount = <MonthlyOrderCount>[];
    //   json['monthly_order_count'].forEach((v) {
    //     monthlyOrderCount!.add(new MonthlyOrderCount.fromJson(v));
    //   });
    // }
    // if (json['monthly_payment_completed_report'] != null) {
    //   monthlyPaymentCompletedReport = <MonthlyPaymentCompletedReport>[];
    //   json['monthly_payment_completed_report'].forEach((v) {
    //     monthlyPaymentCompletedReport!.add(new MonthlyPaymentCompletedReport.fromJson(v));
    //   });
    // }
    // if (json['monthly_payment_cancelled_report'] != null) {
    //   monthlyPaymentCancelledReport = <MonthlyPaymentCancelledReport>[];
    //   json['monthly_payment_cancelled_report'].forEach((v) {
    //     monthlyPaymentCancelledReport!.add(new MonthlyPaymentCancelledReport.fromJson(v));
    //   });
    // }
    // if (json['country_city_data'] != null) {
    //   countryCityData = <CountryCityData>[];
    //   json['country_city_data'].forEach((v) {
    //     countryCityData!.add(new CountryCityData.fromJson(v));
    //   });
    // }
    deliverManVersion = json['deliver_man_version'] != null ? new DeliverManVersion.fromJson(json['deliver_man_version']) : null;
    crispData = json['crisp_data'] != null ? new CrispData.fromJson(json['crisp_data']) : null;
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();

    // data['total_city'] = this.totalCity;
    // data['total_client'] = this.totalClient;
    // data['total_delivery_man'] = this.totalDeliveryMan;
    // data['total_order'] = this.totalOrder;
    // data['today_register_user'] = this.todayRegisterUser;
    // data['total_earning'] = this.totalEarning;
    // data['total_cancelled_order'] = this.totalCancelledOrder;
    // data['total_create_order'] = this.totalCreateOrder;
    // data['total_active_order'] = this.totalActiveOrder;
    // data['total_delayed_order'] = this.totalDelayedOrder;
    // data['total_courier_assigned_order'] = this.totalCourierAssignedOrder;
    // data['total_courier_picked_up_order'] = this.totalCourierPickedUpOrder;
    // data['total_courier_departed_order'] = this.totalCourierDepartedOrder;
    // data['total_courier_arrived_order'] = this.totalCourierArrivedOrder;
    // data['total_completed_order'] = this.totalCompletedOrder;
    // data['total_failed_order'] = this.totalFailedOrder;
    // data['today_create_order'] = this.todayCreateOrder;
    // data['today_active_order'] = this.todayActiveOrder;
    // data['today_delayed_order'] = this.todayDelayedOrder;
    // data['today_cancelled_order'] = this.todayCancelledOrder;
    // data['today_courier_assigned_order'] = this.todayCourierAssignedOrder;
    // data['today_courier_picked_up_order'] = this.todayCourierPickedUpOrder;
    // data['today_courier_departed_order'] = this.todayCourierDepartedOrder;
    // data['today_courier_arrived_order'] = this.todayCourierArrivedOrder;
    // data['today_completed_order'] = this.todayCompletedOrder;
    // data['today_failed_order'] = this.todayFailedOrder;
    if (this.appSetting != null) {
      data['app_setting'] = this.appSetting!.toJson();
    }
    // if (this.upcomingOrder != null) {
    //   data['upcoming_order'] = this.upcomingOrder!.map((v) => v.toJson()).toList();
    // }
    // if (this.recentOrder != null) {
    //   data['recent_order'] = this.recentOrder!.map((v) => v.toJson()).toList();
    // }
    // if (this.recentClient != null) {
    //   data['recent_client'] = this.recentClient!.map((v) => v.toJson()).toList();
    // }
    // if (this.recentDeliveryMan != null) {
    //   data['recent_delivery_man'] = this.recentDeliveryMan!.map((v) => v.toJson()).toList();
    // }
    // if (this.week != null) {
    //   data['week'] = this.week!.toJson();
    // }
    // if (this.weeklyOrderCount != null) {
    //   data['weekly_order_count'] = this.weeklyOrderCount!.map((v) => v.toJson()).toList();
    // }
    // if (this.userWeeklyCount != null) {
    //   data['user_weekly_count'] = this.userWeeklyCount!.map((v) => v.toJson()).toList();
    // }
    // data['all_unread_count'] = this.allUnreadCount;
    // if (this.weeklyPaymentReport != null) {
    //   data['weekly_payment_report'] = this.weeklyPaymentReport!.map((v) => v.toJson()).toList();
    // }
    // if (this.month != null) {
    //   data['month'] = this.month!.toJson();
    // }
    // if (this.monthlyOrderCount != null) {
    //   data['monthly_order_count'] = this.monthlyOrderCount!.map((v) => v.toJson()).toList();
    // }
    // if (this.monthlyPaymentCompletedReport != null) {
    //   data['monthly_payment_completed_report'] = this.monthlyPaymentCompletedReport!.map((v) => v.toJson()).toList();
    // }
    // if (this.monthlyPaymentCancelledReport != null) {
    //   data['monthly_payment_cancelled_report'] = this.monthlyPaymentCancelledReport!.map((v) => v.toJson()).toList();
    // }
    // if (this.countryCityData != null) {
    //   data['country_city_data'] = this.countryCityData!.map((v) => v.toJson()).toList();
    // }
    if (this.deliverManVersion != null) {
      data['deliver_man_version'] = this.deliverManVersion!.toJson();
    }
    if (this.crispData != null) {
      data['crisp_data'] = this.crispData!.toJson();
    }
    return data;
  }
}

class AppSetting {
  int? isSmsOrder;

  AppSetting({
    this.isSmsOrder,
  });

  AppSetting.fromJson(Map<String, dynamic> json) {
    isSmsOrder = json['is_sms_order'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['is_sms_order'] = this.isSmsOrder;

    return data;
  }
}

class NotificationSettings {
  Active? active;
  Active? cancel;
  Active? create;
  Active? failed;
  Active? delayed;
  Active? completed;
  Active? courierArrived;
  Active? courierAssigned;
  Active? courierTransfer;
  Active? courierPickupUp;
  Active? departedAssigned;
  Active? paymentStatusMessage;

  NotificationSettings({this.active, this.cancel, this.create, this.failed, this.delayed, this.completed, this.courierArrived, this.courierAssigned, this.courierTransfer, this.courierPickupUp, this.departedAssigned, this.paymentStatusMessage});

  NotificationSettings.fromJson(Map<String, dynamic> json) {
    active = json['active'] != null ? new Active.fromJson(json['active']) : null;
    cancel = json['cancel'] != null ? new Active.fromJson(json['cancel']) : null;
    create = json['create'] != null ? new Active.fromJson(json['create']) : null;
    failed = json['failed'] != null ? new Active.fromJson(json['failed']) : null;
    delayed = json['delayed'] != null ? new Active.fromJson(json['delayed']) : null;
    completed = json['completed'] != null ? new Active.fromJson(json['completed']) : null;
    courierArrived = json['courier_arrived'] != null ? new Active.fromJson(json['courier_arrived']) : null;
    courierAssigned = json['courier_assigned'] != null ? new Active.fromJson(json['courier_assigned']) : null;
    courierTransfer = json['courier_transfer'] != null ? new Active.fromJson(json['courier_transfer']) : null;
    courierPickupUp = json['courier_pickup_up'] != null ? new Active.fromJson(json['courier_pickup_up']) : null;
    departedAssigned = json['departed_assigned'] != null ? new Active.fromJson(json['departed_assigned']) : null;
    paymentStatusMessage = json['payment_status_message'] != null ? new Active.fromJson(json['payment_status_message']) : null;
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    if (this.active != null) {
      data['active'] = this.active!.toJson();
    }
    if (this.cancel != null) {
      data['cancel'] = this.cancel!.toJson();
    }
    if (this.create != null) {
      data['create'] = this.create!.toJson();
    }
    if (this.failed != null) {
      data['failed'] = this.failed!.toJson();
    }
    if (this.delayed != null) {
      data['delayed'] = this.delayed!.toJson();
    }
    if (this.completed != null) {
      data['completed'] = this.completed!.toJson();
    }
    if (this.courierArrived != null) {
      data['courier_arrived'] = this.courierArrived!.toJson();
    }
    if (this.courierAssigned != null) {
      data['courier_assigned'] = this.courierAssigned!.toJson();
    }
    if (this.courierTransfer != null) {
      data['courier_transfer'] = this.courierTransfer!.toJson();
    }
    if (this.courierPickupUp != null) {
      data['courier_pickup_up'] = this.courierPickupUp!.toJson();
    }
    if (this.departedAssigned != null) {
      data['departed_assigned'] = this.departedAssigned!.toJson();
    }
    if (this.paymentStatusMessage != null) {
      data['payment_status_message'] = this.paymentStatusMessage!.toJson();
    }
    return data;
  }
}

class Active {
  String? iSFIREBASENOTIFICATION;
  String? iSONESIGNALNOTIFICATION;

  Active({this.iSFIREBASENOTIFICATION, this.iSONESIGNALNOTIFICATION});

  Active.fromJson(Map<String, dynamic> json) {
    iSFIREBASENOTIFICATION = json['IS_FIREBASE_NOTIFICATION'];
    iSONESIGNALNOTIFICATION = json['IS_ONESIGNAL_NOTIFICATION'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['IS_FIREBASE_NOTIFICATION'] = this.iSFIREBASENOTIFICATION;
    data['IS_ONESIGNAL_NOTIFICATION'] = this.iSONESIGNALNOTIFICATION;
    return data;
  }
}

class RecentClient {
  int? id;
  String? name;
  String? email;
  String? username;
  int? status;
  String? userType;
  int? countryId;
  String? countryName;
  int? cityId;
  String? cityName;
  Null? address;
  String? contactNumber;
  String? profileImage;
  String? loginType;
  Null? latitude;
  Null? longitude;
  String? uid;
  String? playerId;
  Null? fcmToken;
  Null? lastNotificationSeen;
  int? isVerifiedDeliveryMan;
  String? createdAt;
  String? updatedAt;
  Null? deletedAt;
  Null? userBankAccount;
  String? otpVerifyAt;
  String? emailVerifiedAt;
  String? documentVerifiedAt;
  String? appVersion;
  String? appSource;
  String? lastActivedAt;
  bool? isEmailVerification;
  bool? isMobileVerification;
  bool? isDocumentVerification;
  String? referralCode;
  Null? partnerReferralCode;
  Null? vehicleId;
  Null? deliverymanVehicleHistory;

  RecentClient(
      {this.id,
      this.name,
      this.email,
      this.username,
      this.status,
      this.userType,
      this.countryId,
      this.countryName,
      this.cityId,
      this.cityName,
      this.address,
      this.contactNumber,
      this.profileImage,
      this.loginType,
      this.latitude,
      this.longitude,
      this.uid,
      this.playerId,
      this.fcmToken,
      this.lastNotificationSeen,
      this.isVerifiedDeliveryMan,
      this.createdAt,
      this.updatedAt,
      this.deletedAt,
      this.userBankAccount,
      this.otpVerifyAt,
      this.emailVerifiedAt,
      this.documentVerifiedAt,
      this.appVersion,
      this.appSource,
      this.lastActivedAt,
      this.isEmailVerification,
      this.isMobileVerification,
      this.isDocumentVerification,
      this.referralCode,
      this.partnerReferralCode,
      this.vehicleId,
      this.deliverymanVehicleHistory});

  RecentClient.fromJson(Map<String, dynamic> json) {
    id = json['id'];
    name = json['name'];
    email = json['email'];
    username = json['username'];
    status = json['status'];
    userType = json['user_type'];
    countryId = json['country_id'];
    countryName = json['country_name'];
    cityId = json['city_id'];
    cityName = json['city_name'];
    address = json['address'];
    contactNumber = json['contact_number'];
    profileImage = json['profile_image'];
    loginType = json['login_type'];
    latitude = json['latitude'];
    longitude = json['longitude'];
    uid = json['uid'];
    playerId = json['player_id'];
    fcmToken = json['fcm_token'];
    lastNotificationSeen = json['last_notification_seen'];
    isVerifiedDeliveryMan = json['is_verified_delivery_man'];
    createdAt = json['created_at'];
    updatedAt = json['updated_at'];
    deletedAt = json['deleted_at'];
    userBankAccount = json['user_bank_account'];
    otpVerifyAt = json['otp_verify_at'];
    emailVerifiedAt = json['email_verified_at'];
    documentVerifiedAt = json['document_verified_at'];
    appVersion = json['app_version'];
    appSource = json['app_source'];
    lastActivedAt = json['last_actived_at'];
    isEmailVerification = json['is_email_verification'];
    isMobileVerification = json['is_mobile_verification'];
    isDocumentVerification = json['is_document_verification'];
    referralCode = json['referral_code'];
    partnerReferralCode = json['partner_referral_code'];
    vehicleId = json['vehicle_id'];
    deliverymanVehicleHistory = json['DeliverymanVehicleHistory'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['id'] = this.id;
    data['name'] = this.name;
    data['email'] = this.email;
    data['username'] = this.username;
    data['status'] = this.status;
    data['user_type'] = this.userType;
    data['country_id'] = this.countryId;
    data['country_name'] = this.countryName;
    data['city_id'] = this.cityId;
    data['city_name'] = this.cityName;
    data['address'] = this.address;
    data['contact_number'] = this.contactNumber;
    data['profile_image'] = this.profileImage;
    data['login_type'] = this.loginType;
    data['latitude'] = this.latitude;
    data['longitude'] = this.longitude;
    data['uid'] = this.uid;
    data['player_id'] = this.playerId;
    data['fcm_token'] = this.fcmToken;
    data['last_notification_seen'] = this.lastNotificationSeen;
    data['is_verified_delivery_man'] = this.isVerifiedDeliveryMan;
    data['created_at'] = this.createdAt;
    data['updated_at'] = this.updatedAt;
    data['deleted_at'] = this.deletedAt;
    data['user_bank_account'] = this.userBankAccount;
    data['otp_verify_at'] = this.otpVerifyAt;
    data['email_verified_at'] = this.emailVerifiedAt;
    data['document_verified_at'] = this.documentVerifiedAt;
    data['app_version'] = this.appVersion;
    data['app_source'] = this.appSource;
    data['last_actived_at'] = this.lastActivedAt;
    data['is_email_verification'] = this.isEmailVerification;
    data['is_mobile_verification'] = this.isMobileVerification;
    data['is_document_verification'] = this.isDocumentVerification;
    data['referral_code'] = this.referralCode;
    data['partner_referral_code'] = this.partnerReferralCode;
    data['vehicle_id'] = this.vehicleId;
    data['DeliverymanVehicleHistory'] = this.deliverymanVehicleHistory;
    return data;
  }
}

class RecentDeliveryMan {
  int? id;
  String? name;
  String? email;
  String? username;
  int? status;
  String? userType;
  int? countryId;
  String? countryName;
  int? cityId;
  String? cityName;
  String? address;
  String? contactNumber;
  String? profileImage;
  Null? loginType;
  String? latitude;
  String? longitude;
  String? uid;
  String? playerId;
  Null? fcmToken;
  Null? lastNotificationSeen;
  int? isVerifiedDeliveryMan;
  String? createdAt;
  String? updatedAt;
  Null? deletedAt;
  Null? userBankAccount;
  String? otpVerifyAt;
  String? emailVerifiedAt;
  String? documentVerifiedAt;
  String? appVersion;
  String? appSource;
  String? lastActivedAt;
  bool? isEmailVerification;
  bool? isMobileVerification;
  bool? isDocumentVerification;
  String? referralCode;
  Null? partnerReferralCode;
  Null? vehicleId;
  Null? deliverymanVehicleHistory;

  RecentDeliveryMan(
      {this.id,
      this.name,
      this.email,
      this.username,
      this.status,
      this.userType,
      this.countryId,
      this.countryName,
      this.cityId,
      this.cityName,
      this.address,
      this.contactNumber,
      this.profileImage,
      this.loginType,
      this.latitude,
      this.longitude,
      this.uid,
      this.playerId,
      this.fcmToken,
      this.lastNotificationSeen,
      this.isVerifiedDeliveryMan,
      this.createdAt,
      this.updatedAt,
      this.deletedAt,
      this.userBankAccount,
      this.otpVerifyAt,
      this.emailVerifiedAt,
      this.documentVerifiedAt,
      this.appVersion,
      this.appSource,
      this.lastActivedAt,
      this.isEmailVerification,
      this.isMobileVerification,
      this.isDocumentVerification,
      this.referralCode,
      this.partnerReferralCode,
      this.vehicleId,
      this.deliverymanVehicleHistory});

  RecentDeliveryMan.fromJson(Map<String, dynamic> json) {
    id = json['id'];
    name = json['name'];
    email = json['email'];
    username = json['username'];
    status = json['status'];
    userType = json['user_type'];
    countryId = json['country_id'];
    countryName = json['country_name'];
    cityId = json['city_id'];
    cityName = json['city_name'];
    address = json['address'];
    contactNumber = json['contact_number'];
    profileImage = json['profile_image'];
    loginType = json['login_type'];
    latitude = json['latitude'];
    longitude = json['longitude'];
    uid = json['uid'];
    playerId = json['player_id'];
    fcmToken = json['fcm_token'];
    lastNotificationSeen = json['last_notification_seen'];
    isVerifiedDeliveryMan = json['is_verified_delivery_man'];
    createdAt = json['created_at'];
    updatedAt = json['updated_at'];
    deletedAt = json['deleted_at'];
    userBankAccount = json['user_bank_account'];
    otpVerifyAt = json['otp_verify_at'];
    emailVerifiedAt = json['email_verified_at'];
    documentVerifiedAt = json['document_verified_at'];
    appVersion = json['app_version'];
    appSource = json['app_source'];
    lastActivedAt = json['last_actived_at'];
    isEmailVerification = json['is_email_verification'];
    isMobileVerification = json['is_mobile_verification'];
    isDocumentVerification = json['is_document_verification'];
    referralCode = json['referral_code'];
    partnerReferralCode = json['partner_referral_code'];
    vehicleId = json['vehicle_id'];
    deliverymanVehicleHistory = json['DeliverymanVehicleHistory'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['id'] = this.id;
    data['name'] = this.name;
    data['email'] = this.email;
    data['username'] = this.username;
    data['status'] = this.status;
    data['user_type'] = this.userType;
    data['country_id'] = this.countryId;
    data['country_name'] = this.countryName;
    data['city_id'] = this.cityId;
    data['city_name'] = this.cityName;
    data['address'] = this.address;
    data['contact_number'] = this.contactNumber;
    data['profile_image'] = this.profileImage;
    data['login_type'] = this.loginType;
    data['latitude'] = this.latitude;
    data['longitude'] = this.longitude;
    data['uid'] = this.uid;
    data['player_id'] = this.playerId;
    data['fcm_token'] = this.fcmToken;
    data['last_notification_seen'] = this.lastNotificationSeen;
    data['is_verified_delivery_man'] = this.isVerifiedDeliveryMan;
    data['created_at'] = this.createdAt;
    data['updated_at'] = this.updatedAt;
    data['deleted_at'] = this.deletedAt;
    data['user_bank_account'] = this.userBankAccount;
    data['otp_verify_at'] = this.otpVerifyAt;
    data['email_verified_at'] = this.emailVerifiedAt;
    data['document_verified_at'] = this.documentVerifiedAt;
    data['app_version'] = this.appVersion;
    data['app_source'] = this.appSource;
    data['last_actived_at'] = this.lastActivedAt;
    data['is_email_verification'] = this.isEmailVerification;
    data['is_mobile_verification'] = this.isMobileVerification;
    data['is_document_verification'] = this.isDocumentVerification;
    data['referral_code'] = this.referralCode;
    data['partner_referral_code'] = this.partnerReferralCode;
    data['vehicle_id'] = this.vehicleId;
    data['DeliverymanVehicleHistory'] = this.deliverymanVehicleHistory;
    return data;
  }
}

class Week {
  String? weekStart;
  String? weekEnd;

  Week({this.weekStart, this.weekEnd});

  Week.fromJson(Map<String, dynamic> json) {
    weekStart = json['week_start'];
    weekEnd = json['week_end'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['week_start'] = this.weekStart;
    data['week_end'] = this.weekEnd;
    return data;
  }
}

class WeeklyOrderCount {
  String? day;
  int? total;
  String? date;

  WeeklyOrderCount({this.day, this.total, this.date});

  WeeklyOrderCount.fromJson(Map<String, dynamic> json) {
    day = json['day'];
    total = json['total'];
    date = json['date'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['day'] = this.day;
    data['total'] = this.total;
    data['date'] = this.date;
    return data;
  }
}

class WeeklyPaymentReport {
  String? day;
  int? totalAmount;
  String? date;

  WeeklyPaymentReport({this.day, this.totalAmount, this.date});

  WeeklyPaymentReport.fromJson(Map<String, dynamic> json) {
    day = json['day'];
    totalAmount = json['total_amount'];
    date = json['date'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['day'] = this.day;
    data['total_amount'] = this.totalAmount;
    data['date'] = this.date;
    return data;
  }
}

class Month {
  String? monthStart;
  String? monthEnd;
  double? diff;

  Month({this.monthStart, this.monthEnd, this.diff});

  Month.fromJson(Map<String, dynamic> json) {
    monthStart = json['month_start'];
    monthEnd = json['month_end'];
    diff = json['diff'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['month_start'] = this.monthStart;
    data['month_end'] = this.monthEnd;
    data['diff'] = this.diff;
    return data;
  }
}

class MonthlyOrderCount {
  int? total;
  String? date;

  MonthlyOrderCount({this.total, this.date});

  MonthlyOrderCount.fromJson(Map<String, dynamic> json) {
    total = json['total'];
    date = json['date'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['total'] = this.total;
    data['date'] = this.date;
    return data;
  }
}

class MonthlyPaymentCompletedReport {
  int? totalAmount;
  String? date;

  MonthlyPaymentCompletedReport({this.totalAmount, this.date});

  MonthlyPaymentCompletedReport.fromJson(Map<String, dynamic> json) {
    totalAmount = json['total_amount'];
    date = json['date'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['total_amount'] = this.totalAmount;
    data['date'] = this.date;
    return data;
  }
}

class CountryCityData {
  int? id;
  String? name;
  String? code;
  String? distanceType;
  String? weightType;
  Null? links;
  int? status;
  String? createdAt;
  String? updatedAt;
  Null? deletedAt;
  List<Cities>? cities;

  CountryCityData({this.id, this.name, this.code, this.distanceType, this.weightType, this.links, this.status, this.createdAt, this.updatedAt, this.deletedAt, this.cities});

  CountryCityData.fromJson(Map<String, dynamic> json) {
    id = json['id'];
    name = json['name'];
    code = json['code'];
    distanceType = json['distance_type'];
    weightType = json['weight_type'];
    links = json['links'];
    status = json['status'];
    createdAt = json['created_at'];
    updatedAt = json['updated_at'];
    deletedAt = json['deleted_at'];
    if (json['cities'] != null) {
      cities = <Cities>[];
      json['cities'].forEach((v) {
        cities!.add(new Cities.fromJson(v));
      });
    }
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['id'] = this.id;
    data['name'] = this.name;
    data['code'] = this.code;
    data['distance_type'] = this.distanceType;
    data['weight_type'] = this.weightType;
    data['links'] = this.links;
    data['status'] = this.status;
    data['created_at'] = this.createdAt;
    data['updated_at'] = this.updatedAt;
    data['deleted_at'] = this.deletedAt;
    if (this.cities != null) {
      data['cities'] = this.cities!.map((v) => v.toJson()).toList();
    }
    return data;
  }
}

class Cities {
  int? id;
  String? name;
  int? countryId;
  Null? address;
  int? fixedCharges;
  int? cancelCharges;
  int? minDistance;
  int? minWeight;
  int? perDistanceCharges;
  int? perWeightCharges;
  int? status;
  String? createdAt;
  String? updatedAt;
  Null? deletedAt;
  String? commissionType;
  int? adminCommission;

  Cities({this.id, this.name, this.countryId, this.address, this.fixedCharges, this.cancelCharges, this.minDistance, this.minWeight, this.perDistanceCharges, this.perWeightCharges, this.status, this.createdAt, this.updatedAt, this.deletedAt, this.commissionType, this.adminCommission});

  Cities.fromJson(Map<String, dynamic> json) {
    id = json['id'];
    name = json['name'];
    countryId = json['country_id'];
    address = json['address'];
    fixedCharges = json['fixed_charges'];
    cancelCharges = json['cancel_charges'];
    minDistance = json['min_distance'];
    minWeight = json['min_weight'];
    perDistanceCharges = json['per_distance_charges'];
    perWeightCharges = json['per_weight_charges'];
    status = json['status'];
    createdAt = json['created_at'];
    updatedAt = json['updated_at'];
    deletedAt = json['deleted_at'];
    commissionType = json['commission_type'];
    adminCommission = json['admin_commission'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['id'] = this.id;
    data['name'] = this.name;
    data['country_id'] = this.countryId;
    data['address'] = this.address;
    data['fixed_charges'] = this.fixedCharges;
    data['cancel_charges'] = this.cancelCharges;
    data['min_distance'] = this.minDistance;
    data['min_weight'] = this.minWeight;
    data['per_distance_charges'] = this.perDistanceCharges;
    data['per_weight_charges'] = this.perWeightCharges;
    data['status'] = this.status;
    data['created_at'] = this.createdAt;
    data['updated_at'] = this.updatedAt;
    data['deleted_at'] = this.deletedAt;
    data['commission_type'] = this.commissionType;
    data['admin_commission'] = this.adminCommission;
    return data;
  }
}

class DeliverManVersion {
  String? androidForceUpdate;
  String? androidVersionCode;
  String? appstoreUrl;
  String? iosForceUpdate;
  String? iosVersion;
  String? playstoreUrl;

  DeliverManVersion({this.androidForceUpdate, this.androidVersionCode, this.appstoreUrl, this.iosForceUpdate, this.iosVersion, this.playstoreUrl});

  DeliverManVersion.fromJson(Map<String, dynamic> json) {
    androidForceUpdate = json['android_force_update'];
    androidVersionCode = json['android_version_code'];
    appstoreUrl = json['appstore_url'];
    iosForceUpdate = json['ios_force_update'];
    iosVersion = json['ios_version'];
    playstoreUrl = json['playstore_url'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['android_force_update'] = this.androidForceUpdate;
    data['android_version_code'] = this.androidVersionCode;
    data['appstore_url'] = this.appstoreUrl;
    data['ios_force_update'] = this.iosForceUpdate;
    data['ios_version'] = this.iosVersion;
    data['playstore_url'] = this.playstoreUrl;
    return data;
  }
}

class CrispData {
  String? crispChatWebsiteId;
  bool? isCrispChatEnabled;

  CrispData({this.crispChatWebsiteId, this.isCrispChatEnabled});

  CrispData.fromJson(Map<String, dynamic> json) {
    crispChatWebsiteId = json['crisp_chat_website_id'];
    isCrispChatEnabled = json['is_crisp_chat_enabled'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['crisp_chat_website_id'] = this.crispChatWebsiteId;
    data['is_crisp_chat_enabled'] = this.isCrispChatEnabled;
    return data;
  }
}
