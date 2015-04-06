<?php 

	require_once('../../conectar_db.php');
	
?>

<script>

	abrir_ficha = function(id) {
		inter_ficha = window.open('interconsultas/visualizar_ic.php?tipo=revisar_inter_ficha&inter_id='+id,		'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
		inter_ficha.focus();
	}

	abrir_oa = function(id) {
		inter_ficha = window.open('interconsultas/visualizar_oa.php?tipo=revisar&oa_id='+id,		'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
		inter_ficha.focus();
	}

	descargar_sigges=function( fase ) {

		if($('tipo').value==0) {
			$('pac').value=$('pac').value.toUpperCase();
			if(!comprobar_rut($('pac').value)) {
				$('pac').style.background='red';
				alert('R.U.T. incorrecto.');
				$('pac').select(); $('pac').focus();
				return;				
			} 
		}
		
		$('pac').style.background='';
		
		var params='';		
		
		if(fase==1) {
			params+='&confirma=1&ic=1';
		}
		
		/*if(fase==0)
			$('info').innerHTML='<br /><br /><br /><br /><img src="imagenes/ajax-loader3.gif" /><br /><br /> Consultando a SIGGES...';
		else if(fase==1)
			$('info').innerHTML='<br /><br /><br /><br /><img src="imagenes/ajax-loader3.gif" /><br /><br /> Descargando Historial Cl&iacute;nico<br /> desde SIGGES...<br /><i>(Esto puede demorar algunos segundos...)</i>';*/
			
		var myAjax=new Ajax.Request(
			'interconsultas/ingreso_inter_auto/sql_queue.php',
			{
				method:'post',
				parameters: $('pac').serialize()+'&'+$('folio').serialize(),
				onComplete:function(r) {
					
					if(trim(r.responseText)=='')
						listar_queue();
					else {
						listar_queue();
						alert(r.responseText);
					}
						
				}						
			}		
		);		
		
		return;		
		
		var myAjax=new Ajax.Updater(
			'info',
			'conectores/sigges/descargar_datos.php',
			{
				method:'post',
				parameters:$('tipo').serialize()+'&'+$('pac').serialize()+params,
				evalScripts:true, 
				onComplete:function(r) {
					
				}
			}		
		);		
		
	}
	
	listar_queue=function() {
		var myAjax=new Ajax.Updater(
			'info',
			'interconsultas/ingreso_inter_auto/listar_queue.php'		
		);	
	}
	
	eliminar_queue=function(pacq_id) {
		var conf=confirm("&iquest;Desea eliminar la descarga de su bandeja?".unescapeHTML());
		if(!conf) return;
		var myAjax=new Ajax.Request(
			'interconsultas/ingreso_inter_auto/sql_eliminar.php',
			{
				method:'post',
				parameters:'pacq_id='+pacq_id,
				onComplete:function(r) {
					listar_queue();
				}	
			}		
		);	
	}
	
	verdoc = function(pacq_id) {
		var val=$('pacq_'+pacq_id).value;
		var t=val.substr(0,2);
		var id=val.substr(2,val.length-2);
	
		if(t=='IC') abrir_ficha(id);
		else abrir_oa(id);	
			
	}		
	
	validar_ic=function() {
		
		var chk=$('lista_ics').getElementsByTagName('input');
		
		var fnd=false;		
		
		for(var i=0;i<chk.length;i++) {
			if(chk[i].type=='checkbox' && chk[i].checked) {
				fnd=true; break;	
			}	
		}
		
		if(!fnd) {
			alert('Debe seleccionar por lo menos una interconsulta para confirmar ingreso al sistema.');
			return;	
		}		
		
		var myAjax=new Ajax.Request(
			'interconsultas/ingreso_inter_auto/sql_validar.php',
			{
				method:'post',
				parameters:$('pac_id').serialize()+'&'+$('lista_ics').serialize(),
				onComplete:function(r) {
					alert('Interconsulta(s) ingresadas al sistema.');
				}	
			}		
		);			
			
	}		
	
</script>

<center>

<div class='sub-content' style='width:950px;'>

<div class='sub-content'>
<img src='iconos/application_put.png'>
<b>Ingreso Autom&aacute;tico de Interconsultas</b>
</div>

<div class='sub-content'>
<table style='width:100%;'>
<tr><td style='text-align:right;width:100px;'>
Paciente:
</td><td style='width:150px;'>
<select id='tipo' name='tipo' style='width:100%;'>
<option value='0'>R.U.T.</option>
</select>
</td><td>
<input type='text' id='pac' name='pac' value='' size=25>
</td><td>Nro. Folio:</td><td>
<input type='text' id='folio' name='folio' value='' size=25>
</td><td>
<input type='button' id='descarga' name='descarga' value='Aceptar...' 
onClick='descargar_sigges(0);' />
</td></tr>
</table>

</div>

<div id='info' name='info' class='sub-content2' 
style='height:300px;overflow:auto;'>


</div>

<center>
<input type='button' value='-- Actualizar Listado... --' onClick='listar_queue();'
</center>

</div>
</center>

<script>

	$('pac').focus();

</script>