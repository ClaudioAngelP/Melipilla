<?php 
    
    require_once('../../conectar_db.php');
    
    $fechashtml = desplegar_opciones_sql( 
    "SELECT fecha, fecha FROM fechas_monitoreo_ges
    ORDER BY fecha", NULL, '', ""); 

    $siggespathtml = desplegar_opciones_sql( 
    "SELECT DISTINCT trim(pst_patologia_interna), trim(pst_patologia_interna)  FROM patologias_sigges_traductor
    ORDER BY trim(pst_patologia_interna)", NULL, '', ""); 

    
?>

<script>

listado_proceso=function() {

	if($('tipo_inf').value*1==0)
		var url='prestaciones/monitoreo_ges/listado_monitoreo.php';
	else
		var url='prestaciones/monitoreo_ges/informe_monitoreo.php';

    params=$('filtro').serialize();

	 $('listado').innerHTML='<br /><br /><br /><br /><img src="imagenes/ajax-loader2.gif"><br />Cargando Listado...';
    
    var myAjax=new Ajax.Updater(
    'listado',
    url,
    {
            method:'post', parameters:params, evalScripts:true
    });

}

listado_proceso_xls=function() {

	$('filtro').submit();

}

cargar_csv=function() {

    carga_masiva = window.open('prestaciones/monitoreo_ges/abrir_carga.php',
    'carga_masiva', 'left='+(screen.width/2-250)+',top='+(screen.height/2-100)+',width=480,height=270,status=0,scrollbars=1');
			
    carga_masiva.focus();

}

abrir_proceso=function() {

    proceso_ges = window.open('prestaciones/monitoreo_ges/abrir_proceso.php',
    'proceso_ges', 'left='+(screen.width/2-250)+',top='+(screen.height/2-100)+',width=480,height=270,status=0,scrollbars=1');
			
    proceso_ges.focus();

}

abrir_monitoreo=function(mon_id) {

    mon_ges = window.open('prestaciones/monitoreo_ges/form_monitoreo.php?mon_id='+mon_id,
    'proceso_ges', 'left='+((screen.width/2)-425)+',top='+((screen.height/2)-300)+',width=950,height=600,status=0,scrollbars=1');
			
    mon_ges.focus();

}
enviar_cartas=function() {

    mon_ges = window.open('prestaciones/correo_cartas_ges.php',
    'proceso_ges', 'left='+((screen.width/2)-225)+',top='+((screen.height/2)-150)+',width=450,height=300,status=0,scrollbars=1');
                        
    mon_ges.focus();
}

</script>

<center>
<div class='sub-content' style='width:980px;'>

<div class="sub-content">
<img src='iconos/error.png'>
<b>Monitoreo GES</b>
<i>
Fecha &Uacute;ltima Carga: <u><?php 
	$tmp=cargar_registro("SELECT MAX(mon_fecha_ingreso) AS fecha FROM monitoreo_ges;");
	print(substr($tmp['fecha'],0,16));
?></u></i>
</div>

<div class="sub-content">

<form id='filtro' name='filtro' action='prestaciones/monitoreo_ges/listado_monitoreo_xls.php' method='post'
onSubmit='return false;'>

<center>
<table style='width:100%;'>

<tr>
<td style='text-align:right;'>Patolog&iacute;a:</td>
<td>
<select id='pat' name='pat'>
<option value=-1 SELECTED>(Todas las Patolog&iacute;as...)</option>
<?php echo $siggespathtml; ?>
</select>
</td>
<td colspan=2>

<center>
<select id='tipo_ver' name='tipo_ver'>
<option value='0' SELECTED>Ver Condici&oacute;n</option>
<option value='1'>Ver Garant&iacute;a/Rama</option>
</select>

&nbsp; &nbsp;

<select id='tipo_inf' name='tipo_inf'>
<option value='0' SELECTED>Visualizar Detalle</option>
<option value='1'>Visualizar Totales</option>
</select>

</center>

</td>
</tr>

<tr id='gar_tr' style='display:none;'>
<td style='text-align:right;'>Garant&iacute;a:</td>
<td colspan=3 id='gar_td'>
<select id="filtrogar" name="filtrogar"></select>
</td>
</tr>

<tr id='cond_tr' style='display:none;'>
<td style='text-align:right;'>Condiciones:</td>
<td colspan=3 id='cond_td'>
<select id="filtrocond" name="filtrocond"><option value="0" SELECTED>(Todas las Clasificaciones...)</option></select>
</td>
</tr>

<tr>
<td style='text-align:right;'>Filtro:</td>
<td>
<input type='text' id='filtro2' name='filtro2' size=40 />
</td>
<td style='text-align:right;'>Estado:</td>
<td>
<select id='estado' name='estado'>
<option value=-2>(Todas los Estados...)</option>
<option value=-1 SELECTED>Vigentes y Vencidos</option>
<option value=0>Vigentes</option>
<option value=1>Vencidos</option>
<option value=2>Cerrados</option>
<option value=3>Exceptuados</option>
</select>
</td>
</tr>

</table>
</center>
</form>

<center>
<input type='button' onClick='listado_proceso();' value='-- Actualizar Listado --'>
<input type='button' onClick='listado_proceso_xls();' value='-- Descargar XLS --'>
<input type='button' onClick='cargar_csv();' value='-- Carga Masiva CSV --'>
<input type='button' onClick='abrir_proceso();' value='-- Actualizar Registro de Monitoreo GES --'>
<?php if(_cax(58)) { ?><input type='button' onClick='enviar_cartas();' value='-- Enviar Cartas Pendientes --'><?php } ?>

</center>
</div>

<div class='sub-content2' id='listado' name='listado'
style='height:250px;overflow:auto;'>

</div>

</div>
</center>

