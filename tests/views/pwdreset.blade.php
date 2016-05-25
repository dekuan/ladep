<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta property="qc:admins" content="27155754347564603636" />
    <title>重置密码</title>
    <link href="{{asset('/css/bootstrap.css')}}" rel="stylesheet"  xsdep="1" >
    <link href="{{asset('/css/passwordRefind.css')}}" rel="stylesheet"  xsdep="1" >
    <link href="{{asset('/images/logo-32-32.png')}}" rel="Shortcut Icon" type="image/x-icon">
    <script src="{{asset('/js/html5shiv.min.js')}}"  xsdep="1" ></script>
    <script src="{{asset('/js/respond.js')}}"  xsdep="1" ></script>
</head>
<body>
<input name="hidden_umid" class="js-hidden-umid" type="hidden">
<!--*********************************重置密码页面*****************************************-->
<header class="container" id="reset_header" name="pwd_reset_page">
    <div class="row">
        <div class="col-md-11 col-sm-10 col-xs-9"></div>
        <div class="col-md-1 col-sm-2 col-xs-3"><a href="/web/usersignin" id="header_login">登录</a></div>
    </div>
</header>
<!--****************************************选择用户名类型************************************-->
                <div id="user_type_choice">
                    <p id="user_type_choice_span">请选择用户名类型</p>
                    <input id="user_type_submit" type="button" value="确定">
                </div>
<!--*******************************************************************************************-->
<section class="container" id="reset_section">
    <div class="row">
        <div class=" col-sm-2 col-xs-1 pwd_left"></div>
        <div class=" col-sm-8 col-xs-10 pwd_middle container">
            <div class="row" id="title_box">
                <div class="col-md-3 col-sm-4 col-xs-6">
                    <span id="pwd_reset_title">密码找回</span>
                </div>
                <div class="col-md-9 col-sm-8 col-xs-6"></div>
            </div>
            <div class="row" id="step">
                <div class="col-xs-3 row current_step" id="step_1st_">
                    <div class="col-xs-4 number">01</div>
                    <div class="col-xs-8">确认信息</div>
                </div>
                <div class="col-xs-1"><img src="../images/password_jt.png" width="12" height="18"></div>
                <div class="col-xs-3  row"  id="step_2nd_">
                    <div class="col-xs-4 number">02</div>
                    <div class="col-xs-8">安全验证</div>
                </div>
                <div class="col-xs-1"><img src="../images/password_jt.png" width="12" height="18"></div>
                <div class="col-xs-3  row"  id="step_3rd_">
                    <div class="col-xs-4 number">03</div>
                    <div class="col-xs-8">重置密码</div>
                </div>
                <div class="col-xs-1"></div>
            </div>


            <!--**************找回密码1*********************-->
            <form method="post" class="row" id="step1st_box" name="pwd_reset_step1">
               <input type="hidden" id="js-hidden-usertype-token"/>
                <div class="row">
                    <div class="col-xs-3"></div>
                    <div class="col-xs-6">
                        <p id="tip">请输入您已绑定的手机号：</p>
                    </div>
                    <div class="col-xs-3"></div>
                </div>

                <div class="row input_group">
                    <div class="col-xs-3"></div>
                    <input type="hidden" id="js-hidden-genenry-input"/>
                    <div class="inputlist">
                        <span class="lefticon_u"></span>
                        <input class="box col-xs-6 input" id="pwd_reset_user" required  maxlength="30" type="text" placeholder="已注册的手机号"/>

                        <span class="uinput_spanbig" id="user_name_span_pwd_reset">请按照提示填写正确信息</span>
                        <div class="col-xs-3"><span id="pwd_reset_user_name_span"></span></div>
                    </div>
                </div>

                <div class="row input_group">
                    <div class="col-xs-3"></div>
                        <div class="inputlist">
                            <span class="lefticon_s"></span>
                            <input id="pwd_reset_ver" class="col-xs-6"  required maxlength="6"  placeholder="验证码"/>
                            <div class="refresh">
                                <img id="pwd_reset_ver_img" alt=""/>
                                <input id="pwd_reset_ver_btn" type="button"/>
                            </div>
                        </div>
                    <div class="col-xs-3">
                        <span id="pwd_reset_ver_span"></span>
                    </div>
                </div>
                <div class="row input_group" id="pwd_reset_next_btn">
                    <div class="col-xs-3"></div>
                    <input id="nextstep" type="button" class="col-xs-6 box input next_step" value="下一步" />
                    <div class="col-xs-3"></div>
                </div>
            </form>

            <!--***************透过绑定的手机号码找回密码2**************-->
            <div class="row" id="step2nd_box"  name="pwd_reset_step2" >
                <div class="row" id="by_way">
                    <div class="col-md-1"></div>
                    <div class="col-md-10 row viewcenter" >
                        <span><img id="by_way_img" src="{{asset('images/phone.gif')}}"/></span>
                        <span id="by_way_span">通过绑定的手机号码</span>
                        <span id="by_way_text"></span>
                    </div>
                    <div class="col-md-1"></div>
                </div>


                <div class="row" id="pwd_reset_phone_ver">
                    <div class="col-xs-3"></div>
                    <div class="col-xs-6 row">
                        <div class="inputlist">
                            <span class="lefticon_s"></span>
                            <input class="col-xs-8" id="phone_ver_input" required type="text" maxlength="6"  placeholder="输入手机收到的验证码"/>
                            <span class="uinput_spansmall"></span>
                            <input class="col-xs-4" id="phone_ver_button" type="button"  value="获取验证码"/>
                        </div>
                    </div>
                    <div class="col-xs-3"><span id="user_phone_verify_span"></span></div>
                </div>
                <div class="row">
                    <div class="col-md-3"></div>
                    <input class="col-md-6 next_step" type="button" value="下一步" id="next_step2nd"/>
                    <div class="col-md-3"></div>
                </div>
            </div>


            <!--***************透过绑定的邮箱找回密码2**************-->
            <!--
            <div class="row" id="step2nd_mail_box"  name="pwd_reset_mail_step2" >
               <div class="row" id="by_way_mail">
               <input type="hidden" id="js-hidden-reset2nd-token">
                                   <div class="col-xs-1"></div>
                                   <div class="col-xs-10 row viewcenter">
                                       <span>
                                           <img id="by_way_mail_img" src="{{asset('images/mail_find_pwd.png')}}"/>
                                       </span>
                                       <span id="by_way_mail_span">通过绑定的邮箱</span>
                                       <span id="by_way_mail_text"></span>
                                   </div>
                                   <div class="col-xs-1"></div>
                               </div>

                <div class="row" id="pwd_reset_mail_ver">
                    <div class="col-xs-3"></div>
                    <div class="col-xs-6 row">
                         <div class="inputlist">
                             <span class="lefticon_s"></span>
                             <input class="col-xs-8" id="mail_ver_input" required type="text"  placeholder="输入邮箱收到的验证码"/>
                             <input class="col-xs-4" id="mail_ver_button" type="button" maxlength="6"  value="获取验证码"/>
                         </div>
                    </div>
                    <div class="col-xs-3"><span id="user_mail_verify_span"></span></div>
                </div>
                    <div class="row">
                        <div class="col-xs-3"></div>
                        <input class="col-xs-6 next_step" type="button" value="下一步" id="next_mail_step2nd" />
                        <div class="col-xs-3"></div>
                    </div>
            </div>
            -->

            <!--***************************重置密码****************************-->
            <form  class="row resetpass" id="step3rd_box"  name="pwd_reset_step3" >
                <input type="hidden" name="_token" value=""/>
                <div class="row">
                    <div class="col-xs-3"></div>
                    <div class="inputlist inputlistok">
                        <span class="lefticon_s"></span>
                        <input type="password" class="col-xs-6 pwd" maxlength="12" id="pwd_reset_pwd" placeholder="新密码"/>
                        <span class="uinput_spanbig" id="pwd_span_pwd_reset">请按照提示填写正确信息</span>
                        <div class="col-xs-3"><span id="pwd_reset_pwd_span"></span></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-3"></div>
                    <div class="inputlist inputlistok">
                        <span class="lefticon_s"></span>
                        <input type="password" class="col-xs-6 pwd" maxlength="12" id="pwd_reset_repwd" placeholder="确认密码"/>
                        <span class="uinput_spanbig" id="repwd_span_pwd_reset">请按照提示填写正确信息</span>
                        <div class="col-xs-3"><span id="pwd_reset_repwd_span"></span></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-3"></div>
                    <input class="col-xs-6 next_step" type="button" id="next_step3rd" value="完成"/>
                    <div class="col-xs-3"></div>
                </div>
            </form>


        </div>
        <div class=" col-sm-2 col-xs-1 pwd_right"></div>
    </div>
