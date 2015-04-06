<?php 

	require_once("../../conectar_db.php");

	function ch($v) {
		if(isset($v)) return $v; else return '';
	}
	
	$sep=explode('|',utf8_decode($_POST['sepultura']));

	$clase=pg_escape_string($sep[0]);
	$codigo=pg_escape_string($sep[1]);
	$numero=$sep[2]*1;

	$t=cargar_registro("SELECT * FROM tipo_sepultura WHERE tsep_clase='$clase'", true);	
	$s=cargar_registro("SELECT * FROM propiedad_sepultura
									LEFT JOIN clientes USING (clirut) 
									WHERE ps_clase='$clase' AND ps_codigo='$codigo'
									AND ps_numero=$numero AND ps_vigente
									", true);

	$u=cargar_registros_obj("SELECT * FROM uso_sepultura
									WHERE sep_clase='$clase' AND sep_codigo='$codigo'
									AND sep_numero=$numero AND us_estado<=2", true);

	if($s) {
		
		$b=cargar_registro("SELECT *, bolfec::date AS bolfec FROM boletines WHERE bolnum=".$s['bolnum']);
		
		if($b) 
			$cr=cargar_registro("SELECT * FROM creditos WHERE crecod=".$b['crecod']);
		else $cr=false;
		
		if($cr) 
			$cl=cargar_registro("SELECT * FROM clientes WHERE clirut=".($cr['clirut']*1) );
		else $cl=false;
		
		if(!$cl)
			$cl=cargar_registro("SELECT * FROM clientes WHERE clirut=".($b['clirut']*1) );
		
	} else { $b=false; $cr=false; $cl=false; }

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

<input type='hidden' id='clase' name='clase' value='<?php echo htmlentities($clase); ?>'>
<input type='hidden' id='codigo' name='codigo' value='<?php echo htmlentities($codigo); ?>'>
<input type='hidden' id='numero' name='numero' value='<?php echo htmlentities($numero); ?>'>

<div class='sub-content'>
<table style='width:100%;'>

<?php 

	print("<tr><td colspan=2 
			style='font-weight:bold;font-size:20px;text-align:center;'>
			Sepultura: 
			<u>".htmlentities($clase)." ".htmlentities($codigo)." : ".$numero."</u>
			");
	
	if(!$s) print("[Registro Nuevo]");	
						
	print("</td></tr>");	

?>

</table>
</div>

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

<input type='text' name='fecha1' id='fecha1' size=10
  style='text-align: center;' value='<?php if($b) echo $b['bolfec']; else echo date("d/m/Y"); ?>'>
  <img src='iconos/date_magnify.png' id='fecha1_boton'>
  
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

</table>

</div>


<div class='sub-content'>
<img src='iconos/user.png' />
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
<input type='hidden' id='comcod' name='comcod' <?php echo ch($cl['comcod']); ?> >
<input type='text' id='comdes' name='comdes' size=25 <?php echo ch($cl['comdes']) ?> >
</td>
</tr>

<tr>
<td style='text-align:right;'>Tel&eacute;fono Fijo:</td>
<td><input type='text' id='clifon' name='clifon' size=15  <?php echo 'value="'.ch($cl['clifon']).'"'; ?> >
</td>
</tr>

<tr>
<td style='text-align:right;'>Tel&eacute;fono M&oacute;vil:</td>
<td><input type='text' id='clicel' name='clicel' size=15>
</td>
</tr>

<tr>
<td style='text-align:right;'>e-mail:</td>
<td><input type='text' id='climail' name='climail' size=15>
</td>
</tr>

<tr>
<td style='text-align:right;'>Observaciones:</td>
<td><input type='text' id='cliobs' name='cliobs' size=35>
</td>
</tr>

</table>

</div>

<div class='sub-content'>
<table style='width:100%;' cellpadding=0 cellspacing=0><tr><td>
<img src='iconos/group.png' />
</td><td>
<b>Datos de Uso de Sepultura</b>
</td><td style='width:50%;text-align:right;'>
<input type='button' value='Agregar Registro' onClick='agregar();'>
</td></tr></table>
</div>

<div class='sub-content2 grilla' id='listado_uso' 
style='height:200px;overflow:auto;'>



</div>


<center>
<br /><br />
<input type='button' value='- Guardar Registro -' onClick='guardar();'>
<br /><br />
</center>

<script>

	uso=<?php echo json_encode($u); ?>;
	lista_sepulturas='<?php echo $t['tsep_estructura']; ?>';

    Calendar.setup({
        inputField     :    'fecha1',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha1_boton'
    });


		seleccionar_comuna=function(d) {

			$('comdes').value=d[1].unescapeHTML();
			$('comcod').value=d[0]*1;				

		}

		autocompletar_comunas = new AutoComplete(
      'comdes', 
      'autocompletar_sql.php',
      function() {
        if($('comdes').value.length<2) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=comunas&'+$('comdes').serialize()
        }
      }, 'autocomplete', 350, 200, 150, 1, 1, seleccionar_comuna);


<?php if(ch($cl['clirut'])!='') echo "validar_rut();"; ?>

		redibujar();

		validacion_rut($('clirut'));

</script>
