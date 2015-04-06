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

$stid = oci_parse($conn2, "SELECT ID_PATIENT_NEW,ID_PATIENT,FLG_STATUS,PATIENT_RUN,PATIENT_RUT,PATIENT_NAMES,PATIENT_FATHER_SURNAME,
							PATIENT_MOTHER_SURNAME,GENDER,TO_CHAR(DT_BIRTH,'DD/MM/YYYY')AS DT_BIRTH,ID_ADDRESS_TYPE,DESC_ADDRESS_TYPE,
							ADDRESS,ADDRESS_NUMBER,ADDRESS_COMPLEMENT,ID_COMUNA,DESC_COMUNA,POSTAL_CODE,ID_COUNTRY,DESC_COUNTRY,
							ID_STATE,DESC_STATE,ID_CITY,DESC_CITY,PHONE_NUM,ID_SCHOLARSHIP,DESC_SCHOLARSHIP,ID_OCCUPAPTION,DESC_OCCUPATION,
							DOCUMENT_NUMBER,DOCUMENT_DT_EMITED,DOCUMENT_TYPE,DOCUMENT_ORG_SHIPPER,MARITAL_STATUS,ID_HEALTH_PLAN,
							HEALTH_PLAN_NAME,NUM_HEALTH_PLAN,RECORD_DATE
							FROM patient ORDER BY id_patient_new DESC");

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
