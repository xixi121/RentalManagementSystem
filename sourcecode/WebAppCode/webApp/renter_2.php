<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
      <title>Renters rent more than 1</title>
   </head>
   <body>
   <!-- Complete the URL -->
   
   <a href='https://ssl.students.engr.scu.edu/~xliu11/renter_2.php?show=true'>Renters rented more than one properties</a>
   <br><br>
  
<?php

if (isset($_GET['show'])) {
    showRenter();
}

function showRenter(){
	//connect to your database. Type in your username, password and the DB path
	$conn=oci_connect('xliu11','helenliuxi13','//oracle.engr.scu.edu/db11g');
	if(!$conn) {
	     print "<br> connection failed:";       
        exit;
	}		

	$query = oci_parse($conn, "SELECT RName, RPhone FROM Renter
	WHERE RPhone IN (SELECT RPhone FROM Lease GROUP BY RPhone HAVING count(*)>1)");
	
	// Execute the query
	oci_execute($query);
	while (($row = oci_fetch_array($query, OCI_BOTH)) != false) {		
		print "Renter's Name:";	
		echo "<font color='green'> $row[0] </font>";
		print ",   Renter's Phone:";
		echo "<font color='green'> $row[1] </font></br></br></br>";	
	}
	OCILogoff($conn);	
}




?>
<!-- end PHP script -->
   </body>
</html>

