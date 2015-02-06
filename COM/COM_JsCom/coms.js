JsCom = {

	lista:{
		nodeBase:[],
		init: function(){
			$(document).ready(function(){
				$(".jscom-lista").each(function(){
					$(".jscom-lista-add-" + this.id).attr('rel', this.id).click(JsCom.lista.add);
					$(".jscom-lista-del", this).attr('rel', this.id).click(JsCom.lista.del);
					var nd = $(".jscom-lista-item", this);
					JsCom.lista.nodeBase[this.id] = nd.clone(true).removeClass("jscom-lista-item");
					nd.remove();
					$(this).sortable({
						axis: 'y',
						handle: '.jscom-lista-mov'
					});
				});
			});
		},
		add: function(){
			var id = $(this).attr('rel');
			$("#"+id).append(JsCom.lista.nodeBase[id].clone(true));
		},
		del: function(){
			var bs = $("#"+$(this).attr('rel')).get(0), filho, o = this;
			while(o != bs){
				filho = o;
				o = o.parentNode;
			}
			bs.removeChild(filho);
		}
	}

}
