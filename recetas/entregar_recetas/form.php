<?php

  require_once("../../conectar_db.php");

	$sexohtml = desplegar_opciones("sexo", 
	"sex_id, sex_desc",'','true','ORDER BY sex_id'); 
	
  $estcivhtml = desplegar_opciones("estado_civil", 
	"estciv_id, estciv_nombre",'0','true', 'ORDER BY estciv_id');

	$nacionhtml = desplegar_opciones("nacionalidad", 
	"nacion_id, nacion_nombre",'0','true', 'ORDER BY nacion_id');

  $previsionhtml = desplegar_opciones("prevision", 
	"prev_id, prev_desc",'0','true','ORDER BY prev_id'); 
	
	$sangrehtml = desplegar_opciones("grupo_sanguineo", 
	"sang_id, sang_desc",'0','true','ORDER BY sang_id'); 
	
	$grupohtml = desplegar_opciones("grupos_etnicos", 
	"getn_id, getn_desc",'0','true','ORDER BY getn_id'); 
	
	$institucionhtml = desplegar_opciones("institucion_solicita", 
	"instsol_id, instsol_desc",'','true','ORDER BY instsol_desc');
  
  $comunahtml = desplegar_opciones("comunas", 
	"ciud_id, ciud_desc",'','true','ORDER BY ciud_desc');
  	
	$bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1',
  'bod_id IN ('._cav(10),')',	'ORDER BY bod_glosa'); 
	
