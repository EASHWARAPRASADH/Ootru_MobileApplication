import 'dart:io';

import 'package:country_code_picker/country_code_picker.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:geolocator/geolocator.dart';
import 'package:permission_handler/permission_handler.dart';
import '../../extensions/confirmation_dialog.dart';
import '../../extensions/extension_util/context_extensions.dart';
import '../../extensions/extension_util/int_extensions.dart';
import '../../extensions/extension_util/string_extensions.dart';
import '../../extensions/extension_util/widget_extensions.dart';
import '../../main/models/AddressListModel.dart';
import '../../main/network/RestApis.dart';

import '../../extensions/app_text_field.dart';
import '../../extensions/common.dart';
import '../../extensions/shared_pref.dart';
import '../../extensions/system_utils.dart';
import '../../extensions/text_styles.dart';
import '../../main.dart';
import '../../main/components/CommonScaffoldComponent.dart';
import '../../main/models/CountryListModel.dart';
import '../../main/models/PlaceAddressModel.dart';
import '../../main/utils/Common.dart';
import '../../main/utils/Constants.dart';
import '../../main/utils/Widgets.dart';
import '../../main/utils/dynamic_theme.dart';
import 'GoogleMapScreen.dart';

class AddAddressScreen extends StatefulWidget {
  final AddressData? addressData;

  AddAddressScreen({this.addressData});

  @override
  AddAddressScreenState createState() => AddAddressScreenState();
}

class AddAddressScreenState extends State<AddAddressScreen> {
  GlobalKey<FormState> formKey = GlobalKey<FormState>();

  TextEditingController addressController = TextEditingController();
  TextEditingController contactController = TextEditingController();
  TextEditingController addressTypeController = TextEditingController();
  double? latitude, longitude;
  String countryCode = defaultPhoneCode;

  @override
  void initState() {
    super.initState();
    init();
  }

