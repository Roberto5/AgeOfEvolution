var prev_user=null;
var prev_email=null; 
var prev_pass=null;
var prev_pass2=null;
var bemail=false;
var buser=false;
function controlRegister()
{
	user=$("#user").val();
   email=$("#email").val();
	pass=$("#pass").val();
	pass2=$("#pass2").val();
   bool=false;
   // controllo password
   if((pass!=pass2)||(pass=="")||(pass2=="")) {
		bool=false;
		$("#pass").css("border","2px solid red");
   }else{
		if((pass.length>4)&&(pass.length<16)) {
			$("#pass").css("border","2px solid green");
			bool=true;
		}else{
			bool=false;
			$("#pass").css("border","2px solid red");
		}
	}
   //controllo email aiax
   if ((email!="")&&(email!=prev_email))
   {
		if (email.match(/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}$/gi)) {
			//chiamata ajax
			$("#email-label").html('<label class="required" for="email">Email'+': <img src="'+path+'/common/images/loading.gif" alt="load" /></label>');
			$.ajax({
				url : path+"/ajax/reg",
				type : "post" ,
				data : 'cerca=user_mail&valore='+email,
				dataType : "json" ,
				success : function (data,stato) {
					$("#email-label").html('<label class="required" for="email">Email</label>');
					if (data) {
						bool=false;
                        bmail=false;
						$("#email").css("border","2px solid red");
					}
					else {
						bool=bool&&true;
                                                bmail=true;
						$("#email").css("border","2px solid green");
					}
				},
				error : function (richiesta,stato,errori) {
					alert("An error occurred. "+errori);
				}
			});
        } 
        else {
            bool=false;
			$("#email").css("border","2px solid red");
        }
    }
    else bool=bool && bmail;
    //controllo user
    if ((user)&&(prev_user!=user)) {
        if ((user.length>=4)&&(user.length<=30)) {
			//chiamata ajax
        	$("#user-label").html('<label class="required" for="user">Username'+': <img src="'+path+'/common/images/loading.gif" alt="load" /></label>');
			$.ajax({
				url : path+"/ajax/reg",
				type : "post" ,
				data : 'cerca=username&valore='+user,
				dataType : "json" ,
				success : function (data,stato) {
					$("#user-label").html('<label class="required" for="user">Username</label>');
					if (data) {
						bool=false;
                                                buser=false;
						$("#user").css("border","2px solid red");
					}
					else {
						bool=bool&&true;
                                                buser=true;
						$("#user").css("border","2px solid green");
					}
				},
				error : function (richiesta,stato,errori) {
					alert("An error occurred. "+errori);
				}
			});
        }else {
            bool=false;
			$("#user").css("border","2px solid red");
        }
    }
    else bool=bool && buser;
    prev_user=user;
    prev_email=email;
    return bool;
}
function sendemail()
{
    email=$("#email").val();
    uid=$("#ID").val();
    html=$("#load").html();
    $("#load").html('<img src="'+path+'/common/images/loading.gif" alt="load" />');
    $.ajax({
        url: path+"ajax/sendMail" ,
        data : {mail: email, id:uid} ,
        type : "post",
        dataType : "json" ,
        success: function (data,stato) {
            if (data) {
                $("#load").html("Email send a " + email);
                window.setTimeout("location.href = "+path+"\'index\';", 5000 );
            }
            else {
                $("#load").html("Error!"+"<br />"+html);
            }
        } ,
        error : function (richiesta,stato,errori) {
            $("#load").html("error!"+"<br />"+html);
            alert("An error occurred. "+errori);
        }
    });
}