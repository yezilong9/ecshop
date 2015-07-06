/* *
 * 会员登录
 */
function userLogin()
{
  var frm      = document.forms['popup_login_submit'];
  var username = frm.elements['account'].value;
  var password = frm.elements['password'].value;
  var msg = '';

  if (username.length == 0)
  {
    msg += '用户名不能为空' + '\n';
  }

  if (password.length == 0)
  {
    msg += '密码不能为空' + '\n';
  }

  if (msg.length > 0)
  {
    alert(msg);
    return false;
  }
  else
  {
    return true;
  }
}

function selectShipping(goods_id)
{
    if(goods_id == 10721 || goods_id == 10783 || goods_id == 10784 || goods_id == 10785 )
    {
        document.getElementById('lianjie').disabled ="";
        document.getElementById('lianjie_2').disabled ="";
        document.getElementById('lianjie_3').disabled ="";
        document.getElementById('lianjie_4').disabled ="";
        document.getElementById('lianjie_5').disabled ="";
        document.getElementById('lianjie_6').disabled ="";
    }
    else 
    {
        document.getElementById('lianjie').disabled ="disabled";
        document.getElementById('lianjie_2').disabled ="disabled";
        document.getElementById('lianjie_3').disabled ="disabled";
        document.getElementById('lianjie_4').disabled ="disabled";
        document.getElementById('lianjie_5').disabled ="disabled";
        document.getElementById('lianjie_6').disabled ="disabled";
    }
    
    Ajax.call('christmas_activity.php?act=select_goods&goods_id='+goods_id,'', abc, 'GET', 'TEXT', true, true);
}
function abc(result)
{
    //alert(result);
    document.getElementById('count_money').innerHTML = "<p class=count>苹果费用：<strong>￥"+result+"</strong></p>"+                                                      
                                                        "<p class=total>合计：￥"+result+" </p>";
                                                        
    document.getElementById('count_money_2').innerHTML = "<p class=count>苹果费用：<strong>￥"+result+"</strong></p>"+                                                      
                                                       "<p class=total>合计：￥"+result+" </p>";
    document.getElementById('count_money_3').innerHTML = "<p class=count>苹果费用：<strong>￥"+result+"</strong></p>"+                                                      
                                                       "<p class=total>合计：￥"+result+" </p>";
    document.getElementById('count_money_4').innerHTML = "<p class=count>苹果费用：<strong>￥"+result+"</strong></p>"+                                                      
                                                       "<p class=total>合计：￥"+result+" </p>";
    document.getElementById('count_money_5').innerHTML = "<p class=count>苹果费用：<strong>￥"+result+"</strong></p>"+                                                      
                                                       "<p class=total>合计：￥"+result+" </p>";
    document.getElementById('count_money_6').innerHTML = "<p class=count>苹果费用：<strong>￥"+result+"</strong></p>"+                                                      
                                                       "<p class=total>合计：￥"+result+" </p>";
}


function selectaddr(show_value)
{
    if(show_value == 7)
    {
        document.getElementById('show_div').style.display= ''; 
        document.getElementById('show_div_2').style.display= ''; 
        document.getElementById('show_div_3').style.display= ''; 
        document.getElementById('show_div_4').style.display= ''; 
        document.getElementById('show_div_5').style.display= '';
        document.getElementById('show_div_6').style.display= '';
    }
    else
    {
        document.getElementById('show_div').style.display= 'none';   
        document.getElementById('show_div_2').style.display= 'none'; 
        document.getElementById('show_div_3').style.display= 'none';  
        document.getElementById('show_div_4').style.display= 'none';  
        document.getElementById('show_div_5').style.display= 'none';
        document.getElementById('show_div_6').style.display= 'none';
    } 
}

function subtocar(select_value)
{ 
    if(select_value == 1 ) 
    {
       var message_url = document.getElementById("lianjie").value; 
       var user_name   = document.getElementById("user_name").value;
       var phone       = document.getElementById("phone").value;
       var address     = document.getElementById("address").value;
    }
    else if(select_value == 2)
    {
       var message_url = document.getElementById("lianjie_2").value;  
       var user_name   = document.getElementById("user_name_2").value;
       var phone       = document.getElementById("phone_2").value;
       var address     = document.getElementById("address_2").value;
    }
    else if(select_value == 3)
    {
       var message_url = document.getElementById("lianjie_3").value; 
       var user_name   = document.getElementById("user_name_3").value;
       var phone       = document.getElementById("phone_3").value;
       var address     = document.getElementById("address_3").value; 
    }
    else if(select_value == 4)
    {
       var message_url = document.getElementById("lianjie_4").value;  
       var user_name   = document.getElementById("user_name_4").value;
       var phone       = document.getElementById("phone_4").value;
       var address     = document.getElementById("address_4").value;
    }
    else if(select_value == 5)
    {
       var message_url = document.getElementById("lianjie_5").value;  
       var user_name   = document.getElementById("user_name_5").value;
       var phone       = document.getElementById("phone_5").value;
       var address     = document.getElementById("address_5").value;
    }
    else if(select_value == 6)
    {
       var message_url = document.getElementById("lianjie_6").value;  
       var user_name   = document.getElementById("user_name_6").value;
       var phone       = document.getElementById("phone_6").value;
       var address     = document.getElementById("address_6").value;
    }
   
    var temp = document.getElementsByName("radio_group1");
    for(var i=0;i<temp.length;i++)
    {
        if(temp[i].checked)
        var goods_radio = temp[i].value;
    }
    if(goods_radio == undefined)
    {
        alert("请选择商品进行购物");
        return false;
    }
   
    var temp_2 = document.getElementsByName("radio_group2");
    for(var i=0;i<temp_2.length;i++)
    {
        if(temp_2[i].checked)
        var addr_radio = temp_2[i].value;
    }
    if(addr_radio == undefined)
    {
        alert("请选择相应地址");
        return false;
    }
    else 
    {
        if(addr_radio == 7)
        {
           if(user_name =='')
           {
            alert("请填写姓名");
            return false;
           }
           if(phone =='')
           {
            alert("请填写手机号码");
            return false;
           }
           if(address =='')
           {
            alert("请填写具体地址");
            return false;
           }
        }
    }

    
    Ajax.call('christmas_activity.php?act=addtocar&goods_id='+goods_radio+'&addr_radio='+addr_radio+
              '&message_url='+message_url+'&user_name='+user_name+'&phone='+phone+'&address='+address,'', resultmessage, 'GET', 'TEXT', true, true);
    
    return false;
}
function resultmessage(rerult)
{
    if(rerult == 'false')
    {
       alert("添加商品去支付的时候失败,请检查商品是否正常存在");
    }
    else 
    {
        if(rerult == '0false')
        {
            alert("添加商品去支付的时候失败,请检查商品是否正常存在");
            return false;
        }
        alert("购买成功,前往支付");
        //window.location.href = 'flow.php?step=checkout';
        if(rerult == '')
        {
            //window.open("flow.php?step=checkout"); 
              location.href = 'flow.php?step=checkout';
        }
        else 
        {
           //window.open("flow.php?step=checkout&message_url='"+rerult+"'");  
           location.href = "flow.php?step=checkout&message_url='"+rerult+"'";
        }
        
    } 
}

