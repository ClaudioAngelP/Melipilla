<?php  require_once('../../conectar_db.php');

	$orden_nro=pg_escape_string(utf8_decode($_GET['orden_nro']));

?>

<html>

<title> Modificar Orden de Compra</title>





<?php cabecera_popup('../../');?>









<script>

 

var cont_pedidos

var contiva

var contexento

bloquear_ingreso=false;

//*****************************************************************



function abrir_pedido(pedido_numero) {



  l=(screen.availWidth/2)-250;

  t=(screen.availHeight/2)-200;



  win = window.open('../../visualizar.php?pedido_nro='+pedido_numero, 'ver_pedido',

                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+

                    'resizable=no, width=600, height=445');



  win.focus();



}

//*****************************************************************



numero_orden= function()

{

    if(lista_detalle[0][0].orden_numero==false)

    {
        html1='<table>';
        html1+='<tr>';
        html1+='<td style="text-align:Left;"><b>Sin N&deg; de &Oacute;rden de Compra</b></td>';
        html1+='<td><input type="hidden" name="id_orden" id="id_orden" value='+$('norden').value+'  size="10" style="text-align:center;"></td>';
        html1+='</tr>';
        html1+='</table>';
    }
    else
    {	
        if(lista_detalle[0][0].orden_numero==$("norden").value)
        {
            html1='<table>';
            html1+='<tr>';
            html1+='<td style="text-align:Left;"><b>C&oacute;digo de Orden:</b></td>';
            html1+='<td><input type="text" name="interyorden" id="interyorden" value='+lista_detalle[0][0].orden_id+' disabled size="10" style="text-align:center;">';
            html1+='<input type="hidden" name="id_orden" id="id_orden" value='+lista_detalle[0][0].orden_id+' size="10" style="text-align:center;"></td>';
            html1+='<td style="text-align:Left;"><b>Fecha</b></td>';
            html1+='<td><input type="text" name="fecha" id="fecha" value='+lista_detalle[0][0].orden_fecha+' size="15" style="text-align:center;">';
				html1+='</tr>';
            html1+='</table>';
        }

        else

        {

            html1='<table>';

            html1+='<tr>';

            html1+='<td style="text-align:Left;"><b>C&oacute;digo de &Oacute;rden:</b></td>';

            html1+='<td><input type="text" name="interyorden" id="interyorden" value='+lista_detalle[0][0].orden_numero+' disabled size="15" style="text-align:center;"></td>';

            html1+='<td><input type="hidden" name="id_orden" id="id_orden" value='+$('norden').value+'  size="10" style="text-align:center;"></td>';
			
			html1+='<td style="text-align:Left;"><b>Fecha</b></td>';

            html1+='<td><input type="text" name="fecha" id="fecha" value='+lista_detalle[0][0].orden_fecha+' size="15" style="text-align:center;">';


            html1+='</tr>';

            html1+='</table>';

        }

    }

    $("interno").innerHTML=html1;

}

//****************************************************************************

/*
ver_anular = function(){

		console.log(lista_detalle[0][0].orden_estado);
		
		if(lista_detalle[0][0].orden_estado==0)
		{
			anulahtml+='<td>';
			anulahtml+='<div id="anular_orden" class="boton" >';
			anulahtml+='<table>';
			anulahtml+='<tr>';
			anulahtml+='<td>';
			anulahtml+='<img src="../../iconos/database_delete.png">';
			anulahtml+='</td>';
			anulahtml+='<td>';
			anulahtml+='<a href="#" onClick="anular_orden();">Anular &Oacute;rden de Compra</a>';
			anulahtml+='</td>';
			anulahtml+='</tr>';
			anulahtml+='</table>';
			anulahtml+='</div>';
			anulahtml+='</td>';
		}
		
		$("anular").innerHTML=anulahtml;
}
*/

//****************************************************************************



escribir_proveedores = function()

{

    $("proveedor_id").value=lista_detalle[0][0].prov_id.unescapeHTML();

    $("rutproveedor").value=lista_detalle[0][0].prov_rut.unescapeHTML();

    $("nombreprove").value=lista_detalle[0][0].prov_glosa.unescapeHTML();

    $("fonoprove").value=lista_detalle[0][0].prov_fono.unescapeHTML();

    $("direccionprove").value=lista_detalle[0][0].prov_direccion.unescapeHTML();

    $("ciudadprove").value=lista_detalle[0][0].prov_ciudad.unescapeHTML();



}

//*****************************************************************************************

escribir_pedidos = function()

