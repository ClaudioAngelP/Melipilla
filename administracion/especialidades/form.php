<?php

  require_once('../../conectar_db.php');

?>

<script>

    cargar_listado=function() {
    
      var myAjax=new Ajax.Updater(
      'tabla','administracion/especialidades/listar_especialidades.php'
      );
    
    }           
    
    
    
    cambiar_esp=function(obj_name)
    {
        var data=obj_name.split('_');
        var esp_id=data[1];
	var accion=((data[0]=='receta')?'0':'1');
	var val=($(obj_name).checked?'1':'0');
        var myAjax=new Ajax.Request('administracion/especialidades/sql_especialidades.php',
        {
            method:'post',
            parameters: 'esp_id='+esp_id+'&accion='+accion+'&valor='+val, onComplete:function(r) {
                listar_especialidades();
            }
        });
    }
    
    
    
    /*
    editar=function(esp_id) {
    
      var l=screen.availWidth/2-300;
      var t=screen.availHeight/2-150;
    
      var win=window.open(
      'administracion/especialidades/editar_especialidades.php'+
      '?esp_id='+esp_id,
        'editar_especialidad',
      'scrollbars=no, toolbar=no, left='+l+', top='+t+
      'location=no, directories=no, status=no,'+
      'menubar=no, resizable=no, width=600, height=300');
    
      win.focus();
    
    }
    
    agregar=function(esp_padre_id) {

      var l=screen.availWidth/2-300;
      var t=screen.availHeight/2-150;
    
      var win=window.open(
      'administracion/especialidades/editar_especialidades.php'+
      '?esp_id=0&esp_padre_id='+esp_padre_id,
        'editar_especialidad',
      'scrollbars=no, toolbar=no, left='+l+', top='+t+
      'location=no, directories=no, status=no,'+
      'menubar=no, resizable=no, width=600, height=300');
    
      win.focus();
    
    }
    
    */
    
    

</script>
<center>
    <div class='sub-content' style='width:750px;'>
        <div class='sub-content'>
            <img src='iconos/chart_line.png'> <b>Especialidades</b>
        </div>
        <div class='sub-content2' id='tabla' name='tabla' style='height:400px;overflow:auto;'>
        </div>
    </div>
</center>
<script>
    cargar_listado();
</script>