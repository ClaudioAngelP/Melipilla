<?php 

        chdir(dirname(__FILE__));

	require_once('../../config.php');
	require_once('../sigh.php');
	require_once('simplehtmldom/simple_html_dom.php');

  function xml_date($date) {
  
    $partes=split('T', $date);
    $fecha=split('-', $partes[0]);
    $hora=split('-', $partes[1]);
  
    return $fecha[2].'/'.$fecha[1].'/'.$fecha[0].' '.$hora[0];
  
  }

	function getfieldval($tag, $obj) {

		$tmp = $obj->find($tag);
	
		if(isset($tmp[0]->attr['value']))	
			return urlencode($tmp[0]->attr['value']);
		if(isset($tmp[0]->attr['VALUE']))
                        return urlencode($tmp[0]->attr['VALUE']);
                else
			return '';

	}

	function getfieldval2($tag, $obj) { 

                $tmp = $obj->find($tag);

                if(isset($tmp[0]->attr['value']))     
                        return pg_escape_string(utf8_decode($tmp[0]->attr['value']));
                if(isset($tmp[0]->attr['VALUE']))
                        return pg_escape_string(utf8_decode($tmp[0]->attr['VALUE']));
                else
                        return '';

        }


	

	function getINPUT($regex, $str) {

		preg_match('/'.$regex.'/',$str,$tmp);
               	return $tmp[1];	

	}

	function fixfields($str) {
		
		$str=str_replace("\n", "&", trim($str));
		$str=str_replace(":", "=", $str);

		return $str;
	}


	$rut='158664704';
	$clave='ci1d09s';

	$buscar_rut='';
	$buscar_ficha='';

	if(isset($_GET['buscar']))
		$buscar_rut=$_GET['buscar'];
	if(isset($_GET['buscar2']))
		$buscar_ficha=$_GET['buscar2'];
	
	$ch = curl_init();
	
	function trakcare_login() {
		
		global $ch, $rut, $clave, $buscar_rut, $buscar_ficha;

		curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)');

		curl_setopt( $ch, CURLOPT_VERBOSE, true);

		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );

		curl_setopt($ch, CURLOPT_COOKIESESSION, true);
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
		
		curl_setopt( $ch, CURLOPT_URL, 'http://10.8.163.40/trakcare/csp/logon.csp?LANGID=1' );
		
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
		
		$login1 = curl_exec ($ch);
                $dom = str_get_html($login1);		

		curl_setopt( $ch, CURLOPT_URL, 'http://10.8.163.40/trakcare/scripts_gen/ssuserlogon.js' );

                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);

                $login2 = curl_exec ($ch);
		preg_match('/frm.TEVENT.value=\'(.+)\';/',$login2,$teventln);
		$tevent=urlencode($teventln[1]);
		
		$form_fields=fixfields("TFORM:SSUserLogon
TPAGID:".getfieldval('input[id=TPAGID]', $dom)."
TEVENT:$tevent
TXREFID:1
TOVERRIDE:
TDIRTY:1
TWKFL:
TWKFLI:
TFRAME:
TWKFLL:
TWKFLJ:
TREPORT:
TRELOADID:".getfieldval('input[id=TRELOADID]', $dom)."
TOVERLAY:
TINFO:
TINFOMODE:
RELOGON:
LocationListFlag:0
SSUSERGROUPDESC:
changeinlogonhosp:
Hospital:
BioKey:
UsernameEntered:
PasswordEntered:
LocListLocID:
LocListGroupID:
TTUID:
USERNAME:$rut
PASSWORD:$clave");	

		curl_setopt( $ch, CURLOPT_URL, 'http://10.8.163.40/trakcare/csp/logon.csp' );
		
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $form_fields);
		
		//print("\n\nPASO 2\n\n");

		$login2 = curl_exec ($ch);
		file_put_contents('data2.log',$login2);
		$tmp = str_get_html($login2);

		preg_match('/frame id="eprmenu" name="eprmenu" src="(.+)"/',$login2,$frameurl);
                $url2=($frameurl[1]);

		$url3=explode('&quot;', $url2);
		$url2=$url3[0];

		//print("URL: [$url2]");

		if($url2=='') {

			preg_match('/name="LocListLocIDz1" type="hidden" value="(.+)"/',$login2,$tmpp);		
			$LocListLocID=$tmpp[1];
			preg_match('/name="LocListGroupIDz1" type="hidden" value="(.+)"/',$login2,$tmpp);
                        $LocListGroupID=$tmpp[1];

			
			 $form_fields=fixfields("TFORM:SSUserLogon
TPAGID:".getfieldval('input[id=TPAGID]', $tmp)."
TEVENT:$tevent
TXREFID:1
TOVERRIDE:
TDIRTY:1
TWKFL:
TWKFLI:
TFRAME:
TWKFLL:
TWKFLJ:
TREPORT:
TRELOADID:".getfieldval('input[id=TRELOADID]', $tmp)."
TOVERLAY:
TINFO:
TINFOMODE:
RELOGON:
LocationListFlag:0
SSUSERGROUPDESC:
changeinlogonhosp:
Hospital:
BioKey:
UsernameEntered:$rut
PasswordEntered:$clave
LocListLocID:$LocListLocID
LocListGroupID:$LocListGroupID
TTUID:");

                curl_setopt( $ch, CURLOPT_URL, 'http://10.8.163.40/trakcare/csp/logon.csp' );

                curl_setopt( $ch, CURLOPT_POST, 1);
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $form_fields);

                //print("\n\nPASO 2\n\n");

                $login2 = curl_exec ($ch);
                file_put_contents('data2.5.log',$login2);
                $tmp = str_get_html($login2);

                preg_match('/frame id="eprmenu" name="eprmenu" src="(.+)"/',$login2,$frameurl);
                $url2=($frameurl[1]);

                $url3=explode('&quot;', $url2);
                $url2=$url3[0];

                //print("URL2: [$url2]");


		}

		curl_setopt( $ch, CURLOPT_URL, 'http://10.8.163.40/trakcare/csp/'.$url2 );

                curl_setopt( $ch, CURLOPT_POST, 0);
                curl_setopt( $ch, CURLOPT_POSTFIELDS, '');

                //print("\n\nPASO 3\n\n");

                $login2 = curl_exec ($ch);
                file_put_contents('data3.log',$login2);

		preg_match('/tkTUIDS=\"(.+)\";/',$login2,$teventln);

		//print_r($teventln);

                $wevent=urlencode($teventln[1]);


		preg_match('/TMENU=52136&TPAGID=(.+)"/',$login2,$frameurl);
                $wargtmp=($frameurl[1]);

		$warg=explode('--', $wargtmp);
                $tpagdata=$warg[0];


		$brokerfields=fixfields("WARGC:2
WEVENT:$wevent
WARG_1:websys.csp?a=a&TMENU=52136&TPAGID=".urlencode($tpagdata)."--
WARG_2:6");

		//print($brokerfields);

		curl_setopt( $ch, CURLOPT_URL, 'http://10.8.163.40/trakcare/csp/%25CSP.Broker.cls' );

                curl_setopt( $ch, CURLOPT_POST, 1);
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $brokerfields);

                //print("\n\nPASO 4\n\n");

                $login2 = curl_exec ($ch);
                file_put_contents('data4.log',$login2);

		$data=explode("\n", $login2);

		$url=trim($data[4]);

		curl_setopt( $ch, CURLOPT_URL, 'http://10.8.163.40/trakcare/csp/'.$url );

                curl_setopt( $ch, CURLOPT_POST, 1);
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $brokerfields);

                //print("\n\nPASO 5\n\n");

                $login2 = curl_exec ($ch);
                file_put_contents('data5.log',$login2);
		$dom3=str_get_html($login2);