{



    pedidos1=new Array();

    if(lista_detalle[3]!=false )

    {



        cont_pedidos=lista_detalle[3].length;

        html1='<table style="width:100%;">';

        html1+='<tr>';

        html1+='<td>';

        html1+='<table class="tabla_header">';

        html1+='<tr>';

        html1+='<td>Pedidos Asociados a &Oacute;rden de Compra:</td>';

        html1+='</tr>';

        html1+='</table>';

        html1+='</td>';

        html1+='<td style="text-align:right;"><span id="btn_agre_pedido">';

        html1+='<span onClick="agregar_pedido();" style="cursor:pointer;color:#6666FF;">Agregar Pedido</span></span></td>';

        html1+='</tr>';

        html1+='</table>';

        $("encabezado_pedido").innerHTML=html1;

        html1='<table>';

        html1+='<tr>';

        for(i=0;i<lista_detalle[3].length;i++)

        {

            pedidos1[i]= new Object();

            pedidos1[i].id_pedido=lista_detalle[3][i].pedido_id;

            pedidos1[i].pedido_nro= lista_detalle[3][i].pedido_nro;

            

            //if(i%2==0) color='#dfe6ef'; else color='#cfd6df';

            //html1+='<td bgcolor="'+color+'">'+lista_detalle[3][i].pedido_nro+'</td>';

            html1+='<td>';

            html1+='<span class="texto_tooltip" onClick="abrir_pedido('+lista_detalle[3][i].pedido_nro+');">';

            //html1+=''+lista_detalle[3][i].pedido_nro+'</span></td>';

            html1+=''+pedidos1[i].pedido_nro+'</span></td>';

            html1+='<td><center><img src="../../iconos/delete.png"';

            html1+='onClick="quitar('+i+');" style="cursor:pointer;"></center></td>&nbsp;';

        }

        html1+='</tr>';

        html1+='</table>';

        $("pedidos").innerHTML=html1;



    }

    else

    {

        cont_pedidos=0;

        html1='<table style="width:100%;">';

        html1+='<tr class="tabla_header">';

        html1+='<tr>';

        html1+='<td>';

        html1+='<table class="tabla_header">';

        html1+='<tr>';

        html1+='<td>La &Oacute;rden de Compra no tiene Pedidos Asociados.</td>';

        html1+='</tr>';

        html1+='</table>';

        html1+='</td>';

        html1+='<td style="text-align:right;"><span id="btn_agre_pedido">';

        html1+='<span onClick="agregar_pedido();" style="cursor:pointer;color:#6666FF;">Agregar Pedido</span></span></td>';

        html1+='</tr>';

        html1+='</table>';

        $("encabezado_pedido").innerHTML=html1;



    }





}



//************************************************************************************



agregar_pedido = function()

{

    l=(screen.availWidth/2)-300;

    t=(screen.availHeight/2)-75;

    win = window.open('../../abastecimiento/ajustar_orden_compra/listar_pedidos1.php', '',

                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+

                    'resizable=no, width=600, height=400');

    win.focus();

}



//************************************************************************************



usar_pedido1 = function(pedido_datos, pedido_detalle)

{

    for(i=0;i < pedidos1.length;i++)

    {

        if(pedidos1[i].pedido_nro==pedido_datos[0].pedido_nro)

        {

            alert('El pedido que ha seleccionado ya se encuentra asociado a esta orden de compra.');

            return

        }

    }

    var index=pedidos1.length

    pedidos1[index]= new Object();

    pedidos1[index].id_pedido=pedido_datos[0].pedido_id;

    pedidos1[index].pedido_nro=pedido_datos[0].pedido_nro;

    cont_pedidos=pedidos1.length;

    html1='<table>';

    html1+='<tr>';

    for(i=0;i < pedidos1.length;i++)

    {

        html1+='<td>';

        html1+='<span class="texto_tooltip" onClick="abrir_pedido('+pedidos1[i].pedido_nro+');">';

        html1+=''+pedidos1[i].pedido_nro+'</span></td>';

        html1+='<td><center><img src="../../iconos/delete.png"';

        html1+=' onClick="quitar('+i+');" style="cursor:pointer;"></center></td>&nbsp;';

    }

    html1+='</tr>';

    html1+='</table>';

    $("pedidos").innerHTML=html1;

   





}

//************************************************************************************



quitar=function(num)

{

    pedidos1=pedidos1.without(pedidos1[num]);

    html1='<table>';

    html1+='<tr>';

    cont_pedidos=pedidos1.length;

    for(i=0;i < pedidos1.length;i++)

    {

        html1+='<td>';

        html1+='<span class="texto_tooltip" onClick="abrir_pedido('+pedidos1[i].pedido_nro+');">';

        html1+=''+pedidos1[i].pedido_nro+'</span></td>';

        html1+='<td><center><img src="../../iconos/delete.png"';

        html1+='onClick="quitar('+i+');" style="cursor:pointer;"></center></td>&nbsp;';

    }

    html1+='</tr>';

    html1+='</table>';

    $("pedidos").innerHTML=html1;

}









//************************************************************************************



agregar_articulos_servicios =function()

{

    var x=0;

    var i=0;

    articulos=new Array();

    if(lista_detalle[1]!=false) //guarda detalle de ordenes de compra que esten en tabla orden_detalle

    {

        for(i=0;i<lista_detalle[1].length;i++)

        {

            

            articulos[i]= new Object();

            articulos[i].ordetalle_orserv_id=lista_detalle[1][i].ordetalle_id;

            articulos[i].orden_id=lista_detalle[1][i].ordetalle_orden_id;

            articulos[i].art_id=lista_detalle[1][i].ordetalle_art_id;

            articulos[i].cant=lista_detalle[1][i].ordetalle_cant*1;

            articulos[i].subtotal=lista_detalle[1][i].ordetalle_subtotal;

            articulos[i].glosa=lista_detalle[1][i].art_glosa;

            articulos[i].codigo=lista_detalle[1][i].art_codigo;

            articulos[i].item_glosa=lista_detalle[1][i].item_glosa;

            articulos[i].item_codigo=lista_detalle[1][i].item_codigo

            

        }



    }

    if(lista_detalle[2]!=false) // guarda detalle de ordenes de compra que esten en tabla orden_servicios

    {

        x=articulos.length;

        

        if(x!=0)

        {

            //x=x-1;

           

            for(i=0;i<lista_detalle[2].length;i++)

            {

                articulos[x]= new Object();

                articulos[x].ordetalle_orserv_id=lista_detalle[2][i].orserv_id;

                articulos[x].orden_id=lista_detalle[2][i].orserv_orden_id;

                articulos[x].art_id='(n/a)';

                articulos[x].cant=lista_detalle[2][i].orserv_cant;

                articulos[x].subtotal=lista_detalle[2][i].orserv_subtotal;

                articulos[x].glosa=lista_detalle[2][i].orserv_glosa;

                articulos[x].codigo='(n/a)';

                articulos[x].item_glosa=lista_detalle[2][i].item_glosa

                articulos[x].item_codigo=lista_detalle[2][i].item_codigo

                x=x+1;

            }

        }

        else

        {

            for(i=0;i<lista_detalle[2].length;i++)

            {

                articulos[i]= new Object();

                articulos[i].ordetalle_orserv_id=lista_detalle[2][i].orserv_id;

                articulos[i].orden_id=lista_detalle[2][i].orserv_orden_id;

                articulos[i].art_id='(n/a)';

                articulos[i].cant=lista_detalle[2][i].orserv_cant;

                articulos[i].subtotal=lista_detalle[2][i].orserv_subtotal;

                articulos[i].glosa=lista_detalle[2][i].orserv_glosa;

                articulos[i].codigo='(n/a)';

                articulos[i].item_glosa=lista_detalle[2][i].item_glosa;

                articulos[i].item_codigo=lista_detalle[2][i].item_codigo

            }

        }

    }

}



