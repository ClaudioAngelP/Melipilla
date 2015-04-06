<?php 
    require_once('../../conectar_db.php');
    $servicio=false;
    if(isset($_POST['servicio']))
    {
        $servicio=true;
        $centro_ruta=pg_escape_string($_POST['centro_ruta']);
    }
?>
<html>
<title>Solicitud de Pr&eacute;stamo de Ficha</title>
<?php 
    if($servicio)
    {
        cabecera_popup('/'); 
    }
    else
    {
        cabecera_popup('../..'); 
    }
?>
<script>
    var lista_ficha=new Array();
    
    solicitar_ficha=function()
    {
        if(($('servicio').value*1)!=1)
        {
            var url="sql_solicitar.php";
            if(!$('check_lista').checked)
            {
                if($('pac_id').value==0 || $('doc_id').value==0 || $('esp_id').value==0 || $('amp_id').value==0)
                {
                    alert('Complete el formulario para solicitar la ficha.');
                    return
                }
            }
            else
            {
                if((lista_ficha.length*1)==0)
                {
                    alert('No ha ingresado Fichas a solicitar');
                    return;
                }
                if($('esp_id').value==0)
                {
                    alert('Debe indicar especialidad solicitante');
                    return;
                }
                if($('amp_id').value==0)
                {
                    alert('Debe indicar motivo de solicitud');
                    return;
                }
            }
        }
        else
        {
            var url="prestaciones/archivo_fichas/sql_solicitar.php";
            if(!$('check_lista').checked)
            {
                if($('pac_id').value==0 || $('amp_id').value==0)
                {
                    alert('Complete el formulario para solicitar la ficha.');
                    return
                }
            }
            else
            {
                if((lista_ficha.length*1)==0)
                {
                    alert('No ha ingresado Fichas a solicitar');
                    return;
                }
                if($('amp_id').value==0)
                {
                    alert('Debe indicar motivo de solicitud');
                    return;
                }
            }
        }
        var params='';
        if($('check_lista').checked)
        {
            params+='&list_fichas='+encodeURIComponent(lista_ficha.toJSON());
        }
                
        var myAjax=new Ajax.Request(url,
        {
            method:'post',
            parameters: $('datos').serialize()+params,
            onComplete:function(r)
            {
                var datos=r.responseText.evalJSON(true);
                if(datos[0]==false)
                {
                    alert('Solicitud de pr&eacute;stamo de fichas enviada exitosamente.'.unescapeHTML());
                }
                else
                {
                    if($('check_lista').checked)
                    {
                        alert('Se han encontrado '+datos[0].length+' solicitud(es) de ficha(s), que ya ha solicitado para el d\u00eda de hoy con anterioridad');
                    }
                    else
                    {
                        alert('Ya ha solicitado esta ficha para el d\u00eda de hoy con anterioridad');
                    }
                }
                if(($('servicio').value*1)!=1)
                {
                    this.close();
                    
                }
                else
                {
                    $("solicitar_ficha").win_obj.close();
                }
            }
        });
    }
    
    dibujar_lista=function()
    {
        if(lista_ficha.length==0)
        {
            $('div_list').innerHTML='';
        }
        else
        {
            var html='';
            html+='<table style="width:100%" border="0">';
                html+='<tr class="tabla_header">';
                    html+='<td style="text-align:center;">Rut</td>';
                    html+='<td style="text-align:center;">Ficha</td>';
                    html+='<td style="text-align:center;">Nombre</td>';
                    html+='<td style="text-align:center;width:40px;">&nbsp;</td>';
                html+='</tr>';
                for(var i=0;i<lista_ficha.length;i++)
                {
                    if(i%2==0) clase='tabla_fila'; else clase='tabla_fila2';
                    html+='<tr class="'+clase+'">';
                        html+='<td style="text-align:center;font-weight:bold;">'+lista_ficha[i].pac_rut+'</td>';
                        html+='<td style="text-align:center;font-<weight:bold;">'+lista_ficha[i].pac_ficha+'</td>';
                        html+='<td style="text-align:left;">'+lista_ficha[i].pac_nombre+'</td>';
                        html+='<td><center><img src=\'/produccion/iconos/delete.png\' style=\'cursor:pointer;\' onClick="quitar_pac('+i+');" /></center></td>';
                    html+='</tr>';
                }
            html+='</table>';
            $('div_list').innerHTML=html;
        }
        
        
    }
    
    seleccionar_paciente = function(d)
    {
        if($('check_lista').checked)
        {
            var encontrado=false;
            for(var i=0;i<lista_ficha.length;i++)
            {
                if(lista_ficha[i].pac_id==d[4])
                {
                    alert("Ya ha ingresado al paciente en la lista".unescapeHTML());
                    encontrado=true;
                    break;
                }
            }
            if(encontrado==false)
            {
                num=lista_ficha.length;
                lista_ficha[num]=new Object();
                lista_ficha[num].pac_rut=d[0];
                lista_ficha[num].pac_ficha=d[3];
                lista_ficha[num].pac_nombre=d[2].unescapeHTML();
                lista_ficha[num].pac_id=d[4];
                dibujar_lista();
            }
            limpiar_paciente();
        }
        else
        {
            lista_ficha.length=0;
            $('pac_rut').value=d[0];
            $('paciente').value=d[2].unescapeHTML();
            $('pac_id').value=d[4];
        }
    }
    
    tipo_solicitud=function()
    {
        lista_ficha.length=0;
        if($('check_lista').checked)
            $('div_list').style.display='';
        else
            $('div_list').style.display='none';
        $('div_list').innerHTML='';
        limpiar_paciente();
    }
    
    quitar_pac=function(id)
    {
        
        lista_ficha=lista_ficha.without(lista_ficha[id]);
        dibujar_lista();
    }
