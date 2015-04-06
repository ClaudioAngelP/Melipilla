<?php
    require_once('../../conectar_db.php');
    $bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1','bod_id IN ('._cav(70),')', 'ORDER BY bod_glosa');
    $servs="'".str_replace(',','\',\'',_cav2(70))."'";
    $servicioshtml = desplegar_opciones_sql(
                    "SELECT centro_ruta, centro_nombre FROM centro_costo WHERE
                    length(regexp_replace(centro_ruta, '[^.]', '', 'g'))=3 AND
                    centro_medica AND centro_ruta IN (".$servs.")
                    ORDER BY centro_nombre", NULL, '', "font-style:italic;color:#555555;");
    $fecha1=date('d/m/Y', mktime(0,0,0,date('m'),date('d')-7,date('Y')));
    $fecha2=date('d/m/Y');
?>


<script>
    var articulos = new Array();
    bloquear_ingreso=false;


busqueda_pacientes = function(objetivo, callback_func) {

      top=Math.round(screen.height/2)-150;
      left=Math.round(screen.width/2)-250;
  
      new_win =
      window.open('buscadores.php?tipo=pacientes', 'win_funcionarios',
        'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=650, height=400, '+
        'top='+top+', left='+left);
  
      new_win.objetivo_cod = objetivo;
      new_win.onCloseFunc = callback_func;
  
      new_win.focus();

    }

    //*******************************************************************888
    seleccionar_articulo = function (art)
    {
        var num_stock=number_format(art[4]*1,1);
        
        
        $('art_id').value=art[6];
        $('art_codigo').value=art[0];
        $('art_glosa').value=art[2];
        $('art_nombre').innerHTML=art[2];
        $('art_clasificacion').value=art[3];
        
        if(art[3]=='KIT' && art[7]=='KIT' && art[8]=='KIT') 
			kit=true;
		else
			kit=false;
        
        if(!kit) {
			$('art_cant').value=art[4];
			$('art_stock').innerHTML=num_stock+'.-';
			$('art_kit').value='0';
			
		} else {
			$('art_cant').value='0';
			$('art_stock').innerHTML='';	
			$('art_kit').value='1';

		}
		
        $('cantidad').value='1';
        $('cantidad').select();
        $('cantidad').focus();
        
        
    }

//*******************************************************************888
    agregar_articulo = function ()
    {
		
		if(!(($('cantidad').value*1)/1) || ($('cantidad').value*1)<1){
			
			alert('Debe ingresar cantidad de articulos.'.unescapeHTML());
			 return;
		}
		
		
		if($('art_kit').value=='1') {
			
			var myAjax=new Ajax.Request(
				'abastecimiento/hoja_cargo/cargar_kit.php',
				{
					method:'post',
					parameters:$('art_id').serialize()+'&'+$('cantidad').serialize()+'&'+$('bodega_id').serialize(),
					onComplete:function(r) {

						try {
						
						var det=r.responseText.evalJSON(true);
						
						for(var d=0;d<det.length;d++) {
	
							num = articulos.length;

							articulos[num] = new Object();
							articulos[num].id=det[d].id;
							articulos[num].codigo=det[d].codigo;
							articulos[num].glosa=det[d].glosa;
							articulos[num].clasificacion=det[d].clasificacion;
							articulos[num].cantidad=det[d].cantidad;
							articulos[num].stock=det[d].stock;

							
						}
						
						redibujar();

						$('codigo').select();
						$('codigo').focus();

						} catch(err) { alert(err); }
						
					}
				}
			);
			
			return;
			
		}
		
		
		
       if(($('cantidad').value*1)>($('art_cant').value*1))
        {
            if($('art_cant').value>0)
            {
                alert('No hay saldo suficiente del art&iacute;culo seleccionado.'.unescapeHTML());
                $('cantidad').select();
                $('cantidad').focus();
            }
            else
            {
                alert('El art&iacute;culo seleccionado no tiene saldo disponible.'.unescapeHTML());
                $('codigo').select();
                $('codigo').focus();
            }
            return;
        }

        num=-1;
      
        //     Si el articulo ya fué ingresado se retira.
        for(var i=0;i<articulos.length;i++)
            if(articulos[i].id==$('art_id').value) num=i;

        if(num==-1) num = articulos.length;
      
        articulos[num] = new Object();
        articulos[num].id=$('art_id').value;
        articulos[num].codigo=$('art_codigo').value;
        articulos[num].glosa=$('art_glosa').value;
        articulos[num].clasificacion=$('art_clasificacion').value;
        articulos[num].cantidad=$('cantidad').value*1;
        articulos[num].stock=$('art_cant').value*1;
        redibujar();
        $('codigo').select();
        $('codigo').focus();
    }

    //*******************************************************************

    redibujar = function()
    {
        html='<table style="width:100%;"><tr class="tabla_header">';
        html+='<td>C&oacute;digo Int.</td><td>Glosa</td>';
        html+='<td>Stock</td><td>Cantidad</td>';
        html+='<td colspan=2>Acciones</td></tr>';
    
        for(var i=0;i<articulos.length;i++)
        {
            if((i%2)==0) clase='tabla_fila'; else clase='tabla_fila2';
            html+='<tr class="'+clase+'"';
            html+='onMouseOver="this.className=\'mouse_over\';"';
            html+='onMouseOut="this.className=\''+clase+'\';">';
            html+='<td style="text-align:right;">'+articulos[i].codigo+'</td>';
            html+='<td>'+articulos[i].glosa+'</td>';
            html+='<td style="text-align:right;">'+number_format(articulos[i].stock,2)+'.-</td>';
            html+='<td style="text-align:center;"><input type="text" style="width:50px;text-align:right;" id="cant_'+i+'" name="cant_'+i+'" value="'+number_format(articulos[i].cantidad,0)+'" onKeyUp="guarda_vals('+i+');" /></td>';
            html+='<td><center>';
            html+='<img src="iconos/delete.png" ';
            html+='style="cursor:pointer;" onClick="remover('+i+');">';
            html+='</center></td>';
            html+='</tr>';
        }
        html+='</table>';
        $('seleccion').innerHTML=html;
    }
    
    guarda_vals=function(i) {
		
        //for(var i=0;i<articulos.length;i++)
        //{

			articulos[i].cantidad=($('cant_'+i).value*1);

		//}
		
	}

//*******************************************************************

    remover = function (art)
    {
        articulos = articulos.without(articulos[art]);
        redibujar();
    }

//*******************************************************************

    buscar_paciente = function()
    {
        if($('paciente_rut').value.charAt(0)=='R')
        {
            $('paciente_tipo_id').value=0;
            $('paciente_rut').value=
            $('paciente_rut').value.substring(1,$('paciente_rut').value.length);
        }
        else if($('paciente_rut').value.charAt(0)=='P')
        {
            $('paciente_tipo_id').value=1;
            $('paciente_rut').value=
            $('paciente_rut').value.substring(1,$('paciente_rut').value.length);
        }
        else if($('paciente_rut').value.charAt(0)=='I')
        {
            $('paciente_tipo_id').value=2;
            $('paciente_rut').value=
            $('paciente_rut').value.substring(1,$('paciente_rut').value.length);
        }
        params=$('paciente_rut').serialize();
        params+='&'+$('paciente_tipo_id').serialize();
        var myAjax = new Ajax.Request(
			'registro.php', 
			{
                method: 'get',
				parameters: 'tipo=paciente&'+params,
				onComplete: function (pedido_datos)
                {
                    if(pedido_datos.responseText=='')
                    {
                        if($('paciente_tipo_id').value==0) conector_hsmq();
                        else
                        {
                            alert('Paciente no encontrado.');
                            $('paciente_rut').select();
                            $('paciente_rut').focus();
                            return;
                        }
            
                    }
                    else
                    {
                        datosxxx=pedido_datos.responseText.evalJSON(true);
                        var nombre=datosxxx[3];
						nombre+=' '+datosxxx[4];
						nombre+=' '+datosxxx[2];
						$('pac_id').value=datosxxx[0];
						$('nom_pac').innerHTML=nombre;
                    }
				
                }
            }
			);
    }

//*******************************************************************

    buscar_paciente2 = function()
    {
        if($('paciente_rut2').value.charAt(0)=='R')
        {
            $('paciente_tipo_id2').value=0;
            $('paciente_rut2').value=
            $('paciente_rut2').value.substring(1,$('paciente_rut2').value.length);
        }
        else if($('paciente_rut2').value.charAt(0)=='P')
        {
            $('paciente_tipo_id2').value=1;
            $('paciente_rut2').value=
            $('paciente_rut2').value.substring(1,$('paciente_rut2').value.length);
        } 
        else if($('paciente_rut2').value.charAt(0)=='I')
        {
            $('paciente_tipo_id2').value=2;
            $('paciente_rut2').value=
            $('paciente_rut2').value.substring(1,$('paciente_rut2').value.length);
        }
    
        params='paciente_rut='+encodeURIComponent($('paciente_rut2').value);
        params+='&paciente_tipo_id=';
        params+=encodeURIComponent($('paciente_tipo_id2').value);
    
        var myAjax = new Ajax.Request(
			'registro.php', 
			{
				method: 'get', 
				parameters: 'tipo=paciente&'+params,
				onComplete:function (pedido_datos)
                {
                    if(pedido_datos.responseText=='')
                    {
                        if($('paciente_tipo_id2').value==0) conector_hsmq();
                        else
                        {
                            alert('Paciente no encontrado.');
                            $('paciente_rut2').select();
                            $('paciente_rut2').focus();
                            return;
                        }
                    }
                    else
                    {
                        datosxxx=pedido_datos.responseText.evalJSON(true);
                        var nombre=datosxxx[3];
						nombre+=' '+datosxxx[4];
						nombre+=' '+datosxxx[2];
                        $('pac_id2').value=datosxxx[0];
						$('nom_pac2').innerHTML=nombre;
						
						listar_salidas(); 
						
                    }
                }
			}
        );
    }

//*******************************************************************
    
    conector_hsmq = function()
    {
        rut_partes = $('paciente_rut').value.split('-');
        var myAjax = new Ajax.Request(
        'conectores/hsmq_sco_server.php',
        {
            method: 'get',
            parameters: 'rut='+rut_partes[0],
            onComplete: function(r) {
            try
            {
                campos = r.responseText.split('|');
                if(!campos || campos[0]!=rut_partes[0])
                {
                    alert('Paciente no encontrado.');
                    return;
                }
                var nombre=campos[6];
				nombre+=' '+campos[4];
				nombre+=' '+campos[5];
                $('nom_pac').innerHTML=nombre;
                $('codigo').focus();
            }
            catch(err)
            {
                alert(err);
            }
            }
        }
        );
    }

//*******************************************************************

    autocompletar_medicamentos = new AutoComplete(
      'codigo', 
      'abastecimiento/hoja_cargo/autocompletar_kits.php',
      function() {
        if($('codigo').value.length<3) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=buscar_arts_stock&'+$('codigo').serialize()+'&'+
                      $('bodega_id').serialize()
        }
      }, 'autocomplete', 550, 200, 150, 1, 3, seleccionar_articulo);

