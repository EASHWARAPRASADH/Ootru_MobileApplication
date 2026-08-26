import 'dart:async';

import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:mighty_delivery/delivery/screens/DeliveryDashBoard.dart';
import 'package:mighty_delivery/extensions/colors.dart';
import 'package:mighty_delivery/extensions/extension_util/widget_extensions.dart';
import '../../extensions/shared_pref.dart';
import '../../main/utils/Constants.dart';
import 'package:flutter_inappwebview/flutter_inappwebview.dart';
import 'DashboardScreen.dart';

class LoadpaytrWebview extends StatefulWidget {
  String? content;
  String? status;
  LoadpaytrWebview({super.key, this.content, String? status});

  @override
  State<LoadpaytrWebview> createState() => _LoadpaytrWebviewState();
}

class _LoadpaytrWebviewState extends State<LoadpaytrWebview> {
  String finalUrl = "";
  @override
  void initState() {
    //   if (widget.status == "success") {
    // Timer(Duration(seconds: 5), () {
    //   if (mounted) {
    //     print("laodpaytr");
    //     DashboardScreen().launch(context, isNewTask: true);
    //   }
    // });
    //  }
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        leading: IconButton(
          icon: Icon(
            Icons.arrow_back,
            color: white,
          ),
          onPressed: () {
            Navigator.of(context).pop();
          },
        ),
      ),
      body: InAppWebView(
        initialData: InAppWebViewInitialData(
          data: widget.content!,
        ),
        onLoadStart: (controller, navigationAction) async {
          final uri = navigationAction?.data?.uri;
          final urlString = uri.toString();
          print("------onLoad start${urlString}");
          if (urlString.toString().contains("paytr-failed")) {
            Timer(Duration(seconds: 5), () {
              print("laodpaytr");
              if (getStringAsync(USER_TYPE) == DELIVERY_MAN) {
                DeliveryDashBoard().launch(context, isNewTask: true);
              } else {
                DashboardScreen().launch(context, isNewTask: true);
              }
            });
          }
          if (urlString.toString().contains("paytr-success")) {
            Timer(Duration(seconds: 5), () {
              print("laodpaytr");
              if (getStringAsync(USER_TYPE) == DELIVERY_MAN) {
                DeliveryDashBoard().launch(context, isNewTask: true);
              } else {
                DashboardScreen().launch(context, isNewTask: true);
              }
            });
          }
        },
        onUpdateVisitedHistory: (controller, webUri, isReload) async {
          print("------ononUpdateVisitedHistory${webUri}");
          final uri = webUri.toString();
          final urlString = uri.toString();
          print("------ononUpdateVisitedHistory${urlString}");
          if (urlString.toString().contains("paytr-failed")) {
            Timer(Duration(seconds: 5), () {
              print("laodpaytr");
              DashboardScreen().launch(context, isNewTask: true);
            });
          }
        },
        onLoadStop: (controller, navigationAction) async {
          final uri = navigationAction?.data?.uri;
          final urlString = uri.toString();
          print("------onLoadStop${finalUrl}----------${urlString}");
          if (finalUrl.contains("paytr-success")) {
            Timer(Duration(seconds: 5), () {
              //if (mounted) {
              print("laodpaytr");
              if (getStringAsync(USER_TYPE) == DELIVERY_MAN) {
                DeliveryDashBoard().launch(context, isNewTask: true);
              } else {
                DashboardScreen().launch(context, isNewTask: true);
              }
              //}
            });
          }
          if (finalUrl.contains("paytr-failed")) {
            print("--------------else part");
            Timer(Duration(seconds: 5), () {
              if (mounted) {
                print("laodpaytr");
                if (getStringAsync(USER_TYPE) == DELIVERY_MAN) {
                  DeliveryDashBoard().launch(context, isNewTask: true);
                } else {
                  DashboardScreen().launch(context, isNewTask: true);
                }
              }
            });
          }
        },
        shouldOverrideUrlLoading: (controller, navigationAction) async {
          var url = navigationAction.request.url.toString();
          print("URL----->>" + url.toString());
          finalUrl = url.toString();
          //    https: //www.cbkcabukkurye.com.tr/
          /* if (url.contains("paytr-success")) {
            Timer(Duration(seconds: 5), () {
              if (mounted) {
                print("laodpaytr");
                DashboardScreen().launch(context, isNewTask: true);
              }
            });
            return NavigationActionPolicy.CANCEL;
          }
          if (url.contains("paytr-failed")) {
            Timer(Duration(seconds: 5), () {
              if (mounted) {
                print("laodpaytr");
                DashboardScreen().launch(context, isNewTask: true);
              }
            });
            return NavigationActionPolicy.CANCEL;
          }*/

          return NavigationActionPolicy.ALLOW;
        },
      ),
    );
  }
}
