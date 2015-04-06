<?php
    require_once('../../conectar_db.php');  
?>
<script>
    listar_convenios = function() {
        $('mostrar_convenio').style.display='none';
        $('filtro_provee').style.display='';
        var myAjax = new Ajax.Updater('lista_convenios','administracion/convenios/listar_convenios.php',
        {
            method: 'post',
            parameters: 'prov_id='+($('id_proveedor2').value*1)+'&'+$('sel_id_licitacion').serialize()+'&'+$('art_id2').serialize()+'&'+$('filtro_estado').serialize()
        });
    }

    listar_convenios_excel = function(){
        art_id2=$('art_id2').serialize();
        filtro_estado=$('filtro_estado').serialize();
        //param='prov_id='+($('id_proveedor2').value*1)+'&'+$('sel_id_licitacion').serialize()+'&'+$('art_id2').serialize()+'&'+$('filtro_estado').serialize();
	nuevaVentana=window.open('administracion/convenios/listar_convenios_excel.php?prov_id='+($('id_proveedor2').value*1)+'&'+$('sel_id_licitacion').serialize()+'&'+$('art_id2').serialize()+'&'+$('filtro_estado').serialize(),'',"toolbar=0,location=0,status=0,width=600,height=400");
    }


    validacion_fecha2=function(obj) {
        var objeto=$(obj);
        var val=trim(objeto.value);
        if(objeto.value=='') {
            objeto.style.background='';
            return true;
	} else {
            return validacion_fecha(objeto);
        }
    }

    buscar_licitacion = function(){
        var myAjax = new Ajax.Request('administracion/convenios/buscar_licitacion.php',
        {
            method: 'get',
            parameters: $('convenio_licitacion').serialize(),
            onComplete: function(respuesta) {
                try {
                    datos = respuesta.responseText.evalJSON(true);
                } catch(err) { 
                    alert('ERROR:\n\n'+err); 
                }
      
                if(datos[0]) {
                    $('categoria').value=datos[1][0];  
                    $('tipo_lic').value=datos[1][1];
                    $('nombre_convenio').value=datos[1][2]; 
                    $('res_aprueba').value=datos[1][3];
                    $('res_adjudica').value=datos[1][4];
                    $('fecha_aprueba').value=datos[1][5];
                    $('fecha_adjudica').value=datos[1][6];
                    validacion_fecha($('fecha_aprueba'));
                    validacion_fecha($('fecha_adjudica'));
                }
            }
        });
    }

    buscar_codigo_prod = function() {
        $('art_cargando').style.display='';
        $('art_id').value=0;
        var myAjax = new Ajax.Request('administracion/convenios/abrir_articulo.php',
        {
            method: 'get',
            parameters: $('art_codigo').serialize(),
            onComplete: function(respuesta) {
                $('art_cargando').style.display='none';
                try {
                    datos = respuesta.responseText.evalJSON(true);
                } catch(err) { 
                    alert('ERROR:\n\n'+err); 
                }
                if(datos[0]) {
                    $('art_id').value=datos[1][0];
                    $('nombre_articulo').innerHTML='<span id="__art_id_'+datos[1][0]+'" class="texto_tooltip">'+datos[1][2]+'</span>';
                } else {
                    $('nombre_articulo').innerHTML='<i>Art&iacute;culo no existe.</i>';
                }
            }
        });
    }

    agregar_articulo=function() {
        $('agregar_articulo').style.display='';
        $('agregar_articulo_boton').style.display='none';
        $('codigo').focus();
    }

    cancelar_articulo=function() {
        $('agregar_articulo').style.display='none';
        $('agregar_articulo_boton').style.display='';
        $('art_id').value=0;  
        $('codigo').value='';
        $('art_nombre').innerHTML='';
    }

    insertar_articulo=function() {
        art_id = ($('art_id').value*1);
        convenio_id = ($('convenio_id').value*1);
        if(art_id==0 || convenio_id==0)
            return;
        
        var myAjax = new Ajax.Request('administracion/convenios/sql_articulo.php',
        {
            method: 'get',
            parameters: 'convenio_id='+convenio_id+'&art_id='+art_id+'&'+$('conveniod_punit').serialize()+'&'+$('conveniod_cant').serialize(),
            onComplete: function(respuesta) {
                try {
                    datos = respuesta.responseText.evalJSON(true);
                } catch (err) {
                    alert(err);
                }
                if(datos[0]==true) {
                    abrir_convenio(convenio_id);
                } else {
                    alert('El art&iacute;culo ya est&aacute; asociado a un convenio. '+'Nombre del Convenio ['+datos[1]+']'.unescapeHTML());
                }
            }
        });
    }

    quitar_articulo = function(id)
    {
        convenio_id = ($('convenio_id').value*1);
        if(art_id==0 || convenio_id==0)
            return;
        
        confirma=confirm('&iquest;Est&aacute; seguro que desea eliminar el art&iacute;culo de convenio? - No hay opciones para deshacer.'.unescapeHTML());
        if(confirma)
        {
            var myAjax = new Ajax.Request('administracion/convenios/sql_articulo_quitar.php',
            {
                method: 'get',
                parameters: 'convenio_id='+convenio_id+'&art_id='+id,
                onComplete: function(respuesta)
                {
                    try {
                        datos = respuesta.responseText.evalJSON(true);
                    }
                    catch (err)
                    {
                        alert(err);
                    }
                    if(datos==true)
                    {
                        abrir_convenio(convenio_id);
                    }
                }
            });
        }
    }

    guardar_convenio=function()
    {
        if(trim($('convenio_licitacion').value)=='')
        {
            alert('Debe ingresar ID de licitaci&oacute;n.'.unescapeHTML());
            $('convenio_licitacion').focus();
            return;
	}
		
	if($('categoria').value=='-1')
        {
            alert('Debe seleccionar la categor&iacute;a Bienes o Servicios.'.unescapeHTML());
            $('categoria').focus();
            return;
	}
		
	if($('tipo_lic').value=='-1')
        {
            alert('Debe seleccionar el tipo de licitaci&oacute;n.'.unescapeHTML());
            $('categoria').focus();
            return;
	}

		
	if($('id_proveedor').value*1==0)
        {
            alert('Debe seleccionar un proveedor.');
            $('nombre_proveedor').focus();
            return;
	}

	if($('monto').value*1==0)
        {
            alert('Debe seleccionar un monto total para el convenio.');
            $('monto').focus();
            return;
	}

	if($('plazo').value*1==0)
        {
            alert( 'Debe seleccionar un plazo en dias mayor que cero.'.unescapeHTML() );
            $('plazo').focus();
            return;
	}

	if(!validacion_fecha($('inicio')))
        {
            alert( 'Debe seleccionar una fecha inicial v&aacute;lida.'.unescapeHTML() );
            $('inicio').focus();
            return;
	}

	if(!validacion_fecha($('termino')))
        {
            alert( 'Debe seleccionar una fecha final v&aacute;lida.'.unescapeHTML() );
            $('termino').focus();
            return;
	}

	/*
        if(!validacion_fecha($('fecha_aprueba'))) {
			alert( 'Debe seleccionar una fecha de aprobaci&oacute;n de las bases v&aacute;lida.'.unescapeHTML() );
			$('fecha_aprueba').focus();
			return;
	}
        */

	/*
        if(!validacion_fecha($('fecha_adjudica'))) {
			alert( 'Debe seleccionar una fecha de adjudicaci&oacute;n v&aacute;lida.'.unescapeHTML() );
			$('fecha_adjudica').focus();
			return;
	}
        */

	if(!validacion_fecha2($('fecha_prorroga')))
        {
            alert( 'Debe seleccionar una fecha de prorroga v&aacute;lida.'.unescapeHTML() );
            $('fecha_prorroga').focus();
            return;
	}

	if(!validacion_fecha2($('fecha_aumento')))
        {
            alert( 'Debe seleccionar una fecha de aumento de contrato v&aacute;lida.'.unescapeHTML() );
            $('fecha_aumento').focus();
            return;
	}

	if($('res_prorroga').value!='')
        {
            if($('sel_aprueba').value=='-1')
            {
                alert('Debe seleccionar tipo APRUEBA');
		$('sel_aprueba').focus();
		return;
            }
	}

        /*if(!validacion_fecha($('fecha_contrato'))) {
            alert( 'Debe seleccionar una fecha de aprobaci&oacute;n del contrato v&aacute;lida.'.unescapeHTML() );
            $('fecha_contrato').focus();
            return;
	}*/

	if(trim($('fecha_boleta').value)!='' && !validacion_fecha($('fecha_boleta')))
        {
            alert( 'Debe seleccionar una fecha de boleta de garant&iacute;a v&aacute;lida.'.unescapeHTML() );
            $('fecha_boleta').focus();
            return;
	}

	var myAjax = new Ajax.Request('administracion/convenios/sql.php',
        {
            method: 'post',
            parameters: $('datos_convenio').serialize(),
            onComplete: function(respuesta)
            {
                r=respuesta.responseText;	
		//alert(r);
		var res=r.split(",");	
		if(res[0]=='1')
                {
                    var archivos = document.getElementById("archivos");//Damos el valor del input tipo file
                    var archivo = archivos.files; //Obtenemos el valor del input (los arcchivos) en modo de arreglo
                    if(window.XMLHttpRequest)
                    { 
                        var Req = new XMLHttpRequest();
                    }
                    else if(window.ActiveXObject)
                    { 
                        var Req = new ActiveXObject("Microsoft.XMLHTTP"); 
                    }
				
                    var data = new FormData();
                    for(i=0; i<archivo.length; i++)
                    {
                        data.append('archivo'+i,archivo[i]);
                    }
                    var ruta = 'administracion/convenios/adjuntos_convenio/';
                    var tabla= 'convenio_adjuntos';
                    func_id = $('func_id').value;
                    Req.open("POST", "subir.php?id="+res[1]+'&ruta='+ruta+'&tabla='+tabla+'&func_id='+func_id+'&convenio='+1, true);
                    $('archivos').value = '';
                    Req.onload = function(Event)
                    {
                        //Validamos que el status http sea ok 
			var msg = Req.responseText;
			if (Req.status == 200)
                        { 
                            //Recibimos la respuesta de php
                            if(msg != true)
                                alert('Convenio Ingresado exitosamente.\n'+msg+' '.unescapeHTML());
                            
                            if($('convenio_id').value == 0)
                            {
                                
                                var myAjax = new Ajax.Request('administracion/convenios/enviar_correo.php',
                                {
                                    method: 'post',
                                    parameters: 'convenio_id='+res[1],
                                    onComplete: function(respuesta)
                                    {
                                        abrir_convenio(res[1]);
                                    }
                                });		
                            }
                            else
                            {
                                abrir_convenio(res[1]);
                            }
                        }
                        else
                        {
                            console.log(Req.status); //Vemos que paso.
                            alert(msg); 			
                            abrir_convenio(res[1]);	  
			} 
                        //$('cargando_img').style.display = 'none';					
                    };
                    //Enviamos la petición 
		    Req.send(data); 
                    //listar_convenios();
                    //$('h_estado').value='1';
                    //mostrar_historico();
                }
                else
                {
                    alert('ERROR:\n\n'+respuesta.responseText);
                }
            }
        });
    }

    abrir_convenio=function(id) {
        $('mostrar_convenio').style.display='';
		$('filtro_provee').style.display='none';
        $('nombre_convenio').innerHTML='<img src="imagenes/ajax-loader1.gif">';
        $('convenio_id').value=id;

        var myAjax = new Ajax.Updater('lista_convenios','administracion/convenios/abrir_convenio.php',
        {
            method: 'get',
            parameters: 'convenio_id='+(id*1),
            evalScripts: true
        });
        listar_adjuntos(id);
    }

    listar_adjuntos=function(id) {
        //$('convenio_id').value=id;
	var myAjax = new Ajax.Updater('adjuntos','administracion/convenios/buscar_archivos.php',
        {
            method: 'get',
            parameters: 'convenio_id='+(id*1),
            evalScripts: true
        });
    }
        
    //**************************************************
    eliminar_adjunto=function(cad_id,convenio_id)
    {
        //$('convenio_id').value=id;
        var myAjax = new Ajax.Updater('adjuntos','administracion/convenios/eliminar_archivo.php',
        {
            method: 'get',
            parameters: 'adjunto_id='+(cad_id*1)+'&convenio_id='+(convenio_id*1),
            evalScripts: true
        });
        listar_adjuntos(convenio_id);
    }
    //**************************************************
    eliminar_convenio = function(id) {
        confirma=confirm('&iquest;Est&aacute; seguro que desea eliminar este convenio? - No hay opciones para deshacer.'.unescapeHTML());
        if(confirma){
            var myAjax2 = new Ajax.Request('administracion/convenios/elimina_convenio.php',
            {
                method: 'get',
                parameters: 'convenio_id='+(id*1),
                onComplete: function(respuesta) {
                    if(respuesta.responseText=='2') {
                        alert('Convenio Eliminado exitosamente.');
                        listar_convenios();
                    } else {
                        alert('ERROR:\n\n'+respuesta.responseText);
                    }
                }
            });
        }
    }

 //**************************************************
 ver_convenio=function(convenio_id) {

    top=Math.round(screen.height/2)-225;
    left=Math.round(screen.width/2)-350;
    new_win = 
    window.open('administracion/convenios/ver_convenio.php?convenio_id='+convenio_id,
    'win_talonarios', 'toolbar=no, location=no, directories=no, status=no, '+
    'menubar=no, scrollbars=yes, resizable=yes, width=800, height=650, '+
    'top='+top+', left='+left);
	

	}
	
 ver_convenio_xls=function(convenio_id,xls) {

    top=Math.round(screen.height/2)-225;
    left=Math.round(screen.width/2)-350;
    new_win = 
    window.open('administracion/convenios/ver_convenio.php?convenio_id='+convenio_id+'&xls',
    'win_talonarios', 'toolbar=no, location=no, directories=no, status=no, '+
    'menubar=no, scrollbars=yes, resizable=yes, width=700, height=450, '+
    'top='+top+', left='+left);
	

	}
	
