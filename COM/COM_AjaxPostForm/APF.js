APF ={
	c: 0,
	aplicar: function(o){
		$('form', o).each(function(){
			var of = $(this), filhos = $('.APF', of);
			if (filhos.length > 0 || of.hasClass('APF')){
				/* modificar o target e controlar o onsubmit */
				of.submit(function(){
					var of = $(this);
					if (of.hasClass('APF-emExecucao'))
						return false;
					var id = '__APF'+(APF.c++), ifra = $('<iframe src="about:blank" name="'+id+'" style="position:absolute; border:0; width:1px; height:1px; left:-10px; top:-10px" />');
					of.attr('target', id).addClass('APF-emExecucao');
					$('body').append(ifra);
					ifra.load(function(o, of){
						return function(){
							try{
								var code = o.contents().find("body").html();
								if (code != "")
									eval(code);
							}catch(e){
								alert("Erro APF: " + e + "\n" +code);
							}
							of.removeClass('APF-emExecucao');
							setTimeout(function(o){
								return function(){
									o.remove();
								}
							}(o), 100);
						}
					}(ifra, of));
				});
			}
			filhos.click(function(){
				var fl = $(this);
				if (this.nodeName == 'A' && fl.attr('href') != ''){
					var a = of.attr('action');
					of.attr('action', fl.attr('href'));
					of.submit();
					of.attr('action', a);
				}else
					of.submit();
				return false;
			});
		});
	}
}
$(document).ready(function(){
	APF.aplicar(document);
});