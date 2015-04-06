<?php

  require_once("../conectar_db.php");

  $art_talonarios = pg_query($conn, 

  "SELECT art_id, tipotalonario_funcionario FROM receta_tipo_talonario

  WHERE art_id IS NOT NULL");

	

	$talcadena='';

	$talcadena2='';



	for($i=0;$i<pg_num_rows($art_talonarios);$i++) {



    $tal = pg_fetch_row($art_talonarios);



    $talcadena .= $tal[0];

    if($i<($art_talonarios-1)) $talcadena.=',';



    $talcadena2 .= $tal[1];

    if($i<($art_talonarios-1)) $talcadena2.=',';



  }



?>



// JavaScript

// Funciones de Uso Común del Sistema



	var __id_art_talonarios = new Array();

	var __func_art_talonarios = new Array();





  _global_iva=<?php echo $_global_iva; ?>;

	__usuario='<?php echo $_SESSION['sgh_username']; ?>';

	__id_art_talonarios=[<?php echo $talcadena; ?>];

	__func_art_talonarios=[<?php echo $talcadena2; ?>];





	function if_null(obj, ret) {



    alert(typeof(obj));



    alert(obj)



    if(IsNull(obj)) return ret;



    if(typeof(obj)=='string')

      if(trim(obj)=='') return ret;





    return obj;



  }



  function number_formats(nStr,prefix){

    var prefix = prefix || '';

    nStr += '';

    x = nStr.split('.');

    x1 = x[0];

    x2 = x.length > 1 ? ',' + x[1] : '';

    var rgx = /(\d+)(\d{3})/;

    while (rgx.test(x1))

        x1 = x1.replace(rgx, '$1' + '.' + '$2');

    return prefix + x1 + x2;

  }



  function toggle_visible(elemento) {



    obj = $(elemento);



    if(obj.style.display=='') {

      obj.style.display='none';

    } else {

      obj.style.display='';

    }



  };



  function toggle_text(elemento,texto1,texto2) {



    obj = $(elemento);



    if(obj.innerHTML==texto1) {

      obj.innerHTML=texto2;

    } else {

      obj.innerHTML=texto1;

    }



  }



  function findPos(obj) {

	var curleft = curtop = 0;

	if (obj.offsetParent) {

		curleft = obj.offsetLeft

		curtop = obj.offsetTop

		while (obj = obj.offsetParent) {

			curleft += obj.offsetLeft

			curtop += obj.offsetTop

		}

	}

		return [curleft,curtop];

	}



	function cambiar_pagina(mostrar, funcion) {



		Windows.closeAll();



    $('contenido').style.display='none';

		$('contenido').style.overflow='hidden';

		$('carga').style.display='';



    var myAjax = new Ajax.Updater(

			'contenido',

			mostrar,

			{

				method: 'get',

				evalScripts: true,

				onSuccess: function(pedido_datos) {



            $('contenido').style.overflow='auto';

		        $("contenido").style.height=window.innerHeight-70;

		        $('carga').style.display='none';

				    $('contenido').style.display='';



		        // Ejecuta la función que se haya asignado al terminar.

		        if(typeof(funcion)=='function') {

              funcion();

            }



    		    // Workaround Bug Mozilla Cursor Desaparece



				    // setTimeout(firefox_fix($('contenido')),500);



		    }

			}



			);



	}



	function firefox_fix(_div) {



            try {



            _inputs = _div.getElementsByTagName('input');



				    for( i = 0 ; i < _inputs.length ; i ++ ) {



              _input = inputs[i];

              _parent = _input.parent;

              _parent.style.overflow='visible';



              setTimeout(



              function() {

                _parent.style.overflow='auto';

              }



              , 10);



            }



            $('contenido').style.overflow='visible';



            setTimeout(



            function() {

              $('contenido').style.overflow='auto';

            }



            , 20);



            }



            catch(err) {  }



  }





	function tab_up(tab_name) {



    if($(tab_name+'_content').style.display!='') {



    $(tab_name).className='tabs';

    $(tab_name).style.cursor='default';

    $(tab_name+'_content').style.display='';



    }





  }



  function tab_down(tab_name) {



    if($(tab_name+'_content').style.display=='') {



    $(tab_name).className='tabs_fade';

    $(tab_name).style.cursor='pointer';

    $(tab_name+'_content').style.display='none';



    }



  }

  

  getRadioVal=function(form, group) {

    

      var objs=$(form).getElementsByTagName('input');

      

      for(var i=0;i<objs.length;i++)

        if(objs[i].name==group && objs[i].checked) {

          return objs[i].value;

        }

        

      return false;

    

  }





  function buscar_codigos_barra(bod_id, callback_func, tipo_buscador, buscador_solicitado, bodega_sel) {



   var win = new Window("busca_cod_bar", {className: "alphacube",

                          top:40, left:0,

                          width: 500, height: 200,

                          title: 'Seleccionar Art&iacute;culos',

                          minWidth: 500, minHeight: 200,

                          maximizable: false, minimizable: false,

                          wiredDrag: true, resizable: false });



    win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})



    win.setAjaxContent('abastecimiento/buscador_codigos.php',

			{

				method: 'get',

				parameters: 'buscador='+buscador_solicitado+'&tipo_buscador='+tipo_buscador+'&bodega_sel='+bodega_sel,

				evalScripts: true



			});



    $("busca_cod_bar").objeto = win;

		$("busca_cod_bar")._bod_id = bod_id;

		$("busca_cod_bar")._callback_fn = callback_func;



    win.setDestroyOnClose();

    win.showCenter(false, true);

    win.show();



    return win;





  }



  function buscar_diagnosticos(objetivo, callback_func) {



    var win = new Window("busca_diag", {className: "alphacube", top:40, left:0,

                          width: 300, height: 300,

                          title: 'Buscar C&oacute;digo de Diagn&oacute;stico',

                          minWidth: 300, minHeight: 300,

                          maximizable: false, minimizable: false,

                          wiredDrag: true, resizable: false });



    win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})



    win.setAjaxContent('buscadores.php',

			{

				method: 'get',

				parameters: 'tipo=diagnosticos',

				evalScripts: true



			});



		$("busca_diag").objetivo_cod = objetivo;

		$("busca_diag").objeto = win;

		$("busca_diag").onCloseFunc = callback_func;



    win.setDestroyOnClose();

    win.showCenter();

    win.show();



    return win;



  }

  

  function buscar_funcionarios(objetivo, callback_func, ruta) {



    top=Math.round(screen.height/2)-150;

    left=Math.round(screen.width/2)-200;



    new_win =

    window.open(ruta+'buscadores.php?tipo=funcionarios', 'win_funcionarios',

      'toolbar=no, location=no, directories=no, status=no, '+

      'menubar=no, scrollbars=yes, resizable=no, width=400, height=300, '+

      'top='+top+', left='+left);



    new_win.objetivo_cod = objetivo;

    new_win.onCloseFunc = callback_func;



    new_win.focus();



  }



  function buscar_medicos(objetivo, callback_func, ruta) {



    top=Math.round(screen.height/2)-150;

    left=Math.round(screen.width/2)-200;



    new_win =

    window.open(ruta+'buscadores.php?tipo=medicos', 'win_funcionarios',

      'toolbar=no, location=no, directories=no, status=no, '+

      'menubar=no, scrollbars=yes, resizable=no, width=400, height=300, '+

      'top='+top+', left='+left);



    new_win.objetivo_cod = objetivo;

    new_win.onCloseFunc = callback_func;



    new_win.focus();



  }





  function buscar_articulos(objetivo, callback_func) {



    var win = new Window("busca_arts", {className: "alphacube", top:40, left:0,

                          width: 400, height: 250,

                          title: 'Buscar Art&iacute;culos',

                          minWidth: 400, minHeight: 250,

                          maximizable: false, minimizable: false,

                          wiredDrag: true, resizable: false });



    win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})



    win.setAjaxContent('buscadores.php',

			{

				method: 'get',

				parameters: 'tipo=articulos',

				evalScripts: true



			});



		$("busca_arts").objetivo_cod = objetivo;

		$("busca_arts").objeto = win;

		$("busca_arts").onCloseFunc = callback_func;



    win.setDestroyOnClose();

    win.showCenter();

    win.show();



    return win;



  }



  function buscar_medicamentos(objetivo, callback_func) {



    var win = new Window("busca_meds", {className: "alphacube", top:40, left:0,

                          width: 400, height: 250,

                          title: 'Buscar Medicamentos Com&uacute;nes',

                          minWidth: 400, minHeight: 250,

                          maximizable: false, minimizable: false,

                          wiredDrag: true, resizable: false });



    win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})



    win.setAjaxContent('buscadores.php',

			{

				method: 'get',

				parameters: 'tipo=medicamentos',

				evalScripts: true



			});



		$("busca_meds").objetivo_cod = objetivo;

		$("busca_meds").objeto = win;

		$("busca_meds").onCloseFunc = callback_func;



    win.setDestroyOnClose();

    win.showCenter();

    win.show();



    return win;



  }



  function buscar_medicamentos_controlados(objetivo, callback_func) {



    var win = new Window("busca_meds", {className: "alphacube", top:40, left:0,

                          width: 400, height: 250,

                          title: 'Buscar Medicamentos Controlados',

                          minWidth: 400, minHeight: 250,

                          maximizable: false, minimizable: false,

                          wiredDrag: true, resizable: false });



    win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})



    win.setAjaxContent('buscadores.php',

			{

				method: 'get',

				parameters: 'tipo=medicamentos_controlados',

				evalScripts: true



			});



		$("busca_meds").objetivo_cod = objetivo;

		$("busca_meds").objeto = win;

		$("busca_meds").onCloseFunc = callback_func;



    win.setDestroyOnClose();

    win.showCenter();

    win.show();



    return win;



  }



  function editar_receta(id) {



    var win = new Window("editar_recetas",

                          {className: "alphacube",

                          top:40, left:0,

                          width: 650, height: 400,

                          title: '<img src="iconos/pill.png"> Modificaci&oacute;n de Recetas',

                          minWidth: 650, minHeight: 400,

                          maximizable: false, minimizable: false,

                          wiredDrag: true, resizable: false });



    win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})



    win.setAjaxContent('recetas/editar_recetas/form.php',

			{

				method: 'get',

				parameters: 'receta_id='+id,

				evalScripts: true



			});



		$("editar_recetas").objeto = win;



    win.setDestroyOnClose();

    win.showCenter();

    win.show();



    return win;





  }



  function visualizar_receta(id) {



    var win = new Window("editar_recetas",

                          {className: "alphacube",

                          top:40, left:0,

                          width: 650, height: 400,

                          title: '<img src="iconos/pill.png"> Detalles de Recetas',

                          minWidth: 650, minHeight: 400,

                          maximizable: false, minimizable: false,

                          wiredDrag: true, resizable: false });



    win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})



    win.setAjaxContent('recetas/editar_recetas/form.php',

			{

				method: 'get',

				parameters: 'visualizar&receta_id='+id,

				evalScripts: true



			});



		$("editar_recetas").objeto = win;



    win.setDestroyOnClose();

    win.showCenter();

    win.show();



    return win;





  }





  function eliminar_receta(id) {



    var win = new Window("editar_recetas",

                          {className: "alphacube",

                          top:40, left:0,

                          width: 650, height: 400,

                          title: '<img src="iconos/pill.png"> Eliminar Receta',

                          minWidth: 650, minHeight: 400,

                          maximizable: false, minimizable: false,

                          wiredDrag: true, resizable: false });



    win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})



    win.setAjaxContent('recetas/editar_recetas/form.php',

			{

				method: 'get',

				parameters: 'eliminar&receta_id='+id,

				evalScripts: true



			});



		$("editar_recetas").objeto = win;



    win.setDestroyOnClose();

    win.showCenter();

    win.show();



    return win;





  }













  function abrir_popup(titulo, ancho, alto, max, url) {



    var win = new Window("popper", {className: "alphacube", top:40, left:0,

                          width: ancho, height: alto, title: titulo,

                          minWidth: ancho, minHeight: alto,

                          maximizable: max, minimizable: false,

                          url: url, wiredDrag: true });



    win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})

    win.setDestroyOnClose();

    win.showCenter();

    win.show();



    return win;



  }
  
  
  function validacion_hora(obj) {
	 
		var val=trim(obj.value);
		var hr=val;
	
		if(val=='') {
			
			obj.style.background='red';
			return false;
				
		}	
		
			if(!val.match(/^[0-9]{2}:*[0-9]{2}:*[0-9]{0,2}$/)) {
					obj.style.background='red';
					return false;														
			}		
		
			if(val.search(/\:/)==-1) {

				if(val.length==4) {
					hr=val.charAt(0)+val.charAt(1)+':'+val.charAt(2)+val.charAt(3);
				} else if(val.length==6) { 
					hr=val.charAt(0)+val.charAt(1)+':'+val.charAt(2)+val.charAt(3)+':'+val.charAt(4)+val.charAt(5);
				} else {
					obj.style.background='red';
					return false;										
				}	

			} 
			
		var chk = hr.split(':');
		
		if( (chk.length==2 || chk.length==3) 
				&& chk[0]*1>=0 && chk[0]*1<24 ) {
					
			for(var i=0;i<chk.length;i++) {
				if(chk[i].length!=2 || chk[i]*1<0 || (i>0 && chk[i]*1>=60)) {
					obj.style.background='red';
					return false;	
				}							
			}
					
			obj.style.background='yellowgreen';
			obj.value=hr;
			
			return true;
				
		} else {
			
			obj.style.background='red';
			return false;
							
		}							 
	 	
	 }	 	

  
  
	function validacion_fecha(obj) {
		
	obj.value=trim(obj.value.replace(/-/gi,'/'));
	
	var fecha_ok=obj.value;
	
	switch(fecha_ok.length) {
		case 6:

			decada=(fecha_ok.charAt(4)+''+fecha_ok.charAt(5))*1;

			if(decada>50) 
				siglo='19';
			else
				siglo='20';
				
			fecha_ok=fecha_ok.charAt(0)+
						fecha_ok.charAt(1)+'/'+
						fecha_ok.charAt(2)+
						fecha_ok.charAt(3)+'/'+siglo+
						fecha_ok.charAt(4)+
						fecha_ok.charAt(5);
			break;						
		case 8:
			fecha_ok=fecha_ok.charAt(0)+
						fecha_ok.charAt(1)+'/'+
						fecha_ok.charAt(2)+
						fecha_ok.charAt(3)+'/'+
						fecha_ok.charAt(4)+
						fecha_ok.charAt(5)+
						fecha_ok.charAt(6)+
						fecha_ok.charAt(7);
			break;
	}
	
	if( !isDate(fecha_ok) ) {
		obj.style.background='red';
		return false;
	} else {
		obj.style.background='yellowgreen';
		obj.value=fecha_ok;
		return true;	
	}
		
		
	}


	function isDate(dateStr) {



    var datePat = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;

    var matchArray = dateStr.match(datePat); // is the format ok?



    if (matchArray == null) {

        return false;

    }



    month = matchArray[3]; // parse date into variables

    day = matchArray[1];

    year = matchArray[5];



    if (month < 1 || month > 12) { // check month range

        return false;

    }



    if (day < 1 || day > 31) {

        return false;

    }



    if ((month==4 || month==6 || month==9 || month==11) && day==31) {

        return false;

    }



    if (month == 2) { // check for february 29th

        var isleap = (year % 4 == 0 && (year % 100 != 0 || year % 400 == 0));

        if (day > 29 || (day==29 && !isleap)) {

            return false;

        }

    }

	

    return true; // date is valid

}



	function serializar(objeto) {

		

		serializado=$(objeto).serialize();

		serializado=serializado.replace($(objeto).id+'=','');

			

		return serializado;

		

	}

	

	function selval(__objeto, __valor) {

		

		var __selector;

		

		__selector = $(__objeto);

		

		__selector.selectedIndex=0;

		

		for(i=0;i<__selector.options.length;i++) {

			if(__selector.options[i].value==__valor) __selector.selectedIndex=i;

		}

		

	}

	

	function formatoDinero(num) {

		num = num.toString().replace(/\$|\,/g,'');

		if(isNaN(num))

		num = "0";

		sign = (num == (num = Math.abs(num)));

		num = Math.floor(num*100+0.50000000001);

		cents = num%100;

		num = Math.floor(num/100).toString();

		if(cents<10)

		cents = "0" + cents;

		for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)

		num = num.substring(0,num.length-(4*i+3))+'.'+

		num.substring(num.length-(4*i+3));

		return (((sign)?'':'-') + '$' + num + '.-');

	}

	

	function serializar_objetos(fuente) {

	

		var registros = new Object;

			

		// Toma los inputs dentro de la fuente...

	

		campos=$(fuente).getElementsByTagName('input');

			

			for(a=0;a<campos.length;a++) {

				switch(campos[a].type.toLowerCase()) {

				case 'checkbox':

					registros[campos[a].name]=campos[a].checked;

					break;

				default:

					registros[campos[a].name]=campos[a].value;

					break;

				}

			}

			

			offset=campos.length;

	

		// Toma los selects dentro de la fuente...

			

		campos=$(fuente).getElementsByTagName('select');

			

			for(a=0;a<campos.length;a++) {

				registros[campos[a].name]=campos[a].value;

			}

			

		return $H(registros).toQueryString();

	

	}

	

	function trim(str)

	{

  		 return str.replace(/^\s*|\s*$/g,"");

	}

	

	String.prototype.repeat = function(multiplier) {

    var newString = '';



    for (var i = 0; i < multiplier; i++) {

        newString += this;

    }



    return newString;

	} 

	

	function dateDiff(strDate1,strDate2){

     

	 datDate1= Date.parse(strDate1);

     datDate2= Date.parse(strDate2);

	 

     return ((datDate2-datDate1)/(24*60*60*1000))

     

	}

	



