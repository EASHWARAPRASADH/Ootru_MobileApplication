import 'package:flutter/foundation.dart';

class AppServerConfig {
  static String get baseUrl {
    if (kReleaseMode) {
     return 'https://aspigrow-admin.onrender.com';
    } else if (kProfileMode) {
      return 'https://aspigrow-admin.onrender.com';
    } else {
      return 'https://aspigrow-admin.onrender.com';
    }
  }
}
