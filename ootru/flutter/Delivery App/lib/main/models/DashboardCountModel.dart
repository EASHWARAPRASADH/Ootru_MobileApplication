class DashboardCount {
  int? todayOrder;
  int? pendingOrder;
  int? inprogressOrder;
  int? completeOrder;
  num? commission;
  num? walletBalance;
  num? pendingWithdrawRequest;
  num? completeWithdrawRequest;
  bool? isEmergency;

  DashboardCount({this.todayOrder, this.pendingOrder, this.inprogressOrder, this.completeOrder, this.commission, this.walletBalance, this.pendingWithdrawRequest, this.completeWithdrawRequest, this.isEmergency});

  factory DashboardCount.fromJson(Map<String, dynamic> json) => DashboardCount(
        todayOrder: json["today_order"],
        pendingOrder: json["pending_order"],
        inprogressOrder: json["inprogress_order"],
        completeOrder: json["complete_order"],
        commission: json["commission"],
        walletBalance: json["wallet_balance"],
        pendingWithdrawRequest: json["pending_withdraw_request"],
        completeWithdrawRequest: json["complete_withdraw_request"],
        isEmergency: json["is_emergency"],
      );

  Map<String, dynamic> toJson() => {
        "today_order": todayOrder,
        "pending_order": pendingOrder,
        "inprogress_order": inprogressOrder,
        "complete_order": completeOrder,
        "commission": commission,
        "wallet_balance": walletBalance,
        "pending_withdraw_request": pendingWithdrawRequest,
        "complete_withdraw_request": completeWithdrawRequest,
        "is_emergency": isEmergency,
      };
}
