<?php 
    chdir(dirname(__FILE__));
    require_once('simplehtmldom/simple_html_dom.php');
    
    function xml_date($date)
    {
        $partes=split('T', $date);
        $fecha=split('-', $partes[0]);
        $hora=split('-', $partes[1]);
        return $fecha[2].'/'.$fecha[1].'/'.$fecha[0].' '.$hora[0];
    }

    $usuario='carorojase';
    $clave='ca1234';

    if(isset($_GET['orden_numero']))
    {
        $fecha1=date('d/m/Y'); $fecha2=date('d/m/Y'); $oc=$_GET['orden_numero'];
	$act='ToolkitScriptManager1|btnSearchByCode';
	$btn='&btnSearchByCode=Ver';
    }
    else
    {
        require_once('../../config.php');
        require_once('../sigh.php');
        $fecha1=date('d/m/Y');
        $fecha2=date('d/m/Y');
        $oc=''; $act='ToolkitScriptManager1|btnSearch';
        $btn='&btnSearch=Buscar';
    }
	
    $ch = curl_init();
	
    function mercadopublico_login()
    {
        global $ch, $action, $code, $usuario, $clave, $rut, $fecha1, $fecha2, $oc, $act, $btn;
        curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)');
        curl_setopt( $ch, CURLOPT_VERBOSE, true);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
	curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
	curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
	curl_setopt( $ch, CURLOPT_URL, 'https://www.mercadopublico.cl/Portal/login.aspx' );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
	$data='';
        print("PASO 1\n\n");
        $login1 = curl_exec ($ch);
	file_put_contents('data1.log',$login1);
	$tmp = str_get_html($login1);
        $form = $tmp->find('form[id=frmLogin]');
	$event_target = $tmp->find('input[id=__EVENTTARGET]');
	$event_argument = $tmp->find('input[id=__EVENTARGUMENT]');
	$view_state = $tmp->find('input[id=__VIEWSTATE]');
        $_event_target = urlencode($event_target[0]->attr['value']);
	$_event_argument = urlencode($event_argument[0]->attr['value']);
	$_view_state = urlencode($view_state[0]->attr['value']);
	$post_data='__EVENTTARGET='.$_event_target.'&__EVENTARGUMENT'.$_event_argument.'=&__VIEWSTATE='.$_view_state.'&txtUser='.$usuario.'&txtPassword='.$clave.'&btnLoginNuevo=Ingresar&lbl_rutaBusqueda=&lbl_model=&lbl_busqueda=&lbl_option=&validacion=&hdnCambioContrasena=&hdnUsuario=&busquedaFast=0';
	
        curl_setopt( $ch, CURLOPT_URL, 'https://www.mercadopublico.cl/Portal/login.aspx' );
	curl_setopt( $ch, CURLOPT_POST, 1);
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_data);
        
	print("\n\nPASO 2\n\n");
        $login2 = curl_exec ($ch);
	file_put_contents('data2.log',$login2);
	$tmp = str_get_html($login2);
        curl_setopt( $ch, CURLOPT_URL, 'https://www.mercadopublico.cl/Portal/Modules/Desktop/Desktop.aspx' );
	curl_setopt( $ch, CURLOPT_POST, 1);
	curl_setopt( $ch, CURLOPT_POSTFIELDS,'');
	print("\n\nPASO 3\n\n");
        $login3 = curl_exec ($ch);
	file_put_contents('data3.log',$login3);
	$tmp = str_get_html($login3);
        curl_setopt( $ch, CURLOPT_URL, 'http://www.mercadopublico.cl/PurchaseOrder/Modules/PO/SearchPurchaseOrder.aspx' );
	curl_setopt( $ch, CURLOPT_POST, 1);
	curl_setopt( $ch, CURLOPT_POSTFIELDS,'');
	print("\n\nPASO 4\n\n");
        $login4 = curl_exec ($ch);
	file_put_contents('data4.log',$login4);
	$tmp = str_get_html($login4);
        
        $toolscript1 = $tmp->find('input[id=ToolkitScriptManager1]');
	$toolscript2 = $tmp->find('input[id=ToolkitScriptManager1_HiddenField]');
	$event_target = $tmp->find('input[id=__EVENTTARGET]');
	$event_argument = $tmp->find('input[id=__EVENTARGUMENT]');
	$view_state = $tmp->find('input[id=__VIEWSTATE]');
        $_tsm1 = urlencode($toolscript1[0]->attr['value']);
	$_tsm2 = urlencode($toolscript2[0]->attr['value']);
	$_event_target = urlencode($event_target[0]->attr['value']);
	$_event_argument = urlencode($event_argument[0]->attr['value']);
	$_view_state = urlencode($view_state[0]->attr['value']);
	$post_data='ToolkitScriptManager1='.urlencode($act).'&ToolkitScriptManager1_HiddenField='.$_tsm2.'&__EVENTTARGET='.$_event_target.'&__EVENTARGUMENT'.$_event_argument.'=&__VIEWSTATE='.$_view_state.'&txtUser='.$usuario.'&txtPassword='.$clave.'&__SCROLLPOSITIONX=0&__SCROLLPOSITIONY=0&txtPOCode='.urlencode($oc).'&txtName=&cboOrderBy=porDate&txtProv=&calFrom='.urlencode($fecha1).'&cboState=-1&calTo='.urlencode($fecha2).'&cboBuyUnit=&hdnShowQuestion=&hdnOcRechazada=&__ASYNCPOST=true'.$btn;
	//print($post_data);
        curl_setopt( $ch, CURLOPT_URL, 'http://www.mercadopublico.cl/PurchaseOrder/Modules/PO/SearchPurchaseOrder.aspx' );
	curl_setopt( $ch, CURLOPT_POST, 1);
	curl_setopt( $ch, CURLOPT_POSTFIELDS,$post_data);
	print("\n\nPASO 5\n\n");

	$login5 = curl_exec ($ch);
        
	file_put_contents('data5.log',$login5);
	$tmp = str_get_html($login5);
        
		
	$apags=$tmp->find('a[class=cssFwkLinkButtonOrden]');
        

	$nregs=$tmp->find('span[id=_PagerBlock__LblRecordCount]'); // SE OBTIENE NUMERO DE REGISTROS PARA CALCULAR HOJAS
        
        
        if(count($nregs)>1)
        {
            $nregs=$nregs[0]->find('text');
            $nregs=$nregs[0];
        }

	$nregstmp=explode(' ', trim($nregs));
        
	$nnregs=$nregstmp[0]*1;
		
	//$npags=sizeof($apags)+2; // ESTE NUMERO LIMITA EN 10 PAGINAS POR LO TANTO NO SE PUEDE USAR...
        $npags=ceil($nnregs/10); // MERCADOPUBLICO MUESTRA 10 REGISTROS POR HOJA...
	
	for($pag=1;$pag<=$npags;$pag++)
        {
            
            if($pag>1)
            {
                if($pag>11) $realpag=$pag-10; else $realpag=$pag;
		if($realpag>11) $realpag=$realpag-10;
                if($realpag>11) $realpag=$realpag-10;
                
                $_tsm1=urlencode('UpdatePanel|_PagerBlock$_PagerBlock_pageNumber'.$realpag);
		$_event_target=urlencode('_PagerBlock$_PagerBlock_pageNumber'.$realpag);
		preg_match('/\|__VIEWSTATE\|([a-zA-Z0-9\/=\+]+)\|/',$login5, $_tmp);
		print_r($_tmp);
		$_view_state=urlencode($_tmp[1]);

		$post_data='ToolkitScriptManager1='.$_tsm1.'&ToolkitScriptManager1_HiddenField='.$_tsm2.'&__EVENTTARGET='.$_event_target.'&__EVENTARGUMENT'.$_event_argument.'=&__VIEWSTATE='.$_view_state.'&__SCROLLPOSITIONX=0&__SCROLLPOSITIONY=0&txtPOCode=&txtName=&cboOrderBy=porDate&txtProv=&calFrom='.urlencode($fecha1).'&cboState=-1&calTo='.urlencode($fecha2).'&cboBuyUnit=&hdnShowQuestion=&hdnOcRechazada=&__ASYNCPOST=true';

		print("\n\n".$post_data."\n\n");
				
		curl_setopt( $ch, CURLOPT_URL, 'http://www.mercadopublico.cl/PurchaseOrder/Modules/PO/SearchPurchaseOrder.aspx' );
				
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS,$post_data);

		$login5 = curl_exec ($ch);
		file_put_contents('data5.'.$pag.'.log',$login5);
		$tmp = str_get_html($login5);
            }

            $ocs = $tmp->find('input[type=image]');
            
            for($i=0;$i<sizeof($ocs);$i++)
            {
                $id=$ocs[$i]->attr['id'];
		if(strstr($id, 'imgXMLOc'))
                {
                    $click=$ocs[$i]->attr['onclick'];
                    $tmp=explode('&#39;', $click);
                    $url=$tmp[1];
                    curl_setopt( $ch, CURLOPT_URL, 'http://www.mercadopublico.cl/PurchaseOrder/Modules/PO/'.$url );
                    curl_setopt( $ch, CURLOPT_POST, 0);
                    curl_setopt( $ch, CURLOPT_POSTFIELDS,'');
                    curl_setopt( $ch, CURLOPT_HEADER,0);
                    $login6 = curl_exec($ch);
                    $estado_oc='';
                    preg_match('/<BuyerOrderNumber>(.+)<\/BuyerOrderNumber>/',$login6,$data);
                    file_put_contents('xml/Orden_'.$data[1].'.xml',$login6);
                    $chk=cargar_registro("SELECT * FROM orden_compra WHERE orden_numero='".$data[1]."';");
					
                    preg_match('/<SummaryNote>(.+)<\/SummaryNote>/',$login6,$data2);
					
                    $estado_oc=pg_escape_string(utf8_decode($data2[1]));
					
                    preg_match('/<PromiseDate>(.+)<\/PromiseDate>/',$login6,$data3);
					
                    if($data3[1]!='')
                        $fecha_oc=xml_date($data3[1]);
                    else
                        $fecha_oc='';

                    if($fecha_oc!='' AND !strstr($fecha_oc, '01/01/0001') AND substr($fecha_oc,0,10)!=$fecha1)
                        $fecha_oc="'$fecha_oc'";
                    else
                        $fecha_oc="('$fecha1'::date+'3 days'::interval)";
                    
                    preg_match('/<Currency><CurrencyCoded>([A-Z]+)<\/CurrencyCoded>/',$login6,$data5);
                    $moneda_oc=pg_escape_string(substr($data5[1],0,20));
                                     
                    preg_match('/<QuoteReference><RefNum xmlns="rrn:org.xcbl:schemas\/xcbl\/v4_0\/core\/core.xsd">(.+)<\/RefNum><\/QuoteReference>/', $login6, $data5);
                    
                    $nombre_oc = pg_escape_string(utf8_decode($data5[1]));
                    
                    preg_match('/<ListOfCostCenter><CostCenter xmlns="rrn:org.xcbl:schemas\/xcbl\/v4_0\/core\/core.xsd"><CostCenterNumber>(.+)<\/CostCenterNumber>/', $login6, $data6);
                    
                    $caracteres = array(".", "-");
                    if(isset($data6[1]))
                        $costo = pg_escape_string(utf8_decode(str_replace($caracteres,"",$data6[1])));
                    else
                        $costo="";
                    
                    preg_match('/([0-9]+\-[0-9]+\-[LR]{1}[EP0-9]{1}[0-9]{2})/',$login6,$data4);
                    
                    if(isset($data4[1]))
                        $licitacion_oc=pg_escape_string($data4[1]);
                    else
                        $licitacion_oc='';
                    
                    if($estado_oc=='OC Guardada')
                        continue;

                    if($chk)
                    {
                        pg_query("UPDATE orden_compra SET orden_estado_portal='$estado_oc', orden_fecha_entrega=$fecha_oc, orden_licitacion='$licitacion_oc', orden_moneda='$moneda_oc', orden_nombre='$nombre_oc', orden_centro_costo='$costo' WHERE orden_id=".$chk['orden_id']);
			// Si la OC esta "Guardada" debe reactualizarse el detalle...
			if(trim($chk['orden_estado_portal'])!='OC Guardada')
                        {
                            continue;
			}
                        else
                        {
                            pg_query("DELETE FROM orden_servicios WHERE orserv_orden_id=".$chk['orden_id']);
                            pg_query("DELETE FROM orden_detalle WHERE ordetalle_orden_id=".$chk['orden_id']);
			}
                    }
					
                    if(!$xml_obj=new SimpleXMLElement($login6))
                    {
                        print("Error procesando archivo XML ".'xml/Orden_'.$data[1].'.xml'."\n\n");
			continue;
                    }
                    if($xml_obj->getName()!='Order')
                    {
                        $ordenes_obj = $xml_obj->OrdersList->Order;
                    }
                    else
                    {
                        $ordenes_obj = Array($xml_obj);
                    }
                    foreach($ordenes_obj AS $orden_obj)
                    {
                        $nro_orden = pg_escape_string($orden_obj->OrderHeader->OrderNumber->BuyerOrderNumber);
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
			$otipo_codigo = strtoupper(trim(str_replace('/','',$tipo_orden[0])));
					
			$func=$orden_obj->OrderHeader->OrderParty->BuyerParty;
			$func_contacto = $func->PrimaryContact->ListOfContactNumber;
			$func_mail=''; $func_fono='';
                        foreach($func_contacto->ContactNumber AS $contacto)
			{
                            if($contacto->ContactNumberTypeCoded=='EmailAddress')
                            {
                                $func_mail=pg_escape_string(utf8_decode($contacto->ContactNumberValue));
				if($chk['orden_id']*1 != 0)
                                {
                                    $tmp_mail = cargar_registro("SELECT orden_mail FROM orden_compra WHERE orden_id=".$chk['orden_id']);
                                    pg_query("UPDATE orden_compra SET orden_mail='$func_mail' WHERE orden_id=".$chk['orden_id']);
				}
                            }
                            else if($contacto->ContactNumberTypeCoded=='TelephoneNumber')
                            {
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
			$prov_mail=''; $prov_fono='';

			foreach($prov_contacto->ContactNumber AS $contacto)
			{
                            if($contacto->ContactNumberTypeCoded=='EmailAddress')
                            {
                                $prov_mail=utf8_decode($contacto->ContactNumberValue);
                            }
                            else if($contacto->ContactNumberTypeCoded=='TelephoneNumber')
                            {
                                $prov_fono=utf8_decode($contacto->ContactNumberValue);
                            }
			}
						
			$tasa_iva=$orden_obj->OrderHeader;
			$iva_fin='';
						
			foreach($tasa_iva->OrderTaxReference AS $iva)
			{
                            $iva_fin=$iva->TaxPercent;
                            if($iva_fin==0)
                                $iva_fin=0;
                            else
                                $iva_fin=($iva_fin*0.01)+1;
                        }
                        //$orden_ref = xml_get_first('OrderHeaderNote', $orden_obj);
			$sprov=strtoupper(trim(str_replace('.','',$prov_rut)));
			$prov_conf=cargar_registro("SELECT * FROM proveedor WHERE prov_rut='".$sprov."'");
			if(!$prov_conf)
                        {
                            print("SIN PROVEEDOR\n");
                            pg_query("INSERT INTO proveedor VALUES (DEFAULT, '$sprov', initcap('$prov_nombre'), '$prov_direccion', '$prov_ciudad', '$prov_fono', '', '$prov_mail');");
                            $tmp=cargar_registro("SELECT CURRVAL('proveedor_prov_id_seq') AS id;");
                            $prov_id=$tmp['id']*1;
			}
                        else
                        {
                            print("CON PROVEEDOR\n");
                            $prov_id=$prov_conf['prov_id']*1;
                            pg_query("UPDATE proveedor SET prov_fono='$prov_fono' WHERE prov_id=$prov_id AND (prov_fono='' OR prov_fono IS NULL)");
                            pg_query("UPDATE proveedor SET prov_mail='$prov_mail' WHERE prov_id=$prov_id AND (prov_mail='' OR prov_mail IS NULL)");
			}
			
                        if($moneda_oc=='USD')
                        {
                            $valor_m=cargar_registro("SELECT dolar_valor FROM dolar_observado WHERE dolar_fecha='$fecha_emision'::timestamp::date");
                            if($valor_m)
                            {
                                $valor_moneda=$valor_m['dolar_valor']*1;
				$moneda_oc='CLP';
                            }
                            else
                            {
                                $fecha_dolar=cargar_registro("SELECT MAX(dolar_fecha) AS fecha_dolar
                                FROM dolar_observado 
                                WHERE dolar_fecha <= '$fecha_emision' 
                                AND dolar_fecha > CAST('$fecha_emision' AS DATE) - CAST('10 days' AS INTERVAL);");
                                $fecha_d=$fecha_dolar['fecha_dolar'];
				$valor_m=cargar_registro("SELECT dolar_valor FROM dolar_observado WHERE dolar_fecha='$fecha_d'::timestamp::date");
				$valor_moneda=$valor_m['dolar_valor']*1;
				$moneda_oc='CLP';
                            }
			}
                        else
                        {
                            $valor_moneda=1;
			}
						
			$chk=cargar_registro("SELECT * FROM orden_compra WHERE orden_numero='$nro_orden';");
						
			if(!$chk)
                        {
                            pg_query("INSERT INTO orden_compra VALUES (DEFAULT, '$nro_orden', '' , '$fecha_emision', $prov_id, $func_id, 0, '".$comentario_ref."', $iva_fin, '$estado_oc', $fecha_oc, '$licitacion_oc', '$moneda_oc', $valor_moneda, '$nombre_oc', '$otipo_codigo', '$costo', null, '$func_mail' );");
                            $orden_id="CURRVAL('orden_compra_orden_id_seq1')";
			}
                        else
                        {
                            $tmp_mail = cargar_registro("SELECT orden_mail FROM orden_compra WHERE orden_id=".$chk['orden_id']);
                            if($tmp_mail['orden_mail']=="")
                            {
                                pg_query("UPDATE orden_compra SET orden_mail='$func_mail' WHERE orden_id=".$chk['orden_id']);
                            }
                            pg_query("UPDATE orden_compra SET
                            orden_fecha='$fecha_emision',
                            orden_prov_id=$prov_id,
                            orden_func_id=$func_id,
                            orden_estado_portal='$estado_oc',
                            orden_fecha_entrega='$fecha_oc',
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
                            preg_match('/([0-9]{3}[0-9P]{1}[0-9]{3,}[\/]{0,1}[0-9]{0,})/',$art_glosa,$codigos);
                            if(isset($codigos[1]))
                            {
                                if(strstr($codigos[1], '/'))
                                {
                                    $_tmp=explode('/',$codigos[1]);
                                    $codigos[1]=$_tmp[0];
                                    if($_tmp[1]*1>0)
                                        $cantidad=$_tmp[1]*1;
				}
                                $art_codigo=pg_escape_string($codigos[1]);
                                $art=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$art_codigo';");
                                if($art)
                                    $art_id=$art['art_id'];
                            }
                            else
                            {
                                $art=false;
                            }
                            if(!$art)
                            {
                                $chk_servicios = cargar_registro("SELECT * FROM orden_servicios WHERE orserv_orden_id=$orden_id AND orserv_glosa='$art_glosa' AND orserv_cant=$cantidad");
                                if(!$chk_servicios)
                                    pg_query("INSERT INTO orden_servicios VALUES (DEFAULT, $orden_id, '$art_glosa', $art_subtotal*$valor_moneda, '', $cantidad);");
                            }
                            else
                            {
                                $chk_detalle = cargar_registro("SELECT * FROM orden_detalle WHERE ordetalle_orden_id=$orden_id AND ordetalle_art_id=$art_id AND ordetalle_cant=$cantidad");
				if(!$chk_detalle)
                                    pg_query("INSERT INTO orden_detalle VALUES (DEFAULT, $orden_id, $art_id, $cantidad, $art_subtotal*$valor_moneda);");
                            }
                        }
                    }
                }
            }
        }
    }
	
    /*
    function regcivil_logout()
    {
        global $ch, $action, $code;
        $url='http://monitoweb.srcei.cl'.$action;
        $post_data='_message=&_initial=&_action='.urlencode('4:SALIR   ');
        curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_POST, 1);
	curl_setopt( $ch, CURLOPT_POSTFIELDS,$post_data);
        $login5 = curl_exec ($ch);
	file_put_contents('data.log',$login5,FILE_APPEND);
    }
    */
    mercadopublico_login();
?>