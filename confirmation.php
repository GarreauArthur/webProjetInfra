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

			// On récupère le prix de la chambre

			$prix = $connection->prepare("SELECT prix FROM Chambres WHERE hotel = :hotel AND numeroChambre = :chambre");
			$prix->execute(array(
				':hotel'   => $_POST["hotel"],
				':chambre' => $_POST["chambre"]
				)
			);
			$prixChambre = $prix->fetch();

			// On récupère le nom de l'hotel

			$hotel = $connection->prepare("SELECT nom FROM Hotels WHERE id = :hotel");
			$hotel->execute(array(
				':hotel' => $_POST["hotel"]
				)
			);
			$nomHotel = $hotel->fetch();

			// On reformate les dates de dbt et de fin 

			$dateDebut = $_POST["dateDebut"];
			$dateDebutReformate = date("d-m-Y", strtotime($dateDebut));
			$dateFin = $_POST["dateFin"];
			$dateFinReformate = date("d-m-Y", strtotime($dateFin));
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
	<link rel="stylesheet" type="text/css" href="./lecss.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
</head>
<body>

<?php if ( $error ) : ?>
	<h1>ERREUR</h1>
	<h2>Il y a eu une erreur veuillez <a href="index.php">recommencer</a></h2>
<?php else : ?>
	<h1>Votre réservation a été enregistrée</h1>
	<h2> Détail de réservation :</h2>
	<p> Vous avez choisi la chambre <?php echo $_POST["chambre"]?> de l'hôtel <?php echo $nomHotel["nom"]?> !</p>
	<p> Vous l'avez réservé au nom de <strong><?php echo $_POST["prenom"]?> <?php echo $_POST["nom"]?></strong> du <?php echo $dateDebutReformate?> au <?php echo $dateFinReformate?>.</p>
	<p> Son prix est de <?php echo $prixChambre["prix"]?>.</p>
	<p> L'équipe <strong>Bangaloducul</strong> vous souhaite un bon séjour !</p>

<?php endif; ?>


</body>
</html>