$form_params=fixfields("TFORM:PAPerson.Find
TPAGID:".getINPUT("<INPUT TYPE='HIDDEN' ID='TPAGID' NAME='TPAGID' VALUE='(.+)'>", $login2)."
TEVENT:".getINPUT("<INPUT TYPE='HIDDEN' ID='TEVENT' NAME='TEVENT' VALUE='(.+)'>", $login2)."
TXREFID:".getINPUT("<INPUT TYPE='HIDDEN' ID='TXREFID' NAME='TXREFID' VALUE='(.+)'>", $login2)."
TOVERRIDE:
TDIRTY:1
TWKFL:".getINPUT("<INPUT TYPE='HIDDEN' ID='TWKFL' NAME='TWKFL' VALUE='(.+)'>", $login2)."
TWKFLI:1
TFRAME:
TWKFLL:
TWKFLJ:
TREPORT:
TRELOADID:".getINPUT("<INPUT TYPE='HIDDEN' ID='TRELOADID' NAME='TRELOADID' VALUE=\"(.+)\">", $login2)."
TOVERLAY:
TINFO:
TINFOMODE:
validateSex:
PAPERDob:
admType:
EpisodeID:
tempPatientID:
mergePatient:
hiddenFlag:".getINPUT('<input id="hiddenFlag" name="hiddenFlag" type="hidden" value="(.+)">',$login2)."
PATCF:".getINPUT('<input id="PATCF" name="PATCF" type="hidden" value="(.+)">', $login2)."
locType:
returnSelected:
HospMRType:".getINPUT('<input id="HospMRType" name="HospMRType" type="hidden" value="(.+)">', $login2)."
NewApptIDFlag:
PAComplaintID:^
secgrp:".getINPUT('<input id="secgrp" name="secgrp" type="hidden" value="(.+)">', $login2)."
NoUnique:
SelHospital:
CopyAdmission:
AgeSearchTypeID:
SelAdmissionType:
SelHospitalAll:
ExpAdmDateFrom:
ExpAdmDateTo:
SelBookingType:
CurrAppts:
SelAdmHospital:
HiddenHosps:^
SchedID:
VNAID:
obData:
params:
updateBooking:
ValidAgr:
MinimumSearchFields:
EvID:
ExtSearch:
SelPTYPEDesc:
eRefID:
dobrangeunitsid:
dobrangehidden:
hiddenFlag2:".getINPUT('<input id="hiddenFlag2" name="hiddenFlag2" type="hidden" value="(.+)">', $login2)."
HiddenGPCode:
dodrangeunitsid:
dodrangehidden:
InterfaceSelect:
OrigPatID:
dobHIDDEN:
WLTreatmentID:
MotherDR:
HiddenDoctorCode:
FHResidentNo:
NationalID:$buscar_rut
PAPERPassportNumber:
PAPERName:
PAPERName3:
PAPERName2:
Age:
MedicalRecordNo:$buscar_ficha
RegistrationNo:
VisitStatus:");

		//print($form_params); 

		curl_setopt( $ch, CURLOPT_URL, 'http://10.8.163.40/trakcare/csp/websys.csp' );

                curl_setopt( $ch, CURLOPT_POST, 1);
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $form_params);

                //print("\n\nPASO 6\n\n");

                $login2 = curl_exec ($ch);
                file_put_contents('data6.log',$login2);

		$url=getINPUT("A id=\"RTMAS_MRNoz1\" href='(.+)'", $login2);	

		curl_setopt( $ch, CURLOPT_URL, 'http://10.8.163.40/trakcare/csp/'.$url );

                curl_setopt( $ch, CURLOPT_POST, 0);
                curl_setopt( $ch, CURLOPT_POSTFIELDS, '');

                //print("\n\nPASO 7\n\n");

                $login2 = curl_exec ($ch);
                file_put_contents('data7.log',$login2);
		//print($login2);

		$login2=preg_replace('/(\<HEAD\>.+\<\/HEAD\>)/s', '', $login2);
		$login2=preg_replace('/(\<SCRIPT\s.+\<\/SCRIPT\>)/s', '', $login2);
		$login2=preg_replace('/(\<script\s.+\<\/script\>)/s', '', $login2);
		$login2=str_replace('"', "'", $login2);

		preg_match("/\<FORM (.+)\<\/FORM\>/s", $login2, $ttmp);

		$login2='<FORM '.$ttmp[1]."</FORM>";

		$pacdom=str_get_html($login2);

		$rut=strtoupper(getfieldval2('input[id=PAPERID]', $pacdom));
		$ficha=getfieldval2('input[id=RTMASMRNo]', $pacdom);
		$nombres=getfieldval2('input[id=PAPERName2]', $pacdom);
		$paterno=getfieldval2('input[id=PAPERName]', $pacdom);
		$materno=getfieldval2('input[id=PAPERName3]', $pacdom);
		$direccion=getfieldval2('input[id=PAPERStNameLine1]', $pacdom);
		$comuna=getfieldval2('input[id=CTCITDesc]', $pacdom);
		$fono=getfieldval2('input[id=PAPERTelH]', $pacdom);
		$celular=getfieldval2('input[id=PAPERMobPhone]', $pacdom);
		$sexo=getfieldval2('input[id=CTSEXDesc]', $pacdom);
		$fechanac=getfieldval2('input[id=PAPERDob]', $pacdom);

		$prev_id=-1; $sector=''; $tramo=''; $pasaporte=''; $mail='';
		$prais='null'; $id_sidra=getfieldval2('input[id=RegistrationNumber]', $pacdom);

		$estciv=-1;
		$sex_id=2;
		if($sexo=='Masculino') $sex_id=0;
		if($sexo=='Femenino') $sex_id=1;


		$chk=cargar_registro("SELECT * FROM pacientes WHERE (pac_rut='$rut' AND NOT pac_rut='') OR (pac_ficha='$ficha' AND NOT pac_ficha='' AND pac_ficha IS NOT NULL);");
	

		$ciud=cargar_registro("SELECT * FROM comunas WHERE ciud_desc ILIKE '%$comuna%';");

		if($ciud) $ciud_id=$ciud['ciud_id']*1;
		else	$ciud_id=-1;
	
		if(!$chk) {
                    

			$query="INSERT INTO pacientes VALUES (
				DEFAULT,
				'$rut', 
				'$nombres', '$paterno', '$materno', 
				'$fechanac', $sex_id, $prev_id, '$sector', -1, -1, 
				'$direccion', $ciud_id,
				1, $estciv, '$fono', '', '', '$tramo', '$pasaporte', '$ficha', -1, 
				'$mail', '$celular', $prais, '$id_sidra'
			);";

			//print($query);

			$q=pg_query($query);
			
			$pid=pg_query("SELECT CURRVAL('pacientes_pac_id_seq') AS pid;");
			$pid=pg_fetch_assoc($pid);
			$pac_id=$pid['pid']*1;


		} else {

			$pac_id=$chk['pac_id']*1;

			pg_query("UPDATE pacientes SET pac_ficha='$ficha' WHERE pac_id=$pac_id;");

		}

		print($pac_id);
			
	}
	


trakcare_login();

?>
