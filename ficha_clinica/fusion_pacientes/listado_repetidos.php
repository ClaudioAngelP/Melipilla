<?php 
    require_once('../../conectar_db.php');
    $xls=($_POST['xls'] AND $_POST['xls']*1==1);
    $_ruts_fusionados=Array();
    
    function fusion_paciente($pac_id2, $pac_id)
    {
        pg_query("START TRANSACTION;");
        $tmp=Array();
	$tmp['receta']=cargar_registros_obj("SELECT receta_id FROM receta WHERE receta_paciente_id=$pac_id2");
	pg_query("UPDATE receta SET receta_paciente_id=$pac_id WHERE receta_paciente_id=$pac_id2");
	$tmp['cargo_pyxis']=cargar_registros_obj("SELECT id FROM cargo_pyxis WHERE pac_id=$pac_id2");
	pg_query("UPDATE cargo_pyxis SET pac_id=$pac_id WHERE pac_id=$pac_id2");
	$tmp['boletines']=cargar_registros_obj("SELECT bolnum FROM boletines WHERE pac_id=$pac_id2");
	pg_query("UPDATE boletines SET pac_id=$pac_id WHERE pac_id=$pac_id2");
        $tmp['creditos']=cargar_registros_obj("SELECT crecod FROM creditos WHERE pac_id=$pac_id2");
	pg_query("UPDATE creditos SET pac_id=$pac_id WHERE pac_id=$pac_id2");
	$tmp['hospitalizacion']=cargar_registros_obj("SELECT hosp_id FROM hospitalizacion WHERE hosp_pac_id=$pac_id2");
	pg_query("UPDATE hospitalizacion SET hosp_pac_id=$pac_id WHERE hosp_pac_id=$pac_id2");
		
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
    
    function fixpac($rut)
    {
        GLOBAL $_ruts_fusionados;
	if(isset($_ruts_fusionados[$rut])) return;
	
        $_ruts_fusionados[$rut]='OK';
	
	$q=cargar_registros_obj("SELECT * FROM pacientes WHERE ltrim(pac_rut,'0')='$rut'");
		
	$id=0;
	$fic=null;
		
	for($i=0;$i<sizeof($q);$i++)
        {
            if($fic==null OR $q[$i]['pac_ficha']*1<$fic*1)
            {
                $id=$q[$i]['pac_id']*1;
		$fic=$q[$i]['pac_ficha'];
            }
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
	
    $q=cargar_registros_obj("
    select * from 
    (
        select ltrim(pac_rut,'0') AS rut, count(*) AS cnt, array_to_string(array_agg(case when pac_ficha='0' then null else pac_ficha end ),'|','X') AS fichas
        from pacientes
        where pac_rut is not null and not pac_rut='0000000-0' and not pac_rut='' group by ltrim(pac_rut,'0')
    ) AS foo 
    join pacientes on rut=ltrim(pac_rut,'0')
    where cnt>1 and (rut!='' and rut!='*')
    ORDER BY cnt DESC;", true);
	
    if($xls)
    {
        header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: filename=\"Pacientes_RUN_Repetidos.xls\";");
	print("
	<table>
            <tr>
                <td colspan=4><b>Pacientes RUN Repetido</b></td>
            </tr>
            <tr>
                <td colspan=2>Fecha Emisi&oacute;n:</td>
		<td>".date('d/m/Y H:i:s')."</td>
            </tr>
	</table>
	");
    }
?>
<table style='width:100%;'>
<tr></tr>
<tr class='tabla_header'>
<td>#</td>
<td>R.U.N.</td>
<td>Regs.</td>
<td>Fichas (Cant.)</td>
<td>Paterno</td>
<td>Materno</td>
<td>Nombres</td>
<td>Fec.Nac.</td>
<td>Reparar</td>
</tr>
<?php 
$fixcnt=0;
if($q)
    for($i=0;$i<sizeof($q);$i++)
    {
        $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
	list($cnt,$txt_fichas,$fics)=ficfix($q[$i]['fichas']);
	print("
	<tr class='$clase'onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"$clase\";'>
        <td style='text-align:right;'>".($i+1)."</td>
	<td style='text-align:right;font-weight:bold;'>".$q[$i]['rut']."</td>
	<td style='text-align:center;'>".$q[$i]['cnt']."</td>
	<td style='text-align:center;'>".$txt_fichas." ($cnt)</td>
	<td style='text-align:left;'>".$q[$i]['pac_appat']."</td>
	<td style='text-align:left;'>".$q[$i]['pac_apmat']."</td>
	<td style='text-align:left;'>".$q[$i]['pac_nombres']."</td>
        <td style='text-align:center;'>".$q[$i]['pac_fc_nac']."</td>
        <td><center><img src='iconos/wrench.png' onClick='reparar_paciente(\"".$q[$i]['rut']."\");' style='cursor:pointer;' /></center></td>
        </tr>
	");
        //if($cnt==1 AND $fics[0]!='X' AND $fics[0]*1>0 AND $fics[0]*1<900000) { fixpac($q[$i]['rut']); $fixcnt++; }	
    }
?>
</table>