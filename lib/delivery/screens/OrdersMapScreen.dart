import 'package:flutter/material.dart';
import 'package:flutter_polyline_points/flutter_polyline_points.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import '../../extensions/extension_util/context_extensions.dart';
import '../../extensions/extension_util/int_extensions.dart';
import '../../extensions/extension_util/string_extensions.dart';
import '../../extensions/extension_util/widget_extensions.dart';
import '../../extensions/text_styles.dart';
import '../../main.dart';
import '../../main/utils/Constants.dart';
import '../../main/utils/Images.dart';
import '../../main/utils/Widgets.dart';
import '../../main/utils/dynamic_theme.dart';
import '../../user/screens/OrderDetailScreen.dart';

import '../../extensions/colors.dart';
import '../../main/network/RestApis.dart';
import '../../main/utils/Common.dart';

class OrdersMapScreen extends StatefulWidget {
  const OrdersMapScreen({super.key});

  @override
  State<OrdersMapScreen> createState() => _OrdersMapScreenState();
}

class _OrdersMapScreenState extends State<OrdersMapScreen> {
  List<Marker> markers = [];
  GoogleMapController? googleMapController;
  Set<Polyline> _polylines = {};
  List<LatLng> polylineCoordinates = [];
  PolylinePoints polylinePoints = PolylinePoints();
  List<LatLng> assignedOrders = [];
  List<LatLng> acceptedOrders = [];
  BitmapDescriptor? assignedMarkerIcon;
  BitmapDescriptor? acceptedMarkerIcon;
  LatLng? _selectedMarkerPosition;
  bool _isInfoWindowVisible = false;
  Offset? infoWindowOffset;
  InfoWindow? selectedInfoWindow;
  List<InfoWindow> infoWindowItems = [];
  // LatLng? _center;
  void onMapCreated(GoogleMapController controller) async {
    setState(() {
      googleMapController = controller;
      // setPolylines().then((_) => setMapFitToCenter(_polylines));
    });
  }

  @override
  void initState() {
    super.initState();
    setMarkerIcons();
    getLatLngOfOrdersApi();
  }

  setMarkerIcons() async {
    assignedMarkerIcon = await createMarkerIconFromAsset(ic_assigned_marker);
    acceptedMarkerIcon = await createMarkerIconFromAsset(ic_accepted_marker);
  }

  getLatLngOfOrdersApi() async {
    appStore.setLoading(true);
    await getLatLngOfOrders().then((value) {
      print("----------------------${value}");
      markers.clear();
      infoWindowItems.clear();
      value.data!.forEach((element) {
        if (element.status == ORDER_ASSIGNED) {
          InfoWindow item = new InfoWindow(
              id: element.id.toString(),
              startTime: element.pickupPoint!.startTime,
              endTime: element.pickupPoint!.endTime,
              status: element.status,
              address: element.pickupPoint!.address,
              title: language.pendingPickup);

          markers.add(
            Marker(
              markerId: MarkerId(element.id.toString()),
              position: LatLng(element.pickupPoint!.latitude.toDouble(), element.pickupPoint!.longitude.toDouble()),
              icon: assignedMarkerIcon!,
              onTap: () => _onMarkerTapped(
                  position: LatLng(element.pickupPoint!.latitude.toDouble(), element.pickupPoint!.longitude.toDouble()), id: element.id!),
            ),
          );
          infoWindowItems.add(item);
          // assignedOrders
          //     .add(LatLng(element.pickupPoint!.latitude!.toDouble(), element.pickupPoint!.longitude!.toDouble()));
        } else if (element.status == ORDER_ACCEPTED ||
            element.status == ORDER_PICKED_UP ||
            element.status == ORDER_ARRIVED ||
            element.status == ORDER_DEPARTED) {
          InfoWindow item = new InfoWindow(
              id: element.id.toString(),
              startTime: element.deliveryPoint!.startTime,
              endTime: element.deliveryPoint!.endTime,
              status: element.status,
              address: element.deliveryPoint!.address,
              title: language.pendingDelivery);

          markers.add(
            Marker(
              markerId: MarkerId(element.id.toString()),
              position: LatLng(element.deliveryPoint!.latitude.toDouble(), element.deliveryPoint!.longitude.toDouble()),
              onTap: () => _onMarkerTapped(
                position: LatLng(
                  element.deliveryPoint!.latitude.toDouble(),
                  element.deliveryPoint!.longitude.toDouble(),
                ),
                id: element.id!,
              ),
              icon: acceptedMarkerIcon!,
            ),
          );
          infoWindowItems.add(item);
          // acceptedOrders
          //     .add(LatLng(element.deliveryPoint!.latitude!.toDouble(), element.deliveryPoint!.longitude!.toDouble()));
        }
      });
      appStore.setLoading(false);
      setState(() {});
    }).catchError((error) {
      print("-----------------${error.toString()}");
    });
  }