//**************************************************************************

habilitar_botones = function()

{

 $('guardar_orden').style.display='';

 $('cancelar_cambios_orden').style.display='';
 
 $('anular_orden').style.display='';

}



//*****************************************************************



desabilitar_botones = function()

{

 $('guardar_orden').style.display='none';

 $('cancelar_cambios_orden').style.display='none';
 
 $('anular_orden').style.display='none';
	
}



//*****************************************************************


	abrir_articulo_ac = function(d, obj) {
		
		try {
			
			var obj_name=$(obj).name.split('_');
			var contador=obj_name[2]*1;
		
			//alert(d);
		
			$('art_id_'+contador).value=d[5];
			$('glosa_'+contador).innerHTML=d[2];
			
			articulos[contador].art_id=d[5];
			articulos[contador].codigo=d[0];
			articulos[contador].glosa=d[2];
			
			if(d[7]!='') {
				$('item_id_'+contador).innerHTML=d[6];
				$('txt_id_item_'+contador).value=d[6];
				$('txt_glosa_item_'+contador).value=d[7];
				$('span_item_'+contador).innerHTML=d[7];
				articulos[contador].item_codigo=d[6];
				articulos[contador].item_glosa=d[7];
			} else {
				$('item_id_'+contador).value='';
				$('txt_id_item_'+contador).innerHTML='';
				$('txt_glosa_item_'+contador).value='';
				$('span_item_'+contador).innerHTML='';
				articulos[contador].item_codigo='';
				articulos[contador].item_glosa='';
			}
		
	} catch(err) {
		
		alert(err);
		alert(d[0]);
		
	}
		
	}


dibujar_tabla = function()

