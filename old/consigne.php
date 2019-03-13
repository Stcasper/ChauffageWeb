<html>

<head>

<!-- La feuille de style _ Ne pas changer -->
<style type="text/css" media="screen">@import "../style.css";</style>



<!-- Le titre de la page -->
<title>Logs</title>

</head>

<body>


<!-- Le paragraphe dans lequel le corps de la page se place -->
<DIV CLASS='Div1'>


<?
//Parametres de conexion à la base :
$base = "stcasper";
$mot_de_passe = "o1fdzc1a";


// Conexion a la base
$mysqlconnectid=mysql_connect("localhost",$base,$mot_de_passe);

//Mise à jour de la table
$sql = "SELECT * FROM Temp_consigne;";
//echo $sql;
$requete=mysql_db_query($base,$sql,$mysqlconnectid);
$num=mysql_num_rows($requete);

$i=0;

?>

<TABLE>
<TR><TH CLASS='tableauth'><B>Date</B></TH><TH CLASS='tableauth'><B>Température</B></TH><TH CLASS='tableauth'><B>Demandeur</B></TH>
<TH CLASS='tableauth'><B>Date application</B></TH><TH CLASS='tableauth'><B>Heure application</B></TH></TR>

<?

for ($i=0;$i<$num;++$i)
{
	$Date=mysql_result($requete,$i,"Date");
	$Temperature=mysql_result($requete,$i,"Temperature");
	$Demandeur=mysql_result($requete,$i,"Demandeur");
	$Date_Appli=mysql_result($requete,$i,"Date_Application");
	$Heure_Appli=mysql_result($requete,$i,"Heure_Application");

?>
<TR>
<TD CLASS='tableautd_double2'><? echo $Date ?></TD>
<TD CLASS='tableautd_double2'><? echo $Temperature ?></TD>
<TD CLASS='tableautd_double2'><? echo $Demandeur ?></TD>
<TD CLASS='tableautd_double2'><? echo $Date_Appli ?></TD>
<TD CLASS='tableautd_double2'><? echo $Heure_Appli ?></TD>

</TR>
<?
}
?>
</TABLE>
<?
mysql_close($mysqlconnectid);

?>