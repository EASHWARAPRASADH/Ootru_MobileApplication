import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:mighty_delivery/extensions/decorations.dart';
import 'package:mighty_delivery/extensions/extension_util/widget_extensions.dart';
import 'package:mighty_delivery/extensions/text_styles.dart';
import 'package:mighty_delivery/main/models/EmergencyResponseListModel.dart';
import 'package:mighty_delivery/main/utils/Constants.dart';
import 'package:mighty_delivery/main/utils/dynamic_theme.dart';

import '../../extensions/common.dart';
import '../../extensions/system_utils.dart';
import '../../main.dart';
import '../../main/components/CommonScaffoldComponent.dart';
import '../../main/network/RestApis.dart';
import '../../main/utils/Common.dart';
import '../../main/utils/Widgets.dart';

class ResolveEmergencyScreen extends StatefulWidget {
  const ResolveEmergencyScreen({super.key});

  @override
  State<ResolveEmergencyScreen> createState() => _ResolveEmergencyScreenState();
}

class _ResolveEmergencyScreenState extends State<ResolveEmergencyScreen> {
  EmergencyPendingListResonse? response;
  List<EmergencyItem> data = [];
  String msg = "";
  @override
  void initState() {
    appStore.setLoading(true);
    getEmergencyListApiCall();
    super.initState();
  }

  getEmergencyListApiCall() async {
    data.clear();
    await getEmergencyList().then((value) {
      msg = value.message.toString();
      response = value;
      appStore.setLoading(false);
      if (value.data != null && value.data!.length > 0) {
        data.addAll(value.data!);
        setState(() {});
      } else {
        appStore.setLoading(false);
        msg = value.message.toString();
        setState(() {});
        throw value.message.toString();
      }
    }).catchError((error) {
      msg = error.toString();
      setState(() {});
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  @override
  Widget build(BuildContext context) {
    return CommonScaffoldComponent(
      appBar: commonAppBarWidget(
        language.resolveEmergency,
        backWidget: IconButton(
          onPressed: () {
            finish(context, false);
          },
          icon: Icon(Icons.arrow_back, color: Colors.white).onTap(() {
            finish(context, data.length > 0 ? false : true);
          }),
        ),
      ),
      body: Observer(
        builder: (_) => Stack(
          children: [
            Positioned.fill(
              child: ListView.builder(
                  itemCount: data.length,
                  itemBuilder: (context, index) {
                    return Container(
                      decoration: boxDecorationWithRoundedCorners(borderRadius: BorderRadius.circular(defaultBlurRadius), border: Border.all(color: ColorUtils.colorPrimary, width: 1)),
                      margin: .symmetric(vertical: 8, horizontal: 8),
                      padding: .all(8),
                      child: Row(
                        mainAxisAlignment: .spaceBetween,
                        children: [
                          Text(data[index].emrgencyReason.toString()),
                          TextButton(
                              onPressed: () async {
                                appStore.setLoading(true);
                                Map<String, dynamic> request = {
                                  "emergency_resolved": "Emergency Resolved",
                                };

                                try {
                                  await emergancyResolved(request, data[index].id!).then((value) {
                                    appStore.setLoading(false);
                                    toast(value.message);
                                    Navigator.of(context).pop({
                                      'confirmed': true,
                                    });
                                  });
                                } catch (e) {
                                  appStore.setLoading(false);
                                  toast("Error: $e");
                                }
                              },
                              child: Text(
                                language.resolve,
                              ))
                        ],
                      ),
                    );
                  }),
            ),
            Positioned.fill(child: loaderWidget().center().visible(appStore.isLoading)),
            if (data.length <= 0)
              Positioned.fill(
                  child: Text(
                msg.toString(),
                style: boldTextStyle(),
              ).center())
          ],
        ),
      ),
    );
  }
}
