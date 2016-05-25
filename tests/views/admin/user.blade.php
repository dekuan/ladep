@extends('admin.dashboard')
@section('main')
<script>
    function doSearch(){


        $('#tt').datagrid('load',{
            u_mid: $('#u_mid').val(),
            u_name: $('#u_name').val()
        });
    }
</script>
<div id="tb" style="padding:3px">
    <span>用户mid</span>
    <input id="u_mid" style="line-height:26px;border:1px solid #ccc">
    <span>用户名:</span>
    <input id="u_name" style="line-height:26px;border:1px solid #ccc">
    <a href="#" class="easyui-linkbutton" plain="true" onclick="doSearch()">查询</a>
</div>
<table id="tt"  class="easyui-datagrid" style="width:1200px;height:250px"
       url="user" toolbar="#tb"
       title="用户信息" iconCls="icon-save"
       rownumbers="true" pagination="true">
    <thead>
    <tr>
        <th field="u_mid" width="80">用户mid</th>
        <th field="u_pmid" width="80">用户绑定mid</th>
        <th field="u_name" width="80" align="right">用户名</th>
        <th field="u_nickname" width="80" align="right">用户昵称</th>
        <th field="u_gender" width="150">用户性别</th>
        <th field="u_imgid" width="60" align="center">用户头像</th>
        <th field="u_birth" width="60" align="center">用户生日</th>
        <th field="u_status" width="60" align="center">用户状态</th>
        <th field="u_type" width="60" align="center">用户类型</th>
        <th field="u_action" width="60" align="center">用户权限</th>
        <th field="u_email" width="60" align="center">邮箱</th>
        <th field="u_mobile" width="60" align="center">手机号</th>
        <th field="u_qq" width="60" align="center">QQ</th>
        <th field="u_wechat" width="60" align="center">微信</th>
        <th field="u_weibo" width="60" align="center">微博</th>
        <th field="u_addr" width="60" align="center">地址</th>
        <th field="u_source" width="60" align="center">注册来源</th>
        <th field="u_cdate" width="60" align="center">创建时间</th>
        <th field="u_mdate" width="60" align="center">更新时间</th>
    </tr>
    </thead>
</table>
@endsection
