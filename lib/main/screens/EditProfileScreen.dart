import 'dart:io';

import 'package:country_code_picker/country_code_picker.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:http/http.dart';
import 'package:image_picker/image_picker.dart';
import 'package:geolocator/geolocator.dart';
import 'package:geocoding/geocoding.dart';
import '../../extensions/extension_util/bool_extensions.dart';
import '../../extensions/extension_util/context_extensions.dart';
import '../../extensions/extension_util/int_extensions.dart';
import '../../extensions/extension_util/string_extensions.dart';
import '../../extensions/extension_util/widget_extensions.dart';
import '../../extensions/app_text_field.dart';
import '../../extensions/colors.dart';
import '../../extensions/common.dart';
import '../../extensions/decorations.dart';
import '../../extensions/shared_pref.dart';
import '../../extensions/system_utils.dart';
import '../../extensions/text_styles.dart';
import '../../main.dart';
import '../../main/network/RestApis.dart';
import '../../main/utils/Common.dart';
import '../../main/utils/Constants.dart';
import '../../main/utils/Widgets.dart';
import '../components/CommonScaffoldComponent.dart';
import '../models/LoginResponse.dart';
import '../utils/Images.dart';
import '../utils/dynamic_theme.dart';
import '../../user/screens/DashboardScreen.dart';
import '../../delivery/fragment/DHomeFragment.dart';
import 'UserCitySelectScreen.dart';

class EditProfileScreen extends StatefulWidget {
  static String tag = '/EditProfileScreen';
  final bool? isGoogle;

  EditProfileScreen({this.isGoogle = false});

  @override
  EditProfileScreenState createState() => EditProfileScreenState();
}

class EditProfileScreenState extends State<EditProfileScreen> {
  GlobalKey<FormState> _formKey = GlobalKey<FormState>();
  String countryCode = defaultPhoneCode;

  TextEditingController emailController = TextEditingController();
  // TextEditingController usernameController = TextEditingController();
  TextEditingController nameController = TextEditingController();
  TextEditingController contactNumberController = TextEditingController();
  TextEditingController addressController = TextEditingController();

  FocusNode emailFocus = FocusNode();
//  FocusNode usernameFocus = FocusNode();
  FocusNode nameFocus = FocusNode();
  FocusNode contactFocus = FocusNode();
  FocusNode addressFocus = FocusNode();

  XFile? imageProfile;

  @override
  void initState() {
    super.initState();
    init();
  }

  Future<void> init() async {
    String phoneNum = getStringAsync(USER_CONTACT_NUMBER);
    emailController.text = getStringAsync(USER_EMAIL);
    nameController.text = getStringAsync(NAME);
    if (phoneNum.isNotEmpty) {
      if (phoneNum.contains(" ")) {
        countryCode = phoneNum.split(" ").first;
        contactNumberController.text = phoneNum.split(" ").last;
      } else {
        String digits = phoneNum.replaceAll(RegExp(r'[^\d]'), '');
        if (digits.length > 10) {
          contactNumberController.text = digits.substring(digits.length - 10);
        } else {
          contactNumberController.text = digits;
        }
      }
    }
    addressController.text = getStringAsync(USER_ADDRESS).validate();
    if (addressController.text.isEmpty) {
      fetchLiveAddress(showToast: false);
    }
  }

