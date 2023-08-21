<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
      <title>Lease Agreement</title>
   </head>
   <body>
   <!-- Complete the URL -->
   
   <a href='https://ssl.students.engr.scu.edu/~xliu11/showLease.php?show=true'>Show All Lease Agreements</a>
   <br><br>
  
<?php

if (isset($_GET['show'])) {
    showLease();
}

function showLease(){
	//connect to your database. Type in your username, password and the DB path
	$conn=oci_connect('xliu11','helenliuxi13','//oracle.engr.scu.edu/db11g');
	if(!$conn) {
	     print "<br> connection failed:";       
        exit;
	}		

	$query = oci_parse($conn, "SELECT l.LeaseNo, p.ProNo, p.ProStr,p.ProCity,p.Prozip, l.deposit, r.Rname,r.RStr,r.RCity,r.RZip,r.RPhone,l.start_date, l.end_date, l.deposit 	
	FROM Property p, Renter r, Lease l 
	WHERE p.ProNo = l.ProNo AND l.RPhone = R.RPhone 
	ORDER BY l.LeaseNo");
	
	// Execute the query
	oci_execute($query);
	while (($row = oci_fetch_array($query, OCI_BOTH)) != false) {		
		print "lease number:";	
		echo "<font color='green'> $row[0] </font>";		
		print ",  property number:";	
		echo "<font color='green'> $row[1] </font></br>";
		print "property address:";
		echo "<font color='green'> $row[2] </font>";
		print ", ";
		echo "<font color='green'> $row[3] </font>";
		print ", ";	
		echo "<font color='green'> $row[4] </font></br>";
		print "rent:";
		echo "<font color='green'> $row[5] </font></br>";
		print "renter:";
		echo "<font color='green'> $row[6] </font></br>";
		print "renter's address:";
		echo "<font color='green'> $row[7] </font>";
		print ", ";
		echo "<font color='green'> $row[8] </font>";
		print ", ";
		echo "<font color='green'> $row[9] </font></br>";
		print "renter's phone:";
		echo "<font color='green'> $row[10] </font></br>";
		print "start date:";
		echo "<font color='green'> $row[11] </font>";
		print "  end date:";
		echo "<font color='green'> $row[12] </font>";
		print "  deposit:";
		echo "<font color='green'> $row[13] </font></br></br></br>";
	}
	OCILogoff($conn);	
}




?>
<!-- end PHP script -->
   </body>
</html>

