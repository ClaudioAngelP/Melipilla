<?php

  require_once('../../conectar_db.php');

  $servs="'".str_replace(',','\',\'',_cav2(50))."'";

  $servicioshtml = desplegar_opciones_sql( 
  "SELECT centro_ruta, centro_nombre FROM centro_costo WHERE
  length(regexp_replace(centro_ruta, '[^.]', '', 'g'))=3 AND
          centro_medica AND centro_ruta IN (".$servs.")
  ORDER BY centro_nombre", NULL, '', "font-style:italic;color:#555555;"); 

?>

<script>


    listar_fap = function() {
		
		try {

      $('listado_fap').style.height='280px';
      $('lista_fap').value='Actualizar Listado...'.unescapeHTML();
      $('buscador').style.display='';
      $('ver_regs').style.display='none';
		
      $('listado_fap').innerHTML='<br /><br /><br /><img src="imagenes/ajax-loader2.gif" /><br />Cargando...'

      var myAjax = new Ajax.Updater(
      'listado_fap',
      'prestaciones/ingreso_fap/buscar_fap_pdf.php',
      {
        method:'post',
        parameters:$('info_fap').serialize(),
        evalScripts: true
      });
      
      } catch(err) {
      	
      	alert(err);
      		
      }

    }
   
	var cursor=0;  
	
	imprimir_fap_completo = function(fap_id) {

      top=Math.round(screen.height/2)-165;
      left=Math.round(screen.width/2)-340;

      new_win = 
      window.open('prestaciones/ingreso_fap/imprimir_fap_completo.php?fap_id='+fap_id,
      'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=680, height=330, '+
      'top='+top+', left='+left);

      new_win.focus();
		
	}
	comprueba_pausa = function(fap_id, fcl_id, tipo_imp,ub){
		
		var fcl_ids='9|10|12';
		
		var myAjax = new Ajax.Request(
		'prestaciones/ingreso_fap/comprueba_pausas.php',
		{
		  method:'post',
		  parameters: 'fap_id='+fap_id+'&ptipo=1&fcl_id='+fcl_id+'&fcl_ids='+fcl_ids,
		  onComplete: function(resp) {
			   try {
			
					resultado=resp.responseText;
				
					if(resultado!=''){
						alert('Debe completar la pausa de seguridad');
						
						
						
						return;
					}else{
						if(tipo_imp==0) imprimir_pausa(fap_id); else comprueba_hoja(fap_id,1,ub);
					}
				}catch(err){
					alert("ERROR: " + resp.responseText);
				}
		  }
		  });
	}
	
	comprueba_hoja = function(fap_id,tipo_imp,ub){
				
		var myAjax = new Ajax.Request(
		'prestaciones/ingreso_fap/comprueba_hoja.php',
		{
		  method:'post',
		  parameters: 'fap_id='+fap_id,
		  onComplete: function(resp) {
			   try {
			
					resultado=resp.responseText;
				
					if(resultado!=''){
						alert("Debe completar la hoja de intervenci&oacute;n. \n".unescapeHTML()+resultado);
						abrir_fap(fap_id,ub);
						return;
					}else{
						if(tipo_imp==0) imprimir_hoja(fap_id); else imprimir_fap_completo(fap_id);
					}
				}catch(err){
					alert("ERROR: " + resp.responseText);
				}
		  }
		  });
	}
	
	

</script>

<center>

<div class='sub-content' style='width:95%;height:640px;overflow:auto;'>
<form id='info_fap' onSubmit='return false;'>

<div class='sub-content'>
<img src='iconos/table_edit.png'> <b>Historial de Prestaciones Quir&uacute;rgicas</b>
</div>

<div class='sub-content' id='buscador'>

<table style='width:100%;'>
<tr>
<td style='width:100px;text-align:right;'>Tipo:</td>
<td>
<select id='tipo' name='tipo'
<?php if(_cax(209)) { ?>
<onChange='
	if(this.value*1==5) {
		$("genera_fap").style.display="";
	} else {
		$("genera_fap").style.display="none";		
	}
'
<?php } ?>
>

<?php if(_cax(208) OR _cax(209)) { ?> <option value='5' SELECTED>Pabell&oacute;n</option> <?php } ?>
</select>
</td>
</tr>


<tr>
<td style='width:100px;text-align:right;'>Fecha Inicio:</td>
<td>
<input type='text' name='fecha1' id='fecha1' size=10
  style='text-align: center;' value='<?php echo date("d/m/Y")?>'
  <onChange='listar_fap();'>
  <img src='iconos/date_magnify.png' id='fecha1_boton'>
  
</td>

<td id='ver_refs' style='text-align:center;'>

</td>
</tr>
<tr>
<td style='width:100px;text-align:right;'>Fecha Termino:</td>
<td>
<input type='text' name='fecha2' id='fecha2' size=10
  style='text-align: center;' value='<?php echo date("d/m/Y")?>'
 <onChange='listar_fap();'>
  <img src='iconos/date_magnify.png' id='fecha2_boton'>
  
</td>

<td id='ver_refs' style='text-align:center;'>

</td>
</tr>

<tr>
<td style='text-align:right;'>Filtrar Lista:</td>
<td>
<input type='text' size=50 id='filtro' name='filtro' value='' />
</td>
</tr>




<tr><td colspan=3>

<center>



<input type='button' id='lista_fap' 
onClick='listar_fap();'
value='Actualizar Listado...'>

<input type='button' id='imprime_fap' 
onClick='imprimir_lista_fap();'
value='Imprimir Listado...'>

</center>

</td></tr>

</table>

</div>
<div class='sub-content' id='ver_regs' style='display:none;'>
<table style='width:100%;'>
<tr><td id='ver_ubica' style='width:130px;text-align:center;'>

</td><td style='text-align:right;'>
Fecha FAP:
</td><td id='fap_fechas' style='text-align:center;font-weight:bold;font-size:9px;width:60px;'>

</td><td style='text-align:right;'>
Ficha Nro.:
</td><td id='nro_ficha' style='text-align:center;font-weight:bold;width:60px;'>

</td><td style='text-align:right;'>
Nombre:
</td><td id='ver_paciente' style='width:350px;font-weight:bold;'>

</td></tr></table>
</div>

<div class='sub-content2'  style='height:400px; overflow:auto;'
id='listado_fap'>

</div>

</form>

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
        inputField     :    'fecha2',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha2_boton'

    });

    listar_fap();
    
   /*if($('tipo').value*1==5) {
		$("genera_fap").style.display="";
	} else {
		$("genera_fap").style.display="none";		
	}*/
    
</script>
