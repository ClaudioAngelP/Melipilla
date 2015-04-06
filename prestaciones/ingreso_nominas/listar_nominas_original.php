<?php
    require_once('../../conectar_db.php');
    
    function img($t)
    {
        if($t=='t')
            return '<center><img src="iconos/tick.png" width=8 height=8></center>';
        else
            return '<center><img src="iconos/cross.png" width=8 height=8></center>';
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    $fecha = pg_escape_string($_POST['fecha1']);  
    $esp_id = $_POST['esp_id']*1;
    $doc_id = $_POST['doc_id']*1;
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    if( $esp_id!=-1 )
    {
        $w_esp='nom_esp_id='.$esp_id;	
    }
    elseif( $esp_id==-1 AND _cax(300) AND !_cax(202))
    {
        $w_esp='nom_esp_id IN ('._cav(300).')';
    }
    elseif($esp_id==-1)
    {
        $esp_aut=_cav(311);
        if($esp_aut!='')
        {
            $w_esp='nom_esp_id IN ('._cav(311).')';
        }
        else
        {
            $w_esp='true';
        }
    }
    else
    {
        $w_esp='true';
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    if( $doc_id!=-1 )
    {
        $w_doc='nom_doc_id='.$doc_id.'';
    }
    else
    {
        $w_doc='true';
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    $q="
    SELECT 
    nom_id, nom_folio, esp_desc, doc_rut, doc_paterno, doc_materno, doc_nombres,
    nom_digitar, nom_motivo,
    (SELECT COUNT(*) FROM nomina_detalle AS nd WHERE nd.nom_id=nomina.nom_id AND pac_id=0 AND nomd_diag_cod NOT IN ('X','T','B')) AS libres,
    (SELECT COUNT(*) FROM nomina_detalle AS nd WHERE nd.nom_id=nomina.nom_id AND NOT pac_id=0 AND nomd_diag_cod NOT IN ('T')) AS ocupados,
    nom_tipo_contrato,
    nom_estado,
    (SELECT MIN(nomd_hora) FROM nomina_detalle AS nd WHERE nd.nom_id=nomina.nom_id limit 1)as min_hora,
    (select sum(cupos_cantidad_c) from cupos_atencion where cupos_atencion.nom_id=nomina.nom_id group by cupos_atencion.nom_id)as extras,
    (SELECT COUNT(*) FROM nomina_detalle AS nd WHERE nd.nom_id=nomina.nom_id AND NOT pac_id=0 AND nomd_extra='S' AND nomd_diag_cod NOT IN ('T'))as extras_ocupados,
    (
    (select sum(cupos_cantidad_c) from cupos_atencion where cupos_atencion.nom_id=nomina.nom_id group by cupos_atencion.nom_id)
    -
    (SELECT COUNT(*) FROM nomina_detalle AS nd WHERE nd.nom_id=nomina.nom_id AND NOT pac_id=0 AND nomd_extra='S' AND nomd_diag_cod NOT IN ('T'))
    )as extras_disponibles
    FROM nomina
    JOIN especialidades ON nom_esp_id=esp_id
    JOIN doctores ON nom_doc_id=doc_id
    WHERE nom_fecha::date='$fecha' AND $w_esp AND $w_doc 
    ORDER BY esp_desc, doc_paterno, doc_materno, doc_nombres,min_hora
    ";
    
    ///print($q);
   
    
    
    $lista = cargar_registros_obj($q,true);
   
    if($esp_id==-1)
        $esp=desplegar_opciones_sql("SELECT DISTINCT esp_id, esp_desc FROM nomina
        JOIN especialidades ON nom_esp_id=esp_id
        WHERE nom_fecha::date='$fecha'
        ORDER BY esp_desc");
?>
<table style='width:100%;' class='lista_small'>
<tr class='tabla_header'>
<td>Nro. Folio</td>
<td>Especialidad</td>
<td>R.U.T.</td>
<td>Nombre</td>
<td>Tipo</td>
<td>Tipo Contrato</td>
<td>L</td>
<td>O</td>
<td>EX</td>
<td>Editar</td>
<td>&nbsp;</td>
</tr>
<?php 
    if($lista)
        for($i=0;$i<count($lista);$i++)
        {
            ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
            if($lista[$i]['nom_digitar']=='f')
            {
                $color='color:#FF0000;';
            }
            else
            {
                if($lista[$i]['nom_estado']=='-1')
                {
                   $color='background-color:#ffff80;';
                }
                else
                {
                    $color='';
                }
            }
            $lote=false;
            $string_lote="&nbsp;";
            $consulta="select count(nomd_hora)as cantidad,nomd_hora from nomina_detalle
            join nomina USING(nom_id)
            where nom_id=".$lista[$i]['nom_id']." AND (nomd_diag_cod NOT IN ('T')) and nomd_extra is null
            group by nomd_hora";
            $grupo_hrs = cargar_registros_obj($consulta);
            if($grupo_hrs)
            {
                if(count($grupo_hrs)==1)
                {
                    if(($grupo_hrs[0]['cantidad']*1)>1)
                    {
                        $lote=true;
                        $string_lote="<center><img src='iconos/world.png' style='' onClick=''></center>";
                    }
                }
            }
            print("
                <tr class='$clase' style='$color' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"".$clase."\";'>
                    <td style='text-align:center;'>".$lista[$i]['nom_folio']."</td>
                    <td>".$lista[$i]['esp_desc']."</td>");
                    if($lista[$i]['doc_rut']!='(n/a)' AND $lista[$i]['doc_rut']!='')
                    {
                        print("<td style='text-align:right;'>".((formato_rut($lista[$i]['doc_rut'])))."</td>");
                    }
                    else
                    {
                        print("<td style='text-align:right;'>&nbsp;</td>");    	
                    }
                    print("<td>".($lista[$i]['doc_paterno'].' '.$lista[$i]['doc_materno'].' '.$lista[$i]['doc_nombres'])."</td>             
                    <td>".utf8_encode($lista[$i]['nom_motivo'])."</td>
                    <td>".utf8_encode($lista[$i]['nom_tipo_contrato'])."</td>
                    ");    
            
                    if($lista[$i]['libres']*1>0)
                        $color1='green';
                    else
                        $color1='gray';
                    
                    if($lista[$i]['ocupados']*1>0)
                        $color2='red';
                    else
                        $color2='gray';
                    
                    if($lista[$i]['extras_disponibles']*1>0)
                        $color3='orange';
                    else
                        $color3='gray';
                    
                    
                        
    
                    print("
                    <td style='text-align:right;font-weight:bold;color:$color1;'>".$lista[$i]['libres']."</td>
                    <td style='text-align:right;font-weight:bold;color:$color2;'>".$lista[$i]['ocupados']."</td>
                    <td style='text-align:right;font-weight:bold;color:$color3;'>".$lista[$i]['extras_disponibles']."</td>
                    <td>
                        <center>");
                    if($lista[$i]['nom_estado']=='-1')
                    {
                        print("<img src='iconos/pencil.png' style='cursor:pointer;' onClick='mensaje_nomina(-1);'>");
                    }
                    else
                    {
                        print("<img src='iconos/pencil.png' style='cursor:pointer;' onClick='abrir_nomina(\"".$lista[$i]['nom_folio']."\", 1);'>");
                    }
                    print("
                        </center>
                    </td>
                    <td>
                        $string_lote
                    </td>
                    ");
            print('</tr>');
        }
?>
</table>
<?php 
    
    $query='';
    if(_cax(202))
    {
        $query="
	SELECT DISTINCT especialidades.esp_id, especialidades.esp_desc FROM nomina
	JOIN especialidades ON nom_esp_id=esp_id
	LEFT JOIN procedimiento USING (esp_id)
  	WHERE nom_fecha::date='$fecha' AND 
  	procedimiento.esp_id IS NULL
  	";
    }
    
    if(_cax(202) AND _cax(300))
        $query.=' UNION ';	
    
    if(_cax(300))
    {
        $query.="SELECT DISTINCT e1.esp_id, e1.esp_desc FROM especialidades AS e1
        LEFT JOIN procedimiento USING (esp_id)
        WHERE e1.esp_id IN ("._cav(300).") AND 
	procedimiento.esp_id IS NOT NULL";
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    $query='SELECT * FROM ('.$query.') AS foo ORDER BY esp_desc';
	
    $esp=pg_query($query);
	
    $esp_html='';
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    while($o=pg_fetch_assoc($esp))
    {
        if($o['esp_id']*1==$esp_id)
            $sel='SELECTED';
        else
            $sel='';
		
        $esp_html.="<option value='".$o['esp_id']."' ".$sel." >".htmlentities($o['esp_desc'])."</option>";
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    if( $esp_id!=-1 )
    {
        $w_doc="";
        if($doc_id!=-1)
        {
            $doc_reg = cargar_registro("select doc_id,(doc_nombres || ' ' || doc_paterno || ' ' ||doc_materno)as doc_nombres from doctores where doc_id=$doc_id");
            $w_doc=" and doc_id!=$doc_id";
        }
        $query_nomina_doc="
        SELECT DISTINCT doc_id,(doc_nombres || ' ' || doc_paterno || ' ' ||doc_materno)as doc_nombres  from nomina
        LEFT JOIN doctores on nom_doc_id=doc_id
        WHERE nom_fecha::date='$fecha' and nom_esp_id in ($esp_id) $w_doc order by doc_nombres";
    }
    else
    {
        $esp_aut=_cav(311);
        if($esp_aut!='')
        {
            $query_nomina_doc="
            SELECT DISTINCT doc_id,(doc_nombres || ' ' || doc_paterno || ' ' ||doc_materno)as doc_nombres  from nomina
            LEFT JOIN doctores on nom_doc_id=doc_id
            WHERE nom_fecha::date='$fecha' and nom_esp_id in ("._cav(311).") order by doc_nombres";
        }
        else
        {
            $query_nomina_doc="
            SELECT DISTINCT doc_id,(doc_nombres || ' ' || doc_paterno || ' ' ||doc_materno)as doc_nombres  from nomina
            LEFT JOIN doctores on nom_doc_id=doc_id
            WHERE nom_fecha::date='$fecha' order by doc_nombres";
        }
    }
    
    $doc=pg_query($query_nomina_doc);
	
    $doc_html='';
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    while($o=pg_fetch_assoc($doc))
    {
        if($o['doc_id']*1==$doc_id)
            $sel='SELECTED';
        else
            $sel='';
		
        $doc_html.="<option value='".$o['doc_id']."' ".$sel." >".htmlentities($o['doc_nombres'])."</option>";
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
?>
<script>
    mensaje_nomina=function(index)
    {
        if(index=="-1")
        {
            alert("La nomina y cupos de la misma ha sido asignado a otro profesional");
            return;
        }
        
    }
    var html='<select id="esp_id" name="esp_id">';
    html+='<option value="-1">(Todas las Especialidades...)</option>';
    html+="<?php echo $esp_html; ?>";
    html+='</select>';
    //$('select_especialidades').innerHTML=html;
    
    var html='<select id="doc_id" name="doc_id" onChange="listar_nominas(0);">';
        <?php
        if($doc_id==-1 or $doc_id=="")
        {
        ?>
            html+='<option value="-1" SELECTED>(Todos los Medicos o Sala...)</option>';
        <?php
        }
        else
        {
            if($doc_reg)
            {
        ?>
                html+='<option value="<?php echo $doc_reg['doc_id']; ?>" SELECTED><?php echo $doc_reg['doc_nombres']?></option>';
        <?php
            }
            else
            {
        ?>
                html+='<option value="-1" SELECTED>(Todos los Medicos o Sala...)</option>';
        <?php
            }
        }
        ?>
        html+="<?php echo $doc_html; ?>";
    html+='</select>';
    $('select_medico').innerHTML=html
    
    
    
</script>