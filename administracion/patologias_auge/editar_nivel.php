<?php

  require_once('../../conectar_db.php');
  
  $detpat_id=$_GET['detpat_id']*1;
  $detpat_padre_id=$_GET['detpat_padre_id']*1;
  $pat_id=$_GET['pat_id']*1;
  $etapa=$_GET['etapa']*1;
  
  if($detpat_id==0) {
    
    $presta_codigo='';
    $presta_desc='&nbsp;';
    $ipd=''; $sigges='';
    $cantidad=''; $unidad=2; $edad=0; $edad2='';
    $redad=-1; $ufrec='I'; $sexo=-1; $excluyentes='';
    
    $canasta=false;
    
  } else {
  
    $nivel = cargar_registro("
    SELECT * FROM detalle_patauge 
    LEFT JOIN codigos_prestacion ON codigo=presta_codigo 
    WHERE detpat_id=$detpat_id
    ");
  
    $detpat_padre_id=$nivel['detpat_padre_id'];
    $presta_codigo=$nivel['presta_codigo'];
    $presta_desc=$nivel['glosa'];
    $ipd=$nivel['detpat_ipd']=='t'?'CHECKED':'';
    $sigges=$nivel['detpat_sigges']=='t'?'CHECKED':'';
    $cantidad=$nivel['detpat_plazo']*1;
    $unidad=$nivel['detpat_uplazo']*1;
    $edad=$nivel['detpat_edad']*1;
    $edad2=$nivel['detpat_edad2']*1;
    $redad=$nivel['detpat_redad']*1;
    $frec=$nivel['detpat_frec']*1;
    $ufrec=$nivel['detpat_ufrec'];
    $sexo=$nivel['detpat_sexo']*1;
    $excluyentes=$nivel['detpat_excluyentes'];
    
    $canasta=cargar_registros_obj("
      SELECT * FROM detalle_patcanasta
      JOIN codigos_prestacion USING (codigo) 
      WHERE detpat_id=$detpat_id
      ORDER BY codigo
    ");
  
  }
  
  $sexohtml = desplegar_opciones("sexo", 
	"sex_id, sex_desc",$sexo,'true','ORDER BY sex_id'); 

  
  print("
  <html>
  <title>Editar Nivel</title>
  ");
    
  cabecera_popup('../..');
  
?>

<script>

var canasta=<?php echo json_encode($canasta); ?>

ver_general = function() {

  tab_up('tab_general');
  tab_down('tab_canasta');
  tab_down('tab_mover');

}

ver_canasta = function() {

  tab_down('tab_general');
  tab_up('tab_canasta');
  tab_down('tab_mover');

}

ver_movs = function() {

  tab_down('tab_general');
  tab_down('tab_canasta');
  tab_up('tab_mover');

}

</script>

<body class='fuente_por_defecto popup_background'>

<form id='nivel' name='nivel' onSubmit='return false;'>

      <table cellpadding=0 cellspacing=0>
      <tr><td>
		  <div class='tabs' id='tab_general' style='cursor: default;' 
      onClick='ver_general();'>
      <img src='../../iconos/chart_line.png'>
      Prestaci&oacute;n/Trazadora</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_canasta' style='cursor: pointer;'
      onClick='ver_canasta();'>
      <img src='../../iconos/basket.png'>
      Canastas</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_mover' style='cursor: pointer;'
      onClick='ver_movs();'>
      <img src='../../iconos/table_refresh.png'>
      Mover Nodo</div>
		  </td>
      </tr>
      </table>


<div class='tabbed_content'
style='height:330px;overflow:auto;font-size:10px!important;'
id='tab_general_content'>

<div class='sub-content'>
<b>Datos Generales</b>
</div>

<div class='sub-content'>
<input type='hidden' id='detpat_padre_id' name='detpat_padre_id' 
value='<?php echo $detpat_padre_id; ?>'>
<input type='hidden' id='pat_id' name='pat_id' 
value='<?php echo $pat_id; ?>'>
<input type='hidden' id='detpat_id' name='detpat_id' 
value='<?php echo $detpat_id; ?>'>
<input type='hidden' id='etapa' name='etapa' 
value='<?php echo $etapa; ?>'>

<table style='width:100%;'>

<tr>
<td style='text-align:right;width:120px;'>
C&oacute;digo Prestaci&oacute;n:</td>
<td>
<input type='text' id='cod_presta' name='cod_presta'
value='<?php echo $presta_codigo; ?>'>
<input type='hidden' id='codigo_prestacion' name='codigo_prestacion'
value='<?php echo $presta_codigo; ?>'>
</td>
<td colspan=2 id='desc_presta'>
<?php echo $presta_desc; ?>
</td>
</tr>
<tr>
<td style='text-align:right;'>Plazo de Prestaci&oacute;n:</td>
<td colspan=3>
<input type='text' size=10 id='cant' name='cant' value='<?php if($unidad!=-1 AND $unidad!=-2) echo $cantidad; ?>'
<?php if($unidad==-1 OR $unidad==-2) echo 'DISABLED'; ?>>
<select id='unidad' name='unidad' onClick='
if(this.value==-1 || this.value==-2) {
  $("cant").disabled=true;
  $("cant").value="";
} else {
  $("cant").disabled=false;
}
'>
<option value='-2' <?php if($unidad==-2) echo 'SELECTED'; ?> >(No se Aplica...)</option>
<option value='0' <?php if($unidad==0) echo 'SELECTED'; ?> >Minutos</option>
<option value='1' <?php if($unidad==1) echo 'SELECTED'; ?> >Horas</option>
<option value='2' <?php if($unidad==2) echo 'SELECTED'; ?> >D&iacute;as</option>
<option value='3' <?php if($unidad==3) echo 'SELECTED'; ?> >Meses</option>
<?php if($detpat_padre_id!=0) { ?>
<option value='-1' <?php if($unidad==-1) echo 'SELECTED'; ?> >(Usar Rango Anterior...)</option>
<?php } ?>
</select>




</td>
</tr>
<tr>
<td style='text-align:right;'>
Generar I.P.D.:
</td>
<td>
<input type='checkbox' id='ipd' name='ipd' <?php echo $ipd;?> >
</td>
</tr>
<tr>
<td style='text-align:right;'>
Alimentar SIGGES:
</td>
<td>
<input type='checkbox' id='sigges' name='sigges' <?php echo $sigges; ?>>
</td>
</tr>

</table>

</div>

<div id='trazadoras'>
<div class='sub-content'>
<b>Restricciones/Trazadoras</b>
</div>

<div class='sub-content'>

<table style='width:100%;'>

<tr>
<td style='text-align:right;'>
Rango de Edad:
</td>
<td>
<select id='redad' name='redad'
onClick='
if(this.value==-1) {
  $("edad").disabled=true;
  $("edad").value="";
} else {
  $("edad").disabled=false;
  if(this.value==5) $("edad2").disabled=false; else 
  { $("edad2").disabled=true; $("edad2").value=""; }
}
'>
<option value=-1 <?php if($redad==-1) echo 'SELECTED'; ?>>(Cualquier edad...)</option>
<option value=1 <?php if($redad==1) echo 'SELECTED'; ?>>menor o igual</option>
<option value=2 <?php if($redad==2) echo 'SELECTED'; ?>>menor</option>
<option value=3 <?php if($redad==3) echo 'SELECTED'; ?>>mayor</option>
<option value=4 <?php if($redad==4) echo 'SELECTED'; ?>>mayor o igual</option>
<option value=5 <?php if($redad==5) echo 'SELECTED'; ?>>desde / hasta</option>
</select>
<input type='text' id='edad' name='edad' size=5 
<?php if($redad==-1) echo 'DISABLED'; else echo 'value="'.$edad.'"';?>>
<input type='text' id='edad2' name='edad2' size=5 
<?php if($redad!=5) echo 'DISABLED'; else echo 'value="'.$edad2.'"';?>>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Sexo:
</td>
<td>
<select id='sexo' name='sexo'>
<option value=-1 <?php if($sexo==-1) echo 'SELECTED'; ?>>(Cualquiera...)</option>
<?php echo $sexohtml; ?>
</select>
</td>
</tr>


<tr>
<td style='text-align:right;'>
Frecuencia:
</td>
<td>
<input type='text' id='frec' name='frec' size=5 
<?php if($ufrec=='I') echo 'DISABLED'; else echo 'value="'.$frec.'"'; ?>>
<select id='ufrec' name='ufrec'
onClick='
if(this.value=="I") {
  $("frec").disabled=true;
  $("frec").value="";
} else {
  $("frec").disabled=false;
}
'>
<option value='I' <?php if($ufrec=='I') echo 'SELECTED'; ?>>(Indefinida...)</option>
<option value='V' <?php if($ufrec=='V') echo 'SELECTED'; ?>>Vida</option>
<option value='M' <?php if($ufrec=='M') echo 'SELECTED'; ?>>Mes</option>
<option value='A' <?php if($ufrec=='A') echo 'SELECTED'; ?>>A&ntilde;o</option>
</select>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Prestaciones Excluyentes:
</td>
<td>
<input type='text' id='excluyentes' name='excluyentes' size=30
value='<?php echo $excluyentes; ?>'>
</td>
</tr>

</table>

</div>

</div>

</form>

</div>

<div class='tabbed_content'
style='height:330px;overflow:auto;font-size:10px!important;display:none;'
id='tab_canasta_content'>

<div class='sub-content'>
<table style='width:100%;'>
<tr><td style='text-align:right;'>Agregar C&oacute;digo:</td>
<td>
<input type='text' id='cod_presta2' name='cod_presta2' value=''>
<input type='hidden' id='codigo_prestacion2' name='codigo_prestacion2'
value=''>
</td>
<td id='desc_presta2' style='width:300px;'>

</td></tr>
</table>
</div>
<div style='height:250px;overflow:auto;' class='sub-content2' 
id='lista_canasta'>

</div>

</div>

<div class='tabbed_content'
style='height:330px;overflow:auto;font-size:10px!important;display:none;'
id='tab_mover_content'>
<center>
<table><tr><td style='text-align:right;'>Operaci&oacute;n:</td><td>
<select id='accion' name='accion'>
  <option value='mover_todo' SELECTED>Mover nodos descendientes y nodo padre.</option>
  <option value='mover_hijos'>Mover solo nodos descendientes.</option>
</select>
</td></tr></table>
</center>

<?php 

  $params=true;
  require_once('describir_patologia.php');
  
?>

</div>


<center>
  <table><tr><td>
		<div class='boton'>
		<table><tr><td>
		<img src='../../iconos/disk.png'>
		</td><td>
		<a href='#' onClick='guardar_nivel();'> Guardar Cambios...</a>
		</td></tr></table>
		</div>
		</td></tr></table>
		</div>
		</td></tr></table>
  </center>

</body>
</html>

<script>

    guardar_nivel = function() {
    
      if($('codigo_prestacion').value=='') {
        alert('Debe seleccionar un C&oacute;digo de Prestaci&oacute;n.'.unescapeHTML());
        return;
      }
      
      params='&canasta='+encodeURIComponent(canasta.toJSON());
      
      $('accion').disabled=true;
      var myAjax=new Ajax.Request(
      'sql_nivel.php',
      {
        method: 'post',
        parameters: $('nivel').serialize()+params,
        onComplete: function(resp) {
        
          if(resp.responseText=='') {

            var func = window.opener.detallar_patologia.bind(window.opener);
            func(window.opener.$('pat_id').value, window.opener.$('etapa').value);
            window.close();
            window.opener.focus();
          
          } else {
          
            alert(resp.responseText);
          
          }
        
        }
      }
      );
      $('accion').disabled=false;
      
          
    }
    
    mover_nivel = function(detpat_padre_id, etapa) {
      
      var params=$('accion').serialize()+'&detpat_id=<?php echo $detpat_id; ?>';
      params+='&detpat_padre_id='+detpat_padre_id+'&etapa='+etapa;
      
      var myAjax=new Ajax.Request(
      'sql_nivel.php',
      {
        method:'post',
        parameters: params,
        onComplete: function(resp) {
            
            var func = window.opener.detallar_patologia.bind(window.opener);
            func(window.opener.$('pat_id').value, window.opener.$('etapa').value);
            window.close();
            window.opener.focus();
        }
      }
      );
      
    }


    seleccionar_prestacion = function(presta) {
    
      $('codigo_prestacion').value=presta[0];
      $('desc_presta').innerHTML=presta[2];
      
      $('cant').focus();
      
    }

    seleccionar_prestacion2 = function(presta) {
    
      $('codigo_prestacion2').value=presta[0];
      $('desc_presta2').innerHTML=presta[2];
      
      fnd=false;
      
      for(var n=0;n<canasta.length;n++)
        if(presta[0]==canasta[n].codigo) fnd=true; 
      
      if(!fnd) {
        var num=canasta.length;
        canasta[num]=new Object();
        canasta[num].codigo=presta[0];
        canasta[num].glosa=presta[2];
        
        listar_canasta();
      }
      
      $('cod_presta2').select();
      $('cod_presta2').focus();
      
    }
    
    eliminar_canasta = function(n) {
    
      canasta=canasta.without(canasta[n]);
      
      listar_canasta();
      
    }
    
    listar_canasta = function() {
    
      var html='<table style="width:100%;font-size:11px;"><tr class="tabla_header">';
      html+='<td>C&oacute;digo</td><td>Glosa</td><td>Eliminar</td></tr>';
      
      if(canasta)
      for(var i=0;i<canasta.length;i++) {
        c=canasta[i]; clase=(i%2==0)?'tabla_fila':'tabla_fila2';
        
        html+='<tr class="'+clase+'" ';
        html+='onMouseOver="this.className=\'mouse_over\';" ';
        html+='onMouseOut="this.className=\''+clase+'\';"> ';
        html+='<td style="text-align:center;">';
        html+=c.codigo+'</td><td>'+c.glosa+'</td>';
        html+='<td><center><img src="../../iconos/delete.png" style="cursor:pointer;" ';
        html+='onClick="eliminar_canasta('+i+');"></center></td></tr>';
      }
      
      html+='</table>';
      
      $('lista_canasta').innerHTML=html;
    
    }


    autocompletar_prestaciones = new AutoComplete(
      'cod_presta', 
      '../../autocompletar_sql.php',
      function() {
        if($('cod_presta').value.length<3) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=prestacion&'+$('cod_presta').serialize()
        }
      }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_prestacion);

    autocompletar_prestaciones2 = new AutoComplete(
      'cod_presta2', 
      '../../autocompletar_sql.php',
      function() {
        if($('cod_presta2').value.length<3) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=prestacion&cod_presta='+encodeURIComponent($('cod_presta2').value)
        }
      }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_prestacion2);
      

    if(!canasta) canasta=new Array();
    else listar_canasta();
      
    $('cod_presta').select();
    $('cod_presta').focus();

</script>