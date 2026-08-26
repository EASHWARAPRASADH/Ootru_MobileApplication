import 'dart:convert';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter_mobx/flutter_mobx.dart';
import 'package:flutter_paytabs_bridge/BaseBillingShippingInfo.dart' as payTab;
import 'package:flutter_paytabs_bridge/IOSThemeConfiguration.dart';
import 'package:flutter_paytabs_bridge/PaymentSdkApms.dart';
import 'package:flutter_paytabs_bridge/PaymentSdkConfigurationDetails.dart';
import 'package:flutter_paytabs_bridge/flutter_paytabs_bridge.dart';
import 'package:flutter_stripe/flutter_stripe.dart' hide PaymentIntent;
import 'package:flutterwave_standard_smart/core/flutterwave.dart';
import 'package:flutterwave_standard_smart/models/requests/customer.dart';
import 'package:flutterwave_standard_smart/models/requests/customizations.dart';
import 'package:flutterwave_standard_smart/models/responses/charge_response.dart';
import 'package:flutterwave_standard_smart/view/view_utils.dart';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';
import 'package:mighty_delivery/user/screens/web_view_screen.dart';
import 'package:pay_with_paystack/pay_with_paystack.dart';
import 'package:paytmpayments_allinonesdk/paytmpayments_allinonesdk.dart';
import '../../extensions/extension_util/context_extensions.dart';
import '../../extensions/extension_util/int_extensions.dart';
import '../../extensions/extension_util/string_extensions.dart';
import '../../extensions/extension_util/widget_extensions.dart';
import '../../languageConfiguration/LanguageDefaultJson.dart';
import '../../main/utils/Images.dart';
import 'package:my_fatoorah/my_fatoorah.dart';
import 'package:razorpay_flutter/razorpay_flutter.dart';

import '../../extensions/common.dart';
import '../../extensions/decorations.dart';
import '../../extensions/shared_pref.dart';
import '../../extensions/system_utils.dart';
import '../../extensions/text_styles.dart';
import '../../main.dart';
import '../../main/components/CommonScaffoldComponent.dart';
import '../../main/models/CityListModel.dart';
import '../../main/models/CountryListModel.dart';
import '../../main/models/PaymentGatewayListModel.dart';
import '../../main/models/StripePayModel.dart';
import '../../main/network/RestApis.dart';
import '../../main/utils/Common.dart';
import '../../main/utils/Constants.dart';
import '../../main/utils/Widgets.dart';
import '../../main/utils/dynamic_theme.dart';
import 'DashboardScreen.dart';
import 'PaytrScreen.dart';

import 'package:package_info_plus/package_info_plus.dart';

class PaymentScreen extends StatefulWidget {
  static String tag = '/PaymentScreen';
  final num totalAmount;
  final int? orderId;
  final bool? isWallet;
  final bool? isOnline;

  PaymentScreen(
      {required this.totalAmount,
      this.orderId,
      this.isWallet = false,
      this.isOnline = false});

  @override
  PaymentScreenState createState() => PaymentScreenState();
}

class PaymentScreenState extends State<PaymentScreen> {
  String? razorKey,
      stripPaymentKey,
      stripPaymentPublishKey,
      flutterWavePublicKey,
      flutterWaveSecretKey,
      flutterWaveEncryptionKey,
      payStackPublicKey,
      payPalTokenizationKey,
      mercadoPagoPublicKey,
      mercadoPagoAccessToken,
      payTabsProfileId,
      payTabsServerKey,
      payTabsClientKey,
      paytmMerchantId,
      paytmMerchantKey,
      myFatoorahToken;
  List<PaymentGatewayData> paymentGatewayList = [];
  String? selectedPaymentType;
  bool isTestType = true;
  late Razorpay _razorpay;

  Map<String, Object> paypalValue = {
    "is_test": true,
    "client_id": "1234",
    "client_secret": "1234",
  };
  // final plugin = PaystackPlugin();
  // CheckoutMethod method = CheckoutMethod.card;

  bool isDisabled = false;

  bool loading = false;

