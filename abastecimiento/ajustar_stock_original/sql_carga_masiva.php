<?php 

	require_once('../../conectar_db.php');
	
	$bod_id=$_POST['bodega_id']*1;
	
	$bod=cargar_registro("SELECT * FROM bodega WHERE bod_id=$bod_id", true);
	
	$bodega=$bod['bod_glosa'];
	
	if(!isset($_FILES['archivo_carga']) OR $_FILES['archivo_carga']['error']!=0) {
		exit('<script>alert("ERROR AL CARGAR PLANILLA.");window.close();</script>');
	}
	
	$d=explode("\n",file_get_contents($_FILES['archivo_carga']['tmp_name']));
	
	$c=0;
	
	$html='';
	
	
	for($i=0;$i<sizeof($d);$i++) {
		
		if(trim($d[$i])=='') continue;
		
		$r=explode(";", trim($d[$i]));
		
		$codigo=pg_escape_string(trim($r[0]));
		$lote=pg_escape_string(str_replace('-','/',trim($r[3])));
		$cantidad=trim($r[4]);
		
		if($codigo=='' OR $cantidad=='') continue;
		
		$art=cargar_registro("SELECT *, calcular_stock(art_id, $bod_id) AS cantidad FROM articulo WHERE art_codigo='$codigo'", true);
		
		if(!$art) continue;
		
		$c++;
		
		$art_id=$art['art_id'];
		$nombre=$art['art_glosa'];
		$cactual=$art['cantidad']*1;
		$cantidad*=1;
		
		$dif=$cantidad-$cactual;
		
		if($dif>0) $signo='+';
		if($dif<0) $signo='-';
		
		$dif=abs($dif);
		
		$clase=($c%2==0)?'tabla_fila':'tabla_fila2';
		
		$html.="<tr><td style='text-align:right;'>$c</td><td style='text-align:right;font-weight:bold;'>$codigo</td><td>$nombre</td><td style='text-align:center;'>$lote</td><td style='text-align:right;'>".number_format($cactual,0,',','.')."</td><td style='text-align:right;'>".number_format($cantidad,0,',','.')."</td><td style='text-align:right;font-weight:bold;'>$signo".number_format($dif,0,',','.')."</td></tr>";
		
		
	}
	
	print("
	<html>
	<title>Ajuste Masivo de Saldos</title>
	");
	
	cabecera_popup('../..');
	
	print("
	<body class='fuente_por_defecto popup_background'>
	<center><h1>Ajuste Masivo de Saldos<br><u>$bodega</u></h1></center>
	<table style='width:100%;'>
		<tr class='tabla_header'>
			<td>#</td><td>C&oacute;digo</td><td>Nombre</td><td>Lote</td><td>Cant. Actual</td><td>Cantidad</td><td>Diferencia</td>
		</tr>
		$html
	</table>
	<center><br><br>
	<input type='button' id='' name='' value='[[[[ Confirmar Ajuste de Saldos ]]]]' onClick='alert(\"CONSULTE AL ADMINISTRADOR DEL SISTEMA.\");'></center>
	</body>
	</html>
	");

?>
