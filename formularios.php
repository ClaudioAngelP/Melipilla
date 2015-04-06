<?php

	// Script de despliegue de Formularios
	// Sistema de Gestión
	// Hospital San Martín de Quillota
	// =============================================================================
	// Rodrigo Carvajal J.
	// Soluciones Computacionales Viña del Mar LTDA.
	// =============================================================================
	
	
	// Formularios pedidos por el Index...
	// =================================================================================
	// Incluyen: 	Divs Propios del Formulario
	// ---------	Funciones Javascript propias del Formulario
	//				Referencias a Tablas de Información y Búsqueda del Formulario 
	
	require_once("conectar_db.php");
							
	if($_GET['form']=='recetas') {
	
	$bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1','1=1',
	'ORDER BY bod_glosa'); 
	

	printf("
	
	<script>
	
	mostrar_receta = function() {
	
		if($('tipo_receta').value==1) {
		
			receta_display='';
			
		}
		
		if($('tipo_receta').value==3) {
		
			receta_display='none';
			
		}
		
		$('fic_pac').style.display=receta_display;
		$('medico').style.display=receta_display;
		$('rut_pac').style.display=receta_display;
		$('nom_pac').style.display=receta_display;
		$('auge').style.display=receta_display;
		$('diagnos').style.display=receta_display;
		
	}
	
	abrir_busqueda = function() {
		
		buscar_win = window.open('formularios.php?form=productos_receta&'+$('bodega_origen').serialize(),
		'buscar_productos', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0');
			
		buscar_win.focus();
		
	}
	
		quitar_art = function(numero) {
		
			Element.remove('art_'+numero);
			
			articulos_sel = $('seleccion').getElementsByTagName('table');
			
			if(articulos_sel.length==0) {			
				art_num=0;
				articulos_sel=0;
				$('nosel').style.display='';
				$('seleccion').style.display='none';
			}
		
		}
		
		calcular_cantidad = function() {
		
			cant = Math.ceil(($('dosis').value)*(($('dias').value*24)/$('horas').value));
					
			if(cant>0) {
				$('cantidad').value=cant;
			} else {
				$('cantidad').value=0;
			}
			
		
		}
		
		seleccionar_articulo = function(idarticulo,cantidad) {
		
			idarticulo=idarticulo*1;
			cantidad=cantidad*1;
		
			var myAjax = new Ajax.Request(
			'mostrar.php', 
			{
				method: 'get', 
				parameters: 'tipo=sel_stock&id='+idarticulo+'&'+$('bodega_origen').serialize()+
				'&cantidad='+cantidad,
				onComplete: function (pedido_datos) {
		
					datos = eval(pedido_datos.responseText);
					
					detalle_art='';
					
					for(i=1;i<datos.length;i++) {
						detalle_art+=datos[i][0]+'='+datos[i][1];
						if(i<(datos.length-1)) { detalle_art=detalle_art+'!'; } 
					}
					
					producto='<table id=\"art_'+art_num+'\" name=\"art_'+art_num+'\" style=\"width: 410;\"><input type=\"hidden\" name=\"id_art_'+art_num+'\" value=\"'+idarticulo+'\"><input type=\"hidden\" name=\"detalle_art_'+art_num+'\" value=\"'+detalle_art+'\">';
					
					i=0;
					
					datos.each( function(dato) {
						if(i==0) { 
							clase='tabla_fila'; clase2=''; estilo='<b>'; 
							agregar='<td rowspan='+datos.length+' width=\"5%%\"><center><a href=\"#\"><img src=\"borrar.png\" onClick=\"quitar_art('+art_num+')\" border=0></a></center></td>';
						} else { 
							clase='tabla_fila2'; clase2='derecha'; estilo=''; agregar='';
						}
						
						i++;
						producto+='<tr class=\"'+clase+'\"><td class=\"'+clase2+'\" width=\"50%%\">'+estilo+dato[0]+'</td><td  width=\"15%%\" class=\"derecha\">'+estilo+dato[1]+'</td>'+agregar+'</tr>';
						
					});
					
					producto+='</table>';
					
					if(articulos_sel==0) {
						$('nosel').style.display='none';
						$('seleccion').style.display='';
						
						articulos_sel=1;
					}
					
					$('seleccion').innerHTML+=producto;
					
					art_num++;
					
					$('buscar').value='';
					
					$('buscar').focus();
				
				}
			}
			
			);
			
		}
		
		verifica_tabla = function() {
		
			if(!($('tipo_receta').value==3)) {
		
			if(($('numero_receta').value*1)==0) {
				alert('No ha ingresado n&uacute;mero de receta.'.unescapeHTML());
				return;
			}
			
			if(($('ficha_paciente').value*1)==0) {
				alert('No ha ingresado ficha de paciente.'.unescapeHTML());
				return;
			}
			
			if(($('rut_medico').value*1)==0) {
				alert('No ha especificado rut del m&eacute;dico.'.unescapeHTML());
				return;
			}
			
			if(articulos_sel==0) {
				alert('No ha seleccionado art&iacute;culos.'.unescapeHTML());
				return;
			}
			
			}
			
			var myAjax = new Ajax.Request(
			'sql.php', 
			{
				method: 'get', 
				parameters: 'accion=receta&cant='+art_num+'&'+$('receta').serialize(),
				onComplete: function (pedido_datos) {
					
					if(pedido_datos.responseText=='OK') {
					
						alert('Receta ingresada exitosamente.');
						cambiar_pagina(\"Ingreso de Recetas\",\"recetas\");
						
					} else {
					
						alert('ERROR:'+chr(13)+pedido_datos.responseText);
						
					}
				}
			}
			
			);
					
		}
	
		$('numero_receta').focus(); 
		articulos_sel=0;
		art_num=0;
	
	</script>
	
	<center>
	<form id='receta' name='receta'>
	<table>
	<tr><td valign='top'>
	
	<div class='sub-content'>
		<center><br>
		<b>Tipo de Receta:</b><br>
		<table><tr><td>
		<select name='tipo_receta' id='tipo_receta' onChange='mostrar_receta()'>
			<option value='1' selected> Receta Agudo</option>
			<option value='2'> Receta Cheque</option>
			<option value='3'> Receta Asistencia P&uacute;blica</option>
		</select>
		</td></tr></table>
		<br>
		</center>
	</div>
	
		<div class='sub-content'>
		<table>
		<tr><td class='derecha'>
		Ubicaci&oacute;n:
		</td><td>
		<select name='bodega_origen' id='bodega_origen'>
		" . ($bodegashtml) . "
		</select>
		</td></tr>
		<tr id='num_rec'><td class='derecha'>Nro. Receta:</td>
		<td><input type='text'  id='numero_receta' name='numero_receta' value=''></td></tr>
		<tr id='fic_pac'><td class='derecha'>Ficha Paciente:</td>
		<td><input type='text' id='ficha_paciente' name='ficha_paciente' value=''></td></tr>
		<tr id='medico'><td class='derecha'>M&eacute;dico:</td>
		<td><input type='text' id='rut_medico' name='rut_medico' value=''></td></tr>
		<tr id='rut_pac'><td class='derecha'>R.U.T. Paciente:</td><td></td></tr>
		<tr id='nom_pac'><td class='derecha'>Nombre Paciente:</td><td></td></tr>
		<tr id='auge'><td class='derecha'>Auge:</td><td></td></tr>
		<tr id='diagnos'><td class='derecha'>Diagn&oacute;stico:</td>
		<td><textarea id='diagnostico' name='diagnostico'></textarea></td></tr>
		</table>
		</div>
			
	</td><td valign='top'>
		
	<div style='width: 450px;'>
	
	<center>
	<div class='boton'>
		<table><tr><td>
		<img src='informes.png'>
		</td><td>
		<a href='#' onClick='abrir_busqueda();'>Agregar Productos...</a>
		</td></tr></table>
	</div>
	</center>
	
	</div>
	
		<div class='sub-content' height='400' style='width:450px;'>
			<div class='sub-content'><b>Selecci&oacute;n de Productos</b></div>
			<div id='nosel'>
				(No se han seleccionado productos para la receta...)
			</div>
			<div id='seleccion' name='seleccion' class='sub-content2' style='display: none;'>
			</div>
		</div>
	
	</td></tr></table>
	
	<table><tr><td>
		<div class='boton'>
		<table><tr><td>
		<img src='guardar.png'>
		</td><td>
		<a href='#' onClick='verifica_tabla();'>Ingresar Receta...</a>
		</td></tr></table>
		</div>
		</td><td>
		<div class='boton'>
		<table><tr><td>
		<img src='resetear.png'>
		</td><td>
		<a href='#' onClick='cambiar_pagina(\"Ingreso de Recetas\",\"recetas\");'>
		Limpiar Formulario...</a>
		</td></tr></table>
		</div>
		
	</td></tr></table><br>
	</form>
	
	</center>
	
	");
	
	}
	
	if($_GET['form']=='stock') {
	
	$bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'0','1=1',
	'ORDER BY bod_glosa'); 
	
	
	printf("
	
	<center>
	<br>
	<table>
	<tr class='tabla_header'><td colspan=2><b>Generar contabilizando desde:</b></td></tr>
	<tr><td style='text-align: right;'>Ubicaci&oacute;n de Stock:
	</td><td>
	<select name='bodega'>
	<option value=0 selected>(Global...)</option>
	" . $bodegashtml . "
	</select>
	</td></tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	<tr class='tabla_header'><td colspan=2><b>Inclu&iacute;r los siguientes campos:</b></td></tr>
	<tr><td style='text-align: right;'><input type='checkbox' name='codigo'></td><td>C&oacute;digo</td></tr>
	<tr><td style='text-align: right;'><input type='checkbox' name='codigo'></td><td>Nombre</td></tr>
	<tr><td style='text-align: right;'><input type='checkbox' name='codigo'></td><td>Forma Farmac&eacute;utica</td></tr>
	<tr><td style='text-align: right;'><input type='checkbox' name='codigo'></td><td>Stock de Pedido</td></tr>
	<tr><td style='text-align: right;'><input type='checkbox' name='codigo'></td><td>Stock de Cr&iacute;tico</td></tr>
	<tr><td style='text-align: right;'><input type='checkbox' name='codigo'></td><td>Stock Actual</td></tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	<tr class='tabla_header'><td colspan=2><b>Cumpliendo con las sgtes. condiciones:</b></td></tr>
	<tr><td colspan=2><center>
	<select name='condicion'>
	<option value=0 selected>(Ninguna...)</option>
	<option value=1 >Stock &lt; Stock Pedido</option>
	<option value=2 >Stock &lt; Stock Cr&iacute;tico</option>
	</select>
	</center></td></tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	<tr class='tabla_header'><td colspan=2><b>En el sgte. Formato:</b></td></tr>
	<tr><td colspan=2><center><select name='formato'>
	<option value=0 selected>P&aacute;gina Web</option>
	<option value=1>Planilla .xls (MS Excel)</option>
	<option value=2>Documento PDF (Adobe Acrobat)</option>
	</select></center></td></tr>
	</table>
	<br>
	</center>
	
	");
	
	}
	
	if($_GET['form']=='productos_traslado' or $_GET['form']=='productos_receta') {
	
	$bodega_origen = $_GET['bodega_origen'];
	
	printf("
	<html>
	
	<title>Busqueda de Productos para Traslado</title>
	
	<script src='prototype.js' type='text/javascript'></script>
	
	<link rel='stylesheet' href='estilos.css' type='text/css'>	
	
	<style>

	body {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 10px;
	}

	</style>
	
	<script>
	
		realizar_busqueda = function(pagina,orden,orienta) {
			
			if($('buscar').value.length<2) {
				return;
			}
		
			var myAjax = new Ajax.Updater(
			'busqueda', 
			'registro.php', 
			{
				method: 'get', 
				evalScripts: true,
				parameters: 'tipo=busca_prod&'+$('buscar').serialize()+'&'+$('bodega_origen').serialize()+'&pagina='+pagina+'&orden='+orden+'&orienta='+orienta
			}
			
			);
		
		}
		
		abrir_producto = function(idarticulo, foco) {
		
			var myAjax = new Ajax.Updater(
			'busqueda', 
			'mostrar.php', 
			{
				method: 'get', 
				parameters: 'tipo=stock&id='+idarticulo+'&'+$('bodega_origen').serialize(),
				onComplete: function() {
					if(foco==1) $('cantidad').focus();
				}
			}
			
			);
		}
		
		sel_art = function(idprod,cantprod) {

				window.opener.seleccionar_articulo(idprod,cantprod,0);
				
		}

	</script>
	
	<body 
	onLoad='
		$(\"buscar\").focus();
	' topmargin=0 leftmargin=0 rightmargin=0
	style='background-color: #ddd;'>
	<div class='sub-content'>
	<input type='hidden' name='bodega_origen' id='bodega_origen' value='".$bodega_origen."'>
	<div id='articulos' class='sub-content'>
	<center>
	<table><tr><td><img src='lupa.png' border=0></td><td>
	Buscar Art&iacute;culos:
	</td><td>
	<input type='text' id='buscar' name='buscar' onKeyUp='
	realizar_busqueda(0,\"art_glosa\", 0);
	' size=40>
	</td><td>
	</td></tr></table>
	</center>
	</div>
	
	<div id='busqueda' class='sub-content2' style='
	min-height:300px;
  	height:auto !important;
  	height:300px;
  	'>
		<center>
		(No se ha realizado a&uacute;n una b&uacute;squeda...)
		</center>
	</div>
	
	</div>
	</body>
	
	");
	
	}
	
	if($_GET['form']=='ingreso_inter') {
	
	$sexohtml = desplegar_opciones("sexo", 
	"sex_id, sex_desc",'','true','ORDER BY sex_id'); 
	
	$previsionhtml = desplegar_opciones("prevision", 
	"prev_id, prev_desc",'','true','ORDER BY prev_id'); 
	
	$sangrehtml = desplegar_opciones("grupo_sanguineo", 
	"sang_id, sang_desc",'','true','ORDER BY sang_id'); 
	
	$grupohtml = desplegar_opciones("grupos_etnicos", 
	"getn_id, getn_desc",'','true','ORDER BY getn_id'); 
	
	$especialidadhtml = desplegar_opciones("especialidades", 
	"esp_id, esp_desc",'','true','ORDER BY esp_id'); 
	
	$institucionhtml = desplegar_opciones("institucion_solicita", 
	"instsol_id, instsol_desc",'','true','ORDER BY instsol_desc');
  
  $comunahtml = desplegar_opciones("comunas", 
	"ciud_id, ciud_desc",'','true','ORDER BY ciud_desc');
  
  
  	print("
		
		<script>
		
		calcular_edad = function() {
		
			if(trim($('paciente_fecha').value)=='') {
				$('paciente_fecha').style.background='';
				return;
			}
		
			
			if(isDate($('paciente_fecha').value)) {
				$('mostrar_edad').innerHTML='<i>'+calc_edad($('paciente_fecha').value)+'</i>';
				$('paciente_fecha').style.background='inherit';
			} else {
				alert('Fecha de Nacimiento Incorrecta.');
        		$('paciente_fecha').style.background='red';
			}
			
		}
		
		buscar_paciente = function() {
    
      var myAjax = new Ajax.Request(
			'registro.php', 
			{
				method: 'get', 
				parameters: 'tipo=paciente&'+$('paciente_rut').serialize(),
				onComplete: function (pedido_datos) {
				
				  if(pedido_datos.responseText=='') {
					
						$('paciente_nombre').disabled=false;
						$('paciente_paterno').disabled=false;
						$('paciente_materno').disabled=false;
						$('paciente_dire').disabled=false;
						$('paciente_comuna').disabled=false;
						$('paciente_fecha').disabled=false;
						$('paciente_sexo').disabled=false;
						$('paciente_prevision').disabled=false;
						$('paciente_sangre').disabled=false;
						$('paciente_grupo').disabled=false;
						
						$('paciente_fecha').style.background='';
				    $('mostrar_edad').innerHTML='';
				    
						$('paciente_id').value=0;
						$('paciente_nombre').value='';
						$('paciente_paterno').value='';
						$('paciente_materno').value='';
						$('paciente_dire').value='';
						$('paciente_comuna').value=-1;
						$('paciente_fecha').value='';
						$('paciente_sexo').value='';
						$('paciente_prevision').value=0;
						$('paciente_sangre').value=-1;
						$('paciente_grupo').value=0;
						
						
						$('paciente_nombre').focus();
						
					} else {
					
					  	datosxxx = eval(trim(pedido_datos.responseText)); 
					
					  $('paciente_id').value=datosxxx[0].unescapeHTML();
						$('paciente_nombre').value=datosxxx[2].unescapeHTML();
						$('paciente_paterno').value=datosxxx[3].unescapeHTML();
						$('paciente_materno').value=datosxxx[4].unescapeHTML();
						$('paciente_dire').value=datosxxx[11].unescapeHTML();
						$('paciente_comuna').value=datosxxx[12];
						$('paciente_fecha').value=datosxxx[5];
						$('paciente_sexo').value=datosxxx[6];
						$('paciente_prevision').value=datosxxx[7];
						$('paciente_sangre').value=datosxxx[10];
						$('paciente_grupo').value=datosxxx[9];
						
						calcular_edad();
						
						$('paciente_nombre').disabled=true;
						$('paciente_paterno').disabled=true;
						$('paciente_materno').disabled=true;
						$('paciente_dire').disabled=true;
						$('paciente_comuna').disabled=true;
						$('paciente_fecha').disabled=true;
						$('paciente_sexo').disabled=true;
						$('paciente_prevision').disabled=true;
						$('paciente_sangre').disabled=true;
						$('paciente_grupo').disabled=true;
						
						$('inter_especialidad').focus();
						
					}
				}
			}
			
			);

    
    }
		
		verificar_rut = function() {
    
      if(comprobar_rut($('paciente_rut').value)) {
        $('paciente_rut').style.background='inherit';
        buscar_paciente();
      } else {
        alert('Rut Incorrecto.');
        $('paciente_rut').style.background='red';
      }
      
    }
		
		verifica_tabla = function() {
		
			if(($('nro_folio').value*1)==0) {
				alert('N&uacute;mero de Folio incorrecto.'.unescapeHTML());
				return;
			}
			
			if(trim($('paciente_rut').value)=='') {
				alert('RUT del Paciente incorrecto.'.unescapeHTML());
				return;
			}
			
			if(trim($('paciente_nombre').value)=='') {
				alert('Nombre del Paciente est&aacute; vac&iacute;o.'.unescapeHTML());
				return;
			}
			
			if(trim($('paciente_paterno').value)=='') {
				alert('Paterno del Paciente est&aacute; vac&iacute;o.'.unescapeHTML());
				return;
			}
			
			if(trim($('paciente_materno').value)=='') {
				alert('Materno del Paciente est&aacute; vac&iacute;o.'.unescapeHTML());
				return;
			}
			
			if(trim($('paciente_fecha').value)=='') {
				alert('Fecha de Nacimiento del Paciente est&aacute; vac&iacute;o.'.unescapeHTML());
				return;
			}
			
			if(trim($('paciente_dire').value)=='') {
				alert('No ha ingresado direcci&oacute;n.'.unescapeHTML());
				return;
			}
			
      if($('paciente_comuna').value==-1) {
				alert('No ha seleccionado Comuna de Or&iacute;gen.'.unescapeHTML());
				return;
			}
			
			if(trim($('inter_funda').value)=='') {
				alert('Fundamento Cl&iacute;nico de la Interconsulta est&aacute; vac&iacute;o.'.unescapeHTML());
				return;
			}
		
			var myAjax = new Ajax.Request(
			'sql.php', 
			{
				method: 'get', 
				parameters: 'accion=interconsulta&'+$('interconsulta').serialize(),
				onComplete: function (pedido_datos) {
				
				  if(pedido_datos.responseText=='OK') {
					
						alert('Interconsulta ingresada exitosamente.');
						cambiar_pagina(\"Ingresar Interconsultas\",\"ingreso_inter\");
						
					} else {
					
						alert('ERROR:\\n'+pedido_datos.responseText.unescapeHTML());
						
					}
				}
			}
			
			);
		
		}
		
		$('nro_folio').focus();
		
		</script>
		
		<table width=650>
		<tr><td>
		<form name='interconsulta' id='interconsulta'>
		<div class='sub-content'>
		
		<div align='right'>
		<table width=630><tr>
		<td style='text-align: right;'>Instituci&oacute;n Solicitante:</td>
		<td width=55% style='text-align: left;'>
		<select id='institucion' name='institucion'>
		".$institucionhtml."
		</select>
		<td style='text-align: right;'><b>N&uacute;mero Folio:</b></td>
		<td><input type='text' name='nro_folio' id='nro_folio' size=8
		style='text-align: right;'></td></tr></td>
		</table>
		</div>
		
		</div>
		
		<div class='sub-content'>
		
		<div class='sub-content'><img src='iconos/user_red.png'> <b>Datos del Paciente</b></div>
		
		<div class='sub-content2' id='datos_paciente'>
		
		<center>
		<input type='hidden' id='paciente_id' name='paciente_id' value=0>
		<table>
<tr>
<td style='text-align:center;'>RUT</td>
<td style='text-align:center;'>Nombre(s)</td>
<td style='text-align:center;'>Apellido Paterno</td>
<td style='text-align:center;'>Apellido Materno</td>
</tr>
<tr>
<td><input type='text' id='paciente_rut' name='paciente_rut' size='12'
style='text-align: center;' onBlur='
this.value=this.value.toUpperCase();
verificar_rut();
'></td>
<td><input type='text' id='paciente_nombre' name='paciente_nombre' size='22'></td>
<td><input type='text' id='paciente_paterno' name='paciente_paterno' size='22'></td>
<td><input type='text' id='paciente_materno' name='paciente_materno' size='22'></td>
</tr>
</table>
<table>
<tr style='text-align: center;'>
<td colspan=3>Direcci&oacute;n:</td>
<td>Comuna:</td>
</tr>
<tr>
<td colspan=3>
<input type='text' name='paciente_dire' id='paciente_dire' size=64>
</td>
<td>
<select name='paciente_comuna' id='paciente_comuna'>
<option value=-1>(Seleccionar Comuna...)</option>
".$comunahtml."
</select>
</td>
</tr>

</table>
<center>
<table>
<tr>
<td style='text-align: right;'>Fecha de Nacimiento:</td>
<td><input type='text' id='paciente_fecha' name='paciente_fecha' size='12'
style='text-align: center;' onBlur='calcular_edad();'></td>
<td style='text-align: right;'>Edad:</td><td width=200 id='mostrar_edad'></td></tr>
<tr>
<td valign='top' style='text-align: right;' rowspan=3>Sexo:</td>
<td rowspan=3 valign='top'>
<select id='paciente_sexo' name='paciente_sexo' size=3>
".$sexohtml."
</select>
</td>
<td style='text-align: right;'>Previsi&oacute;n:</td>
<td>
<select id='paciente_prevision' name='paciente_prevision'>
".$previsionhtml."
</select>
</td>
</tr>
<tr>
<td style='text-align: right;'>Grupo Sangu&iacute;neo:</td><td>
<select id='paciente_sangre' name='paciente_sangre'>
<option value=-1>(Seleccionar...)</option>
".$sangrehtml."
</select>
</td></tr>
<tr>
<td style='text-align: right;'>Grupo &Eacute;tnico:</td><td>
<select id='paciente_grupo' name='paciente_grupo'>
".$grupohtml."
</select>
</td></tr>

</table>

</center>

</center>

		</div>
		
		</div>
		
		<!------ Datos de INTERCONSULTA ----->
		
		<div class='sub-content'>
		
		<div class='sub-content'><img src='iconos/chart_organisation.png'> <b>Datos de Interconsulta</b></div>
		
		<div class='sub-content2'>
		<center>
<table>
<tr>
<td style='text-align: right;'>Especialidad Cl&iacute;nica:</td>
<td>
<select id='inter_especialidad' name='inter_especialidad'>
".$especialidadhtml."
</select>
</td></tr>
<td valign='top' style='text-align: right;'>Fundamentos Cl&iacute;nicos:</td>
<td><textarea cols=50 rows=6 id='inter_funda' name='inter_funda'></textarea></td></tr>
<td valign='top' style='text-align: right;'>Ex&aacute;menes Complementarios:</td>
<td><textarea cols=50 rows=6 id='inter_examen' name='inter_examen'></textarea></td></tr>
<td valign='top' style='text-align: right;'>Comentarios:</td>
<td><textarea cols=50 rows=6 id='inter_comenta' name='inter_comenta'></textarea></td></tr>

</table>
		</center>
		</div>
		
		</div>
		
<div class='sub-content'>
<center>
	<table><tr><td>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/accept.png'>
		</td><td>
		<a href='#' onClick='verifica_tabla();'>Ingresar Interconsulta...</a>
		</td></tr></table>
		</div>
		</td><td>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/delete.png'>
		</td><td>
		<a href='#' onClick='cambiar_pagina(\"Ingresar Interconsultas\",\"ingreso_inter\");'>
		Limpiar Formulario...</a>
		</td></tr></table>
		</div>
	</td></tr></table>
</center>
</div>
		</form>
		</td></tr></table>
		

		
		");
	
	}
	
	if($_GET['form']=='estado_inter') {
	
		$institucionhtml = desplegar_opciones("institucion_solicita", 
		"instsol_id, instsol_desc",'','true','ORDER BY instsol_desc'); 

		print("
		
		<script>
		
		realizar_busqueda = function() {
		
			var myAjax = new Ajax.Updater(
			'resultado', 
			'mostrar.php', 
			{
				method: 'get', 
				parameters: 'tipo=estado_interconsultas&'+$('busqueda').serialize()
			}
			
			);
		
		}
		
		abrir_ficha = function(folio) {
		
			inter_ficha = window.open('mostrar.php?tipo=inter_ficha&nro_folio='+folio+'&'+$('institucion').serialize(),
			'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
			
			inter_ficha.focus();
		
		}
		
		$('buscar').focus();
		
		</script>
		
		<table width=650>
		<tr><td>
		
		<div class='sub-content'>
		<div class='sub-content'>
		<img src='iconos/chart_organisation.png'> <b>Listado de Interconsultas</b>
		</div>
		<div class='sub-content'>
		<form name='busqueda' id='busqueda'
		onChange='
		realizar_busqueda();
		'>
		<table>
		<tr><td>Instituci&oacute;n Solicitante:
		</td><td>
		<select id='institucion' name='institucion'>
		".$institucionhtml."
		</select>
		</td></tr>
		<tr><td style='text-align: right;'>Buscar:
		</td><td>
		<input type='text' name='buscar' id='buscar' size=60>
		</td></tr>
		<tr><td style='text-align: right;'>Ordenar por:
		</td><td>
		<select id='orden' name='orden'>
		<option value=4 SELECTED>N&uacute;mero de Folio</option>
		<option value=0>Fecha Ingreso</option>
		<option value=1>Rut</option>
		<option value=2>Paterno - Materno - Nombre(s)</option>
		<option value=3>Especialidad</option>
		</select>
		<input type='checkbox' name='ascendente' id='ascendente' CHECKED> Ascendente
		</td></tr>
		</table>
		</form>
		</div>
		
		<div class='sub-content2' id='resultado' 
		style='min-height:250px;
  		height:auto !important;
  		height:250px;'>
		<center>(No se ha efectuado una b&uacute;squeda...)</center>
		</div>
	
		</div>
		
		</div>
		
		
		</td>
		</table>
		
		<script>
		realizar_busqueda();
		</script>
		");
	
	}

	if($_GET['form']=='revision_inter') {
	
		$especialidadhtml = desplegar_opciones("especialidades", 
		"esp_id, esp_desc",'','true','ORDER BY esp_id'); 
	
		print("
		
		<script>
		
		realizar_busqueda = function() {
		
			var myAjax = new Ajax.Updater(
			'resultado', 
			'mostrar.php', 
			{
				method: 'get', 
				parameters: 'tipo=revisar_interconsultas&'+$('busqueda').serialize()
			}
			
			);
		
		}
		
		abrir_ficha = function(folio, inst) {
		
			inter_ficha = window.open('mostrar.php?tipo=revisar_inter_ficha&nro_folio='+folio+'&institucion='+inst,
			'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
			
			inter_ficha.focus();
		
		}
		
		</script>
		
		<table width=650>
		<tr><td>
		
		<div class='sub-content'>
		<div class='sub-content'>
		<img src='iconos/chart_organisation.png'> <b>Listado de Interconsultas</b>
		</div>
		<div class='sub-content'>
		<form name='busqueda' id='busqueda'
		onChange='
		realizar_busqueda();
		'>
		<table>
		<tr><td>Especialidad Cl&iacute;nica:
		</td><td>
		<select id='especialidad' name='especialidad'>
		".$especialidadhtml."
		</select>
		</td></tr>
		</table>
		</form>
		</div>
		
		<div class='sub-content2' id='resultado' 
		style='min-height:250px;
  		height:auto !important;
  		height:250px;'>
		<center>(No se ha efectuado una b&uacute;squeda...)</center>
		</div>
	
		</div>
		
		</div>
		
		
		</td>
		</table>
		
		<script>
		realizar_busqueda();
		</script>
		");
	
	}
   
?>
