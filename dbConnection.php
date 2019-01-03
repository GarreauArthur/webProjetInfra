<?php
try {
	$connection = new PDO('pgsql:dbname=hothell;host=localhost;user=postgres;password=postgres'); // :)
}
catch ( PDOException $e ) {
    die('Connection failed: ' . $e->getMessage());
}