<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
      <title>Owners</title>
   </head>
   <body>
   <!-- Complete the URL -->
   
   <a href='https://ssl.students.engr.scu.edu/~xliu11/showOwner.php?show=true'>Show Owner Information</a>
   <br><br>
  
<?php

if (isset($_GET['show'])) {
    showO();
}

function showO(){
	//connect to your database. Type in your username, password and the DB path
	$conn=oci_connect('xliu11','helenliuxi13','//oracle.engr.scu.edu/db11g');
	if(!$conn) {
	     print "<br> connection failed:";       
        exit;
	}		

	$query = oci_parse($conn, "SELECT *	FROM Owner");
	
	// Execute the query
	oci_execute($query);
	while (($row = oci_fetch_array($query, OCI_BOTH)) != false) {		
		print "Owner Name:";	
		echo "<font color='green'> $row[0] </font></br>";		
		print "Owner address:";
		echo "<font color='green'> $row[1] </font>";
		print ", ";
		echo "<font color='green'> $row[2] </font>";
		print ", ";	
		echo "<font color='green'> $row[3] </font></br>";
		print "Owner Phone";	
		echo "<font color='green'> $row[4] </font></br></br></br>";
	}
	OCILogoff($conn);	
}


?>
<!-- end PHP script -->
   </body>
</html>

