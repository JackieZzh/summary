$(document).ready(function(){
//数字+字母验证
$("#getcode_char").click(function(){
		$(this).attr("src",'code_char.php?' + Math.random());
	});
	
$("#url").val(window.location.href);
	
});


function Checkcode(){//判断验证码是否为空
 if(document.form1.code.value=="")
	{
		
		failure("code_box","验证码为空，请填写！");
		return false;
	}else{
	
	pass("code_box");
		   return true;
	
	}


}



function isChinaOrNumbOrLett(){//判断是否是汉字、字母、数字组成
    if(document.form1.username.value=="" || document.form1.username.value=="请输入中文名或英文名")
	{
		document.form1.username.value='请输入中文名或英文名';
		document.form1.username.style.color='#999';
		failure("username_box","请输入您的姓名");
		return false;
	}
	else
	{
       var regu = "^[a-zA-Z\u4e00-\u9fa5]+$";
	   var s = document.form1.username.value;  
       var re = new RegExp(regu); 
       if (re.test(s))
	   {
		   pass("username_box");
		   return true;
       }
	   else
	   {
		   failure("username_box","输入错误，输入必须为汉字或英文字母");
		   return false;
       }
    }
}





function getObj(id){ 

var Obj = document.getElementById(id).value; 

return Obj; 

} 

 

function check(){
	
      if(getObj("username")=="" || getObj("phone")=="")
	  { 
        // alert("文本框输入为空，不能提交表单！");
	     if(getObj("username")=="") 
	     {
			failure("username_box","输入错误，输入必须为汉字或英文字母");
	     }
		 else if(getObj("phone")=="") 
	     {
			failure("phone_box","请输入电话号码或手机号码。如：02089011456,020-89011456，13800138000");
	     }
		 
        return false;//false:阻止提交表单
     } 
	 else
	 {
		if(isChinaOrNumbOrLett() && Checkreg() && Checkcode())
		{
			document.form1.submit();
			document.getElementById("jiaohou").style.display = "block";
			document.getElementById("jiaoqian").style.display = "none";
			return true;
		}
		else
		{ 
			return false;
		} 
	 }

} 

//手机验证的方法
function Checkphone(){
 var phone=getObj("phone");
if(Checkreg()){
$.ajax({
	type: 'GET',  //这里用GET
	url: 'http://web.prykweb.com/index.php?m=purui&c=datadeal&a=Getajax_phone_time&phone='+phone+'&host=8',
    dataType: 'jsonp',  //类型
	data: {name:"getitems"},
	jsonp: 'callback', //jsonp回调参数，必需
    async: false,
      success: function(msg) {//返回的json数据
		if(msg!=2){ 
		var linkphone=getObj("linkphone");
		//alert('您的手机号已经预约过，预约号为'+msg+'。如有需要，请在24小时之后再预约！');
		$(".yytips").html("");
		var c='<span class="close" id="close" onclick="yyclose(\'f\')">x</span><h3>普瑞温馨提示：</h3><p>您已经预约过，请耐心等候！</p><p>预约号为：<strong class="yyh">'+msg+'</strong></p><p>如有需要请<strong class="yyh">24</strong>小时之后再进行预约！</p><p>或至电：'+linkphone+'进行预约。</p>';
	    tc('f',c);
		document.form1.phone.value='';
		failure("phone_box","请重新输入电话号码和手机号码");
		return false;
		}else{ pass("phone_box"); return true;}
				},
		error : function (xml, err) {         //alert("数据错误！请重试！"); 
		    $(".yytips").html("");
			var c='<span class="close" id="close" onclick="yyclose(\'cw\',"")">x</span><p><strong class="yyh">数据错误！请重试！</strong></p>';
	        tc('f',c);
		    return false;    
		} 			

	/* type : 'get',
	url  : 'http://www.prykweb.com/index.php?m=purui&c=datadeal&a=Getajax_phone_time&phone='+phone+'&host=9',
	cache:false,
	dataType:'json',
	data:phone,
	success : function (msg) {
		if(msg==1){

		alert('您的手机号已经预约过，请耐心等候！如有需要，请在24小时之后再预约！');
		document.form1.phone.value='';
		failure("phone_box","请重新输入电话号码和手机号码");
		return false;
		}else{ pass("phone_box"); return true;}
	},
	error : function (xml, err) {         alert("数据错误！请重试！"); return false;    } 	 */
});
}


}




