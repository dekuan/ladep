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
    <link href="{{asset('/wap/css/style.css')}}" rel="stylesheet" type="text/css"  xsdep="1">
    <link href="{{asset('/wap/css/userinfo_form.css')}}" rel="stylesheet" type="text/css"  xsdep="1">
    <base target="_self">
    <link href="{{asset('/wap/images/logo-114-114.png')}}" rel="apple-touch-icon">
    <link href="{{asset('/wap/images/logo-114-114.png')}}" rel="Shortcut Icon" type="image/x-icon">
</head>
<body>
<section>
    <h1><a href="/web/userinfo" target="_self"><img src="{{asset('/wap/images/return_left.png')}}"></a>绑定手机</h1>
    <form class="userinfo_form">
        <input type="text"   placeholder="请输入手机号" id="user_phone_num">
        <p>
            <input type="text"  class="w60" id="phone_verificationCode">
            <input type="button" value="获取验证码" class="w35 authcode bgblue disabled"  id="phone_verificationcode_btn">
        </p>
        <p class="red phone_regtext" >&nbsp;</p>
        <input type="button" value="绑&nbsp;&nbsp;定" id="phone_bind_btn">
    </form>
</section>
<div class='ajax_aplly_error'></div>
<script src="{{asset('/wap/js/jquery-1.11.3.js')}}"  xsdep="1"></script>
<script src="{{asset('/wap/js/userinfo_bind_phone.js')}}"  xsdep="1"></script>
<script src="{{asset('/wap/js/userinfo_loaddata.js')}}"  xsdep="1"></script>
<script>
    var g_CAccountCenter_BindingMsg=new CAccountCenter_BindingMsg();
    $(function(){
        g_CAccountCenter_BindingMsg.OnDocumentReady();
    })
</script>
</body>
</html>