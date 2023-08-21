<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
      <title>Expire Properties</title>
   </head>
   <body>
   <!-- Complete the URL -->
   
   <a href='https://ssl.students.engr.scu.edu/~xliu11/ex_pro.php?show=true'>Properties with Leases Expired Within 2 Months</a>
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

	$query = oci_parse($conn, "SELECT p.ProNo, p.ProStr, p.ProCity, p.Prozip, o.OName, l.end_date 	
	FROM Property p, Owner o, Lease l 
	WHERE l.end_date IN (SELECT end_date FROM Lease WHERE end_date >= SYSDATE AND end_date <= ADD_MONTHS(SYSDATE, 2)) 
	AND p.ProNo = l.ProNo AND p.OPhone = o.OPhone");
	
	// Execute the query
	oci_execute($query);
	while (($row = oci_fetch_array($query, OCI_BOTH)) != false) {		
		print "Property No:";	
		echo "<font color='green'> $row[0] </font></br>";
		print "property address:";
		echo "<font color='green'> $row[1] </font>";
		print ", ";
		echo "<font color='green'> $row[2] </font>";
		print ", ";	
		echo "<font color='green'> $row[3] </font></br>";
		print "Owner Name:";	
		echo "<font color='green'> $row[4] </font></br>";
		print "End Date:";	
		echo "<font color='green'> $row[5] </font></br></br></br>";		
	}
	OCILogoff($conn);	
}




?>
<!-- end PHP script -->
   </body>
</html>

