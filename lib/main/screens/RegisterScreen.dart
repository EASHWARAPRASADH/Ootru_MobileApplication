import 'dart:math';

import 'package:country_code_picker/country_code_picker.dart';
import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import '../../extensions/extension_util/context_extensions.dart';
import '../../extensions/extension_util/int_extensions.dart';
import '../../extensions/extension_util/string_extensions.dart';
import '../../extensions/extension_util/widget_extensions.dart';

import '../../extensions/app_text_field.dart';
import '../../extensions/common.dart';
import '../../extensions/decorations.dart';
import '../../extensions/shared_pref.dart';
import '../../extensions/system_utils.dart';
import '../../extensions/text_styles.dart';
import '../../main.dart';
import '../../main/utils/Common.dart';
import '../../main/utils/Constants.dart';
import '../../main/utils/Widgets.dart';
import '../components/CommonScaffoldComponent.dart';
import '../../delivery/fragment/DHomeFragment.dart';
import '../../user/screens/DashboardScreen.dart';
import '../helper/encrypt_data.dart';
import '../network/RestApis.dart';
import '../services/AuthServices.dart';
import 'package:geolocator/geolocator.dart';
import '../utils/dynamic_theme.dart';

class RegisterScreen extends StatefulWidget {
  final String? userType;
  static String tag = '/RegisterScreen';

  RegisterScreen({this.userType});

  @override
  RegisterScreenState createState() => RegisterScreenState();
}

class RegisterScreenState extends State<RegisterScreen> {
  final GlobalKey<FormState> formKey = GlobalKey<FormState>();
  AuthServices authService = AuthServices();
  String countryCode = defaultPhoneCode;

  TextEditingController nameController = TextEditingController();
  TextEditingController emailController = TextEditingController();
  TextEditingController phoneController = TextEditingController();
  TextEditingController passController = TextEditingController();
  TextEditingController partnerCodeController = TextEditingController();

  FocusNode nameFocus = FocusNode();
  FocusNode emailFocus = FocusNode();
  FocusNode phoneFocus = FocusNode();
  FocusNode passFocus = FocusNode();
  FocusNode partnerCodeFocus = FocusNode();

  bool isAcceptedTc = true;

  @override
  void initState() {
    super.initState();
    init();
  }

  Future<void> init() async {
    try {
      LocationPermission permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
      }
    } catch (e) {
      log("RegisterScreen location check: $e");
    }
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  Future<void> registerApiCall() async {
    if (formKey.currentState!.validate()) {
      formKey.currentState!.save();
      hideKeyboard(context);
      if (isAcceptedTc) {
        appStore.setLoading(true);
        String name = nameController.text.trim();
        String username = emailController.text.trim();
        String userType = widget.userType ?? CLIENT;
        String contactNumber = '$countryCode ${phoneController.text.trim()}';
        String email = emailController.text.trim();
        String password = passController.text.trim();

        // Save local session
        await setValue(NAME, name.isNotEmpty ? name : 'User');
        await setValue(USER_NAME, username);
        await setValue(USER_EMAIL, email);
        await setValue(USER_PASSWORD, password);
        await setValue(USER_TYPE, userType);
        await setValue(USER_CONTACT_NUMBER, contactNumber);
        await setValue(STATUS, 1);
        await setValue(IS_LOGGED_IN, true);
        await setValue(OTP_VERIFIED, true);
        await setValue(EMAIL_VERIFIED, true);
        await setValue(IS_VERIFIED_DELIVERY_MAN, true);
        await setValue(USER_TOKEN, 'local_token_${DateTime.now().millisecondsSinceEpoch}');
        await setValue(USER_ID, DateTime.now().millisecondsSinceEpoch ~/ 1000);

        appStore.setUserEmail(email);
        appStore.setUserType(userType);
        appStore.setLogin(true);
        appStore.setLoading(false);

        toast("Registered successfully!");

        if (userType == DELIVERY_MAN) {
          DHomeFragment().launch(context, isNewTask: true, pageRouteAnimation: PageRouteAnimation.Slide);
        } else {
          DashboardScreen().launch(context, isNewTask: true, pageRouteAnimation: PageRouteAnimation.Slide);
        }
      } else {
        toast(language.acceptTermService);
      }
    }
  }

  void generateRandomValues() {
    final random = Random();

    nameController.text = "User${random.nextInt(1000)}";
    emailController.text = "user${random.nextInt(1000)}@example.com";
    phoneController.text = "${random.nextInt(900000000) + 100000000}";
    passController.text = "pass${random.nextInt(10000)}";
    setState(() {});
  }

