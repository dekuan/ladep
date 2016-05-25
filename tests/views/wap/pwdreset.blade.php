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
<!---------------------------------------第一步------------------------------------------------>
<section id="pwdfindback_one">
    <h1><a href="/web/userinfo" target="_self"><img src="{{asset('/wap/images/return_left.png')}}"></a>找回密码</h1>
    <form class="userinfo_form" >
        <input type="hidden" class="js_hidden_genenry_input"/>
        <input type="text" value=""  placeholder="输入手机号"  id="pwdfindback_phone_num_one">
        <p class="imgcode">
            <input type="text" id="pwdfindback_phone_num_imgcode" placeholder="验证码" >
            <span class="refresh">
                <img id="pwd_reset_ver_img" src="" alt="">
                <b id="pwd_reset_ver_btn">换一换</b>
            </span>
        </p>

        <p class="red phone_regtext">&nbsp;</p>
        <input type="button" value="下一步" id="pwdfindback_next_btn1" class="disabled">
    </form>
</section>

<!--------------------------------------第二步------------------------------------------------>

<section id="pwdfindback_two">
    <h1><a href="/web/cpwdverify" ><img src="{{asset('/wap/images/return_left.png')}}"></a>找回密码</h1>
    <form class="userinfo_form" >
        <input type="text" value=""  id="pwdfindback_phone_num_two" disabled="disabled" class="bggray">
        <p>
            <input type="text" value=""  class="w60" placeholder="输入验证码"  id="pwdfindback_vcode" >
            <input type="button" value="获取验证码" class="w35 authcode bgblue" id="pwdfindback_vcode_btn">
             <input type="hidden" class="js_hidden_genenry_input"/>
        </p>
        <p class="red phone_regtext">&nbsp;</p>
        <input type="button" value="下一步" id="pwdfindback_next_btn2">
    </form>
</section>

<!--------------------------------------第三步------------------------------------------------>

<section id="pwdfindback_three">
    <h1><a href="/web/cpwdverify" ><img src="{{asset('/wap/images/return_left.png')}}"></a>找回密码</h1>
     <form class="userinfo_form" >
        <input type="text" value="" placeholder="请输入新密码" id="pwdfindback_new_pwd">
        <input type="text" value="" placeholder="请输入确认密码" id="pwdfindback_confirm_pwd">
         <input type="hidden" class="js_hidden_genenry_input"/>
        <p class="red phone_regtext">&nbsp;</p>
        <input type="button" value="完&nbsp;&nbsp;成" id="pwdfindback_finish_btn" class="disabled">
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