function comprobar_rut(texto)

{	



  // Salvoconducto para recetas ilegibles...

  

  if(texto=='7999') return true;



  texto = texto.toUpperCase();

  

  var partes = texto.split('-');

  

  try {

    if(partes.length!=2 && partes[1].length!=1) {

      return false;

    }

  } catch(err) {

    return false;

  }

  

  numeracion = partes[0];

  

  serie=2;

  sumatoria=0;

  

  for(i=numeracion.length-1;i>=0;i--) {

  

      if(

      numeracion.charAt(i)!='0' &&

      numeracion.charAt(i)!='1' &&

      numeracion.charAt(i)!='2' &&

      numeracion.charAt(i)!='3' &&

      numeracion.charAt(i)!='4' &&

      numeracion.charAt(i)!='5' &&

      numeracion.charAt(i)!='6' &&

      numeracion.charAt(i)!='7' &&

      numeracion.charAt(i)!='8' &&

      numeracion.charAt(i)!='9'

      ) {

          return false;

      }

      

      

      sumatoria+=((numeracion.charAt(i)*1)*serie);

  

      if(serie==7) { serie=2; } else { serie++; }

  

  }



  modulo = 11-(sumatoria % 11);

  

  if( modulo==(partes[1]*1) || (modulo==10 && partes[1]=='K') || (modulo==11 && (partes[1]*1)==0) )  {

    return true;

  } else {

    return false;

  }

  

}