?> 
  
		<script>
		
		abrir_despachos = function(receta_id) {
    
        l=(screen.availWidth/2)-250;
        t=(screen.availHeight/2)-200;
  
        win = window.open('recetas/entregar_recetas/despachos.php?receta_id='+receta_id, 
                          'ver_despacho',
                          'scrollbars=yes, toolbar=no, left='+l+', top='+t+', '+
                          'resizable=no, width=500, height=415');
                          
        win.focus();
    
    }
		
		calcular_edad = function() {
		
			if(trim($('paciente_fecha').value)=='') {
				$('paciente_fecha').style.background='';
				return;
			}
		
			
			if(isDate($('paciente_fecha').value)) {
				$('mostrar_edad').innerHTML='<i>'+calc_edad($('paciente_fecha').value)+'</i>';
				$('paciente_fecha').style.background='inherit';
			} else {
				alert('Fecha de Nacimiento Incorrecta.');
        		$('paciente_fecha').style.background='red';
			}
			
		}
		
		deshabilitar_ficha_basica = function(estado) {
            
            $('paciente_nombre').disabled=estado;
						$('paciente_paterno').disabled=estado;
						$('paciente_materno').disabled=estado;
						$('paciente_dire').disabled=estado;
						$('paciente_comuna').disabled=estado;
						$('paciente_fecha').disabled=estado;
						$('paciente_sexo').disabled=estado;
						$('paciente_prevision').disabled=estado;
						$('paciente_sangre').disabled=estado;
						$('paciente_grupo').disabled=estado;
						$('paciente_sector').disabled=estado;
						$('paciente_fono').disabled=estado;
						$('paciente_nacion').disabled=estado;
						$('paciente_estciv').disabled=estado;
						$('paciente_cod_prev').disabled=estado;
						$('paciente_tramo').disabled=estado;
						$('paciente_padre').disabled=estado;
						$('paciente_madre').disabled=estado;
						
    
    }
		
		buscar_paciente = function()
        {
            $('cargando').style.display='';
            var myAjax = new Ajax.Request(
			'registro.php', 
			{
				method: 'get', 
				parameters: 'tipo=paciente&'+$('paciente_rut').serialize(),
				onComplete: function (pedido_datos)
                {
                    if(pedido_datos.responseText=='')
                    {
                        $('paciente_id').value=0;
						$('paciente_nombre').value='';
						$('paciente_paterno').value='';
						$('paciente_materno').value='';
						$('paciente_dire').value='';
						$('paciente_comuna').value=-1;
						$('paciente_fecha').value='';
						$('paciente_sexo').value='';
						$('paciente_prevision').value=0;
						$('paciente_sangre').value=-1;
						$('paciente_grupo').value=0;
						$('paciente_tramo').value='';
						
						actualizar_cod_prev();
						
						deshabilitar_ficha_basica(true);
						
						$('mostrar_edad').innerHTML='';

						
						alert('Paciente no est&aacute; ingresado al sistema'.unescapeHTML());
					}
                    else
                    {
                        datosxxx = eval(trim(pedido_datos.responseText));
                        $('paciente_id').value=datosxxx[0].unescapeHTML();
						$('paciente_nombre').value=datosxxx[2].unescapeHTML();
						$('paciente_paterno').value=datosxxx[3].unescapeHTML();
						$('paciente_materno').value=datosxxx[4].unescapeHTML();
						$('paciente_dire').value=datosxxx[11].unescapeHTML();
						$('paciente_comuna').value=datosxxx[12];
						$('paciente_fecha').value=datosxxx[5];
						$('paciente_sexo').value=datosxxx[6];
						$('paciente_prevision').value=datosxxx[7];
						$('paciente_tramo').value=datosxxx[18].unescapeHTML();
						$('paciente_sangre').value=datosxxx[10];
						$('paciente_grupo').value=datosxxx[9];
						$('paciente_sector').value=datosxxx[8].unescapeHTML();
						$('paciente_fono').value=datosxxx[15].unescapeHTML();
						$('paciente_nacion').value=datosxxx[13];
						$('paciente_estciv').value=datosxxx[14];
						$('paciente_padre').value=datosxxx[16].unescapeHTML();
						$('paciente_madre').value=datosxxx[17].unescapeHTML();
			
						calcular_edad();
						
						actualizar_cod_prev();
						
						deshabilitar_ficha_basica(true);
						
						listar_parientes(datosxxx[0]*1,0);
						
						mostrar_recetas();
						mostrar_historial();
                    }
					
					$('cargando').style.display='none';
				}
			}
			
			);
        }
    
    listar_parientes = function(id_paciente, modo) {
    
      if(modo==0) {
      
      var myAjax = new Ajax.Updater(
			'parentezco_div',
      'ficha_clinica/parientes.php', 
			{
				method: 'get', 
				parameters: 'accion=listar_parientes_estatico&paciente='+id_paciente,
				evalScripts: true
			}
			
			);
			
			} else {
			
      var myAjax = new Ajax.Updater(
			'parentezco_div',
      'ficha_clinica/parientes.php', 
			{
				method: 'get', 
				parameters: 'accion=listar_parientes&paciente='+id_paciente
			}
			
			);
			
      }
    
    }
    
    abrir_pariente = function (rut_pariente) {
    
      $("paciente_rut").disabled=false; 
      $("paciente_rut").value=rut_pariente;
      verificar_rut();			
    
    
    }
		
		verificar_rut = function() 
        {
            if(comprobar_rut($('paciente_rut').value))
            {
                $('paciente_rut').style.background='inherit';
                buscar_paciente();
            }
            else
            {
                alert('Rut Incorrecto.');
                $('paciente_rut').style.background='red';
            }
        }
    
    mostrar_recetas = function() {
    
      $('tab_recetas_content').innerHTML='<br><br><br><br><center><img src="imagenes/ajax-loader3.gif"><br><br>Cargando</center>';
    
      var myAjax = new Ajax.Updater(
			'tab_recetas_content', 
			'recetas/entregar_recetas/recetario.php', 
			{
				method: 'get', 
				parameters: 'tipo=recetario&paciente='+$('paciente_id').value+'&'+$('bodega_id').serialize(),
				evalScripts: true
	
			}
	    );
	    }
	    
	    mostrar_historial = function (){

	    $('tab_historial_content').innerHTML='<br><br><br><br><center><img src="imagenes/ajax-loader3.gif"><br><br>Cargando</center>';
	    
	    var myAjax2 = new Ajax.Updater(
			'tab_historial_content', 
			'recetas/entregar_recetas/historial_recetas.php', 
			{
				method: 'get', 
				parameters: 'paciente='+$('paciente_id').value+'&'+$('bodega_id').serialize(),
				evalScripts: true
	
			}
	    );
	    
	 
	  }
   
    entregar_receta = function(numero) {
  
    var win = new Window("mostrar_lotes", {className: "alphacube", 
                          top:40, left:0, 
                          width: 400, height: 400, 
                          title: '<img src="iconos/pill_go.png"> Lotes para Despacho de Receta',
                          minWidth: 400, minHeight: 400,
                          maximizable: false, minimizable: false, 
                          wiredDrag: true, resizable: false });
                          
    win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})
    
    win.setAjaxContent('recetas/entregar_recetas/mostrar_lotes.php', 
			{
				method: 'get', 
				evalScripts: true,
        parameters: 'bodega_id='+$('bodega_id').value+'&receta_id='+numero+'&'+serializar_objetos($('receta_'+numero))+'&'+$('paciente_id').serialize()
	
			});
			
		$("mostrar_lotes").win_obj=win;
  
    win.setDestroyOnClose();
    win.showCenter();
    win.show(true);
  
    }
  
  
  	ver_ficha = function () {
    
      tab_up('tab_ficha');
      tab_down('tab_refs');
      tab_down('tab_recetas');
      tab_down('tab_historial');
    
    }
		
		ver_refs = function () {
    
      tab_down('tab_ficha');
      tab_up('tab_refs');
      tab_down('tab_recetas');
      tab_down('tab_historial');
    
    }
    
    ver_recetas = function() {
      
      tab_down('tab_ficha');
      tab_down('tab_refs');
      tab_up('tab_recetas');
      tab_down('tab_historial');
    
    }
    
    ver_historial = function() {
    
      tab_down('tab_ficha');
      tab_down('tab_refs');
      tab_down('tab_recetas');
      tab_up('tab_historial');
    
    }
    
    actualizar_cod_prev = function() {
      $("paciente_cod_prev").disabled=false;
      $("paciente_cod_prev").value=$("paciente_prevision").value;
      $("paciente_cod_prev").disabled=true;
    }
    
    imprimir_talonario = function (receta_id) {
		
				win = window.open('talonario.php?receta_id='+receta_id,
      	   	               'win_talonario');
   	   	win.focus();
   	}
		
  
  </script>
  