  @override
  void initState() {
    super.initState();
    afterBuildCreated(() {
      init();
    });
  }

  Future<void> init() async {
    await paymentListApiCall();
    if (paymentGatewayList
        .any((element) => element.type == PAYMENT_TYPE_STRIPE)) {
      Stripe.publishableKey = stripPaymentPublishKey.validate();
      Stripe.merchantIdentifier = mStripeIdentifier;
      await Stripe.instance.applySettings().catchError((e) {
        log("${e.toString()}");
      });
    }
    // if (paymentGatewayList
    //     .any((element) => element.type == PAYMENT_TYPE_PAYSTACK)) {
    //   plugin.initialize(publicKey: payStackPublicKey.validate());
    // }
    if (paymentGatewayList
        .any((element) => element.type == PAYMENT_TYPE_RAZORPAY)) {
      _razorpay = Razorpay();
      _razorpay.on(Razorpay.EVENT_PAYMENT_SUCCESS, _handlePaymentSuccess);
      _razorpay.on(Razorpay.EVENT_PAYMENT_ERROR, _handlePaymentError);
      _razorpay.on(Razorpay.EVENT_EXTERNAL_WALLET, _handleExternalWallet);
    }
    selectedPaymentType = paymentGatewayList.first.type;
  }

  /// Get Payment Gateway Api Call
  Future<void> paymentListApiCall() async {
    appStore.setLoading(true);
    await getPaymentGatewayList().then((value) {
      appStore.setLoading(false);
      paymentGatewayList.addAll(value.data!);
      if (paymentGatewayList.isNotEmpty) {
        paymentGatewayList.forEach((element) {
          if (element.type == PAYMENT_TYPE_STRIPE) {
            stripPaymentKey = element.isTest == 1
                ? element.testValue!.secretKey
                : element.liveValue!.secretKey;
            stripPaymentPublishKey = element.isTest == 1
                ? element.testValue!.publishableKey
                : element.liveValue!.publishableKey;
          } else if (element.type == PAYMENT_TYPE_PAYSTACK) {
            payStackPublicKey = element.isTest == 1
                ? element.testValue!.publicKey
                : element.liveValue!.publicKey;
          } else if (element.type == PAYMENT_TYPE_RAZORPAY) {
            razorKey = element.isTest == 1
                ? element.testValue!.keyId.validate()
                : element.liveValue!.keyId.validate();
          } else if (element.type == PAYMENT_TYPE_FLUTTERWAVE) {
            flutterWavePublicKey = element.isTest == 1
                ? element.testValue!.publicKey
                : element.liveValue!.publicKey;
            flutterWaveSecretKey = element.isTest == 1
                ? element.testValue!.secretKey
                : element.liveValue!.secretKey;
            flutterWaveEncryptionKey = element.isTest == 1
                ? element.testValue!.encryptionKey
                : element.liveValue!.encryptionKey;
          } else if (element.type == PAYMENT_TYPE_PAYPAL) {
            payPalTokenizationKey = element.isTest == 1
                ? element.testValue!.tokenizationKey
                : element.liveValue!.tokenizationKey;
            if (element.isTest == 1) {
              paypalValue = {
                "is_test": true,
                //"client_id": "Ac8LLq1kIPUq1mdExtXHlim208LHG4pV_VagO3F297Qjv1xMswNlVzLPCWFLd40GO5jyXsIfC-Ef89la",
                "client_id": "${element.testValue?.publicKey}",
                "client_secret": "${element.testValue?.secretKey}",
                // "client_secret": "EOiDT5b9f7C8BZjrUtO1I-VNTm4zS9mys-2QgIDRRY543nXsfM3ebSClAl7WftDAdiaHJQZqiKYVu-oC",
              };
            } else {
              paypalValue = {
                "is_test": false,
                "client_id": "${element.liveValue?.publicKey}",
                "client_secret": "${element.liveValue?.secretKey}",
              };
            }
          } else if (element.type == PAYMENT_TYPE_PAYTABS) {
            payTabsProfileId = element.isTest == 1
                ? element.testValue!.profileId
                : element.liveValue!.profileId;
            payTabsClientKey = element.isTest == 1
                ? element.testValue!.clientKey
                : element.liveValue!.clientKey;
            payTabsServerKey = element.isTest == 1
                ? element.testValue!.serverKey
                : element.liveValue!.serverKey;
          } else if (element.type == PAYMENT_TYPE_MERCADOPAGO) {
            mercadoPagoPublicKey = element.isTest == 1
                ? element.testValue!.publicKey
                : element.liveValue!.publicKey;
            mercadoPagoAccessToken = element.isTest == 1
                ? element.testValue!.accessToken
                : element.liveValue!.accessToken;
          } else if (element.type == PAYMENT_TYPE_PAYTM) {
            paytmMerchantId = element.isTest == 1
                ? element.testValue!.merchantId
                : element.liveValue!.merchantId;
            paytmMerchantKey = element.isTest == 1
                ? element.testValue!.merchantKey
                : element.liveValue!.merchantKey;
          } else if (element.type == PAYMENT_TYPE_MYFATOORAH) {
            myFatoorahToken = element.isTest == 1
                ? element.testValue!.accessToken
                : element.liveValue!.accessToken;
          }
        });
        setState(() {});
      }
    }).catchError((error) {
      appStore.setLoading(false);
      log(error.toString());
    });
  }

