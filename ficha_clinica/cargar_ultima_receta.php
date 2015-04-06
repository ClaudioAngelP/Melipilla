<?php
    require_once('../conectar_db.php');
    $pac_id=$_POST['pac_id']*1;
    $bod_id=$_POST['bodega_id']*1;
    $receta_id=$_POST['receta_id']*1;
    if(!isset($_POST['nomina_detalle'])){
        if($receta_id==0) {
            $receta_w="
            receta_paciente_id=$pac_id
            AND receta_tipotalonario_id = 0
            ORDER BY receta_fecha_emision DESC
            ";
        } else {
            $receta_w="receta.receta_id=$receta_id";
        }
        $consulta="SELECT receta.*,doctores.*,centro_costo.*,pacientes.*,
        adq_id,adq_rut,adq_nombres,adq_appat,adq_apmat,adq_direccion,adq_ciud_id,
        receta_fecha_emision::date as fecha_retro 
        FROM receta 
        JOIN doctores ON receta_doc_id=doc_id
        JOIN centro_costo ON receta_centro_ruta=centro_ruta
        JOIN pacientes on pac_id=receta_paciente_id
        LEFT JOIN receta_adquiriente ra ON ra.receta_id=receta.receta_id
        WHERE $receta_w;";
        $r=cargar_registro($consulta,true);
        if($receta_id==0)
            $r['id']='';
        else
            $r['id']=$receta_id;
	
        if($r) {
            $consulta="
            SELECT *,
            COALESCE(
                        (
                            SELECT SUM(stock_cant) FROM stock
                            JOIN logs ON stock_log_id=log_id
                            LEFT JOIN pedido ON log_id_pedido=pedido_id
                            LEFT JOIN pedido_detalle ON pedido.pedido_id=pedido_detalle.pedido_id AND stock_art_id=pedido_detalle.art_id
                            WHERE 
                            stock_art_id=recetad_art_id AND 
                            (pedido.pedido_id IS NULL OR pedidod_estado OR origen_bod_id=0) AND stock_bod_id=$bod_id
                        )
                        ,0
            ) AS stock,
            upper(forma_nombre) AS forma_nombre,
            upper(COALESCE(art_unidad_adm, forma_nombre)) AS art_unidad_adm,
            COALESCE(art_unidad_cantidad, 1) AS art_unidad_cantidad_adm,
            (SELECT count(*) FROM lotes_vigentes(art_id, $bod_id))as cant_lotes
            FROM recetas_detalle 
            JOIN articulo ON recetad_art_id=art_id
            LEFT JOIN bodega_forma ON art_forma=forma_id
            WHERE recetad_receta_id=".$r['receta_id']."
            ";
            $r['detalle']=cargar_registros_obj($consulta, true);
        }
        exit(json_encode($r));
    } else {
        $esp_id=$_POST['esp_id']*1;
        if($receta_id==0) {
            $receta_w=" receta_paciente_id=$pac_id AND receta_tipotalonario_id=0 ORDER BY receta_fecha_emision DESC";
        } else {
            $receta_w="receta_id=$receta_id";
        }
        $consulta="SELECT *,receta_fecha_emision::date as fecha_retro FROM receta 
        JOIN doctores ON receta_doc_id=doc_id
        JOIN centro_costo ON receta_centro_ruta=centro_ruta
        WHERE $receta_w;";
        $r=cargar_registro($consulta,true);
        if($r){
            
            
            
        }
        
    }
?>