<?php 
    require_once('../../conectar_db.php');
    $pac_rut=pg_escape_string($_POST['pac_rut']);
    $_ruts_fusionados=Array();
    
    
    function fusion_paciente($pac_id2, $pac_id)
    {
        pg_query("START TRANSACTION;");
        $reg_ficha=cargar_registro("SELECT pac_ficha from pacientes where pac_id=$pac_id");
        if($reg_ficha)
        {
            $pac_ficha=$reg_ficha['pac_ficha'];
        }
        $tmp=Array();
        

        $tmp['receta']=cargar_registros_obj("SELECT receta_id FROM receta WHERE receta_paciente_id=$pac_id2");
        pg_query("UPDATE receta SET receta_paciente_id=$pac_id WHERE receta_paciente_id=$pac_id2");

        $tmp['nomina_detalle']=cargar_registros_obj("SELECT nomd_id FROM nomina_detalle WHERE pac_id=$pac_id2");
        pg_query("UPDATE nomina_detalle SET pac_id=$pac_id WHERE pac_id=$pac_id2");

        $tmp['solicitud_examen']=cargar_registros_obj("SELECT sol_exam_id FROM solicitud_examen WHERE sol_pac_id=$pac_id2");
        pg_query("UPDATE solicitud_examen SET sol_pac_id=$pac_id WHERE sol_pac_id=$pac_id2");

        $tmp['ficha_espontanea']=cargar_registros_obj("SELECT fesp_id FROM ficha_espontanea WHERE pac_id=$pac_id2");
        pg_query("UPDATE ficha_espontanea SET pac_id=$pac_id,pac_ficha='$pac_ficha' WHERE pac_id=$pac_id2");
        
        $tmp['archivo_movimientos']=cargar_registros_obj("SELECT am_id FROM archivo_movimientos WHERE pac_id=$pac_id2");
        pg_query("UPDATE archivo_movimientos SET pac_id=$pac_id,pac_ficha='$pac_ficha' WHERE pac_id=$pac_id2");
        
        $tmp['interconsultas']=cargar_registros_obj("SELECT inter_id FROM interconsulta WHERE inter_pac_id=$pac_id2");
        pg_query("UPDATE interconsulta SET inter_pac_id=$pac_id WHERE inter_pac_id=$pac_id2");
        
        $tmp['hospitalizacion']=cargar_registros_obj("SELECT hosp_id FROM hospitalizacion WHERE hosp_pac_id=$pac_id2");
        pg_query("UPDATE hospitalizacion SET hosp_pac_id=$pac_id WHERE hosp_pac_id=$pac_id2");
        
        $tmp['orden_atencion']=cargar_registros_obj("SELECT oa_id FROM orden_atencion WHERE oa_pac_id=$pac_id2");
        pg_query("UPDATE orden_atencion SET oa_pac_id=$pac_id WHERE oa_pac_id=$pac_id2");
        
        $tmp['fap_pabellon']=cargar_registros_obj("SELECT fap_id FROM fap_pabellon WHERE pac_id=$pac_id2");
        pg_query("UPDATE fap_pabellon SET pac_id=$pac_id WHERE pac_id=$pac_id2");
        
        $tmp['boletines']=cargar_registros_obj("SELECT bolnum FROM boletines WHERE pac_id=$pac_id2");
        pg_query("UPDATE boletines SET pac_id=$pac_id WHERE pac_id=$pac_id2");
        
        $tmp['creditos']=cargar_registros_obj("SELECT crecod FROM creditos WHERE pac_id=$pac_id2");
        pg_query("UPDATE creditos SET pac_id=$pac_id WHERE pac_id=$pac_id2");
        

        //$tmp['cargo_pyxis']=cargar_registros_obj("SELECT id FROM cargo_pyxis WHERE pac_id=$pac_id2");
        //pg_query("UPDATE cargo_pyxis SET pac_id=$pac_id WHERE pac_id=$pac_id2");

        //$tmp['inte_hospitalizados ']=cargar_registros_obj("SELECT intehosp_id FROM inte_hospitalizados WHERE intehosp_pac_id=$pac_id2");
        //pg_query("UPDATE inte_hospitalizados SET intehosp_pac_id=$pac_id, hosp_estado_leido=0 WHERE intehosp_pac_id=$pac_id2");

        //$tmp['inte_pacientes']=cargar_registros_obj("SELECT inpac_id FROM inte_pacientes WHERE inpac_pac_id=$pac_id2");
        //pg_query("UPDATE inte_pacientes SET rut_paciente='', dv='', nombres='NN', ap_paterno='NN', ap_materno='NN', nro_ficha=NULL, estado=0 WHERE inpac_pac_id=$pac_id2");

        /* create table fusion_pacientes (
        id bigserial, 
        fecha timestamp without time zone, 
        func_id bigint, 
        pac_id bigint, 
        pac_id2 bigint, 
        datos_paciente text, 
        datos_modificados text
        );
        */

        $datos_pac=pg_escape_string(json_encode(cargar_registro("SELECT * FROM pacientes WHERE pac_id=$pac_id2", true)));
        $datos_mod=pg_escape_string(json_encode($tmp));

        $func_id=$_SESSION['sgh_usuario_id']*1;

        pg_query("INSERT INTO fusion_pacientes VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, $pac_id2, '$datos_pac', '$datos_mod');");
        pg_query("DELETE FROM pacientes WHERE pac_id=$pac_id2;");
        pg_query("COMMIT;");
    }
    
    function fixpac($rut, $id)
    {
        GLOBAL $_ruts_fusionados;
        if(isset($_ruts_fusionados[$rut])) return;

        $_ruts_fusionados[$rut]='OK';
        if($rut!="1")
        {
            $q=cargar_registros_obj("SELECT * FROM pacientes WHERE ltrim(trim(pac_rut),'0')='$rut'");
        }
        else
        {
            $strin_pac=pg_escape_string($_POST['string_pac']);
            if($strin_pac!="")
            {
                $strin_pac=trim($strin_pac, '|');
                $strin_pac=str_replace("|",",",$strin_pac);
            }
            $q=cargar_registros_obj("SELECT * FROM pacientes WHERE pac_id in ($strin_pac)");
        }

        for($i=0;$i<sizeof($q);$i++)
        {
            if($q[$i]['pac_id']*1!=$id*1)
            {
                fusion_paciente($q[$i]['pac_id'], $id);
            }
        }
    }
    
    function ficfix($str)
    {
        $f=explode('|', $str);
        $nstr='';

        $tmp_array=Array();

        for($k=0;$k<sizeof($f);$k++)
        {
            if($f[$k]!='')
                $tmp_array[]=$f[$k];
        }

        $tmp_array=array_unique($tmp_array);

        return array(sizeof($tmp_array),implode('/', $tmp_array),$tmp_array);
        //return $str;
    }
    
    if(!isset($_POST['llamada']))
    {
        if(isset($_POST['pac_id']) AND $_POST['pac_id']*1>0)
        {
            $pac_id=$_POST['pac_id']*1;
            fixpac($pac_rut,$pac_id);
            print("<script>alert('Registros fusionados exitosamente.');</script>");
        }
	
        $q=cargar_registros_obj("
        select * from (
        select ltrim(pac_rut,'0') AS rut, count(*) AS cnt, array_to_string(array_agg(case when pac_ficha='0' then null else pac_ficha end),'|','X') AS fichas
        from pacientes
        where ltrim(trim(pac_rut),'0')='$pac_rut'
        group by ltrim(pac_rut,'0')
        ) AS foo 
        join pacientes on rut=ltrim(pac_rut,'0')
        left join comunas using (ciud_id)
        ORDER BY pac_ficha;", true);
    }
    else
    {
        $llamada=$_POST['llamada']*1;
        if($llamada==1)
        {
            $tipo_busqueda=$_POST['tipo_busqueda']*1;
            $strin_pac=pg_escape_string($_POST['string_pac']);
            if($strin_pac!="")
            {
                $strin_pac=trim($strin_pac, '|');
                $strin_pac=str_replace("|",",",$strin_pac);
            }
            $q=cargar_registros_obj("
            select * from (
            select ltrim(pac_rut,'0') AS rut, count(*) AS cnt, array_to_string(array_agg(case when pac_ficha='0' then null else pac_ficha end),'|','X') AS fichas
            from pacientes
            where pac_id in ($strin_pac)
            group by ltrim(pac_rut,'0')
            ) AS foo 
            join pacientes on rut=ltrim(pac_rut,'0')
            left join comunas using (ciud_id)
            where pac_id in ($strin_pac)
            ORDER BY pac_ficha;", true);
        }
    }
?>
<table style='width:100%;'>
<tr></tr>
<tr class='tabla_header' style='font-size:10px;'>
<td>#</td>
<td>R.U.N.</td>
<td>Ficha</td>
<td>Paterno</td>
<td>Materno</td>
<td>Nombres</td>
<td>Fec.Nac.</td>
<td>Direcci&oacute;n</td>
<td>Comuna</td>
<td>Fonos</td>
</tr>
<?php 
$fixcnt=0;
$fic=null;
if($q)
    for($i=0;$i<sizeof($q);$i++)
    {
        if(($fic==null AND $q[$i]['pac_ficha']*1>0)  OR ($q[$i]['pac_ficha']*1>0 AND $q[$i]['pac_ficha']*1<$fic))
        {
            $fic=$q[$i]['pac_ficha'];
	}
    }

if($q)
    for($i=0;$i<sizeof($q);$i++)
    {
        $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
	
        list($cnt,$txt_fichas,$fics)=ficfix($q[$i]['fichas']);
	
	if($q[$i]['pac_ficha']!='' and $q[$i]['pac_ficha']!='0')
        {
            $sel=($q[$i]['pac_ficha']==$fic)?'CHECKED':'';
            $input="<input type='radio' id='pac_id_".$q[$i]['pac_id']."' name='pac_id' style='font-size:9px;' value='".$q[$i]['pac_id']."' $sel />";
	}
        else
            $input='';
	
        //<tr class='$clase' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"$clase\";' onClick='cargar_run(\"".$q[$i]['rut']."\");'>
	print("
	<tr class='$clase' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"$clase\";' onClick=''>
        <td style='text-align:right;'>".($i+1)."</td>
	<td style='text-align:right;font-weight:bold;'>".$q[$i]['rut']."</td>");
        if($q[$i]['pac_ficha']!='0')
        {
            print("<td style='text-align:center;'>".$q[$i]['pac_ficha']." $input</td>");
        }
        else
        {
            print("<td style='text-align:center;'>&nbsp;</td>");
        }
        print("
	<td style='text-align:left;'>".$q[$i]['pac_appat']."</td>
	<td style='text-align:left;'>".$q[$i]['pac_apmat']."</td>
	<td style='text-align:left;'>".$q[$i]['pac_nombres']."</td>
	<td style='text-align:center;'>".$q[$i]['pac_fc_nac']."</td>
	<td style='text-align:center;'>".$q[$i]['pac_direccion']."</td>
	<td style='text-align:center;'>".$q[$i]['ciud_desc']."</td>
	<td style='text-align:center;'>".$q[$i]['pac_fono']."</td>
	</tr>
	");

	//if($cnt==1 AND $fics[0]!='X' AND $fics[0]*1>0 AND $fics[0]*1<900000) { fixpac($q[$i]['rut']); $fixcnt++; }	
    }
?>
</table>
<?php if($q AND count($q)>1) { ?>
<center>
    <?php
    if(!isset($_POST['llamada']))
    {
    ?>    
        <input type='button' value='[[ Fusionar Registros... ]]' onClick='fusionar_paciente("<?php echo $pac_rut; ?>");' />
    <?php
    }
    else
    {
    ?>
        <input type='button' value='[[ Fusionar Registros... ]]' onClick='fusionar_paciente(1);' />
    <?php
    }
    ?>
</center>
<?php } ?>
