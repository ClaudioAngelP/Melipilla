<?php 

	require_once('../../conectar_db.php');
	
	$nomd_id=$_GET['nomd_id']*1;
	
	$ndet=cargar_registro("
	  SELECT 
		pacientes.*, nomina_detalle.*, nomina.*, diag_desc, 
		date_part('year',age(pac_fc_nac)) as edad  
	  FROM nomina_detalle
	  JOIN nomina USING (nom_id)
	  JOIN pacientes USING (pac_id)
	  LEFT JOIN diagnosticos ON diag_cod=nomd_diag_cod
	  WHERE nomd_id=$nomd_id	  ORDER BY nomd_folio, 
	  (CASE WHEN trim(both from pac_ficha)='' THEN '0' 
	  	ELSE pac_ficha END)::bigint	
	", true);
	
	$pac_id=$ndet['pac_id']*1;
	
	$inf=cargar_registro("SELECT * FROM nomina_detalle_informe 
								JOIN doctores ON nomdi_doc_id=doc_id
								WHERE nomd_id=$nomd_id", true);
		
?>

<html>
<title>Registro de Informe Cl&iacute;nico</title>

<?php cabecera_popup('../..'); ?>

<!-- TinyMCE -->
<script type="text/javascript" src="../../js/tiny_mce/tiny_mce.js"></script>

<script type="text/javascript">

	tid=tinyMCE.init({
		// General options
		mode : "textareas",
		language: 'es',
		theme : "advanced",
		plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups,autosave,spellchecker",

		// Theme options
		theme_advanced_buttons1 : "spellchecker,|,cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,cleanup,help,code,|,insertdate,inserttime,|,forecolor,backcolor",
		theme_advanced_buttons2 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons3 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example word content CSS (should be your site CSS) this one removes paragraph margins
		content_css : "css/word.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		},
		spellchecker_languages : "+Spanish=es"
	});
	
	function guardar_informe() {

		if(tinyMCE.activeEditor.getContent()=='') {
			alert("Debe ingresar el cuerpo del mensaje.");
			return false;
		}
		
	}
	
	function adjuntar_archivo() {
	
      top=Math.round(screen.height/2)-90;      left=Math.round(screen.width/2)-175;
      new_win =       window.open('adjuntar.html',      'win_adjuntar', 'toolbar=no, location=no, directories=no, status=no, '+      'menubar=no, scrollbars=yes, resizable=no, width=350, height=180, '+      'top='+top+', left='+left);
      new_win.focus();	
		$('adjuntar').target='win_adjuntar';
	
		$('adjuntar').submit();	
		
	}

	function listar_adjuntos() {
	
		var myAjax=new Ajax.Updater(
			'lista_adjuntos',
			'listar_adjuntos.php',
			{
				method:'post',
				parameters:$('nomd_id').serialize()	
			}		
		);	
		
	}

	function eliminar_adjunto(nomda_id) {
	
		var conf=confirm( '&iquest;Est&aacute; seguro que desea eliminar el archivo adjunto?'.unescapeHTML() );
		
		if(!conf) return;	
	
		var myAjax=new Ajax.Request(
			'eliminar_adjunto.php',
			{
				method:'post',
				parameters:'nomda_id='+nomda_id,
				onComplete:function() {
					listar_adjuntos();	
				}	
			}		
		);	
		
	}

	function guardar_informe() {

		if(tinyMCE.activeEditor.getContent()=='') {
			alert("Debe ingresar el cuerpo del mensaje.");
			return false;
		}
	
		var params=$('datos').serialize();
		params+='&html='+encodeURIComponent(tinyMCE.activeEditor.getContent());
		params+='&'+$('doc_id').serialize();
		params+='&'+$('fecha1').serialize();
		params+='&'+$('fecha2').serialize();
		params+='&'+$('fecha3').serialize();
	
		var myAjax=new Ajax.Request(
			'sql_informe.php',
			{
				method:'post',
				parameters: params,
				onComplete:function() {
					alert('Informe guardado exitosamente.');
					tinyMCE.activeEditor.destroy();
					window.close();	
				}						
			}		
		);	
		
	}
	
	function abrir_adjunto(nomda_id) {
		
		window.open('abrir_adjunto.php?nomda_id='+nomda_id,'_self');
		
	}


</script>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content'>
<img src='../../iconos/script_edit.png' />
<b>Registro de Informe Cl&iacute;nico</b>
</div>

<form id='datos' name='datos' onSubmit='return false'>
<input type='hidden' id='nomd_id' name='nomd_id' value='<?php echo $nomd_id; ?>' />
<input type='hidden' id='esp_id' name='esp_id' value='<?php echo $esp_id; ?>' />
<input type='hidden' id='cambia_presta' name='cambia_presta' value='0' />
</form>

<div class='sub-content'>

<table style='width:100%;'>

<tr><td style='width:150px;text-align:right;' class='tabla_fila2'>
R.U.T.:
</td><td colspan=3 class='tabla_fila' style='text-align:left;font-size:16px;'>
<?php echo $ndet['pac_rut']; ?>
</td><td style='width:100px;text-align:right;' class='tabla_fila2'>
Nro. Ficha:
</td><td class='tabla_fila' style='text-align:center;font-size:16px;font-weight:bold;'>
<?php echo $ndet['pac_ficha']; ?>
</td></tr>

<tr><td style='width:100px;text-align:right;' class='tabla_fila2'>
Nombre Paciente:
</td><td colspan=5 class='tabla_fila' style='text-align:left;font-weight:bold;font-size:16px;'>
<?php echo $ndet['pac_nombres'].' '.$ndet['pac_appat'].' '.$ndet['pac_apmat']; ?>
</td></tr>

<tr>
<td style='text-align:right;' class='tabla_fila2'>
Fecha de Solicitud:
</td><td class='tabla_fila'>
<input type='text' name='fecha1' id='fecha1' size=10  style='text-align: center;' value='<?php echo $inf['nomdi_fecha_solicitud'] ?>'>  <img src='../../iconos/date_magnify.png' id='fecha1_boton'>
</td><td style='text-align:right;' class='tabla_fila2'>
Fecha de Digitaci&oacute;n:
</td><td class='tabla_fila'>
<input type='text' name='fecha2' id='fecha2' size=10  style='text-align: center;' value='<?php echo $inf['nomdi_fecha_digitacion'] ?>'>  <img src='../../iconos/date_magnify.png' id='fecha2_boton'>
</td><td style='text-align:right;' class='tabla_fila2'>
Fecha de Entrega:
</td><td class='tabla_fila'>
<input type='text' name='fecha3' id='fecha3' size=10  style='text-align: center;' value='<?php echo $inf['nomdi_fecha_entrega'] ?>'>  <img src='../../iconos/date_magnify.png' id='fecha3_boton'>
</td>
</tr>


<tr><td style='width:100px;text-align:right;' class='tabla_fila2'>
Informado por:
</td><td colspan=5 class='tabla_fila' style='text-align:left;'>
<input type='hidden' id='doc_id' name='doc_id' value='<?php echo $inf['doc_id']; ?>'>
<input type='text' id='rut_medico' name='rut_medico' size=10
style='text-align: center;' value='<?php echo $inf['doc_rut']; ?>' disabled>
<input type='text' id='nombre_medico' 
value='<?php echo trim($inf['doc_paterno'].' '.$inf['doc_materno'].' '.$inf['doc_nombres']); ?>' name='nombre_medico' size=45>
</td></tr>

</table>

</div>
<textarea id='informe' name='informe' style='width:100%;height:250px;'><?php echo $inf['nomdi_html']; ?></textarea>

<div class='sub-content'>
<form id='adjuntar' name='adjuntar' action='adjuntar.php' 
onSubmit='return false;' method='post' enctype="multipart/form-data">
<input type='hidden' id='nomd_id' name='nomd_id' value='<?php echo $nomd_id; ?>' />

<table style='width:100%;'>
<tr>
<td style='text-align:right;' class='tabla_fila2'>Adjuntar Archivo:</td>
<td class='tabla_fila'>
<input type='file' id='archivo' name='archivo' />
</td><td style='text-align:right;' class='tabla_fila2'>Descripci&oacute;n:</td>
<td class='tabla_fila'>
<input type='text' size=30 id='descripcion' name='descripcion' value='' >
<input type='button' id='' name='' value='Adjuntar...' onClick='adjuntar_archivo();' />
</td></tr>
<tr><td colspan=4 id='lista_adjuntos'>

</td></tr>
</table>
</form>
</div>


<center><br />
<input type='button' id='' name='' value='--- Guardar Registro ---' onClick='guardar_informe();' />
</center>

</body>
</html>

<script>

      ingreso_rut=function(datos_medico) {
      	$('doc_id').value=datos_medico[3];
      	$('rut_medico').value=datos_medico[1];
      }

      autocompletar_medicos = new AutoComplete(
      	'nombre_medico', 
      	'../../autocompletar_sql.php',
      function() {
        if($('nombre_medico').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=medicos&'+$('nombre_medico').serialize()
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_rut);


		listar_adjuntos();

</script>
