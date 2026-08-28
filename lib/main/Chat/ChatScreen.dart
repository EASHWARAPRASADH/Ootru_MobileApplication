import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:infinite_scroll_pagination/infinite_scroll_pagination.dart';

import '../../extensions/colors.dart';
import '../../extensions/decorations.dart';
import '../../extensions/extension_util/context_extensions.dart';
import '../../extensions/extension_util/int_extensions.dart';
import '../../extensions/extension_util/string_extensions.dart';
import '../../extensions/extension_util/widget_extensions.dart';
import '../../extensions/shared_pref.dart';
import '../../extensions/system_utils.dart';
import '../../extensions/text_styles.dart';
import '../../main.dart';
import '../../main/utils/Widgets.dart';
import '../components/CommonScaffoldComponent.dart';
import '../models/ChatMessageModel.dart';
import '../models/LoginResponse.dart';
import '../utils/Constants.dart';
import '../utils/dynamic_theme.dart';
import 'ChatItemWidget.dart';

class ChatScreen extends StatefulWidget {
  final UserData? userData;
  String? orderId;

  ChatScreen({this.userData, this.orderId});

  @override
  _ChatScreenState createState() => _ChatScreenState();
}

class _ChatScreenState extends State<ChatScreen> {
  String id = '';
  var messageCont = TextEditingController();
  var messageFocus = FocusNode();
  bool isMe = false;

  final PagingController<DocumentSnapshot?, ChatMessageModel> _pagingController = PagingController(firstPageKey: null);

  UserData sender = UserData(
    name: getStringAsync(USER_NAME),
    profileImage: appStore.userProfile,
    uid: getStringAsync(UID),
    playerId: getStringAsync(PLAYER_ID),
  );

  @override
  void initState() {
    super.initState();
    init();
    _pagingController.addPageRequestListener((pageKey) {
      _fetchPage(pageKey);
    });
  }

  @override
  void dispose() {
    _pagingController.dispose();
    super.dispose();
  }

  init() async {
    id = getStringAsync(UID);
    mIsEnterKey = getBoolAsync(IS_ENTER_KEY, defaultValue: false);
    mSelectedImage = getStringAsync(SELECTED_WALLPAPER, defaultValue: "assets/default_wallpaper.png");

    ordersMessageService.setUnReadStatusToTrue(orderId: widget.orderId);
    setState(() {});
  }

  Future<void> _fetchPage(DocumentSnapshot? nextPageMarker) async {
    try {
      Query query = ordersMessageService
          .chatMessagesWithPagination(
            currentUserId: getStringAsync(UID),
            receiverUserId: widget.userData!.uid.validate(),
            orderId: widget.orderId,
          )
          .orderBy('createdAt', descending: true)
          .limit(PER_PAGE_CHAT_COUNT);

      if (nextPageMarker != null) {
        query = query.startAfterDocument(nextPageMarker);
      }

      final querySnapshot = await query.get();

      final newItems = querySnapshot.docs.map((doc) {
        ChatMessageModel data = ChatMessageModel.fromJson(doc.data() as Map<String, dynamic>);
        data.isMe = data.senderId == sender.uid;
        return data;
      }).toList();

      final isLastPage = newItems.length < PER_PAGE_CHAT_COUNT;

      if (isLastPage) {
        _pagingController.appendLastPage(newItems);
      } else {
        _pagingController.appendPage(newItems, querySnapshot.docs.last);
      }
    } catch (e) {
      _pagingController.error = e;
    }
  }

  sendMessage({FilePickerResult? result}) async {
    if (result == null && messageCont.text.trim().isEmpty) {
      messageFocus.requestFocus();
      return;
    }

    ChatMessageModel data = ChatMessageModel();
    data.receiverId = widget.userData!.uid;
    data.senderId = sender.uid;
    data.message = messageCont.text;
    data.isMessageRead = false;
    data.createdAt = DateTime.now().millisecondsSinceEpoch;

    if (result != null) {
      if (result.files.single.path.isImage) {
        data.messageType = MessageType.IMAGE.name;
      } else {
        data.messageType = MessageType.TEXT.name;
      }
    } else {
      data.messageType = MessageType.TEXT.name;
    }

    notificationService.sendPushNotifications(getStringAsync(USER_NAME), messageCont.text, receiverPlayerId: widget.userData!.playerId).catchError(log);
    messageCont.clear();
    setState(() {});

    await ordersMessageService.addOrderMessage(data, widget.orderId);
    _pagingController.refresh(); // reload chat after sending
  }

  @override
  Widget build(BuildContext context) {
    return CommonScaffoldComponent(
      showBack: false,
      appBar: commonAppBarWidget(
        '',
        titleWidget: Row(
          mainAxisAlignment: .start,
          crossAxisAlignment: .start,
          children: [
            CircleAvatar(
              backgroundColor: context.cardColor,
              backgroundImage: NetworkImage(widget.userData!.profileImage.validate()),
              minRadius: 20,
            ),
            10.width,
            Column(
              crossAxisAlignment: .start,
              children: [
                Text(widget.userData!.name.validate(), style: secondaryTextStyle(size: 16, color: whiteColor)),
                4.height,
                Text(' # ${widget.orderId.validate()}', style: secondaryTextStyle(size: 14, color: Colors.white60)),
              ],
            ).expand(),
          ],
        ),
      ),
      body: Container(
        height: context.height(),
        width: context.width(),
        child: Stack(
          children: [
            PagedListView<DocumentSnapshot?, ChatMessageModel>.separated(
              padding: .only(left: 8, top: 8, right: 8, bottom: 76),
              reverse: true,
              pagingController: _pagingController,
              physics: BouncingScrollPhysics(),
              builderDelegate: PagedChildBuilderDelegate<ChatMessageModel>(
                itemBuilder: (context, item, index) {
                  return ChatItemWidget(data: item);
                },
                noItemsFoundIndicatorBuilder: (context) => Offstage(),
                firstPageProgressIndicatorBuilder: (context) => Center(child: CircularProgressIndicator()),
                newPageProgressIndicatorBuilder: (context) => Center(child: CircularProgressIndicator()),
              ),
              separatorBuilder: (context, index) => SizedBox(height: 8),
            ),
            Positioned(
              bottom: 16,
              left: 16,
              right: 16,
              child: Container(
                decoration: boxDecorationWithShadow(
                  borderRadius: BorderRadius.circular(30),
                  spreadRadius: 1,
                  blurRadius: 1,
                  backgroundColor: context.cardColor,
                ),
                padding: .only(left: 8, right: 8),
                child: Row(
                  children: [
                    TextField(
                      controller: messageCont,
                      decoration: InputDecoration(
                        border: InputBorder.none,
                        hintText: language.writeAMessage,
                        hintStyle: secondaryTextStyle(),
                        contentPadding: .symmetric(vertical: 18, horizontal: 4),
                      ),
                      cursorColor: appStore.isDarkMode ? Colors.white : Colors.black,
                      focusNode: messageFocus,
                      textCapitalization: TextCapitalization.sentences,
                      keyboardType: TextInputType.multiline,
                      minLines: 1,
                      style: primaryTextStyle(),
                      textInputAction: mIsEnterKey ? TextInputAction.send : TextInputAction.newline,
                      onSubmitted: (s) {
                        sendMessage();
                      },
                      cursorHeight: 20,
                      maxLines: 5,
                    ).expand(),
                    IconButton(
                      icon: Icon(Icons.send, color: ColorUtils.colorPrimary),
                      onPressed: () {
                        sendMessage();
                      },
                    )
                  ],
                ),
              ),
            )
          ],
        ),
      ),
    );
  }
}
