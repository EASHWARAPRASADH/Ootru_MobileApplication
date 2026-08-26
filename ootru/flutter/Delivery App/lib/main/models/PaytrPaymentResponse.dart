class PaytrPaymentResponse {
  String? status;
  String? data;

  PaytrPaymentResponse({this.status, this.data});

  PaytrPaymentResponse.fromJson(Map<String, dynamic> json) {
    status = json['status'];
    data = json['data'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['status'] = this.status;
    data['data'] = this.data;
    return data;
  }
}
