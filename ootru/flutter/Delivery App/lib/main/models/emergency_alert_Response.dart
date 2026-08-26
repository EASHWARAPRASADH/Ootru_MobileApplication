// class AlertMessageResponse {
//   String? message;
//   bool? status;
//
//   AlertMessageResponse({this.message});
//
//   AlertMessageResponse.fromJson(Map<String, dynamic> json) {
//     message = json['message'] != null ? new Message.fromJson(json['message']) : null;
//   }
//
//   Map<String, dynamic> toJson() {
//     final Map<String, dynamic> data = new Map<String, dynamic>();
//     if (this.message != null) {
//       data['message'] = this.message!.toJson();
//     }
//     return data;
//   }
// }

class AlertMessageResponse {
  String? message;
  int? id;
  bool? status;

  AlertMessageResponse({this.message, this.id});

  AlertMessageResponse.fromJson(Map<String, dynamic> json) {
    message = json['message'];
    status = json['status'];
    id = json['id'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['message'] = this.message;
    data['status'] = this.status;
    data['id'] = this.id;
    return data;
  }
}
