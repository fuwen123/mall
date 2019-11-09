$('body').on('click', '.preview', function () {
    getAllForm();
    //预览
    layer.open({
        type: 2 //Page层类型
        ,area: ['30%', '80%']
        ,title: '预览'
        ,shade: 0.6 //遮罩透明度
        ,maxmin: true //允许全屏最小化
        ,anim: 0 //0-6的动画形式，-1不开启
        ,content:"/index.php/admin/BespeakTemplate/preview"
        ,cancel: function(){
            //右上角关闭回调
            //return false 开启该代码可禁止点击该按钮关闭
        }
        ,success: function(layero, index){
            //弹窗加载完成
            var preview = layer.getChildFrame('#preview_data', index);
            preview.val(json);

        }
    });

});

function getAllForm() {
    subForm = new Object();
    getForm();
    getTemplate();
    subForm['template'] = template;
    if($('#delete_template_id').val()){
        subForm['delete_template'] = $('#delete_template_id').val();
    }
    json = JSON.stringify(subForm);
    // var jsonobj=eval('('+json+')');
    // console.log(json)
}

//获取自定义表单
function getForm() {
    var form = new Array();
    $('.tpd-appointment-wrap .commonBd-list').each(function () {
        var user_arr = new Object();
        if(Number($(this).find('input').attr('data-unit'))){
            user_arr['template_unit_id'] = $(this).find('input').attr('data-unit');
        }
        user_arr['required'] =$(this).find('input').attr('data-required')?1:0;
        user_arr['format'] =$(this).find('input').attr('data-format')?$(this).find('input').attr('data-format'):'';
        user_arr['name'] =$(this).find('input').attr('name');
        user_arr['type'] = user_arr['name']=='take_time'?8:1;
        user_arr['title'] = $(this).find('.commonBd-name').text();
        user_arr['sort'] = 88;
        user_arr['placeholder'] =$(this).find('input').attr('placeholder')?$(this).find('input').attr('placeholder'):'';
        user_arr['desc'] =$(this).find('input').attr('placeholder')?$(this).find('input').attr('placeholder'):'';
        form.push(user_arr);
    });
    $('.tpd-edits-hidden').each(function (i,o) {
        var id = $(this).attr('data-eidtid');
        var title = $(this).find('.TextName').text();
        var value = $(this).find('.cont-list').children();
        var arr = new Object();
        var v = new Object();
        // console.log(id);
        switch (Number(id)){
            //单文本
            case 0:
                if(Number(value.eq(0).attr('data-unit'))){
                    arr['template_unit_id'] = value.eq(0).attr('data-unit');
                }
                arr['required'] = value.eq(0).attr('data-required')?1:0;
                arr['format'] = value.eq(0).attr('data-format')?value.eq(0).attr('data-format'):'';
                arr['sort'] = value.eq(0).attr('data-sort');
                arr['type'] = 1;
                arr['title'] = title;
                arr['desc'] = value.eq(0).parent().prev().text();
                arr['placeholder'] = value.eq(0).attr('placeholder')?value.eq(0).attr('placeholder'):'';
                $.each(value,function (i) {
                });
                break;
            //多行文本
            case 1:
                if(Number(value.eq(0).attr('data-unit'))){
                    arr['template_unit_id'] = value.eq(0).attr('data-unit');
                }
                arr['required'] = value.eq(0).attr('data-required')?1:0;
                arr['type'] = 2;
                arr['sort'] = value.eq(0).attr('data-sort');
                arr['title'] = title;
                arr['desc'] = value.eq(0).parent().prev().text();
                arr['placeholder'] = value.eq(0).attr('placeholder')?value.eq(0).attr('placeholder'):'';
                $.each(value,function () {
                });
                break;
            //单选按钮
            case 2:
                if(Number(value.eq(0).attr('data-unit'))){
                    arr['template_unit_id'] = value.eq(0).attr('data-unit');
                }
                arr['required'] = value.eq(0).attr('data-required')?1:0;
                arr['type'] = 3;
                arr['sort'] = value.eq(0).attr('data-sort');
                arr['title'] = title;
                var v = new Array();
                $.each(value,function (i) {
                    v[i] = $(this).find('label').text();
                });
                if(v){
                    arr['value'] = v.join(',');
                }
                break;
            //复选
            case 3:
                if(Number(value.eq(0).attr('data-unit'))){
                    arr['template_unit_id'] = value.eq(0).attr('data-unit');
                }
                arr['required'] = value.eq(0).attr('data-required')?1:0;
                arr['type'] = 4;
                arr['sort'] = value.eq(0).attr('data-sort');
                arr['title'] = title;
                var v = new Array();
                $.each(value,function (i) {
                    v[i] = $(this).find('label').text();
                });
                if(v){
                    arr['value'] = v.join(',');
                }
                break;
            //下拉
            case 4:
                if(Number(value.eq(0).attr('data-unit'))){
                    arr['template_unit_id'] = value.eq(0).attr('data-unit');
                }
                arr['required'] = value.eq(0).attr('data-required')?1:0;
                arr['type'] = 5;
                arr['sort'] = value.eq(0).attr('data-sort');
                arr['title'] = title;
                var v = new Array();
                $.each(value,function (i) {
                    v[i] = $(this).text();
                });
                if(v){
                    arr['value'] = v.join(',');
                }
                break;
            //图片
            case 5:
                if(Number(value.eq(0).attr('data-unit'))){
                    arr['template_unit_id'] = value.eq(0).attr('data-unit');
                }
                if(value.eq(0).attr('data-upload-num')==1){
                    arr['type'] = 6;
                }else {
                    arr['type'] = 7;
                }
                arr['sort'] = value.eq(0).attr('data-sort');
                arr['required'] = value.eq(0).attr('data-required')?1:0;
                arr['title'] = title;
                $.each(value,function () {
                });
                break;

        }
        form.push(arr);
    });

    subForm['form'] = form;
}
//获取默认模板
function getTemplate() {
    template = new Object();
    // if(Number($('#template_id').val())){
    //     template['template_id'] = $('#template_id').val();
    // }
    template['reserved_days'] = $("input[name='reserved_days']").val();
    template['numbers'] = $("input[name='numbers']").val();
    template['name'] = $("input[name='name']").val();
    getTemplateTime();
    getEquipment();
}

