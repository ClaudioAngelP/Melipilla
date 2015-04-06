<?php 

	require_once('../../conectar_db.php');
	
	$personal=cargar_registros_obj("SELECT * FROM personal_pabellon ORDER BY pp_id", true);
	$pabellones=cargar_registros_obj("SELECT * FROM fappab_pabellones ORDER BY fapp_id", true);	

?>

<script>

	var personal=<?php echo json_encode($personal); ?>;
	var pabellones=<?php echo json_encode($pabellones); ?>;

	guardar_pp=function(pp_id) {
		
		var params='pp_rut='+encodeURIComponent($('pp_rut_'+pp_id).value)+'&';
		params+='pp_paterno='+encodeURIComponent($('pp_paterno_'+pp_id).value)+'&';
		params+='pp_materno='+encodeURIComponent($('pp_materno_'+pp_id).value)+'&';
		params+='pp_nombres='+encodeURIComponent($('pp_nombres_'+pp_id).value)+'&';
		params+='pp_caracteristicas='+encodeURIComponent($('pp_caracteristicas_'+pp_id).value)+'&';
		params+='pp_desc='+encodeURIComponent($('pp_desc_'+pp_id).value)+'&';
		params+='pp_id='+pp_id;		
		
		var myAjax=new Ajax.Request(
			'administracion/mantenedor_pabellon/sql.php',
			{
				method:'post',
				parameters:params,
				onComplete:function(r) {
					var d=r.responseText.evalJSON(true);
					
					alert('Registro guardado exitosamente.');					
					
					personal=d;
					
					listar_personal();
				}					
			}						
		);
		
	}		

	eliminar_pp=function(pp_id) {
		
		var conf=confirm("&iquest;Desea eliminar el funcionario?".unescapeHTML());
		if(!conf) return;		
		
		var params='pp_id='+pp_id;		
		
		var myAjax=new Ajax.Request(
			'administracion/mantenedor_pabellon/sql_eliminar.php',
			{
				method:'post',
				parameters:params,
				onComplete:function(r) {
					var d=r.responseText.evalJSON(true);
					
					personal=d;
					
					listar_personal();
				}					
			}						
		);
		
	}		

	listar_personal=function() {
		
		var html='<table style="width:100%;">';
		html+='<tr class="tabla_header">';
		html+='<td>R.U.T.</td>';		
		html+='<td>Paterno</td>';		
		html+='<td>Materno</td>';		
		html+='<td>Nombres</td>';		
		html+='<td>Desc.</td>';		
		html+='<td>Funciones</td>';		
		html+='<td colspan=2>Acciones</td>';		
		html+='</tr>';

		for(var i=0;i<personal.length;i++) {
			var clase=(i%2==0)?'tabla_fila':'tabla_fila2';
			html+='<tr class="'+clase+'" style="font-size:10px;">';
			html+='<td><input type="text" style="width:100%;text-align:right;" id="pp_rut_'+personal[i].pp_id+'" name="pp_rut_'+personal[i].pp_id+'" value="'+personal[i].pp_rut+'" /></td>';
			html+='<td><input type="text" style="width:100%;" id="pp_paterno_'+personal[i].pp_id+'" name="pp_paterno_'+personal[i].pp_id+'" value="'+personal[i].pp_paterno+'" /></td>';
			html+='<td><input type="text" style="width:100%;" id="pp_materno_'+personal[i].pp_id+'" name="pp_materno_'+personal[i].pp_id+'" value="'+personal[i].pp_materno+'" /></td>';
			html+='<td><input type="text" style="width:100%;" id="pp_nombres_'+personal[i].pp_id+'" name="pp_nombres_'+personal[i].pp_id+'" value="'+personal[i].pp_nombres+'" /></td>';
			html+='<td><input type="text" style="width:100%;" id="pp_desc_'+personal[i].pp_id+'" name="pp_desc_'+personal[i].pp_id+'" value="'+personal[i].pp_desc+'" /></td>';
			html+='<td><input type="text" style="width:100%;" id="pp_caracteristicas_'+personal[i].pp_id+'" name="pp_caracteristicas_'+personal[i].pp_id+'" value="'+personal[i].pp_caracteristicas+'" /></td>';
			html+='<td><center><img src="iconos/disk.png" onClick="guardar_pp('+personal[i].pp_id+');"/></center></td>'
			html+='<td><center><img src="iconos/delete.png" onClick="eliminar_pp('+personal[i].pp_id+');"/></center></td>'
			html+='</tr>';	
		}

			var clase=(i%2==0)?'tabla_fila':'tabla_fila2';
			var i=0;
			
			html+='<tr class="'+clase+'" style="font-size:10px;">';			
			html+='<td><input type="text" style="width:100%;text-align:right;" id="pp_rut_'+i+'" name="pp_rut_'+i+'" value="" /></td>';
			html+='<td><input type="text" style="width:100%;" id="pp_paterno_'+i+'" name="pp_paterno_'+i+'" value="" /></td>';
			html+='<td><input type="text" style="width:100%;" id="pp_materno_'+i+'" name="pp_materno_'+i+'" value="" /></td>';
			html+='<td><input type="text" style="width:100%;" id="pp_nombres_'+i+'" name="pp_nombres_'+i+'" value="" /></td>';
			html+='<td><input type="text" style="width:100%;" id="pp_desc_'+i+'" name="pp_desc_'+i+'" value="" /></td>';
			html+='<td><input type="text" style="width:100%;" id="pp_caracteristicas_'+i+'" name="pp_caracteristicas_'+i+'" value="" /></td>';
			html+='<td><center><img src="iconos/disk.png" onClick="guardar_pp(0);"/></center></td>'
			html+='<td>&nbsp;</td>'
			html+='</tr>';	

		
		html+='</table>';
		
		$('lista_personal').innerHTML=html;
			
	}
	
	listar_pabellones = function() {
		var htmlpab='<table style="width:100%;">';
		htmlpab+='<tr class="tabla_header">';
		htmlpab+='<td>Pabell&oacute;n</td>';		
		htmlpab+='<td>Activar</td>';		
		htmlpab+='</tr>';

		for(var i=0;i<pabellones.length;i++) {
			var clase=(i%2==0)?'tabla_fila':'tabla_fila2';
			
			if(pabellones[i].fapp_activado=='t') chk='CHECKED'; else chk='';
			
			htmlpab+='<tr class='+clase+' onMouseOver=\'this.className=\"mouse_over\";\' onMouseOut=\'this.className=\"'+clase+'\";\'>';
			htmlpab+='<td style="font-weight:bold;">'+pabellones[i].fapp_desc+'</td>';
			htmlpab+='<td><center><input type="checkbox" id="pab_'+pabellones[i].fapp_id+'" name="'+pabellones[i].fapp_id+'" onClick="activar_pab('+pabellones[i].fapp_id+');" '+chk+' /></center></td>';
			htmlpab+='</tr>';	
		}
		
		htmlpab+='</table>';
		
		$('lista_pabellones').innerHTML=htmlpab;
	}		
	
	activar_pab = function(pos){

		var val=($('pab_'+pos).checked?'1':'0');	
		
		var params='fapp_id='+pos+'&check='+val;	
		
		var myAjax=new Ajax.Request(
			'administracion/mantenedor_pabellon/sql_activar_pab.php',
			{
				method:'post',
				parameters:params,
				onComplete:function(r) {
					var d=r.responseText.evalJSON(true);
					
					if(d)
						alert('Operaci&oacute;n Realizada.'.unescapeHTML());
				}					
			}						
		);
	}

</script>

<body class='fuente_por_defecto popup_background'>

<center>
<div class='sub-content' style='width:950px;'>
<div class='sub-content'>
<img src='iconos/group.png' />
<b>Personal de Pabell&oacute;n</b>
</div>

<div class='sub-content2' id='lista_personal' style='height:250px;overflow:auto;'>

</div>

</div>

<div class='sub-content' style='width:450px;'>
	<div class='sub-content'>
		<img src='iconos/building.png' />
			<b>Pabellones</b>
	</div>
	
	<div class='sub-content2' id='lista_pabellones' style='height:250px;overflow:auto;'>
	</div>
	
</div>

</div>
</center>

</body>
</html>

<script>

	listar_personal();
	$('lista_personal').scrollTop=0;
	
	listar_pabellones();
	$('lista_pabellones').scrollTop=0;

</script>
