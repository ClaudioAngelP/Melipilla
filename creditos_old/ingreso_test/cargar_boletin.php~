<?php 

	require_once('../../conectar_db.php');
	
	$bolnum=$_POST['nbolnum']*1;
	
	$b=cargar_registro("SELECT * FROM boletines WHERE bolnum=$bolnum", true);
	
	if(!$b) {
		
		exit(json_encode(array(false, false, false, false, false, false, false)));
			
	} else {

		if($b['clirut']*1!=0)
			$cl=cargar_registro("SELECT * FROM clientes WHERE clirut=".$b['clirut'], true);
		else 
			$cl=false;

		$db=cargar_registros_obj("SELECT boletin_detalle.*, 
												productos.*,
												propiedad_sepultura.*,
												uso_sepultura.*,
												psep_id,
												productos_sepultura.sep_clase AS psep_clase,
												productos_sepultura.sep_codigo AS psep_codigo,
												productos_sepultura.sep_numero AS psep_numero,
												productos_sepultura.sep_letra AS psep_letra												
											FROM boletin_detalle 
											LEFT JOIN productos ON bdet_prod_id=prod_id
											LEFT JOIN propiedad_sepultura USING (bdet_id)
											LEFT JOIN uso_sepultura USING (bdet_id)
											LEFT JOIN productos_sepultura USING (bdet_id)											
											WHERE boletin_detalle.bolnum=$bolnum", true);	
	
		if($b['crecod']*1!=0)
			$c=cargar_registro("SELECT * FROM creditos WHERE crecod=".$b['crecod'], true);
		else 
			$c=false;
			
		$desc=cargar_registros_obj("SELECT * FROM descuentos WHERE bolnum=$bolnum", true);

		$bloq=cargar_registros_obj("SELECT * FROM uso_sepultura WHERE bolnum2=$bolnum", true);
		
		$fpago=cargar_registros_obj("SELECT * FROM forma_pago WHERE bolnum=$bolnum");

		$cheques=cargar_registros_obj("SELECT * FROM cheques WHERE bolnum=$bolnum");
		
		exit(json_encode(array($b, $db, $c, $cl, $desc, $bloq, $fpago, $cheques)));	
		
	}
		

?>