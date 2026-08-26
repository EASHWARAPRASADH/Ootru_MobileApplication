class SOSContactsListResponse {
  bool? status;
  List<ContactItem>? data;

  SOSContactsListResponse({this.status, this.data});

  SOSContactsListResponse.fromJson(Map<String, dynamic> json) {
    status = json['status'];
    if (json['data'] != null) {
      data = <ContactItem>[];
      json['data'].forEach((v) {
        data!.add(new ContactItem.fromJson(v));
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

class ContactItem {
  int? id;
  int? deliveryManId;
  String? name;
  String? contactNumber;
  String? createdAt;
  String? updatedAt;

  ContactItem({this.id, this.deliveryManId, this.name, this.contactNumber, this.createdAt, this.updatedAt});

  ContactItem.fromJson(Map<String, dynamic> json) {
    id = json['id'];
    deliveryManId = json['delivery_man_id'];
    name = json['name'];
    contactNumber = json['contact_number'];
    createdAt = json['created_at'];
    updatedAt = json['updated_at'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['id'] = this.id;
    data['delivery_man_id'] = this.deliveryManId;
    data['name'] = this.name;
    data['contact_number'] = this.contactNumber;
    data['created_at'] = this.createdAt;
    data['updated_at'] = this.updatedAt;
    return data;
  }
}
