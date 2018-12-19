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

<style type="text/css">
	body{
		margin:auto;
		width: 760px;
	}
	.hotel{
		background-color: #6161ff;
		width:240px;
		min-height: 250px;
		text-align: center;
		color:white;
		display: inline-block;
		vertical-align: top;
		margin:5px;
	}
	.fa-hotel{font-size: 100px}
	header{
		text-align: center;
	}
	a{
		text-decoration:none;
	}
	#listHotels{
		margin:auto;
		display: flex;
		flex-wrap:wrap;
	}
</style>

</head>
<body>


	<header>
		<h1>Get the best experience in our hotels</h1>
		<h2>Pick the one you want to stay in</h2>
	</header>

	<div id="listHotels"><!--

	<?php foreach ($res as $val) { ?>
		--><a href="date.php?hotel=<?=$val['id'] ?>" class="hotel"><!--
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