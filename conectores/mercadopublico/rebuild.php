<?php 

        chdir(dirname(__FILE__));

	require_once('../../config.php');
	require_once('../sigh.php');
	require_once('simplehtmldom/simple_html_dom.php');

  function xml_date($date) {
  
    $partes=split('T', $date);
    $fecha=split('-', $partes[0]);
    $hora=split('-', $partes[1]);
  
    return $fecha[2].'/'.$fecha[1].'/'.$fecha[0].' '.$hora[0];
  
  }
    
  $ocs=scandir('xml');
  
  for($xx=2;$xx<sizeof($ocs);$xx++) {
	  
					$login6=file_get_contents('xml/'.$ocs[$xx]);

					print("PROCESANDO 'xml/".$ocs[$xx]."'\n\n");

					$estado_oc='';
					
					preg_match('/<BuyerOrderNumber>(.+)<\/BuyerOrderNumber>/',$login6,$data);
					
					$chk=cargar_registro("SELECT * FROM orden_compra WHERE orden_numero='".$data[1]."';");
					
					preg_match('/<SummaryNote>(.+)<\/SummaryNote>/',$login6,$data2);
					
					$estado_oc=pg_escape_string(utf8_decode($data2[1]));

					 preg_match("/<OrderIssueDate>(.+)<\/OrderIssueDate>/",$login6,$data8);

                                        $fecha1=substr(xml_date($data8[1]),0,10);
					
					preg_match('/<PromiseDate>(.+)<\/PromiseDate>/',$login6,$data3);
					
					$fecha_oc=xml_date($data3[1]);

					if($fecha_oc!='' AND !strstr($fecha_oc, '01/01/0001') AND substr($fecha_oc,0,10)!=$fecha1) 
						$fecha_oc="'$fecha_oc'";
					else
						$fecha_oc="('$fecha1'::date+'3 days'::interval)";

					preg_match('/<Currency><CurrencyCoded>([A-Z]+)<\/CurrencyCoded>/',$login6,$data5);

                                        $moneda_oc=pg_escape_string(substr($data5[1],0,20));

					preg_match('/([0-9]+\-[0-9]+\-[LR]{1}[EP0-9]{1}[0-9]{2})/',$login6,$data4);

					if(isset($data4[1]))
                                                $licitacion_oc=pg_escape_string($data4[1]);
                                        else
                                                $licitacion_oc='';

					if($estado_oc=='OC Guardada') continue;

					
					if($chk) {
						pg_query("UPDATE orden_compra SET orden_estado_portal='$estado_oc', orden_fecha_entrega=$fecha_oc, orden_licitacion='$licitacion_oc', orden_moneda='$moneda_oc' WHERE orden_id=".$chk['orden_id']);
						
						// Si la OC esta "Guardada" debe reactualizarse el detalle...
						if(trim($chk['orden_estado_portal'])!='OC Guardada') {
							continue;
						} else {
							pg_query("DELETE FROM orden_servicios WHERE orserv_orden_id=".$chk['orden_id']);
							pg_query("DELETE FROM orden_detalle WHERE ordetalle_orden_id=".$chk['orden_id']);
						}
					}
					
					if(!$xml_obj=new SimpleXMLElement($login6)) {
  
						print("Error procesando archivo XML ".'xml/Orden_'.$data[1].'.xml'."\n\n");
						continue;
    
					}
    
					if($xml_obj->getName()!='Order') {
						$ordenes_obj = $xml_obj->OrdersList->Order;
					} else {
						$ordenes_obj = Array($xml_obj);
					}
					
					foreach($ordenes_obj AS $orden_obj)
					{
						$nro_orden = pg_escape_string($orden_obj->OrderHeader->OrderNumber->BuyerOrderNumber);
						//$fecha_emision = xml_date($orden_obj->OrderHeader->OrderIssueDate);
						$fecha_emision=$fecha1;
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
						
						$func=$orden_obj->OrderHeader->OrderParty->BuyerParty;
						$func_contacto = $func->PrimaryContact->ListOfContactNumber;
						$func_mail=''; $func_fono='';

						foreach($func_contacto->ContactNumber AS $contacto)
						{
							if($contacto->ContactNumberTypeCoded=='EmailAddress')
							{
								$func_mail=pg_escape_string(utf8_decode($contacto->ContactNumberValue));
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

						//$orden_ref = xml_get_first('OrderHeaderNote', $orden_obj);
					
						$sprov=strtoupper(trim(str_replace('.','',$prov_rut)));
					
						$prov_conf=cargar_registro("
						SELECT * FROM proveedor WHERE prov_rut='".$sprov."'
						");
					
						if(!$prov_conf) {
							print("SIN PROVEEDOR\n");
							pg_query("INSERT INTO proveedor VALUES (DEFAULT, '$sprov', initcap('$prov_nombre'), '$prov_direccion', '$prov_ciudad', '$prov_fono', '', '$prov_mail');");
							$tmp=cargar_registro("SELECT CURRVAL('proveedor_prov_id_seq') AS id;");
							$prov_id=$tmp['id']*1;
						} else {
							print("CON PROVEEDOR\n");
							
							$prov_id=$prov_conf['prov_id']*1;

							pg_query("UPDATE proveedor SET prov_fono='$prov_fono' WHERE prov_id=$prov_id AND (prov_fono='' OR prov_fono IS NULL)");
							pg_query("UPDATE proveedor SET prov_mail='$prov_mail' WHERE prov_id=$prov_id AND (prov_mail='' OR prov_mail IS NULL)");
						}
						
						$chk=cargar_registro("SELECT * FROM orden_compra WHERE orden_numero='$nro_orden';");
						
						if(!$chk) {
							pg_query("INSERT INTO orden_compra VALUES (DEFAULT, '$nro_orden', '' , '$fecha_emision', $prov_id, $func_id, 0, '".$comentario_ref."', 1.19, '$estado_oc', $fecha_oc, '$licitacion_oc', '$moneda_oc' );");
							$orden_id="CURRVAL('orden_compra_orden_id_seq1')";
						} else {

							pg_query("UPDATE orden_compra SET
                                                        orden_fecha='$fecha_emision',
                                                        orden_prov_id=$prov_id,
                                                        orden_func_id=$func_id,
                                                        orden_estado_portal='$estado_oc',
                                                        orden_fecha_entrega='$fecha_oc',
                                                        orden_licitacion='$licitacion_oc',
                                                        orden_observacion='$comentario_ref',
                                                        orden_moneda='$moneda_oc'
                                                        WHERE orden_id=".$chk['orden_id']);

							$orden_id=$chk['orden_id'];
						}
						
						
						foreach($orden_obj->OrderDetail->ListOfItemDetail->ItemDetail AS $art)
						{
            
							$art_glosa = pg_escape_string(utf8_decode($art->BaseItemDetail->ItemIdentifiers->ItemDescription));
							$cantidad = $art->BaseItemDetail->TotalQuantity->QuantityValue*1;
							$art_subtotal = ((double)$art->PricingDetail->LineItemTotal->MonetaryAmount);

							preg_match('/([0-9]{3}[0-9P]{1}[0-9]{3,})/',$art_glosa,$codigos);

							if(isset($codigos[1])) {
	                                                	$art_codigo=pg_escape_string($codigos[1]);
								$art=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$art_codigo';");
								if($art) $art_id=$art['art_id'];
							} else {
								$art=false;
							}

							if(!$art)
								pg_query("INSERT INTO orden_servicios VALUES (DEFAULT, $orden_id, '$art_glosa', $art_subtotal, '', $cantidad);");
							else
								pg_query("INSERT INTO orden_detalle VALUES (DEFAULT, $orden_id, $art_id, $cantidad, $art_subtotal);");

						}
						


					}

					
				}


?>
