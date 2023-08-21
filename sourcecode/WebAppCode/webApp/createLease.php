
<?php
if($_POST){
    
    // collect input data
	// Get the renter's name	
	$name = $_POST['name'];

	// Get the property number
    $ProNo = $_POST['PropertyNo']; 

	// Get the renter's phone number	
    $RPhone = $_POST['phone']; 

	// Get the renter's address	
	$str = $_POST['street'];
	$city = $_POST['city'];
	$zip = $_POST['zipcode'];
	 
	// Get the start and end date and check if it is valid length.
	$start_date = $_POST['start_date'];
	$end_date = $_POST['end_date'];
	$start_date = DateTime::createFromFormat('Y-m-d', $start_date);
	$end_date = DateTime::createFromFormat('Y-m-d', $end_date);
	$diff = date_diff($start_date,$end_date);
	if($diff->y > 1 || $diff->m < 6){
		die("rent length should be more than 6 months and less than 1 year");
    }else if($diff->y == 1 && $diff->m > 6){
		die("rent length should be more than 6 months and less than 1 year");
	}
	if(!is_numeric($ProNo)){
		die("Property number must be a number");
	}

    function prepareInput($inputData){
		$inputData = trim($inputData);
  		$inputData  = htmlspecialchars($inputData);
  		return $inputData;
	}

	$name = prepareInput( $name);		
	$str = prepareInput( $str);		
	$city = prepareInput( $city);	
	// Format the date as "d-M-Y" 
    $start_date = $start_date->format('d-M-Y');
	$end_date = $end_date->format('d-M-Y');

	//connect to your database. Type in your username, password and the DB path
	$conn=oci_connect('xliu11','helenliuxi13','//oracle.engr.scu.edu/db11g');
	if(!$conn) {
    	print "<br> connection failed: ";       
    	exit;
	}
	//Call the procedure "create_lease" in the database	
	$sql = 'BEGIN create_lease(:no,:start_date,:end_date,:name,:phone,:str,:city,:zip);END;';
	$stmt = oci_parse($conn, $sql);
	//Bind the input parameter
	oci_bind_by_name($stmt, ':no', $ProNo);
	oci_bind_by_name($stmt, ':start_date', $start_date);
	oci_bind_by_name($stmt, ':end_date', $end_date);
	oci_bind_by_name($stmt, ':name', $name);
	oci_bind_by_name($stmt, ':phone', $RPhone);
	oci_bind_by_name($stmt, ':str', $str);
	oci_bind_by_name($stmt, ':city', $city);
	oci_bind_by_name($stmt, ':zip', $zip,5);	 	
	
	// Execute the query
	$res = oci_execute($stmt);
	if($res){
		echo "Lease application has sent successfully.<br><br>";
		$query = oci_parse($conn, "SELECT l.LeaseNo, p.ProNo, p.ProStr, p.ProCity, p.Prozip, 			p.rent, r.Rname, r.RStr, r.RCity, r.RZip, r.RPhone, l.start_date, l.end_date, 	l.deposit 
		FROM Property p, Renter r, Lease l 
		WHERE p.ProNo = :ProNo AND l.ProNo = :ProNo AND R.RPhone = :phone");
		oci_bind_by_name($query, ':ProNo', $ProNo);
		oci_bind_by_name($query, ':phone', $RPhone);
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
			echo "<font color='green'> $row[13] </font></br></br></br>";}	
	}else{
		echo "Error creating lease from your input: ".$stmt->error;
	}

	OCILogoff($conn);


}
?>