{

    html='<table style="font-size:11px;width:100%;" >';

    html+='<tr class="tabla_header">';

    html+='<td style="display:none;">detalle-Serv-id</td>'

    html+='<td style="display:none;">Art_id</td>';

    html+='<td>Cod.Int.</td>';

    html+='<td>Glosa</td>';

    html+='<td colspan=2>Item Presupuestario</td>';

    html+='<td>Cant.</td>';

    html+='<td>P.Unit.</td>';

    html+='<td>Subtotal</td><td>&nbsp;</td>';

    html+='</tr>';

    for(i=0;i<articulos.length;i++)

    {

        if(i%2==0) clase='tabla_fila'; else clase='tabla_fila2';
        
        html+='<tr class="'+clase+'">';

        html+='<td style="display:none;">'+articulos[i].ordetalle_orserv_id+'</td>';

        html+='<td style="display:none;"><input type="text" id="art_id_'+i+'" name="art_id_'+i+'" value="'+articulos[i].art_id+'" /></td>';

        html+='<td style="text-align:right;font-size:11px;">';

		if(articulos[i].codigo!='(n/a)')
			html+='<input type="text" id="codigo_art_'+i+'" name="codigo_art_'+i+'" value="'+articulos[i].codigo+'" size=10 /></td>';
		else
			html+='<input type="text" id="codigo_art_'+i+'" name="codigo_art_'+i+'" value="" size=10 /></td>';

        html+='<td style="font-size:11px;" id="glosa_'+i+'">'+articulos[i].glosa+'</td>';

        html+='<td style="text-align:center;font-size:11px;">';

        html+='<input type="hidden" id="txt_id_item_'+i+'" name="txt_id_item_'+i+'" value="'+articulos[i].item_codigo+'" />';

        html+='<span id="item_id_'+i+'" name="item_id_'+i+'">'+articulos[i].item_codigo+'</span>';

        html+='</td>';

        html+='<td style="text-align:center;font-size:11px;">';

        html+='<input type="hidden" id="txt_glosa_item_'+i+'" name="txt_glosa_item_'+i+'" value="'+articulos[i].item_glosa+'" />';

        if(articulos[i].item_codigo!='')

        {

            if(articulos[i].codigo=='(n/a)')

            {

                html+='<span id="span_item_'+i+'" name="span_item_'+i+'" style=cursor:pointer; onClick=seleccionar_item_a('+i+');>'+articulos[i].item_glosa+'</span>';

            }

            else

            {

                html+='<span id="span_item_'+i+'" name="span_item_'+i+'">'+articulos[i].item_glosa+'</span>';

            }

        }

        else

        {

            html+='<span id="span_item_'+i+'" name="span_item_'+i+'" style="cursor:pointer; color:red;" onClick=seleccionar_item_a('+i+');>N/A</span>';

        }

        html+='</td>';

        html+='<td style="font-size:11px;"><center><input onKeyUp="recalcular();" type=text id=cant_'+i+' name=cant_'+i+' value='+number_format(articulos[i].cant,2,',','.')+' style=text-align:right; size=6></center></td>';

        html+='<td id="punit_'+i+'" name="punit_'+i+'" align=right style="text-align:right;font-size:11px;">$'+number_format(Math.round((articulos[i].subtotal/articulos[i].cant)*100)/100,1,',','.')+'.-</td>';

        //html+='<td id=punit_'+i+' name=punit_'+i+' align=right style="text-align:center;font-size:11px;">'+number_format(articulos[i].subtotal/articulos[i].cant,1,',','.')+'</td>';

        html+='<td style="font-size:11px;"><center><input  onKeyUp="recalcular();" type=text id=subtotal_'+i+' name=subtotal_'+i+' value='+Math.round(articulos[i].subtotal)+' style=text-align:right; size=10></center></td>';

        //html+='<td style="font-size:11px;"><center><input  onKeyUp="recalcular();" type=text id=subtotal_'+i+' name=subtotal_'+i+' value='+number_format(articulos[i].subtotal,1,',','.')+' style=text-align:right; size=10></center></td>';

        html+='<td><center><img src="../../iconos/delete.png"';

        html+='onClick="quitar_articulos('+i+');" style="cursor:pointer;"></center></td>';

        html+='</tr>';

        

    }

   

    if(i%2==0) clase='tabla_fila'; else clase='tabla_fila2';



    html+='<tr class="'+clase+'" style="font-weight:bold;">';

    html+='<td colspan=6 style="text-align:right;">Descuento:</td>';

    if(lista_detalle[0][0].orden_desc==null)

    {

        html+='<td style="font-size:11px;text-align:center;"><input onKeyUp="recalcular();" type=text id=orden_dsc name=orden_dsc  value='+number_format(0,0)+' style="text-align:right"; size=10></td>';

    }

    else

    {

        html+='<td style="font-size:11px;text-align:center;"><input onKeyUp="recalcular();" type=text id=orden_dsc name=orden_dsc  value='+number_format(lista_detalle[0][0].orden_desc,0)+' style="text-align:right"; size=10></td>';

    }

    //html+='<td id="orden_neto" style="text-align:right;">$0.-</td>';

    html+='<td rowspan=4>&nbsp;</td></tr>';

    html+='<tr class="tabla_header" style="font-weight:bold;">';

    html+='<td colspan=6 style="text-align:right;">Neto:</td>';

    html+='<td id="orden_neto" style="text-align:right;">$0.-</td></tr>';

    //html+='<td style="font-size:11px;"><right><input onKeyUp="recalcular();" type=text id=orden_dsc name=orden_dsc  value='+number_format(lista_detalle[0][9],1,',','.')+'.- style=text-align:right; size=7></right></td></tr>';

    

    html+='<tr class="tabla_header" style="font-weight:bold;">';

    html+='<td colspan=6 style="text-align:right;">I.V.A.:</td>';

    html+='<td id="orden_iva" style="text-align:right;">$0.-</td></tr>';

    html+='<tr class="tabla_header" style="font-weight:bold;">';

    html+='<td colspan=6 style="text-align:right;">Total:</td>';

    html+='<td id="orden_total" style="text-align:right;">$0.-</td></tr>';

    html+='</table>'

    $("detalle_orden").innerHTML=html;

	autocompletar_medicamentos=[];

	for(i=0;i<articulos.length;i++) {

				  eval("autocompletar_medicamentos["+i+"] = new AutoComplete("+
				  "'codigo_art_"+i+"',"+
				  "'../../autocompletar_sql.php',"+
				  "function() {"+
				  " if($('codigo_art_"+i+"').value.length<3) return false;"+
				  "	return {"+
				  "	  method: 'get',"+
				  "	  parameters: 'tipo=buscar_arts&codigo='+encodeURIComponent($('codigo_art_"+i+"').value)"+
				  "	}"+
				  "}, 'autocomplete', 350, 150, 150, 1, 3, abrir_articulo_ac);");

	}


    recalcular();

    

}





//*****************************************************************



seleccionar_item_a = function(fila)

{

    

    params= 'item='+encodeURIComponent($('txt_id_item_'+fila).value)+

    '&fila='+encodeURIComponent(fila);

    top=Math.round(screen.height/2)-150;

    left=Math.round(screen.width/2)-200;

    new_win =

    window.open('../../abastecimiento/ajustar_orden_compra/seleccionar_item.php?'+

    params,

    'win_items', 'toolbar=no, location=no, directories=no, status=no, '+

    'menubar=no, scrollbars=yes, resizable=no, width=400, height=300, '+

    'top='+top+', left='+left);

    new_win.focus();

}



//*****************************************************************



cont_iva = function()

{

    contiva=1;

}



//*****************************************************************



recalcular = function()

