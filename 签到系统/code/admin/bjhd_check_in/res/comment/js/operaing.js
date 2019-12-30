function changeSort(id, type){
    var sort = $('#changeSort'+id).val();
    var str = "&type="+type+"&id="+ id+"&sort="+sort;
    $.ajax({
        url:"{url 'site/entry/ChangeSort' array('m'=>$_GPC['m'],'op'=>'ChangeSort','version_id'=>$_GPC['version_id'])}",
        type: 'get',
        data: str,
        cache: false,
        contentType: false,
        processData: false,
        success: function (res) {
            res = $.parseJSON(res);
            if(res.code == 200){
                layer.msg(res.errorMsg, {icon:1, time:1500});
            } else {
                layer.msg(res.errorMsg, {icon:5, time:1500});
            }
        },
        error: function (data) {}
    });
}