import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter_ringtone_player/flutter_ringtone_player.dart';
import 'package:mighty_delivery/extensions/extension_util/int_extensions.dart';
import 'package:mighty_delivery/extensions/extension_util/widget_extensions.dart';
import 'package:mighty_delivery/main.dart';
import 'package:mighty_delivery/main/models/emergency_alert_Response.dart';
import 'package:mighty_delivery/main/utils/dynamic_theme.dart';
import '../../extensions/common.dart';
import '../../extensions/system_utils.dart';
import '../../extensions/text_styles.dart';
import '../../main/components/CommonScaffoldComponent.dart';
import '../../main/network/RestApis.dart';
import '../../main/utils/Common.dart';
import '../fragment/DHomeFragment.dart';

class EmergencyAlertScreen extends StatefulWidget {
  const EmergencyAlertScreen({Key? key}) : super(key: key);

  @override
  State<EmergencyAlertScreen> createState() => _EmergencyAlertScreenState();
}

class _EmergencyAlertScreenState extends State<EmergencyAlertScreen> {
  String selectedValue = "Road accidents";
  bool _showCancelButton = false;
  bool _isLoading = false;
  bool isEmergencyReasonSelected = false;
  int _secondsLeft = 5;
  Timer? _timer;
  AlertMessageResponse? emergencyResponseModel;
  int? id;
  bool showCancelText = false;
  int _cancelSecondsLeft = 5;
  int _start = 5;
  final List<String> emergencyReasons = [
    "Road accidents",
    "Harassment or physical threats",
    "Robbery or theft",
    "Any other life-threatening situation",
  ];

  @override
  void initState() {
    super.initState();
    startFallbackTimer();
    //startTimer();
  }

