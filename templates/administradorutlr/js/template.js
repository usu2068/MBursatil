/**
 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       3.2
 *
*/

function gEnt(id_admin){
	
	var error = 0;
	
	if(document.getElementById('nombre_ent').value==""){
		
		jQuery.ajax({
			type: 'POST',
			dataType :  'html' , 
			success: jQuery('#mjs_ent').html('<div class="alert alert-warning" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> Es necesario el nombre de la entidad para la creación dentro del sistema.</div>')
		});
		
		error ++;
	}
	
	if(document.getElementById('nit_ent').value==""){
		
		jQuery.ajax({
			type: 'POST',
			dataType :  'html' , 
			success: jQuery('#mjs_ent').html('<div class="alert alert-warning" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> Es necesario el nit de la entidad para su creación dentro del sistema.</div>')
		});
		
		error ++;
	}
	
	if(document.getElementById('logo_ent').value==""){
		
		jQuery.ajax({
			type: 'POST',
			dataType :  'html' , 
			success: jQuery('#mjs_ent').html('<div class="alert alert-warning" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> Es necesario el logo de la entidad para su creación dentro del sistema.</div>')
		});
		
		error ++;
	}
	
	if(error == 0){
		
		var valores =	"id_admin=" + id_admin +
						"&nombre=" + document.getElementById('nombre_ent').value  +
						"&nit=" + document.getElementById('nit_ent').value +
						"&image=" + document.getElementById('logo_ent').value;
		
		jQuery.ajax({
			url:'/utlr/templates/administradorutlr/html/guarda_ent.php',
			type: 'POST',
			dataType :  'html' , 
			data: valores,
			success: function( data ){
				jQuery('#mjs_ent').html(data);
			}
		});
	}
}

function edita(id){
	
	var error = 0;
	var id_inp_nom =  'nombre_edi_' + id;
	var id_inp_nit =  'nit_edi_' + id;
	var id_inp_log =  'logo_edi_' + id;
	
	if(document.getElementById(id_inp_nom).value==""){
		
		jQuery.ajax({
			type: 'POST',
			dataType :  'html',
			success: jQuery('#mjs_edi').html('<div class="alert alert-warning" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> El Nombre de la entidad no es valido.</div>')
		});
		
		error ++;
	}
	
	if(document.getElementById(id_inp_nit).value==""){
		
		jQuery.ajax({
			type: 'POST',
			dataType :  'html' , 
			success: jQuery('#mjs_edi').html('<div class="alert alert-warning" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> El nit ingresado no es valido.</div>')
		});
		
		error ++;
	}
	
	if(error == 0){
		var valores =	"id=" + id + 
						"&tipo=" + 'E' +
						"&nombre=" + document.getElementById(id_inp_nom).value + 
						"&nit=" + document.getElementById(id_inp_nit).value +
						"&image=" + document.getElementById(id_inp_log).value;
		
		jQuery.ajax({
			url:'/utlr/templates/administradorutlr/html/edicion.php',
			type: 'POST',
			dataType :  'html' , 
			data: valores,
			success: function( data ){
				jQuery('#mjs_edi').html(data);
				location.reload(true);
			}
		});
	}
}

function eliminar(id, tipo){
	
	var valores =	"id=" + id + 
					"&tipo=" + tipo;
		
		jQuery.ajax({
			url:'/utlr/templates/administradorutlr/html/elimina.php',
			type: 'POST',
			dataType :  'html' ,
			data: valores,
			success: function( data ){
				location.reload(true);
			}
		});
}

function g_usu(id_admin, ids, tip){
	
	var error = 0;
	
	if(tip == 'Crear') var div_msj = '#mjs_usu_new';
	else if(tip == 'Editar') var div_msj = '#mjs_usu_edi_' + id_admin;
	
	for(var i = 0; i < ids.length; ++ i){
		if(document.getElementById(ids[i]).value == "" || document.getElementById(ids[i]).value == 0){
			jQuery.ajax({
				type: 'POST',
				dataType :  'html' ,
				success: jQuery(div_msj).html('<div class="alert alert-warning" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> Es necesario que llene los datos solicitados en el formulario para realizar un registro exitoso.</div>')
			});
			
			error ++;
		}
	}

	if(document.getElementById(ids[6]).value != document.getElementById(ids[7]).value){
		jQuery.ajax({
				type: 'POST',
				dataType :  'html' ,
				success: jQuery(div_msj).html('<div class="alert alert-warning" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> Las contraseñas no coinciden por favor verifique y vuelva a intentarlo.</div>')
			});
			
			error ++;
	}
	
	if(error == 0){
	
		var valores =	"id_admin=" + id_admin + 
						"&entidad=" + document.getElementById(ids[0]).value + 
						"&nombre=" + document.getElementById(ids[1]).value +
						"&apellido=" + document.getElementById(ids[2]).value +
						"&cedula=" + document.getElementById(ids[3]).value +
						"&email=" + document.getElementById(ids[4]).value + 
						"&usuario=" + document.getElementById(ids[5]).value +
						"&pass=" + document.getElementById(ids[6]).value;
		if(tip == 'Crear'){	
			
			jQuery.ajax({
				url:'/utlr/templates/administradorutlr/html/guarda_usu.php',
				type: 'POST',
				dataType :  'html' ,
				data: valores,
				success: function( data ){
					jQuery(div_msj).html(data);
					location.reload(true);
				}
			});
			
		}else if(tip == 'Editar'){
				
				var valores = valores + "&tipo=" + 'U';
				jQuery.ajax({
					url:'/utlr/templates/administradorutlr/html/edicion.php',
					type: 'POST',
					dataType :  'html' ,
					data: valores,
					success: function( data ){
						jQuery(div_msj).html(data);
						location.reload(true);
				}
			});
			
		}
	}
}

