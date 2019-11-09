//全部  赚取  消费 tab栏切换
$('.account_detail .detail_content ul li').on('click',function () {
    $(this).addClass('active').siblings().removeClass('active')
    // $('.account_detail .detail_content .income_content').eq($(this).index()).show().siblings('.income_content').hide()
})

//余额明细 充值记录 提现记录 tab栏切换
$('.account_detail .detail_tab li').on('click',function () {
    $(this).addClass('active').siblings().removeClass('active')
    $('.account_detail .detail_content').eq($(this).index()).show().siblings('.account_detail .detail_content').hide()
})