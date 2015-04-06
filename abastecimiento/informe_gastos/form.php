<?php

  require_once('../../conectar_db.php');
  
  $opts=desplegar_opciones_sql("SELECT bod_id, bod_glosa FROM bodega ORDER BY bod_id",$campo[3]);
  
?>

<script>

generar_informe = function() {

  if($('tipoinf').value==0) xls=''; else xls='&xls';

      params='submit&fecha1='+encodeURIComponent($('fecha1').value)
              +'&fecha2='+encodeURIComponent($('fecha2').value)+'&bod_id='+encodeURIComponent($('bod_id').value)+'&option_view='+encodeURIComponent($('option_view').value)+xls;
    
      top=Math.round(screen.height/2)-200;
      left=Math.round(screen.width/2)-300;
      
      new_win = 
      window.open('abastecimiento/informe_gastos/centrositems.php'+
      '?'+params, 'win_informe', 
      'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=600, height=400, '+
      'top='+top+', left='+left);
      
      new_win.focus();
      
}

</script>

<center>
<div class='sub-content' style='width:650px;'>

<div class='sub-content' style='font-weight:bold;'>
<img src='iconos/coins.png'>
Informe General de Gastos por Centro de Costo
</div>

<div class='sub-content2'>
<br>
<table width='100%'>
    <tr>
        <td style='text-align: right;'>Visualizaci&oacute;n:</td>
        <td>
            <select id='option_view' name='option_view' onClick=''>
                <option value=1 selected="">Centros de Responsabilidad / Costos</option>
                <option value=2 >Alias Winsig</option>
            </select>
        </td>
    </tr>
    <tr>
        <td style='text-align: right;'>Ubucaci&oacute;n:</td>
        <td>
            <select id='bod_id' name='bod_id' onClick=''>
                <option value=-1 selected="">(Todas Las Ubicaciones...)</option>
                <?php echo $opts; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td style='text-align: right;'>Fecha Inicio:</td>
        <td>
            <input type='text' name='fecha1' id='fecha1' size=10 style='text-align: center;' value='<?php echo date("d/m/Y"); ?>'>
            <img src='iconos/date_magnify.png' id='fecha1_boton'>
        </td>
    </tr>
    <tr>
        <td style='text-align: right;'>Fecha Final:</td>
        <td>
            <input type='text' name='fecha2' id='fecha2' size=10 style='text-align: center;' value='<?php echo date("d/m/Y"); ?>'>
            <img src='iconos/date_magnify.png' id='fecha2_boton'>
        </td>
    </tr>
    <tr>
        <td style='text-align:right;'>Informe:</td>
        <td>
            <select id='tipoinf' name='tipoinf'>
                <option value=0>En Pantalla</option>
                <option value=1>Descargar en XLS (MS Excel)</option>
            </select>
        </td>
    </tr>
</table>
<br>
<br>
</div>
<center>
    <table>
        <tr>
            <td>
		<div class='boton'>
                    <table>
                        <tr>
                            <td>
                                <img src='iconos/script.png'>
                            </td>
                            <td>
                                <a href='#' onClick='generar_informe();'> Generar Informe...</a>
                            </td>
                        </tr>
                    </table>
		</div>
            </td>
	</tr>
  </table>
</center>

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