visualizar_documento=function(doc_id) {

        l=(screen.availWidth/2)-250;
        t=(screen.availHeight/2)-200;
        win = window.open('../../visualizar.php?doc_id='+doc_id,
		'win_documento', 'toolbar=no, location=no, directories=no, status=no, '+
		'menubar=no, scrollbars=yes, resizable=yes, width=700, height=450, '+
		'top='+top+', left='+left);
                            win.focus();
	}

ver_ordenes=function(convenio_id) {

    top=Math.round(screen.height/2)-225;
    left=Math.round(screen.width/2)-350;
    new_win = 
    window.open('administracion/convenios/ver_ordenes.php?convenio_id='+convenio_id,
    'win_ordenes', 'toolbar=no, location=no, directories=no, status=no, '+
    'menubar=no, scrollbars=yes, resizable=yes, width=700, height=450, '+
    'top='+top+', left='+left);
	

	}

visualizar_orden=function(orden_id) {

    top=Math.round(screen.height/2)-225;
    left=Math.round(screen.width/2)-350;
    new_win = 
    window.open('../../visualizar.php?orden_id='+orden_id,
    'win_orden', 'toolbar=no, location=no, directories=no, status=no, '+
    'menubar=no, scrollbars=yes, resizable=yes, width=700, height=450, '+
    'top='+top+', left='+left);
	

	}
	

  mostrar_proveedor=function(datos) {
    $('id_proveedor').value=datos[3];
    $('rut_proveedor').value=datos[1];
    $('nombre_proveedor').value=datos[2].unescapeHTML();
  }
  
  liberar_proveedor=function() {
    $('id_proveedor').value=-1;
    $('nombre_proveedor').value='';
    $('rut_proveedor').value='';
    $('nombre_proveedor').focus();
  }

  autocompletar_proveedores = new AutoComplete(
    'nombre_proveedor', 
    'autocompletar_sql.php',
    function() {
      if($('nombre_proveedor').value.length<3) return false;
      
      return {
        method: 'get',
        parameters: 'tipo=proveedores&busca_proveedor='+encodeURIComponent($('nombre_proveedor').value)
      }
    }, 'autocomplete', 450, 200, 250, 1, 2, mostrar_proveedor);


  mostrar_proveedor2=function(datos) {
    $('id_proveedor2').value=datos[3];
    $('rut_proveedor2').value=datos[1];
    $('nombre_proveedor2').value=datos[2].unescapeHTML();
    listar_convenios();
  }
  
  liberar_proveedor2=function() {
    $('id_proveedor2').value=0;
    $('nombre_proveedor2').value='';
    $('rut_proveedor2').value='';
    $('nombre_proveedor2').focus();
    listar_convenios();
  }

  autocompletar_proveedores = new AutoComplete(
    'nombre_proveedor2', 
    'autocompletar_sql.php',
    function() {
      if($('nombre_proveedor2').value.length<3) return false;
      
      return {
        method: 'get',
        parameters: 'tipo=proveedores&busca_proveedor='+encodeURIComponent($('nombre_proveedor2').value)
      }
    }, 'autocomplete', 450, 200, 250, 1, 2, mostrar_proveedor2);


  mostrar_funcionario=function(datos) {
    $('func_id').value=datos[3];
    $('rut_funcionario').value=datos[1];
    $('nombre_funcionario').value=datos[2].unescapeHTML();
  }
  
  liberar_funcionario=function() {
    $('func_id').value=-1;
    $('rut_funcionario').value='';
    $('nombre_funcionario').value='';
    $('nombre_funcionario').focus();
  }

  autocompletar_funcionario = new AutoComplete(
    'nombre_funcionario', 
    'autocompletar_sql.php',
    function() {
      if($('nombre_funcionario').value.length<3) return false;
      
      return {
        method: 'get',
        parameters: 'tipo=funcionarios&nomfuncio='+encodeURIComponent($('nombre_funcionario').value)
      }
    }, 'autocomplete', 450, 200, 250, 1, 2, mostrar_funcionario);




	abrir_articulo=function(d) {
		$('art_id').value=d[5];
		$('art_nombre').innerHTML=d[2];
		$('art_forma').innerHTML=d[3];
		$('conveniod_punit').value='';
		$('conveniod_punit').focus();
	}




	abrir_articulo2 = function(d) {
      	
      	$('art_id2').value=d[5];
		$('art_glosa2').value=d[2].unescapeHTML();
		$('art_codigo2').value=d[0];
		
		listar_convenios();
      	
    }

	liberar_articulo2=function() {
		
		$('sel_id_licitacion').value='';
		$('nombre_proveedor2').value='';
		$('rut_proveedor2').value='';
		$('id_proveedor2').value='';
      	$('art_id2').value='';
		$('art_glosa2').value='';
		$('art_codigo2').value='';
		
		listar_convenios();
	}
      
      autocompletar_medicamentos2 = new AutoComplete(
      'art_glosa2', 
      'autocompletar_sql.php',
      function() {
        if($('art_glosa2').value.length<3) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=buscar_arts&codigo='+encodeURIComponent($('art_glosa2').value)
        }
      }, 'autocomplete', 350, 200, 250, 1, 3, abrir_articulo2);

