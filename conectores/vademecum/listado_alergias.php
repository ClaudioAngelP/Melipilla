<?php 

require_once('../../conectar_db.php');

$pac_id=($_POST['pac_id']*1);

$als = cargar_registros_obj("SELECT * FROM paciente_alergias JOIN funcionario USING (func_id) WHERE pac_id=".$pac_id." ORDER BY fecha_ingreso DESC;", true);


if(!$als)
	exit("<center><h1><i>(Sin alergias registradas...)</i></h1></center>");

?>
<table style='width:100%;font-size:11px;'>
<tr class='tabla_header'><td>ID VADEMECUM&copy;</td><td style='width:50%;'>Tipo Alergia</td><td>Fecha Registro</td><td>Usuario Registro</td></tr>
<?php 
if($als)
for($i=0;$i<sizeof($als);$i++) {

	$al=$als[$i];

	$clase=$i%2==1?'tabla_fila':'tabla_fila2';

	$color=$al['al_tipo']=='1'?'red':'orange';

   echo '<tr class="'.$clase.'" style="cursor:pointer;" onMouseOver="this.className=\'mouse_over\'" onMouseOut="this.className=\''.$clase.'\'" onDblClick="eliminar_alergia('.$al['al_id'].');"><td style="text-align:center;font-size:16px;font-weight:bold;color:'.$color.';">'.$al['id_alergia'].'</td><td style="font-size:16px;">'.$al['alergia'].'</td><td style="text-align:center;">'.substr($al['fecha_ingreso'],0,16).'</td><td>'.$al['func_nombre'].'</td></tr>';
}


?>	
