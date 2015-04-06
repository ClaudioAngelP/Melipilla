<?php

  require_once('../../conectar_db.php');

?>

<script>

	meses_arr = ['Enero','Febrero','Marzo','Abril','Mayo',
		'Junio','Julio','Agosto','Septiembre','Octubre',
		'Noviembre','Diciembre'];
    
    gasto = new Object();
    gasto.detalle=new Array();
    gasto.totaldet=0;


    listar_pagos = function()
    {
		
		var url='';
		
		if($('reporte').value=='0') {
			url='abastecimiento/pago_cuentas/listado_pagos.php?tipo=1';
		} else if($('reporte').value=='1') {
			url='abastecimiento/pago_cuentas/listado_pagos.php?tipo=2';
		} else if($('reporte').value=='2') {
			url='abastecimiento/pago_cuentas/listado_pagos.php?tipo=3';	
		} else if($('reporte').value=='3') {
			url='abastecimiento/pago_cuentas/autoriza_pagos.php?tipo=1';	
		} else if($('reporte').value=='4') {
			url='abastecimiento/pago_cuentas/autoriza_pagos.php?tipo=2';	
		}

        $('listado').innerHTML='<center><table><tr><td style="width:530px;height:160px;"><center><img src="imagenes/ajax-loader2.gif"></center></td></tr></table></center>';
        var myAjax = new Ajax.Updater(
        'listado',
        url,
        {
            method: 'post',
            parameters: 'prov_id='+($('id_proveedor2').value*1),
            evalScripts: true
        }
        );

  }
  
  ver_guardar=function(doc_id) {
	  $('guardar_'+doc_id).show();
  }
  
  guardar_pago=function(doc_id) {
	  
	  var params='doc_id='+doc_id+'&fecpago='+encodeURIComponent($('fecpago_'+doc_id).value)+'&fpago='+$('fpago_'+doc_id).value+'&numpago='+$('numpago_'+doc_id).value;
	  
	  $('listado').innerHTML='<center><table><tr><td style="width:530px;height:160px;"><center><img src="imagenes/ajax-loader2.gif"></center></td></tr></table></center>';
      
      var myAjax = new Ajax.Request(
		'abastecimiento/pago_cuentas/sql_pagar_factura.php',
		{
			method:'post',
			parameters:params,
			onComplete:function() {
				listar_pagos();
			}
		}
	  );
	  
	  
  }


  guardar_aut=function(doc_id) {
	  
	  var params='doc_id='+doc_id+'&aut='+$('fpago_'+doc_id).value;
	  
	  $('listado').innerHTML='<center><table><tr><td style="width:530px;height:160px;"><center><img src="imagenes/ajax-loader2.gif"></center></td></tr></table></center>';
      
      var myAjax = new Ajax.Request(
		'abastecimiento/pago_cuentas/sql_pagar_factura.php',
		{
			method:'post',
			parameters:params,
			onComplete:function() {
				listar_pagos();
			}
		}
	  );
	  
	  
  }


  asociar_factura=function(doc_id) {
	  
	  var d;
	  
	  for(var i=0;i<docs.length;i++) {
		  if(docs[i].doc_id==doc_id)
			d=docs[i];
			
	  }
	  
	  if(d.doc_tipo==0) tipo_doc='GUIA DE DESPACHO';
	  if(d.doc_tipo==2) tipo_doc='BOLETA';
	  if(d.doc_tipo==3) tipo_doc='OTROS';
	  
	  var numero=prompt(("Ingrese Número de Factura para:\n\n"+tipo_doc+" "+d.doc_num+" Recepcionada el: "+d.doc_fecha_recepcion+"\n\nPROVEEDOR:\nRUT: "+d.prov_rut+"\nNOMBRE: "+d.prov_glosa+"\n\n").unescapeHTML());
	  
	  if(numero==null) return;
	  
	  var myAjax = new Ajax.Request(
		'abastecimiento/pago_cuentas/sql_asociar_factura.php',
		{
			method:'post',
			parameters:'doc_id='+doc_id+'&numero='+numero,
			onComplete:function() {
				listar_pagos();
			}
		}
	  );
	  
  }


  asociar_oc=function(doc_id) {
	  
	  var d;
	  
	  for(var i=0;i<docs.length;i++) {
		  if(docs[i].doc_id==doc_id)
			d=docs[i];
			
	  }
	  
	  if(d.doc_tipo==0) tipo_doc='GUIA DE DESPACHO';
	  if(d.doc_tipo==1) tipo_doc='FACTURA';
	  if(d.doc_tipo==2) tipo_doc='BOLETA';
	  if(d.doc_tipo==3) tipo_doc='OTROS';
	  
	  var numero=prompt(("Ingrese Orden de Compra para:\n\n"+tipo_doc+" "+d.doc_num+" Recepcionada el: "+d.doc_fecha_recepcion+"\n\nPROVEEDOR:\nRUT: "+d.prov_rut+"\nNOMBRE: "+d.prov_glosa+"\n\n").unescapeHTML());
	  
	  if(numero==null) return;
	  
	  var myAjax = new Ajax.Request(
		'abastecimiento/pago_cuentas/sql_asociar_factura.php',
		{
			method:'post',
			parameters:'doc_id='+doc_id+'&orden_compra='+numero,
			onComplete:function() {
				listar_pagos();
			}
		}
	  );
	  
  }

