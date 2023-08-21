<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
      <title>Manager Supervisor Property</title>
   </head>
   <body>
   <!-- Complete the URL -->
   
   <a href='https://ssl.students.engr.scu.edu/~xliu11/MaSuPro.php?show=true'>List of managers, their supervisors and supervisors' properties</a>
   <br><br>
  
<?php

if (isset($_GET['show'])) {
    showL();
}

function showL(){
	//connect to your database. Type in your username, password and the DB path
	$conn=oci_connect('xliu11','helenliuxi13','//oracle.engr.scu.edu/db11g');
	if(!$conn) {
	     print "<br> connection failed:";       
        exit;
	}		

	$query = oci_parse($conn, "SELECT t.EmpName, e.ManagerID, e.EmpName, e.EmpID, p.ProNo, p.ProStr, p.ProCity, p.ProZip
	FROM Employees e, Employees t, Property p 
	WHERE e.position = 'SUPERVISOR' AND t.EmpID = e.ManagerID AND p.supervisorID = e.EmpID ORDER BY e.ManagerID");
	
	// Execute the query
	oci_execute($query);
	while (($row = oci_fetch_array($query, OCI_BOTH)) != false) {		
		print "Manager Name:";	
		echo "<font color='green'> $row[0] </font>";
		print ",   Manager ID:";
		echo "<font color='green'> $row[1] </font></br>";
		print "Supervisor Name:";	
		echo "<font color='green'> $row[2] </font>";
		print ",   Supervisor ID:";
		echo "<font color='green'> $row[3] </font></br>";
		print "Property No:";
		echo "<font color='green'> $row[4] </font></br>";
		print "Property Address:";	
		echo "<font color='green'> $row[5] </font>";
		print ", ";
		echo "<font color='green'> $row[6] </font>";
		print ",  ";
		echo "<font color='green'> $row[7] </font></br></br></br>";
	}
	OCILogoff($conn);	
}




?>
<!-- end PHP script -->
   </body>
</html>

