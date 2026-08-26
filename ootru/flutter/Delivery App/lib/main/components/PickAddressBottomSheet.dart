import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import '../../extensions/extension_util/context_extensions.dart';
import '../../extensions/extension_util/int_extensions.dart';
import '../../extensions/extension_util/widget_extensions.dart';

import '../../extensions/common.dart';
import '../../extensions/shared_pref.dart';
import '../../extensions/system_utils.dart';
import '../../extensions/text_styles.dart';
import '../../main.dart';
import '../../main/utils/Constants.dart';
import '../../user/screens/MyAddressListScreen.dart';
import '../models/AddressListModel.dart';
import '../network/RestApis.dart';
import '../utils/Common.dart';
import '../utils/dynamic_theme.dart';

class PickAddressBottomSheet extends StatefulWidget {
  final Function(AddressData) onPick;
  final bool isPickup;
  Function? onAddNewAddress;

  PickAddressBottomSheet({
    required this.onPick,
    this.isPickup = true,
    this.onAddNewAddress,
  });

  @override
  PickAddressBottomSheetState createState() => PickAddressBottomSheetState();
}

class PickAddressBottomSheetState extends State<PickAddressBottomSheet> {

  List<AddressData> addressList = [];
  ScrollController scrollController = ScrollController();
  int page = 1;
  int totalPage = 1;
  bool isLastPage = false;


  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  @override
  void initState() {
    super.initState();
    init();
  }

  void init() async {
    await getAddressList(page: page).then((value) {
      print("Address Length = ${value.data!.length}");
      appStore.setLoading(false);
      totalPage = value.pagination!.totalPages.validate(value: 1);
      page = value.pagination!.currentPage.validate(value: 1);
      isLastPage = false;
      if (page == 1) {
        addressList.clear();
      }
      addressList.addAll(value.data!);

      List<AddressData> list = [];
      addressList.forEach((e) {
        list.add(e);
      });
      setValue(RECENT_ADDRESS_LIST, list.map((element) => jsonEncode(element)).toList());

      setState(() {});
    }).catchError((e) {
      isLastPage = true;
      appStore.setLoading(false);
      toast(e.toString(), print: true);
    });
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      child: Column(
        crossAxisAlignment: .start,
        children: [
          Container(
            padding: .all(16),
            color: ColorUtils.colorPrimary.withOpacity(0.1),
            child: Column(
              crossAxisAlignment: .stretch,
              children: [
                Text(widget.isPickup ? language.choosePickupAddress : language.chooseDeliveryAddress, style: boldTextStyle()),
                8.height,
                Text(language.showingAllAddress, style: secondaryTextStyle()),
              ],
            ),
          ),
          Row(
            children: [
              Icon(Icons.add_circle_outline, color: ColorUtils.colorPrimary),
              10.width,
              Text(language.addNewAddress, style: boldTextStyle(color: ColorUtils.colorPrimary)),
            ],
          ).onTap(widget.onAddNewAddress != null ? widget.onAddNewAddress : () async {
            MyAddressListScreen().launch(context).then((value) => setState(() {}));
          }).paddingAll(16),
          Divider(color: context.dividerColor),
          Stack(
            children: [
              ListView.separated(
               // itemCount: (getStringListAsync(RECENT_ADDRESS_LIST) ?? []).length,
                 itemCount: addressList.length,
                shrinkWrap: true,
                controller: scrollController,
                itemBuilder: (context, index) {
                  // int len = (getStringListAsync(RECENT_ADDRESS_LIST) ?? []).length;
                 // AddressData mData = AddressData.fromJson(jsonDecode(getStringListAsync(RECENT_ADDRESS_LIST)![len - index - 1]));
                   AddressData mData = addressList[index];
                  return Row(
                    children: [
                      Icon(Icons.location_on_outlined),
                      10.width,
                      Column(
                        crossAxisAlignment: .start,
                        children: [
                          Text('${mData.addressType}', style: secondaryTextStyle(size: 12), maxLines: 1),
                          Container(width: context.width() * 0.82, child: Text('${mData.address}', style: primaryTextStyle(), maxLines: 2)),
                        ],
                      ),
                    ],
                  ).onTap(() async {
                    widget.onPick.call(mData);
                    finish(context);
                  }).paddingSymmetric(vertical: 8, horizontal: 16);
                },
                separatorBuilder: (context, index) {
                  return Divider(color: context.dividerColor);
                },
              ),
              Observer(builder: (context) => loaderWidget().visible(appStore.isLoading)),
            ],
          ).expand(),
        ],
      ),
    );
  }
}
