<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title></title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="keywords" content="手机小说阅读网">
    <meta name="description" content="手机小说阅读网,小说手机阅读。手机小说阅读网提供最新章节首发">
    <meta name="format-detection" content="telphone=no, email=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <link href="{{asset('/wap/css/style.css')}}" rel="stylesheet" type="text/css" xsdep="1">
    <link href="{{asset('/wap/css/userinfo_form.css')}}" rel="stylesheet" type="text/css" xsdep="1">
    <base target="_self">
    <link href="{{asset('/wap/images/logo-114-114.png')}}" rel="apple-touch-icon">
    <link href="{{asset('/wap/images/logo-114-114.png')}}" rel="Shortcut Icon" type="image/x-icon">
</head>
<body>
<section>
    <h1><a href="/web/userinfo"><img src="{{asset('/wap/images/return_left.png')}}"></a>修改密码</h1>
    <form class="userinfo_form" id="pwdprev">
        <input type="text" value="" class="bggray" id="phone_num" disabled="disabled">
        <p>
            <input type="text" value=""  class="w60" placeholder="输入验证码"  id="phone_num_vcode" >
            <input type="hidden" id="js-hidden-verify-token"/>
            <input type="button" value="获取验证码" class="w35 authcode bgblue disabled" id="phone_num_vcode_btn">
        </p>
        <p class="red phone_regtext">&nbsp;</p>
        <input type="button" value="下一步" id="phone_num_next_btn">
    </form>

    <form class="userinfo_form" id="pwdnext">
        <input type="text" value="" placeholder="请输入新密码" id="new_pwd">
        <input type="text" value="" placeholder="请输入确认密码" id="confirm_pwd">
        <p class="red phone_regtext">&nbsp;</p>
        <input type="button" value="完&nbsp;&nbsp;成" id="finish_pwd">
    </form>
</section>
<div class='ajax_aplly_error'></div>
<script src="{{asset('/wap/js/jquery-1.11.3.js')}}" xsdep="1"></script>
<script src="{{asset('/wap/js/userinfo_change_pwd.js')}}" xsdep="1"></script>
<script src="{{asset('/wap/js/userinfo_loaddata.js')}}" xsdep="1"></script>
<script>
    var g_cAccountCenterPwd=new CAccountCenter_ChangePwd();
        $(function(){
            g_cAccountCenterPwd.OnDocumentReady();
        });
</script>
</body>
</html>