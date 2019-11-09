//自适应
function sadpt() {
    var body = document.body;
    var html = document.documentElement;
    var bodyWidth = body.clientWidth;
    var fontSize=bodyWidth / 16;
    html.style.fontSize =fontSize + 'px';
}
function bodychange() {
    document.addEventListener('DOMContentLoaded', function () {
        sadpt()
    });
}
(function () {
    bodychange()
})();
document.body.onresize = function () {
    sadpt()
    //动态获取滑动窗口高度
    $('.stair-list-left').css('height',$(window).height())
    $('.stair-list-right').css('height',$(window).height())
}