{

    

    var total=0;

    var neto=0;

    var iva=0;

    var aux=0;

    

    var desc=$('orden_dsc').value.replace('.','')*1;

    









    for(i=0;i<articulos.length;i++)

    {

        var cant=$('cant_'+i).value.replace('.','');

        cant=cant.replace(',','.');

        

        var cant2=$('subtotal_'+i).value.replace('.','');

        cant2=cant2.replace(',','.');

        

        articulos[i].cant=(cant*1);

        articulos[i].subtotal=(cant2*1);

        if(articulos[i].subtotal!=0 && articulos[i].cant!=0)

        {

            unittmp=number_format((Math.round((articulos[i].subtotal/articulos[i].cant)*100)/100), 1, ',', '.');



            //unittmp=number_format((articulos[i].subtotal/articulos[i].cant),1,',','.');

           

        }

        else

        {

            unittmp=number_format(0, 1, ',', '.');;

        }

        $('punit_'+i).innerHTML='$'+unittmp+'.-';

      



        neto=neto+($('subtotal_'+i).value.replace('.',''))*1;

    }

    neto=neto-desc;

    



if($('exectoiva').checked)

{

      

        iva=0;

        total=neto;

}

else

{

    if($('ivaincl').checked)

    {

            if(contiva==1)

            {

                

                neto=0;

                for(i=0;i<articulos.length;i++)

                {

                    $('subtotal_'+i).value=Math.round(articulos[i].subtotal/_global_iva,0);

                    neto=neto+($('subtotal_'+i).value.replace('.',''))*1;

                }

            

                

            }

            neto=neto-desc;

            

            //total=neto;

            total=neto*_global_iva;

            //neto=total/_global_iva;

            iva=total-neto;

        }

        else

        {

            

           if(contiva==1)

           {

                

                neto=0;

                for(i=0;i<articulos.length;i++)

                {

                    $('subtotal_'+i).value=Math.round(articulos[i].subtotal*_global_iva,0);

                    neto=neto+($('subtotal_'+i).value.replace('.',''))*1;

                }

            

                neto=neto-desc;

           }



            

            

            total=neto*_global_iva;

            iva=total-neto;

        }

contiva=0;







    }



    $('orden_neto').innerHTML='$'+number_format(neto, 1, ',', '.')+'.-';

    //$('orden_neto').innerHTML='$'+neto+'.-';

    $('orden_iva').innerHTML='$'+number_format(iva, 1, ',', '.')+'.-';

    $('orden_total').innerHTML='$'+number_format(total, 1, ',', '.')+'.-';



}





//*****************************************************************



validar = function()

{

    if($('exectoiva').checked)

    {

        

        

        $('ivaincl').checked=false;

        $('ivaincl').disabled=true;

    }

    else

    {

        

        $('ivaincl').checked=false;

        $('ivaincl').disabled=false;

    }    

    

        recalcular();

    

    

 



}



//*****************************************************************



quitar_articulos = function(num)

{

    articulos=articulos.without(articulos[num]);

    dibujar_tabla();

}



//*****************************************************************



buscarorden = function()

{

    //var ordencompra = document.getElementById('ordentxt');\

    var nro_orden=$('norden').serialize();

    var myAjax = new Ajax.Request('../../abastecimiento/ajustar_orden_compra/buscar_orden_compra.php',

    {method:'post',parameters: nro_orden, evalScripts:true,

        onComplete: function(resp)

        {

            lista_detalle=resp.responseText.evalJSON(true);

        if(lista_detalle==false)

            {

              html='<div class=sub-content style="text-align:center;">"EL NUMERO DE ORDEN DE COMPRA INGRESADO NO EXISTE"</div>';

                $("detalle_orden").innerHTML=html;

            }

            else

            {

                

                numero_orden();

                escribir_proveedores();

                escribir_pedidos();

                agregar_articulos_servicios();

                $('iva_agregar').style.display='';

                if(lista_detalle[0][0].orden_iva!=1.00)

                {

                    $('ivaincl').checked=false;

                }

                else

                {

                    $('exectoiva').checked=true;

                    $('ivaincl').disabled=true;

                }



                dibujar_tabla();







                //recalcular();

                habilitar_botones();
                
                ver_anular();

            }

        }

    }

    );

}



//*****************************************************************



limpiar = function()

{

    

    

    $("rutproveedor").value='';

    $("nombreprove").value='';

    $("fonoprove").value='';

    $("direccionprove").value='';

    $("ciudadprove").value='';

    html1='';

    $("interno").innerHTML=html1;

    $("encabezado_pedido").innerHTML=html1;

    $("pedidos").innerHTML=html1;

    $("pedidos").innerHTML=html1;

    $("iva_agregar").style.display='none';

    html='';

    $('div_agregar_articulos').style.display='none';

    $('div_agregar_servicios').style.display='none';

    $("detalle_orden").innerHTML=html;

    $('ivaincl').disabled=false;

    $('ivaincl').checked=false;

    $('exectoiva').checked=false;

    desabilitar_botones();

}



//*****************************************************************



limpiar_todo = function()

{



    $("norden").value='';

    $("rutproveedor").value='';

    $("nombreprove").value='';

    $("fonoprove").value='';

    $("direccionprove").value='';

    $("ciudadprove").value='';

    html1='';

    $("interno").innerHTML=html1;

    $("encabezado_pedido").innerHTML=html1;

    $("pedidos").innerHTML=html1;

    $("pedidos").innerHTML=html1;

    $("iva_agregar").style.display='none';

    html='';

    $('div_agregar_articulos').style.display='none';

    $('div_agregar_servicios').style.display='none';

    $("detalle_orden").innerHTML=html;

    desabilitar_botones();





}



//*****************************************************************



agrega_arts = function()

{

    $('div_agregar_articulos').style.display='';

    $('div_agregar_servicios').style.display='none';

    $('codigo').value='';

    $('art_nombre').value='';

    $('codigo').focus();

}



//*****************************************************************



agrega_servs = function()

