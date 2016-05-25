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
    <link href="{{asset('/wap/css/style.css')}}" rel="stylesheet" type="text/css"  lava="1">
    <link href="{{asset('/wap/css/userinfo_form.css')}}" rel="stylesheet" type="text/css" lava="1">
    <base target="_self">
    <link href="{{asset('/wap/images/logo-114-114.png')}}" rel="apple-touch-icon">
    <link href="{{asset('/wap/images/logo-114-114.png')}}" rel="Shortcut Icon" type="image/x-icon">
</head>
<body>
<section>
    <h1><a href="/web/userinfo"><img src="{{asset('/wap/images/return_left.png')}}"></a>修改昵称</h1>
    <form class="userinfo_form">
        <input type="text" value="" placeholder="" id="nick_name_input"  maxlength="15">
        <p class="red phone_regtext" style="color:#333;">昵称不能超过15个字符哦~</p>
        <input type="button" value="确&nbsp;&nbsp;定" id="nick_name_btn">
    </form>
</section>
<div class='ajax_aplly_error'></div>
<script src="{{asset('/wap/js/jquery-1.11.3.js')}}" lava="1"></script>
<script src="{{asset('/wap/js/const.js')}}" lava="1"></script>
<script src="{{asset('/wap/js/userinfo_nickname.js')}}" lava="1"></script>
<script src="{{asset('/wap/js/userinfo_loaddata.js')}}" lava="1"></script>
<script src="{{asset('/wap/js/xslib.js')}}" lava="1"></script>
<script>
    var g_CAccountCenter_Nickname=new CAccountCenter_Nickname();
    $(function(){
        g_CAccountCenter_Nickname.OnDocumentReady();
    })
</script>
</body>
</html>