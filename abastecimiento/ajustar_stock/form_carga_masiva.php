<?php 

	require_once('../../conectar_db.php');
	
	$bod_id=$_POST['bodega_id']*1;
	
	$bod=cargar_registro("SELECT * FROM bodega WHERE bod_id=$bod_id", true);
	
	$bodega=$bod['bod_glosa'];
	
	$comentarios = pg_escape_string($_POST['comentarios']);
	
	if(!isset($_FILES['archivo_carga']) OR $_FILES['archivo_carga']['error']!=0) {
		exit('<script>alert("ERROR AL CARGAR PLANILLA.");window.close();</script>');
	}

	$d=explode("\n",file_get_contents($_FILES['archivo_carga']['tmp_name']));
	
	$c=0;
	
	$html='';
	$ids='';
	
	
	for($i=0;$i<sizeof($d);$i++) {
		
		if(trim($d[$i])=='') continue;
		
		$r=explode(";", trim($d[$i]));
		
		$codigo=pg_escape_string(trim($r[0]));
		$lote=pg_escape_string(str_replace('-','/',trim($r[3])));
		$cantidad=trim($r[4]);
		
		if($codigo=='' OR $cantidad=='') continue;
		
		/*if($lote=='')
			$fvence='stock_vence IS null';
		else
			$fvence="stock_vence='$lote'";*/
		
		$art=cargar_registro("SELECT *, 
		COALESCE((SELECT SUM(stock_cant) FROM stock WHERE stock_bod_id=$bod_id AND stock_art_id=art_id),0) AS cantidad
		FROM articulo WHERE art_codigo='$codigo'
		ORDER BY art_glosa"
		, true);
		
		if(!$art) continue;
		
		if($art['art_vence']==true AND $lote=='')
			$lote='31/12/2013';
			
		
		$c++;
		
		$art_id=$art['art_id'];
		$nombre=$art['art_glosa'];
		$cactual=$art['cantidad']*1;
		$cantidad*=1;
		
		$dif=$cantidad-$cactual;

		//$tmpdif=$dif;
		$tmpdif=$cantidad;
		
		if($dif>0) $signo='+';
		if($dif<0) $signo='-';
		if($dif==0) $signo='';
		
		$dif=abs($dif);
		
		$clase=($c%2==0)?'tabla_fila':'tabla_fila2';
		
		$html.="<tr class='$clase'><td style='text-align:right;'>$c</td><td style='text-align:right;font-weight:bold;'>
		$codigo</td><td>$nombre</td>
		<td style='text-align:center;'>
		$lote</td>
		<td style='text-align:right;'>".number_format($cactual,0,',','.')."</td>
		<td style='text-align:right;'>".number_format($cantidad,0,',','.')."</td>
		<td style='text-align:right;font-weight:bold;'>
		$signo".number_format($dif,0,',','.')."</td></tr>";
		
		$ids.=$art_id."!!$lote!!$tmpdif&&&";
		
	}
	
?>
<script>

	confirmar = function(){
		
		var myAjax=new Ajax.Request(
      'sql_carga_masiva.php',
      {
        method: 'post',
        parameters: $('datos_ajuste').serialize(),
        onComplete: function(resp) {
        
          op = resp.responseText.evalJSON(true);
          
          if(op)
            visualizador_documentos('Ajuste de Stock', 'log_id='+op);
        
          limpiar_todo();
          mutex=false;
        
        }
      }
      );
	
	}
	
	
imprimir_diferencias=function() {
    
    _detalle = $('datos_ajuste').innerHTML;
  
  imprimirHTML(_detalle);

}

</script>
<?php
	
	print("
	<html>
	<title>Ajuste Masivo de Saldos</title>
	");
	
	cabecera_popup('../..');
	
	print("
	<body class='fuente_por_defecto popup_background'>
	<form id='datos_ajuste' name='datos_ajuste' action='sql_carga_masiva.php'
	onSubmit='return false;'>
	<center><h1>Ajuste Masivo de Saldos<br><u>$bodega</u></h1></center>
	<input type='hidden' name='bod_id' id='bod_id' value='".$bod['bod_id']."'>
	<input type='hidden' name='ids' id='ids' value='".trim($ids,',')."' >
	<input type='hidden' name='comentarios' id='comentarios' value='$comentarios'>
	<table style='width:100%;'>
		<tr class='tabla_header'>
			<td>#</td><td>C&oacute;digo</td><td>Nombre</td><td>Lote</td><td>Cant. Actual</td><td>Cantidad</td><td>Diferencia</td>
		</tr>
		$html
	</table>
	</form>
	<center><br><br>
	<input type='button' id='' name='' value='[[[[ Confirmar Ajuste de Saldos ]]]]' onClick='confirmar();'>
	<input type='button' id='' name='' value='[[[[ Imprimir Diferencias ]]]]' onClick='imprimir_diferencias();'></center>
	</body>
	</html>
	");

?>