//Creación de fondos o tipos
function new_fon(tip, id_ent){
	
	var error = 0;
	var valores;
	
	if(tip == 0){//Creacion Tipos de fondos
		
		div_msj = '#msj_newtfon';
		
		if(document.getElementById('new_fon').value==""){
			
			jQuery.ajax({
				type: 'POST',
				dataType :  'html',
				success: jQuery(div_msj).html('<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> El nombre del tipo de fondo no es valido.</div>')
			});
			
			error ++;
		}
		
		valores = 'nom_fon=' + document.getElementById('new_fon').value + '&tip=' + tip;
		
	}else if(tip == 1){//Creacion de fondos
		
		div_msj = '#msj_newfon';
		num_cod = document.getElementById('cod_fond').value;
		
		if(document.getElementById('nom_fond').value==""){
			
			jQuery.ajax({
				type: 'POST',
				dataType :  'html',
				success: jQuery(div_msj).html('<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> El nombre del fondo no es valido.</div>')
			});
			
			error ++;
		}
		
		if(document.getElementById('cod_fond').value=="" || num_cod.length < 4){
			
			jQuery.ajax({
				type: 'POST',
				dataType :  'html',
				success: jQuery(div_msj).html('<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> Codigo del fondo no es valido.</div>')
			});
			
			error ++;
		}
		
		if(document.getElementById('sel_tip').value=="0"){
			
			jQuery.ajax({
				type: 'POST',
				dataType :  'html',
				success: jQuery(div_msj).html('<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> Debe seleccionar un tipo de fondo para continuar.</div>')
			});
			
			error ++;
		}
		
		if(document.getElementById('sel_ent').value=="0"){
			
			jQuery.ajax({
				type: 'POST',
				dataType :  'html',
				success: jQuery(div_msj).html('<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> Debe seleccionar una entidad para continuar.</div>')
			});
			
			error ++;
		}
		valores = 'nom_fon=' + document.getElementById('nom_fond').value + '&sel_tip=' + document.getElementById('sel_tip').value + '&sel_ent=' + document.getElementById('sel_ent').value + '&tip=' + tip+ '&cod_fond=' + document.getElementById('cod_fond').value;
	}
	
	if(error == 0){//Envio datos a php por ajax
		
		jQuery(div_msj).html('<div><img src="/utlr/templates/img/loding_fmr.gif" /></div>');
		
		jQuery.ajax({
			url:'/utlr/templates/administradorutlr/html/new_fon.php' ,
			type: 'POST' ,
			dataType :  'html' ,
			data: valores ,
			success: function( data ){
			
				jQuery(div_msj).html(data);
				valores = 'id_ent=' + id_ent + '&tip=' + tip;
				
				if(tip == 0) var div = '#list_fond';
				else if(tip == 1) var div = '#fond';
				
				jQuery(div).html('<div><img src="/utlr/templates/img/loding_fmr.gif" /></div>');
				
				jQuery.ajax({
					url: '/utlr/templates/administradorutlr/html/actualiza_tfon.php',
					type: 'POST',
					dataType: 'html',
					data: valores ,
					success: function( data ){
						jQuery('#fond').html(data);
					}
				});
			}
		});
	}
}

function carga_tip_fon(tip, id_ent){ 
	
	//var id_ent = document.getElementById('sel_ent').value;
	var valores = 'id_ent=' + id_ent + '&tip=' + tip;
	
	if(tip == 0) var div = '#list_fond';
	else if(tip == 1) var div = '#fond';
	//alert(div);
	jQuery(div).html('<div><img src="/utlr/templates/img/loding_fmr.gif" /></div>');
	
	jQuery.ajax({
		url: '/utlr/templates/administradorutlr/html/actualiza_tfon.php',
		type: 'POST',
		dataType: 'html',
		data: valores ,
		success: function( data ){
			jQuery(div).html(data);
		}
	});
}

function elim_tip(id_tip){
	
	valores = 'id_tip=' + id_tip;
	
	jQuery.ajax({
		url:'/utlr/templates/administradorutlr/html/elim_fon.php',
		type: 'POST',
		dataType :  'html' ,
		data: valores,
		success: function( data ){
			location.reload(true);
		}
	});
	
}

