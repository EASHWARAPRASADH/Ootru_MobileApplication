import '../../main/models/WalletListModel.dart';

import '../../main/models/PaginationModel.dart';
import 'CreateOrderDetailModel.dart';
import 'VehicleModel.dart';

class OrderListModel {
  PaginationModel? pagination;
  List<OrderData>? data;
  int? allUnreadCount;
  UserWalletModel? walletData;

  OrderListModel({this.pagination, this.data, this.allUnreadCount, this.walletData});

  OrderListModel.fromJson(Map<String, dynamic> json) {
    pagination = json['pagination'] != null ? new PaginationModel.fromJson(json['pagination']) : null;
    if (json['data'] != null) {
      data = <OrderData>[];
      json['data'].forEach((v) {
        data!.add(new OrderData.fromJson(v));
      });
    }
    allUnreadCount = json['all_unread_count'];
    walletData = json['wallet_data'] != null ? new UserWalletModel.fromJson(json['wallet_data']) : null;
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    if (this.pagination != null) {
      data['pagination'] = this.pagination!.toJson();
    }
    if (this.data != null) {
      data['data'] = this.data!.map((v) => v.toJson()).toList();
    }
    data['all_unread_count'] = this.allUnreadCount;
    if (this.walletData != null) {
      data['wallet_data'] = this.walletData!.toJson();
    }
    return data;
  }
}

class PickupPoint {
  String? name;
  String? address;
  String? latitude;
  String? longitude;
  String? description;
  String? contactNumber;
  String? startTime;
  String? endTime;
  String? instruction;

  PickupPoint({this.address, this.name, this.latitude, this.longitude, this.description, this.contactNumber, this.startTime, this.instruction, this.endTime});

  PickupPoint.fromJson(Map<String, dynamic> json) {
    name = json['name'];
    instruction = json["instruction"];
    address = json['address'];
    latitude = json['latitude'];
    longitude = json['longitude'];
    description = json['description'];
    contactNumber = json['contact_number'];
    startTime = json['start_time'];
    endTime = json['end_time'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['address'] = this.address;
    data['latitude'] = this.latitude;
    data['longitude'] = this.longitude;
    data['description'] = this.description;
    data['contact_number'] = this.contactNumber;
    data['start_time'] = this.startTime;
    data['end_time'] = this.endTime;
    data['name'] = this.name;
    data['instruction'] = this.instruction;
    return data;
  }
}

class PackagingSymbol {
  final String key;
  final String title;

  PackagingSymbol({
    required this.key,
    required this.title,
  });

  factory PackagingSymbol.fromJson(Map<String, dynamic> json) => PackagingSymbol(
        key: json["key"],
        title: json["title"],
      );

  Map<String, dynamic> toJson() => {
        "key": key,
        "title": title,
      };
}

class OrderData {
  String? orderTrackingId;
  int? id;
  int? clientId;
  String? clientName;
  String? date;
  PickupPoint? pickupPoint;
  PickupPoint? deliveryPoint;
  int? countryId;
  String? countryName;
  int? cityId;
  String? cityName;
  String? parcelType;
  num? totalWeight;
  var totalDistance;
  String? pickupDatetime;
  int? bid_type; // Added for bid type
  String? deliveryDatetime;
  int? parentOrderId;
  String? status;
  int? paymentId;
  String? paymentType;
  String? paymentStatus;
  String? paymentCollectFrom;
  int? deliveryManId;
  String? deliveryManName;
  num? fixedCharges;
  var extraCharges;
  var totalAmount;
  String? reason;
  int? pickupConfirmByClient;
  int? pickupConfirmByDeliveryMan;
  String? pickupTimeSignature;
  String? deliveryTimeSignature;
  String? deletedAt;
  bool? returnOrderId;
  num? weightCharge;
  num? distanceCharge;
  num? vehicleCharge;
  num? totalParcel;
  int? autoAssign;
  List<dynamic>? cancelledDeliverManIds;
  int? vehicleId;
  VehicleData? vehicleData;
  String? vehicleImage;
  String? invoice;
  List<PackagingSymbol>? packagingSymbols = [];
  num? insuranceCharge;
  num? baseTotal;
  List<ExtraCharges>? extraChargesList;
  CityDetail? cityDetails;
  int? isClaimed;
  int? isRescheduled;
  String? reScheduleDateTime;
  RatingDetail? ratingDetail;

