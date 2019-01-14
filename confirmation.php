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

include_once('./mail.php');

// on se connecte à la base de données
include_once('./dbconnection.php');

// on vérifie que personne n'a réservé la chambre entre temps
$numeroChambre = $connection->prepare("SELECT numeroChambre FROM Chambres WHERE hotel = :hotel AND numeroChambre NOT IN (SELECT chambre FROM Reservations WHERE hotel = :hotel AND (dateDebut < :dEnd) AND (dateFin > :dStart ))");
$numeroChambre->execute(array(
	                ':hotel'   => $_POST["hotel"],
	                ':dStart'  => $_POST["dateDebut"],
	                ':dEnd'  => $_POST["dateFin"]
                    )
			 );
$numeroChambreDisponible = $numeroChambre->fetchAll(); // récupération des numéros de chambres disponible à cet hôtel 
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Confirmation</title>
	<link rel="stylesheet" type="text/css" href="./lecss.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
</head>
<body>

<?php

$estDisponible=false;

foreach ($numeroChambreDisponible as $numero){
    if ($numero["numerochambre"] == $_POST["chambre"]){
		$estDisponible=true;
	}
}

if( !$estDisponible) : ?>
	<h1>Dommage ! Quelqu'un a réservé cette chambre pendant que vous vous identifiiez...</h1>
	<a href="choixDates.php?hotel=<?php echo $_POST["hotel"]?>">Choisir de nouvelles dates</a>

<?php else : 

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
   
if ( $error ) : ?>
	<h1>ERREUR</h1>
	<h2>Il y a eu une erreur veuillez <a href="index.php">recommencer</a></h2>
<?php else : ?>
	<h1>Votre réservation a été enregistrée</h1>
	<h2> Détail de réservation :</h2>
	<p> Vous avez choisi la chambre <?php echo $_POST["chambre"]?> de l'hôtel <?php echo $_POST["nomHotel"]?> !</p>
	<p> Vous l'avez réservé au nom de <strong><?php echo $_POST["prenom"]?> <?php echo $_POST["nom"]?></strong> du <?php echo $dateDebutReformate?> au <?php echo $dateFinReformate?>.</p>
	<p> Son prix est de <?php echo $_POST["prixTotal"]?>.</p>
	<p> L'équipe <strong>Bangaloducul</strong> vous souhaite un bon séjour !</p>

<?php endif;
endif;?>


</body>
</html>