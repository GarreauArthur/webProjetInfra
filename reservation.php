<?php
// s'il n'y a pas de paramètre, ou si c'est une mauvaise valeur
if ( EMPTY($_POST["hotel"])     || !is_numeric($_POST["hotel"])
  || EMPTY($_POST["dateDebut"]) || EMPTY($_POST["dateFin"])
  || EMPTY($_POST["chambre"])
)
{
	// on retourne à la page d'accueil
	header('Location: ./index.php');
}

// on se connecte à la base de données
include_once('./dbconnection.php');

$stm = $connection->prepare(
	"SELECT * FROM Chambres INNER JOIN Hotels ON Hotels.id = Chambres.hotel"
	." WHERE chambres.hotel = :hotel AND chambres.numeroChambre = :chambre"
);
$stm->execute(array(
	                ':hotel'   => $_POST["hotel"],
	                ':chambre'  => $_POST["chambre"]
                    )
             );

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>One more step</title>
	<link rel="stylesheet" type="text/css" href="./lecss.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
</head>
<body>
	<h1>Confirmer la réservation</h1>
	<!-- TODO récap réservation -->
	<form action="confirmation.php" method="POST">
		<input type="hidden" name="hotel"     value="<?= htmlspecialchars($_POST["hotel"]); ?>">
		<input type="hidden" name="chambre"   value="<?= htmlspecialchars($_POST["chambre"]); ?>">
		<input type="hidden" name="dateDebut" value="<?= htmlspecialchars($_POST["dateDebut"]); ?>">
		<input type="hidden" name="dateFin"   value="<?= htmlspecialchars($_POST["dateFin"]); ?>">
		<input class="infos" type="text" name="nom" placeholder="Nom">
		<input class="infos" type="text" name="prenom" placeholder="Prenom">
		<input class="infos" type="text" name="mail" placeholder="Mail">
		<input class="infos" type="text" name="telephone" placeholder="Telephone">
		<input type="submit" value="Confirmer la réservation">
	</form>
</body>
</html>