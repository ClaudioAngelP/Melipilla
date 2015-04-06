<?php 

	require_once('../../conectar_db.php');
	
	$fecha=pg_escape_string(utf8_decode(($_GET['fecha1'])));
	$func_id=($_GET['funcionarios']*1);
	
	/*$d=cargar_registros_obj("
		SELECT * FROM caja_detalle
		WHERE cd_fecha='$fecha' AND func_id=$func_id	
	");*/

	$chq=array();
	
	$chq[1]=20000;
	$chq[2]=10000;
	$chq[3]=5000;
	$chq[4]=2000;
	$chq[5]=1000;
	$chq[6]=500;
	$chq[7]=100;
	$chq[8]=50;
	$chq[9]=10;
	$chq[10]=5;
	$chq[11]=1;

	for($i=1;$i<=11;$i++) {

		$dg[$i]=0;
		
		if($d)
		
		for($j=0;$j<sizeof($d);$j++) {
			
			if($d[$j]['cd_tipo']==$chq[$i]) {
				$dg[$i]=$d[$j]['cd_monto']*1;
			}
		}
		
		if($dg[$i]==0) $dg[$i]='';

	}	
		

?>
<script>

chequear = function(obj, div){
	calcular_totales();
}


calcular_totales = function(){

	var chq=[];

	chq[1]=20000;
	chq[2]=10000;
	chq[3]=5000;
	chq[4]=2000;
	chq[5]=1000;
	chq[6]=500;
	chq[7]=100;
	chq[8]=50;
	chq[9]=10;
	chq[10]=5;
	chq[11]=1;
	
	
	
	var monto=0;
	var chk=true;
	
	for(var i=1;i<=11;i++) {
		
		var obj=$('m_'+i);
		
		monto+=obj.value*1;
		
		if(obj.value*1==0) {
			
			obj.value='';
			obj.style.background='';
				
		} else {
		
			var d=(obj.value*1)%chq[i];
	
			if(d != 0) {
				obj.style.background='red';	
			} else {
				obj.style.background='yellowgreen';	
			}		

		}
				
	}
	 
	
	$('montoing').innerHTML='$ '+number_format(monto,0,',','.')+'.-';
	$('monto_ing_rec').value = monto;	
	
}



chequear_variable = function(obj, div){
	calcular_totales_variable();
}

var efectivo_variable=<?php echo json_encode($efectivo+30000); ?>;


calcular_totales_variable = function(){
	
	var chq_variable=[];

	chq_variable[1]=20000;
	chq_variable[2]=10000;
	chq_variable[3]=5000;
	chq_variable[4]=2000;
	chq_variable[5]=1000;
	chq_variable[6]=500;
	chq_variable[7]=100;
	chq_variable[8]=50;
	chq_variable[9]=10;
	chq_variable[10]=5;
	chq_variable[11]=1;
	
	
	
	var monto_variable=0;
	var chk_variable=true;
	
	for(var i=1;i<=11;i++) {
		
		var obj=$('m_variable_'+i);
		
		monto_variable+=obj.value*1;
		
		if(obj.value*1==0) {
			
			obj.value='';
			obj.style.background='';
				
		} else {
		
			var d=(obj.value*1)%chq_variable[i];
	
			if(d != 0) {
				obj.style.background='red';	
			} else {
				obj.style.background='yellowgreen';	
			}		

		}
				
	}
	
	$('montoing_variable').innerHTML='$ '+number_format(monto_variable,0,',','.')+'.-';	
	$('monto_ing_var').value = monto_variable;
}


auditar_montos = function(){
	
aud_motivo_id  = $('aud_motivo_id').value;
monto_ing_rec  = $('monto_ing_rec').value;
monto_ing_var  = $('monto_ing_var').value;
ac_id 		   = $('ac_id').value;
func_id 	   = $('func_id').value;

params =$('auditoria_caja').serialize();

if(monto_ing_rec == ''){alert('Ingrese  el monto recaudado');return;}	
if(monto_ing_var == ''){alert('Ingrese  el fondo variable');return;}

	window.open('ingresos/auditoria_caja/imprimir_auditoria.php?params='+params+'&aud_motivo_id='+aud_motivo_id,"_blank",'toolbar=0,menubar=no,status=no');
} 
/*
var myAjax=new Ajax.Request(
	'ingresos/auditoria_caja/sql.php',
	{
		method:'post',
		parameters: params,
		onComplete: function(resp) {
			resp = resp.responseText; 
			if(resp == ""){
				window.open('ingresos/auditoria_caja/imprimir_auditoria.php?bolnum='+bolnum,'_blank');
			
		 	}else{
		 		alert("Ha ocurrido un error, comuniquese con Sistema Expertos");
		 	}
		}	
	}	
);
*/

