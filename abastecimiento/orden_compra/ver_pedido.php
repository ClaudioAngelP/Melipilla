<?php 

	require_once('../../conectar_db.php');
	
	$pedido_nro=$_GET['pedido_nro']*1;
	
	$p=cargar_registro("
		SELECT * FROM pedido 
		LEFT JOIN bodega ON origen_bod_id=bod_id
		LEFT JOIN centro_costo ON origen_centro_ruta=centro_ruta
		WHERE pedido_nro=$pedido_nro
	", true);
	
	$pedido_id=$p['pedido_id']*1; 

	$d=cargar_registros_obj("
		SELECT art_codigo, art_glosa, forma_nombre, pedidod_cant, art_val_ult, (pedidod_cant*art_val_ult) AS subtotal,
		articulo.art_id,pedido_id,pedidod_id,pedidod_tramite,
		(convenio_fecha_inicio+CAST(convenio_plazo||' days' AS INTERVAL))::date AS fecha_entrega, 
	COALESCE(conveniod_plazo_entrega,(((convenio_fecha_inicio+CAST(convenio_plazo||' days' AS INTERVAL))::date)-convenio_fecha_inicio)) AS plazo
		FROM pedido_detalle 
		JOIN articulo USING (art_id)
		LEFT JOIN bodega_forma ON art_forma=forma_id
		LEFT JOIN convenio_detalle AS cd ON cd.art_id=articulo.art_id
		LEFT JOIN convenio ON cd.convenio_id=convenio.convenio_id AND convenio_fecha_inicio<=CURRENT_DATE AND convenio_fecha_final>CURRENT_DATE
		LEFT JOIN proveedor ON convenio.prov_id=proveedor.prov_id
		WHERE pedido_id=$pedido_id
	", true);
	
	$conv=explode(':',$p['pedido_comentario']);
	 

?>
<script>

regenerar_detalle = function(){

	var detalle=<?php echo json_encode($d); ?>;
	var chk='';
	var_clase='';
		
	detallehtml="<table style='width:100%;font-size:14px;'>";
	detallehtml+="<tr class='tabla_header'>";
	detallehtml+="<td>#</td>";
	detallehtml+="<td style='width:65%;'>C&oacute;digo O.C.</td>";
	detallehtml+="<td>Cantidad</td>";
	detallehtml+="<td>Formato</td>"
	detallehtml+="<td>$Unit</td>";
	detallehtml+="<td>$Subtotal</td>";
	detallehtml+="<td>Plazo (D&uacute;as)</td>";
	detallehtml+="<td>En Tr&aacute;mite</td>";
	detallehtml+="</tr>";

	for(var i=0;i<detalle.length;i++) {
		
		if(i%2==0)clase='tabla_fila'; else clase='tabla_fila2';
		if(detalle[i].pedidod_tramite=='t') chk='CHECKED'; else chk='';
		
		detallehtml+="<tr class='"+clase+"' style='height:40px;'>";
		detallehtml+="<td style='text-align:center;font-size:18px;'>"+(i+1)+"</td>";
		detallehtml+="<td style='text-align:left;font-weight:bold;'>["+detalle[i].art_codigo+"] "+detalle[i].art_glosa+"</td>"
		detallehtml+="<td style='text-align:right;font-weight:bold;'>"+detalle[i].pedidod_cant+"</td>";
		detallehtml+="<td style='text-align:left;'>"+detalle[i].forma_nombre+"</td>";
		detallehtml+="<td style='text-align:right;'>$"+detalle[i].art_val_ult+".-</td>";
		detallehtml+="<td style='text-align:right;'>$"+detalle[i].subtotal+".-</td>";
		detallehtml+="<td style='text-align:center;'>"+detalle[i].plazo+"</td>";
		detallehtml+="<td style='text-align:center;'>";
		detallehtml+="<input type='checkbox' name='chk_"+detalle[i].pedidod_id+"' id='chk_"+detalle[i].pedidod_id+"' ";
		detallehtml+="onClick='marcar_pedido("+detalle[i].pedidod_id+");' "+chk+" >";
		detallehtml+="<img src='../../imagenes/ajax-loader1.gif' id='pedido_marcar_"+detalle[i].pedidod_id+"'";
		detallehtml+="style='display:none;'></td></tr>";
		
	}
	
	detallehtml+="</table>";
	
	$('detalle').innerHTML=detallehtml;
}

marcar_pedido=function(id,pedido)
{
    valchk=$('chk_'+id).checked;
    if(valchk) valchk='1'; else valchk='0';
    $('chk_'+id).style.display='none';
    $('pedido_marcar_'+id).style.display='';
    checks = $('detalle').getElementsByTagName('input');
    var x = checks.length;
    for(var i=0;i<x;i++)
    {
        if(checks[i].type=='checkbox')
        {
            checks[i].disabled=true;
        }

    }
    var myAjax = new Ajax.Request('marcar_pedido.php',
  {
    method: 'post',
    parameters: 'pedidod_id='+id+'&pedido_id='+pedido+'&val='+valchk,
    onComplete: function()
    {
       window.opener.listar_pedidos();
       this.location.reload();
       
    }
  }
  );
 
}

</script>

<html>
<title>Visualizar Pedido # <?php echo $pedido_nro; ?></title>

<?php cabecera_popup('../..'); ?>
<!--<div class='sub-content'>-->
<body class="fuente_por_defecto popup_background">

<center>
<h1><u>Pedido Nro. [<?php echo $pedido_nro; ?>]</u></h1>

<br />
<center>
<div class='sub-content'>
<table style='width:100%;border: 1px solid black;font-size:16px;'>
	<tr>
		<td style='text-align:right;'>Fecha Generaci&oacute;n:</td>
		<td style='text-align:left;font-weight:bold;'><?php echo substr($p['pedido_fecha'],0,16); ?></td>
	</tr>
	<tr>
		<td style='text-align:right;'>Lugar de Or&iacute;gen:</td>
		<td style='text-align:left;font-weight:bold;'><?php echo $p['bod_glosa']; echo $p['centro_nombre']; ?></td>
	</tr>
	<tr>
		<td style='text-align:right;'>Comentarios:</td>
		<td style='text-align:left;'><?php echo (str_replace("\n","<br>",$p['pedido_comentario'])); ?></td>
	</tr>
	<tr>
		<td style='text-align:right;'>Total de Art&iacute;culos:</td>
		<td style='text-align:left;'><?php echo sizeof($d); ?></td>
	</tr>
	<!--<tr>
		<td style='text-align:right;'>Plazo:</td><td><?php $conv; ?></td>
	</tr>-->
</table>
</div>
</center>
<br />
<div class='sub-content2' id='detalle' name='detalle'>
<table style='width:100%;font-size:14px;'>
	<tr class='tabla_header'>
		<td>#</td>
		<td style='width:65%;'>C&oacute;digo O.C.</td>
		<td>Cantidad</td>
		<td>Formato</td>
		<td>$Unit</td>
		<td>$Subtotal</td>
		<td>Plazo (D&iacute;as)</td>
		<td>En Tr&aacute;mite</td>
	</tr>
	
<?php 

	for($i=0;$i<sizeof($d);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		if($d[$i]['pedidod_tramite']=='t') $chk='CHECKED'; else $chk='';
		
		print("
			<tr class='$clase' style='height:40px;'>
			<td style='text-align:center;font-size:18px;'>".($i+1)."</td>
			<td style='text-align:left;font-weight:bold;'>[".($d[$i]['art_codigo'])."] ".($d[$i]['art_glosa'])."</td>
			<td style='text-align:right;font-weight:bold;'>".number_format($d[$i]['pedidod_cant']*1,0,',','.')."</td>
			<td style='text-align:left;'>".($d[$i]['forma_nombre'])."</td>
			<td style='text-align:right;'>$".number_format($d[$i]['art_val_ult']*1,2,',','.').".-</td>
			<td style='text-align:right;'>$".number_format($d[$i]['subtotal']*1,0,',','.').".-</td>
			<td style='text-align:center;'>".$d[$i]['plazo']."</td>
			<td style='text-align:center;'><input type='checkbox' name='chk_".$d[$i]['pedidod_id']."' id='chk_".$d[$i]['pedidod_id']."'
				onClick='marcar_pedido(".$d[$i]['pedidod_id'].",".$pedido_id.");' $chk><img src='../../imagenes/ajax-loader1.gif' 
                id='pedido_marcar_".$d[$i]['pedidod_id']."' style='display:none;'> </td>
			</tr>
		");
		
	}

?>	
	
	
</table>
</div>
</center>

</body>
<!--</div>-->
</html>
