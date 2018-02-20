(function($) {
	/**!
	 * @preserve Color animation 1.6.0
	 * http://www.bitstorm.org/jquery/color-animation/
	 * Copyright 2011, 2013 Edwin Martin
	 * Released under the MIT and GPL licenses.
	 */

	/**
	 * Check whether the browser supports RGBA color mode.
	 *
	 * Author Mehdi Kabab <http://pioupioum.fr>
	 * @return {boolean} True if the browser support RGBA. False otherwise.
	 */
	function isRGBACapable() {
		var $script = $('script:first'),
				color = $script.css('color'),
				result = false;
		if (/^rgba/.test(color)) {
			result = true;
		} else {
			try {
				result = ( color != $script.css('color', 'rgba(0, 0, 0, 0.5)').css('color') );
				$script.css('color', color);
			} catch (e) {
			}
		}

		return result;
	}

	$.extend(true, $, {
		support: {
			'rgba': isRGBACapable()
		}
	});

	var properties = ['color', 'backgroundColor', 'borderBottomColor', 'borderLeftColor', 'borderRightColor', 'borderTopColor', 'outlineColor'];
	$.each(properties, function(i, property) {
		$.Tween.propHooks[ property ] = {
			get: function(tween) {
				return $(tween.elem).css(property);
			},
			set: function(tween) {
				var style = tween.elem.style;
				var p_begin = parseColor($(tween.elem).css(property));
				var p_end = parseColor(tween.end);
				tween.run = function(progress) {
					style[property] = calculateColor(p_begin, p_end, progress);
				}
			}
		}
	});

	// borderColor doesn't fit in standard fx.step above.
	$.Tween.propHooks.borderColor = {
		set: function(tween) {
			var style = tween.elem.style;
			var p_begin = [];
			var borders = properties.slice(2, 6); // All four border properties
			$.each(borders, function(i, property) {
				p_begin[property] = parseColor($(tween.elem).css(property));
			});
			var p_end = parseColor(tween.end);
			tween.run = function(progress) {
				$.each(borders, function(i, property) {
					style[property] = calculateColor(p_begin[property], p_end, progress);
				});
			}
		}
	}

	// Calculate an in-between color. Returns "#aabbcc"-like string.
	function calculateColor(begin, end, pos) {
		var color = 'rgb' + ($.support['rgba'] ? 'a' : '') + '('
				+ parseInt((begin[0] + pos * (end[0] - begin[0])), 10) + ','
				+ parseInt((begin[1] + pos * (end[1] - begin[1])), 10) + ','
				+ parseInt((begin[2] + pos * (end[2] - begin[2])), 10);
		if ($.support['rgba']) {
			color += ',' + (begin && end ? parseFloat(begin[3] + pos * (end[3] - begin[3])) : 1);
		}
		color += ')';
		return color;
	}

	// Parse an CSS-syntax color. Outputs an array [r, g, b]
	function parseColor(color) {
		var match, quadruplet;

		// Match #aabbcc
		if (match = /#([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})/.exec(color)) {
			quadruplet = [parseInt(match[1], 16), parseInt(match[2], 16), parseInt(match[3], 16), 1];

			// Match #abc
		} else if (match = /#([0-9a-fA-F])([0-9a-fA-F])([0-9a-fA-F])/.exec(color)) {
			quadruplet = [parseInt(match[1], 16) * 17, parseInt(match[2], 16) * 17, parseInt(match[3], 16) * 17, 1];

			// Match rgb(n, n, n)
		} else if (match = /rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(color)) {
			quadruplet = [parseInt(match[1]), parseInt(match[2]), parseInt(match[3]), 1];

		} else if (match = /rgba\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9\.]*)\s*\)/.exec(color)) {
			quadruplet = [parseInt(match[1], 10), parseInt(match[2], 10), parseInt(match[3], 10),parseFloat(match[4])];

			// No browser returns rgb(n%, n%, n%), so little reason to support this format.
		} else {
			quadruplet = colors[color];
		}
		return quadruplet;
	}

	// Some named colors to work with, added by Bradley Ayers
	// From Interface by Stefan Petre
	// http://interface.eyecon.ro/
	var colors = {
		'aqua': [0,255,255,1],
		'azure': [240,255,255,1],
		'beige': [245,245,220,1],
		'black': [0,0,0,1],
		'blue': [0,0,255,1],
		'brown': [165,42,42,1],
		'cyan': [0,255,255,1],
		'darkblue': [0,0,139,1],
		'darkcyan': [0,139,139,1],
		'darkgrey': [169,169,169,1],
		'darkgreen': [0,100,0,1],
		'darkkhaki': [189,183,107,1],
		'darkmagenta': [139,0,139,1],
		'darkolivegreen': [85,107,47,1],
		'darkorange': [255,140,0,1],
		'darkorchid': [153,50,204,1],
		'darkred': [139,0,0,1],
		'darksalmon': [233,150,122,1],
		'darkviolet': [148,0,211,1],
		'fuchsia': [255,0,255,1],
		'gold': [255,215,0,1],
		'green': [0,128,0,1],
		'indigo': [75,0,130,1],
		'khaki': [240,230,140,1],
		'lightblue': [173,216,230,1],
		'lightcyan': [224,255,255,1],
		'lightgreen': [144,238,144,1],
		'lightgrey': [211,211,211,1],
		'lightpink': [255,182,193,1],
		'lightyellow': [255,255,224,1],
		'lime': [0,255,0,1],
		'magenta': [255,0,255,1],
		'maroon': [128,0,0,1],
		'navy': [0,0,128,1],
		'olive': [128,128,0,1],
		'orange': [255,165,0,1],
		'pink': [255,192,203,1],
		'purple': [128,0,128,1],
		'violet': [128,0,128,1],
		'red': [255,0,0,1],
		'silver': [192,192,192,1],
		'white': [255,255,255,1],
		'yellow': [255,255,0,1],
		'transparent': [255,255,255,0]
	};
