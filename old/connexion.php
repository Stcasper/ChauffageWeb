 <?
//Parametres de conexion à la base :
$base = "stcasper";
$mot_de_passe = "o1fdzc1a";


// Conexion a la base
$mysqlconnectid=mysql_connect("localhost",$base,$mot_de_passe);

if( isset($_POST['username']) && isset($_POST['password']) ){
	
	$req = "SELECT user_id, user_fullname, user_email FROM users WHERE user_fullname = '".$_POST['username']."' AND user_password = '".md5($_POST['password'])."' LIMIT 1";
	//$req = "SELECT * from users";
	$sql = mysql_db_query($base,$req,$mysqlconnectid);
	if(mysql_num_rows($sql) > 0){
		echo "Success";    
	}
	else{ // Sinon
		echo "Failed";
	}
}

mysql_close($mysqlconnectid);

?>