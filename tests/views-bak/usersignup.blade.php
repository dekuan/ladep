<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0"/>
    <meta property="qc:admins" content="27155754347564603636" />
    <title>注册</title>
    <link href="{{asset('/css/bootstrap.css')}}" rel="stylesheet" xsdep="1" >
    <link rel="stylesheet" href="{{asset('/css/login.css')}}" xsdep="1" />
    <link rel="stylesheet" href="{{asset('/css/mail_test.css')}}" xsdep="1" />
    <link href="{{asset('/images/logo-32-32.png')}}" rel="Shortcut Icon" type="image/x-icon">
    <script src="{{asset('/js/html5shiv.min.js')}}" xsdep="1" ></script>
    <script src="{{asset('/js/respond.js')}}" xsdep="1"></script>
</head>
<body>
<div class="container" id="ucenter_header" name="uCenterPage">
    <div class="row">
        <div class="col-md-11 col-sm-10 col-xs-9"></div>
        <div class="col-md-1 col-sm-2 col-xs-3 row" id="exit">
            <a href="/web/usersignin" id="user_exit">退出</a>
        </div>
    </div>
</div>
<!--************************************邮箱验证*************************************-->
<!--
<section id="mail_test" class="container" name="mail_check_page">
    <div class="row top mail_top">
        <div class="col-md-12"></div>
    </div>
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

    <div class="row"  id="mail_test_content">
        <div class="col-lg-2 col-xs-1 left"></div>
        <div class="col-lg-8 col-xs-10 middle">
            <div class="row">
                <div class="col-md-1 col-xs-1"></div>
                <div class="col-md-10 col-xs-10">
                    <h3>验证邮箱，完成注册</h3>
                    <p>欢迎您加入小说阅读网大家庭！</p>
                    <p><span>验证信息已发送至</span><span id="mail_number"></span></p>
                    <p>点击邮箱内的链接即可完成注册</p>
                    <p><a id="enter_mail">马上去邮箱验证》</a></p>
                </div>
            </div>
            <div class="col-md-1 col-xs-1"></div>
            <hr/>
            <div class="row">
                <div class="col-md-1 col-xs-1"></div>
                <div class="col-md-10 col-xs-10">
                    <h4 class="mail_yz">Q&A</h4>

                    <p>没有收到验证邮件怎么办？</p>

                    <p><span>---邮箱填写错误？</span>&nbsp;&nbsp;&nbsp;&nbsp;<a id="change_mail">换个邮箱</a></p>

                    <p>---看看是否在邮箱的垃圾邮件、广告邮件目录里。</p>

                    <p><span>稍等几分钟，若还未收到验证邮件，</span><a id="resend_mail">点击这里</a><span>重新发送验证邮件</span></p>
                </div>
                <div class="col-md-1 col-xs-1"></div>
            </div>
        </div>
        <div class="col-lg-2 col-xs-1 right"></div>
    </div>
</section>
-->


<div id="head">
   <img src="../images/logo.png" width="108" height="28" id="logo"/>
</div>

<div id="page" name="uRegLoginPage">
    <div id="header">
        <span class="span"></span>
        <a href="/web/usersignin" class="loginOrReg" id="login">登录</a>
        <span class="dot">·</span>
        <a href="/web/usersignup" class="loginOrReg current" id="reg" style="color:#333;">注册</a>
        <span class="span"></span>
    </div>
    <!--<p id="notice"></p>-->
    <form action="" method="post" id="reg_content"  name="register_page">
        <!--*******************注册页面*****************-->
        <input type="hidden" name="_token" value="{{csrf_token()}}"/>
        <div class="inputlist">
            <span class="lefticon_u"></span>
            <input class="input_box js-signup-input" type="text" required maxlength="30" id="user"  placeholder="请输入手机号码"/>
            <span class="uinput_spanbig" id="user_name_span_signup">请按照提示填写正确信息</span>
            <div id="user_box"><span id="user_span"></span></div>
        </div>
        <div class="inputlist">
            <span class="lefticon_s"></span>
            <input class="input_box js-signup-input" type="password"  id="pwd" maxlength="12" placeholder="6~12位数字,字母,符号组合" />
            <span class="uinput_spanbig" id="pwd_span_signup">请按照提示填写正确信息</span>
            <div id="pwd_box"><span id="pwd_span"></span></div>
        </div>
        <div class="inputlist">
            <span class="lefticon_s"></span>
            <input class="input_box js-signup-input" type="password" required   id="repwd" maxlength="12" placeholder="请与密码输入信息一致"/>
            <span class="uinput_spanbig" id="repwd_span_signup">请按照提示填写正确信息</span>
            <div id="repwd_box"><span id="repwd_span"></span></div>
        </div>
        <div class="inputlist">
            <span class="lefticon_m"></span>
            <input  id="cap" class="js-signup-input" size="6" required  placeholder="验证码" maxlength="6" type="text" size="6" autocomplete="off"/>
            <div id="cap_box"><span id="cap_span"></span></div>
            <!--<span class="uinput_spansmall"></span>-->
        </div>

            <input style="display:block;" type="button" id="cap_button" value="获取验证码"/>
            <input type="hidden" name="u_genenry" class="js-u_genenry" id="hidden"/>


        <img  alt="图片验证码" style="display:none;" id="cap_img"/>

        <div id="user_check">
            <label>
                <input type="checkbox" required checked id="check_box"/>
                <span>我已阅读并同意</span>
            </label>
            <a id="user_protect_link" href="/user_protection.html">《用户隐私保护协议》</a>
        </div>
        <!--<p id="signup_final_notice" ></p>-->
        <input type="button" value="创建" id="create_btn"/>
        <input type="button" value="注册" style="display:none;" id="mail_create_btn">
    </form>
</div>
<div class="gray_page" style="display:none;width: 2000px;height:2000px;filter:alpha(opacity=50);background-color: #222;opacity:0.5;position:fixed;left: 0;top: 0;right:0;bottom:0;z-index:299;"></div>
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
<script src="{{asset('/js/jquery-1.11.3.js')}}" xsdep="1" ></script>
<script src="{{asset('/js/loaddata.js')}}" xsdep="1" ></script>
<script src="{{asset('/js/xslib.js')}}" xsdep="1" ></script>
<script src="{{asset('/js/bootstrap.js')}}" xsdep="1" ></script>
<script src="{{asset('/js/laddress.js')}}" xsdep="1" ></script>
<script src="{{asset('/js/usersignup.js')}}" xsdep="1" ></script>
<script>
    var g_cLogin = new Clogin();
    $(function () {
        g_cLogin.OnDocumentReady();
    });

</script>
</body>
</html>