function Checkreg() 
{ 
    //验证电话号码手机号码，包含153，159号段 
   if (document.form1.phone.value==""){ 
      // document.form1.phone.value='请输入手机或固定电话号码';
	  document.form1.phone.style.color='#999';
	  failure("phone_box","请输入电话号码和手机号码");
      return false; 
   } 
   if (document.form1.phone.value != "")
   { 
       //alert(document.form1.phone.value.length);
       if(document.form1.phone.value.length > 11)
	   {
		 var phone=document.form1.phone.value; 
         var p1 = /^(([0\+]\d{2,3}-)?(0\d{2,3})-)?(\d{7,8})$/; 
		 var p2 = /^(([0\+]\d{2,3})?(0\d{2,3}))?(\d{7,8})$/; 
         var me = false; 
         if (p1.test(phone)||p2.test(phone))
		 {
			 me=true; 
			 pass("phone_box");
		     return true;
		 }
        if (!me)
		 { 
            //document.form1.phone.value=''; 
			failure("phone_box","对不起，您输入的电话号码有错误。如：02089011456,020-89011456"); 
            return false; 
         }   
	  }
	  else if(document.form1.phone.value.length==11)
	  {
		  //alert(0);
		  var phone=document.form1.phone.value; 
          var p1 = /^(([0\+]\d{2,3})?(0\d{2,3}))?(\d{7})$/; 
		  var p2 = /^(\d{11})$/;
		  if(p1.test(phone))
		  {
			 pass("phone_box");
			 return true;
		  }
		  else if(p2.test(phone))
		  { 
            if(checkmobile())
			{
			  return true;
			}
			else
			{
				return false;
			}
          }  
		  else
		  {
			  return false;
		  }
		  
	  }
	  else if(7<=document.form1.phone.value.length <= 8)
	  {
		 var phone=document.form1.phone.value; 
		 var p1 = /^(\d{7,8})$/; 
         var me = false; 
         if (p1.test(phone))
		 {
			 me=true; 
			 pass("phone_box");
			 return true;
		}
        if (!me){ 
            //document.form1.phone.value=''; 
			failure("phone_box","对不起，您输入的电话号码应为7-8位数字。");  
            return false; 
         }   
	  }
	  else
	  {
		 if(checkmobile())
			{
			  return true;
			}
			else
			{
				return false;
			}
		
	  }  
   } 
}

function checkmobile()
{
	     var mobile=document.form1.phone.value; 
      	 var reg0 = /^13\d{5,9}$/; 
     	 var reg1 = /^15(0|1|2|3|5|6|7|8|9)\d{4,8}$/; 
	 var reg2 = /^18(0|1|2|3|5|6|7|8|9)\d{4,8}$/;
   	 var reg3 = /^17(0|1|2|3|5|6|7|8|9)\d{4,8}$/;
   	     var my = false; 
   	     if (reg0.test(mobile))
		   {
			   pass("phone_box");
			   my=true;
		   }
   	     if (reg1.test(mobile))
		   {
			   pass("phone_box");
			   my=true;
		   }
    	 if (reg2.test(mobile))
		   {
			   pass("phone_box");
			   my=true;
		   } 
          if (reg3.test(mobile))
		   {
			   pass("phone_box");
			   my=true;
		   } 

    	 if (!my)
	      { 
            //document.form1.phone.value=''; 
			failure("phone_box","对不起，您输入的手机或小灵通号码有错误。");   
            return false; 
          } 
  return true;   
}

/*function  tx_date()
{
	if(document.form1.date.value="")
	{
		$(document).ready(function(e) {
		       $("#date_box").find("p").remove();
		       $("#date_box").append("<p class=failure>对不起，您输入的手机或小灵通号码有错误。</p>");
		});
		return false;
	}
	else
	{
		 var date=document.form1.phone.value;
      	 var reg0 = /^(20\d{2}-)(\d{1,2}-)(\d{1,2})$/;
		 if (reg0.test(date))
		  {
			$(document).ready(function(e) {
		       $("#date_box").find("p").remove();
		       $("#date_box").append("<p class=pass></p>");
		    });
		  }
		  else
		  {
			  $(document).ready(function(e) {
		         $("#date_box").find("p").remove();
		         $("#date_box").append("<p class=failure>日期输入错误。如2013-8-16。</p>");
		      });
		  }
	}
}
*/