  void _onMarkerTapped({required LatLng position, required int id}) async {
    final screenCoordinate = await googleMapController!.getScreenCoordinate(position);
    final RenderBox mapBox = context.findRenderObject() as RenderBox;
    final Offset mapPosition = mapBox.localToGlobal(Offset.zero);

    setState(() {
      _selectedMarkerPosition = position;
      selectedInfoWindow = infoWindowItems.firstWhere((infoWindow) => infoWindow.id == id.toString());
      _isInfoWindowVisible = true;
      infoWindowOffset = Offset(
        screenCoordinate.x.toDouble() - mapPosition.dx,
        screenCoordinate.y.toDouble() - mapPosition.dy - 100,
      );
    });
  }

  void _onMapTapped(LatLng position) {
    setState(() {
      _isInfoWindowVisible = false;
      _selectedMarkerPosition = null; // Close the currently open InfoWindow
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      //
      appBar: commonAppBarWidget(language.trackOrder),
      body: Stack(
        children: [
          Column(
            mainAxisAlignment: .center,
            children: [
              markers.isNotEmpty
                  ? GoogleMap(
                      markers: markers.map((e) => e).toSet(),
                      polylines: _polylines,
                      mapType: MapType.normal,
                      cameraTargetBounds: CameraTargetBounds.unbounded,
                      initialCameraPosition: CameraPosition(
                        target: markers.first.position,
                        zoom: 12.0,
                      ),
                      onMapCreated: onMapCreated,
                      onTap: _onMapTapped,
                      tiltGesturesEnabled: true,
                      scrollGesturesEnabled: true,
                      zoomGesturesEnabled: true,
                      // trafficEnabled: true,
                    ).expand()
                  : !appStore.isLoading
                      ? Center(child: emptyWidget())
                      : SizedBox(),
            ],
          ),
          if (appStore.isLoading && !markers.isNotEmpty) Center(child: loaderWidget()),
          if (_isInfoWindowVisible && _selectedMarkerPosition != null)
            Positioned(
              left: MediaQuery.of(context).size.width / 2 - 75,
              top: MediaQuery.of(context).size.height / 2 - 100,
              child: _isInfoWindowVisible && _selectedMarkerPosition != null
                  ? _customInfoWindow()
                  : Container(
                      width: 100,
                      height: 100,
                      color: Colors.red,
                    ),
            ),
        ],
      ),
    );
  }

  Widget _customInfoWindow() {
    return Container(
      width: context.width() * 0.5,
      padding: .all(8.0),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(8.0),
        boxShadow: [
          BoxShadow(
            color: Colors.black26,
            offset: Offset(0, 2),
            blurRadius: 6.0,
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: .start,
        children: [
          Row(
            mainAxisAlignment: .spaceBetween,
            children: [
              Container(
                decoration: BoxDecoration(
                    color: statusColor(selectedInfoWindow!.status.validate()).withOpacity(0.08), borderRadius: BorderRadius.circular(6)),
                padding: .symmetric(horizontal: 8, vertical: 6),
                child: Text(orderStatus(selectedInfoWindow!.status!),
                    style: primaryTextStyle(size: 14, color: statusColor(selectedInfoWindow!.status.validate()))),
              ),
              5.width,
              Container(
                decoration: BoxDecoration(
                    color: statusColor(selectedInfoWindow!.title.validate()).withOpacity(0.08), borderRadius: BorderRadius.circular(6)),
                padding: .symmetric(horizontal: 8, vertical: 6),
                child: Text(
                  selectedInfoWindow!.title!,
                  style: primaryTextStyle(size: 14),
                  overflow: TextOverflow.ellipsis,
                  maxLines: 1,
                ),
              ).expand(),
            ],
          ),
          SizedBox(height: 8.0),
          Row(
            mainAxisAlignment: .spaceBetween,
            children: [
              Text(
                '#${selectedInfoWindow!.id.toString()}',
                style: boldTextStyle(),
              ),
              Container(
                decoration: BoxDecoration(color: ColorUtils.colorPrimary, borderRadius: BorderRadius.circular(6)),
                padding: .symmetric(horizontal: 6, vertical: 4),
                child: Text(language.view, style: primaryTextStyle(size: 14, color: white)).onTap(() {
                  OrderDetailScreen(
                    orderId: selectedInfoWindow!.id.toInt(),
                  ).launch(context);
                }),
              ),
            ],
          ),
          SizedBox(height: 8.0),
          Text(selectedInfoWindow!.address.toString()),
        ],
      ),
    );
  }
}

class InfoWindow {
  final String? id;
  final String? startTime;
  final String? endTime;
  final String? status;
  final String? title;
  final String? address;

  InfoWindow(
      {required this.id, required this.startTime, required this.endTime, required this.status, required this.title, required this.address});
}
