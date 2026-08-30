import 'dart:async';
import 'dart:math' as math;

import 'package:flutter/material.dart';
import 'package:flutter_polyline_points/flutter_polyline_points.dart';
import 'package:flutter_vector_icons/flutter_vector_icons.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import '../../extensions/decorations.dart';
import '../../extensions/extension_util/context_extensions.dart';
import '../../extensions/extension_util/int_extensions.dart';
import '../../extensions/extension_util/string_extensions.dart';
import '../../extensions/extension_util/widget_extensions.dart';
import '../../extensions/text_styles.dart';
import '../../main.dart';
import '../../main/Chat/ChatScreen.dart';
import '../../main/components/CommonScaffoldComponent.dart';
import '../../main/models/LoginResponse.dart';
import '../../main/models/OrderListModel.dart';
import '../../main/network/RestApis.dart';
import '../../main/utils/Common.dart';
import '../../main/utils/Constants.dart';
import '../../main/utils/Images.dart';
import '../../main/utils/dynamic_theme.dart';

class OrderTrackingScreen extends StatefulWidget {
  static String tag = '/OrderTrackingScreen';

  final OrderData orderData;

  OrderTrackingScreen({required this.orderData});

  @override
  OrderTrackingScreenState createState() => OrderTrackingScreenState();
}

class OrderTrackingScreenState extends State<OrderTrackingScreen> {
  GoogleMapController? mapController;
  Timer? timer;

  List<Marker> markers = [];
  Set<Polyline> _polylines = Set<Polyline>();
  List<LatLng> polylineCoordinates = [];

  late PolylinePoints polylinePoints;

  LatLng? sourceLocation;
  LatLng? targetLocation;

  double cameraZoom = 14;
  double cameraTilt = 0;
  double cameraBearing = 0;

  UserData? deliveryBoyData;
  late Marker deliveryBoyMarker;
  bool isLoading = true;
  String? lastUpdatedText;

  @override
  void initState() {
    super.initState();
    init();
  }

  Future<void> init() async {
    polylinePoints = PolylinePoints();
    _initTargetLocation();
    await getDeliveryBoyDetails();
    timer = Timer.periodic(Duration(seconds: 5), (Timer t) => getDeliveryBoyDetails());
  }

  void _initTargetLocation() {
    bool isHeadingToPickup = widget.orderData.status == ORDER_ACCEPTED ||
        widget.orderData.status == ORDER_ASSIGNED ||
        widget.orderData.status == ORDER_TRANSFER;

    if (isHeadingToPickup && widget.orderData.pickupPoint != null) {
      targetLocation = LatLng(
        widget.orderData.pickupPoint!.latitude.toDouble(),
        widget.orderData.pickupPoint!.longitude.toDouble(),
      );
    } else if (widget.orderData.deliveryPoint != null) {
      targetLocation = LatLng(
        widget.orderData.deliveryPoint!.latitude.toDouble(),
        widget.orderData.deliveryPoint!.longitude.toDouble(),
      );
    }
  }

  Future<void> getDeliveryBoyDetails() async {
    int? deliveryManId = widget.orderData.deliveryManId;
    if (deliveryManId == null || deliveryManId == 0) {
      setState(() {
        isLoading = false;
      });
      return;
    }

    try {
      UserData value = await getUserDetail(deliveryManId);
      deliveryBoyData = value;

      double? lat = double.tryParse(deliveryBoyData!.latitude.toString());
      double? lng = double.tryParse(deliveryBoyData!.longitude.toString());

      if (lat != null && lng != null && lat != 0 && lng != 0) {
        sourceLocation = LatLng(lat, lng);
      } else if (targetLocation != null) {
        sourceLocation ??= targetLocation;
      }

      if (deliveryBoyData?.updatedAt != null) {
        try {
          lastUpdatedText = printDateWithoutAt("${deliveryBoyData!.updatedAt!}Z");
        } catch (_) {}
      }

      _updateMarkers();

      if (sourceLocation != null && targetLocation != null) {
        await setPolyLines(deliveryLatLng: sourceLocation!, destinationLatLng: targetLocation!);
      }

      isLoading = false;
      setState(() {});
    } catch (error) {
      print("Error fetching delivery boy details: $error");
      if (mounted) {
        setState(() {
          isLoading = false;
        });
      }
    }
  }