</script>
<body class='fuente_por_defecto popup_background'>
    <form id='datos' name='datos' onSubmit='return false;'>
        <?php
        if($servicio)
        {
        ?>
            <input type='hidden' id='servicio' name='servicio' value='1' />
            <input type='hidden' id='centro_ruta' name='centro_ruta' value='<?php echo $centro_ruta;?>' />
        <?php
        }
        else
        {
        ?>
            <input type='hidden' id='servicio' name='servicio' value='0' />
            <input type='hidden' id='centro_ruta' name='centro_ruta' value='' />
        <?php
        }
        
        ?>
        <table style='width:100%;'>
            <tr>
                <td class="tabla_fila2" style="text-align:right;" colspan="3">
                    <input type="checkbox" id="check_lista" name="check_lista" onchange="tipo_solicitud();" ><b>Hacer Lista de Solicitud</b>
                </td>
            </tr>
            <tr>
                <td class='tabla_fila2' style='text-align:right;'>Paciente:</td>
		<td class='tabla_fila'>
                    <input type='hidden' id='pac_id' name='pac_id' value='0' />
                    <input type='text' size=10 id='pac_rut' name='pac_rut' value='' onDblClick='limpiar_paciente();' style='font-size:16px;' />
                </td>
                <td class='tabla_fila'>
                    <input type='text' id='paciente' name='paciente'  onDblClick='limpiar_paciente();' style='text-align:left;font-size:16px;' DISABLED size=40 />
                </td>
            </tr>
            <?php
            if(!$servicio)
            {
            ?>
            <tr>
                <td class='tabla_fila2' style='text-align:right;'>Especialidad:</td>
		<td class='tabla_fila' colspan=2>
                    <input type='hidden' id='esp_id' name='esp_id' value='0' />
                    <input type='text' size=45 id='esp_desc' name='esp_desc' value='' onDblClick='limpiar_especialidad();' style='font-size:16px;' />
		</td>
            </tr>
            <tr>
		<td class='tabla_fila2' style='text-align:right;'>Profesional:</td>
		<td class='tabla_fila'>
                    <input type='hidden' id='doc_id' name='doc_id' value='0' />
                    <input type='text' size=10 id='doc_rut' name='doc_rut' value='' onDblClick='limpiar_profesional();' style='font-size:16px;'  />
                </td>
                <td class='tabla_fila'>
                    <input type='text' id='profesional' name='profesional'  onDblClick='limpiar_profesional();' style='text-align:left;font-size:16px;' DISABLED size=40 />
		</td>
            </tr>
            <?php
            }
            ?>
            <tr>
                <td class='tabla_fila2' style='text-align:right;'>Motivo Solicitud:</td>
		<td class='tabla_fila' colspan=2>
                    <select id='amp_id' name='amp_id' style='font-size:18px;'>
                        <option value=''>(Especifique el motivo de su solicitud...)</option>
                        <?php 
                            $amp=cargar_registros_obj("SELECT * FROM archivo_motivos_prestamo ORDER BY amp_id;", true);
                            for($i=0;$i<sizeof($amp);$i++) {
				print("<option value='".$amp[$i]['amp_id']."'>".$amp[$i]['amp_nombre']."</option>");
                            }
                        ?>
                    </select>
		</td>
            </tr>
            <tr>
                <td colspan=3>
                    <center>
                        <input type='button' id='' name='' value='[Enviar Solicitud]' onClick='solicitar_ficha();' style='font-size:20px;'/>
                    </center>
            	</td>
            </tr>
        </table>
            <div class='sub-content2' id="div_list" style='height: 250px; overflow:auto;display: none;'></div>
    </form>
