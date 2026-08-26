import 'package:flutter/foundation.dart';

class AppServerConfig {
  static String get baseUrl {
    if (kReleaseMode) {
      return 'http://192.168.31.248:8000';
    } else if (kProfileMode) {
      return 'http://192.168.31.248:8000';
    } else {
      return 'http://192.168.31.248:8000';
    }
  }
}
