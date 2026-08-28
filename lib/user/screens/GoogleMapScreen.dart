import 'package:flutter/material.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:google_maps_place_picker_mb/google_maps_place_picker.dart';
import '../../extensions/app_text_field.dart';
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
  List<Map<String, dynamic>> presetLocations = [
    {"name": "Current Location / Downtown Center", "address": "Downtown Center, Main Street", "lat": 40.7128, "lng": -74.0060},
    {"name": "Central Business District", "address": "Building 5, Business Avenue", "lat": 40.7306, "lng": -73.9352},
    {"name": "City Mall & Shopping Complex", "address": "City Mall, 2nd Floor, Park Lane", "lat": 40.7589, "lng": -73.9851},
    {"name": "Airport Terminal Area", "address": "Cargo Terminal 3, Airport Road", "lat": 40.6413, "lng": -73.7781},
    {"name": "Residential North Park", "address": "North Park Avenue, Block C", "lat": 40.7829, "lng": -73.9654},
  ];
  Map<String, dynamic>? selectedPreset;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    if (widget.isPick) {
      selectedPreset = presetLocations[0];
      addressController.text = presetLocations[0]["address"];
    } else {
      selectedPreset = presetLocations[1];
      addressController.text = presetLocations[1]["address"];
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
          : Container(
              padding: EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(language.searchAddress, style: boldTextStyle(size: 16)),
                  10.height,
                  AppTextField(
                    controller: addressController,
                    textFieldType: TextFieldType.OTHER,
                    decoration: commonInputDecoration(
                      hintText: "Type or edit location address",
                      suffixIcon: Icons.location_on,
                    ),
                    onChanged: (val) {
                      setState(() {});
                    },
                  ),
                  20.height,
                  Text("Quick Locations:", style: secondaryTextStyle(size: 14)),
                  10.height,
                  Expanded(
                    child: ListView.separated(
                      itemCount: presetLocations.length,
                      separatorBuilder: (ctx, i) => Divider(height: 1),
                      itemBuilder: (ctx, i) {
                        final item = presetLocations[i];
                        final isSelected = selectedPreset == item || addressController.text == item["address"];
                        return ListTile(
                          contentPadding: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                          leading: CircleAvatar(
                            backgroundColor: isSelected ? ColorUtils.colorPrimary : Colors.grey.withOpacity(0.2),
                            child: Icon(
                              Icons.location_pin,
                              color: isSelected ? Colors.white : Colors.grey,
                            ),
                          ),
                          title: Text(item["name"], style: boldTextStyle(size: 14)),
                          subtitle: Text(item["address"], style: secondaryTextStyle(size: 12)),
                          trailing: isSelected ? Icon(Icons.check_circle, color: ColorUtils.colorPrimary) : null,
                          onTap: () {
                            setState(() {
                              selectedPreset = item;
                              addressController.text = item["address"];
                            });
                          },
                        );
                      },
                    ),
                  ),
                  16.height,
                  commonButton(
                    buildButtonText(),
                    () {
                      String address = addressController.text.trim().isNotEmpty
                          ? addressController.text.trim()
                          : (selectedPreset != null ? selectedPreset!["address"] : "Selected Location");
                      double lat = selectedPreset != null ? selectedPreset!["lat"] : (widget.isPick ? 40.7128 : 40.7306);
                      double lng = selectedPreset != null ? selectedPreset!["lng"] : (widget.isPick ? -74.0060 : -73.9352);
                      confirmSelection(address, lat, lng);
                    },
                    width: context.width(),
                  ),
                ],
              ),
            ),
    );
  }
}
