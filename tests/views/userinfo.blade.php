<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta property="qc:admins" content="27155754347564603636" />
    <title>用户中心</title>
    <link href="{{asset('/css/bootstrap.css')}}" rel="stylesheet" ladep="1" >
    <script src="{{asset('/js/html5shiv.min.js')}}" ladep="1" ></script>
    <script src="{{asset('/js/respond.js')}}" ladep="1" ></script>
    <link href="{{asset('/images/logo-32-32.png')}}" rel="Shortcut Icon" type="image/x-icon">
    <link href="{{asset('/css/usercenter.css')}}" ladep="1" rel="stylesheet">
    <link href="{{asset('/css/jquery.Jcrop.min.css')}}" ladep="1" rel="stylesheet">
</head>
<body>
<input name="hidden_umid" class="js-hidden-umid" type="hidden">
<input id="js-hidden-user-phone-bind" type="hidden">
<input id="js-hidden-user-mail-bind" type="hidden">
<!--*****************************************************用户中心相关页面*********************************************************************-->
<div class="container" id="ucenter_header" name="uCenterPage">
    <div class="row">
        <div class="col-sm-10 col-xs-8"></div>
        <div class="col-sm-2 col-xs-4 row" id="exit">
            <a id="user_exit"><img src="../images/exit.gif"> 退出</a>
        </div>
    </div>
</div>

<!--************************************用户中心***************************************-->
<section id="main" class="container" name="user_center_page">
    <input id="js-hidden-imgid" type="hidden"/>
    <div class="row top">
        <div class="col-md-12"></div>
    </div>
    <div class="row">
        <div class="col-lg-2 col-md-1 "></div>
        <div class="col-lg-8 col-md-10 middle">
            <div class="row" id="user_massage">
                <div class="col-xs-2" id="user_picture_box">
                    <img id="user_picture" src="{{asset('/images/user_picture.png')}}"/>
                </div>
                <div class="col-xs-10" id="user_formation" >
                    <div id="user_name">
                        <span id="username"></span>
                        <a id="change_self"></a>
                    </div>
                    <div id="user_self_massage">
                        性别：<span id="sex"></span><span id="u_mid"></span><br/>
                        生日：<span id="birthday"></span>
                    </div>
                </div>
            </div>
            <div class="row" id="red_recode">
                <div class="col-xs-7 ">
                    <img src="{{asset('/images/warning.png')}}" alt=""/>
                    <span id="recode_span">敏感操作记录</span>
                </div>

                <span class="col-xs-2 " id="red_recode_span"></span>

                <div class="col-xs-3 " id="search">
                    <button id="search_all">查看全部</button>
                </div>
            </div>
            <div id="safe" class="row">
                <span class="col-xs-6 " id="safe_left">
                    安全保护
                </span>
                <div class="col-xs-6  row" id="safe_right">
                    <span id="safe_rank">安全级别：</span>
                    <span id="left_span"></span>
                    <span id="right_span"></span>
                    <span id="safe_rank_span">安全</span>
                </div>
            </div>
            <div class="row" id="way">
                <div class="col-sm-6 col-xs-12" id="way_left">
                    <p>
                        <span class="by_way">修改密码</span><br/>
                        <span class="recent_do" id="recent_pwd_reset">暂无修改密码记录</span>
                        <span id="change_pwd" class="change">修改</span>
                    </p>
                </div>
                <div class="col-sm-6 col-xs-12" id="way_middle">
                    <p>
                        <span class="by_way">绑定手机</span><br/>
                        <span class="recent_do" id="recent_phone_bind"></span>
                        <span id="change_phone" class="change">修改</span>
                        <a id="phone_bind_a" class="bind_a">未绑定，马上去绑定<span style="font-family:simsum;"> >></span></a>
                    </p>
                </div>

                <!--
                <div class="col-sm-4 col-xs-12" id="way_right">
                    <p>
                        <span class="by_way">绑定邮箱</span><br/>
                        <span class="recent_do" id="recent_mail_bind"></span>
                        <span id="change_bind_mail" class="change">修改</span>
                        <a id="mail_bind_a" class="bind_a">未绑定，马上去绑定<span style="font-family:simsum;"> >></span></a>
                    </p>
                </div>
                -->

            </div>
            <div id="login_recode" class="row">
                <div class="row" id="login_title">
                    <div class="col-sm-4 col-xs-12" id="login_title_left">
                        <span>账号访问历史</span>
                    </div>
                    <div class="col-sm-6 col-sm-pull-1 col-xs-12" id="login_title_middle">
                        <span>如确定非您本人登录，建议立即</span><span id="change_pwd_" class="change fix_pwd">修改密码</span>
                    </div>
                    <div class="col-sm-2 col-xs-12" id="login_title_right">
                        <span id="search_all_login" class="change">查看全部</span>
                    </div>
                </div>
                <table id="recode_table">
                    <tr id="table_thead">
                        <td>日期</td>
                        <td>地点</td>
                        <td>设备</td>
                        <td>IP</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-lg-2 col-md-1 "></div>
    </div>
    <div class="row bottom" id="ucenter_footer">
        <div class="col-md-2"></div>
        <div class="col-md-8" id="footer_a">
            <div class="row">
                <a href="javascript:0">关于我们</a>
                <span>|</span>
                <a href="javascript:0">商务洽谈</a>
                <span>|</span>
                <a href="javascript:0">联系我们</a>
                <span>|</span>
                <a href="javascript:0">版权保护</a>
                <span>|</span>
                <a href="javascript:0">使用帮助</a>
            </div>
            <div>
                <span>
                    Copyright&copy;2004-2016
                </span>
                <span>小说阅读网版权所有，京ICP证100530号京ICP备11018996号</span>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