  @override
  Widget build(BuildContext context) {
    return CommonScaffoldComponent(
      appBarTitle:
          "${language.signUp.capitalizeFirstLetter()} ${language.forKey} ${widget.userType == CLIENT ? language.lblUser.toLowerCase() : language.lblDeliveryBoy.toLowerCase()}",
      body: Stack(
        children: [
          SingleChildScrollView(
            padding: .all(16),
            physics: BouncingScrollPhysics(),
            child: Form(
              key: formKey,
              child: Column(
                crossAxisAlignment: .start,
                children: [
                  // ElevatedButton(
                  //   onPressed: generateRandomValues,
                  //   child: Text("Generate Random Data"),
                  // ),
                  16.height,
                  Text(language.name, style: primaryTextStyle()),
                  8.height,
                  AppTextField(
                    controller: nameController,
                    textFieldType: TextFieldType.NAME,
                    focus: nameFocus,
                    nextFocus: emailFocus,
                    decoration: commonInputDecoration(),
                    errorThisFieldRequired: language.fieldRequiredMsg,
                  ),
                  16.height,
                  Text(language.email, style: primaryTextStyle()),
                  8.height,
                  AppTextField(
                      controller: emailController,
                      textFieldType: TextFieldType.EMAIL,
                      focus: emailFocus,
                      nextFocus: phoneFocus,
                      decoration: commonInputDecoration(),
                      errorThisFieldRequired: language.fieldRequiredMsg,
                      errorInvalidEmail: language.emailInvalid),
                  16.height,
                  Text(language.contactNumber, style: primaryTextStyle()),
                  8.height,
                  AppTextField(
                    controller: phoneController,
                    textFieldType: TextFieldType.PHONE,
                    focus: phoneFocus,
                    nextFocus: passFocus,
                    decoration: commonInputDecoration(
                      prefixIcon: IntrinsicHeight(
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            CountryCodePicker(
                              initialSelection: countryCode,
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
                                countryCode = c?.dialCode ?? defaultPhoneCode;
                              },
                              onChanged: (c) {
                                countryCode = c?.dialCode ?? defaultPhoneCode;
                              },
                            ),
                            VerticalDivider(
                                color: Colors.grey.withOpacity(0.5)),
                          ],
                        ),
                      ),
                    ),
                    validator: (value) {
                      if (value == null || value.trim().isEmpty)
                        return language.fieldRequiredMsg;
                      // if (value.trim().length < minContactLength || value.trim().length > maxContactLength) return language.contactLength;
                      return null;
                    },
                    inputFormatters: [
                      FilteringTextInputFormatter.digitsOnly,
                    ],
                  ),
                  16.height,
                  Text(language.password, style: primaryTextStyle()),
                  8.height,
                  AppTextField(
                    controller: passController,
                    textFieldType: TextFieldType.PASSWORD,
                    focus: passFocus,
                    decoration: commonInputDecoration(),
                    errorThisFieldRequired: language.fieldRequiredMsg,
                    errorMinimumPasswordLength: language.passwordInvalid,
                  ),
                  8.height,
                  Text(language.parnerCode, style: primaryTextStyle()),
                  8.height,
                  AppTextField(
                    controller: partnerCodeController,
                    textFieldType: TextFieldType.NAME,
                    focus: partnerCodeFocus,
                    isValidationRequired: false,
                    decoration: commonInputDecoration(),
                  ),
                  16.height,
                  Row(
                    children: [
                      SizedBox(
                        height: 20,
                        width: 20,
                        child: Checkbox(
                          shape:
                              RoundedRectangleBorder(borderRadius: radius(4)),
                          checkColor: Colors.white,
                          materialTapTargetSize:
                              MaterialTapTargetSize.shrinkWrap,
                          focusColor: ColorUtils.colorPrimary,
                          activeColor: ColorUtils.colorPrimary,
                          value: isAcceptedTc,
                          onChanged: (bool? value) async {
                            isAcceptedTc = value ?? false;
                            setState(() {});
                          },
                        ),
                      ),
                      10.width,
                      RichText(
                        text: TextSpan(children: [
                          TextSpan(
                              text: '${language.iAgreeToThe} ',
                              style: secondaryTextStyle()),
                          TextSpan(
                            text: language.termOfService,
                            style: boldTextStyle(
                                color: ColorUtils.colorPrimary, size: 14),
                            recognizer: TapGestureRecognizer()
                              ..onTap = () {
                                commonLaunchUrl(mTermAndCondition);
                              },
                          ),
                          TextSpan(text: ' & ', style: secondaryTextStyle()),
                          TextSpan(
                            text: language.privacyPolicy,
                            style: boldTextStyle(
                                color: ColorUtils.colorPrimary, size: 14),
                            recognizer: TapGestureRecognizer()
                              ..onTap = () {
                                commonLaunchUrl(mPrivacyPolicy);
                              },
                          ),
                        ]),
                      ).expand()
                    ],
                  ),
                  30.height,
                  commonButton(language.signUp, () {
                    registerApiCall();
                  }, width: context.width()),
                  30.height,
                  Row(
                    mainAxisAlignment: .center,
                    children: [
                      Text(language.alreadyHaveAnAccount,
                          style: primaryTextStyle()),
                      4.width,
                      Text(language.signIn,
                              style:
                                  boldTextStyle(color: ColorUtils.colorPrimary))
                          .onTap(() {
                        finish(context);
                      }),
                    ],
                  ),
                  16.height,
                ],
              ),
            ),
          ),
          Observer(
              builder: (context) => loaderWidget().visible(appStore.isLoading)),
        ],
      ),
    );
  }
}
