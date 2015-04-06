<?php
   /*
   Nombre Informe: Lsitado de pacientes con recetas
   Entrega informacion de todos los pacientes que tengan recetas, especificando
   Claudio Esteban angel Pinda.
   Soluciones Computacionales
   Viña del mar.
   */
 set_time_limit(0);
 ini_set("memory_limit","250M");
 require_once('../../../conectar_db.php');
 require_once('../../infogen.php');

$resultado=cargar_registros_obj("select pedido_fecha, pedido_nro from pedido where origen_bod_id=6 and pedido_estado=0 order by pedido_fecha",false);

if($resultado!=false)
{
    for($i=0;$i<count($resultado);$i++)
    {
        $fecha_actual=date("m-d-Y");
        $fecha_actual=explode("-", $fecha_actual);
        $timestamp1 = mktime(0,0,0,$fecha_actual[0],$fecha_actual[1],$fecha_actual[2]);
        $fecha=explode(" ",$resultado[$i]['pedido_fecha']);
        $fechanormal=$fecha[0];
        $fecha=explode("/",$fecha[0]);
        $timestamp2 = mktime(0,0,0,$fecha[1],$fecha[0],$fecha[2]);
        $diferencia=$timestamp1 - $timestamp2;
        $dias=$diferencia / (60 * 60 * 24);
        $dias=abs($dias);
        $dias= floor($dias);
        if($dias>21)
        {
            //print($fechanormal.' <br>');
            print('<br>');
            print('<br>');
            print('<table border=1>');
            print('<tr>');
            print('<td>Nro pedido</td>');
            print('<td>dias</td>');
            print('<td>Fecha</td>');
            print('</tr>');
            print('<tr>');
            print('<td>'.$resultado[$i]['pedido_nro'].'</td>');
            print('<td>'.$dias.'</td>');
            print('<td>'.$fechanormal.'</td>');
            print('</tr>');
            print('</table>');
            print('<br>');
            print('<strong>Detalle de pedido</strong>');
            print('<br>');

            $totales = cargar_registros_obj("
            SELECT
            art_codigo,
            art_glosa,
            pedidod_cant,
            pedidod_estado
            FROM
            pedido_detalle
            JOIN articulo USING (art_id)
            JOIN pedido USING (pedido_id)
            WHERE pedido_nro=".$resultado[$i]['pedido_nro']."",false);
            if($totales!=false)
            {
                print('<table border=1>');
                print('<tr>');
                print('<td><b>Codigo int.</b></td>');
                print('<td><b>Glosa</b></td>');
                print('<td><b>Solicitada</b></td>');
                print('<td><b>Recepci&oacute;n</b></td>');
                print('</tr>');
                for($aa=0;$aa<count($totales);$aa++)
                {
                    
                    print('<tr>');
                    print('<td>'.$totales[$aa]['art_codigo'].'</td>');
                    print('<td><font size=1>'.$totales[$aa]['art_glosa'].'</font></td>');
                    print('<td>'.$totales[$aa]['pedidod_cant'].'</td>');
                    if(($totales[$aa]['pedidod_estado'])==t)
                    {
                        print('<td>Si</td>');
                    }
                    else
                    {
                        print('<td>No</td>');
                    }
                    


                    print('</tr>');
                    
                }
                print('</table>');
            }
            
            
        }
        else
        {
           if($dias==21)
           {
               print('<br>');

               //print($fechanormal.' <br>');
               print($resultado[$i]['pedido_nro'].'---VENCIDO HOY--'.$dias.'--Dias. <br>');
           }
           
           else
           {
               
                print('<br>');
                print('<br>');
                for($x=1;$x<7;$x++)
                {
                    $diastmp=$dias+$x;
                    if($diastmp==21)
                    {
                        //print($fechanormal.' <br>');
                        print($resultado[$i]['pedido_nro'].'--'.$dias.'-VENCERA en '.$x.'--más. <br>');
                        print($fechanormal.' <br>');
                        break;

                    }
                    //else
                    //{
                    //    print($resultado[$i]['pedido_nro'].'---NO VENCIDO--'.$dias.'--Dias. <br>');
                    //
                        
                    //}
                }
            }
        }


    }

}
   
?>
