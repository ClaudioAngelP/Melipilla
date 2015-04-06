<?php 

  require_once('../../conectar_db.php');
  
  $bodegashtml = desplegar_opciones("bodega", 
                  "bod_id, bod_glosa",'1','bod_id IN ('._cav(24),')',
                  'ORDER BY bod_glosa'); 


?>

<script>

    var articulos = new Array();
    var mutex=false;

    seleccionar_preparacion = function (art) {
    
      $('art_id_prep').value=art[0];
      $('codigo_prep').value=art[1];
      $('nombre_prep').innerHTML=art[2];
      
      $('cantidad').select(); $('cantidad').focus();
      
	}

    seleccionar_articulo = function (art) {
    
      $('art_nombre').innerHTML=art[2];
      $('art_stock').innerHTML=art[4];
      
      // Si el articulo ya fue ingresado se retira.
      for(var i=0;i<articulos.length;i++) 
        if(articulos[i].id==art[6]) return;

      
      num = articulos.length;
      articulos[num] = new Object();
      
      articulos[num].id=art[6];
      articulos[num].codigo=art[0];
      articulos[num].glosa=art[2];
      articulos[num].clasificacion=art[3];
      articulos[num].lotes=false;
      
      cargar_lotes(num);
      
      redibujar();

		}
		
		cargar_lotes = function(num) {
    
      var myAjax = new Ajax.Request(
          'abastecimiento/preparaciones/cargar_lotes.php',
          {
            method: 'get',
            parameters: 'art_id='+articulos[num].id+'&'+
                        $('bodega_id').serialize(),
            onComplete: function(resp) {
            
              val = resp.responseText.evalJSON(true);
          
              if(val)
                articulos[num].lotes = val;
              else
                articulos[num].lotes = true;

              try {
                redibujar();
              } catch(err) {
                console.error(err);
              }
            }
                      
          });
    
    }
    
    redibujar = function() {
    
      html='<table style="width:100%;"><tr class="tabla_header">';
      html+='<td>C&oacute;digo Int.</td><td>Glosa</td>';
      html+='<td>Lote</td><td>Cant. Actual</td><td colspan=2>Cant. Utilizada</td>';
      html+='<td colspan=2>Acciones</td></tr>';
    
      for(var i=0;i<articulos.length;i++) {
      
        temp_html='';
        temp_html2='';

        if((i%2)==0) clase='tabla_fila'; else clase='tabla_fila2';
        
        if(articulos[i].lotes) {

          lotes=articulos[i].lotes;
                  
          if((lotes[0][2])!=null) {
          for(var n=0;n<lotes.length;n++) {
          
            var nom='art_'+i+'_lote_'+n;
                        
            if(n>0) { 
              temp_html2+='<tr class="'+clase+'">';
              
              if(lotes[n].length<4) {
                temp_html2+='<td style="text-align:center;">';
                temp_html2+=lotes[n][2]+'</td><td style="text-align:right;">';
              } else {
                temp_html2+='<td style="text-align:center;">';
                temp_html2+=''+lotes[n][2]+'';
                temp_html2+='</td><td style="text-align:right;">';
              
              }
              
              temp_html2+=lotes[n][0]+'</td>';
              temp_html2+='<td style="text-align:center;">';
              temp_html2+='<input type="text" id="'+nom+'" ';
              temp_html2+='name="'+nom+'" size=10 ';
              temp_html2+='value="'+lotes[n][1]+'" ';
              temp_html2+='onKeyUp="guarda_val('+i+','+n+');" ';
              temp_html2+='style="text-align:right;"></td>';
              temp_html2+='<td style="text-align:center;">';
              temp_html2+='<img src="iconos/cross.png" style="cursor:pointer;"';
              temp_html2+=' onClick="remover_lote('+i+', '+n+');">';
              temp_html2+='</td>';
              temp_html2+='</tr>';  
              
            } else {
              
              if(lotes[n].length<4) {
                temp_html+='<td style="text-align:center;">';
                temp_html+=lotes[n][2]+'</td><td style="text-align:right;">';
              } else {
                temp_html+='<td style="text-align:center;">';
                temp_html+=''+lotes[n][2]+'';
                temp_html+='</td><td style="text-align:right;">';
              
              }
              
              temp_html+=lotes[n][0]+'</td>';
              temp_html+='<td style="text-align:center;">';
              temp_html+='<input type="text" id="'+nom+'" ';
              temp_html+='name="'+nom+'" size=10 ';
              temp_html+='value="'+lotes[n][1]+'" ';
              temp_html+='onKeyUp="guarda_val('+i+','+n+');" ';
              temp_html+='style="text-align:right;"></td>';
              temp_html+='<td style="text-align:center;">';
              temp_html+='<img src="iconos/cross.png" style="cursor:pointer;" ';
              temp_html+='onClick="remover_lote('+i+', '+n+');">';
              temp_html+='</td>';
              
            }
            
            }
          
            rowspan=lotes.length;
          
          } else {
            
              var nom='art_'+i+'_lote_0';
            
              temp_html+='<td style="text-align:center;">';
              temp_html+='No Perecible</td><td style="text-align:right;">';
              temp_html+=lotes[0][0]+'</td>';
              temp_html+='<td style="text-align:center;">';
              temp_html+='<input type="text" id="'+nom+'" ';
              temp_html+='name="'+nom+'" size=10 ';
              temp_html+='value="'+lotes[0][1]+'" ';
              temp_html+='onKeyUp="guarda_val('+i+', 0);" ';
              temp_html+='style="text-align:right;"></td>';
              temp_html+='<td style="text-align:center;">';
              temp_html+='<img src="iconos/cross.png" style="cursor:pointer;" ';
              temp_html+='onClick="remover_lote('+i+', 0);">';
              temp_html+='</td>';
          
              rowspan=1;    
            
          }
          
          
        } else {
        
          temp_html+='<td colspan=4><img src="imagenes/ajax-loader1.gif">';
          temp_html+=' Cargando Lotes... </td>';
        
          rowspan=1;
          agrega_lotes=false;
        
        }
        
        
        html+='<tr class="'+clase+'">';
        html+='<td rowspan='+rowspan+' style="text-align:right;">';
        html+=articulos[i].codigo+'</td>';
        html+='<td rowspan='+rowspan+'>'+articulos[i].glosa+'</td>';
        
        html+=temp_html;

		  agrega_lotes=(articulos[i].lotes[0][3]=='1');
        
        if(agrega_lotes) {
          html+='<td rowspan='+rowspan+'><center>';
          html+='<img src="iconos/add.png" ';
          html+='style="cursor:pointer;" onClick="agregar('+i+');">';
          html+='</center></td>';
        } else {
          html+='<td rowspan='+rowspan+'><center>';
          html+='<img src="iconos/stop.png" title="No Perecible"';
          html+=' alt="No Perecible">';
          html+='</center></td>';
        
        }
        
        html+='<td rowspan='+rowspan+'><center>';
        html+='<img src="iconos/delete.png" ';
        html+='style="cursor:pointer;" onClick="remover('+i+');">';
        html+='</center></td>';
        html+='</tr>';
        
        html+=temp_html2;
      
      }
      
      html+='</table>';
      
      $('seleccion').innerHTML=html;
    
    }
		
	guarda_val = function(art, lote) {
    
    	if(typeof(articulos[art].lotes)=='boolean') {
    		articulos[art].lotes=[];
    		articulos[art].lotes[lote]=[0,0];
    	}

      articulos[art].lotes[lote][1] = $('art_'+art+'_lote_'+lote).value;
    
    }
    
    guarda_fec = function(art, lote, val) {
    
      articulos[art].lotes[lote][2]=val;
    
    }
    
    agregar = function (art) {
    
      if(typeof(articulos[art].lotes)=='boolean')
        articulos[art].lotes=new Array();
        
      var num = articulos[art].lotes.length;
      
      articulos[art].lotes[num] = new Array();
      articulos[art].lotes[num][0]='0.00';
      articulos[art].lotes[num][1]='0.00';
      articulos[art].lotes[num][2]='';
      articulos[art].lotes[num][3]='1';
      
      redibujar();
    
    }
    
    remover_lote = function(art, lote) {
    
      if(articulos[art].lotes[lote].length<4) {
        articulos[art].lotes[lote][1]='0.00';
      } else {
        articulos[art].lotes=
          articulos[art].lotes.without(articulos[art].lotes[lote]);
        if(articulos[art].lotes.length==0)
          articulos[art].lotes=true;
      }
      
      redibujar();
    
    }
    
    remover = function (art) {
    
      articulos = articulos.without(articulos[art]);
      redibujar();
    
    }
    
    limpiar_formulario = function() {
    
      articulos = new Array();
      redibujar();
    
    }
    
    limpiar_todo = function() {
    
      limpiar_formulario();
    
    }
    
    ingresar_ajuste = function () {
    
      // Comprobaciones
      
      if(mutex) return;

      if($('art_id_prep').value*1==0) {
        alert('No ha seleccionado c&oacute;digo de preparaci&oacute;n.'.unescapeHTML());
        return;
      }

      if($('cantidad').value*1==0) {
        alert('No ha especificado la cantidad de la preparaci&oacute;n.'.unescapeHTML());
        return;
      }
      
      if(articulos.length==0) {
        alert('No ha seleccionado articulos.');
        return;
      }
      
      var mods=0;
      
      for(var i=0;i<articulos.length;i++) {
      
        for(var n=0;n<articulos[i].lotes.length;n++) {
          
          if(articulos[i].lotes[n].length<4) {
             if(articulos[i].lotes[n][1]<0) {
                alert('No se pueden especificar lotes negativos.');
                $('art_'+i+'_lote_'+n).select();
                $('art_'+i+'_lote_'+n).focus();
                return;
             } 
          
					console.log('es '+articulos[i].lotes[n][0]+'!='+articulos[i].lotes[n][1]+' !!!');          
          
             if(articulos[i].lotes[n][0]!=articulos[i].lotes[n][1]) mods++; 
          } else {
            mods++;
          }
          
        }
      
      }
      
      if(mods==0) {
        alert('No hay modificaciones especificadas en la lista.');
        return;
      }
      
      mutex=true;
      
      params=$('bodega_id').serialize()+'&'+$('comentarios').serialize()+'&';
      params+='arts='+encodeURIComponent(articulos.toJSON());
      params+='&'+$('tipo_mov').serialize();
      
      alert('MODULO DE PRUEBAS - AJUSTE NO SERÁ REALIZADO.');
      
      return;
      
      var myAjax=new Ajax.Request(
      'abastecimiento/ajustar_stock/sql.php',
      {
        method: 'post',
        parameters: params,
        onComplete: function(resp) {
        
          op = resp.responseText.evalJSON(true);
          
          if(op)
            visualizador_documentos('Ajuste de Stock', 'log_id='+op);
        
          limpiar_todo();
          mutex=false;
        
        }
      }
      );
        
    
    }


	autocompletar_preparaciones = new AutoComplete(
      'codigo_prep', 
      'abastecimiento/preparaciones/autocompletar_preparaciones.php',
      function() {
        if($('codigo_prep').value.length<3) return false;
      
        return {
          method: 'get',
          parameters: $('codigo_prep').serialize()+'&'+
                      $('bodega_id').serialize()
        }
      }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_preparacion);


		
	autocompletar_medicamentos = new AutoComplete(
      'codigo', 
      'autocompletar_sql.php',
      function() {
        if($('codigo').value.length<3) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=buscar_arts_stock&'+$('codigo').serialize()+'&'+
                      $('bodega_id').serialize()
        }
      }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_articulo);


      redibujar();
      
      alert('IMPORTANTE: NO UTILIZAR MODULO EN ETAPA DE PRUEBAS.');

