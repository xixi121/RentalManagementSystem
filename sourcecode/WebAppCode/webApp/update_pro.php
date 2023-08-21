<?php


update();


function update(){
	//connect to your database. Type in your username, password and the DB path
	$conn=oci_connect('xliu11','helenliuxi13','//oracle.engr.scu.edu/db11g');
	if(!$conn) {
	     print "<br> connection failed:";       
        exit;
	}
	$sql = 'BEGIN update_property;END;';
	$stmt = oci_parse($conn, $sql);
	
	// Execute the query
	$res = oci_execute($stmt);
	if($res){
		echo "Successfully updated the information of properties in real time.<br><br>";
	}else{
		echo "Error".$stmt->error;
	}

	OCILogoff($conn);	
}

?>
<!-- end PHP script -->


