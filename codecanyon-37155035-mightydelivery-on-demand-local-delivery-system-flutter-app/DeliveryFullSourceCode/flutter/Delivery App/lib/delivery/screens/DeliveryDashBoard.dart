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
import '../../user/screens/OrderDetailScreen.dart';
import 'ReceivedScreenOrderScreen.dart';

class DeliveryDashBoard extends StatefulWidget {
  final int selectedIndex;

  DeliveryDashBoard({this.selectedIndex = 0});

  @override
  DeliveryDashBoardState createState() => DeliveryDashBoardState();
}

class DeliveryDashBoardState extends State<DeliveryDashBoard> with WidgetsBindingObserver {
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
    await getDeliveryBoyOrderList(
            page: currentPage, deliveryBoyID: getIntAsync(USER_ID), cityId: getIntAsync(CITY_ID), countryId: getIntAsync(COUNTRY_ID), orderStatus: statusList[selectedStatusIndex])
        .then((value) {
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
        preferredSize: Size(ContextExtensions(context).width(), 110),
        child: commonAppBarWidget(
          '${language.hey} ${getStringAsync(NAME)} 👋',
          showBack: false,
          actions: [
            Container(
              margin: .symmetric(vertical: 12, horizontal: 8),
              padding: .symmetric(horizontal: 8, vertical: 4),
              decoration: boxDecorationWithRoundedCorners(borderRadius: radius(defaultRadius), backgroundColor: Colors.white24),
              child: Row(children: [
                Icon(Ionicons.ios_location_outline, color: Colors.white, size: 18),
                8.width,
                Text(CityModel.fromJson(getJSONAsync(CITY_DATA)).name.validate(), style: primaryTextStyle(color: white)),
              ]).onTap(() {
                UserCitySelectScreen(
                  isBack: true,
                  onUpdate: () {
                    currentPage = 1;
                    getOrderListApiCall();
                    setState(() {});
                  },
                ).launch(context);
              }, highlightColor: Colors.transparent, hoverColor: Colors.transparent, splashColor: Colors.transparent),
            ),
            Stack(
              clipBehavior: Clip.none,
              children: [
                Align(alignment: AlignmentDirectional.center, child: Icon(Ionicons.md_notifications_outline, color: Colors.white)),
                Observer(builder: (context) {
                  return Positioned(
                    right: 0,
                    top: 2,
                    child: Container(
                        height: 20,
                        width: 20,
                        alignment: Alignment.center,
                        decoration: BoxDecoration(color: Colors.orange, shape: BoxShape.circle),
                        child: Text('${appStore.allUnreadCount < 99 ? appStore.allUnreadCount : '99+'}',
                            style: primaryTextStyle(size: appStore.allUnreadCount < 99 ? 12 : 8, color: Colors.white))),
                  ).visible(appStore.allUnreadCount != 0);
                }),
              ],
            ).withWidth(30).onTap(() {
              NotificationScreen().launch(context);
            }),
            IconButton(
              onPressed: () async {
                DHomeFragment().launch(context, pageRouteAnimation: PageRouteAnimation.SlideBottomTop, isNewTask: true);
              },
              icon: Icon(Ionicons.stats_chart_outline, color: Colors.white),
            ),
            IconButton(
              padding: .only(right: 8),
              onPressed: () async {
                DProfileFragment().launch(context, pageRouteAnimation: PageRouteAnimation.SlideBottomTop);
              },
              icon: Icon(Ionicons.settings_outline, color: Colors.white),
            ),
          ],
          bottom: PreferredSize(
            preferredSize: Size(ContextExtensions(context).width(), 100),
            child: HorizontalList(
              controller: scrollController1,
              itemCount: statusList.length,
              itemBuilder: (ctx, index) {
                return KeyedSubtree(
                  key: itemKeys[index],
                  child: Theme(
                    data: ThemeData(splashColor: Colors.transparent, highlightColor: Colors.transparent),
                    child: Text(orderStatus(statusList[index]),
                            style: statusList[selectedStatusIndex] == statusList[index] ? boldTextStyle(color: Colors.white) : secondaryTextStyle(color: Colors.white70))
                        .paddingAll(8)
                        .onTap(() {
                      currentPage = 1;
                      selectedStatusIndex = statusList.indexWhere((item) => item == statusList[index]);
                      pageController.jumpToPage(selectedStatusIndex);
                      //   getOrderListApiCall();
                      setState(() {});
                    }),
                  ),
                );
              },
            ).paddingOnly(left: 6, right: 6),
          ),
        ),
      ),
      body: Stack(
        children: [
          PageView(
            controller: pageController,
            onPageChanged: (value) {
              selectedStatusIndex = statusList.indexWhere((item) => item == statusList[value]);
              orderData.clear();
              final ctx = itemKeys[selectedStatusIndex].currentContext;
              if (ctx != null) {
                Scrollable.ensureVisible(
                  ctx,
                  alignment: 0.5, // 0.5 = center, 0.0 = top/left, 1.0 = end/right
                  duration: const Duration(milliseconds: 200),
                  curve: Curves.easeOut,
                );
              }
              getOrderListApiCall();
              setState(() {});
            },
            children: statusList.map((e) {
              return Stack(
                children: [
                  AnimatedListView(
                    itemCount: orderData.length,
                    shrinkWrap: true,
                    physics: BouncingScrollPhysics(),
                    listAnimationType: ListAnimationType.Slide,
                    padding: .only(left: 16, right: 16, top: 16, bottom: 60),
                    flipConfiguration: FlipConfiguration(duration: Duration(seconds: 1), curve: Curves.fastOutSlowIn),
                    fadeInConfiguration: FadeInConfiguration(duration: Duration(seconds: 1), curve: Curves.fastOutSlowIn),
                    onNextPage: () {
                      if (currentPage < totalPage) {
                        currentPage++;
                        setState(() {});
                        getOrderListApiCall();
                      }
                    },
                    onSwipeRefresh: () async {
                      currentPage = 1;
                      await getAppSetting().then((value) {
                        appStore.setOtpVerifyOnPickupDelivery(value.otpVerifyOnPickupDelivery == 1);
                        appStore.setCurrencyCode(value.currencyCode ?? CURRENCY_CODE);
                        appStore.setCurrencySymbol(value.currency ?? CURRENCY_SYMBOL);
                        appStore.setCurrencyPosition(value.currencyPosition ?? CURRENCY_POSITION_LEFT);
                        appStore.isVehicleOrder = value.isVehicleInOrder ?? 0;
                        appStore.setSiteEmail(value.siteEmail ?? "");
                        appStore.setCopyRight(value.siteCopyright ?? "");
                        appStore.setIsInsuranceAllowed(value.isInsuranceAllowed ?? "0");
                        appStore.setInsurancePercentage(value.insurancePercentage ?? "0");
                        //   appStore.setOrderTrackingIdPrefix(value.orderTrackingIdPrefix ?? "");
                        appStore.setInsuranceDescription(value.insuranceDescription ?? "");
                        appStore.setMaxAmountPerMonth(value.maxEarningsPerMonth ?? '');
                        appStore.setClaimDuration(value.claimDuration ?? '');
                      }).catchError((error) {
                        log(error.toString());
                      });
                      getOrderListApiCall();
                      return Future.value(true);
                    },
                    itemBuilder: (context, i) {
                      OrderData item = orderData[i];
                      return item.status != ORDER_DRAFT ? orderCard(item) : SizedBox();
                    },
                  ).visible(orderData.length > 0),
                  loaderWidget().visible(appStore.isLoading),
                  emptyWidget().visible(orderData.length == 0 && !appStore.isLoading),
                ],
              );
            }).toList(),
          ),
        ],
      ),
      floatingActionButton: (appStore.isCrispChatEnabled == true)
          ? Stack(
              alignment: Alignment.bottomRight,
              children: [
                // Background tap to close (optional)
                if (_isExpanded)
                  GestureDetector(
                    onTap: _toggleFAB,
                    child: Container(
                      color: Colors.transparent,
                      width: double.infinity,
                      height: double.infinity,
                    ),
                  ),

                // Buttons with animation
                AnimatedPositioned(
                  duration: Duration(milliseconds: 350),
                  bottom: _isExpanded ? 150 : 20,
                  right: 10,
                  child: AnimatedOpacity(
                    duration: Duration(milliseconds: 300),
                    opacity: _isExpanded ? 1 : 0,
                    child: FloatingActionButton(
                      shape: RoundedRectangleBorder(borderRadius: radius(40)),
                      backgroundColor: appStore.availableBal >= 0 ? ColorUtils.colorPrimary : textSecondaryColorGlobal,
                      child: Icon(Icons.pin_drop_outlined, color: Colors.white),
                      onPressed: () {
                        OrdersMapScreen().launch(context);
                      },
                    ),
                  ),
                ),

                AnimatedPositioned(
                  duration: Duration(milliseconds: 300),
                  bottom: _isExpanded ? 80 : 20,
                  right: 10,
                  child: AnimatedOpacity(
                    duration: Duration(milliseconds: 300),
                    opacity: _isExpanded ? 1 : 0,
                    child: FloatingActionButton(
                        onPressed: () async {
                          configureCrispChat();
                          await FlutterCrispChat.openCrispChat(config: configData);
                        },
                        backgroundColor: ColorUtils.colorPrimary,
                        // Use your app's primary color
                        child: CachedNetworkImage(
                          imageUrl: crispChatIcon ?? "",
                          errorWidget: (context, url, error) => Icon(Icons.chat_bubble_outline),
                        )),
                  ),
                ),
                // Main FAB
                FloatingActionButton(
                  // heroTag: "main",
                  onPressed: _toggleFAB,
                  backgroundColor: ColorUtils.colorPrimary,
                  child: Icon(_isExpanded ? Icons.close : Icons.menu),
                ).paddingAll(10),
              ],
            )
          : FloatingActionButton(
              shape: RoundedRectangleBorder(borderRadius: radius(40)),
              backgroundColor: appStore.availableBal >= 0 ? ColorUtils.colorPrimary : textSecondaryColorGlobal,
              child: Icon(Icons.pin_drop_outlined, color: Colors.white),
              onPressed: () {
                OrdersMapScreen().launch(context);
              },
            ).paddingAll(10),
      floatingActionButtonLocation: FloatingActionButtonLocation.endDocked,
    );
  }

  Widget orderCard(OrderData data) {
    return GestureDetector(
      child: Container(
        margin: .only(bottom: 16),
        decoration: boxDecorationWithRoundedCorners(
            borderRadius: BorderRadius.circular(defaultRadius), border: Border.all(color: ColorUtils.colorPrimary), backgroundColor: Colors.transparent),
        padding: .all(12),
        child: Column(
          mainAxisAlignment: .start,
          crossAxisAlignment: .start,
          children: [
            Row(
              children: [
                Container(
                  height: 50,
                  child: Column(
                    crossAxisAlignment: .start,
                    children: [
                      Text('${language.order}# ${data.id}', style: boldTextStyle(size: 14)).expand(),
                      Text('${data.orderTrackingId}', style: boldTextStyle(size: 12, color: ColorUtils.colorPrimary)).expand(),
                    ],
                  ),
                ).expand(),
                Container(
                  decoration: boxDecorationWithRoundedCorners(
                      backgroundColor: appStore.isDarkMode ? ColorUtils.scaffoldSecondaryDark : ColorUtils.colorPrimaryLight,
                      borderRadius: BorderRadius.circular(defaultRadius),
                      border: Border.all(color: ColorUtils.colorPrimary.withOpacity(0.5))),
                  padding: .symmetric(horizontal: 4, vertical: 4),
                  child: Icon(
                    Icons.navigation_outlined,
                    color: ColorUtils.colorPrimary,
                    size: 28,
                  ),
                )
                    .onTap(() async {
                      Position position = await Geolocator.getCurrentPosition(desiredAccuracy: LocationAccuracy.high);

                      double currentLat = position.latitude;
                      double currentLng = position.longitude;
                      if (data.status == ORDER_ACCEPTED) {
                        openMap(position.latitude, position.longitude, double.parse(data.pickupPoint!.latitude.validate()), double.parse(data.pickupPoint!.longitude.validate()));
                      } else if (data.status == ORDER_DEPARTED) {
                        //
                        openMap(
                            position.latitude, position.longitude, double.parse(data.deliveryPoint!.latitude.validate()), double.parse(data.deliveryPoint!.longitude.validate()));
                      } else {
                        openMap(double.parse(data.pickupPoint!.latitude.validate()), double.parse(data.pickupPoint!.longitude.validate()),
                            double.parse(data.deliveryPoint!.latitude.validate()), double.parse(data.deliveryPoint!.longitude.validate()));
                      }
                      // openMap(double.parse(data.pickupPoint!.latitude.validate()), double.parse(data.pickupPoint!.longitude.validate()), double.parse(data.deliveryPoint!.latitude.validate()), double.parse(data.deliveryPoint!.longitude.validate()));
                    })
                    .paddingSymmetric(horizontal: 5)
                    .visible(data.status != ORDER_DELIVERED && data.status != ORDER_CANCELLED && data.status != ORDER_SHIPPED),
                Container(
                  decoration: boxDecorationWithRoundedCorners(
                      borderRadius: BorderRadius.circular(defaultRadius), border: Border.all(color: Colors.red), backgroundColor: Colors.red.withOpacity(0.2)),
                  padding: .symmetric(horizontal: 4, vertical: 4),
                  child: Icon(
                    Icons.close,
                    color: Colors.red,
                    size: 28,
                  ),
                ).onTap(() {
                  showConfirmDialogCustom(
                    context,
                    primaryColor: Colors.red,
                    dialogType: DialogType.CONFIRMATION,
                    title: language.orderCancelConfirmation,
                    positiveText: language.yes,
                    negativeText: language.no,
                    onAccept: (c) async {
                      await cancelOrder(data);
                    },
                  );
                }).visible(data.status == ORDER_ASSIGNED),
                (statusList[selectedStatusIndex] == ORDER_ASSIGNED || statusList[selectedStatusIndex] == ORDER_PENDING)
                    ? Container(
                        decoration: boxDecorationWithRoundedCorners(
                            borderRadius: BorderRadius.circular(defaultRadius), border: Border.all(color: ColorUtils.colorPrimary), backgroundColor: ColorUtils.colorPrimary),
                        padding: .symmetric(horizontal: 4, vertical: 4),
                        child: Icon(Icons.check, color: Colors.white, size: 28),
                      ).onTap(() {
                        FlutterRingtonePlayer().stop();
                        showConfirmDialogCustom(
                          context,
                          primaryColor: ColorUtils.colorPrimary,
                          dialogType: DialogType.CONFIRMATION,
                          title: orderTitle(statusList[selectedStatusIndex]),
                          positiveText: language.yes,
                          negativeText: language.no,
                          onAccept: (c) async {
                            appStore.setLoading(true);
                            await onTapData(orderData: data, orderStatus: statusList[selectedStatusIndex]);
                            appStore.setLoading(false);
                          },
                        );
                      }).paddingSymmetric(horizontal: 5)
                    : SizedBox(),
                (statusList[selectedStatusIndex] != ORDER_CANCELLED && statusList[selectedStatusIndex] != ORDER_ASSIGNED && statusList[selectedStatusIndex] != ORDER_PENDING)
                    ? AppButton(
                        elevation: 0,
                        text: buttonText(statusList[selectedStatusIndex]),
                        padding: .symmetric(vertical: 4, horizontal: 8),
                        textStyle: boldTextStyle(color: Colors.white, size: 14),
                        color: ColorUtils.colorPrimary,
                        onTap: () {
                          if (statusList[selectedStatusIndex] == ORDER_ACCEPTED) {
                            onTapData(orderData: data, orderStatus: statusList[selectedStatusIndex]);
                          } else if (statusList[selectedStatusIndex] == ORDER_ARRIVED) {
                            onTapData(orderData: data, orderStatus: statusList[selectedStatusIndex]);
                          } else if (statusList[selectedStatusIndex] == ORDER_DEPARTED) {
                            int val = 0;
                            return showInDialog(
                              barrierDismissible: true,
                              getContext,
                              builder: (p0) {
                                return StatefulBuilder(builder: (context, selectedImagesUpdate) {
                                  // This is used to toggle the visibility of the reschedule form

                                  return Form(
                                    key: rescheduleFormKey,
                                    child: SingleChildScrollView(
                                      child: Container(
                                        child: !appStore.isLoading
                                            ? Column(
                                                mainAxisSize: MainAxisSize.min,
                                                mainAxisAlignment: .start,
                                                crossAxisAlignment: .start,
                                                children: [
                                                  Row(
                                                    children: [
                                                      // Reschedule button - shows the reschedule form
                                                      commonButton(language.reschedule, size: 12, () {
                                                        selectedImagesUpdate(() {
                                                          val = 1;
                                                          print("$val"); // This will make the reschedule form visible
                                                        });
                                                      }).expand(),

                                                      2.width,

                                                      // Departed button - triggers the API call and hides the form
                                                      commonButton(
                                                        language.confirmDelivery,
                                                        size: 12,
                                                        () async {
                                                          if (context.mounted) {
                                                            Navigator.pop(context);
                                                          }
                                                          onTapData(orderData: data, orderStatus: statusList[selectedStatusIndex]);
                                                        },
                                                      ).expand(),
                                                    ],
                                                  ).visible(val == 0),

                                                  // Reschedule form (only visible when val == 1)
                                                  Column(
                                                    mainAxisAlignment: .start,
                                                    crossAxisAlignment: .start,
                                                    children: [
                                                      Text(language.rescheduleTitle, style: boldTextStyle(), textAlign: TextAlign.start),
                                                      10.height,
                                                      Divider(color: dividerColor, height: 1),
                                                      8.height,

                                                      // Reason text field
                                                      Text(language.reason, style: boldTextStyle()),
                                                      12.height,
                                                      AppTextField(
                                                        isValidationRequired: true,
                                                        controller: reasonTitleTextEditingController,
                                                        textFieldType: TextFieldType.NAME,
                                                        errorThisFieldRequired: language.fieldRequiredMsg,
                                                        decoration: commonInputDecoration(hintText: language.reason),
                                                      ),
                                                      8.height,

                                                      // Date picker
                                                      Text(language.date, style: boldTextStyle()),
                                                      12.height,
                                                      DateTimePicker(
                                                        controller: pickDateController,
                                                        type: DateTimePickerType.date,
                                                        initialDate: DateTime.now(),
                                                        firstDate: DateTime.now(),
                                                        lastDate: DateTime.now().add(Duration(days: 30)),
                                                        onChanged: (value) {
                                                          pickDate = DateTime.parse(value);
                                                        },
                                                        validator: (value) {
                                                          if (value!.isEmpty) return language.fieldRequiredMsg;
                                                          return null;
                                                        },
                                                        decoration: commonInputDecoration(suffixIcon: Icons.calendar_today, hintText: language.date),
                                                      ),

                                                      16.height,

                                                      // Buttons inside the reschedule form
                                                      Row(
                                                        children: [
                                                          commonButton(language.cancel, size: 14, () {
                                                            finish(getContext, 0); // Close the dialog
                                                          }).expand(),

                                                          6.width,

                                                          // Reschedule button inside the form
                                                          commonButton(language.reschedule, size: 14, () async {
                                                            if (rescheduleFormKey.currentState!.validate()) {
                                                              // Trigger the reschedule API call
                                                              // Example API call
                                                              Map request = {
                                                                "order_id": data.id,
                                                                "reason": reasonTitleTextEditingController.text.toString(),
                                                                "date": DateFormat('yyyy-MM-dd').format(pickDate!),
                                                              };
                                                              appStore.setLoading(true);
                                                              await rescheduleOrder(request).then((value) {
                                                                toast(value.message);
                                                                appStore.setLoading(false);
                                                                finish(context);
                                                              });
                                                            }
                                                          }).expand(),
                                                        ],
                                                      ),
                                                    ],
                                                  ).visible(val == 1),
                                                  // This makes the form visible based on the value of "val"
                                                ],
                                              )
                                            : Observer(builder: (context) => loaderWidget().visible(appStore.isLoading)).center(),
                                      ),
                                    ),
                                  );
                                });
                              },
                            );
                            //    onTapData(orderData: data, orderStatus: statusList[selectedStatusIndex]);
                          } else {
                            showConfirmDialogCustom(
                              context,
                              primaryColor: ColorUtils.colorPrimary,
                              dialogType: DialogType.CONFIRMATION,
                              title: orderTitle(statusList[selectedStatusIndex]),
                              positiveText: language.yes,
                              negativeText: language.no,
                              onAccept: (c) async {
                                appStore.setLoading(true);
                                await onTapData(orderData: data, orderStatus: statusList[selectedStatusIndex]);
                                appStore.setLoading(false);
                                // finish(context);
                              },
                            );
                          }
                        },
                      )
                        .visible(statusList[selectedStatusIndex] != ORDER_DELIVERED && statusList[selectedStatusIndex] != ORDER_SHIPPED)
                        .paddingOnly(right: appStore.selectedLanguage == "ar" ? 10 : 0)
                    : SizedBox()
              ],
            ),
            8.height,
            Column(
              crossAxisAlignment: .start,
              children: [
                if (data.pickupDatetime != null)
                  Column(
                    crossAxisAlignment: .start,
                    children: [
                      Text(language.picked, style: secondaryTextStyle(size: 12)),
                      4.height,
                      Text('${language.at} ${printDateWithoutAt("${data.pickupDatetime!}Z")}', style: secondaryTextStyle(size: 12)),
                    ],
                  ),
                4.height,
                Row(
                  children: [
                    Column(
                      children: [
                        GestureDetector(
                          onTap: () {
                            OrderDetailScreen(orderId: data.id!).launch(context, pageRouteAnimation: PageRouteAnimation.SlideBottomTop, duration: Duration(milliseconds: 400));
                          },
                          child: Row(
                            children: [
                              ImageIcon(AssetImage(ic_from), size: 24, color: ColorUtils.colorPrimary),
                              12.width,
                              Text('${data.pickupPoint!.address}', style: primaryTextStyle(size: 14)).expand(),
                            ],
                          ),
                        ),
                      ],
                    ).expand(),
                    12.width,
                    if (data.pickupPoint!.contactNumber != null && data.status != COMPLETED)
                      Icon(Ionicons.ios_call_outline, size: 20, color: ColorUtils.colorPrimary).onTap(() {
                        commonLaunchUrl('tel:${data.pickupPoint!.contactNumber}');
                      }),
                  ],
                ),
                if (data.pickupDatetime == null && data.pickupPoint!.endTime != null && data.pickupPoint!.startTime != null)
                  Row(
                    children: [
                      Text('${language.note} ${language.courierWillPickupAt} ${DateFormat('dd MMM yyyy').format(DateTime.parse(data.pickupPoint!.startTime!).toLocal())} ${language.from} ${DateFormat('hh:mm').format(DateTime.parse(data.pickupPoint!.startTime!).toLocal())} ${language.to} ${DateFormat('hh:mm').format(DateTime.parse(data.pickupPoint!.endTime!).toLocal())}',
                              style: secondaryTextStyle(size: 12, color: Colors.red), maxLines: 2, overflow: TextOverflow.ellipsis)
                          .expand(),
                    ],
                  ),
              ],
            ),
            16.height,
            Column(
              crossAxisAlignment: .start,
              children: [
                if (data.deliveryDatetime != null)
                  Column(
                    crossAxisAlignment: .start,
                    children: [
                      Text(language.delivered, style: secondaryTextStyle(size: 12)),
                      4.height,
                      Text('${language.at} ${printDateWithoutAt("${data.deliveryDatetime!}Z")}', style: secondaryTextStyle(size: 12)),
                    ],
                  ),
                4.height,
                Row(
                  children: [
                    GestureDetector(
                      onTap: () {
                        OrderDetailScreen(orderId: data.id!).launch(context, pageRouteAnimation: PageRouteAnimation.SlideBottomTop, duration: Duration(milliseconds: 400));
                      },
                      child: Row(
                        children: [
                          ImageIcon(AssetImage(ic_to), size: 24, color: ColorUtils.colorPrimary),
                          12.width,
                          Text('${data.deliveryPoint!.address}', style: primaryTextStyle(size: 14), textAlign: TextAlign.start).expand(),
                        ],
                      ),
                    ).expand(),
                    12.width,
                    if (data.deliveryPoint!.contactNumber != null && data.status != COMPLETED)
                      Icon(Ionicons.ios_call_outline, size: 20, color: ColorUtils.colorPrimary).onTap(() {
                        commonLaunchUrl('tel:${data.deliveryPoint!.contactNumber}');
                      }),
                  ],
                ),
                if (data.deliveryDatetime == null && data.deliveryPoint!.endTime != null && data.deliveryPoint!.startTime != null)
                  Text('${language.note} ${language.courierWillDeliverAt} ${DateFormat('dd MMM yyyy').format(DateTime.parse(data.deliveryPoint!.startTime!).toLocal())} ${language.from} ${DateFormat('hh:mm').format(DateTime.parse(data.deliveryPoint!.startTime!).toLocal())} ${language.to} ${DateFormat('hh:mm').format(DateTime.parse(data.deliveryPoint!.endTime!).toLocal())}',
                          style: secondaryTextStyle(color: Colors.red, size: 12))
                      .paddingOnly(top: 4),
                if (data.reScheduleDateTime != null)
                  Text('${language.note} ${language.rescheduleMsg} ${DateFormat('yyyy-MM-dd HH:mm').format(DateTime.parse(data.reScheduleDateTime!))} ',
                          style: secondaryTextStyle(color: Colors.red, size: 12))
                      .paddingOnly(top: 4)
              ],
            ),
            Divider(height: 30, thickness: 1, color: context.dividerColor),
            Row(
              children: [
                Container(
                  decoration: boxDecorationWithRoundedCorners(
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(color: ColorUtils.borderColor, width: appStore.isDarkMode ? 0.2 : 1),
                      backgroundColor: context.cardColor),
                  padding: .all(8),
                  child: Image.asset(parcelTypeIcon(data.parcelType.validate()), height: 24, width: 24, color: Colors.grey),
                ),
                8.width,
                Column(
                  crossAxisAlignment: .start,
                  children: [
                    Text(data.parcelType.validate(), style: boldTextStyle()),
                    4.height,
                    Row(
                      children: [
                        data.date != null ? Text(printDate("${data.date}"), style: secondaryTextStyle()).expand() : SizedBox(),
                        Text('${printAmount(data.totalAmount ?? 0)}', style: boldTextStyle()),
                      ],
                    ),
                  ],
                ).expand(),
              ],
            ),
            Row(
              mainAxisAlignment: .end,
              children: [
                Align(
                  alignment: Alignment.topRight,
                  child: AppButton(
                    elevation: 0,
                    color: Colors.transparent,
                    padding: .symmetric(vertical: 8, horizontal: 16),
                    shapeBorder: RoundedRectangleBorder(borderRadius: BorderRadius.circular(defaultRadius), side: BorderSide(color: ColorUtils.colorPrimary)),
                    child: Text(language.notifyUser, style: primaryTextStyle(color: ColorUtils.colorPrimary)),
                    onTap: () {
                      showConfirmDialogCustom(
                        context,
                        primaryColor: ColorUtils.colorPrimary,
                        dialogType: DialogType.CONFIRMATION,
                        title: language.areYouSureWantToArrive,
                        positiveText: language.yes,
                        negativeText: language.cancel,
                        onAccept: (c) async {
                          appStore.setLoading(true);
                          await updateOrder(orderStatus: ORDER_ARRIVED, orderId: data.id).then((value) {
                            toast(language.orderArrived);
                          });
                          appStore.setLoading(false);
                          // finish(context);
                          int i = statusList.indexWhere((item) => item == ORDER_ARRIVED);
                          pageController.jumpToPage(i);
                          getOrderListApiCall();
                        },
                      );
                    },
                  ),
                ).paddingOnly(top: 10).visible(data.status == ORDER_ACCEPTED),
              ],
            ),
          ],
        ),
      ),
      onTap: () {
        OrderDetailScreen(orderId: data.id!).launch(context, pageRouteAnimation: PageRouteAnimation.SlideBottomTop, duration: Duration(milliseconds: 400));
      },
    );
  }

  Future<void> onTapData({required String orderStatus, required OrderData orderData}) async {
    FlutterRingtonePlayer().stop();
    if (orderStatus == ORDER_ASSIGNED) {
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