//*******************************************************************

      ver_ingreso=function() {
      
        tab_down('tab_salidas');
        tab_up('tab_ingreso');
      
      }
      
      ver_salidas=function() {

        tab_up('tab_salidas');
        tab_down('tab_ingreso');
      
      }
      
      limpiar_paciente = function() {
      
        $('pac_id2').value=0;
        $('nom_pac2').innerHTML='';
        $('paciente_rut2').value='';
      
      }
      
      verifica_tabla = function() {
      
        if(articulos.length==0) {
          alert('No ha seleccionado art&iacute;culos'.unescapeHTML());
          return;
        }
        
        if($('pac_id').value==0) {
          alert('No ha seleccionado un paciente.');
          return;
        }
        
        if(bloquear_ingreso) return;
        
        bloquear_ingreso=true;
        
        params='arts='+encodeURIComponent(articulos.toJSON());
        params+='&'+$('cabecera').serialize();
        
        var myAjax=new Ajax.Request(
        'abastecimiento/hoja_cargo/sql.php',
        {
          method: 'post',
          parameters: params,
          onComplete: function(resp) {
          
            try {
            
              var datos = resp.responseText.evalJSON(true);
            
              alert('Cargo a paciente realizado exitosamente.');
              cambiar_pagina('abastecimiento/hoja_cargo/form.php');
              
            } catch(err) {
            
              alert('ERROR:\n\n'+resp.responseText);
              bloquear_ingreso=false;
            
            }
          
          }
        }
        );
      
      }
      
      lista_salidas = function() {
      
        var myAjax=new Ajax.Updater(
        'listado_movs',
        'abastecimiento/hoja_cargo/listar_pacientes.php',
        {
          method:'post',
          parameters: $('cabecera2').serialize()
        }
        );
      
      }