calc_edad = function(fechax) {

		

			var hoy = new Date();

			var fecha = new Date();

			

			datos = fechax.split('/');

			

			fecha.setDate((datos[0]*1));

			fecha.setMonth((datos[1]*1)-1);

			fecha.setYear((datos[2]*1));

			

			diferencia = dateDiff(fecha, hoy);

			

			anyo = Math.floor(diferencia/365);

			

			meses = Math.floor((diferencia%365)/30);

			

			if(meses>0) { 

				if (meses>1) {

					meses=', '+meses+' meses';

				} else {

					meses=', '+meses+' mes';

				} 

			} else { 

				meses=''; 

			}

			

			dias = Math.floor((diferencia%365)%30);

			

			if(dias>0) {

				if(dias>1) {

					dias=' y '+dias+' d&iacute;as.';

				} else {

					dias=' y '+dias+' d&iacute;a.';

				}

			} else {

				dias='.';

			}



	if(anyo>1) {

		return anyo+' a&ntilde;os'+meses+dias;

	} else {

		return anyo+' a&ntilde;o'+meses+dias;

	}

}



function imprimirHTML(_codigo_html) {



  var __ventana = window.open('', '_blank');

  

  __ventana.document.write('<html>');

  __ventana.document.write('<LINK href="css/printer-friendly.css" type="text/css" rel="stylesheet">');

  __ventana.document.write('<body>');

  __ventana.document.write(_codigo_html);

  __ventana.document.write('</body></html>');

  

  __ventana.document.close();

  

  __divs = __ventana.document.getElementsByTagName('div');

  

  for(i=0;i<__divs.length;i++) {

    __divs[i].overflow='';

  }

  

  __ventana.print();

  

  __ventana.close();

  

}



