<?php 

	require_once('../../conectar_db.php');
	
	$fecha=pg_escape_string(utf8_decode(($_GET['fecha1'])));
	$func_id=($_GET['funcionarios']*1);
	
	/*$d=cargar_registros_obj("
		SELECT * FROM caja_detalle
		WHERE cd_fecha='$fecha' AND func_id=$func_id	
	");*/

	$chq=array();
	
	$chq[1]=20000;
	$chq[2]=10000;
	$chq[3]=5000;
	$chq[4]=2000;
	$chq[5]=1000;
	$chq[6]=500;
	$chq[7]=100;
	$chq[8]=50;
	$chq[9]=10;
	$chq[10]=5;
	$chq[11]=1;

	for($i=1;$i<=11;$i++) {

		$dg[$i]=0;
		
		if($d)
		for($j=0;$j<sizeof($d);$j++) {
			if($d[$j]['cd_tipo']==$chq[$i]) {
				$dg[$i]=$d[$j]['cd_monto']*1;
			}
		}
		
		if($dg[$i]==0) $dg[$i]='';

	}	
		
	$func_w='func_id='.$func_id;
	$func_w2='func_id_ejecuta='.$func_id;
	
	$ttmp=cargar_registro("SELECT * FROM apertura_cajas WHERE func_id=$func_id AND ac_fecha_cierre IS NULL;");

	if(!$ttmp) {

		exit("<script>alert('No ha realizado apertura de caja.');window.close()</script>");

	}
	
	$fecha1=$ttmp['ac_fecha_apertura'];
	
	//print($fecha1);
	
	$l=cargar_registros_obj("
		

SELECT *, bolfec AS bolfec, (
			SELECT SUM(monto) FROM cheques
			WHERE cheques.bolnum=boletines.bolnum		
		) AS cheques,(
			SELECT SUM(monto) FROM forma_pago
			WHERE forma_pago.bolnum=boletines.bolnum
		) AS fpagos 
		FROM boletines 
		WHERE bolfec >= '$fecha1' AND anulacion='' AND $func_w
	
	");
	
	$dev=cargar_registro("SELECT SUM(monto_total) from devolucion_boletines 
	where  dev_ejecuta >= '$fecha1' AND $func_w2 ");
		
	$efectivo=0;
	
	if($l)
	for($i=0;$i<sizeof($l);$i++) {
			
		$befectivo=($l[$i]['bolmon']*1)-($l[$i]['cheques']*1)-($l[$i]['fpagos']*1);
		
		$efectivo+=$befectivo;	

	}
	$efectivo=$efectivo+$dev['sum']*1;
	print_r($dev);
?>

<html><title>
Completar Detalle de Efectivo
</title>

<?php cabecera_popup('../..'); ?>

<script>

var efectivo=<?php echo json_encode($efectivo+50000); ?>;

function chequear(obj, div) {
	
	calcular_totales();
	
}

function calcular_totales() {

	var chq=[];

	chq[1]=20000;
	chq[2]=10000;
	chq[3]=5000;
	chq[4]=2000;
	chq[5]=1000;
	chq[6]=500;
	chq[7]=100;
	chq[8]=50;
	chq[9]=10;
	chq[10]=5;
	chq[11]=1;

	var monto=0;
	var chk=true;
	
	for(var i=1;i<=11;i++) {
		
		var obj=$('m_'+i);
		
		monto+=obj.value*1;
		
		if(obj.value*1==0) {
			
			obj.value='';
			obj.style.background='';
				
		} else {
		
			var d=(obj.value*1)%chq[i];
	
			if(d != 0) {
				obj.style.background='red';	
			} else {
				obj.style.background='yellowgreen';	
			}		

		}
				
	}
	
	if(monto==efectivo && chk) {
		$('guardar').disabled=false;	
	} else {
		$('guardar').disabled=true;			
	}	
	
	$('montoing').innerHTML='$ '+number_format(monto,0,',','.')+'.-';	
	
}

function guardar_datos() {
	
	$('datos').submit();	

	return;

	var myAjax=new Ajax.Request(
		'sql_detalle.php',
		{
			method:'post',
			parameters: $('datos').serialize(),
			onComplete: function(resp) {
				alert('Detalle guardado exitosamente.');
				window.close();	
			}	
		}	
	);	
	
}

</script>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content'>
<img src='../../iconos/coins.png'>
<b>Registrar Detalle de Efectivo <?php echo $fecha; ?></b>
</div>

<form id='datos' name='datos' action='sql_detalle.php' method='post' target='_self'
onSubmit='return false;'>

<input type='hidden' id='fecha' name='fecha' value='<?php echo $fecha; ?>' />
<input type='hidden' id='func_id' name='func_id' value='<?php echo $func_id; ?>' />

<div class='sub-content'>

<table style='width:100%;'>

<tr><td valign='top'>

<table style='width:100%;'>

<tr><td colspan=2 style='text-align:center;font-weight:bold;'><u>Billetes</u></td></tr>

<tr><td style='text-align:right;font-weight:bold;width:70px;'>
$ 20.000.-
</td><td>
<input type='text' size=10 style='text-align:right;' value='<?php echo $dg[1]; ?>' 
id='m_1' name='m_1' onKeyUp='chequear(this,20000);'/>
</td></tr>

<tr><td style='text-align:right;font-weight:bold;'>
$ 10.000.-
</td><td>
<input type='text' size=10 style='text-align:right;'  value='<?php echo $dg[2]; ?>'
id='m_2' name='m_2' onKeyUp='chequear(this,10000);'/>
</td></tr>

<tr><td style='text-align:right;font-weight:bold;'>
$ 5.000.-
</td><td>
<input type='text' size=10 style='text-align:right;'  value='<?php echo $dg[3]; ?>'
id='m_3' name='m_3' onKeyUp='chequear(this,5000);'/>
</td></tr>

<tr><td style='text-align:right;font-weight:bold;'>
$ 2.000.-
</td><td>
<input type='text' size=10 style='text-align:right;'  value='<?php echo $dg[4]; ?>'
id='m_4' name='m_4' onKeyUp='chequear(this,2000);'/>
</td></tr>

<tr><td style='text-align:right;font-weight:bold;'>
$ 1.000.-
</td><td>
<input type='text' size=10 style='text-align:right;'  value='<?php echo $dg[5]; ?>'
id='m_5' name='m_5' onKeyUp='chequear(this,1000);'/>
</td></tr>

</table>

</td><td valign='top'>

<table style='width:100%;'>

<tr><td colspan=2 style='text-align:center;font-weight:bold;'><u>Monedas</u></td></tr>

<tr><td style='text-align:right;font-weight:bold;width:70px;'>
$ 500.-
</td><td>
<input type='text' size=10 style='text-align:right;'  value='<?php echo $dg[6]; ?>'
id='m_6' name='m_6' onKeyUp='chequear(this,500);'/>
</td></tr>

<tr><td style='text-align:right;font-weight:bold;'>
$ 100.-
</td><td>
<input type='text' size=10 style='text-align:right;'  value='<?php echo $dg[7]; ?>'
id='m_7' name='m_7' onKeyUp='chequear(this,100);'/>
</td></tr>

<tr><td style='text-align:right;font-weight:bold;'>
$ 50.-
</td><td>
<input type='text' size=10 style='text-align:right;'  value='<?php echo $dg[8]; ?>'
id='m_8' name='m_8' onKeyUp='chequear(this,50);'/>
</td></tr>

<tr><td style='text-align:right;font-weight:bold;'>
$ 10.-
</td><td>
<input type='text' size=10 style='text-align:right;'  value='<?php echo $dg[9]; ?>'
id='m_9' name='m_9' onKeyUp='chequear(this,10);'/>
</td></tr>

<tr><td style='text-align:right;font-weight:bold;'>
$ 5.-
</td><td>
<input type='text' size=10 style='text-align:right;'  value='<?php echo $dg[10]; ?>'
id='m_10' name='m_10' onKeyUp='chequear(this,5);'/>
</td></tr>

<tr><td style='text-align:right;font-weight:bold;'>
$ 1.-
</td><td>
<input type='text' size=10 style='text-align:right;'  value='<?php echo $dg[11]; ?>'
id='m_11' name='m_11' onKeyUp='chequear(this,1);'/>
</td></tr>

</table>

</td></tr>

<tr><td style='text-align:right;'>
Monto Recaudado:
</td><td id='montodet' style='font-weight:bold;color:black;'>$ <?php echo number_format($efectivo,0,',','.'); ?>.-</td></tr>

<tr><td style='text-align:right;'>
Fondo Fijo:
</td><td id='montorec' style='font-weight:bold;color:black;'>$ <?php echo number_format(50000,0,',','.'); ?>.-</td></tr>

<tr><td style='text-align:right;'>
Monto a Rendir:
</td><td id='montorec' style='font-weight:bold;color:blue;'>$ <?php echo number_format($efectivo+50000,0,',','.'); ?>.-</td></tr>

<tr><td style='text-align:right;'>
Monto Ingresado:
</td><td id='montoing' style='font-weight:bold;'>$ 0.-</td></tr>

</table>

<center><br />
<input type='button' id='guardar' name='guardar' DISABLED
value='Guardar Detalle de Caja...' onClick='guardar_datos();'/>
</center>
<br />
</div>

</form>

</body>

</html>

<script> calcular_totales(); </script>
