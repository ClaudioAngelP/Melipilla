<?php 

	require_once('../../conectar_db.php');
	
	$cert_id=$_GET['cert_id']*1;
	
	$c=cargar_registro("SELECT * FROM item_presupuestario_certificados
								WHERE cert_id=$cert_id;", true);
	
	$d=cargar_registros_obj("SELECT *, (0) AS certd_disponible FROM item_presupuestario_certificados_detalle
								LEFT JOIN item_presupuestario_sigfe ON certd_item=item_codigo
								WHERE cert_id=$cert_id
								ORDER BY certd_item;", true);
								
	if(!$d)
		$d=Array();
	
?>

<html>
<title>Generar Certificado de Compromiso Presupuestario</title>

<?php cabecera_popup('../..'); ?>

<script>

	var detalle=<?php echo json_encode($d); ?>;

	function agregar_item() {

		if(trim($('monto').value)=='') {
			alert('El item especificado no es v&aacute;lido.'.unescapeHTML());
			return;			
		}
		
		if($('monto').value*1==0) {
			alert('El monto no es v&aacute;lido.'.unescapeHTML());
			return;			
		}
		
		var num=detalle.length;
		
		detalle[num]=new Object();
		detalle[num].certd_id=0;
		detalle[num].certd_item=trim($('item').value);
		detalle[num].item_nombre=$('nombre').value;
		detalle[num].certd_disponible=0;
		detalle[num].certd_monto=$('monto').value*1;

		$('item').value='';
		$('nombre').value='';
		$('monto').value='0';
		$('item').focus();
		
		dibujar_tabla();		
		
	}
	
	function eliminar_item(i) {
		
      detalle = detalle.without(detalle[i]);
      dibujar_tabla();
		
		
	}

	function dibujar_tabla() {
		
		var html='<table style="width:100%;">'+
					  '<tr class="tabla_header">'+
						  '<td style="width:15%;">C&oacute;digo</td>'+
						  '<td style="width:35%;">Glosa</td>'+
						  '<td>Disponible</td>'+
						  '<td style="width:15%;">Monto</td>'+
						  '<td style="width:5%;">Acci&oacute;n</td>'+
					  '</tr>';
				  
		var monto_total=0;		  
				  
		for(var i=0;i<detalle.length;i++) {
			
			clase=(i%2==0)?'tabla_fila':'tabla_fila2';
			
			html+='<tr class="'+clase+'">';
			html+='<td style="text-align:right;font-weight:bold;">'+detalle[i].certd_item+'</td>';
			html+='<td style="text-align:left;font-size:10px;">'+detalle[i].item_nombre+'</td>';
			html+='<td style="text-align:right;">$'+number_format(detalle[i].certd_disponible,0,',','.')+'.-</td>';
			html+='<td style="text-align:right;">$'+number_format(detalle[i].certd_monto,0,',','.')+'.-</td>';
			html+='<td><center><img src="../../iconos/delete.png" style="cursor:pointer;" onClick="eliminar_item('+i+');" /></center></td>';
			html+='</tr>';
			
			monto_total+=(detalle[i].certd_monto*1);
			
		}

		html+='<tr class="tabla_header">';
		html+='<td style="text-align:right;font-weight:bold;" colspan=3>Total:</td>';
		html+='<td style="text-align:right;font-weight:bold;">$'+number_format(monto_total,0,',','.')+'.-</td>';
		html+='<td>&nbsp;</td></tr>';
		
		html+='</table>';
		
		$('cert_monto').value='$ '+number_format(monto_total,0,',','.')+'.-';
		$('detalle_cert').innerHTML=html;
		
	}

	function guardar_certificado() {

		$('detalle').value=detalle.toJSON();

		if($('cert_descripcion').value=='') {
			alert('Debe ingresar una descripci&oacute;n para este certificado.'.unescapeHTML());
			return;
		}
		
		if($('cert_monto').value*1==0) {
			alert('El detalle de items y montos no es v&aacute;lido.'.unescapeHTML());
			return;
		}
		
		var myAjax = new Ajax.Request(
			'sql_cert.php',
			{
				method:'post',
				parameters:$('cert').serialize(),
				onComplete:function(r) {
					window.open('cert_pdf.php?cert_id='+trim(r.responseText),'_self');
				}
			}
		);
		
	}
	
	function abrir_cert(cert_id) {
		
		window.open('cert_pdf.php?cert_id='+cert_id,'_self');
		
	}

</script>

<body class='popup_background fuente_por_defecto'>

<div class='sub-content'>
<img src='../../iconos/money.png'> <b>Certificado de Compromiso Presupuestario</b>
</div>

<form id='cert' name='' action='sql_cert.php' method='post'>

<input type='hidden' id='cert_id' name='cert_id' value='<?php echo $cert_id; ?>' />
<input type='hidden' id='detalle' name='detalle' value='' />

<table style='width:100%;'>
	<tr>
		<td style='text-align:right;' class='tabla_fila2'>Descripci&oacute;n:</td>
		<td class='tabla_fila'><input type='text' id='cert_descripcion' name='cert_descripcion' value='<?php echo $c['cert_descripcion']; ?>' size=35 /></td>
	</tr>

	<tr>
		<td style='text-align:right;' class='tabla_fila2'>Orden de Compra:</td>
		<td class='tabla_fila'><input type='text' id='cert_orden_compra' name='cert_orden_compra' value='<?php echo $c['cert_orden_compra']; ?>' size=15 /></td>
	</tr>
	<tr>
		<td style='text-align:right;' class='tabla_fila2'>C&oacute;digo SIGFE:</td>
		<td class='tabla_fila'><input type='text' id='cert_cod_sigfe' name='cert_cod_sigfe' value='<?php echo $c['cert_cod_sigfe']; ?>' size=10 /></td>
	</tr>


	<tr>
		<td style='text-align:right;' class='tabla_fila2' valign='top'>Observaciones:</td>
		<td class='tabla_fila'><textarea id='cert_observaciones' name='cert_observaciones' style='width:100%;height:50px;'><?php echo $c['cert_observaciones']; ?></textarea></td>
	</tr>
	<tr>
		<td style='text-align:right;' class='tabla_fila2'>Monto $:</td>
		<td class='tabla_fila'><input type='text' id='cert_monto' name='cert_monto' style='text-align:right;' value='$ 0.-' DISABLED /></td>
	</tr>

	<tr style='display:none;'>
		<td style='text-align:right;' class='tabla_fila2'>Resoluci&oacute;n Exenta:</td>
		<td class='tabla_fila'><input type='text' id='cert_resolucion' name='cert_resolucion' value='<?php echo $c['cert_resolucion']; ?>' size=15 />
		<input type='button' id='' name='' value='[[ Generar Folio ]]' onClick='generar_resolucion();' /> 
		</td>
	</tr>
	
	<tr style='display:none;'>
		<td style='text-align:right;' class='tabla_fila2' valign='top'>Vistos:</td>
		<td class='tabla_fila'><textarea id='cert_vistos' name='cert_vistos' style='width:100%;height:80px;'><?php echo $c['cert_visto']; ?></textarea></td>
	</tr>
	
	<tr style='display:none;'>
		<td style='text-align:right;' class='tabla_fila2' valign='top'>Considerando:</td>
		<td class='tabla_fila'><textarea id='cert_considerando' name='cert_considerando' style='width:100%;height:80px;'><?php echo $c['cert_considerando']; ?></textarea></td>
	</tr>


</table>

<div class='sub-content'>
<img src='../../iconos/coins.png'> <b>Detalle</b>
</div>
<div class='sub-content'>
<table style='width:100%;'>
<tr>	
<td style='width:5%;'><img src='../../iconos/add.png'></td>
<td style='width:20%;'><input type='text' id='item' name='item' style='width:100%;' value='' /></td>
<td style='width:40%;'><input type='text' id='nombre' name='nombre' style='width:100%;' value='' DISABLED /></td>
<td style='width:20%;'><input type='text' id='monto' name='monto' style='width:100%;text-align:right;' value='0' onFocus='this.select();' /></td>
<td style='width:15%;'><center><input type='button' id='' name='' value='[Agregar...]' onMouseUp='agregar_item();' onKeyUp='agregar_item();' /></center></td>
</tr>
</table>
</div>

<div class='sub-content2' style='height:180px;overflow:auto;' id='detalle_cert'>

</div>


<center><input type='button' id='' name='' onClick='guardar_certificado();' value='-- Guardar Certificado... --' /></center>

</form>
</body>

<script>


	seleccionar_item=function(r) {
		
		$('item').value=r[0];
		$('nombre').value=r[1].unescapeHTML();
		$('monto').focus();
		
	}

	autocompletar_items = new AutoComplete(
      'item', 
      'autocompletar_items.php',
      function() {
        if($('item').value.length<3) return false;
      
        return {
          method: 'get',
          parameters: 'cadena='+encodeURIComponent($('item').value)
        }
      }, 'autocomplete', 350, 200, 150, 0, 2, seleccionar_item);


dibujar_tabla();

</script>


</html>

