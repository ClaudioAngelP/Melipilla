<?php

	// Script de Generación de Informes Dinámico
	// Sistema de Gestión
	// Hospital San Martín de Quillota
	// =======================================================================
	// Rodrigo Carvajal J. (rcarvajal@scv.cl)
	// Soluciones Computacionales Viña del Mar LTDA.
	// =======================================================================


  $vals=Array();
  $flds=Array();
  $tabla="";
  
  $query="";
  $nquery="";
  $campos=Array();
  $formato=Array();
  $pie="";
  
  $condiciones = Array(
    "/\[if\s+([^\]]*?)\]([^[\]]*)\[else\]([^[\]]*)\[\/if\]/sieU",
    "/\[if\s+([^\]]*?)\]([^[\]]*)\[\/if\]/sieU"
  );
        
  $condiciones_eval = Array(
    "evaluar_cond(\"\\1\", \"\\2\", \"\\3\")",
    "evaluar_cond(\"\\1\", \"\\2\")"
  );
      

  // $campos
  // =======
  // Arreglo de campos a mostrar/capturar.
  // $campos[0] = Nombre de Variable (*)
  // $campos[1] = Nombre a Mostrar (*)
  // $campos[2] = Tipo de Campo (*)
  // $campos[3] = Valor por Defecto
  // $campos[4] = Opciones
  // (*) Obligatorios


  // $formato
  // =======
  // Arreglo de campos a mostrar/capturar.
  // $formato[0] = Nombre de Campo en Base de Datos (*)
  // $formato[1] = Nombre de Columna (*)
  // $formato[2] = Tipo de Columna (*)
  // $formato[3] = Alineación de Columna (*)
  // (*) Obligatorios


  function ejecutar_consulta() {

    GLOBAL  $tabla, $campos, $query, $nquery, $flds,
            $condiciones, $condiciones_eval, $vals;

    if(!isset($_POST['form_submit'])) return;

      // Si el formulario fue contestado, cargar valores devueltos.

      // Captura todos los valores devueltos por el formulario

      for($i=0;$i<count($campos);$i++) {
        $vals[$campos[$i][0]]=$_POST['form_'.$campos[$i][0]];
        $flds[$i][0]=$campos[$i][0];
      }

      // Reemplaza en la cadena de consulta los lugares donde se
      // pone un [%variable] por el valor correspondiente devuelto
      // desde el formulario.

      for($i=0;$i<count($campos);$i++)
        $query = str_replace(   '[%'.$campos[$i][0].']',
                                $vals[$campos[$i][0]],
                                $query);

      // Reemplaza los bloques condicionales dentro de la cadena
      // tomando la parte condicional, evaluandola, y poniendo el texto
      // correspondiente con la evaluación.

      $nquery = preg_replace($condiciones,
                             $condiciones_eval,
                             $query);

      // Se carga consulta para generar la tabla correspondiente.

      $tabla = cargar_sql($nquery);

  }

  function procesar_formulario($nombre, $callback='') {

    GLOBAL $_POST, $vals, $flds, $tabla, $campos, $formato, $pie, $nquery, $sghservicio, $sghinstitucion;
 
    $flds=$campos;

    // Definición de campos por defecto en todos los informe...

    $visualizaciones= Array(
                        Array(0, 'En Pantalla'),
                        Array(1, 'En Impresora'),
                        Array(2, 'Archivo XLS')
                      );

    $submit     =Array('submit',  '',             -1,   0);
    $visualizar =Array('ver',     'Visualizar',   10,   0,    $visualizaciones);

    if(isset($_POST['form_submit'])) {

      // Crea la Tabla dependiendo del Formato

      $forma=$_POST['form_ver'];

      if($forma==0 OR $forma==1 OR $forma==2) {

        // En HTML para Pantalla o Impresión

        $print_script='';

        $colnum=count($formato);

        switch($forma) {
          case 0:
                  $background='popup_background';
          break;
          case 1:
                  $background='';
                  $print_script='window.print(); window.close();';
          break;
        }

        $html='<html><title>'.$nombre.'</title>';

        if($forma==0 OR $forma==1)
        $html.='<head>
                '.cabecera_popup_head('../../..').'
                </head>
                <body class="fuente_por_defecto '.$background.'"
                onLoad="'.$print_script.'">
                <table style="width:100%;">
                <tr class="tabla_header">';

        if($forma==2)
        $html.='<body>
                <table style="width:100%;" border=1>
                <tr class="tabla_header">';

        $html.='<tr><td colspan='.$colnum.' style="text-align:center;font-weight:bold;">
                <h3>
                '.htmlentities($sghservicio).'<br>
                '.htmlentities($sghinstitucion).' 
                </h3></td></tr>
                <tr><td colspan='.$colnum.' style="text-align:center;font-weight:bold;">
                <h2><u>'.$nombre.'</u></h2>
                </td></tr>
                <tr><td colspan='.$colnum.'>&nbsp;</td></tr>';

        for($i=0;$i<count($campos);$i++)
            $html.='<tr>'.mostrar_campo($campos[$i], $colnum).'</tr>';

        $campofecha=Array('','Fecha Emisi&oacute;n',100);

        $html.='<tr>'.mostrar_campo($campofecha, $colnum).'</tr>';


        if($forma==2)
        $html.='<tr><td colspan='.$colnum.'>&nbsp;</td></tr>
                </table>
                <table style="width:100%;" border=1>
                <tr class="tabla_header" style="font-weight:bold;">';
        else
        $html.='</table><table style="width:100%;">
                <tr class="tabla_header" style="font-weight:bold;">';

        for($i=0;$i<count($formato);$i++)
          $html.='<td>'.$formato[$i][1].'</td>';

        $html.='</tr>';

        for($k=0;$k<count($tabla);$k++) {
          ($k%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
          $html.='<tr class="'.$clase.'">';
          for($i=0;$i<count($formato);$i++) {
            $align=$formato[$i][3];
            
            switch($formato[$i][2]) {
              case 0: 
                                    $celda=$tabla[$k][$formato[$i][0]];
                                    if($formato[$i][0]=='log_fecha'){
                                      $celda=substr($celda,0,10);
                                    }
                           break;
              case 1:
				if($forma!=2) {
					$celda=number_format($tabla[$k][$formato[$i][0]],0,',','.');
				} else {
					$celda=number_format($tabla[$k][$formato[$i][0]],0,'.','');
				}
				
              break;
              case 2: 
				if($forma!=2) {
					$celda=number_format($tabla[$k][$formato[$i][0]],1,',','.');
				} else {
					$celda=number_format($tabla[$k][$formato[$i][0]],1,'.','');
				}
					
              break;
              case 3:
				if($forma!=2) {
					if($tabla[$k][$formato[$i][0]]>=0) $signo=''; else $signo='-';
					$celda=$signo.'$'.number_format(abs($tabla[$k][$formato[$i][0]]),0,',','.').'.-';
				} else {
					if($tabla[$k][$formato[$i][0]]>=0) $signo=''; else $signo='-';
					$celda=$signo.''.number_format(abs($tabla[$k][$formato[$i][0]]),0,'.','');
				}
				
              break;
              case 4: $celda=$tabla[$k][$formato[$i][0]];
              break;
            }
            $html.='<td style="text-align:'.$align.';">'.$celda.'</td>';
          }
          $html.='</tr>';
          
          if($callback!='') eval($callback.'($tabla[$k]);');
          
        }

        $html.=$pie.'</table>
                <!----
                '.$nquery.'
                ---->
                </body></html>';

        if($forma==2) {
          header("Content-type: application/vnd.ms-excel");
          header("Content-Disposition: attachment; filename=\"$nombre ".date('d-m-Y his')."\".xls\";");

        }

        print($html);

      }


    } else {

      // Si el formulario no ha sido contestado, desplegarlo
  
      $html='<form id="genform" name="genform" onSubmit="return false;">
              <table style="width:100%;">
              <tr class="tabla_header" style="font-weight:bold;">
              <td colspan=2 style="font-size:14px;">'.$nombre.'</td></tr>
              <tr><td colspan=2>&nbsp;</td></tr>';
      
      for($i=0;$i<count($campos);$i++)
        generar_campo($campos[$i], $html, $script, $val_script);

      generar_campo($submit,      $html, $script, $val_script);
      generar_campo($visualizar,  $html, $script, $val_script);


      $html.="<tr><td colspan=2>&nbsp;</td></tr>
              <tr><td colspan=2>
              <center>
                <div class='boton'>
    		          <table><tr><td>
    		          <img src='iconos/script.png'>
              		</td><td>
              		<a href='#' onClick='procesar_formulario();'>
              		Crear Informe...</a>
              		</td></tr></table>
            		</div>
                </center>
              </td></tr>
              </table></form>";

      print($html.'<br><br>
		<script>
		
		'.$script.'
		
		validaciones=function() {
			
			'.$val_script.'
			
			return true;
			
		}
		
		</script>
		
		');

    }

  }

  function generar_campo($campo, &$html, &$script, &$val_script) {

    // Muestra un Campo dependiendo del tipo ($campo[2])
    // $html    alberga la parte del formulario que se expresa en HTML
    // $script  incorpora las funciones javascript necesarias para
    //          que el campo funcione como es debido.

    $tag_ids='id="form_'.$campo[0].'" name="form_'.$campo[0].'"';

    if($campo[2]>=0)
      $html.='<tr><td style="text-align:right;">'.$campo[1].':</td><td>';

    switch($campo[2]) {

      case -1:    // Campo escondido

        $html.='<input type="hidden" '.$tag_ids.' value="'.$campo[3].'"/>';

        break;

      case 0:     // Seleccionar Bodega

        if(!isset($campo[3])) $campo[3]=0;

        $opts=desplegar_opciones_sql(
                "SELECT bod_id, bod_glosa FROM bodega ORDER BY bod_id",
                $campo[3]);

        $html.='<select '.$tag_ids.'>'.$opts.'</select>';

        break;

      case 1:     // Seleccionar Fechas

        if(!isset($campo[3])) $campo[3]=date('d/m/Y');

        $html.='<input type="text" '.$tag_ids.' size=15
                style="text-align:center;" value="'.$campo[3].'">
                <img src="iconos/date_magnify.png"
                id="img_'.$campo[0].'">';

        $script.="
            Calendar.setup({
              inputField     :    'form_".$campo[0]."',
              ifFormat       :    '%d/%m/%Y',
              showsTime      :    false,
              button         :   'img_".$campo[0]."'
            });";

        break;




     case 2:     // Seleccionar Artículos

        $html.='<input type="hidden" '.$tag_ids.' value="">
                <input type="text"
                id="art_codigo_'.$campo[0].'"
                name="art_codigo_'.$campo[0].'"
                size=10
                style="text-align:left; font-size:11px;">
                <input type="text"
                id="art_glosa_'.$campo[0].'"
                name="art_glosa_'.$campo[0].'"
                size=35 DISABLED
                style="text-align:left; font-size:11px;">
                ';

        $script.="
                autocompletar_medicamentos_".$campo[0]." = new AutoComplete(
                'art_codigo_".$campo[0]."',
                'autocompletar_sql.php',
                function() {
                  if($('art_codigo_".$campo[0]."').value.length<3) return false;

                  return {
                    method: 'get',
                    parameters: 'tipo=buscar_arts&codigo='+
                            encodeURIComponent($('art_codigo_".$campo[0]."').value)
                  }
                }, 'autocomplete', 350, 200, 250, 1, 3,
                function(art) {

                  
                  $('form_".$campo[0]."').value=art[5];
                  $('art_codigo_".$campo[0]."').value=art[1];
                  $('art_glosa_".$campo[0]."').value=art[2];

                }
                );";

        $val_script.="
				if($('form_".$campo[0]."').value*1==0) {
					alert('Debe seleccionar un art&iacute;culo.'.unescapeHTML());
					return false;
				}
        ";


        break;

    case 3:     // Seleccionar Bodega con Despacho de  Recetas

        if(!isset($campo[3])) $campo[3]=0;

        $opts=desplegar_opciones_sql(
                "SELECT bod_id, bod_glosa FROM bodega
                WHERE bod_despacho ORDER BY bod_id",
                $campo[3]);

        $html.='<select '.$tag_ids.'>'.$opts.'</select>';

        break;


    case 4:     // Seleccionar Pacientes

        $html.='<input type="hidden" '.$tag_ids.' value="">
                <input type="text"
                id="pac_rut_'.$campo[0].'"
                name="pac_rut_'.$campo[0].'"
                size=12
                style="text-align:left; font-size:11px;">
                <input type="text"
                id="pac_nombre_'.$campo[0].'"
                name="pac_nombre_'.$campo[0].'"
                size=35 DISABLED
                style="text-align:left; font-size:11px;">
                ';

        $script.="
                autocompletar_paciente_".$campo[0]." = new AutoComplete(
                'pac_rut_".$campo[0]."',
                'autocompletar_sql.php',
                function() {
                if($('pac_rut_".$campo[0]."').value.length<3) return false;

                  return {
                    method: 'get',
                    parameters: 'tipo=pacientes&busca_paciente='+
                            encodeURIComponent($('pac_rut_".$campo[0]."').value)
                  }
                }, 'autocomplete', 350, 200, 250, 1, 3,
                function(pac) {

                  $('form_".$campo[0]."').value=pac[4];
                  $('pac_rut_".$campo[0]."').value=pac[1];
                  $('pac_nombre_".$campo[0]."').value=pac[2].unescapeHTML();

                }
                );";
                
        $val_script.="
				if($('form_".$campo[0]."').value*1==0) {
					alert('Debe seleccionar un paciente.');
					return false;
				}
        ";

        break;

    case 5:     // Seleccionar Convenio

        if(!isset($campo[3])) $campo[3]=0;

        $opts=desplegar_opciones_sql(
                "SELECT convenio_id, convenio_nombre FROM convenio ORDER BY convenio_id",
                $campo[3]);

        $html.='<select '.$tag_ids.'>'.$opts.'</select>';

        break;

    case 6:     // Seleccionar Medicos

        $html.='<input type="hidden" '.$tag_ids.' value="">
                <input type="text"
                id="doc_rut_'.$campo[0].'"
                name="doc_rut_'.$campo[0].'"
                size=12
                style="text-align:left; font-size:11px;">
                <input type="text"
                id="doc_nombre_'.$campo[0].'"
                name="doc_nombre_'.$campo[0].'"
                size=43 DISABLED
                style="text-align:left; font-size:11px;">
                ';

        $script.="
                autocompletar_docto_".$campo[0]." = new AutoComplete(
                'doc_rut_".$campo[0]."',
                'autocompletar_sql.php',
                function() {
                if($('doc_rut_".$campo[0]."').value.length<3) return false;

                  return {
                    method: 'get',
                    parameters: 'tipo=doctor&nombre_doctor='+
                            encodeURIComponent($('doc_rut_".$campo[0]."').value)
                  }
                }, 'autocomplete', 350, 200, 250, 1, 3,
                function(doc) {

                  $('form_".$campo[0]."').value=doc[0];
                  $('doc_rut_".$campo[0]."').value=doc[1];
                  $('doc_nombre_".$campo[0]."').value=doc[2].unescapeHTML();

                }
                );";

        $val_script.="
				if($('form_".$campo[0]."').value*1==0) {
					alert('Debe seleccionar un m&acute;dico.'.unescapeHTML());
					return false;
				}
        ";


        break;

    case 7:     // Seleccionar Item Presupuestario

        if(!isset($campo[3])) $campo[3]=0;

        $opts=desplegar_opciones_sql(
                "SELECT item_codigo, item_glosa FROM item_presupuestario  ORDER BY item_glosa",
                $campo[3]);

        $html.='<select '.$tag_ids.'>'.$opts.'</select>';

        break;

      case 8:     // Seleccionar Proveedor

         $html.='<input type="hidden" '.$tag_ids.' value="">
                <input type="text"
                id="prov_rut_'.$campo[0].'"
                name="prov_rut_'.$campo[0].'"
                size=12
                style="text-align:left; font-size:11px;">
                <input type="text"
                id="prov_glosa_'.$campo[0].'"
                name="prov_glosa_'.$campo[0].'"
                size=44 DISABLED
                style="text-align:left; font-size:11px;">
                ';

        $script.="
                autocompletar_proveedor_".$campo[0]." = new AutoComplete(
                'prov_rut_".$campo[0]."',
                'autocompletar_sql.php',
                function() {
                if($('prov_rut_".$campo[0]."').value.length<3) return false;

                  return {
                    method: 'get',
                    parameters: 'tipo=proveedores&busca_proveedor='+
                            encodeURIComponent($('prov_rut_".$campo[0]."').value)
                  }
                }, 'autocomplete', 350, 200, 250, 1, 3,
                function(prov) {

                  $('form_".$campo[0]."').value=prov[0];
                  $('prov_rut_".$campo[0]."').value=prov[1];
                  $('prov_glosa_".$campo[0]."').value=prov[2].unescapeHTML();

                }
                );";

        $val_script.="
				if($('form_".$campo[0]."').value*1==0) {
					alert('Debe seleccionar un proveedor.'.unescapeHTML());
					return false;
				}
        ";

        break;




   case 10:    // Seleccion Opciones Fijas

        $opts=desplegar_opciones_array($campo[4], $campo[3]);

        $html.='<select '.$tag_ids.'>'.$opts.'</select>';

        break;

   case 11:     //  Seleccionar Bodega recepcion proveedor

        if(!isset($campo[3])) $campo[3]=0;

        $opts=desplegar_opciones_sql(
                "SELECT bod_id, bod_glosa FROM bodega
                WHERE bod_proveedores ORDER BY bod_id",
                $campo[3]);

        $html.='<select '.$tag_ids.'>'.$opts.'</select>';

      break;

   case 20:     // Seleccionar Centro de Costo

       $html.=' <input type="hidden" '.$tag_ids.'>
                <input type="text" id="centro_nombre_'.$campo[0].'"
                name="centro_nombre_'.$campo[0].'" size=40 disabled
                style="text-align:left;">
                <img src="iconos/zoom_in.png"
                onClick="seleccionar_centro(\''.$campo[0].'\');"
                id="img_'.$campo[0].'">';



        $script.="

        	seleccionar_centro = function(nombre_campo) {

           params=  'centro='+encodeURIComponent($('form_'+nombre_campo).value)+
                    '&campo='+encodeURIComponent(nombre_campo);

             top=Math.round(screen.height/2)-150;
             left=Math.round(screen.width/2)-200;

             new_win =
             window.open('estadisticas/dialogos/seleccionar_centro.php?'+
             params,
             'win_items', 'toolbar=no, location=no, directories=no, status=no, '+
             'menubar=no, scrollbars=yes, resizable=no, width=400, height=300, '+
             'top='+top+', left='+left);

             new_win.focus();

          }";

        break;

      case 22:    //Selecciona dato unico

        if (!isset($campo[3])) $campo[3]='';

         $html.='<input type="text" '.$tag_ids.' size=25
                 style="text-align:left;" value="'.$campo[3].'">';
        break;   



    }



    if($campo[2]>=0)
      $html.='</td></tr>';


  }

  function desplegar_opciones_array($ops, $sel) {

    $html_opts='';

    for($i=0;$i<count($ops);$i++)
      $html_opts.='<OPTION VALUE="'.$ops[$i][0].'" '.selif($ops[$i][0], $sel).'>'.$ops[$i][1].'</OPTION>';

    return $html_opts;

  }

  function evaluar_cond($eval, $str1, $str2="") {

    GLOBAL $flds, $vals;

    // Reemplaza en la cadena de consulta los lugares donde se
    // pone un [%variable] por el valor correspondiente devuelto
    // desde el formulario.

    for($i=0;$i<count($flds);$i++)
      $eval = str_replace(   '%'.$flds[$i][0],
                              $vals[$flds[$i][0]],
                              $eval);

    eval('$ret=('.$eval.');');

    if($ret) {
      return stripslashes($str1);
    } else {
      return stripslashes($str2);
    }

  }

  function cargar_sql($sql) {

	  GLOBAL $conn;

    $registro = array();
    $filas = pg_query($conn, $sql);

    if(pg_num_rows($filas)==0) return false;

    for($r=0;$r<pg_num_rows($filas);$r++) {
      $registro[$r]=Array();

      for($i=0;$i<pg_num_fields($filas);$i++)
        $registro[$r][pg_field_name($filas,$i)]=pg_fetch_result($filas, $r, $i);
    }

    return $registro;

  }

  function mostrar_campo($campo, $colnum=1) {

    GLOBAL $_POST, $vals;

    switch($campo[2]) {
      case  3:  case   0:
        $bodega=cargar_sql('SELECT bod_glosa FROM bodega
                            WHERE bod_id='.$vals[$campo[0]]*1);
        $nom=$campo[1];
        $val=$bodega[0]['bod_glosa'];
      break;
      case  2:
        $art=cargar_sql("SELECT art_codigo, art_glosa FROM articulo
                          WHERE art_id=".$vals[$campo[0]]*1);
        $nom=$campo[1];
        $val=$art[0]['art_codigo'].' '.$art[0]['art_glosa'];
      break;
      case  4:
        $art=cargar_sql("SELECT pac_rut, pac_appat || ' ' || pac_apmat || ' ' || pac_nombres AS pac_nombre
                          FROM pacientes
                          WHERE pac_id=".$vals[$campo[0]]*1);
        $nom=$campo[1];
        $val=$art[0]['pac_rut'].' '.$art[0]['pac_nombre'];
      break;

      case  5:
        $convenio=cargar_sql('SELECT convenio_nombre FROM convenio
                            WHERE convenio_id='.$vals[$campo[0]]*1);
        $nom=$campo[1];
        $val=$convenio[0]['convenio_nombre'];
      break;
      case  6:
        $med=cargar_sql("SELECT doc_rut, doc_paterno || ' ' || doc_materno || ' ' || doc_nombres AS doc_nombre
                          FROM doctores
                          WHERE doc_id=".$vals[$campo[0]]*1);
        $nom=$campo[1];
        $val=$med[0]['doc_rut'].' '.$med[0]['doc_nombre'];
      break;
       case  7:
        $convenio=cargar_sql("SELECT item_glosa FROM item_presupuestario
                            WHERE item_codigo='".($vals[$campo[0]]*1)."'");
        $nom=$campo[1];
        $val=$convenio[0]['item_glosa'];
      break;

       case  8:
        $prov=cargar_sql('SELECT prov_rut,prov_glosa FROM proveedor
                            WHERE prov_id='.$vals[$campo[0]]*1);
        $nom=$campo[1];
        $val=$prov[0]['prov_rut'].' '.$prov[0]['prov_glosa'];

     break;

      case  10:
        $valores=$campo[4];
        for($i=0;$i<count($valores);$i++)
          if($valores[$i][0]==$vals[$campo[0]]) $valor=$valores[$i][1];
        $nom=$campo[1];
        $val=$valor;
      break;
      case  11:
        $bodega=cargar_sql('SELECT bod_glosa FROM bodega
                            WHERE bod_id='.$vals[$campo[0]]*1);
        $nom=$campo[1];
        $val=$bodega[0]['bod_glosa'];
      break;
      case  20:
        $centro=cargar_sql('SELECT centro_nombre FROM centro_costo
                            WHERE centro_ruta=\''.$vals[$campo[0]].'\'');
        $nom=$campo[1];
        $val=$centro[0]['centro_nombre'];
      break;
      case  100:
        $nom=$campo[1];
        $val=date('d/m/Y h:i:s');
      break;
      default:
        $nom=$campo[1];
        $val=$vals[$campo[0]];
      break;

    }

    return '<td style="text-align:right; width:200px;">'.$nom.':</td>
            <td style="text-align:left;font-weight:bold;"
            colspan='.($colnum-1).'>'.$val.'</td>';

  }

  function formulario_ingresado() {
    if(!isset($_POST['form_submit']))
      return false;
    else
      return true;
  }

  // Funciones Agregadas

  function infoSUM($cnom) {

    GLOBAL $vals, $tabla;

    if(!formulario_ingresado()) return;

    $sum=0;

    for($i=0;$i<count($tabla);$i++)
      $sum+=$tabla[$i][$cnom];

    return $sum;

  }

  function infoCOUNT($cnom) {

    GLOBAL $vals, $tabla;
    
    if(!formulario_ingresado()) return;

    $unicos=array();
    
    for($i=0;$i<count($tabla);$i++)
		$unicos[$tabla[$i][$cnom]]='1';

    return sizeof($unicos);

  }



   function infoPROM($cnom) {

    GLOBAL $vals, $tabla;

    if(!formulario_ingresado()) return;

    $sum=0;

    for($i=0;$i<count($tabla);$i++)
      $sum+=$tabla[$i][$cnom];

    return ($sum/count($tabla));

  }

  function infoMONEY($str) {

	GLOBAL $vals, $tabla;
	
	$forma=$_POST['form_ver']*1;

    if(!formulario_ingresado()) return;

    if($forma!=2)	return '$'.number_format($str,0,',','.').'.-';
    else return number_format($str,0,'.','');

  }


    function agrega_iva($cnom) {

    GLOBAL $vals, $tabla;

    if(!formulario_ingresado()) return;

    $sum=0;

    for($i=0;$i<count($tabla);$i++)
      $sum+=$tabla[$i][$cnom];

    return ($sum*(1.19));

  }

   function contador($cnom) {

    GLOBAL $vals, $tabla;

    if(!formulario_ingresado()) return;

     $cont=0;

    for($i=0;$i<count($tabla);$i++)
      $cont+=$tabla[$i][$cnom];
    return $cont;
  }

?>
