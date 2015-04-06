<?php
    require_once('../../conectar_db.php');
    error_reporting(E_ALL); 
    pg_query($conn, "START TRANSACTION;");
    // Ingresa Documento
    $prov_id=($_POST['prov_id']*1);
    $orden_compra=pg_escape_string($_POST['orden_compra_num']);
    $fecha_recep=pg_escape_string($_POST['fecha1']);
    $numero=$_POST['bodega_doc_asociado_num'];
    $tipo_doc=$_POST['bodega_doc_asociado']*1;
    $doc_id=($_POST['doc_id']*1);
    $tipo=$_POST['tipo_dist']*1;
    $observaciones=pg_escape_string (utf8_decode($_POST['observaciones']));
    $centro_costo=pg_escape_string($_POST['centro_ruta']);
    $gastoext_id=$_POST['centro_grupo']*1;
    if(isset($_POST['iva_incl'])){
        $descuento=($_POST['doc_descuento']*1)/$_global_iva;
    } 
    else {
        $descuento=($_POST['doc_descuento']*1);
    }
    
    if($doc_id==0) {
        $orden_id=0;
        $tmp1=cargar_registro("SELECT * FROM orden_compra WHERE orden_numero='$orden_compra'");
        if($tmp1) {
            $orden_id=$tmp1['orden_id']*1;
        }
        
        pg_query($conn, "
        INSERT INTO 
	documento
	VALUES (
	DEFAULT,
	$prov_id,
	$tipo_doc,
	$numero,
        $_global_iva,
        $descuento,
        $orden_id, 
        '$orden_compra',
        '$observaciones',
        null,
        '$fecha_recep' )
        ");
        
        $doc_query="CURRVAL('documento_doc_id_seq')";
    } 
    else {
        $doc_query=$doc_id;
    }
		
    // Ingresa Log
    pg_query($conn, "
    INSERT INTO 
    logs
    VALUES (
    DEFAULT,
    ".($_SESSION['sgh_usuario_id']*1).",
    50,
    current_timestamp,
    0,
    $doc_query,
    NULL )
    ");
	
    if($tipo==0) {
        pg_query($conn, "INSERT INTO cargo_centro_costo VALUES (CURRVAL('logs_log_id_seq'),'$centro_costo');");
    } 
    else {
        pg_query($conn, "INSERT INTO cargo_gasto_externo VALUES (CURRVAL('logs_log_id_seq'),$gastoext_id);");
    }
    // Ingresa Gastos por Servicios
    $cant_servs=($_POST['cant_gasto']*1);
    for($i=0;$i<$cant_servs;$i++) {
        if(isset($_POST['gasto_item_'.$i])) {
            $item=pg_escape_string($_POST['gasto_item_'.$i]);
            $glosa=pg_escape_string($_POST['gasto_glosa_'.$i]);
            $unidad=pg_escape_string($_POST['gasto_unidad_'.$i]);
            $cant=$_POST['gasto_cant_'.$i]*1;
            $valunit=$_POST['gasto_valunit_'.$i]*1;
            $art_id=$_POST['gasto_art_id_'.$i]*1;
          
            if(isset($_POST['iva_incl'])) {
                $val=($_POST['gasto_val_'.$i]*1)/$_global_iva;
            }
            else {
                $val=($_POST['gasto_val_'.$i]*1);
            }
            pg_query("INSERT INTO servicios VALUES (DEFAULT,'$glosa',$cant,$val,'$unidad','$item',CURRVAL('logs_log_id_seq'),$art_id)");
        } 
    } // Fin del FOR
		
    if($doc_id==0) {
        $doc_id = pg_query($conn, "SELECT CURRVAL('documento_doc_id_seq');");
        $doc_arr = pg_fetch_row($doc_id);
        $doc_id_m = $doc_arr[0];
    }
    else {
        $doc_id_m = $doc_id;
    }
    
    if($orden_compra!='') {
        $tmp1=cargar_registro("SELECT * FROM orden_compra WHERE orden_numero='$orden_compra'");
        if($tmp1) {
            $orden_id=$tmp1['orden_id']*1;
            pg_query("UPDATE orden_compra SET orden_estado=2 WHERE orden_id=$orden_id;");
        }
    }
    pg_query($conn, "COMMIT;");
    print(json_encode(Array(true, $doc_id_m)));
?>