function new_pro_pol(id_pap, parent, tip, pc){
	
	var error = 0;
	var tip = tip; //tipo de compocición el 1 corresponde a prohibiciones
	if(tip == 1){ var inp = "new_pro_txt"; var div = "#msj_newpro"; parent = 0;}
	else if(tip == 0){ var inp = "new_pol_txt"; var div = "#msj_newpol"; parent = document.getElementById('sel_comp').value;}

	if(document.getElementById(inp).value=="0"){
		
		jQuery.ajax({
			type: 'POST',
			dataType :  'html',
			success: jQuery('#msj_newpro').html('<div class="alert alert-warning" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> El Nombre de la Compocición no es valido.</div>')
		});
		
		error ++;
	}
	
//	if(parent != 0)
	
	
	var valores = "id_pap=" + id_pap + "&parent=" + parent + "&nombre_com=" + document.getElementById(inp).value + "&tipo_g=" + tip + "&pc=" + pc;
	//alert(valores);
	if(error == 0){
		//alert(valores);
		jQuery.ajax({
			url:'/utlr/templates/administradorutlr/html/new_pol_pro.php',
			type: 'POST',
			dataType :  'html' ,
			data: valores,
			success: function( data ){
				jQuery(div).html(data);
				//location.reload(true);
			}
		});
	}
}

function new_tit_pol(id_comp){

	var error = 0;
	var max = document.getElementById('new_tit_pol_max').value;
	var min = document.getElementById('new_tit_pol_min').value;
	var tip = 0; //tipo de compocición el 1 corresponde a prohibiciones
	
	if(max == "" || max < 0) max = 0;
	if(min == "" || min < 0) min = 0;
	
	if(document.getElementById('new_tit_pol_nom').value==""){
		
		jQuery.ajax({
			type: 'POST',
			dataType :  'html',
			success: jQuery('#msj_newtit_pol').html('<div class="alert alert-warning" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> El Nombre del Título no es valido.</div>')
		});
		
		error ++;
	}
	
	/*if(document.getElementById('new_tit_pol_cod').value==""){
		
		jQuery.ajax({
			type: 'POST',
			dataType :  'html',
			success: jQuery('#msj_newtit_pol').html('<div class="alert alert-warning" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> El Codigo del Título no es valido.</div>')
		});
		
		error ++;
	}
	
	var valores = "id_comp=" + id_comp + "&cod=" + document.getElementById('new_tit_pol_cod').value + "&tipo=" + tip + "&nom=" + document.getElementById('new_tit_pol_nom').value + "&min=" + min + "&max=" + max;*/
	
	var valores = "id_comp=" + id_comp + "&tipo=" + tip + "&nom=" + document.getElementById('new_tit_pol_nom').value + "&min=" + min + "&max=" + max;
	
	if(error == 0){
		//alert(valores);
		jQuery.ajax({
			url:'/utlr/templates/administradorutlr/html/new_tit.php',
			type: 'POST',
			dataType :  'html',
			data: valores,
			success: function( data ){
				jQuery('#msj_newtit_pol').html(data);
				//location.reload(true);
			}
		});
	}
}

function new_tit_pro(id_comp){
	
	var error = 0;
	var max = document.getElementById('new_tit_max').value;
	var min = document.getElementById('new_tit_min').value;
	var tip = 1; //tipo de compocición el 1 corresponde a prohibiciones
	
	if(max == "" || max < 0) max = 0;
	if(min == "" || min < 0) min = 0;
	
	var sel_tit = 'new_tit_nom' + id_comp;
	var msj_comp = '#msj_newtit_c' + id_comp;
	
	if(document.getElementById(sel_tit).value=="0"){
		
		jQuery.ajax({
			type: 'POST',
			dataType :  'html',
			success: jQuery(msj_comp).html('<div class="alert alert-warning" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> El Nombre del Título no es valido.</div>')
		});
		
		error ++;
	}
	
	
	var valores = "id_comp=" + id_comp + "&tipo=" + tip + "&nom=" + document.getElementById(sel_tit).value + "&min=" + min + "&max=" + max;
	
	if(error == 0){
		
		//alert(valores);
		jQuery.ajax({
			url:'/utlr/templates/administradorutlr/html/new_tit.php',
			type: 'POST',
			dataType :  'html',
			data: valores,
			success: function( data ){
				jQuery('#msj_newtit').html(data);
				//location.reload(true);
			}
		});
	}

}

function asig_tit(id_grptit, tip){
	
	var error = 0;
	var sel_tit = 'sel_' + id_grptit;
	var id_tit = document.getElementById(sel_tit).value;
	
	if(id_tit == "0"){		
		jQuery.ajax({
			type: 'POST',
			dataType :  'html',
			success: jQuery('#msj_asigtit').html('<div class="alert alert-warning" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> Debe seleccionar un titulo.</div>')
		});
		error ++;
	}
	
	if(error == 0){
		var valores = 'id_grptit=' + id_grptit + '&id_tit=' + id_tit + '&tip=' + tip;
		//alert(valores);
		jQuery.ajax({
			url:'/utlr/templates/administradorutlr/html/asig_tit.php',
			type: 'POST',
			dataType :  'html',
			data: valores,
			success: function( data ){
				jQuery('#msj_asigtit').html(data);
			}
		});		
	}
}

