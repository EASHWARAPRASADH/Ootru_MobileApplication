import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:intl/intl.dart';
import 'package:mighty_delivery/extensions/extension_util/int_extensions.dart';
import 'package:mighty_delivery/extensions/extension_util/string_extensions.dart';
import 'package:mighty_delivery/extensions/extension_util/widget_extensions.dart';
import 'package:mighty_delivery/extensions/system_utils.dart';

import '../../extensions/animatedList/animated_list_view.dart';
import '../../extensions/decorations.dart';
import '../../extensions/text_styles.dart';
import '../../main.dart';
import '../../main/components/CommonScaffoldComponent.dart';
import '../../main/models/PayTrPaymentsListModel.dart';
import '../../main/network/RestApis.dart';
import '../../main/utils/Common.dart';
import '../../main/utils/Constants.dart';
import '../../main/utils/dynamic_theme.dart';

class PaytrPaymentHistoryscreen extends StatefulWidget {
  const PaytrPaymentHistoryscreen({super.key});

  @override
  State<PaytrPaymentHistoryscreen> createState() => _PaytrPaymentHistoryscreenState();
}

class _PaytrPaymentHistoryscreenState extends State<PaytrPaymentHistoryscreen> {
  PayTrPaymentsListModel? paymentsListData;
  @override
  void initState() {
    super.initState();
    init();
  }

  init() async {
    appStore.setLoading(true);
    setState(() {});
    paymentsListData = await getPaytrPaymentsList().whenComplete(() {
      appStore.setLoading(false);
      setState(() {});
    },);
    // await getPaytrPaymentsList().then((value) {
    //   paymentsListData = value;
    //   appStore.setLoading(false);
    //   setState(() {});
    // });
  }

  @override
  Widget build(BuildContext context) {
    return CommonScaffoldComponent(
      appBarTitle: language.paytrHistory,
      body: Stack(
        children: [
          paymentsListData != null
              ? AnimatedListView(
                  padding: .all(16),
                  itemCount: paymentsListData!.data!.length,
                  emptyWidget: Stack(
                    children: [
                      loaderWidget().visible(appStore.isLoading),
                      emptyWidget().visible(!appStore.isLoading),
                    ],
                  ),
                  onPageScrollChange: () {
                    // appStore.setLoading(true);
                  },
                  onNextPage: () {},
                  shrinkWrap: true,
                  itemBuilder: (_, index) {
                    PaytrPaymentItem data = paymentsListData!.data![index];
                    return Container(
                      margin: .only(bottom: 16),
                      padding: .all(8),
                      decoration: boxDecorationWithRoundedCorners(borderRadius: radius(defaultRadius), backgroundColor: Colors.transparent, border: Border.all(color: ColorUtils.colorPrimary.withOpacity(0.08))),
                      child: Row(
                        children: [
                          10.width,
                          Expanded(
                            flex: 2,
                            child: Column(
                              crossAxisAlignment: .start,
                              children: [
                                Text(data.paymentStatus ?? "", style: boldTextStyle(color: textPrimaryColorGlobal)),
                                SizedBox(height: 8),
                                Text(DateFormat('dd/MM/yyyy HH:mm').format(DateTime.parse(data.createdAt.validate())), style: secondaryTextStyle(size: 12)),
                                SizedBox(height: 8),
                              ],
                            ),
                          ),
                          Container(
                            child: Column(
                              children: [
                                Text(printAmount(data.totalAmount), style: boldTextStyle(color: Colors.green)),
                              ],
                            ),
                          )
                        ],
                      ),
                    );
                  },
                )
              : appStore.isLoading
                  ? loaderWidget()
                  : SizedBox(),
          Observer(builder: (context) => loaderWidget().visible(appStore.isLoading)),
        ],
      ),
    );
  }
}
