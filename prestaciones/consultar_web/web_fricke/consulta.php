<?php 

	// Proxy para obtener datos desde servidor HGF por red MINSAL

	error_reporting(E_ALL);

	session_start();

	
	if(!isset($_SESSION['hgf_rut'])) {

		if(isset($_POST['rut'])) {
		
			$rut=$_POST['rut'];
			$clave=$_POST['clave'];
		
			$_SESSION['hgf_rut']=$rut;
			$_SESSION['hgf_clave']=$clave;
		
		} else {

			session_destroy();
			exit("<script>window.open('login.php','_self');</script>");
		
		}
	
	} else {

		$rut=$_SESSION['hgf_rut'];
		$clave=$_SESSION['hgf_clave'];
		
	}
	
	$accion=0;
	
	if(isset($_GET['accion'])) {
		$accion=$_GET['accion']*1;
	} 
	
	if($accion==10) {
		
		session_destroy();
		exit("
			<script>
				alert('SESION TERMINADA.');
				window.open('login.php','_self');
			</script>
		");
		
	}
	
	if($accion==0) {
		
		$data=file_get_contents('http://10.6.75.33/sgh/prestaciones/consultar_web/consulta.php?rut='.$rut.'&clave='.$clave);
		print($data);
		
	} elseif($accion==1) {
		
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: inline; filename=CITACIONES_'.$rut.'.pdf');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		ob_clean();
		flush();
		
		if(!isset($_GET['citacion']))
			readfile('http://10.6.75.33/sgh/prestaciones/consultar_web/citaciones.php?rut='.$rut.'&clave='.$clave);
		else
			readfile('http://10.6.75.33/sgh/prestaciones/consultar_web/citaciones.php?rut='.$rut.'&clave='.$clave.'&citacion='.($_GET['citacion']*1));
		
	} else {
		 
		$data=file_get_contents('http://10.6.75.33/sgh/prestaciones/consultar_web/consulta.php?rut='.$rut.'&clave='.$clave.'&accion='.$accion);
		print($data);
	
	}

?>
