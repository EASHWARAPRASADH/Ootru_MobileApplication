class EmergencyPendingListResonse {
  String? message;
  List<EmergencyItem>? data;
  bool? status;

  EmergencyPendingListResonse({this.message, this.data, this.status});

  EmergencyPendingListResonse.fromJson(Map<String, dynamic> json) {
    message = json['message'];
    if (json['data'] != null) {
      data = <EmergencyItem>[];
      json['data'].forEach((v) {
        data!.add(new EmergencyItem.fromJson(v));
      });
    }
    status = json['status'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['message'] = this.message;
    if (this.data != null) {
      data['data'] = this.data!.map((v) => v.toJson()).toList();
    }
    data['status'] = this.status;
    return data;
  }
}

class EmergencyItem {
  int? id;
  int? deliveryManId;
  String? datetime;
  String? emrgencyReason;
  String? emergencyResolved;
  int? status;
  String? createdAt;
  String? updatedAt;

  EmergencyItem({this.id, this.deliveryManId, this.datetime, this.emrgencyReason, this.emergencyResolved, this.status, this.createdAt, this.updatedAt});

  EmergencyItem.fromJson(Map<String, dynamic> json) {
    id = json['id'];
    deliveryManId = json['delivery_man_id'];
    datetime = json['datetime'];
    emrgencyReason = json['emrgency_reason'];
    emergencyResolved = json['emergency_resolved'];
    status = json['status'];
    createdAt = json['created_at'];
    updatedAt = json['updated_at'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['id'] = this.id;
    data['delivery_man_id'] = this.deliveryManId;
    data['datetime'] = this.datetime;
    data['emrgency_reason'] = this.emrgencyReason;
    data['emergency_resolved'] = this.emergencyResolved;
    data['status'] = this.status;
    data['created_at'] = this.createdAt;
    data['updated_at'] = this.updatedAt;
    return data;
  }
}
