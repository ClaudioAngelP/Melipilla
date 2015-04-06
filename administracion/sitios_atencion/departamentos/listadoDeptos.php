<?php
	require_once('../../conectar_db.php');
	
	/*
	* Recepcion de la variable tipo, recibida por get
	*/

	$tipo = $_GET['tipo'];
	
	/*
	* SWITCH para comprobar a que funcion referenciar
	*/
	
	switch($tipo) {
		case 'listado':
			listado($conn);
			break;
			
		case 'depto':
			depto($conn);
			break;
			
		case 'listarIps':
			listarIps($conn);
			break;
			
		case 'listarIps2':
			listarIps2($conn);
			break;
			
		case 'ingreso_edicion_depto':
			ingreso_edicion_depto($conn);
			break;
			
		case 'delete':
			eliminar($conn);
			break;
	}
	
	/*
	* Fin del SWITCH
	*/
	
	function listado($conn) {
		$busqueda = iconv("UTF-8", "ISO-8859-1", $_GET['buscar']);
		
		if(trim($busqueda)=="") {
			$buscar="";
		} else {
			$buscar="WHERE nombre ILIKE '%$busqueda%'";
		}
	
		
?>	
	<table width=320>
	<tr class='tabla_header'>
    	<td><b>Departamentos</b></td>
    </tr>
	
<?php
	
		$departamentos = pg_query($conn," SELECT * FROM departamentos $buscar ");
		$i = 0;
		
		while($dato = pg_fetch_array($departamentos)) {
			if(($i%2)==0) {
				$clase='tabla_fila';
			} else {
				$clase='tabla_fila2';
			}
		
?>
		<tr class='<?=$clase?>' onClick='seleccionar_depto(<?=$dato['id']?>,1);' onMouseOver='this.className="mouse_over"' onMouseOut='this.className="<?=$clase?>"'>
		<td><?=htmlentities($dato['nombre'])?></td>
		</tr>
<?php
		$i++;
		}
	
?>
</table>
<?php

	}
	
	
	function depto($conn) {
		$busqueda = ($_GET['buscar']*1);
	
		$deptos = pg_query($conn,"SELECT * FROM departamentos WHERE id = $busqueda LIMIT 1 ");
	
		$datos = pg_fetch_row($deptos);
	
		for($i=0;$i<count($datos);$i++) {
			$datos[$i] = htmlentities($datos[$i]);
		}
		
		$deptos = pg_query($conn,"SELECT * FROM departamentos WHERE id = $busqueda LIMIT 1 ");
		$deptos_ips_array = pg_fetch_row($deptos);
		if(!empty($deptos_ips_array)) {
			$arrayIp = array();
			$j = 0;
			$deptos_ips = pg_query($conn,"SELECT * FROM departamentos_ips WHERE depto_id = $busqueda ");
			while($dat_deptos_ips = pg_fetch_array($deptos_ips)) {
				$arrayIp[$j] = $dat_deptos_ips['ip'];
				$j++;
			}
		}
		$i +=1;
		$datos[$i-1] = $arrayIp;
		
		print(json_encode($datos));
	}
	
	function ingreso_edicion_depto($conn) {
		$id = ($_GET['func_id']*1);
		$nombre = iconv("UTF-8", "ISO-8859-1", $_GET['func_nombre']);
		
		if($id != 0) {
			pg_query($conn, "UPDATE departamentos SET nombre='$nombre' WHERE id=$id ");
		} else {
			pg_query($conn, "INSERT INTO departamentos VALUES (DEFAULT, '$nombre')");
		}
		
		print('1');
	}

	function eliminar($conn) {
		//$rut = $_GET['id'];
		$id = ($_GET['func_id']*1);
		if(!empty($id)) {
			pg_query($conn, "DELETE from departamentos WHERE id='$id'");
			pg_query($conn, "DELETE from departamentos_ips WHERE depto_id = '$id'");
		} else {
			return;
		}
		print('1');
	}
	
	
	
?>