</script>


<input type='hidden' id='art_id_prep' name='art_id_prep' value='0' />

<center>
<div class="sub-content" style="width:750px;">
<div class="sub-content"><img src="iconos/pill.png"> 
<b>Generaci&oacute;n de Preparaciones</b></div>
<div class="sub-content">
<table>
<tr>
<td style="text-align:right;">
Ubicaci&oacute;n:
</td>
<td>
<select id="bodega_id" name="bodega_id" onChange="limpiar_formulario();">
<?php echo $bodegashtml; ?>
</select>
</td>
</tr>
<tr>
<td style="text-align:right;">
Tipo:
</td>
<td>
<select id="tipo_mov" name="tipo_mov">
<option value=0 SELECTED>Preparaci&oacute;n de Gal&eacute;nica</option>
<option value=1>Preparaci&oacute;n de Receta Magistral</option>
<option value=2>Preparaci&oacute;n de NPT</option>
</select>
</td>
</tr>



<tr>
<td style="text-align:right;">
Preparaci&oacute;n:
</td>
<td>
  <input type='text' id='codigo_prep' name='codigo_prep' size=15>
  <span id='nombre_prep'>(Seleccione C&oacute;digo de Preparaci&oacute;n...)</span>
</td>
</tr>

<tr>
<td style="text-align:right;">
Cantidad/Duraci&oacute;n:
</td>
<td>
<input type='text' id='cantidad' name='cantidad' size=10 style='text-align:right;' />
<select id="duracion" name="duracion">
<option value=0 SELECTED>3 meses.</option>
<option value=1>6 meses.</option>
</select>
</td>
</tr>


