<?php

include_once('dbConnection.php'); 

$query = $connection->query("SELECT * FROM hotels");
$res = $query->fetchAll()

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>hothell</title>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="./lecss.css">

</head>
<body>


	<header>
		<h1>Get the best experience in our hotels</h1>
		<h2>Pick the one you want to stay in</h2>
	</header>

	<div id="listHotels"><!--

	<?php foreach ($res as $val) { ?>
		--><a href="choixDates.php?hotel=<?=$val['id'] ?>" class="hotel"><!--
			--><div><!--
				--><p><?=$val['nom'] ?></p><!--
				--><i class="fas fa-hotel"></i><!--
				--><p><?=$val['adresse'] ?></p><!--
			--></div><!--
		--></a><!--
	<?php
	}
	?>
	--></div>
</body>
</html>