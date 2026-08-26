class PayTrPaymentsListModel {
  String? status;
  List<PaytrPaymentItem>? data;

  PayTrPaymentsListModel({this.status, this.data});

  PayTrPaymentsListModel.fromJson(Map<String, dynamic> json) {
    status = json['status'];
    if (json['data'] != null) {
      data = <PaytrPaymentItem>[];
      json['data'].forEach((v) {
        data!.add(new PaytrPaymentItem.fromJson(v));
      });
    }
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['status'] = this.status;
    if (this.data != null) {
      data['data'] = this.data!.map((v) => v.toJson()).toList();
    }
    return data;
  }
}

class PaytrPaymentItem {
  int? id;
  int? merchantOid;
  int? clientId;
  int? merchantId;
  String? hash;
  String? datetime;
  num? totalAmount;
  String? paymentType;
  String? paymentStatus;
  String? createdAt;
  String? updatedAt;

  PaytrPaymentItem({this.id, this.merchantOid, this.clientId, this.merchantId, this.hash, this.datetime, this.totalAmount, this.paymentType, this.paymentStatus, this.createdAt, this.updatedAt});

  PaytrPaymentItem.fromJson(Map<String, dynamic> json) {
    id = json['id'];
    merchantOid = json['merchant_oid'];
    clientId = json['client_id'];
    merchantId = json['merchant_id'];
    hash = json['hash'];
    datetime = json['datetime'];
    totalAmount = json['total_amount'];
    paymentType = json['payment_type'];
    paymentStatus = json['payment_status'];
    createdAt = json['created_at'];
    updatedAt = json['updated_at'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['id'] = this.id;
    data['merchant_oid'] = this.merchantOid;
    data['client_id'] = this.clientId;
    data['merchant_id'] = this.merchantId;
    data['hash'] = this.hash;
    data['datetime'] = this.datetime;
    data['total_amount'] = this.totalAmount;
    data['payment_type'] = this.paymentType;
    data['payment_status'] = this.paymentStatus;
    data['created_at'] = this.createdAt;
    data['updated_at'] = this.updatedAt;
    return data;
  }
}
