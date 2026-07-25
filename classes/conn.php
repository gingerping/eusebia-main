<?php
	
 
	$conn = new PDO( 'mysql:host=localhost;dbname=jean_files', 'root', '');
	if(!$conn){
		die("Error: Failed to connect to database!");
	}

?>