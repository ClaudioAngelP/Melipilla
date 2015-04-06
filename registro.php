<?php
    // REQUIERE: Librería PECL JSON en PHP
    require_once('conectar_db.php');
    require_once('conectores/fonasa/cargar_paciente_fonasa.php');
    if($_GET['tipo']=='articulo')
    {
        $codigo = pg_escape_string($_GET['codigo']);
        $articulo = pg_query($conn, "
        SELECT
        art_id,
        art_glosa,
        art_vence,
        art_nombre,
        art_forma,
        art_clasifica_id,
        art_auge,
        art_reposicion,
        art_item,
        art_prioridad_id,
        art_activado,
        art_control,
        art_val_ult
        FROM articulo 
        WHERE 
        art_codigo='$codigo' 
        LIMIT 1");
	
        $datos = pg_fetch_row($articulo);
        if (count($datos) > 1)
        {
            for($i=0;$i<count($datos);$i++)
            {
                $datos[$i]=htmlentities($datos[$i]);
            }
            print(json_encode($datos));
        }
    } 
    if($_GET['tipo']=='articulo_resumen')
    {
        $codigo = $_GET['codigo'];
        $articulo = pg_query($conn, "
        SELECT
        art_id,
        art_glosa,
        art_nombre,
        item_glosa,
        clasifica_nombre,
        forma_nombre,
        art_vence,
        art_control,
        art_val_ult 
        FROM articulo 
        LEFT JOIN item_presupuestario ON item_codigo=art_item
        LEFT JOIN bodega_clasificacion ON clasifica_id=art_clasifica_id
        LEFT JOIN bodega_forma ON forma_id=art_forma
        WHERE 
	art_codigo='$codigo' 
	LIMIT 1");
	
	$datos = pg_fetch_row($articulo);
	for($i=0;$i<count($datos);$i++)
        {
            $datos[$i]=htmlentities($datos[$i]);
	}
	
        if (count($datos) > 1)
        {
            print(json_encode($datos));
	}
    } 
	
    if($_GET['tipo']=='fecha_lote')
    {
        $codigo = $_GET['codigo'];
	$fecha_art = $_GET['fecha'];
	$fecha = pg_query($conn, "SELECT * FROM stock, articulo 
	WHERE art_codigo='$codigo' 
	AND 
	stock_vence='$fecha_art'
	AND
	art_id=stock_art_id");
	if(pg_num_rows($fecha)) { $devuelve=1; } else { $devuelve=0; }
	print(json_encode($devuelve));
    }
	
    if($_GET['tipo']=='busca_prod')
    {
	$cadena = pg_escape_string($_GET['buscar']);
	$bodega = $_GET['bodega_origen'];
	$orden = $_GET['orden'];
	$listado = pg_query($conn, "
	SELECT 
	*
	FROM
	(
            SELECT 
            art_id,
            art_codigo,
            art_glosa,
            forma_nombre,
            (
                SELECT 
		SUM(stock_cant) 
		FROM stock 
		WHERE stock_art_id=art_id
		AND stock_bod_id=$bodega
		AND (stock_vence>current_date OR stock_vence is null)
            )AS stock 
            FROM 
            articulo
            LEFT JOIN bodega_forma
            ON art_forma=forma_id
            WHERE (
                art_codigo ILIKE '%$cadena%'
		OR 
		art_glosa ILIKE '%$cadena%'
		OR
		art_nombre ILIKE '%$cadena%'
            )
        ) AS ss
        ORDER BY art_codigo
        LIMIT 10");
        if(pg_num_rows($listado)==1)
        {
            $fila = pg_fetch_row($listado);
            printf("<script> abrir_producto(\"".$fila[0]."\",0); </script>");
        }
	if(pg_num_rows($listado)==0 or pg_num_rows($listado)>1)
        {
            print("<center>
                <table><tr class='tabla_header'>
		<td class='tabla_header' width=80><b><i>C&oacute;digo</i></b></td>
		<td width=150><b><i>Glosa Producto</i></b></td>
		<td><b><i>Forma Farmac&eacute;utica</i></b></td>
		<td><b><i>Stock Disp.</i></b></td>
		</tr>
            ");
            $alterna=0;
            for($i=0;$i<pg_num_rows($listado);$i++)
            {
                $fila=pg_fetch_row($listado);
		if($alterna==0)
                {
                    $clase='tabla_fila';
                    $alterna=1;
		}
                else
                {
                    $clase='tabla_fila2';
                    $alterna=0;
		}
		printf("
		<tr class='$clase'
		onClick='abrir_producto(\"".$fila[0]."\",1);'
		onMouseOver='this.className=\"mouse_over\"'
		onMouseOut='this.className=\"$clase\"'
		>");
		print("
		<td class='izquierda'>
		<i><b>".$fila[1]."</b></i></td>
		<td class='izquierda'>
		<b>".htmlentities($fila[2])."</b></td>
		<td>".htmlentities($fila[3])."</td>
		<td class='derecha'>".($fila[4]*1)."</td>
		</tr>");
            }
            print('</table></center>');
        }
    }
	
    if($_GET['tipo']=='paciente')
    {
        if(isset($_GET['paciente_tipo_id']))
        {
            $tipo = $_GET['paciente_tipo_id']*1;
        }
        else
        {
            $tipo = 0;    
        }
        if($tipo!=2)
            $id = pg_escape_string($_GET['paciente_rut']);
        else
            $id = $_GET['paciente_rut']*1;
        if($tipo==0)
        {
            $paciente = pg_query($conn,"SELECT * FROM pacientes WHERE pac_rut='$id';");
        }
        else if($tipo==3)
            $paciente = pg_query($conn,"SELECT * FROM pacientes WHERE pac_ficha='$id';");
        else if($tipo==1)
            $paciente = pg_query($conn,"SELECT * FROM pacientes WHERE pac_pasaporte='$id';");
        else
            $paciente = pg_query($conn,"SELECT * FROM pacientes WHERE pac_id=$id;");
        
        if(pg_num_rows($paciente)>0)
        {
            // Paciente encontrado en base de datos local.
            $pac = pg_fetch_row($paciente);
            for($i=0;$i<count($pac);$i++)
            {
                $pac[$i]=htmlentities($pac[$i]);
                if(is_null($pac[$i])) $pac[$i]='';
            }
            print(json_encode($pac));
        }
        else if($tipo==0 or $tipo==3)
        {
            // Paciente deberá ser buscado usando conectores
            // a bases de datos secundarias.    
            $q=$id;
            /*
            $conectores=scandir('conectores/pacientes/');
            for($i=2;$i<count($conectores);$i++)
            { 
                include('conectores/pacientes/'.$conectores[$i]);
                if($pac_id!=-1) break;
            }        
            if($pac_id==-1) exit();
            $paciente = pg_query($conn,"SELECT * FROM pacientes WHERE pac_id=$pac_id;");
            $pac = pg_fetch_row($paciente);
            for($i=0;$i<count($pac);$i++)
            {
                $pac[$i]=htmlentities($pac[$i]);
            }
            */
            function formatear_rut($str)
            {
                $partes=explode('-',$str);
                return number_format($partes[0]*1,0,',','.').'-'.strtolower($partes[1]);
                
            }
            $pac=cargar_registro("SELECT *,(SELECT MAX(cert_fecha) FROM pacientes_fonasa WHERE pacientes_fonasa.pac_rut=pacientes.pac_rut) AS fecha_fonasa FROM pacientes WHERE pac_rut='".$q."'", true);
            if(!$pac)
            {
               pac_fonasa($q,0);
            }
            $pac=cargar_registro("SELECT *,(SELECT MAX(cert_fecha) FROM pacientes_fonasa WHERE pacientes_fonasa.pac_rut=pacientes.pac_rut) AS fecha_fonasa FROM pacientes WHERE pac_rut='".$q."'", true);
            if($pac)
            {
                $pac_id=$pac['pac_id'];
                $paciente = pg_query($conn,"SELECT * FROM pacientes WHERE pac_id=$pac_id;");
                $pac = pg_fetch_row($paciente);
                for($i=0;$i<count($pac);$i++)
                {
                    $pac[$i]=htmlentities($pac[$i]);
                    if(is_null($pac[$i]))
                        $pac[$i]='';
                }
                exit(json_encode($pac));
            }
            else
            {
                echo 'Error al procesar paciente';
                die();
            }
            /*
            $ch = curl_init();
            if($tipo==0)
                curl_setopt($ch, CURLOPT_URL, "http://10.5.132.11/produccion/conectores/trakcare/login.php?buscar=".urlencode(formatear_rut($q)));
            if($tipo==3)
                curl_setopt($ch, CURLOPT_URL, "http://10.5.132.11/produccion/conectores/trakcare/login.php?buscar2=".urlencode($q));
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $_id=curl_exec($ch);
            curl_close($ch);
            if($_id==0)
            {
                $pac_id=-1;
                $pac=false;
            }
            else
            {
                $pac_id=$_id;
                $paciente = pg_query($conn,"SELECT * FROM pacientes WHERE pac_id=$pac_id;");
                $pac = pg_fetch_row($paciente);
                for($i=0;$i<count($pac);$i++)
                {
                    $pac[$i]=htmlentities($pac[$i]);
                    if(is_null($pac[$i]))
                        $pac[$i]='';
                }
            }
            exit(json_encode($pac));
            */
        }
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    if($_GET['tipo']=='buscar_presta') {
	
	   $grupos = pg_query($conn,"
     SELECT 
     id,
     grupo,
     sub_grupo,
     presta     
     FROM 
     mai
     WHERE 
     (NOT grupo='00')
     AND
     sub_grupo='00'
     AND
     presta='000'
     AND
     corr='0000'
     ORDER BY
     grupo
     "
     );
     
     $gruposhtml='';
     
     for($i=0;$i<pg_num_rows($grupos);$i++) {
     
        $fila = pg_fetch_row($grupos);
        
        $gruposhtml.='<option value="'.$fila[1].'">';
        
        $glosa = pg_query($conn, "
        SELECT
        glosa
        FROM 
        mai
        WHERE
        grupo='".$fila[1]."'
        AND
        sub_grupo='".$fila[2]."'
        AND
        presta='".$fila[3]."'
        ORDER BY
        corr
        ");
        
        $glosa_comp='';
        
        for($a=0;$a<pg_num_rows($glosa);$a++) {
            $pedazo = pg_fetch_row($glosa);
            $glosa_comp.=$pedazo[0];
        }
        
        $gruposhtml.=substr($glosa_comp,0,30).'</option>';
     
     }
	
	   print("
     <html>
		
  		<title>Buscar Prestaci&oacute;n</title>
		
  	  <script src='prototype.js' type='text/javascript'></script>
	
		  <link rel='stylesheet' href='estilos.css' type='text/css'>	
     
      <script>
      
      traer_subgrupo = function () {
      
      var myAjax = new Ajax.Updater(
			'sub_grupo_div', 
			'auxphp.php', 
			{
				method: 'get', 
				parameters: 'tipo=sub_grupo&grupo='+$('grupo').value,
				evalScripts: true
	
			}
	   );
      
      }
      
      </script>
     
      <body topmargin=0 leftmargin=0 rightmargin=0 style='
      font-family: Arial, Helvetica, sans-serif;
      '>
     
     <div class='sub-content'>
     <div class='sub-content'><img src='iconos/user_go.png'>
     <b>Buscar Prestaciones</b></div>
     <div class='sub-content'>
     <table>
     <tr><td style='text-align:right;'><b>Grupo:</b></td>
     <td>
     <input type='text' size=2 id='grupo_cod' name='grupo_cod' onBlur='
     $(\"grupo\").value=$(\"grupo_cod\").value;
     traer_subgrupo();
     '>
     <select id='grupo' name='grupo' onClick='
     $(\"grupo_cod\").value=$(\"grupo\").value;
     traer_subgrupo();
     '>
     ".$gruposhtml."
     </select>
     </td></tr>
     <tr><td style='text-align:right;'><b>Sub-Grupo:</b></td>
     <td>
     <div id='sub_grupo_div'>
     <input type='text' size=2 id='subgrupo_cod' name='subgrupo_cod' DISABLED>
     <select id='subgrupo' name='subgrupo' DISABLED></select>
     </div>
     </td></tr>
     </table>
     
     </div> 
     <div class='sub-content2' id='busqueda' style='
     height:240px; min-height:240px; overflow:auto;
     '>
     ");
     
     
     
     print("
     </div>
     </div>
     
     </body>
     </html>
     ");
	
  } 

?>