  void _updateMarkers() {
    markers.clear();

    bool isHeadingToPickup = widget.orderData.status == ORDER_ACCEPTED ||
        widget.orderData.status == ORDER_ASSIGNED ||
        widget.orderData.status == ORDER_TRANSFER;

    if (sourceLocation != null) {
      MarkerId boyId = MarkerId("DeliveryBoy");
      deliveryBoyMarker = Marker(
        markerId: boyId,
        position: sourceLocation!,
        infoWindow: InfoWindow(
          title: deliveryBoyData?.name.validate(value: language.lblDeliveryBoy),
          snippet: lastUpdatedText != null ? '${language.lastUpdatedAt} $lastUpdatedText' : '',
        ),
        icon: BitmapDescriptor.defaultMarkerWithHue(BitmapDescriptor.hueGreen),
      );
      markers.add(deliveryBoyMarker);
    }

    if (widget.orderData.pickupPoint != null) {
      markers.add(
        Marker(
          markerId: MarkerId('PickupPoint'),
          position: LatLng(
            widget.orderData.pickupPoint!.latitude.toDouble(),
            widget.orderData.pickupPoint!.longitude.toDouble(),
          ),
          infoWindow: InfoWindow(
            title: '${language.pickupLocation}: ${widget.orderData.pickupPoint!.address.validate()}',
          ),
          icon: BitmapDescriptor.defaultMarkerWithHue(
            isHeadingToPickup ? BitmapDescriptor.hueRed : BitmapDescriptor.hueOrange,
          ),
        ),
      );
    }

    if (widget.orderData.deliveryPoint != null) {
      markers.add(
        Marker(
          markerId: MarkerId('DeliveryPoint'),
          position: LatLng(
            widget.orderData.deliveryPoint!.latitude.toDouble(),
            widget.orderData.deliveryPoint!.longitude.toDouble(),
          ),
          infoWindow: InfoWindow(
            title: '${language.deliveryLocation}: ${widget.orderData.deliveryPoint!.address.validate()}',
          ),
          icon: BitmapDescriptor.defaultMarkerWithHue(
            !isHeadingToPickup ? BitmapDescriptor.hueRed : BitmapDescriptor.hueViolet,
          ),
        ),
      );
    }
  }

  Future<void> setPolyLines({required LatLng deliveryLatLng, required LatLng destinationLatLng}) async {
    try {
      var result = await polylinePoints.getRouteBetweenCoordinates(
        googleApiKey: googleMapAPIKey,
        request: PolylineRequest(
          origin: PointLatLng(deliveryLatLng.latitude, deliveryLatLng.longitude),
          destination: PointLatLng(destinationLatLng.latitude, destinationLatLng.longitude),
          mode: TravelMode.driving,
        ),
      );

      if (result.points.isNotEmpty) {
        polylineCoordinates.clear();
        for (var element in result.points) {
          polylineCoordinates.add(LatLng(element.latitude, element.longitude));
        }

        _polylines.clear();
        _polylines.add(
          Polyline(
            visible: true,
            width: 5,
            polylineId: PolylineId('route_polyline'),
            color: ColorUtils.colorPrimary,
            points: polylineCoordinates,
          ),
        );
      }
    } catch (e) {
      print("Polyline error: $e");
    }
  }

  void _fitBounds() {
    if (mapController == null) return;
    if (sourceLocation != null && targetLocation != null) {
      double minLat = math.min(sourceLocation!.latitude, targetLocation!.latitude);
      double maxLat = math.max(sourceLocation!.latitude, targetLocation!.latitude);
      double minLng = math.min(sourceLocation!.longitude, targetLocation!.longitude);
      double maxLng = math.max(sourceLocation!.longitude, targetLocation!.longitude);

      LatLngBounds bounds = LatLngBounds(
        southwest: LatLng(minLat, minLng),
        northeast: LatLng(maxLat, maxLng),
      );

      mapController!.animateCamera(CameraUpdate.newLatLngBounds(bounds, 70));
    } else if (sourceLocation != null) {
      mapController!.animateCamera(CameraUpdate.newLatLngZoom(sourceLocation!, 15));
    }
  }

  @override
  void dispose() {
    timer?.cancel();
    super.dispose();
  }

  @override
  void setState(fn) {
    if (mounted) super.setState(fn);
  }

