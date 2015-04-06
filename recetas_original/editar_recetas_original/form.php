<?php

  require_once('../../conectar_db.php');
  
  if(isset($_GET['visualizar'])) {
    $visualizar=true;
    $edicion='';
    $eliminar=false;
  } else {
    $visualizar=false;
    $edicion='edicion&';
    $eliminar=false;
  }

  if(isset($_GET['eliminar'])) {
    $visualizar=false;
    $edicion='';
    $eliminar=true;

  }

  $receta_id=($_GET['receta_id']*1);  
  
  $doctoreshtml = desplegar_opciones("doctores", "doc_rut, doc_paterno || ' ' || doc_materno || ' ' || doc_nombres AS nombre", '1', '1=1', 'ORDER BY nombre');
  
  $receta_q = pg_query($conn, "
  SELECT
  date_trunc('second', receta_fecha_emision),
  pac_rut,
  pac_appat || ' ' || pac_apmat || ' ' || pac_nombres,
  doc_rut,
  receta_diag_cod,
  diag_desc,
  receta_comentarios,
  receta_cronica,
  receta_paciente_id,
  doc_paterno || ' ' || doc_materno || ' ' || doc_nombres,
  tipotalonario_id,
  tipotalonario_nombre,
  receta_numero,
  receta_nro_du
  FROM receta
  JOIN pacientes ON receta_paciente_id=pac_id
  JOIN doctores ON receta_doc_id=doc_id
  LEFT JOIN diagnosticos ON receta_diag_cod=diag_cod
  LEFT JOIN receta_tipo_talonario ON receta_tipotalonario_id=tipotalonario_id
  WHERE receta_id=".$receta_id."
  ");


  if(pg_num_rows($receta_q)==1) {
    $receta_a = pg_fetch_row($receta_q);
  } else {
    die('Error Inesperado.');
  }

   if ($receta_a[10]==0 or $receta_a[10]='' ){

       if ($receta_a[7]=='f'){
          $receta_a[11]='Receta Aguda';
        }else{
         $receta_a[11]='Receta Cronica';
         }
   }

?>

<script>



  __abrir_paciente = function() {

  $('__cargar_paciente').style.display='';

  var myAjax = new Ajax.Request(
  'recetas/ver_recetas/abrir_paciente.php',
  {
    method: 'get',
    parameters: 'pac_rut='+$('__rut_paciente').value,
    onComplete: function(respuesta) {

      $('__cargar_paciente').style.display='none';

      try {
      datos = respuesta.responseText.evalJSON(true);
      } catch (err) {
      alert('ERROR:\n\n'+err);
      }

      if(datos!=false) {
        $('__id_paciente').value=datos[1][0];
        $('__nombre_paciente').style.display='';
        $('__nombre_paciente').innerHTML=datos[1][1];
      } else {
        $('__id_paciente').value=-1;
        $('__nombre_paciente').style.display='none';
        $('__nombre_paciente').innerHTML='';
      }

    }
  }
  );

  }

  __abrir_diag = function() {

  $('__cargar_diag').style.display='';

  var myAjax = new Ajax.Request(
  'recetas/ver_recetas/abrir_diagnostico.php',
  {
    method: 'get',
    parameters: 'diag_cod='+$('__codigo_diag').value,
    onComplete: function(respuesta) {

      $('__cargar_diag').style.display='none';

      try {
      datos = respuesta.responseText.evalJSON(true);
      } catch (err) {
      alert('ERROR:\n\n'+err);
      }

      if(datos!=false) {
        $('__id_diag').value=datos[1][0];
        $('__nombre_diag').style.display='';
        $('__nombre_diag').innerHTML=datos[1][1];
      } else {
        $('__id_diag').value=-1;
        $('__nombre_diag').style.display='none';
        $('__nombre_diag').innerHTML='';
      }

    }
  }
  );

  }
//**************************************************************************

  __eliminar_receta = function() {
	  
	  var motivo=prompt('Motivo:');
	  
	  if(motivo=='' || motivo==null) return;

     var myAjax = new Ajax.Request(
    'recetas/editar_recetas/sql_borrar_receta.php',
    {
      method: 'get',
      parameters: 'receta_id=<?php echo $receta_id;?>&motivo='+motivo+'&user=<?php echo ($_SESSION['sgh_usuario_id']); ?>',
      onComplete: function (respuesta) {

        try {
          datos = respuesta.responseText.evalJSON(true);
        } catch(err) {
          alert('ERROR:\n\n'+err);
          return;
        }

        if(datos) {
          alert('Anulaci&oacute;n realizada exitosamente.'.unescapeHTML());
          $("editar_recetas").objeto.close();
          	$('win_informe').win_obj = win;

            win.setDestroyOnClose();
            win.showCenter();
            win.show();
            generar_informe();

        } else {

          alert('No es posible eliminar la receta,esta asociada a reposici&oacute;n'.unescapeHTML());
        }

      }
    }

    );

  }

  //===============================================================

  __imprime_receta = function() {

     var myAjax = new Ajax.Request(
    'recetas/editar_recetas/imprime_receta.php',
    {
      method: 'get',
      parameters: $('bodega_id').serialize()+'&'+'receta_id=<?php echo $receta_id;?>',
      onComplete: function(informe) {

        imprimirHTML(informe.responseText);

        consulta_enviada=false;

      }

     }

        );

  }

  //===============================================================

  __guardar_receta = function() {

    __paciente = $('__id_paciente');
    __diagnostico = $('__id_diag');
    __medico = $('nombre_medico');

    var myAjax = new Ajax.Request(
    'recetas/editar_recetas/sql.php',
    {
      method: 'get',
      parameters: $('__receta').serialize()+'&'+$('bodega_id').serialize(),
      onComplete: function (respuesta) {
     // alert($('__receta').serialize);


        try {
          datos = respuesta.responseText.evalJSON(true);
        } catch(err) {
          alert('ERROR:\n\n'+err);
          //alert('Lote sin saldo');
          return;
        }

        if(datos) {
          alert('Modificaci&oacute;n realizada exitosamente.'.unescapeHTML());
          $("editar_recetas").objeto.close();
        } else {
          alert('ERROR:\n\n'+respuesta.responseText);
        }

      }
    }

    );

  }

      __nombre2rut = function() {

        $('__rut_medico').value=$('__nombre_medico').value;

      }

      __rut2nombre = function() {

        $('__rut_medico').value=trim($('__rut_medico').value);

        valor = $('__rut_medico').value;

        opciones = $('__nombre_medico').options;

        for(i=0;i<opciones.length;i++) {

          if(valor==opciones[i].value) {
            $('__nombre_medico').value=valor;
            return;
          }

        }

        $('__nombre_medico').value=-1;

      }

      __recetad_actual=0;

      __calcular_dif = function(id_recetad) {

        __fila_recetad = document.getElementById('fila_recetad_'+id_recetad);

        __cols = __fila_recetad.getElementsByTagName('td');
        __inputs = __fila_recetad.getElementsByTagName('input');

        if(__inputs[1].value>24)  __inputs[1].value=24;
        if(__inputs[1].value<1)   __inputs[1].value=1;

        __cols[5].innerHTML=((__inputs[2].value*24)/__inputs[1].value*__inputs[0].value);

        // Cantidad entregada es producto de los campos
        // sumados de las entradas individuales...

        _id_logs = $('logs_recetad_'+id_recetad).value.split('|');

       /* ____sumar=0;

        for(a=0;a<(_id_logs.length);a++) {

          ____sumar+=($('cant_log_'+_id_logs[a]).value*1);


        }*/

       // __cols[6].innerHTML=____sumar;

        // Calcular Diferencia

       // __cols[7].innerHTML=(__cols[5].innerHTML-__cols[6].innerHTML);

       __cols[7].innerHTML=(__cols[6].innerHTML-__cols[5].innerHTML);
       //diferencia          entregado     - recetado
      }

      __log_actual=0;

      __editar_log = function(id_log) {

        __fila_log = document.getElementById('fila_log_'+id_log);
        __cols = __fila_log.getElementsByTagName('td');

       // __cant = __cols[4].innerHTML*1;

        //__cols[4].innerHTML='<input type="text" id="cant_log_'+id_log+'" name="cant_log_'+id_log+'" size=6 style="text-align: right;" value="'+__cant+'" onKeyUp="__calcular_dif('+__recetad_actual+');">';


      }

      __aceptar_log = function(id_log) {

        __fila_log__ = document.getElementById('fila_log_'+id_log);
        __cols__ = __fila_log__.getElementsByTagName('td');

        //__cant__ = ($('cant_log_'+id_log).value*1);

       /* __cols__[4].innerHTML=__cant__;

        if(($('valor_log_'+id_log).value*1)!=__cant__) {
          __cols__[4].style.color='red';
        } else {
          __cols__[4].style.color='';
        } */

      }

      __cancelar_log = function(id_log) {

        __fila_log__ = document.getElementById('fila_log_'+id_log);
        __cols__ = __fila_log__.getElementsByTagName('td');

       // __cols__[4].innerHTML=$('valor_log_'+id_log).value;

       // __cols__[4].style.color='';

      }

      __aceptar_recetad = function(id_recetad) {

          __fila_recetad_act = document.getElementById('fila_recetad_'+id_recetad);

          __cols_act = __fila_recetad_act.getElementsByTagName('td');


          if((__cols_act[7].innerHTML*1)<0) {

            alert('La diferencia entre el medicamento recetado y la cantidad despachada no puede ser negativa.');

            return;

          }

          $('__cant_n_'+id_recetad).value=$('cant_recetad_'+id_recetad).value;
          $('__horas_n_'+id_recetad).value=$('horas_recetad_'+id_recetad).value;
          $('__dias_n_'+id_recetad).value=$('dias_recetad_'+id_recetad).value;


          __cols_act[2].innerHTML=($('cant_recetad_'+id_recetad).value*1);
          __cols_act[3].innerHTML=($('horas_recetad_'+id_recetad).value*1);
          __cols_act[4].innerHTML=($('dias_recetad_'+id_recetad).value*1);

          if($('__cant_n_'+id_recetad).value!=$('__cant_'+id_recetad).value)
          __cols_act[2].style.color='red';
          else
          __cols_act[2].style.color='';

          if($('__horas_n_'+id_recetad).value!=$('__horas_'+id_recetad).value)
          __cols_act[3].style.color='red';
          else
          __cols_act[3].style.color='';

          if($('__dias_n_'+id_recetad).value!=$('__dias_'+id_recetad).value)
          __cols_act[4].style.color='red';
          else
          __cols_act[4].style.color='';


          $('_edita_recetad_'+id_recetad).style.display='';
          $('_acepta_recetad_'+id_recetad).style.display='none';
          $('_cancela_recetad_'+id_recetad).style.display='none';

          __recetad_actual=0;


         _id_logs = $('logs_recetad_'+id_recetad).value.split('|');

          for(a=0;a<(_id_logs.length);a++) {

            __aceptar_log(_id_logs[a]);

          }


      }

      __cancelar_recetad = function(id_recetad) {

          __fila_recetad_act = document.getElementById('fila_recetad_'+id_recetad);

          __cols_act = __fila_recetad_act.getElementsByTagName('td');

          __cols_act[2].innerHTML=
          ($('__cant_'+id_recetad).value*1);
          __cols_act[3].innerHTML=
          ($('__horas_'+id_recetad).value*1);
          __cols_act[4].innerHTML=
          ($('__dias_'+id_recetad).value*1);

          $('__cant_n_'+id_recetad).value=$('__cant_'+id_recetad).value;
          $('__horas_n_'+id_recetad).value=$('__horas_'+id_recetad).value;
          $('__dias_n_'+id_recetad).value=$('__dias_'+id_recetad).value;

          __cols_act[2].style.color='';
          __cols_act[3].style.color='';
          __cols_act[4].style.color='';

          $('_edita_recetad_'+id_recetad).style.display='';
          $('_acepta_recetad_'+id_recetad).style.display='none';
          $('_cancela_recetad_'+id_recetad).style.display='none';

          __recetad_actual=0;

          _id_logs = $('logs_recetad_'+id_recetad).value.split('|');

          for(a=0;a<(_id_logs.length);a++) {

            __cancelar_log(_id_logs[a]);

          }
      }

      __editar_recetad = function(id_recetad) {

        __fila_recetad = document.getElementById('fila_recetad_'+id_recetad);

        __cols = __fila_recetad.getElementsByTagName('td');

        if (__recetad_actual!=0) {
          __cancelar_recetad(__recetad_actual);
        }

        __cant = __cols[2].innerHTML;

        __cols[2].innerHTML='<center><input type="text" id="cant_recetad_'+id_recetad+'" name="cant_recetad_'+id_recetad+'" size=4 value='+__cant+'  style="text-align: right;" onChange="__calcular_dif('+id_recetad+');"  onKeyUp="__calcular_dif('+id_recetad+');"></center>';

        __horas = __cols[3].innerHTML;

        __cols[3].innerHTML='<center><input type="text" id="horas_recetad_'+id_recetad+'" name="horas_recetad_'+id_recetad+'" size=4 value='+__horas+' style="text-align: right;" onChange="__calcular_dif('+id_recetad+');" onKeyUp="__calcular_dif('+id_recetad+');"></center>';

        __dias = __cols[4].innerHTML;

        __cols[4].innerHTML='<center><input type="text" id="dias_recetad_'+id_recetad+'" name="dias_recetad_'+id_recetad+'" size=4 value='+__dias+' style="text-align: right;" onChange="__calcular_dif('+id_recetad+');" onKeyUp="__calcular_dif('+id_recetad+');"></center>';

        __recetad_actual=id_recetad;

        $('_edita_recetad_'+id_recetad).style.display='none';
        $('_acepta_recetad_'+id_recetad).style.display='';
        $('_cancela_recetad_'+id_recetad).style.display='';
       
        $('cant_recetad_'+id_recetad).select();

        _id_logs = $('logs_recetad_'+id_recetad).value.split('|');
        
        for(a=0;a<(_id_logs.length);a++) {
        
          __editar_log(_id_logs[a]);
          
        }
        
      }

      cargar_medicamentos = function() {
      
        var myAjax = new Ajax.Updater(
        '__medicamentos',
        'recetas/editar_recetas/mostrar_medicamentos.php',
        {
          method: 'get',
          parameters: '<?php echo $edicion; ?>receta_id=<?php echo $receta_id;?>',
        }
        );
      
      }

</script>


<?php
  if($visualizar) {
?>

<table width=100%>
    <tr>
      <td style='text-align: right; font-style: italic; width:150px;'>
        Fecha de Emisi&oacute;n:</td>
      <td colspan=2>
        <b><?php echo htmlentities($receta_a[0])?></b>
        </td>
    </tr>
    <tr>
		<td>N&uacute;mero de Receta:        
        <b><?php echo htmlentities($receta_a[12])?></b>
        </td>
    </tr>

<?php
  if($receta_a[10]!=0) {
?>

    <tr>
      <td style='text-align: right; font-style: italic; width:150px;'>
        N&uacute;mero de Receta:
        <b><?php echo htmlentities($receta_a[12])?></b>
        </td>
    </tr>

<?php
  }
?>

    <tr>
      <td style='text-align: right; font-style: italic;'>
        RUT Paciente:</td>
      <td style='text-align: center; font-weight: bold; font-style: italic; font-size: 12px; width: 100px;'>
      <?php echo htmlentities($receta_a[1])?>
      </td>
      <td style='text-align: left; font-weight: bold; font-style: italic; font-size: 12px;'>
      <?php echo htmlentities($receta_a[2])?>
      </td>
    </tr>
    <tr>
      <td style='text-align: right; font-style: italic;'>
        RUT M&eacute;dico:</td>
      <td style='text-align: center; font-weight: bold; font-style: italic; font-size: 12px;'>

      <?php echo htmlentities($receta_a[3])?>
      </td>
      <td style='text-align: left; font-weight: bold; font-style: italic; font-size: 12px;'>
      <?php echo htmlentities($receta_a[9])?>
      </td>
    </tr>
    <tr>
      <td style='text-align: right; font-style: italic;' valign='top'>
        C&oacute;digo Diag.:</td>
      <td valign='top' colspan=2>
      <i>[<?php echo htmlentities($receta_a[4])?>]</i>
      <span style='text-align: left; font-weight: bold; font-style: italic; font-size: 12px;'>
      <?php echo htmlentities($receta_a[5])?>
      </span>
      </td>
    </tr>
    <tr>
      <td style='text-align: right; font-style: italic;' valign='top'>
        Observaciones:</td>
      <td colspan=2>
      <?php echo htmlentities($receta_a[6])?>
      </td>
    </tr>





<?php
  } else if($eliminar) {
?>
  <table width=100%>
    <tr>
      <td style='text-align: right; font-style: italic; width:150px;'>
        Fecha de Emisi&oacute;n:</td>
      <td colspan=2>
        <b><?php echo htmlentities($receta_a[0])?></b>
        </td>
    </tr>
    <tr>
      <td style='text-align: right; font-style: italic; width:150px;'>
        N&uacute;mero D.U.:</td>
      <td colspan=2>
        <b><?php echo htmlentities($receta_a[13])?></b>
        </td>
    </tr>

<?php
  if($receta_a[10]!=0) {
?>

    <tr>
      <td style='text-align: right; font-style: italic; width:150px;'>
        N&uacute;mero de Receta:
        <b><?php echo htmlentities($receta_a[12])?></b>
        </td>
    </tr>

<?php
  }
?>

    <tr>
      <td style='text-align: right; font-style: italic;'>
        RUT Paciente:</td>
      <td style='text-align: center; font-weight: bold; font-style: italic; font-size: 12px; width: 100px;'>
      <?php echo htmlentities($receta_a[1])?>
      </td>
      <td style='text-align: left; font-weight: bold; font-style: italic; font-size: 12px;'>
      <?php echo htmlentities($receta_a[2])?>
      </td>
    </tr>
    <tr>
      <td style='text-align: right; font-style: italic;'>
        RUT M&eacute;dico:</td>
      <td style='text-align: center; font-weight: bold; font-style: italic; font-size: 12px;'>

      <?php echo htmlentities($receta_a[3])?>
      </td>
      <td style='text-align: left; font-weight: bold; font-style: italic; font-size: 12px;'>
      <?php echo htmlentities($receta_a[9])?>
      </td>
    </tr>
    <tr>
      <td style='text-align: right; font-style: italic;' valign='top'>
        C&oacute;digo Diag.:</td>
      <td valign='top' colspan=2>
      <i>[<?php echo htmlentities($receta_a[4])?>]</i>
      <span style='text-align: left; font-weight: bold; font-style: italic; font-size: 12px;'>
      <?php echo htmlentities($receta_a[5])?>
      </span>
      </td>
    </tr>
    <tr>
      <td style='text-align: right; font-style: italic;' valign='top'>
        Observaciones:</td>
      <td colspan=2>
      <?php echo htmlentities($receta_a[6])?>
      </td>
    </tr>


<?php
  } else {
?>

<form id='__receta' name='__receta'>
<input type='hidden' id='__receta_id' name='__receta_id' value=<?php echo $receta_id?>>

  <table width=100%>
    <tr>
      <td style='text-align: right; font-style: italic; width:150px;'>
        Fecha de Emisi&oacute;n:</td>
      <td colspan=2>
        <?php echo htmlentities($receta_a[0])?></td>
    </tr>

    <tr>
      <td style='text-align: right; font-style: italic; width:150px;'>
        Tipo de Receta:</td>
      <td colspan=2>
        <b><?php echo htmlentities($receta_a[11])?></b>
        </td>
    </tr>
    
<?php
  if($receta_a[10]!=0) {
?>
    
    <tr>
      <td style='text-align: right; font-style: italic; width:150px;'>
        N&uacute;mero de Receta:
        <b><?php echo htmlentities($receta_a[12])?></b>
        </td>
    </tr>
    
<?php
  }
?>
    
    <tr>
      <td style='text-align: right; font-style: italic;'>
        RUT Paciente:</td>
      <td>
      <input type='hidden' id='__id_paciente' name='__id_paciente' 
      value=<?php echo $receta_a[8]?>>
      <center>
      <input type='text' id='__rut_paciente' name='__rut_paciente' size=10
      style='text-align: center;' onChange='__abrir_paciente();'
      onKeyUp='if(event.which==13) __abrir_paciente(); '
      value='<?php echo htmlentities($receta_a[1])?>'>
      <img src='iconos/zoom_in.png' style='cursor: pointer;'
      onClick='
      buscar_pacientes("__rut_paciente", function() { __abrir_paciente(); });
      '
      alt='Buscar Paciente...'
      title='Buscar Paciente...'>
      </center>
      </td>
      <td style='text-align: left;'>
      <img src='imagenes/ajax-loader1.gif' id='__cargar_paciente'
      style='display: none;'>
      <span id='__nombre_paciente' class='texto_tooltip'>
      <?php echo htmlentities($receta_a[2])?>
      </span>
      </td>
    </tr>
    <tr>
      <td style='text-align: right; font-style: italic;'>
        RUT M&eacute;dico:</td>
      <td style='width:130px;'>
      <center>
      <input type='text' id='__rut_medico' name='__rut_medico' size=10
      style='text-align: center;' onChange='__rut2nombre();'
      value='<?php echo htmlentities($receta_a[3])?>'>
      <img src='iconos/zoom_in.png'
      alt='Buscar M&eacute;dico...'
      text='Buscar M&eacute;dico...'>
      </center>
      </td>
      <td style='text-align: left;'>
      <select id='__nombre_medico' name='__nombre_medico'
      onChange='__nombre2rut();'>
      <option value=-1>(Seleccionar...)</option>
      <?php echo $doctoreshtml?>
      </select>

      </td>
    </tr>
    <tr>
      <td style='text-align: right; font-style: italic;' valign='top'>
        C&oacute;digo Diag.:</td>
      <td valign='top'>
      <center>
      <input type='text' id='__codigo_diag' name='codigo_diag' size=5
      style='text-align: center;' onChange='__abrir_diag();'
      value='<?php echo htmlentities($receta_a[4])?>'>
      <input type='hidden' id='__id_diag' name='__id_diag'
      value='<?php echo htmlentities($receta_a[4])?>'>
      <img src='iconos/zoom_in.png' style='cursor: pointer;'
      onClick='
      buscar_diagnosticos("__codigo_diag", function() { __abrir_diag(); } );'
      alt='Buscar Diagn&oacute;stico...'
      text='Buscar Diagn&oacute;stico...'>
      </center>
      </td>
      <td style='text-align: left; font-weight: bold; font-style: italic; font-size: 12px;'>
      <img src='imagenes/ajax-loader1.gif' id='__cargar_diag'
      style='display:none;'>
      <span id='__nombre_diag'>
      <?php echo htmlentities($receta_a[5])?>
      </span>
      </td>
    </tr>
    <tr>
      <td style='text-align: right;' valign='top'>
        Observaciones:</td>
      <td colspan=2>
      <textarea id='observaciones' name='observaciones' cols=55 rows=2><?php echo htmlentities($receta_a[6])?></textarea>
      </td>
    </tr>


<?php
    }
?>

     <tr><td colspan=3>
    <div class='sub-content3' style='height: 180px; overflow: auto;'
    id='__medicamentos' name='__medicamentos'>

    </div>
    </td></tr>
    <tr>
    <td colspan=3>




<?php

  if ((!$visualizar) and (!$eliminar)) {

?>

<center>

<table>
  <tr><td>

  <div class='boton' id='__guardar_boton'>
	<table><tr><td>
	<img src='iconos/pill_go.png'>
	</td><td>
	<a href='#' onClick='__guardar_receta();'> Guardar Cambios...</a>
	</td></tr></table>
	</div>
  </center>

  </td><td>

  <div class='boton' id='__cancelar_boton'>
	<table><tr><td>
	<img src='iconos/pill_delete.png'>
	</td><td>
	<a href='#' onClick='$("editar_recetas").objeto.close();'> Cancelar Modificaciones...</a>
	</td></tr></table>
	</div>

	</td></tr></table>

</center>
</form>

<?php
    }
?>

<?php

  if ($eliminar) {

?>

<center>

<table>
  <tr><td>

  <div class='boton' id='__guardar_boton'>
	<table><tr><td>
	<img src='iconos/delete.png'>
	</td><td>
	<a href='#' onClick='__eliminar_receta();'> Eliminar Receta...</a>
	</td></tr></table>
	</div>
  </center>

  </td><td>

  <div class='boton' id='__cancelar_boton'>
	<table><tr><td>
	<img src='iconos/cross.png'>
	</td><td>
	<a href='#' onClick='$("editar_recetas").objeto.close();'> Cancelar ...</a>
	</td></tr></table>
	</div>

	</td></tr></table>

</center>
</form>

<?php
    }
?>


<?php
 //==========================================================
  if ($visualizar) {

?>

<center>

<table>
  <tr><td>

  <div class='boton' id='__guardar_boton'>
	<table><tr><td>
	<img src='iconos/printer.png'>
	</td><td>
	<a href='#' onClick='__imprime_receta();'> Imprime Receta...</a>
	</td></tr></table>
	</div>
  </center>

</table>

</center>
</form>

<?php
    }   //==========================================================
?>





<script>

<?php
if ((!$visualizar) and (!$eliminar)) {
?>

__rut2nombre();

<?php
  }
?>

cargar_medicamentos();

</script>

