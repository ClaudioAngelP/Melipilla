<?php
    require_once('../../conectar_db.php');
    //$nomd_id=$_POST['nomd_id']*1;
    //$nd=cargar_registro("SELECT * FROM nomina_detalle JOIN nomina USING (nom_id) WHERE nomd_id=$nomd_id;");
    //$pac_id2=$nd['pac_id']*1;
    $esp_id=$_POST['esp_id'];
    $doc_id=$_POST['doc_id'];
    $select_nom_motivo=$_POST['select_nom_motivo'];
    $pac_id=0;
    $tipo_cupos=$_POST['select_tipo_cupos']*1;
    
    
    if($esp_id!="-1")
        $esp_id=$esp_id*1;
    else
        $esp_id=0;

    if($doc_id!="0")
        $doc_id=$doc_id*1;
    else
        $doc_id=0;

    //$esp_id=$nd['nom_esp_id']*1;
    //$doc_id=$nd['nom_doc_id']*1;
    if(isset($_POST['fecha1']))
        $f1=pg_escape_string($_POST['fecha1']);
    else
        $f2=pg_escape_string(date('d/m/Y'));
   
    if(isset($_POST['fecha2']))
        $f2=pg_escape_string($_POST['fecha2']);
    else
        $f2='';
    
    $h1='';
    $h2='';
    //$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_id=$pac_id2", true);
    //$esp=cargar_registro("SELECT * FROM especialidades WHERE esp_id=$esp_id;");
    //$doc=cargar_registro("SELECT * FROM doctores WHERE doc_id=$doc_id;");
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
    
    $w_tipo_atencion="";
    if($select_nom_motivo!=-1)
    {
        $w_tipo_atencion="and nom_motivo='$select_nom_motivo'";
    }
    
    if($pac_w=='pac_id=0' AND $esp_w=='true' AND $doc_id=='true')
    {
?>
        <center><h2>Ingrese par&aacute;metros para su b&uacute;squeda.</h2></center>
<?php 
	exit();	
    }
?>
<script>
    
    
    
    
    
