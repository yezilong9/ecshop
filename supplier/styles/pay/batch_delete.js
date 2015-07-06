/******************************************************************************
Filename       : admin/jscript/batch_delete.js
Author         : WeiChen
Email          : chengjiawei2000@163.com
Date/time      : 2009-07-27 14:22:10
Purpose        : 做批量删除操作函数
Mantis ID      : 
Description    : 
Revisions      : 
Modify         : 
Inspect        :
******************************************************************************/
<!--
 //全选/反选
  function CheckAll(para1,para2){
    var objForm = document.forms[para1];
    var objLen  = objForm.length;
 
    for (var i = 0; i < objLen; i++){
        if (para2.checked == true){
            if (objForm.elements[i].type == "checkbox"){
                objForm.elements[i].checked = true;
            }
        }else{
            if (objForm.elements[i].type == "checkbox"){
                objForm.elements[i].checked = false;
            }
        }
    }
  } 

  //删除选定产品
  function del_chk(para1,handle){
    var obj_form = document.forms[para1];
    var chkflag  = false;
    var num = obj_form.elements.length;
    var intCount = 0;
    
    for(var i = 0; i < num; i++){
    	  if(obj_form.elements[i].checked){
    		   chkflag = true;
    		   intCount = intCount+1;
    			 break;
    	  }
    }

    if(intCount < 1){
       window.alert("请选择所需处理的记录！");
       return false;
    }
    

    var isAlert = window.confirm("确认操作选定的记录?");
    if(!isAlert) return false;

     obj_form.action = handle;  
    //obj_form.action = handle+"?actions=del";
    obj_form.submit();
  } 

      
-->
