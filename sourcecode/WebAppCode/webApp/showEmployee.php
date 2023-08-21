<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
      <title>Employees</title>
   </head>
   <body>
   <!-- Complete the URL -->
   
   <a href='https://ssl.students.engr.scu.edu/~xliu11/showEmployee.php?show=true'>Show All Employee Information</a>
   <br><br>
  
<?php

if (isset($_GET['show'])) {
    showEmp();
}

function showEmp(){
	//connect to your database. Type in your username, password and the DB path
	$conn=oci_connect('xliu11','helenliuxi13','//oracle.engr.scu.edu/db11g');
	if(!$conn) {
	     print "<br> connection failed:";       
        exit;
	}		

	$query = oci_parse($conn, "SELECT *	FROM Employees");
	
	// Execute the query
	oci_execute($query);
	while (($row = oci_fetch_array($query, OCI_BOTH)) != false) {		
		print "Employee ID:";	
		echo "<font color='green'> $row[0] </font>";		
		print ",  Name:";	
		echo "<font color='green'> $row[1] </font>";
		print ",  Phone:";	
		echo "<font color='green'> $row[2] </font></br>";
		print "Start Date:";
		echo "<font color='green'> $row[3] </font></br>";
		print "Position:";
		echo "<font color='green'> $row[4] </font></br>";
		print "Manager ID:";	
		echo "<font color='green'> $row[5] </font></br>";
		print "Branch No:";	
		echo "<font color='green'> $row[6] </font></br></br></br>";
	}
	OCILogoff($conn);	
}


?>
<!-- end PHP script -->
   </body>
</html>

