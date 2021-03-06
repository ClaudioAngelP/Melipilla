<?php 

	require_once('../../config.php');
	require_once('../sigh.php');
	
	error_reporting(E_ALL);
	
	function fixdate($str) {
		
		$d=explode('-', $str);
		
		return ($d[2].'/'.$d[1].'/'.$d[0]);
		
	}

	function fixdatetime($str) {

		$dt=explode('T', $str);
				
		$d=explode('-', $dt[0]);
		
		return ($d[2].'/'.$d[1].'/'.$d[0].' '.$dt[1]);
		
	}
	
	function info_paciente($xml, $filename) {

		$data=$xml;
		
		$rut=pg_escape_string($data->NationalId[0]);

		if(isset($data->PassportNumber[0]))
			$pasaporte=pg_escape_string($data->PassportNumber[0]);
		else
			$pasaporte='';

		$id_sidra=pg_escape_string($data->RegistrationNumber[0]);

		$paterno=pg_escape_string(utf8_decode($data->FamilyName[0]));
		$materno=pg_escape_string(utf8_decode($data->SecondaryName[0]));
		$nombres=pg_escape_string(utf8_decode($data->GivenName[0]));
		
		if(isset($data->DateOfBirth[0])) 
			$fechanac=fixdate($data->DateOfBirth[0]);
		else
			$fechanac='01/01/1900';
		
		$sexo=trim($data->SexCode[0]);
		
		if($sexo=='M') {
			$sex_id=0;
		} elseif($sexo=='F') {
			$sex_id=1;	
		} else {
			$sex_id=2;		
		}
		
		$direccion=pg_escape_string(utf8_decode($data->HomeAddressStreet[0]));

		$cod_comuna=pg_escape_string($data->HomeAddressCityCode[0]);
		$comuna=pg_escape_string($data->HomeAddressCityDesc[0]);
		
		$com=cargar_registro("SELECT * FROM comunas WHERE ciud_desc ILIKE '$comuna' OR ciud_cod_nacional=$cod_comuna");
		
		if($com) $ciud_id=$com['ciud_id']*1; else $ciud_id=-1;
		
		if(isset($data->HomeAddressCityAreaDesc[0]))
			$sector=pg_escape_string(utf8_decode($data->HomeAddressCityAreaDesc[0]));
		else
			$sector='';
      
		
		if(isset($data->HomePhone[0]))
			$fono=pg_escape_string(utf8_decode($data->HomePhone[0]));
		else
			$fono='';
		
		if(isset($data->MobilePhone[0]))
			$celular=pg_escape_string(utf8_decode($data->MobilePhone[0]));
		else
			$celular='';
		
		$prais=$data->PRAIS[0];
		
		if($prais!='true') {
			$prais='false';
		}

		$estadocivil=$data->MaritalStatusCode[0]*1;
		
		$estciv=0;
		
		switch($estadocivil) {
			case 1: $estciv=1; break; // SOLTERO
			case 2: $estciv=2; break; // CASADO
			case 3: $estciv=5; break; // VIUDO
			case 4: $estciv=3; break; // SEPARADO
			case 5: $estciv=6; break; // CONVIVE
			case 6: $estciv=4; break; // DIVORCIADO
			case 7: $estciv=0; break; // INDETERMINADO
		}
		
		//$xml->{"SOAP-ENVBody"}[0]->SendEvent[0]->pRequest[0]->
		
		if(isset($data->EMail[0]))
			$mail=pg_escape_string($data->EMail[0]);
		else
			$mail='';

		if(isset($xml->{"SOAP-ENVBody"}[0]->SendEvent[0]->pRequest[0]->MedicalRecords[0]->MedicalRecord[0])) {

			$data=$xml->{"SOAP-ENVBody"}[0]->SendEvent[0]->pRequest[0]->MedicalRecords[0]->MedicalRecord[0];
		
			$ficha=pg_escape_string($data->MRN[0]*1);
		
		} else {
			
			$ficha='';
			
		}

		if(isset($xml->{"SOAP-ENVBody"}[0]->SendEvent[0]->pRequest[0]->Insurances[0]->Insurance[0])) {

			$data=$xml->{"SOAP-ENVBody"}[0]->SendEvent[0]->pRequest[0]->Insurances[0]->Insurance[0];
		
			$prev_desc=pg_escape_string(trim($data->PlanCode[0]));
			
			if($prev_desc=='A') {
				$prev_id=1; $tramo='A';
			} else if($prev_desc=='B') {
				$prev_id=2; $tramo='B';
			} else if($prev_desc=='C') {
				$prev_id=3; $tramo='C';
			} else if($prev_desc=='D') {
				$prev_id=4; $tramo='D';
			} else {
				$prev_id=8;
			}
				
			$pac_tramo=substr(strtoupper($prev_desc),0,1);
		
		} else {
			
			$prev_id=-1; $tramo='';
			
		}

		if(isset($xml->{"SOAP-ENVBody"}[0]->SendEvent[0]->pRequest[0]->MedicalRecords[0]->MedicalRecord[0]->OldMRN[0])) {
			
			$data=$xml->{"SOAP-ENVBody"}[0]->SendEvent[0]->pRequest[0]->MedicalRecords[0]->MedicalRecord[0];
		
			$sel_ficha=pg_escape_string($data->OldMRN[0]*1);
			
			if($sel_ficha!='') {
			
				$ficha_w="pac_ficha='$sel_ficha'";
				
			} elseif($ficha!='') {
				
				$ficha_w="pac_ficha='$ficha'";				
				
			} else {
			
				$ficha_w='false';
				
			}
		
		} else {
			
			if($ficha!='') {
			
				$ficha_w="pac_ficha='$ficha'";
			
			} else {
				
				$ficha_w='false';
			
			}
			
		}
		
		if($rut!='') {
			$rut_w="pac_rut='$rut'";
		} else {
			$rut_w='false';			
		}		
		
		$chk=cargar_registro("SELECT * FROM pacientes WHERE $rut_w;");

		if(!$chk AND $ficha_w!='false') {

			$chk=cargar_registro("SELECT * FROM pacientes WHERE $ficha_w;");
			
		}
		
		if(!$chk) {

			$query="INSERT INTO pacientes VALUES (
				DEFAULT,
				'$rut', 
				'$nombres', '$paterno', '$materno', 
				'$fechanac', $sex_id, -1, '$sector', -1, -1, 
				'$direccion', $ciud_id,
				1, $estciv, '$fono', '', '', '$tramo', '$pasaporte', '$ficha', -1, 
				'$mail', '$celular', $prais, '$id_sidra'
			);";

			$q=pg_query($query);
			
			$pid=pg_query("SELECT CURRVAL('pacientes_pac_id_seq') AS pid;");
			$pid=pg_fetch_assoc($pid);
			$pac_id=$pid['pid']*1;

		} else {

			$query="UPDATE pacientes SET
			
				pac_nombres='$nombres',
				pac_appat='$paterno',
				pac_apmat='$materno',

				pac_fc_nac='$fechanac',
				sex_id=$sex_id,
				estciv_id=$estciv,

				pac_direccion='$direccion',
				sector_nombre='$sector',
				ciud_id=$ciud_id,

				pac_fono='$fono',
				pac_celular='$celular',

				pac_mail='$mail',
				pac_prais=$prais,
				pac_pasaporte='$pasaporte',
				id_sidra='$id_sidra'

			WHERE pac_id=".$chk['pac_id'];
			
			$pac_id=$chk['pac_id']*1;
	
			$q=pg_query($query);
			
			if($ficha!='') 
			pg_query("UPDATE pacientes SET
						
				pac_ficha='$ficha'
							
			WHERE pac_id=".$chk['pac_id']);

			if($prev_id!=-1)
			pg_query("UPDATE pacientes SET
						
				prev_id=$prev_id, pac_tramo='$pac_tramo'
							
			WHERE pac_id=".$chk['pac_id']);
	

		}
		
		if(!$q AND $filename!='') {
				
				copy('data/'.$filename,'errors/'.$filename);
				$qerror=pg_last_error();
				file_put_contents('errors/'.$filename.'.error.log', $qerror, FILE_APPEND);
				file_put_contents('errors/'.$filename.'.sql.log', $query, FILE_APPEND);
				
		
		}

		return $pac_id;
		
	}
	
	function procesar_paciente($xml, $filename='') {
	
		$data=$xml->{'SOAP-ENVBody'}[0]->SendEvent[0]->pRequest[0]->Patient[0];
		
		$rut=pg_escape_string($data->NationalId[0]);

		$id_sidra=pg_escape_string($data->RegistrationNumber[0]);

		if(isset($data->PassportNumber[0]))
			$pasaporte=pg_escape_string($data->PassportNumber[0]);
		else
			$pasaporte='';

		$paterno=pg_escape_string(utf8_decode($data->FamilyName[0]));
		$materno=pg_escape_string(utf8_decode($data->SecondaryName[0]));
		$nombres=pg_escape_string(utf8_decode($data->GivenName[0]));
		
		if(isset($data->DateOfBirth[0])) 
			$fechanac=fixdate($data->DateOfBirth[0]);
		else
			$fechanac='01/01/1900';
		
		$sexo=trim($data->SexCode[0]);
		
		if($sexo=='M') {
			$sex_id=0;
		} elseif($sexo=='F') {
			$sex_id=1;	
		} else {
			$sex_id=2;		
		}
		
		$direccion=pg_escape_string(utf8_decode($data->HomeAddressStreet[0]));

		$cod_comuna=pg_escape_string($data->HomeAddressCityCode[0]);
		$comuna=pg_escape_string($data->HomeAddressCityDesc[0]);
		
		$com=cargar_registro("SELECT * FROM comunas WHERE ciud_desc ILIKE '$comuna' OR ciud_cod_nacional=$cod_comuna");
		
		if($com) $ciud_id=$com['ciud_id']*1; else $ciud_id=-1;
		
		if(isset($data->HomeAddressCityAreaDesc[0]))
			$sector=pg_escape_string(utf8_decode($data->HomeAddressCityAreaDesc[0]));
		else
			$sector='';
      
		
		if(isset($data->HomePhone[0]))
			$fono=pg_escape_string(utf8_decode($data->HomePhone[0]));
		else
			$fono='';
		
		if(isset($data->MobilePhone[0]))
			$celular=pg_escape_string(utf8_decode($data->MobilePhone[0]));
		else
			$celular='';
		
		$prais=$data->PRAIS[0];
		
		if($prais!='true') {
			$prais='false';
		}
		
		$estadocivil=$data->MaritalStatusCode[0]*1;
		
		$estciv=0;
		
		switch($estadocivil) {
			case 1: $estciv=1; break; // SOLTERO
			case 2: $estciv=2; break; // CASADO
			case 3: $estciv=5; break; // VIUDO
			case 4: $estciv=3; break; // SEPARADO
			case 5: $estciv=6; break; // CONVIVE
			case 6: $estciv=4; break; // DIVORCIADO
			case 7: $estciv=0; break; // INDETERMINADO
		}
		
		//$xml->{"SOAP-ENVBody"}[0]->SendEvent[0]->pRequest[0]->
		
		if(isset($data->EMail[0]))
			$mail=pg_escape_string($data->EMail[0]);
		else
			$mail='';

		if(isset($xml->{"SOAP-ENVBody"}[0]->SendEvent[0]->pRequest[0]->MedicalRecords[0]->MedicalRecord[0])) {

			$data=$xml->{"SOAP-ENVBody"}[0]->SendEvent[0]->pRequest[0]->MedicalRecords[0]->MedicalRecord[0];
		
			$ficha=pg_escape_string($data->MRN[0]*1);
		
		} else {
			
			$ficha='';
			
		}

		if(isset($xml->{"SOAP-ENVBody"}[0]->SendEvent[0]->pRequest[0]->Insurances[0]->Insurance[0])) {

			$data=$xml->{"SOAP-ENVBody"}[0]->SendEvent[0]->pRequest[0]->Insurances[0]->Insurance[0];
		
			$prev_desc=pg_escape_string(trim($data->PlanCode[0]));
			
			if($prev_desc=='A')
				$prev_id=1;
			else if($prev_desc=='B')
				$prev_id=2;
			else if($prev_desc=='C')
				$prev_id=3;
			else if($prev_desc=='D')
				$prev_id=4;
			else
				$prev_id=8;
				
			$pac_tramo=substr(strtoupper($prev_desc),0,1);
		
		} else {
			
			$prev_id=-1;
			
		}
		
		if(isset($xml->{"SOAP-ENVBody"}[0]->SendEvent[0]->pRequest[0]->MedicalRecords[0]->MedicalRecord[0]->OldMRN[0])) {
			
			$data=$xml->{"SOAP-ENVBody"}[0]->SendEvent[0]->pRequest[0]->MedicalRecords[0]->MedicalRecord[0];
		
			$sel_ficha=pg_escape_string($data->OldMRN[0]*1);
			
			if($sel_ficha!='') {
			
				$ficha_w="pac_ficha='$sel_ficha'";
				
			} elseif($ficha!='') {
				
				$ficha_w="pac_ficha='$ficha'";				
				
			} else {
			
				$ficha_w='false';
				
			}
		
		} else {
			
			if($ficha!='') {
			
				$ficha_w="pac_ficha='$ficha'";
			
			} else {
				
				$ficha_w='false';
			
			}
			
		}
		
		if($rut!='') {
			$rut_w="pac_rut='$rut'";
		} else {
			$rut_w='false';			
		}		
		
		
		$chk=cargar_registro("SELECT * FROM pacientes WHERE $rut_w;");
		
		if(!$chk AND $ficha_w!='false') {

			$chk=cargar_registro("SELECT * FROM pacientes WHERE $ficha_w;");
			
		}
		
		if(!$chk) {

			$query="INSERT INTO pacientes VALUES (
				DEFAULT,
				'$rut', 
				'$nombres', '$paterno', '$materno', 
				'$fechanac', $sex_id, -1, '$sector', -1, -1, 
				'$direccion', $ciud_id,
				1, $estciv, '$fono', '', '', '$tramo', '$pasaporte', '$ficha', -1, 
				'$mail', '$celular', $prais, '$id_sidra'
			);";

			$q=pg_query($query);
			
		} else {

			$query="UPDATE pacientes SET
			
				pac_nombres='$nombres',
				pac_appat='$paterno',
				pac_apmat='$materno',

				pac_fc_nac='$fechanac',
				sex_id=$sex_id,
				estciv_id=$estciv,

				pac_direccion='$direccion',
				sector_nombre='$sector',
				ciud_id=$ciud_id,

				pac_fono='$fono',
				pac_celular='$celular',

				pac_mail='$mail',
				pac_prais=$prais,
				pac_pasaporte='$pasaporte',
				id_sidra='$id_sidra'

			WHERE pac_id=".$chk['pac_id'];

			$q=pg_query($query);
			
			if($ficha!='') 
			pg_query("UPDATE pacientes SET
						
				pac_ficha='$ficha'
							
			WHERE pac_id=".$chk['pac_id']);

			if($prev_id!=-1)
			pg_query("UPDATE pacientes SET
						
				prev_id=$prev_id, pac_tramo='$pac_tramo'
							
			WHERE pac_id=".$chk['pac_id']);
	

		}
		
		if(!$q AND $filename!='') {
				
				copy('data/'.$filename,'errors/'.$filename);
				$qerror=pg_last_error();
				file_put_contents('errors/'.$filename.'.error.log', $qerror, FILE_APPEND);
				file_put_contents('errors/'.$filename.'.sql.log', $query, FILE_APPEND);
				
		}

	
	}
	
	function procesar_citacion($xml, $filename='') {

		$data=$xml->{'SOAP-ENVBody'}[0]->SendEvent[0]->pRequest[0];
		
		$id_sidra=pg_escape_string($data->AppointmentId[0]);
		$tipo_cita=trim($data->SessionTypeCode[0]);
		
		if(isset($data->IsOverbooking[0]))
			$sobrecupo=trim($data->IsOverbooking[0]);
		else
			$sobrecupo='false';

		$data3=$data->Patient[0];
		
		$rut=pg_escape_string($data3->NationalId[0]);

		//$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_rut='$rut';");
		
		//if(!$pac) {
			$pac_id=info_paciente($data3, $filename);
		//} else {
			//$pac_id=$pac['pac_id']*1;
		//}
		
		if(isset($data->Services[0]->ApptServiceInfo[0]->ApptDateAndTime[0])) {
		
			$fec=pg_escape_string(fixdatetime($data->Services[0]->ApptServiceInfo[0]->ApptDateAndTime[0]));
		
		} else {
	
			$fec=pg_escape_string(fixdatetime($data->AdmInfo[0]->AdmissionDateTime[0]));
			
		}

		$ftmp=explode(' ',$fec);
		
		$fecha=$ftmp[0]; $hora=$ftmp[1];

		$data2=$data->AdmInfo[0];
		
		if(isset($data->Personnel[0]->ApptPersonInfo[0]->PersonResId[0])) {
			$doc_rut=pg_escape_string(strtoupper($data->Personnel[0]->ApptPersonInfo[0]->PersonResId[0]));		
		} else {
			$doc_rut=pg_escape_string(strtoupper($data2->AttendingDoctorNationalId[0]));		
		}
		
		$doc=cargar_registro("SELECT * FROM doctores WHERE doc_rut='$doc_rut';"); 
		
		if($doc) {
		
			$doc_id=$doc['doc_id']; 
			
		} else {
		
			$data3=$data->Personnel[0]->ApptPersonInfo[0];
		
			$nombre=pg_escape_string(utf8_decode($data3->PersonResGivenName[0]));
			
			list($paterno, $materno)=explode(' ',pg_escape_string(utf8_decode($data3->PersonResSurnames[0])));
			
			pg_query("INSERT INTO doctores VALUES (DEFAULT, '$doc_rut', '$paterno', '$materno', '$nombre');");
			
			$doc_id="CURRVAL('doctores_doc_id_seq')";
		
		}
		
		$ubica_cod=pg_escape_string($data2->LocCode[0]); // ??
		$ubica_desc=pg_escape_string($xml->{'SOAP-ENVBody'}[0]->SendEvent[0]->pRequest[0]['SendingLocationDesc']);
		
		$chk=cargar_registro("SELECT * FROM especialidades WHERE esp_codigo_int='$ubica_cod'");
		
		if(!$chk) {
			pg_query("INSERT INTO especialidades VALUES (DEFAULT, '$ubica_desc', 0, 1, '', '$ubica_cod');");
			$esp_id="CURRVAL('especialidades_esp_id_seq')";
		} else {
			$esp_id=$chk['esp_id']*1;
		}
		
		$chk2=cargar_registro("SELECT * FROM nomina WHERE nom_fecha='$fecha' AND nom_esp_id=$esp_id AND nom_doc_id=$doc_id");
		
		if(!$chk2) {

			$query="INSERT INTO nomina VALUES (
				DEFAULT, CURRVAL('nomina_nom_id_seq'),
				$esp_id, $doc_id,
				'',
				0,
				false,
				'$fecha',
				0,
				true,
				0,0,0,
				''
			);";
			
			$q=pg_query($query);
			
			$nom_id="CURRVAL('nomina_nom_id_seq')";

			if(!$q AND $filename!='') {
				
				copy('data/'.$filename,'errors/'.$filename);
				$qerror=pg_last_error();
				file_put_contents('errors/'.$filename.'.error.log', $qerror, FILE_APPEND);
				file_put_contents('errors/'.$filename.'.sql.log', $query, FILE_APPEND);

				
			}

			
		} else {
						
			$nom_id=$chk2['nom_id']*1;
			
		} 

		
		$cod_presta=pg_escape_string($data->Services[0]->ApptServiceInfo[0]->ServiceCode[0]);
		
		$diag_cod=pg_escape_string($data->StatusCode[0]);
		$cod_cancela=pg_escape_string($data->ReasonForCancelCode[0]);
		$cod_no_atiende=pg_escape_string($data->ReasonForNotSeenCode[0]);;
		
		if($diag_cod=='P') $diag_cod='';

		$chk3=cargar_registro("SELECT * FROM nomina_detalle WHERE id_sidra='$id_sidra'");
		
		if(!$chk3) {
		
			if($tipo_cita=='CN')
				$tipo='N';
			else
				$tipo='C';
		
			if($sobrecupo=='true')
				$extra='S';
			else
				$extra='N';
		
			$diag='';  
			$sficha='';
			$motivo='';
			$destino='';
			$auge='';
			$estado='';
					
			$query="INSERT INTO nomina_detalle VALUES (
					DEFAULT,
					$nom_id,
					$pac_id,
					'$tipo','$extra',
					'$diag','$sficha',
					'$diag_cod','$motivo',
					'$destino','$auge',
					'$estado',
					0, '$hora', 'A', '$id_sidra', $nom_id, '', 0, '', 0, 
					'$id_sidra', '$cod_cancela', '$cod_no_atiende', '$cod_presta'
				);";
					
			$q=pg_query($query);			
		
			
		} else {
			
			$query="UPDATE nomina_detalle SET nomd_hora='$hora', nomd_folio='$id_sidra', id_sidra='$id_sidra', nomd_codigo_cancela='$cod_cancela', nomd_codigo_no_atiende='$cod_no_atiende', nomd_codigo_presta='$cod_presta' WHERE nomd_id=".$chk3['nomd_id'];
			
			$q=pg_query($query);
			
			$dcod=trim(strtoupper($chk3['nomd_diag_cod']));

      if($dcod=='' OR $dcod=='X' OR $dcod=='H' OR $dcod=='T') {

  			if($diag_cod=='N' AND $cod_no_atiende=='08') // No se Presenta
  				pg_query("UPDATE nomina_detalle SET nomd_diag_cod='NSP' WHERE nomd_id=".$chk3['nomd_id']);
  			elseif($diag_cod!='A') // Otros Codigos...
  				pg_query("UPDATE nomina_detalle SET nomd_diag_cod='$diag_cod' WHERE nomd_id=".$chk3['nomd_id']);
				
			}
			
		}


		if(!$q AND $filename!='') {
				
				copy('data/'.$filename,'errors/'.$filename);
				$qerror=pg_last_error();
				file_put_contents('errors/'.$filename.'.error.log', $qerror, FILE_APPEND);
				file_put_contents('errors/'.$filename.'.sql.log', $query, FILE_APPEND);
				
		}
	
	}
	
	function procesar_interconsulta($xml,$filename='') {

		$data=$xml->{'SOAP-ENVBody'}[0]->SendEvent[0]->pRequest[0];
				
		$id_sidra=pg_escape_string($data->WLEntryCode[0]);
		
		if(isset($data->WLEntry[0]->GeneralData[0]))
			$folios=pg_escape_string($data->WLEntry[0]->GeneralData[0]);
		else
			$folios='0|';
			
		$ftmp=explode('|', $folios);
		
		$folio=$ftmp[0]*1; 
		$idiag=pg_escape_string($ftmp[1]);
		
		$estado=1; $prof_id=-1;

		if(isset($data->RequestingCareProvider[0]->NationalId[0])) {
			
			$prof_rut=pg_escape_string($data->RequestingCareProvider[0]->NationalId[0]);
			$prof_rut=str_replace('.','',trim($prof_rut));
			
			$prof=cargar_registro("SELECT * FROM profesionales_externos WHERE prof_rut='$prof_rut'");
			if($prof) { 
				$paterno=pg_escape_string($data->RequestingCareProvider[0]->FamilyName[0]);
				$nombres=pg_escape_string($data->RequestingCareProvider[0]->GivenName[0]);
				pg_query("UPDATE profesionales_externos SET prof_paterno='$paterno', prof_nombres='$nombres' WHERE prof_id=".$prof['prof_id']*1);
				$prof_id=$prof['prof_id']*1;
			} else {
				$paterno=pg_escape_string($data->RequestingCareProvider[0]->FamilyName[0]);
				$nombres=pg_escape_string($data->RequestingCareProvider[0]->GivenName[0]);
				pg_query("INSERT INTO profesionales_externos VALUES (DEFAULT, '$paterno', '', '$nombres', '$prof_rut');");
				$prof_id="CURRVAL('profesionales_externos_prof_id_seq')";
			}
		} else {
			$prof_id=-1;
		}
		
		$data2=$data->Patient[0];
		
		$rut=pg_escape_string($data2->NationalId[0]);

		//$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_rut='$rut';");
		
		//if(!$pac) {
			$pac_id=info_paciente($data2, $filename);
		//} else {
			//$pac_id=$pac['pac_id']*1;
		//}
		
		$data2=$data->WLEntry[0];
		
		$fecha_digitacion=fixdatetime($data2->ReferralDate[0]);
		
		$inst_cod1=pg_escape_string($data2->OriginatingFacilityCode[0]);
		$inst_cod2=pg_escape_string($data2->DestinationFacilityCode[0]);
		$inst1=cargar_registro("SELECT * FROM instituciones WHERE inst_codigo_ifl='$inst_cod1' AND NOT id_sigges=0;");
		$inst2=cargar_registro("SELECT * FROM instituciones WHERE inst_codigo_ifl='$inst_cod2' AND NOT id_sigges=0;");
		if($inst1) $inst_id1=$inst1['inst_id']*1; else $inst_id1=-1;
		if($inst2) $inst_id2=$inst2['inst_id']*1; else $inst_id2=$sgh_inst_id;
		
		$esp_cod=pg_escape_string($data2->RequestedSpecialtyCode[0]);	
		$esp=cargar_registro("SELECT * FROM especialidades WHERE esp_codigo_ifl_usuario='$esp_cod'");
		if($esp) $esp_id=$esp['esp_id']*1; else $esp_id=-1;
				
		$local_cod=pg_escape_string($data2->InternalDestSpecialtyCode[0]);
		$local=cargar_registro("SELECT * FROM especialidades WHERE esp_codigo_int='$local_cod'");
		if($local) $local_id=$local['esp_id']*1; else $local_id=-1;	
		
		$codigo_fonasa=pg_escape_string($data2->RequestedProcedureCode[0]);
		
		$cie10_cod=pg_escape_string(strtoupper($data2->DiagnosisCode[0]));
		
		if($cie10_cod=='0') $cie10_cod='';

		$fundamentos=pg_escape_string(strtoupper(utf8_decode($data2->Remarks[0])));
		$hipotesis=pg_escape_string(strtoupper(utf8_decode($data2->DiagnosisRemarks[0])));
		
		$prioridad=$data2->EpisodePriorityCode[0];
		
		$prior=0;
		
		switch($prioridad) {
			case '001': $prior=1; break;
			case '002': $prior=2; break;
			case '003': $prior=3; break;
			case '004': $prior=6; break;
			case '005': $prior=2; break;
			default: $prior=0; break;
		}
		
		if(isset($data2->ReferralRemovalReasonCode[0]) AND $data2->ReferralRemovalReasonCode[0]*1!=0) {
			$motivo_salida=$data2->ReferralRemovalReasonCode[0]*1;
			$fecha_salida="'".fixdatetime($data2->ReferralRemovalDate[0])."'";
		} else {
			$motivo_salida=0;
			$fecha_salida='null';			
		}
		
		$chk=cargar_registro("SELECT * FROM interconsulta WHERE id_sidra='$id_sidra';");
		
/*

CREATE TABLE interconsulta
(
  inter_id bigserial NOT NULL,
  inter_folio integer,
  inter_inst_id1 bigint,
  inter_especialidad integer,
  inter_unidad integer,
  inter_estado integer,
  inter_fundamentos text,
  inter_examenes text,
  inter_comentarios text,
  inter_pac_id bigint,
  inter_inst_id2 bigint,
  inter_notifica smallint NOT NULL,
  inter_ingreso date NOT NULL DEFAULT ('now'::text)::date,
  inter_rev_med text,
  inter_doc_id integer,
  inter_prioridad smallint NOT NULL DEFAULT 2,
  inter_diag_cod character varying(20),
  inter_motivo smallint,
  inter_otro_motivo text,
  inter_garantia_id integer,
  inter_motivo_salida smallint DEFAULT 0,
  inter_prof_id bigint,
  inter_patrama_id bigint DEFAULT 0,
  inter_fecha timestamp without time zone,
  inter_fecha_ingreso timestamp without time zone,
  id_sigges bigint DEFAULT (-1),
  id_caso bigint DEFAULT 0,
  func_id bigint DEFAULT 0,
  func_id2 bigint DEFAULT 0,
  func_id3 bigint DEFAULT 0,
  inter_diagnostico text,
  inter_fecha_salida timestamp without time zone,
  id_sidra character varying(15),
  CONSTRAINT interconsulta_inter_id_key PRIMARY KEY (inter_id)
)
WITH (
  OIDS=FALSE
);
 
*/		
		
		if(!$chk) {
			$query="
				INSERT INTO interconsulta VALUES (
				DEFAULT, $folio,  $inst_id1, $esp_id, $local_id, $estado, 
				'$fundamentos', '$hipotesis', '', $pac_id, $inst_id2, 0,
				'$fecha_digitacion', '', 0, $prior, '$cie10_cod', 0, '', 
				0, $motivo_salida, $prof_id, 0, CURRENT_TIMESTAMP, '$fecha_digitacion',
				-1, 0, 0,0,0, '$idiag', $fecha_salida, '$id_sidra'
				);
			";
			$q=pg_query($query);
		} else {
			$query="
				UPDATE interconsulta SET
				inter_inst_id1=$inst_id1,
				inter_inst_id2=$inst_id2,
				inter_especialidad=$esp_id,
				inter_unidad=$local_id,
				inter_fundamentos='$fundamentos',
				inter_examenes='$hipotesis',
				inter_pac_id=$pac_id,
				inter_ingreso='$fecha_digitacion',
				inter_diag_cod='$cie10_cod',
				inter_diagnostico='$idiag',
				inter_prioridad=$prior,
				inter_prof_id=$prof_id,
				inter_motivo_salida=$motivo_salida,
				inter_fecha_salida=$fecha_salida
				WHERE id_sidra='$id_sidra';
			";
			$q=pg_query($query);
		}

		if(!$q AND $filename!='') {
				
				copy('data/'.$filename,'errors/'.$filename);
				$qerror=pg_last_error();
				file_put_contents('errors/'.$filename.'.error.log', $qerror, FILE_APPEND);
				file_put_contents('errors/'.$filename.'.sql.log', $query, FILE_APPEND);
				
		}

	
	}

?>
