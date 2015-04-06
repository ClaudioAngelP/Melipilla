<?php require_once('../../conectar_db.php');
# Resources
# http://wiki.oracle.com/page/PHP+Oracle+FAQ
# http://download-west.oracle.com/docs/cd/B12037_01/network.101/b10775/naming.htm#i498306
# http://ubuntuforums.org/showthread.php?p=7581997
       
        $username2 = 'INTERFACE_TERAPRIM';
        //$password2 = 'INTERFACE_TERAPRIMSUP';
        $password2 = 'sidra#teraprim';
        $host2 = '10.8.165.15';
        $port2 = '1521';
        $service_name2 = 'EMR';
        $tns_service_name2 = 'SSMN_EMR';
        $nn=0;
        
        $run=$_GET['paciente_rut'];
        
        if(strlen($run)==9){
			$run_chk=str_pad($run, 10, "0", STR_PAD_LEFT);  // agrega "0" a la izquierda;
			$run=$run_chk;
		}

        $conn2 = oci_connect($username2, $password2, "//$host2:$port2/$service_name2");
if (!$conn2) {
    $e2 = oci_error();
    trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
}
	// Prepare the statement

$stid = oci_parse($conn2, "SELECT *
							FROM recepta");

if (!$stid) {
    $e2 = oci_error($conn2);
    trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
}

// Perform the logic of the query
$r = oci_execute($stid);
if (!$r) {
    $e2 = oci_error($stid);
    trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
}

// Fetch the results of the query
print "<table border='1'>\n";
while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
	$nn++;
  print "<tr>\n";
    foreach ($row as $item) {
        print " <td>$nn</td><td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
    }
    print "</tr>\n";
  
}

if(oci_num_rows($stid)==0){
		print("Sin Registros...");
}

print "</table>\n";

oci_free_statement($stid);
oci_close($conn2);

?>
