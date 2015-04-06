<?php
    require_once('../../conectar_db.php');
    $doc_id=$_GET['doc_id']*1;
    $esp_id=$_GET['esp_id']*1;
    $fecha=pg_escape_string($_GET['fecha']);
    
    $fechas = cargar_registros_obj("
    SELECT DISTINCT
    date_trunc('day', cupos_fecha) AS cupos_fecha,
    cupos_horainicio, cupos_horafinal, cupos_id, 
    cupos_cantidad_n, cupos_cantidad_c, cupos_ficha, cupos_adr,
    esp_desc, nom_motivo,nom_tipo_contrato
    FROM cupos_atencion
    JOIN especialidades ON esp_id=cupos_esp_id
    left join nomina on cupos_atencion.nom_id=nomina.nom_id
    WHERE cupos_doc_id=$doc_id AND cupos_fecha='$fecha' and cupos_esp_id=$esp_id;
    ", true);
    
    if($fechas)
    {
        $string_cupos_id="";
        for($i=0;$i<count($fechas);$i++)
        {
            if($i==0)
            {
                $string_cupos_id=$fechas[$i]['cupos_id'];
            }
            else
            {
                $string_cupos_id.="|".$fechas[$i]['cupos_id'];
            }
        }
    }

    $fechas_ocupadas = cargar_registros_obj("
    SELECT DISTINCT
    date_trunc('day', cupos_fecha) AS cupos_fecha,
    cupos_horainicio, cupos_horafinal, cupos_id, 
    cupos_cantidad_n, cupos_cantidad_c, cupos_extras,
    esp_desc
    FROM cupos_atencion
    JOIN especialidades ON esp_id=cupos_esp_id
    WHERE cupos_doc_id=$doc_id;
    ", true);

    $fechas_ausencias = cargar_registros_obj("
    SELECT DISTINCT
    ausencia_fechainicio, ausencia_fechafinal
    FROM ausencias_medicas
    WHERE (doc_id=$doc_id OR doc_id=0);
    ", true);
  
    $fechas_ausente=Array();
  
    for($i=0;$i<count($fechas_ausencias);$i++)
    {
        if($fechas_ausencias[$i]['ausencia_fechafinal']=='')
            $fechas_ausente[count($fechas_ausente)]=$fechas_ausencias[$i]['ausencia_fechainicio'];
        else
        {
            $finicio=explode('/',$fechas_ausencias[$i]['ausencia_fechainicio']);
            $ffinal=explode('/',$fechas_ausencias[$i]['ausencia_fechafinal']);
            $fi=mktime(0,0,0,$finicio[1],$finicio[0],$finicio[2]);
            $ff=mktime(0,0,0,$ffinal[1],$ffinal[0],$ffinal[2]);
            for(;$fi<=$ff;$fi+=86400)
            {
                $fechas_ausente[count($fechas_ausente)]=date('d/m/Y',$fi);
            }
        }
    }
?>
<html>
    <title>Replicar Agenda M&eacute;dica</title>
    <?php cabecera_popup('../..'); ?>
    <script>
        var fechas_ocupadas = <?php echo json_encode($fechas_ocupadas); ?>;
        var fechas_ausente = <?php echo json_encode($fechas_ausente); ?>;
        
        function estado_fecha(date, y, m, d)
        {
            // Devuelve 'disabled' para desactivar la fecha...
            // Devuelve false para dejar la fecha intacta...
            // Devuelve '(string)' para usar esa clase...
            var clase='';
            var fecha=d+'/'+(m+1)+'/'+y;
            if(d<10)
                d='0'+d;
            if((m+1)<10)
                m='0'+(m+1);
            else
                m=(m+1);
            
            var fecha2=d+'/'+m+'/'+y+' 00:00:00';
            var fecha3=d+'/'+m+'/'+y;
            
            for(var i=0;i<fechas_ausente.length;i++)
            {
                if(fecha3==fechas_ausente[i]) clase='ausente disabled';
            }
    
            for(var i=0;i<fechas_ocupadas.length;i++)
            {
                if(fecha2==fechas_ocupadas[i].cupos_fecha)
                    clase='fechaset';
            }
            if(clase=='')
                return false;
            else
                return clase;
        }
        
        
        
        check_examen=function(cupo_id)
        {
            if($j('#chk_cupo_'+cupo_id).is(':checked'))
            {
                if($j('#cupos_id').val()=='')
                {
                    $j('#cupos_id').val(cupo_id);
                }
                else
                {
                    $j('#cupos_id').val($j('#cupos_id').val()+'|'+cupo_id);
                }
            }
            else
            {
                if($j('#cupos_id').val()!='')
                {
                    var cupos=$j('#cupos_id').val().split('|');
                    $j('#cupos_id').val('');
                    for(var i=0;i<cupos.length;i++)
                    {
                        if(cupos[i]!=cupo_id)
                        {
                            if($j('#cupos_id').val()=='')
                            {
                                $j('#cupos_id').val(cupos[i]);
                            }
                            else
                            {
                                $j('#cupos_id').val($j('#cupos_id').val()+'|'+cupos[i]);
                            }
                        }
                        
                    }
                    
                }
                /*                if($('txt_nomd_id').value!='0')
                {
                    if($('txt_examenes').value!='')
                {
                    var exam = $('txt_examenes').value.split('|');
                    $('txt_examenes').value='';
                    if((exam.length)>1)
                    {
                        for(var i=0;i<exam.length;i++)
                        {
                            if(exam[i]!=sol_examd_id)
                            {
                                if($('txt_examenes').value=='')
                                {
                                    $('txt_examenes').value=exam[i];
                                }
                                else
                                {
                                    $('txt_examenes').value=$('txt_examenes').value+'|'+exam[i];
                                }
                            }
                        }
                    }
                    else
                    {
                        $('txt_nomd_id').value=0;
                    }
                }
                else
                {
                    $('txt_nomd_id').value=0;
                }
                */
            }
        }
     //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    </script>
    

<style>

  .fechaset {
    background-color: #99EE99;
    font-weight:bold;
    color: #FFAAAA;
  }
  
  .horas {
  
    background-color: #FFFFFF;
    border: 1px solid black;
  
  }
  
  .horas td {
    border: 1px solid black;
  }
  
  .libre {
    background-color: #99EE99;
  }
  
  .ocupado {
    background-color: #DDDDDD;
  }

  .ausente {
    background-color: black;
  }

</style>

<body class='fuente_por_defecto popup_background'>

<form id='replicar' name='replicar' method='post' action='sql_replicar.php'>

<input type='hidden' id='doc_id' name='doc_id' value='<?php echo $doc_id; ?>'>
<input type='hidden' id='fecha' name='fecha' value='<?php echo $fecha; ?>'>
<input type='hidden' id='esp_id' name='esp_id' value='<?php echo $esp_id; ?>'>
<input type='hidden' id='cupos_id' name='cupos_id' value='<?php echo $string_cupos_id; ?>'>
<div class='sub-content'>
<img src='../../iconos/calendar_delete.png'>
<b>Replicar Agenda M&eacute;dica del <?php echo $fecha; ?></b>
</div>

<div class='sub-content2' id='lista' style='height:85px;overflow:auto;'>
    <table style="width:100%;font-size:12px;">
        <tr class="tabla_header">
            <td>&nbsp;</td>
            <td>Inicio</td>
            <td>T&eacute;rmino</td>
            <td>C</td>
            <td>E</td>
            <td>F</td>
            <td>ADR</td>
            <td>Especialidad</td>
            <td>Tipo Atenci&oacute;n</td>
            <td>Tipo Contrato</td>
            <td>Acci&oacute;n</td>
        </tr>
        <?php 
        for($i=0;$i<count($fechas);$i++)
        {
            $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
            if($fechas[$i]['cupos_ficha']=='t') 
                $f='<center><img src="../../iconos/tick.png" width=8 height=8></center>';
            else
                $f='<center><img src="../../iconos/cross.png" width=8 height=8></center>';

            if($fechas[$i]['cupos_adr']=='t')
                $a='<center><img src="../../iconos/tick.png" width=8 height=8></center>';
            else
                $a='<center><img src="../../iconos/cross.png" width=8 height=8></center>';
            ?>
            
            <tr class="<?php echo $clase; ?>" onMouseOver="this.className='mouse_over';" onMouseOut="this.className='<?php echo $clase; ?>';">
                <td style="text-align:center;"><center><input type="checkbox" id="chk_cupo_<?php echo $fechas[$i]['cupos_id'];?>" name="chk_cupo_<?php echo $fechas[$i]['cupos_id'];?>" onchange="check_examen(<?php echo $fechas[$i]['cupos_id'];?>);" checked=""/></center></td>
                <td style="text-align:center;"><?php echo $fechas[$i]['cupos_horainicio'] ?></td>
                <td style="text-align:center;"><?php echo $fechas[$i]['cupos_horafinal'] ?></td>
                <td style="text-align:center;"><?php echo $fechas[$i]['cupos_cantidad_n'] ?></td>
                <td style="text-align:center;"><?php echo $fechas[$i]['cupos_cantidad_c'] ?></td>
                <td style="text-align:center;"><?php echo $f; ?></td>
                <td style="text-align:center;"><?php echo $a; ?></td>
                <td style="text-align:center;"><?php echo $fechas[$i]['esp_desc'] ?></td>
                <td style="text-align:center;"><?php echo $fechas[$i]['nom_motivo'] ?></td>
                <td style="text-align:center;"><?php echo $fechas[$i]['nom_tipo_contrato'] ?></td>
                <td>
                    <center>
                        <!--<img src="../../iconos/delete.png" style="cursor:pointer;" onClick="eliminar_rango(<?php echo $fechas[$i]['cupos_id'] ?>);">-->
                        <img src="../../iconos/delete.png" style="cursor:pointer;" onClick="">
                    </center>
                </td>
            </tr>
            
            <?php
        }
        ?>
    </table>
</div>

<div class='sub-content'>

<table style='width:100%;'>

  <tr><td style='text-align: right;width:150px;'>Fecha Inicio:</td>
  <td><input type='text' name='fecha1' id='fecha1' size=10
  style='text-align: center;' value='<?php echo date("d/m/Y")?>'>
  <img src='../../iconos/date_magnify.png' id='fecha1_boton'></td></tr>
  <tr><td style='text-align: right;'>Fecha Final:</td>
  <td><input type='text' name='fecha2' id='fecha2' size=10
  style='text-align: center;' 
  value='<?php echo date("d/m/Y",mktime(0,0,0,date('m')+1,date('d'),date('Y')));  ?>'>
  <img src='../../iconos/date_magnify.png' id='fecha2_boton'></td></tr>

  <tr><td  style='text-align: right;' valign='top'>
  D&iacute;as de la Semana:
  </td><td>
  <input type='checkbox' id='dia_0' name='dia_0'> Lunes<br>
  <input type='checkbox' id='dia_1' name='dia_1'> Martes<br>
  <input type='checkbox' id='dia_2' name='dia_2'> Mi&eacute;rcoles<br>
  <input type='checkbox' id='dia_3' name='dia_3'> Jueves<br>
  <input type='checkbox' id='dia_4' name='dia_4'> Viernes<br>
  <input type='checkbox' id='dia_5' name='dia_5'> S&aacute;bado<br>
  <input type='checkbox' id='dia_6' name='dia_6'> Domingo<br>
  </td></tr>

</table>
<br>
<center>
<input type='submit' value='-- Realizar R&eacute;plica... --'>
</center>

</div>

</form>

</body>
</html>

<script>

    Calendar.setup({
        inputField     :    'fecha1',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha1_boton',
        dateStatusFunc : estado_fecha

    });
    Calendar.setup({
        inputField     :    'fecha2',
        ifFormat       :    '%d/%m/%Y',
        showsTime      :    false,
        button          :   'fecha2_boton',
        dateStatusFunc : estado_fecha

    });

</script>


