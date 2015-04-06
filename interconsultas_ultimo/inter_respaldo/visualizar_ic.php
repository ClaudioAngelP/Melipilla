<?php
  require_once('../conectar_db.php');
  if($_GET['tipo']=='inter_ficha' OR $_GET['tipo']=='revisar_inter_ficha') {
  	$id=$_GET['inter_id'];
  				$datos=pg_query($conn, "
		SELECT 
		inter_folio, 
		inter_ingreso, 
		pac_rut, 
		pac_appat, 
		pac_apmat, 
		pac_nombres,
		pac_fc_nac,
		pac_direccion,
		ciud_desc,
		prov_desc,
		reg_desc,
		sex_desc,
		prev_desc,
		sang_desc,
		getn_desc,
		prof_rut, 
    prof_paterno, 
    prof_materno, 
    prof_nombres
		
		FROM interconsulta 
		
		LEFT JOIN pacientes ON inter_pac_id=pac_id
		LEFT JOIN comunas ON pacientes.ciud_id=comunas.ciud_id
		LEFT JOIN provincias ON comunas.prov_id=provincias.prov_id
		LEFT JOIN regiones ON provincias.reg_id=regiones.reg_id
		LEFT JOIN sexo ON pacientes.sex_id=sexo.sex_id
		LEFT JOIN prevision ON pacientes.prev_id=prevision.prev_id
		LEFT JOIN grupo_sanguineo ON pacientes.sang_id=grupo_sanguineo.sang_id
		LEFT JOIN grupos_etnicos ON pacientes.getn_id=grupos_etnicos.getn_id
		LEFT JOIN profesionales_externos ON prof_id=inter_prof_id
		WHERE inter_id=$id
		
		");
		
		$datos2 = pg_query("
    SELECT
    e1.esp_desc,
		inter_fundamentos,
		inter_examenes,
		inter_comentarios,
		inter_estado,
		inter_rev_med,
		inter_prioridad,
		i1.inst_nombre,
		inter_inst_id1,
		inter_motivo,
      inter_diag_cod,
		inter_diagnostico,
		COALESCE(garantia_nombre, ''),
		COALESCE(garantia_id, 0),
		i2.inst_nombre AS inst_nombre2,
		inter_inst_id2,
		inter_ingreso, ice_icono, ice_desc,
		
		unidad.esp_desc AS unidad_desc, inter_unidad
    FROM interconsulta
    LEFT JOIN especialidades AS e1 ON inter_especialidad=e1.esp_id
		LEFT JOIN instituciones AS i1 ON inter_inst_id1=inst_id
		LEFT JOIN instituciones AS i2 ON inter_inst_id2=i2.inst_id
		LEFT JOIN garantias_atencion ON inter_garantia_id=garantia_id
		LEFT JOIN interconsulta_estado ON inter_estado=ice_id		

		LEFT JOIN especialidades AS unidad ON inter_unidad=unidad.esp_id		
    WHERE inter_id=$id
		
    ");
		$inter = pg_fetch_row($datos);
		$inter2 = pg_fetch_row($datos2);
		
    $institucion=$inter2[8];

		switch($inter2[9]) {
      case 0: $inter2[9]='Confirmaci&oacute;n Diagn&oacute;stica'; break;
      case 1: $inter2[9]='Realizar Tratamiento'; break;
      case 2: $inter2[9]='Seguimiento'; break;
      default: $inter2[9]='Otro Motivo'; break;
    	}
				for($a=0;$a<count($inter);$a++) $inter[$a] = htmlentities($inter[$a]);
		if($inter[0]=='-1') $inter[0]='<i>(Sin Folio Asignado)</i>';
			
?>
		<html>
		
		<title>Ficha de Interconsulta</title>
		
<?php  cabecera_popup('..'); ?>
    
		<script>
		guardar_resolucion = function() {
	
			if($('estado').value==1 && $('esp_id').value=='') {
				alert('Debe seleccionar unidad de destino.');
				$('especialidad').focus();
				return;	
			}						
			$('inter_diag_cod').disabled=false;

			var myAjax = new Ajax.Request(			'revision_inter/sql_resolucion.php', 			{
				method: 'post', 				parameters: 'accion=guardar_resolucion&'+$('resolucion').serialize()+'&'+$('inter_diag_cod').serialize(),				onComplete: function (pedido_datos) {
	         if(trim(pedido_datos.responseText)=='OK') {
					  alert('Resoluci&oacute;n guardada exitosamente.'.unescapeHTML());						window.opener.realizar_busqueda();						window.opener.focus();						window.close();
				} else {
						alert('ERROR:\\n'+pedido_datos.responseText);
				}
				}
			}
			
			);
					
		}

		abrir_ficha = function(id) {
			inter_ficha = window.open('visualizar_ic.php?tipo=inter_ficha&inter_id='+id,			'inter_ficha_ver', 'left='+(20)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
			inter_ficha.focus();
		}

		modificar_resolucion = function(id) {
			inter_ficha = window.open('visualizar_ic.php?tipo=revisar_inter_ficha&inter_id='+id,			'_self');
		}		
		</script>
		<style>
		body {
			font-family: Arial, Helvetica, sans-serif;
			font-size: 10px;
		}

		</style>
			<body topmargin=0 leftmargin=0 rightmargin=0>
		<div class='sub-content'>
		<div class='sub-content'><img src='../iconos/script.png'> <b>Ficha de Interconsulta</b></div>
		<div class='sub-content2'>
		<center>
		
<?php
	switch($inter2[9]) {
		case 0: $motivo='Confirmaci&oacute;n Diagn&oacute;stica'; break;	
		case 1: $motivo='Realizar Tratamiento'; break;	
		case 2: $motivo='Seguimiento'; break;	
		case 3: $motivo='Control Especialidad'; break;	
		case 4: $motivo='Otro Motivo...'; break;
	}

    print("		
		<table>
		<tr><td style='text-align:right;'>Procedencia:</td><td> <b>".$inter2[7]."</b></td></tr>
		<tr><td style='text-align:right;'>Destino:</td><td> <b>".$inter2[14]."</b></td></tr>
		<tr><td style='text-align:right;'>Fecha de Ingreso:</td><td><b>".$inter2[16]."</b></td></tr>
		<tr><td style='text-align:right;'>N&uacute;mero de Folio:</td><td><b>".$inter[0]."</b></td></tr>
		</table>
		</center>
		</div>
		
		<div class='sub-content'><img src='../iconos/user_orange.png'> <b>Datos de Paciente</b></div>
		<div class='sub-content2'>
		<table>
		<tr><td style='text-align:right;'>RUT:</td>				<td><b>".$inter[2]."</b></td></tr>
		<tr><td style='text-align:right;'>Apellido Paterno:</td>
		<td><b><i>".$inter[3]."</i></b></td></tr>
		<tr><td style='text-align:right;'>Apellido Materno:</td>
		<td><b><i>".$inter[4]."</i></b></td></tr>
		<tr><td style='text-align:right;'>Nombre(s):</td>			
		<td><b><i>".$inter[5]."</i></b></td></tr>
		<tr><td style='text-align:right;'>Fecha de Nacimiento:</td>
    <td>".$inter[6]."</td></tr>
		<tr><td style='text-align:right;'>Edad:</td>
    <td id='paciente_edad'>".$inter[6]."</td></tr>
		<tr><td style='text-align:right;'>Direcci&oacute;n:</td>				
    <td>".$inter[7]."</td></tr>
		<tr><td style='text-align:right;'>Comuna:</td>				
    <td><b>".$inter[8]."</b>, ".$inter[9].", <i>".$inter[10]."</i>.- </td></tr>
		<tr><td style='text-align:right;'>Sexo:</td>				
    <td>".$inter[11]."</td></tr>
		<tr><td style='text-align:right;'>Previsi&oacute;n:</td>	
    <td>".$inter[12]."</td></tr>
		<tr><td style='text-align:right;'>Grupo Sangu&iacute;neo:</td>			
    <td><b>".$inter[13]."</b></td></tr>
		<tr><td style='text-align:right;'>Grupo &Eacute;tnico:</td>
    <td>".$inter[14]."</td></tr>
		</table>
		</div>
		
		<div class='sub-content'><img src='../iconos/chart_organisation.png'> <b>Informaci&oacute;n de Interconsulta</b></div>
		<div class='sub-content2'>
		<table>
		<tr><td style='text-align:right;'>Especialidad:</td>		
		<td width=60%><b>".$inter2[0]."</b></td></tr>
		");

	 	if($inter2[19]!='')
		print("<tr><td style='text-align:right;'>Unidad Receptora:</td>				<td width=60%><b>".htmlentities($inter2[19])."</b></td></tr>");
		
		print("
		<tr><td style='text-align:right;'>Motivo Derivaci&oacute;n:</td>		
		<td width=60%>".$motivo."</td></tr>
    	<tr><td style='text-align:right;' valign='top'>Diagn&oacute;stico (Pres.):</td>		
		<td width=60%><b>".$inter2[10]."</b><br>".htmlentities($inter2[11])."</td></tr>
    	<tr><td style='text-align:right;' valign='top'>Sospecha AUGE:</td>
    	");
    
	 $dic=cargar_registro("SELECT * FROM interconsulta WHERE inter_id=$id");
    $caso=cargar_registro("SELECT * FROM casos_auge WHERE id_sigges=".$dic['id_caso']);
    if($caso)    	print("<td><b>".htmlentities($caso['ca_patologia'])."</b></td></tr>");  	else    	print("<td>No hay sospecha.</td></tr>");
    print("<tr><td style='text-align:right;' valign='top'>Fundamentos Cl&iacute;nicos:</td>				
		<td>".$inter2[1]."</td></tr>
		");
		
		if(trim($inter2[2])!="")
		print("
		<tr><td style='text-align:right;' valign='top'>Ex&aacute;menes Comp.:</td>
		<td>".$inter2[2]."</td></tr>");
		
		if(trim($inter2[3])!="")
		print("
		<tr><td style='text-align:right;' valign='top'>Comentarios:</td>			
		<td>".$inter2[3]."</td></tr>");
		
		print("
		</table>
		</div>");
		
	if($_GET['tipo']=='inter_ficha') {
			 if($inter2[6]==0)   $inter2[6]='Sin Priorizaci&oacute;n';	 if($inter2[6]==1)   $inter2[6]='Baja';    if($inter2[6]==2)   $inter2[6]='Media';    if($inter2[6]==3)   $inter2[6]='Alta';
    if($inter2[6]==4)   $inter2[6]='Fecha Asignada';
    if($inter2[6]==5)   $inter2[6]='Documento en Auditor&iacute;a';
    
    print("
		<div class='sub-content'><img src='../iconos/user_comment.png'> <b>Datos del Profesional Solicitante</b></div>
		
    <div class='sub-content2'>
		
    <table style='width:100%;'>
		<tr><td style='text-align:right;width:100px;'>RUT:</td>
    <td style='font-weight:bold;'>".$inter[15]."</td></tr>
		<tr><td style='text-align:right'>Nombre:</td>
    <td>".($inter[16])." ".($inter[17])." ".($inter[18])."</td></tr>
		</table>
		
    </div>
    ");
    
    print("
    <div class='sub-content'><img src='../iconos/page_edit.png'> 
		<b>Resoluci&oacute;n</b></div>
		<div class='sub-content2'>
		<table style='width:100%;'>
		<tr><td style='text-align:right;' width=150>Estado Actual:</td>				
		<td>
		
		<table><tr><td><img src='../iconos/".$inter2[17].".png'></td>
		<td> <b>".htmlentities($inter2[18])."</b>
		</td></tr></table>
		
		</td></tr>
		
    ");
    
    
    if($inter2[4]==1)
    	print("
    	<tr><td style='text-align:right;' valign='top'>Prioridad:</td>
		<td>".$inter2[6]."</td></tr>
		");
		
		if(trim($inter2[5])!='') 		
		print("		
		<tr><td style='text-align:right;' valign='top'>Observaciones:</td>
		<td>".htmlentities($inter2[5])."</td></tr>
		");

		if(_cax(35))
		print("
			<tr>
			<td colspan=2>
			<center><input type='button' id='modifica' name='modifica' 
			onClick='modificar_resolucion(".$id.");' value='--- Modificar Resoluci&oacute;n... ---' /></center>		
			</td>		
			</tr>
		");

		
		print("
    
    </table>
		</div>");
		
		} else {
			
		$estado=$inter2[4]*1<=0?'1':($inter2[4]*1);			
			
		$estadohtml=desplegar_opciones_sql(
			"SELECT ice_id, ice_desc FROM interconsulta_estado 
			WHERE ice_id>=-1
			ORDER BY ice_id", $estado		
		);

		$prioridadhtml = desplegar_opciones("prioridad", 		"prior_id, prior_desc",$inter2[6],'true','ORDER BY prior_id'); 
    list($i) = cargar_registros_obj("SELECT * FROM interconsulta WHERE inter_id=$id");

    $ic = cargar_registros_obj("      SELECT * FROM interconsulta       JOIN instituciones ON inter_inst_id1=inst_id      LEFT JOIN especialidades ON inter_especialidad=esp_id
      LEFT JOIN casos_auge ON interconsulta.id_caso=casos_auge.id_sigges      LEFT JOIN garantias_atencion ON inter_garantia_id=garantia_id		LEFT JOIN interconsulta_estado ON inter_estado=ice_id		      WHERE inter_pac_id=".$i['inter_pac_id']." AND 
      (inter_estado >= 0 AND inter_estado < 2)      ORDER BY inter_ingreso DESC
    ");
    
    if($ic AND count($ic)>1) {
      print("
    	<div class='sub-content'><img src='../iconos/exclamation.png'>         <b>Interconsultas Vigentes (".(count($ic)-1).")</b></div>
        <div class='sub-content2'>        <table style='width:100%;'>
      ");
      
      for($n=0;$n<count($ic);$n++) {
        if($ic[$n]['inter_id']==$id) continue;
        if($ic[$n]['inter_folio']==-1)          $ic[$n]['inter_folio']='<i>(s/n)</i>';

        print("
        <tr class='tabla_header'>
        <td style='text-align:right;width:40%;'>Nro. Folio:</td>
        <td style='font-weight:bold;cursor:pointer;font-size:16px;' 
        onClick='abrir_ficha(".$ic[$n]['inter_id'].");'>
        <u>".$ic[$n]['inter_folio']."</u></td>
        <td style='text-align:right;width:40%;'>Estado:</td>
        <td>
        <img src='../iconos/".$ic[$n]['ice_icono'].".png' 
        alt='".htmlentities($ic[$n]['ice_desc'])."' 
        title='".htmlentities($ic[$n]['ice_desc'])."' />
        </td>
        </tr>
        <tr>        <td style='text-align:right;' class='tabla_fila2'>Fecha:</td>        <td colspan=3 class='tabla_fila'>".$ic[$n]['inter_ingreso']."</td>        </tr>        <tr>        <td style='text-align:right;' class='tabla_fila2'>Instituci&oacute;n Solicitante:</td>        <td colspan=3 class='tabla_fila'>".$ic[$n]['inst_nombre']."</td>        </tr>
        <td style='text-align:right;' class='tabla_fila2'>Especialidad:</td>        <td colspan=3 style='font-weight:bold;' class='tabla_fila'>".$ic[$n]['esp_desc']."</td>        </tr>");        
        if($ic[$n]['inter_pat_id']!=0) {			print("<tr>          <td style='text-align:right;' class='tabla_fila2'>Patolog&iacute;a AUGE:</td>          <td colspan=3>".htmlentities($ic[$n]['ca_patologia'])."</td>          </tr>");

         /* print("        
          <tr>
          <td style='text-align:right;' class='tabla_fila2'>Patolog&iacute;a AUGE:</td>
          <td colspan=3>".$ic[$n]['pat_glosa']."</td>
          </tr>");
          if($ic[$n]['rama_nombre']!='') 
            print("<tr>
            <td style='text-align:right;' class='tabla_fila2'>Rama Patolog&iacute;a AUGE:</td>
            <td colspan=3>".$ic[$n]['rama_nombre']."</td>
            </tr>        
            ");*/
        }
          
      }
      print("	        </table>        </div>      ");      
    }
	print("
		<div class='sub-content'><img src='../iconos/page_edit.png'> 		<b>Resoluci&oacute;n</b></div>		<div class='sub-content2'>		<form name='resolucion' id='resolucion' onsubmit='return false;'>		<table>		<tr><td style='text-align:right;' width=150>Estado del Caso:</td>						<td>
		<input type='hidden' name='inter_id' id='inter_id' value='".$id."'>		<input type='hidden' name='institucion' id='institucion' value='".$institucion."'>
		<select id='estado' name='estado'
		onChange='if(this.value*1==1) {
			$(\"prioridad_tr\").style.display=\"\";
			$(\"unidad_tr\").style.display=\"\";
		} else { 		
			$(\"prioridad_tr\").style.display=\"none\";
			$(\"unidad_tr\").style.display=\"none\";
		}'>
			".$estadohtml."

		</select>
		</td></tr>
		<tr id='prioridad_tr' ".($inter2[4]>1?'style="display:none;"':'')."><td style='text-align:right;' width=150>Prioridad:</td>				
		<td>
		
		<select id='prioridad' name='prioridad'>
		".$prioridadhtml."
	   </select>
		</td></tr>

		<tr id='unidad_tr' ".($inter2[4]>1?'style="display:none;"':'').">
		<td style='text-align:right;'>Unidad Receptora:</td>
		<td>
		<input type='hidden' id='esp_id' name='esp_id' value='".$inter2[20]."'>
		<input type='text' id='especialidad' 
		value='".htmlentities($inter2[19])."' name='especialidad' size=35>
		</td>
		</tr>

		<tr>
		<td style='text-align:right;'>Diag. CIE10:</td>
		<td>
		<input type='text' id='inter_diag_cod' name='inter_diag_cod' 
		value='".$inter2[10]."' DISABLED size=5 style='font-weight:bold;text-align:center;' />
		<input type='text' id='inter_diagnostico' 
		value='".htmlentities($inter2[11])."' name='inter_diagnostico' size=25>
		</td>
		</tr>
    
		<tr><td style='text-align:right;' valign='top'>Observaciones:</td>
		<td>
		<textarea cols=30 rows=3 id='observaciones' name='observaciones'>".htmlentities($inter2[5])."</textarea>
		</td></tr>
		</table>
		</div>
		<center><div class='boton'><table><tr><td>
		<img src='../iconos/accept.png'>
		</td><td>
		<a href='#' onClick='guardar_resolucion();'>Guardar Resoluci&oacute;n...</a>
		</td></tr></table></div>
		</center>
		</form>
		
		<script>
      ingreso_especialidades=function(datos_esp) {
      	$('esp_id').value=datos_esp[0];
      	$('especialidad').value=datos_esp[2];
      }

      autocompletar_especialidades = new AutoComplete(
      	'especialidad', 
      	'../autocompletar_sql.php',
      function() {
        if($('especialidad').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=subespecialidad&cadena='+encodeURIComponent($('especialidad').value)
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_especialidades);

      ingreso_diagnosticos=function(datos_diag) {
      	var cie10=datos_diag[0].charAt(0)+datos_diag[0].charAt(1)+datos_diag[0].charAt(2);
      	cie10+='.'+datos_diag[0].charAt(3);
      	
      	$('inter_diag_cod').value=cie10;
      	$('inter_diagnostico').value=datos_diag[2].unescapeHTML();
      }

      autocompletar_diagnosticos = new AutoComplete(
      	'inter_diagnostico', 
      	'../autocompletar_sql.php',
      function() {
        if($('inter_diagnostico').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=diagnostico_tapsa&cadena='+encodeURIComponent($('inter_diagnostico').value)
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_diagnosticos);
		
		</script>
		
		");
		
		
		}
		
?>
		
		</div>
		</body>
		<script>
			$('paciente_edad').innerHTML = '<i>'+window.opener.calc_edad($('paciente_edad').innerHTML)+'</i>';
		</script>
		</html>


<?php } ?>
