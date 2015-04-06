<?php 
    require_once('../../conectar_db.php');
?>
<script>
    abrir_articulo = function(d, obj)
    {
        try {
                //console.log(d);
                var obj_name=$(obj).name.split('_');
                var contador=obj_name[2]*1;
                if($('art_id_'+contador).value!=d[5])
                    $('guardar_'+contador).show();
		
		$('art_id_'+contador).value=d[5];
		$('codigo_art_'+contador).value=d[0];
		$('glosa_'+contador).innerHTML=d[2];
        }
        catch(err)
        {
            alert(err);
            alert(d[0]);
	}
    }


    actualizar_lista=function(pagina)
    {
        var tipo_inf=$('filtro').value;
        
        if((tipo_inf*1)==1)
            $('btn_todo').show();
        else
            $('btn_todo').hide();
            
            
        $('lista_codigos').innerHTML='<br /><br /><img src="imagenes/ajax-loader3.gif"><br />Cargando...';
        var myAjax=new Ajax.Updater('lista_codigos','abastecimiento/codificacion_masiva/listar_codigos.php',
        {
            method: 'post',
            parameters: $('filtro').serialize()+'&pagina='+pagina,
            evalScripts: true
	});
    }

    modificar_glosa=function(i)
    {
        if(i!=undefined)
        {
            params='glosa='+encodeURIComponent(glosas[i].orserv_glosa)+'&art_id='+$('art_id_'+i).value*1;
            if(!confirm("Se van a modificar --- "+glosas[i].cuenta+" Ordenes de Compra ---, no hay opciones para deshacer. &iquest;Est&aacute; seguro?".unescapeHTML()))
                return;
	
            var myAjax=new Ajax.Request('abastecimiento/codificacion_masiva/sql.php',
            {
                method:'post',
                parameters: params,
                onComplete:function(d)
                {
                    alert(d.responseText);
                    actualizar_lista(0);
                }
            }); 
        }
        else
        {
            var cant=($('cant_articulos').value*1);
            articulos=new Array();
            conta=0;
            for(var x=0;x<cant;x++)
            {
                if($('art_id_'+x).value!='')
                {
                    articulos[conta]=new Array();
                    articulos[conta][0]=$('art_id_'+x).value;
                    articulos[conta][1]=$('codigo_art_'+x).value;
                    articulos[conta][2]=glosas[x].orserv_glosa
                    conta++;
                }
            }
            if((conta*1)>0)
            {
                if(!confirm(("Se asignar&aacute;n --- "+conta+" --- asignaciones de c\u00f3digos ---, no hay opciones para deshacer. &iquest;Est&aacute; seguro?").unescapeHTML()))
                {
                    return;
                }
                else
                {
                    params='art_id=0&articulos='+encodeURIComponent(articulos.toJSON());
                    var myAjax=new Ajax.Request('abastecimiento/codificacion_masiva/sql.php',
                    {
                        method:'post',
                        parameters: params,
                        onComplete:function(d)
                        {
                            //alert(d.responseText);
                            actualizar_lista(0);
                        }
                    }); 
                }
           }
            else
            {
                alert("No se han encontrado asignaciones de articulos");
                return;
            }
        }
    }
    
    limpiar=function(i)
    {
        $('art_id_'+i).value='';
        $('codigo_art_'+i).value='';
        $('glosa_'+i).innerHTML='';
        $('guardar_'+i).hide();
    }
    /*
    eliminar_asignacion=function(artn_id)
    {
        if(!confirm(("Esta seguro de eliminar la Asignaci&oacute;n.?").unescapeHTML()))
        {
            return;
        }
        var myAjax=new Ajax.Request('abastecimiento/codificacion_masiva/sql.php',
        {
            method:'post',
            parameters:'artn_id='+artn_id+'&eliminar=1',
            onComplete:function(r)
            {
                actualizar_lista(0);
                //window.location.reload();
            }
        });
    }
    */
</script>
<center>
    <div class='sub-content' style='width:850px;'>
        <input type="hidden" id="cant_articulos" name="cant_articulos" value="0"/>
        <div class='sub-content'>
            <table>
                <tr>
                    <td>
                        <img src='iconos/package.png' />
                        <b>Codificaci&oacute;n Masiva de Ordenes de Compra</b>
                    </td>
                    <td>
                        <select id='filtro' name='filtro' onChange='actualizar_lista(0);'>
                            <option value='1'>Ver Pendientes</option>
                            <option value='2'>Ver Asignadas</option>
                        </select>
                    </td>
                    <td>
                        <input type='button' style='display:block;' id='btn_todo' value='Asignar todos' onClick='modificar_glosa();' />
                    </td>
                </tr>
            </table>
        </div>
        <div class='sub-content2' style='height:400px;overflow:auto;' id='lista_codigos'>
        </div>
    </div>
</center>
<script>
    actualizar_lista(0);
</script>