<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

          // s'il n'y a pas de paramètre, ou si c'est une mauvaise valeur
if ( EMPTY($_POST["hotel"])     || !is_numeric($_POST["hotel"])
  || EMPTY($_POST["dateDebut"]) || EMPTY($_POST["dateFin"])
  || EMPTY($_POST["chambre"])   || EMPTY($_POST["nom"])
  || EMPTY($_POST["prenom"])    || EMPTY($_POST["mail"])
  || EMPTY($_POST["telephone"])
)
{
	// on retourne à la page d'accueil
	header('Location: ./index.php');
}

// on se connecte à la base de données
include_once('./dbconnection.php');


/*

On va faire une transaction
on commence par enregistrer le nouveau client
on récupère l'id
on fait la reservation

 */

$error = false;
try {

	$connection->beginTransaction();

	$clientCreated = $connection->prepare(
		"INSERT INTO Clients (prenom , nom, mail, telephone) VALUES (:prenom , :nom, :mail, :telephone)"
	);
	$clientCreated = $clientCreated->execute(
		array(
			':prenom' => $_POST["prenom"],
			':nom' => $_POST["nom"],
			':mail' => $_POST["mail"],
			':telephone' => $_POST["telephone"]
		)
	);

	if ( !$clientCreated ) {
		$error = true;
		$connection->rollBack();
	}
	else {
		// get the id of the newly created client
		$idClient = $connection->lastInsertId("clients_id_seq");
		
		//reservation
		$stm = $connection->prepare(
			"INSERT INTO Reservations (dateDebut, dateFin, client, chambre, hotel) VALUES (:dateDebut, :dateFin, :client, :chambre, :hotel)"
		);

		$res = $stm->execute(array(
			':dateDebut' => $_POST["dateDebut"],
			':dateFin'   => $_POST["dateFin"],
			':client'    => $idClient,
	        ':hotel'     => $_POST["hotel"],
	        ':chambre'   => $_POST["chambre"]
		    )
		);
		// si la requête a échoué
		if ( !$res ) {
			$error = true;
			echo "yooo";
			$connection->rollBack();
		}
		else {
			$connection->commit();
		}

	}

} // end try
catch ( Exception $e ) {
	die($e->getMessage());
	$error = true;
}
   
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>

<?php if ( $error ) : ?>
	<h1>ERREUR</h1>
	<h2>Il y a eu une erreur veuillez <a href="index.php">recommencer</a></h2>
<?php else : ?>
	<h1>Votre réservation a été enregistrée</h1>
<?php endif; ?>


</body>
</html>