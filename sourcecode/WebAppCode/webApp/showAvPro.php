<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
      <title>Available Properties</title>
   </head>
   <body>
   <!-- Complete the URL -->
   
   <a href='https://ssl.students.engr.scu.edu/~xliu11/showAvPro.php?show=true'>Show Available Properties</a>
   <br><br>
  
<?php

if (isset($_GET['show'])) {
	showPro();
}

function showPro(){
	//connect to your database. Type in your username, password and the DB path
	$conn=oci_connect('xliu11','helenliuxi13','//oracle.engr.scu.edu/db11g');
	if(!$conn) {
	     print "<br> connection failed:";       
        exit;
	}
	$query = oci_parse($conn, "SELECT *
	FROM Property
	WHERE status = 'available' OR av_date < SYSDATE");
	
	// Execute the query
	oci_execute($query);
	while (($row = oci_fetch_array($query, OCI_BOTH)) != false) {			
		print "property number:";	
		echo "<font color='red'> $row[0] </font></br>";
		print "property address:";
		echo "<font color='green'> $row[1] </font>";
		print ", ";
		echo "<font color='green'> $row[2] </font>";
		print ", ";	
		echo "<font color='green'> $row[3] </font></br>";
		print"room: ";
		echo "<font color='green'> $row[4] </font></br>";
		print "rent:";
		echo "<font color='green'> $row[5] </font></br>";
		print "status:";
		echo "<font color='green'> $row[6] </font></br>";
		print "available date:";
		echo "<font color='green'> $row[7] </font>";
		print "supervisorID: ";
		echo "<font color='green'> $row[8] </font>";
		print "owner's phone: ";
		echo "<font color='green'> $row[9] </font></br></br></br>";
	}
	OCILogoff($conn);	
}

?>
<!-- end PHP script -->
	<p>Available properties include the properties not leased now, and the properties whose leases end before today.</p>
   </body>
</html>