  Future<void> fetchLiveAddress({bool showToast = true}) async {
    try {
      LocationPermission permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
      }
      if (permission == LocationPermission.denied || permission == LocationPermission.deniedForever) {
        if (showToast) toast("Location permission is required to fetch live address");
        return;
      }
      Position position = await Geolocator.getCurrentPosition(desiredAccuracy: LocationAccuracy.high).timeout(Duration(seconds: 4));
      List<Placemark> placemarks = await placemarkFromCoordinates(position.latitude, position.longitude);
      if (placemarks.isNotEmpty) {
        Placemark place = placemarks.first;
        List<String> parts = [];
        if (place.street != null && place.street!.isNotEmpty && !place.street!.contains('+')) parts.add(place.street!);
        if (place.subLocality != null && place.subLocality!.isNotEmpty && !parts.contains(place.subLocality)) parts.add(place.subLocality!);
        if (place.locality != null && place.locality!.isNotEmpty && !parts.contains(place.locality)) parts.add(place.locality!);
        if (place.administrativeArea != null && place.administrativeArea!.isNotEmpty && !parts.contains(place.administrativeArea)) parts.add(place.administrativeArea!);
        if (place.postalCode != null && place.postalCode!.isNotEmpty) parts.add(place.postalCode!);
        String fullAddress = parts.isNotEmpty ? parts.join(', ') : (place.locality ?? '');
        if (fullAddress.isNotEmpty) {
          addressController.text = fullAddress;
          await setValue(USER_ADDRESS, fullAddress);
          if (showToast) toast("Live location address fetched!");
        }
      }
    } catch (e) {
      log("fetchLiveAddress error: $e");
    } finally {
      if (mounted) setState(() {});
    }
  }

  Widget profileImage() {
    if (imageProfile != null) {
      return Image.file(File(imageProfile!.path), height: 100, width: 100, fit: BoxFit.cover, alignment: Alignment.center)
          .cornerRadiusWithClipRRect(100)
          .center();
    } else {
      if (appStore.userProfile.isNotEmpty) {
        return commonCachedNetworkImage(appStore.userProfile.validate(), fit: BoxFit.cover, height: 100, width: 100)
            .cornerRadiusWithClipRRect(100)
            .center();
      } else {
        return commonCachedNetworkImage(ic_profile, height: 90, width: 90)
            .cornerRadiusWithClipRRect(50)
            .paddingOnly(right: 4, bottom: 4)
            .center();
      }
    }
  }

  Future<void> getImage() async {
    imageProfile = null;
    imageProfile = await ImagePicker().pickImage(source: ImageSource.gallery, imageQuality: 100);
    setState(() {});
  }

  Future<void> save() async {
    appStore.setLoading(true);
    // Immediately persist locally so values are never lost
    await setValue(NAME, nameController.text.trim());
    await setValue(USER_CONTACT_NUMBER, '$countryCode ${contactNumberController.text.trim()}');
    await setValue(USER_ADDRESS, addressController.text.trim());
    await updateProfile(
      file: imageProfile != null ? File(imageProfile!.path.validate()) : null,
      name: nameController.text.validate(),
      userName: emailController.text.validate(),
      userEmail: emailController.text.validate(),
      address: addressController.text.validate(),
      contactNumber: '$countryCode ${contactNumberController.text.trim()}',
    ).then((value) {
      // finish(context);
    }).catchError((error) {
      log(error);
      appStore.setLoading(false);
    });
  }

  Future updateProfile({String? userName, String? name, String? userEmail, String? address, String? contactNumber, File? file}) async {
    MultipartRequest multiPartRequest = await getMultiPartRequest('update-profile');
    multiPartRequest.fields['id'] = getIntAsync(USER_ID).toString();
    multiPartRequest.fields['username'] = userName.validate();
    multiPartRequest.fields['email'] = userEmail ?? appStore.userEmail;
    multiPartRequest.fields['name'] = name.validate();
    multiPartRequest.fields['contact_number'] = contactNumber.validate();
    multiPartRequest.fields['address'] = address.validate();

    if (file != null) multiPartRequest.files.add(await MultipartFile.fromPath('profile_image', file.path));

    await sendMultiPartRequest(multiPartRequest, onSuccess: (data) async {
      if (data != null) {
        LoginResponse res = LoginResponse.fromJson(data);
        if (res.data != null) {
          appStore.setLoading(false);
          if (widget.isGoogle == true) {
            ensureDefaultCity();
            autoDetectCityFromGps();
            if (getStringAsync(USER_TYPE) == CLIENT) {
              DashboardScreen().launch(context, isNewTask: true);
            } else {
              DHomeFragment().launch(context, isNewTask: true);
            }
          } else {
            Navigator.pop(context);
          }
          await setValue(NAME, res.data!.name.validate());
          await setValue(USER_NAME, res.data!.username.validate());
          await setValue(USER_ADDRESS, res.data!.address.validate());
          await setValue(USER_CONTACT_NUMBER, res.data!.contactNumber.validate());
          await appStore.setUserEmail(res.data!.email.validate());
          appStore.setUserProfile(res.data!.profileImage.validate());
        }
        toast(res.message.toString());
      }
    }, onError: (error) {
      appStore.setLoading(false);
      toast(error.toString());
    });
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  @override
  Widget build(BuildContext context) {
    return CommonScaffoldComponent(
      showBack: !widget.isGoogle.validate(),
      appBarTitle: language.editProfile,
      body: Stack(
        children: [
          SingleChildScrollView(
            padding: .only(left: 16, top: 16, right: 16, bottom: 16),
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: .start,
                children: [
                  Stack(
                    children: [
                      profileImage(),
                      Align(
                        alignment: Alignment.bottomCenter,
                        child: Container(
                          margin: .only(top: 60, left: 80),
                          padding: .all(6),
                          decoration: boxDecorationWithRoundedCorners(
                              backgroundColor: ColorUtils.colorPrimary,
                              border: Border.all(width: 1, color: Colors.white),
                              boxShape: BoxShape.circle),
                          child: Icon(
                            Icons.edit,
                            color: white,
                            size: 16,
                          ),
                        ),
                      )
                    ],
                  ).onTap(() {
                    getImage();
                  }, highlightColor: Colors.transparent, splashColor: Colors.transparent),
                  16.height,
                  Text(language.email, style: primaryTextStyle()),
                  8.height,
                  AppTextField(
                    readOnly: true,
                    controller: emailController,
                    textFieldType: TextFieldType.EMAIL,
                    focus: emailFocus,
                    nextFocus: nameFocus,
                    decoration: commonInputDecoration(),
                    onTap: () {
                      toast(language.notChangeEmail);
                    },
                  ),
                  16.height,
                  Text(language.name, style: primaryTextStyle()),
                  8.height,
                  AppTextField(
                    controller: nameController,
                    textFieldType: TextFieldType.NAME,
                    focus: nameFocus,
                    nextFocus: addressFocus,
                    decoration: commonInputDecoration(),
                    errorThisFieldRequired: language.fieldRequiredMsg,
                  ),
                  16.height,
                  Text(language.contactNumber, style: primaryTextStyle()),
                  8.height,
                  AppTextField(
                    controller: contactNumberController,
                    textFieldType: TextFieldType.PHONE,
                    readOnly: !widget.isGoogle.validate() && contactNumberController.text.isNotEmpty,
                    focus: contactFocus,
                    nextFocus: addressFocus,
                    decoration: commonInputDecoration(
                      prefixIcon: IntrinsicHeight(
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            CountryCodePicker(
                              initialSelection: countryCode,
                              showCountryOnly: false,
                              dialogSize: Size(context.width() - 60, context.height() * 0.6),
                              showFlag: true,
                              enabled: widget.isGoogle.validate() || contactNumberController.text.isEmpty,
                              showFlagDialog: true,
                              showOnlyCountryWhenClosed: false,
                              alignLeft: false,
                              textStyle: primaryTextStyle(),
                              dialogBackgroundColor: Theme.of(context).cardColor,
                              barrierColor: Colors.black12,
                              dialogTextStyle: primaryTextStyle(),
                              searchDecoration: InputDecoration(
                                iconColor: Theme.of(context).dividerColor,
                                enabledBorder: UnderlineInputBorder(borderSide: BorderSide(color: Theme.of(context).dividerColor)),
                                focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: ColorUtils.colorPrimary)),
                              ),
                              searchStyle: primaryTextStyle(),
                              onInit: (c) {
                                countryCode = c!.dialCode!;
                              },
                              onChanged: (c) {
                                countryCode = c.dialCode!;
                              },
                            ),
                            VerticalDivider(color: Colors.grey.withOpacity(0.5)),
                          ],
                        ),
                      ),
                    ),
                    validator: (value) {
                      if (value!.trim().isEmpty) return language.fieldRequiredMsg;
                      //  if (value.trim().length < minContactLength || value.trim().length > maxContactLength) return language.contactLength;
                      if (value.trim().length < minContactLength || value.trim().length > maxContactLength)
                        return language.phoneNumberInvalid;
                      return null;
                    },
                    onTap: () {
                      if (!widget.isGoogle.validate() && contactNumberController.text.isNotEmpty) toast(language.notChangeMobileNo);
                    },
                    inputFormatters: [
                      FilteringTextInputFormatter.digitsOnly,
                    ],
                  ),
                  16.height,
                  if (!widget.isGoogle.validate()) ...[
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(language.address, style: primaryTextStyle()),
                        InkWell(
                          onTap: () async {
                            await fetchLiveAddress(showToast: true);
                          },
                          borderRadius: BorderRadius.circular(8),
                          child: Container(
                            padding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                            decoration: boxDecorationWithRoundedCorners(
                              backgroundColor: ColorUtils.colorPrimary.withOpacity(0.12),
                              borderRadius: BorderRadius.circular(8),
                              border: Border.all(color: ColorUtils.colorPrimary.withOpacity(0.3)),
                            ),
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Icon(Icons.my_location, color: ColorUtils.colorPrimary, size: 14),
                                4.width,
                                Text("Fetch Live Location", style: boldTextStyle(color: ColorUtils.colorPrimary, size: 12)),
                              ],
                            ),
                          ),
                        ),
                      ],
                    ),
                    8.height,
                    AppTextField(
                      controller: addressController,
                      textFieldType: TextFieldType.MULTILINE,
                      focus: addressFocus,
                      textInputAction: TextInputAction.done,
                      decoration: commonInputDecoration(hintText: "Enter address or fetch live location"),
                      errorThisFieldRequired: language.fieldRequiredMsg,
                    ),
                    16.height,
                  ],
                ],
              ),
            ),
          ),
          Observer(builder: (_) => loaderWidget().visible(appStore.isLoading)),
        ],
      ),
      bottomNavigationBar: Padding(
        padding: .all(16),
        child: commonButton(language.saveChanges, () {
          if (_formKey.currentState!.validate()) {
            if (getStringAsync(USER_EMAIL) == 'jose@gmail.com' || getStringAsync(USER_EMAIL) == 'mark@gmail.com') {
              toast(language.demoMsg);
            } else {
              save();
            }
          }
        }),
      ),
    );
  }
}