</script>
<?php
$func_id = $_SESSION['sgh_usuario_id'];
$result = pg_query($conn, "SELECT 
								ac_fecha_apertura,
								ac_id,
								func_id 
							FROM 
								apertura_cajas 
							WHERE func_id = $func_id 
						    ORDER BY 
						    	ac_fecha_apertura 
						    DESC LIMIT 1") 
					or die("pg_last_error()");
	
					
while($row = pg_fetch_array($result)){
	
	$ac_fecha_apertura 	= $row['ac_fecha_apertura'];
	$ac_fecha_apertura 	= explode('.', $ac_fecha_apertura);
	$func_id 			= $row['func_id'];
	$ac_id		 		= $row['ac_id'];
	
}					
 
?>
<center>
<div class='sub-content' style='width:780px;'>

<div class='sub-content'>
<img src='iconos/money.png'>
<b>Auditoria Cajas</b>

</div>
<form id='auditoria_caja' name='auditoria_caja' onSubmit='return false;'>
<div class='sub-content'>
<center>
<table style='width:100%;'>
	<tr>
		<td style='width:60%;'>
			<table style='width:100%;'>
			  <tr>
			  	<td style='text-align: right;'>Descripci&oacute;n</td>
			  		<td>
			  			<input type="text" style="width: 400px" 
			  			name="aud_motivo_id"
			  			id="aud_motivo_id">
			  		</td>
			  	</tr>
			</table>
		</td>
	</tr>
	<tr>	
		<td>
			<table >
			  <tr>
			  	<td style="width: 100px; text-align: left; ">
					Nombre
			  	</td>
			  	<td style="width: 120px; text-align: left; ">
					<?= $_SESSION['sgh_usuario'] ?> 
			  	</td>
			  </tr>
			  <tr>
			  	<td style="width: 120px; text-align: left; ">
					Cargo
			  	</td>
			  	<td style="width: 100px; text-align: left; ">
					<?= $_SESSION['sgh_cargo']?> 
			  	</td>
			  </tr>
			  <tr>
			  	<td style="width: 120px; text-align: left; ">
					Fecha Apertura Caja
			  	</td>
			  	<td style="width: 100px; text-align: left; ">
					<?= $ac_fecha_apertura[0] ?>
			  	</td>
			  	<td>
			  		<center>
						<input type='button' onClick='auditar_montos();' name="auditar" id="auditar"
						 value='Auditar Caja'>
					</center>
		  		</td>
			  <tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="width: 100%">
			<table>
				<tr>
					
					<input type="text" id="monto_ing_rec"
					 name="monto_ing_rec" style="display: none;"/>
					
					<input type="text" id="monto_ing_var" 
					name="monto_ing_var" style="display: none;"/>
					
					<input type="text" id="func_id" 
					name="func_id" value="<?php echo $func_id; ?>"
					style="display: none;"/>
					
					<input type="text" id="ac_id" 
					name="ac_id" value="<?php echo $ac_id; ?>"
					style="display: none;"/>
</form>					
					<td valign="top" >
					<table>
						<tr>
							<td style="font-size:16px; font-weight: bold;">
								Monto recaudado
							</td>
						</tr>
						<tr>	
							<td valign="top"  >
								<table >
									<tr>
										<td colspan="1" style="font-weight: bold;"  >
											<u>Billetes</u>
										</td>
									</tr>
									<tr>
										<td style='text-align:right;font-weight:bold;width:70px;'>
											$ 20.000.-
										</td>
										<td>
											<input type='text' size=10 
											style='text-align:right;' id='m_1' name='m_1'
											value='<?php echo $dg[1]; ?>' onKeyUp='chequear(this,20000);'/>
										</td>
									</tr>
									<tr>
										<td style='text-align:right;font-weight:bold;'>
											$ 10.000.-
										</td>
										<td>
											<input type='text' size=10 
											style='text-align:right;'
											value='<?php echo $dg[2]; ?>'
											id='m_2' name='m_2' onKeyUp='chequear(this,10000);'/>
										</td>
									</tr>			
									<tr>
										<td style='text-align:right;font-weight:bold;'>
											$ 5.000.-
										</td>
										<td>
											<input type='text' size=10
											style='text-align:right;'  value='<?php echo $dg[3]; ?>'
											id='m_3' name='m_3' onKeyUp='chequear(this,5000);'/>
										</td>
										</tr>
									<tr>
										<td style='text-align:right;font-weight:bold;'>
												$ 2.000.-
										</td>
										<td>
											<input type='text' size=10 style='text-align:right;'  
											value='<?php echo $dg[4]; ?>'
											id='m_4' name='m_4' onKeyUp='chequear(this,2000);'/>
										</td>
									</tr>
									<tr>
										<td style='text-align:right;font-weight:bold;'>
											$ 1.000.-
										</td>
										<td>
											<input type='text' size=10 
											style='text-align:right;'  value='<?php echo $dg[5]; ?>'
											id='m_5' name='m_5' onKeyUp='chequear(this,1000);'/>
										</td>
									</tr>
								</table>
							</td>
							<td >
								<table>
									<tr>
										<td colspan=1 style='text-align:center;font-weight:bold;'>
											<u>Monedas</u>
										</td>
									</tr>
									<tr>
										<td style='text-align:right;font-weight:bold;width:70px;'>
											$ 500.-
										</td>
										<td>
											<input type='text' size=10 style='text-align:right;' 
											 value='<?php echo $dg[6]; ?>'
											id='m_6' name='m_6' onKeyUp='chequear(this,500);'/>
										</td>
									</tr>
									<tr>
										<td style='text-align:right;font-weight:bold;'>
											$ 100.-
										</td>
										<td>
											<input type='text' size=10 style='text-align:right;' 
											 value='<?php echo $dg[7]; ?>'
											id='m_7' name='m_7' onKeyUp='chequear(this,100);'/>
										</td>
									</tr>
									<tr>
										<td style='text-align:right;font-weight:bold;'>
											$ 50.-
										</td>
										<td>
											<input type='text' size=10 style='text-align:right;'  
											value='<?php echo $dg[8]; ?>'
											id='m_8' name='m_8' onKeyUp='chequear(this,50);'/>
										</td>
									</tr>
									<tr>
										<td style='text-align:right;font-weight:bold;'>
											$ 10.-
										</td>
										<td>
											<input type='text' size=10 style='text-align:right;' 
											 value='<?php echo $dg[9]; ?>'
											id='m_9' name='m_9' onKeyUp='chequear(this,10);'/>
										</td>
									</tr>
									<tr>
										<td style='text-align:right;font-weight:bold;'>
											$ 5.-
										</td>
										<td>
											<input type='text' size=10 style='text-align:right;'  
											value='<?php echo $dg[10]; ?>'
											id='m_10' name='m_10' onKeyUp='chequear(this,5);'/>
										</td>
									</tr>
									<tr>
										<td style='text-align:right;font-weight:bold;'>
											$ 1.-
										</td>
										<td>
											<input type='text' size=10 style='text-align:right;'  
											value='<?php echo $dg[11]; ?>'
											id='m_11' name='m_11' onKeyUp='chequear(this,1);'/>
										</td>
									</tr>
								</table>
							</td>
						</tr>	
						<!--<tr>
							<td style='text-align:right;'>
								Fondo Fijo:
							</td>
							<td id='montorec' style='font-weight:bold;color:black;'>
								$ <?php echo number_format(30000,0,',','.'); ?>.-
							</td>
						</tr>
						<tr>
							<td style='text-align:right;'>
								Monto a Rendir:
							</td>
							<td id='montorec' style='font-weight:bold;color:blue;'>
								$ <?php echo number_format($efectivo+30000,0,',','.'); ?>.-
							</td>
						</tr>-->
						<tr>
							<td style='text-align:right;'>
								Monto Ingresado:
							</td>
							<td id='montoing' style='font-weight:bold;'>
								$ 0.-
							</td>
						</tr>
					</table>
				</td>
				<td  valign="top">
					<table style="border-left: 1px solid black;" >
						<tr>
							<td style="font-size:16px; font-weight: bold;">
								Fondo Variable
							</td>
						</tr>
							<td valign="top">
								<table>
									<tr>
										<td valign="top" >
											<table>
												<tr>
													<td colspan="1" style="font-weight: bold;"  >
														<u>Billetes</u>
													</td>
												</tr>
												<tr>
													<td style='text-align:right;font-weight:bold;width:70px;'>
														$ 20.000.-
													</td>
													<td>
														<input type='text' size=10 
														style='text-align:right;'
														 value='<?php echo $dg[1]; ?>' 
														id='m_variable_1' name='m_variable_1' 
														onKeyUp='chequear_variable(this,20000);'/>
													</td>
												</tr>
												<tr>
													<td style='text-align:right;font-weight:bold;'>
														$ 10.000.-
													</td>
													<td>
														<input type='text' size=10 
														style='text-align:right;'
														value='<?php echo $dg[2]; ?>'
														id='m_variable_2' name='m_variable_2' 
														onKeyUp='chequear_variable(this,10000);'/>
													</td>
												</tr>			
												<tr>
													<td style='text-align:right;font-weight:bold;'>
														$ 5.000.-
													</td>
													<td>
														<input type='text' size=10
														style='text-align:right;'  value='<?php echo $dg[3]; ?>'
														id='m_variable_3' name='m_variable_3' 
														onKeyUp='chequear_variable(this,5000);'/>
													</td>
													</tr>
												<tr>
													<td style='text-align:right;font-weight:bold;'>
															$ 2.000.-
													</td>
													<td>
														<input type='text' size=10 style='text-align:right;'  
														value='<?php echo $dg[4]; ?>'
														id='m_variable_4' name='m_variable_4' 
														onKeyUp='chequear_variable(this,2000);'/>
													</td>
												</tr>
												<tr>
													<td style='text-align:right;font-weight:bold;'>
														$ 1.000.-
													</td>
													<td>
														<input type='text' size=10 
														style='text-align:right;'  value='<?php echo $dg[5]; ?>'
														id='m_variable_5' name='m_variable_5' 
														onKeyUp='chequear_variable(this,1000);'/>
													</td>
												</tr>
											</table>
										</td>
										<td>
											<table>
												<tr>
													<td colspan=1 style='text-align:center;font-weight:bold;'>
														<u>Monedas</u>
													</td>
												</tr>
												<tr>
													<td style='text-align:right;font-weight:bold;width:70px;'>
														$ 500.-
													</td>
													<td>
														<input type='text' size=10 style='text-align:right;'  value='<?php echo $dg[6]; ?>'
														id='m_variable_6' name='m_variable_6' 
														onKeyUp='chequear_variable(this,500);'/>
													</td>
												</tr>
												<tr>
													<td style='text-align:right;font-weight:bold;'>
														$ 100.-
													</td>
													<td>
														<input type='text' size=10 style='text-align:right;'  value='<?php echo $dg[7]; ?>'
														id='m_variable_7' name='m_variable_7' 
														onKeyUp='chequear_variable(this,100);'/>
													</td>
												</tr>
												<tr>
													<td style='text-align:right;font-weight:bold;'>
														$ 50.-
													</td>
													<td>
														<input type='text' size=10 style='text-align:right;'  value='<?php echo $dg[8]; ?>'
														id='m_variable_8' name='m_variable_8'
														 onKeyUp='chequear_variable(this,50);'/>
													</td>
												</tr>
												<tr>
													<td style='text-align:right;font-weight:bold;'>
														$ 10.-
													</td>
													<td>
														<input type='text' size=10 style='text-align:right;'  value='<?php echo $dg[9]; ?>'
														id='m_variable_9' name='m_variable_9' 
														onKeyUp='chequear_variable(this,10);'/>
													</td>
												</tr>
												<tr>
													<td style='text-align:right;font-weight:bold;'>
														$ 5.-
													</td>
													<td>
														<input type='text' size=10 style='text-align:right;'  value='<?php echo $dg[10]; ?>'
														id='m_variable_10' name='m_variable_10' 
														onKeyUp='chequear_variable(this,5);'/>
													</td>
												</tr>
												<tr>
													<td style='text-align:right;font-weight:bold;'>
														$ 1.-
													</td>
													<td>
														<input type='text' size=10 style='text-align:right;'  value='<?php echo $dg[11]; ?>'
														id='m_variable_11' name='m_variable_11' 
														onKeyUp='chequear_variable(this,1);'/>
													</td>
												</tr>
												<tr>
													
											</table>
										</td>		
									</tr>
									<!--<tr>
										<td style='text-align:right;'>
											Monto Recaudado:
										</td>
										<td id='montodet' style='font-weight:bold;color:black;'>
											$ <?php echo number_format($efectivo,0,',','.'); ?>.-
										</td>
									</tr>-->
									<tr>
										<td style='text-align:right;'>
											Monto Ingresado:
										</td> 
										<td id='montoing_variable' style='font-weight:bold;'>
											$ 0.- 
										</td>
									</tr>
								</table>			
							</td>
						</tr>
						
					</table>
				</td>
				</tr>
				
			</table>
	</tr>
</table>
</div>
</center>