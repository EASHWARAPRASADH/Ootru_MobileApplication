import 'package:flutter/material.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
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
  static final kInitialPosition = LatLng(13.0827, 80.2707);
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

class _GoogleMapScreenState extends State<GoogleMapScreen> {
  GoogleMapController? googleMapController;
  TextEditingController addressController = TextEditingController();
  TextEditingController searchController = TextEditingController();
  double currentLat = 13.0827;
  double currentLng = 80.2707;
  bool isMapReady = false;
  bool isGeocoding = false;

  @override
  void initState() {
    super.initState();
    initLocation();
  }

  Future<void> initLocation() async {
    try {
      Position position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
      ).timeout(Duration(seconds: 4));
      currentLat = position.latitude;
      currentLng = position.longitude;
      googleMapController?.animateCamera(
        CameraUpdate.newLatLngZoom(LatLng(currentLat, currentLng), 16.0),
      );
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
        if (place.name != null && place.name!.isNotEmpty && !place.name!.contains('+') && place.name != place.street) {
          parts.add(place.name!);
        }
        if (place.street != null && place.street!.isNotEmpty && !place.street!.contains('+')) {
          parts.add(place.street!);
        }
        if (place.subLocality != null && place.subLocality!.isNotEmpty && !parts.contains(place.subLocality)) {
          parts.add(place.subLocality!);
        }
        if (place.locality != null && place.locality!.isNotEmpty && !parts.contains(place.locality)) {
          parts.add(place.locality!);
        }
        if (place.administrativeArea != null && place.administrativeArea!.isNotEmpty && !parts.contains(place.administrativeArea)) {
          parts.add(place.administrativeArea!);
        }
        if (place.postalCode != null && place.postalCode!.isNotEmpty) {
          parts.add(place.postalCode!);
        }
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

  Future<void> searchLocation(String query) async {
    if (query.trim().isEmpty) return;
    try {
      hideKeyboard(context);
      isGeocoding = true;
      setState(() {});
      List<Location> locations = await locationFromAddress(query);
      if (locations.isNotEmpty) {
        Location loc = locations.first;
        currentLat = loc.latitude;
        currentLng = loc.longitude;
        googleMapController?.animateCamera(
          CameraUpdate.newLatLngZoom(LatLng(currentLat, currentLng), 16.0),
        );
        await reverseGeocode(currentLat, currentLng);
      } else {
        toast("Location not found");
      }
    } catch (e) {
      toast("Location not found");
    } finally {
      isGeocoding = false;
      setState(() {});
    }
  }

  @override
  void dispose() {
    searchController.dispose();
    addressController.dispose();
    googleMapController?.dispose();
    super.dispose();
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
    return CommonScaffoldComponent(
      appBarTitle: buildTitle(),
      body: Stack(
        children: [
          // 1. Authentic Native Google Map
          GoogleMap(
            initialCameraPosition: CameraPosition(
              target: LatLng(currentLat, currentLng),
              zoom: 16.0,
            ),
            myLocationEnabled: true,
            myLocationButtonEnabled: false,
            zoomControlsEnabled: false,
            compassEnabled: true,
            mapToolbarEnabled: false,
            onMapCreated: (GoogleMapController controller) {
              googleMapController = controller;
            },
            onTap: (LatLng point) {
              currentLat = point.latitude;
              currentLng = point.longitude;
              googleMapController?.animateCamera(CameraUpdate.newLatLng(point));
              reverseGeocode(point.latitude, point.longitude);
            },
            onCameraMove: (CameraPosition position) {
              currentLat = position.target.latitude;
              currentLng = position.target.longitude;
            },
            onCameraIdle: () {
              reverseGeocode(currentLat, currentLng);
            },
          ),

          // 2. Interactive Center Pin
          Center(
            child: Padding(
              padding: EdgeInsets.only(bottom: 42),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Container(
                    padding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: Colors.black.withOpacity(0.8),
                      borderRadius: BorderRadius.circular(12),
                      boxShadow: [
                        BoxShadow(color: Colors.black26, blurRadius: 4, offset: Offset(0, 2)),
                      ],
                    ),
                    child: Text(
                      widget.isPick ? language.pickupLocation : language.deliveryLocation,
                      style: primaryTextStyle(color: Colors.white, size: 11),
                    ),
                  ),
                  4.height,
                  Icon(
                    Icons.location_pin,
                    color: ColorUtils.colorPrimary,
                    size: 46,
                  ),
                ],
              ),
            ),
          ),

          // 3. Top Floating Search Bar
          Positioned(
            top: 12,
            left: 16,
            right: 16,
            child: Container(
              padding: EdgeInsets.symmetric(horizontal: 12),
              decoration: boxDecorationWithRoundedCorners(
                backgroundColor: context.cardColor,
                borderRadius: BorderRadius.circular(12),
                boxShadow: [
                  BoxShadow(color: Colors.black12, blurRadius: 8, spreadRadius: 1),
                ],
              ),
              child: Row(
                children: [
                  Icon(Icons.search, color: ColorUtils.colorPrimary, size: 20),
                  8.width,
                  Expanded(
                    child: TextField(
                      controller: searchController,
                      decoration: InputDecoration(
                        hintText: language.searchAddress,
                        hintStyle: secondaryTextStyle(),
                        border: InputBorder.none,
                      ),
                      textInputAction: TextInputAction.search,
                      onSubmitted: (query) => searchLocation(query),
                    ),
                  ),
                  if (searchController.text.isNotEmpty)
                    IconButton(
                      icon: Icon(Icons.clear, size: 18, color: Colors.grey),
                      onPressed: () {
                        searchController.clear();
                        setState(() {});
                      },
                    ),
                ],
              ),
            ),
          ),

          // 4. GPS Re-center Floating Button
          Positioned(
            right: 16,
            bottom: 230,
            child: FloatingActionButton.small(
              heroTag: 'google_map_my_location_btn',
              backgroundColor: context.cardColor,
              elevation: 4,
              onPressed: () async {
                try {
                  Position position = await Geolocator.getCurrentPosition(
                    desiredAccuracy: LocationAccuracy.high,
                  );
                  currentLat = position.latitude;
                  currentLng = position.longitude;
                  googleMapController?.animateCamera(
                    CameraUpdate.newLatLngZoom(LatLng(currentLat, currentLng), 16.0),
                  );
                  reverseGeocode(currentLat, currentLng);
                } catch (e) {
                  toast("Could not get current location");
                }
              },
              child: Icon(Icons.my_location, color: ColorUtils.colorPrimary),
            ),
          ),

          // 5. Bottom Sheet Card with Address and Confirm Button
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
                        addressController.text.isNotEmpty
                            ? addressController.text
                            : "Pinned Location (${currentLat.toStringAsFixed(4)}, ${currentLng.toStringAsFixed(4)})",
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
