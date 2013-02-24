$(function() {
	$('#registration_form').validate({
		rules:{
			username:{
				minlength:4,
				maxlength:30,
				regExpr:/^[a-zA-Z\d]+$/,
				remote:{
					url : path+"/reg/ctrl",
					type : "post" 
					//dataType : "json"
				}
			},
			password:{
				minlength:8,
				maxlength:16,
				regExpr:/^[a-zA-Z\d]+$/
			},
			password2:{
				equalTo:"#password"
			},
			email: {
				remote: {
					url : path+"/reg/ctrl",
					type : "post" 
				}
			}
		},
		messages:{
			username: {
				remote:jQuery.validator.format("{0} is already taken"),
				regExpr:'only characters alphanumeric'
			},
			password:{
				regExpr:'only characters alphanumeric'
			},
			email: {
				remote:jQuery.validator.format("{0} is already taken")
			}
		}
	});
});