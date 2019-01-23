<?php
session_start();
// s'il n'y a pas de paramètre, ou si c'est une mauvaise valeur
if ( EMPTY($_SESSION["hotel"])     || !is_numeric($_SESSION["hotel"])
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

	// on sauvegarde les données dans la session
	$_SESSION["dateDebut"] = $_POST["dateStart"];
	$_SESSION["dateFin"]   = $_POST["dateEnd"];
}

// on se connecte à la base de données
include_once('./dbConnection.php');
// on récupère les infos
$stm = $connection->prepare("SELECT * FROM Chambres WHERE hotel = :hotel AND numeroChambre NOT IN (SELECT chambre FROM Reservations WHERE hotel = :hotel AND (dateDebut < :dEnd) AND (dateFin > :dStart ))");
$stm->execute(array(
	                ':hotel'   => $_SESSION["hotel"],
	                ':dStart'  => $_POST["dateStart"],
	                ':dEnd'  => $_POST["dateEnd"]
                    )
             );

?>
<!DOCTYPE html>
<html lang="fr">
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
	<h1>Disponibilité</h1>
	<h2>Pas de chambres disponibles pour cette période</h2>
	<div class="liens">
		<a href="choixDates.php?hotel=<?php echo $_SESSION["hotel"]?>">Choisir de nouvelles dates de réservation</a>
		<br/><br/>
		<a href="index.php">Choisir un nouvel hôtel</a>
	</div>
<?php else : 
	echo '<h1>Disponibilité</h1>';
	echo '<h2>Il y a '.$nbAvailable.' chambre(s) disponible(s)</h2>';
	$res = $stm->fetchAll();
	function toInt($str) // fonction qui transforme un prix (money) en nombre (int)
	{
		return preg_replace("/([^0-9\\.])/i", "", $str);
	}
	setlocale(LC_MONETARY, 'en_US'); // pour les dolls
	?>
	<div class="list-chambres">
	<?php 
	
	foreach ($res as $val) { ?>

		<div class="chambre">
			<div class="chambre-element">
				<p><i class="fas fa-bed"></i>
				Numéro chambre : <?= $val["numerochambre"]; ?></p>
				<p>nombre de lit simple : <?= $val["nblitsimple"]; ?></p>
				<p>nombre de lit double : <?= $val["nblitdouble"]; ?></p>
				<p>prix : <?= $val["prix"]; ?></p>
				<p>prix total pour <?= $nbNuits; ?> nuits : <?= $prixTotal = money_format('%.2n',toInt($val["prix"])*$nbNuits); ?></p>
				<p>gamme de la chambre : <?= $val["gammechambre"]; ?></p>
				<p>etage: <?= $val["etage"]; ?></p>
			</div>

			<div class="chambre-element">
				<div class="chambre-element-absolute">
				<form method="POST" action="reservation.php">
					<input type="hidden" name="hotel"     value="<?= $val["hotel"]; ?>">
					<input type="hidden" name="chambre"   value="<?= $val["numerochambre"]; ?>">
					<input type="hidden" name="prixTotal" value="<?= $prixTotal; ?>">
					<input class="submit-blue" type="submit" value="Réserver">
				</form>
				</div>

			</div>
	    </div>
	
<?php

	}//foreach
	

endif; ?>


</body>
</html>
