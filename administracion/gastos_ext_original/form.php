<?php

  require_once('../../conectar_db.php');

?>

<script>

gasto = new Object();

gasto.detalle=new Array();
gasto.totaldet=0;

listar_gastos = function() {

  var myAjax = new Ajax.Updater(
  'listado',
  'administracion/gastos_ext/listado_gastos.php',
  {
    method: 'get'
  }
  );

}

cargar_gasto = function(id_gasto) {

  var myAjax = new Ajax.Request(
  'administracion/gastos_ext/cargar_gasto.php',
  {
    method: 'get',
    parameters: 'gasto_id='+id_gasto,
    onComplete: function(resultado) {
      
      gasto=resultado.responseText.evalJSON(true);
      
      mostrar_gasto(id_gasto);
      
      if(id_gasto==0) {
        $('gasto_id').value=0;
        $('gasto_nombre').value='';
        $('gasto_unidad').value='';
        $('gasto_valmax').value=0;
        $('gasto_nombre').focus();
        $('botones_gasto').style.display='';
      }
      
      $('botones_gasto').style.display='';
      
    }
  }
  );

}

mostrar_gasto = function(id_gasto) {

  if(id_gasto!=0) {
    $('gasto_id').value=gasto.gastoext_id;
    $('gasto_nombre').value=gasto.gastoext_nombre.unescapeHTML();
    $('gasto_unidad').value=gasto.gastoext_unidad.unescapeHTML();
    $('gasto_valmax').value=gasto.gastoext_valortotal;
  }
  
  desactivar_gasto(false);
  
  dibujar_tabla_cc();
  
  calcular_tabla_cc();
    
  if(id_gasto!=0) {
    $('gasto_valmax').select();
    $('gasto_valmax').focus();
  }
    
}

dibujar_tabla_cc = function() {

  genhtml='<table style="width:100%;">';
  genhtml+='<tr class="tabla_header">';
  genhtml+='<td>Centro de Costo</td>';
  genhtml+='<td>Valor</td>';
  genhtml+='<td>Unidad</td>';
  genhtml+='<td>%</td>';
  genhtml+='</tr>';
  
  for(i=0;i<gasto.detalle.length;i++) {
    gasd = gasto.detalle[i];
    
    if((i%2)==0) clase='tabla_fila'; else clase='tabla_fila2';
    
    var espacios='<img src="iconos/blank.gif">'.repeat((gasd[4]-1));
    
    genhtml+='<tr class="'+clase+'" style="font-size: 10px;">';
    genhtml+='<td style="width: 200px;">';
    genhtml+='<table><tr><td>'+espacios+'</td><td>';
    genhtml+=gasd[0]+'</td></tr></table></td>';
    
    if(gasd[4]>1) {
      genhtml+='<td><center>';
      genhtml+='<input type="hidden" id="gextn_'+i+'" name="gextn_'+i+'" ';
      genhtml+='value="'+gasd[3]+'">';
      genhtml+='<input type="text" style="text-align: right;" ';
      genhtml+='size=10 onFocus="this.select();"';
      genhtml+='value="'+gasd[1]+'" id="gextd_'+i+'" ';
      genhtml+='onKeyUp="gasto.detalle['+i+'][1]=this.value; ';
      genhtml+='calcular_tabla_cc();" ';
      genhtml+='name="gextd_'+i+'"></center></td>';
      genhtml+='<td style="text-align: left;">'+gasd[2]+'</td>';
      genhtml+='<td id="gextdp_'+i+'" style="text-align: right;">';
      genhtml+='</td>';
    } else {
      genhtml+='<td colspan=3>&nbsp;</td>'    
    }
    genhtml+='</tr>';
    
  }
  
  genhtml+='</table>';
  
  $('listado_cc').innerHTML=genhtml;

}

