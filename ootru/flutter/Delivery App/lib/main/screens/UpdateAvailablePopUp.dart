import 'dart:io';
import 'package:flutter/material.dart';
import 'package:mighty_delivery/extensions/app_button.dart';
import 'package:mighty_delivery/extensions/common.dart';
import 'package:mighty_delivery/extensions/extension_util/context_extensions.dart';
import 'package:mighty_delivery/main/utils/dynamic_theme.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../extensions/text_styles.dart';
import '../../main.dart';
import '../utils/Images.dart';

class UpdateAvailable extends StatefulWidget {
  bool? force;
  String storeUrl;

  UpdateAvailable({super.key, this.force, required this.storeUrl});

  @override
  State<UpdateAvailable> createState() => _UpdateAvailableState();
}

class _UpdateAvailableState extends State<UpdateAvailable> {
  @override
  Widget build(BuildContext context) {
    return WillPopScope(
      onWillPop: () async {
        if (widget.force != true) {
          return true;
        }
        return false;
      },
      child: Material(
        color: Colors.transparent,
        child: Wrap(
          runAlignment: WrapAlignment.center,
          children: [
            Container(
              margin: .symmetric(
                horizontal: 45,
              ),
              padding: .all(16),
              decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12)),
              child: Stack(children: [
                Column(
                  mainAxisAlignment: .center,
                  children: [
                    Container(width: context.width() * 0.40, child: Image.asset(ic_force_update)),
                    SizedBox(height: 16),
                    Text(
                      textAlign: TextAlign.center,
                      language.updateAvailable,
                      style: boldTextStyle(color: ColorUtils.colorPrimary),
                    ),
                    SizedBox(
                      height: 8,
                    ),
                    Text(
                      textAlign: TextAlign.center,
                      language.updateNote,
                      style: secondaryTextStyle(color: ColorUtils.colorPrimary),
                    ),
                    SizedBox(
                      height: 32,
                    ),
                    Row(
                      mainAxisAlignment: widget.force != true ? .spaceBetween : .center,
                      mainAxisSize: MainAxisSize.max,
                      children: [
                        AppButton(
                          text: language.updateNow,
                          color: ColorUtils.colorPrimary,
                          textStyle: boldTextStyle(size: 18, color: Colors.white),
                          onTap: () async {
                            if (Platform.isAndroid) {
                              // try {
                              //   launchUrl(Uri.parse('${widget.storeUrl}'), mode: LaunchMode.externalApplication);
                              // } catch (e) {
                              //   toast(e.toString());
                              // }.
                              final url = widget.storeUrl;
                              if (await canLaunchUrl(Uri.parse(url))) {
                                await launchUrl(Uri.parse(url), mode: LaunchMode.externalApplication);
                              } else {
                                toast(language.urlLaunchError);
                              }
                            } else if (Platform.isIOS) {
                              final url = widget.storeUrl;
                              // try {
                              //   launchUrl(Uri.parse("${widget.storeUrl}"), mode: LaunchMode.externalApplication);
                              // } catch (e) {
                              //   toast(e.toString());
                              // }
                              if (await canLaunchUrl(Uri.parse(url))) {
                                await launchUrl(Uri.parse(url), mode: LaunchMode.externalApplication);
                              } else {
                                toast(language.urlLaunchError);
                              }
                            }
                          },
                        ),
                        if (widget.force != true) SizedBox(width: 8),
                        if (widget.force != true)
                          AppButton(
                            text: language.skip,
                            color: Colors.white,
                            shapeBorder: RoundedRectangleBorder(side: BorderSide(color: ColorUtils.colorPrimary), borderRadius: BorderRadius.circular(12)),
                            textStyle: boldTextStyle(size: 18, color: ColorUtils.colorPrimary),
                            onTap: () {
                              Navigator.pop(context);
                            },
                          )
                      ],
                    ),
                  ],
                ),
                // Observer(builder: (context) {
                //   return Loader().center().visible(appStore.isLoading);
                // })
              ]),
            ),
          ],
        ),
      ),
    );
  }
}