</section>
<section id="change_self_box" class="box_hidden">
</section>
<!--******************用户修改个人信息pop**********************-->
<div id="change_self_pop" class="container pop_hidden" >
    <div class="row pop_close_btn">
        <span class="col-xs-12"><img src="../images/win_close.png"></span>
    </div>
    <!--**************************头像修改部分*****************333********-->
    <form>
        <div class="row" id="pop_user_picture">
            <div class="col-xs-2">头像</div>
            <div class="col-xs-10 row" id="before_change">
                <div class="col-xs-4" id="pop_user_picture_"><img id="user_img" style="width:108px;height:108px;" src="{{asset('/images/user_picture.png')}}" alt=""/></div>
                <div class="col-xs-8" id="file_box">
                   <span>100*100像素以上jpg&nbsp;或&nbsp;png，大小不超过2M</span>
                   <div id="click_up_file" class="clickpicupload">
                        点击上传头像
                        <input type="file" name="file" style="display:none;" id="file" onchange="loadImageFile();" >
                   </div>
                </div>
            </div>
        </div>
    </form>


<!--用户其它个人资料修改*********************************************************************-->
    <form >
       <!--<input type="hidden" name="_token" value="{{csrf_token()}}"/>-->
        <div class="row" id="pop_user_name">
            <div class="col-xs-2">昵称</div>
            <input class="col-xs-6" id="pop_username" maxlength="15" type="text"/>
            <div class="col-xs-4"></div>
        </div>
        <div class="row col-xs-offset-2" id="pop_user_name_require">
            <span >15字符以内</span>
            <span id="font_number"></span>
        </div>
        <div class="row" id="pop_user_sex">
            <div class="col-xs-2">性别</div>
            <div class="col-xs-10 row" id="sex_choice">
                <input type="radio" class="checkbox " name="sex" id="sex_input_man"/>
                <input type="radio" class="checkbox " name="sex" id="sex_input_woman"/>
                <span class="checkbox" id="sex_checkbox_man"></span>
                <span class="sex" id="sex_man_font">男</span>
                <span class="checkbox" id="sex_checkbox_woman"></span>
                <span class="sex" id="sex_woman_font">女</span>
            </div>
        </div>
        <div class="row" id="pop_user_birthday">
            <div class="col-xs-2">生日</div>
            <div class="col-xs-10 row">
                <div class="birthdiv" id="yearmodel">
                    <font id="yeartext"></font>
                    <div class="sel_year" > </div>
                </div>

                <div class="birthdiv" id="monthmodel">
                    <font id="monthtext">1</font>
                    <div class="sel_month" > </div>
                </div>

                <div class="birthdiv" id="daymodel">
                    <font id="daytext">1</font>
                    <div class="sel_day"> </div>
                </div>
            </div>
        </div>
        <div class="row" id="pop_user_submit">
            <input id="submit_save_change" type="button" value="保存修改"/>
            <input id="cancel" type="button" value="取消"/>
        </div>
    </form>
