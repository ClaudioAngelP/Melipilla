<?php

	$op = $_GET['op'];
	
	require_once('../../conectar_db.php');
	
	switch($op) {
		case 'post':
			funcPost($conn);
			break;
			
		case 'ver':
			ver();
			break;
			
		case 'remueveForm':
			remueveForm();
			break;
			
		case 'del':
			eliminarItem($conn);
			break;
			
		case 'update':
			actualizar($conn);
			break;
			
		default:
			break;
	}
	
	function ver() {
	$idParent = ($_GET['idParent']);
?>
<form id="_ingresaIp" action="." method="post" name="_ingresaIp" onsubmit="ingresaIp(this.id);return false;">
<table width="80%" border=0>
	<tr class="tabla_fila">
		<td style="font-size: 10px;" width="28%">
        <input type="hidden" id="ipId" name="ipId" size="15" value="<?=$idParent?>" class="text">
			<input type="text" id="ipNew" name="ipNew" size="15" class="text">
		</td>
		<td>
			<center>
			<input type="image" src="../../iconos/accept.png" alt="Ingresar Ip..." title="Ingresar Ip...">		
			</center>
		</td>
		<td>
			<center>
			<img src="../../iconos/cancel.png" onClick='cancelaIngresoIp("_ingresaIp");' alt="Cancelar..." title="Cancelar...">			
			</center>
		</td>
	</tr>
	</table>
</form>

<script> $('ipNew').focus(); </script>

<?php
	}
	
	function funcPost($conn) {
		$ipId = $_POST['ipId'];
		$ipNew = $_POST['ipNew'];
		if(!empty($ipId) && !empty($ipNew)) {
			if(($items = pg_query($conn, "INSERT INTO departamentos_ips VALUES ('$ipId', '$ipNew')"))) {
			echo "Dato agregado satisfactoriamente";
			
			$deptos = pg_query($conn,"SELECT * FROM departamentos_ips WHERE depto_id = $ipId ");
			?>
                 <table width=100%>
        	<?php
				
				while($dato = pg_fetch_array($deptos)) {
				?>
            		<tr>
	            		<td align="left">
                			<input type="text" name="ip" id="ip" value="<?=$dato['ip']?>" size=15 DISABLED>
	                	</td>                       
    	        	</tr>
				<?php
				}
				?>
        		</table>
        <?php
			} else {
				echo "no";
			}
			
		} else {
			echo "Los datos no fueron guardados";
		}
		
	}
	
	function remueveForm() {}
	
	function eliminarItem($conn) {
		$idDepto	= $_GET['idDepto'];
		$ip 		= $_GET['ip'];
		if(!empty($idDepto) && !empty($ip)) {
			if($items = pg_query($conn, "DELETE FROM departamentos_ips WHERE depto_id = '$idDepto' AND ip = '$ip'")) {
				echo "Dato eliminado satisfactoriamente";
				?>
				<br  />
				<center>
					<img src="imagenes/ajax-loader1.gif" />
				</center>
				<?php
			}
		}
	}
	
	function actualizar($conn) {
		$id		= $_GET['idDepto'];
		$ipOld	= $_GET['ipOld'];
		$ipNew	= $_GET['ipNew'];
			

		if(!empty($id) && !empty($ipOld) && !empty($ipNew)) {
			if($items = pg_query($conn, "UPDATE departamentos_ips SET ip = '$ipNew' WHERE depto_id = '$id' AND ip = '$ipOld'")) {
				echo "Dato actualizados satisfactoriamente";
				?>
				<br  />
				<center>
					<img src="imagenes/ajax-loader1.gif" />
				</center>
				<?php
			}
		}
			
	}
?>