bloquear_ventana = function() {

    

    var win = new Window("blocker_popper", 

                          {className: "alphacube", top:40, left:0, 

                          width: 300, height: 220, 

                          title: 'Demasiado Tiempo de Inactividad',

                          minWidth: 300, minHeight: 220,

                          maximizable: false, minimizable: false,

                          wiredDrag: true, draggable: false,

                          closable: false, resizable: false });

                          

    win.setDestroyOnClose();

    

    win.setAjaxContent('light_login.php', 

			{

			

				method: 'get',

        parameters: 'usuario='+__usuario, 

				evalScripts: true

	

			});

    

    win.showCenter();

    win.show(true);

    

    __light_logger=win;

    

}



visualizador_documentos = function(titulo, parametros) {



    var win = new Window("viewer_popper", 

                          {className: "alphacube", top:40, left:0, 

                          width: 670, height: 450, 

                          title: titulo,

                          minWidth: 500, minHeight: 400,

                          maximizable: false, minimizable: false,

                          wiredDrag: true, draggable: true,

                          closable: true, resizable: false });

                          

    win.setDestroyOnClose();

    

    win.setAjaxContent('visualizar.php', 

			{

			

				method: 'get',

        parameters: parametros, 

				evalScripts: true

	

			});

    

    win.showCenter();

    win.show(true);



}