</div>
<!--用户头像修改*********************************************************************-->

    <div id="avatar_editor_overlay" class="window-overlay" style="display:none;">
         <div id="avatar_editor" class="avatar-editor-container editor-page round pop-over clearfix" >
           <div class="avatar-real-image round pop-overs">
               <img src="/images/gray.png" class="" width="465" height="410" border="0" id="real_img">
           </div>
           <div class="avatar-preview-image round pop-overs">
               <img src="/images/gray.png" class="jcrop-preview-image round pop-overs" width="465" height="410" border="0">
           </div>
           <div class="avatar-image-text">头像缩略图</div>
               <a class="close-avatar js-close-avatar-editor cursor-pointer hide">
                   <span class="app-icon pageprev-icon"></span>
               </a>
           <div class="avatar-editor-buttons">
             <div id="avatar_uploader">
             </div>
             <a class="js-cancel-avatar-changes cool-button grayd long rounds pop-overs cursor-pointer" style="float:right;margin:15px 10px 0 0;">取消</a>
             <a class="js-save-avatar-changes cool-button longb rounds pop-overs cursor-pointer disabled" style="float:right;margin:15px 5px 0 0;" disabled="disabled">保存</a>
              <div class="spinner f0f0f0small js-save-avatar-spinner" ></div>
           </div>
         </div>
      </div>
<!--****************敏感操作pop**********************-->
<section class="container" id="pop_red_recode">
    <div class="row top">
        <div class="col-md-12"></div>
    </div>
    <div class="row">
        <div class="col-md-2 col-xs-1 left"></div>
        <div class="col-md-8 col-xs-10 middle" id="pop_recode_main">

      <div class="row" id="search_all_span">
                      <div class="col-xs-12" id="span_left">
                          敏感操作记录
                          <div  id="span_right" style=" position:relative;top:-45px; right:-4%; "></div>
                      </div>

                  </div>
            <table id="pop_recode_table">
                <tr id="pop_thead">
                    <td>日期</td>
                    <td>操作</td>
                    <td>设备</td>
                    <td>地点</td>
                </tr>
            </table>
            <span id="pop_notice"></span>
            <input id="return" type="button" value="返回个人中心"/>
        </div>
        <div class="col-md-2 col-xs-1 right"></div>
    </div>
</section>
<!--****************账号访问历史pop**********************-->
<section id="pop_search_all_login" class="container">
    <div class="row top">
        <div class="col-md-12"></div>
    </div>
    <div class="row">
        <div class="col-md-2 col-xs-1 left"></div>
        <div class="col-md-8 col-xs-10 middle" id="pop_login_recode_main">
            <div class="row" id="search_login_all_span">
                <div class="col-sm-4 col-xs-12" id="span_recode_left">
                    账号访问历史
                </div>
                <div class="col-sm-8 col-sm-pull-1 col-xs-12" id="span_recode_middle">
                    <span>如确定非您本人登陆建议您立即</span>
                    <span class="change fix_pwd" id="login_recode_fix_pwd">修改密码</span>
                    <div  id="span_recode_right" style=" position:relative;top:-45px; right:-4%;width:50px;float:right;top:0;"></div>
                </div>
            </div>

            <table id="pop_login_recode_table">
                <tr id="pop_login_thead">
                    <td>日期</td>
                    <td>地点</td>
                    <td>设备</td>
                    <td>ip</td>
                </tr>
            </table>
            <span id="pop_login_notice"></span>
            <input id="pop_return" type="button" value="返回个人中心"/>
        </div>
        <div class="col-md-2 col-xs-1 right"></div>
    </div>
