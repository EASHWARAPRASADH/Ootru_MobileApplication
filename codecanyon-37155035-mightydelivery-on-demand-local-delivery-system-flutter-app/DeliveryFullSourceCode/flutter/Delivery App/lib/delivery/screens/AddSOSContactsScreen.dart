import 'package:country_code_picker/country_code_picker.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:mighty_delivery/extensions/common.dart';
import 'package:mighty_delivery/extensions/extension_util/context_extensions.dart';
import 'package:mighty_delivery/extensions/extension_util/int_extensions.dart';
import 'package:mighty_delivery/extensions/extension_util/widget_extensions.dart';
import 'package:mighty_delivery/main/network/RestApis.dart';
import '../../extensions/app_text_field.dart';
import '../../extensions/decorations.dart';
import '../../extensions/text_styles.dart';
import '../../main.dart';
import '../../main/components/CommonScaffoldComponent.dart';
import '../../main/models/SOSContactsListResponse.dart';
import '../../main/utils/Common.dart';
import '../../main/utils/Constants.dart';
import '../../main/utils/Widgets.dart';
import '../../main/utils/dynamic_theme.dart';

class Addsoscontactsscreen extends StatefulWidget {
  const Addsoscontactsscreen({super.key});

  @override
  State<Addsoscontactsscreen> createState() => _AddsoscontactsscreenState();
}

class _AddsoscontactsscreenState extends State<Addsoscontactsscreen> {
  TextEditingController phoneController = TextEditingController();
  TextEditingController nameController = TextEditingController();
  String countryCode = defaultPhoneCode;
  FocusNode nameFocus = FocusNode();
  FocusNode phoneFocus = FocusNode();
  GlobalKey<FormState> formKey = GlobalKey<FormState>();
  bool isAdd = false, isShow = true;
  SOSContactsListResponse? response;

  @override
  void initState() {
    super.initState();
    getContactList();
  }

  getContactList() async {
    appStore.setLoading(true);
    setState(() {});
    response = await getSosContactsList();
    appStore.setLoading(false);
    setState(() {});
  }

  @override
  Widget build(BuildContext context) {
    return CommonScaffoldComponent(
      appBarTitle: language.addSOSContacts,
      action: [
        Text("${language.add}", style: boldTextStyle(size: 16, color: Colors.white)).paddingRight(20).onTap(() {
          isAdd = true;
          isShow = false;
          setState(() {});
        }).visible(isShow),
        Text(language.show, style: boldTextStyle(size: 16, color: Colors.white)).paddingRight(20).onTap(() {
          isShow = true;
          isAdd = false;
          getContactList();
          setState(() {});
        }).visible(isAdd),
      ],
      body: Stack(
        children: [
          Padding(
              padding: const .all(16),
              child: Stack(
                children: [
                  Column(
                    children: [
                      if (isAdd)
                        Stack(
                          children: [
                            Column(
                              children: [
                                Form(
                                  key: formKey,
                                  child: Column(
                                    crossAxisAlignment: .start,
                                    children: [
                                      Text(language.contactPersonName, style: primaryTextStyle()),
                                      8.height,
                                      AppTextField(
                                        controller: nameController,
                                        textFieldType: TextFieldType.NAME,
                                        focus: nameFocus,
                                        decoration: commonInputDecoration(),
                                        errorThisFieldRequired: language.fieldRequiredMsg,
                                        errorMinimumPasswordLength: language.name,
                                      ),
                                      16.height,
                                      Text(language.contactNumber, style: primaryTextStyle()),
                                      8.height,
                                      AppTextField(
                                        controller: phoneController,
                                        textFieldType: TextFieldType.PHONE,
                                        focus: phoneFocus,
                                        nextFocus: phoneFocus,
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
                                          // if (value.trim().length < minContactLength || value.trim().length > maxContactLength) return language.contactLength;
                                          return null;
                                        },
                                        inputFormatters: [
                                          FilteringTextInputFormatter.digitsOnly,
                                        ],
                                      ),
                                      50.height,
                                      commonButton(language.add, () async {
                                        if (formKey.currentState!.validate()) {
                                          appStore.setLoading(true);
                                          setState(() {});
                                          Map<String, dynamic> request = {"name": nameController.text.toString(), "contact_number": "$countryCode${phoneController.text.trim()}"};
                                          await addSOSContacts(request).then((value) {
                                            toast(value.message);
                                            nameController.clear();
                                            phoneController.clear();
                                            isAdd = false;
                                            isShow = true;
                                            getContactList();
                                          });
                                        }
                                      }, width: context.width()),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                            //   loaderWidget().center().visible(appStore.isLoading),
                          ],
                        ),
                      if (isShow)
                        Expanded(
                          child: (response != null && response!.data!.length > 0)
                              ? ListView.builder(
                                  itemCount: response!.data!.length,
                                  itemBuilder: (context, index) {
                                    return Container(
                                      //  color: Colors.white10,
                                      margin: const .symmetric(vertical: 6),
                                      decoration: boxDecorationWithRoundedCorners(border: Border.all(color: ColorUtils.colorPrimary), backgroundColor: Colors.white10),
                                      child: ListTile(
                                        // leading: Icon(Icons.warning, color: Colors.redAccent),
                                        title: Text(
                                          response!.data![index].name ?? "",
                                          style: boldTextStyle(color: ColorUtils.colorPrimary),
                                        ),
                                        subtitle: Text(
                                          response!.data![index].contactNumber ?? "",
                                          style: primaryTextStyle(color: Colors.grey),
                                        ),
                                        trailing: Icon(Icons.delete, size: 24, color: Colors.red).onTap(() {
                                          appStore.setLoading(true);
                                          deleteSosContact(response!.data![index].id!).then((value) {
                                            appStore.setLoading(false);
                                            getContactList();
                                          });
                                        }),
                                      ),
                                    );
                                  },
                                )
                              : appStore.isLoading == false
                                  ? Text(language.noDataFound, style: boldTextStyle()).center()
                                  : SizedBox(),
                        ),
                    ],
                  ),
                  loaderWidget().center().visible(appStore.isLoading),
                ],
              )),
        ],
      ),
    );
  }
}
