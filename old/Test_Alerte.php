<html>

<head>
<!-- Le titre de la page -->
<title>Alerte</title>
</head>

<body>
<?
require "../sms.php";

function date_getMicroTime()
{
	list($usec, $sec) = explode(' ', microtime());
	return ((float) $usec + (float) $sec);
} 

$sms = new SMS\FreeMobile(); 

/** * configure l'ID utilisateur et la clé disponible dans * le compte Free Mobile après avoir activé l'option. */ 
$sms->setKey("FelqcwPbivXANe")
	->setUser("12807543");

$expediteur   = "no-reply<stcasper@free.fr>";
$sujet        = "Rapport de fonctionnement chauffage";
$reponse      = "stcasper@free.fr";  /*  Repondre à : reply-To*/
$destinataire = "fred.stcasper@gmail.com, besse.didier@orange.fr, besse.annie@orange.fr, gabrielle.besse@gmail.com, besse.guillaume@gmail.com";

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n"; /* encodage message */
$headers .= "From: $expediteur\r\nReply-To: $reponse\r\n";


//Parametres de conexion à la base :
$base = "stcasper";
$mot_de_passe = "o1fdzc1a";
$Corps="";
$CorpsSMS="";

// Alerte
$AlerteGal=false;
// Conexion a la base
$mysqlconnectid=mysql_connect("localhost",$base,$mot_de_passe);

//Mise à jour de la table
$sql = mysql_db_query($base,"SELECT distinct Numero, Nom FROM Radiateurs where Numero<6",$mysqlconnectid);
if(mysql_num_rows($sql) > 0)
{
	$num=mysql_num_rows($sql)+1;
	$i=0;
	// Pour chaque radiateur
	for ($i=1;$i<$num;++$i)
	{
		$alerte=false;
		$Corps = $Corps.mysql_result($sql,$i-1,"Nom")."<BR/>";
		$CorpsSMS = $CorpsSMS.mysql_result($sql,$i-1,"Nom")."\r\n";
		$sql1 = mysql_db_query(base,"SELECT Date, Temperature, Allume FROM Temp_releve WHERE Radiateur= ".$i." order by date desc",$mysqlconnectid);
		if(mysql_num_rows($sql1) > 4) // Si on a plus de 4 lignes de stats
		{
			// Vérification de la dernière mesure. Si elle date de plus de 30 minutes => Alerte
			$derniere = mysql_result($sql1,0,"Date");
			$date_der = new DateTime($derniere);
			$now = new DateTime('NOW');
			$diff = $now->getTimestamp() - $date_der->getTimestamp();
			if($diff > 1800) 
			{
				$alerte=true;
				$AlerteGal=true;
				$Corps = $Corps.'&emsp;&emsp;- Pas de mesure depuis plus de 30 minutes'."<BR/></BR>"; 
				$CorpsSMS = $CorpsSMS.'      - Pas de mesure depuis plus de 30 minutes'."\r\n\r\n"; 
			}
			
			// Vérification des relevés de température interieure. Si 3 mesure consécutives à 0 => Alerte
			$temperature = mysql_result($sql1,0,"Temperature");
			//echo $temperature."<BR/></BR>";
			if ($temperature=="0" && $alerte==false)
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
						$CorpsSMS = $CorpsSMS.'      - Sonde de température HS'."\r\n\r\n";
					}
				}
			}
			
			// Vérification des relevés de température interieure. Si le chauffage chauffe mais que la température descend pendant 4 mesures => Alerte
			$Allume=mysql_result($sql1,0,"Allume");
			$diff = mysql_result($sql1,0,"Temperature") - mysql_result($sql1,1,"Temperature");
			if ($Allume=="1" && $diff <= 0.11  && $alerte==false)
			{
				//echo $diff."<BR/></BR>";
				$Allume=mysql_result($sql1,1,"Allume");
				$diff = mysql_result($sql1,1,"Temperature") - mysql_result($sql1,2,"Temperature");
				if ($Allume=="1" && $diff <= 0.11)
				{
					//echo $diff."<BR/></BR>";
					$Allume=mysql_result($sql1,2,"Allume");
					$diff = mysql_result($sql1,2,"Temperature") - mysql_result($sql1,3,"Temperature");
					if ($Allume=="1" && $diff <= 0.11)
					{
						//echo $diff."<BR/></BR>";
						$Allume=mysql_result($sql1,3,"Allume");
						$diff = mysql_result($sql1,3,"Temperature") - mysql_result($sql1,4,"Temperature");
						if ($Allume=="1" && $diff <= 0.11)
						{
							//echo $diff."<BR/></BR>";
							$Allume=mysql_result($sql1,4,"Allume");
							if ($Allume=="1")
							{
								$alerte=true;
								$AlerteGal=true;
								$Corps = $Corps.'&emsp;&emsp;- Fenêtre ouverte ou relai en panne'."<BR/></BR>";
								$CorpsSMS = $CorpsSMS.'      - Fenêtre ouverte ou relai en panne'."\r\n\r\n";
							}
						}
					}
				}
			}
			if ($alerte == false)
			{
				$Corps = $Corps.'&emsp;&emsp;- Tout est en ordre'."<BR/></BR>";
				$CorpsSMS = $CorpsSMS.'      - Tout est en ordre'."\r\n\r\n";
			}
		}
	}
	echo $Corps;
	if ($AlerteGal)
	{
		try 
		{
			// envoi d'un message
			$sms->send($CorpsSMS);
		} 
		catch (Exception $e) 
		{
			// le monde n'est pas parfait, il y aura
			// peut-être des erreurs.
			echo "Erreur sur envoi de SMS: (".$e->getCode().") ".$e->getMessage();
		}
		
		$total = 0;
		$essai=0;
		while($total < 1.5 AND $essai<10)
		{
			$start = date_getMicroTime();
			for($i = 0 ; $i < 999999 ; $i++) 1;
			mail($destinataire,$sujet,$Corps,$headers);
			$total = round(date_getMicroTime() - $start, 3);
			$essai++;
		}
	}
}
mysql_close($mysqlconnectid);

?>
</body>

</html>