{

    $('div_agregar_articulos').style.display='none';

    $('div_agregar_servicios').style.display='';

    $('glosa_serv').value='';

    $('item_serv').value='';

    $('glosa_serv').focus();

}



//*****************************************************************



seleccionar_articulo = function (art)

{

    $('art_nombre').innerHTML=art[2];

    $('art_id').value=art[5];

    $('art_codigo').value=art[0];

    $('item_codigo').value=art[6];

    $('item_glosa').value=art[7];

    $('btn_agregar').focus();



}



//*****************************************************************



seleccionar_item = function (item)

{

    $('item_codigo').value=item[0];

    $('item_glosa').value=item[2];

    $('btn_agregar2').focus();

}



//*****************************************************************



mostrar_proveedor = function(datos)

{

    $("proveedor_id").value=datos[3];

    $("nombreprove").value=datos[2];

    $("fonoprove").value=datos[5];

    $("direccionprove").value=datos[4];

    $("ciudadprove").value=datos[6]

    cambioprovee=true;



}

//*****************************************************************



limpiar_proveedor = function()

{

    $("proveedor_id").value='';

    $("nombreprove").value='';

    $("fonoprove").value='';

    $("direccionprove").value='';

    $("ciudadprove").value='';

}



//*****************************************************************



limpiar_arts = function()

{

    $('art_nombre').innerHTML='';

    $('art_id').value='';

    $('art_codigo').value='';

    $('item_codigo').value='';

    $('item_glosa').value='';

    $('codigo').value='';

    $('codigo').focus();



}



//*****************************************************************



    agregar_art = function()

    {

        if($('codigo').value!='')

        {

            // Si el articulo ya fué ingresado se retira.

            for(var i=0;i<articulos.length;i++)

            {

                if(articulos[i].art_id==$('art_id').value)

                {

                    alert(('Este Articulo ya se encuentra ingresado en esta &oacute;rden').unescapeHTML());

                    limpiar_arts();

                    return;

                }

            }

            num = articulos.length;

            articulos[num] = new Object();

            articulos[num].ordetalle_orserv_id='(n/a)';

            articulos[num].orden_id=$('id_orden').value;

            articulos[num].art_id=$('art_id').value;

            articulos[num].cant=0;

            articulos[num].subtotal=0;

            articulos[num].glosa=$('art_nombre').innerHTML;

            articulos[num].codigo=$('art_codigo').value;

            articulos[num].item_glosa=$('item_glosa').value;

            articulos[num].item_codigo=$('item_codigo').value;

            dibujar_tabla();

            $('codigo').value='';

            $('codigo').select();

            $('codigo').focus();

        }

        else

        {

            alert(('No ha seleccionado art&iacute;culos para ingresar en la &oacute;rden').unescapeHTML());

            $('codigo').value='';

            $('codigo').select();

            $('codigo').focus();

        }

    }



//*****************************************************************



agregar_serv = function ()

{

    num = articulos.length;

    articulos[num] = new Object();

    articulos[num].ordetalle_orserv_id='(n/a)';

    articulos[num].orden_id=$('id_orden').value;

    articulos[num].art_id='(n/a)';

    articulos[num].cant=0;

    articulos[num].subtotal=0;

    articulos[num].glosa=$('glosa_serv').value;

    articulos[num].codigo='(n/a)';

    articulos[num].item_glosa=$('item_glosa').value;

    articulos[num].item_codigo=$('item_serv').value;

    dibujar_tabla();

    $('glosa_serv').value='';

    $('item_serv').value='';

    $('glosa_serv').select();

    $('glosa_serv').focus();





}



//*****************************************************************



guardar_cambios=function()

