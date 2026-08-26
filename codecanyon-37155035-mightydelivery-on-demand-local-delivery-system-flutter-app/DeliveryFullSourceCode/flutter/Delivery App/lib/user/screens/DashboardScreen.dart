import 'package:animated_bottom_navigation_bar/animated_bottom_navigation_bar.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:crisp_chat/crisp_chat.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:flutter_vector_icons/flutter_vector_icons.dart';
import 'package:mighty_delivery/main/utils/Common.dart';
import 'package:mobx/mobx.dart';
import '../../extensions/extension_util/context_extensions.dart';
import '../../extensions/extension_util/int_extensions.dart';
import '../../extensions/extension_util/string_extensions.dart';
import '../../extensions/extension_util/widget_extensions.dart';
import '../../main/network/RestApis.dart';
import '../../main/services/VersionServices.dart';
import '../../main/utils/Widgets.dart';
import '../../extensions/LiveStream.dart';
import '../../extensions/colors.dart';
import '../../extensions/common.dart';
import '../../extensions/decorations.dart';
import '../../extensions/shared_pref.dart';
import '../../extensions/text_styles.dart';
import '../../main.dart';
import '../../main/components/CommonScaffoldComponent.dart';
import '../../main/models/CityListModel.dart';
import '../../main/models/models.dart';
import '../../main/screens/NotificationScreen.dart';
import '../../main/screens/UserCitySelectScreen.dart';
import '../../main/utils/Constants.dart';
import '../../main/utils/dynamic_theme.dart';
import '../../user/components/FilterOrderComponent.dart';
import '../../user/fragment/AccountFragment.dart';
import '../../user/fragment/OrderFragment.dart';
import '../../user/screens/CreateOrderScreen.dart';
import '../../user/screens/WalletScreen.dart';

class DashboardScreen extends StatefulWidget {
  static String tag = '/DashboardScreen';

  @override
  DashboardScreenState createState() => DashboardScreenState();
}

class DashboardScreenState extends State<DashboardScreen> with WidgetsBindingObserver {
  List<BottomNavigationBarItemModel> bottomNavBarItems = [];

