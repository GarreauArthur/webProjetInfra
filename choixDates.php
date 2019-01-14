<?php
// s'il n'y a pas de paramètre, ou si c'est une mauvaise valeur
if(EMPTY($_GET["hotel"]) || !is_numeric($_GET["hotel"]))
{
	// on retourne à la page d'accueil
	header('Location: ./index.php');
}

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
if (!EMPTY($_GET["msg"]) && $_GET["msg"]=="error"){
?>
	<h1 style="color: red"> Erreur : dates invalides, veuillez en choisir de nouvelles.</h1>
<?php
}

// on se connecte à la base de données
include_once('./dbconnection.php');
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
		<h1>Obtenez la meilleure expérience dans nos hôtels</h1>
		<h2>Sélectionnez vos dates de réservation</h2>
		<!-- adds info about the hotel -->
	</header>
	<form method="POST" action="chambres.php">
		<input type="hidden" name="hotel" value="<?= $res["id"]; ?>">
		Date de début : <input id="dateDbt" type="date" name="dateStart" min="<?php echo date('Y-m-d');?>">
		Date de fin : <input id="dateFin" type="date" name="dateEnd">
		<input type="submit">
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