</section>
<div class="row" id="reset_footer">
    <div class="col-xs-3"></div>
    <div class="col-xs-6" id="pwd_reset_foot">
        <span>如遇到账户找回问题，请联系客服:</span>
        <span id="service_number">400-609-9933</span>
    </div>
    <div class="col-xs-3"></div>
</div>
<div class="gray_page" style="display:none;width: 2000px;height:2000px;filter:alpha(opacity=50);background-color: #222;opacity:0.5;position:fixed;left: 0;top: 0;right:0;bottom:0;z-index: 299;"></div>
<div class="dialog_pop" style="display:none;z-index:300;position:fixed;width:550px;height:166px;background-color:#fff;left:50%;margin-left:-275px;top:200px;">
    <div style="height:45px;background-color:#fafafa;border-bottom:1px solid #eee;">
        <span class="reminder" style="float:left;color:#333;height:45px;line-height:45px;margin-left:25px;font-size:18px;">提示</span>
        <span class="dialog_close_btn" style="display:inline-block;width:20px;height:20px;background:url('/images/close_button.png');float:right;margin:12px 12px;cursor:pointer;"></span>
    </div>
    <p style="height:60px;line-height:60px;color:#333;font-size:16px;" >
        <span style="display:inline-block;padding-left:35px;margin-left:25px;background:url('../images/pop_warning.png') no-repeat;height:25px;line-height:25px;" class="dialog_para">未知的错误发生了,请稍后再试~</span>
    </p>
    <div style="height:51px;background-color:#fafafa;border-top:1px solid #eee;">
        <input type="button" class="pop_sure_btn" style="width:65px;height:35px;background-color:#f9494c;color:#fff;margin:8px 20px 20px 460px;border:none;border-radius:5px;font-size:16px;" value="确认"/>
    </div>
</div>
<script src="{{asset('/js/jquery-1.11.3.js')}}"  xsdep="1"></script>
<script src="{{asset('/js/bootstrap.js')}}"  xsdep="1"></script>
<script src="{{asset('/js/xslib.js')}}"  xsdep="1"></script>
<script src="{{asset('/js/laddress.js')}}"  xsdep="1"></script>
<script src="{{asset('/js/passwordRefind.js')}}"  xsdep="1"></script>
<script src="{{asset('/js/loaddata.js')}}"  xsdep="1"></script>
<script src="{{asset('/js/usercenter.js')}}"  xsdep="1"></script>

<script>
    var g_cFindPwd = new CFindPwd();
    $(function () {
        g_cFindPwd.OnDocumentReady();

    });


</script>
</body>
</html>