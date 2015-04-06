<?php
    require_once('../../conectar_db.php');
    $fecha_inicio=pg_escape_string($_GET['fecha3']);
    $fecha_final=pg_escape_string($_GET['fecha4']);
    
    $xls=true;
    
        
    $reg_recep=cargar_registros_obj("
    SELECT pacientes.pac_ficha as num_ficha,pac_rut,pac_nombres || ' ' || pac_appat || ' ' || pac_apmat as nombre_paciente,
    func_rut,func_nombre,date_trunc('second',am_fecha) fecha_recepcion 
    FROM archivo_movimientos 
    LEFT JOIN pacientes ON pacientes.pac_id=archivo_movimientos.pac_id
    LEFT JOIN funcionario ON func_id=am_func_id
    WHERE am_fecha::date BETWEEN '$fecha_inicio' AND '$fecha_final'
    AND am_estado=3 AND destino_esp_id=0 AND (am_centro_ruta_destino='' OR am_centro_ruta_destino is null) 
    ORDER BY am_fecha
    ");
?>
    <style type="text/css">
        .xl65
        {
            mso-style-parent:style0;
            mso-number-format:"\@";
        }
    </style>
<?php
    if($reg_recep)
    {
        $class_txt="class='xl65'";
        header("Content-Type: application/vnd.ms-excel;");
	header("Content-Disposition: filename=\"Registrorecepciones_".$fecha_inicio."_".$fecha_final."xls\";");
        
        print("
        <table>
            <tr>
                <td style='text-align:center;font-weight:bold;' colspan=4><b>REGISTRO DE RECEPCIONES EN ARCHIVO</b></td>
            </tr>
        </table>
        <br>
        <table border=1>
            <tr>
                <td style='text-align:center;font-weight:bold;'>&nbsp;</td>
                <td style='text-align:center;font-weight:bold;'>Nro Ficha</td>
                <td style='text-align:center;font-weight:bold;'>Rut Paciente</td>
                <td style='text-align:center;font-weight:bold;'>Nombre Paciente</td>
                <td style='text-align:center;font-weight:bold;'>Rut Funcionario</td>
                <td style='text-align:center;font-weight:bold;'>Nombre Funcionario</td>
                <td style='text-align:center;font-weight:bold;'>Fecha Recepci&oacute;n</td>
            </tr>
        
        ");
        for($i=0;$i<count($reg_recep);$i++)
        {
                print("<tr>");
                    print("<td style='text-align:center;font-weight:bold;'>".($i+1)."</td>");
                    print("<td $class_txt style='text-align:center;font-weight:bold;'>".$reg_recep[$i]['num_ficha']."</td>");
                    print("<td style='text-align:center;font-weight:bold;'>".strtoupper($reg_recep[$i]['pac_rut'])."</td>");
                    print("<td style='text-align:left;font-weight:bold;'>".strtoupper($reg_recep[$i]['nombre_paciente'])."</td>");
                    print("<td style='text-align:left;font-weight:bold;'>".strtoupper($reg_recep[$i]['func_rut'])."</td>");
                    print("<td style='text-align:left;font-weight:bold;'>".strtoupper($reg_recep[$i]['func_nombre'])."</td>");
                    print("<td style='text-align:left;font-weight:bold;'>".$reg_recep[$i]['fecha_recepcion']."</td>");
                print("</tr>");
        }
        print("</table>");
    }
    else
    {
        print("
        <table>
            <tr>
                <td style='text-align:center;font-weight:bold;' colspan=4><b>NO SE HAN ENCONTRADO REGISTROS</b></td>
            </tr>
        </table>
        ");
    }
?>