<?php
    require_once('../../conectar_db.php');
?>
<script>
    listar_repetidos=function()
    {
        $('listado_repetidos').innerHTML='<center><br><br><img src="imagenes/ajax-loader3.gif" /><br/>Cargando...</center>';
        var myAjax=new Ajax.Updater('listado_repetidos','ficha_clinica/fusion_pacientes/listado_repetidos.php',
        {
            method:'post',
            parameters:''
        });
    }

    reparar_paciente=function(rut)
    {
        $('string_pac').value="";
        limpiar_paciente();
        $('ver_paciente').innerHTML='<center><br><br><img src="imagenes/ajax-loader3.gif" /><br/>Cargando...</center>';
        var myAjax=new Ajax.Updater('ver_paciente','ficha_clinica/fusion_pacientes/cargar_rut.php',
        {
            method:'post',
            parameters:'pac_rut='+encodeURIComponent(rut)
        });
    }

    fusionar_paciente=function(rut)
    {
        if(!confirm("&iquest;Est&aacute; seguro que desea fusionar los registros?".unescapeHTML()))
            return;
	
        
        var radios=$$('input[type="radio"][name="pac_id"]');
	var pac_id=0;
	
        for(var i=0;i<radios.length;i++)
        {
            if(radios[i].checked)
            {
                pac_id=radios[i].value;
		break;
            }
	}
	
        if(pac_id==0)
            return;
        
	$('ver_paciente').innerHTML='<center><br><br><img src="imagenes/ajax-loader3.gif" /><br/>Cargando...</center>';

	var myAjax=new Ajax.Updater('ver_paciente','ficha_clinica/fusion_pacientes/cargar_rut.php',
        {
            method:'post',
            parameters:'pac_rut='+encodeURIComponent(rut)+'&pac_id='+pac_id+'&string_pac='+$('string_pac').value,
            evalScripts:true
	});
        $('ver_paciente').innerHTML='';
        listar_repetidos();
    }

    descargar_repetidos=function()
    {
        $('form_fusion').submit();
    }
    
    
    
    verificar_rut = function()
    {
        var texto = $('pac_rut').value;
        if(texto.charAt(0)=='R')
        {
            $('paciente_tipo_id').value=0;
            $('pac_rut').value=texto.substring(1,texto.length);
        }
        else if(texto.charAt(0)=='P')
        {
            $('paciente_tipo_id').value=1;
            $('pac_rut').value=texto.substring(1,texto.length);
        }
        else if(texto.charAt(0)=='I')
        {
            $('paciente_tipo_id').value=2;
            $('pac_rut').value=texto.substring(1,texto.length);
        }
        if($('paciente_tipo_id').value==0)
        {
            if(comprobar_rut($('pac_rut').value))
            {
                //$('pac_rut').style.background='inherit';
                //buscar_paciente();
            }
            else
            {
                //$('pac_rut').style.background='red';
            }
        }
        else if($('paciente_tipo_id').value>0)
        {
            //$('pac_rut').style.background='yellowgreen';
            //buscar_paciente();
        }
    }
    
    limpiar_paciente=function()
    {
        $('pac_rut').value='';
	$('paciente').value='';
	$('pac_id').value='';
        //$('txt_paciente').value='';
    }
    
    tipo_busqueda = function()
    {
        if($('paciente_tipo_id').value==0)
        {
            //$('pac_rut').style.display='';
            //$('txt_paciente').style.display='none';
            
        }
        else
        {
            //$('pac_rut').style.display='none';
            //$('txt_paciente').style.display='';
        }
        limpiar_paciente();
    }
    
    
    abrir_paciente = function()
    {
        var myAjax = new Ajax.Request('../../recetas/ver_recetas/abrir_paciente.php',
        {
            method: 'get',
            parameters: 'pac_rut='+$('pac_rut').value,
            onComplete: function(respuesta)
            {
                try
                {
                    datos = respuesta.responseText.evalJSON(true);
                }
                catch (err)
                {
                    alert('ERROR:\n\n'+err);
                }
                if(datos!=false)
                {
                    $('pac_id').value=datos[1][0];
                    $('paciente').innerHTML=datos[1][1];
                }
                else
                {
                    $('pac_id').value=-1;
                    $('paciente').innerHTML='';
                }
            }
        }
        );
    }
    
    agregar_paciente = function()
    {
        if($('pac_id').value=="")
        {
            alert("Debe ingresar un paciente para poder agregar a la lista");
            return;
        }
        $('string_pac').value=$('pac_id').value+"|"+$('string_pac').value;
        $('ver_paciente').innerHTML='<center><br><br><img src="imagenes/ajax-loader3.gif" /><br/>Cargando...</center>';
        var myAjax=new Ajax.Updater('ver_paciente','ficha_clinica/fusion_pacientes/cargar_rut.php',
        {
            method:'post',
            parameters:'pac_rut='+encodeURIComponent($('pac_rut').value)+'&llamada=1&tipo_busqueda='+$('paciente_tipo_id').value+'&string_pac='+$('string_pac').value
        });
        limpiar_paciente();
    }
    
    
    busqueda_pacientes = function(objetivo, callback_func)
    {
        //$('pac_rut').style.display='block';
        $('paciente_tipo_id').value = 0;
        top=Math.round(screen.height/2)-150;
        left=Math.round(screen.width/2)-250;
        new_win = window.open('buscadores.php?tipo=pacientes', 'win_funcionarios',
        'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=650, height=400, '+
        'top='+top+', left='+left);
        new_win.objetivo_cod = objetivo;
        new_win.onCloseFunc = callback_func;
        new_win.focus();
    }
    