{

    var valores0=false;

    var cont=0;

    if(bloquear_ingreso) return;

    if (articulos.length==0)

    {

        alert(('No tiene producto o servicios ingresados a esta orden de compra.').unescapeHTML());

        return;

    }

    if($("nombreprove").value=='')

    {

        alert(('No se ha seleccionado un proveedor valido.').unescapeHTML());

        return;

    }

    for(i=0;i < articulos.length;i++)

    {

        if($('txt_id_item_'+i).value=='')

        {

            alert('Para poder guardar los cambios de la orden de compra debe asociar el o los item presupuestario faltantes')

            return;

        }

        else

        {

            

            articulos[i].item_codigo=$('txt_id_item_'+i).value;

            articulos[i].item_glosa=$('txt_glosa_item_'+i).value;

            

        }

    }

    if(pedidos1.length==0)

    {

        confirmarpds=confirm(('La orden no tiene pedidos asociados esta seguro de guardar cambios.').unescapeHTML());

        if(confirmarpds)

        {

            for(i=0;i<articulos.length;i++)

            {

                if(articulos[i].cant==0 || articulos[i].subtotal==0)

                {

                    cont=cont+i;

                    valores0=true;

                    break;

                }

            }

            if(valores0==true)

            {

                alert(('exciten datos como cantidad o subtotal en un articulo o servicio con valores en 0 en esta orden').unescapeHTML());

                return;

            }

            else

            {

                confirmar=confirm(('Esta seguro de realizar cambios en la &oacute;rden de compra').unescapeHTML());

                if(confirmar)

                {



                    bloquear_ingreso=true;



                    var detalle_arts=encodeURIComponent(articulos.toJSON());

                    var pedidos=encodeURIComponent(pedidos1.toJSON());

               

                                        

                    params=$('id_orden').serialize()+'&'+$('proveedor_id').serialize()+'&'+$('ivaincl').serialize()+'&'+$('exectoiva').serialize()+'&'+$('orden_dsc').serialize()+'&cont_pedidos='+cont_pedidos+'&articulos='+detalle_arts+'&pedidos='+pedidos;

                    var myAjax = new Ajax.Request(

                    '../../abastecimiento/ajustar_orden_compra/guardar_orden_compra.php',

                    {

                        method: 'post',

                        parameters: params,

                        onComplete: function(resp)

                        {

                            try {



                                    var datos=resp.responseText.evalJSON(true);

                                    if(datos[0])

                                    {

                                        alert('&Oacute;rden de Compra Modificada exitosamente.'.unescapeHTML());

                                        limpiar();

                                    }

                                    else

                                    {

                                        alert('ERROR:\n\n'+resp.responseText);

                                    }



                                }

                                catch(err){

                                    alert('ERROR:\n\n'+resp.responseText);

                                }



                                bloquear_ingreso=false;

                        }

                    }

                    );









                }

                else

                {

                    

                    $('norden').select();

                    $('norden').focus();



                }

            }

        }

        else

        {

            return;

        }

    }

    else

    {

        for(i=0;i<articulos.length;i++)

            {

                if(articulos[i].cant==0 || articulos[i].subtotal==0)

                {

                    cont=cont+i;

                    valores0=true;

                    break;

                }

            }

            if(valores0==true)

            {

                alert(('exciten datos como cantidad o subtotal en un articulo o servicio con valores en 0 en esta orden').unescapeHTML());

                return;

            }

            else

            {

                confirmar=confirm(('Esta seguro de realizar cambios en la &oacute;rden de compra').unescapeHTML());

                if(confirmar)

                {



                    bloquear_ingreso=true;





                    var detalle_arts=encodeURIComponent(articulos.toJSON());

                    var pedidos=encodeURIComponent(pedidos1.toJSON());

                    

                    

                   



                    params=$('id_orden').serialize()+'&'+$('proveedor_id').serialize()+'&'+$('ivaincl').serialize()+'&'+$('exectoiva').serialize()+'&'+$('orden_dsc').serialize()+'&cont_pedidos='+cont_pedidos+'&articulos='+detalle_arts+'&pedidos='+pedidos;

                    var myAjax = new Ajax.Request(

                    '../../abastecimiento/ajustar_orden_compra/guardar_orden_compra.php',

                    {

                        method: 'post',

                        parameters: params,

                        onComplete: function(resp)

                        {

                            try {



                                    var datos=resp.responseText.evalJSON(true);

                                    if(datos[0])

                                    {

                                        alert('&Oacute;rden de Compra ingresada exitosamente.'.unescapeHTML());

                                        limpiar();

                                    }

                                    else

                                    {

                                        alert('ERROR:\n\n'+resp.responseText);

                                    }



                                }

                                catch(err)

                                {

                                    alert('ERROR:\n\n'+resp.responseText);

                                }



                                bloquear_ingreso=false;

                        }

                    }

                    );

                }

                else

                {

                    $('norden').select();

                    $('norden').focus();



                }

            }

        }

}


		anular_orden = function(){

					confirmar=confirm(('Esta seguro de anular la &oacute;rden de compra').unescapeHTML());

                if(confirmar)
                {
                    bloquear_ingreso=true;                                     

                    params=$('id_orden').serialize();

                    var myAjax = new Ajax.Request(
                    '../../abastecimiento/ajustar_orden_compra/anular_orden_compra.php',
                    {
                        method: 'post',
                        parameters: params,
                        onComplete: function(resp)
                        {
                            try {
                                    var datos=resp.responseText.evalJSON(true);
                                    if(datos[0])
                                    {
                                        alert('&Oacute;rden de Compra Anulada exitosamente.'.unescapeHTML());
                                        limpiar();
                                    }
                                    else
                                    {
                                        alert('ERROR:\n\n'+resp.responseText);
                                    }
                                }
                                catch(err){
                                    alert('ERROR:\n\n'+resp.responseText);
                                }
                                bloquear_ingreso=false;
                        }
                    }
                    );
                }
     }

</script>



<body class="popup_background fuente_por_defecto">



<center>