comprobar_talonario = function(___art_id) {

    

    ___encontrado=false;

      

    for(___n=0;___n<__id_art_talonarios.length;___n++) {

      if((___art_id*1)==__id_art_talonarios[___n]) ___encontrado=true;

    }

      

    return ___encontrado;

    

}



funcionario_talonario = function(___art_id) {

    

    ___encontrado=false;

      

    for(___n=0;___n<__id_art_talonarios.length;___n++) {

      if((___art_id*1)==__id_art_talonarios[___n]) 

          ___encontrado=__func_art_talonarios[___n];

    }

      

    return ___encontrado;

    

}



number_format = function(num, decs, dsep, tsep) {



  var decs = (!decs ? 0 : decs);

  var dsep = (!dsep ? ',' : dsep);

  var tsep = (!tsep ? '.' : tsep);



  var number = ''+((num*1).toFixed(decs));

  

  var parts = number.split('.');

  

  if(parts[0].length>3) {

    var c = Math.ceil(parts[0].length/3)-1;

    var l = parts[0].length;

    var intpart='';



    for(u=0;u<=c;u++) {

      if(u!=c) ts=tsep; else ts='';

      intpart=ts+''+parts[0].charAt((l-3)-(u*3))+

                    parts[0].charAt((l-2)-(u*3))+

                    parts[0].charAt((l-1)-(u*3))+intpart;

    } 



  } else {



    var intpart=parts[0];



  }

  

  if(parts[1]==null) {

    

    zerofill='';

    for(var i=0;i<decs;i++) zerofill+='0';

    

    parts[1]=zerofill;

  

  } 

  

  if(decs>0)

    return intpart+dsep+parts[1];

  else

    return intpart;

  



}		



