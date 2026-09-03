import 'dart:async';

import 'package:cached_network_image/cached_network_image.dart';
import 'package:crisp_chat/crisp_chat.dart';
import 'package:date_time_picker/date_time_picker.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:flutter_ringtone_player/flutter_ringtone_player.dart';
import 'package:flutter_vector_icons/flutter_vector_icons.dart';
import 'package:geocoding/geocoding.dart';
import 'package:geolocator/geolocator.dart';
import 'package:intl/intl.dart';
import 'package:mighty_delivery/delivery/fragment/DHomeFragment.dart';
import 'package:mighty_delivery/main/services/VersionServices.dart';
import '../../delivery/screens/OrdersMapScreen.dart';
import '../../extensions/app_text_field.dart';
import '../../extensions/extension_util/context_extensions.dart';
import '../../extensions/extension_util/int_extensions.dart';
import '../../extensions/extension_util/string_extensions.dart';
import '../../extensions/extension_util/widget_extensions.dart';
import '../../extensions/widgets.dart';
import '../../main/utils/Colors.dart';
import '../../main/utils/Widgets.dart';
import '../../main/utils/dynamic_theme.dart';

import '../../delivery/fragment/DProfileFragment.dart';
import '../../extensions/LiveStream.dart';
import '../../extensions/animatedList/animated_configurations.dart';
import '../../extensions/animatedList/animated_list_view.dart';
import '../../extensions/app_button.dart';
import '../../extensions/colors.dart';
import '../../extensions/common.dart';
import '../../extensions/confirmation_dialog.dart';
import '../../extensions/decorations.dart';
import '../../extensions/horizontal_list.dart';
import '../../extensions/shared_pref.dart';
import '../../extensions/system_utils.dart';
import '../../extensions/text_styles.dart';
import '../../main.dart';
import '../../main/components/CommonScaffoldComponent.dart';
import '../../main/models/CityListModel.dart';
import '../../main/models/OrderListModel.dart';
import '../../main/network/RestApis.dart';
import '../../main/screens/NotificationScreen.dart';
import '../../main/screens/UserCitySelectScreen.dart';
import '../../main/utils/Common.dart';
import '../../main/utils/Constants.dart';
import '../../main/utils/Images.dart';
import '../../main/services/AuthServices.dart'; // for sendOtp
import '../../user/screens/OrderDetailScreen.dart';
import '../components/OTPDialog.dart'; // for OTP dialog
import 'ReceivedScreenOrderScreen.dart';

class DeliveryDashBoard extends StatefulWidget {
  final int selectedIndex;
  final bool? isHistory;

  DeliveryDashBoard({this.selectedIndex = 0, this.isHistory});

  @override
  DeliveryDashBoardState createState() => DeliveryDashBoardState();
}

