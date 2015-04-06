<?php
	require_once('../../conectar_db.php');
	$log_id = ($_GET['id_log']*1);
	$gral=cargar_registro("SELECT *,date_trunc('second', pedidolog_fecha) AS pedidolog_fecha FROM pedido_log_rev 
	LEFT JOIN funcionario USING (func_id)
	LEFT JOIN pedido USING (pedido_id)
	WHERE log_id=$log_id;");
   
	$detalle=cargar_registros_obj("SELECT * FROM((SELECT art_codigo, art_glosa, stock_vence, -(stock_cant)As stock_cant, 0 AS tipo
	FROM stock JOIN articulo ON stock_art_id=art_id JOIN logs ON log_id=$log_id
	JOIN pedido ON pedido_id=log_id_pedido WHERE stock_log_id=$log_id) 
	UNION 
	(SELECT art_codigo, art_glosa, stock_vence, -(stock_cant)AS stock_cant, 1 AS tipo
	FROM stock_rechazado JOIN articulo ON stock_art_id=art_id JOIN logs ON log_id=$log_id
	JOIN pedido ON pedido_id=log_id_pedido WHERE stock_log_id=$log_id 
	))AS foo where stock_cant>0 ORDER BY art_glosa ");
							 
	
	$htmlsi="<table style='width:100%;'>
	<tr class='tabla_header'>
	<td style='text-align:center;' colspan=5>Detalle Art&iacute;culos Recibidos por la Unidad</td>
	</tr>
	<tr class='tabla_header'>
	<td>#</td>
	<td>C&oacute;digo</td>
	<td>Descripci&oacute;n</td>
	<td>Fecha Vencimiento</td>
	<td>Cantidad</td>
	</tr>";
	
	$htmlno="<table style='width:100%;'>
	<tr class='tabla_header'>
	<td style='text-align:center;' colspan=5>Detalle Art&iacute;culos Rechazados por la Unidad</td>
	</tr>
	<tr class='tabla_header'>
	<td>#</td>
	<td>C&oacute;digo</td>
	<td>Descripci&oacute;n</td>
	<td>Fecha Vencimiento</td>
	<td>Cantidad</td>
	</tr>";
	
	for($i=0;$i<sizeof($detalle);$i++)
	{
		if($detalle[$i]['tipo']==0)
		{
			($si%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
			$htmlsi.="<tr class='$clase'>
			<td>".($si+1)."</td>
			<td>".$detalle[$i]['art_codigo']."</td>
			<td>".htmlentities($detalle[$i]['art_glosa'])."</td>
			<td style='text-align:center;'>".$detalle[$i]['stock_vence']."</td>
			<td style='text-align:right;'>".$detalle[$i]['stock_cant']."</td>
			</tr>";
			$si++;
		}
		else
		{
			($no%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
			$htmlno.="<tr class='$clase'>
			<td>".($no+1)."</td>
			<td>".$detalle[$i]['art_codigo']."</td>
			<td>".htmlentities($detalle[$i]['art_glosa'])."</td>
			<td style='text-align:center;'>".$detalle[$i]['stock_vence']."</td>
			<td style='text-align:right;'>".$detalle[$i]['stock_cant']."</td>
			</tr>";
			$no++;
		}
	}
	$htmlsi.="</table>";
	$htmlno.="</table>";
?>
<html>
	<div id='imprime' name='imprime'>
		<title>Visualizar Recepci&oacute;n Pedido</title>
		<?php cabecera_popup('../..'); ?>
		<script>
			/*imprimir_recepcion=function()
			{
				_detalle = $('imprime').innerHTML;
    			_pedido_conforme='<br><br><br><br><br><br><center><table><tr><td style="text-align: right;">________________________<br>Firma Recepci&oacute;n Conforme</td></tr></table></center>';
    			imprimirHTML(_detalle+_pedido_conforme);
			}*/
		
			imprimir_recepcion=function()
			{		
				imprimirHTML($('imprime').innerHTML+'<br><br><br><br><center><table><tr><td style="text-align: right;">________________________<br>Firma Recepci&oacute;n Conforme</td></tr></table></center>');	
			}
		</script>
		Recepci&oacute;n del Pedido
		</br>
		</br>
		<?php 
		if($gral)
		{
		?>
			<table width=100%;>
			<tr>
				<td style='text-align:right;' class='tabla_fila2' width=25%;>Nro. Pedido:</td>
				<td class='tabla_fila'><?php echo $gral['pedido_nro']; ?></td>
			</tr>
			<tr>
				<td style='text-align:right;' class='tabla_fila2'>Fecha Recep.:</td>
				<td class='tabla_fila'><?php echo $gral['pedidolog_fecha']; ?></td>
			</tr>
			<tr>
				<td style='text-align:right;' class='tabla_fila2'>Funcionario:</td>
				<td class='tabla_fila'><?php echo $gral['func_nombre']; ?></td>
			</tr>
			</table>
		<?php 
		}
		else
		{
		?>
			<center>
			<h3>Este despacho no ha sido Recepcionado a&uacute;n.</h3>
			</center>
		<?php 	
		}
		?>
		<!--
			<div id='gral' name='gral'>
				<center>
					<h1>Recepci&oacute;n del Pedido</h1>
					<div class='sub-content'>
						<?php 
						if($gral)
						{
						?>
							<table width=100%;>
								<tr>
									<td style='text-align:right;' class='tabla_fila2' width=25%;>Nro. Pedido:</td>
									<td class='tabla_fila'><?php echo $gral['pedido_nro']; ?></td>
								</tr>
								<tr>
									<td style='text-align:right;' class='tabla_fila2'>Fecha Recep.:</td>
									<td class='tabla_fila'><?php echo $gral['pedidolog_fecha']; ?></td>
								</tr>
								<tr>
									<td style='text-align:right;' class='tabla_fila2'>Funcionario:</td>
									<td class='tabla_fila'><?php echo $gral['func_nombre']; ?></td>
								</tr>
							</table>
					<?php 
					}
					else
					{
					?>
						<center>
							<h3>Este despacho no ha sido Recepcionado a&uacute;n.</h3>
						</center>
					<?php 	
					}
					?>
					</div>
				</center>
			</div>
			-->
			<div id='recep' name='recep'>
				<?php
					if($gral)
					{
				?>
						<table width=100%;>
						<tr>
						<td>
				<?php
						if($si>0)
						{
							print("<div class='sub-content2'>");
								print($htmlsi);
							print("</div>");
						}
						if($no>0)
						{
							print("<div class='sub-content2'>");
								print($htmlno);
							print("</div>");
						}
				?>
						</td>
						</tr>
						</table>
				<?php
					}
				?>
			</div>
			<br><br>
		
	</div>
	<center>
   	<table>
      	<tr>
            	<td>
					<?php
					if($gral)
					{
					?>
						<div class='boton' id='imprimir_pedido_btn'>
                  	<table>
                    		<tr>
                        	<td>
                           	<img src='../../iconos/printer.png' >
                        	</td>
                        	<td>
										<a href='#' onClick='imprimir_recepcion();'>
                              	Imprimir Recepci&oacute;n...
                            	</a>
                        	</td>
                    		</tr>
							</table>
						</div>
					<?php
					}
					?>
               </td>
               <td>
						<div class='boton' id='cerrar'>
                  	<table>
                    		<tr>
                        	<td>
                           	<img src='../../iconos/error.png'>
                        	</td>
                        	<td>
                           	<a href='#' onClick='window.close();'>
                              	Cerrar...
                            	</a>
                        	</td>
                    		</tr>
                    </table>
						</div>
                </td>
				</tr>
			</table>
	</center>
</html>