enviar_archivo = function() {

	

	  top=Math.round(screen.height/2)-750;

      left=Math.round(screen.width/2)-75;



	  var sendfile =window.open('administracion/convenios/convenio_archivo.php?'+$('convenio_id').serialize(),

	        'win_chat_file', 'toolbar=no, location=no, directories=no, status=no, '+

			'menubar=no, scrollbars=yes, resizable=no, width=800, height=200, '+

			'top='+top+', left='+left);

		

	  sendfile.focus();



	

}

muestra_campos = function(){
var campo = $('sel_aprueba').value;
	
	if(campo=='prorroga'){
		$('tr_resAprueba').style.display='';
		$('tr_monto_aumento').style.display='none';
		$('tr_nrores_prorroga').style.display='';
		$('tr_aumento_s').style.display='none';
		
	}else if($('sel_aprueba').value=='aumento'){
		$('tr_monto_aumento').style.display='';
		$('tr_resAprueba').style.display='';
		$('tr_nrores_prorroga').style.display='none';
		$('tr_aumento_s').style.display='';

	}else{
		$('tr_monto_aumento').style.display='none';
		$('tr_resAprueba').style.display='';
		$('tr_nrores_prorroga').style.display='none';
		$('tr_aumento_s').style.display='none';
	}	
}

