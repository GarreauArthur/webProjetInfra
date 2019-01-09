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
		<h1>Get the best experience in our hotels</h1>
		<h2>Select the dates</h2>
		<!-- adds info about the hotel -->
	</header>
	<form method="POST" action="chambres.php">
		<input type="hidden" name="hotel" value="<?= $res["id"]; ?>">
		Date start : <input type="date" name="dateStart">
		Date end : <input type="date" name="dateEnd">
		<input type="submit">
	</form>
</body>
</html>