</body>
</html>
<script>
    limpiar_paciente = function(d)
    {
        $('pac_rut').value='';
	$('paciente').value='';
	$('pac_id').value=0;
    }
    <?php
    if($servicio)
    {
    ?>
        var ruta="";
    <?php
    }
    else
    {
    ?>
        var ruta="../../";
    <?php
    }
    ?>
    
    autocompletar_pacientes = new AutoComplete(
    'pac_rut', 
    ''+ruta+'autocompletar_sql.php',
    function() {
    if($('pac_rut').value.length<2) return false;
    return {
    method: 'get',
    parameters: 'tipo=pacientes&nompac='+encodeURIComponent($('pac_rut').value)
    }
    }, 'autocomplete', 500, 200, 150, 1, 3, seleccionar_paciente);
      
      
    seleccionar_profesional = function(d)
    {
        $('doc_rut').value=d[1];
	$('profesional').value=d[2].unescapeHTML();
	$('doc_id').value=d[0];
    }

    limpiar_profesional = function(d)
    {
        $('doc_rut').value='';
	$('profesional').value='';
	$('doc_id').value=0;
    }
    <?php
    if(!$servicio)
    {
    ?>
        autocompletar_profesionales = new AutoComplete(
        'doc_rut', 
        ''+ruta+'autocompletar_sql.php',
        function() {
        if($('doc_rut').value.length<2) return false;
        return {
        method: 'get',
        parameters: 'tipo=doctor&nombre_doctor='+encodeURIComponent($('doc_rut').value)
        }
        }, 'autocomplete', 500, 200, 150, 1, 3, seleccionar_profesional);
    <?php
    }
    ?>
    seleccionar_especialidad = function(d)
    {
        $('esp_id').value=d[0];
	$('esp_desc').value=d[2].unescapeHTML();
    }

    limpiar_especialidad = function(d)
    {
        $('esp_id').value=0;
	$('esp_desc').value='';
    }
    <?php
    if(!$servicio)
    {
    ?>
        autocompletar_especialidad = new AutoComplete(
        'esp_desc', 
        ''+ruta+'autocompletar_sql.php',
        function() {
        if($('esp_desc').value.length<2) return false;
        return {
        method: 'get',
        parameters: 'tipo=especialidad&'+$('esp_desc').serialize()
        }
        }, 'autocomplete', 500, 200, 150, 1, 3, seleccionar_especialidad);
    <?php
    }
    ?>
    limpiar_todo=function()
    {
        $('pac_rut').value='';
	$('paciente').value='';
	$('pac_id').value=0;
        $('esp_id').value=0;
	$('esp_desc').value='';
        $('doc_rut').value='';
	$('profesional').value='';
	$('doc_id').value=0;
        $('amp_id').value='';
    }

</script>