//获取预约时间
function getTemplateTime() {
    var work  = $('.work').find('.timeAppointment-list-ul .timechenk');
    var week  = $('.week').find('.timeAppointment-list-ul .timechenk');
    $.each(work,function () {
        template[$(this).attr('data-work')]=1;
    });
    $.each(week,function () {
        template[$(this).attr('data-work')]=1;
    });
    if(template['monday'] || template['tuesday'] || template['wednesday'] || template['thursday'] || template['friday']){
        template['work_am_start_time'] = $("input[name='work_am_start_time']").val();
        template['work_am_end_time'] = $("input[name='work_am_end_time']").val();
        template['work_pm_start_time'] = $("input[name='work_pm_start_time']").val();
        template['work_pm_end_time'] = $("input[name='work_pm_end_time']").val();
    }
    if(template['saturday'] || template['sunday']){
        template['weekend_am_start_time'] = $("input[name='weekend_am_start_time']").val();
        template['weekend_am_end_time'] = $("input[name='weekend_am_end_time']").val();
        template['weekend_pm_start_time'] = $("input[name='weekend_pm_start_time']").val();
        template['weekend_pm_end_time'] = $("input[name='weekend_pm_end_time']").val();
    }

}

//场景设备

function getEquipment() {
    var equipment = $("input[name='equipment[]']");
    var arr = new Array();
    $.each(equipment,function (i) {
        if($(this).val()){
            arr[i] = $(this).val();
        }
    });
    template['equipment'] = arr.join(',');

}

var ajax_status = 1;
$('body').on('click', '.submit', function () {
    getAllForm();
    if(!$("input[name='name']").val()){
        layer.open({icon: 2, content:'请输入模板名称', time: 2000});
        return false;
    }
   if($('.timechenk').length <=0){
       layer.open({icon: 2, content:'一周最少选一天', time: 2000});
       return false;
   }

    if(!ajax_status){
        return false;
    }
    // return false;
    ajax_status = 0;
    $.ajax({
        type: "POST",
        url: $("form").attr('action'),
        dataType: 'json',
        // data: $("form").serialize(),
        data: subForm,
        success: function (data) {
            if (data.status == 1) {
                layer.open({icon: 1, content: data.msg, time: 1000});
                if($('.submit').attr('data-iframe')==1){
                    parent.layer.closeAll();
                    if(data.data['template_id']){
                        var html = '<option value="'+data.data['template_id']+'">'+$("input[name='name']").val()+'</option>';
                        window.parent.template_call_back(html)
                    }

                }else{
                    window.location.href = data.url;
                }
            } else {
                layer.open({icon: 2, content: data.msg, time: 1000});
            }
            ajax_status = 1;
        }
    });

});


