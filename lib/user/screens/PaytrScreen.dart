import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:mighty_delivery/extensions/app_text_field.dart';
import 'package:mighty_delivery/extensions/colors.dart';
import 'package:mighty_delivery/extensions/decorations.dart';
import 'package:mighty_delivery/extensions/extension_util/context_extensions.dart';
import 'package:mighty_delivery/extensions/extension_util/int_extensions.dart';
import 'package:mighty_delivery/extensions/extension_util/widget_extensions.dart';
import 'package:mighty_delivery/extensions/text_styles.dart';

import '../../extensions/app_button.dart';
import '../../extensions/common.dart';
import '../../main.dart';
import '../../main/components/CommonScaffoldComponent.dart';
import '../../main/network/RestApis.dart';
import '../../main/utils/Common.dart';
import '../../main/utils/Constants.dart';
import '../../main/utils/dynamic_theme.dart';
import 'DashboardScreen.dart';
import 'LoadPaytrWebView.dart';

final _formKey = GlobalKey<FormState>();

class Paytrscreen extends StatefulWidget {
  num? totalAmount;
  int? orderId;
  Paytrscreen({super.key, this.totalAmount, this.orderId});

  @override
  State<Paytrscreen> createState() => _PaytrscreenState();
}

class _PaytrscreenState extends State<Paytrscreen> {
  TextEditingController cardNumberController = TextEditingController();
  TextEditingController cardHolderController = TextEditingController();
  TextEditingController expiryController = TextEditingController();
  TextEditingController cvvController = TextEditingController();

  @override
  void initState() {
    super.initState();
    cardNumberController.addListener(updateUI);
    cardHolderController.addListener(updateUI);
    expiryController.addListener(updateUI);
  }

  void updateUI() {
    setState(() {}); // Trigger UI rebuild when text changes
  }

