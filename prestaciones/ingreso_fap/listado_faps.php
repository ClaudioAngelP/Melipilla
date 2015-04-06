<?php

  require_once('../../conectar_db.php');
	function cut($str, $len) {
		
		$str=trim($str);		
		
		if(strlen($str)>$len-3) {
			$str=substr($str,0,$len-3).'...';
		} 
		
		return $str;
			
	}		


  function img($t) {
    if($t=='t') return '<center><img src="iconos/tick.png" 
                                  width=8 height=8></center>';
    else return '<center><img src="iconos/cross.png" 
                                  width=8 height=8></center>';
  }


  $fecha = pg_escape_string($_GET['fecha']);
  $tipo = ($_GET['tipo']*1);
  
  switch($tipo) {

		case 1: $tipo_w='(fap_tipo_atencion=1)'; break;
		case 2: $tipo_w='(fap_tipo_atencion=2)'; break;
		case 3: $tipo_w='(fap_tipo_atencion=3 OR fap_tipo_atencion=4)'; break;
		case 5: $tipo_w='(fap_tipo_atencion=5)'; break;
  	
  }
  
  if($tipo!=5) {
  	
  $lista = cargar_registros_obj("
	  
	  SELECT 
	  
			fap.*, 
			pacientes.pac_appat, 
			pacientes.pac_apmat, 
			pacientes.pac_nombres, 
			pacientes.pac_id, 
			pacientes.pac_rut, 
			pacientes.pac_ficha
			
	  FROM fap
	  JOIN pacientes ON fap_pac_id=pac_id
	  WHERE fap_fecha::date='$fecha' AND $tipo_w
	  ORDER BY fap_ftipo, fap_fnumero
	  
  ", true);
  
  } else {

  $lista = cargar_registros_obj("
	  
	  SELECT 
	  
			fap_pabellon.*, 
			pacientes.pac_appat, 
			pacientes.pac_apmat, 
			pacientes.pac_nombres, 
			pacientes.pac_id, 
			pacientes.pac_rut, 
			pacientes.pac_ficha,
			fappab_pabellones.*, 
			funcionario.*,
			date_trunc('second',fap_fecha)::time AS fap_hora,
			prevision.*
			
	  FROM fap_pabellon
	  JOIN pacientes USING (pac_id)
	  LEFT JOIN prevision ON prevision.prev_id=pacientes.prev_id		
	  LEFT JOIN fappab_pabellones ON fap_numpabellon=fapp_id		
	  LEFT JOIN funcionario ON funcionario.func_id=fap_pabellon.func_id		
	  WHERE fap_fecha::date='$fecha'
	  ORDER BY fap_fnumero ASC
	  
  ", true);
  	
  }

?>
<html>
<title>Listado de FAPS</title>

<?php cabecera_popup('../..'); ?>

<body class='fuente_por_defecto popup_background' onLoad='window.print(); window.close();'>
<center>
<h2>Listado de FAP <?php echo $fecha; ?></h2>
<br /><br />

<table style='width:100%;' class='lista_small' border='1'>

<tr class='tabla_header'>
<td>N&uacute;mero</td>
<?php if($tipo==5) { ?> <td>Hora</td> <?php } ?>
<td>Nro. Ficha</td>
<td>R.U.T.</td>

<?php if($tipo!=5) { ?>

<td>Paterno</td>
<td>Materno</td>
<td>Nombres</td>

<?php } else { ?>

<td>Nombre</td>

<?php } ?>

<td>Previsi&oacute;n</td>
<td width="50">C&oacute;digo</td>
<td>Desc.</td>

</tr>

<?php 

  if($lista)
  for($i=0;$i<count($lista);$i++) {

    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
	$presta=cargar_registros_obj("SELECT DISTINCT * FROM fap_prestacion 
			LEFT JOIN codigos_prestacion ON fappr_codigo=codigo
			WHERE fap_id=".$lista[$i]['fap_id']." order by fappr_id asc");

    print("
    <tr class='$clase'
    onMouseOver='this.className=\"mouse_over\";'
    onMouseOut='this.className=\"".$clase."\";'>
    <td style='text-align:center;font-weight:bold;'>".$lista[$i]['fap_fnumero']."</td>
    ");
    
	 if($tipo==5) {
	    print("
	    	<td style='text-align:center;'>".$lista[$i]['fap_hora']."</td>
	    ");
	 }    
    
    print("
	    <td style='text-align:center;'>".$lista[$i]['pac_ficha']."</td>
	    <td style='text-align:right;font-weight:bold;'>".$lista[$i]['pac_rut']."</td>
    ");
    
    if($tipo!=5)
	    print("
	    <td style='font-size:8px;'>".(($lista[$i]['pac_appat']))."</td>
	    <td style='font-size:8px;'>".(($lista[$i]['pac_apmat']))."</td>
	    <td style='font-size:8px;'>".(($lista[$i]['pac_nombres']))."</td>
	    ");
    else 
	    print("
	    <td style='font-size:8px;'>".trim((($lista[$i]['pac_appat']))."
	    ".(($lista[$i]['pac_apmat']))."
	    ".(($lista[$i]['pac_nombres'])))."</td>
	    ");    
/*
//EGF
	$presta[0]['glosa']=cut($presta[0]['glosa'], 35);

	 print("
	 <td style='text-align:center;'>".$lista[$i]['prev_desc']."</td>
    <td style='text-align:center;font-weight:bold;'>".$presta[0]['fappr_codigo']."</td>
    <td style='text-align:center;font-size:9px;'>".htmlentities($presta[0]['glosa'])."</td>
    </tr>
    ");

*/
//EGF
print("
	 <td style='text-align:center;'>".$lista[$i]['prev_desc']."</td>");
echo "<td colspan=2>
        <table> ";
       for ($x = 0; $x<3;$x++)
       {
         $presta[0]['glosa']=cut($presta[$x]['glosa'], 35);
         echo "<tr><td style='text-align:center;font-weight:bold;' width='50'>".$presta[$x]['fappr_codigo']."</td>
         <td style='text-align:left;font-size:9px;'>".htmlentities($presta[$x]['glosa'])."</td></tr>";
       }
echo   "</table>
      </td>";
//FIN EGF

  }

?>
</table>

</center>

</body>
</html>
