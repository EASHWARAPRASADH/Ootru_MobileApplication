import 'package:flutter/material.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:google_maps_place_picker_mb/google_maps_place_picker.dart';
import '../../extensions/extension_util/widget_extensions.dart';
import '../../extensions/system_utils.dart';
import '../../main.dart';
import '../../main/components/CommonScaffoldComponent.dart';
import '../../main/models/PlaceAddressModel.dart';
import '../../main/utils/Constants.dart';

class GoogleMapScreen extends StatefulWidget {
  static final kInitialPosition = LatLng(-33.8567844, 151.213108);
  final bool isPick;
  final bool isSaveAddress;
  final bool isAddAddress;

  GoogleMapScreen(
      {this.isPick = true,
      this.isSaveAddress = false,
      this.isAddAddress = false});

  @override
  _GoogleMapScreenState createState() => _GoogleMapScreenState();
}

class _GoogleMapScreenState extends State<GoogleMapScreen>
    with WidgetsBindingObserver {
  PickResult? selectedPlace;
  bool showPlacePickerInContainer = false;
  bool showGoogleMapInContainer = false;
  GlobalKey<_GoogleMapScreenState> placePickerKey =
      GlobalKey<_GoogleMapScreenState>();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    print("--onResume called");
    if (state == AppLifecycleState.resumed) {
      setState(() {
        placePickerKey = GlobalKey<_GoogleMapScreenState>();
      });
    }
  }

  String buildTitle() {
    if (widget.isSaveAddress || widget.isAddAddress) {
      return language.selectLocation;
    } else if (widget.isPick) {
      return language.selectPickupLocation;
    } else {
      return language.selectDeliveryLocation;
    }
  }

  String buildButtonText() {
    print("buildButtonText() called ---------------------------");
    if (widget.isPick) {
      return language.confirmPickupLocation;
    } else if (widget.isAddAddress) {
      return language.addNewAddress;
    } else {
      return language.confirmDeliveryLocation;
    }
  }

  @override
  Widget build(BuildContext context) {
    return CommonScaffoldComponent(
      appBarTitle: buildTitle(),
      body: Column(
        children: [
          PlacePicker(
            key: placePickerKey,
            apiKey: googleMapAPIKey,
            hintText: language.searchAddress,
            searchingText: language.pleaseWait,
            selectText:buildButtonText(),
            outsideOfPickAreaText: language.addressNotInArea,
            initialPosition: GoogleMapScreen.kInitialPosition,
            useCurrentLocation: true,
            selectInitialPosition: true,
            usePinPointingSearch: true,
            usePlaceDetailSearch: true,
            zoomGesturesEnabled: true,
            zoomControlsEnabled: true,
            automaticallyImplyAppBarLeading: false,
            autocompleteLanguage: appStore.selectedLanguage,
            onMapCreated: (GoogleMapController controller) {
              //
            },
            // resizeToAvoidBottomInset: false,
            onPlacePicked: (PickResult result) {
              setState(() {
                selectedPlace = result;
                PlaceAddressModel selectedModel = PlaceAddressModel(
                  placeId: selectedPlace!.placeId!,
                  latitude: selectedPlace!.geometry!.location.lat,
                  longitude: selectedPlace!.geometry!.location.lng,
                  placeAddress: selectedPlace!.formattedAddress,
                );
                print("===============KK${selectedModel.toJson().toString()}");
                finish(context, selectedModel);
              });
            },
            onMapTypeChanged: (MapType mapType) {
              //
            },
          ).expand(),
        ],
      ),
    );
  }
}
