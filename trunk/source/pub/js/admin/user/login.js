/**
 * 變數定義
 *
 */
var $block = null;
var $frmLogin = null;
var $messageList = null;

/**
 * 送出表單前載入畫面
 *
 */
var beforeSubmitLoading = function () {
    try {
        $block.block({
            message: 'Loading...'
        });
    } catch (e) {
        console.log(e);
    }
};

/**
 * 送出表單成功
 *
 */
var formSuccess = function (json) {
    if (json.error) {
        $block.unblock();
        for (var i in json.messages) {
            $messageList.append('<p>' + json.messages[i] + '</p>');
        }
    } else {
        location.reload();
    }
};

/**
 * 載入 DOM
 *
 */
$(function () {
    $block = $('#loginBlock .block');
    $frmLogin = $('#frmLogin');
    $messageList = $('#loginBlock .messageList');
    $frmLogin.ajaxForm({
        beforeSubmit: beforeSubmitLoading,
        dataType: 'json',
        success: formSuccess
    });
});