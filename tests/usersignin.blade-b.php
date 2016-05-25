<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0"/>
    <meta property="qc:admins" content="27155754347564603636" />
    <title>登录</title>
    <link rel="stylesheet" href="{{asset('/css/bootstrap.css')}}" xsdep="1" >
    <link rel="stylesheet" href="{{asset('/css/login.css')}}" xsdep="1" />
    <link rel="stylesheet" href="{{asset('/css/mail_test.css')}}" xsdep="1" />
    <link href="{{asset('/images/logo-32-32.png')}}" rel="Shortcut Icon" type="image/x-icon">
    <script src="{{asset('/js/html5shiv.min.js')}}" xsdep="1"></script>
    <script src="{{asset('/js/respond.js')}}" xsdep="1"></script>
</head>
<body>
<input name="hidden_umid" class="js-hidden-umid" type="hidden">
<div id="head"><img src="../images/logo.png" width="108" height="28" id="logo"/></div>
<div class="container" id="ucenter_header" name="uCenterPage">
    <div class="row">
        <div class="col-md-11 col-sm-10 col-xs-9"></div>
        <div class="col-md-1 col-sm-2 col-xs-3 row" id="exit">
            <a href="/web/usersignin" id="user_exit">退出</a>
        </div>
    </div>
</div>
 <!--************************************重新发送邮件成功页面******************************************-->
 <!--
 <script src = "{{asset('/js/jquery-1.11.3.js')}}" xsdep =  1 ></script>

        <div class="row" id="mail_resend_content">
            <div>
                <div class="col-md-2 col-xs-0 mail_resend_left"></div>
                <div class="col-md-8 col-xs-12 mail_resend_middle">
                    <h3 id="mail_resend_h">重发验证邮件成功</h3>
                    <p>
                    <span>我们已经重新向您的邮箱</span>
                    <span id="mail_num"></span>
                    <span>发送了一封激活邮件，请点击邮箱中的链接完成</span>
                    </p>
                    <input id="enter_mail_btn" type="button" value="立即进入邮箱"/>
                </div>
                <div class="col-md-2 col-xs-0 mail_resend_right"></div>
            </div>
        </div>
 -->
<div id="page" name="uRegLoginPage" >
    <div id="header">
        <span class="span"></span>
        <a href="/web/usersignin" class="loginOrReg current" id="login" style="color:#333;">登录</a>
        <span class="dot">·</span>
        <a href="/web/usersignup" class="loginOrReg " id="reg">注册</a>
        <span class="span"></span>
    </div>

    <!--*******************登录页面*****************-->
    <form action="" method="post" id="login_content" name="login_page">
       <input type="hidden" name="_token" value="{{csrf_token()}}"/>
        <div class="inputlist">
            <span class="lefticon_u"></span>
            <input class="input_box" id="uname" type="text" placeholder="已注册的手机号码" /></input>
            <span class="uinput_spanbig" id="user_name_span_login">请按照提示填写正确信息</span>
        </div>
        <div class="inputlist" id="inputlist_pwd">
            <span class="lefticon_s"></span>
            <input class="input_box" id="upwd" type="password" maxlength="12" placeholder="密码" />
            <span class="uinput_spanbig" id="pwd_span_login">请按照提示填写正确信息</span>
            <div id="user_check_span">
                <label><input  id="check_box_keepalive" name="keepalive" type="checkbox" checked="checked"/><span>30天自动登录</span></label>
                <a id="pwd_reset_a" href="/web/pwdreset">忘记密码？</a>
            </div>
        </div>
        <input value="登录" id="login_btn" type="button"><br/>
        <span class="notice"></span>
        <span id="span">无需注册，用社交账号直接登录</span>
        <span class="notice"></span>
        <div id="chat_tools">
            <div id="qq"><a id="qq_link"></a><span>QQ</span></div>
            <div id="chat"><a id="chat_link"></a><span>微信</span></div>
            <div id="sina"><a id="sina_link"></a><br/><span>新浪微博</span></div>
        </div>
    </form>
</div>
<div class="gray_page" style="display:none;width: 2000px;height:2000px;filter:alpha(opacity=50);background-color: #222;opacity:0.5;position:fixed;left: 0;top: 0;right:0;bottom:0;z-index: 299;"></div>
<div class="dialog_pop" style="display:none;z-index:300;position:fixed;width:550px;height:166px;background-color:#fff;left:50%;margin-left:-275px;top:200px;">
    <div style="height:45px;background-color:#fafafa;border-bottom:1px solid #eee;">
        <span class="reminder" style="float:left;color:#333;height:45px;line-height:45px;margin-left:25px;font-size:18px;">提示</span>
        <span class="dialog_close_btn" style="display:inline-block;width:20px;height:20px;background:url('/images/close_button.png');float:right;margin:12px 12px;cursor:pointer;"></span>
    </div>
    <p style="height:60px;color:#333;font-size:16px; padding:10px 15px; text-indent:0" >
        <span style="display:inline-block;background:url('../images/pop_warning.png') 0 13px no-repeat;line-height:25px; padding-left:35px; " class="dialog_para">未知的错误发生了,请稍后再试~</span>
    </p>
    <div style="height:51px;background-color:#fafafa;border-top:1px solid #eee;">
        <input type="button" class="pop_sure_btn" style="width:65px;height:35px;background-color:#f9494c;color:#fff; float:right; margin-top:8px; margin-right:15px;border:none;border-radius:5px;font-size:16px;" value="确认"/>
    </div>
</div>

<script src="{{asset('/js/bootstrap.js')}}" xsdep="1"></script>
<script src="{{asset('/js/xslib.js')}}" xsdep="1"></script>
<script src="{{asset('/js/laddress.js')}}" xsdep="1"></script>
<script src="{{asset('/js/usersignin.js')}}" xsdep="1"></script>
<script src="{{asset('/js/loaddata.js')}}" xsdep="1"></script>
<script src="{{asset('/js/purl.js')}}" xsdep="1"></script>
<script>
    var g_cSignin = new CSignin();
    $(function () {
        g_cSignin.OnDocumentReady();

    });

</script>
</body>
</html>