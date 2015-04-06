<?php 

	require_once('../../conectar_db.php');
	
	$ccamashtml = desplegar_opciones_sql( 
	  "SELECT tcama_id, tcama_tipo 
		FROM clasifica_camas 
		WHERE tcama_gest_camas 
	   ORDER BY tcama_num_ini",  $_GET['tcama_id']*1, '', "");
	
	if(!isset($_GET['tcama_id'])) {
		$ccamas=cargar_registros_obj("SELECT * FROM clasifica_camas WHERE tcama_gest_camas ORDER BY tcama_num_ini;", true);
	} else {
		$ccamas=cargar_registros_obj("SELECT * FROM clasifica_camas WHERE tcama_id=".($_GET['tcama_id']*1)." ORDER BY tcama_num_ini;", true);
	}
	
	$tcamas=cargar_registros_obj("SELECT * FROM tipo_camas ORDER BY cama_num_ini;", true);
	
	$bcamas=cargar_registros_obj("
	SELECT *
	FROM bloqueo_camas
	JOIN bloqueo_camas_motivos ON bloq_motivo=bmot_id
	JOIN funcionario USING (func_id)
	LEFT JOIN tipo_camas ON
	cama_num_ini<=bloq_numero_cama AND cama_num_fin>=bloq_numero_cama
	LEFT JOIN clasifica_camas ON 
	tcama_num_ini<=bloq_numero_cama AND tcama_num_fin>=bloq_numero_cama
	WHERE (
		bloq_fecha_ini<=CURRENT_DATE AND 
		(
			bloq_fecha_fin IS NULL OR 
			bloq_fecha_fin>=CURRENT_DATE
		)
	)
	ORDER BY bloq_fecha_ini, bloq_numero_cama");
	
	$bloq=array();
	
	for($i=0;$i<sizeof($bcamas);$i++) {
		$bloq[$bcamas[$i]['bloq_numero_cama']*1]=$bcamas[$i];
	}

	$html='';
	
	for($i=0;$i<sizeof($ccamas);$i++) {
	
		$n='sector_'.$ccamas[$i]['tcama_id'];
		$t=$ccamas[$i]['tcama_tipo'];	
		
		if(($ccamas[$i]['tcama_id']*1)>55)
			$orden_cc = 'cama_tipo';
		else
			$orden_cc = 'cama_num_ini';

		$tc=cargar_registros_obj("SELECT * FROM tipo_camas WHERE cama_num_ini BETWEEN ".$ccamas[$i]['tcama_num_ini']." AND ".$ccamas[$i]['tcama_num_fin'].' ORDER BY '.$orden_cc, true);
	
		$html.='<div class="sector" id="'.$n.'" name="'.$n.'">
			<div class="sector_titulo">'.$t.'</div>';
		
		for($k=0;$k<sizeof($tc);$k++) {		

			if($tc[$k]['cama_color']!='')
				$estilo='style="background-color:#'.$tc[$k]['cama_color'].'"';
			else 
				$estilo='';

			$html.='<div class="sala_titulo">
						'.($tc[$k]['cama_tipo']).'
					  </div>
					  <div class="sala" '.$estilo.'>';
			
			$j = 1;
			
			for(  $n=$tc[$k]['cama_num_ini']*1;
					$n<=$tc[$k]['cama_num_fin']*1;
					$n++) {

			$nn=($n-$ccamas[$i]['tcama_num_ini'])+1;

			if(!isset($bloq[$n])) {
		
				$html.="
					<table cellpadding=0 cellspacing=0 
					class='cama' id='cama_$n' name='cama_$n'>
					<tr>
					<td class='ucama' id='icama_$n'></td>
					</tr>
					<tr>
					<td class='nro_cama' style='width:20px;'>$j</td>
					</tr></table>
					";
					
			} else {

				$html.="
					<table cellpadding=0 cellspacing=0 
					class='cama' id='cama_$n' name='cama_$n'>
					<tr>
					<td class='ucama_bloq' id='icama_$n'></td>
					</tr>
					<tr>
					<td class='nro_cama' style='width:20px;'>$j</td>
					</tr></table>
					";
				
			}
				$j++;
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
			WHERE hosp_fecha_egr IS NULL AND hosp_numero_cama=0 AND hosp_anulado=0
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
					case 'A1': case 'A2': case 'A3': case 'B1': 
						$icono.='a'; break;	
					case 'B2': case 'B3': case 'C1': case 'C2':  
						$icono.='b'; break;	
					case 'C3': case 'D1': case 'D2': case 'D3': 
						$icono.='c'; break;	
					default: $icono.='c'; break;	
				}


		$hp.='<tr>
				<td class="ucama" id="icama_0_'.$l[$i]['hosp_id'].'">
				<img class="uso_cama" id="hospi_0_'.$l[$i]['hosp_id'].'" src="'.$icono.'.png" /></td>
				<td style="text-align:right;font-weight:bold;">
				'.$l[$i]['pac_rut'].'</td>
				<td>
				'.$l[$i]['pac_nombres'].'
				'.$l[$i]['pac_appat'].' 
				'.$l[$i]['pac_apmat'].' 
				</td> 
				<td style="text-align:center;font-weight:bold;">'.$l[$i]['hosp_criticidad'].'</td></tr>';	
	}
	
	$hp.='</table>';

?>
<html>
  <head>
  
 	 <LINK href="../../css/interface.css" type='text/css' rel='stylesheet'>
     
     <script type="text/javascript" src="jquery-1.4.2.min.js"></script>
     <script type="text/javascript" src="jquery-ui-1.8.custom.min.js"></script>
     <script type="text/javascript" src="json2.js"></script>

<!--
  	  <link type="text/css" href="http://jqueryui.com/latest/themes/base/jquery.ui.all.css" rel="stylesheet" />
		
  		<script type="text/javascript" src="http://jqueryui.com/latest/jquery-1.4.2.js"></script>
  		<script type="text/javascript" src="http://jquery-ui.googlecode.com/svn/tags/1.8rc1/jquery-1.4.1.js"></script>
		<script type="text/javascript" src="http://jquery-ui.googlecode.com/svn/tags/1.8rc1/ui/jquery-ui.js"></script>
-->

    	<script type="text/javascript">

		var ccamas=<?php echo json_encode($ccamas); ?>;
		var tcamas=<?php echo json_encode($tcamas); ?>;
		var bcamas=<?php echo json_encode($bloq); ?>;
		
		var mov_estado=0;
		var mov_id=0;
		var mov_id_dst=0;
		var ucama_id=0;
		var movs=[];

		function mover_camas(event) {

			var p=$(this);
			
			if(p.attr('id')=='lista_salidas' && mov_estado==0) {
				//console.log('click malo');
				return;
			}
						
			var o=p.find('.uso_cama');			
			
			if(p.html()!='' && mov_estado==0) {

				id_obj=o.attr('id').split('_');
				
				//console.log(o.attr('id'));
				
				ucama_id=id_obj[1]*1;
				if(ucama_id==0)
					ucama_id=id_obj[2]*1;
				
				id_obj=p.attr('id').split('_');
				mov_id=id_obj[1]*1;
				mov_estado=1;
			
				var j;
				
				for(j=0;j<ucamas.length;j++) {
					if(ucamas[j].hosp_id*1==ucama_id*1) {
							break;
					}
				}
					
				var u=ucamas[j];
				var nom=u.pac_nombres+' '+u.pac_appat+' '+u.pac_apmat;
				      	
				$('#follower_desc').html(nom);				
				
				$('#follower').show();
      	
			} else if((p.html()=='' || p.attr('id')=='lista_salidas') 
							&& mov_estado==1) {
				
				if(mov_id!=0) {
					
					var tmp=$('#icama_'+mov_id).html();
					$('#icama_'+mov_id).html('');

				} else {

					var tmp=$('#icama_0_'+ucama_id).html();
					$('#icama_0_'+ucama_id).html('');
					
				}

				if(p.attr('id')!='lista_salidas') {
					id_obj=p.attr('id').split('_');
					mov_id_dst=id_obj[1]*1;
				} else mov_id_dst=-1;
				
				var num=movs.length;
				
				movs[num]=new Object();
				movs[num].ucama_id=ucama_id;
				movs[num].mov_id=mov_id;				
				movs[num].mov_id_dst=mov_id_dst;				
				
				redibujar_movs();				

				if(p.attr('id')!='lista_salidas')
					p.html(tmp);

				var i=0;

				for(;i<ucamas.length;i++) {
					if(ucamas[i].hosp_id==ucama_id) {
						break;
					}
				}

				mov_estado=0;
				
				$("#follower").hide();	

			}	
				
		}      
      
		$(document).ready(function(){

		   $("#follower").hide();
    		
    		$(document).mousemove(function(e){
				if(mov_estado==1) {        	
	        		$("#follower").show();
	        		$("#follower").css({
	            	top: (e.pageY + 15) + "px",
	            	left: (e.pageX + 15) + "px"
	        		});
	        	}   	
        	});


			$('.ucama').click(mover_camas);
			
			$('#lista_salidas').click(mover_camas);
						
			cargar_camas();			
			
   	});      



      
      var ucamas;
      
      function cargar_camas() {
      
			 $.getJSON('listado_camas.php', function(data) {
			 	
				ucamas=data;	 					 	
  				dibujar_pacientes();
  				redibujar_movs();
  				
			 });     
      	
      }
      
      function dibujar_pacientes() {
      
			for(var i=0;i<ucamas.length;i++) {
			
				var ncama=ucamas[i].hosp_numero_cama;
				
				var icono=tipo_icono(ucamas[i].sex_id, ucamas[i].hosp_criticidad);					
				
				$('#icama_'+ucamas[i].hosp_numero_cama).html('<img id="hospi_'+ucamas[i].hosp_id+'_'+i+'" name="hospi_'+ucamas[i].hosp_id+'_'+i+'" class="uso_cama" src="'+icono+'.png" />');								
								
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
      	
      	$('#pac_rut').html(u.pac_rut);
      	$('#pac_nombre').html(u.pac_appat+' '+u.pac_apmat+' '+u.pac_nombres);
      	
      	$('#hosp_criticidad').html(u.hosp_criticidad);
      	
      	$('#hosp_fecha_ing').html(u.hosp_fecha_ing);
      	$('#ciud_desc').html(u.ciud_desc);
      	
      	$('#doc_nombre').html(u.doc_nombres+' '+u.doc_paterno+' '+u.doc_materno);
      	
      	$('#popup').show();
      	
      }

      function datos_bloq(i) {
      	
      	var b=bcamas[i];      	
      	
      	$('#bloq_fecha_ini').html(b.bloq_fecha_ini);
      	$('#bloq_fecha_fin').html(b.bloq_fecha_fin);
      	
      	$('#bmot_desc').html(b.bmot_desc);
      	
      	$('#bloq_observaciones').html(b.bloq_observaciones);
      	
      	$('#func_nombre').html(b.func_nombre);
      	
      	$('#popup2').show();
      	
      }
		
	  function listado() {
		  
			if($('#tcama_id').val()!=-1){
			
				window.open('form.php?tcama_id='+$('#tcama_id').val(),'_self');
			
			} else {
				
				window.open('form.php','_self');
			
			}		
		  }      
      
      function redibujar_movs() {
      	
			var html='<table style="width:100%;font-size:11px;"><tr class="tabla_header">';
			html+='<td colspan=3>Paciente</td><td>Or&iacute;gen</td><td>Destino</td><td>Eliminar</td></tr>';      	

			var html2='<table style="width:100%;font-size:11px;"><tr class="tabla_header">';
			html2+='<td colspan=3>Paciente</td><td>Cama</td></tr>';      	
      	
			for(var i=0;i<movs.length;i++) {
				
				html+='<tr>';
				html+=datos_pac(movs[i].ucama_id);
				html+='<td style="text-align:center;">'+(movs[i].mov_id!=0?movs[i].mov_id:'<i>(n/a)</i>')+'</td>';
				
				if(movs[i].mov_id_dst!=-1)
					html+='<td style="text-align:center;">'+movs[i].mov_id_dst+'</td>';
				else
					html+='<td style="text-align:center;">(Alta)</td>';				

				if(i==movs.length-1) {
					html+='<td style="text-align:center;"><center><img src="../../iconos/delete.png" ';
					html+='style="cursor:pointer;" onClick="eliminar_mov('+i+');" /></center></td>';
				} else {
					html+='<td>&nbsp;</td>';
				}
				
				html+='</tr>';	
				
				if(movs[i].mov_id_dst==-1) {
					html2+='<tr>';
					html2+=datos_pac(movs[i].ucama_id);
					html2+='<td style="text-align:center;">'+movs[i].mov_id+'</td>';
					html2+='</tr>';						
				}
			}      	
      	
			html+='</td></tr></table>';
			
			html2+='</td></tr></table>';
			
			$('#lista_movimientos').html(html);	      	
			$('#lista_salidas').html(html2);	      	
      	
      }

		function eliminar_mov(i) {
		
			var mmov=movs.splice(i,1);
			
			var destino=mmov[0].mov_id_dst;
			var origen=mmov[0].mov_id;

			var j=0;
	
			for(;j<ucamas.length;j++) {
				if(ucamas[j].hosp_id==mmov[0].ucama_id) {
					break;
				}
			}
			
			if(destino!=-1) {
				
				var _html=$('#icama_'+destino).html();
				$('#icama_'+destino).html('');
				
			} else {
				
				var ncama=ucamas[j].hosp_numero_cama;
				var icono=tipo_icono(ucamas[j].sex_id, ucamas[j].hosp_criticidad);					
				var _html='<img id="hospi_'+ucamas[j].hosp_id+'_'+j+'" name="hospi_'+ucamas[j].hosp_id+'_'+i+'" class="uso_cama" src="'+icono+'.png" />';
												
			}

			if(origen!=0) {
							
				$('#icama_'+origen).html(_html);
					
				mov_estado=0;
					
				$("#follower").hide();	

			} else {
			
				$('#icama_0_'+mmov[0].ucama_id).html(_html);
				
			}
			
			redibujar_movs();		
			
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
		
		guardar_movs=function(chk) {
		
			if(movs.length==0) {
				alert('No ha ingresado nuevos movimientos.');
				return;	
			}		
		
			if(chk) {
			
				fnd=false;		
		
				for(var i=0;i<movs.length;i++) {
					if(movs[i].mov_id_dst==-1) {
						fnd=true;
						break;
					}	
				}
				
				if(fnd) {
					
			      top=Math.round(screen.height/2)-265;
			      left=Math.round(screen.width/2)-400;
			        
			      new_win = 
			      window.open('form_altas.php',
			      'win_camas2', 'toolbar=no, location=no, directories=no, status=no, '+
			      'menubar=no, scrollbars=no, resizable=no, width=800, height=580, '+
			      'top='+top+', left='+left);
			          
			      new_win.focus();
			      
			     	return;
								
				}		
			
			}
			
			$.ajax({
				url: 'sql_camas.php',
				type: 'POST', 
				data: {movs: JSON.stringify(movs)}, 
				success: function(data) {
					alert('Movimientos guardados exitosamente.');
					window.opener.listado();
					window.location.reload();
				}
			});		
			
		}
	
		$(document).ready(
			function() {

			  		$('.ucama').hover(function(e) {
						
						chk=($(this).find('img'));
						
						if(chk.length>0) {
							var dat=$(chk).attr('id').split('_');
							datos_hosp(dat[2]);
						} 
						
					}, function (e) {
						
						$('#popup').hide();						
						
					});

			  		$('.ucama_bloq').hover(function(e) {
						
						//console.log('!!');
						
						chk=($(this).find('img'));
						
						if(chk.length>0) {
							var dat=$(chk).attr('id').split('_');
							//console.log(dat);
							datos_bloq(dat[2]);
						} 
						
					}, function (e) {
						
						$('#popup2').hide();						
						
					});
				
					$(window).resize(function() {
			  			$('#diagrama').height( $(window).height() * 0.7 );
			  			$('#lista_movimientos').height( $(window).height() * 0.15 );
					});
			
			  		$('#diagrama').height( $(window).height() * 0.7 );
			  		$('#lista_movimientos').height( $(window).height() * 0.15 );			  		
									
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
			height:300px;border:1px solid black;
			background-color:#ffaa00;overflow:auto;padding:2px;
		}

		#lista_salidas {
			height:120px;border:1px solid black;
			background-color:#00aaff;overflow:auto;padding:2px;
		}

		#lista_movimientos {
			height:100px;border:1px solid black;
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
		
		.img_cama {
			background-image: url(icono_cama3.png);
			width:24px;height:18px;
		}
		
		.ucama {
			width:24px;height:18px;
			text-align:center;
		}

		.ucama_bloq {
			width:24px;height:18px;
			text-align:center;
		}

		#diagrama .ucama {
			background-image: url(icono_cama3.png);
			width:24px;height:18px;
			text-align:center;
		}

		#diagrama .ucama_bloq {
			background-image: url(icono_cama_bloq.png);
			width:24px;height:18px;
			text-align:center;
		}

		.uso_cama {
			width:12px;height:12px;cursor:pointer;
		}

		.nro_cama {
			width:15px;text-align:center;
		}
		
		.paciente {
			margin:5px;	
		}
		
		#follower { background: #fff; padding: 2px; border: 1px solid #ddd; position: absolute; }
		#follower_desc { font-size: 11px; }
		
		#popup {
			position: absolute;
			width:350px;
			margin-top:auto;
			margin-right:auto;
			top: 10px;
			right:10px;
			font-size:10px;	
			background-color:#eeeeee;
			border:1px solid black;
		}

		#popup2 {
			position: absolute;
			width:350px;
			margin-top:auto;
			margin-right:auto;
			top: 10px;
			right:10px;
			font-size:10px;	
			background-color:#eeeeee;
			border:1px solid black;
		}
			    
    </style>
 
	<title>Gesti&oacute;n Centralizada de Camas</title> 
 
  </head>
  <body topmargin=0 leftmargin=0 rightmargin=0>

	<div id='popup' style='display:none;' class='ui-corner-all'>

	<table style='font-size:10px;'>
	<tr><td class="datos_der">RUT:</td><td id='pac_rut' style='font-weight:bold;'></td></tr>    
    <tr><td class="datos_der">Nombre:</td><td id='pac_nombre'><i></i></td></tr>
    <tr><td class="datos_der">Cat.:</td><td id='hosp_criticidad' style='font-weight:bold;'></td></tr>
    <tr><td class="datos_der">Fecha Ingreso:</td><td id='hosp_fecha_ing'></td></tr>
    <tr><td class="datos_der">Or&iacute;gen:</td><td id='ciud_desc'></td></tr>  	
    <tr><td class="datos_der">M&eacute;dico Tratante:</td><td id='doc_nombre'>(Sin Asignar...)</td></tr>
    </table>
	
	</div>

	<div id='popup2' style='display:none;' class='ui-corner-all'>

	<table style='font-size:10px;'>
	<tr><td class="datos_der">Fecha Inicio:</td><td id='bloq_fecha_ini' style='font-weight:bold;'></td></tr>    
    <tr><td class="datos_der">Fecha Final:</td><td id='bloq_fecha_fin'><i></i></td></tr>
    <tr><td class="datos_der">Motivo:</td><td id='bmot_desc' style='font-weight:bold;'></td></tr>
    <tr><td class="datos_der">Observaciones:</td><td id='bloq_observaciones'></td></tr>
    <tr><td class="datos_der">Funcionario:</td><td id='func_nombre'></td></tr>
    </table>
	
	</div>


	<div class='sub-content'>
	<img src='../../iconos/building.png'>
	<b>Movimiento y Gesti&oacute;n Centralizada de Camas
	Sector:&nbsp;</b>
	<select id='tcama_id' name='tcama_id' onChange='listado();' >
	<option value='-1'>(Ver Todo...)</option>
	<?php echo $ccamashtml; ?>
	</select>
	</td>
	</div>

<center>

<table style='width:100%;'>
<tr><td valign='top' style='width:30%;'>

	<center>
	
	<div class='titulo_content'>
	<img src='../../iconos/user_go.png'>
	Pacientes Sin Asignaci&oacute;n	
	</div>
	
	<div id='lista_entradas'>
	<?php echo $hp; ?>
	</div>

	<!--<div class='titulo_content'>
	<img src='../../iconos/book_next.png'>
	Pacientes de Alta	
	</div>
			
	<div id='lista_salidas'>
	
	</div> -->
	
	<br />
	
	<!--<input type='button' id='guardar' name='guardar' 
	onClick='guardar_movs(true);' value='-- Guardar Movimiento(s)... --' />
	<br />
	<br />
	<input type='button' id='limpiar' name='limpiar' 
	onClick='location.reload();' value='-- Limpiar Formulario... --' />
	-->
	</center>

</td><td style='width:75%;'>

	<div id='diagrama' name='diagrama'>
	<center>
	<?php echo $html; ?>
	</center>	
	</div>
	
</td></tr>

<tr><td colspan=2>
	
	<div class='titulo_content'>
	<img src='../../iconos/arrow_refresh.png'>
	Movimientos	
	</div>
			
	<div id='lista_movimientos'>
	
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