</section>
<!--**************已绑定手机号情况下的修改密码pop******************-->
<div id="change_pwd_box">
    <input type="hidden" id="js-hidden-verify-token"/>
    <!--<input type="hidden" name="_token" value="{{csrf_token()}}"/>-->
    <div class="pop_close_btn">
        <span id="change_pwd_box_close"><img src="../images/win_close.png"></span>
    </div>
    <div id="pop_pwd_notice">
        <span id="change_pwd_span">修改密码</span>
        <span id="change_pwd_notice">请验证您的绑定信息，进行密码修改</span><br/>
    </div>
    <div id="pop_phone_bind">
        <span class="noticeSpan" id="change_pwd_bind_massage">已绑定号码</span>
        <span id="binded_phone_number"></span>
    </div>
    <div id="pop_ver">
        <span class="noticeSpan">验证码</span>
        <input type="text" id="pop_ver_input"/>
        <input type="button" id="pop_ver_btn" value="获取验证码"/>
    </div>
    <p class="user_notice" id="pwd_reset_bind_notice"></p>
    <input id="pop_next_step" type="button" value="下一步"/>
    <input id="fix_pop_next_step" type="button" value="下一步"/>
</div>
<!--******************无绑定修改密码pop*****************-->
<form id="change_pwd_box_" >
    <!--<input type="hidden" name="_token" value="{{csrf_token()}}"/>-->
    <div class="pop_close_btn">
        <span id="change_pwd_box_close_"><img src="../images/win_close.png"></span>
    </div>
    <span id="change_pwd_span_">修改密码</span>
    <div id="new_pwd" class="inputlist">
        <span id="new_pwd_span" class="noticeSpan">新密码</span>
        <input type="password" required id="new_pwd_input"  maxlength="12" minlength="6" placeholder="6-12位字母数字密码"/>
        <span class="uinput_spanbig" id="pwd_span_userinfo"></span>
    </div>
    <div id="re_pwd" class="inputlist">
        <span id="re_pwd_span" class="noticeSpan">确认密码</span>
        <input type="password" required id="re_pwd_input" maxlength="12" minlength="6"  placeholder="请与密码保持一致"/>
        <span class="uinput_spanbig"  id="repwd_span_userinfo"></span>
    </div>
    <p class="user_notice" id="pwd_reset_direct_notice"></p>
    <input id="pop_submit" type="button"  value="完成"/>
</form>
<!--******************用户绑定邮箱pop**************-->
<form id="bind_mail_box" >
    <!--<input type="hidden" name="_token" value="{{csrf_token()}}"/>-->
    <div class="pop_close_btn">
        <span id="bind_mail_box_close"><img src="../images/win_close.png"></span>
    </div>
    <div id="bind_mail_notice">
        <span id="bind_mail_span">绑定邮箱</span>
        <span id="bind_notice">该帐号尚未绑定任何邮箱</span><br/>
    </div>
    <!--
    <p id="enter_mail_p" style='text-align: center;display:none;margin-left:25px;margin-top:10px;'>
    <span>验证码已发入您的邮箱，</span><a id='enter_mail_get_ver_a' style='cursor:pointer;'>立即进入邮箱</a></p>
    -->
    <div id="pop_mail_bind" class="inputlist">
        <span class="noticeSpan" id="bind_mail">邮箱</span>
        <input id="bind_mail_input" required  type="text" maxlength="30" placeholder="邮箱"/>
        <span class="uinput_spanbig" id="mail_span_userinfo"></span>
    </div>
    <div id="pop_mail_ver">
        <span class="noticeSpan">验证码</span>
        <input type="text" required id="pop_mail_ver_input"  placeholder="验证码"/>
        <!--<img id="mail_ver_img" src=""/>-->
        <input type="hidden" id="js-mail-bind-hidden-genenry-input"/>
        <input type="button" maxlength="6" id="pop_mail_ver_btn" value="获取验证码"/>
    </div>
    <p class="user_notice" id="mail_bind_notice"></p>
    <input id="pop_mail_next_step" type="button" value="完成"/>
