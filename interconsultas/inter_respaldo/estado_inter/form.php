<?php 
   require_once('../../conectar_db.php');
	$institucionhtml = desplegar_opciones("institucion_solicita", 	"instsol_id, instsol_desc",'','true','ORDER BY instsol_desc'); 
?>
		<script>
		realizar_busqueda = function() {
			var myAjax = new Ajax.Updater(			'resultado', 			'interconsultas/listar_interconsultas.php', 
			{				method: 'get', 				parameters: 'tipo=estado_interconsultas&'+$('busqueda').serialize()			}			);
		}
				abrir_ficha = function(id) {
			inter_ficha = window.open('interconsultas/visualizar_ic.php?tipo=inter_ficha&inter_id='+id,
			'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
			inter_ficha.focus();
		}
		abrir_oa = function(id) {
			inter_ficha = window.open('interconsultas/visualizar_oa.php?oa_id='+id,
			'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
			inter_ficha.focus();
		}		
		$('buscar').focus();
		</script>
		
		<center>
		
		<table width='850'>
		<tr><td>
		
		<div class='sub-content'>
		<div class='sub-content'>
		<img src='iconos/chart_organisation.png'> <b>Listado de Interconsultas</b>
		</div>
		<div class='sub-content'>
		<form name='busqueda' id='busqueda' onSubmit='return false;'>
	<table style='width:100%;'>
		<tr><td style='text-align: right;'>Buscar:
		</td><td>
		<input type='text' name='buscar' id='buscar' size=60>
		</td></tr>

    <tr>
		<td style='text-align: right;'>Instituci&oacute;n Solicitante:</td>
		<td width=75% style='text-align: left;'>
		<input type='hidden' id='inst_id1' name='inst_id1' value=''>

		<input type='text' id='institucion1' name='institucion1' size=40>

		</td>
    </tr>
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
		<tr><td colspan=2>
		<center><input type='button' value='Actualizar Listado...' onClick='realizar_busqueda();'>
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

</script>			