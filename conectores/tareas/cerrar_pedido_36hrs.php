<?php
    require_once('config.php');
    require_once('sigh.php');
    $consulta="select 
    pedido.pedido_id,
    pedido.pedido_nro, 
    (select log_id from logs where log_id_pedido=pedido_id order by log_fecha desc limit 1)as log_id,
    (select log_fecha::date from logs where log_id_pedido=pedido_id order by log_fecha desc limit 1)as log_fecha,
    (now()-(select log_fecha from logs where log_id_pedido=pedido_id order by log_fecha desc limit 1)) as dif_fechas,
    ((extract(epoch from age(now(),((select log_fecha from logs where log_id_pedido=pedido_id order by log_fecha desc limit 1))))/60)/60)as dif_horas,
    (EXTRACT (DAY FROM age(now(),(select log_fecha from logs where log_id_pedido=pedido_id order by log_fecha desc limit 1) ))) as dif_dias
    from pedido 
    where pedido_estado=1 and destino_bod_id!=0 
    and ((extract(epoch from age(now(),((select log_fecha from logs where log_id_pedido=pedido_id order by log_fecha desc limit 1))))/60)/60)>=36 
    order by log_fecha";
        
    $reg_pedidos=cargar_registros_obj($consulta);
    if($reg_pedidos)
    {
        //print_r($reg_pedidos);
        //die();
        $dias = array("domingo","lunes","martes","mi&eacute;rcoles","jueves","viernes","s&aacute;bado");
        pg_query("START TRANSACTION;");
        for($i=0;$i<count($reg_pedidos);$i++)
        {
            print('<br>');
            print("Nro Pedido:".$reg_pedidos[$i]['pedido_nro']." | -dif Dias:".$reg_pedidos[$i]['dif_dias']."|Horas: ".$reg_pedidos[$i]['dif_horas']);
            print('<br>');
            print("Fecha Log:".$reg_pedidos[$i]['log_fecha']);
            print('<br>');
            $fecha_inicial=explode("/",$reg_pedidos[$i]['log_fecha']);
            $fecha_inicio="".$fecha_inicial[2]."-".$fecha_inicial[1]."-".$fecha_inicial[0]."";
            $dif_horas=$reg_pedidos[$i]['dif_horas'];
            $hora_pedido=0;
            //print('<br>');
            //print($fecha_inicio);
            //print('<br>');
            
            for($ii=0;$ii<($reg_pedidos[$i]['dif_dias']*1);$ii++)
            {
                print("<br>");
                $fecha_siguiente = strtotime ( '+'.($ii+1).' day' , strtotime($fecha_inicio )) ;
                $fecha_nueva = date ( 'Y-m-j' , $fecha_siguiente);
                $fecha_normal = date ( 'j-m-Y' , $fecha_siguiente);
                $dayoftheweek = date("N",strtotime($fecha_normal)); 
                print("<br>");
                print("Nueva Fecha: ".$fecha_nueva);
                print("<br>");
                print("Fecha Normal:".$fecha_normal);
                print("<br>");
                print("Dia Nro:".$dayoftheweek);
                print("<br>");
                print("Dia Texto: ".$dias[date("w",strtotime($fecha_normal))]);
                if($fecha_normal=="18-09-2014" or $fecha_normal=="19-09-2014")
                {
                    if($hora_pedido==0)
                        $hora_pedido=$dif_horas-24;
                    else
                        $hora_pedido=$hora_pedido-24;
                    
                    print("<br>");
                    print("RESTA:".$hora_pedido);
                    print("<br>");
                }
                if(($dayoftheweek*1)==6 or ($dayoftheweek*1)==7)
                {
                    if($hora_pedido==0)
                        $hora_pedido=$dif_horas-24;
                    else
                        $hora_pedido=$hora_pedido-24;
                    
                    print("<br>");
                    print("RESTA:".$hora_pedido);
                    print("<br>");
                }
                print("<br>");
                
            }
            print("<br>");
            print("Hora Final de pedido: ".$hora_pedido);
            print("<br><br>-----------------------------------------------------------------------");
            if(($hora_pedido*1)>=36)
            {
                print('<br>');
                print("INSERT INTO pedido_log_rev VALUES (DEFAULT, ".$reg_pedidos[$i]['pedido_id'].", ".$reg_pedidos[$i]['log_id'].", current_timestamp, 7,'');");
                print('<br>');
                print("UPDATE pedido_detalle SET pedidod_estado=true WHERE pedido_id=".$reg_pedidos[$i]['pedido_id'].";");
                print('<br>');
                print("UPDATE pedido SET pedido_estado=2 WHERE pedido_id=".$reg_pedidos[$i]['pedido_id'].";");
                print('<br>');
                pg_query("INSERT INTO pedido_log_rev VALUES (DEFAULT, ".$reg_pedidos[$i]['pedido_id'].",".$reg_pedidos[$i]['log_id'].", current_timestamp, 7,'TERMINADO AUTOMATICAMENTE POR SISTEMA.');");
                pg_query("UPDATE pedido_detalle SET pedidod_estado=true WHERE pedido_id=".$reg_pedidos[$i]['pedido_id'].";");
                pg_query("UPDATE pedido SET pedido_estado=2 WHERE pedido_id=".$reg_pedidos[$i]['pedido_id'].";");
            }
            /*
            print("INSERT INTO pedido_log_rev VALUES (DEFAULT, ".$reg_pedidos[$i]['pedido_id'].", ".$reg_pedidos[$i]['log_id'].", current_timestamp, 7,'');");
            print('<br>');
            print('<br>');
            print("UPDATE pedido SET pedido_estado=2 WHERE pedido_id=".$reg_pedidos[$i]['pedido_id'].";");
            print('<br>');
            pg_query("INSERT INTO pedido_log_rev VALUES (DEFAULT, ".$reg_pedidos[$i]['pedido_id'].",".$reg_pedidos[$i]['log_id'].", current_timestamp, 7,'TERMINADO AUTOMATICAMENTE POR SISTEMA.');");
            pg_query("UPDATE pedido_detalle SET pedidod_estado=true WHERE pedido_id=".$reg_pedidos[$i]['pedido_id']."");
            pg_query("UPDATE pedido SET pedido_estado=2 WHERE pedido_id=".$reg_pedidos[$i]['pedido_id'].";");
            */
        }
        pg_query("COMMIT;");
    }
    
?>