  /// Save Payment
  Future<void> savePaymentApiCall(
      {String? paymentType,
      String? txnId,
      String? paymentStatus = PAYMENT_PENDING,
      Map? transactionDetail}) async {
    Map req = {
      "id": "",
      "order_id": widget.orderId.toString(),
      "client_id": getIntAsync(USER_ID).toString(),
      "datetime": DateFormat('yyyy-MM-dd hh:mm:ss').format(DateTime.now()),
      "total_amount": widget.totalAmount.toString(),
      "payment_type": paymentType,
      "txn_id": txnId,
      "payment_status": paymentStatus,
      "is_online": widget.isOnline,
      "transaction_detail": transactionDetail ?? {},
    };

    appStore.setLoading(true);

    savePayment(req).then((value) {
      appStore.setLoading(false);
      toast(value.message.toString());
      DashboardScreen().launch(context, isNewTask: true);
    }).catchError((error) {
      appStore.setLoading(false);
      print(error.toString());
    });
  }

  /// Razor Pay
  void razorPayPayment() async {
    var options = {
      'key': razorKey.validate(),
      'amount': (widget.totalAmount * 100).toInt(),
      'theme.color': '#5957b0',
      'name': mAppName,
      'description': 'On Demand Local Delivery System',
      'retry': {'enabled': true, 'max_count': 1},
      'send_sms_hash': true,
      'prefill': {
        'contact': getStringAsync(USER_CONTACT_NUMBER),
        'email': getStringAsync(USER_EMAIL)
      },
      'external': {
        'wallets': ['paytm']
      }
    };

    try {
      _razorpay.open(options);
    } catch (e) {
      debugPrint('Error: e');
    }
  }

  void _handlePaymentSuccess(PaymentSuccessResponse response) {
    //  Fluttertoast.showToast(msg: "SUCCESS: " + response.paymentId!, toastLength: Toast.LENGTH_SHORT);
    toast("SUCCESS: ${response.paymentId}");
    Map<String, dynamic> req = {
      'order_id': response.orderId ?? widget.orderId.toString(),
      'txn_id': response.paymentId,
      'signature': response.signature,
    };
    if (widget.isWallet == true) {
      paymentConfirm(
          paymentType: PAYMENT_TYPE_RAZORPAY,
          transactionId: response.paymentId);
    } else {
      savePaymentApiCall(
          paymentType: PAYMENT_TYPE_RAZORPAY,
          paymentStatus: 'paid',
          txnId: response.paymentId,
          transactionDetail: req);
    }
  }

  void _handlePaymentError(PaymentFailureResponse response) {
    // Fluttertoast.showToast(
    //     msg: "ERROR: " + response.code.toString() + " - " + response.message!,
    //     toastLength: Toast.LENGTH_SHORT);
    toast("ERROR : ${response.code.toString() + " - " + response.message!}");
  }

