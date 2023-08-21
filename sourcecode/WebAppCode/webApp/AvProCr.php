<?php
if($_POST){
    print "Available Properties";
    // collect input data
    $city = $_POST['city']; 
	$room = $_POST['room'];
	$rent = $_POST['rent'];
	$conn=oci_connect('xliu11','helenliuxi13','//oracle.engr.scu.edu/db11g');
	if(!$conn) {
	     print "<br> connection failed:";       
        exit;
	}		
		$query = oci_parse($conn, "SELECT * FROM Property WHERE ProCity = :city AND (room = :room OR rent <= :rent) AND (status = 'available' OR av_date < SYSDATE)");
		oci_bind_by_name($query, ':city', $city);
		oci_bind_by_name($query, ':room', $room);
		oci_bind_by_name($query, ':rent', $rent);

		// Execute the query
		oci_execute($query);
		while (($row = oci_fetch_array($query, OCI_BOTH)) != false){			
			print "Property Number:";	
			echo "<font color='green'> $row[0] </font></br>";		
			print "Property address:";
			echo "<font color='green'> $row[1] </font>";
			print ", ";
			echo "<font color='green'> $row[2] </font>";
			print ", ";	
			echo "<font color='green'> $row[3] </font></br>";
			print "Room:";	
			echo "<font color='green'> $row[4] </font>";
			print "   Rent:";	
			echo "<font color='green'> $row[5] </font></br>";
			print "Status:";	
			echo "<font color='green'> $row[6] </font>";
			print "Available Date:";	
			echo "<font color='green'> $row[7] </font></br>";
			print "Supervisor ID:";	
			echo "<font color='green'> $row[8] </font></br>";
			print "Owner Phone:";	
			echo "<font color='green'> $row[9] </font></br></br></br>";
	}
	OCILogoff($conn);	
}

?>



