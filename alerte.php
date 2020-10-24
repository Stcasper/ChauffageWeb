<html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src="menu.js"></script>

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-light-blue.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<!-- Le titre de la page -->
<title>Etat du chauffage</title>
</head>

<body>
<nav class="w3-sidebar w3-bar-block w3-card w3-animate-left w3-center" style="display:none" id="mySidebar">
  <h1 class="w3-xxxlarge w3-text-theme">Chauffage</h1>
  <button class="w3-bar-item w3-button" onclick="w3_close()">Fermer <i class="fa fa-remove"></i></button>
  <a href="./" class="w3-bar-item w3-button">Réglage</a>
  <a href="./logs.html" class="w3-bar-item w3-button">Historique</a>
  <a href="./admin.html" class="w3-bar-item w3-button">Admin</a>
</nav>

<!-- Header -->
<header class="w3-container w3-theme w3-padding" id="myHeader">
  <i onclick="w3_open()" class="fa fa-bars w3-xlarge w3-button w3-theme"></i> 
  <div class="w3-center">
  <h1 class="w3-xxxlarge w3-animate-bottom">Etat du chauffage</h1>
  </div>
  <div id="val" class="w3-center">
  </div>
</header>


<DIV id="div1" CLASS='class="w3-center w3-border"'>
<?
$expediteur   = "no-reply<stcasper@free.fr>";
$sujet        = "Rapport de fonctionnement chauffage";
$reponse      = "stcasper@free.fr";  /*  Repondre à : reply-To*/
$destinataire = "fred.stcasper@gmail.com";

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n"; /* encodage message */
$headers .= "From: $expediteur\r\nReply-To: $reponse\r\n";


//Parametres de conexion à la base :
$base = "stcasper";
$mot_de_passe = "o1fdzc1a";
$Corps="";

// Alerte
$AlerteGal=false;
// Conexion a la base
$mysqlconnectid=mysql_connect("localhost",$base,$mot_de_passe);

//Mise à jour de la table
$sql = mysql_db_query($base,"SELECT distinct Numero, Nom FROM Radiateurs where Numero<8 and Numero!=4",$mysqlconnectid);
if(mysql_num_rows($sql) > 0)
{
	$num=mysql_num_rows($sql)+1;
	$i=0;
	// Pour chaque radiateur
	for ($i=1;$i<$num;++$i)
	{
		$alerte=false;
		$Corps = $Corps.mysql_result($sql,$i-1,"Nom")."<BR/>";
		$sql1 = mysql_db_query(base,"SELECT Date, Temperature, Allume FROM Temp_releve WHERE Radiateur= ".$i." order by date desc",$mysqlconnectid);
		if(mysql_num_rows($sql1) > 4) // Si on a plus de 4 lignes de stats
		{
			// Vérification de la dernière mesure. Si elle date de plus de 30 minutes => Alerte
			$derniere = mysql_result($sql1,0,"Date");
			//echo $derniere."<BR/>";
			$date_der = new DateTime($derniere);
			$now = new DateTime('NOW');
			/*echo $now->format('Y-m-d H:i:s')."<BR/>";
			echo $date_der->format('Y-m-d H:i:s')."<BR/>";
			echo $date_der->getTimestamp()."<BR/>";
			echo $now->getTimestamp()."<BR/>";*/
			$diff = $now->getTimestamp() - $date_der->getTimestamp();
			//echo $diff."<BR/>";
			if($diff > 1800) 
			{
				$alerte=true;
				$AlerteGal=true;
				$Corps = $Corps.'&emsp;&emsp;- Pas de mesure depuis plus de 30 minutes'."<BR/></BR>"; 
			}
			
			// Vérification des relevés de température interieure. Si 3 mesure consécutives à 0 => Alerte
			$temperature = mysql_result($sql1,0,"Temperature");
			//echo $temperature."<BR/></BR>";
			if ($temperature=="0")
			{
				$temperature = mysql_result($sql1,1,"Temperature");
				//echo $temperature."<BR/></BR>";
				if ($temperature=="0")
				{
					$temperature = mysql_result($sql1,2,"Temperature");
					//echo $temperature."<BR/></BR>";
					if ($temperature=="0")
					{
						$alerte=true;
						$AlerteGal=true;
						$Corps = $Corps.'&emsp;&emsp;- Sonde de température HS'."<BR/></BR>";
					}
				}
			}
			
			// Vérification des relevés de température interieure. Si le chauffage chauffe mais que la température descend pendant 4 mesures => Alerte
			$Allume=mysql_result($sql1,0,"Allume");
			$diff = abs(mysql_result($sql1,0,"Temperature") - mysql_result($sql1,1,"Temperature"));
			if ($Allume=="1" && $diff <= 0.11)
			{
				//echo $diff."<BR/></BR>";
				$Allume=mysql_result($sql1,1,"Allume");
				$diff = abs(mysql_result($sql1,1,"Temperature") - mysql_result($sql1,2,"Temperature"));
				if ($Allume=="1" && $diff <= 0.11)
				{
					//echo $diff."<BR/></BR>";
					$Allume=mysql_result($sql1,2,"Allume");
					$diff = abs(mysql_result($sql1,2,"Temperature") - mysql_result($sql1,3,"Temperature"));
					if ($Allume=="1" && $diff <= 0.11)
					{
						//echo $diff."<BR/></BR>";
						$Allume=mysql_result($sql1,3,"Allume");
						$diff = abs(mysql_result($sql1,3,"Temperature") - mysql_result($sql1,4,"Temperature"));
						if ($Allume=="1" && $diff <= 0.11)
						{
							//echo $diff."<BR/></BR>";
							$Allume=mysql_result($sql1,4,"Allume");
							if ($Allume=="1")
							{
								$alerte=true;
								$AlerteGal=true;
								$Corps = $Corps.'&emsp;&emsp;- Fenêtre ouverte ou relai en panne'."<BR/></BR>";
							}
						}
					}
				}
			}
			if ($alerte == false)
			{
				$Corps = $Corps.'&emsp;&emsp;- Tout est en ordre'."<BR/></BR>";
			}
		}
	}
	echo $Corps;
	if ($AlerteGal)
	{
		//mail($destinataire,$sujet,$Corps,$headers);
	}
}
mysql_close($mysqlconnectid);

?>
</div>
</body>

</html>