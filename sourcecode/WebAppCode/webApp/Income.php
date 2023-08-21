<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
      <title>Monthly Income</title>
   </head>
   <body>
   <!-- Complete the URL -->
   
   <a href='https://ssl.students.engr.scu.edu/~xliu11/Income.php?show=true'>Strawberryfield Inc Monthly Income</a>
   <br><br>
  
<?php

if (isset($_GET['show'])) {
    showIncome();
}

function showIncome(){
	//connect to your database. Type in your username, password and the DB path
	$conn=oci_connect('xliu11','helenliuxi13','//oracle.engr.scu.edu/db11g');
	if(!$conn) {
	     print "<br> connection failed:";       
        exit;
	}		

	$query = oci_parse($conn, "SELECT get_income(SYSDATE) FROM DUAL");
	
	// Execute the query
	oci_execute($query);
	while (($row = oci_fetch_array($query, OCI_BOTH)) != false) {		
		print "Monthly Income:";	
		echo "<font color='green'> $row[0] </font></br></br></br>";		
	}
	OCILogoff($conn);	
}




?>
<!-- end PHP script -->
  	<p>The rental agency earns 10% of the rent of the properties that are currently rented per month.</p>
   </body>
</html>

