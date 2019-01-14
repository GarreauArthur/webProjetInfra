<?php
// s'il n'y a pas de paramètre, ou si c'est une mauvaise valeur
if ( EMPTY($_POST["hotel"])     || !is_numeric($_POST["hotel"])
  || EMPTY($_POST["dateStart"]) || EMPTY($_POST["dateEnd"])
)
{
	// on retourne à la page d'accueil
	header('Location: ./index.php');
}

else {

	// On transforme les 2 dates en timestamp
	$dateStart = strtotime($_POST["dateStart"]);
	$dateEnd = strtotime($_POST["dateEnd"]);
	
	// On récupère la différence de timestamp entre les 2 précédents
	$nbNuitsTimestamp = $dateEnd - $dateStart;
	
	// ** Pour convertir le timestamp (exprimé en secondes) en nuits **
	// On sait que 1 heure = 60 secondes * 60 minutes et que 1 jour = 24 heures donc :
	$nbNuits = $nbNuitsTimestamp/86400; // 86 400 = 60*60*24

	// Date d'aujourd'hui

	$dateNow = strtotime("today");
	
	// TEST : date de début supérieur à date de fin -> message d'erreur
	// TEST : date de début ou de fin inférieure(s) à celle d'aujourd'hui -> message d'erreur
	
	if ($nbNuits < 0 || $dateStart < $dateNow || $dateEnd < $dateNow){
		header('Location: ./choixDates.php?hotel='.$_POST["hotel"].'&msg=error');
	}
}

// on se connecte à la base de données
include_once('./dbconnection.php');
// on récupère les infos
$stm = $connection->prepare("SELECT * FROM Chambres WHERE hotel = :hotel AND numeroChambre NOT IN (SELECT chambre FROM Reservations WHERE hotel = :hotel AND (dateDebut < :dEnd) AND (dateFin > :dStart ))");
$stm->execute(array(
	                ':hotel'   => $_POST["hotel"],
	                ':dStart'  => $_POST["dateStart"],
	                ':dEnd'  => $_POST["dateEnd"]
                    )
             );

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Chambres</title>
	<link rel="stylesheet" type="text/css" href="./lecss.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
</head>
<body>

<?php 
$nbAvailable = $stm->rowCount();
if( $nbAvailable == 0) : ?>
	<h1>Pas de chambres disponibles pour cette période</h1>
<?php else : 
	echo '<h1>Il y a '.$nbAvailable.' chambre(s) disponible(s)</h1>';
	$res = $stm->fetchAll();
	function toInt($str) // fonction qui transforme un prix (money) en nombre (int)
	{
		return preg_replace("/([^0-9\\.])/i", "", $str);
	}
	setlocale(LC_MONETARY, 'en_US'); // pour les dolls
	
	foreach ($res as $val) { ?>

		<div class="chambre">
	    	<i class="fas fa-bed"></i>
		    Numéro chambre : <?= $val["numerochambre"]; ?><br>
			nbLitSimple : <?= $val["nblitsimple"]; ?><br>
			nbLitDouble : <?= $val["nblitdouble"]; ?><br>
			prix : <?= $val["prix"]; ?><br>
			prix total pour <?= $nbNuits; ?> nuits : <?= $prixTotal = money_format('%.2n',toInt($val["prix"])*$nbNuits); ?><br>
			gammeChambre : <?= $val["gammechambre"]; ?><br>
			etage: <?= $val["etage"]; ?><br>

			<form method="POST" action="reservation.php">
				<input type="hidden" name="hotel"     value="<?= $val["hotel"]; ?>">
				<input type="hidden" name="chambre"   value="<?= $val["numerochambre"]; ?>">
				<input type="hidden" name="dateDebut" value="<?= htmlspecialchars($_POST["dateStart"]); ?>">
				<input type="hidden" name="dateFin"   value="<?= htmlspecialchars($_POST["dateEnd"]); ?>">
				<input type="hidden" name="prixTotal" value="<?= $prixTotal; ?>">
				<input type="submit" value="Réserver">
			</form>
	    </div>			

<?php

	}//foreach

endif; ?>


</body>
</html>