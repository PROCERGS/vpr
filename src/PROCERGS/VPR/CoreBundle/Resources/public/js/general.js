function completaZerosEsquerda(numero, tamanho) {
	var ret = "";
	if (numero.length > 0) {
		var qtdCompleta = tamanho - numero.length;
		var zeros = "";
		for (var i = 0; i < qtdCompleta; i++) {
			zeros += "0";
		}
		ret = zeros + numero;
	}
	return ret;
}
function somenteNumeros(e) {
	if (window.event) {
		// for IE, e.keyCode or window.event.keyCode can be used
		key = e.keyCode;
	} else {
		if (e.which) {
			// netscape
			key = e.which;
		} else {
			key = 9;
		}
	}
	if (!isNum(key)) {
		return false;
	} else {
		return true;
	}
}
function validarTitulo(inscricao) {
	var paddedInsc = inscricao;
	// alert("validando inscricao " + paddedInsc);
	var dig1 = 0;
	var dig2 = 0;
	var tam = paddedInsc.length;
	var digitos = paddedInsc.substr(tam - 2, 2);
	var estado = paddedInsc.substr(tam - 4, 2);
	var titulo = paddedInsc.substr(0, tam - 2);
	var exce = (estado == '01') || (estado == '02');
	dig1 = (titulo.charCodeAt(0) - 48) * 9 + (titulo.charCodeAt(1) - 48) * 8
			+ (titulo.charCodeAt(2) - 48) * 7 + (titulo.charCodeAt(3) - 48) * 6
			+ (titulo.charCodeAt(4) - 48) * 5 + (titulo.charCodeAt(5) - 48) * 4
			+ (titulo.charCodeAt(6) - 48) * 3 + (titulo.charCodeAt(7) - 48) * 2;
	var resto = (dig1 % 11);
	if (resto == 0) {
		if (exce) {
			dig1 = 1;
		} else {
			dig1 = 0;
		}
	} else {
		if (resto == 1) {
			dig1 = 0;
		} else {
			dig1 = 11 - resto;
		}
	}
	dig2 = (titulo.charCodeAt(8) - 48) * 4 + (titulo.charCodeAt(9) - 48) * 3
			+ dig1 * 2;
	resto = (dig2 % 11);
	if (resto == 0) {
		if (exce) {
			dig2 = 1;
		} else {
			dig2 = 0;
		}
	} else {
		if (resto == 1) {
			dig2 = 0;
		} else {
			dig2 = 11 - resto;
		}
	}
	if ((digitos.charCodeAt(0) - 48 == dig1)
			&& (digitos.charCodeAt(1) - 48 == dig2)) {
		return true; // Titulo valido
	} else {
		return false;
	}
}