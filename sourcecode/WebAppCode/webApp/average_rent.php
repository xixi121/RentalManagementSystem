<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
      <title>Average Rent</title>
   </head>
   <body>
   <!-- Complete the URL -->
   
   <a href='https://ssl.students.engr.scu.edu/~xliu11/average_rent.php?show=true'>Average Rent Per Month</a>
   <br><br>
  
<?php

if (isset($_GET['show'])) {
    showRent();
}

function showRent(){
	//connect to your database. Type in your username, password and the DB path
	$conn=oci_connect('xliu11','helenliuxi13','//oracle.engr.scu.edu/db11g');
	if(!$conn) {
	     print "<br> connection failed:";       
        exit;
	}		

	$query = oci_parse($conn, "SELECT 
							(SELECT COUNT(*) FROM Property WHERE status = 'available'),
							(SELECT COUNT(*) FROM Property WHERE status != 'available'),
							(SELECT AVG(rent) FROM Property)
							FROM DUAL");
	
	// Execute the query
	oci_execute($query);
	while (($row = oci_fetch_array($query, OCI_BOTH)) != false) {		
		print "Number of Available Properties:";	
		echo "<font color='green'> $row[0] </font></br>";
		print "Number of Leased Properties";
		echo "<font color='green'> $row[1] </font></br>";
		print "Average Rent for All Properties:";	
		echo "<font color='green'> $row[2] </font></br></br></br>";		
	}
	OCILogoff($conn);	
}




?>
<!-- end PHP script -->
   </body>
</html>

