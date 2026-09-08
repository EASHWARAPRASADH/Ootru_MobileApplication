import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:geolocator/geolocator.dart';
import 'package:geocoding/geocoding.dart';
import 'package:http/http.dart' as http;
import '../../extensions/app_text_field.dart';
import '../../extensions/common.dart';
import '../../extensions/decorations.dart';
import '../../extensions/extension_util/context_extensions.dart';
import '../../extensions/extension_util/int_extensions.dart';
import '../../extensions/system_utils.dart';
import '../../extensions/text_styles.dart';
import '../../main.dart';
import '../../main/components/CommonScaffoldComponent.dart';
import '../../main/models/PlaceAddressModel.dart';
import '../../main/utils/Common.dart';
import '../../main/utils/dynamic_theme.dart';
import '../../main/utils/Widgets.dart';

class GoogleMapScreen extends StatefulWidget {
  static final kInitialPosition = LatLng(13.0827, 80.2707);
  final bool isPick;
  final bool isSaveAddress;
  final bool isAddAddress;
  final double? initialLat;
  final double? initialLng;
  final String? initialAddress;

  GoogleMapScreen({
    this.isPick = true,
    this.isSaveAddress = false,
    this.isAddAddress = false,
    this.initialLat,
    this.initialLng,
    this.initialAddress,
  });

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
  Set<Marker> markers = {};

  @override
  void initState() {
    super.initState();
    initLocation();
  }

