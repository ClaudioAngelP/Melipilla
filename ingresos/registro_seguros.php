<?php 

	require_once('../conectar_db.php');

	$companias=explode("\n",trim(file_get_contents('companias.list')));
	$pac_id=pg_escape_string($_GET['pac_id']*1);
	
	$p=cargar_registro("SELECT * FROM pacientes LEFT JOIN prevision USING (prev_id) WHERE pac_id=$pac_id;");
	$prev_id=$p['prev_id']*1;
	$prev_desc=pg_escape_string($p['prev_desc']);
	
	$cias=array();
	
	for($i=0;$i<sizeof($companias);$i++) {
		if(trim($companias[$i])=='') continue;
		$cias[]=htmlentities($companias[$i]);
	}
	
	sort($cias);

	$ts=cargar_registros_obj("SELECT * FROM tipos_seguro ORDER BY ts_id;");

	$s=array();

	for($i=0;$i<sizeof($ts);$i++) {
		$s[$i]=array($ts[$i]['ts_id']*1,htmlentities($ts[$i]['ts_nombre']) );
	}
	
	$espec=cargar_registros_obj("SELECT * FROM especialidades ORDER BY esp_desc asc;");

	$es=array();

	for($i=0;$i<sizeof($espec);$i++) {
		$es[$i]=array($espec[$i]['esp_id']*1,htmlentities($espec[$i]['esp_desc']) );
	}
	
?>

<html>
<title>Registro &Uacute;nico de Seguros</title>

<?php cabecera_popup('..'); ?>

<style>

.cheq {
	font-size:11px;
	width:100%;
}

</style>

