// ─────────────────────────────────────────────────────────────────────────────
// ENTRY POINT: FREELEFT RIDERS (Delivery Partner App)
// Build command: flutter build apk --flavor riders --target lib/main_riders.dart
// ─────────────────────────────────────────────────────────────────────────────
import 'config/app_flavor.dart';
import 'main.dart' as app;

void main() {
  AppFlavor.setRiderApp(); // isRiderApp = true
  app.main();
}
