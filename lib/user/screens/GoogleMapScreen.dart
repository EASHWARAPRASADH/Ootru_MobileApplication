import 'package:flutter/material.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:google_maps_place_picker_mb/google_maps_place_picker.dart';
import 'package:flutter_map/flutter_map.dart' as fmap;
import 'package:latlong2/latlong.dart' as osm;
import 'package:geolocator/geolocator.dart';
import 'package:geocoding/geocoding.dart';
import '../../extensions/app_text_field.dart';
import '../../extensions/common.dart';
import '../../extensions/decorations.dart';
import '../../extensions/extension_util/context_extensions.dart';
import '../../extensions/extension_util/int_extensions.dart';
import '../../extensions/extension_util/widget_extensions.dart';
import '../../extensions/system_utils.dart';
import '../../extensions/text_styles.dart';
import '../../main.dart';
import '../../main/components/CommonScaffoldComponent.dart';
import '../../main/models/PlaceAddressModel.dart';
import '../../main/utils/Constants.dart';
import '../../main/utils/Common.dart';
import '../../main/utils/dynamic_theme.dart';
import '../../main/utils/Widgets.dart';

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

  TextEditingController addressController = TextEditingController();
  final fmap.MapController mapController = fmap.MapController();
  double currentLat = 13.0827;
  double currentLng = 80.2707;
  bool isMapReady = false;
  bool isGeocoding = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    initLocation();
  }

  Future<void> initLocation() async {
    try {
      Position position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
      ).timeout(Duration(seconds: 4));
      currentLat = position.latitude;
      currentLng = position.longitude;
      await reverseGeocode(currentLat, currentLng);
    } catch (e) {
      log("initLocation error: $e");
      if (addressController.text.isEmpty) {
        addressController.text = widget.isPick ? "Pickup Location" : "Delivery Location";
      }
    }
    isMapReady = true;
    setState(() {});
  }

  Future<void> reverseGeocode(double lat, double lng) async {
    try {
      isGeocoding = true;
      setState(() {});
      List<Placemark> placemarks = await placemarkFromCoordinates(lat, lng);
      if (placemarks.isNotEmpty) {
        Placemark place = placemarks.first;
        List<String> parts = [];
        if (place.street != null && place.street!.isNotEmpty && !place.street!.contains('+')) parts.add(place.street!);
        if (place.subLocality != null && place.subLocality!.isNotEmpty && !parts.contains(place.subLocality)) parts.add(place.subLocality!);
        if (place.locality != null && place.locality!.isNotEmpty && !parts.contains(place.locality)) parts.add(place.locality!);
        if (place.administrativeArea != null && place.administrativeArea!.isNotEmpty && !parts.contains(place.administrativeArea)) parts.add(place.administrativeArea!);
        if (place.postalCode != null && place.postalCode!.isNotEmpty) parts.add(place.postalCode!);
        addressController.text = parts.isNotEmpty ? parts.join(', ') : "${place.locality ?? 'Selected Location'}";
      }
    } catch (e) {
      log("reverseGeocode error: $e");
      if (addressController.text.isEmpty) {
        addressController.text = "Pinned Location (${lat.toStringAsFixed(4)}, ${lng.toStringAsFixed(4)})";
      }
    } finally {
      isGeocoding = false;
      setState(() {});
    }
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
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
    if (widget.isPick) {
      return language.confirmPickupLocation;
    } else if (widget.isAddAddress) {
      return language.addNewAddress;
    } else {
      return language.confirmDeliveryLocation;
    }
  }

  void confirmSelection(String address, double lat, double lng) {
    PlaceAddressModel selectedModel = PlaceAddressModel(
      placeId: 'loc_${DateTime.now().millisecondsSinceEpoch}',
      latitude: lat,
      longitude: lng,
      placeAddress: address,
    );
    finish(context, selectedModel);
  }

  @override
  Widget build(BuildContext context) {
    bool hasValidGoogleMapsKey = googleMapAPIKey.isNotEmpty && googleMapAPIKey != 'GOOGLE_MAPS_API_KEY';

    return CommonScaffoldComponent(
      appBarTitle: buildTitle(),
      body: hasValidGoogleMapsKey
          ? Column(
              children: [
                PlacePicker(
                  key: placePickerKey,
                  apiKey: googleMapAPIKey,
                  hintText: language.searchAddress,
                  searchingText: language.pleaseWait,
                  selectText: buildButtonText(),
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
                  onMapCreated: (GoogleMapController controller) {},
                  onPlacePicked: (PickResult result) {
                    setState(() {
                      selectedPlace = result;
                      PlaceAddressModel selectedModel = PlaceAddressModel(
                        placeId: selectedPlace!.placeId!,
                        latitude: selectedPlace!.geometry!.location.lat,
                        longitude: selectedPlace!.geometry!.location.lng,
                        placeAddress: selectedPlace!.formattedAddress,
                      );
                      finish(context, selectedModel);
                    });
                  },
                ).expand(),
              ],
            )
          : Stack(
              children: [
                // OpenStreetMap Interactive Map
                fmap.FlutterMap(
                  mapController: mapController,
                  options: fmap.MapOptions(
                    initialCenter: osm.LatLng(currentLat, currentLng),
                    initialZoom: 15.0,
                    onTap: (tapPosition, point) {
                      currentLat = point.latitude;
                      currentLng = point.longitude;
                      reverseGeocode(point.latitude, point.longitude);
                    },
                  ),
                  children: [
                    fmap.TileLayer(
                      urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                      userAgentPackageName: 'com.mighty.delivery',
                    ),
                    fmap.MarkerLayer(
                      markers: [
                        fmap.Marker(
                          point: osm.LatLng(currentLat, currentLng),
                          width: 48,
                          height: 48,
                          child: Icon(
                            Icons.location_pin,
                            color: ColorUtils.colorPrimary,
                            size: 48,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),

                // Top Hint Banner
                Positioned(
                  top: 12,
                  left: 16,
                  right: 16,
                  child: Container(
                    padding: EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                    decoration: boxDecorationWithRoundedCorners(
                      backgroundColor: Colors.black.withOpacity(0.75),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(Icons.touch_app, color: Colors.white, size: 16),
                        8.width,
                        Text(
                          "Tap anywhere on map to pin location",
                          style: primaryTextStyle(color: Colors.white, size: 12),
                        ),
                      ],
                    ),
                  ),
                ),

                // GPS Current Location Re-center Button
                Positioned(
                  right: 16,
                  bottom: 220,
                  child: FloatingActionButton.small(
                    heroTag: 'osm_my_location_btn',
                    backgroundColor: ColorUtils.colorPrimary,
                    onPressed: () async {
                      try {
                        Position position = await Geolocator.getCurrentPosition(
                          desiredAccuracy: LocationAccuracy.high,
                        );
                        currentLat = position.latitude;
                        currentLng = position.longitude;
                        mapController.move(osm.LatLng(currentLat, currentLng), 16.0);
                        reverseGeocode(currentLat, currentLng);
                      } catch (e) {
                        toast("Could not get current location");
                      }
                    },
                    child: Icon(Icons.my_location, color: Colors.white),
                  ),
                ),

                // Bottom Sheet Card with Address and Confirm Button
                Positioned(
                  left: 0,
                  right: 0,
                  bottom: 0,
                  child: Container(
                    padding: EdgeInsets.all(16),
                    decoration: boxDecorationWithRoundedCorners(
                      backgroundColor: context.cardColor,
                      borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
                      boxShadow: [
                        BoxShadow(color: Colors.black12, blurRadius: 10, spreadRadius: 2),
                      ],
                    ),
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Icon(Icons.location_on, color: ColorUtils.colorPrimary, size: 20),
                            8.width,
                            Text(buildTitle(), style: boldTextStyle(size: 15)),
                            Spacer(),
                            if (isGeocoding)
                              SizedBox(
                                width: 16,
                                height: 16,
                                child: CircularProgressIndicator(strokeWidth: 2),
                              ),
                          ],
                        ),
                        10.height,
                        AppTextField(
                          controller: addressController,
                          textFieldType: TextFieldType.MULTILINE,
                          maxLines: 2,
                          decoration: commonInputDecoration(
                            hintText: "Enter or edit location address",
                            suffixIcon: Icons.edit_location_alt_outlined,
                          ),
                          onChanged: (val) {
                            setState(() {});
                          },
                        ),
                        14.height,
                        commonButton(
                          buildButtonText(),
                          () {
                            confirmSelection(
                              addressController.text,
                              currentLat,
                              currentLng,
                            );
                          },
                          width: context.width(),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),
    );
  }
}