  int currentIndex = 0;
  List widgetList = [
    OrderFragment(),
    AccountFragment(),
  ];
  late CrispConfig configData;
  String? crispChatIcon;
  bool isEnable = false;
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    appStore.setLoading(true);
    loadData();
  }

  initCrispChat() async {
    await configCrispChatData();
  }

  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    positionStream?.cancel();
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    switch (state) {
      case AppLifecycleState.resumed:
        print("📢 line 67 ${appStore.isCrispChatEnabled}");
        onResumed();
        break;
      default:
    }
  }

  void onResumed() async {
    //  getDashboardDetails();
  }

  Future<void> loadData() async {
    await Future.wait<void>([
      getDashboardDetails(),
      init(),
      initCrispChat(),
    ]);
    setState(() {
      appStore.setLoading(false);
    });
  }

  getDashboardDetails() async {
    await Future.delayed(Duration(seconds: 2));
    await getDashboardDetail().then((value) {
      if (value.deliverManVersion != null) {
        VersionService().getVersionData(context, value.deliverManVersion);
      }
      if (value.crispData != null) {
        print("-----------configData 61${value.crispData!.isCrispChatEnabled}-----------${value.crispData!.crispChatWebsiteId}");
        if (value.crispData!.isCrispChatEnabled == null) {
          appStore.setIsCrispChatEnabled(false);
        } else {
          isEnable = value.crispData!.isCrispChatEnabled!;
          print("----------${isEnable}");
          appStore.setIsCrispChatEnabled(value.crispData!.isCrispChatEnabled!);
          appStore.setCrispChatWebsiteId(value.crispData!.crispChatWebsiteId!);

          User user = User(email: appStore.userEmail, nickName: " ${getStringAsync(USER_NAME)}", avatar: appStore.userProfile ?? "");
          FlutterCrispChat.resetCrispChatSession();
          configData = CrispConfig(
            user: user,
            tokenId: getIntAsync(USER_ID).toString(),
            enableNotifications: true,
            websiteID: value.crispData!.crispChatWebsiteId!,
          );
          setState(() {});
        }
      }
      if (value.appSetting != null) {
        appStore.setIsSmsOrder(value.appSetting!.isSmsOrder ?? 0);
      }
    });
  }

  Future<void> init() async {
    await Future.delayed(Duration(seconds: 3));
    bottomNavBarItems.add(BottomNavigationBarItemModel(icon: Icons.shopping_bag, title: language.myOrders));
    bottomNavBarItems.add(BottomNavigationBarItemModel(icon: Icons.person, title: language.account));
    LiveStream().on('UpdateLanguage', (p0) {
      setState(() {});
    });
    LiveStream().on('UpdateTheme', (p0) {
      setState(() {});
    });
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  String getTitle() {
    String title = language.myOrders;
    if (currentIndex == 0) {
      title = '${language.hey} ${getStringAsync(NAME)} 👋';
    } else if (currentIndex == 1) {
      title = language.account;
    }
    return title;
  }

  configCrispChatData() {
    /// Config crispChat
    print("-----------configData 93${appStore.isCrispChatEnabled == true}-----------${appStore.crispChatWebsiteId}");
    if (appStore.isCrispChatEnabled == true) {
      print("-----------configData 93${appStore.isCrispChatEnabled}-----------${appStore.crispChatWebsiteId}");
      User user = User(email: appStore.userEmail, nickName: " ${getStringAsync(USER_NAME)}", avatar: appStore.userProfile ?? "");
      FlutterCrispChat.resetCrispChatSession();
      configData = CrispConfig(
        user: user,
        tokenId: getIntAsync(USER_ID).toString(),
        enableNotifications: true,
        websiteID: appStore.crispChatWebsiteId,
      );
    }
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
      print("error in crispchat${e.toString()}");
      toast(e.toString());
    }
  }

  @override
  Widget build(BuildContext context) {
    return CommonScaffoldComponent(
      extendedBody: true,
      appBar: PreferredSize(
        preferredSize: Size(context.width(), 60),
        child: commonAppBarWidget(getTitle(),
            actions: [
              Container(
                margin: .symmetric(vertical: 12, horizontal: 12),
                padding: .symmetric(horizontal: 8, vertical: 4),
                decoration: boxDecorationWithRoundedCorners(borderRadius: radius(defaultRadius), backgroundColor: Colors.white24),
                child: Row(
                  children: [
                    Icon(Ionicons.ios_location_outline, color: Colors.white, size: 18),
                    8.width,
                    Text(CityModel.fromJson(getJSONAsync(CITY_DATA)).name.validate(), style: primaryTextStyle(color: white)),
                  ],
                ).onTap(() {
                  UserCitySelectScreen(
                    isBack: true,
                    onUpdate: () {
                      setState(() {});
                    },
                  ).launch(context);
                }, highlightColor: Colors.transparent, hoverColor: Colors.transparent, splashColor: Colors.transparent),
              ),
              4.width,
              4.width,
              Stack(
                clipBehavior: Clip.none,
                children: [
                  Align(alignment: AlignmentDirectional.center, child: Icon(Ionicons.md_notifications_outline, color: Colors.white)),
                  if (appStore.allUnreadCount != 0)
                    Observer(builder: (context) {
                      return Positioned(
                        right: -5,
                        top: 8,
                        child: Container(
                          height: 20,
                          width: 20,
                          alignment: Alignment.center,
                          decoration: BoxDecoration(color: Colors.orange, shape: BoxShape.circle),
                          child: Text(appStore.allUnreadCount.toString(), style: boldTextStyle(size: appStore.allUnreadCount > 99 ? 10 : 10)),
                        ),
                      );
                    }),
                ],
              ).onTap(() {
                NotificationScreen().launch(context);
              }, highlightColor: Colors.transparent, hoverColor: Colors.transparent, splashColor: Colors.transparent).visible(currentIndex == 0),
              8.width,
              Stack(
                children: [
                  Align(alignment: AlignmentDirectional.center, child: Icon(Ionicons.md_options_outline, color: Colors.white)),
                  Observer(builder: (context) {
                    return Positioned(
                      right: 8,
                      top: 16,
                      child: Container(
                        height: 10,
                        width: 10,
                        decoration: BoxDecoration(color: Colors.orange, shape: BoxShape.circle),
                      ),
                    ).visible(appStore.isFiltering);
                  }),
                ],
              ).withWidth(40).onTap(() {
                showModalBottomSheet(
                  context: context,
                  isScrollControlled: true,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.only(topLeft: Radius.circular(defaultRadius), topRight: Radius.circular(defaultRadius))),
                  builder: (context) {
                    return FilterOrderComponent();
                  },
                );
              }, splashColor: Colors.transparent, hoverColor: Colors.transparent, highlightColor: Colors.transparent).visible(currentIndex == 0),
            ],
            showBack: false),
      ),
      body: Stack(
        children: [
          Column(
            children: [
              Expanded(
                child: widgetList[currentIndex], // Ensures it takes available space
              ),
            ],
          ),
          (isEnable == true)
              ? Positioned(
                  bottom: context.height() * 0.08,
                  right: 16,
                  child: FloatingActionButton(
                      onPressed: () async {
                        configureCrispChat();
                        await FlutterCrispChat.openCrispChat(config: configData);
                      },
                      backgroundColor: ColorUtils.colorPrimary,
                      child: CachedNetworkImage(
                        imageUrl: crispChatIcon ?? "",
                        errorWidget: (context, url, error) => Icon(Icons.chat_bubble_outline),
                      )),
                )
              : SizedBox()
        ],
      ),
      floatingActionButton: FloatingActionButton(
        shape: RoundedRectangleBorder(borderRadius: radius(40)),
        backgroundColor: appStore.availableBal >= 0 ? ColorUtils.colorPrimary : textSecondaryColorGlobal,
        child: Icon(AntDesign.plus, color: Colors.white),
        onPressed: () {
          if (appStore.availableBal >= 0) {
            CreateOrderScreen().launch(context, pageRouteAnimation: PageRouteAnimation.SlideBottomTop);
          } else {
            toast(language.balanceInsufficient);
            WalletScreen().launch(context);
          }
        },
      ),
      floatingActionButtonLocation: FloatingActionButtonLocation.centerDocked,
      bottomNavigationBar: AnimatedBottomNavigationBar(
        backgroundColor: ColorUtils.bottomNavigationColor,
        icons: [AntDesign.home, FontAwesome.user_o],
        activeIndex: currentIndex,
        gapLocation: GapLocation.center,
        notchSmoothness: NotchSmoothness.defaultEdge,
        activeColor: ColorUtils.colorPrimary,
        inactiveColor: Colors.grey,
        onTap: (index) => setState(() => currentIndex = index),
      ),
    );
  }
}