/**!
 * ColumnPro JS
 */
 	function getAllVals(){
 		var allVals = '';
 		$('form').each(function(){
 			allVals += $(this).serialize();
 		});
 		return allVals;
 	}
 	function fltr_notify(mssg,time){
		var msgDiv = document.createElement('div');
		msgDiv.setAttribute('id','pop_msg_div');
		var txt = document.createTextNode(mssg);
		msgDiv.appendChild(txt);
		$('body')[0].appendChild(msgDiv);
		$('#wpwrap').fadeTo(400,0.2);
		$('#pop_msg_div').fadeTo(400,1);
		setTimeout(function(){
				$('#wpwrap').fadeTo(400,1);
			$('#pop_msg_div').fadeTo(400,0,function(){
				$('#pop_msg_div').remove();
			});
		},time);
	}
	function updateOpnCls(){
		var oc=[];
		$('.sld-slct').each(function(){
			var ocVal = ($(this).hasClass('close'))?'c':'o';
			oc.push(ocVal);
		});
		var data={_wpnonce:columnpro.wpnonce,oc:oc,fuid:columnpro.uid,action:'yg_flt_update_mn_oc'}
		$.ajax({
			url:ajaxurl,
			data:data,
			type:"POST",
			success:function(data){
				
			}
		});
	}
	$(document).ready(function() {
		var allVals = getAllVals();
		var formSubmitting = false;
		$('.select_pt').change(function(e){
			var ptp = $(this).val();
			var parnt = $(this).parents('.select_clm');
			var mt_select = parnt.find('.select_mt');
			if(ptp!=''){
				var data={posttype:ptp,_wpnonce:parnt.attr('flt_nonce'),action:'yg_fltr_get_meta'}
				mt_select.prop('disabled',true);
				$.ajax({
					url:ajaxurl,
					data:data,
					type:"POST",
					//dataType:"json",
					success:function(data){
						mt_select.find('option').not(':first').remove().end();
						mt_select.append(data);
						mt_select[0].selectedIndex = 0;
						mt_select.prop('disabled',false);
					}
				});
			}
		});
		$('.select_pt_t').change(function(e){
			var ptp = $(this).val();
			var parnt = $(this).parents('.select_clm');
			var mt_select = parnt.find('.select_mt_t');
			if(ptp!=''){
				var data={posttype:ptp,_wpnonce:parnt.attr('flt_nonce'),action:'yg_fltr_get_tax'}
				mt_select.prop('disabled',true);
				$.ajax({
					url:ajaxurl,
					data:data,
					type:"POST",
					//dataType:"json",
					success:function(data){
						mt_select.find('option').not(':first').remove().end();
						mt_select.append(data);
						mt_select[0].selectedIndex = 0;
						mt_select.prop('disabled',false);
					}
				});
			}
		});
		$('.wrap.filter-wrap').on('change','.select_mt',function(e){
			var isthumb = ($(this).val()=='thumbnail');
			var parnt = $(this).parents('.select_clm,.pt-val.opened');
			parnt.find('input[type="checkbox"]').each(function(){
				$(this).prop('disabled',isthumb);
				if(isthumb)
					$(this).prop('checked',false);
			});
		});
		$('.pt-val-delete').click(function(e){
			var parnt = $(this).parents('.pt-val');
			var parntForm = $(this).parents('form').find('.button.button-primary');
			parnt.css({overflow:'hidden',height:parnt.height()});
			parnt.animate({
				opacity: 0,
				width: 0
			},200, function() {
				parnt.remove();
				parntForm.animate({backgroundColor:'#77bed8'},500, function(){parntForm.animate({backgroundColor:'#0085ba'},500)});
			});
		});
		$('.pt-val-edit').click(function(e){
			var parnt = $(this).parents('.pt-val');
			var indx = $(this).attr('div-int');

			var meta_slct = parnt.find('#yg_pop_filter_meta'+indx);
			var meta_val = meta_slct.val();
			var clm_name = parnt.find('#yg_pop_filter_name'+indx);
			var srt = parnt.find('#yg_pop_filter_sort'+indx);
			var srt_num = parnt.find('#yg_pop_filter_sort_num'+indx);
			var dd = parnt.find('#yg_pop_filter_dd'+indx);
			var msdd = parnt.find('#yg_pop_filter_msdd'+indx);

			if(!parnt.hasClass('opened')){
				parnt.addClass('opened');
				var pt = parnt.find('#yg_pop_filter_ptype'+indx).val();

				meta_slct.wrap('<div class="filt_row"></div>');
				meta_slct.removeAttr('value').removeAttr('type')
				var slct_attrs = {};
				$.each(meta_slct[0].attributes, function(idx, attr) {
				    slct_attrs[attr.nodeName] = attr.nodeValue;
				});
				meta_slct.replaceWith(function () {
				    return $('<select />', slct_attrs).append($(this).contents());
				});
				meta_slct = parnt.find('#yg_pop_filter_meta'+indx);
				meta_slct.addClass('select_mt').append('<option value="">Select</option>');
				var act = ($(this).hasClass('metaval'))?'yg_fltr_get_meta':'yg_fltr_get_tax';
				var data={posttype:pt,_wpnonce:parnt.attr('flt_nonce'),action:act}
				meta_slct.prop('disabled',true);
				$.ajax({
					url:ajaxurl,
					data:data,
					type:"POST",
					//dataType:"json",
					success:function(data){
						meta_slct.append(data);
						meta_slct[0].selectedIndex = meta_slct.find('option').index(meta_slct.find('option[value="'+meta_val+'"]'));
						meta_slct.prop('disabled',false);
					}
				});

				clm_name.attr('type','text').wrap('<div class="filt_row"></div>').blur(function(){
					$(this).parents('.pt-val').find('.clm-hdr-name').text($(this).val());
				});

				var isthumb = (meta_val=='thumbnail');
				srt.attr('type','checkbox').wrap('<div class="filt_row"></div>');
				srt.prop('checked',srt.val()==1).prop('disabled',isthumb).val(1).after('<label class="filt_cb">Shortable</label>');

				srt_num.attr('type','checkbox').wrap('<div class="filt_row"></div>');
				srt_num.prop('checked',srt_num.val()==1).prop('disabled',isthumb).val(1).after('<label class="filt_cb">Short by number</label>');

				dd.attr('type','checkbox').wrap('<div class="filt_row"></div>');
				dd.prop('checked',dd.val()==1).prop('disabled',isthumb).val(1).after('<label class="filt_cb">Add dropdown</label>');

				msdd.attr('type','checkbox').wrap('<div class="filt_row"></div>');
				msdd.prop('checked',msdd.val()==1).prop('disabled',isthumb).val(1).after('<label class="filt_cb">Set multi-select</label>');
			}else{
				meta_val = meta_slct.val();

				var slct_attrs = {};
				$.each(meta_slct[0].attributes, function(idx, attr) {
				    slct_attrs[attr.nodeName] = attr.nodeValue;
				});
				meta_slct.replaceWith(function () {
				    return $('<input />', slct_attrs);
				});
				meta_slct = parnt.find('#yg_pop_filter_meta'+indx);
				meta_slct.removeClass('select_mt').attr('type','hidden').val(meta_val).unwrap();

				clm_name.unwrap().attr('type','hidden');
				parnt.find('.filt_cb').remove();
				cVal = (srt.prop('checked'))?1:'';
				srt.val((srt.prop('checked'))?1:'').unwrap().attr('type','hidden');
				srt_num.val((srt_num.prop('checked'))?1:'').unwrap().attr('type','hidden');
				dd.val((dd.prop('checked'))?1:'').unwrap().attr('type','hidden').val((dd.prop('checked'))?1:'');
				msdd.val((msdd.prop('checked'))?1:'').unwrap().attr('type','hidden').val((msdd.prop('checked'))?1:'');
				parnt.removeClass('opened');
			}
		});
		$('.sld-slct').click(function(e){
			$(this).next('.sld-div').slideToggle('fast',function(){
				$(this).next('.sld-div').toggleClass('closed');
			});
			$(this).toggleClass('close');
			updateOpnCls();
		});
		$('#clr_fltr_cache').click(function(e){
			e.preventDefault();
			var data={_wpnonce:$(this).attr('pop_nonce'),action:'yg_flt_clr_cache'}
			$.ajax({
				url:ajaxurl,
				data:data,
				type:"POST",
				success:function(data){
					fltr_notify(data,2000);
				}
			});
		});
		$('.pt-val-div.sld-div').sortable({
      		placeholder: 'ui-state-highlight',
      		axis: 'y',
      		forcePlaceholderSize: true
    	});
    	$('.pt-val-div.sld-div').disableSelection();

		$('form').submit(function(e){
			window.formSubmit = true;
		});
 		window.formSubmit = false;
		$(window).bind('beforeunload', function(){
			$dirtyForms = (allVals == getAllVals());
			if ($dirtyForms || window.formSubmit){
				return;
			}
			// Prevent multiple prompts - seen on Chrome and IE
			if (navigator.userAgent.toLowerCase().match(/msie|chrome/)){
				if (window.aysHasPrompted){
					return;
				}
				window.aysHasPrompted = true;
				window.setTimeout(function() {window.aysHasPrompted = false;}, 900);
			}
			return 'You have unsaved changes';
		});
	});
})(jQuery);