<?php

	// Script de Conexión, Login y Sesiones de Usuarios
	// Sistema de Gestión
	// Hospital San Martín de Quillota
	// =======================================================================
	// Rodrigo Carvajal J. (rcarvajal@scv.cl)
	// Soluciones Computacionales Viña del Mar LTDA.
	// =======================================================================
	
	
	require_once("config.php");
	
	GLOBAL $conn;
	GLOBAL $_global_iva;
	GLOBAL $acceso_funcionario;
	GLOBAL $acceso_func_array;
		
	function cabecera_popup($pfix) {
  
    // Muestra headers comunes para ventanas de popup, se cargan las librerias
    // y estilos necesarios para el funcionamiento correcto de las funciones
    // preestablecidas en la ventana padre.
    // $pfix corresponde a la ruta para llegar a la base de la carpeta del sist.
    
    print('
    
    <LINK href="'.$pfix.'/css/interface.css" type="text/css" rel="stylesheet">
    <LINK href="'.$pfix.'/css/autocomplete.css" type="text/css" rel="stylesheet">

    <!-- estilos: ventanas... -->
    <LINK href="'.$pfix.'/css/windows/default.css" rel="stylesheet" type="text/css" >
    <LINK href="'.$pfix.'/css/windows/alphacube.css" rel="stylesheet" type="text/css" >
    <script type="text/javascript" src="'.$pfix.'/js/jquery-1.10.2.js"></script>
    <script type="text/javascript">
        var $j=jQuery.noConflict();
    </script>

    <!--- javascript: ajax framework... -->
    <SCRIPT src="'.$pfix.'/js/prototype.js" type="text/javascript"></SCRIPT>

    <!--- javascript: prototype autocomplete... -->
    <SCRIPT src="'.$pfix.'/js/autocomplete.js" type="text/javascript"></SCRIPT>

    <!--- javascript: manejador de ventana... -->
    <SCRIPT src="'.$pfix.'/js/window.js" type="text/javascript"></script>

    <!--- javascript: funciones comúnes... -->
    <SCRIPT src="'.$pfix.'/js/common.js.php" type="text/javascript"></SCRIPT>
  
    <!-- estilos: menú -->
    <LINK href="'.$pfix.'/css/office_xp/office_xp.css" type="text/css" rel="stylesheet">
  
    <!-- javascript: librerías del menú... -->
    <SCRIPT src="'.$pfix.'/js/jsdo/jsdomenu.js" type="text/javascript"></SCRIPT>
    <SCRIPT src="'.$pfix.'/js/jsdo/jsdomenubar.js" type="text/javascript"></SCRIPT>

    <SCRIPT src="'.$pfix.'/js/mainmenu.js.php" type="text/javascript"></SCRIPT>

    <!-- estilos: calendario -->
    <link rel="stylesheet" type="text/css" media="all" href="'.$pfix.'/css/calendar/calendar-blue.css" title="win2k-cold-1" />

    <!-- javascript: librerías de calendario... -->
    <script type="text/javascript" src="'.$pfix.'/js/calendar.js"></script>
    <script type="text/javascript" src="'.$pfix.'/js/lang/calendar-es.js"></script>
    <script type="text/javascript" src="'.$pfix.'/js/calendar-setup.js"></script>

    ');
  
  }
  
  function cabecera_popup_head($pfix) {
  
    // Muestra headers comunes para ventanas de popup, se cargan las librerias
    // y estilos necesarios para el funcionamiento correcto de las funciones
    // preestablecidas en la ventana padre.
    // $pfix corresponde a la ruta para llegar a la base de la carpeta del sist.
    
    return '
    
    <LINK href="'.$pfix.'/css/interface.css" type="text/css" rel="stylesheet">
    <LINK href="'.$pfix.'/css/autocomplete.css" type="text/css" rel="stylesheet">

    <!-- estilos: ventanas... -->
    <LINK href="'.$pfix.'/css/windows/default.css" rel="stylesheet" type="text/css" >
    <LINK href="'.$pfix.'/css/windows/alphacube.css" rel="stylesheet" type="text/css" >
    <script type="text/javascript" src="'.$pfix.'/js/jquery-1.10.2.js"></script>
    <script type="text/javascript">
        var $j=jQuery.noConflict();
    </script>

    <!--- javascript: ajax framework... -->
    <SCRIPT src="'.$pfix.'/js/prototype.js" type="text/javascript"></SCRIPT>

    <!--- javascript: prototype autocomplete... -->
    <SCRIPT src="'.$pfix.'/js/autocomplete.js" type="text/javascript"></SCRIPT>

    <!--- javascript: manejador de ventana... -->
    <SCRIPT src="'.$pfix.'/js/window.js" type="text/javascript"></script>

    <!--- javascript: funciones comúnes... -->
    <SCRIPT src="'.$pfix.'/js/common.js.php" type="text/javascript"></SCRIPT>
  
    <!-- estilos: menú -->
    <LINK href="'.$pfix.'/css/office_xp/office_xp.css" type="text/css" rel="stylesheet">
  
    <!-- javascript: librerías del menú... -->
    <SCRIPT src="'.$pfix.'/js/jsdo/jsdomenu.js" type="text/javascript"></SCRIPT>
    <SCRIPT src="'.$pfix.'/js/jsdo/jsdomenubar.js" type="text/javascript"></SCRIPT>

    <SCRIPT src="'.$pfix.'/js/mainmenu.js.php" type="text/javascript"></SCRIPT>

    <!-- estilos: calendario -->
    <link rel="stylesheet" type="text/css" media="all" href="'.$pfix.'/css/calendar/calendar-blue.css" title="win2k-cold-1" />

    <!-- javascript: librerías de calendario... -->
    <script type="text/javascript" src="'.$pfix.'/js/calendar.js"></script>
    <script type="text/javascript" src="'.$pfix.'/js/lang/calendar-es.js"></script>
    <script type="text/javascript" src="'.$pfix.'/js/calendar-setup.js"></script>

    ';
  
  }
  
	function formato_rut($rut) {
		$r=explode('-',$rut);
		return number_formats($r[0]*1).'-'.$r[1];	
	}  
  
  function number_formats($numero) {
    return number_format($numero, 0, ',', '.');
  }
  
  function selif($val1, $val2) {
    if($val1==$val2)  return 'SELECTED';
    else              return '';
  }
	
	function desplegar_opciones($tabla, $campos, $valsel, $condicion, $orden) {
	
		GLOBAL $conn;
	
		// **** TODO **** Escapar cadenas de entrada.
	
		$regs = 
		@pg_query($conn, "SELECT * FROM (SELECT $campos FROM $tabla) AS v1 WHERE $condicion $orden;");
		
		if(!$regs) return '';
		
		$returnhtml = '';
		
		while ($filas = pg_fetch_row($regs))  { 
		
			$filas[0]=htmlentities($filas[0]);
			$filas[1]=htmlentities($filas[1]);
			
			if($valsel==$filas[0]) {
				$returnhtml.="<OPTION VALUE='".$filas[0]."' SELECTED>".$filas[1]."</OPTION>\n"; 
			} else {
				$returnhtml.="<OPTION VALUE='".$filas[0]."'>".$filas[1]."</OPTION>\n"; 
			}
		}
		
		return $returnhtml;
	
	}
	
	function desplegar_opciones_sql($sql, $valsel=NULL, $prefix='', $style='') {
	
		GLOBAL $conn;
	
		// **** TODO **** Escapar cadenas de entrada.
	
		$regs = 
		pg_query($conn, $sql);
		
		$returnhtml = '';
		
		while ($filas = pg_fetch_row($regs))  { 
		
			$filas[0]=htmlentities($filas[0]);
			$filas[1]=htmlentities($filas[1]);
			
			if($valsel==$filas[0]) {
				$returnhtml.="<OPTION VALUE='".$prefix.''.$filas[0]."' style='$style' SELECTED>".$filas[1]."</OPTION>\n"; 
			} else {
				$returnhtml.="<OPTION VALUE='".$prefix.''.$filas[0]."' style='$style'>".$filas[1]."</OPTION>\n"; 
			}
		}
		
		return $returnhtml;
	
	}
	
	
	function cargar_registro($sql, $html=false) {
	
	  GLOBAL $conn;
  
    $registro = array();
 
    if(!($fila = pg_query($conn, $sql))) {
    	echo 'ERROR EN SQL: <pre>'.$sql.'</pre>';
    	return false;	
    }
    
    if(pg_num_rows($fila)==0) return false;
    
    for($i=0;$i<pg_num_fields($fila);$i++) {
    
      if(!$html)
        $registro[pg_field_name($fila, $i)]=pg_fetch_result($fila, 0, $i);
      else
        $registro[pg_field_name($fila, $i)]=htmlentities(pg_fetch_result($fila, 0, $i));
      
    
    }
    
    return $registro;
  
  }
  
  function cargar_registros($sql, $html=false) {
	
	  GLOBAL $conn;
  
    $registro = array();

    if(!($filas = pg_query($conn, $sql))) {
    	echo 'ERROR EN SQL: <pre>'.$sql.'</pre>';
    	return false;	
    }
    
    if(pg_num_rows($filas)==0) return false;
    
    for($r=0;$r<pg_num_rows($filas);$r++) {
      $registro[$r]= array();
      
      for($i=0;$i<pg_num_fields($filas);$i++) {
        if(!$html) $registro[$r][$i]=pg_fetch_result($filas, $r, $i);
        else  $registro[$r][$i]=htmlentities(pg_fetch_result($filas, $r, $i));
      }
    
    }
    
    return $registro;
  
  }

  function cargar_registros_obj($sql, $html=false) {
	
	  GLOBAL $conn;
  
    $registro = array();
    $filas = pg_query($conn, $sql);

    if(!($filas = pg_query($conn, $sql))) {
    	echo 'ERROR EN SQL: <pre>'.$sql.'</pre>';
    	return false;	
    }
    
    if(pg_num_rows($filas)==0) return false;
    
    for($r=0;$r<pg_num_rows($filas);$r++) {
      $registro[$r]= array();
      
      for($i=0;$i<pg_num_fields($filas);$i++) {
        if(!$html) $registro[$r][pg_field_name($filas, $i)]=pg_fetch_result($filas, $r, $i);
        else  $registro[$r][pg_field_name($filas, $i)]=htmlentities(pg_fetch_result($filas, $r, $i));
      }
    
    }
    
    return $registro;
  
  }

  
  function pg_array_parse($s) {
  
    if($s!='{""}' AND $s!=NULL) {
      $s = str_replace("{", "Array('", $s);
      $s = str_replace("}", "')", $s);
      $s = str_replace(",", "','", $s);   
    } else {
      $s = 'Array("")';
    } 
    
    $s = "\$retval = $s;";
    
    eval($s);
    return $retval;
    
  }

	
	function comprobar_talonario($art_id) {
    
    GLOBAL $tal_art;
    
    for($i=0;$i<count($tal_art);$i++) {
      
      if($tal_art[$i][0]==$art_id) return true;
      
    }
      
    return false;
    
  }
 
	function tipo_talonario($art_id) {
	
	  GLOBAL $tal_art;
    
    for($i=0;$i<count($tal_art);$i++) {
      
      if($tal_art[$i][0]==$art_id) return $tal_art[$i][1];
      
    }
      
    return false;
    
  }
  
  function funcionario_talonario($art_id) {
	
	  GLOBAL $tal_art;
    
    for($i=0;$i<count($tal_art);$i++) {
      
      if($tal_art[$i][0]==$art_id) 
          return $tal_art[$i][2];
        
    }
      
    return false;
    
  }

  //function validez_talonario($tipo_talonario, $nro_talonario, $nro_inicial, $nro_final) {
  function validez_talonario($tipo_talonario, $nro_inicial, $nro_final) {


  GLOBAL $conn;

  if($nro_final<=$nro_inicial) {
    return(Array(false, 'Numeraci&oacute;n ingresada no es v&aacute;lida. '.$nro_final.'<='.$nro_inicial));
  }
  
 /* $tal_q = pg_query($conn, "
  SELECT talonario_id FROM talonario WHERE
  talonario_tipotalonario_id=".$tipo_talonario." AND
  talonario_numero=".$nro_talonario.";
  ");
  
  if(pg_num_rows($tal_q)!=0) {
    return(Array(false, 'Talonario ya existe en la base de datos.'));
  } */
  
  $tal_q = pg_query($conn, "
  SELECT talonario_id FROM talonario 
  WHERE
  talonario_tipotalonario_id=".$tipo_talonario." AND
  (
  (talonario_inicio<=$nro_inicial AND talonario_final>=$nro_inicial)
  OR
  (talonario_inicio<=$nro_final AND talonario_final>=$nro_final)
  OR
  (talonario_inicio>=$nro_inicial AND talonario_final<=$nro_final)
  )
  ");
  
  if(pg_num_rows($tal_q)!=0) {
    return(Array(false, 'La numeraci&oacute;n ingresada para el talonario ya est&aacute; siendo utilizada.'));
  }
  
  return Array(true,null);
  
  }
	
	function _ca($_accesos,$_permiso_id,$_valores=NULL) {
  
    // Compara un valor(es) específico contra la lista de permisos
    // cargada pasada y devuelve true en caso de existir
    
    $_vals = explode(',', $_valores);
  
    for($i=0;$i<count($_accesos);$i++) {
    
      if($_accesos[$i][1]==$_permiso_id) {
        
        if(is_null($_valores))  
          return true;
        
        $_ivals = explode(',', $_accesos[$i][2]);
        
        foreach($_vals AS $_val)
          foreach($_ivals AS $_ival) 
            if($_ival==$_val) return true;
        
      }
    
    }
    
    return false;
  
  }
  
  function _func_permitido($acceso, $valor) {
    GLOBAL $acceso_func_array;
    
    if(!isset($acceso_func_array[$acceso])) return false;
    
    $vals = explode(',', $acceso_func_array[$acceso]);
    
    for($i=0;$i<count($vals);$i++) 
      if($valor==$vals[$i]) return true;
      
    return false;
  }
  
  function _func_permitido_cc($acceso, $valor) {
    GLOBAL $acceso_func_array2;
    
    if(!isset($acceso_func_array2[$acceso])) return false;
    
    $vals = explode(',', $acceso_func_array2[$acceso]);
    
    for($i=0;$i<count($vals);$i++) 
      if($valor==$vals[$i]) return true;
      
    return false;
  }
  
  
	
  function _cax($_permiso_id) {
  
    GLOBAL $acceso_func_array; 
  
    // Compara un valor(es) específico contra la lista de permisos
    // cargada pasada y devuelve true en caso de existir
    
   if(isset($acceso_func_array[$_permiso_id])) return true;
    
    return false;
  
  }
  
  function _cav($_permiso_id) {
    GLOBAL $acceso_func_array;
    return $acceso_func_array[$_permiso_id];
  }
	
  function _cav2($_permiso_id) {
    GLOBAL $acceso_func_array2;
    
    return $acceso_func_array2[$_permiso_id];
  }

	
	function lotes_vigentes($art_id, $bod_id) {
  
  $lotes = pg_query($conn, "
  SELECT 
	log_fecha AS lote_fecha_ingreso, 
	stock_vence AS lote_fecha, 
	SUM(stock_cant) AS lote_stock_entrada, 
	0 AS lote_stock 
	FROM stock 
	LEFT JOIN logs ON log_id=stock_log_id
	WHERE 
	stock_art_id=".$art_id."
	AND
	stock_bod_id=".$bod_id."
	AND
	(log_tipo IN (1,2,4,5,20)) 
	AND stock_cant>0
	GROUP BY log_fecha, stock_vence
	ORDER BY stock_vence
  ");
  
  }
		


function CargarXML($ruta_fichero, $tagnames)
{
$contenido = "";
if($da = fopen($ruta_fichero,"r"))
{
while ($aux= fgets($da,1024))
{
$contenido.=$aux;
}
fclose($da);
}
else
{
echo "Error: no se ha podido leer el archivo <strong>$ruta_fichero</strong>";
}

$contenido=iconv("UTF-8", "ISO-8859-1", $contenido);

if (!$xml = domxml_open_mem($contenido))
{
echo "Ha ocurrido un error al procesar el documento<strong> \"$ruta_fichero\"</strong> a XML <br>";
exit;
}
else
{
$raiz = $xml->document_element();

$tam=sizeof($tagnames);

for($i=0; $i<$tam; $i++)
{
$nodo = $raiz->get_elements_by_tagname($tagnames[$i]);
$j=0;
foreach ($nodo as $etiqueta)
{
$matriz[$j][$tagnames[$i]]=$etiqueta->get_content();
$j++;
}
}

return $matriz;
}
} 
	


	$conn = pg_connect("host=$sghserver port=$sghport 
	dbname=$sghdbname user=$sghuser password=$sghpass"); 
	
  
  if(!$conn) { die('Problemas con la Conexi&oacute;n.'); }
	
	session_start();
	
	if(!isset($_SESSION['sgh_usuario']) AND !isset($_POST['usuario'])) {
    // Pantalla de Login
  }
	
	if(!isset($_SESSION['sgh_usuario']) AND isset($_POST['usuario'])) {
	
		// Logueo e inicio de sesión...
		
		$user = pg_escape_string(strtoupper($_POST['usuario']));
		$pass = pg_escape_string($_POST['pass']);
		
		$usuario = pg_query($conn, 
		"SELECT 
    func_rut, func_nombre, func_cargo, func_clave, func_id 
    FROM
		funcionario 
    WHERE func_rut='$user' 
    AND func_clave=md5('$pass') 
    LIMIT 1");
		
		if(pg_num_rows($usuario)!=1) {
		  die('<script> 
			alert("Datos de ingreso al sistema incorrectos."); 
			window.open("login.php", "_self");
			</script>');
		} 
		
  		$datos = pg_fetch_row($usuario);
			
			$nombre_usuario = $datos[1];
			$cargo_usuario = $datos[2];
			$funcionario_id = $datos[4];
		
		  $_SESSION['sgh_username']   = $user;
		  $_SESSION['sgh_usuario']    = $nombre_usuario;
		  $_SESSION['sgh_cargo']      = $cargo_usuario;
		  $_SESSION['sgh_usuario_id'] = $funcionario_id;
		  
		  ob_start();
		  
      $fp = fopen($sghsessionpath.''.$_SERVER['REMOTE_ADDR'].'.session', 'w');
		  fwrite($fp, $_SESSION['sgh_username']."\n");
		  fwrite($fp, $_SESSION['sgh_usuario']."\n");
		  fwrite($fp, $_SESSION['sgh_cargo']."\n");
		  fwrite($fp, $_SESSION['sgh_usuario_id']."\n");
		  fwrite($fp, date('d-m-Y H:i:s'));
      fclose($fp);
      
      ob_end_clean();		  
		  
	} 
	
	// Sistema de Respaldo de Sesiones.
	// Recupera informacion relevante en caso de que las cookies de 
	// sesión hayan expirado o se hayan perdido.
  
  ob_start();
  
  $fsess=$sghsessionpath.''.$_SERVER['REMOTE_ADDR'].'.session';
  
  if(!$_SESSION OR 
        !isset($_SESSION['sgh_usuario_id']) OR 
        ($_SESSION['sgh_usuario_id'])==0) {
  
		  if(file_exists($fsess)) {
        $fp = explode("\n", file_get_contents($fsess));
  		  $_SESSION['sgh_username'] = $fp[0];
  		  $_SESSION['sgh_usuario'] = $fp[1];
  		  $_SESSION['sgh_cargo'] = $fp[2];
  		  $_SESSION['sgh_usuario_id'] = $fp[3];
  		  $rec="RECUPERADO (".$fp[3].")";
  		  $die=false;
		  } else {
        $rec="NO RECUPERADO!!!";
        $die=true;
		  }
		  
		  $fp = fopen($sghsessionpath.'errores.log', 'a');
		  fwrite($fp, date('d-m-Y H:i:s').": ERROR DE SESION - ".$rec."\n");
		  fwrite($fp, "En el módulo '".$_SERVER['SCRIPT_FILENAME']."'\n");
		  fwrite($fp, "IP: ".$_SERVER['REMOTE_ADDR']."\n");
		  fwrite($fp, "================================================\n");
		  fclose($fp);
		  
		  if($die) {
		    header('Location: login.php');
        die();
      }
      
  } else {
  
  	  $fp = fopen($fsess, 'w');
		  fwrite($fp, $_SESSION['sgh_username']."\n");
		  fwrite($fp, $_SESSION['sgh_usuario']."\n");
		  fwrite($fp, $_SESSION['sgh_cargo']."\n");
		  fwrite($fp, $_SESSION['sgh_usuario_id']."\n");
		  fwrite($fp, date('d-m-Y H:i:s'));
      fclose($fp);		  
	
  }
  
  ob_end_clean();


  // -----------------------------------------------------------
	
	$accesos_actuales = pg_query($conn,
  "SELECT acceso_id, permiso_id, valor FROM func_acceso 
  WHERE func_id=".($_SESSION['sgh_usuario_id']*1)." AND NOT acceso_ruta");
  
  $accesos_actuales2 = pg_query($conn,
  "SELECT acceso_id, permiso_id, valor FROM func_acceso 
  WHERE func_id=".($_SESSION['sgh_usuario_id']*1)." AND acceso_ruta");
  
  for($m=0;$m<pg_num_rows($accesos_actuales);$m++) {
    $acceso_funcionario[$m] = pg_fetch_row($accesos_actuales);
    $acceso_func_array[$acceso_funcionario[$m][1]]=$acceso_funcionario[$m][2];
  }
  
  for($m=0;$m<pg_num_rows($accesos_actuales2);$m++) {
    $acceso_funcionario[$m] = pg_fetch_row($accesos_actuales2);
    $acceso_func_array2[$acceso_funcionario[$m][1]]=$acceso_funcionario[$m][2];
  }
  
	$globales = pg_query($conn,
  "SELECT iva FROM globales");
  
  list($sgh_institucion) = cargar_registros_obj( 
  "SELECT * FROM instituciones WHERE inst_codigo_ifl=".$sgh_inst_codigo_ifl
  );
  
  $art_talonarios = pg_query($conn, 
  "SELECT art_id, tipotalonario_id, tipotalonario_funcionario 
  FROM receta_tipo_talonario
  WHERE art_id IS NOT NULL");
	
	$tal_art=Array();
	
	for($i=0;$i<pg_num_rows($art_talonarios);$i++) {
  
    $tal_art[$i][0]=pg_fetch_result($art_talonarios, $i, 0);
    $tal_art[$i][1]=pg_fetch_result($art_talonarios, $i, 1);
    $tal_art[$i][2]=pg_fetch_result($art_talonarios, $i, 2);
      
  }
    
  $datos_globales = pg_fetch_row($globales);
      
  $_global_iva = $datos_globales[0];

// LOG DE ACCESOS...

/*

create table logs_acceso (la_id bigserial, func_id bigint, fecha timestamp without time zone, ip text, ruta text, http_get text, http_post text);

*/

ob_start();
$func_id=$_SESSION['sgh_usuario_id']*1;
$ip=$_SERVER['REMOTE_ADDR'];
$ruta=pg_escape_string($_SERVER['PHP_SELF']);
$get=pg_escape_string(json_encode($_GET));
$post=pg_escape_string(json_encode($_POST)); // SOLO PARA DEBUG?
if($ruta!='/produccion/chat_status.php' and $ruta!='/produccion/autocompletar_sql.php' and $ruta!='/produccion/js/common.js.php' and $ruta!='/produccion/js/mainmenu.js.php')
{
	pg_query("INSERT INTO logs_acceso VALUES (DEFAULT, $func_id, CURRENT_TIMESTAMP, '$ip', '$ruta', '$get', '$post');");
}
ob_end_clean();
?>