  @override
  Widget build(BuildContext context) {
    bool isHeadingToPickup = widget.orderData.status == ORDER_ACCEPTED ||
        widget.orderData.status == ORDER_ASSIGNED ||
        widget.orderData.status == ORDER_TRANSFER;

    LatLng initialPos = sourceLocation ??
        targetLocation ??
        LatLng(
          widget.orderData.pickupPoint?.latitude.toDouble() ?? 0,
          widget.orderData.pickupPoint?.longitude.toDouble() ?? 0,
        );

    return CommonScaffoldComponent(
      appBarTitle: language.trackOrder,
      body: Stack(
        children: [
          initialPos.latitude != 0
              ? GoogleMap(
                  markers: markers.toSet(),
                  polylines: _polylines,
                  mapType: MapType.normal,
                  myLocationButtonEnabled: false,
                  zoomControlsEnabled: false,
                  initialCameraPosition: CameraPosition(
                    target: initialPos,
                    zoom: cameraZoom,
                    tilt: cameraTilt,
                    bearing: cameraBearing,
                  ),
                  onMapCreated: (GoogleMapController controller) {
                    mapController = controller;
                    _fitBounds();
                  },
                )
              : Center(child: loaderWidget()),

          // Re-center floating button
          Positioned(
            right: 16,
            top: 16,
            child: FloatingActionButton.small(
              backgroundColor: context.cardColor,
              child: Icon(Icons.my_location, color: ColorUtils.colorPrimary),
              onPressed: () {
                _fitBounds();
              },
            ),
          ),

          // Bottom live tracking info panel
          Positioned(
            left: 16,
            right: 16,
            bottom: 20,
            child: Container(
              padding: .all(16),
              decoration: boxDecorationWithRoundedCorners(
                borderRadius: BorderRadius.circular(16),
                backgroundColor: context.cardColor,
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.12),
                    blurRadius: 10,
                    offset: Offset(0, 4),
                  ),
                ],
              ),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Order header & status
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('${language.order} #${widget.orderData.id.validate()}', style: boldTextStyle(size: 16)),
                          4.height,
                          Text(
                            isHeadingToPickup ? language.courierWillPickupAt : language.courierWillDeliverAt,
                            style: secondaryTextStyle(size: 12),
                          ),
                        ],
                      ),
                      Container(
                        decoration: BoxDecoration(
                          color: statusColor(widget.orderData.status.validate()).withOpacity(0.12),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        padding: .symmetric(horizontal: 10, vertical: 6),
                        child: Row(
                          children: [
                            Container(
                              width: 8,
                              height: 8,
                              decoration: BoxDecoration(
                                color: statusColor(widget.orderData.status.validate()),
                                shape: BoxShape.circle,
                              ),
                            ),
                            6.width,
                            Text(
                              orderStatus(widget.orderData.status.validate()),
                              style: boldTextStyle(
                                size: 12,
                                color: statusColor(widget.orderData.status.validate()),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  12.height,
                  Divider(color: context.dividerColor, height: 1),
                  12.height,

                  // Delivery partner profile & direct contact actions
                  if (deliveryBoyData != null)
                    Row(
                      children: [
                        deliveryBoyData!.profileImage.validate().isNotEmpty
                            ? Image.network(
                                deliveryBoyData!.profileImage.validate(),
                                height: 48,
                                width: 48,
                                fit: BoxFit.cover,
                              ).cornerRadiusWithClipRRect(24)
                            : commonCachedNetworkImage(
                                ic_profile,
                                height: 48,
                                width: 48,
                                fit: BoxFit.cover,
                              ).cornerRadiusWithClipRRect(24),
                        12.width,
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              deliveryBoyData!.name.validate(value: language.lblDeliveryBoy),
                              style: boldTextStyle(size: 15),
                            ),
                            4.height,
                            Text(
                              lastUpdatedText != null
                                  ? '${language.lastUpdatedAt} $lastUpdatedText'
                                  : language.lblDeliveryBoy,
                              style: secondaryTextStyle(size: 11),
                            ),
                          ],
                        ).expand(),
                        if (deliveryBoyData!.contactNumber.validate().isNotEmpty)
                          Container(
                            decoration: boxDecorationWithRoundedCorners(
                              borderRadius: BorderRadius.circular(10),
                              backgroundColor: ColorUtils.colorPrimary.withOpacity(0.1),
                            ),
                            padding: .all(8),
                            child: Icon(Ionicons.call_outline, color: ColorUtils.colorPrimary, size: 20),
                          ).onTap(() {
                            commonLaunchUrl('tel:${deliveryBoyData!.contactNumber}');
                          }),
                        8.width,
                        Container(
                          decoration: boxDecorationWithRoundedCorners(
                            borderRadius: BorderRadius.circular(10),
                            backgroundColor: ColorUtils.colorPrimary.withOpacity(0.1),
                          ),
                          padding: .all(8),
                          child: Icon(Icons.chat_bubble_outline, color: ColorUtils.colorPrimary, size: 20),
                        ).onTap(() {
                          ChatScreen(
                            userData: deliveryBoyData,
                            orderId: widget.orderData.id.toString(),
                          ).launch(context);
                        }),
                      ],
                    )
                  else
                    Row(
                      children: [
                        Icon(Icons.directions_bike, color: ColorUtils.colorPrimary, size: 24),
                        8.width,
                        Text(language.lblDeliveryBoy, style: boldTextStyle(size: 14)).expand(),
                      ],
                    ),

                  12.height,
                  // Target destination display
                  Container(
                    padding: .all(10),
                    decoration: boxDecorationWithRoundedCorners(
                      borderRadius: BorderRadius.circular(8),
                      backgroundColor: context.scaffoldBackgroundColor,
                    ),
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        ImageIcon(
                          AssetImage(isHeadingToPickup ? ic_from : ic_to),
                          size: 20,
                          color: ColorUtils.colorPrimary,
                        ),
                        8.width,
                        Text(
                          isHeadingToPickup
                              ? widget.orderData.pickupPoint?.address.validate() ?? ''
                              : widget.orderData.deliveryPoint?.address.validate() ?? '',
                          style: primaryTextStyle(size: 13),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ).expand(),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),

          if (isLoading)
            Positioned(
              top: 16,
              left: 16,
              child: Container(
                padding: .symmetric(horizontal: 12, vertical: 6),
                decoration: boxDecorationWithRoundedCorners(
                  backgroundColor: Colors.black.withOpacity(0.7),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    SizedBox(
                      width: 12,
                      height: 12,
                      child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                    ),
                    8.width,
                    Text('Updating...', style: primaryTextStyle(size: 12, color: Colors.white)),
                  ],
                ),
              ),
            ),
        ],
      ),
    );
  }
}
