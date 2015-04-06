<?php 

	require_once('../../conectar_db.php');

	$ccamas=cargar_registros_obj("SELECT * FROM clasifica_camas ORDER BY tcama_num_ini;", true);
	$tcamas=cargar_registros_obj("SELECT * FROM tipo_camas ORDER BY cama_num_ini;", true);

	$html='';
	
	for($i=0;$i<sizeof($ccamas);$i++) {
	
		$n='sector_'.$ccamas[$i]['tcama_id'];
		$t=$ccamas[$i]['tcama_tipo'];	

		$tc=cargar_registros_obj("SELECT * FROM tipo_camas WHERE cama_num_ini BETWEEN ".$ccamas[$i]['tcama_num_ini']." AND ".$ccamas[$i]['tcama_num_fin'].' ORDER BY cama_num_ini', true);
		
		$id='chart_'.$ccamas[$i]['tcama_id'];	
	
		$html.='<div class="sector" id="'.$n.'" name="'.$n.'">
			<div class="sector_titulo">
			<table style="width:100%;"><tr><td>'.$t.'</td>
			<td style="width:70px;height:40px;">
			
			<table cellpadding=0 cellspacing=1
			style="font-size:10px;text-align:right;">			
			<tr>
			<td rowspan=4>
			<div id="'.$id.'">
			!!
			</div>			
			</td>
			<td style="text-align:right;">
			<div class="chart_rojo"></div>			
			</td>
			<td id="'.$id.'_rojo" style="color:#ffffff;"></td>
			<td id="'.$id.'_rojo_p" style="font-weight:bold;"></td>
			</tr>

			<tr>
			<td style="text-align:right;">
			<div class="chart_amarillo"></div>			
			</td>
			<td id="'.$id.'_amarillo" style="color:#ffffff;"></td>
			<td id="'.$id.'_amarillo_p" style="font-weight:bold;"></td>
			</tr>

			<tr>
			<td style="text-align:right;">
			<div class="chart_verde"></div>			
			</td>
			<td id="'.$id.'_verde" style="color:#ffffff;"></td>
			<td id="'.$id.'_verde_p" style="font-weight:bold;"></td>
			</tr>

			<tr>
			<td style="text-align:right;">
			<div class="chart_blanco"></div>			
			</td>
			<td id="'.$id.'_blanco" style="color:#ffffff;"></td>
			<td id="'.$id.'_blanco_p" style="font-weight:bold;"></td>
			</tr>

			</table>
			
			</td></tr>
			</table>
			
			</div>';
		
		for($k=0;$k<sizeof($tc);$k++) {		

			if($tc[$k]['cama_color']!='')
				$estilo='style="background-color:#'.$tc[$k]['cama_color'].'"';
			else 
				$estilo='';

			$html.='<div class="sala_titulo">
						'.($tc[$k]['cama_tipo']).'
					  </div>
					  <div class="sala" '.$estilo.'>';

			for(  $n=$tc[$k]['cama_num_ini']*1;
					$n<=$tc[$k]['cama_num_fin']*1;
					$n++) {

			$nn=($n-$ccamas[$i]['tcama_num_ini'])+1;
		
			$html.="
				<table cellpadding=0 cellspacing=0 
				class='cama' id='cama_$n' name='cama_$n'>
				<tr>
				<td class='ucama' id='icama_$n'></td>
				</tr><tr>
				<td class='nro_cama'>$nn</td>
				</tr></table>
				";
				
			}

			$html.='</div>';
						
		}
			
		$html.='</div>';	
		
	}
	
	$l=cargar_registros_obj("
			SELECT *, hosp_fecha_ing::date AS hosp_fecha_ing, 
			hospitalizacion.hosp_id AS id
			FROM hospitalizacion
			JOIN pacientes ON hosp_pac_id=pac_id
			LEFT JOIN tipo_camas ON
				cama_num_ini<=hosp_numero_cama AND cama_num_fin>=hosp_numero_cama
			LEFT JOIN clasifica_camas ON 
				tcama_num_ini<=hosp_numero_cama AND tcama_num_fin>=hosp_numero_cama
			WHERE hosp_fecha_egr IS NULL AND hosp_numero_cama=0
			ORDER BY hospitalizacion.hosp_fecha_ing		
	");

	$hp='<table style="font-size:10px;width:100%;">';
	
	if($l)
	for($i=0;$i<sizeof($l);$i++) {

				if($l[$i]['sex_id']*1==0)
					$icono='icono_m_';
				else
					$icono='icono_h_';
					
				switch($l[$i]['hosp_criticidad']) {
					case 'A1': case 'A2': case 'A3': 
					case 'B1': case 'B2': 
						$icono.='a'; break;	
					case 'B3': case 'C1': case 'C2': case 'D1': 
						$icono.='b'; break;	
					case 'C3': case 'D2': case 'D3': 
						$icono.='c'; break;	
					default: $icono.='c'; break;	
				}


		$hp.='<tr>
				<td class="ucama" id="icama_0_'.$l[$i]['hosp_id'].'">
				<img class="uso_cama" id="hospi_0_'.$l[$i]['hosp_id'].'" 
				src="'.$icono.'.png" /></td>
				<td style="text-align:right;font-weight:bold;">
				'.$l[$i]['pac_rut'].'</td>
				<td>
				'.$l[$i]['pac_appat'].' 
				'.$l[$i]['pac_apmat'].' 
				'.$l[$i]['pac_nombres'].'</td> 
				<td style="text-align:center;font-weight:bold;">'.$l[$i]['hosp_criticidad'].'</td>
				</tr>';	
	}
	
	$hp.='</table>';

?>
<html>

  <head>
  
 	  <LINK href="../../css/interface.css" type='text/css' rel='stylesheet'>

     
     <script type="text/javascript" src="jquery-1.4.2.min.js"></script>
     <script type="text/javascript" src="jquery-ui-1.8.custom.min.js"></script>
     <script type="text/javascript" src="jquery.simpletip-1.3.1.js"></script>

     <script type="text/javascript" src="jquery.sparkline.js"></script>

<!--
  	  <link type="text/css" href="http://jqueryui.com/latest/themes/base/jquery.ui.all.css" rel="stylesheet" />
		
  		<script type="text/javascript" src="http://jqueryui.com/latest/jquery-1.4.2.js"></script>
  		<script type="text/javascript" src="http://jquery-ui.googlecode.com/svn/tags/1.8rc1/jquery-1.4.1.js"></script>
		<script type="text/javascript" src="http://jquery-ui.googlecode.com/svn/tags/1.8rc1/ui/jquery-ui.js"></script>
-->

    	<script type="text/javascript">

		var ccamas=<?php echo json_encode($ccamas); ?>;
		var tcamas=<?php echo json_encode($tcamas); ?>;
		
      var ucamas;
      
      function cargar_camas() {
      
			 $.getJSON('listado_camas.php', function(data) {
			 	
				ucamas=data;	 					 	
  				dibujar_pacientes();
  				
			 });     


			$.getJSON('info_grafico.php', 
			'tcama_id=0', 
			function(data) {

					var total=data[1][0]+
								 data[1][1]+
								 data[1][2]+
								 data[1][3];
								 
					var factor=100/total;
								 					
						
					$('#chart_'+data[0]+'_rojo').html(data[1][0]);					
					$('#chart_'+data[0]+'_amarillo').html(data[1][1]);					
					$('#chart_'+data[0]+'_verde').html(data[1][2]);					
					$('#chart_'+data[0]+'_blanco').html(data[1][3]);					
					
					$('#chart_'+data[0]+'_rojo_p').html(Math.round(data[1][0]*factor)+'%');					
					$('#chart_'+data[0]+'_amarillo_p').html(Math.round(data[1][1]*factor)+'%');					
					$('#chart_'+data[0]+'_verde_p').html(Math.round(data[1][2]*factor)+'%');					
					$('#chart_'+data[0]+'_blanco_p').html(Math.round(data[1][3]*factor)+'%');					
					
					$('#chart_'+data[0]).sparkline(
						data[1], {
							type:'pie',
							sliceColors:['red','yellow','yellowgreen','#cccccc'],
							width:'100px',
							height:'100px'
							}					
					);
						
			});
			
			for(var i=0;i<ccamas.length;i++) {

				var tcama_id=ccamas[i].tcama_id;
			
				$.getJSON('info_grafico.php', 
				'tcama_id='+tcama_id, 
				function(data) {
					
					try {
					
					var total=data[1][0]+
								 data[1][1]+
								 data[1][2]+
								 data[1][3];
								 
					var factor=100/total;
								 					
						
					$('#chart_'+data[0]+'_rojo').html(data[1][0]);					
					$('#chart_'+data[0]+'_amarillo').html(data[1][1]);					
					$('#chart_'+data[0]+'_verde').html(data[1][2]);					
					$('#chart_'+data[0]+'_blanco').html(data[1][3]);					
					
					$('#chart_'+data[0]+'_rojo_p').html(Math.round(data[1][0]*factor)+'%');					
					$('#chart_'+data[0]+'_amarillo_p').html(Math.round(data[1][1]*factor)+'%');					
					$('#chart_'+data[0]+'_verde_p').html(Math.round(data[1][2]*factor)+'%');					
					$('#chart_'+data[0]+'_blanco_p').html(Math.round(data[1][3]*factor)+'%');					
					
					
					$('#chart_'+data[0]).sparkline(
						data[1], {
							type:'pie',
							sliceColors:['red','yellow','yellowgreen','#eeeeee'],
							width:'50px',
							height:'50px'
							}					
					);
					
					} catch(err) {
					
						console.log(err);
						console.log(data[0]+'!');					
						
					}						
						
				});
					
			}
      	
      }
      
      function dibujar_pacientes() {
      
			for(var i=0;i<ucamas.length;i++) {
			
				var ncama=ucamas[i].hosp_numero_cama;
				
				var icono=tipo_icono(ucamas[i].sex_id, ucamas[i].hosp_criticidad);					
				
				$('#icama_'+ucamas[i].hosp_numero_cama).html('<img id="hospi_'+ucamas[i].hosp_id+'_'+i+'" name="hospi_'+ucamas[i].hosp_id+'_'+i+'" class="uso_cama" src="'+icono+'.png" />');								

				$('#icama_'+ucamas[i].hosp_numero_cama).simpletip({
						content: datos_hosp(i),
						position: 'left'					
					});
								
			}      
      	
      }
      
      function tipo_icono(sex_id, criticidad) {

				if(sex_id*1==0)
					var icono='icono_m_';
				else
					var icono='icono_h_';


				switch(criticidad) {
					case 'A1': case 'A2': case 'A3': case 'B1': case 'B2': 
						icono+='a'; break;	
					case 'B3': case 'C1': case 'C2': case 'D1': 
						icono+='b'; break;	
					case 'C3': case 'D2': case 'D3': 
						icono+='c'; break;	
					default: icono+='c'; break;	
				}      
				
				return icono;
	
      }
      
      function datos_hosp(i) {
      	
      	var u=ucamas[i];      	
      	var html='<table class="datos_tool"><tr><td class="datos_der">RUT:</td><td><b>'+u.pac_rut+'</b></td></tr>';
      	html+='<tr><td class="datos_der">Nombre:</td><td><i>'+u.pac_appat+' '+u.pac_apmat+' '+u.pac_nombres+'</i></td></tr>';
      	html+='<tr><td class="datos_der">Cat.:</td><td><b>'+u.hosp_criticidad+'</b></td></tr>';
      	html+='<tr><td class="datos_der">Fecha Ingreso:</td><td>'+u.hosp_fecha_ing+'</td></tr>';
      	html+='<tr><td class="datos_der">Or&iacute;gen:</td><td>'+u.ciud_desc+'</td></tr>';
      	
      	if(u.hosp_doc_id!=0)
      		html+='<tr><td class="datos_der">M&eacute;dico Tratante:</td><td>'+u.doc_nombres+' '+u.doc_paterno+' '+u.doc_materno+'</td></tr></table>';
			else
      		html+='<tr><td class="datos_der">M&eacute;dico Tratante:</td><td>(Sin Asignar...)</td></tr></table>';

      	return html;	

      }
      
		function datos_pac(id) {
			
			var i=0;
			
			for(;i<ucamas.length;i++)
      		if(id==ucamas[i].hosp_id) break;
      		
      	u=ucamas[i];

      	var icono=tipo_icono(u.sex_id, u.hosp_criticidad);
      	
      	var html='<td style="width:20px;"><img class="uso_cama" src="'+icono+'.png" /></td><td style="text-align:right;">'+u.pac_rut+'</td><td>'+u.pac_appat+' '+u.pac_apmat+' '+u.pac_nombres+'</td>';
      	
      	return html;
      	      				
		}

		
		$(document).ready(
			function() {
				
				   $("#follower").hide();
		
					cargar_camas();
					
					$(window).resize(function() {
			  			$('#diagrama').height( $(window).height() - 190 );
			  			
			  			$('#lista_entradas').height( 110 );
					});
			
			  		$('#diagrama').height( $(window).height() - 190 );
			  		
			  		$('#lista_entradas').height( 110 );
			}		
		);	
	
		      
    </script>
    
    <style>
		
		.tooltip { 
			position: absolute; 
			top: 0; left: 0; z-index: 3; 
			display: none; font-size:11px; padding:4px;
			border:1px solid black;
			background-color:#aaaacc; 
		}
		
		
		body {
		  font-family: Arial, Liberation Sans, sans-serif;
		}	
		
		.datos_tool {
			font-size:11px;	
		}	
		
		.datos_der {
			text-align:right;	
		}
				
		.titulo_content {
			border:1px solid black;padding:2px;
			background-color:#aaaaff; font-weight:bolder;
			font-size:13px;text-align:left;	
		}


		#lista_entradas {
			height:120px;border:1px solid black;
			background-color:#ffaa00;overflow:auto;padding:2px;
		}

		#lista_salidas {
			height:120px;border:1px solid black;
			background-color:#00aaff;overflow:auto;padding:2px;
		}

		#lista_movimientos {
			border:1px solid black;
			background-color:#00aaff;overflow:auto;padding:2px;
		}
		
		#diagrama {		
			height:400px;overflow:auto;
			text-align:center;
		}
		
		.sector {
			width:90%;border:1px solid black;
			background-color:#ccf0ff;overflow:hidden;
			margin:5px; display:block;
		}
		
		.sector_titulo {
			width:100%; border:1px solid black; 
			background-color: #00bbbb;
			cursor: move; text-align:center; font-weight:bolder;
		}

		.sala { width:100%; border:1px solid black; }
		
		.sala_titulo { 
			width:100%; border:1px solid black; 
			background-color: #00cccc; 
			text-align:center;font-size:11px;
		}

		.cama {
			margin:5px;width:24px;
			font-size:10px;display:inline-block;
		}		

		.lista_camas {}
				
		.ucama {
			background-image: url(icono_cama3.png);
			background-repeat: no-repeat;
			width:24px;height:18px;
			text-align:center;
		}
		
		.uso_cama {
			width:12px;height:12px;cursor:pointer;
		}


		.nro_cama {
			font-size:16px; width:15px;text-align:center;
		}
		
		.paciente {
			margin:5px;	
		}
		
		#follower { background: #fff; padding: 2px; border: 1px solid #ddd; position: absolute; }
		#follower_desc { font-size: 11px; }
		
		.chart_rojo {
			background-color: #ff0000;
			border: 1px solid black;
			width:15px; height:8px;
			overflow: hidden;	
		}

		.chart_amarillo {
			background-color: #ffff00;
			border: 1px solid black;
			width:15px; height:8px;
			overflow: hidden;	
		}

		.chart_verde {
			background-color: #00ff00;
			border: 1px solid black;
			width:15px; height:8px;
			overflow: hidden;	
		}

		.chart_blanco {
			background-color: #dddddd;
			border: 1px solid black;
			width:15px; height:8px;
			overflow: hidden;	
		}
			    
    </style>
 
	<title>Gesti&oacute;n Centralizada de Camas</title> 
 
  </head>
  <body topmargin=0 leftmargin=0 rightmargin=0>

	<div class='sub-content'>
	<img src='../../iconos/building.png'>
	<b>Gesti&oacute;n Centralizada de Camas</b>
	</div>

