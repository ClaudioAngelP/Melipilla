<?php 

	require_once("../conectar_db.php");

	function ch($v) {
		if(isset($v)) return $v; else return '';
	}
	
	$clase=pg_escape_string(utf8_decode($_GET['clase']));
	$codigo=pg_escape_string(utf8_decode($_GET['codigo']));
	$numero=$_GET['numero']*1;
	$letra=pg_escape_string($_GET['letra']);

	$t=cargar_registro("SELECT * FROM tipo_sepultura WHERE tsep_clase='$clase'", true);	
	$s=cargar_registro("SELECT * FROM propiedad_sepultura
									LEFT JOIN clientes USING (clirut) 
									WHERE ps_clase='$clase' AND ps_codigo='$codigo'
									AND ps_numero=$numero AND ps_letra='$letra' 
									AND ps_vigente
									", true);

	$u=cargar_registros_obj("SELECT * FROM uso_sepultura
									WHERE sep_clase='$clase' AND sep_codigo='$codigo'
									AND sep_numero=$numero AND us_estado<=2", true);

	if($s) {
		
		$b=cargar_registro("SELECT *, bolfec::date AS bolfec FROM boletines WHERE bolnum=".$s['bolnum']);
		
		if($b) 
			$cr=cargar_registro("SELECT * FROM creditos WHERE crecod=".$b['crecod']);
		else $cr=false;
		
		/*if($cr) 
			$cl=cargar_registro("SELECT * FROM clientes WHERE clirut=".($cr['clirut']*1) );
		else $cl=false;
		
		if(!$cl)
			$cl=cargar_registro("SELECT * FROM clientes WHERE clirut=".($b['clirut']*1) );
		*/
		
		if(($s['clirut']*1)!=0)
			$cl=cargar_registro("SELECT * FROM clientes LEFT JOIN comunas USING (comcod) WHERE clirut=".($s['clirut']*1) );
		else 
			$cl=false;
			
		if(($b['clirut']*1)!=0)			
			$cl2=cargar_registro("SELECT * FROM clientes LEFT JOIN comunas USING (comcod) WHERE clirut=".($b['clirut']*1) );
		else
			$cl2=false;
					
	} else { $b=false; $cr=false; $cl=false; $cl2=false; }

	if($s['ps_refcliente']!='' AND !$cl) {
	
		$d=explode('|', $s['ps_refcliente']);
		$r=explode('-', $d[0]);
		$n=explode(' ', $d[1]);

		$cl['clirut']=ch($r[0]);
		$cl['clidv']=ch($r[1]);
		$cl['clinom']=ch($n[0]);
		$cl['clipat']=ch($n[1]);
		$cl['climat']=ch($n[2]);
		$cl['clidir']=ch($d[2]);

		$com=cargar_registros("SELECT * FROM comunas WHERE comdes='".pg_escape_string(html_entity_decode($d[3]))."'");
		if($com) {
			$cl['comcod']=$com['comcod'];
			$cl['comdes']=ch($d[3]);
		} else {
			$cl['comcod']='';
			$cl['comdes']='';
		}	

		$cl['clifon']=ch($d[4]);
		
	} 
?>

<html>
<title>Edici&oacute;n de Sepulturas</title>

<?php cabecera_popup('..'); ?>

<script>

var bloquear=0;

cargar_boletin = function() {

	var myAjax=new Ajax.Request(
		'info_boletin.php', {
			method:'get',
			parameters:$('bolnum').serialize(),
			onComplete: function(resp) {
				r=resp.responseText.evalJSON(true);
				
				if(r) {
					
					try {

						$('bolmon').value=r.bolmon;
						$('fecha1').value=r.bolfec;
						$('bolobs').value=r.bolobs;
						$('crecod').value=r.crecod;
						$('cretot').value=(r.cretot*1)+(r.crepie*1);
						
						if(r.clirut != null && r.clidv != null ) {
							$('clirut').value=r.clirut+'-'+r.clidv;
							validar_rut();
						}
					
					} catch(err) {
						alert(err);
					}
					
				}
				
			}
		}	
	);

}

validar_rut = function() {

	if(bloquear) return;

	bloquear=1;

	var myAjax=new Ajax.Request('info_cliente.php',
	{
		method:'get',
		parameters: $('clirut').serialize(),
		onComplete: function(resp) {
			d=resp.responseText.evalJSON(true);
			
			if(d) {

				$('clipat').value=d['clipat'].unescapeHTML();
				$('climat').value=d['climat'].unescapeHTML();
				$('clinom').value=d['clinom'].unescapeHTML();
				$('clidir').value=d['clidir'].unescapeHTML();
				$('comcod').value=d['comcod'].unescapeHTML();
				$('comdes').value=d['comdes'].unescapeHTML();
				$('clifon').value=d['clifon'].unescapeHTML();
				$('clicel').value=d['clicel'].unescapeHTML();
				$('climail').value=d['climail'].unescapeHTML();
				$('cliobs').value=d['cliobs'].unescapeHTML();
				
			} else {

				/*
				$('clipat').value='';
				$('climat').value='';
				$('clinom').value='';
				$('clidir').value='';
				$('comcod').value='';
				$('comdes').value='';
				$('clifon').value='';
				$('clicel').value='';
				$('climail').value='';
				$('cliobs').value='';
				*/
							
			}

			validacion_rut($('clirut'));
			
			$('clipat').focus();

			bloquear=0;

		}	
	});

}

validar_rut2 = function() {

	//if(bloquear) return;

	bloquear=1;

	var myAjax=new Ajax.Request('info_cliente.php',
	{
		method:'get',
		parameters: 'clirut='+encodeURIComponent($('clirut2').value),
		onComplete: function(resp) {
			var d=resp.responseText.evalJSON(true);
			
			if(d) {

				try {

				$('clipat2').value=d['clipat'].unescapeHTML();
				$('climat2').value=d['climat'].unescapeHTML();
				$('clinom2').value=d['clinom'].unescapeHTML();
				$('clidir2').value=d['clidir'].unescapeHTML();
				$('comcod2').value=d['comcod'].unescapeHTML();
				$('comdes2').value=d['comdes'].unescapeHTML();
				$('clifon2').value=d['clifon'].unescapeHTML();
				$('clicel2').value=d['clicel'].unescapeHTML();
				$('climail2').value=d['climail'].unescapeHTML();
				$('cliobs2').value=d['cliobs'].unescapeHTML();
				
				} catch(err) {
					alert(err);	
				}
								
			} else {

				/*
				$('clipat').value='';
				$('climat').value='';
				$('clinom').value='';
				$('clidir').value='';
				$('comcod').value='';
				$('comdes').value='';
				$('clifon').value='';
				$('clicel').value='';
				$('climail').value='';
				$('cliobs').value='';
				*/
							
			}

			validacion_rut($('clirut2'));

			$('clipat2').focus();

			bloquear=0;

		}	
	});

}


function guardar() {

	/*
	for(var i=0;i<uso.length;i++) {
		if(!validacion_fecha($('r_'+i+'_fecha_sep'))) {
			alert('Ha ingresado una fecha de sepultaci&oacute;n inv&aacute;lida.'.unescapeHTML());
			return;
		}
	}*/

	var myAjax=new Ajax.Request('sql_sepulturas.php',{
		method:'post', parameters: $('registro').serialize(),
		onComplete: function(resp) {
		
			if(resp.responseText=='') {
				alert('Registro guardado exitosamente.');
				//volver();
			} else {
				alert(resp.responseText);
			}

		}	
	});

}

var uso=<?php echo json_encode($u); ?>;
var lista_estados='Entero,Reducido,Anfora,Eliminar...';
var lista_sepulturas='<?php echo $t['tsep_estructura']; ?>';

function eselect(v) {

	var html='';
	var estados=lista_estados.split(',');
	
	for(var i=0;i<estados.length;i++)
		html+='<option value="'+i+'" '+(i==v?'SELECTED':'')+'>'+estados[i]+'</option>';

	return html;

}

function tselect(v) {

	var html='';
	var estados=lista_sepulturas.split(',');
	
	for(var i=0;i<estados.length;i++) {
		var sel=trim(estados[i].unescapeHTML())==v.unescapeHTML()?'SELECTED':'';
		html+='<option value="'+trim(estados[i])+'" '+(sel)+'>'+trim(estados[i])+'</option>';
	}

	return html;

}

function guardar_uso() {

	for(var i=0;i<uso.length;i++) {
		uso[i].us_id=$('r_'+i+'_us_id').value;
		uso[i].bolnum=$('r_'+i+'_bolnum').value;
		uso[i].us_fecha_sep=$('r_'+i+'_fecha_sep').value;
		uso[i].us_rut=$('r_'+i+'_rut').value;
		uso[i].us_nombre=$('r_'+i+'_nombre').value;
		uso[i].us_referencias=$('r_'+i+'_referencias').value;
		uso[i].us_vence=$('r_'+i+'_vence').value;
		uso[i].us_ubicacion=$('r_'+i+'_ubicacion').value;
		uso[i].us_estado=$('r_'+i+'_estado').value;
	}

}

function agregar() {

	if(!uso) uso=[];

	var n=uso.length;
	
	guardar_uso();

	uso[n]=new Object();
	uso[n].us_id=0; 
	uso[n].bolnum=''; 
	uso[n].us_fecha_sep=''; 
	uso[n].us_rut=''; 
	uso[n].us_nombre='';
	uso[n].us_referencias='';
	uso[n].us_vence='';
	uso[n].us_ubicacion='';
	uso[n].us_estado=0;

	redibujar();
	
}

function eliminar(v, n) {

	if(v==3) {
		conf=confirm("&iquest;Desea eliminar el registro?".unescapeHTML());
		if(!conf) {
			$('r_'+n+'_estado').value=uso[n].us_estado; 
			return;
		}
		guardar_uso();	
		uso=uso.without(uso[n]);
		redibujar();
	}
	
}	


function redibujar() {

	var html='<table cellpadding=0 cellspacing=0 style="width:100%;font-size:11px;"><tr class="tabla_header">';
	
	html+='<td>Bolet&iacute;n</td>';	
	html+='<td>Fecha Sep.</td>';	
	html+='<td>R.U.T.</td>';	
	html+='<td style="width:25%;">Nombre</td>';	
	html+='<td>Referencias</td></td>';	
	html+='<td>Vencimiento</td></td>';	
	html+='<td>Ubicaci&oacute;n</td>';	
	html+='<td>Estado</td>';	
	
	html+='</tr>';
	
	for(var i=0;i<uso.length;i++) {
	
		var u=uso[i];
		var clase=(i%2==0)?'tabla_fila':'tabla_fila2';
		
		html+='<tr onMouseOver="this.className=\'mouse_over\'" ';
		html+='onMouseOut="this.className=\''+clase+'\'">';
		
		html+='<input type="hidden" id="r_'+i+'_us_id" name="r_'+i+'_us_id" style="text-align:center;" value="'+u.us_id+'" /></td>';				
		html+='<td><input type="text" id="r_'+i+'_bolnum" name="r_'+i+'_bolnum" style="text-align:center;" value="'+u.bolnum+'" /></td>';				
		html+='<td><input type="text" id="r_'+i+'_fecha_sep" name="r_'+i+'_fecha_sep" style="text-align:center;" value="'+u.us_fecha_sep+'" onBlur="validacion_fecha(this);" /></td>';				
		html+='<td><input type="text" id="r_'+i+'_rut" name="r_'+i+'_rut" style="text-align:right;" value="'+u.us_rut+'" onKeyUp="validacion_rut(this);" /></td>';				
		html+='<td><input type="text" id="r_'+i+'_nombre" name="r_'+i+'_nombre" style="text-align:left;" value="'+u.us_nombre+'" /></td>';				
		html+='<td><input type="text" id="r_'+i+'_referencias" name="r_'+i+'_referencias" style="text-align:left;" value="'+u.us_referencias+'" /></td>';				
		html+='<td><input type="text" id="r_'+i+'_vence" name="r_'+i+'_vence" style="text-align:center;" value="'+u.us_vence+'" onBlur="validacion_fecha(this);" /></td>';				
		html+='<td><select id="r_'+i+'_ubicacion" name="r_'+i+'_ubicacion" style="text-align:left;">'+tselect(u.us_ubicacion)+'</select></td>';				
		html+='<td><select id="r_'+i+'_estado" name="r_'+i+'_estado" style="text-align:left;" onChange="eliminar(this.value,'+i+');">'+eselect(u.us_estado)+'</select></td>';				
		
		html+='</tr>';

	}	
	
	html+='</table>';
	
	$('listado').innerHTML=html;

	for(var i=0;i<uso.length;i++) {
		validacion_fecha($('r_'+i+'_fecha_sep'));
		validacion_rut($('r_'+i+'_rut'));
	}

}

function volver() {
	window.open("examinar_sepulturas.php?clase="+encodeURIComponent($('clase').value)+"&codigo="+encodeURIComponent($('codigo').value), '_self');
}

</script>

<style>

	#listado input {width:100%; font-size:11px; }
	#listado select {width:100%; font-size:11px; }

</style>

<body class='popup_background fuente_por_defecto'>

<form id='registro' name='registro' onSubmit='return false;'>
<input type='hidden' id='clase' name='clase' value='<?php echo htmlentities($clase); ?>'>
<input type='hidden' id='codigo' name='codigo' value='<?php echo htmlentities($codigo); ?>'>
<input type='hidden' id='numero' name='numero' value='<?php echo htmlentities($numero); ?>'>

<table style='width:100%;'>

<?php 

	print("<tr><td colspan=2 
			style='font-weight:bold;font-size:20px;text-align:center;'>
			Editando Sepultura: 
			<u>".htmlentities($clase)." ".htmlentities($codigo)." : ".$numero."</u>
			</td></tr>");	

?>

</table>

<div class='sub-content'>

<table style='width:100%;'>

<tr>
<td style='text-align:right;width:20%;'>Bolet&iacute;n:</td>
<td><input type='text' id='bolnum' name='bolnum'
onBlur='cargar_boletin();' onKeyUp='if(event.which==13) cargar_boletin();' 
size=10 <?php if($s) echo "value='".$s['bolnum']."'"; ?> ></td>
</tr>

<tr>
<td style='text-align:right;width:20%;'>Fecha Bolet&iacute;n:</td>
<td>

<input type='text' name='fecha1' id='fecha1' size=10 onBlur='validacion_fecha(this);'
  style='text-align: center;' value='<?php if($b) echo $b['bolfec']; else echo date("d/m/Y"); ?>'>
  <img src='../iconos/date_magnify.png' id='fecha1_boton'>
  
</td>
</tr>


<tr>
<td style='text-align:right;width:20%;'>Monto $:</td>
<td><input type='text' id='bolmon' name='bolmon' 
size=10 <?php if($b) echo "value='".$b['bolmon']."'"; ?> ></td>
</tr>

<tr>
<td style='text-align:right;width:20%;'>Observaciones:</td>
<td><input type='text' id='bolobs' name='bolobs' 
size=10 <?php if($b) echo "value='".$b['bolobs']."'"; ?> ></td>
</tr>

<tr>
<td style='text-align:right;width:20%;'>Cod. Cr&eacute;dito:</td>
<td><input type='text' id='crecod' name='crecod' 
size=10 <?php if($b) echo "value='".$b['crecod']."'"; ?> ></td>
</tr>

<tr>
<td style='text-align:right;width:20%;'>Total Cr&eacute;dito $:</td>
<td><input type='text' id='cretot' name='cretot' 
size=10 <?php if($cr) echo "value='".($cr['crepie']+$cr['cretot'])."'"; ?> ></td>
</tr>

<tr>
<td style='text-align:right;width:20%;'>Fecha Vencimiento:</td>
<td>

<input type='text' name='fecha2' id='fecha2' size=10 onBlur='validacion_fecha(this);'
  style='text-align: center;' value='<?php echo $s['ps_vence']; ?>'>
  <img src='../iconos/date_magnify.png' id='fecha2_boton'>
  
</td>
</tr>


</table>

</div>


<div class='sub-content'>
<img src='../iconos/user_go.png' />
<b>Datos del Cliente</b>
</div>

<div class='sub-content'>

<table style='width:100%;'>

<tr>
<td style='text-align:right;width:20%;'>R.U.T.:</td>
<td style='font-weight:bold;'>
<input type='text' id='clirut2' name='clirut2' 
<?php if($cl2 AND ch($cl2['clirut'])!='' AND ch($cl2['clidv'])!='') echo "value='".$cl2['clirut']."-".$cl2['clidv']."'"; ?>
onKeyUp='validacion_rut(this); if(event.which==13) validar_rut2();' onBlur='validar_rut2();'
size=10>
</td>
</tr>

<tr>
<td style='text-align:right;width:20%;'>Paterno:</td>
<td style='font-weight:bold;'>
<input type='text' id='clipat2' name='clipat2' size=15 <?php echo 'value="'.ch($cl2['clipat']).'"'; ?> >
</td>
</tr>

<tr>
<td style='text-align:right;'>Materno:</td>
<td style='font-weight:bold;'>
<input type='text' id='climat2' name='climat2' size=15 <?php echo 'value="'.ch($cl2['climat']).'"'; ?> >
</td>
</tr>

<tr>
<td style='text-align:right;'>Nombres:</td>
<td style='font-weight:bold;'>
<input type='text' id='clinom2' name='clinom2' size=15 <?php echo 'value="'.ch($cl2['clinom']).'"'; ?> >
</td>
</tr>

<tr>
<td style='text-align:right;'>Direcci&oacute;n:</td>
<td><input type='text' id='clidir2' name='clidir2' size=35  <?php echo 'value="'.ch($cl2['clidir']).'"'; ?> ></td>
</tr>

<tr>
<td style='text-align:right;'>Comuna:</td>
<td>
<input type='hidden' id='comcod2' name='comcod2' <?php echo 'value="'.ch($cl2['comcod']).'"'; ?> >
<input type='text' id='comdes2' name='comdes2' size=25 <?php echo 'value="'.ch($cl2['comdes']).'"' ?> >
</td>
</tr>

<tr>
<td style='text-align:right;'>Tel&eacute;fono Fijo:</td>
<td><input type='text' id='clifon2' name='clifon2' size=15  <?php echo 'value="'.ch($cl2['clifon']).'"'; ?> >
</td>
</tr>

<tr>
<td style='text-align:right;'>Tel&eacute;fono M&oacute;vil:</td>
<td><input type='text' id='clicel2' name='clicel2' size=15   <?php echo 'value="'.ch($cl2['clicel']).'"'; ?>>
</td>
</tr>

<tr>
<td style='text-align:right;'>e-mail:</td>
<td><input type='text' id='climail2' name='climail2' size=15   <?php echo 'value="'.ch($cl2['climail']).'"'; ?>>
</td>
</tr>

<tr>
<td style='text-align:right;'>Observaciones:</td>
<td><input type='text' id='cliobs2' name='cliobs2' size=35   <?php echo 'value="'.ch($cl2['cliobs']).'"'; ?>>
</td>
</tr>

</table>

</div>


<div class='sub-content'>
<img src='../iconos/user.png' />
<b>Datos del Propietario</b>
</div>

<div class='sub-content'>

<table style='width:100%;'>

<tr>
<td style='text-align:right;width:20%;'>R.U.T.:</td>
<td style='font-weight:bold;'>
<input type='text' id='clirut' name='clirut' 
<?php if($cl AND ch($cl['clirut'])!='' AND ch($cl['clidv'])!='') echo "value='".$cl['clirut']."-".$cl['clidv']."'"; ?>
onKeyUp='validacion_rut(this); if(event.which==13) validar_rut();' onBlur='validar_rut();'
size=10>
</td>
</tr>

<tr>
<td style='text-align:right;width:20%;'>Paterno:</td>
<td style='font-weight:bold;'>
<input type='text' id='clipat' name='clipat' size=15 <?php echo 'value="'.ch($cl['clipat']).'"'; ?> >
</td>
</tr>

<tr>
<td style='text-align:right;'>Materno:</td>
<td style='font-weight:bold;'>
<input type='text' id='climat' name='climat' size=15 <?php echo 'value="'.ch($cl['climat']).'"'; ?> >
</td>
</tr>

<tr>
<td style='text-align:right;'>Nombres:</td>
<td style='font-weight:bold;'>
<input type='text' id='clinom' name='clinom' size=15 <?php echo 'value="'.ch($cl['clinom']).'"'; ?> >
</td>
</tr>

<tr>
<td style='text-align:right;'>Direcci&oacute;n:</td>
<td><input type='text' id='clidir' name='clidir' size=35  <?php echo 'value="'.ch($cl['clidir']).'"'; ?> ></td>
</tr>

<tr>
<td style='text-align:right;'>Comuna:</td>
<td>
<input type='hidden' id='comcod' name='comcod' <?php echo 'value="'.ch($cl['comcod']).'"'; ?> >
<input type='text' id='comdes' name='comdes' size=25 <?php echo 'value="'.ch($cl['comdes']).'"' ?> >
</td>
</tr>

<tr>
<td style='text-align:right;'>Tel&eacute;fono Fijo:</td>
<td><input type='text' id='clifon' name='clifon' size=15  <?php echo 'value="'.ch($cl['clifon']).'"'; ?> >
</td>
</tr>

<tr>
<td style='text-align:right;'>Tel&eacute;fono M&oacute;vil:</td>
<td><input type='text' id='clicel' name='clicel' size=15   <?php echo 'value="'.ch($cl['clicel']).'"'; ?>>
</td>
</tr>

<tr>
<td style='text-align:right;'>e-mail:</td>
<td><input type='text' id='climail' name='climail' size=15   <?php echo 'value="'.ch($cl['climail']).'"'; ?>>
</td>
</tr>

<tr>
<td style='text-align:right;'>Observaciones:</td>
<td><input type='text' id='cliobs' name='cliobs' size=35   <?php echo 'value="'.ch($cl['cliobs']).'"'; ?>>
</td>
</tr>

</table>

</div>

<div class='sub-content'>
<table style='width:100%;' cellpadding=0 cellspacing=0><tr><td>
<img src='../iconos/group.png' />
</td><td>
<b>Datos de Uso de Sepultura</b>
</td><td style='width:50%;text-align:right;'>
<input type='button' value='Agregar Registro' onClick='agregar();'>
</td></tr></table>
</div>

<div class='sub-content2' id='listado' 
style='height:150px;overflow:auto;'>



</div>


<?php if($s) { ?>

<div class='sub-content'>
<table style='width:100%;'>
<tr><td style='width:20%;text-align:right;'>
<input type='checkbox' id='anular' name='anular'>
</td><td>
Eliminar informaci&oacute;n de propiedad de sepultura. (Estado &quot;Disponible&quot;).
</td></tr>
</table>
</div>

<?php } ?>

<center>
<br /><br />
<input type='button' value='- Guardar Registro -' onClick='guardar();'>
<input type='button' value='- Volver Atr&aacute;s... -' onClick='volver();'>
<br /><br />
</center>
</form>

</body>
</html>

<script>

    Calendar.setup({
        inputField     :    'fecha1',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha1_boton'
    });

    Calendar.setup({
        inputField     :    'fecha2',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha2_boton'
    });


		seleccionar_comuna=function(d) {

			$('comdes').value=d[1].unescapeHTML();
			$('comcod').value=d[0]*1;				

		}

		autocompletar_comunas = new AutoComplete(
      'comdes', 
      '../autocompletar_sql.php',
      function() {
        if($('comdes').value.length<2) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=comunas&'+$('comdes').serialize()
        }
      }, 'autocomplete', 350, 200, 150, 1, 1, seleccionar_comuna);

		seleccionar_comuna2=function(d) {

			$('comdes2').value=d[1].unescapeHTML();
			$('comcod2').value=d[0]*1;				

		}

		autocompletar_comunas2 = new AutoComplete(
      'comdes2', 
      'autocompletar_sql.php',
      function() {
        if($('comdes2').value.length<2) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=comunas&comdes='+encodeURIComponent($('comdes2').value)
        }
      }, 'autocomplete', 350, 200, 150, 1, 1, seleccionar_comuna2);


		redibujar();
		
		validacion_fecha($('fecha1'));
		validacion_fecha($('fecha2'));

		validacion_rut($('clirut'));
		validacion_rut($('clirut2'));

</script>