function pass(contex)
{
	$(document).ready(function() {
		       $("#"+contex).find("p").remove();
		       $("#"+contex).append("<p class=pass></p>");
	});  
}
function failure(contex,content)
{
	$(document).ready(function() {
		       $("#"+contex).find("p").remove();
		       $("#"+contex).append("<p class=failure>"+content+"</p>");
	});  
}

function myreset(contex)
{
	$(document).ready(function() {
		$("#"+contex).find("p").remove();
		$("#username").css({color:"#999"});  
		$("#phone").css({color:"#999"}); 
	});  
}
function GetDateStr(AddDayCount) {
	    var dd = new Date();
	    dd.setDate(dd.getDate()+AddDayCount);//获取AddDayCount天后的日期
	    var y = dd.getFullYear();
	    var m = dd.getMonth()+1;//获取当前月份的日期
	    var d = dd.getDate();
	    return y+"-"+m+"-"+d;
	}
 function mydate()
 {
    var myDate = new Date();
    var year = myDate.getFullYear();  //获取完整的年份(4位,1970-????)
    var month = myDate.getMonth();      //获取当前月份(0-11,0代表1月)
    var day = myDate.getDate();      //获取当前日(1-31)
    
	document.form1.date.value=GetDateStr(1);//year +"-" + (month+1) + "-"+ (day+1);
  }


$(function(){
	$("#form1").Validform({
		tiptype:function(msg,o,cssctl){
			var objtip=o.obj.parents("form").find("#msgdemo").show().find(".Validform_checktip");
			if(o.type==2)
			{
				o.obj.nextAll(".tip-right").addClass("display-block");
				o.obj.parents("form").find("#msgdemo").hide();
			}
			else
			{
				o.obj.nextAll(".tip-right").removeClass("display-block");
			}
			cssctl(objtip,o.type);
			objtip.text(msg);
		},
		datatype:{"f":/^(-?\d+)(\.\d+)?$/,
			"hanzi":/^[\u4e00-\u9fa5]+$/,
			"m":/^13[0-9]{9}$|14[0-9]{9}|15[0-9]{9}|17[0-9]{9}$|18[0-9]{9}$/,
		},
		label:".yz-label"
	});
})

function postcallback(data){
	var content=null;
	if(data==1)
	{
		$(".yytips").html("");
		content='<span class="close" id="close" onclick="yyclose(\'cw\','+data+')">x</span><p><strong id="yyh" class="yyh">数据传送失败</strong></p>';
		tc('cw',content);
		document.getElementById("form1").reset();
	}
	else if(data==2)
	{
		$(".yytips").html("");
		content='<span class="close" id="close" onclick="yyclose(\'cw\','+data+')">x</span><p><strong id="yyh" class="yyh">验证码错误!</strong></p>';
		tc('cw',content);
		document.getElementById("form1").reset();
	}
	else if(data==3)
	{
		$(".yytips").html("");
		content='<span class="close" id="close" onclick="yyclose(\'cw\','+data+')">x</span><p><strong id="yyh" class="yyh">预约时间错误!</strong></p>';
		tc('cw',content);
		document.getElementById("form1").reset();
	}
	else if(data==9)
	{
		$(".yytips").html("");
		content='<span class="close" id="close" onclick="yyclose(\'cw\','+data+')">x</span><p><strong id="yyh" class="yyh">姓名或手机为空!</strong></p>';
		tc('cw',content);
		document.getElementById("form1").reset();
	}
	else if(data==5)
	{
		Checkphone();
		document.getElementById("form1").reset();
	}
	else
	{
		$(".yytips").html("");
		content='<span class="close" id="close" onclick="yyclose(\'r\','+data+')">x</span><h3>普瑞温馨提示：</h3><p>您的预约号码为:<strong id="yyh" class="yyh">'+data+'</strong>,请保管好！</p>';
		tc('r',content);
        document.getElementById("form1").reset();
	}
}

$(document).ready(function(){
	var yuming = window.location.host;
	document.form1.fasongurl.value ="http://"+yuming;
});

function tc(y,content){
	$(".tipsbox").addClass(y);
	$(".yytips").html(content);
	easyDialog.open({container :'secuss',fixed : false});
}

function yyclose(i,j){
	$(".tipsbox").removeClass(i);
	$("#close").attr("onclick","");
	easyDialog.close();
	if(i=='r'){location.reload(true) ;}
	if(j==2){
		document.form1.code.value="";
		$("#code_img").attr("src","http://web.prykweb.com/api.php?op=checkcode&code_len=4&font_size=14&width=110&height=30&font_color=&background=");
	}
}