comprueba_campos = function(){

	var campo = $('sel_aprueba').value;
	
	//alert(campo);
	
	if(campo=='prorroga'){
		$('tr_resAprueba').style.display='';
		$('tr_monto_aumento').style.display='none';
		$('tr_nrores_prorroga').style.display='';
		$('tr_aumento_s').style.display='none';
		$('plazo').value='';
		$('plazo2').value='';
		$('tipo_garantia').value=0;
		$('nro_boleta').value='';
		$('fecha_boleta').value='';
		$('banco_boleta').value='';
		$('monto_boleta').value='';
		$('multa').value='';
		$('resp_apr_prorroga').value='';
		$('fecha_nro_prorroga').value='';
		$('inicio').value='';
		$('termino').value='';
		$('comenta').value='';
		
	}else if($('sel_aprueba').value=='aumento'){
		$('tr_monto_aumento').style.display='';
		$('tr_resAprueba').style.display='';
		$('tr_nrores_prorroga').style.display='none';
		$('tr_aumento_s').style.display='';
		$('plazo').value='';
		$('plazo2').value='';
		$('tipo_garantia').value=0;
		$('nro_boleta').value='';
		$('fecha_boleta').value='';
		$('banco_boleta').value='';
		$('monto_boleta').value='';
		$('multa').value='';
		$('t_aumento').value='';
		$('b_aumento').value='';
		$('inicio').value='';
		$('termino').value='';
		$('comenta').value='';
	}else{
		$('tr_monto_aumento').style.display='none';
		$('tr_resAprueba').style.display='';
		$('tr_nrores_prorroga').style.display='none';
		$('tr_aumento_s').style.display='none';
		//$('plazo').value='';
		//$('plazo2').value='';
		//$('tipo_garantia').value=0;
		//$('nro_boleta').value='';
		//$('fecha_boleta').value='';
		//$('banco_boleta').value='';
		//$('monto_boleta').value='';
		//$('multa').value='';
		//$('inicio').value='';
		//$('termino').value='';
		//$('comenta').value='';
		$('monto').value='';
		
	}
	
}
cerosIzq = function(sVal, nPos){
    var sRes = sVal;
    for (var i = sVal.length; i < nPos; i++)
     sRes = "0" + sRes;
    return sRes;
}
 
