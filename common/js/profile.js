/**
 * 
 */
$(function(){
	$('button.edit.profile').unbind('click').click(function() {
		profile.edit($(this).parent().parent());
		return false;
	}
	);
	//email_validator={optional:function(e){return false;},email:jQuery.validator.methods.email};
	$('form.profile').validate({
		submitHandler:function() {
			
			
		},
		errorPlacement: function(error, element) {},
		rules:{
			username:{
				minlength:4,
				maxlength:30,
				regExpr:/^[a-zA-Z\d]+$/,
				remote:{
					url : path+"/account/ctrl",
					type : "post" 
					//dataType : "json"
				}
			},
			password:{
				minlength:8,
				maxlength:16,
				regExpr:/^[a-zA-Z\d]+$/,
				remote:{
					url : path+"/account/ctrl",
					type : "post" 
					//dataType : "json"
				}
			},
			'new':{
				minlength:8,
				maxlength:16,
				regExpr:/^[a-zA-Z\d]+$/
			},
			new2:{
				equalTo:'#password'
			},
			email: {
				remote: {
					url : path+"/account/ctrl",
					type : "post" 
				}
			}
		},
	});// perche ricarica??
	$('.profile').submit(function(e){
		return false;
	});
	$('.button button:eq(0)').click(function(){
		profile.password(this);
	});
	$('.button button:eq(1)').click(function(){
		$("#dialog").dialog('open');
	});
	$("#dialog").dialog({
		autoOpen : false,
		modal : true,
		//width : 1000,
		buttons : {
			Ok : function() {
				ev.request('/account/delete','post',{password:$('#delete').val()},function(data){
					//if (data) location.reload();
				});
				$(this).dialog("close");
			},
			Cancel : function() {
				$(this).dialog("close");
			}
		},
	});
	
});
var profile={
	edit:function(row) {
		row.find('input,select,textarea').show();
		row.find('span:eq(0)').hide();
		button=row.find('button');
		button.unbind('click').click(function(){
			profile.send($(this).parent().parent());
		});
		button.button('option','icons',{
			primary:'ui-icon-check'
		});
	},
	send:function(row) {
		i=row.find('input,select,textarea').hide();
		if (i.is('select')) {
			v=i.find('option:selected').val();
			label=i.find('option:selected').text();
		}
		else {v=i.val();label=i.val();}
		
		row.find('span:eq(0)').show().text(label);
		button=row.find('button');
		button.unbind('click').click(function(){
			profile.edit($(this).parent().parent());
			return false;
		});
		$('.button button:eq(0)').button('option','icons',{
			primary:'ui-icon-wrench'
		});
		ev.request('/account/edit','post',{key:row.attr('id'),value:v});
		return false;
	},
	password:function(button) {
		$('.password').show();
		$(button).unbind('click').click(function(){
			pass=$('.password:eq(1) input');
			if (pass.eq(0).val()==pass.eq(1).val()) {
				ev.request('/account/password',{key:'password',value:pass.eq(0).val()});
			}
			$('.profile')[0].reset();
			$('.password').hide();
			$(button).unbind('click').click(function(){
				profile.password(this);
			}).button('option','icons',{
				primary:''
			});
		}).button('option','icons',{
			primary:'ui-icon-check'
		});
	}
};