</form>
<!--********************************************用户绑定手机pop**************************************************-->
<form id="bind_phone_box" >
    <!--<input type="hidden" name="_token" value="{{csrf_token()}}"/>-->
    <div class="pop_close_btn">
        <span id="bind_phone_box_close_btn"><img src="../images/win_close.png"></span>
    </div>
    <div id="bind_phone_notice">
        <span id="bind_phone_span">绑定手机</span>
        <span id="phone_bind_notice">该帐号尚未绑定任何手机</span><br/>
    </div>
    <div id="pop_phone_bind" class="inputlist">
        <span class="noticeSpan" id="bind_phone">手机</span>
        <input id="bind_phone_input" required type="text" maxlength="20"  placeholder="手机"/>
        <span class="uinput_spanbig"  id="phone_span_userinfo"></span>
    </div>
    <div id="pop_phone_ver">
        <span class="noticeSpan">验证码</span>
        <input type="text" required maxlength="6" id="pop_phone_ver_input"  placeholder="验证码"/>
        <input type="button" id="pop_phone_ver_btn" value="获取验证码">
    </div>
    <p class="user_notice" id="user_phone_bind_notice"></p>
    <input id="pop_phone_finish_step" type="button" value="完成"/>
</form>
<!--*****************************解绑邮箱pop***************************************-->
<form id="pop_mail_unbind_box">
    <!--<input type="hidden" name="_token" value="{{csrf_token()}}"/>-->
    <div class="pop_close_btn">
        <span id="unbind_mail_box_close"><img src="../images/win_close.png"></span>
    </div>
    <div id="bind_mail_notice_">
        <span id="bind_mail_span_">解绑邮箱</span>
    </div>
    <div id="binded_mail_number_span">
        <span id="binded_mail_span">
            已绑定邮箱
        </span>
        <span id="binded_mail_number_"></span>
    </div>
    <p id="unbind_mail_notice">
        如果要替换请先解除当前绑定状态
    </p>
    <input id="pop_mail_next_step_"  type="button" value="解绑邮箱">
</form>
<!--***********************************解绑邮箱第二步*********************************************-->
<div id="mail_unbind_test_box" >
    <!--<input type="hidden" name="_token" value="{{csrf_token()}}"/>-->
    <div class="pop_close_btn">
            <span id="unbind_mail_test_close">x</span>
    </div>
    <div id="bind_mail_test_notice">
            <span id="bind_test_mail_span">解绑邮箱</span>
    </div>
     <p class="user_notice" id="mail_unbind_pop_notice"></p>
     <div id="binded_mail_number_span_">
             <span id="binded_mail_ver_span">已发送验证码至</span>
             <span id="binded_mailnumber"></span>
     </div>
     <div id="binded_mailnumber_">
             <span>验证码</span>
             <input id="binded_mailnumber_input" required maxlength="6" type="text"/>
             <input id="binded_mailnumber_btn" type="button" value="获取验证码"/>
     </div>
         <!--
         <p id="enter_email_p_" style='text-align: center;display:none;margin-left:30px;margin-top:15px;margin-bottom:15px;'><span>验证码已发入您的邮箱，</span><a id='enter_email_get_ver_a_' style='cursor:pointer;'>立即进入邮箱</a></p>
         <p class="user_notice" id="user_mail_unbind_pop_notice"></p>
         -->
         <input id="pop_mail_unbind_next_step_" type="button" value="完成"/>


</div>
<!--*****************************解绑手机号pop**********************************-->
<div id="pop_phone_unbind_box">
    <!--<input type="hidden" name="_token" value="{{csrf_token()}}"/>-->
    <div class="pop_close_btn">
        <span id="bind_phone_box_close"><img src="../images/win_close.png"></span>
    </div>
    <div id="bind_phone_notice">
        <span id="bind_phone_span">解绑手机</span>
    </div>
    <div id="binded_phone_number_span">
        <span id="binded_phone_span">
            已绑定手机号
        </span>
        <span id="binded_phone_number_"></span>
    </div>
    <p id="unbind_phone_notice">
        如果要替换请先解除当前绑定状态
    </p>
    <button id="pop_phone_next_step" type="button">解绑手机号</button>
