<script>
    tipoedita=0;
    nodoactual=null;
    textoactual='';
    checkboxes=new Array();
    cargar_listado = function()
    {
        $j('#arbol').html('<center><table><tr><td><img src=imagenes/ajax-loader2.gif></td></tr></table></center>');
        var myAjax = new Ajax.Updater('arbol','administracion/centros_responsabilidad/centros_listado.php',
        {
            method: 'get',
            onComplete: function()
            {
                objetos = $('arbol').getElementsByTagName('input');
                checkboxes=new Array();
                var cnt=0;
                for(i=0;i<objetos.length;i++)
                {
                    if(objetos[i].type=='checkbox')
                    {
                        checkboxes[cnt]=objetos[i];
                        cnt++;
                    }
                }
            }
      });
    }
  
    guardar_pildora = function(nodo)
    {
        checkbox = $('medica_'+nodo);
        cargador = $('cargam_'+nodo);
        deshabilitar_checks();
        if(checkbox.checked)
            checkear='1';
        else
            checkear='0';
        checkbox.style.display='none';
        cargador.style.display='';
        var myAjax = new Ajax.Request('administracion/centros_responsabilidad/cambiar_medica.php',
        {
            method: 'get',
            parameters: 'nodo='+nodo+'&estado='+checkear,
            onComplete: function() {
                cargar_listado();
            }
        });
    }

    guardar_gasto = function(nodo)
    {
        checkbox = $('gasto_'+nodo);
        cargador = $('cargag_'+nodo);
        deshabilitar_checks();
        if(checkbox.checked)
            checkear='1';
        else
            checkear='0';

        checkbox.style.display='none';
        cargador.style.display='';
        var myAjax = new Ajax.Request('administracion/centros_responsabilidad/cambiar_gasto.php',
        {
            method: 'get',
            parameters: 'nodo='+nodo+'&estado='+checkear,
            onComplete: function()
            {
                cargar_listado();
            }
        });
    }

    deshabilitar_checks=function()
    {
        for(i=0;i<checkboxes.length;i++)
        {
            checkboxes[i].disabled=true;
        }
    }


    editar_centro = function(nodo)
    {
        try { editar_cancela(); }
        catch(err) { }
        campo = document.getElementById('texto_'+nodo);
        nodoactual=nodo;
        textoactual=campo.innerHTML;
        tipoedita=0;
        campo.innerHTML='<input type=\"text\" id=\"costo_'+nodo+'\" name=\"costo_'+nodo+'\" value=\"'+textoactual+'\"  size=20> <img src=\"iconos/accept.png\" onClick=\"editar_guardar(\''+nodo+'\');\" alt=\"Guardar Cambios...\" title=\"Guardar Cambios...\"> <img src=\"iconos/cancel.png\" onClick=\"editar_cancela();\" alt=\"Cancelar...\" title=\"Cancelar...\">';
        campo_editar = document.getElementById('costo_'+nodo);
        campo_editar.select();
    }

    agregar_centro = function(nodo)
    {
        editar_cancela();
        campo = document.getElementById('ruta_'+nodo);
        columnas = campo.getElementsByTagName('td');
        cantidad_espacios = columnas[0].getElementsByTagName('img');
        espaciado = '<img src=\"iconos/blank.gif\">'.repeat(cantidad_espacios.length);
        new Insertion.After(campo,'<tr id=\"nuevo_'+nodo+'\"><td colspan=4>'+espaciado+'<img src=\"iconos/bullet_orange.png\"><input type=\"text\" id=\"costo_'+nodo+'\" name=\"costo_'+nodo+'\" size=20> <img src=\"iconos/accept.png\" onClick=\"editar_guardar(\''+nodo+'\');\" alt=\"Guardar Nuevo...\" title=\"Guardar Nuevo...\"> <img src=\"iconos/cancel.png\" onClick=\"editar_cancela();\" alt=\"Cancelar...\" title=\"Cancelar...\"></td></tr>');
        campo_editar = document.getElementById('costo_'+nodo);
        campo_editar.select();
        nodoactual=nodo;
	tipoedita=1;
    }

    borrar_centro = function(nodo,ramas)
    {
        campo = document.getElementById('texto_'+nodo);
        if(ramas>0)
        {
            confirmar = confirm(('&iquest;Desea eliminar la rama \"'+campo.innerHTML+'\" y toda(s) la(s) '+ramas+' rama(s) interior(es)?').unescapeHTML());
	}
        else
        {
            confirmar = confirm(('&iquest;Desea eliminar la rama \"'+campo.innerHTML+'\"?').unescapeHTML());
	}
        if(!confirmar) { return; }
        var myAjax2 = new Ajax.Request('sql.php',
        {
            method: 'get',
            parameters: 'accion=costo_eliminar&nodo='+nodo,
            onComplete: function (pedido_datos)
            {
                if(pedido_datos.responseText=='')
                {
                    cargar_listado();
                }
                else
                {
                    alert('ERROR: '+pedido_datos.responseText.unescapeHTML());
		}
            }
	});                      
    }

    editar_cancela = function()
    {
        if(nodoactual==null)
            return;

	if(tipoedita==0)
        {
            campo = document.getElementById('texto_'+nodoactual);
            campo.innerHTML=textoactual;
	}
        else
        {
            campo = document.getElementById('nuevo_'+nodoactual);
            campo.remove();
	}
	nodoactual=null;
    }
	
    editar_guardar = function(nodo)
    {
        if(tipoedita==0)
        {
            campo = document.getElementById('texto_'+nodo);
            campo_editar = document.getElementById('costo_'+nodo);
            var myAjax2 = new Ajax.Request('sql.php',
            {
                method: 'get',
		parameters: 'accion=costo_editar&nodo='+nodo+'&nuevo='+serializar(campo_editar),
		onComplete: function (pedido_datos)
                {
                    if(pedido_datos.responseText=='')
                    {
                        nodoactual=null;
			campo.innerHTML=campo_editar.value;
                    }
                    else
                    {
                        alert('ERROR: '+pedido_datos.responseText.unescapeHTML());
                    }
		}
            });
        }
        else
        {
            campo = document.getElementById('nuevo_'+nodo);
            campo_editar = document.getElementById('costo_'+nodo);
            var myAjax2 = new Ajax.Request('sql.php',
            {
                method: 'get',
                parameters: 'accion=costo_nuevo&nodo='+nodo+'&nuevo='+serializar(campo_editar),
                onComplete: function (pedido_datos)
                {
                    if(pedido_datos.responseText=='')
                    {
                        nodoactual=null;
			cargar_listado();
                    }
                    else
                    {
                        alert('ERROR: '+pedido_datos.responseText.unescapeHTML());
                    }
		}
            });
        }
    }
    //--------------------------------------------------------------------------
    guardar_alias= function(nodo)
    {
        campo_winsig = document.getElementById('alias_'+nodo);
        //campo_editar = document.getElementById('costo_'+nodo);
        var myAjax2 = new Ajax.Request('sql.php',
        {
            method: 'get',
            parameters: 'accion=costo_winsig&nodo='+nodo+'&campo_winsig='+serializar(campo_winsig),
            onComplete: function (pedido_datos)
            {
                if(pedido_datos.responseText=='')
                {
                    nodoactual=null;
                    cargar_listado();
                }
                else
                {
                    alert('ERROR: '+pedido_datos.responseText.unescapeHTML());
                }
            }
        });
    }
    //--------------------------------------------------------------------------
</script>
<center>
    <table>
        <tr>
            <td>
                <div class='sub-content' style='width: 700px;'>
                    <div class='sub-content'>
                        <img src='iconos/group.png'> <b>Centros de Responsabilidad / Costos</b>
                    </div>
                    <div class='sub-content3' id='arbol' style='height: 450px; overflow: auto;'>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</center>
<script>
    cargar_listado();
</script>