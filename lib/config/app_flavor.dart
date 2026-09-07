// ─────────────────────────────────────────────────────────────────────────────
// APP FLAVOR CONFIGURATION
// ─────────────────────────────────────────────────────────────────────────────
// This file is imported by both main_user.dart and main_riders.dart.
// The entry-point file (main_user.dart / main_riders.dart) sets this flag
// before calling runApp(), so you should NOT edit this file directly.
//
// To understand the current build target, check which entry point is being used:
//   • lib/main_user.dart  → isRiderApp = false → Freeleft (Customer)
//   • lib/main_riders.dart → isRiderApp = true  → Freeleft Riders (Delivery)
// ─────────────────────────────────────────────────────────────────────────────

/// True when building the Freeleft Riders (Delivery Partner) APK.
/// False when building the Freeleft (Customer) APK.
///
/// This is set by the entry-point file (main_user.dart / main_riders.dart)
/// via the [AppFlavor] class below and should never be set directly.
bool isRiderApp = false;

class AppFlavor {
  static void setCustomerApp() {
    isRiderApp = false;
  }

  static void setRiderApp() {
    isRiderApp = true;
  }
}