calcular_tabla_cc = function() {
  
  gasto.totaldet=0;
  
  for(i=0;i<gasto.detalle.length;i++) {

    gasd = gasto.detalle[i];

    if(gasd[4]<=1) continue;

    gasto.totaldet+=gasd[1]*1;
    
    if((gasto.gastoext_valortotal*1)!=0) {
      $('gextdp_'+i).innerHTML=number_format(Math.round(((gasd[1]*100/gasto.gastoext_valortotal)*100))/100, 2)+'%';
      if($('gextdp_'+i).innerHTML=='NaN,00%') 
        $('gextdp_'+i).innerHTML='?%';
    } else {
      $('gextdp_'+i).innerHTML='?%';
    }
  
  }
  
  gasto.gastoext_valortotal=$('gasto_valmax').value;
  
  if((gasto.gastoext_valortotal*1)!=0) {
    porcentaje = Math.round(gasto.totaldet*100/gasto.gastoext_valortotal,2);
  } else {
    porcentaje = 0;
  }
  
  if (porcentaje!=100) {
    pcol='<span style="color: red;">';
    pcole='</span>';
  } else {
    pcol='<span style="color: green;">';
    pcole='</span>';
  }
  
  $('gasto_valact').innerHTML=number_format(gasto.totaldet,2)+' '+
                              $('gasto_unidad').value+
                              ' ('+pcol+number_format(porcentaje,2)+'%'+pcole+')';
  
}

desactivar_gasto = function(opcion) {

  $('gasto_nombre').disabled=opcion;
  $('gasto_unidad').disabled=opcion;
  $('gasto_valmax').disabled=opcion;
  
}

guardar_gasto = function() {

  var myAjax = new Ajax.Request(
  'administracion/gastos_ext/sql.php',
  {
    method: 'post',
    parameters: $('gasto_frm').serialize(),
    onComplete: function (resp) {
        respuesta=resp.responseText.evalJSON(true);
        
        if(respuesta) {
          alert('Modificaci&oacute;n de Gasto guardada exitosamente.'.unescapeHTML());
          listar_gastos();
        } else {
          alert(resp.responseText);
        }
    }
  }  
  );

}

nuevo_gasto = function() {

  gasto = new Object();

  gasto.detalle=new Array();
  gasto.totaldet=0;
  
  cargar_gasto(0);
  
}


</script>

<center>
<table style='width: 780px;'>
<tr><td>

<div class='sub-content'>


<form id='gasto_frm' name='gasto_frm'>
<table style='width: 100%;'>
<tr>
<td colspan=2>
<div class='sub-content'>
<img src='iconos/money_dollar.png'>
<b>Gastos Actualmente Definidos</b>
</div>
<div class='sub-content2' style='height:150px;overflow:auto;' id='listado'>

</div>
</td>
</tr>
<tr>
<td style='width: 270px;'>
<div class='sub-content'>
<img src='iconos/money.png'>
Informaci&oacute;n del Gasto
</div>

<div class='sub-content2' style='height:220px;'>
<input type='hidden' id='gasto_id' name='gasto_id' value=0>
<table width=100%>
<tr>
<td style='text-align: right;'>Nombre:</td>
<td><input type='text' size=20 id='gasto_nombre' name='gasto_nombre' disabled></td>
</tr>
<tr>
<td style='text-align: right;'>Unidad:</td>
<td><input type='text' size=10 id='gasto_unidad' name='gasto_unidad' disabled></td>
</tr>
<tr>
<td style='text-align: right;'>Valor Total Max.:</td>
<td>
<input type='text' size=10 id='gasto_valmax' name='gasto_valmax' disabled
onKeyUp='calcular_tabla_cc();' onFocus='this.select();'>
</td>
</tr>
<tr>
<td style='text-align: right;'>Valor Total Act.:</td>
<td style='font-weight: bold;' id='gasto_valact'></td>
</tr>
</table>

<div id='botones_gasto' style='display: none;'>
<div class='boton'>
		<table><tr><td>
		<img src='iconos/accept.png'>
		</td><td>
		<a href='#' onClick='guardar_gasto();'>
		Guardar Definici&oacute;n del Gasto...</a>
		</td></tr></table>
</div>

<div class='boton'>
		<table><tr><td>
		<img src='iconos/delete.png'>
		</td><td>
		<a href='#' onClick='cargar_gasto($("gasto_id").value);'>
		Cancelar Modificaci&oacute;n del Gasto...</a>
		</td></tr></table>
</div>
</div>
<div class='boton'>
		<table><tr><td>
		<img src='iconos/add.png'>
		</td><td>
		<a href='#' onClick='nuevo_gasto();'>
		A&ntilde;adir Gasto Nuevo...</a>
		</td></tr></table>
</div>


</div>

</td>
<td>
<div class='sub-content'>
<img src='iconos/page_white_magnify.png'>
Detalle del Gasto Parcializado
</div>

<div class='sub-content2' style='height:220px;overflow:auto;' id='listado_cc'>

</div>

</td>
</tr>
</table>
</form>

</div>

</td></tr></table>
</center>

<script> 
  listar_gastos();
</script>
