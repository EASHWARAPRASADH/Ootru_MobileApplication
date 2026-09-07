// ─────────────────────────────────────────────────────────────────────────────
// ENTRY POINT: FREELEFT (Customer App)
// Build command: flutter build apk --flavor user --target lib/main_user.dart
// ─────────────────────────────────────────────────────────────────────────────
import 'config/app_flavor.dart';
import 'main.dart' as app;

void main() {
  AppFlavor.setCustomerApp(); // isRiderApp = false
  app.main();
}