  OrderData({
    this.orderTrackingId,
    this.id,
    this.clientId,
    this.clientName,
    this.date,
    this.pickupPoint,
    this.deliveryPoint,
    this.countryId,
    this.countryName,
    this.cityId,
    this.cityName,
    this.parcelType,
    this.totalWeight,
    this.totalDistance,
    this.pickupDatetime,
    this.deliveryDatetime,
    this.parentOrderId,
    this.status,
    this.paymentId,
    this.paymentType,
    this.paymentStatus,
    this.paymentCollectFrom,
    this.deliveryManId,
    this.deliveryManName,
    this.fixedCharges,
    this.extraCharges,
    this.vehicleCharge,
    this.totalAmount,
    this.reason,
    this.pickupConfirmByClient,
    this.pickupConfirmByDeliveryMan,
    this.pickupTimeSignature,
    this.deliveryTimeSignature,
    this.deletedAt,
    this.returnOrderId,
    this.weightCharge,
    this.distanceCharge,
    this.totalParcel,
    this.autoAssign,
    this.cancelledDeliverManIds,
    this.vehicleId,
    this.vehicleData,
    this.vehicleImage,
    this.packagingSymbols,
    this.invoice,
    this.insuranceCharge,
    this.baseTotal,
    this.extraChargesList,
    this.bid_type = 0,
    this.cityDetails,
    this.isClaimed,
    this.isRescheduled,
    this.reScheduleDateTime,
    this.ratingDetail,
  });

