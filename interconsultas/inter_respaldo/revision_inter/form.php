<?php
  require_once('../../conectar_db.php');
	$especialidadhtml = desplegar_opciones_sql("
	SELECT esp_id, esp_desc FROM especialidades WHERE esp_padre_id=-1 
	ORDER BY esp_desc
	"); 
?>
		<script>
		realizar_busqueda = function() {
			var myAjax = new Ajax.Updater(			'resultado', 			'interconsultas/listar_interconsultas.php', 			{				method: 'get', 				parameters: 'tipo=revisar_interconsultas&'+$('busqueda').serialize()			}
			);
		}
		abrir_ficha = function(id) {
			inter_ficha = window.open('interconsultas/visualizar_ic.php?tipo=revisar_inter_ficha&inter_id='+id,			'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
			inter_ficha.focus();
		}
		abrir_oa = function(id) {
			inter_ficha = window.open('interconsultas/visualizar_oa.php?tipo=revisar&oa_id='+id,			'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
			inter_ficha.focus();
		}
		</script>
		<center>
		<table width='900'>
		<tr><td>
		<div class='sub-content'>
		<div class='sub-content'>
		<img src='iconos/chart_organisation.png'> <b>Listado de Interconsultas Pendientes</b>
		</div>
		<div class='sub-content'>
		<form name='busqueda' id='busqueda' onSubmit='return false;'>
		<table style='width:100%;'>


    <tr>
		<td style='text-align: right;width:150px;'>Filtro:</td>
		<td width=75% style='text-align: left;'>
		<input type='text' id='filtro' name='filtro' size=40 value="" />

		</td>
    </tr>

    <tr>
		<td style='text-align: right;width:150px;'>Instituci&oacute;n Solicitante:</td>
		<td width=75% style='text-align: left;'>
		<input type='hidden' id='inst_id1' name='inst_id1' value='0'>

		<input type='text' id='institucion1' name='institucion1' size=40
		ondblclick='$("inst_id1").value="0"; $("institucion1").value="";'>

		</td>
    </tr>
		<tr><td style='text-align:right;'>Especialidad Cl&iacute;nica:
		</td><td>
		<select id='especialidad' name='especialidad'>
		<option value='-1'>(Todas las Especialidades...)</option>

		<?php echo $especialidadhtml; ?>
		</select>
		</td></tr>
		
		<tr><td colspan=2>
		<center>
		<input type='button' value='-- Actualizar Listado... --' 
		onClick='realizar_busqueda();' />		
		</center>
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
		</center>
		<script>
    seleccionar_inst1 = function(d) {
    
      $('inst_id1').value=d[0];
      $('institucion1').value=d[2].unescapeHTML();
    
    }

    autocompletar_institucion1 = new AutoComplete(
      'institucion1', 
      'autocompletar_sql.php',
      function() {
        if($('institucion1').value.length<3) return false;

        return {
          method: 'get',
          parameters: 'tipo=instituciones&cadena='+encodeURIComponent($('institucion1').value)
        }
      }, 'autocomplete', 350, 200, 150, 2, 3, seleccionar_inst1);


		  realizar_busqueda();
		</script>