  Future<void> initLocation() async {
    // If initial lat/lng was passed from caller, use that
    if (widget.initialLat != null &&
        widget.initialLng != null &&
        widget.initialLat! != 0 &&
        widget.initialLng! != 0) {
      currentLat = widget.initialLat!;
      currentLng = widget.initialLng!;
      if (widget.initialAddress != null && widget.initialAddress!.isNotEmpty) {
        addressController.text = widget.initialAddress!;
      }
      _updateMarker(LatLng(currentLat, currentLng), animateCamera: true, zoom: 16.5);
      if (addressController.text.isEmpty) {
        await reverseGeocode(currentLat, currentLng);
      }
      isMapReady = true;
      setState(() {});
      return;
    }

    // Otherwise, fetch current GPS location
    try {
      Position position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
      ).timeout(Duration(seconds: 4));
      currentLat = position.latitude;
      currentLng = position.longitude;
      _updateMarker(LatLng(currentLat, currentLng), animateCamera: true, zoom: 16.5);
      await reverseGeocode(currentLat, currentLng);
    } catch (e) {
      log("initLocation error: $e");
      _updateMarker(LatLng(currentLat, currentLng), animateCamera: false);
      if (addressController.text.isEmpty) {
        addressController.text = widget.isPick ? "Pickup Location" : "Delivery Location";
      }
    }
    isMapReady = true;
    setState(() {});
  }

  void _updateMarker(LatLng point, {bool animateCamera = true, double? zoom}) {
    currentLat = point.latitude;
    currentLng = point.longitude;

    markers = {
      Marker(
        markerId: MarkerId('selected_pin'),
        position: point,
        draggable: true,
        icon: BitmapDescriptor.defaultMarkerWithHue(
          widget.isPick ? BitmapDescriptor.hueRed : BitmapDescriptor.hueAzure,
        ),
        infoWindow: InfoWindow(
          title: widget.isPick ? language.pickupLocation : language.deliveryLocation,
          snippet: "Drag to adjust or tap any building",
        ),
        onDragEnd: (LatLng newPos) {
          _updateMarker(newPos, animateCamera: false);
          reverseGeocode(newPos.latitude, newPos.longitude);
        },
      ),
    };
    setState(() {});

    if (animateCamera && googleMapController != null) {
      if (zoom != null) {
        googleMapController!.animateCamera(CameraUpdate.newLatLngZoom(point, zoom));
      } else {
        googleMapController!.animateCamera(CameraUpdate.newLatLng(point));
      }
    }
  }

  void onMapTapped(LatLng point) {
    _updateMarker(point, animateCamera: true);
    reverseGeocode(point.latitude, point.longitude);
  }

  Future<void> reverseGeocode(double lat, double lng) async {
    isGeocoding = true;
    setState(() {});

    String resolvedAddress = "";

    // 1. High-accuracy Nominatim reverse geocode (granular street names like Kutchery Street, Parangipettai)
    try {
      final url = Uri.parse(
        'https://nominatim.openstreetmap.org/reverse?lat=$lat&lon=$lng&format=json&addressdetails=1',
      );
      final response = await http.get(
        url,
        headers: {'User-Agent': 'FreeleftDeliveryApp/1.0'},
      ).timeout(Duration(seconds: 4));

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data is Map && data.containsKey('address')) {
          Map addr = data['address'];
          List<String> parts = [];

          // POI / Building / Amenity / Shop / Office
          String poi = addr['amenity'] ?? addr['building'] ?? addr['shop'] ?? addr['office'] ?? '';
          if (poi.isNotEmpty) parts.add(poi);

          // House Number
          String house = addr['house_number'] ?? '';
          if (house.isNotEmpty && !parts.contains(house)) parts.add(house);

          // Street / Road
          String road = addr['road'] ?? addr['pedestrian'] ?? addr['street'] ?? addr['footway'] ?? addr['residential'] ?? '';
          if (road.isNotEmpty && !parts.contains(road)) parts.add(road);

          // Suburb / Neighborhood / Locality
          String suburb = addr['suburb'] ?? addr['neighbourhood'] ?? '';
          if (suburb.isNotEmpty && !parts.contains(suburb)) parts.add(suburb);

          // Town / City / Village
          String city = addr['town'] ?? addr['city'] ?? addr['village'] ?? addr['municipality'] ?? '';
          if (city.isNotEmpty && !parts.contains(city)) parts.add(city);

          // District / County (only if city is empty or not in parts)
          String district = addr['county'] ?? addr['state_district'] ?? '';
          if (district.isNotEmpty && !parts.contains(district) && city.isEmpty) parts.add(district);

          // State
          String state = addr['state'] ?? '';
          if (state.isNotEmpty && !parts.contains(state)) parts.add(state);

          // Postcode
          String postcode = addr['postcode'] ?? '';
          if (postcode.isNotEmpty && !parts.contains(postcode)) parts.add(postcode);

          if (parts.isNotEmpty) {
            resolvedAddress = parts.join(', ');
          } else if (data['display_name'] != null && data['display_name'].toString().isNotEmpty) {
            resolvedAddress = data['display_name'];
          }
        }
      }
    } catch (e) {
      log("Nominatim reverse geocode error: $e");
    }

    // 2. Fallback: Native device placemarkFromCoordinates
    if (resolvedAddress.isEmpty) {
      try {
        List<Placemark> placemarks = await placemarkFromCoordinates(lat, lng).timeout(Duration(seconds: 4));
        if (placemarks.isNotEmpty) {
          Placemark place = placemarks.first;
          List<String> parts = [];
          if (place.name != null && place.name!.isNotEmpty && !place.name!.contains('+') && place.name != place.street) {
            parts.add(place.name!);
          }
          if (place.street != null && place.street!.isNotEmpty && !place.street!.contains('+') && !parts.contains(place.street)) {
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
          if (parts.isNotEmpty) {
            resolvedAddress = parts.join(', ');
          } else {
            resolvedAddress = place.locality ?? 'Selected Location';
          }
        }
      } catch (e) {
        log("Native reverse geocode error: $e");
      }
    }

    if (resolvedAddress.isNotEmpty) {
      addressController.text = resolvedAddress;
    } else if (addressController.text.isEmpty) {
      addressController.text = "Pinned Location (${lat.toStringAsFixed(4)}, ${lng.toStringAsFixed(4)})";
    }

    isGeocoding = false;
    setState(() {});
  }

  Future<void> searchLocation(String query) async {
    if (query.trim().isEmpty) return;
    hideKeyboard(context);
    isGeocoding = true;
    setState(() {});

    bool found = false;

    // 1. Try Nominatim search first
    try {
      final url = Uri.parse(
        'https://nominatim.openstreetmap.org/search?q=${Uri.encodeComponent(query.trim())}&format=json&addressdetails=1&limit=1',
      );
      final response = await http.get(
        url,
        headers: {'User-Agent': 'FreeleftDeliveryApp/1.0'},
      ).timeout(Duration(seconds: 5));

      if (response.statusCode == 200) {
        final list = json.decode(response.body);
        if (list is List && list.isNotEmpty) {
          final item = list.first;
          double lat = double.parse(item['lat'].toString());
          double lon = double.parse(item['lon'].toString());

          _updateMarker(LatLng(lat, lon), animateCamera: true, zoom: 17.0);
          await reverseGeocode(lat, lon);
          found = true;
        }
      }
    } catch (e) {
      log("Nominatim search error: $e");
    }

    // 2. Fallback: Native device locationFromAddress
    if (!found) {
      try {
        List<Location> locations = await locationFromAddress(query.trim()).timeout(Duration(seconds: 5));
        if (locations.isNotEmpty) {
          Location loc = locations.first;
          _updateMarker(LatLng(loc.latitude, loc.longitude), animateCamera: true, zoom: 17.0);
          await reverseGeocode(loc.latitude, loc.longitude);
          found = true;
        }
      } catch (e) {
        log("Native search error: $e");
      }
    }

    if (!found) {
      toast("Location not found. Try searching street, town or pincode.");
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
          // 1. Authentic Native Google Map with Real Markers
          GoogleMap(
            initialCameraPosition: CameraPosition(
              target: LatLng(currentLat, currentLng),
              zoom: 16.5,
            ),
            markers: markers,
            myLocationEnabled: true,
            myLocationButtonEnabled: false,
            zoomControlsEnabled: false,
            compassEnabled: true,
            mapToolbarEnabled: false,
            buildingsEnabled: true,
            onMapCreated: (GoogleMapController controller) {
              googleMapController = controller;
              _updateMarker(LatLng(currentLat, currentLng), animateCamera: false);
            },
            onTap: (LatLng point) {
              onMapTapped(point);
            },
          ),

          // 2. Top Floating Search Bar
          Positioned(
            top: 12,
            left: 16,
            right: 16,
            child: Container(
              padding: EdgeInsets.symmetric(horizontal: 12, vertical: 2),
              decoration: boxDecorationWithRoundedCorners(
                backgroundColor: context.cardColor,
                borderRadius: BorderRadius.circular(12),
                boxShadow: [
                  BoxShadow(color: Colors.black12, blurRadius: 8, spreadRadius: 1),
                ],
              ),
              child: Row(
                children: [
                  Icon(Icons.search, color: ColorUtils.colorPrimary, size: 22),
                  8.width,
                  Expanded(
                    child: TextField(
                      controller: searchController,
                      decoration: InputDecoration(
                        hintText: "Search street, area or pincode...",
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
                  IconButton(
                    icon: Icon(Icons.arrow_forward, size: 20, color: ColorUtils.colorPrimary),
                    onPressed: () => searchLocation(searchController.text),
                  ),
                ],
              ),
            ),
          ),

          // 3. Floating Instruction Banner
          Positioned(
            top: 68,
            left: 20,
            right: 20,
            child: Center(
              child: Container(
                padding: EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                decoration: BoxDecoration(
                  color: Colors.black.withOpacity(0.75),
                  borderRadius: BorderRadius.circular(20),
                  boxShadow: [
                    BoxShadow(color: Colors.black26, blurRadius: 4, offset: Offset(0, 2)),
                  ],
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Icon(Icons.touch_app, color: Colors.white, size: 15),
                    6.width,
                    Text(
                      "Tap any building on map to drop pin",
                      style: primaryTextStyle(color: Colors.white, size: 12),
                    ),
                  ],
                ),
              ),
            ),
          ),

          // 4. GPS Re-center Floating Button
          Positioned(
            right: 16,
            bottom: 245,
            child: FloatingActionButton.small(
              heroTag: 'google_map_my_location_btn',
              backgroundColor: context.cardColor,
              elevation: 4,
              onPressed: () async {
                try {
                  Position position = await Geolocator.getCurrentPosition(
                    desiredAccuracy: LocationAccuracy.high,
                  );
                  _updateMarker(LatLng(position.latitude, position.longitude), animateCamera: true, zoom: 16.5);
                  await reverseGeocode(position.latitude, position.longitude);
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
                  4.height,
                  Text(
                    "You can edit house/door/flat number below:",
                    style: secondaryTextStyle(size: 11),
                  ),
                  8.height,
                  AppTextField(
                    controller: addressController,
                    textFieldType: TextFieldType.MULTILINE,
                    maxLines: 2,
                    decoration: commonInputDecoration(
                      hintText: "e.g. 12/34, Kacheri Street, Parangipettai",
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