</script>
<?php
    if($tipo_cupos==0)
    {
        $consulta="SELECT *, to_char(nom_fecha, 'D') AS dow,nom_motivo,
        (SELECT COUNT(DISTINCT nomd_hora) FROM nomina_detalle where nomina_detalle.nom_id=nomina.nom_id)as cantidad
        FROM nomina_detalle
        JOIN nomina USING (nom_id)
        JOIN especialidades ON nom_esp_id=esp_id
        JOIN doctores ON nom_doc_id=doc_id
        WHERE (nomd_diag_cod NOT IN ('X','T','B') OR nomd_diag_cod IS NULL)  AND $pac_w AND $esp_w AND $doc_w AND
        $f1_w AND $f2_w AND $h1_w AND $h2_w $w_tipo_atencion
        ORDER BY nom_fecha, nomd_hora
        ";
    
        //print($consulta);
        $c=cargar_registros_obj($consulta,true);
    
        /*
        $tmp=cargar_registro("
        SELECT count(*) AS cuenta  FROM nomina_detalle
        JOIN nomina USING (nom_id)
        JOIN especialidades ON nom_esp_id=esp_id
        JOIN doctores ON nom_doc_id=doc_id
        WHERE (nomd_diag_cod NOT IN ('X','T','B') OR nomd_diag_cod IS NULL) AND NOT pac_id=0 AND $esp_w AND $doc_w AND
        $f1_w AND $f2_w AND $h1_w AND $h2_w
        ", true);

        $num=$tmp['cuenta']*1;

        $tmp2=cargar_registro("
        SELECT count(*) AS cuenta  FROM nomina_detalle
        JOIN nomina USING (nom_id)
        JOIN especialidades ON nom_esp_id=esp_id
        JOIN doctores ON nom_doc_id=doc_id
        WHERE (nomd_diag_cod NOT IN ('X','T','B') OR nomd_diag_cod IS NULL) AND pac_id=0 AND $esp_w AND $doc_w AND
        $f1_w AND $f2_w AND $h1_w AND $h2_w
        ", true);

        $num2=$tmp2['cuenta']*1;
        * 
        */
        if(!$c)
        {
?>
            <center><h2>No hay cupos libres similares para su b&uacute;squeda.</h2></center>
<?php 
            exit();
        }
?>
            
        <table style='width:100%;font-size:12px;' cellspacing=0>
            <tr class='tabla_header' style='font-size:14px;'>
                <td>D&iacute;a de la Semana</td>
                <td>Fecha</td>
                <td>Hora</td>
                <td>Tipo Atenci&oacute;n</td>
                <?php if($esp_w=='true') { ?><td>Especialidad</td><?php } ?>
                <?php if($doc_w=='true') { ?><td>Rut</td><?php } ?>
                <?php if($doc_w=='true') { ?><td>Profesional</td><?php } ?>
                
                <td style='width:5%;'>Reagendar N&oacute;mina</td>
            </tr>
<?php 
            $dias=array('','Domingo', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado');
            for($i=0;$i<sizeof($c);$i++)
            {
                $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
                print("<tr class='$clase' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"$clase\";'>
                    <td style='text-align:right;font-size:18px;'><i>".$dias[$c[$i]['dow']*1]."</i></td>
                    <td style='text-align:center;font-size:16px;'>".substr($c[$i]['nom_fecha'],0,10)."</td>
                    <td style='text-align:center;font-size:20px;'>".substr($c[$i]['nomd_hora'],0,5)."</td>
                    <td style='text-align:center;font-size:20px;'>".$c[$i]['nom_motivo']."</td>");
		
                    if($esp_w=='true')
                        print("<td style='text-align:left;'>".$c[$i]['esp_desc']."</td>");
                    
                    if($doc_w=='true')
                        print("<td style='text-align:center;'>".$c[$i]['doc_rut']."</td>");
		
                    if($doc_w=='true')
                        print("<td style='text-align:left;'>".$c[$i]['doc_paterno']." ".$c[$i]['doc_materno']." ".$c[$i]['doc_nombres']."</td>");
                                
                    //print("<td><center><img src='../../iconos/arrow_refresh.png' style='cursor:pointer;width:24px;height:24px;' onClick='usar_citacion(".$c[$i]['nom_id'].",\"".substr($c[$i]['nomd_hora'],0,5)."\")'></center></td>");
                    if(($c[$i]['cantidad']*1)!=1)
                    {
                        print("<td><center><img src='../../iconos/arrow_refresh.png' style='cursor:pointer;width:24px;height:24px;' onClick='usar_citacion(".$c[$i]['nom_id'].",\"".substr($c[$i]['nomd_hora'],0,5)."\")'></center></td>");
                    }
                    else
                    {
                        print("<td><center><img src='../../iconos/arrow_refresh.png' style='cursor:pointer;width:24px;height:24px;' onClick='usar_citacion(".$c[$i]['nom_id'].",\"".substr($c[$i]['nomd_hora'],0,5)."_".$c[$i]['nomd_id']."\")'></center></td>");
                    }
                print("</tr>");
            }
?>
        </table>
<?php
    }
    if($tipo_cupos==1)
    {
        
        $consulta="select * from (
        select nomina.nom_id,nom_esp_id,nom_doc_id,nom_fecha::date,nom_motivo,doc_rut,doc_paterno,doc_materno,doc_nombres,nom_folio,cupos_cantidad_c,
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
        $f1_w AND $f2_w $w_tipo_atencion
        order by nom_fecha
        )as foo
        where extras_disponibles>0 order by nom_fecha, nom_doc_id";
        
        $c=cargar_registros_obj($consulta, true);
        if(!$c)
        {
?>
            <center><h2>No hay cupos Extras libres en su b&uacute;squeda.</h2></center>
<?php 
            exit();
        }
?>
        <table style='width:100%;' cellspacing=0>
            <tr class='tabla_header' style='font-size:16px;'>
                <td>&nbsp;</td>
                <td>Fecha</td>
                <?php if($esp_w=='true') { ?><td>Especialidad</td><?php } ?>
                <?php if($doc_w=='true') { ?><td>Rut</td><?php } ?>
                <?php if($doc_w=='true') { ?><td>Profesional</td><?php } ?>
                <td>Tipo Atenci&oacute;n</td>
                <td>Extras Disponibles</td>
                <td>Horas</td>
                <td style='width:5%;'>Utilizar Extra</td>
            </tr>
<?php
            for($i=0;$i<sizeof($c);$i++)
            {
                $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
                print("<tr class='$clase' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"$clase\";'>
                    <td style='text-align:right;font-size:18px;'><i>".($i+1)."</i></td>
                    <td style='text-align:center;font-size:16px;'>".substr($c[$i]['nom_fecha'],0,10)."</td>");
                    if($esp_w=='true')
                        print("<td style='text-align:left;'>".$c[$i]['esp_desc']."</td>");
                    if($doc_w=='true')
                        print("<td style='text-align:center;'>".$c[$i]['doc_rut']."</td>");
                    if($doc_w=='true')
                        print("<td style='text-align:left;'>".$c[$i]['doc_paterno']." ".$c[$i]['doc_materno']." ".$c[$i]['doc_nombres']."</td>");
                    print("<td style='text-align:center;font-size:20px;'>".$c[$i]['nom_motivo']."</td>");
                    print("<td style='text-align:center;font-size:20px;'>".$c[$i]['extras_disponibles']."</td>");
                    print("<td style='text-align:center;font-size:20px;'>");
                    $nom_id=$c[$i]['nom_id']*1;
                    $lote=false;
                    $string_lote="&nbsp;";
                    $consulta="select count(nomd_hora)as cantidad,nomd_hora from nomina_detalle
                    join nomina USING(nom_id)
                    where nom_id=".$nom_id." AND (nomd_diag_cod NOT IN ('T')) and nomd_extra!='S'
                    group by nomd_hora";
                    $grupo_hrs = cargar_registros_obj($consulta);
                    if($grupo_hrs)
                    {
                        if(count($grupo_hrs)==1)
                        {
                            if(($grupo_hrs[0]['cantidad']*1)>1)
                            {
                                $lote=true;
                            }
                        }
                    }
                    $consulta="SELECT DISTINCT nomd_hora FROM nomina_detalle WHERE nom_id=$nom_id AND nomd_hora IS NOT NULL AND NOT nomd_hora='00:00:00' ORDER BY nomd_hora";
                    $reg_horas=cargar_registros_obj($consulta);
                    if($reg_horas)
                    {
                        $horas_extra_html="<select id='nomd_hora_extra_".$nom_id."' name='nomd_hora_extra".$nom_id."'>";
                        for($k=0;$k<sizeof($reg_horas);$k++)
                        {
                            $horas_extra_html.="<option value='".substr($reg_horas[$k]['nomd_hora'],0,5)."'>".substr($reg_horas[$k]['nomd_hora'],0,5)."</option>";
                            if($lote)
                                break;
                        }
                        $horas_extra_html.='</select>';
                        print($horas_extra_html);
                    
                    }
                    else
                    {
                        print("&nbsp;");
                    }
                    print("</td>");
                    print("<td><center><img src='../../iconos/arrow_refresh.png' style='cursor:pointer;width:24px;height:24px;' onClick='usar_citacion($nom_id,\"extra\");'></center></td>");
                print("</tr>");
            }
?>
            </table>
<?php
    }
?>
