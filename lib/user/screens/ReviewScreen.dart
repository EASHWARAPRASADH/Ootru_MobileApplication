import 'dart:developer';

import 'package:flutter/material.dart';
import 'package:flutter_rating_bar/flutter_rating_bar.dart';
import 'package:mighty_delivery/extensions/extension_util/int_extensions.dart';
import 'package:mighty_delivery/extensions/extension_util/string_extensions.dart';
import 'package:mighty_delivery/extensions/system_utils.dart';
import 'package:mighty_delivery/main/network/RestApis.dart';

import '../../extensions/app_text_field.dart';
import '../../extensions/common.dart';
import '../../extensions/text_styles.dart';
import '../../main.dart';
import '../../main/models/LoginResponse.dart';
import '../../main/models/OrderListModel.dart';
import '../../main/utils/Common.dart';
import '../../main/utils/Widgets.dart';

class Reviewscreen extends StatefulWidget {
  UserData? userData;
  OrderData? orderData;
  Reviewscreen({super.key, this.userData, this.orderData});

  @override
  State<Reviewscreen> createState() => _ReviewscreenState();
}

class _ReviewscreenState extends State<Reviewscreen> {
  GlobalKey<FormState> formKey = GlobalKey<FormState>();

  TextEditingController reviewController = TextEditingController();
  TextEditingController tipController = TextEditingController();
  num rattingData = 0;

  Future<void> saveReview() async {
    hideKeyboard(context);
    Map req = {'order_id': widget.orderData!.id, 'rating': rattingData, 'comment': reviewController.text.trim()};
    appStore.setLoading(true);
    await createReview(req).then((value) {
      toast(value.message.validate());
      appStore.setLoading(false);
      finish(context, true);
    }).catchError((error) {
      log(error);
      appStore.setLoading(false);
      toast(error.toString());
      finish(context, true);
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        centerTitle: true,
        automaticallyImplyLeading: false,
        title: Text(language.rateUs, style: boldTextStyle(color: Colors.white)),
        actions: [
          TextButton(
            onPressed: () {
              Navigator.pop(context, false); // Return false when skipped
            },
            child: Text(
              language.skip,
              style: boldTextStyle(color: Colors.white),
            ),
          ),
        ],
      ),
      body: Stack(
        children: [
          SingleChildScrollView(
            padding: .only(top: 20, left: 16, right: 16, bottom: 16),
            child: Column(
              crossAxisAlignment: .start,
              children: [
                Text(
                  language.rateExp,
                  style: boldTextStyle(),
                  overflow: TextOverflow.ellipsis,
                  maxLines: 2,
                ),
                16.height,
                Row(
                  crossAxisAlignment: .start,
                  children: [
                    ClipRRect(
                      borderRadius: BorderRadius.circular(35),
                      child: commonCachedNetworkImage(widget.userData?.profileImage.validate(), height: 60, width: 60, fit: BoxFit.cover),
                    ),
                    8.width,
                    Column(
                      crossAxisAlignment: .start,
                      children: [
                        SizedBox(height: 8),
                        Text(
                          widget.userData?.name ?? language.unknownDeliveryman,
                          style: boldTextStyle(),
                        ),
                        SizedBox(height: 4),
                        Text(
                          widget.userData?.email ?? 'jacob@gmail.com',
                          style: primaryTextStyle(),
                        ),
                      ],
                    ),
                  ],
                ),
                16.height,
                RatingBar.builder(
                  direction: Axis.horizontal,
                  glow: false,
                  allowHalfRating: false,
                  wrapAlignment: WrapAlignment.spaceBetween,
                  itemCount: 5,
                  itemBuilder: (context, _) => Icon(Icons.star, color: Colors.amber),
                  onRatingUpdate: (rating) {
                    setState(() {
                      rattingData = rating;
                    });
                  },
                ),
                16.height,
                Text(language.addReview, style: boldTextStyle()),
                16.height,
                AppTextField(
                  controller: reviewController,
                  decoration: commonInputDecoration(hintText: language.writeYourReview),
                  textFieldType: TextFieldType.NAME,
                  minLines: 2,
                  maxLines: 10,
                ),
                16.height,
              ],
            ),
          )
        ],
      ),
      bottomNavigationBar: Padding(
        padding: .all(16),
        child: commonButton(language.submit, () {
          saveReview();
        }),
      ),
    );
  }
}
