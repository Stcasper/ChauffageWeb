function w3_open() {
    var x = document.getElementById("mySidebar");
    x.style.width = "60%";
    x.style.fontSize = "40px";
    x.style.paddingTop = "10%";
    x.style.display = "block";
}
function w3_close() {
    document.getElementById("mySidebar").style.display = "none";
}

function tab(data) {
	var tbl_body = document.createElement("tbody");
	$.each(data, function() {
		var tbl_row = tbl_body.insertRow();
		$.each(this, function(k , v) {
			var cell = tbl_row.insertCell();
			cell.append(document.createTextNode(v.toString()));
		})
	})
	$("#formulaire").hide();
	$("#deco").show();
	$("#Tablelog").append("<TR class=\"w3-theme\"><TH><B>Numéro</B></TH><TH><B>login</B></TH><TH><B>Mail</B></TH></TR>");
	$("#Tablelog").append(tbl_body);
}


 
$(document).ready(function(){
	
	$.get(
		'temp_rest.php',
		{
			rquest : "check_session"
		},
		function (data, status, xhr){
			if (xhr.status === 200) {
				$.getJSON(
					'temp_rest.php',
					{
						rquest : "users"
					},
					function toto(data){
						tab(data);
				});
			}
			else {
				$("#formulaire").show();
			}
		});
 
 
    $("#submit").click(function(e){
        e.preventDefault();
         $.post(
            'temp_rest.php', 
            {
                rquest : "login",
				username : $("#username").val(),
                password : $("#password").val()
            },
			function(data, status, xhr){
				if (xhr.status === 200) {
					$.getJSON(
					'temp_rest.php',
					{
						rquest : "users"
					},
					function toto(data){
						tab(data);
				    });
				}
			},
            'text'
         );
    });
	
	$("#unlog").click(function(e){
        e.preventDefault();
         $.post(
            'temp_rest.php', 
            {
                rquest : "unlogin"
            },
			function(data, status, xhr){
				if (xhr.status === 200) {
					$("#formulaire").show();
					$("#deco").hide();
					$("#Tablelog").empty();
				}
			},
            'text'
         );
    });
});