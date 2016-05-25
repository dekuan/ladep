<?php

$domain = 'http://' . $_SERVER['SERVER_NAME'];

?>

<html>

<head>

    <script>

        function startTest(urlid, formid) {

            document.getElementById(formid).action = document.getElementById(urlid).innerText;

            ;

            document.getElementById(formid).submit();

        }

    </script>

</head>

<body>



<table width="100%">

    <tr>

        <td><h2 id="u_name转换u_mid,分表信息查询"></h2></td>

    </tr>

    <form id="maininfoform" action="" method='POST' target='maininfo'>

        <tr>

            <td><b>1,u_name,u_mid,分表信息查询</b></td>

        </tr>

        <tr>

            <td>请求方式POST请求</td>

        </tr>

        <tr>

            <td>

                <div><span>接口地址:</span><span id="maininfourl">http://api-account.xs.cn/maininfo</span></div>

            </td>

        </tr>

        <tr>

            <td>u_mid<input type='text' size='15' name='u_mid'/></td>

        </tr>

        <tr>

            <td>u_name<input type='text' size='15' name='u_name'/></td>

        </tr>

        <tr>

            <td width="100%">

                <iframe width="40%" name="maininfo" scrolling="auto"></iframe>

            </td>

        </tr>

        <tr>

            <td><input type='button' value='确认'

                       onclick="startTest('maininfourl','maininfoform')"/></td>

        </tr>

        <tr height="20px"></tr>

        <tr>

            <td><a href="#pwd_reset">密码重置</a></td>

        </tr>

        <tr>

            <td><a href="#account_mobile">返回手机注册登录</a></td>

        </tr>

        <tr>

            <td><a href="#account_mail">返回邮箱注册登录</a></td>

        </tr>

        <tr>

            <td>

                <hr/>

            </td>

        </tr>



    </form>



    <tr>

        <td><h2 id="删除用户信息（测试用）"></h2></td>

    </tr>

    <form id="delmaininfoform" action="" method='POST' target='delmaininfo'>

        <tr>

            <td><b>删除用户信息（测试用）</b></td>

        </tr>

        <tr>

            <td>请求方式POST请求</td>

        </tr>

        <tr>

            <td>

                <div><span>接口地址:</span><span id="delmaininfourl">http://api-account.xs.cn/delmaininfo</span></div>

            </td>

        </tr>

        <tr>

            <td>u_name<input type='text' size='15' name='u_name'/></td>

        </tr>

        <tr>

            <td width="100%">

                <iframe width="40%" name="delmaininfo" scrolling="auto"></iframe>

            </td>

        </tr>

        <tr>

            <td><input type='button' value='确认'

                       onclick="startTest('delmaininfourl','delmaininfoform')"/></td>

        </tr>

        <tr>

            <td>

                <hr/>

            </td>

        </tr>



    </form>



    <tr></tr>

    <tr>

        <td><h2 id="account_mobile">手机注册登录</h2></td>

    </tr>

    <form id="sendmobileform" action="" method='POST' target='send_mobile'>

        <tr>

            <td><b>1,手机校验码发送</b></td>

        </tr>

        <tr>

            <td>请求方式POST请求</td>

        </tr>

        <tr>

            <td>

                <div><span>接口地址:</span><span id="sendmobileurl"><?php echo $domain ?>/api/mobileverification</span>

                </div>

            </td>

        </tr>

        <tr>

            <td>手机号<input type='text' size='15' name='u_name'/></td>

        </tr>

        <tr>

            <td>

                验证码类型

                <select name="u_usetype">

                    <option value="1">注册使用</option>

                    <option value="2">激活使用</option>

                    <option value="3">重置密码使用</option>

                    <option value="4">登录使用</option>

                    <option value="5">绑定</option>

                    <option value="6">解绑</option>

                    <option value="7">修改密码</option>

                </select>

            </td>

        </tr>

        <tr>

            <td width="100%">

                <iframe width="40%" name="send_mobile" scrolling="auto"></iframe>

            </td>

        </tr>

        <tr>

            <td><input type='button' value='确认'

                       onclick="startTest('sendmobileurl','sendmobileform')"/></td>

        </tr>

    </form>



    <form id='mailsendverifyform' action='' method='POST' target='mailsendverify'>

        <tr>

            <td><b>2,邮箱校验信息发送</b></td>

        </tr>

        <tr>

            <td>请求方式POST请求</td>

        </tr>

        <tr>

            <td><span>接口地址：</span><span id="mailsendverifyurl"><?php echo $domain ?>/api/mailverification</span>

            </td>

        </tr>



        <tr>

            <td>邮箱<input type='text' size='15' name='u_name'/></td>

        </tr>

        <tr>



            <td>

                验证码类型

                <select name="u_usetype">

                    <option value="1">注册使用</option>

                    <option value="2">激活使用</option>

                    <option value="3">重置密码使用</option>

                    <option value="4">登录使用</option>

                    <option value="5">绑定</option>

                    <option value="6">解绑</option>

                    <option value="7">修改密码</option>

                </select>

            </td>



        </tr>

        <tr>

            <td width="100%">

                <iframe width="40%" name="mailsendverify" scrolling="auto"></iframe>

            </td>

        </tr>

        <tr>

            <td><input

                        type='button' value='确认' onclick="startTest('mailsendverifyurl','mailsendverifyform')"/></td>

        </tr>

    </form>



    <form id='captchaform' action='' method='POST' target='captcha'>

        <tr>

            <td><b>3,校验码输出</b></td>

        </tr>

        <tr>

            <td>请求方式POST请求</td>

        </tr>

        <tr>

            <td><span>接口地址：</span><span id="captchaurl"><?php echo $domain ?>/api/captcha</span>

            </td>

        </tr>



        <tr>

            <td width="100%">

                <iframe width="40%" name="captcha" scrolling="auto"></iframe>

            </td>

        </tr>

        <tr>

            <td><input

                        type='button' value='确认' onclick="startTest('captchaurl','captchaform')"/></td>

        </tr>

    </form>





    <tr height="20px"></tr>

    <form id="regform2" action="" method='POST' target='reg2'>

        <tr>

            <td><b>2,手机用户注册</b></td>

        </tr>

        <tr>

            <td>请求方式POST请求</td>

        </tr>

        <tr>



            <td><span>接口地址：</span><span id="regurl2"><?php echo $domain ?>/api/register</span>

        </tr>

        <tr>

            <td>手机号码<input type='text' size='15' name='u_name'/></td>

        </tr>

        <tr>

            <td>密码<input type='text' size='15' name='u_pwd'/></td>

        </tr>

        <tr>

            <td>确认密码<input type='text' size='15' name='u_pwd2'/></td>

        </tr>

        <tr>

            <td>验证码<input type='text' size='15' name='u_vcode'/></td>

        </tr>

        <tr>

            <td width="100%">

                <iframe width="40%" name="reg2" scrolling="auto"></iframe>

            </td>

        </tr>

        <tr>

            <td><input style="height: 100px;" type='button' value='确认'

                       onclick="startTest('regurl2','regform2')"/></td>

        </tr>

    </form>



    <tr height="20px"></tr>

    <tr>

        <td><h2 id="account_mail">邮箱注册登录</h2></td>

    </tr>

    <form id="regform" action="" method='POST' target='reg'>

        <tr>

            <td><b>4,邮箱用户注册</b></td>

        </tr>

        <tr>

            <td>请求方式POST请求</td>

        </tr>

        <tr>



            <td><span>接口地址：</span><span id="regurl"><?php echo $domain ?>/api/register</span>

        </tr>

        <tr>

            <td>邮箱<input type='text' size='15' name='u_name'/></td>

        </tr>

        <tr>

            <td>密码<input type='text' size='15' name='u_pwd'/></td>

        </tr>

        <tr>

            <td>确认密码<input type='text' size='15' name='u_pwd2'/></td>

        </tr>

        <tr>

            <td>验证码<input type='text' size='15' name='u_vcode'/></td>

        </tr>

        <tr>

            <td>校验密文<input type='text' size='15' name='u_genenry'/></td>

        </tr>

        <tr>

            <td width="100%">

                <iframe width="40%" name="reg" scrolling="auto"></iframe>

            </td>

        </tr>

        <tr>

            <td><input style="height: 100px;" type='button' value='确认'

                       onclick="startTest('regurl','regform')"/></td>

        </tr>

    </form>





    <tr>

        <td><a href="#confirm_login">验证是否登录</a></td>

    </tr>

    <tr>

        <td>

            <hr/>

        </td>

    </tr>





    <tr height="20px"></tr>

    <form id='mailverificationform' action='' method='GET' target='mailverification'>

        <tr>

            <td><b>6,邮箱用户激活</b></td>

        </tr>

        <tr>

            <td>请求方式GET请求</td>

        </tr>

        <tr>

            <td><span>接口地址：</span><span id="mailverificationurl"><?php echo $domain ?>/api/mailverification</span>

            </td>

        </tr>

        <tr>

            <td>用户u_mid<input type='text' size='15' name='u_mid'/></td>

        </tr>

        <tr>

            <td>激活码<input type='text' size='15' name='u_vcode'/></td>

        </tr>

        <tr>

            <td width="100%">

                <iframe width="40%" name="mailverification" scrolling="auto"></iframe>

            </td>

        </tr>

        <tr>

            <td><input type='button' value='确认'

                       onclick="startTest('mailverificationurl','mailverificationform')"/></td>

        </tr>

    </form>



    <tr height="20px"></tr>

    <form id='loginform' action='' method='POST' target='login'>

        <tr>

            <td><b>7,用户登录</b></td>

        </tr>

        <tr>

            <td>请求方式POST请求</td>

        </tr>

        <tr>

            <td><span>接口地址：</span><span id="loginurl"><?php echo $domain ?>/api/login</span>

            </td>

        </tr>

        <tr>

            <td>用户名<input type='text' size='15' name='u_name'/></td>

        </tr>

        <tr>

            <td>密码<input type='text' size='15' name='u_pwd'/></td>

        </tr>

        <tr>

            <td>返回cookie类型<input type='text' size='15' name='u_ctype'/></td>

        </tr>

        <tr>

            <td>是否长连接<input type='text' size='15' name='u_keep'/></td>

        </tr>

        <tr>

            <td width="100%">

                <iframe width="40%" name="login" scrolling="auto"></iframe>

            </td>

        </tr>

        <tr>

            <td><input style="height: 100px;" type='button' value='确认'

                       onclick="startTest('loginurl','loginform')"/></td>

        </tr>

    </form>





    <tr height="20px"></tr>

    <form id='isloginform' action='' method='POST' target='islogin'>

        <tr>

            <td><b>登录校验</b></td>

        </tr>

        <tr>

            <td>请求方式POST请求</td>

        </tr>

        <tr>

            <td><span>接口地址：</span><span id="isloginurl"><?php echo $domain ?>/api/islogin</span>

            </td>

        </tr>

        <tr>

            <td width="100%">

                <iframe width="40%" name="islogin" scrolling="auto"></iframe>

            </td>

        </tr>

        <tr>

            <td><input style="height: 100px;" type='button' value='确认'

                       onclick="startTest('isloginurl','isloginform')"/></td>

        </tr>

    </form>

    <tr height="20px"></tr>

    <form id='anonymousform' action='' method='POST' target='anonymous'>

        <tr>

            <td><b>匿名登录</b></td>

        </tr>

        <tr>

            <td>请求方式POST请求</td>

        </tr>

        <tr>

            <td><span>接口地址：</span><span id="anonymousurl"><?php echo $domain ?>/api/anonymous</span>

            </td>

        </tr>

        <tr>

            <td width="100%">

                <iframe width="40%" name="anonymous" scrolling="auto"></iframe>

            </td>

        </tr>

        <tr>

            <td><input style="height: 100px;" type='button' value='确认'

                       onclick="startTest('anonymousurl','anonymousform')"/></td>

        </tr>

    </form>

    <tr height="20px"></tr>

    <form id='logoutform' action='' method='POST' target='logout'>

        <tr>

            <td><b>用户退出</b></td>

        </tr>

        <tr>

            <td>请求方式POST请求</td>

        </tr>

        <tr>

            <td><span>接口地址：</span><span id="logouturl"><?php echo $domain ?>/api/logout</span>

            </td>

        </tr>

        <tr>

            <td width="100%">

                <iframe width="40%" name="logout" scrolling="auto"></iframe>

            </td>

        </tr>

        <tr>

            <td><input style="height: 100px;" type='button' value='确认'

                       onclick="startTest('logouturl','logoutform')"/></td>

        </tr>

    </form>



    <tr>

        <td><a href="#confirm_login">验证是否登录</a></td>

    </tr>



    <tr height="20px"></tr>

    <form id='hasuserform' action='' method='POST' target='hasuser'>

        <tr>

            <td><b>8,验证用户是否不存在</b></td>

        </tr>

        <tr>

            <td>请求方式POST请求</td>

        </tr>

        <tr>

            <td><span>接口地址：</span><span id="hasuserurl"><?php echo $domain ?>/api/username</span>

            </td>

        </tr>

        <tr>

            <td>用户名<input type='text' size='15' name='u_name'/></td>

        </tr>

        <tr>

            <td width="100%">

                <iframe width="40%" name="hasuser" scrolling="auto"></iframe>

            </td>

        </tr>

        <tr>

            <td><input style="height: 100px;" type='button' value='确认'

                       onclick="startTest('hasuserurl','hasuserform')"/></td>

        </tr>

    </form>

    <tr>

        <td><a href="#confirm_login">验证是否登录</a></td>

    </tr>

    <tr>

        <td>

            <hr/>

        </td>

    </tr>

    <tr height="20px"></tr>



    <form id='applyform' action='' method='POST' target='apply'>

        <tr>

            <td><b>9,找回密码申请</b></td>

        </tr>

        <tr>

            <td>请求方式POST请求</td>

        </tr>

        <tr>

            <td><span>接口地址：</span><span id="applyurl"><?php echo $domain ?>/api/password</span>

            </td>

        </tr>



        <tr>

            <input type='hidden' size='15' name='u_action' value="reset"/>

            <input type='hidden' size='15' name='u_step' value="apply"/>

            <td>用户名<input type='text' size='15' name='u_name'/></td>

        </tr>

        <tr>

            <td>校验码<input type='text' size='15' name='u_vcode' value=""/></td>

        </tr>

        <tr>

            <td>密文<input type='text' size='15' name='u_genenry' value=""/></td>

        </tr>

        <tr>

            <td>

                用户类型

                <select name="u_usertype">

                    <option value="0">默认</option>

                    <option value="1">邮箱用户</option>

                    <option value="2">手机用户</option>

                </select>

            </td>

        </tr>

        <tr>

            <td width="100%">

                <iframe width="40%" name="apply" scrolling="auto"></iframe>

            </td>

        </tr>

        <tr>

            <td><input

                        type='button' value='确认' onclick="startTest('applyurl','applyform')"/></td>

        </tr>

    </form>



    <tr height="20px"></tr>

    <form id='verifyform' action='' method='POST' target='verify'>

        <tr>

            <td><b>10,找回密码校验</b></td>

        </tr>

        <tr>

            <td>请求方式POST请求</td>

        </tr>

        <tr>

            <td><span>接口地址：</span><span id="verifyurl"> <?php echo $domain ?>/api/password</span>

            </td>

        </tr>



        <tr>

            <input type='hidden' size='15' name='u_action' value="reset"/>

            <input type='hidden' size='15' name='u_step' value="verify"/>

            <td>用户名<input type='text' size='15' name='u_name'/></td>

        </tr>

        <tr>

            <td>验证码<input type='text' size='15' name='u_vcode'/></td>

        </tr>

        <tr>

            <td>校验TOKEN<input type='text' size='15' name='u_s'/></td>

        </tr>

        <tr>

            <td>

                用户类型

                <select name="u_usertype">r

                    <option value="0">默认</option>

                    <option value="1">邮箱用户</option>

                    <option value="2">手机用户</option>

                </select>

            </td>

        </tr>

        <tr>

            <td width="100%">

                <iframe width="40%" name="verify" scrolling="auto"></iframe>

            </td>

        </tr>

        <tr>

            <td>

                <input

                        type='button' value='确认' onclick="startTest('verifyurl','verifyform')"/>

            </td>

        </tr>

    </form>



    <tr height="20px"></tr>

    <form id='executeform' action='' method='POST' target='execute'>

        <tr>

            <td><b>11,执行 密码重置</b></td>

        </tr>

        <tr>

            <td>请求方式POST请求</td>

        </tr>

        <tr>

            <td><span>接口地址：</span><span id="executeurl"><?php echo $domain ?>/api/password</span>

            </td>

        </tr>

        <tr>

            <input type='hidden' size='15' name='u_action' value="reset"/>

            <input type='hidden' size='15' name='u_step' value="execute"/>

        </tr>

        <tr>

            <td>用户名<input type='text' size='15' name='u_name'/></td>

        </tr>

        <tr>

            <td>新密码<input type='text' size='15' name='u_newpwd'/></td>

        </tr>

        <tr>

            <td>重复新密码<input type='text' size='15' name='u_newpwd2'/></td>

        </tr>

        <tr>

            <td>校验TOKEN<input type='text' size='15' name='u_s'/></td>

        </tr>

        <tr>

            <td width="100%">

                <iframe width="40%" name="execute" scrolling="auto"></iframe>

            </td>

        </tr>

        <tr>

            <td>

                <input

                        type='button' value='确认' onclick="startTest('executeurl','executeform')"/>

            </td>

        </tr>

    </form>

    <tr height="20px"></tr>

    <form id='modifyverifyform' action='' method='POST' target='modifyverify'>

        <tr>

            <td><b>11,修改密码校验</b></td>

        </tr>

        <tr>

            <td>请求方式POST请求</td>

        </tr>

        <tr>

            <td><span>接口地址：</span><span id="modifyverifyurl"><?php echo $domain ?>/api/password</span>

            </td>

        </tr>

        <tr>

            <td>

                用户类型

                <select name="u_usertype">r

                    <option value="0">默认</option>

                    <option value="1">邮箱用户</option>

                    <option value="2">手机用户</option>

                </select>

            </td>

            <input type='hidden' size='15' name='u_action' value="reset"/>

            <input type='hidden' size='15' name='u_step' value="modifyverify"/>

        </tr>

        <tr>

            <td>校验码<input type='text' size='15' name='u_vcode'/></td>

        </tr>

        <tr>

            <td>校验TOKEN<input type='text' size='15' name='u_s'/></td>

        </tr>

        <tr>

            <td width="100%">

                <iframe width="40%" name="modifyverify" scrolling="auto"></iframe>

            </td>

        </tr>

        <tr>

            <td>

                <input

                        type='button' value='确认' onclick="startTest('modifyverifyurl','modifyverifyform')"/>

            </td>

        </tr>

    </form>

    <tr height="20px"></tr>

    <form id='modifyform' action='' method='POST' target='modify'>

        <tr>

            <td><b>11,修改密码</b></td>

        </tr>

        <tr>

            <td>请求方式POST请求</td>

        </tr>

        <tr>

            <td><span>接口地址：</span><span id="modifyurl"><?php echo $domain ?>/api/password</span>

            </td>

        </tr>

        <tr>

            <input type='hidden' size='15' name='u_action' value="reset"/>

            <input type='hidden' size='15' name='u_step' value="modify"/>

        </tr>

        <td>

            用户类型

            <select name="u_usertype">r

                <option value="0">默认</option>

                <option value="1">邮箱用户</option>

                <option value="2">手机用户</option>

            </select>

        </td>

        <tr>

            <td>校验TOKEN<input type='text' size='15' name='u_s'/></td>

        </tr>



        <tr>

            <td>新密码<input type='text' size='15' name='u_newpwd'/></td>

        </tr>

        <tr>

            <td>重复新密码<input type='text' size='15' name='u_newpwd2'/></td>

        </tr>

        <tr>

            <td width="100%">

                <iframe width="40%" name="modify" scrolling="auto"></iframe>

            </td>

        </tr>

        <tr>

            <td>

                <input

                        type='button' value='确认' onclick="startTest('modifyurl','modifyform')"/>

            </td>

        </tr>

    </form>

    <tr>

        <td><a href="#confirm_login">验证是否登录</a></td>

    </tr>

    <tr>

        <td>

            <hr/>

        </td>

    </tr>

    <tr>

        <td><h2 id="confirm_login">登录验证</h2></td>

    </tr>

    <tr>

        <td><a href="#account_mobile">返回手机注册登录</a></td>

    </tr>

    <tr>

        <td><a href="#account_mail">返回邮箱注册登录</a></td>

    </tr>

    <tr>

        <td><a href="#pwd_reset">返回密码重置</a></td>

    </tr>





    <tr height="20px"></tr>

    <form id='userinfoform' action='' method='get' target='userinfo'>

        <tr>

            <td><b>8,用户资料显示</b></td>

        </tr>

        <tr>

            <td>请求方式POST请求</td>

        </tr>

        <tr>

            <td><span>接口地址：</span><span id="userinfourl"><?php echo $domain ?>/api/user</span>

            </td>

        </tr>



        <tr>

            <td>用户ID<input type='text' size='15' name='u_mid'/></td>

        </tr>

        <tr>

            <td width="100%">

                <iframe width="40%" name="userinfo" scrolling="auto"></iframe>

            </td>

        </tr>

        <tr>

            <td><input style="height: 100px;" type='button' value='确认'

                       onclick="startTest('userinfourl','userinfoform')"/></td>

        </tr>

    </form>



    <tr height="20px"></tr>

    <form id='updateuserinfoform' action='' method='POST' target='updateuserinfo'>

        <tr>

            <td><b>8,用户资料修改</b></td>

        </tr>

        <tr>

            <td>请求方式POST请求</td>

        </tr>

        <tr>

            <td><span>接口地址：</span><span id="updateuserinfourl"><?php echo $domain ?>/api/user</span>

            </td>

        </tr>





        <tr>

            <td>性别

                <select name="u_gender">

                    <option value="1">男</option>

                    <option value="2">女</option>

                </select>

            </td>

        </tr>

        <tr>

            <td>昵称<input type='text' size='15' name='u_nickname'/></td>

        </tr>

        <tr>

            <td>生日<input type='text' size='15' name='u_birth'/></td>

        </tr>

        <tr>

            <td>头像<input type='text' size='15' name='u_imgid'/></td>

        </tr>

        <tr>

            <td width="100%">

                <iframe width="40%" name="updateuserinfo" scrolling="auto"></iframe>

            </td>

        </tr>

        <tr>

            <td><input style="height: 100px;" type='button' value='确认'

                       onclick="startTest('updateuserinfourl','updateuserinfoform')"/></td>

        </tr>

    </form>





    <tr height="20px"></tr>

    <form id='bindform' action='' method='POST' target='bind'>

        <tr>

            <td><b>8,用户手机或邮箱绑定</b></td>

        </tr>

        <tr>

            <td>请求方式POST请求</td>

        </tr>

        <tr>

            <td><span>接口地址：</span><span id="bindurl"><?php echo $domain ?>/api/bind</span>

            </td>

        </tr>



        <tr>

            <td>用户ID<input type='text' size='15' name='u_mid'/></td>

        </tr>

        <tr>

            <td>

                绑定类型

                <select name="u_btype">r

                    <option value="1">邮箱</option>

                    <option value="2">手机</option>

                </select>

            </td>

        </tr>

        <tr>

            <td>

                绑定类型

                <select name="u_action">

                    <option value="bind">绑定</option>

                    <option value="unbind">解绑</option>

                </select>

            </td>

        </tr>

        <tr>

            <td>手机或邮箱<input type='text' size='15' name='u_name'/></td>

        </tr>

        <tr>

            <td>验证码<input type='text' size='15' name='u_vcode'/></td>

        </tr>

        <tr>

            <td width="100%">

                <iframe width="40%" name="bind" scrolling="auto"></iframe>

            </td>

        </tr>

        <tr>

            <td><input style="height: 100px;" type='button' value='确认'

                       onclick="startTest('bindurl','bindform')"/></td>

        </tr>
    </form>
</table>
</body>
</html>