function cam_est_tit(id_tit, id_grptit, est, tip){
	
	var valores = 'id_grptit=' + id_grptit + '&id_tit=' + id_tit + '&est=' + est +'&tip=' + tip;
		
		jQuery.ajax({
			url:'/utlr/templates/administradorutlr/html/cam_est_tit.php',
			type: 'POST',
			dataType :  'html',
			data: valores,
			success: function( data ){
				jQuery('#msj_asigtit').html(data);
			}
		});
	
}

function new_grp(id_tit, tip, id_btn){
	
	var error = 0;
	
	if(tip == 0){ // cuando se crea y asigna un grupo
		
		var nom = document.getElementById('new_nom_grp').value;
		var max = document.getElementById('new_grp_max').value;
		var min = document.getElementById('new_grp_min').value;
		
		if(max == "") max = 0;
		if(min == "") min = 0;
		
		if(max > 100 || max < 0 || max < min){
		
			jQuery.ajax({
				type: 'POST',
				dataType :  'html',
				success: jQuery('#msj_newgrp').html('<div class="alert alert-warning" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> El porcentaje maximo no puede ser mayor al 100% ni menor que 0%, así como tampoco puede ser menor que el minimo.</div>')
			});
			error ++;
			
		}
		
		if(min > 100 || min < 0 || min > max){
		
			jQuery.ajax({
				type: 'POST',
				dataType :  'html',
				success: jQuery('#msj_newgrp').html('<div class="alert alert-warning" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> El porcentaje minimo no puede ser mayor al 100% ni menor que 0%, así como tampoco puede ser mayor que el maximo.</div>')
			});
			error ++;
			
		}		
		
		if(nom == ""){ 
		
			jQuery.ajax({
				type: 'POST',
				dataType :  'html',
				success: jQuery('#msj_newgrp').html('<div class="alert alert-warning" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> El Nombre del grupo no es valido.</div>')
			});
			
			error ++;
		}
		
		if(document.getElementById('che_gre').cheked == true) tip_grp = 'esp';
		else tip_grp = 'tot';
		
		if(error == 0){
			
			var valores = "id_tit=" + id_tit + "&nom=" + nom + "&max=" + max + "&min=" + min + "&tip=" + tip + "&tip_grp=" + tip_grp;
			
			//alert(valores);
			jQuery.ajax({
				url:'/utlr/templates/administradorutlr/html/new_asig_grp.php',
				type: 'POST',
				dataType :  'html',
				data: valores,
				success: function( data ){
					jQuery('#msj_newgrp').html(data);
					jQuery(id_btn).addClass('btn-success');
					jQuery(id_btn).popover('toggle');
					location.reload(true);
				}
			});
		}
		
	}else if(tip == 1){ // cuando se asigna grupo ya creado
	
		var grp = document.getElementById('sel_grp').value;
		
		if(grp == 0) 
		
			jQuery.ajax({
				type: 'POST',
				dataType :  'html',
				success: jQuery('#msj_newgrp').html('<div class="alert alert-warning" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> Debe Seleccionar uno de los grupos creados.</div>')
			});
			
		else{
			
			var valores = "id_tit=" + id_tit + "&grp=" + grp + "&tip=" + tip;
			//alert(valores);
			jQuery.ajax({
				url:'/utlr/templates/administradorutlr/html/new_asig_grp.php',
				type: 'POST',
				dataType :  'html',
				data: valores,
				success: function( data ){
					jQuery('#msj_newgrp').html(data);
					jQuery(id_btn).removeClass('btn-danger');
					jQuery(id_btn).addClass('btn-success');
					jQuery(id_btn).popover('hide');
				}
			});
		}
	}
	
}
	
