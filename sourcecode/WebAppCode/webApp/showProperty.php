<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
      <title>Pro</title>
   </head>
   <body>
   <!-- Complete the URL -->
   
   <a href='https://ssl.students.engr.scu.edu/~xliu11/showProperty.php?show=true'>Show Property Information</a>
   <br><br>
  
<?php

if (isset($_GET['show'])) {
    showP();
}

function showP(){
	//connect to your database. Type in your username, password and the DB path
	$conn=oci_connect('xliu11','helenliuxi13','//oracle.engr.scu.edu/db11g');
	if(!$conn) {
	     print "<br> connection failed:";       
        exit;
	}		

	$query = oci_parse($conn, "SELECT *	FROM Property");
	// Execute the query
	oci_execute($query);
	while (($row = oci_fetch_array($query, OCI_BOTH)) != false) {		
		print "Property Number:";	
		echo "<font color='green'> $row[0] </font></br>";		
		print "Property address:";
		echo "<font color='green'> $row[1] </font>";
		print ", ";
		echo "<font color='green'> $row[2] </font>";
		print ", ";	
		echo "<font color='green'> $row[3] </font></br>";
		print "Room:";	
		echo "<font color='green'> $row[4] </font>";
		print "   Rent:";	
		echo "<font color='green'> $row[5] </font></br>";
		print "Status:";	
		echo "<font color='green'> $row[6] </font>";
		print "Available Date:";	
		echo "<font color='green'> $row[7] </font></br>";
		print "Supervisor ID:";	
		echo "<font color='green'> $row[8] </font></br>";
		print "Owner Phone:";	
		echo "<font color='green'> $row[9] </font></br></br></br>";
	}
	OCILogoff($conn);	
}


?>
<!-- end PHP script -->
   </body>
</html>

