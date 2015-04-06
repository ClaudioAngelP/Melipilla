<?php
    require_once('../../conectar_db.php');
    $autf_id=$_POST['autf_id']*1;
    if($autf_id==102 OR $autf_id==98)
        exit('DATOS PROTEGIDOS.');
    $lista=cargar_registros_obj("
    SELECT *,
    (case when autfp_fecha_final is null then
    (CASE WHEN (autfp_vigente AND autfp_fecha_inicio<=CURRENT_DATE) THEN true ELSE false END)
    else
    (CASE WHEN (autfp_vigente AND autfp_fecha_inicio<=CURRENT_DATE AND autfp_fecha_final>CURRENT_DATE) THEN true ELSE false END)
    end)as vigente
    FROM autorizacion_farmacos_pacientes
    JOIN autorizacion_farmacos USING (autf_id)
    JOIN pacientes USING (pac_id)
    JOIN funcionario USING (func_id)
    WHERE autf_id=$autf_id
    ORDER BY pac_appat, pac_apmat, pac_nombres
    LIMIT 1500;
    ", true);
    $aut=cargar_registro("SELECT * FROM autorizacion_farmacos WHERE autf_id=$autf_id;");
?>
<table style='width:100%;'>
    <tr class='tabla_header'>
    <td>#</td>
    <td>RUT</td>
    <td>Ficha</td>
    <td>Nombre</td>
    <td>Funcionario</td>
    <?php if($aut['autf_validar']=='t') { ?><td>M&eacute;dico Autoriza</td><?php } ?>
    <td>Fecha Inicial</td>
    <td>Fecha Final</td>
    <td>GES</td>
    <td>Vigente</td>
    <td>Activada</td>
    <td>Acciones</td>
    </tr>
    <?php 
    if($lista)
        for($i=0;$i<sizeof($lista);$i++)
        {
		
		$class=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		print("
			<tr class='$class'>
			<td style='text-align:right;'>".($i+1)."</td>
			<td style='text-align:right;font-weight:bold;'>".$lista[$i]['pac_rut']."</td>
			<td style='text-align:center;font-weight:bold;'>".$lista[$i]['pac_ficha']."</td>
			<td>".trim($lista[$i]['pac_appat']." ".$lista[$i]['pac_apmat']." ".$lista[$i]['pac_nombres'])."</td>
			<td>".$lista[$i]['func_nombre']."</td>
			");
			
			
		if($aut['autf_validar']=='t') {
			if($lista[$i]['doc_nombres']!='')
				print("<td style='color:green;'>".$lista[$i]['doc_nombres']."</td>");
			else
				print("<td style='color:red;'><i>(Pendiente...)</i></td>");
			
		}
			
		print("
			<td style='text-align:center;'>".$lista[$i]['autfp_fecha_inicio']."</td>
			<td style='text-align:center;'>".$lista[$i]['autfp_fecha_final']."</td>
			<td style='text-align:center;'>".$lista[$i]['autfp_ges']."</td>
			<td style='text-align:center;'><center><img src='iconos/".($lista[$i]['vigente']=='t'?'tick':'cross').".png' style='width:10px;height:10px;' /></center></td>
			<td style='text-align:center;'><center><img src='iconos/".($lista[$i]['autfp_vigente']=='t'?'tick':'cross').".png' style='width:10px;height:10px;' /></center></td>
			<td><center><img src='iconos/delete.png' onClick='eliminar_paciente(".$lista[$i]['pac_id'].");' />
		");
		
		if(_cax(503)){
		
			print("<img src='iconos/pencil.png' />");
		}
		
		print("</center></td></tr>");
		
	}

?>	
	
	
</table>