<tr>
<td style="text-align:right;" valign="top">Comentarios:</td>
<td>
<textarea id="comentarios" name="comentarios" rows=2 cols=60></textarea>
</td>
</tr>
</table>
</div>

<center>
  
  <div class="sub-content">
  
  <center>
  <table><tr><td>
  <input type='text' id='codigo' name='codigo' size=15>
  </td><td style="width:300px;" id="art_nombre">
  (Seleccione Art&iacute;culos...)
  </td><td style="width:150px;text-align:right;" id="art_stock">
  0.-
  </td></tr></table>
  </center>
  
  </div>
  
</center>
	
<div class="sub-content2" style="height: 220px; overflow:auto;" id='seleccion'>

</div>

<center>

<table><tr><td>

    <div class='boton'>
		<table><tr><td>
		<img src='iconos/database_edit.png'>
		</td><td>
		<a href='#' onClick='ingresar_ajuste();'> Realizar Preparaci&oacute;n...</a>
		</td></tr></table>
		</div>

	</td><td>

    <div class='boton'>
		<table><tr><td>
		<img src='iconos/database.png'>
		</td><td>
		<a href='#' onClick='limpiar_todo();'> Limpiar Formulario...</a>
		</td></tr></table>
		</div>
	
</td></tr></table>

</center>
</div>



</center>