armaFecha = function(nDia, nMes, nAno){
    var sRes = cerosIzq(String(nDia), 2);
    sRes = sRes + "/" + cerosIzq(String(nMes), 2);
    sRes = sRes + "/" + cerosIzq(String(nAno), 4);
    return sRes;
}
   
sumaMes = function(nDia, nMes, nAno, nSum){
    if (nSum >= 0){
     for (var i = 0; i < Math.abs(nSum); i++){
      if (nMes == 12){
       nMes = 1;
       nAno += 1;
      } else nMes += 1;
     }
    } else {
     for (var i = 0; i < Math.abs(nSum); i++){
      if (nMes == 1){
       nMes = 12;
       nAno -= 1;
      } else nMes -= 1;
     }
    }
    return armaFecha(nDia, nMes, nAno);
}

 calculaFecha = function(){
	 campo = '';
	 if($('sel_aprueba').value=='contrato'){
		campo=$('fecha_prorroga');
	 }else if($('sel_aprueba').value=='prorroga'){
		campo=$('fecha_nro_prorroga');
	 }else if($('sel_aprueba').value=='aumento'){
		 campo=$('b_aumento');
	 }
	 
    var sFc0 = campo.value; // Se asume válida
    var nSum = parseInt($('plazo2').value);
    var sFc1 = sFc0;
    if (!isNaN(nSum)){
     var nDia = parseInt(sFc0.substr(0, 2));
     var nMes = parseInt(sFc0.substr(3, 2));
     var nAno = parseInt(sFc0.substr(6, 4));
     sFc1 = sumaMes(nDia, nMes, nAno, nSum);
    }
    $('termino').value = sFc1;
    validacion_fecha($('termino'));
   }
   
agrega_fecha = function(){
	/**
	if($('res_aprueba').value.length>0){
		var fecha1 = new Date();
		var dd = fecha1.getDate();
		var mm = fecha1.getMonth()+1; //January is 0!

		var yyyy = fecha1.getFullYear();
		if(dd<10){dd='0'+dd} if(mm<10){mm='0'+mm} var fecha = dd+'/'+mm+'/'+yyyy;
		$('inicio').value=fecha;
	}else{
		$('inicio').value='';
	}
	validacion_fecha($('inicio'));*/
		if($('sel_aprueba').value=='contrato'){
			if($('fecha_prorroga').value.length>6){
				$('inicio').value=$('fecha_prorroga').value;
				$('plazo2').value;
			}else{
				$('inicio').value='';
			}
		}else if($('sel_aprueba').value=='prorroga'){
			if($('fecha_nro_prorroga').value.length>6){
				$('inicio').value=$('fecha_nro_prorroga').value;
				$('plazo2').value;
			}else{
				$('inicio').value='';
			}
		}else if($('sel_aprueba').value=='aumento'){
			if($('b_aumento').value.length>6){
				$('inicio').value=$('b_aumento').value;
				$('plazo2').value;
			}else{
				$('inicio').value='';
			}
		}
		validacion_fecha($('inicio'));
		
		/**if($('fecha_nro_prorroga').value.length>6){
			$('inicio').value=$('fecha_nro_prorroga').value;
			$('plazo2').value;
		}else{
			$('inicio').value='';
		}
		validacion_fecha($('inicio'));*/
	
	
}


agregar_multa = function(id_convenio,nro_lic,tipo,multa_id){
    
    tipo=tipo*1;
    multa_id=multa_id*1;
    l=(screen.availWidth/2)-250;
    t=(screen.availHeight/2)-200;
    new_win = window.open('administracion/convenios/form_multas.php?'+'licitacion='+id_convenio+'&licitacion_nro='+nro_lic+'&tipo='+tipo+'&multa_id='+multa_id, 'win_multas',
                      'scrollbars=yes, toolbar=no, left='+l+', top='+t+', '+
                      'resizable=no, width=470, height=480');
   
    new_win.focus();
}

eliminar_multa = function(multa_id){
	
	confirma=confirm('&iquest;Est&aacute; seguro que desea eliminar esta multa?'.unescapeHTML());

     if(confirma){

       var myAjax2 = new Ajax.Request(
		'administracion/convenios/elimina_multa.php',
		{
			method: 'post',
            parameters: 'multa_id='+(multa_id*1),
            onComplete: function(respuesta) {
                
				if(respuesta) {
					alert('Multa eliminada!');
					listado_multas();
              } else {
					alert('ERROR:\n\n'+respuesta.responseText);
              }
            }
        });
     }
	
}

