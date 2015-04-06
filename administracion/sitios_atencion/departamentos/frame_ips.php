<?php
	$idDepto = $_GET['idDepto'];
?>
<html>
	<script src="../../js/prototype.js" type="text/javascript"></script>
	<script src="../../js/effects.js" type="text/javascript"></script>
	<script src="../../js/common.js" type="text/javascript"></script>
    
	<script src="funciones.js" type="text/javascript"></script>

	<link rel="stylesheet" type="text/css" href="../../css/interface.css">
	<body style='font-family: Arial, Helvetica, sans-serif;' onLoad='cargar_listado();'>
	<center>
    <table width=90%>
			<tr class='tabla_header'>
            	<td><b>Ip Asignada</b></td>
                <td>
					<input type="hidden" name="func_id" id="func_id" value="<?=$idDepto?>">
                	<img src='../../iconos/add.png' onClick='muestraForm("centro", "_ingresaIp");' alt='Agregar Ip...' title='Agregar Ip...'>
                </td>
            </tr>
            <tr>
            	<td id="tdContenido">
                	<div id="contenidoP">
					<div id="centro"></div>
					<div id="formBox" > </div>
				</div>
                </td>
            </tr> 
	</table>
<?php
	
	require_once('../../conectar_db.php');

	$tipo = $_GET['tipo'];
	
	/*
	* SWITCH para comprobar a que funcion referenciar
	*/
	
	switch($tipo) {
		case 'ver':
			ver($conn);
			break;
			
	}
	
	
	function ver($conn) {
		$idDepto = $_GET['idDepto'];
		$deptos = pg_query($conn,"SELECT * FROM departamentos_ips WHERE depto_id = $idDepto");
		?>
		<div id="tablaItems">
        <table width=90%>
			                      
        <?php
		while($dato = pg_fetch_array($deptos)) {
			?>
            	<tr>
	        	    	<td align="left">
                        <input type="hidden" name="func_id" id="func_id" value="<?=$idDepto?>">
                			<input type="text" name="ip_<?=$dato['ip']?>" id="ip_<?=$dato['ip']?>" value="<?=$dato['ip']?>" size=15 DISABLED>
                		</td>
	                    <td>
    	                	<img src="../../iconos/pencil.png" onClick="editarIp('<?=$idDepto?>', '<?=$dato['ip']?>');" alt="Editar Ip" title="Editar Ip">
        	            </td>
            	        <td>
                	    <img src="../../iconos/delete.png" onClick="borrarIp('<?=$idDepto?>', '<?=$dato['ip']?>');" alt="Borrar Ip" title="Borrar Ip">
                    	</td>
	            	</tr>
			<?php
		}
		
		?>
        </table>
        </div>
        <?php
	}

?>
<div class="boton" id="guardar_boton" style="display: none;">
	<table><tr><td>
	<img src="../../iconos/user_go.png">
	</td><td>
	<span id="guardar_texto">Guardar Cambios...</span>
	</td></tr></table>
	</div>
</center>
</body>
</html>