<center>
    <table width=670>
        <tr>
            <td>
                <div class='sub-content'>
                    <table>
                    <tr>
                        <td>
                            Ubicaci&oacute;n:
                        </td>
                        <td>
                            <select name='bodega_id' id='bodega_id'>
                                <?php echo $bodegashtml; ?>
                            </select>
                        </td>
                    </tr>
                    </table>
                </div>
    
                <div class='sub-content'>
                    <div class='sub-content'><img src='iconos/user_red.png'> <b>Datos del Paciente</b></div>
                    <div class='sub-content'>
                        <table>
                        <tr style='height:22px; font-weight: bold;'>
                            <td style='text-align:center;'>
                                <center>
                                    <table border=0 cellpadding=0 cellspacing=0>
                                    <tr>
                                        <td style='font-weight: bold;'>RUT</td>
                                        <td>
                                            <img src='imagenes/ajax-loader1.gif' id='cargando' style='display: none;'>
                                        </td>
                                    </tr>
                                    </table>
                                </center>
                            </td>
                            <td style='text-align:center;'>Nombre(s)</td>
                            <td style='text-align:center;'>Apellido Paterno</td>
                            <td style='text-align:center;'>Apellido Materno</td>
                        </tr>
                        <tr>
                        <td>
                            <input type='text' id='paciente_rut' name='paciente_rut' size='12'
                            style='text-align: center;' onKeyUp='
                            if(event.which==13) { this.value=this.value.toUpperCase();
                            verificar_rut(); }
                            ' maxlength=11>
                        </td>
                        <td>
                            <input type='text' id='paciente_nombre' name='paciente_nombre' size='22' onKeyUp='
                            if(event.which==8 && this.value.length==0) $("paciente_rut").focus();
                            ' maxlength=100>
                        </td>
                        <td>
                            <input type='text' id='paciente_paterno' name='paciente_paterno' size='22'
                                onKeyUp='
                                if(event.which==8 && this.value.length==0) $("paciente_nombre").focus();
                            ' maxlength=50>
                        </td>
                        <td>
                            <input type='text' id='paciente_materno' name='paciente_materno' size='22'
                            onKeyUp='
                            if(event.which==8 && this.value.length==0) $("paciente_paterno").focus();
                            ' maxlength=50>
                        </td>
                        </tr>
                        </table>
                    </div>
                    <div class='sub-content' id='ajax_fonasa' style='display: none;'>
                        <center>
                        <table>
                            <tr>
                                <td>
                                    <img src='imagenes/ajax-loader3.gif'>
                                </td>
                                <td>
                                    <b>Conectando con Fonasa...</b><br>
                                </td>
                            </tr>
                        </table>
                        </center>
                    </div>
                    <table cellpadding=0 cellspacing=0>
                    <tr>
                        <td>
                            <div class='tabs' id='tab_ficha' style='cursor: default;'
                                onClick='ver_ficha();'>
                                <img src='iconos/report_user.png'>
                                Ficha B&aacute;sica
                            </div>
                        </td>
                        <td>
                            <div class='tabs_fade' id='tab_refs' style='cursor: pointer;'
                                onClick='ver_refs();'>
                                <img src='iconos/group.png'>
                                Referencias
                            </div>
                        </td>
                        <td>
                            <div class='tabs_fade' id='tab_recetas' style='cursor: pointer;'
                                onClick='ver_recetas();'>
                                <img src='iconos/pill_go.png'>
                                Recetas Vigentes
                            </div>
                        </td>
                        <td>
                            <div class='tabs_fade' id='tab_historial' style='cursor: pointer;'
                                onClick='ver_historial();'>
                                <img src='iconos/pill.png'>
                                Historial de Recetas
                            </div>
                        </td>
                    </tr>
                    </table>
                    <div class='tabbed_content' id='tab_ficha_content'>
                        <center>
                            <input type='hidden' id='paciente_id' name='paciente_id' value=0>
                            <table width=100%>
                                <tr style='text-align: center;'>
                                    <td colspan=2>Direcci&oacute;n:</td>
                                    <td>Sector:</td>
                                    <td>Tel&eacute;fono:</td>
                                </tr>
                                <tr style='text-align: center;'>
                                    <td colspan=2>
                                        <input type='text' name='paciente_dire' id='paciente_dire' size=45>
                                    </td>
                                    <td>
                                        <input type='text' name='paciente_sector' id='paciente_sector' size=24
                                        maxlength=80>
                                    </td>
                                    <td>
                                        <input type='text' name='paciente_fono' id='paciente_fono' size=15
                                        maxlength=60>
                                    </td>
                                </tr>
                                <tr style='text-align: center;'>
                                    <td>Comuna:</td>
                                    <td>Nacionalidad:</td>
                                    <td>Fecha de Nacimiento:</td>
                                    <td>Edad:</td>
                                </tr>
                                <tr style='text-align: center;'>
                                    <td>
                                        <select name='paciente_comuna' id='paciente_comuna'>
                                            <option value=-1>(Seleccionar Comuna...)</option>
                                            <?php echo $comunahtml; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select id='paciente_nacion' name='paciente_nacion'>
                                            <?php echo $nacionhtml; ?>
                                        </select>
                                    </td>
                                    <td style='text-align: center;'>
                                        <input type='text' id='paciente_fecha' name='paciente_fecha'
                                        style='text-align: center;' onBlur='calcular_edad();'>
                                    </td>
                                    <td width=200 id='mostrar_edad' style='text-align: center;'></td>
                                </tr>
                                <tr style='text-align: center;'>
                                    <td>Estado Civil:</td>
                                    <td>Grupo Sangu&iacute;neo:</td>
                                    <td>Grupo &Eacute;tnico:</td>
                                    <td>Sexo</td>
                                </tr>
                                <tr style='text-align: center;'>
                                    <td>
                                        <select id='paciente_estciv' name='paciente_estciv'>
                                            <?php echo $estcivhtml; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select id='paciente_sangre' name='paciente_sangre'>
                                            <?php echo $sangrehtml; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select id='paciente_grupo' name='paciente_grupo'>
                                            <?php echo $grupohtml; ?>
                                        </select>
                                    </td>
                                    <td rowspan=2 valign='top'>
                                        <select id='paciente_sexo' name='paciente_sexo'>
                                            <?php echo $sexohtml; ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                            <div class='sub-content'>
                                <div class='sub-content'>
                                    <img src='iconos/vcard.png'> Informaci&oacute;n Previsional
                                </div>
                                <div class='sub-content2'>
                                    <table width=100%>
                                        <tr style='text-align: center;'>
                                            <td>Previsi&oacute;n:</td>
                                            <td>
                                                <input type='text' size='2'
                                                id='paciente_cod_prev' name='paciente_cod_prev'
                                                style='text-align: center;' value=0>
                                            </td>
                                            <td>
                                                <select id='paciente_prevision' name='paciente_prevision'
                                                    onClick='actualizar_cod_prev();'>
                                                    <?php echo $previsionhtml; ?>
                                                </select>
                                            </td>
                                            <td>Tramo:</td>
                                            <td>
                                                <input type='text' size='1' maxlength=1
                                                id='paciente_tramo' name='paciente_tramo'
                                                style='text-align: center;'>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </center>
                    </div>
                    <div class='tabbed_content' id='tab_refs_content' style='display: none;'>
                        <table>
                        <tr>
                            <td style='text-align: right;'>Nombre del Padre:</td>
                            <td>
                                <input type='text' size=70 maxlength=200
                                id='paciente_padre' name='paciente_padre'>
                            </td>
                        </tr>
                        <tr>
                            <td style='text-align: right;'>Nombre de la Madre:</td>
                            <td>
                                <input type='text' size=70 maxlength=200
                                id='paciente_madre' name='paciente_madre'>
                            </td>
                        </tr>
                        </table>
                        <div class='sub-content'>
                            <div class='sub-content'>
                                <img src='iconos/group_link.png'> Parentezco
                            </div>
                            <div class='sub-content2' id='parentezco_div' name='parentezco_div'
                                style='min-height: 100px; height: 100px; overflow: auto;'>
                            </div>
                        </div>
                    </div>
                    <div class='tabbed_content' id='tab_recetas_content' style='
                        height:200px; min-height:200px; overflow: auto;
                        display: none;'>
                    </div>
                    <div class='tabbed_content' id='tab_historial_content' style='
                        height:200px; min-height:200px; overflow: auto;
                        display: none;'>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</center>

<script> 
deshabilitar_ficha_basica(true); 
$('paciente_rut').focus();
</script>
  
