<?php
if($_POST){
    print "Available Properties at Branch ";
	print $_POST['BrName'];
	echo "</br></br>";
    // collect input data
    $BrName = $_POST['BrName']; 
	$value = "available";

	//connect to your database. Type in your username, password and the DB path
	$conn=oci_connect('xliu11','helenliuxi13','//oracle.engr.scu.edu/db11g');
	if(!$conn) {
	     print "<br> connection failed:";       
        exit;
	}		

	$query = oci_parse($conn, "SELECT p.* FROM Property p JOIN Employees e ON e.EmpID = p.SupervisorID JOIN Branch b ON b.BrNo = e.BrNo
						WHERE b.BrName = :BrName and (status= :value OR av_date<SYSDATE)");
	oci_bind_by_name($query, ':BrName', $BrName);
	oci_bind_by_name($query, ':value', $value);

	// Execute the query
	oci_execute($query);
	while (($row = oci_fetch_array($query, OCI_BOTH)) != false) {			
		print "Property number:";	
		echo "<font color='green'> $row[0] </font></br>";		
		print "Property address:";	
		echo "<font color='green'> $row[1] </font>";
		print "  ,";
		echo "<font color='green'> $row[2] </font>";
		print "  ,";
		echo "<font color='green'> $row[3] </font></br>";
		print "Room:";	
		echo "<font color='green'> $row[4] </font>";
		print "   Rent:";
		echo "<font color='green'> $row[5] </font></br>";
		print "Status:";
		echo "<font color='green'> $row[6] </font>";
		print "   Available Date:";
		echo "<font color='green'> $row[7] </font></br>";
		print "Supervisor ID:";
		echo "<font color='green'> $row[8] </font>";
		print ",   Owner's Phone";
		echo "<font color='green'> $row[9] </font></br></br></br>";
	}
	OCILogoff($conn);	
}

?>