listado_multas=function() {
	
		var params=$('convenio_id').serialize();		
	
		var myAjax=new Ajax.Updater(
			'listado_multas',
			'administracion/convenios/listado_multas.php',
			{  method:'post', parameters:params 	}	
			
		);
	
}

muestra_multa = function(){

	campo = $('c_multa').value*1;
	
	if(campo==0){
		$('listado_multas').hide();
		$('c_multa').value='1';
		$('btn_ver_multas').value='(Ver Multas)';
	}else{
		$('listado_multas').show();
		$('c_multa').value='0';
		$('btn_ver_multas').value='(Esconder Multas)';
	}

}
muestra_multa();

mostrar_historico = function(){
	
	if($('h_estado').value=='0'){
		$('tr_historico').show();
		$('h_estado').value='1';
		$('btn_hist').value='(Esconder Hist&oacute;rico...)'.unescapeHTML();
	}else{
		$('tr_historico').hide();
		$('h_estado').value='0';
		$('btn_hist').value='(Ver Hist&oacute;rico...)'.unescapeHTML();
	}
}

comprueba_campos();
agrega_fecha();
</script>

<center>
    <table style='width:950px;'>
        <tr>
            <td>
                <div class='sub-content'>
                    <div class='sub-content'>
                        <img src='iconos/database_link.png'> <b>Convenios</b>
                    </div>
                    <div class='sub-content' id="mostrar_convenio">
                        <center>
                            <form id='datos_convenio' name='datos_convenio' onSubmit='return false;'>
                                <input type='hidden' name='convenio_id' id='convenio_id' value='' />
                                <table>
                                    <tr>
                                        <td style='text-align:right;'>ID Licitaci&oacute;n:</td>
                                        <td colspan=3>
                                            <input type='text' id="convenio_licitacion" name="convenio_licitacion" value="" onkeyup="if(event.which==13) buscar_licitacion();" />
                                            <select id="categoria" name="categoria">
                                                <option value="-1" SELECTED>(Seleccionar...)</option>
                                                <option value="bienes">Bienes</option>
                                                <option value="servicios">Servicios</option>
                                            </select>
                                            <select id="tipo_lic" name="tipo_lic">
                                                <option value="-1" SELECTED>(Seleccionar...)</option>
                                                <option value="1">Apoyo Cl&iacute;nico</option>
                                                <option value="2">Recursos F&iacute;sicos</option>
                                                <option value="3">Prestaci&oacute;n de Servicios Cl&iacute;nicos</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='text-align:right;'>Nombre Convenio:</td>
                                        <td colspan=3>
                                            <input type='text' id="nombre_convenio" name="nombre_convenio" value="" size=60 />
                                        </td>
                                    </tr>
                                    <tr id='tr_resAprueba'>
                                        <td style='text-align:right;'>Nro. Res. Aprueba Bases:</td>
                                        <td>
                                            <input type='text' id="res_aprueba" name="res_aprueba" value="" size=20 onkeyUp='agrega_fecha();' />
                                        </td>
                                        <td style='text-align:right;'>Fecha:</td>
                                        <td>
                                            <input type='text' id="fecha_aprueba" name="fecha_aprueba" value="" style='text-align:center;' onBlur='validacion_fecha2(this);' size=10 />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='text-align:right;'>Nro. Res. Adjudica:</td>
                                        <td>
                                            <input type='text' id="res_adjudica" name="res_adjudica" value="" size=20 />
                                        </td>
                                        <td style='text-align:right;'>Fecha:</td>
                                        <td>
                                            <input type='text' id="fecha_adjudica" name="fecha_adjudica" value="" style='text-align:center;' onBlur='validacion_fecha(this);' size=10 />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='text-align:right;'>Nro. Res. Aprueba:</td>
                                        <td>
                                            <span id='sp_sel_aprueba'></span><br>
                                            <select id="sel_aprueba" name="sel_aprueba" onChange="comprueba_campos(); if(this.value=='prorroga'){$('plazo').value=''; $('plazo2').value='';}">
                                                <option value="" SELECTED>(Seleccionar...)</option>
                                                <option value="contrato">Contrato</option>
                                                <option value="prorroga">Prorroga</option>
                                                <option value="aumento">Aumento</option>
                                            </select>
                                            <input type='text' id="res_prorroga" name="res_prorroga" value="" size=20 />
                                        </td>
                                        <td style='text-align:right;'>Fecha:</td>
                                        <td>
                                            <input type='text' id="fecha_prorroga" name="fecha_prorroga" value="" style='text-align:center;' onBlur='validacion_fecha2(this); agrega_fecha();' size=10 />
                                        </td>
                                    </tr>
                                    <tr id='tr_nrores_prorroga' style='display:none;'>
                                        <td style='text-align:right;'>N° Res. Aprueba Prorroga:</td>
                                        <td>
                                            <input type='text' id="resp_apr_prorroga" name="resp_apr_prorroga" value="" size=20 />
                                        </td>
                                        <td style='text-align:right;'>Fecha:</td>
                                        <td>
                                            <input type='text' id="fecha_nro_prorroga" name="fecha_nro_prorroga" value="" style='text-align:center;' onBlur='validacion_fecha2(this); agrega_fecha();' size=10 />
                                        </td>
                                    </tr>
                                    <tr id='tr_aumento_s' style='display:none;'>
                                        <td style='text-align:right;'>N° Res. Aprueba Aumento:</td>
                                        <td>
                                            <input type='text' id="t_aumento" name="t_aumento" value="" size=20 />
                                        </td>
                                        <td style='text-align:right;'>Fecha:</td>
                                        <td>
                                            <input type='text' id="b_aumento" name="b_aumento" value="" size=10 onblur="validacion_fecha2(this); agrega_fecha();" />
                                        </td>
                                    </tr>
                                    <tr id='td_res_aumento' name='td_res_aumento' style='display:none;'>
                                        <td style='text-align:right;'>Nro. Res. Aprueba Aumento Contrato:</td>
                                        <td>
                                            <input type='text' id="res_aumento" name="res_aumento" value="" size=20 />
                                        </td>
                                        <td style='text-align:right;'>Fecha:</td>
                                        <td>
                                            <input type='text' id="fecha_aumento" name="fecha_aumento" value="" style='text-align:center;' onBlur='validacion_fecha2(this);' size=10 />
                                        </td>
                                    </tr>
                                    <tr id='td_res_apruebac' name='td_res_apruebac' style='display:none;'>
                                        <td style='text-align:right;'>Nro. Res. Aprueba Contrato:</td>
                                        <td>
                                            <input type='text' id="res_contrato" name="res_contrato" value="" size=20 />
                                        </td>
                                        <td style='text-align:right;'>Fecha:</td>
                                        <td>
                                            <input type='text' id="fecha_contrato" name="fecha_contrato" value="" style='text-align:center;' onBlur='validacion_fecha(this);' size=10 />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan=5 style='text-align:center;'>
                                            <input type='hidden' name='h_estado' id='h_estado' value='0'>
                                            <input type='button' id='btn_hist' value='(Ver Hist&oacute;rico...)' onClick='mostrar_historico();'>
                                        </td>
                                    </tr>
                                    <tr id='tr_historico' style='display:none;'>
                                        <td colspan=5 style='text-align:center;'>
                                            <div id='historico' class="sub-content3" style="height:80px; overflow:auto;" ></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='text-align:right;'>Proveedor:</td>
                                        <td colspan=3>
                                            <input type="hidden" id="id_proveedor" name="id_proveedor" value="-1">
                                            <input type="text" id="rut_proveedor" name="rut_proveedor" size=10 style='text-align:right;font-size:11px;' DISABLED />
                                            <input type="text" id="nombre_proveedor" name="nombre_proveedor" size=50 style='font-size:11px;' onDblClick='liberar_proveedor();' />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='text-align:right;'>Administrador Contrato:</td>
                                        <td colspan=3>
                                            <input type="hidden" id="func_id" name="func_id" value="-1">
                                            <input type="text" id="rut_funcionario" name="rut_funcionario" size=10 style='text-align:right;font-size:11px;' DISABLED />
                                            <input type="text" id="nombre_funcionario" name="nombre_funcionario" size=50 style='font-size:11px;' onDblClick='liberar_funcionario();' />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='text-align:right;'>e-mail(s) Contacto:</td>
                                        <td colspan=3>
                                            <input type='text' id='mails' name='mails' value='' size=60 />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='text-align:right;'>Monto $:</td>
                                        <td>
                                            <input type='text' id='monto' name='monto' value='' size=10 />
                                        </td>
                                        <td style='text-align:right;'>Plazo de Entrega (D&iacute;as):</td>
                                        <td>
                                            <input type='text' id='plazo' name='plazo' value='' size=5 />
                                        </td>
                                    </tr>
                                    <tr id='tr_monto_aumento' style='display:none;'>
                                        <td style='text-align:right;'>Monto Aumento $:</td>
                                        <td>
                                            <input type='text' id="monto_aumento" name="monto_aumento" value="" style='text-align:center;' size=10 />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan=3 style='text-align:right;'>Duraci&oacute;n (Meses):</td>
                                        <td>
                                            <input type='text' id='plazo2' name='plazo2' value='' size=5 onBlur="calculaFecha();" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='text-align:right;'>Fecha Inicio:</td>
                                        <td>
                                            <input type='text' id='inicio' name='inicio' value='' onBlur='validacion_fecha(this);' style='text-align:center;' size=10 />
                                        </td>
                                        <td style='text-align:right;'>Fecha T&eacute;rmino:</td>
                                        <td>
                                            <input type='text' id='termino' name='termino' value='' onBlur='validacion_fecha(this);' style='text-align:center;' size=10 />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='text-align:right;'>Tipo de Garant&iacute;a</td>
                                        <td>
                                            <SELECT id='tipo_garantia' name='tipo_garantia' onChange="if(this.value=='4'){ $('td_fecha_boleta').hide(); $('fecha_boleta').hide(); $('fecha_boleta').value=''; }else{ $('td_fecha_boleta').show(); $('fecha_boleta').show(); }">
                                                <OPTION value='0'>(Seleccione...)</OPTION>
                                                <OPTION value='1'>Poliza de Seguro</OPTION>
                                                <OPTION value='2'>Certificado de Fianza</OPTION>
                                                <OPTION value='3'>Boleta Bancaria</OPTION>
                                                <OPTION value='4'>Vale Vista</OPTION>
                                                <OPTION value='5'>Deposito a la Vista</OPTION>
                                            </SELECT>
                                            <input type='text' id='nro_boleta' name='nro_boleta' value='' style='text-align:center;' size=20 />
                                        </td>
                                        <td style='text-align:right;' id='td_fecha_boleta'>Fecha Venc. Garant&iacute;a:</td>
                                        <td>
                                            <input type='text' id='fecha_boleta' name='fecha_boleta' value='' onBlur='validacion_fecha(this);' style='text-align:center;' size=10 />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='text-align:right;'>Banco Garant&iacute;a:</td>
                                        <td>
                                            <input type='text' id='banco_boleta' name='banco_boleta' value='' style='text-align:center;' size=20 />
                                        </td>
                                        <td style='text-align:right;'>Monto Garant&iacute;a $:</td>
                                        <td>
                                            <input type='text' id='monto_boleta' name='monto_boleta' value='' style='text-align:center;' size=15 />
                                        </td>
                                    </tr>
                                    <tr style='display:none;'>
                                        <td colspan=4 style='text-align:center;'>
                                            <?php if(_cax(48)){ ?><input type='button' value='(Agregar Multas...)' onClick="agregar_multa($('convenio_id').value,$('convenio_licitacion').value);"><?php } ?>
                                            <input type='hidden' id='c_multa' name='c_multa' value='0'>
                                            <input type='button' id='btn_ver_multas' value='(Ver Multas...)' onClick='muestra_multa(); listado_multas();'>
                                        </td>
                                    </tr>
                                    <tr style='display:none;'>
                                        <td colspan=4>
                                            <div class='sub-content2' id='listado_multas' name='listado_multas' style='width:100%; height:100px; overflow:auto;'></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='text-align:right;'>Multa (Descripci&oacute;n):</td>
                                        <td colspan=3>
                                            <textarea id='multa' name='multa' value='' cols=60 rows=3></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='text-align:right;'>Comentarios:</td>
                                        <td colspan=3>
                                            <textarea id='comenta' name='comenta' value='' cols=60 rows=3></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='text-align:right;'>Adjuntos:</td>
                                        <td colspan=3>
                                            <input id="archivos" type="file" name="archivos[]" multiple="multiple" />
                                            <BR/>
                                            <center>
                                                <div id='tr_adjuntos' name='tr_adjuntos' style='text-align:left;'>
                                                    <table>
                                                        <tr>
                                                            <td>
                                                                <div id='adjuntos' >
                                                                    <span id="cargando_img" name="cargando_img" style="display: none;">--Cargando--</span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </center>
                                        </td>
                                    </tr>
                                </table>
                            </form>
                            <input type='button' id='' name='' value='-- Guardar Convenio --' onClick='guardar_convenio();' />
                            <!-- <input type='button' id='bt_adjunto' name='bt_adjunto' value='-- Adjuntar Archivo... --' onClick='enviar_archivo();' />-->
                            <input type='button' id='' name='' value='-- Exportar a Excel --' onClick="ver_convenio_xls($('convenio_id').value);" />
                            <input type='button' id='' name='' value='-- Volver Atr&aacute;s --' onClick='listar_convenios();' />
                        </center>
                    </div>
                    <div id='listado_convenios'>
                        <table style='width:100%;' id='filtro_provee'>
                            <tr>
                                <td style='text-align:right;'>ID Licitaci&oacute;n:</td>
                                <td colspan=3>
                                    <INPUT TYPE='text' id='sel_id_licitacion' name='sel_id_licitacion' value='' />
                                    <input type='button' value='Buscar...'   onClick='listar_convenios();' />
                                    <input type="button" value='Limpiar...' onclick="liberar_articulo2();" />
                                </td>
                            </tr>
                            <tr>
                                <td style='text-align:right;'>Filtro Proveedor:</td>
                                <td colspan=3>
                                    <input type="hidden" id="id_proveedor2" name="id_proveedor2" value="0" onChange='listar_convenios();'>
                                    <input type="text" id="rut_proveedor2" name="rut_proveedor2" size=10 style='text-align:right;font-size:11px;' DISABLED />
                                    <input type="text" id="nombre_proveedor2" name="nombre_proveedor2" size=50 style='font-size:11px;' onDblClick='liberar_proveedor2();' />
                                </td>
                            </tr>
                            <tr>
                                <td style='text-align:right;'>Filtro Art&iacute;culo:</td>
                                <td colspan=3>
                                    <input type="hidden" id="art_id2" name="art_id2" value="0" onChange='listar_convenios();'>
                                    <input type="text" id="art_codigo2" name="art_codigo2" size=10 style='text-align:right;font-size:11px;' DISABLED />
                                    <input type="text" id="art_glosa2" name="art_glosa2" size=50 style='font-size:11px;' onDblClick='liberar_articulo2();' />
                                </td>
                            </tr>
                            <tr>
                                <td style='text-align:right;'>Estado:</td>
                                <td colspan=3>
                                    <select id='filtro_estado' name='filtro_estado' onChange='listar_convenios();'>
                                        <option value='1'>Vigentes</option>
                                        <option value='2'>Cerrados</option>
                                        <option value='0'>Todos</option>
                                    </select>
                                    <input type="button" value="Exportar a Excel..." onclick="listar_convenios_excel();"/>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:right;">Leyenda:</td>
                                <td colspan="4">
                                    <img src="iconos/color_000000.png">  Vigencia Mayor o igual a 180 d&iacute;as.
                                    <img src="iconos/color_999900.png">  Vigencia entre 90 y 180 d&iacute;as.
                                    <img src="iconos/color_FF0000.png">  Vigencia desde 90 a 5 d&iacute;as.
                                    <img src="iconos/color_FF0000_linea.png">  Vigencia menor igual 5 d&iacute;as y vencidos.
                                </td>
                            </tr>
                        </table>
                        <div class='sub-content2' id='lista_convenios' style='overflow: auto; height: 300px;'>
                            
                        </div>
                            
                    </div>
                        
                </div>
            </td>
        </tr>
    </table>
</center>
<script>  listar_convenios(); </script>