<script>

	seguros=new Object();
	
	lista_companias=<?php echo json_encode($cias); ?>;	
	tipos_seguros=<?php echo json_encode($s); ?>;	
	lista_especialidades=<?php echo json_encode($es); ?>;	
	
	prev_desc='<?php echo $prev_desc; ?>';
	prev_id=<?php echo $prev_id; ?>;
			
	function validacion_rut(obj) {

		obj.value=trim(obj.value);

		if( !comprobar_rut(obj.value) ) {
			obj.style.background='red';
			return false;
		} else {
			obj.style.background='yellowgreen';
			return true;	
		}

	}

	
	function init() {
	
		p=window.opener.seguros;
		
		if(p==undefined || p.item==undefined) {
		
			seguros=new Object();
			seguros.item=[];
			seguros.item[0]=new Object();
			seguros.item[0].tipo='1';
			seguros.item[0].compania=0;
			seguros.item[0].poliza='';
			seguros.item[0].rut='';
			seguros.item[0].nombre='';
			seguros.item[0].fecha='';
			seguros.item[0].serie='';
			seguros.item[0].prevision=prev_desc;
			seguros.item[0].esp_id='';
			seguros.item[0].prev_id=prev_id;
			seguros.item[0].fono='';
			$('nro_seguros').value='1';
			calcular_totales(0);
			
		} else {

			seguros=p;
			$('nro_seguros').value=p.item.length;
			calcular_totales(0);		

		}
		
	}

	
	function valin(v) {
		
		try {
			return $(v).value;
		} catch(err) {
			return 'error';
		}	
	
	}	
	
	function combo_companias(vsel) {
		
		var html=''; var sel='';
		
		for(i=0;i<lista_companias.length;i++) {
			
			if(lista_companias[i]==vsel)
				sel='SELECTED'; else sel='';

			html+='<option value="'+lista_companias[i]+'" '+sel+'>'+lista_companias[i]+'</option>';	

		}
		
		return html;
		
	}
	
	function combo_tipo(vsel) {
		
		var html='';	
	
		for(var i=0;i<tipos_seguros.length;i++) {
			var sel=(tipos_seguros[i][0]==vsel)?'SELECTED':'';
			html+='<option value="'+tipos_seguros[i][0]+'" '+sel+'>'+tipos_seguros[i][1]+'</option>';
		}	
		
		return html;
	
	}
	
	function combo_esp(vsel) {
		
		var html='';	
	
		for(var i=0;i<lista_especialidades.length;i++) {
			var sel=(lista_especialidades[i][0]==vsel)?'SELECTED':'';
			html+='<option value="'+lista_especialidades[i][0]+'" '+sel+'>'+lista_especialidades[i][1]+'</option>';
		}	
		
		return html;
	
	}

		
	function calcular_totales(refresh) {
	
		var nro_seguros=$('nro_seguros').value*1;
	
		seguros.nro_seguros=nro_seguros;
		
		var htmlseguros='<table style="width:100%;font-size:11px;"><tr class="tabla_header">';
		htmlseguros+='<td>Tipo</td>';
		htmlseguros+='<td>Compa&ntilde;ia</td>';
		htmlseguros+='<td>Nro. P&oacute;liza/Parte</td>';
		htmlseguros+='<td style="width:100px;">R.U.T.</td>';
		htmlseguros+='<td style="width:120px;">Nombre</td>';
		htmlseguros+='<td style="width:100px;">Fecha</td>';
		htmlseguros+='<td style="width:100px;">Serie/Patente</td>';
		htmlseguros+='<td style="width:100px;">Previsi&oacute;n</td>';
		htmlseguros+='<td style="width:100px;">Especialidad</td>';
		htmlseguros+='<td style="width:100px;">Tel&eacute;fono</td>';
		htmlseguros+='</tr>';	

		if(nro_seguros==0)
			seguros.item=[];
		
		for(var i=0;i<nro_seguros;i++) {
		
			var clase=(i%2==0)?'tabla_fila':'tabla_fila2';		

			if(refresh) {
				seguros.item[i]=new Object();
				seguros.item[i].tipo=valin('tipo_chq_'+i);
				seguros.item[i].compania=valin('compania_chq_'+i);
				seguros.item[i].poliza=valin('poliza_chq_'+i);
				seguros.item[i].rut=valin('rut_chq_'+i);
				seguros.item[i].nombre=valin('nombre_chq_'+i);
				seguros.item[i].fecha=valin('fecha_chq_'+i);
				seguros.item[i].serie=valin('serie_chq_'+i);
				seguros.item[i].prevision=<?php echo json_encode($prev_desc); ?>;
				seguros.item[i].esp_id=valin('tipo_chq_'+i);
				seguros.item[i].prev_id=<?php echo json_encode($prev_id); ?>;
				seguros.item[i].fono=valin('fono_chq_'+i);
			}
			//pago.cheques[i].rut=$('rut_chq_'+i).value;
					
			c=seguros.item[i];
		
			htmlseguros+='<tr class="'+clase+'">';
			htmlseguros+='<td>';
			htmlseguros+='<select id="tipo_chq_'+i+'" style="text-align:left;width:125px;font-size:11px;" ';
			htmlseguros+='name="tipo_chq_'+i+'" class="cheq">';
			htmlseguros+=combo_tipo(c.tipo);
			htmlseguros+='</select></td>';
			htmlseguros+='<td>';
			htmlseguros+='<select id="compania_chq_'+i+'" style="text-align:left;width:125px;font-size:10px;" ';
			htmlseguros+='name="compania_chq_'+i+'" class="cheq">';
			htmlseguros+=combo_companias(c.compania);
			htmlseguros+='</select></td>';
			htmlseguros+='<td>';
			htmlseguros+='<input type="text" id="poliza_chq_'+i+'" style="text-align:right;" ';
			htmlseguros+='name="poliza_chq_'+i+'" class="cheq"  value="'+c.poliza+'" />';
			htmlseguros+='</td>';
			htmlseguros+='<td>';
			htmlseguros+='<input type="text" id="rut_chq_'+i+'" style="text-align:right;" ';
			htmlseguros+='name="rut_chq_'+i+'" class="cheq" value="'+c.rut+'" onKeyUp="validacion_rut(this);">';
			htmlseguros+='</td>';
			htmlseguros+='<td>';
			htmlseguros+='<input type="text" id="nombre_chq_'+i+'" ';
			htmlseguros+='name="nombre_chq_'+i+'"  class="cheq" value="'+c.nombre+'" >';
			htmlseguros+='</td>';
			htmlseguros+='<td>';
			htmlseguros+='<input type="text" id="fecha_chq_'+i+'" style="text-align:center;" onBlur="validacion_fecha(this);" ';
			htmlseguros+='name="fecha_chq_'+i+'" class="cheq" value="'+c.fecha+'">';
			htmlseguros+='</td>';
			htmlseguros+='<td>';
			htmlseguros+='<input type="text" id="serie_chq_'+i+'" style="text-align:center;" ';
			htmlseguros+='name="serie_chq_'+i+'" class="cheq" value="'+c.serie+'">';
			htmlseguros+='</td>';
			htmlseguros+='<td>';
			htmlseguros+='<input type="text" id="prevision_'+i+'" disabled style="text-align:center;" DISABLED=TRUE';
			htmlseguros+='name="prevision_'+i+'" class="cheq" value="'+c.prevision+'">';
			htmlseguros+='</td>';
			htmlseguros+='<td>';
			htmlseguros+='<select id="especialidad_'+i+'" style="text-align:left;width:125px;font-size:11px;" ';
			htmlseguros+='name="especialidad_'+i+'" class="cheq">';
			htmlseguros+=combo_esp(c.esp_id);
			htmlseguros+='</select></td>';
			htmlseguros+='<td>';
			htmlseguros+='<input type="text" id="fono_chq_'+i+'" ';
			htmlseguros+='name="fono_chq_'+i+'"  class="cheq" value="'+c.fono+'" >';
			htmlseguros+='</td>';

			htmlseguros+='</tr>';	
			
			
		
		}
		
		htmlseguros+='</table>';
		
		$('lista_seguros').innerHTML=htmlseguros;

		for(var i=0;i<nro_seguros;i++) {
			validacion_rut($('rut_chq_'+i));
			validacion_fecha($('fecha_chq_'+i));
		}
		
		actualizar_seguros();
	
	}

	function actualizar_seguros() {
		
		var nro_seguros=$('nro_seguros').value*1;
		
		if(nro_seguros==0)
			seguros.item=[];
	
		for(var i=0;i<nro_seguros;i++) {
			seguros.item[i].tipo=valin('tipo_chq_'+i);
			seguros.item[i].compania=valin('compania_chq_'+i);
			seguros.item[i].poliza=valin('poliza_chq_'+i);
			seguros.item[i].rut=valin('rut_chq_'+i);
			seguros.item[i].nombre=valin('nombre_chq_'+i);
			seguros.item[i].fecha=valin('fecha_chq_'+i);
			seguros.item[i].serie=valin('serie_chq_'+i);
			seguros.item[i].prevision=<?php echo json_encode($prev_desc); ?>;
			seguros.item[i].esp_id=valin('especialidad_'+i);
			seguros.item[i].prev_id=<?php echo json_encode($prev_id); ?>;
			seguros.item[i].fono=valin('fono_chq_'+i);
		}	
	
	}
	
	function confirmar_seguros() {
	
			var nro_seguros=$('nro_seguros').value*1;
	
			actualizar_seguros();

			for(var i=0;i<nro_seguros;i++) {
			
				if(!validacion_rut($('rut_chq_'+i))) {
					alert('RUT ingresado no es v&aacute;lido'.unescapeHTML());
					$('rut_chq_'+i).select();
					$('rut_chq_'+i).focus();
					return;
				}
				
				if(!validacion_fecha($('fecha_chq_'+i))) {
					alert('Fecha ingresada no es v&aacute;lida'.unescapeHTML());
					$('fecha_chq_'+i).select();
					$('fecha_chq_'+i).focus();
					return;
				}
				
			}
			
			window.opener.seguros=seguros;
			window.opener.$('reg_seguros').value='Registro de Seguros ('+seguros.item.length+')';
			var fn=window.opener.redibujar_tabla.bind(window.opener);
			fn();
			window.close();
			
	}



</script>

<body class='popup_background fuente_por_defecto' onLoad='init();'>

<div class='sub-content'>
<img src='../iconos/table.png' />
<b>Registro &Uacute;nico de Seguros</b>
</div>

<form id='form_seguros' name='form_seguros' onSubmit='return false;'>


<div class='sub-content'>
<table style='width:100%;'><tr><td style='width:20px;'>
<img src='../iconos/vcard_edit.png' />
</td><td>
<b>Listado de Seguros Comprometidos</b>
</td><td style='width:150px;'>Cantidad:</td><td style='width:50px;'>
<input type='text' id='nro_seguros' 
name='nro_seguros' size=5 onKeyUp='calcular_totales(1);'
style='text-align:center;'
value='0'>
</td>
</tr></table>
</div>

<div class='sub-content2' style='height:380px;overflow:auto;' id='lista_seguros'>

</div>


<center>
<input type='button' onClick='confirmar_seguros();' id='confirmar' 
value='--- Confirmar Informaci&oacute;n de Seguros... ---'>
</center>

</body>
</html>