//*******************************************************************

    mostrar_detalle=function(num)
    {

        var myAjax=new Ajax.Updater(
        'listado_movs',
        'abastecimiento/hoja_cargo/listar_salidas.php',
        {
            method:'post',
            parameters: 'pac_id='+num+'&'+$('cabecera2').serialize()
        }
        );

    }

//*******************************************************************
    mostrar_detalle2=function(num)
    {

        var myAjax=new Ajax.Updater(
        'listado_movs',
        'abastecimiento/hoja_cargo/listar_salidas2.php',
        {
            method:'post',
            parameters: $('bodega_id2').serialize()+'&'+$('paciente_rut2').serialize()
        }
        );

    }

 //*******************************************************************
 
 
 
 mantener_kits=function() {
	 
	params='bod_id='+($('bodega_id').value*1);
	
    top=Math.round(screen.height/2)-300;
    left=Math.round(screen.width/2)-400;
    
    new_win = 
    window.open('abastecimiento/hoja_cargo/form_kits.php?'+params,
    'win_talonarios', 'toolbar=no, location=no, directories=no, status=no, '+
    'menubar=no, scrollbars=yes, resizable=yes, width=800, height=600, '+
    'top='+top+', left='+left);
      
    new_win.focus();

	 
 }

 

</script>

