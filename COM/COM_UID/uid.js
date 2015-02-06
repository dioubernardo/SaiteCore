
function uid(id, f){
	/*
	 * retorno 
	 * 	0 -> erros internos 
	 * 	1 -> liberado
	 * 	?? -> coisas vindas da execução
	 * */
	this.ev = function(o){
		if (o == 1)
			this.l = true;
		if (typeof f != 'undefined')
			this.f(o);
	};
	this.send = function(url, vars){
		if (this.l){
			this.o.reset();
			for(v in vars) this.o.set(v, vars[v]);
			this.o.send(url);
		}
	};
	this._init = function(){
		$('body').append('<div id="'+this.id+'"></div>');
		$('#'+this.id).css({
			position: 'absolute',
			left:0,
			top:0
		});
		var ap = new SWFObject("{URL}core/com/UID/uid.swf?id="+this.id, 'uid'+this.id, "1", "1", 8);
		ap.addParam("wmode", "transparent");
		ap.addParam("allowScriptAccess", "sameDomain");
		ap.write(this.id);		
		this.o = (navigator.appName.indexOf("Microsoft") != -1) ? document.all['uid'+this.id] : document['uid'+this.id]; 
	};
	this.id = id;
	this.f = f;
	this.o = null;
	this.l = false;
	var o = this;
	$(document).ready(function (){
		o._init();
	});
} 