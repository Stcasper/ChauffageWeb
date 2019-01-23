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

function tableau(val,rad) {
	$.getJSON(
		'temp_rest.php',
		{
			rquest : "logs_last",
			cpt : val
		},
		function toto(data){
			var tbl_body = document.createElement("tbody");
			var odd_even = false;
			$.each(data, function() {
				var tbl_row = tbl_body.insertRow();
				tbl_row.className = odd_even ? "odd" : "even";
				$.each(this, function(k , v) {
					var cell = tbl_row.insertCell();
					cell.append(document.createTextNode(v.toString()));
			})        
        odd_even = !odd_even;               
		})
		$("#Tablelog").append(tbl_body);
		});
}
 
 
function tableau2(val,rad) {
	$.getJSON(
		'temp_rest.php',
		{
			rquest : "logs_last_rad",
			cpt : val,
			radiateur : rad
		},
		function toto(data){			
			var tbl_body = document.createElement("tbody");
			var odd_even = false;
			$.each(data, function() {
				var tbl_row = tbl_body.insertRow();
				tbl_row.className = odd_even ? "odd" : "even";
				$.each(this, function(k , v) {
					var cell = tbl_row.insertCell();
					cell.append(document.createTextNode(v.toString()));
			})        
        odd_even = !odd_even;               
		})
		$("#Tablelog").append(tbl_body);
		});
}
$(document).ready(function(){
	
	tableau(50);
  
    $("#submit").click(function(e){
        e.preventDefault();
		$("#Tablelog tbody").empty();
		tableau2($("#nbLignes").val(),$("#radiateur").val());
    });
});