  void _handleExternalWallet(ExternalWalletResponse response) {
    // Fluttertoast.showToast(
    //     msg: "EXTERNAL_WALLET: " + response.walletName!,
    //     toastLength: Toast.LENGTH_SHORT);
    toast("EXTERNAL_WALLET: ${response.walletName!}");
  }

  /// FlutterWave Payment
  void flutterWaveCheckout() async {
    final customer = Customer(
        name: getStringAsync(NAME),
        phoneNumber: getStringAsync(USER_CONTACT_NUMBER),
        email: getStringAsync(USER_EMAIL));

    final Flutterwave flutterwave = Flutterwave(
      context: context,
      publicKey: flutterWavePublicKey.validate(),
      currency: appStore.currencyCode,
      redirectUrl: "https://www.google.com",
      txRef: DateTime.now().millisecond.toString(),
      amount: widget.totalAmount.toString(),
      customer: customer,
      paymentOptions: "card, payattitude",
      customization: Customization(title: "Test Payment"),
      isTestMode: isTestType,
    );
    final ChargeResponse response = await flutterwave.charge();
    if (response.status == 'successful') {
      Map<String, dynamic> req = {
        "txn_id": response.transactionId.toString(),
        "status": response.status.toString(),
        "reference": response.txRef.toString(),
      };
      if (widget.isWallet == true) {
        paymentConfirm(
            paymentType: PAYMENT_TYPE_FLUTTERWAVE,
            transactionId: response.transactionId);
      } else {
        savePaymentApiCall(
            paymentStatus: PAYMENT_PAID,
            txnId: response.transactionId,
            paymentType: PAYMENT_TYPE_FLUTTERWAVE,
            transactionDetail: req);
      }
      print("${response.toJson()}");
    } else {
      FlutterwaveViewUtils.showToast(context, language.transactionFailed);
    }
  }

  /// StripPayment
  void stripePay() async {
    Map<String, String> headers = {
      HttpHeaders.authorizationHeader: 'Bearer ${stripPaymentKey.validate()}',
      HttpHeaders.contentTypeHeader: 'application/x-www-form-urlencoded',
    };

    var request = http.Request('POST', Uri.parse(stripeURL));

    request.bodyFields = {
      'amount': '${(widget.totalAmount * 100).toInt()}',
      'currency': "${appStore.currencyCode}",
    };

    log(request.bodyFields);
    request.headers.addAll(headers);

    log(request);

    appStore.setLoading(true);

    await request.send().then((value) {
      appStore.setLoading(false);
      http.Response.fromStream(value).then((response) async {
        print("response => ${response.body.toString()}");
        if (response.statusCode == 200) {
          var res = StripePayModel.fromJson(jsonDecode(response.body));

          SetupPaymentSheetParameters setupPaymentSheetParameters =
              SetupPaymentSheetParameters(
            paymentIntentClientSecret: res.clientSecret.validate(),
            style: ThemeMode.light,
            appearance: PaymentSheetAppearance(
                colors: PaymentSheetAppearanceColors(
                    primary: ColorUtils.colorPrimary)),
            applePay: PaymentSheetApplePay(
                merchantCountryCode: appStore.currencyCode.toUpperCase()),
            googlePay: PaymentSheetGooglePay(
                merchantCountryCode: appStore.currencyCode.toUpperCase(),
                testEnv: true),
            merchantDisplayName: mAppName,
            customerId: getIntAsync(USER_ID).toString(),
          );

          await Stripe.instance
              .initPaymentSheet(
                  paymentSheetParameters: setupPaymentSheetParameters)
              .then((value) async {
            await Stripe.instance.presentPaymentSheet().then((value) async {
              if (widget.isWallet == true) {
                print("");
                paymentConfirm(
                    paymentType: PAYMENT_TYPE_STRIPE, transactionId: res.id);
              } else {
                savePaymentApiCall(
                    paymentType: PAYMENT_TYPE_STRIPE,
                    paymentStatus: PAYMENT_PAID,
                    txnId: res.id);
              }
            });
          }).catchError((e) {
            log("presentPaymentSheet ${e.toString()}");
          });
        }
      }).catchError((e) {
        appStore.setLoading(false);
        toast(e.toString(), print: true);
      });
    }).catchError((e) {
      appStore.setLoading(false);
      toast(e.toString(), print: true);
    });
  }

