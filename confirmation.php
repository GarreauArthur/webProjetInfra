<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
// s'il n'y a pas de paramètre, ou si c'est une mauvaise valeur
if ( EMPTY($_SESSION["hotel"])     || !is_numeric($_SESSION["hotel"])
  || EMPTY($_SESSION["dateDebut"]) || EMPTY($_SESSION["dateFin"])
  || EMPTY($_SESSION["chambre"])   || EMPTY($_POST["nom"])
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
	                ':hotel'   => $_SESSION["hotel"],
	                ':dStart'  => $_SESSION["dateDebut"],
	                ':dEnd'  => $_SESSION["dateFin"]
                    )
			 );
$numeroChambreDisponible = $numeroChambre->fetchAll(); // récupération des numéros de chambres disponible à cet hôtel 
?>

<!DOCTYPE html>
<html lang="fr">
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
    if ($numero["numerochambre"] == $_SESSION["chambre"]){
		$estDisponible=true;
	}
}

if( !$estDisponible) : ?>
	<h1>Dommage ! Quelqu'un a réservé cette chambre pendant que vous vous identifiiez...</h1>
	<a href="choixDates.php?hotel=<?php echo $_SESSION["hotel"]?>">Choisir de nouvelles dates</a>

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
			':dateDebut' => $_SESSION["dateDebut"],
			':dateFin'   => $_SESSION["dateFin"],
			':client'    => $idClient,
	        ':hotel'     => $_SESSION["hotel"],
	        ':chambre'   => $_SESSION["chambre"]
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

			$dateDebut = $_SESSION["dateDebut"];
			$dateDebutReformate = date("d-m-Y", strtotime($dateDebut));
			$dateFin = $_SESSION["dateFin"];
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
	<p> Vous avez choisi la chambre <?php echo htmlspecialchars($_SESSION["chambre"])?> de l'hôtel <?php echo htmlspecialchars($_SESSION["nomHotel"])?> !</p>
	<p> Vous l'avez réservé au nom de <strong><?php echo htmlspecialchars($_POST["prenom"])?> <?php echo htmlspecialchars($_POST["nom"])?></strong> du <?php echo $dateDebutReformate?> au <?php echo $dateFinReformate?>.</p>
	<p> Son prix est de <?php echo htmlspecialchars($_SESSION["prixTotal"])?>.</p>
	<p> L'équipe <strong>Bangaloducul</strong> vous souhaite un bon séjour !</p>

<?php endif;
endif;?>


</body>
</html>