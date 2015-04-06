<?php 

	require_once('../../conectar_db.php');

  $htmlfuncionarios = desplegar_opciones_sql( 
  "SELECT DISTINCT func_id, (func_nombre) 
  FROM funcionario 
  JOIN func_acceso USING (func_id) 
  WHERE permiso_id=301 ORDER BY (func_nombre)", 
  NULL, '', "font-style:italic;color:#555555;"); 
	

?>

<script>

abrir_credito=function(crecod, ruta) {

	var params='crecod='+crecod;
	
	if(ruta==null) ruta='';
	
    l=(screen.availWidth/2)-300;
    t=(screen.availHeight/2)-250;
        
    win = window.open(ruta+'creditos/visualizar_credito.php?'+params, 
                    '_ver_credito',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=600, height=500');
                    
    win.focus();


}

abrir_boletin=function(bolnum, ruta) {

	var params='bolnum='+bolnum;
	
    l=(screen.availWidth/2)-325;
    t=(screen.availHeight/2)-200;
        
    win = window.open(ruta+'creditos/visualizar_boletin.php?'+params, 
                    '_ver_boletin',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=650, height=415');
                    
    win.focus();


}


consultar=function(v) {

	if($('fecha1').value==$('fecha2').value && 
		$('funcionarios').value==<?php echo $_SESSION['sgh_usuario_id']; ?>) {
		$('detcaja').style.display='';	
	} else {
		$('detcaja').style.display='none';			
	}
	
	if($('informe').value*1==0)
		url='ingresos/cierre_caja/listado_caja.php';
	else if($('informe').value*1==1)
		url='ingresos/cierre_caja/cierres_caja.php';
	else if($('informe').value*1==2)
		url='ingresos/cierre_caja/prestaciones.php';
	

	if(v==0) {	
	
		var params=$('fecha1').serialize()+'&'+$('fecha2').serialize()+'&'+$('funcionarios').serialize();		
		
		$('listado_caja').innerHTML='<br /><br /><br /><br /><img src="imagenes/ajax-loader3.gif" /><br />Cargando Informaci&oacute;n...';
		
		var myAjax = new Ajax.Updater(
		'listado_caja',url, {
			method: 'post',parameters: params
		});
	
	} else {

		$('filtro').action=url;
	
		$('filtro').submit();
	
	}


}

completar_caja=function(n) {

			var params=$('fecha1').serialize()+'&'+$('funcionarios').serialize();
			
		    l=(screen.availWidth/2)-300;
		    t=(screen.availHeight/2)-225;
		        
		    win = window.open('ingresos/cierre_caja/detalle_caja.php?'+params, 
		                    '_ver_sepulturas',
		                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
		                    'resizable=no, width=600, height=450');
		    
		    win.focus();

}

informe_caja=function(ac_id) {

	var params='ac_id='+ac_id;
	
    l=(screen.availWidth/2)-325;
    t=(screen.availHeight/2)-200;
        
    win = window.open('ingresos/cierre_caja/imprimir_cierre_caja.php?'+params, 
                    '_ver_boletin',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=650, height=415');
                    
    win.focus();


}




imprimir_caja=function() {

  _datos = $('listado_caja').innerHTML;
  
  _separador = '<h3>Informe de Caja</h3><hr>';
  
  imprimirHTML(_separador+_datos);
	
}

</script>

<center>
<div class='sub-content' style='width:780px;'>

<div class='sub-content'>
<img src='iconos/money.png'>
<b>Informes de Caja</b>
</div>

<form id='filtro' name='filtro' method='post' 
action='ingresos/cierre_caja/listado_caja.php'>
<input type='hidden' id='xls' name='xls' value='1'> 

<div class='sub-content'>

<table style='width:100%;'><tr><td style='width:60%;'>

<table style='width:100%;'>

  <tr><td style='text-align: right;'>Fecha Inicio:</td>
  <td><input type='text' name='fecha1' id='fecha1' size=10
  style='text-align: center;' value='<?php echo date("d/m/Y")?>'>
  <img src='iconos/date_magnify.png' id='fecha1_boton'></td></tr>
  <tr><td style='text-align: right;'>Fecha Final:</td>
  <td><input type='text' name='fecha2' id='fecha2' size=10
  style='text-align: center;' value='<?php echo date("d/m/Y")?>'>
  <img src='iconos/date_magnify.png' id='fecha2_boton'></td></tr>

<tr><td style='text-align:right;'>Informe:</td>
<td><select id='informe' name='informe'>
<option value=0>Recaudaci&oacute;n</option>
<option value=1>Aperturas y Cierre de Caja</option>
<option value=2>Prestaciones</option>
</select></td></tr>

<tr><td style='text-align:right;'>Contabilizar M&oacute;dulo(s):</td>
<td><select id='funcionarios' name='funcionarios'>
<option value=-1>(Todos los M&oacute;dulos...)</option>
<?php echo $htmlfuncionarios; ?>
</select></td></tr>


</table>

</td><td>
<center>
<input type='button' onClick='consultar(0);' value='Visualizar Informe de Caja...'>
<br />
<br />
<input type='button' onClick='consultar(1);' value='Descargar Informe en XLS...'>
</center>
</td></tr></table>

</div>

</form>

<div class='sub-content2' style='height:260px;overflow:auto;' id='listado_caja'>

</div>

<div id='detcaja' name='detcaja' style='display:none;'>
<center>
<input type='button' 
value='-- Ingresar Detalle de Efectivo --' onClick='completar_caja();' /> 
<!----<input type='button' 
value='-- Imprimir Cierre de Caja --' onClick='imprimir_caja();' /> --->
</center>
</div>

</div>
</center>

  <script>
  
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

  
  </script>
  