  ///PayStack Payment
  void payStackPayment(BuildContext context) async {
    print("---------${payStackPublicKey}");
    final uniqueTransRef = PayWithPayStack().generateUuidV4();
    PayWithPayStack().now(
        context: context,
        secretKey: payStackPublicKey!,
        customerEmail: getStringAsync(USER_EMAIL),
        reference: uniqueTransRef,
        currency: appStore.currencyCode,
        amount: (widget.totalAmount * 100),
        paymentChannel: ["card", "mobile_money"],
        callbackUrl: "https://google.com",
        transactionCompleted: (paymentData) {
          log("paymentData ====> ${paymentData.toJson()}");
          Map<String, dynamic> req = {
            "status": paymentData.status.toString(),
            "card": {
              "number": paymentData.authorization?.accountName ?? "",
              "cvc": paymentData.authorization?.last4 ?? "",
              "expiry_month": paymentData.authorization?.cardType ?? "",
              "expiry_year": paymentData.authorization?.bin ?? "",
            },
            "message": paymentData.message.toString(),
            "method": paymentData.channel.toString(),
            "reference": paymentData.reference.toString(),
          };
          if (paymentData.status == true) {
            if (widget.isWallet == true) {
              paymentConfirm(
                  paymentType: PAYMENT_TYPE_PAYSTACK,
                  transactionId: paymentData.reference);
            } else {
              savePaymentApiCall(
                  paymentType: PAYMENT_TYPE_PAYSTACK,
                  paymentStatus: PAYMENT_PAID,
                  transactionDetail: req,
                  txnId: paymentData.reference);
            }
          } else {
            toast(language.transactionFailed);
          }
          debugPrint(paymentData.toString());
        },
        transactionNotCompleted: (reason) {
          debugPrint("==> Transaction failed reason $reason");
          toast(language.transactionFailed);
        });
  }

  payStackUpdateStatus(String? reference, String message) {
    payStackShowMessage(message, const Duration(seconds: 7));
  }

  void payStackShowMessage(String message,
      [Duration duration = const Duration(seconds: 4)]) {
    toast(message);
    log(message);
  }

  String _getReference() {
    String platform;
    if (Platform.isIOS) {
      platform = 'iOS';
    } else {
      platform = 'Android';
    }
    return 'ChargedFrom${platform}_${DateTime.now().millisecondsSinceEpoch}';
  }

  Future<PackageInfo> getAppInformation() async {
    PackageInfo packageInfo = await PackageInfo.fromPlatform();
    return packageInfo;
  }

  /// Paypal Payment
  Future<String?> getPaypalAccessToken() async {
    String url = paypalValue['is_test'] == true
        ? 'https://api-m.sandbox.paypal.com/v1/oauth2/token'
        : 'https://api-m.paypal.com/v1/oauth2/token';
    String credentials =
        '${paypalValue['client_id']}:${paypalValue['client_secret']}';
    String encodedCredentials = base64Encode(utf8.encode(credentials));

    Map<String, String> headers = {
      'Authorization': 'Basic $encodedCredentials',
      'Content-Type': 'application/x-www-form-urlencoded',
    };

    Map<String, String> body = {
      'grant_type': 'client_credentials',
    };

    try {
      final response = await http.post(
        Uri.parse(url),
        headers: headers,
        body: body,
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        String accessToken = data['access_token'];
        return accessToken;
      } else {
        print('Failed to get token: ${response.statusCode} - ${response.body}');
        return null;
      }
    } catch (e) {
      print('Error: $e');
      return null;
    }
  }

