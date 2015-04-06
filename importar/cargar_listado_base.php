<?php 

	require_once('../conectar_db.php');
	
	$fa=explode("\n",utf8_decode(file_get_contents('listado_base.csv')));
	
	pg_query("START TRANSACTION;");
	
	for($i=1;$i<sizeof($fa);$i++) {
		
		if(trim($fa[$i])=="") continue;
	
		$r=explode('|',pg_escape_string(($fa[$i])));
		
		$cod=str_replace('-', '', $r[0]);
		
		$nom=trim(strtoupper($r[1]));
		$forma=trim(strtoupper($r[2]));
		$precio=$r[3];
		
		$f=cargar_registro("SELECT * FROM bodega_forma WHERE forma_nombre='$forma'");
		
		if($f) {
			$forma_id=$f['forma_id'];
		} else {
			pg_query("INSERT INTO bodega_forma VALUES (DEFAULT, '$forma');");
			$forma_id="CURRVAL('bodega_forma_forma_id_seq')";
		}
		
		$chk=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$cod'");

/*


CREATE TABLE articulo
(
  art_id bigserial NOT NULL,
  art_codigo text,
  art_vence character(1),
  art_glosa text,
  art_nombre text,
  art_forma integer,
  art_auge boolean,
  art_clasifica_id integer,
  art_reposicion boolean,
  art_item text NOT NULL,
  art_val_min double precision,
  art_val_med double precision,
  art_val_max double precision,
  art_val_ult double precision,
  art_prioridad_id smallint NOT NULL DEFAULT 1,
  art_activado boolean DEFAULT true,
  art_control integer,
  art_arsenal boolean DEFAULT false,
  CONSTRAINT articulo_id_index PRIMARY KEY (art_id)
)
WITH (
  OIDS=FALSE
);
*/
		
		if(!$chk) {
			$query = "
				INSERT INTO articulo VALUES (
					DEFAULT, '$cod', '1', '$nom', '$nom',
					$forma_id, false, 0, false, '2204004001', 0, 0, 0, 0, 1,
					true, 0, false
				);
			";
			
		pg_query($query);	
		echo $query;
		flush();
		} 
		
	}
	
	pg_query("COMMIT;");

?>