number_format_input = function(num, decs) {



  dsep='.';

  tsep='';



  var number = ''+(num);

  

  var parts = number.split('.');

  

  if(parts[0].length>3) {

    var c = Math.ceil(parts[0].length/3)-1;

    var l = parts[0].length;

    var intpart='';



    for(u=0;u<=c;u++) {

      if(u!=c) ts=tsep; else ts='';

      intpart=ts+''+parts[0].charAt((l-3)-(u*3))+

                    parts[0].charAt((l-2)-(u*3))+

                    parts[0].charAt((l-1)-(u*3))+intpart;

    } 



  } else {



    var intpart=parts[0];



  }

  

  if(parts[1]==null) {

    

    zerofill='';

    for(var i=0;i<decs;i++) zerofill+='0';

    

    parts[1]=zerofill;

  

  } else {



    var l2 = parts[1].length;

    var dif = l2-decs;

    

    var div = Math.pow(10,dif);

    

    parts[1]=Math.round(parts[1]/div);

  

  }

  

  if(decs>0)

    return intpart+dsep+parts[1];

  else

    return intpart;

  



}		



function seleccionar_institucion(cid, cnombre) {



  l=(screen.availWidth/2)-250;

  t=(screen.availHeight/2)-200;

  

  win = window.open('administracion/seleccionar_institucion.php?cid='+encodeURIComponent(cid)+

                    '&cnombre='+encodeURIComponent(cnombre), 'sel_institucion',

                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+

                    'resizable=no, width=500, height=415');

                    

  win.focus();

  



}



function abrir_orden(orden_id) {



  l=(screen.availWidth/2)-250;

  t=(screen.availHeight/2)-200;

  

  win = window.open('visualizar.php?orden_id='+orden_id, 'ver_orden',

                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+

                    'resizable=no, width=700, height=465');

                    

  win.focus();



}



function abrir_pedido(pedido_numero) {



  l=(screen.availWidth/2)-250;

  t=(screen.availHeight/2)-200;

  

  win = window.open('visualizar.php?pedido_nro='+pedido_numero, 'ver_pedido',

                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+

                    'resizable=no, width=500, height=445');

                    

  win.focus();



}



function abrir_solficha(solf_id) {



  l=(screen.availWidth/2)-250;

  t=(screen.availHeight/2)-200;

  

  win = window.open('visualizar.php?solf_id='+solf_id, 'ver_solicitud',

                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+

                    'resizable=no, width=500, height=445');

                    

  win.focus();



}



function abrir_ficha(pac_id) {



  l=(screen.availWidth/2)-325;

  t=(screen.availHeight/2)-200;

  

  win = window.open('visualizar.php?pac_id='+pac_id, 'ver_solicitud',

                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+

                    'resizable=no, width=650, height=445');

                    

  win.focus();



}