function guard_para_pol(tip, ids_tit, ids_tot, id_pap, ids_grp_eco){
	
	var vacio = 0;
	var vacio_tot = 0;	
	var maxs = new Array();
	var mins = new Array();	
	var maxst = new Array();
	var minst = new Array();
	var ids_tit_int = ids_tit;
	var ids_tot_int = ids_tot;
	var max_grp_inv = new Array();
	var max_grp_dep = new Array();
	var max_grp_tot = new Array();
	
	var div_gua = '#gua'+tip;
	var div_guaII = '#guaII'+tip;
	
	for(var i=0; i < ids_tit.length; ++i){
		
		var inp_max = 'max_' + ids_tit[i];
		var inp_min = 'min_' + ids_tit[i];
		
		if(document.getElementById(inp_max).value == '' && document.getElementById(inp_min).value == ''){ 
			ids_tit[i]=0; 
			vacio ++;
			maxs[i] = document.getElementById(inp_max).value;
			mins[i] = document.getElementById(inp_min).value;
		
		}else if(document.getElementById(inp_max).value != '' && document.getElementById(inp_min).value != ''){
			maxs[i] = document.getElementById(inp_max).value;
			mins[i] = document.getElementById(inp_min).value;
			
		}else if(document.getElementById(inp_max).value == ''){
			maxs[i] = document.getElementById(inp_max).placeholder;
			mins[i] = document.getElementById(inp_min).value;
			
		}else{
			maxs[i] = document.getElementById(inp_max).value;
			mins[i] = document.getElementById(inp_min).placeholder;
		}
	}
	
	
	for(var j=0; j < ids_tot.length; ++j){
		
		var inp_max = 'maxt_' + ids_tot[j];
		var inp_min = 'mint_' + ids_tot[j];
		var error = 0;
		//alert(ids_tot);
		if(document.getElementById(inp_max).value == '' && document.getElementById(inp_min).value == '' ){ 
			ids_tot[j]=0; 
			vacio_tot ++; 
			
			maxst[j] = document.getElementById(inp_max).placeholder;
			minst[j] = document.getElementById(inp_min).placeholder;
			
		}else if(document.getElementById(inp_max).value != '' && document.getElementById(inp_min).value != '' ){
			maxst[j] = document.getElementById(inp_max).value;
			minst[j] = document.getElementById(inp_min).value;
		
		}else if(document.getElementById(inp_max).value == ''){
			maxst[j] = document.getElementById(inp_max).placeholder;
			minst[j] = document.getElementById(inp_min).value;
			
		}else if(document.getElementById(inp_min).value == ''){
			maxst[j] = document.getElementById(inp_max).value;
			minst[j] = document.getElementById(inp_min).placeholder;
		}
		
	}
	
	for(var k = 0; k < ids_grp_eco.length; ++k){
		
		var id_max_inv_grp = 'max_inv_grp' + id_pap + ids_grp_eco[k];
		var id_max_dep_grp = 'max_dep_grp' + id_pap + ids_grp_eco[k];
		var id_max_tot_grp = 'max_tot_grp' + id_pap + ids_grp_eco[k];
		
		if(document.getElementById(id_max_inv_grp).value != '') max_grp_inv[k] = document.getElementById(id_max_inv_grp).value;
		else max_grp_inv[k] = document.getElementById(id_max_inv_grp).placeholder;
		
		if(document.getElementById(id_max_dep_grp).value != '')max_grp_dep[k] = document.getElementById(id_max_dep_grp).value;
		else max_grp_dep[k] = document.getElementById(id_max_dep_grp).placeholder;
		
		if(document.getElementById(id_max_tot_grp).value != '') max_grp_tot[k] = document.getElementById(id_max_tot_grp).value;
		else max_grp_tot[k] = document.getElementById(id_max_tot_grp).placeholder;
		
	}
	
	//alert(max_grp_inv);
	
	var id_max_inv = 'max_inv' + id_pap;
	var id_min_inv = 'min_inv' + id_pap;
	var id_max_dep = 'max_dep' + id_pap;
	var id_min_dep = 'min_dep' + id_pap;
	var id_max_tota = 'max_tota' + id_pap;
	var id_min_tota = 'min_tota' + id_pap;
	var id_prompo = 'max_prompo' + id_pap;
	
	var max_inv = document.getElementById(id_max_inv).value;
	var min_inv = document.getElementById(id_min_inv).value;
	var max_dep = document.getElementById(id_max_dep).value;
	var min_dep = document.getElementById(id_min_dep).value;
	var max_tota = document.getElementById(id_max_tota).value;
	var min_tota = document.getElementById(id_min_tota).value;
	var prompo = document.getElementById(id_prompo).value;
	
	if( max_inv == '' ){ max_inv =  document.getElementById(id_max_inv).placeholder }
	if( min_inv == '' ){ min_inv =  document.getElementById(id_min_inv).placeholder }
	if( max_dep == '' ){ max_dep =  document.getElementById(id_max_dep).placeholder }
	if( min_dep == '' ){ min_dep =  document.getElementById(id_min_dep).placeholder }
	if( max_tota == '' ){ max_tota =  document.getElementById(id_max_tota).placeholder }
	if( min_tota == '' ){ min_tota =  document.getElementById(id_min_tota).placeholder }	
	if( prompo == '' ){ prompo =  document.getElementById(id_min_tota).placeholder }
	
	if(vacio >= ids_tit.length && vacio_tot >= ids_tot.length && max_inv == '' && max_dep == '' && min_inv == '' && min_dep == '' && max_tota == '' && min_tota == ''){
		
		jQuery.ajax({
			type: 'POST',
			dataType :  'html',
			success: jQuery('#gua'+tip).html('<div class="alert alert-info" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> No se realizo ningun cambio.</div>')
		});
		
		jQuery.ajax({
			type: 'POST',
			dataType :  'html',
			success: jQuery(div_guaII).html('<div class="alert alert-info" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> No se realizo ningun cambio.</div>')
		});
		
	}else{
		var tr_tot;
		var tr_tit;
		
		//alert('max=' + maxs + ';  min=' + mins);
		for(var k=0; k < ids_tit.length; ++k){
			
			if( parseInt(maxs[k]) < parseInt(mins[k]) ){
				
				tr_tit = '#tr_m_' + ids_tit[k];		
				
				jQuery.ajax({
					type: 'POST',
					dataType :  'html',
					success:
						jQuery(tr_tit).addClass('has-error')
				});
				error ++;
			}
		}
		
		for(var k=0; k < ids_tot.length; ++k){
			
			if( parseInt(maxst[k]) < parseInt(minst[k]) ){
				
				tr_tot = '#tr_mt_' + ids_tot[k];
				jQuery.ajax({
					type: 'POST',
					dataType :  'html',
					success: jQuery(tr_tot).addClass('has-error')
				});
				error ++;
			}
		}
		
/***********/
		if(error == 0){
			
			var valores = "ids_tit=" + JSON.stringify(ids_tit) + "&maxs=" + JSON.stringify(maxs) + "&mins=" + JSON.stringify(mins) + "&ids_tot=" + JSON.stringify(ids_tot) + "&maxst=" + JSON.stringify(maxst) + "&minst=" + JSON.stringify(minst) + '&tip=' + tip + '&max_inv=' + max_inv + '&min_inv=' + min_inv + '&max_dep=' + max_dep + '&min_dep=' + min_dep + '&max_tota='+ max_tota + '&min_tota=' + min_tota + '&id_fon=' + id_pap + '&prompo=' + prompo + '&ids_grp_eco=' + JSON.stringify(ids_grp_eco) + '&max_grp_inv=' + JSON.stringify(max_grp_inv) + '&max_grp_dep=' + JSON.stringify(max_grp_dep) + '&max_grp_tot=' + JSON.stringify(max_grp_tot);
		
			//alert(valores);
			jQuery.ajax({
				url:'/utlr/templates/administradorutlr/html/guarda_parametros.php',
				type: 'POST',
				dataType :  'html',
				data: valores,
				success: function( data ){
					jQuery(div_gua).html(data);
					
					for(var l=0; l<ids_tit_int.length; ++l){
						
						var inp_max = 'max_' + ids_tit_int[l];
						var inp_min = 'min_' + ids_tit_int[l];
						tr_tit = '#tr_m_' + ids_tit[l];
						
						if(ids_tit_int[l] != 0){
						
							document.getElementById(inp_max).value = '';
							document.getElementById(inp_min).value = '';
							
							document.getElementById(inp_max).placeholder = maxs[l] + '%';
							document.getElementById(inp_min).placeholder = mins[l] + '%';
							
							jQuery(tr_tit).removeClass("has-error");
							jQuery(tr_tit).addClass('has-success');
						}
					}
					
					
					for(var l=0; l<ids_tot_int.length; ++l){
						
						var inp_max = 'maxt_' + ids_tot_int[l];
						var inp_min = 'mint_' + ids_tot_int[l];
						tr_tot = '#tr_mt_' + ids_tot[l];
						
						if(ids_tot_int[l] != 0){
						
							document.getElementById(inp_max).value = '';
							document.getElementById(inp_min).value = '';
							
							document.getElementById(inp_max).placeholder = maxst[l] + '%';
							document.getElementById(inp_min).placeholder = minst[l] + '%';
							
							jQuery(tr_tot).removeClass("has-error");
							jQuery(tr_tot).addClass("has-success");
						}
					}
					
					if(document.getElementById(id_max_inv).value != ''){ 
						document.getElementById(id_max_inv).placeholder = max_inv + '%'; 
						document.getElementById(id_max_inv).value = '';
					}
					if(document.getElementById(id_min_inv).value != ''){ 
						document.getElementById(id_min_inv).placeholder = min_inv + '%'; 
						document.getElementById(id_min_inv).value = '';
					}
					if(document.getElementById(id_max_dep).value != ''){ 
						document.getElementById(id_max_dep).placeholder = max_dep + '%'; 
						document.getElementById(id_max_dep).value = '';
					}
					if(document.getElementById(id_min_dep).value != ''){ 
						document.getElementById(id_min_dep).placeholder = min_dep + '%'; 
						document.getElementById(id_min_dep).value = '';
					}
					if(document.getElementById(id_max_tota).value != ''){ 
						document.getElementById(id_max_tota).placeholder = max_tota + '%'; 
						document.getElementById(id_max_tota).value = '';
					}
					if(document.getElementById(id_min_tota).value != ''){ 
						document.getElementById(id_min_tota).placeholder = min_tota + '%'; 
						document.getElementById(id_min_tota).value = '';
						}
					
					for(var k = 0; k < ids_grp_eco.length; ++k){
						var id_max_inv_grp = 'max_inv_grp' + id_pap + ids_grp_eco[k];
						var id_max_dep_grp = 'max_dep_grp' + id_pap + ids_grp_eco[k];
						var id_max_tot_grp = 'max_tot_grp' + id_pap + ids_grp_eco[k];
						
						if(document.getElementById(id_max_inv_grp).value != ''){ 
							document.getElementById(id_max_inv_grp).placeholder = document.getElementById(id_max_inv_grp).value + ' %'; 
							document.getElementById(id_max_inv_grp).value = '';
						}
						if(document.getElementById(id_max_dep_grp).value != ''){ 
							document.getElementById(id_max_dep_grp).placeholder = document.getElementById(id_max_dep_grp).value + ' %'; 
							document.getElementById(id_max_dep_grp).value = '';
						}
						if(document.getElementById(id_max_tot_grp).value != ''){ 
							document.getElementById(id_max_tot_grp).placeholder = document.getElementById(id_max_tot_grp).value + ' %'; 
							document.getElementById(id_max_tot_grp).value = '';
						}
					}					
				}
			});

		}else{
			jQuery.ajax({
				type: 'POST',
				dataType :  'html',
				success: jQuery(div_gua).html('<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Precaución!</strong> El maximo subrrayado es menor que el minimo correspondiente.</div>')
			});

			jQuery.ajax({
				type: 'POST',
				dataType :  'html',
				success: jQuery(div_guaII).html('<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert"> &times;</button> <strong>Precaución!</strong> El maximo subrrallado es menor que el minimo correspondiente.</div>')
			});
		}
	}
}

