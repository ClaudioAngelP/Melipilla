<?php



  require_once('../conectar_db.php');



  if($_GET['tipo']=='inter_ficha' OR $_GET['tipo']=='revisar_inter_ficha') {

  

  	$id=$_GET['inter_id']*1;

    list($dato_i) = cargar_registros_obj("SELECT * FROM interconsulta WHERE inter_id=$id");
    
    $pac_id=$dato_i['inter_pac_id'];
  		

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

    prof_nombres, sector_nombre, pac_fono

		

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

    

    esp_desc,

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

		diag_desc,

		COALESCE(garantia_nombre, pat_glosa),

		COALESCE(garantia_id, pat_id),

		i2.inst_nombre AS inst_nombre2,

		inter_inst_id2,

		inter_ingreso

		

    FROM interconsulta

    

    LEFT JOIN especialidades ON inter_especialidad=esp_id

		LEFT JOIN instituciones AS i1 ON inter_inst_id1=inst_id

		LEFT JOIN instituciones AS i2 ON inter_inst_id2=i2.inst_id

		LEFT JOIN diagnosticos ON inter_diag_cod=diag_cod

		LEFT JOIN garantias_atencion ON inter_garantia_id=garantia_id

		LEFT JOIN patologias_auge ON inter_pat_id=pat_id

		

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

		

		switch($inter2[4]) {

    				case 0: $imagen = '../iconos/time.png'; 

    						$texto='En espera...'; break;

    				case 1: $imagen = '../iconos/tick.png'; 

    						$texto='Aceptado'; break;

            case 2: $imagen = '../iconos/cross.png'; 

    						$texto='Rechazado'; break;

    				case 3: $imagen = '../iconos/arrow_refresh.png';

    						$texto='Atendido y en Control'; break;

    				case 4: $imagen = '../iconos/user_go.png';

    						$texto='Derivado...'; break;

    				case 5: $imagen = '../iconos/user_go.png'; 

    						$texto='Atendido y Retornado'; break;

    				case 6: $imagen = '../iconos/user_add.png'; 

    						$texto='Atendido y Permanente'; break;

            case 10: $imagen = '../iconos/user_delete.png'; 

    						$texto='Paciente Rechaza Tratamiento'; break;

		}

		

		if($inter[0]=='-1') $inter[0]='<i>(Sin Folio Asignado)</i>';
		if($inter[0]=='-2') $inter[0]='<i>(Dcto. Interno)</i>';

			

?>

		<html>

		

		<title>Ficha de Interconsulta</title>

		

<?php  cabecera_popup('..'); ?>

    

		<script>

		

		guardar_resolucion = function() {

		

			var myAjax = new Ajax.Request(

			'revision_inter/sql_resolucion.php', 

			{

				method: 'get', 

				parameters: 'accion=guardar_resolucion&'+$('resolucion').serialize(),

				onComplete: function (pedido_datos) {

					

          if(pedido_datos.responseText=='OK') {

						

					  alert('Resoluci&oacute;n guardada exitosamente.'.unescapeHTML());

						window.opener.realizar_busqueda();

						window.opener.focus();

						window.close();

												

					} else {

					

						alert('ERROR:\\n'+pedido_datos.responseText);

						

					}

				}

			}

			

			);

					

		}



		abrir_ficha = function(id) {

			inter_ficha = window.open('visualizar_ic.php?tipo=inter_ficha&inter_id='+id,
			'inter_ficha_ver', 'left='+(20)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');

			inter_ficha.focus();

		}


    editar_ficha_basica = function() {

      var l=(screen.width/2)-350;
      var t=(screen.height/2)-300;

			editar_ficha = window.open('../ficha_clinica/editar_ficha_basica.php?'+
                                  'pac_id=<?php echo $pac_id; ?>', 'inter_ficha_ver', 
                                  'left='+l+',top='+t+',width=700,height=490,'+
                                  'status=0,scrollbars=1');

			editar_ficha.focus();

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

		<table style='width:100%;'>

		<tr><td style='text-align:right;width:140px;'>RUT:</td>				<td><b>".$inter[2]."</b></td></tr>

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

    <td>".htmlentities($inter[7]).(($inter[19]!='')?', '.htmlentities($inter[19]):'')."</td></tr>

    <tr><td style='text-align:right;'>Tel&eacute;fono:</td>				

    <td>".(($inter[20]!='')?htmlentities($inter[20]):'<i>(No hay registro)</i>')."</td></tr>

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
    
    <tr>
    <td colspan=2 style='text-align:center;'>
    <br>
    <input type='button' onClick='editar_ficha_basica();'
    value='-- Modificar Ficha Básica del Paciente --'>
    <br><br>
    </td></tr>

		</table>

		</div>

		

		<div class='sub-content'><img src='../iconos/chart_organisation.png'> <b>Informaci&oacute;n de Interconsulta</b></div>

		<div class='sub-content2'>

		<table>

		<tr><td style='text-align:right;'>Especialidad:</td>		

		<td width=60%><b>".$inter2[0]."</b></td></tr>

		<tr><td style='text-align:right;'>Motivo Derivaci&oacute;n:</td>		

		<td width=60%>".$inter2[9]."</td></tr>
    ");
    
    if($inter2[10]!='')

    print("
    <tr><td style='text-align:right;' valign='top'>Diagn&oacute;stico (Pres.):</td>		

		<td width=60%><b>".$inter2[10]."</b><br>".htmlentities($inter2[11])."</td></tr>
    ");


    print("
    
    <tr><td style='text-align:right;' valign='top'>Sospecha AUGE:</td>

    ");

    

    

    if($inter2[13]!=1)

    	print("<td><b>".$inter2[12]."</b></td></tr>");

  	else

    	print("<td>No hay sospecha.</td></tr>");

  	
  	if(trim($inter2[1])!="")


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

		

		if($inter2[6]==1)   $inter2[6]='Baja';

    if($inter2[6]==2)   $inter2[6]='Media';

    if($inter2[6]==3)   $inter2[6]='Alta';

    
    if($inter[15]!='')
    print("

		<div class='sub-content'><img src='../iconos/user_comment.png'> <b>Datos del Profesional Solicitante</b></div>

		

    <div class='sub-content2'>

		

    <table style='width:100%;'>

		<tr><td style='text-align:right'>RUT:</td>

    <td style='font-weight:bold;'>".$inter[15]."</td></tr>

		<tr><td style='text-align:right'>Nombre:</td>

    <td>".htmlentities($inter[16])." ".htmlentities($inter[17])." ".htmlentities($inter[18])."</td></tr>

		</table>

		

    </div>

    ");

    

    print("

    <div class='sub-content'><img src='../iconos/page_edit.png'> 

		<b>Resoluci&oacute;n M&eacute;dica</b></div>

		<div class='sub-content2'>

		<table>

		<tr><td style='text-align:right;' width=150>Estado Actual:</td>				

		<td>

		

		<table><tr><td><img src='".$imagen."'></td><td> <b>".$texto."</b>

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

		<td>".$inter2[5]."</td></tr>

		");

		

		print("

    

    </table>

		</div>");

		

		} else {

		

		$prioridadhtml = desplegar_opciones("prioridad", 

		"prior_id, prior_desc",'prior_id=0','true','ORDER BY prior_id'); 



    $ic = cargar_registros_obj("

      SELECT * FROM interconsulta 

      JOIN instituciones ON inter_inst_id1=inst_id
      LEFT JOIN patologias_auge ON inter_pat_id=pat_id
      LEFT JOIN patologias_auge_ramas ON inter_patrama_id=patrama_id
      LEFT JOIN garantias_atencion ON inter_garantia_id=garantia_id

      WHERE inter_pac_id=$pac_id AND 
            (inter_estado=1 OR inter_estado=3 OR inter_estado=0)

      ORDER BY inter_fecha

    ");

    

    if($ic AND count($ic)>1) {

    

      print("

    		<div class='sub-content'><img src='../iconos/exclamation.png'> 

        <b>Interconsultas Vigentes (".(count($ic)-1).")</b></div>

    		

        <div class='sub-content2'>

        <table style='width:100%;'>

      ");

      

      for($n=0;$n<count($ic);$n++) {

        

        if($ic[$n]['inter_id']==$id) continue;

        

        if($ic[$n]['inter_folio']==-1)

          $ic[$n]['inter_folio']='<i>(s/n)</i>';

          

    		switch($ic[$n]['inter_estado']) {

    				case 0: $imagen = '../iconos/time.png'; 

    						$texto='En espera...'; break;

    				case 1: $imagen = '../iconos/tick.png'; 

    						$texto='Aceptado'; break;

            case 2: $imagen = '../iconos/cross.png'; 

    						$texto='Rechazado'; break;

    				case 3: $imagen = '../iconos/arrow_refresh.png';

    						$texto='Atendido y en Control'; break;

    				case 4: $imagen = '../iconos/user_go.png';

    						$texto='Derivado...'; break;

    				case 5: $imagen = '../iconos/user_go.png'; 

    						$texto='Atendido y Retornado'; break;

    				case 6: $imagen = '../iconos/user_add.png'; 

    						$texto='Atendido y Permanente'; break;

            case 10: $imagen = '../iconos/user_delete.png'; 

    						$texto='Paciente Rechaza Tratamiento'; break;

    		}

          

        print("

        <tr class='tabla_header'>

        <td style='text-align:right;width:40%;'>Nro. Folio:</td>

        <td style='font-weight:bold;cursor:pointer;' 

        onClick='abrir_ficha(".$ic[$n]['inter_id'].");'>

        <u>".$ic[$n]['inter_folio']."</u></td>

        <td style='text-align:right;width:40%;'>Estado:</td>

        <td><img src='".$imagen."'></td>

        </tr>

        <tr>

        <td style='text-align:right;' class='tabla_fila2'>Fecha:</td>

        <td colspan=3>".$ic[$n]['inter_fecha']."</td>

        </tr>

        <tr>

        <td style='text-align:right;' class='tabla_fila2'>Instituci&oacute;n Solicitante:</td>

        <td colspan=3>".$ic[$n]['inst_nombre']."</td>

        </tr>");

        

        if($ic[$n]['inter_pat_id']==0)

          print("<tr>

          <td style='text-align:right;' class='tabla_fila2'>Garant&iacute;a de Oportunidad:</td>

          <td colspan=3>".$ic[$n]['garantia_nombre']."</td>

          </tr>");

        else {

          print("        

          <tr>

          <td style='text-align:right;' class='tabla_fila2'>Patolog&iacute;a AUGE:</td>

          <td colspan=3>".$ic[$n]['pat_glosa']."</td>

          </tr>");

          if($ic[$n]['rama_nombre']!='') 

            print("<tr>

            <td style='text-align:right;' class='tabla_fila2'>Rama Patolog&iacute;a AUGE:</td>

            <td colspan=3>".$ic[$n]['rama_nombre']."</td>

            </tr>        

            ");

        }

          

      }

      

      print("	

        </table>

        </div>

      ");      

    

    }



	

		print("

		<div class='sub-content'><img src='../iconos/page_edit.png'> 

		<b>Resoluci&oacute;n M&eacute;dica</b></div>

		<div class='sub-content2'>

		<form name='resolucion' id='resolucion' onsubmit='return false;'>

		<table>

		<tr><td style='text-align:right;' width=150>Estado del Caso:</td>				

		<td>

		

		<input type='hidden' name='inter_id' id='inter_id' value='".$id."'>

		<input type='hidden' name='institucion' id='institucion' value='".$institucion."'>

		<select id='estado' name='estado'>

		<option value=1>Aceptado</option>

		<option value=2>Rechazado...</option>

		<option value=3>Derivado...</option>

		</select>

		

		</td></tr>

		

		<tr><td style='text-align:right;' width=150>Prioridad:</td>				

		<td>

		

		<select id='prioridad' name='prioridad'>

		".$prioridadhtml."

    </select>

		

		</td></tr>

		

		<tr><td style='text-align:right;' valign='top'>Cod. Diagn&oacute;stico:</td>

    <td>

    <input type='text' id='diag_cod' name='diag_cod' size=10><br>

    <span id='diagnostico' style='font-weight:bold;'></span>

    </td></tr>

    

		<tr><td style='text-align:right;' valign='top'>Observaciones:</td>

		<td>

		<textarea cols=30 rows=3 

    id='observaciones' name='observaciones'></textarea>

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

		

		seleccionar_diagnostico = function(d) {

    

      $('diag_cod').value=d[0];

      $('diagnostico').innerHTML='['+d[0]+'] '+d[2];

    

    }

    

    autocompletar_diagnostico = new AutoComplete(

      'diag_cod', 

      '../autocompletar_sql.php',

      function() {

        if($('diag_cod').value.length<3) return false;

      

        return {

          method: 'get',

          parameters: 'tipo=diagnostico&cadena='+encodeURIComponent($('diag_cod').value)

        }

      }, 'autocomplete', 200, 100, 150, 1, 3, seleccionar_diagnostico);



		

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

