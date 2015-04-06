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

	$run=$_GET['paciente_rut'];

	if(strlen($run)==9){
		$run_chk=str_pad($run, 10, "0", STR_PAD_LEFT);  // agrega "0" a la izquierda;
	}else{
		$run_chk=$run;
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
					 FROM patient WHERE patient_run='".str_replace("-","",$run_chk)."' AND rownum<2 ORDER BY id_patient_new DESC"); //

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

while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
	
	//print_r($row);
    
	$ID_PATIENT_NEW=$row['ID_PATIENT_NEW'];
	$ID_PATIENT=$row['ID_PATIENT'];
	$FLG_STATUS=$row['FLG_STATUS'];
	$PATIENT_RUN=$row['PATIENT_RUN'];
	$PATIENT_RUT=$row['PATIENT_RUT'];
	$PATIENT_NAMES=strtoupper($row['PATIENT_NAMES']);
	$PATIENT_FATHER_SURNAME=strtoupper($row['PATIENT_FATHER_SURNAME']);
	$PATIENT_MOTHER_SURNAME=strtoupper($row['PATIENT_MOTHER_SURNAME']);
	if($row['GENDER']==1){$GENDER=0;}elseif($row['GENDER']==2){$GENDER=1;}elseif($row['GENDER']==3){$GENDER=2;}else{$GENDER=3;}
	$DT_BIRTH=$row['DT_BIRTH'];
	$ID_ADDRESS_TYPE=$row['ID_ADDRESS_TYPE']; $DESC_ADDRESS_TYPE=$row['DESC_ADDRESS_TYPE'];
	$ADDRESS=$row['ADDRESS'].' '.$row['ADDRESS_NUMBER'].' '.$row['ADDRESS_COMPLEMENT'];
	$ID_COMUNA=$row['ID_COMUNA']; $DESC_COMUNA=$row['DESC_COMUNA'];
			
		$ciud_chk=cargar_registro("SELECT ciud_id,ciud_desc FROM comunas WHERE ciud_desc ilike '%".$DESC_COMUNA."%';");
			
		if(!$ciud_chk){
			pg_query("INSERT INTO comunas VALUES ((SELECT MAX(ciud_id)+1 FROM comunas),-1,'".$DESC_COMUNA."');");
			$ciud_id=cargar_registro("SELECT MAX(ciud_id) AS ciud_id FROM comunas");
			$ciud_id=$ciud_id['ciud_id'];
		}else
			$ciud_id=$ciud_chk['ciud_id'];
			
	$POSTAL_CODE=$row['POSTAL_CODE'];
	$ID_COUNTRY=$row['ID_COUNTRY'];	$DESC_COUNTRY=$row['DESC_COUNTRY'];
	$ID_STATE=$row['ID_STATE'];	$DESC_STATE=$row['DESC_STATE'];
	$ID_CITY=$row['ID_CITY']; $DESC_CITY=$row['DESC_CITY'];	
	$PHONE_NUM=$row['PHONE_NUM'];
	$ID_SCHOLARSHIP=$row['ID_SCHOLARSHIP']; $DESC_SCHOLARSHIP=$row['DESC_SCHOLARSHIP'];
	$ID_OCCUPAPTION=$row['ID_OCCUPAPTION']; $DESC_OCCUPATION=$row['DESC_OCCUPATION'];
	$DOCUMENT_NUMBER=$row['DOCUMENT_NUMBER']; $DOCUMENT_DT_EMITED=$row['DOCUMENT_DT_EMITED']; 
	$DOCUMENT_TYPE=$row['DOCUMENT_TYPE']; $DOCUMENT_ORG_SHIPPER=$row['DOCUMENT_ORG_SHIPPER'];
	if($row['MARITAL_STATUS']=='S'){ $MARITAL_STATUS=1; }elseif($row['MARITAL_STATUS']=='M'){ $MARITAL_STATUS=2; }
	elseif($row['MARITAL_STATUS']=='F'){ $MARITAL_STATUS=3; }elseif($row['MARITAL_STATUS']=='D'){ $MARITAL_STATUS=4; }
	elseif($row['MARITAL_STATUS']=='W'){ $MARITAL_STATUS=5; }elseif($row['MARITAL_STATUS']=='U'){ $MARITAL_STATUS=6; }
	elseif($row['MARITAL_STATUS']=='O'){ $MARITAL_STATUS=0; }else{ $MARITAL_STATUS=0; }
	if($row['ID_HEALTH_PLAN']=='ISAPRE'){$ID_HEALTH_PLAN=5;}elseif($row['ID_HEALTH_PLAN']=='Particular'){$ID_HEALTH_PLAN=6;}
	elseif($row['ID_HEALTH_PLAN']=='FONASA'){$ID_HEALTH_PLAN=10;}else{$ID_HEALTH_PLAN=8;}
	$HEALTH_PLAN_NAME=$row['HEALTH_PLAN_NAME']; $NUM_HEALTH_PLAN=$row['NUM_HEALTH_PLAN'];
	$RECORD_DATE=$row['RECORD_DATE'];
	
	$chk=cargar_registro("SELECT * FROM pacientes WHERE pac_rut='".$run."';");
  
	if(!$chk){
		if(pg_query("INSERT INTO pacientes VALUES (DEFAULT,'$run','$PATIENT_NAMES','$PATIENT_FATHER_SURNAME','$PATIENT_MOTHER_SURNAME',
					'$DT_BIRTH', $GENDER, $ID_HEALTH_PLAN,'',-1,-1,'$ADDRESS',$ciud_id,0,$MARITAL_STATUS,'$PHONE_NUM','','','','',
					'$ID_PATIENT',0,'','$PHONE_NUM',false,'',null,'');")) print("OKi");
	}else{
		
	    if(pg_query("UPDATE pacientes SET pac_nombres='$PATIENT_NAMES', pac_appat='$PATIENT_FATHER_SURNAME	', pac_apmat='$PATIENT_MOTHER_SURNAME',
					pac_fc_nac='$DT_BIRTH', sex_id=$GENDER, prev_id=$ID_HEALTH_PLAN, pac_direccion='$ADDRESS', ciud_id=$ciud_id,
					estciv_id=$MARITAL_STATUS, pac_fono='$PHONE_NUM', pac_ficha='$ID_PATIENT', pac_celular='$PHONE_NUM'
					WHERE pac_rut='".$run."'")) print("OKu");
	}	
}

if(oci_num_rows($stid)==0){
		$chk=cargar_registro("SELECT pac_rut FROM pacientes WHERE pac_rut='$run';");
		if($chk) print("OKn");
}

oci_free_statement($stid);
oci_close($conn2);

?>