function guard_para_proh(tip, ids_tit){

	var vacio = 0;
	var error = 0;
	var max = new Array();
	var min = new Array();
	
	for(var i = 0; i < ids_tit.length; ++i){

		var inp_min = 'min_pr_' + tip + ids_tit[i];
		var inp_max = 'max_pr_' + tip + ids_tit[i];
		
		if(document.getElementById(inp_min).value != '' || document.getElementById(inp_max).value != ''){
		
		//validacion minimo
		
			if(document.getElementById(inp_min).value == '') min[i] = parseInt(document.getElementById(inp_min).placeholder);		
			else min[i] = parseInt(document.getElementById(inp_min).value);
			
		//validacion maximo
		
			if(document.getElementById(inp_max).value == '') max[i] = parseInt(document.getElementById(inp_max).placeholder);
			else max[i] = parseInt(document.getElementById(inp_max).value);
			
			if(max[i] < min[i]){
			
				jQuery.ajax({
					type: 'POST',
					dataType :  'html',
					success: jQuery('#tr_pr_' + ids_tit[i]).addClass('has-error')
				});
				
				jQuery.ajax({
					type: 'POST',
					dataType :  'html',
					success: jQuery('#gua_pr').html('<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert"> &times;</button> <strong>Precaución!</strong> El maximo subrrallado es menor que el minimo correspondiente.</div>')
				});
				
				jQuery.ajax({
					type: 'POST',
					dataType :  'html',
					success: jQuery('#gua_pr_II').html('<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert"> &times;</button> <strong>Precaución!</strong> El maximo subrrallado es menor que el minimo correspondiente.</div>')
				});
				
				++error;
			}
	
	/* No hay cambios */
	
		}else{
			min[i] = 0;
			max[i] = 0;
			
			ids_tit[i] = 0;
			vacio ++;
		}
	}
	
	/*
		* CUANDO NO SE REALIZA NINGUN CAMBIO EN LOS PARAMETROS
	*/
	
	if(vacio >= ids_tit.length){
	
		jQuery.ajax({
			type: 'POST',
			dataType :  'html',
			success: jQuery('#gua_pr').html('<div class="alert alert-info" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> No se realizo ningun cambio.</div>')
		});
		
		jQuery.ajax({
			type: 'POST',
			dataType :  'html',
			success: jQuery('#gua_pr_II').html('<div class="alert alert-info" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> No se realizo ningun cambio.</div>')
		});
		
	}

	/*
		* Envio de los datos al php por post
	*/
	
	if(error == 0){
	
		var valores = "ids_tit=" + JSON.stringify(ids_tit) + "&maxs=" + JSON.stringify(max) + "&mins=" + JSON.stringify(min) + "&tip=" + tip;
		
		
		var inp_min;
		var inp_max;

		jQuery.ajax({
			url:'/utlr/templates/administradorutlr/html/guarda_parametros_proh.php',
			type: 'POST',
			dataType :  'html' , 
			data: valores,
			success: function( data ){
				jQuery('#gua_pr_II').html(data);
				
				for(var j = 0; j < ids_tit.length; ++ j){
			
					inp_min = 'min_pr_' + tip + ids_tit[j];
					inp_max = 'max_pr_' + tip + ids_tit[j];
					tr_tit = '#tr_pr_' + ids_tit[j];
				
					if( ids_tit[j] != 0){
					
						document.getElementById(inp_max).value = '';
						document.getElementById(inp_min).value = '';
						
						document.getElementById(inp_max).placeholder = max[j] + '%';
						document.getElementById(inp_min).placeholder = min[j] + '%';
						
						jQuery(tr_tit).removeClass("has-error");
						jQuery(tr_tit).addClass("has-success");
					}
				}
			}
		});
	}
}