  @override
  Widget build(BuildContext context) {
    return CommonScaffoldComponent(
      appBarTitle: language.payment,
      body: Padding(
        padding: const .all(20),
        child: SingleChildScrollView(
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: .start,
              children: [
                CreditCardWidget(
                  cardNumber: cardNumberController.text,
                  cardHolder: cardHolderController.text,
                  expiryDate: expiryController.text,
                ),
                20.height,
                PaymentForm(
                  cardNumberController: cardNumberController,
                  cardHolderController: cardHolderController,
                  expiryController: expiryController,
                  cvvController: cvvController,
                  totalAmount: widget.totalAmount,
                  orderId: widget.orderId,
                  context: context,
                  onChanged: () => setState(() {}),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class CreditCardWidget extends StatelessWidget {
  String? cardNumber;
  String? cardHolder;
  String? expiryDate;
  CreditCardWidget({required this.cardNumber, required this.cardHolder, required this.expiryDate});
  String formatCardNumber(String number) {
    String text = number.replaceAll(RegExp(r'\s+'), ''); // Remove spaces
    String formattedText = text.replaceAllMapped(RegExp(r'.{1,4}'), (match) => '${match.group(0)} ');
    return formattedText.trim();
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const .all(20),
      decoration: boxDecorationWithRoundedCorners(backgroundColor: ColorUtils.colorPrimary),
      child: Column(
        crossAxisAlignment: .start,
        children: [
          10.height,
          Row(
            mainAxisAlignment: .spaceBetween,
            children: [
              Text(
                (cardNumber != null && cardNumber != "") ? formatCardNumber(cardNumber!) : "XXXX XXXX XXXX XXXX",
                style: boldTextStyle(color: white, size: (cardNumber != null && cardNumber != "") ? 22 : 20, letterSpacing: 3),
              ),
            ],
          ),
          10.height,
          Row(
            mainAxisAlignment: .spaceBetween,
            crossAxisAlignment: .end,
            children: [
              Column(
                crossAxisAlignment: .start,
                children: [
                  Text(
                    language.cardHolder,
                    style: secondaryTextStyle(color: white, size: 12),
                  ),
                  4.height,
                  Text(
                    (cardHolder != null && cardHolder != "") ? cardHolder! : "XXXX",
                    style: boldTextStyle(size: 18, color: white),
                  ),
                ],
              ),
              Column(
                crossAxisAlignment: .start,
                children: [
                  Text(
                    language.expires,
                    style: secondaryTextStyle(color: white),
                  ),
                  Text(
                    (expiryDate != null && expiryDate != "") ? expiryDate! : "XX/XX",
                    style: boldTextStyle(size: 18, color: white),
                  ),
                ],
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class PaymentForm extends StatefulWidget {
  TextEditingController? cardNumberController;
  TextEditingController? cardHolderController;
  TextEditingController? expiryController;
  TextEditingController? cvvController;
  VoidCallback? onChanged;
  num? totalAmount;
  int? orderId;
  BuildContext context;
  PaymentForm({required this.cardNumberController, required this.cardHolderController, required this.expiryController, required this.cvvController, required this.onChanged, required this.totalAmount, required this.context, this.orderId});

  @override
  State<PaymentForm> createState() => _PaymentFormState();
}

class _PaymentFormState extends State<PaymentForm> {
  savePayment() async {
    if (appStore.isLoading) return; // Prevent multiple clicks

    appStore.setLoading(true);
    List<String> expiryParts = widget.expiryController!.text.split('/');
    String expiryMonth = expiryParts.isNotEmpty ? expiryParts[0] : "";
    String expiryYear = expiryParts.length > 1 ? expiryParts[1] : "";
    Map<String, dynamic> paymentRequest = {
      "cc_owner": widget.cardHolderController!.text,
      "card_number": widget.cardNumberController!.text.replaceAll(' ', ''),
      "expiry_month": expiryMonth,
      "expiry_year": expiryYear,
      if (widget.orderId != null) ...{
        "order_id": widget.orderId,
      },
      "cvv": widget.cvvController!.text.trim(),
      "payment_amount": widget.totalAmount!,
      "non_3d": 0,
    };
    // Map<String, dynamic> paymentRequest = {"cc_owner": "John Doe", "card_number": "9792030394440796", "expiry_month": "01", "expiry_year": "00", "cvv": "000", "payment_amount": 120.99, "non_3d": 0};

    await savePaytr(paymentRequest).then((value) async {
      if (value.status == "success") {
        print("value success");
        appStore.setLoading(false);
        LoadpaytrWebview(content: value.data, status: value.status)
            .launch(
          widget.context,
        )
            .then((value) {
          // if (value == true) {
          //   final dashboardState = navigatorKey.currentState?.context.findAncestorStateOfType<DashboardScreenState>();
          //   dashboardState?.refreshOrderFragment(); // Refresh OrderFragment after returning
          // }
          Navigator.pop(context, true); // Return true when payment is done
        });
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: .start,
      children: [
        //buildTextField("CREDIT CARD NUMBER", "1234 5678 0000", cardNumberController!,),
        Text(
          language.cardNumber,
          style: secondaryTextStyle(),
        ),
        5.height,
        AppTextField(
          controller: widget.cardNumberController,
          decoration: commonInputDecoration(),
          textFieldType: TextFieldType.NAME,
          minLines: 1,
          maxLines: 2,
          keyboardType: TextInputType.number,
          inputFormatters: [
            LengthLimitingTextInputFormatter(19), // Account for spaces
            CardNumberFormatter(), // Add spaces dynamically
          ],
          onChanged: (val) => widget.onChanged!(),
        ),

        10.height,
        // buildTextField("CARD HOLDER NAME", "DAISY", cardHolderController!),
        Text(
          language.cardHolderName,
          style: secondaryTextStyle(),
        ),
        5.height,
        AppTextField(
          controller: widget.cardHolderController,
          decoration: commonInputDecoration(),
          textFieldType: TextFieldType.NAME,
          minLines: 1,
          maxLines: 2,
          inputFormatters: [
            FilteringTextInputFormatter.allow(RegExp(r"[a-zA-Z ]*")), // Only letters & spaces
            LengthLimitingTextInputFormatter(30), // Limit length
          ],
          onChanged: (val) => widget.onChanged?.call(),
        ),
        const SizedBox(height: 10),
        Row(
          children: [
            Expanded(
                flex: 1,
                child: Column(
                  crossAxisAlignment: .start,
                  children: [
                    Text(
                      language.expiry,
                      style: secondaryTextStyle(),
                    ),
                    5.height,
                    AppTextField(
                      controller: widget.expiryController,
                      decoration: commonInputDecoration(),
                      textFieldType: TextFieldType.NAME,
                      minLines: 1,
                      maxLines: 2,
                      onChanged: (val) => widget.onChanged!(),
                      isValidationRequired: true,
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return language.expiryValidation;
                        }
                        if (!isValidExpiry(value)) {
                          return language.invalidExpiryDate;
                        }
                        return null;
                      },
                      inputFormatters: [
                        FilteringTextInputFormatter.digitsOnly,
                        LengthLimitingTextInputFormatter(4),
                        ExpiryDateFormatter(),
                      ],
                    ),
                  ],
                )),
            10.width,
            Expanded(
                flex: 1,
                child: Column(
                  crossAxisAlignment: .start,
                  children: [
                    Text(
                      language.cvv,
                      style: secondaryTextStyle(),
                    ),
                    5.height,
                    AppTextField(
                      controller: widget.cvvController,
                      decoration: commonInputDecoration(),
                      textFieldType: TextFieldType.NAME,
                      minLines: 1,
                      maxLines: 2,
                      inputFormatters: [
                        FilteringTextInputFormatter.digitsOnly,
                        LengthLimitingTextInputFormatter(4),
                      ],
                      onChanged: (val) => widget.onChanged!(),
                      isValidationRequired: true,
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return language.cvvValidation;
                        }
                        if (!RegExp(r'^\d{3,4}$').hasMatch(value)) {
                          return language.invalidCvv;
                        }
                        return null; // No error
                      },
                    ),
                  ],
                )),
          ],
        ),
        30.height,
        Align(
          child: Container(
            width: context.width(),
            height: context.width() * 0.15,
            decoration: boxDecorationWithRoundedCorners(
              backgroundColor: ColorUtils.colorPrimary,
            ),
            child: AppButton(
              elevation: 0,
              height: 15,
              color: Colors.transparent,
              padding: .symmetric(vertical: 4),
              shapeBorder: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(defaultRadius),
                side: BorderSide(color: ColorUtils.colorPrimary),
              ),
              child: appStore.isLoading
                  ? CircularProgressIndicator(
                      color: ColorUtils.colorPrimary,
                    ).center()
                  : Text("${language.pay} ${printAmount(widget.totalAmount)}", style: boldTextStyle(color: white)),
              onTap: () {
                if (_formKey.currentState!.validate()) {
                  savePayment();
                }
              },
            ),
          ),
        ),
      ],
    );
  }

  /// Validates expiry date format MM/YY and checks if it's valid
  bool isValidExpiry(String input) {
    if (input.length != 5 || !RegExp(r'^(0[1-9]|1[0-2])/\d{2}$').hasMatch(input)) return false;

    final currentYear = DateTime.now().year % 100; // Get last two digits of current year
    final currentMonth = DateTime.now().month;

    final parts = input.split('/');
    final int month = int.tryParse(parts[0]) ?? 0;
    final int year = int.tryParse(parts[1]) ?? 0;

    if (year < currentYear || (year == currentYear && month < currentMonth)) return false;

    return true;
  }

  // Widget buildTextField(String label, String hintText, TextEditingController controller) {
  //   return Column(
  //     crossAxisAlignment: .start,
  //     children: [
  //       Text(
  //         label,
  //         style: secondaryTextStyle(),
  //       ),
  //       5.height,
  //       AppTextField(
  //         controller: controller,
  //         decoration: commonInputDecoration(),
  //         textFieldType: TextFieldType.NAME,
  //         minLines: 1,
  //         maxLines: 2,
  //         onChanged: (val) => widget.onChanged!(),
  //       ),
  //     ],
  //   );
  // }
}

class CardNumberFormatter extends TextInputFormatter {
  @override
  TextEditingValue formatEditUpdate(TextEditingValue oldValue, TextEditingValue newValue) {
    String rawText = newValue.text.replaceAll(' ', ''); // Remove spaces

    if (rawText.isEmpty) {
      return TextEditingValue(
        text: '',
        selection: TextSelection.collapsed(offset: 0),
      );
    }

    String formattedText = '';
    int selectionIndex = newValue.selection.baseOffset;
    int spaceCount = 0;

    for (int i = 0; i < rawText.length; i++) {
      if (i > 0 && i % 4 == 0) {
        formattedText += ' ';
        spaceCount++;
      }
      formattedText += rawText[i];
    }

    // Adjust cursor position
    int newCursorPosition = newValue.selection.baseOffset + spaceCount;
    newCursorPosition = newCursorPosition.clamp(0, formattedText.length);

    return TextEditingValue(
      text: formattedText,
      selection: TextSelection.collapsed(offset: newCursorPosition),
    );
  }
}

class ExpiryDateFormatter extends TextInputFormatter {
  @override
  TextEditingValue formatEditUpdate(TextEditingValue oldValue, TextEditingValue newValue) {
    String text = newValue.text.replaceAll(RegExp(r'[^0-9]'), ''); // Remove non-numeric characters

    if (text.length > 4) text = text.substring(0, 4); // Limit to 4 digits

    if (text.length >= 3) {
      text = '${text.substring(0, 2)}/${text.substring(2)}'; // Insert "/"
    }

    return TextEditingValue(
      text: text,
      selection: TextSelection.collapsed(offset: text.length),
    );
  }
}
