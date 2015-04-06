<?php
    require_once('../../conectar_db.php');
    require_once('../../graficos/SVGGraph/SVGGraph.php');
    set_time_limit(0);
    $art_id=$_POST['art_id']*1;
    
    $art=cargar_registro("SELECT * FROM articulo where art_id=$art_id");
    
    
    $datos=cargar_registros_obj("SELECT *,abs(stock_subtotal/stock_cant)as precio_unit,
    extract(YEAR FROM log_fecha) AS year,
    extract(MONTH FROM log_fecha) AS month
    FROM stock
    JOIN logs ON log_id=stock_log_id AND log_tipo=1
    WHERE stock_art_id=$art_id
    AND extract(YEAR FROM log_fecha)=extract(YEAR FROM now())
    ");
    //print_r($datos);
    //die();
    
      
    
    $settings = array(
    'back_colour'       => '#eee',    'stroke_colour'      => '#000',
    'back_stroke_width' => 0,         'back_stroke_colour' => '#eee',
    'axis_colour'       => '#333',    'axis_overlap'       => 2,
    'axis_font'         => 'Georgia', 'axis_font_size'     => 10,
    'grid_colour'       => '#666',    'label_colour'       => '#000',
    'pad_right'         => 20,        'pad_left'           => 20,
    'link_base'         => '/',       'link_target'        => '_top',
    'fill_under'        => array(true, false),
    'marker_size'       => 3,
    'marker_type'       => array('circle', 'square'),
    'marker_colour'     => array('blue', 'red')
    );
    
    
    
    $values = array(
    array('Dough' => 30, 'Ray' => 50, 'Me' => 40, 'So' => 25, 'Far' => 45, 'Lard' => 35)
    //array('Dough' => 20, 'Ray' => 30, 'Me' => 20, 'So' => 15, 'Far' => 25, 'Lard' => 35,'Tea' => 45)
    );
    
     
    $colours = array(array('red', 'white'),array('blue', 'white'));
    
    
    //$colours = array(array('', ''), array('', ''));
    //$links = array('Dough' => 'jpegsaver.php', 'Ray' => 'crcdropper.php','Me' => 'svggraph.php');
    print("<br/>");
    print("<table>");
        print("<tr>");
            print("<td style='text-align:left;font-size:12px;'>Codigo Articulos: </td>");
            print("<td style='text-align:left;font-size:12px;'><b>".$art['art_codigo']."</b></td>");
        print("</tr>");
        print("<tr>");
            print("<td style='text-align:left;font-size:12px;'>Glosa Articulo: </td>");
            print("<td style='text-align:left;font-size:12px;'><b>".$art['art_glosa']."</b></td>");
        print("</tr>");
    print("</table>");
    print("<br/>");
    print("<div class='sub-content2'>");
    $graph = new SVGGraph(600, 500, $settings);
        
    $graph->colours = $colours;
    
    $graph->Values($values);
    
    
    $graph->Links($links);
    
    $graph->Render('LineGraph');
    
    print("</div>");
?>