/****************************************************
// EMISORES
****************************************************/

function guarda_nemis(fondo, tipo){

	var inp_emi = 'inp_emi_'+fondo;
	var max = document.getElementById(inp_emi).value;
	
	var ids_emis_selec = new Array();
	
	if (tipo == 1) var id_form = 'emis_inv'+fondo;
	if (tipo == 2) var id_form = 'emis_dep'+fondo;
	//alert (id_form);
	var check_emis = document.getElementById(id_form).checkbox;
	
	if(check_emis.length != undefined){
	
		for (var x=0; x < check_emis.length; x++) {
			if (check_emis[x].checked == true) {
				ids_emis_selec[x] = check_emis[x].value;
				//check_emis[x].value
			}
		}
		
	}/*else {
		
		if (tipo == 1) var id_check = 'check_inv'+fondo;
		if (tipo == 2) var id_check = 'check_dep'+fondo;
		
		alert(id_check);
		
		if(document.getElementById(id_check).checked == true) ids_emis_selec[0] = document.getElementById(id_check).value;
	}
	/*if (check_emis[x].checked == true) {
		ids_emis_selec[x] = check_emis[x].value;
	}*/
	
	valores = 'tipo='+tipo+'&fondo='+fondo+'&max_selec='+max+'&nits_emis='+JSON.stringify(ids_emis_selec);
	
	//alert(valores);
	jQuery.ajax({
		url:'/utlr/templates/administradorutlr/html/g_emis.php' ,
		type: 'POST' ,
		dataType :  'html' , 
		data: valores ,
		success: function( data ){
			jQuery('#div_msj_emi').html(data);
			location.reload(5);
		}
	});
	
	//alert(ids_emis_selec);
	/*for (var i = 0; i<=emis_id.length; i++){
	
		var id_check_emi = 'check_inv'+emis_id[i];
		alert(id_check_emi);
		if(document.getElementById(id_check_emi).cheked == true ){
			emis_selec[num] = emis_id[i];
			num ++;
		}
	}
	
	alert(emis_id);
	/*JSON.stringify();
	jQuery.ajax({
		url:'/utlr/templates/administradorutlr/html/.php',
		type: 'POST',
		dataType :  'html' , 
		data: valores,
		success: function( data ){}
	});*/
}

