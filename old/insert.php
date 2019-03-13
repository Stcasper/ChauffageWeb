<?
//Parametres de conexion à la base :
$base = "stcasper";
$mot_de_passe = "o1fdzc1a";


// Conexion a la base
$mysqlconnectid=mysql_connect("localhost",$base,$mot_de_passe);

/*$temp_ext = $_REQUEST['temp_ext'];
$temp = $_REQUEST['temp'];
$humi = $_REQUEST['humi'];
$Consigne = $_REQUEST['Consigne'];
$Allume = $_REQUEST['Allume'];*/


//Mise à jour de la table
$sql = "INSERT INTO `stcasper`.`Temp_releve` (`Date`, `Temp_Ext`, `Temperature`, `Humidite`, `Tp_Consigne`, `Radiateur`, `Allume`) VALUES (CURRENT_TIMESTAMP, '".$_REQUEST['temp_ext']."', '".$_REQUEST['temp']."', '".$_REQUEST['humi']."', '".$_REQUEST['Consigne']."', '".$_REQUEST['Radiateur']."', '".$_REQUEST['Allume']."');";
echo $sql;
$requete=mysql_db_query($base,$sql,$mysqlconnectid);

mysql_close($mysqlconnectid);

?>