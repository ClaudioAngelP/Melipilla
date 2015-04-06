<?php 

	require_once('../../conectar_db.php');
	
	$filtro=$_POST['filtro']*1;
	
	if($filtro==1) {
		$d=cargar_registros_obj("
		select * from (
			select orserv_glosa, count(*) AS cuenta 
			from orden_servicios 
			join orden_compra on orserv_orden_id=orden_id and (orden_numero ilike '%-CM__' OR orden_numero ILIKE '%-SE__')
			WHERE NOT orserv_glosa=''
			group by orserv_glosa
			
		) AS foo 
		left join articulo_nombres on artn_nombre=orserv_glosa
		left join articulo using (art_id)
		order by cuenta DESC;
		", true);
	} else if ($filtro==2) {
		$d=cargar_registros_obj("
		SELECT * FROM (
		select *, (SELECT count(*) FROM orden_detalle WHERE ordetalle_art_id=articulo_nombres.art_id) AS cuenta 
		FROM articulo_nombres
		left join articulo using (art_id)
		left join funcionario on artn_func_id=func_id
		) AS foo
		order by cuenta DESC;
		", true);
	}
	


?>

<table style='width:100$;'>
	<tr class='tabla_header'>
		<td style='width:3%;'>#</td>
		<td style='width:40%;'>Descripci&oacute;n Art&iacute;culo</td>
		<td>Ocurrencias en O.C.</td>
		<td>C&oacute;digo Interno</td>
		<td style='width:25%;'>Glosa</td>
		<td style='width:5%;'>MODIFICAR</td>
	</tr>
	
<?php 

	$script='';

	if($d)
	for($i=0;$i<sizeof($d);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		if($filtro==1) {
			$accion="modificar_glosa(".$i.");";
			$glosa=$d[$i]['orserv_glosa'];
		} else {
			$accion="alert(\"CONSULTE AL ADMINISTRADOR.\");";
			$glosa=$d[$i]['artn_nombre'].'<br /><span style="font-size:9px;"><i>Asignado el <b>'.substr($d[$i]['artn_fecha_asigna'],0,16).'</b> por <b>'.$d[$i]['func_nombre'].'</b>.</i></span>';
		}

		print("
			<tr class='$clase'
			onMouseOver='this.className=\"mouse_over\";'
			onMouseOut='this.className=\"$clase\";'
			>
			<td style='text-align:right;font-weight:bold;'>".($i+1)."</td>
			<td style='text-align:justify;'>".$glosa."</td>
			<td style='text-align:right;'>".$d[$i]['cuenta']."</td>
			<td style='text-align:right;'><center>
			<input type='hidden' id='art_id_$i' name='art_id_$i' value='".$d[$i]['art_id']."' />
            <input type='text' id='codigo_art_$i' name='codigo_art_$i' size=11 style='font-size:10px;' value='".$d[$i]['art_codigo']."' />
			</td>
			<td style='text-align:justify;color:green;' id='glosa_$i'>".$d[$i]['art_glosa']."</td>
			<td><center><input type='button' style='display:none;' id='guardar_$i' value='[MOD]' onClick='$accion' /></center></td>
			</tr>
		");
		
		$script.="
                
				  autocompletar_medicamentos_$i = new AutoComplete(
				  'codigo_art_$i', 
				  'autocompletar_sql.php',
				  function() {
					if($('codigo_art_$i').value.length<3) return false;
				  
					return {
					  method: 'get',
					  parameters: 'tipo=buscar_arts&codigo='+encodeURIComponent($('codigo_art_$i').value)
					}
					
				  }, 'autocomplete', 550, 200, 250, 1, 3, abrir_articulo);

                
                ";

		
	}

?>

</table>


<script>

glosas=<?php echo json_encode($d); ?>;

<?php echo $script; ?>

</script>