<div class="sub-content" style="width:750px;">

    <div class="sub-content">

        <img src="../../iconos/wand.png">

        <b>Modificar de &Oacute;rden de Compra</b>

    </div>

    <div class="sub-content" >

        <table>

            <tr>

                <td style="text-align:right;"><b>Nro. de Orden de Compra:</b></td>

                <td><input type='text' name='norden' id='norden' value='<?php echo $orden_nro; ?>'
					onKeyUp='if(event.which==13)buscarorden();if(event.which!=13)limpiar();' size=17></td>

                <td style='width:15%;'><td>

                <td>

                    <div id='interno' >



                    </div>

                </td>

            </tr>

        </table>

        <div class="sub-content">

            <table>

                <tr>

                    <input type="hidden" id='proveedor_id' name='proveedor_id' size='12'>

                </tr>

                <tr>

                    <td style="text-align:right;"><b>R.U.T. Proveedor:</b></td>

                    <td><input type='text' name='rutproveedor' id='rutproveedor' onKeyUp='if(event.which!=13)limpiar_proveedor();'size='12'style='text-align:left;'></td>

                    <td style='width:5%;'></td>

                    <td style="text-align:right;"><b>Nombre:</b></td>

                    <td><input type='text' name='nombreprove' id='nombreprove' disabled size='45' style='font-size:11px'></td>

                    <td style='width:1%;'></td>

                    <td style="text-align:right;"><b>Fono:</b></td>

                    <td><input type='text' name='fonoprove' id='fonoprove' disabled size='20' style='font-size:10px; text-align:center;'></td>

                </tr>

                <tr>

                    <td style="text-align:right;"><b>Direcci&oacute;n:</b></td>

                    <td colspan=5><input type='text' name='direccionprove' id='direccionprove' disabled size='66'></td>

                    <td style="text-align:right;"><b>Ciudad:</b></td>

                    <td><input type='text' name='ciudadprove' id='ciudadprove' disabled size='25' style='font-size:10px; text-align:center;'></td>

                </tr>

             </table>

        </div>

        <div id='encabezado_pedido'>

        </div>

        <div class="sub-content2" style='overflow:auto;' id='pedidos'>

        </div>

        <div class='sub-content' id='iva_agregar' style='display:none;'>

            <table style='width:100%;'>

                <tr>

                    <td>

                        <b>

                            Detalle ( 

                            <input type='checkbox' id='ivaincl' name='ivaincl' onClick='cont_iva();recalcular();'>

                                Valores con I.V.A. Inclu&iacute;do)  ///////// (

                            <input type='checkbox' id='exectoiva' name='exectoiva' onClick='validar();'>

                            &Oacute;rden de compra excenta de I.V.A).

                        </b>

                    </td>

                    <td style='text-align:right;'>

                        <span id='botones_agregar'>

                            <span onClick='agrega_arts();' style='cursor:pointer;color:#6666FF;'>Agregar Art&iacute;culo</span> - 

                            <span onClick='agrega_servs();' style='cursor:pointer;color:#6666FF;'>Agregar Servicio</span>

                        </span>

                    </td>

                </tr>

            </table>

        </div>

        <div class='sub-content' id='div_agregar_articulos' style='display:none;'>

            <table style='width:100%;'>

            <tr>

                <td style='text-align:right;'>C&oacute;digo Int.:</td>

                <td>

                    <input type='hidden' id='art_id' name='art_id'>

                    <input type='hidden' id='art_codigo' name='art_codigo'>

                    <input type='hidden'  id='item_codigo' name='item_codigo'>

                    <input type='hidden' id='item_glosa' name='item_glosa'>

                    <input id='codigo' name='codigo'>

                </td>

                <td style='width:45%;' id='art_nombre'></td>

                <td>

                    <input type='button' value='Agregar...' id='btn_agregar' onKeyUp='if(event.which==13)agregar_art();' onclick='agregar_art();'>

                </td>

           </tr>

           </table>

        </div>

        <div class='sub-content' id='div_agregar_servicios' style='display:none;'>

            <table style='width:100%;'>

                <tr>

                    <td style='text-align:right;'>Descripci&oacute;n:</td>

                    <td> <input type='text' id='glosa_serv' name='glosa_serv' size=45></td>

                    <td><input type='text' id='item_serv' name='item_serv' size=25></td>

                    <td><input type='button' value='Agregar...' id='btn_agregar2' onKeyUp='agregar_serv();' onMouseUp='agregar_serv();'></td>

                </tr>

            </table>

        </div>





        <div class='sub-content2' style='overflow:auto;' id='detalle_orden'>

        

        </div>

    </div>

 <center>

    <table><tr><td>

        <div id='guardar_orden' class='boton' style='display: none;'>





            <table>

                <tr>

                    <td>

                        <img src='../../iconos/database_edit.png'>

                    </td>

                    <td>

                        <a href='#' onClick='guardar_cambios();'> Realizar Ajustes a &Oacute;rden de Compra</a>

                    </td>

                </tr>

            </table>

        </div>

        </td>

        <td>

        <div id='cancelar_cambios_orden' class='boton' style='display: none;'>

            <table>

                <tr>

                    <td>

                        <img src='../../iconos/database.png'>

                    </td>

                    <td>

                        <a href='#' onClick='limpiar_todo();'> Cancelar Ajustes a &Oacute;rden de Compra</a>

                    </td>

                </tr>

            </table>

        </div>

        </td>
  
  	   		<div id='anular'>
  	   		
  	   		<td>
			<div id="anular_orden" class="boton" style="display: none;">
			<table>
			<tr>
			<td>
			<img src="../../iconos/database_delete.png">
			</td>
			<td>
			<a href="#" onClick="anular_orden();">Anular &Oacute;rden de Compra</a>
			</td>
			</tr>
			</table>
			</div>
			</td>
  	   	
  	  		 	</div>	
  
 </tr></table>

</center>

</div>



</center>

</body>

</html>







<script>

buscarorden();



autocompletar_medicamentos = new AutoComplete(

      'codigo',

      '../../autocompletar_sql.php',

      function() {

        if($('codigo').value.length<3) return false;



        return {

          method: 'get',

          parameters: 'tipo=buscar_arts&'+$('codigo').serialize()

        }

      }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_articulo);



//*****************************************************************



autocompletar_items = new AutoComplete(

      'item_serv',

      '../../autocompletar_sql.php',

      function() {

        if($('item_serv').value.length<3) return false;



        return {

          method: 'get',

          parameters: 'tipo=buscar_items&cadena='+encodeURIComponent($('item_serv').value)

        }

      }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_item);



//*****************************************************************



autocompletar_proveedores=new AutoComplete(

    'rutproveedor',

    '../../autocompletar_sql.php',

    function() {

      if($('rutproveedor').value.length<3) return false;



      return {

        method: 'get',

        parameters: 'tipo=proveedores&busca_proveedor='+encodeURIComponent($('rutproveedor').value)

      }

    }, 'autocomplete', 350, 200, 250, 1, 2, mostrar_proveedor);





</script>
