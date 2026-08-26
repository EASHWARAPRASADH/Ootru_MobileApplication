import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:intl/intl.dart';
import 'package:mighty_delivery/extensions/common.dart';
import '../../delivery/screens/AddDeliverymanVehicleScreen.dart';
import '../../extensions/extension_util/int_extensions.dart';
import '../../extensions/extension_util/widget_extensions.dart';
import '../../extensions/shared_pref.dart';
import '../../main/components/CommonScaffoldComponent.dart';
import '../../main/network/RestApis.dart';
import '../../extensions/decorations.dart';
import '../../extensions/text_styles.dart';
import '../../main.dart';
import '../../main/models/DeliverymanVehicleListModel.dart';
import '../../main/utils/Common.dart';
import '../../main/utils/Constants.dart';
import '../../main/utils/dynamic_theme.dart';

class SelectVehicleScreen extends StatefulWidget {
  const SelectVehicleScreen({super.key});

  @override
  State<SelectVehicleScreen> createState() => _SelectVehicleScreenState();
}

class _SelectVehicleScreenState extends State<SelectVehicleScreen> {
  List<DeliverymanVehicle> vehicleHistoryList = [];
  ScrollController scrollController = ScrollController();
  int page = 1;
  int totalPage = 1;

  @override
  void initState() {
    super.initState();
    init();
    scrollController.addListener(() {
      if (scrollController.position.pixels == scrollController.position.maxScrollExtent && !appStore.isLoading) {
        if (page < totalPage) {
          page++;
          appStore.setLoading(true);
          init();
        }
      }
    });
  }

  void init() {
    getDeliveryManVehicleListApi();
  }

  Future<void> getDeliveryManVehicleListApi({int? id}) async {
    appStore.setLoading(true);
    await getDeliveryManVehicleList(page).then((value) {
      appStore.setLoading(false);
      totalPage = value.pagination.totalPages.validate(value: 1);
      page = value.pagination.currentPage.validate(value: 1);
      if (page == 1) {
        vehicleHistoryList.clear();
      }
      vehicleHistoryList.addAll(value.data);
      if (id != null) {
        late DeliverymanVehicle match;
        try {
          match = value.data.firstWhere((element) => element.id == id);
          print("---------------match${match.toJson()}");
          setValue(VEHICLE, match.toJson());
        } catch (e) {
          // No match found; pred not set
        }
      }

      appStore.setLoading(false);
      setState(() {});
    }).catchError((error) {
      print("error ===> ${error.toString()}");
      appStore.setLoading(false);
    });
  }

  @override
  Widget build(BuildContext context) {
    return CommonScaffoldComponent(
      appBarTitle: language.vehicleHistory,
      showBack: true,
      body: Observer(builder: (context) {
        return Stack(
          children: [
            vehicleHistoryList.isNotEmpty
                ? ListView.builder(
                    itemCount: vehicleHistoryList.length,
                    shrinkWrap: true,
                    controller: scrollController,
                    padding: .fromLTRB(16, 16, 16, 0),
                    itemBuilder: (context, index) {
                      DeliverymanVehicle item = vehicleHistoryList[index];
                      return Container(
                        margin: .only(bottom: 16),
                        padding: .all(8),
                        decoration: boxDecorationWithRoundedCorners(borderRadius: BorderRadius.circular(defaultRadius), border: Border.all(color: appStore.isDarkMode ? Colors.grey.withOpacity(0.3) : ColorUtils.colorPrimary.withOpacity(0.4)), backgroundColor: Colors.transparent),
                        child: Row(
                          children: [
                            Column(
                              crossAxisAlignment: .start,
                              children: [
                                Row(
                                  mainAxisAlignment: .spaceBetween,
                                  children: [
                                    Row(
                                      children: [
                                        Text("${language.id} : ", style: boldTextStyle()),
                                        Text(item.id.validate().toString(), style: boldTextStyle()),
                                      ],
                                    ),
                                    Text(item.isActive.validate() == 1 ? language.active : language.inActive, style: boldTextStyle(color: item.isActive.validate() == 1 ? ColorUtils.colorPrimary : Colors.red)),
                                  ],
                                ),
                                8.height,
                                Row(
                                  children: [
                                    Text('${language.startDate} :', style: primaryTextStyle()),
                                    Text(DateFormat('dd MMM yyyy').format(item.startDatetime), style: primaryTextStyle()),
                                  ],
                                ),
                                8.height,
                                8.height,
                                Row(
                                  children: [
                                    Text('${language.vehicleInfo} :', style: primaryTextStyle()),
                                    Text(language.clickHere, style: primaryTextStyle(color: ColorUtils.colorPrimary)).onTap(() {
                                      AddDeliverymanVehicleScreen(
                                        vehicle: item.vehicleInfo,
                                      ).launch(context);
                                    }).expand(),
                                    Switch(
                                      value: item.isActive.validate() == 1,
                                      onChanged: (value) async {
                                        // setState(() {
                                        //   item.isActive = value ? 1 : 0;
                                        // });
                                        Map<String, dynamic> req = {"id": item.id};
                                        await updateVehicleStatus(req).then((value) {
                                          getDeliveryManVehicleListApi(id: item.id);
                                          toast(value.message);
                                        });
                                      },
                                    ),
                                  ],
                                ),
                              ],
                            ).expand(),
                          ],
                        ),
                      );
                    },
                  )
                : !appStore.isLoading
                    ? emptyWidget()
                    : SizedBox(),
            loaderWidget().center().visible(appStore.isLoading),
          ],
        );
      }),
      floatingActionButton: FloatingActionButton(
        backgroundColor: ColorUtils.colorPrimary,
        child: Icon(Icons.add, color: Colors.white),
        onPressed: () async {
          AddDeliverymanVehicleScreen().launch(context).then((value) {
            init();
          });
        },
      ),
    );
  }
}
