import 'package:flutter/foundation.dart';

class AppServerConfig {
  static String get baseUrl {
    if (kReleaseMode) {
     return 'https://app.meetmighty.com/delivery-admin';
    } else if (kProfileMode) {
      return 'https://app.meetmighty.com/delivery-admin-dev';
    } else {
      return 'https://app.meetmighty.com/delivery-admin-dev';
    }
  }
}