  void startTimer() {
    _timer = Timer.periodic(Duration(seconds: 1), (timer) {
      if (_start == 1) {
        timer.cancel();
      }
      setState(() {
        _start--;
      });
    });
  }

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }

  double get progress => _secondsLeft / 5;
  double get progressCancel => _cancelSecondsLeft / 5;
  void startFallbackTimer() {
    FlutterRingtonePlayer().play(
      fromAsset: "assets/ringtone/emergency.mp3",
      looping: true,
      volume: 0.9
    );

    _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      setState(() {
        _secondsLeft--;
      });

      if (_secondsLeft <= 0) {
        _timer?.cancel();
        _showCancelButton = true;
        selectedValue = emergencyReasons.first;
        _sendEmergencyAlert().then((value) {
          Timer.periodic(const Duration(seconds: 1), (t) {
            setState(() {
              showCancelText = true;
              _cancelSecondsLeft--;
            });
            if (_cancelSecondsLeft <= 0) {
              t.cancel();
              FlutterRingtonePlayer().stop();
              setState(() {
                _showCancelButton = false;
                showCancelText = false;
              });
              DHomeFragment().launch(context, isNewTask: true);
            }
          });
        });
      }
    });
  }

  Future<void> _sendEmergencyAlert() async {
    setState(() => _isLoading = true);
    await callEmergencyAlertApi();
    setState(() => _isLoading = false);
  }

  Future<void> callEmergencyAlertApi() async {
    Map<String, dynamic> request = {
      "emrgency_reason": selectedValue,
    };
    try {
      emergencyResponseModel = await emergancySave(request);
      if (emergencyResponseModel!.status == true) {
        toast(emergencyResponseModel!.message);
        if (isEmergencyReasonSelected) {
          DHomeFragment().launch(context, isNewTask: true);
        }
        id = emergencyResponseModel!.id;
        Timer(const Duration(seconds: 5), () {
          setState(() {
            _showCancelButton = false;
          });
        });
      } else {
        toast(emergencyResponseModel!.message);
      }
    } catch (e, stack) {
      toast("Error: $e");
    }
  }

  Future<void> emergancyResolvedApiCall() async {
    await Future.delayed(Duration(seconds: 1));

    Map<String, dynamic> request = {
      "id": emergencyResponseModel!.id,
      "emergency_resolved": "Emergency Resolved",
    };

    try {
      final value =
          await emergancyResolved(request, emergencyResponseModel!.id!);
      toast(value.message); // Make sure this doesn't use Navigator
    } catch (e) {
      toast("Error: $e");
    }
  }

  @override
  Widget build(BuildContext context) {
    return CommonScaffoldComponent(
      appBarTitle: 'Alert',
      body: SafeArea(
        child: Container(
          padding: const .all(16),
          width: double.infinity,
          child: Column(
            mainAxisAlignment: .start,
            crossAxisAlignment: .center,
            children: [
              20.height,
              if (_showCancelButton == false)
                Text(language.emergencyAlert,
                    style: boldTextStyle(
                        color: ColorUtils.colorPrimary, size: 24)),
              if (showCancelText && _cancelSecondsLeft > 0)
                Text("${language.cancel} ${language.emergencyAlert}",
                    style: boldTextStyle(
                        color: ColorUtils.colorPrimary, size: 24)),
              50.height,
              if (_showCancelButton == false)
                Align(
                  alignment: Alignment.center,
                  child: TweenAnimationBuilder<double>(
                    tween: Tween(begin: 1.0, end: progress),
                    duration: Duration(seconds: 1),
                    builder: (context, value, child) {
                      return Stack(
                        alignment: Alignment.center,
                        children: [
                          Container(
                            width: 150,
                            height: 150,
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              color: Colors.red,
                              border: Border.all(
                                color: progress > 0 ? Colors.transparent : ColorUtils.colorPrimary,
                                width: 1,
                              ),
                            ),
                          ),
                          Column(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Text(
                                '$_secondsLeft',
                                style: boldTextStyle(
                                  size: 60,
                                  weight: FontWeight.bold,
                                  color: Colors.white,
                                ),
                              ),
                            ],
                          ),
                        ],
                      );
                    },
                  ),
                ),
              if (showCancelText && _cancelSecondsLeft > 0)
                Align(
                  alignment: Alignment.center,
                  child: TweenAnimationBuilder<double>(
                    tween: Tween(begin: 1.0, end: progressCancel),
                    duration: Duration(seconds: 1),
                    builder: (context, value, child) {
                      return Stack(
                        alignment: Alignment.center,
                        children: [
                          Container(
                            width: 150,
                            height: 150,
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              color: Colors.red,
                              border: Border.all(
                                color: progressCancel > 0
                                    ? Colors.transparent
                                    : ColorUtils.colorPrimary,
                                width: 1,
                              ),
                            ),
                          ),
                          Column(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Text(
                                '$_cancelSecondsLeft',
                                style: boldTextStyle(
                                  size: 60,
                                  weight: FontWeight.bold,
                                  color: Colors.white,
                                ),
                              ),
                            ],
                          ),
                        ],
                      );
                    },
                  ),
                ),
              const SizedBox(height: 16),
              50.height,
              if (_showCancelButton == false)
                DropdownButton<String>(
                  value: selectedValue,
                  isExpanded: true,
                  items: emergencyReasons.map((String value) {
                    return DropdownMenuItem<String>(
                      value: value,
                      child: Text(value,
                          style:
                              primaryTextStyle(color: ColorUtils.colorPrimary)),
                    );
                  }).toList(),
                  onChanged: (newValue) async {
                    _timer?.cancel();
                    setState(() {
                      selectedValue = newValue!;
                      isEmergencyReasonSelected = true;
                    });
                    await _sendEmergencyAlert();
                  },
                ),
              const SizedBox(height: 24),
              if (_isLoading) loaderWidget().center(),
              const SizedBox(height: 24),
              if (showCancelText && _cancelSecondsLeft > 0)
                ElevatedButton(
                  onPressed: () async {
                    _timer?.cancel();
                    Map<String, dynamic> request = {
                      "id": emergencyResponseModel!.id,
                      "emergency_resolved": "Emergency Resolved",
                    };

                    try {
                      await emergancyResolved(
                              request, emergencyResponseModel!.id!)
                          .then((value) {
                        toast(value.message);
                        finish(context); // Ma
                      });
                    } catch (e) {
                      print("------Error$e");
                      toast("Error: $e");
                    }
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.redAccent,
                    shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12)),
                  ),
                  child: Text(language.cancel,
                      style: TextStyle(color: Colors.white)),
                ),
            ],
          ),
        ),
      ),
    );
  }
}
