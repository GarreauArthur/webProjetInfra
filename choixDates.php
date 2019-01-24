<?php
session_start();
// s'il n'y a pas de paramètre, ou si c'est une mauvaise valeur
if(EMPTY($_GET["hotel"]) || !is_numeric($_GET["hotel"]))
{
	// on retourne à la page d'accueil
	header('Location: ./index.php');
}

$_SESSION["hotel"] = $_GET["hotel"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Choix dates</title>
	<link rel="stylesheet" type="text/css" href="./lecss.css">
</head>
<body>

<?php

// on se connecte à la base de données
include_once('./dbConnection.php');
// on récupère les infos
$stm = $connection->prepare("SELECT * FROM hotels WHERE id = ?");
$stm->execute(array($_GET["hotel"]));
// si on a rien trouvé on dégage
if($stm->rowCount() != 1)
{
	header('Location: ./index.php');
}

$res = $stm->fetch();

?>

	<header>
		<h1>Dates</h1>
		<h2>Obtenez la meilleure expérience dans nos hôtels</h2>
		<h3>Sélectionnez vos dates de réservation</h3>
		<!-- adds info about the hotel -->
	</header>
	<form method="POST" action="chambres.php">
		<div class="dates">
			<div class="input-dates">Date de début : <input class="input" id="dateDbt" type="date" name="dateStart" min="<?php echo date('Y-m-d');?>"></div>
			<div class="input-dates">Date de fin : <input class="input" id="dateFin" type="date" name="dateEnd"></div>
			<div class="input-dates"><input class="submit" type="submit"></div>
		</div>
		
	</form>

	<script>
		function addDays(date, days) { // On ajoute un jour à la date de dbt pour connaitre la date minimum de fin de séjour
			var result = new Date(date);
			result.setDate(result.getDate() + days);
			return result;
		}
		var dateDbt = document.getElementById("dateDbt");
		var dateFin = document.getElementById("dateFin");
		dateDbt.addEventListener("input", function (e){
			console.log("bla");
			dateFin.min = addDays(dateDbt.value, 1).toISOString().substring(0,10);
			console.log(dateFin.min);
		});
	</script>

</body>
</html>
