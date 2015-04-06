<?php 

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

?>