graficar_item=function(datos) {

	var t=datos[0]*1;
	var v=Math.floor(datos[1]*100/t);
	var a=Math.floor(datos[2]*100/t);
	var r=100-(v+a);
	

	var html='<table style="width:200px;border:1px solid black;">';
	html+='<tr>';
	html+='<td style="width:'+v+'%;"><td>';
	html+='<td style="width:'+a+'%;"><td>';
	html+='<td style="width:'+r+'%;"><td>';
	html+='</table>';
	
}

	
detalle = function(item) {
	
	item_win=window.open('administracion/items_presupuestarios/detalle.php?item_codigo='+encodeURIComponent(item)+'&'+$('mesanio').serialize(),'certificados',
							'width=750,height=400,scrollbars=1');
	
	item_win.focus();
	
}


certificado = function(cert_id) {
	
	item_win=window.open('administracion/items_presupuestarios/generar_certificados.php?cert_id='+cert_id,'certificados',
							'width=750,height=400,scrollbars=1');
	
	item_win.focus();
	
}

imprimir_certificado = function(cert_id) {
	
	item_win=window.open('administracion/items_presupuestarios/cert_pdf.php?cert_id='+cert_id,'certificados',
							'width=750,height=400,scrollbars=1');
	
	item_win.focus();
	
}

importar = function() {

	item_win=window.open('','certificados',
							'width=750,height=400,scrollbars=1');
							
	$('planilla_csv').target='certificados';
	
	$('planilla_csv').submit();
	
}


abrir_recep = function (doc_id) {



  l=(screen.availWidth/2)-250;

  t=(screen.availHeight/2)-200;

  

  win = window.open('visualizar.php?doc_id='+doc_id, 'ver_orden',

                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+

                    'resizable=no, width=700, height=465');

                    

  win.focus();



}





</script>

<center>

 <div class='sub-content' style='width:1200px;'>
  <form id='gasto_frm' name='gasto_frm' style='overflow:auto;' autocomplete="off">
         <div class='sub-content'>
             <img src='iconos/money.png'>
             <b>Cuentas por Pagar</b>
             
             <select id='reporte' name='reporte' onChange='listar_pagos();'>
             <option value='3'>Autorizaci&oacute;n de Pagos</option>
             <option value='4'>Documentos Rechazados</option>
             <option value='0'>Pendientes de Pago</option>
             <option value='1'>Pagadas</option>
             <option value='2'>Todos los Documentos</option>
             </select>
                          
         </div>
         
         <div class='sub-content'>
         
		 <table style='width:100%;'>
		 
			 <tr><td style='text-align:right;'>
				Filtro Proveedor:
				</td><td colspan=3>

				<input type="hidden" id="id_proveedor2" name="id_proveedor2" value="0" onChange='listar_convenios();'>
				<input type="text" id="rut_proveedor2" name="rut_proveedor2" size=10
				style='text-align:right;font-size:11px;' DISABLED />
				<input type="text" id="nombre_proveedor2" name="nombre_proveedor2" size=50
				style='font-size:11px;' onDblClick='liberar_proveedor2();' />

				</td></tr>

		 </table>
         </div>
         
         <div class='sub-content2' style='height:450px;overflow:auto;' id='listado'>

         </div>
  </form>
 </div>

</center>

<script>

  mostrar_proveedor2=function(datos) {
    $('id_proveedor2').value=datos[3];
    $('rut_proveedor2').value=datos[1];
    $('nombre_proveedor2').value=datos[2].unescapeHTML();
    listar_pagos();
  }
  
  liberar_proveedor2=function() {
    $('id_proveedor2').value=0;
    $('nombre_proveedor2').value='';
    $('rut_proveedor2').value='';
    $('nombre_proveedor2').focus();
    listar_pagos();
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




    listar_pagos();

</script>