function guarda_emi(fondo, emis, inv_dep){
	
	var id_inp_emis = '#msj_inv' + fondo;
	var c_inv = 0;
	var c_dep = 0;
	
	var emis_sel_inv = new Array();
	var emis_sel_dep = new Array();
	//var emis = JSON.parse(emis);
	//alert('val_inv_' + emis.length + '_' + fondo);
	for(var i = 0; i < 1000; ++i){
		
		if( emis[i] != undefined){
			var id_inp_emi_inv = 'val_inv_' + emis[i] + '_' + fondo;
			var id_inp_emi_dep = 'val_dep_' + emis[i] + '_' + fondo;
			
		// EVALUA LOS INPUT QUE TIENEN CONTENIDO PARA INVERSIONES
			if(inv_dep == 1){
				if(document.getElementById(id_inp_emi_inv).value != ''){ c_inv = 1;	}
				if(document.getElementById(id_inp_emi_inv).value != null){ emis_sel_inv[i] = document.getElementById(id_inp_emi_inv).value; }
		
		// EVALUA LOS INPUT QUE TIENEN CONTENIDO PARA DEPOSITOS
			}else if(inv_dep == 2){
				if(document.getElementById(id_inp_emi_dep).value != ''){ c_dep = 1; }
				if(document.getElementById(id_inp_emi_dep).value != null){ emis_sel_dep[i] = document.getElementById(id_inp_emi_dep).value; }
			}
			//alert(emis_sel_inv[i]);
		}

	}
	
	var id_emis = JSON.stringify(emis);
	var sel_inv = JSON.stringify(emis_sel_inv);
	var sel_dep = JSON.stringify(emis_sel_dep);
	//alert(emis_sel_inv);
	if(c_dep == 0 && c_inv == 0){
		jQuery.ajax({
			type: 'POST',
			dataType :  'html',
			success: jQuery(id_inp_emis).html('<div class="alert alert-info" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> No se realizo ningun cambio.</div>')
		});		
	}else{
		
		var valores = 'id_fondo=' + fondo + '&ids_emis=' + id_emis + '&sel_inv=' + sel_inv + '&sel_dep=' + sel_dep + '&c_dep=' + c_dep + '&c_inv=' + c_inv;
		
		jQuery.ajax({
			url:'/utlr/templates/administradorutlr/html/g_emi.php' ,
			type: 'POST' ,
			dataType :  'html' , 
			data: valores ,
			success: function( data ){
				jQuery(id_inp_emis).html(data);
				location.reload(5);
			}
		});
		
	}

}

function new_emi(nit_new_emi){
	
	var id_inp_emis = '#msj_inv';
	
	var id_nit_new_emi = 'nit_new_emi_' + nit_new_emi;
	var id_nom_new_emi = 'nom_new_emi_' + nit_new_emi;
	
	var nom_new_emi = document.getElementById(id_nom_new_emi).value;
	var nit_new_emi = document.getElementById(id_nit_new_emi).value;
	
	var valores = 'nom_emi=' + nom_new_emi + '&nit_emi=' + nit_new_emi;
		
	jQuery.ajax({
		url:'/utlr/templates/administradorutlr/html/new_emi.php' ,
		type: 'POST' ,
		dataType :  'html' , 
		data: valores ,
		success: function( data ){
			jQuery(id_inp_emis).html(data);
			location.reload(5);
		}
	});
}

/****************************************************
// SMLV
****************************************************/
function g_smlv(){
	
	var smlv = document.getElementById('val_smlv').value;
	
	var valores = 'smlv=' + smlv;
	
	alert(valores);
	
	jQuery.ajax({
		url:'/utlr/templates/administradorutlr/html/g_smlv.php' ,
		type: 'POST' ,
		dataType :  'html' , 
		data: valores ,
		success: function( data ){
			jQuery('#msj_smlv').html(data);
		}
	});
}
