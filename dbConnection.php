<?php
try {
	$connection = new PDO('pgsql:dbname=test;host=localhost;user=root;password=root');
}
catch ( PDOException $e ) {
    die('Connection failed: ' . $e->getMessage());
}