</div>
<!--*********************解绑发送验证码部分***********************-->
<div id="pop_phone_ver_box">
    <!--<input type="hidden" name="_token" value="{{csrf_token()}}"/>-->
    <div class="pop_close_btn">
        <span id="bind_phone_box_close_"><img src="../images/win_close.png"></span>
    </div>
    <div id="bind_phone_notice_">
        <span id="bind_phone_span_">解绑手机</span>
    </div>
    <p class="user_notice" id="phone_unbind_pop_notice"></p>
    <div id="binded_phone_number_span_">
        <span id="binded_ver_span">已发送验证码至</span>
        <span id="binded_phonenumber"></span>
    </div>
    <div id="binded_phonenumber_">
        <span>验证码</span>
        <input id="binded_phonenumber_input" maxlength="6" required type="text"/>
        <input id="binded_phonenumber_btn" type="button" value="重新获取验证码"/>
    </div>
    <p class="user_notice" id="phone_unbind_pop_notice"></p>
    <input id="pop_next_step_" type="button" value="完成"/>
</div>
<div class="gray_page" style="display:none;width: 2000px;height:2000px;background-color: #222;filter:alpha(opacity=50);opacity:0.5;position:fixed;left: 0;top: 0;right:0;bottom:0;z-index:10000;"></div>
<div class="dialog_pop" style="display:none;z-index:10001;position:fixed;width:550px;height:166px;background-color:#fff;left:50%;margin-left:-275px;top:200px;">
    <div style="height:45px;background-color:#fafafa;border-bottom:1px solid #eee;">
        <span class="reminder" style="float:left;color:#333;height:45px;line-height:45px;margin-left:25px;font-size:18px;">提示</span>
        <span class="dialog_close_btn" style="display:inline-block;width:20px;height:20px;background:url('/images/close_button.png');float:right;margin:12px 12px;cursor:pointer;"></span>
    </div>
    <p style="height:65px;line-height:65px;color:#333;font-size:16px;" >
        <span style="display:inline-block;padding-left:35px;margin-left:25px;background:url('../images/pop_warning.png') no-repeat;height:25px;line-height:25px;" class="dialog_para">未知的错误发生了,请稍后再试~！</span>
    </p>
    <div style="height:51px;background-color:#fafafa;border-top:1px solid #eee;">
        <input type="button" class="pop_sure_btn" style="width:65px;height:35px;background-color:#f9494c;color:#fff;margin:8px 20px 20px 460px;border:none;border-radius:5px;font-size:16px;" value="确认"/>
    </div>
</div>
<!--<script src="{{asset('/js/jquery.js')}}"></script>-->
<script src="{{asset('/js/jquery-1.11.3.js')}}" ladep="1" ></script>
<script src="{{asset('/js/bootstrap.js')}}" ladep="1"></script>
<script src="{{asset('/js/bootstrap.js')}}" ladep="1"></script>
<script src="{{asset('/js/usercenter.js')}}" ladep="1"></script>
<script src="{{asset('/js/loaddata.js')}}" ladep="1"></script>
<script src="{{asset('/js/xslib.js')}}" ladep="1"></script>
<script src="{{asset('/js/llib.js')}}" ladep="1"></script>
<script src="{{asset('/js/laddress.js')}}" ladep="1"></script>
<script src="{{asset('/js/fileuploader-2.0b.js')}}" ladep="1"></script>
<script src="{{asset('/js/ladeptar.js')}}" ladep="1"></script>
<script src="{{asset('/js/birthday.js')}}" ladep="1"></script>
<script src="{{asset('/js/jquery.Jcrop.js')}}" ladep="1"></script>
<script>
    var g_CLAvatar = new CLAvatar( null );
    var g_cUserCenter = new CUserCenter();
    $(function ()
    {
        g_cUserCenter.OnDocumentReady();
        g_CLAvatar.OnDocumentReady();
    });
     $.ms_DatePicker();
</script>
</body>
</html>