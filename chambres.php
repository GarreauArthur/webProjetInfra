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
	echo '<h2>Vous avez choisi l\'hotel '.$_POST["hotel"].' du '.$_POST["dateStart"].' au '.$_POST["dateEnd"].'.</h2>';

	// TEST : date de début inférieur à date de fin

	$dateStart=new DateTime($_POST["dateStart"]);
	$dateEnd=new DateTime($_POST["dateEnd"]);
	
	if ($dateStart >= $dateEnd){
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
	foreach ($res as $val) { ?>

		<div class="chambre">
	    	<i class="fas fa-bed"></i>
		    Numéro chambre : <?= $val["numerochambre"]; ?><br>
			nbLitSimple : <?= $val["nblitsimple"]; ?><br>
			nbLitDouble : <?= $val["nblitdouble"]; ?><br>
			prix : <?= $val["prix"]; ?><br>
			gammeChambre : <?= $val["gammechambre"]; ?><br>
			etage: <?= $val["etage"]; ?><br>

			<form method="POST" action="reservation.php">
				<input type="hidden" name="hotel"     value="<?= $val["hotel"]; ?>">
				<input type="hidden" name="chambre"   value="<?= $val["numerochambre"]; ?>">
				<input type="hidden" name="dateDebut" value="<?= htmlspecialchars($_POST["dateStart"]); ?>">
				<input type="hidden" name="dateFin"   value="<?= htmlspecialchars($_POST["dateEnd"]); ?>">
				<input type="submit" value="Réserver">
			</form>
	    </div>			

<?php

	}//foreach

endif; ?>


</body>
</html>