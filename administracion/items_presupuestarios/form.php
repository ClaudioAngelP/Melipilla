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


    listar_gastos = function()
    {
		
		var url='';
		
		if($('reporte').value=='0') {
			url='administracion/items_presupuestarios/listado_gastos.php';
		} else if($('reporte').value=='1') {
			url='administracion/items_presupuestarios/listado_certs.php';
		} else {
			url='administracion/items_presupuestarios/listado_costos.php';	
		}

        $('listado').innerHTML='<center><table><tr><td style="width:530px;height:160px;"><center><img src="imagenes/ajax-loader2.gif"></center></td></tr></table></center>';
        var myAjax = new Ajax.Updater(
        'listado',
        url,
        {
            method: 'post',
            parameters: $('mesanio').serialize()
        }
        );

  }
  
  modificar_ppto=function(centro_ruta,centro_nombre,item_codigo) {
	  
	  var monto=prompt("Ingrese Presupuesto para Centro de Costo\n"+centro_nombre+"\n\nItem Presupuestario: "+item_codigo+".");
	  
	  if(monto=='' || monto==undefined) return;
	  
	  var myAjax = new Ajax.Request(
		'administracion/items_presupuestarios/sql_ppto.php',
		{
			method:'post',
			parameters:'ppto_monto='+monto+'&centro_ruta='+encodeURIComponent(centro_ruta)+'&ppto_item='+encodeURIComponent(item_codigo)+'&'+$('mesanio').serialize(),
			onComplete:function() {
				listar_gastos();
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




</script>

<center>

 <div class='sub-content' style='width:950px;'>
  <form id='gasto_frm' name='gasto_frm' style='overflow:auto;' autocomplete="off">
         <div class='sub-content'>
             <img src='iconos/money_dollar.png'>
             <b>Items Presupuestarios</b>
             
             <select id='reporte' name='reporte' onChange='listar_gastos();'>
             <option value='2'>Totales por Centros de Costo</option>
             <option value='0'>Ver Totales</option>
             <option value='1'>Ver Detalle</option>
             </select>
             
             Mes:
             <select id='mesanio' name='mesanio' onChange='listar_gastos();'>

             <?php 

				$meses=cargar_registros_obj("
				SELECT * FROM (SELECT DISTINCT extract('month' from orden_fecha) AS mes, extract('year' from orden_fecha) AS anio FROM orden_compra) AS foo ORDER BY anio DESC, mes DESC;
				");
				
				for($i=0;$i<sizeof($meses);$i++) {
					if($meses[$i]['mes']<10) $meses[$i]['mes']='0'.$meses[$i]['mes'];
					print("<option value='".$meses[$i]['mes'].'/'.$meses[$i]['anio']."'>".$meses[$i]['mes'].'/'.$meses[$i]['anio']."</option>");
				}
             
             ?>
             
             
             </select>
             
             <input type='button' id='' name='' value='[ Generar Certificado ]' onClick='certificado(0);' />
             
         </div>
         <div class='sub-content2' style='height:350px;overflow:auto;' id='listado'>

         </div>
  </form>
 </div>



<form id='planilla_csv' name='planilla_csv' enctype="multipart/form-data" 
action='administracion/items_presupuestarios/carga_csv.php' method='post' onSubmit='return false;'>
<div class='sub-content' style='width:750px;'>
<table style='width:100%;'>
<tr>
<td>Carga SIGFE:</td>
<td><input type='file' id='planilla' name='planilla' /></td>
<td><input type='button' onClick='importar();' 
id='' name='' value='-- Importar CSV... --'></td>
</tr>
</table>
</div>
</form>
</center>

<script>
    listar_gastos();

</script>