  OrderData.fromJson(Map<String, dynamic> json) {
    orderTrackingId = json['order_tracking_id'];
    id = json['id'];
    clientId = json['client_id'];
    clientName = json['client_name'];
    date = json['date'];
    pickupPoint = json['pickup_point'] != null ? new PickupPoint.fromJson(json['pickup_point']) : null;
    deliveryPoint = json['delivery_point'] != null ? new PickupPoint.fromJson(json['delivery_point']) : null;
    countryId = json['country_id'];
    countryName = json['country_name'];
    cityId = json['city_id'];
    cityName = json['city_name'];
    parcelType = json['parcel_type'];
    totalWeight = json['total_weight'];
    totalDistance = json['total_distance'];
    pickupDatetime = json['pickup_datetime'];
    deliveryDatetime = json['delivery_datetime'];
    parentOrderId = json['parent_order_id'];
    status = json['status'];
    paymentId = json['payment_id'];
    paymentType = json['payment_type'];
    paymentStatus = json['payment_status'];
    paymentCollectFrom = json['payment_collect_from'];
    deliveryManId = json['delivery_man_id'];
    deliveryManName = json['delivery_man_name'];
    bid_type = json['bid_type'];
    fixedCharges = json['fixed_charges'];
    vehicleCharge = json['vehicle_charge'];
    extraCharges = json['extra_charges'];
    totalAmount = json['total_amount'];
    reason = json['reason'];
    pickupConfirmByClient = json['pickup_confirm_by_client'];
    pickupConfirmByDeliveryMan = json['pickup_confirm_by_delivery_man'];
    pickupTimeSignature = json['pickup_time_signature'];
    deliveryTimeSignature = json['delivery_time_signature'];
    deletedAt = json['deleted_at'];
    returnOrderId = json['return_order_id'];
    weightCharge = json['weight_charge'];
    distanceCharge = json['distance_charge'];
    totalParcel = json['total_parcel'];
    autoAssign = json['auto_assign'];
    cancelledDeliverManIds = json['cancelled_delivery_man_ids'];
    vehicleId = json['vehicle_id'];
    vehicleData = json['vehicle_data'] != null ? new VehicleData.fromJson(json['vehicle_data']) : null;
    vehicleImage = json['vehicle_image'];
    invoice = json['invoice'];
    insuranceCharge = json['insurance_charge'];
    baseTotal = json['base_total'];
    isClaimed = json['isClaimed'];
    isRescheduled = json['is_reschedule'];
    reScheduleDateTime = json['rescheduledatetime'];
    // Fixing the issue for extraChargesList:
    extraChargesList = json['extra_charge_list'] != null ? List<ExtraCharges>.from(json['extra_charge_list'].map((x) => ExtraCharges.fromJson(x))) : [];

    cityDetails = json['city_details_list'] != null ? CityDetail.fromJson(json['city_details_list']) : null;
    packagingSymbols = json["packaging_symbols"] == null ? [] : List<PackagingSymbol>.from(json["packaging_symbols"]!.map((x) => PackagingSymbol.fromJson(x)));
    ratingDetail = json['rating_detail'] != null ? new RatingDetail.fromJson(json['rating_detail']) : null;
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['id'] = this.id;
    data['order_tracking_id'] = this.orderTrackingId;
    data['client_id'] = this.clientId;
    data['client_name'] = this.clientName;
    data['date'] = this.date;
    if (this.pickupPoint != null) {
      data['pickup_point'] = this.pickupPoint!.toJson();
    }
    if (this.deliveryPoint != null) {
      data['delivery_point'] = this.deliveryPoint!.toJson();
    }
    data['country_id'] = this.countryId;
    data['country_name'] = this.countryName;
    data['city_id'] = this.cityId;
    data['city_name'] = this.cityName;
    data['parcel_type'] = this.parcelType;
    data['total_weight'] = this.totalWeight;
    data['total_distance'] = this.totalDistance;
    data['pickup_datetime'] = this.pickupDatetime;
    data['delivery_datetime'] = this.deliveryDatetime;
    data['parent_order_id'] = this.parentOrderId;
    data['status'] = this.status;
    data['payment_id'] = this.paymentId;
    data['payment_type'] = this.paymentType;
    data['payment_status'] = this.paymentStatus;
    data['payment_collect_from'] = this.paymentCollectFrom;
    data['delivery_man_id'] = this.deliveryManId;
    data['delivery_man_name'] = this.deliveryManName;
    data['fixed_charges'] = this.fixedCharges;
    data['vehicle_charge'] = this.vehicleCharge;
    data['extra_charges'] = this.extraCharges;
    data['total_amount'] = this.totalAmount;
    data['reason'] = this.reason;
    data['pickup_confirm_by_client'] = this.pickupConfirmByClient;
    data['pickup_confirm_by_delivery_man'] = this.pickupConfirmByDeliveryMan;
    data['pickup_time_signature'] = this.pickupTimeSignature;
    data['delivery_time_signature'] = this.deliveryTimeSignature;
    data['deleted_at'] = this.deletedAt;
    data['return_order_id'] = this.returnOrderId;
    data['weight_charge'] = this.weightCharge;
    data['distance_charge'] = this.distanceCharge;
    data['total_parcel'] = this.totalParcel;
    data['auto_assign'] = this.autoAssign;
    data['cancelled_delivery_man_ids'] = this.cancelledDeliverManIds;
    data['bid_type'] = this.bid_type;
    data['vehicle_id'] = this.vehicleId;
    if (this.vehicleData != null) {
      data['vehicle_data'] = this.vehicleData!.toJson();
    }
    data['vehicle_image'] = this.vehicleImage;
    data['invoice'] = this.invoice;
    data['insurance_charge'] = this.insuranceCharge;
    data['base_total'] = this.baseTotal;
    data['isClaimed'] = this.isClaimed;
    data['extra_charge_list'] = this.extraChargesList;
    data['city_details_list'] = this.cityDetails;
    data['rescheduledatetime'] = this.reScheduleDateTime;
    data['is_reschedule'] = this.isRescheduled;
    data["packaging_symbols"] = packagingSymbols == null ? [] : List<PackagingSymbol>.from(this.packagingSymbols!.map((x) => x.toJson()));
    if (this.ratingDetail != null) {
      data['rating_detail'] = this.ratingDetail!.toJson();
    }
    return data;
  }
}

class RatingDetail {
  int? id;
  int? deliveryManId;
  int? clientId;
  int? orderId;
  int? rating;
  String? comment;
  String? ratingBy;
  String? createdAt;
  String? updatedAt;

  RatingDetail({this.id, this.deliveryManId, this.clientId, this.orderId, this.rating, this.comment, this.ratingBy, this.createdAt, this.updatedAt});

  RatingDetail.fromJson(Map<String, dynamic> json) {
    id = json['id'];
    deliveryManId = json['delivery_man_id'];
    clientId = json['client_id'];
    orderId = json['order_id'];
    rating = json['rating'];
    comment = json['comment'];
    ratingBy = json['rating_by'];
    createdAt = json['created_at'];
    updatedAt = json['updated_at'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['id'] = this.id;
    data['delivery_man_id'] = this.deliveryManId;
    data['client_id'] = this.clientId;
    data['order_id'] = this.orderId;
    data['rating'] = this.rating;
    data['comment'] = this.comment;
    data['rating_by'] = this.ratingBy;
    data['created_at'] = this.createdAt;
    data['updated_at'] = this.updatedAt;
    return data;
  }
}