<center>
<div class='sub-content' style='width:720px;'>
    <div class='sub-content'>
        <img src='iconos/script.png'>
         <b>Hoja de Cargo de Art&iacute;culos por Paciente</b>
    </div>
    <table width=100% cellpadding=0 cellspacing=0>
    <tr>
        <td>
            <table cellpadding=0 cellspacing=0>
            <tr>
                <td>
                    <div class='tabs' id='tab_ingreso' style='cursor: default;' onClick='ver_ingreso();'>
                        <img src='iconos/script_edit.png'>
                        Salida de Art&iacute;culos
                    </div>
                </td>
                <td>
                    <div class='tabs_fade' id='tab_salidas' style='cursor: pointer;' onClick='ver_salidas();'>
                        <img src='iconos/page_white_find.png'>
                        Visualizar Movimientos
                    </div>
                </td>
            </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <div class='tabbed_content' id='tab_ingreso_content' style='width:700px;'>
                <div class='sub-content'>
                    <form id='cabecera' name='cabecera' onSubmit='return false;'>
                        <table style='width:100%;'>
                        <tr>
                            <td style='text-align:right;'>Ubicaci&oacute;n:</td>
                            <td>
                                <select id='bodega_id' name='bodega_id'>
                                    <?php echo $bodegashtml; echo $servicioshtml; ?>
                                </select>
                                <input type='button' id='kits' name='kits' value='-[[ Mantenedor de KIT ]]-' onClick='mantener_kits();' />  
                            </td>
                        </tr>
                        <tr>
                            <td style='text-align:right;'>Paciente:</td>
                            <td>
                                <table>
                                <tr>
                                    <td>
                                        <select id="paciente_tipo_id" name="paciente_tipo_id" style="font-size:10px;" >
                                            <option value=0 SELECTED>R.U.T.</option>
                                            <option value=1>Pasaporte</option>
                                            <option value=2>Cod. Interno</option>
                                        </select>
                                        <input type='text' id='paciente_rut' name='paciente_rut'
                                        style="font-size:10px;" value='' size=15 onKeyPress='
                                        if(event.which==13) buscar_paciente();'>
                                        <input type='hidden' id='pac_id' name='pac_id' value=''>
                                        <img src='iconos/zoom_in.png' style='cursor:pointer;'
                                        onClick='busqueda_pacientes("paciente_rut",
                                        function() { buscar_paciente(); });'>
                                        <img src='iconos/cross.png' style='cursor:pointer;'
                                        onClick='$("pac_id").value="";$("paciente_rut").value="";$("nom_pac").innerHTML="";'>
                                    </td>
                                    <td style='width:250px;' id='nom_pac'>
                                    </td>
                                </tr>
                                </table>
                            </td>
                        </tr>
                        </table>
                    </form>
                </div>
                <div class="sub-content">
                    <center>
                        <table>
                        <tr>
                            <td>
                                <input type='hidden' id='art_id' name='art_id' value=''>
                                <input type='hidden' id='art_codigo' name='art_codigo' value=''>
                                <input type='hidden' id='art_glosa' name='art_glosa' value=''>
                                <input type='hidden' id='art_cant' name='art_cant' value=''>
                                <input type='hidden' id='art_kit' name='art_kit' value=''>
                                <input type='hidden' id='art_clasificacion' name='art_clasificacion' value=''>
                                <input type='text' id='codigo' name='codigo' size=15>
                            </td>
                            <td style="width:300px;" id="art_nombre">
                                (Seleccione Art&iacute;culos...)
                            </td>
                            <td style="width:150px;text-align:right;" id="art_stock">
                                0.-
                            </td>
                            <td>
                                <input type='text' id='cantidad' name='cantidad'
                                style='text-align:right;' onKeyUp='if(event.which==13)
                                agregar_articulo();' value='0' size=5>
                            </td>
                            <td>
                                <input type='button' id='agregar_art' name='agregar_art'
                                onClick='agregar_articulo();' value='Agregar Art&iacute;culo...'>
                            </td>
                        </tr>
                        </table>
                    </center>
                </div>
                <div class='sub-content2' style='overflow:auto;height:150px;' id='seleccion' name='seleccion'>
                </div>
                <center>
                    <table>
                    <tr>
                        <td>
                            <div class='boton'>
                                <table>
                                <tr>
                                    <td>
                                        <img src='iconos/script_go.png'>
                                    </td>
                                    <td>
                                        <a href='#' onClick='verifica_tabla();'> Realizar Cargo a Paciente...</a>
                                    </td>
                                </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                    </table>
                </center>
            </div>
            <div class='tabbed_content' id='tab_salidas_content' style='width:700px;display:none;'>
                <div class='sub-content'>
                    <form id='cabecera2' name='cabecera2' onChange='' onSubmit='return false;'>
                        <table style='width:100%;'>
                        <tr>
                            <td style='text-align:right;'>Ubicaci&oacute;n:</td>
                            <td colspan=3>
                                <select id='bodega_id2' name='bodega_id2'>
                                    <?php echo $bodegashtml; echo $servicioshtml; ?>
                                </select>
                                <input type='button' onClick='lista_salidas();' value='[[ Actualizar ]]' />
                            </td>
                        </tr>
                        
                                        <tr>
                                            <td style='text-align: right;'>Fecha Inicio:</td>
                                            <td>
                                                <input type='text' name='fecha1' id='fecha1' size=10
                                                style='text-align: center;' value='<?php echo date("d/m/Y")?>'>
                                                <img src='iconos/date_magnify.png' id='fecha1_boton'>
                                            </td>
                                            <td style='text-align: right;'>Fecha Final:</td>
                                            <td>
                                                <input type='text' name='fecha2' id='fecha2' size=10
                                                style='text-align: center;' value='<?php echo date("d/m/Y")?>'>
                                                <img src='iconos/date_magnify.png' id='fecha2_boton'>
                                            </td>
                                        </tr>

                        
                        <tr>
                            <td style='text-align:right;'>Paciente:</td>
                            <td colspan=3>
                                <table>
                                <tr>
                                    <td>
                                        <select id="paciente_tipo_id2" name="paciente_tipo_id2" style="font-size:10px;" >
                                            <option value=0 SELECTED>R.U.T.</option>
                                            <option value=1>Pasaporte</option>
                                            <option value=2>Cod. Interno</option>
                                        </select>
                                        <input type='text' id='paciente_rut2' name='paciente_rut2'
                                        style="font-size:10px;" value='' size=15
                                        onKeyPress="if(event.which==13)
                                        mostrar_detalle2($('paciente_rut2').value);">
                                        <input type='hidden' id='pac_id2' name='pac_id2' value=''>
                                    </td>
                                    <td>
                                        <img src='iconos/zoom_in.png' style='cursor:pointer;'
                                        onClick='busqueda_pacientes("paciente_rut2", function() { buscar_paciente2(); });'>
                                        <img src='iconos/cross.png' style='cursor:pointer;'
                                        onClick='$("pac_id2").value="";$("paciente_rut2").value="";$("nom_pac2").innerHTML="";'>
                                        
                                    </td>
                                    <td style='width:250px;' id='nom_pac2'>
                                    </td>
                                </tr>
                                </table>
                            </td>
                        </tr>
                        </table>
                    </form>
                </div>
                <div class='sub-content2' style='height:180px;overflow:auto;' id='listado_movs'>
                </div>
                <center>
                    <table>
                    <tr>
                        <td>
                            <div class='boton'>
                                <table>
                                <tr>
                                    <td>
                                        <img src='iconos/printer.png'>
                                    </td>
                                    <td>
                                        <a href='#' onClick='verifica_tabla();'> Imprimir Listado...</a>
                                    </td>
                                </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                    </table>
                </center>
            </div>
        </td>
    </tr>
    </table>
</div>
</center>

<script> 

  redibujar(); lista_salidas(); 
  
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
