COM_Form = {
	ChecaData: function(strDate){
		if (strDate.length < 1) return true;
		var strDateArray = strDate.split("/");
		var intday = parseInt(strDateArray[0], 10);
		var intMonth = parseInt(strDateArray[1], 10);
		intYear = parseInt(strDateArray[2], 10);

		if (isNaN(intYear) || isNaN(intMonth) || isNaN(intYear)) return false;
		if (intMonth>12 || intMonth<1) return false;
		if ((intMonth == 1 || intMonth == 3 || intMonth == 5 || intMonth == 7 || intMonth == 8 || intMonth == 10 || intMonth == 12) && (intday > 31 || intday < 1)) return false;
		if ((intMonth == 4 || intMonth == 6 || intMonth == 9 || intMonth == 11) && (intday > 30 || intday < 1)) return false;

		if (intMonth == 2){
			if (intday < 1) return false;
			if (COM_Form.LeapYear(intYear)){
				if (intday > 29) return false;
			}else{
				if (intday > 28) return false;
		   }
		}

		return true;
	},
	ChecaDataHoraMinuto: function(strDate){
		if (strDate.length != 16)
			return false;
		strDate = strDate.split(' ');
		if (!COM_Form.ChecaData(strDate[0]))
			return false;
		strDate = strDate[1].split(':');
		if (parseInt(strDate[0], 10) > 23)
			return false;
		if (parseInt(strDate[1], 10) > 59)
			return false;
		return true;
	},
	LeapYear: function(intYear){
		if ((intYear % 100) == 0){
			if ((intYear % 400) == 0) return true;
		}else{
			if ((intYear % 4) == 0) return true;
		}
		return false;
	},
	cpf: function(v){
		if (v.length != 11)
			return false;
		var c  = v.substr(0,9), dv = v.substr(9,2), d1 = 0, i, d2;
		for (i=0; i<9; i++)
			d1 += c.charAt(i)*(10-i);
		d1 = 11 - (d1 % 11);
		if (d1 > 9)
			d1 = 0;
		d2 = d1 * 2;
		for (i=0; i<9; i++)
			d2 += c.charAt(i)*(11-i);
		d2 = 11 - (d2 % 11);
		if (d2 > 9)
			d2 = 0;
		if (dv.charAt(0) != d1 || dv.charAt(1) != d2)
			return false;
		return true;
	},
	cnpj: function(v){
		if (v.length != 14)
			return false;
		var c = v.substr(0,12), dv = v.substr(12,2), d1 = 0, ms, i, d2 = 0;
		ms = "543298765432";
		for (i=0; i<12; i++)
			d1 += c.charAt(i)*ms.charAt(i);
		d1 = (d1 % 11);
		d1 = (d1 == 0 || d1 == 1) ? 0 : (11 - d1);
		c = c + d1;
		ms = "6543298765432";
		for (i=0; i<13; i++)
			d2 += c.charAt(i)*ms.charAt(i);
		d2 = (d2 % 11);
		d2 = (d2 == 0 || d2 == 1) ? 0 : (11 - d2);
		if (dv.charAt(0) != d1 || dv.charAt(1) != d2)
			return false;
		return true;
	}
};

jQuery.fn.extend({
	tipo: function(p1, p2){
		switch(p1){
			case "data":
				this.mask('99/99/9999');
				this.change(function(){
					var o = $(this), v;
					if ((v = o.val()) != '' && !COM_Form.ChecaData(v)){
						alert('Data inválida');
						o.val('');
					}
				});
			break;
			case "dataHoraMinuto":
				this.mask('99/99/9999 99:99');
				this.change(function(){
					var o = $(this), v;
					if ((v = o.val()) != '' && !COM_Form.ChecaDataHoraMinuto(v)){
						alert('Data inválida');
						o.val('');
					}
				});
			break;
			case "email":
				this.change(function(){
					var o = $(this), v;
					if ((v = o.val()) != '' && !v.match(/^[^ ]+@[^ ]+\.[a-z]{2,3}$/i)){
						alert('E-mail inválido');
						o.val('');
					}
				});
			break;
			case "cpf":
				this.mask('999.999.999-99');
				this.change(function(){
					var o = $(this), v;
					if ((v = o.val()) != '' && !COM_Form.cpf(v.replace(/[^0-9]/g, ''))){
						alert('CPF inválido');
						o.val('');
					}
				});
			break;
			case "cnpj":
				this.mask('99.999.999/9999-99');
				this.change(function(){
					var o = $(this), v;
					if ((v = o.val()) != '' && !COM_Form.cnpj(v.replace(/[^0-9]/g, ''))){
						alert('CNPJ inválido');
						o.val('');
					}
				});
			break;
			case "cpf|cnpj":
				this.change(function(){
					var o = $(this), v;
					if ((v = o.val()) != ''){
						v = v.replace(/[^0-9]/g, '');
						if (!COM_Form.cnpj(v) && !COM_Form.cpf(v)){
							alert('CPF ou CNPJ inválido');
							o.val('');
						}else{
							o.val(v);
						}
					}
				});
			break;
			case "valor":
				this.css('text-align', 'right');
				this.change(function(){
					var o = $(this), v;
					if ((v = o.val()) != ''){
						v = parseFloat(v.replace(/[^0-9,]/g, '').replace(',', '.'));
						if (isNaN(v)) v = 0;
						if (v == 0){
							o.val('');
						}else{
							v = new Number(v).toFixed(parseInt(p2)).replace('.', ',');
							o.val(v);
						}
					}
				});
			break;
			default:
				this.mask(p1, p2);
		}
	}
});