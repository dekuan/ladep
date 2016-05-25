<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>手机小说阅读网</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="keywords" content="手机小说阅读网">
    <meta name="description" content="手机小说阅读网,小说手机阅读。手机小说阅读网提供最新章节首发">
    <meta name="format-detection" content="telphone=no, email=no"/>
    <meta name="screen-orientation"content="portrait">
    <meta name="x5-orientation"content="portrait">
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <link href="{{asset('/wap/css/style.css')}}" rel="stylesheet" type="text/css" lava="1">
    <link href="{{asset('/wap/css/common.css')}}" rel="stylesheet" type="text/css" lava="1">
    <link href="{{asset('/wap/css/main.css')}}" rel="stylesheet" type="text/css" lava="1">
    <link href="{{asset('/wap/images/logo-114-114.png')}}" rel="apple-touch-icon">
    <link href="{{asset('/wap/images/logo-32-32.png')}}" rel="Shortcut Icon" type="image/x-icon">
    <base target="_self">
</head>
<body>
<section>
    <h1 id="title"><a href="javascript:history.back(-1)" target="_self"><img src="{{asset('/wap/images/return_left.png')}}"/></a><span>用户信息</span></h1>
    <ul id="main_ul">
        <li id="user_picture">
            <a href="#">
                <span class="left_a">头像</span>

                <span class="user_picter_span" style="border-radius:100%; float:right; overflow:hidden; border:3px solid #ddd; width:4rem; height:4rem; display:inline-block;
                margin-top:0.75rem;"><img class="right_a" id="user_picture_out" style="border:none; width:100%; height:100%; height:none; margin-top:0px;margin-left:4px;-webkit-border-radius:100%;"/></span>
            </a>
        </li>
        <li id="user_nickname">
            <a href="/web/cnick" target="_self">
                <span class="left_a">昵称</span>
                <span class="right_a" id="u_nickname"></span>
            </a>
        </li>
        <li id="user_gender">
            <a  href="#">
                <span class="left_a">性别</span>
                <span class="right_a" id="u_gender"></span>
            </a>
        </li>
        <li id="user_birthday">
            <a href="#">
                <span class="left_a">生日</span>
                <span class="right_a" id="u_birthday"></span>
            </a>
        </li>
         <li id="user_id">
                            <a href="#" style="background:none">
                                <span class="left_a">ID</span>
                                <span class="right_a" id="u_id" ></span>
                            </a>
                        </li>
        <li id="mobile_bind">
            <a href="/web/bind" target="_self" id="phone_bind">
                <span class="left_a" id="bindorunbind">绑定手机</span>
                <span class="right_a" id="u_phone">未绑定</span>
            </a>
        </li>
        <!--
        <li id="email_bind">
            <a href="/web/bind" target="_self">
                <span class="left_a">绑定邮箱</span>
                <span class="right_a" id="u_mail">未绑定</span>
            </a>
        </li>
        -->
        <li id="user_pwd_reset">
            <a href="/web/pwdmodify" target="_self">
                <span class="left_a">修改密码</span>
                <span class="right_a" id="u_pwdreset"></span>
            </a>
        </li>
        <li id="logout">
            <a href="javascript:0" target="_self" class="logout">
                退出
            </a>
        </li>
    </ul>

    <div id="button_list_box">
        <div class="gray_page"></div>
        <div id="btn_list">
            <a  href="#" class="red_btn" id="first_btn">男</a>
            <a  href="#" class="red_btn" id="second_btn">女</a>
            <a  href="#" class="cancel_btn" id="cancel_btn">取&nbsp;&nbsp;&nbsp;消</a>
        </div>
    </div>


    <div class="demo">
    	<div class="lie"><a href="###" id="beginTime" class="kbtn"></a></div>
    </div>
    <div id="datePlugin"></div>

     <div id="file_upload">
      <h1><a id="picture_return"><img src="{{asset('/wap/images/return_left.png')}}"/></a><span>头像设置</span></h1>
     <div id="avatar_editor">

              <div id="gray_page_picture"></div>
              <div id="middle">
                  <img id="avatar-img-top"/>
              </div>
         <div class="avatar-real-image round pop-overs">
             <img id="real_img"/>
         </div>
         <div id="avatar_uploader">
         </div>
         <div class="avatar-editor-buttons">
             <a id="save_btn" class="red_btn">完&nbsp;&nbsp;成</a>
         </div>
     </div>
     </div>
     <div class='ajax_aplly_error'></div>
     <div class="horizontal" style="display:none;width:100%;height:100%;">
         <div id="notice_picture">
             <img src="{{asset('/wap/images/fail_notice.png')}}"/>
         </div>
         <div id="notice_span">
             <span id="fail_notice_span">很抱歉，</span><br/>
             <span>暂时不支持横屏哦~</span>
         </div>
     </div>
</section>
<style type="text/css">
.demo{width:100%;height:2.75rem;position:relative;top:-12.5rem;}
.demo .lie{margin:0 0 20px 0;}
</style>
<script src="{{asset('/wap/js/jquery-1.11.3.js')}}" lava="1"></script>
<script src="{{asset('/wap/js/jquery.mobile.custom.js')}}" lava="1"></script>
<script src="{{asset('/wap/js/jquery.touchSwipe.min.js')}}" lava="1"></script>
<script src="{{asset('/wap/js/userinfo.js')}}" lava="1"></script>
<script src="{{asset('/wap/js/mavatar.js')}}" lava="1"></script>
<script src="{{asset('/js/fileuploader-2.0b.js')}}" lava="1"></script>
<script src="{{asset('/js/llib.js')}}" lava="1"></script>
<script src="{{asset('/wap/js/userinfo_loaddata.js')}}" lava="1"></script>
<script src="{{asset('/wap/js/date.js')}}" lava="1"></script>
<script src="{{asset('/wap/js/iscroll.js')}}" lava="1"></script>

<script>
    var  g_cAccountCenter=new CAccountCenter();
    var  g_cMAvatar = new CLAvatar( null );
     $("li").on("vmousedown",function(){
                        $(this).css("backgroundColor","#f9f9f9");
                    });
                    $(" li").on("vmouseup",function(){
                        $(this).css("backgroundColor","#fff");
                    });
    $(function(){
               g_cAccountCenter.OnDocumentReady();
               g_cMAvatar.OnDocumentReady();
               $('#beginTime').date();
           });
</script>
</body>
</html>
