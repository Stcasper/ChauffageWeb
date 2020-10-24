<html>

<head>
<!-- Le titre de la page -->
<title>Reboot</title>
</head>

<body>
<?
//Parametres de conexion à la base :
$base = "stcasper";
$mot_de_passe = "o1fdzc1a";


// Conexion a la base
$mysqlconnectid=mysql_connect("localhost",$base,$mot_de_passe);

//Mise à jour de la table
$sql = mysql_db_query($base,"Update Temp_consigne as T1, (select MAX(T2.Compteur) as Mx from Temp_consigne as T2 GROUP BY T2.Radiateur) as T2 set Reboot=1 where T1.Compteur=T2.Mx and T1.Radiateur > 0 and T1.Radiateur < 6",$mysqlconnectid);

mysql_close($mysqlconnectid);

?>
</body>

</html>