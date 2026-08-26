import 'dart:convert';
import 'dart:io';
import 'dart:developer' as lg;

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:http/http.dart';
import 'package:mighty_delivery/extensions/extension_util/widget_extensions.dart';
import '../../extensions/extension_util/int_extensions.dart';
import '../../extensions/extension_util/string_extensions.dart';

import '../../extensions/common.dart';
import '../../extensions/shared_pref.dart';
import '../../extensions/system_utils.dart';
import '../../main.dart';
import '../../main/utils/Constants.dart';
import '../screens/LoginScreen.dart';
import 'RestApis.dart';

Map<String, String> buildHeaderTokens() {
  Map<String, String> header = {
    HttpHeaders.contentTypeHeader: 'application/json; charset=utf-8',
    HttpHeaders.cacheControlHeader: 'no-cache',
    HttpHeaders.acceptHeader: 'application/json; charset=utf-8',
    'Access-Control-Allow-Headers': '*',
    'Access-Control-Allow-Origin': '*',
  };
  if (!getStringAsync(USER_TOKEN).isEmptyOrNull) {
    header.putIfAbsent(HttpHeaders.authorizationHeader, () => 'Bearer ${getStringAsync(USER_TOKEN)}');
  }
  log(jsonEncode(header));
  return header;
}

Uri buildBaseUrl(String endPoint) {
  Uri url = Uri.parse(endPoint);
  if (!endPoint.startsWith('http')) url = Uri.parse('$mBaseUrl$endPoint');

  log('URL: ${url.toString()}');

  return url;
}

Future<Response> buildHttpResponse(String endPoint, {HttpMethod method = HttpMethod.GET, Map? request}) async {
  if (await isNetworkAvailable()) {
    var headers = buildHeaderTokens();
    Uri url = buildBaseUrl(endPoint);

    try {
      Response response;

      if (method == HttpMethod.POST) {
        log('Request: $request');

        response = await http.post(url, body: jsonEncode(request), headers: headers).timeout(300.seconds, onTimeout: () => throw 'Timeout');
      } else if (method == HttpMethod.DELETE) {
        response = await delete(url, headers: headers).timeout(600.seconds, onTimeout: () => throw 'Timeout');
      } else if (method == HttpMethod.PUT) {
        response = await put(url, body: jsonEncode(request), headers: headers).timeout(600.seconds, onTimeout: () => throw 'Timeout');
      } else {
        response = await get(url, headers: headers).timeout(600.seconds, onTimeout: () => throw 'Timeout');
      }

      log('Response ($method): ${url.toString()} ${response.statusCode} ${response.body}');
      dynamic responseLog = json.decode(response.body);
      if (responseLog is Map) {
        var encoder = const JsonEncoder.withIndent("    ");
        lg.log("\n${encoder.convert(responseLog)}", name: "$method ${url.toString()} ${response.statusCode}");
      }

      return response;
    } catch (e) {
      print("--NetworkUtils Catch----${e.toString()}");
      throw language.errorSomethingWentWrong;
    }
  } else {
    throw language.errorInternetNotAvailable;
  }
}

//region Common

Future handleResponse(Response response, [bool? avoidTokenError]) async {
  if (!await isNetworkAvailable()) {
    throw language.errorInternetNotAvailable;
  }
  if (response.statusCode == 401) {
    // if (appStore.isLoggedIn) {
    //   Map req = {
    //     'email': appStore.userEmail,
    //     'password': getStringAsync(USER_PASSWORD),
    //   };
    //   await logInApi(req).then((value) {
    //     throw '';
    //   }).catchError((e) {
    //     throw TokenException(e);
    //   });
    showDialog(
        context: navigatorKey.currentState!.context, // Ensure you have a GlobalKey<NavigatorState>()
        barrierDismissible: false, // Prevent user from dismissing dialog
        builder: (BuildContext context) {
          return AlertDialog(
            title: Text(language.sessionExpired),
            content: Text(language.sessionExpiredMsg),
            actions: [
              TextButton(
                onPressed: () {
                  Navigator.of(context).pop(); // Close the dialog
                  // Navigate to login screen or perform logout
                  LoginScreen().launch(context, isNewTask: true); // Implement your logout logic here
                },
                child: Text(language.yes),
              ),
            ],
          );
        });

    // } else {
    //   throw '';
    // }
  }

  if (response.statusCode.isSuccessful()) {
    return jsonDecode(response.body);
  } else {
    try {
      var body = jsonDecode(response.body);
      throw parseHtmlString(body['message']);
    } on Exception catch (e) {
      log(e);
      throw language.errorSomethingWentWrong;
    }
  }
}

enum HttpMethod { GET, POST, DELETE, PUT }

class TokenException implements Exception {
  final String message;

  const TokenException([this.message = ""]);

  String toString() => "FormatException: $message";
}
