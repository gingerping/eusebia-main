<?php
	
 
	$conn = new PDO( 'mysql:host=localhost;dbname=eusebia', 'root', '');
	if(!$conn){
		die("Error: Failed to connect to database!");
	}

?>