<?php 
    require_once('../../conectar_db.php');
    $pac_id=$_POST['pac_id']*1;
    $esp_id=$_POST['esp_id']*1;
    $doc_id=$_POST['doc_id']*1;
    $tipo_cupos=$_POST['select_tipo_cupos']*1;
    
    
    
    if(isset($_POST['select_nom_motivo']))
    {
        if(($_POST['select_nom_motivo']*1)!=-1)
        {
            $motivo_atencion="upper(nom_motivo)=upper('".pg_escape_string($_POST['select_nom_motivo'])."')";
        }
        else
        {
            $motivo_atencion="true";
        }
    }
    else
    {
        $motivo_atencion="true";
    }
    
    $f1=pg_escape_string($_POST['fecha1']);
    $f2=pg_escape_string($_POST['fecha2']);
    $h1=pg_escape_string($_POST['hora1']);
    $h2=pg_escape_string($_POST['hora2']);
    if($pac_id!=0) 
        $pac_w="pac_id=$pac_id";
    else
        $pac_w='pac_id=0';

    if($doc_id!=0) 
        $doc_w="nom_doc_id=$doc_id";
    else
        $doc_w='true';

    if($esp_id!=0) 
        $esp_w="nom_esp_id=$esp_id";
    else
        $esp_w='true';
		
    if($f1!='')
        $f1_w="nom_fecha>='$f1'";
    else
        $f1_w='true';

    if($f2!='')
        $f2_w="nom_fecha<='$f2'";
    else
        $f2_w='true';

    if($h1!='')
        $h1_w="nomd_hora>='$h1'";
    else
        $h1_w='true';

    if($h2!='')
        $h2_w="nomd_hora<='$h2'";
    else
        $h2_w='true';
		
    if($pac_w=='pac_id=0' AND $esp_w=='true' AND $doc_id=='true')
    {
?>
        <center><h2>Ingrese par&aacute;metros para su b&uacute;squeda.</h2></center>
<?php 
        exit();	
    }
    if($tipo_cupos==0)
    {
        $consulta="
	SELECT * FROM (
	SELECT *, to_char(nom_fecha, 'D') AS dow,nom_motivo,COALESCE(nom_estado,0)as estado_nomina  
	FROM nomina_detalle
        JOIN nomina USING (nom_id)
        JOIN especialidades ON nom_esp_id=esp_id
        JOIN doctores ON nom_doc_id=doc_id
        WHERE (nomd_diag_cod NOT IN ('X','T','B') OR nomd_diag_cod IS NULL) AND $pac_w AND $esp_w AND $doc_w AND
        $f1_w AND $f2_w AND $h1_w AND $h2_w AND $motivo_atencion
        ORDER BY nom_fecha, nomd_hora
	)as foo
	WHERE estado_nomina<>-1
	ORDER BY nom_fecha, nomd_hora
        ";
	//print($consulta);
    
    
        $c=cargar_registros_obj($consulta, true);

	$consulta="SELECT count(*) AS cuenta  FROM nomina_detalle
	JOIN nomina ON nomina.nom_id=nomina_detalle.nom_id AND COALESCE(nom_estado,0)<>-1
        JOIN especialidades ON nom_esp_id=esp_id
        JOIN doctores ON nom_doc_id=doc_id
        WHERE (nomd_diag_cod NOT IN ('X','T','B') OR nomd_diag_cod IS NULL) AND NOT pac_id=0 AND $esp_w AND $doc_w AND
        $f1_w AND $f2_w AND $h1_w AND $h2_w AND $motivo_atencion
	";
	//print($consulta);

        $tmp=cargar_registro($consulta, true);
		
        $num=$tmp['cuenta']*1;
    
    

        $tmp2=cargar_registro("
        SELECT count(*) AS cuenta  FROM nomina_detalle
	JOIN nomina ON nomina.nom_id=nomina_detalle.nom_id AND COALESCE(nom_estado,0)<>-1
        JOIN especialidades ON nom_esp_id=esp_id
        JOIN doctores ON nom_doc_id=doc_id
        WHERE (nomd_diag_cod NOT IN ('X','T','B') OR nomd_diag_cod IS NULL) AND pac_id=0 AND $esp_w AND $doc_w AND
        $f1_w AND $f2_w AND $h1_w AND $h2_w AND $motivo_atencion
        ", true);
		
        $num2=$tmp2['cuenta']*1;
    
    
        if($num>0)
        {
            echo "<center><h3>Hay <u>$num cupos utilizados</u> y $num2 cupos libres seg&uacute;n su b&uacute;squeda.</h3></center>";
        }
        else
        {
            echo "<center><h3>No hay cupos utilizados y $num2 cupos libres seg&uacute;n su b&uacute;squeda.</h3></center>";
        }
       
    
        if(!$c)
        {
?>
            <center><h2>No hay cupos libres para su b&uacute;squeda.</h2></center>
<?php 
            exit();
        }
?>
        <table style='width:100%;' cellspacing=0>
            <tr class='tabla_header' style='font-size:16px;'>
                <td>&nbsp;</td>
                <td>Fecha</td>
                <td>Hora</td>
                <?php if($esp_w=='true') { ?><td>Especialidad</td><?php } ?>
                <?php if($doc_w=='true') { ?><td>Profesional</td><?php } ?>
                <td>Tipo Atenci&oacute;n</td>
                <?php if(!isset($_POST['mostrar'])){ ?><td style='width:5%;'>Abrir N&oacute;mina</td><?php } ?>
                
            </tr>
<?php 
            $dias=array('','Domingo', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado');
            for($i=0;$i<sizeof($c);$i++)
            {
                $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
                print("<tr class='$clase' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"$clase\";'>
                    <td style='text-align:right;font-size:18px;'><i>".$dias[$c[$i]['dow']*1]."</i></td>
                    <td style='text-align:center;font-size:16px;'>".substr($c[$i]['nom_fecha'],0,10)."</td>
                    <td style='text-align:center;font-size:20px;'>".substr($c[$i]['nomd_hora'],0,5)."</td>");
                    if($esp_w=='true')
                        print("<td style='text-align:left;'>".$c[$i]['esp_desc']."</td>");
                    if($doc_w=='true')
                        print("<td style='text-align:left;'>".$c[$i]['doc_paterno']." ".$c[$i]['doc_materno']." ".$c[$i]['doc_nombres']."</td>");
                    print("<td style='text-align:center;font-size:20px;'>".$c[$i]['nom_motivo']."</td>");
                    if($pac_id==0)
                    {
                        if(!isset($_POST['mostrar']))
                            print("<td><center><img src='../../iconos/date_go.png' style='cursor:pointer;width:24px;height:24px;' onClick='abrir_nom(\"".$c[$i]['nom_folio']."\");' /></center></td>");
                    }
                    else
                        print("<td><center><img src='../../iconos/printer.png' style='cursor:pointer;width:24px;height:24px;' onClick='imprimir_citacion(".$c[$i]['nomd_id'].");' /></center></td>");
                print("</tr>");
            }
?>
        </table>
<?php
    }
    if($tipo_cupos==1)
    {
        $consulta="select * from (
        select nomina.nom_id,COALESCE(nomina.nom_estado,0)as nom_estado,nom_esp_id,nom_doc_id,nom_fecha::date,nom_motivo,doc_paterno,doc_materno,doc_nombres,nom_folio,cupos_cantidad_c,
        (
            (select sum(cupos_cantidad_c) from cupos_atencion where cupos_atencion.nom_id=nomina.nom_id group by cupos_atencion.nom_id)
            -
            (SELECT COUNT(*) FROM nomina_detalle AS nd WHERE nd.nom_id=nomina.nom_id AND NOT pac_id=0 AND nomd_extra='S' AND (nomd_diag_cod NOT IN ('T') OR nomd_diag_cod IS NULL))
        )as extras_disponibles
        from nomina 
        JOIN especialidades ON nom_esp_id=esp_id 
        JOIN doctores ON nom_doc_id=doc_id
        JOIN cupos_atencion on nomina.nom_id=cupos_atencion.nom_id
        
        where true AND $esp_w AND $doc_w AND
        $f1_w AND $f2_w AND $motivo_atencion
        order by nom_fecha
        )as foo
        where extras_disponibles>0 AND nom_estado<>-1";
	//print($consulta);
        
        $c=cargar_registros_obj($consulta, true);
        if(!$c)
        {
?>
            <center><h2>No hay cupos libres para su b&uacute;squeda.</h2></center>
<?php 
            exit();
        }
?>
        <table style='width:100%;' cellspacing=0>
            <tr class='tabla_header' style='font-size:16px;'>
                <td>&nbsp;</td>
                <td>Fecha</td>
                <?php if($esp_w=='true') { ?><td>Especialidad</td><?php } ?>
                <?php if($doc_w=='true') { ?><td>Profesional</td><?php } ?>
                <td>Tipo Atenci&oacute;n</td>
                <td>Extras Disponibles</td>
                <?php if(!isset($_POST['mostrar'])){ ?><td style='width:5%;'>Abrir N&oacute;mina</td><?php } ?>
            </tr>
<?php 
            //$dias=array('','Domingo', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado');
            for($i=0;$i<sizeof($c);$i++)
            {
                $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
                print("<tr class='$clase' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"$clase\";'>
                    <td style='text-align:right;font-size:18px;'><i>".($i+1)."</i></td>
                    <td style='text-align:center;font-size:16px;'>".substr($c[$i]['nom_fecha'],0,10)."</td>");
                    if($esp_w=='true')
                        print("<td style='text-align:left;'>".$c[$i]['esp_desc']."</td>");
                    if($doc_w=='true')
                        print("<td style='text-align:left;'>".$c[$i]['doc_paterno']." ".$c[$i]['doc_materno']." ".$c[$i]['doc_nombres']."</td>");
                    print("<td style='text-align:center;font-size:20px;'>".$c[$i]['nom_motivo']."</td>");
                    print("<td style='text-align:center;font-size:20px;'>".$c[$i]['extras_disponibles']."</td>");
                    if(!isset($_POST['mostrar']))
                        print("<td><center><img src='../../iconos/date_go.png' style='cursor:pointer;width:24px;height:24px;' onClick='abrir_nom(\"".$c[$i]['nom_folio']."\");' /></center></td>");
                print("</tr>");
            }
?>
        </table>
<?php
    }
?>

