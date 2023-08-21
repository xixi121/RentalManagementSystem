<?php
if($_POST){
    print "Rental Properties";
    // collect input data
    $BrName = $_POST['BrName']; 
	$OPhone = $_POST['OPhone'];
	$conn=oci_connect('xliu11','helenliuxi13','//oracle.engr.scu.edu/db11g');
	if(!$conn) {
	     print "<br> connection failed:";       
        exit;
	}		
		$query = oci_parse($conn, "SELECT o.OName, o.OPhone, b.BrName, p.ProNo, p.ProStr, p.ProCity, p.ProZip, p.status, p.av_date FROM Property p JOIN Employees e ON e.EmpID = p.SupervisorID JOIN Branch b ON b.BrNo = e.BrNo JOIN Owner o ON p.OPhone = o.OPhone
WHERE b.BrName = :BrName AND o.OPhone = :OPhone AND p.OPhone = :OPhone");
		oci_bind_by_name($query, ':BrName', $BrName);
		oci_bind_by_name($query, ':OPhone', $OPhone);

		// Execute the query
		oci_execute($query);
		while (($row = oci_fetch_array($query, OCI_BOTH)) != false){			
			print "Owner Name:";	
			echo "<font color='green'> $row[0] </font>";		
			print "  Owner Phone:";	
			echo "<font color='green'> $row[1] </font></br>";
			print "Branch Name:";
			echo "<font color='green'> $row[2] </font></br>";
			print "Property No:";
			echo "<font color='green'> $row[3] </font></br>";
			print "Property Address:";	
			echo "<font color='green'> $row[4] </font>";
			print ",  ";
			echo "<font color='green'> $row[5] </font>";
			print ",  ";
			echo "<font color='green'> $row[6] </font></br>";
			print "Status:";
			echo "<font color='green'> $row[7] </font>";
			print "   Available date:";
			echo "<font color='green'> $row[8] </font></br></br>";
	}
	OCILogoff($conn);	
}

?>



