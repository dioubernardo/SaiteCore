COM_Busca = function(o, cpid, request){
	var obj = o, results = $('<div />'), currentSelection, pageX, pageY, timer;
	
	function setHoverClass(el){
		$('div.item', results).removeClass('hover');
		$(el).addClass('hover');
		currentSelection = el;
	}

	function buildResults(resultObjects){
		var i;
		results.html('').hide();
		for (i = 0; i < resultObjects.length; i++){
			var item = $('<div>'+resultObjects[i][1]+'</div>');
			$(item).addClass('item').click(function(n){
					return function(){
						obj.val(resultObjects[n][1]);
						cpid.val(resultObjects[n][0]);
						cpid.change().blur();
						results.hide();
					};
				}(i)).
				mouseover(function(el){
					return function(){ setHoverClass(el); };
				}(item));
			results.append(item);
		}
		if (resultObjects.length > 0){
			currentSelection = undefined;
			results.show();
		}
	}

	function keyListener(e){
		switch (e.keyCode){
			case 13: // return key
				$(currentSelection).trigger('click');
				return false;
			case 40: // down key
				if (typeof currentSelection === 'undefined'){
					currentSelection = $('div.item:first', results).get(0);
				}else{
					currentSelection = $(currentSelection).next().get(0);
				}
				setHoverClass(currentSelection);
				if (currentSelection){
					results.scrollTop(currentSelection.offsetTop);
				}
				return false;
			case 38: // up key
				if (typeof currentSelection === 'undefined'){
					currentSelection = $('div.item:last', results).get(0);
				}else{
					currentSelection = $(currentSelection).prev().get(0);
				}
				setHoverClass(currentSelection);
				if (currentSelection){
					results.scrollTop(currentSelection.offsetTop);
				}
				return false;
			default:
				clearTimeout(timer);
				timer = setTimeout(function(el){
					return function(){
						if (el.value.length < 1){
							results.html('').hide();
							return false;
						}
						$(el).addClass("busca-buscando");
						$.post(request, {v:el.value}, function(data){
							$(el).removeClass("busca-buscando");
							buildResults(eval(data));
						},'text');
					};
				}(this), 500);
		}
	}

	results.addClass('busca-lista').css({
		'top': (obj.position().top + obj.outerHeight()) + 'px',
		'left': obj.position().left + 'px',
		'width': obj.outerWidth() + 'px'
	}).hide();

	obj.after(results).
		keypress(function(e){
			if (e.keyCode == 13)
				e.preventDefault();
		}).
		keyup(keyListener).
		blur(function(e){
			var resPos = results.offset();
			resPos.bottom = resPos.top + results.height();
			resPos.right = resPos.left + results.width();
			if (pageY < resPos.top || pageY > resPos.bottom || pageX < resPos.left || pageX > resPos.right)
				results.hide();
		}).
		focus(function(e){
			results.css({
				'top': (obj.position().top + obj.outerHeight()-1) + 'px',
				'left': obj.position().left + 'px'
			});
			if ($('div', results).length > 0)
				results.show();
		}).
		attr('autocomplete', 'off');

	/*
	 * <span position=relative>
	 * 	<input>
	 * 	<div position=relative>
	 * </span>
	 * */
//	obj.addClass('busca-ico');
	$().mousemove(function(e){
		pageX = e.pageX;
		pageY = e.pageY;
	});

	if ($.browser.opera){
		obj.keydown(function(e){
			if (e.keyCode === 40) // up key
				return keyListener(e);
		});
	}
};

jQuery.fn.extend({
	busca: function(url){
		this.each(function(){
			var hid = $(this);
			COM_Busca($('*[name='+hid.attr('name')+'Text]'), hid, url);
		});
	}
});
