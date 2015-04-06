<?php

require_once('../../conectar_db.php');

$fap_id=$_POST['fap_id']*1;
$pausa_tipo=$_POST['ptipo']*1;
$fcl_id=$_POST['fcl_id']*1;

$res='';

$chk_suspendido=cargar_registro("SELECT fap_suspension FROM fap_pabellon WHERE fap_id=$fap_id;");

if($chk_suspendido['fap_suspension']==''){
if($pausa_tipo==0){
$cl=cargar_registros_obj("SELECT *, (SELECT count(*) FROM fap_checklist_detalle AS fcld WHERE fap_id=$fap_id AND fcld.fcl_id=fap_checklist.fcl_id) AS cnt  FROM fap_checklist WHERE fcl_nombre ilike '%pausa de seg%'ORDER BY fcl_nombre;", true);	
if($cl)
	for($i=0;$i<sizeof($cl);$i++) {
		if($cl[$i]['cnt']*1==0)
			$res.=utf8_decode($cl[$i]['fcl_nombre']).'';
	}

$cl=cargar_registros_obj("SELECT *, (SELECT count(*) FROM fap_checklist_detalle AS fcld WHERE fap_id=$fap_id AND fcld.fcl_id=fap_checklist.fcl_id) AS cnt  FROM fap_checklist WHERE fcl_nombre NOT ILIKE '%pausa de s%'ORDER BY fcl_nombre;", true);
if($cl)
	for($i=0;$i<sizeof($cl);$i++) {
		if($cl[$i]['cnt']*1==0) 
			$res.=$cl[$i]['fcl_nombre'].'';
	}
}else{

if($fcl_id==0){
	$fcl_ids=$_POST['fcl_ids'];
	$fcl=explode('|',$fcl_ids);
}else{
	$fcl[0]=$fcl_id;
}
//INICIO FOR ids_fcl
for($o=0;$o<count($fcl);$o++){

$fcl_id=$fcl[$o];
$c=cargar_registro("SELECT * FROM fap_checklist WHERE fcl_id=$fcl_id");

$campos=explode("\n", $c['fcl_campos']);

$d=cargar_registro("SELECT * FROM fap_checklist_detalle JOIN funcionario USING (func_id) WHERE fap_id=$fap_id AND fcl_id=$fcl_id ORDER BY fcld_fecha DESC LIMIT 1;");

$dr=explode("\n", $d['fcld_datos']);

$digest=Array();

if($dr AND $dr!='')
	for($i=0;$i<sizeof($dr);$i++) {
		$tmp=explode('|',$dr[$i]);
		$digest[$tmp[0]]=$tmp[1];
	}
	

//FOR
	for($i=0;$i<sizeof($campos);$i++) {

		if(trim($campos[$i])=='') continue;
	
		if(strstr($campos[$i],'>>>')) {
			$cmp=explode('>>>',$campos[$i]);
			$nombre=($cmp[0]); $tipo=$cmp[1]*1;
			$nombrex=htmlentities($cmp[0]);
		} else {
			$cmp=$campos[$i]; $tipo=0;
			$nombre=($campos[$i]);
			$nombrex=htmlentities($campos[$i]);
		}
		
		if($tipo!=20 AND $tipo!=10 AND $tipo!=6){
		}else{
			continue;	
		}	
		
		if($tipo==0) {

			if(isset($digest[$nombre])){
				if($digest[$nombre]=='')
					$res='1';
			}else{
				$res='';
			}
			
			
		} elseif($tipo==1) {

			if(isset($digest[$nombre])){
				if($digest[$nombre]=='')
					$res='1';
			}else{
				$res='';
			}

			
							
		} elseif($tipo==3) {

			if(isset($digest[$nombre])) 
				$vact=htmlentities($digest[$nombre]);
			else
                $vact='';

        } elseif($tipo==5) {
		
			$opts=explode('//', $cmp[2]);
						
			if(isset($digest[$nombre])) 
				$vact=htmlentities($digest[$nombre]);
			else 
				$vact='';
			
			for($k=0;$k<sizeof($opts);$k++) {
				
				$opts[$k]=trim(htmlentities($opts[$k]));
				
				if($vact==$opts[$k]) $sel='SELECTED'; else $sel='';
				
			}			
						
		} elseif($tipo==6) {
		
			$opts=explode('//', $cmp[2]);
				
			if(isset($digest[$nombre])) {
				$vact=htmlentities($digest[$nombre]);
				$sel_opts=explode('//',$vact);
			} else {
				$vact='';
				$sel_opts=Array();
			}

			for($k=0;$k<sizeof($opts);$k++) {
				
				$opts[$k]=trim(htmlentities($opts[$k]));
				
				if(in_array($opts[$k],$sel_opts)) $sel='CHECKED'; else $sel='';	
				
			}			
			
		} elseif($tipo==7) {
		
			$opts=explode('//', $cmp[2]);
						
			if(isset($digest[$nombre])) 
				$vact=htmlentities($digest[$nombre]);
			else 
				$vact='';

			for($k=0;$k<sizeof($opts);$k++) {
				
				$opts[$k]=trim(htmlentities($opts[$k]));
				
				if($vact==$opts[$k]) $sel='CHECKED'; else $sel='';
				
			}			
			
		} elseif($tipo==10) {

			if(isset($digest[$nombre])) 
				$vact=htmlentities(str_replace('<br>',"\n",$digest[$nombre]));
			else 
				$vact='';
			
		} else {

			if(isset($digest[$nombre])) 
				$vact=htmlentities($digest[$nombre]);
			else 
				$vact='';
							
		}
		
	}
	if($d){
	if($res=='')
		pg_query("UPDATE fap_checklist_detalle SET fcld_completa=true WHERE fcld_id=".$d['fcld_id']);	
	else
		pg_query("UPDATE fap_checklist_detalle SET fcld_completa=false WHERE fcld_id=".$d['fcld_id']);
	}else{
		$res='.';
	}	
//FIN FOR
}//FIN FOR FCL
	
}
}
print($res);
?>
