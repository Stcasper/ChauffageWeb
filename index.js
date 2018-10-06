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

function vide() {
	$("#temp").val("");
	$("#username").val("");
	$("#password").val("");
	$("#val").empty();
}

function check() {
	if ( $("#temp").val() < 7 | $("#temp").val() > 25) {
		$("#resultat").html("<p>Valeur hors limites !</p>");
		$("#temp").val("");
	}
}

function efface() {
	$("#resultat").html("");
}

function entete() {
	$.getJSON(
		'temp_rest.php',
		{
			rquest : "read_consigne"
		},
		function toto(data){
			var tbl_body = document.createElement("tbody");
			$.each(data, function() {
				$.each(this, function(k , v) {
					if (v.toString() != "Temperature"){
						$("#val").append("<H4>Consigne : "+v.toString()+"°C</H4>");
					}
				})
			})
		});
		
		$.getJSON(
		'temp_rest.php',
		{
			rquest : "logs_last",
			cpt : "1"
		},
		function toto(data){
			var tbl_body = document.createElement("tbody");
			$.each(data, function() {
				$.each(this, function(k , v) {
					if (k.toString() == "Temperature"){
						$("#val").append("<H4>Dernier relevé : "+v.toString()+"°C</H4>");
					}
				})
			})
		});
}

function tableau() {
	$.getJSON(
		'temp_rest.php',
		{
			rquest : "logs_consigne",
			cpt : "10"
		},
		function toto(data){
			var tbl = document.createElement("table");
			tbl.className="w3-table w3-striped w3-bordered";
			tbl.id="Tabletemp";
			$.each(data, function() {
				var tbl_row = tbl.insertRow();
				$.each(this, function(k , v) {
					var cell = tbl_row.insertCell();
					cell.append(document.createTextNode(v.toString()));
				})
			})
			$("#div1").append(tbl);
			$("#Tabletemp").prepend("<TR class=\"w3-theme\"><TH><B>Radiateur</B></TH><TH><B>°C</B></TH><TH><B>Qui ?</B></TH><TH><B>Date</B></TH><TH><B>Heure</B></TH></TR>");
		});
}
 
$(document).ready(function(){
	tableau();	
	entete();
 
    $("#submit").click(function(e){
        e.preventDefault();
		$("#resultat").html("");
        $.post(
            'temp_rest.php', 
            {
                rquest : "login",
				username : $("#username").val(),
                password : $("#password").val()
            },
			function(data, status, xhr){
				if (xhr.status === 200) {
					$.post(
					'temp_rest.php', 
					{
						rquest : "insert_consigne",
						radiateur : $("#radiateur").val(),
						temp_cons : $("#temp").val(),
						user : $("#username").val()
					},
					function(data, status, xhr){
						if (xhr.status === 200) {
							$("#div1").empty();
							tableau();
							$("#resultat").html("<p>Valeur ajoutée avec succés !</p>");
							vide();
							entete();
						}
						else {
							$("#resultat").html("<p>Erreur serveur</p>");
						}
					});
                }
                else{
                     $("#resultat").html("<p>Erreur lors de la connexion...</p>");
                }
         
            },
            'text'
         );
    });
});