<?php

  require_once('../../conectar_db.php');

  function xml_date($date) {
  
    $partes=split('T', $date);
    $fecha=split('-', $partes[0]);
    $hora=split('-', $partes[1]);
  
    return $fecha[2].'/'.$fecha[1].'/'.$fecha[0].' '.$hora[0];
  
  }

  if(!($contenido=@file_get_contents($_FILES['archivo']['tmp_name']))) {
    die("Error al abrir archivo XML [".$_FILES['archivo']['name']."].");
  }

  if(!$xml_obj=new SimpleXMLElement($contenido)) {
  
    die(json_encode(Array(false,"<strong>Error procesando archivo XML 
    [".$_FILES['archivo']['name']."].</strong>")));
    
    
  }
  
  
  if($xml_obj->getName()!='Order') {
    $ordenes_obj = $xml_obj->OrdersList->Order;
  } else {
    $ordenes_obj = Array($xml_obj);
  }
  
  if(!$ordenes_obj) {
  
?>

<html>
<title>Carga de &Oacute;rdenes de Compra XML (ChileCompra)</title>

<?php cabecera_popup('../..'); ?>

<body class="popup_background fuente_por_defecto">
<br><br><br><br>

<div class='sub-content'>
<center><br>
<img src='../../iconos/error.png' style='width:32px;height:32px;'><br><br>
<b><u>ERROR:</u></b><BR>
El archivo XML descargado no contiene informaci&oacute;n v&aacute;lida.<br><br>
<ul>
<li>Intente descargar la &oacute;rden de compra nuevamente.</li>
<li>Si esto falla deber&aacute; ingresar la &oacute;rden manualmente.</li>
</ul>
</center>
</div>

</body>
</html>

<?php
  
  exit();
  
  }
    
?>

<html>
<title>Carga de &Oacute;rdenes de Compra XML (ChileCompra)</title>

<?php cabecera_popup('../..'); ?>

<script>

guardar_ordenes = function()
{
    if(pedido.length==0)
    {
        confirmar=confirm(('La &oacute;rden de Compra que se esta ingresando al sistema no contempla pedidos asociados, verifique &oacute;rden de compra original' +
                          '; Cualquier consulta comun&iacute;quese con el administrador del sistema: &iquest;Desea ingresar esta Orden de Todas Formas?').unescapeHTML());
        if(confirmar)
        {
            
            for(i=0;i < contador;i++)
            {
                if($('tipo_'+i).value!='P')
                {
                    if($('item_codigo_'+i).value=='' && $('art_id_'+i).value=='')
                    {
                        alert('Para poder ingresar la orden de compra debe asociar el o los item presupuestario faltantes.');
                        return;
                    }
                    else
                    {
                        x=$('id_servicio_'+i).value;
                        ordenes[0].servicios[x].art_id=$('art_id_'+i).value;
                        ordenes[0].servicios[x].item=$('item_codigo_'+i).value;
                    }
                }
            }
            
            params=$('ivaincl').serialize()+'&'+'ordenes='+encodeURIComponent(ordenes.toJSON());
            
            var myAjax=new Ajax.Request('sql2.php',
            {
                method:'post',
                parameters: params,
                onComplete: function(resp)
                {
                    try
                    {
                        msg = resp.responseText.evalJSON(true);
            
                        if(msg[0])
                        {
                            alert('Ingreso realizado satisfactoriamente.');
                            window.close();
                        }
                        else
                        {
                            alert(msg[1].unescapeHTML());
                        }
            
                    }
                    catch(err)
                    {
            
                        alert('ERROR: '+resp.responseText);
            
                    }
                }
            
            });
        }
        else
        {
            
            window.close();
            
        }
    }
    else
    {
        for(i=0;i < pedido.length;i++)
        {
            ordenes[0].pedidos[i]=$('nro_pd_'+i).value
        }
       
        for(i=0;i < contador;i++)
        {
            if($('tipo_'+i).value!='P')
            {
                if($('item_codigo_'+i).value=='' && $('art_id_'+i).value=='')
                {
                    alert('Para poder ingresar la orden de compra debe asociar el o los item presupuestarios faltantes')
                    return;
                }
                else
                {
                    x=$('id_servicio_'+i).value;
                    ordenes[0].servicios[x].art_id=$('art_id_'+i).value;
                    ordenes[0].servicios[x].item=$('item_codigo_'+i).value;
                }
            }
        }
        
        params=$('ivaincl').serialize()+'&ordenes='+encodeURIComponent(ordenes.toJSON());
        
        //alert(params);
        
        var myAjax=new Ajax.Request('sql2.php',
        {
            method:'post',
            parameters: params,
            onComplete: function(resp)
            {
                try
                {
                    msg = resp.responseText.evalJSON(true);
                    if(msg[0])
                    {
                        alert('Ingreso realizado satisfactoriamente.');
                        window.close();
                    }
                    else
                    {
                        alert(msg[1].unescapeHTML());
                    }
                    
                }
                catch(err)
                {
                    alert('ERROR: '+resp.responseText);
                }
            }
        });
    }
}



seleccionar_item = function(fila)
    {
        params= 'item='+encodeURIComponent($('item_codigo_'+fila).value)+
        '&fila='+encodeURIComponent(fila);
        top=Math.round(screen.height/2)-150;
        left=Math.round(screen.width/2)-200;
        new_win =
        window.open('../../abastecimiento/orden_compra/seleccionar_item.php?'+
        params,
        'win_items', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=400, height=300, '+
        'top='+top+', left='+left);
        new_win.focus();
    }

	function abrir_articulo(d, obj) {
		
		try {
		
			var obj_name=$(obj).name.split('_');
			var contador=obj_name[2]*1;
		
			$('art_id_'+contador).value=d[5];
		
			if(d[7]!='') {
				$('item_codigo_'+contador).value=d[6];
				$('span_item_'+contador).innerHTML='<font color="0">'+d[7]+'</font>';
			} else {
				$('item_codigo_'+contador).value='';
				$('span_item_'+contador).innerHTML='N/A';
			}
		
	} catch(err) {
		
		alert(err);
		alert(d[0]);
		
	}
		
	}


    
</script>

<body class="popup_background fuente_por_defecto">


<form id='ordenes' name='ordenes' autocomplete='off' onSubmit='return false;' >



<?php  
    $ordenes=Array();
    $o=0;

    foreach($ordenes_obj AS $orden_obj)
    {
        $nro_orden = $orden_obj->OrderHeader->OrderNumber->BuyerOrderNumber;
        $fecha_emision = xml_date($orden_obj->OrderHeader->OrderIssueDate);
        $comentario_ref = $orden_obj->OrderHeader->OrderReferences->OtherOrderReferences->ReferenceCoded->ReferenceDescription;
        preg_match_all('/\[([0-9]+)\]/mi',$comentario_ref,$nros_pedidos);
        $nros=$nros_pedidos[1];
        $html_pedidos='';
    
        for($i=0;$i<count($nros);$i++)
        {
            $nros[$i]=(trim($nros[$i])*1);
            $nro = $nros[$i];
            $html_pedidos.='<b><input type="text" name="nro_pd_'.$i.'" id="nro_pd_'.$i.'" value='.$nro.' size=4 style="text-align: center;"></b>';
            if($i<(count($nros)-1)) $html_pedidos.=' - ';
        }
    
        $prov=$orden_obj->OrderHeader->OrderParty->SellerParty;
        $prov_rut = $prov->PartyID->Ident;
        $prov_nombre = $prov->NameAddress->Name1;
        $prov_contacto = $prov->PrimaryContact->ListOfContactNumber;
        $prov_mail=''; $prov_fono='';

        foreach($prov_contacto->ContactNumber AS $contacto)
        {
            if($contacto->ContactNumberTypeCoded=='EmailAddress')
            {
                $prov_mail=$contacto->ContactNumberValue;
            }
            else if($contacto->ContactNumberTypeCoded=='TelephoneNumber')
            {
                $prov_fono=$contacto->ContactNumberValue;
            }
        }

        //$orden_ref = xml_get_first('OrderHeaderNote', $orden_obj);
    
        $sprov=trim(str_replace('.','',$prov_rut));
    
        $prov_conf=cargar_registro("
        SELECT * FROM proveedor WHERE prov_rut='".$sprov."'
        ");
    
        if(!$prov_conf)
            $prov_color='red';
        else
            $prov_color='yellowgreen';
      
        $ordenes[$o]->numero_orden=trim((string)$nro_orden);
        $ordenes[$o]->fecha_emision=(string)$fecha_emision;
        $ordenes[$o]->pedidos=$nros_pedidos[1];
    
        $ordenes[$o]->proveedor->rut=$sprov;
        $ordenes[$o]->proveedor->nombre=(string)($prov_nombre);
        $ordenes[$o]->proveedor->mail=(string)($prov_mail);
        $ordenes[$o]->proveedor->telefono=(string)($prov_fono);
    
        $ordenes[$o]->articulos=Array();
        $ordenes[$o]->servicios=Array();
    
		if(strstr($nro_orden, 'CM')) {
			$cmarco='<i>(Convenio Marco)</i>';
			$cm=true;
		} else {
			$cmarco='';
			$cm=false;
		}
    
        print("
        <div class='sub-content' style='text-align:center; font-size:18px;'>
            &Oacute;rden de Compra <b>".$nro_orden."</b> $cmarco
        </div>
        <table>
        <tr><td style='text-align:right;width:150px;'>
            Fecha de Emisi&oacute;n:</td>
        <td style='font-style: italic;'>".$fecha_emision."</td></tr>
        <tr><td style='text-align: right;' valign='top'>Comentario de Ref.:</td>
        <td style='font-style: italic;'>
            ".htmlentities(utf8_decode($comentario_ref))."
        </td></tr>
        <tr><td style='text-align: right;' valign='top'>Pedidos Asociados:</td>
        <td style='font-style: italic;'>
            ".$html_pedidos."
        </td></tr>
        <tr><td style='text-align: right;'>Proveedor:</td>
        <td style='color:$prov_color'>
            ".$prov_rut."
            ".htmlentities($prov_nombre)."</td>
        </table>
        <div class='sub-content' style='text-align:left; font-size:15px;'>
            <b>Detalle (<input type='checkbox' id='ivaincl' name='ivaincl' unchecked onclick='' value=1> Valores Exentos de I.V.A.)<b>
        </div>
        ");
    
        $u=0;
        $total=0;
        $contador=0;
        
        $script='';
        
        foreach($orden_obj->OrderDetail->ListOfItemDetail->ItemDetail AS $art)
        {
            
            $art_glosa = $art->BaseItemDetail->ItemIdentifiers->ItemDescription;
            
            $lart=Array();
            preg_match('/\[([^\]|]*)\|([0-9\,\.]*)](.*)/i', $art_glosa, $lart);
            $art_subtotal = (double)$art->PricingDetail->LineItemTotal->MonetaryAmount;
            ($u%2==0) ? $clase='tabla_fila2' : $clase='tabla_fila';
            
            if(count($lart)==4)
            {
                if($contador==0)
                {
                    print("
                        <div class='sub-content' style='text-align:center; font-size:18px;'>
                            <table border='1' style='width:100%;'>
                                <tr class='tabla_header'>
                                    <td colspan=7>Detalle &Oacute;rden de Compra <b>".$nro_orden."</b></td>
                                </tr>
                                <tr class='tabla_header'>
                                    <td colspan=3>Tipo</td>
                                    <td>C&oacute;digo Int.</td>
                                    <td style='width:50%;'>Glosa</td>
                                    <td>Cantidad</td>
                                    <td>Subtotal</td>
                                </tr>
                        </div>");
                }
                
                print("
                        <tr class='.$clase.' style='font-size:12px;'>
                        <td colspan=3 style='text-align:center;'>
                            <input type='hidden' id='tipo_$contador' name='tipo_$contador' value=P>
                            P
                        </td>
                        <td style='text-align:right'>".htmlentities(ucwords($lart[1]))."</td>
                        <td>".ucwords($lart[3])."</td>
                        
                        <td style='text-align:right'>$lart[2]</td>
                        <td style='text-align:right;'>
                            $".number_format($art_subtotal*1, 1, ',', '.').".-
                        </td>
                    </tr>
                    ");
                    
                $cantidad = $art->BaseItemDetail->TotalQuantity->QuantityValue;
                $num=count($ordenes[$o]->articulos);
                $ordenes[$o]->articulos[$num]->codigo=(string)$lart[1];
                //$ordenes[$o]->articulos[$num]->cantidad=(string)$lart[2];
                $ordenes[$o]->articulos[$num]->cantidad=(string)$cantidad;
                $ordenes[$o]->articulos[$num]->subtotal=(double)$art_subtotal;
                
            }
            else
            {
                
                $cantidad = $art->BaseItemDetail->TotalQuantity->QuantityValue;
                if($contador==0)
                {
                    print("<div class='sub-content' style='text-align:center; font-size:18px;'>
                    <table border='1' style='width:100%;'>
                        <tr class='tabla_header'>
                            <td colspan=7>Detalle &Oacute;rden de Compra <b>".$nro_orden."</b></td>
                        </tr>
                        <tr class='tabla_header'>
                            <td colspan=2>Tipo</td>
                            <td>C&oacute;digo Int.</td>
                            <td style='width:50%;'>Glosa</td>
                            <td style='width:30%;'>Item</td>
                            <td>Cantidad</td>
                            <td>Subtotal</td>
                        </tr>
                        </div>");
                }
                
                
                $num = count($ordenes[$o]->servicios);
                
                $chk=cargar_registro("SELECT * FROM articulo_nombres JOIN articulo USING (art_id) LEFT JOIN item_presupuestario ON art_item=item_codigo WHERE artn_nombre='".pg_escape_string(trim(utf8_decode($art_glosa)))."'", true);
                
                if(!$chk) {
					$art_id=''; $art_codigo=''; $item_codigo=''; $item_nombre='N/A';
				} else {
					$art_id=$chk['art_id'];
					$art_codigo=$chk['art_codigo'];
					if($chk['art_item']!='') {
						$item_codigo=$chk['art_item'];
						$item_nombre="<font color='0'>".$chk['item_glosa']."</font>";
					} else {
						$item_codigo='';
						$item_nombre="N/A";	
					}
				}
                
                print("
                <tr class='.$clase.' style='font-size:12px;'>
                <td colspan=2 style='text-align:center;'>
                    <input type='hidden' id='item_codigo_$contador' name='item_codigo_$contador' value='$item_codigo' />
                    <input type='hidden' id='tipo_$contador' name='tipo_$contador' value='S' />
                    <input type='hidden' id='id_servicio_$contador' name='id_servicio_$contador' value='$num' />
                    S
                </td>
                <td>
                    <input type='hidden' id='art_id_$contador' name='art_id_$contador' value='$art_id' />
                    <input type='text' id='codigo_art_$contador' name='codigo_art_$contador' size=11 style='font-size:10px;' value='$art_codigo' />
                </td>
                <td>".htmlentities(ucwords(utf8_decode($art_glosa)))."</td>
                <td>
                    <center>
                        <span id='span_item_$contador' name='span_item_$contador' style='cursor:pointer; color: red;'onClick='seleccionar_item($contador);'>
                            $item_nombre
                        </span>
                    </center>
                </td>
                <td style='text-align:center'>".$cantidad."</td>
                <td style='text-align:right;'>
                    $".number_format($art_subtotal*1, 1, ',', '.').".-
                </td>
                </tr>
                ");
                
                $cantidad = $art->BaseItemDetail->TotalQuantity->QuantityValue;
                $num = count($ordenes[$o]->servicios);
                $ordenes[$o]->servicios[$num]->glosa=(string)$art_glosa;
                $ordenes[$o]->servicios[$num]->cantidad=(string)$cantidad;
                $ordenes[$o]->servicios[$num]->subtotal=(double)$art_subtotal;
                $ordenes[$o]->servicios[$num]->item='';
                
                $script.="
                
				  autocompletar_medicamentos_$contador = new AutoComplete(
				  'codigo_art_$contador', 
				  '../../autocompletar_sql.php',
				  function() {
					if($('codigo_art_$contador').value.length<3) return false;
				  
					return {
					  method: 'get',
					  parameters: 'tipo=buscar_arts&codigo='+encodeURIComponent($('codigo_art_$contador').value)
					}
					
				  }, 'autocomplete', 550, 200, 250, 1, 3, abrir_articulo);

                
                ";

            }
            $total+=$art_subtotal*1;
            $u++;
            $contador=$contador+1;
        }
        print('
        <tr class="tabla_header" style="font-size:12px;font-weight:bold;">
        <td colspan=6 style="text-align:right;">Total ($):</td>
        <td style="text-align:right;">
		$'.number_format($total,1,',','.').'.-
        </td>
        </tr>
        </table>
        <hr />
        ');
        $o++;
    }
?>

    <center>
		<div class='boton'>
		<table><tr><td>
		<img src='../../iconos/application_get.png'>
		</td><td>
		<a href='#' onClick='guardar_ordenes();'>
		Cargar &Oacute;rden(es) de Compra...</a>
		</td></tr></table>
        
		</div>
		</center>
		
</form>

</body>

<script>
    var ordenes = <?php echo json_encode($ordenes); ?>;
    var pedido = <?php echo json_encode($nros); ?>;
    var contador=<?php echo Json_encode($contador); ?>
    
    <?php echo $script; ?>
    
</script>

</html>
