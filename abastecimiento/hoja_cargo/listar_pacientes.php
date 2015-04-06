<?php

    require_once('../../conectar_db.php');
    $bod_id=$_POST['bodega_id2'];
    $pac_id=$_POST['pac_id2'];
    $fecha1=pg_escape_string($_POST['fecha1']);
    $fecha2=pg_escape_string($_POST['fecha2']);
  
    if(strstr($bod_id,'.'))
    {
        //$tabla_stock='cargo_hoja';
        $ubica="stock_centro_ruta='".pg_escape_string($bod_id)."'";
    }
    else
    {
        //$tabla_stock='stock';
        $ubica="stock_bod_id=".($bod_id*1);
    }
    
    if($pac_id!=''){
		$pac_w='cargo_hoja.pac_id='.$pac_id;
	} else {
		$pac_w='true';
	}
    
    $pacientes=cargar_registros_obj("Select * from cargo_hoja
                join pacientes on cargo_hoja.pac_id=pacientes.pac_id
                join logs on cargo_hoja.log_id=logs.log_id
                join stock on logs.log_id=stock_log_id
                where logs.log_tipo=17 and $ubica and $pac_w and log_fecha::date between '$fecha1' and '$fecha2'
                order by pac_rut
                ",true);

?>


<?php 

if($pacientes)
{

   print('<table style="width:100%;">
    <tr class="tabla_header">
    <td>R.U.T.</td>
    <td>Nombre</td>
    <td>Apellido Paterno</td>
    <td>Apellido Materno</td>
    <td>Ficha</td>
    <td>Fono</td>
    <td><img src="iconos/zoom.png"></td>
    </tr>');


    $var_rut='';
    $clase='tabla_fila';
    for($i=0;$i<count($pacientes);$i++)
    {
        //if($i%2==0) $clase='tabla_fila'; else $clase='tabla_fila2';
        if($var_rut!=$pacientes[$i]['pac_rut'])
        {
            print('<tr class="'.$clase.'"><td style="text-align:center;">'.$pacientes[$i]['pac_rut'].'</td>');
            print('<td style="text-align:left;">'.$pacientes[$i]['pac_nombres'].'</td>');
            print('<td style="text-align:left;">'.$pacientes[$i]['pac_appat'].'</td>');
            print('<td style="text-align:left;">'.$pacientes[$i]['pac_apmat'].'</td>');
            print('<td style="text-align:center;">'.$pacientes[$i]['pac_ficha'].'</td>');
            print('<td style="text-align:center;">'.$pacientes[$i]['pac_fono'].'</td>');
            print('<td style="text-align:center;"><img src="iconos/zoom_in.png" style="cursor: pointer;" 
                    onClick="mostrar_detalle('.$pacientes[$i]['pac_id'].');" alt="Ver Detalle..." title="Ver Detalle..."></td>');


            print('</tr>');
            if($clase=='tabla_fila')
            {
                $clase='tabla_fila2';
            }
            else
            {
                $clase='tabla_fila';
            }


        }




        $var_rut=$pacientes[$i]['pac_rut'];
        

    }
    print('</table>');

}
else
{
    print('<table style="width:100%;">
    <tr class="tabla_header">
    <td>Sin Datos</td>
    </tr></table>');
}

?>

