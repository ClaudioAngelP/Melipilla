<?php 	
	require_once('../../conectar_db.php');
	
	$aud_motivo_id	 =	utf8_decode($_GET['aud_motivo_id']);
	$monto_ing_rec 	 =	$_GET['monto_ing_rec'];
	$monto_ing_var 	 =	$_GET['monto_ing_var'];
	$ac_id			 =	$_GET['ac_id'];
	$func_id		 =	$_GET['func_id'];
	
	$result_total_caja = pg_query($conn,"
									SELECT count(bolnum) as cantidadbolnum,SUM((
									SELECT 
										SUM(bdet_valor) 
									FROM 
										boletin_detalle 
									WHERE 
										boletin_detalle.bolnum = boletines.bolnum) ) as super_total
									FROM boletines 
									WHERE bolfec >= (select ac_fecha_apertura from apertura_cajas where func_id=$func_id and ac_fecha_cierre is null) 
									OR anulacion is null AND func_id=$func_id
								") or die(pg_last_error());
	
	
	while($row = pg_fetch_array($result_total_caja)){
		$super_total = $row['super_total'];
		$cantidadbolnum = $row['cantidadbolnum'];	
	}
	if($result_total_caja != TRUE){
		echo pg_last_error();
		return;
	}
	
	
	$result = pg_query($conn, "INSERT INTO 
										aud_caja
					   						(
											  ac_id,
											  aud_caja_fecha,
											  monto_recaudado,
											  fondo_variable,
											  bdet_valor,
											  diferencia,
											  func_id,
											  cantidad_boletines,
											  aud_motivo
											  )
										VALUES 
											($ac_id,
											 CURRENT_TIMESTAMP,
											 $monto_ing_rec,
											 $monto_ing_var,
											 $super_total,
											 $super_total - $monto_ing_var,
											 $func_id,
											 $cantidadbolnum,
											 '$aud_motivo_id'								 
											 )		  		
					   					
					   			") or die(pg_last_error());
					   			
	if($result != TRUE){
		echo pg_last_error();
		return;
	}		

	$rcajero = pg_query($conn,"SELECT func_nombre FROM funcionario WHERE func_id = $func_id") 
						or die(pg_last_error());
	
	while($row = pg_fetch_array($rcajero)){
		
		$func_nombre = $row['func_nombre'];
		
	}
	
	$rregistro = pg_query($conn, "SELECT MAX(aud_caja_id) as idcaja FROM aud_caja ");
	
	while($row = pg_fetch_array($rregistro)){
		$idcaja = $row['idcaja'];
	}
	
	$rcajabierta = pg_query($conn, "select ac_fecha_apertura from apertura_cajas where func_id=$func_id and ac_fecha_cierre is null ");
	
	while($row = pg_fetch_array($rcajabierta)){
		$ac_fecha_apertura = $row['ac_fecha_apertura'];
	}
	
	$ac_fecha_apertura = explode(".", $ac_fecha_apertura);
	
?> 
<script type="text/javascript">
	//desactivar tecla f5
	document.onkeydown = function(e){ 
		tecla = (document.all) ? e.keyCode : e.which;
		
		if (tecla = 116) return false
		if (tecla = 13) return false
	}
</script>
<body style="background-color: #AECAE2; font-family: Arial,Liberation Sans,sans-serif;" onLoad="">
	<table style="width:100%;">
    	<tr class="tabla_header">
		<tr>
			<td colspan=13 style="text-align:center;font-weight:bold;">
        	<h3>Centro de Referencia de Salud Cordillera Oriente</h3>
        	</td>
        </tr>
    	<tr>
    		<td colspan=13 style="text-align:center;font-weight:bold;">
            	<h2>
            		<u>
            			Auditoria Caja / N <?php echo utf8_decode("° ".$idcaja); ?> 
              		</u>
            	</h2>
            </td>
        </tr>
        <tr>
        	<td colspan=13>&nbsp;</td>
        </tr>
        
      <!--  <tr>
        	<td style="text-align:right; width:300px;">Fecha Auditoria</td>
            <td style="text-align:left;font-weight:bold;" colspan=12>
            	<?php echo date('d-m-Y H:i:s');?>
            </td>
        </tr>
        <tr>
        	<td style="text-align:right; width:300px;">Nombre Cajero(a)</td>
            <td style="text-align:left;font-weight:bold;" colspan=12>
            	<?php echo $func_nombre; ?>
            </td>
        </tr>
          <tr>
        	<td style="text-align:right; width:300px;">Fecha Apertura Caja(a)</td>
            <td style="text-align:left;font-weight:bold;" colspan=12>
            	<?php echo $ac_fecha_apertura[0]; ?>
            </td>
        </tr>
        <tr>
        	<td style="text-align:right; width:300px;">Cantidad de Boletines Auditados</td>
            <td style="text-align:left;font-weight:bold;" colspan=12>
            	<?php echo $cantidadbolnum; ?>
            </td>
        </tr>-->
	</table>
	<table style="width:100%; text-align: center;" class="tabla_header">
        <tr  style="font-weight:bold;background-color: #CCCCCC;">
        	<td style="border: 1px solid black;" > 
        		Fecha Auditoria
        	</td >
        	<td style="border: 1px solid black;">
        		Fecha Apertura Caja(a)
        	</td>
        	<td style="border: 1px solid black;">
        		Nombre Cajero(a)
        	</td>
        	<td style="border: 1px solid black;">
        		Cantidad de Boletines Auditados
        	</td>
        	<td style="border: 1px solid black;">
        		Cantidad Total Boletines
        	</td>
        	<td style="border: 1px solid black;">
        		Total Recaudado
        	</td>
        	<td style="border: 1px solid black;">
        		Fondo Variable
        	</td>
        	<td style="border: 1px solid black;">
        		Diferencia
        	</td>
        </tr>
        <tr  style="background-color: #FAEAEA;">
        	<td style="border: 1px solid black;">
        		<?php echo date('d-m-Y H:i:s');?>
        	</td >
        	<td style="border: 1px solid black;">
        		<?php echo $ac_fecha_apertura[0]; ?>
        	</td>
        	<td style="border: 1px solid black;">
        		<?php echo $func_nombre; ?>
        	</td>
        	<td style="border: 1px solid black;">
        		<?php echo $cantidadbolnum; ?>
        	</td>
        	<td style="border: 1px solid black;">
        		$ <?php echo $super_total; ?>
        	</td>
        	<td style="border: 1px solid black;">
        		$ <?php echo $monto_ing_rec; ?>
        	</td>
        	<td style="border: 1px solid black;">
        		$ <?php echo $monto_ing_var; ?>
        	</td>
        	<td style="border: 1px solid black;">
        		$ <?php echo $super_total - $monto_ing_var; ?>
        	</td>
        </tr>
    </table>
    <center>
    <table style="margin-top: 200px;">
    	<tr>
    		<td style="font-weight: bold; width: 200px; text-align: center;">
    			--------------------------<BR/>
    			Firma Auditor
    		</td>
    		<td style="font-weight: bold; width: 200px; text-align: center;">
    			--------------------------<BR/>
    				Cajera
    		</td>
    	</tr>
    </table>
    </center>
          
</body>