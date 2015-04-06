<?php

  require_once('../../conectar_db.php');
  
  $convenio_id=$_POST['convenio_id']*1;
  
  $licitacion_conv = pg_escape_string(strtoupper(trim(utf8_decode($_POST['convenio_licitacion']))));
  $nombre_conv = pg_escape_string(strtoupper(trim(utf8_decode($_POST['nombre_convenio']))));
  
  /*
   
CREATE TABLE convenio
(
  convenio_id bigserial NOT NULL,
  convenio_nombre character varying DEFAULT 80,
  prov_id bigint,
  convenio_monto bigint,
  convenio_plazo integer,
  convenio_mails text,
  convenio_fecha_inicio date,
  convenio_fecha_final date,
  convenio_licitacion text,
  convenio_nro_res_aprueba text,
  convenio_nro_anio_aprueba character varying(4),
  convenio_nro_res_adjudica text,
  convenio_nro_anio_adjudica character varying(4),
  convenio_nro_res_contrato text,
  convenio_fecha_resolucion date,
  func_id bigint,
  convenio_nro_boleta text,
  convenio_banco_boleta text,
  convenio_fecha_boleta date,
  convenio_monto_boleta bigint,
  convenio_multa text,
  convenio_comentarios text,
  convenio_categoria text,
  convenio_aprueba text,
  CONSTRAINT convenio_id PRIMARY KEY (convenio_id)
)
WITH (
  OIDS=FALSE
);
* 
* 
* 
   */
  
  $prov_id=$_POST['id_proveedor']*1;
  $func_id=$_POST['func_id']*1;
  $monto=$_POST['monto']*1;
  $plazo=$_POST['plazo']*1;

  $categoria=$_POST['categoria'];

  $mails=pg_escape_string($_POST['mails']);
  
  $fecha_inicio=pg_escape_string($_POST['inicio']);
  $fecha_final=pg_escape_string($_POST['termino']);
  $fecha_boleta=pg_escape_string($_POST['fecha_boleta']);

  $res_aprueba=pg_escape_string($_POST['res_aprueba']);
  $fecha_aprueba=pg_escape_string($_POST['fecha_aprueba']);
  $sel_aprueba=$_POST['sel_aprueba'];

  $res_adjudica=pg_escape_string($_POST['res_adjudica']);
  $fecha_adjudica=pg_escape_string($_POST['fecha_adjudica']);

  $res_prorroga=pg_escape_string($_POST['res_prorroga']);
  $fecha_prorroga=pg_escape_string($_POST['fecha_prorroga']);

  $res_aumento=pg_escape_string($_POST['res_aumento']);
  $fecha_aumento=pg_escape_string($_POST['fecha_aumento']);

  $res_contrato=pg_escape_string($_POST['res_contrato']);
  $fecha_contrato=pg_escape_string($_POST['fecha_contrato']);
  
  if($fecha_inicio=='') $fecha_inicio='null'; else $fecha_inicio="'$fecha_inicio'";
  if($fecha_final=='') $fecha_final='null'; else $fecha_final="'$fecha_final'";
  if($fecha_contrato=='') $fecha_contrato='null'; else $fecha_contrato="'$fecha_contrato'";
  if($fecha_boleta=='') $fecha_boleta='null'; else $fecha_boleta="'$fecha_boleta'";
  if($fecha_aprueba=='') $fecha_aprueba='null'; else $fecha_aprueba="'$fecha_aprueba'";
  if($fecha_adjudica=='') $fecha_adjudica='null'; else $fecha_adjudica="'$fecha_adjudica'";
  if($fecha_prorroga=='') $fecha_prorroga='null'; else $fecha_prorroga="'$fecha_prorroga'";
  if($fecha_aumento=='') $fecha_aumento='null'; else $fecha_aumento="'$fecha_aumento'";
  if($sel_aprueba=='-1') $sel_aprueba=''; 
  
  $nro_boleta=pg_escape_string($_POST['nro_boleta']);
  $banco_boleta=pg_escape_string($_POST['banco_boleta']);
  $monto_boleta=pg_escape_string($_POST['monto_boleta']*1);
  
  $multa=pg_escape_string($_POST['multa']);
  $comenta=pg_escape_string($_POST['comenta']);

  
  if($convenio_id==0) {
	  
	  pg_query($conn,
	  "
		  INSERT INTO convenio VALUES (
		  DEFAULT,
		  '$nombre_conv',
		  $prov_id, $monto, $plazo, '$mails', $fecha_inicio, $fecha_final,
		  '$licitacion_conv', 
		  '$res_aprueba',
		  '$res_adjudica',
		  '$res_contrato',$fecha_contrato,
		  $func_id,
		  '$nro_boleta',
		  '$banco_boleta',
		  $fecha_boleta,
		  $monto_boleta,
		  '$multa',
		  '$comenta',
		  $fecha_aprueba,
		  $fecha_adjudica,
		  '$res_prorroga', $fecha_prorroga,
		  '$res_aumento', $fecha_aumento,
		  '$categoria','$sel_aprueba'
		  );
	  ");
  
	} else {
		
		pg_query("
		
		UPDATE convenio SET 
		convenio_nombre='$nombre_conv',
		prov_id=$prov_id,
		convenio_monto=$monto,
		convenio_plazo=$plazo,
		convenio_mails='$mails',
		convenio_fecha_inicio=$fecha_inicio,
		convenio_fecha_final=$fecha_final,
		convenio_licitacion='$licitacion_conv',
		convenio_nro_res_aprueba='$res_aprueba',
		convenio_fecha_aprueba=$fecha_aprueba,
		convenio_nro_res_adjudica='$res_adjudica',
		convenio_fecha_adjudica=$fecha_adjudica,
		convenio_nro_res_contrato='$res_contrato',
		convenio_fecha_resolucion=$fecha_contrato,
		func_id=$func_id,
		convenio_nro_boleta='$nro_boleta',
		convenio_banco_boleta='$banco_boleta',
		convenio_fecha_boleta=$fecha_boleta,
		convenio_monto_boleta=$monto_boleta,
		convenio_multa='$multa',
		convenio_comentarios='$comenta',
		convenio_nro_res_prorroga='$res_prorroga',
		convenio_fecha_prorroga=$fecha_prorroga,
		convenio_nro_res_aumento='$res_aumento',
		convenio_fecha_aumento=$fecha_aumento,
		convenio_categoria='$categoria',
		convenio_aprueba='$sel_aprueba'
		WHERE convenio_id=$convenio_id
		
		
		");
		
	}
  
  print('1');

?>
