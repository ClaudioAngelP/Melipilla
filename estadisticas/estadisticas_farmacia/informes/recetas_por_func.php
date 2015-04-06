<?php  require_once('../../../conectar_db.php');
	require_once('../../infogen.php');
	
	$campos=Array(
			Array('fecha1', 'Fecha Inicio', 1),
			Array('fecha2', 'Fecha T&eacute;rmino', 1)
		);

	$query="
		SELECT  UPPER(func_rut)AS func_rut, UPPER(func_nombre)AS func_nombre,count(*)AS cnt 
		FROM receta
		LEFT JOIN funcionario ON func_id=receta_func_id
		LEFT JOIN centro_costo ON centro_ruta=receta _centro_ruta
		WHERE receta_fecha_emision BETWEEN '[%fecha1] 00:00:00' AND '[%fecha1] 23:59:59'
		GROUP BY  func_rut,func_nombre
		ORDER BY func_nombre,cnt
		";

	$formato=Array(
			Array('func_rut', 'R.U.T.', 0, 'left'),
ç			Array('func_nombre', 'Nombre Funcionario', 0, 'left'),
			Array('cnt', 'Cantidad Recetas', 1, 'right')
		);

	ejecutar_consulta();

	procesar_formulario('Recetas por Funcionario');


?>
