<?php

	require_once('../../conectar_db.php');

        $e=cargar_registros_obj("SELECT * FROM especialidades ORDER BY esp_desc;", true);
        $p=cargar_registros_obj("SELECT * FROM doctores ORDER BY doc_paterno, doc_materno, doc_nombres", true);

        $t=cargar_registros_obj("SELECT DISTINCT nom_motivo FROM nomina ORDER BY nom_motivo", true);


?>

<script>

validacion_fecha2=function(obj) {
        var obj=$(obj);

        if(trim(obj.value)=='') {
                obj.value='';
                obj.style.background='skyblue';
                return true;
        } else
                return validacion_fecha(obj);
}


listado=function() {

	$('xls').value='0';
	
	$('listado').innerHTML='<center><br/><br/><img src="imagenes/ajax-loader3.gif" /><br/>Espere un momento, cargando...</center>'

	var myAjax=new Ajax.Updater(
		'listado',
		'prestaciones/archivo_fichas/reportes_archivo.php',
		{
			method:'post',
			parameters:$('filtro').serialize()
		}
	);

}

listado_xls=function() {

	$('xls').value='1';

	$('filtro').submit();

}

configurar_calendarios=function() {

    Calendar.setup({
        inputField     :    'fecha1',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha1_boton'
    });
    Calendar.setup({
        inputField     :    'fecha2',
        ifFormat       :    '%d/%m/%Y',
        showsTime      :    false,
        button          :   'fecha2_boton'
    });
	
	validacion_fecha2($('fecha1'));
	validacion_fecha2($('fecha2'));

}


</script>


<center>
<div class='sub-content' style='width:90%;'>


<div class='sub-content'>
<img src='iconos/layout.png' />
<b>Reportes de Archivo</b>
</div>


<form id='filtro' name='filtro' method='post' action='prestaciones/archivo_fichas/reportes_archivo.php' onSubmit='return false;'>

<input type='hidden' id='xls' name='xls' value='0' />

<div class='sub-content'>
<table style='width:100%;'>
<table style='width:100%'>
<tr><td style='text-align:right;' class='tabla_fila2'>Rango de Fechas:</td>
<td>
  <input type="text" id="fecha1" name="fecha1" size=10 style='text-align:center;' value='<?php echo date('d/m/Y'); ?>' onChange='validacion_fecha2(this);' />
  <img src="iconos/calendar.png" id="fecha1_boton">
  <input type="text" id="fecha2" name="fecha2" size=10 style='text-align:center;' value='<?php echo date('d/m/Y'); ?>' onChange='validacion_fecha2(this);' />
  <img src="iconos/calendar.png" id="fecha2_boton">
</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>Especialidades:</td>
<td>
<select id='esp_id' name='esp_id'>
<option value=''>(Todas...)</option>
<?php 
	for($i=0;$i<sizeof($e);$i++) {

		print("<option value='".$e[$i]['esp_id']."'>".$e[$i]['esp_desc']."</option>");

	}
?>
</select></td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>Profesionales:</td>
<td>
<select id='doc_id' name='doc_id'>
<option value=''>(Todos...)</option>
<?php
        for($i=0;$i<sizeof($p);$i++) {

                print("<option value='".$p[$i]['doc_id']."'>".$p[$i]['doc_nombres']." ".$p[$i]['doc_paterno']." ".$p[$i]['doc_materno']."</option>");

        }
?>
</select></td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>Tipo Atenci&oacute;n:</td>
<td>
<select id='nom_motivo' name='nom_motivo'>
<option value=''>(Todos...)</option>
<?php
        for($i=0;$i<sizeof($t);$i++) {

                print("<option value='".$t[$i]['nom_motivo']."'>".$t[$i]['nom_motivo']."</option>");

        }
?>
</select></td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>Reporte:</td>
<td>
<select id='tipo' name='tipo'>
<option value='0'>Fichas Solicitadas (PROGRAMADAS)</option>
<option value='1'>Fichas Solicitadas (NO PROGRAMADAS)</option>
<option value='2'>Fichas fuera de Archivo</option>
</select>
</td></tr>
</table>

<center>
<input type='button' value='Generar Reporte...' onClick='listado();' />
<input type='button' value='Descargar XLS...' onClick='listado_xls();'  />
</center>
</div>

</form>

<div class='sub-content2' style='height:350px;overflow:auto;' id='listado'>

</div>
</div>
</center>


<script> configurar_calendarios(); </script>