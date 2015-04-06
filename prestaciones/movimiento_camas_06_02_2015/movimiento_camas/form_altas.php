<?php 

	require_once('../../conectar_db.php');

?>

<html>
<title>Definir Destino de Altas</title>

<?php cabecera_popup('../..'); ?>

<script>

		movs=window.opener.movs;
		ucamas=window.opener.ucamas;

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

		function datos_pac(id) {
			
			var i=0;
			
			for(;i<ucamas.length;i++)
      		if(id==ucamas[i].hosp_id) break;
      		
      	u=ucamas[i];

      	var icono=tipo_icono(u.sex_id, u.hosp_criticidad);
      	
      	var html='<td style="width:20px;"><img class="uso_cama" src="'+icono+'.png" /></td><td style="text-align:right;">'+u.pac_rut+'</td><td>'+u.pac_appat+' '+u.pac_apmat+' '+u.pac_nombres+'</td>';
      	
      	return html;
      	      				
		}

		function destinos_alta(mov_id) {
			var html='<option value="0">(Seleccionar...)</option>';	
			html+='<option value="1">Alta a Domicilio</option>';	
			html+='<option value="2">Derivaci&oacute;n</option>';	
			html+='<option value="3">Fallecido</option>';	
			html+='<option value="4">Fugados</option>';
			html+='<option value="5">Otro</option>';		
			return html;
		}

      function redibujar_movs() {
      	
			var html2='<table style="width:100%;font-size:11px;"><tr class="tabla_header">';
			html2+='<td colspan=3>Paciente</td><td>Cama</td><td>Destino</td><td>Instituci&oacute;n</td></tr>';      	
      	
			for(var i=0;i<movs.length;i++) {

				if(movs[i].mov_id_dst==-1) {
					html2+='<tr>';
					html2+=datos_pac(movs[i].ucama_id);
					html2+='<td style="text-align:center;">'+movs[i].mov_id+'</td>';
					html2+='<td><center><select id="dest_'+movs[i].mov_id+'" name="dest_'+movs[i].mov_id+'" onChange="fix_fields('+movs[i].mov_id+');">';
					html2+=destinos_alta(movs[i].mov_id);
					html2+='</select></center></td><td>';
					html2+='<input type="hidden" id="inst_id_'+movs[i].mov_id+'" name="inst_id_'+movs[i].mov_id+'" />';
					html2+='<input type="text" size=35 id="inst_desc_'+movs[i].mov_id+'" name="inst_desc_'+movs[i].mov_id+'" DISABLED />';
					html2+='</td></tr>';						
				}


			}

			html2+='</td></tr></table>';
			
			$('altas').innerHTML=html2;	      	
			
		}
		
		function guardar() {
		
			for(var i=0;i<movs.length;i++) {
				if(movs[i].mov_id_dst==-1)
				if($('dest_'+movs[i].mov_id).value*1==0) {
					alert('Debe definir el destino de todas las altas ingresadas.');
					return;	
				} else {
					movs[i].destino=$('dest_'+movs[i].mov_id).value;	
				}
			}
			
			window.opener.movs=movs;			
			
			var fn=window.opener.guardar_movs;
			fn(false);
			window.close();					
			
		}
		
		function fix_fields(mov_id) {
			if(($('dest_'+mov_id).value*1)==2) {
				$('inst_id_'+mov_id).value='';
				$('inst_desc_'+mov_id).value='';
				$('inst_desc_'+mov_id).disabled=false;
				setup_autocomplete(mov_id);
			} else {
				$('inst_id_'+mov_id).value='';
				$('inst_desc_'+mov_id).value='';
				$('inst_desc_'+mov_id).disabled=true;				
			}				
		}					

	var tmp_mov_id;

    seleccionar_inst = function(d) {
    
      $('inst_id_'+tmp_mov_id).value=d[0];
      $('inst_desc_'+tmp_mov_id).value=d[2].unescapeHTML();
    
    }
    
    function setup_autocomplete( mov_id ) {
    	
		 Event.observe($('inst_desc_'+mov_id),'focus',function() {
		 	tmp_mov_id=mov_id;	
		 });    	
    	
	    var autocompletar_institucion = new AutoComplete(
	      'inst_desc_'+mov_id, 
	      '../../autocompletar_sql.php',
	      function() {
	        if($('inst_desc_'+mov_id).value.length<3) return false;
	
	        return {
	          method: 'get',
	          parameters: 'tipo=instituciones&cadena='+encodeURIComponent($('inst_desc_'+mov_id).value)
	        }
	
	      }, 'autocomplete', 350, 200, 150, 2, 3, seleccionar_inst);
      
     }

</script>

<body class='fuente_por_defecto popup_background' onLoad='redibujar_movs();'>

<div class='sub-content'>
<img src='../../iconos/user_go.png' />
<b>Definir Destino de Altas</b>
</div>

<div class='sub-content' id='altas'>

</div>

<center>
	<input type='button' id='guardar' name='guardar' 
	value='Guardar Destino de Altas...' onClick='guardar();' />
</center>

</body>
</html>