<center>

<table style='width:100%;'>
<tr><td style='width:100%;' colspan=2>

	<div id='diagrama' name='diagrama'>
	<center>
	<?php echo $html; ?>
	</center>	
	</div>
	
</td></tr>

<tr><td style='width:100px;font-size:12px;'>

<center>

<b><u>Indice Ocupacional</u></b><br /><br />

<table><tr><td>

<div id='chart_0'>
!!
</div>

</td><td>

			<table style='text-align:right;'>

			<tr>
			<td style="text-align:right;">
			<div class="chart_rojo"></div>			
			</td>
			<td id="chart_0_rojo"></td>
			<td id="chart_0_rojo_p" style="font-weight:bold;"></td>
			</tr>
			
			<tr>
			<td style="text-align:right;">
			<div class="chart_amarillo"></div>			
			</td>
			<td id="chart_0_amarillo"></td>
			<td id="chart_0_amarillo_p" style="font-weight:bold;"></td>
			</tr>
			
			<tr>
			<td style="text-align:right;">
			<div class="chart_verde"></div>			
			</td>
			<td id="chart_0_verde"></td>
			<td id="chart_0_verde_p" style="font-weight:bold;"></td>
			</tr>
			
			<tr>
			<td style="text-align:right;">
			<div class="chart_blanco"></div>			
			</td>
			<td id="chart_0_blanco"></td>
			<td id="chart_0_blanco_p" style="font-weight:bold;"></td>
			</tr>

			</table>

</td>
</tr>

</table>

</center>

</td><td>
	
	<div class='titulo_content'>
	<img src='../../iconos/user_go.png'>
	Pacientes Sin Asignaci&oacute;n 
	(<?php echo sizeof($l); ?>)	
	</div>
	
	<div id='lista_entradas'>
	<?php echo $hp; ?>
	</div>

</td></tr>


</table>
</center>
  <div id='follower' style='display:none;'>
	<table><tr><td>  	
  	<img src='../../iconos/user_go.png' />
  	</td><td id='follower_desc'>
  	
  	</td></table>
  </div>
  </body>
</html>
