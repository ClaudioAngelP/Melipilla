<?php 
    chdir(dirname(__FILE__));
    require_once('simplehtmldom/simple_html_dom.php');
    require_once('../../config.php');
    require_once('../sigh.php');
    
    
    function xml_date($date) {
        if($date=='') return '';
        $partes=split('T', $date);
        $fecha=split('-', $partes[0]);
        $hora=split('-', $partes[1]);
        return $fecha[2].'/'.$fecha[1].'/'.$fecha[0].' '.$hora[0];
    }
    
    function procesar_xml ($login6) {
        $estado_oc='';
        preg_match('/<BuyerOrderNumber>(.+)<\/BuyerOrderNumber>/',$login6,$data);
        file_put_contents('xml/Orden_'.$data[1].'.xml',$login6);
        
        $chk=cargar_registro("SELECT * FROM orden_compra WHERE orden_numero='".trim($data[1])."';");
        
        
        preg_match('/<SummaryNote>(.+)<\/SummaryNote>/',$login6,$data2);
        $estado_oc=pg_escape_string(utf8_decode($data2[1]));
        
        preg_match("/<OrderIssueDate>(.+)<\/OrderIssueDate>/",$login6,$data8);
        $fecha1=substr(xml_date($data8[1]),0,10);
        
        
        preg_match('/<PromiseDate>(.+)<\/PromiseDate>/',$login6,$data3);
        
        if($data3[1]!='')
            $fecha_oc=xml_date($data3[1]);
        else
            $fecha_oc='';
        
        
        
        if($fecha_oc!='' AND !strstr($fecha_oc, '01/01/0001') AND substr($fecha_oc,0,10)!=$fecha1)
            $fecha_oc="'$fecha_oc'";
	else
            $fecha_oc="('$fecha1'::date+'3 days'::interval)";

        
        preg_match('/([0-9]+\-[0-9]+\-[LR]{1}[EP0-9]{1}[0-9]{2})/',$login6,$data4);

        if(isset($data4[1]))
            $licitacion_oc=pg_escape_string($data4[1]);
        else
            $licitacion_oc='';

        if($estado_oc=='OC Guardada') {
            return;
        }
        if($chk) {
            print("UPDATE orden_compra SET orden_estado_portal='$estado_oc', orden_fecha_entrega=$fecha_oc, orden_licitacion='$licitacion_oc' WHERE orden_id=".$chk['orden_id']."\n\n");
            pg_query("UPDATE orden_compra SET orden_estado_portal='$estado_oc', orden_fecha_entrega=$fecha_oc, orden_licitacion='$licitacion_oc' WHERE orden_id=".$chk['orden_id']);
            /*
            *  "OC Removida"
            "OC No Aceptada"
            "OC Esperando AprovaciÃ³n"
            "OC Cancelada"
            "OC Autorizada"
            "OC Aceptada"
            "OC Cancelada por Comprador"
            "OC Enviada a Vendedor"
            "OC Enviada a Proveedor"
            "OC Esperando AprobaciÃ³n"
            "OC en Proceso"
            ""
            "OC Requerida para CancelaciÃ³n"
            * */
            // Si la OC esta "Guardada" debe reactualizarse el detalle...
            /*
            if(trim($chk['orden_estado_portal'])!='OC Guardada' AND trim($chk['orden_estado_portal'])!='OC Esperando Aprovación' AND trim($chk['orden_estado_portal'])!='OC Esperando Aprobación' AND trim($chk['orden_estado_portal'])!='OC Autorizada') {
                return;
            }
            else {
                pg_query("DELETE FROM orden_servicios WHERE orserv_orden_id=".$chk['orden_id']);
		pg_query("DELETE FROM orden_detalle WHERE ordetalle_orden_id=".$chk['orden_id']);
            }
             * 
             */
            pg_query("DELETE FROM orden_servicios WHERE orserv_orden_id=".$chk['orden_id']);
            pg_query("DELETE FROM orden_detalle WHERE ordetalle_orden_id=".$chk['orden_id']);
        }
					
        if(!$xml_obj=new SimpleXMLElement($login6)) {
            print("Error procesando archivo XML ".'xml/Orden_'.$data[1].'.xml'."\n\n");
            return;
        }
    
        if($xml_obj->getName()!='Order') {
            $ordenes_obj = $xml_obj->OrdersList->Order;
	}
        else {
            $ordenes_obj = Array($xml_obj);
	}
			
	foreach($ordenes_obj AS $orden_obj)
	{
            $moneda_oc=pg_escape_string($orden_obj->OrderHeader->OrderCurrency->CurrencyCoded);
            $nombre_oc=pg_escape_string($orden_obj->OrderHeader->OrderReferences->QuoteReference->RefNum);
            $caracteres = array(".", "-");
            $costo = pg_escape_string(str_replace($caracteres,"",$orden_obj->OrderHeader->OrderReferences->ListOfCostCenter->CostCenter->CostCenterNumber));
		
            $nro_orden = trim(pg_escape_string($orden_obj->OrderHeader->OrderNumber->BuyerOrderNumber));
            $fecha_emision = xml_date($orden_obj->OrderHeader->OrderReferences->AccountCode->RefDate);
            //$fecha_emision=$fecha1;
            
            $comentario_ref = pg_escape_string(utf8_decode($orden_obj->OrderHeader->OrderReferences->OtherOrderReferences->ReferenceCoded->ReferenceDescription));
            preg_match_all('/\[([0-9]+)\]/',$comentario_ref,$nros_pedidos);
            $nros=$nros_pedidos[1];
            $html_pedidos='';
					
            for($j=0;$j<count($nros);$j++)
            {
                $nros[$j]=(trim($nros[$j])*1);
		$nro = $nros[$j];
		$html_pedidos.='.'.$nro.'.';
            }
            
            
            preg_match('/\/\/[A-Za-z0-9]{3}\/\//',$comentario_ref,$tipo_orden);
            
            if(count($tipo_orden)>0){
                $otipo_codigo = strtoupper(trim(str_replace('/','',$tipo_orden[0])));
            }
            else {
                $otipo_codigo='';
            }			
            $func=$orden_obj->OrderHeader->OrderParty->BuyerParty;
            $func_contacto = $func->PrimaryContact->ListOfContactNumber;
            $func_mail='';
            $func_fono='';

            foreach($func_contacto->ContactNumber AS $contacto)
            {
                if($contacto->ContactNumberTypeCoded=='EmailAddress')
		{
                    $func_mail=pg_escape_string(utf8_decode($contacto->ContactNumberValue));
                    if($chk['orden_id']*1 != 0) {
                        $tmp_mail = cargar_registro("SELECT orden_mail FROM orden_compra WHERE orden_id=".$chk['orden_id']);
			pg_query("UPDATE orden_compra SET orden_mail='$func_mail' WHERE orden_id=".$chk['orden_id']);
                    }
                }
                else if($contacto->ContactNumberTypeCoded=='TelephoneNumber') {
                    $func_fono=pg_escape_string(utf8_decode($contacto->ContactNumberValue));
                }
            }
						
            $tmpfunc=cargar_registro("SELECT * FROM funcionario WHERE trim(func_email)='$func_mail' AND func_email IS NOT NULL AND NOT func_email='';");
						
            if($tmpfunc)
                $func_id=$tmpfunc['func_id']*1;
            else
                $func_id=7;
		
            $prov=$orden_obj->OrderHeader->OrderParty->SellerParty;
            $prov_rut = $prov->PartyID->Ident;
            $prov_nombre = utf8_decode($prov->NameAddress->Name1);
            $prov_direccion = '';
            $prov_ciudad = utf8_decode(trim($prov->NameAddress->District.', '.$prov->NameAddress->City,', '));
            $prov_contacto = $prov->PrimaryContact->ListOfContactNumber;
            $prov_mail='';
            $prov_fono='';

            foreach($prov_contacto->ContactNumber AS $contacto) {
                if($contacto->ContactNumberTypeCoded=='EmailAddress') {
                    $prov_mail=utf8_decode($contacto->ContactNumberValue);
                }
		else if($contacto->ContactNumberTypeCoded=='TelephoneNumber') {
                    $prov_fono=utf8_decode($contacto->ContactNumberValue);
                }
            }
						
            $tasa_iva=$orden_obj->OrderHeader;
            $iva_fin='';
						
            foreach($tasa_iva->OrderTaxReference AS $iva) {
                $iva_fin=$iva->TaxPercent;
                if($iva_fin==0)
                    $iva_fin=0;
                else
                    $iva_fin=($iva_fin*0.01)+1;
            }
					
            $sprov=strtoupper(trim(str_replace('.','',$prov_rut)));
					
            $prov_conf=cargar_registro("SELECT * FROM proveedor WHERE prov_rut='".$sprov."';");
            
            if(!$prov_conf) {
                print("SIN PROVEEDOR\n");
		pg_query("INSERT INTO proveedor VALUES (DEFAULT, '$sprov', initcap('$prov_nombre'), '$prov_direccion', '$prov_ciudad', '$prov_fono', '', '$prov_mail');");
		$tmp=cargar_registro("SELECT CURRVAL('proveedor_prov_id_seq') AS id;");
		$prov_id=$tmp['id']*1;
            } 
            else {
                print("CON PROVEEDOR\n");
		$prov_id=$prov_conf['prov_id']*1;
                pg_query("UPDATE proveedor SET prov_fono='$prov_fono' WHERE prov_id=$prov_id AND (prov_fono='' OR prov_fono IS NULL)");
		pg_query("UPDATE proveedor SET prov_mail='$prov_mail' WHERE prov_id=$prov_id AND (prov_mail='' OR prov_mail IS NULL)");
            }
												
            if($moneda_oc=='USD') {
                $valor_m=cargar_registro("SELECT dolar_valor FROM dolar_observado WHERE dolar_fecha='$fecha_emision'::timestamp::date");
		if($valor_m){
                    $valor_moneda=$valor_m['dolar_valor']*1;
                    $moneda_oc='CLP';
                }
                else {
                    $fecha_dolar=cargar_registro("
                    SELECT MAX(dolar_fecha) AS fecha_dolar
                    FROM dolar_observado 
                    WHERE dolar_fecha <= '$fecha_emision' 
                    AND dolar_fecha > CAST('$fecha_emision' AS DATE) - CAST('10 days' AS INTERVAL);");
                    $fecha_d=$fecha_dolar['fecha_dolar'];
                    $valor_m=cargar_registro("SELECT dolar_valor FROM dolar_observado WHERE dolar_fecha='$fecha_d'::timestamp::date");
                    $valor_moneda=$valor_m['dolar_valor']*1;
                    $moneda_oc='CLP';
		}
            } 
            else {
                $valor_moneda=1;
            }
            $chk=cargar_registro("SELECT * FROM orden_compra WHERE orden_numero='$nro_orden';");
            if(!$chk) {
                pg_query("INSERT INTO orden_compra VALUES (DEFAULT, '$nro_orden', '' , '$fecha_emision', $prov_id, $func_id, 0, '".$comentario_ref."', $iva_fin, '$estado_oc', $fecha_oc, '$licitacion_oc', '$moneda_oc', $valor_moneda, '$nombre_oc', '$otipo_codigo', '$costo', null, '$func_mail' );");
		$orden_id="CURRVAL('orden_compra_orden_id_seq1')";
            }
            else {
                $tmp_mail = cargar_registro("SELECT orden_mail FROM orden_compra WHERE orden_id=".$chk['orden_id']);
		if($tmp_mail['orden_mail']=="") {
                    pg_query("UPDATE orden_compra SET orden_mail='$func_mail' WHERE orden_id=".$chk['orden_id']);
		}

		pg_query("
		UPDATE orden_compra SET
		orden_fecha='$fecha_emision',
		orden_prov_id=$prov_id,
		orden_func_id=$func_id,
		orden_estado_portal='$estado_oc',
		orden_fecha_entrega=$fecha_oc,
		orden_licitacion='$licitacion_oc',
		orden_observacion='$comentario_ref',
		orden_moneda='$moneda_oc',
		orden_nombre='$nombre_oc',
		otipo_codigo='$otipo_codigo',
		orden_centro_costo='$costo'
		WHERE orden_id=".$chk['orden_id']);
                
                $orden_id=$chk['orden_id'];
            }
            
            foreach($orden_obj->OrderDetail->ListOfItemDetail->ItemDetail AS $art)
            {
                $art_glosa = pg_escape_string(utf8_decode($art->BaseItemDetail->ItemIdentifiers->ItemDescription));
		$cantidad = $art->BaseItemDetail->TotalQuantity->QuantityValue*1;
		$art_subtotal = ((double)$art->PricingDetail->LineItemTotal->MonetaryAmount);
                preg_match('/([0-9]{3}[-]{0,1}[[:alnum:]]{3,}[\/]{0,1}[0-9]{0,})/',$art_glosa,$codigos);
                if(isset($codigos[1])) {
                    if(strstr($codigos[1], '/')) {
                        $_tmp=explode('/',$codigos[1]);
			$codigos[1]=$_tmp[0];
			if($_tmp[1]*1>0)
                            $cantidad=$_tmp[1]*1;
                    }
                    $art_codigo=pg_escape_string($codigos[1]);
                    $art=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$art_codigo';");
                    if($art) $art_id=$art['art_id'];
                }
                else {
                    $art=false;
                }
                if(!$art) {
                    pg_query("INSERT INTO orden_servicios VALUES (DEFAULT, $orden_id, '$art_glosa', $art_subtotal*$valor_moneda, '', $cantidad);");					
                }
                else {
                    pg_query("INSERT INTO orden_detalle VALUES (DEFAULT, $orden_id, $art_id, $cantidad, $art_subtotal*$valor_moneda);");
                }
            }
        }
    }

    $orden_servicio=cargar_registros_obj("
   select distinct orden_numero  from orden_servicios 
    join orden_compra on orserv_orden_id=orden_id
    where orserv_orden_id in (
        select orden_id from 
        (
            SELECT orden_compra.*, 
            (select count(*) from documento where doc_orden_id=orden_id)as cant_orden_id,
            (select count(*) from documento where doc_orden_desc=orden_numero)as cant_orden_num
            FROM orden_compra WHERE orden_moneda='USD' AND orden_fecha::date<CURRENT_DATE
        )as foo
        where (cant_orden_id=0 and cant_orden_num=0)
    )
    and orserv_subtotal=0");
    
    if($orden_servicio){
        pg_query("START TRANSACTION;");
        for($i=0;$i<count($orden_servicio);$i++) {
            $login6=file_get_contents('xml/Orden_'.$orden_servicio[$i]['orden_numero'].'.xml');
            print("PROCESANDO 'xml/Orden_".$orden_servicio[$i]['orden_numero'].".xml'\n\n");
            procesar_xml($login6);
            
        }
        pg_query("COMMIT;");
    }
    
    $orden_detalle=cargar_registros_obj("
    select distinct orden_numero  from orden_detalle
    join orden_compra on ordetalle_orden_id=orden_id
    where ordetalle_orden_id in (
        select orden_id from 
        (
            SELECT orden_compra.*, 
            (select count(*) from documento where doc_orden_id=orden_id)as cant_orden_id,
            (select count(*) from documento where doc_orden_desc=orden_numero)as cant_orden_num
            FROM orden_compra WHERE orden_moneda='USD' AND orden_fecha::date<CURRENT_DATE
        )as foo
        where (cant_orden_id=0 and cant_orden_num=0)
    )
    ");

    if($orden_detalle){
        pg_query("START TRANSACTION;");
        for($i=0;$i<count($orden_detalle);$i++) {
            $login6=file_get_contents('xml/Orden_'.$orden_detalle[$i]['orden_numero'].'.xml');
            print("PROCESANDO 'xml/Orden_".$orden_detalle[$i]['orden_numero'].".xml'\n\n");
            procesar_xml($login6);
            
        }
        pg_query("COMMIT;");
    }
    
    
    /*
    foreach($ordenes_obj AS $orden_obj)
    {
        $nro_orden = pg_escape_string($orden_obj->OrderHeader->OrderNumber->BuyerOrderNumber);
	$xml=$orden_obj->asXML();
	file_put_contents('xml/Orden_'.$nro_orden.'.xml','<?xml version="1.0" ?>'.$xml);
	//echo $xml;
	procesar_xml($xml);
	print($nro_orden.'<br>');
	//print($xml.'<br>');
    }
     * 
     */

    //procesar_xml($_POST['xml']);
?>
