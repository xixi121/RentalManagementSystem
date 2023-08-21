<?php
if($_POST){
    
    // collect input data
    $ProNo = $_POST['PropertyNo']; 
    $phone = $_POST['phone']; 

	//connect to your database. Type in your username, password and the DB path
	$conn=oci_connect('xliu11','helenliuxi13','//oracle.engr.scu.edu/db11g');
	if(!$conn) {
	     print "<br> connection failed:";       
        exit;
	}		

	$query = oci_parse($conn, "SELECT l.LeaseNo, p.ProNo, p.ProStr, p.ProCity, p.Prozip, l.deposit, r.Rname, r.RStr, r.RCity, r.RZip, r.RPhone, l.start_date, l.end_date, l.deposit 	
	FROM Property p, Renter r, Lease l 
	WHERE p.ProNo = :ProNo AND l.ProNo = :ProNo AND l.RPhone = :phone AND R.RPhone = :phone");
	oci_bind_by_name($query, ':ProNo', $ProNo);
	oci_bind_by_name($query, ':phone', $phone);
	// Execute the query
	oci_execute($query);
	while (($row = oci_fetch_array($query, OCI_BOTH)) != false) {		
		// We can use either numeric indexed starting at 0 
		// or the column name as an associative array index to access the colum value
		// Use the uppercase column names for the associative array indices	
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