  void init() async {
    countryCode =
        CountryModel.fromJson(getJSONAsync(COUNTRY_DATA)).code.isEmptyOrNull
            ? defaultPhoneCode
            : CountryModel.fromJson(getJSONAsync(COUNTRY_DATA)).code.validate();
    if (widget.addressData != null) {
      addressController.text = widget.addressData!.address.validate();
      countryCode =
          widget.addressData!.contactNumber.validate().split(" ").first;
      contactController.text =
          widget.addressData!.contactNumber.validate().split(" ").last;
      addressTypeController.text = widget.addressData!.addressType.validate();
      latitude = widget.addressData!.latitude.toDouble();
      longitude = widget.addressData!.longitude.toDouble();
    }
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  Future<void> saveAddressApiCall() async {
    Map req = {
      "id": widget.addressData != null ? "${widget.addressData!.id}" : "",
      "user_id": getIntAsync(USER_ID),
      "address": addressController.text,
      "latitude": latitude,
      "longitude": longitude,
      "contact_number": '$countryCode ${contactController.text}',
      "city_id": getIntAsync(CITY_ID).toString(),
      "country_id": getIntAsync(COUNTRY_ID).toString(),
      "address_type": addressTypeController.text
    };
    appStore.setLoading(true);

    await saveUserAddress(req).then((value) {
      toast(value.message.toString());
      appStore.setLoading(false);
      finish(context, true);
    }).catchError((e) {
      appStore.setLoading(false);
      toast(e.toString());
    });
  }

  @override
  Widget build(BuildContext context) {
    return CommonScaffoldComponent(
      showBack: true,
      appBarTitle: language.addNewAddress,
      body: Stack(
        children: [
          SingleChildScrollView(
            padding: .all(16),
            child: Form(
              key: formKey,
              child: Column(
                crossAxisAlignment: .start,
                children: [
                  Text(language.address, style: primaryTextStyle()),
                  8.height,
                  AppTextField(
                    controller: addressController,
                    readOnly: true,
                    textInputAction: TextInputAction.next,
                    textFieldType: TextFieldType.MULTILINE,
                    minLines: 5,
                    maxLines: 5,
                    decoration: commonInputDecoration(
                        suffixIcon: Icons.location_on_outlined),
                    validator: (value) {
                      if (value!.isEmpty) return language.fieldRequiredMsg;
                      if (latitude == null || longitude == null)
                        return language.pleaseSelectValidAddress;
                      return null;
                    },
                    onTap: () async {
                      try {
                        final isLocationEnabled =
                            await Geolocator.isLocationServiceEnabled();

                        if (!isLocationEnabled) {
                          final shouldOpenSettings =
                              await showConfirmDialogCustom(
                            context,
                            primaryColor: ColorUtils.colorPrimary,
                            title: "Location Services Disabled",
                            note:
                                "Please enable location services to select your address.",
                            positiveText: "Open Settings",
                            negativeText: language.cancel,
                            onCancel: (p0) => Navigator.pop(context, false),
                            onAccept: (c) => Navigator.pop(context, true),
                          );

                          if (shouldOpenSettings == true) {
                            await Geolocator.openLocationSettings();
                            if (!await Geolocator.isLocationServiceEnabled()) {
                              if (mounted)
                                toast("Location services still disabled");
                              return;
                            }
                          } else {
                            return;
                          }
                        }

                        LocationPermission permission =
                            await Geolocator.checkPermission();

                        if (permission == LocationPermission.deniedForever ||
                            permission == LocationPermission.denied) {
                          if (mounted) {
                            final openAppSetting =
                                await showConfirmDialogCustom(
                              context,
                              primaryColor: ColorUtils.colorPrimary,
                              title: "Location Access Needed",
                              note:
                                  "We need your location in order to deliver/pickup your parcel",
                              positiveText: "Open Settings",
                              negativeText: language.cancel,
                              onCancel: (p0) => Navigator.pop(context, false),
                              onAccept: (c) => Navigator.pop(context, true),
                            );

                            if (openAppSetting == true) {
                              await openAppSettings();
                            }
                          }
                          return;
                        }

                        final res = await GoogleMapScreen(isAddAddress: true, isPick: false, isSaveAddress: false)
                            .launch(context);
                        if (res != null && mounted) {
                          addressController.text = res.placeAddress ?? '';
                          latitude = res.latitude;
                          longitude = res.longitude;
                          setState(() {});
                        }
                      } catch (e) {
                        //toast('Error: ${e.toString()}');
                      }
                    },
                  ),
                  16.height,
                  Text(language.contactNumber, style: primaryTextStyle()),
                  8.height,
                  AppTextField(
                    controller: contactController,
                    textFieldType: TextFieldType.PHONE,
                    textInputAction: TextInputAction.done,
                    decoration: commonInputDecoration(
                      suffixIcon: Icons.phone,
                      prefixIcon: IntrinsicHeight(
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            CountryCodePicker(
                              initialSelection: countryCode,
                              //  initialSelection: CountryModel.fromJson(getJSONAsync(COUNTRY_DATA)).code.validate(),
                              showCountryOnly: false,
                              dialogSize: Size(
                                  context.width() - 60, context.height() * 0.6),
                              showFlag: true,
                              showFlagDialog: true,
                              showOnlyCountryWhenClosed: false,
                              alignLeft: false,
                              textStyle: primaryTextStyle(),
                              dialogBackgroundColor:
                                  Theme.of(context).cardColor,
                              barrierColor: Colors.black12,
                              dialogTextStyle: primaryTextStyle(),
                              searchDecoration: InputDecoration(
                                iconColor: Theme.of(context).dividerColor,
                                enabledBorder: UnderlineInputBorder(
                                    borderSide: BorderSide(
                                        color: Theme.of(context).dividerColor)),
                                focusedBorder: UnderlineInputBorder(
                                    borderSide: BorderSide(
                                        color: ColorUtils.colorPrimary)),
                              ),
                              searchStyle: primaryTextStyle(),
                              onInit: (c) {
                                countryCode = c!.dialCode!;
                              },
                              onChanged: (c) {
                                countryCode = c.dialCode!;
                              },
                            ),
                            VerticalDivider(
                                color: Colors.grey.withOpacity(0.5)),
                          ],
                        ),
                      ),
                    ),
                    validator: (value) {
                      if (value!.trim().isEmpty)
                        return language.fieldRequiredMsg;
                      if (value.trim().length < minContactLength ||
                          value.trim().length > maxContactLength)
                        return language.phoneNumberInvalid;
                      return null;
                    },
                    inputFormatters: [
                      FilteringTextInputFormatter.digitsOnly,
                    ],
                  ),
                  16.height,
                  Text(language.selectAddressType, style: primaryTextStyle()),
                  8.height,
                  AppTextField(
                    isValidationRequired: true,
                    controller: addressTypeController,
                    textFieldType: TextFieldType.NAME,
                    errorThisFieldRequired: language.fieldRequiredMsg,
                    decoration: commonInputDecoration(hintText: language.name),
                  ),
                ],
              ),
            ),
          ),
          Observer(
              builder: (context) => loaderWidget().visible(appStore.isLoading)),
        ],
      ),
      bottomNavigationBar: Padding(
        padding: .all(16),
        child: commonButton(language.save, () {
          if (formKey.currentState!.validate()) {
            saveAddressApiCall();
          }
        }),
      ),
    );
  }
}