class DeliveryDashBoardState extends State<DeliveryDashBoard> with WidgetsBindingObserver {
  bool get isHistoryMode => widget.isHistory ?? (widget.selectedIndex == 6);
  List<String> statusList = [ORDER_PENDING, ORDER_ASSIGNED, ORDER_ACCEPTED, ORDER_ARRIVED, ORDER_PICKED_UP, ORDER_DEPARTED, ORDER_DELIVERED, ORDER_CANCELLED, ORDER_SHIPPED];
  ScrollController scrollController = ScrollController();
  ScrollController scrollController1 = ScrollController();
  PageController pageController = PageController();
  int currentPage = 1;
  int totalPage = 1;
  int selectedStatusIndex = 0;
  List<OrderData> orderData = [];
  GlobalKey<FormState> rescheduleFormKey = GlobalKey<FormState>();
  TextEditingController reasonTitleTextEditingController = TextEditingController();
  TextEditingController dateTextEditingController = TextEditingController();
  TextEditingController pickDateController = TextEditingController();
  DateTime? pickDate;
  bool _isExpanded = false;
  late CrispConfig configData;
  String? crispChatIcon;
  late List<GlobalKey> itemKeys;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    itemKeys = List.generate(statusList.length, (index) => GlobalKey());
    print("Selected Index ======== ${widget.selectedIndex}");
    init();
  }

  configureCrispChat() async {
    try {
      FlutterCrispChat.setSessionString(
        key: getIntAsync(USER_ID).toString(),
        value: getIntAsync(USER_ID).toString(),
      );

      /// Checking session ID After 5 sec
      await Future.delayed(const Duration(seconds: 5), () async {
        String? sessionId = await FlutterCrispChat.getSessionIdentifier();
        if (sessionId != null) {
          if (kDebugMode) {
            print("Session ID::: $sessionId");
          }
        } else {
          print("Session ID not  found::: ");
        }
      });
    } catch (e, stack) {
      print("error in crispchat${e.toString()}-----------$stack");
      toast(e.toString());
    }
  }

  void _toggleFAB() {
    setState(() {
      _isExpanded = !_isExpanded;
    });
  }

  configCrispChatData() {
    /// Config crispChat
    if (appStore.crispChatWebsiteId.isNotEmpty && appStore.isCrispChatEnabled) {
      User user = User(email: appStore.userEmail, nickName: "${getStringAsync(NAME)}", avatar: appStore.userProfile);
      FlutterCrispChat.resetCrispChatSession();
      configData = CrispConfig(user: user, tokenId: getIntAsync(USER_ID).toString(), enableNotifications: true, websiteID: appStore.crispChatWebsiteId);
    }
  }

  void init() async {
    appStore.setLoading(true);
    await getDashboardDetails();
    await configCrispChatData();
    LiveStream().on('UpdateLanguage', (p0) {
      setState(() {});
    });
    LiveStream().on('UpdateTheme', (p0) {
      setState(() {});
    });
    selectedStatusIndex = widget.selectedIndex;
    await getAppSetting().then((value) {
      print("-------------------------------${value.otpVerifyOnPickupDelivery}");
      appStore.setOtpVerifyOnPickupDelivery(value.otpVerifyOnPickupDelivery == 1);
      appStore.setCurrencyCode(value.currencyCode ?? CURRENCY_CODE);
      appStore.setCurrencySymbol(value.currency ?? CURRENCY_SYMBOL);
      appStore.setCurrencyPosition(value.currencyPosition ?? CURRENCY_POSITION_LEFT);
      appStore.isVehicleOrder = value.isVehicleInOrder ?? 0;
      appStore.setSiteEmail(value.siteEmail ?? "");
      appStore.setCopyRight(value.siteCopyright ?? "");
      //   appStore.setOrderTrackingIdPrefix(value.orderTrackingIdPrefix ?? "");
      appStore.setIsInsuranceAllowed(value.isInsuranceAllowed ?? "0");
      appStore.setInsurancePercentage(value.insurancePercentage ?? "0");
      appStore.setInsuranceDescription(value.insuranceDescription ?? "");
      appStore.setMaxAmountPerMonth(value.maxEarningsPerMonth ?? '');
      appStore.setClaimDuration(value.claimDuration ?? "");
      // setValue(IS_VERIFIED_DELIVERY_MAN, (value.isVerifiedDeliveryMan.validate() == 1));
    }).catchError((error) {
      log(error.toString());
    });
    if (await checkPermission()) {
      await checkLocationPermission(context);
    }
    scrollController.addListener(() {
      if (scrollController.position.pixels == scrollController.position.maxScrollExtent) {
        if (currentPage < totalPage) {
          appStore.setLoading(true);
          currentPage++;
          setState(() {});
          getOrderListApiCall();
        }
      }
    });
    if (selectedStatusIndex > 2) {
      scrollController1.animateTo(selectedStatusIndex * 100, duration: Duration(milliseconds: 500), curve: Curves.easeInOut);
      pageController.jumpToPage(selectedStatusIndex);
    }
    orderData.clear();
    await getOrderListApiCall();
    afterBuildCreated(() => appStore.setLoading(true));
  }

  getDashboardDetails() async {
    await getDashboardDetail().then((value) {
      if (value.deliverManVersion != null) {
        VersionService().getVersionData(context, value.deliverManVersion);
      }
      if (value.crispData != null) {
        if (value.crispData!.isCrispChatEnabled == null) {
          appStore.setIsCrispChatEnabled(false);
        } else {
          appStore.setIsCrispChatEnabled(value.crispData!.isCrispChatEnabled!);
        }
        appStore.setCrispChatWebsiteId(value.crispData!.crispChatWebsiteId!);
      }
      if (value.appSetting != null) {
        appStore.setIsSmsOrder(value.appSetting!.isSmsOrder ?? 0);
      }
    });
  }

  Future<void> checkLocationPermission(BuildContext context) async {
    initLocationStream();
  }

  void initLocationStream() async {
    positionStream?.cancel();

    LocationSettings locationSettings = LocationSettings(
      accuracy: LocationAccuracy.best,
      distanceFilter: 100,
    );
    positionStream = Geolocator.getPositionStream(locationSettings: locationSettings).listen((Position event) async {
      List<Placemark> placeMarks = await placemarkFromCoordinates(
        event.latitude,
        event.longitude,
      );
      try {
        if (placeMarks.isNotEmpty)
          updateUserStatus({
            "id": getIntAsync(USER_ID),
            "latitude": event.latitude.toString(),
            "longitude": event.longitude.toString(),
          }).then((value) {
            log("value...." + value.toString());
          });
      } catch (e) {}
    });
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    switch (state) {
      case AppLifecycleState.resumed:
        onResumed();
        break;
      default:
    }
  }

  void onResumed() async {
    await checkLocationPermission(context);
    // await getDashboardDetails();
    if (getStringAsync(USER_TYPE) == DELIVERY_MAN) {
      isSosVisible.value = true;
    } else {
      isSosVisible.value = false;
    }
    setState(() {});
  }

  getOrderListApiCall() async {
    appStore.setLoading(true);
    String fetchStatus = isHistoryMode ? 'history' : 'available';
    await getDeliveryBoyOrderList(
      page: currentPage,
      deliveryBoyID: getIntAsync(USER_ID),
      cityId: getIntAsync(CITY_ID),
      countryId: getIntAsync(COUNTRY_ID),
      orderStatus: fetchStatus,
    ).then((value) {
      appStore.setLoading(false);
      appStore.setAllUnreadCount(value.allUnreadCount.validate());
      currentPage = value.pagination!.currentPage!;
      totalPage = value.pagination!.totalPages!;
      if (currentPage == 1) {
        orderData.clear();
      }
      orderData.addAll(value.data!);
      setState(() {});
    }).catchError((error) {
      log(error);
    }).whenComplete(() {
      appStore.setLoading(false);
    });
  }

  Future<void> cancelOrder(OrderData order) async {
    appStore.setLoading(true);
    List<dynamic> cancelledDeliverManIds = order.cancelledDeliverManIds ?? [];
    cancelledDeliverManIds.add(getIntAsync(USER_ID));
    Map req = {
      "id": order.id,
      "cancelled_delivery_man_ids": cancelledDeliverManIds,
    };
    await cancelAutoAssignOrder(req).then((value) {
      appStore.setLoading(false);
      toast(value.message);
      getOrderListApiCall();
    }).catchError((error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    positionStream?.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return CommonScaffoldComponent(
      appBar: PreferredSize(
        preferredSize: Size(ContextExtensions(context).width(), 60),
        child: commonAppBarWidget(
          isHistoryMode ? 'Delivery History' : 'Available Orders',
          showBack: true,
          backWidget: IconButton(
            icon: Icon(Icons.arrow_back, color: Colors.white),
            onPressed: () => finish(context),
          ),
          actions: [
            IconButton(
              padding: EdgeInsets.only(right: 8),
              onPressed: () async {
                DProfileFragment().launch(context, pageRouteAnimation: PageRouteAnimation.SlideBottomTop);
              },
              icon: Icon(Ionicons.settings_outline, color: Colors.white),
            ),
          ],
        ),
      ),
      body: Stack(
        children: [
          orderData.isNotEmpty
              ? AnimatedListView(
                  itemCount: orderData.length,
                  shrinkWrap: true,
                  physics: BouncingScrollPhysics(),
                  padding: EdgeInsets.only(left: 16, right: 16, top: 16, bottom: 60),
                  onNextPage: () {
                    if (currentPage < totalPage) {
                      currentPage++;
                      setState(() {});
                      getOrderListApiCall();
                    }
                  },
                  onSwipeRefresh: () async {
                    currentPage = 1;
                    getOrderListApiCall();
                    return Future.value(true);
                  },
                  itemBuilder: (context, i) {
                    return orderCard(orderData[i]);
                  },
                )
              : Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      emptyWidget(),
                      12.height,
                      Text(
                        isHistoryMode ? 'No completed orders yet' : 'No available orders right now',
                        style: secondaryTextStyle(size: 14),
                      ),
                    ],
                  ),
                ).visible(!appStore.isLoading),
          Observer(builder: (context) => loaderWidget().visible(appStore.isLoading)),
        ],
      ),
    );
  }

  Widget orderCard(OrderData data) {
    return GestureDetector(
      onTap: () {
        OrderDetailScreen(orderId: data.id!).launch(context, pageRouteAnimation: PageRouteAnimation.SlideBottomTop);
      },
      child: Container(
        margin: EdgeInsets.only(bottom: 16),
        decoration: boxDecorationWithRoundedCorners(
          borderRadius: BorderRadius.circular(defaultRadius),
          border: Border.all(color: ColorUtils.colorPrimary.withOpacity(0.35)),
          backgroundColor: context.cardColor,
        ),
        padding: EdgeInsets.all(14),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('${language.order} #${data.id}', style: boldTextStyle(size: 15)),
                    4.height,
                    Text('${data.orderTrackingId.validate()}', style: boldTextStyle(size: 12, color: ColorUtils.colorPrimary)),
                  ],
                ),
                Container(
                  padding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: boxDecorationWithRoundedCorners(
                    backgroundColor: isHistoryMode ? Colors.green.withOpacity(0.12) : Colors.orange.withOpacity(0.12),
                    borderRadius: BorderRadius.circular(6),
                  ),
                  child: Text(
                    isHistoryMode ? 'Delivered' : 'Available',
                    style: boldTextStyle(color: isHistoryMode ? Colors.green : Colors.orange, size: 12),
                  ),
                ),
              ],
            ),
            10.height,
            Divider(height: 1, color: context.dividerColor),
            10.height,
            // From Location
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Icon(Icons.location_on, color: Colors.green, size: 20),
                8.width,
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('From: ${data.pickupPoint?.name.validate()}', style: boldTextStyle(size: 13)),
                      2.height,
                      Text('${data.pickupPoint?.address.validate()}', style: secondaryTextStyle(size: 12)),
                    ],
                  ),
                ),
                if (data.pickupPoint?.contactNumber != null && data.pickupPoint!.contactNumber!.isNotEmpty)
                  IconButton(
                    icon: Icon(Icons.phone, color: ColorUtils.colorPrimary, size: 20),
                    onPressed: () => commonLaunchUrl('tel:${data.pickupPoint!.contactNumber}'),
                  ),
              ],
            ),
            8.height,
            // To Location
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Icon(Icons.location_pin, color: Colors.red, size: 20),
                8.width,
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('To: ${data.deliveryPoint?.name.validate()}', style: boldTextStyle(size: 13)),
                      2.height,
                      Text('${data.deliveryPoint?.address.validate()}', style: secondaryTextStyle(size: 12)),
                    ],
                  ),
                ),
                if (data.deliveryPoint?.contactNumber != null && data.deliveryPoint!.contactNumber!.isNotEmpty)
                  IconButton(
                    icon: Icon(Icons.phone, color: ColorUtils.colorPrimary, size: 20),
                    onPressed: () => commonLaunchUrl('tel:${data.deliveryPoint!.contactNumber}'),
                  ),
              ],
            ),
            10.height,
            Divider(height: 1, color: context.dividerColor),
            8.height,
            // Parcel, Payment and Amount
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Parcel: ${data.parcelType.validate()}', style: secondaryTextStyle(size: 12)),
                    4.height,
                    Text('Payment: ${data.paymentType.validate().isNotEmpty ? data.paymentType : "Cash"} (${data.paymentStatus.validate().isNotEmpty ? data.paymentStatus : "Paid"})',
                        style: boldTextStyle(size: 12, color: Colors.green)),
                  ],
                ),
                Text('${printAmount(data.totalAmount ?? 0)}', style: boldTextStyle(size: 16, color: ColorUtils.colorPrimary)),
              ],
            ),
            if (!isHistoryMode) ...[
              12.height,
              SizedBox(
                width: double.infinity,
                child: AppButton(
                  text: 'Accept Order',
                  color: ColorUtils.colorPrimary,
                  textStyle: boldTextStyle(color: Colors.white, size: 14),
                  onTap: () {
                    showConfirmDialogCustom(
                      context,
                      primaryColor: ColorUtils.colorPrimary,
                      dialogType: DialogType.CONFIRMATION,
                      title: 'Accept this order for delivery?',
                      positiveText: language.yes,
                      negativeText: language.no,
                      onAccept: (c) async {
                        await acceptOrderWithOtp(data);
                      },
                    );
                  },
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }

  Future<void> acceptOrderWithOtp(OrderData data) async {
    FlutterRingtonePlayer().stop();
    final pickupContact = data.pickupPoint?.contactNumber.validate() ?? '';
    if (pickupContact.isNotEmpty) {
      sendOtp(
        context,
        phoneNumber: pickupContact,
        onUpdate: (verificationId) async {
          await showInDialog(
            context,
            barrierDismissible: false,
            builder: (ctx) => OTPDialog(
              phoneNumber: pickupContact,
              verificationId: verificationId,
              onUpdate: () async {
                Map req = {'order_id': data.id, 'status': ORDER_ACCEPTED};
                await updateOrderStatusForAssignedTab(req);
                toast('Order Accepted!');
                finish(context);
              },
            ),
          );
        },
      );
    } else {
      Map req = {'order_id': data.id, 'status': ORDER_ACCEPTED};
      await updateOrderStatusForAssignedTab(req);
      toast('Order Accepted!');
      finish(context);
    }
  }

  Future<void> onTapData({required String orderStatus, required OrderData orderData}) async {
    FlutterRingtonePlayer().stop();
    if (orderStatus == ORDER_ASSIGNED) {
      // OTP 4.1: Send OTP to pickup contact and require delivery boy to enter it
      final pickupContact = orderData.pickupPoint?.contactNumber.validate() ?? '';
      if (pickupContact.isNotEmpty) {
        sendOtp(
          context,
          phoneNumber: pickupContact,
          onUpdate: (verificationId) async {
            await showInDialog(
              context,
              barrierDismissible: false,
              builder: (ctx) => OTPDialog(
                phoneNumber: pickupContact,
                verificationId: verificationId,
                onUpdate: () async {
                  // OTP verified — now accept the order
                  Map req = {'order_id': orderData.id, 'status': ORDER_ACCEPTED};
                  await updateOrderStatusForAssignedTab(req).then((value) {
                    if (value.success == false) {
                      toast(value.message);
                      appStore.setLoading(false);
                      setState(() {});
                    } else {
                      print("---------res${value.message}");
                      int i = statusList.indexWhere((item) => item == ORDER_ASSIGNED);
                      pageController.jumpToPage(i + 1);
                      getOrderListApiCall();
                    }
                  });
                },
              ),
            );
          },
        );
      } else {
        // No contact number — accept without OTP
        Map req = {'order_id': orderData.id, 'status': ORDER_ACCEPTED};
        await updateOrderStatusForAssignedTab(req).then((value) {
          if (value.success == false) {
            toast(value.message);
            appStore.setLoading(false);
            setState(() {});
          } else {
            print("---------res${value.message}");
            int i = statusList.indexWhere((item) => item == ORDER_ASSIGNED);
            pageController.jumpToPage(i + 1);
            getOrderListApiCall();
          }
        });
      }
    } else if (orderStatus == ORDER_ACCEPTED) {
      if (orderData.pickupPoint!.startTime != null && orderData.pickupPoint!.endTime != null) {
        DateTime startTime = DateTime.parse(orderData.pickupPoint!.startTime!);
        DateTime endTime = DateTime.parse(orderData.pickupPoint!.endTime!);
        DateTime now = DateTime.now();
        // Check if the current time is between start and end times
        if (now.isAfter(startTime) && now.isBefore(endTime)) {
          bool isCheck = await ReceivedScreenOrderScreen(
                  orderData: orderData, isShowPayment: (orderData.paymentId == null || orderData.paymentId == 0) && orderData.paymentCollectFrom == PAYMENT_ON_PICKUP)
              .launch(context, pageRouteAnimation: PageRouteAnimation.SlideBottomTop);
          if (isCheck) {
            Future.delayed(Duration(seconds: 5));
            await getOrderListApiCall();
            int i = statusList.indexWhere((item) => item == ORDER_PICKED_UP);
            pageController.jumpToPage(i);
          }
        } else {
          toast(language.earlyPickupMsg);
        }
      } else {
        bool isCheck = await ReceivedScreenOrderScreen(
                orderData: orderData, isShowPayment: (orderData.paymentId == null || orderData.paymentId == 0) && orderData.paymentCollectFrom == PAYMENT_ON_PICKUP)
            .launch(context, pageRouteAnimation: PageRouteAnimation.SlideBottomTop);
        if (isCheck) {
          Future.delayed(Duration(seconds: 5));
          await getOrderListApiCall();
          int i = statusList.indexWhere((item) => item == ORDER_PICKED_UP);
          pageController.jumpToPage(i);
        }
      }
      // getOrderListApiCall();
    } else if (orderStatus == ORDER_ARRIVED) {
      bool isCheck = await ReceivedScreenOrderScreen(orderData: orderData, isShowPayment: orderData.paymentId == null && orderData.paymentCollectFrom == PAYMENT_ON_PICKUP)
          .launch(context, pageRouteAnimation: PageRouteAnimation.SlideBottomTop);
      if (isCheck) {
        getOrderListApiCall();
        int i = statusList.indexWhere((item) => item == ORDER_ARRIVED);
        pageController.jumpToPage(i + 1);
      }
    } else if (orderStatus == ORDER_PICKED_UP) {
      await updateOrder(orderStatus: ORDER_DEPARTED, orderId: orderData.id).then((value) {
        toast(language.orderDepartedSuccessfully);
        int i = statusList.indexWhere((item) => item == ORDER_PICKED_UP);
        pageController.jumpToPage(i + 1);
        getOrderListApiCall();
      });
    } else if (orderStatus == ORDER_DEPARTED) {
      DateTime startTime = DateTime.parse(orderData.pickupDatetime!);
      DateTime now = DateTime.now();
      // Check if the current time is between start and end times
      if (now.isAfter(startTime)) {
        bool isCheck = await ReceivedScreenOrderScreen(orderData: orderData, isShowPayment: orderData.paymentId == null && orderData.paymentCollectFrom == PAYMENT_ON_DELIVERY)
            .launch(context, pageRouteAnimation: PageRouteAnimation.SlideBottomTop);
        if (isCheck) {
          int i = statusList.indexWhere((item) => item == ORDER_DEPARTED);
          pageController.jumpToPage(i + 1);
          getOrderListApiCall();
        }
      } else {
        toast(language.earlyDeliveryMsg);
      }
    }
  }

  buttonText(String orderStatus) {
    if (orderStatus == ORDER_ASSIGNED) {
      return language.accept;
    } else if (orderStatus == ORDER_ACCEPTED) {
      return language.pickUp;
    } else if (orderStatus == ORDER_ARRIVED) {
      return language.pickUp;
    } else if (orderStatus == ORDER_PICKED_UP) {
      return language.departed;
    } else if (orderStatus == ORDER_DEPARTED) {
      return language.confirmDelivery;
    }
    return '';
  }
}