  void payPalPayment() async {
    appStore.setLoading(true);
    var accessToken = await getPaypalAccessToken();
    String url = paypalValue['is_test'] == true
        ? 'https://api-m.sandbox.paypal.com/v2/checkout/orders'
        : 'https://api-m.paypal.com/v2/checkout/orders';
    Map<String, String> headers = {
      'Authorization': 'Bearer $accessToken',
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
    Map<String, dynamic> body = {
      'intent': 'CAPTURE',
      'purchase_units': [
        {
          'amount': {
            'currency_code': 'USD',
            'value': widget.totalAmount.toString() ?? "0.00",
          },
          'description': 'Wallet Top UP',
        }
      ],
      'application_context': {
        'return_url': 'https://www.google.com',
        'cancel_url': 'https://login.yahoo.com',
        'shipping_preference': 'NO_SHIPPING',
      }
    };
    try {
      final response = await http.post(
        Uri.parse(url),
        headers: headers,
        body: jsonEncode(body),
      );
      if (response.statusCode == 201) {
        final data = jsonDecode(response.body);
        debugPrint('-----Paypal Data----${data}----------');
        String orderId = data['id'];
        var link = paypalValue['is_test'] == true
            ? "https://www.sandbox.paypal.com/checkoutnow?token=${orderId}"
            : "https://www.paypal.com/checkoutnow?token=${orderId}";
        appStore.setLoading(false);
        WebViewScreen(
            onClick: (msg) {
              if (msg == "Success") {
                paymentConfirm(paymentType: PAYMENT_TYPE_PAYPAL,transactionId: "${orderId}");
              }
            },
            mInitialUrl: link)
            .launch(navigatorKey.currentState!.overlay!.context);
      } else {
        toast("Payment failed: Invalid token or unsupported currency.");
        appStore.setLoading(false);
        return null;
      }
    } catch (e) {
      appStore.setLoading(false);
      return null;
    }
  }

  /// PayTabs Payment
  void payTabsPayment() {
    FlutterPaytabsBridge.startCardPayment(generateConfig(), (event) {
      setState(() {
        if (event["status"] == "success") {
          var transactionDetails = event["data"];
          if (transactionDetails["isSuccess"]) {
            toast("successful transaction");
            if (widget.isWallet == true) {
              paymentConfirm(
                  paymentType: PAYMENT_TYPE_PAYTABS,
                  transactionId: transactionDetails['transactionReference']);
            } else {
              savePaymentApiCall(
                  txnId: transactionDetails['transactionReference'],
                  paymentType: PAYMENT_TYPE_PAYTABS,
                  paymentStatus: 'paid');
            }
          } else {
            toast(language.transactionFailed);
          }
          toast("successful transaction");
        } else if (event["status"] == "error") {
          print("error");
        } else if (event["status"] == "event") {
          //
        }
      });
    });
  }

  PaymentSdkConfigurationDetails generateConfig() {
    var billingDetails = payTab.BillingDetails(
        getStringAsync(NAME),
        getStringAsync(USER_EMAIL),
        getStringAsync(USER_CONTACT_NUMBER),
        getStringAsync(USER_ADDRESS),
        CountryModel.fromJson(getJSONAsync(COUNTRY_DATA)).name.validate(),
        CityModel.fromJson(getJSONAsync(CITY_DATA)).name.validate(),
        "",
        "");
    List<PaymentSdkAPms> apms = [];
    apms.add(PaymentSdkAPms.STC_PAY);
    var configuration = PaymentSdkConfigurationDetails(
        profileId: payTabsProfileId,
        serverKey: payTabsServerKey,
        clientKey: payTabsClientKey,
        cartId: widget.orderId.toString(),
        screentTitle: "Pay with Card",
        amount: widget.totalAmount.toDouble(),
        showBillingInfo: true,
        forceShippingInfo: false,
        currencyCode: appStore.currencyCode,
        merchantCountryCode: "IN",
        billingDetails: billingDetails,
        alternativePaymentMethods: apms,
        linkBillingNameWithCardHolderName: true);

    var theme = IOSThemeConfigurations();

    theme.logoImage = ic_logo_transparent;

    configuration.iOSThemeConfigurations = theme;

    return configuration;
  }

  /// PayTm Payment
  void paytmPayment() async {
    setState(() {
      loading = true;
    });

    String callBackUrl = (isTestType
            ? 'https://securegw-stage.paytm.in'
            : 'https://securegw.paytm.in') +
        '/theia/paytmCallback?ORDER_ID=' +
        widget.orderId.toString();

    var url = 'https://desolate-anchorage-29312.herokuapp.com/generateTxnToken';

    var body = json.encode({
      "mid": paytmMerchantId,
      "key_secret": paytmMerchantKey,
      "website": isTestType ? "WEBSTAGING" : "DEFAULT",
      "orderId": widget.orderId,
      "amount": widget.totalAmount.toString(),
      "callbackUrl": callBackUrl,
      "custId": getIntAsync(USER_ID).toString(),
      "testing": isTestType ? 0 : 1
    });

    try {
      final response = await http.post(
        Uri.parse(url),
        body: body,
        headers: {'Content-type': "application/json"},
      );

      String txnToken = response.body;

      var paytmResponse = PaytmPaymentsAllinonesdk().startTransaction(
          paytmMerchantId!,
          widget.orderId.toString(),
          widget.totalAmount.toString(),
          txnToken,
          callBackUrl,
          isTestType,
          false);

      paytmResponse.then((value) {
        setState(() {
          loading = false;
          if (value!['error']) {
            toast(value['errorMessage']);
          } else {
            if (value['response'] != null) {
              toast(value['response']['RESPMSG']);
              if (value['response']['STATUS'] == 'TXN_SUCCESS') {
                if (widget.isWallet == true) {
                  paymentConfirm(
                      paymentType: PAYMENT_TYPE_PAYTM,
                      transactionId: value['response']['TXNID']);
                } else {
                  savePaymentApiCall(
                      paymentType: PAYMENT_TYPE_PAYTM,
                      paymentStatus: 'paid',
                      txnId: value['response']['TXNID']);
                }
              }
            }
          }
        });
      });
    } catch (e) {
      print(e);
    }
  }

  /// My Fatoorah Payment
  Future<void> myFatoorahPayment() async {
    PaymentResponse response = await MyFatoorah.startPayment(
      context: context,
      successChild: Center(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(Icons.verified, size: 50, color: Colors.green),
            SizedBox(height: 16),
            Text(language.success,
                style: boldTextStyle(color: Colors.green, size: 24)),
          ],
        ),
      ),
      errorChild: Center(
          child: Text(language.failed,
              style: boldTextStyle(color: Colors.red, size: 24))),
      request: isTestType
          ? MyfatoorahRequest.test(
              currencyIso: Country.SaudiArabia,
              successUrl: 'https://pub.dev/packages/get',
              errorUrl: 'https://www.google.com/',
              invoiceAmount: widget.totalAmount.toDouble(),
              language: defaultLanguageCode == 'ar'
                  ? ApiLanguage.Arabic
                  : ApiLanguage.English,
              token: myFatoorahToken!,
            )
          : MyfatoorahRequest.live(
              currencyIso: Country.SaudiArabia,
              successUrl: 'https://pub.dev/packages/get',
              errorUrl: 'https://www.google.com/',
              invoiceAmount: widget.totalAmount.toDouble(),
              language: defaultLanguageCode == 'ar'
                  ? ApiLanguage.Arabic
                  : ApiLanguage.English,
              token: myFatoorahToken!,
            ),
    );
    if (response.isSuccess) {
      if (widget.isWallet == true) {
        paymentConfirm(
            paymentType: PAYMENT_TYPE_MYFATOORAH,
            transactionId: response.paymentId);
      } else {
        savePaymentApiCall(
            paymentType: PAYMENT_TYPE_MYFATOORAH,
            txnId: response.paymentId,
            paymentStatus: 'paid');
      }
    } else if (response.isError) {
      toast(language.transactionFailed);
    }
  }

  Future<void> paymentConfirm(
      {String? paymentType, String? transactionId}) async {
    Map req = {
      "user_id": getIntAsync(USER_ID),
      "type": "credit",
      "amount": widget.totalAmount,
      "transaction_type":
          "Your wallet has been upgraded via $paymentType. Transaction ID: $transactionId",
      "currency": appStore.currencyCode,
    };
    appStore.isLoading = true;
    await saveWallet(req).then((value) {
      appStore.isLoading = false;
      finish(context, true);
    }).catchError((error) {
      appStore.isLoading = false;
      finish(context);
      log(error.toString());
    });
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  @override
  Widget build(BuildContext context) {
    return CommonScaffoldComponent(
      appBarTitle: language.payment,
      body: Observer(builder: (context) {
        return Stack(
          children: [
            paymentGatewayList.isNotEmpty
                ? Stack(
                    children: [
                      SingleChildScrollView(
                        padding: .all(16),
                        child: Column(
                          crossAxisAlignment: .start,
                          children: [
                            Text(language.paymentMethod,
                                style: boldTextStyle()),
                            16.height,
                            Wrap(
                              spacing: 16,
                              runSpacing: 16,
                              children: paymentGatewayList.map((mData) {
                                return GestureDetector(
                                  child: Container(
                                    width: (context.width() - 50) * 0.5,
                                    padding: .symmetric(
                                        horizontal: 12, vertical: 16),
                                    alignment: Alignment.center,
                                    decoration: boxDecorationWithRoundedCorners(
                                      backgroundColor: context.cardColor,
                                      borderRadius:
                                          BorderRadius.circular(defaultRadius),
                                      border: Border.all(
                                          color:
                                              mData.type == selectedPaymentType
                                                  ? ColorUtils.colorPrimary
                                                  : ColorUtils.borderColor),
                                    ),
                                    child: Row(
                                      children: [
                                        commonCachedNetworkImage(
                                            '${mData.gatewayLogo}',
                                            width: 40,
                                            height: 40),
                                        12.width,
                                        Text('${mData.title}',
                                                style: primaryTextStyle(),
                                                maxLines: 2)
                                            .expand(),
                                      ],
                                    ),
                                  ),
                                  onTap: () {
                                    selectedPaymentType = mData.type;
                                    isTestType = mData.isTest == 1;
                                    setState(() {});
                                  },
                                );
                              }).toList(),
                            ),
                          ],
                        ),
                      ),
                      Align(
                        alignment: Alignment.bottomCenter,
                        child: commonButton(language.payNow, () {
                          print("-------------${selectedPaymentType}");
                          if (selectedPaymentType == PAYMENT_TYPE_STRIPE) {
                            stripePay();
                          } else if (selectedPaymentType ==
                              PAYMENT_TYPE_RAZORPAY) {
                            razorPayPayment();
                          } else if (selectedPaymentType ==
                              PAYMENT_TYPE_PAYSTACK) {
                            payStackPayment(context);
                          } else if (selectedPaymentType ==
                              PAYMENT_TYPE_FLUTTERWAVE) {
                            flutterWaveCheckout();
                          } else if (selectedPaymentType ==
                              PAYMENT_TYPE_PAYPAL) {
                            payPalPayment();
                          } else if (selectedPaymentType ==
                              PAYMENT_TYPE_PAYTABS) {
                            payTabsPayment();
                          } else if (selectedPaymentType ==
                              PAYMENT_TYPE_MERCADOPAGO) {
                            //  mercadoPagoPayment();
                          } else if (selectedPaymentType ==
                              PAYMENT_TYPE_PAYTM) {
                            paytmPayment();
                          } else if (selectedPaymentType ==
                              PAYMENT_TYPE_MYFATOORAH) {
                            myFatoorahPayment();
                          } else if (selectedPaymentType ==
                              PAYMENT_TYPE_PAYTR) {
                            Paytrscreen(
                                    totalAmount: widget.totalAmount.toDouble(),
                                    orderId: widget.orderId)
                                .launch(
                              context,
                            )
                                .then((value) {
                              Navigator.of(context).pop();
                            });
                          }
                        }, width: context.width())
                            .paddingAll(16),
                      ),
                    ],
                  )
                : !appStore.isLoading
                    ? emptyWidget()
                    : SizedBox(),
            loaderWidget().visible(appStore.isLoading),
          ],
        );
      }),
    );
  }
}
