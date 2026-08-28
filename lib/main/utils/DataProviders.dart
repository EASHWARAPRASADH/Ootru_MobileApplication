import '../../main.dart';
import '../../main/models/models.dart';
import 'Images.dart';

List<String> getUserCancelReasonList() {
  List<String> list = [];
  list.add(language.placeOrderByMistake);
  list.add(language.deliveryTimeIsTooLong);
  list.add(language.duplicateOrder);
  list.add(language.changeOfMind);
  list.add(language.changeOrder);
  list.add(language.incorrectIncompleteAddress);
  list.add(language.other);
  return list;
}

List<String> getDeliveryCancelReasonList() {
  List<String> list = [];
  list.add(language.incorrectIncompleteAddress);
  list.add(language.wrongContactInformation);
  list.add(language.damageCourier);
  list.add(language.paymentIssue);
  list.add(language.personNotAvailableOnLocation);
  list.add(language.invalidCourierPackage);
  list.add(language.courierPackageIsNotAsPerOrder);
  list.add(language.other);
  return list;
}

List<String> getDeliveryBoyBeforePickupCancelReasonList() {
  List<String> list = [];
  list.add(language.invalidPickupAddress);
  list.add(language.refusedBySender);
  list.add(language.notAsOrder);
  list.add(language.damageCourier);
  list.add(language.invalidCourierPackage);
  list.add(language.other);
  return list;
}

List<String> getDeliveryBoyAfterPickupCancelReasonList() {
  List<String> list = [];
  list.add(language.invalidDeliveryAddress);
  list.add(language.exception);
  list.add(language.refusedByRecipient);
  list.add(language.other);
  return list;
}

List<String> getReturnReasonList() {
  List<String> list = [];
  list.add(language.invalidOrder);
  list.add(language.damageCourier);
  list.add(language.sentWrongCourier);
  list.add(language.notAsOrder);
  list.add(language.other);
  return list;
}

List<WalkThroughItemModel> getWalkThroughItems() {
  List<WalkThroughItemModel> list = [];
  list.add(
      WalkThroughItemModel(image: 'assets/walk_through1.png', title: language.walkThrough1Title, subTitle: language.walkThrough1Subtitle));
  list.add(
      WalkThroughItemModel(image: 'assets/walk_through2.png', title: language.walkThrough2Title, subTitle: language.walkThrough2Subtitle));
  list.add(
      WalkThroughItemModel(image: 'assets/walk_through3.png', title: language.walkThrough3Title, subTitle: language.walkThrough3Subtitle));
  return list;
}

List<Map<String, String>> getPackagingSymbols() {
  return [
    {
      'title': language.thisWayUup,
      'image': ic_this_way_up,
      'description': language.thisWayUpDesc,
      'key': 'this_way_up',
    },
    {
      'title': language.doNotStack,
      'image': ic_do_not_stack,
      'description': language.doNotStackDesc,
      'key': 'do_not_stack',
    },
    {
      'title': language.temperatureSensitive,
      'image': ic_temperature_sensitive,
      'description': language.temperatureSensitiveDesc,
      'key': 'temperature_sensitive',
    },
    {
      'title': language.doNotHook,
      'image': ic_do_not_hook,
      'description': language.doNotStackDesc,
      'key': 'do_not_use_hooks',
    },
    {
      'title': language.explosiveMaterial,
      'image': ic_explosive_material,
      'description': language.explosiveMaterialDesc,
      'key': 'explosive_material',
    },
    {
      'title': language.hazard,
      'image': ic_hazard,
      'description': language.hazardDesc,
      'key': 'hazardous_material',
    },
    {
      'title': language.bikeDelivery,
      'image': ic_bike_delivery,
      'description': language.bikeDeliveryDesc,
      'key': 'bike_delivery',
    },
    {
      'title': language.keepDry,
      'image': ic_keep_dry,
      'description': language.keepDryDesc,
      'key': 'keep_dry',
    },
    {
      'title': language.perishable,
      'image': ic_perishable,
      'description': language.perishableDesc,
      'key': 'perishable',
    },
    {
      'title': language.recycle,
      'image': ic_recycle,
      'description': language.recycleDesc,
      'key': 'recycle',
    },
    {
      'title': language.doNotOpenWithSharpObject,
      'image': ic_do_not_open_with_sharp_object,
      'description': language.doNotOpenWithSharpObjectDesc,
      'key': 'do_not_open_with_sharp_objects',
    },
    {
      'title': language.fragile,
      'image': ic_fragile,
      'description': language.fragileDesc,
      'key': 'fragile',
    },
  ];
}
