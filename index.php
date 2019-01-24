<?php

include_once('dbConnection.php'); 

$query = $connection->query("SELECT * FROM hotels");
$res = $query->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>eseotel</title>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="./lecss.css">
	<link rel="shortcut icon" href="./fav.ico" type="image/x-icon">
</head>
<body>

	<header>
		<h1> ESEOTEL</h1>
		<h2>Obtenez la meilleure expérience dans nos hôtels</h2>
		<h3>Choisissez celui dans lequel vous voulez rester</h3>
	</header>

	<div id="listHotels">

	<?php foreach ($res as $val) { ?>
		
		<a href="choixDates.php?hotel=<?=$val['id'] ?>" class="lien-hotel">
			<div class="hotel">
				<p><?=$val['nom'] ?></p>
				<i class="fas fa-hotel"></i>
				<p><?=$val['adresse'] ?></p>
			</div>
		</a>
	
	<?php
	}
	?>
	</div>
</body>
</html>