</script>
<center>
    <div class='sub-content' style='width:80%;'>
        <input type="hidden" id="string_pac" name="string_pac" value="" />
        <div class='sub-content'>
            <table style="width: 100%;">
                <tr>
                    <td style="width:80px;">
                        <img src='iconos/user_go.png' />
                        <b>Fusi&oacute;n de Pacientes</b>
                    </td>
                    <td style="width:8%;">
                        <select id="paciente_tipo_id" name="paciente_tipo_id" onchange="tipo_busqueda();">
                            <option value=0 SELECTED>R.U.T.</option>
                            <option value=3>Nro. Ficha</option>
                            <option value=1>Pasaporte</option>
                            <option value=2>Cod. Interno</option>
                        </select>
                    </td>
                    <td style="width:10px;">
                        <!--<img src='iconos/zoom_in.png' id='buscar_paciente' onClick='busqueda_pacientes("pac_rut", function() { verificar_rut(); });' onKeyUp="fix_bar(this);" alt='Buscar Paciente...' title='Buscar Paciente...'>-->
                        <img src='iconos/zoom_in.png' id='buscar_paciente' onClick='busqueda_pacientes("pac_rut", function() { abrir_paciente(); });' onKeyUp="fix_bar(this);" alt='Buscar Paciente...' title='Buscar Paciente...'>
                        <img src='imagenes/ajax-loader1.gif' id='cargando' style='display: none;'>
                    </td>
                    <td style='width:450px;'>
                        <input type='hidden' id='pac_id' name='pac_id' value='0' />
                        <input type='text' size=20 id='pac_rut' name='pac_rut' value='' onDblClick='limpiar_paciente();' />
                        <!--<input type='text' size=20 id='txt_paciente' name='txt_paciente' value='' onDblClick='limpiar_paciente();' style="display:none"/>-->
                        <input type='text' id='paciente' name='paciente' style='text-align:left;' DISABLED size=45 />
                        <input type='button' id='btn_agregar' value='[[ AGREGAR ]]' onClick='agregar_paciente();' />
                    </td>
                </tr>
            </table>
        </div>
        <form id='form_fusion' name='form_fusion' method='post' action='ficha_clinica/fusion_pacientes/listado_repetidos.php'>
            <input type='hidden' id='xls' name='xls' value='1' />
        </form>
        <div class='sub-content2' id='listado_repetidos' style='height:250px;overflow:auto;'>
        </div>
        <center>
            <input type='button' value='Descargar Reporte en XLS...' onClick='descargar_repetidos();' />
        </center>
        <div class='sub-content2' id='ver_paciente' style='height:250px;overflow:auto;'>
        </div>
    </div>
</center>
<script>
    
    seleccionar_paciente = function(d)
    {
        if(($('paciente_tipo_id').value*1)==0)
        {
            $('pac_rut').value=d[0];
            $('paciente').value=d[2].unescapeHTML();
            $('pac_id').value=d[5];
        }
        if(($('paciente_tipo_id').value*1)==1)
        {
            $('pac_rut').value=d[13];
            $('paciente').value=d[2].unescapeHTML();
            $('pac_id').value=d[5];
        }
        if(($('paciente_tipo_id').value*1)==2)
        {
            $('pac_rut').value=d[5];
            $('paciente').value=d[2].unescapeHTML();
            $('pac_id').value=d[5];
        }
        if(($('paciente_tipo_id').value*1)==3)
        {
            $('pac_rut').value=d[3];
            $('paciente').value=d[2].unescapeHTML();
            $('pac_id').value=d[5];
        }
    }
    
    
    autocompletar_pacientes = new AutoComplete(
    'pac_rut', 
    'autocompletar_sql.php',
    function() {
    if($('pac_rut').value.length<2) return false;
    return {
    method: 'get',
    parameters: 'tipo=pacientes_edad&nompac='+encodeURIComponent($('pac_rut').value)+'&pac_tipo='+$('paciente_tipo_id').value
    }
    }, 'autocomplete', 500, 200, 150, 1, 4, seleccionar_paciente);
    
listar_repetidos();
</script>