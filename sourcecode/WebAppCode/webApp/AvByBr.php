<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
      <title>Available Pro by Branch</title>
   </head>
   <body>
   <!-- Complete the URL -->
   
   <a href='https://ssl.students.engr.scu.edu/~xliu11/AvByBr.php?show=true'>Number of Available Properties by Branch</a>
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

	$query = oci_parse($conn, "SELECT b.BrName, COUNT(p.ProNo) FROM Branch b
	JOIN Employees e ON b.BrNo = e.BrNo
	JOIN Property p ON e.EmpID = p.supervisorID
	WHERE p.status='available' OR p.av_date < SYSDATE
	GROUP BY b.BrName");
	
	// Execute the query
	oci_execute($query);
	while (($row = oci_fetch_array($query, OCI_BOTH)) != false) {		
		print "Branch Name:";	
		echo "<font color='green'> $row[0] </font>";
		print ",   Number of Available Properties:";
		echo "<font color='green'> $row[1] </font></br></br></br>";	
	}
	OCILogoff($conn);	
}




?>
<!-- end PHP script -->
   </body>
</html>

