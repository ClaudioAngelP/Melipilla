<?php



  require_once('../conectar_db.php');

  

    if($_GET['tipo']=='estado_interconsultas' or $_GET['tipo']=='revisar_interconsultas') {



		if($_GET['tipo']=='estado_interconsultas') {

			$buscar = $_GET['buscar'];

			$orden = $_GET['orden'];

		

			if(isset($_GET['ascendente'])) {

				$ascen = '';

			} else {

				$ascen='DESC';

			}

		

		

			switch ($orden) {

				case 0: $orden='inter_ingreso'; break;

				case 1: $orden='pac_rut'; break;

				case 2: $orden='pac_appat, pac_apmat, pac_nombres'; break;

				case 3: $orden='esp_desc'; break;

				case 4: $orden='inter_folio'; break;

			}

		

			if(trim($buscar)!='') {

			$condicion="

			WHERE (

			pac_rut ILIKE '%$buscar%' OR

			pac_appat || ' ' || pac_apmat || ' ' || pac_nombres ILIKE '%$buscar%' OR

			esp_desc ILIKE '%$buscar%'

			) ";

			} else {

			$condicion=" ";

			}

		

			$resultado = pg_query($conn, "

			SELECT inter_folio, inter_ingreso, pac_rut, pac_appat, pac_apmat, pac_nombres, 

			esp_desc, inter_estado, inter_id

			FROM interconsulta 

			

			LEFT JOIN pacientes ON inter_pac_id=pac_id

			LEFT JOIN especialidades ON inter_especialidad=esp_id

			

			$condicion

			ORDER BY $orden

			$ascen

			");

			

			print("<table width=100%>

			<tr class='tabla_header' style='font-weight: bold;'>

			<td>Fecha Ing.</td>

			<td>Nro. Folio</td>

			<td>R.U.T./ID Paciente</td>

			<td>Nombre Completo</td>

			<td>Especialidad</td>

			<td>Estado</td>

			</tr>

			");

		

		} else {

			

			$especialidad=($_GET['especialidad']*1);

			

			$resultado = pg_query($conn, "

			SELECT 

      

        inter_folio, 

        inter_ingreso, 

        pac_rut, pac_appat, pac_apmat, pac_nombres, 

			  esp_desc, 

        inter_estado,

        inst_nombre,

        inter_inst_id1,

        inter_id

			

      FROM interconsulta 

			

			LEFT JOIN pacientes ON inter_pac_id=pac_id

			LEFT JOIN especialidades ON inter_especialidad=esp_id

			LEFT JOIN instituciones ON inter_inst_id1=inst_id

		

			WHERE 

        inter_especialidad=$especialidad AND 

        inter_inst_id2=".$sgh_institucion['inst_id']." AND

        inter_estado=0

			ORDER BY inter_folio

			");

		

			print("<table width=100%>

			<tr class='tabla_header' style='font-weight: bold;'>

			<td>Fecha Ing.</td>

			<td>Procedencia</td>

			<td>Nro. Folio</td>

			<td>R.U.T./ID Paciente</td>

			<td>Nombre Completo</td>

			<td>Estado</td>

			</tr>

			");

		}

		

		

		

		for($i=0;$i<pg_num_rows($resultado);$i++) {

			

			$fila = pg_fetch_row($resultado);

			

			for($a=0;$a<count($fila);$a++) $fila[$a] = htmlentities($fila[$a]);

			

			($i%2)==1	?	$clase='tabla_fila'	: $clase='tabla_fila2';

			

			switch($fila[7]) {

    				case 0: $imagen = 'iconos/time.png'; 

    						$texto='En espera...'; break;

    				case 1: $imagen = 'iconos/tick.png'; 

    						$texto='Aceptado'; break;

            case 2: $imagen = 'iconos/cross.png'; 

    						$texto='Rechazado'; break;

    				case 3: $imagen = 'iconos/arrow_refresh.png';

    						$texto='Atendido y en Control'; break;

    				case 4: $imagen = 'iconos/user_go.png';

    						$texto='Derivado...'; break;

    				case 5: $imagen = 'iconos/user_go.png'; 

    						$texto='Atendido y Retornado'; break;

    				case 6: $imagen = 'iconos/user_add.png'; 

    						$texto='Atendido y Permanente'; break;

            case 10: $imagen = 'iconos/user_delete.png'; 

    						$texto='Paciente Rechaza Tratamiento'; break;

			}

			

			if($fila[0]=='-1' or $fila[0]=='-2') $fila[0]='(s/n)';

		  

			if($_GET['tipo']=='estado_interconsultas') {

		  

			print("

			<tr class='".$clase."' style='height:30px;'

			onMouseOver='this.className=\"mouse_over\";'

			onMouseOut='this.className=\"".$clase."\";'

			onClick='abrir_ficha(".$fila[8].");'

			>

			<td style='text-align: right;'><i>".$fila[1]."</i></td>

			<td style='text-align: center;'><i>".$fila[0]."</i></td>

			<td style='text-align: right;'><b>".$fila[2]."</b></td>

			<td><b>".$fila[3]." ".$fila[4]." ".$fila[5]. "</b></td>

			<td style='text-align: center;'>".$fila[6]."</td>

			<td><center>

			<img src='".$imagen."' alt='".$texto."' title='".$texto."'>

			</center></td>

			</tr>

			");

		

			} else {

		

			print("

			<tr class='".$clase."' style='height:30px;'

			onMouseOver='this.className=\"mouse_over\";'

			onMouseOut='this.className=\"".$clase."\";'

			onClick='abrir_ficha(".$fila[10].",".$fila[9].");'>

			<td style='text-align: right;'><i>".$fila[1]."</i></td>

			<td style='text-align: center;'><i>".$fila[8]."</i></td>

			<td style='text-align: center;'><b>".$fila[0]."</b></td>

			<td style='text-align: right;'><b>".$fila[2]."</b></td>

			<td><b>".$fila[3]." ".$fila[4]." ".$fila[5]."</b></td>

			<td><center>

			<img src='".$imagen."' alt='".$texto."' title='".$texto."'>

			</center></td>

			</tr>

			");

			

			}	

		}

		

		print("</table>");

		

  }





?>

