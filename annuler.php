<?php

session_start();
// s'il n'y a pas de paramètre, ou si c'est une mauvaise valeur
if ( EMPTY($_SESSION["hotel"])     || !is_numeric($_SESSION["hotel"])
  || EMPTY($_SESSION["dateDebut"]) || EMPTY($_SESSION["dateFin"])
  || EMPTY($_SESSION["chambre"]) || EMPTY($_SESSION["reservation"])
)
{
	// on retourne à la page d'accueil
	header('Location: ./index.php');
}

//include_once('./mail.php');

// on se connecte à la base de données
include_once('./dbconnection.php');

?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title>Annuler</title>
	<link rel="stylesheet" type="text/css" href="./lecss.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
</head>
<body>

<?php

$error = false;
try {
	//annulation
	$stm = $connection->prepare(
		"DELETE FROM Reservations WHERE id = :id"
	);

	$res = $stm->execute(array(
		':id' => $_SESSION["reservation"]
		)
	);

}

catch ( Exception $e ) {
	die($e->getMessage());
	$error = true;
}

if ( $error ) : ?>
	<h1>ERREUR</h1>
	<h2>Il y a eu une erreur veuillez <a href="index.php">recommencer</a></h2>
<?php else : ?>
	<h1>Vous avez annulé votre réservation</h1>
	<a href="index.php"> Effectuer une nouvelle réservation</a>
<?php endif;?>


</body>
</html>