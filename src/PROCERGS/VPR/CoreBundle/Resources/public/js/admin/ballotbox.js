var AjaxPoll = {
	ajaxQueue : [],
	ajaxActive : 0,
	ajaxMaxConc : 4,
	addAjax : function(obj) {
	    var callback1 = function() {
        	var r1 = AjaxPoll.ajaxQueue.shift();
        	if (r1) {
        		$.when($.ajax(r1)).always(callback1);	
        	} else {
        		AjaxPoll.ajaxActive--;		
        	}
	    }
	    if (AjaxPoll.ajaxActive === AjaxPoll.ajaxMaxConc) {
	    	AjaxPoll.ajaxQueue.push(obj);
	    } else {
	    	AjaxPoll.ajaxActive++;
	        $.when($.ajax(obj)).always(callback1);
	    }
	}
}
function download(filename, text) {
	  var element = document.createElement('a');
	  element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
	  element.setAttribute('download', filename);

	  element.style.display = 'none';
	  document.body.appendChild(element);

	  element.click();

	  document.body.removeChild(element);
	}
UTF8 = {
		encode: function(s){
			for(var c, i = -1, l = (s = s.split("")).length, o = String.fromCharCode; ++i < l;
				s[i] = (c = s[i].charCodeAt(0)) >= 127 ? o(0xc0 | (c >>> 6)) + o(0x80 | (c & 0x3f)) : s[i]
			);
			return s.join("");
		},
		decode: function(s){
			for(var a, b, i = -1, l = (s = s.split("")).length, o = String.fromCharCode, c = "charCodeAt"; ++i < l;
				((a = s[i][c](0)) & 0x80) &&
				(s[i] = (a & 0xfc) == 0xc0 && ((b = s[i + 1][c](0)) & 0xc0) == 0x80 ?
				o(((a & 0x03) << 6) + (b & 0x3f)) : o(128), s[++i] = "")
			);
			return s.join("");
		}
	};
String.prototype.paddingLeft = function (paddingValue) {
	   return String(paddingValue + this).slice(-paddingValue.length);
	};

$(function() {
	function testHabFile() {
		$('#div-file').hide();
		if (window.File && window.FileReader && window.FileList && window.Blob) {
			$('#div-file').show();
		} else {
			$('#div-file-warn').show();
		}
	}
	
    $("#btn-load-ballotbox").click(function() {
        $("form input[data-val], form select[data-val]").each(function() {
            var val = $(this).data("val");
            $(this).val(val);
        });

        var openingTime = $(".opening-time");
        var closingTime = $(".closing-time");

        if (openingTime.data("day") || closingTime.data("day")) {
            $("form .opening-time select[id$='date_day']").val(openingTime.data("day"));
            $("form .opening-time select[id$='date_month']").val(openingTime.data("month"));
            $("form .opening-time select[id$='date_year']").val(openingTime.data("year"));
            $("form .opening-time select[id$='time_hour']").val(parseInt(openingTime.data("hour")));
            $("form .opening-time select[id$='time_minute']").val(parseInt(openingTime.data("minute")));

            $("form .closing-time select[id$='date_day']").val(closingTime.data("day"));
            $("form .closing-time select[id$='date_month']").val(closingTime.data("month"));
            $("form .closing-time select[id$='date_year']").val(closingTime.data("year"));
            $("form .closing-time select[id$='time_hour']").val(parseInt(closingTime.data("hour")));
            $("form .closing-time select[id$='time_minute']").val(parseInt(closingTime.data("minute")));

            $("#chkDtDiffPoll").prop('checked', true);
            $('.ballotbox-offline-time-itens').show();
        }


    })

    var isOnline = $('#procergs_vpr_corebundle_ballotbox_isOnline');
    var isSms = $('#procergs_vpr_corebundle_ballotbox_isSms');

    $('#procergs_vpr_corebundle_ballotbox_isOnline, #procergs_vpr_corebundle_ballotbox_isSms').change(function(){
        if (isOnline.prop('checked') || isSms.prop('checked')) {
        	$('.ballotbox-offline-itens').hide();
        	$('#procergs_vpr_corebundle_ballotbox_city').val('');
        	$('#procergs_vpr_corebundle_ballotbox_city').prop('selectedIndex', '');
        } else {
        	$('.ballotbox-offline-itens').show();
        	testHabFile();
        }
    });
    $('#chkDtDiffPoll').change(function(){
    	var self = $(this);
    	if (!self.prop('checked')) {
        	$('.ballotbox-offline-time-itens').hide();
        	$('.ballotbox-offline-time-itens select').val('');
        	$('.ballotbox-offline-time-itens select').prop('selectedIndex', '');
        } else {
        	if ($('.ballotbox-offline-time-itens select').val() == '') {
        		if (!$('#procergs_vpr_corebundle_ballotbox_poll').val()) {
        			alert('Selecione um votação primeiro');
        			self.prop('checked', false);
        			return;
        		}
        		$.ajax({
                    url: admin_select_poll,
                    type: 'POST',
                    data: {
                        poll_id: $('#procergs_vpr_corebundle_ballotbox_poll').val()
                    },
                    dataType: 'json',
                    success: function(result) {
                        if(result.poll){
                        	var openingTime = result.poll.openingTime.split(/[^0-9]/);
                        	var closingTime = result.poll.closingTime.split(/[^0-9]/);

                        	$('#procergs_vpr_corebundle_ballotbox_openingTime_date_day').val(openingTime[2]*1);
                        	$('#procergs_vpr_corebundle_ballotbox_openingTime_date_month').val(openingTime[1]*1);
                        	$('#procergs_vpr_corebundle_ballotbox_openingTime_date_year').val(openingTime[0]*1);
                        	$('#procergs_vpr_corebundle_ballotbox_openingTime_time_hour').val(openingTime[3]*1);
                        	$('#procergs_vpr_corebundle_ballotbox_openingTime_time_minute').val(openingTime[4]*1);

                        	$('#procergs_vpr_corebundle_ballotbox_closingTime_date_day').val(closingTime[2]*1);
                        	$('#procergs_vpr_corebundle_ballotbox_closingTime_date_month').val(closingTime[1]*1);
                        	$('#procergs_vpr_corebundle_ballotbox_closingTime_date_year').val(closingTime[0]*1);
                        	$('#procergs_vpr_corebundle_ballotbox_closingTime_time_hour').val(closingTime[3]*1);
                        	$('#procergs_vpr_corebundle_ballotbox_closingTime_time_minute').val(closingTime[4]*1);
                        	$('.ballotbox-offline-time-itens').show();
                        }
                    },
                    error: function(result) {
                    	alert('Aconteceu um erro');
                    	self.prop('checked', false);
                    }
                });
        	}
        }
    });

    $('#procergs_vpr_corebundle_ballotbox_isOnline').trigger('change');
    $('#chkDtDiffPoll').trigger('change');

 
    	
    $('form').on('submit', function(evt){    	
    	var files = evt.target.procergs_vpr_corebundle_ballotbox_lote.files; // FileList object    
    	if (files.length) {
    		try {
        	    for (var i = 0, f; f = files[i]; i++) {
        	    	var reader = new FileReader(); 
    	    		reader.onload = function (evt) {    	    			
    	    			var content = UTF8.decode(evt.target.result);
    	    			var response = "";
    	    			var lines = new CSV(content).parse();
    	    			var headerCsv = lines[0]; 
    	    			if (headerCsv[1] != "Nome" 
                            || headerCsv[2] != "Município" 
                            || headerCsv[3] != "DDD"
                            || headerCsv[4] != "Telefone"
                            || headerCsv[5] != "Email"                        
                            ) {
                            throw new Exception("cabecalho diferente de " . $header);
                        }
    	    			var checked1 = $('#chkDtDiffPoll').is(':checked');
    	    			var opening_time;
    	    			var closing_time;
    	    			var poll_id;
    	    			if (!$('#procergs_vpr_corebundle_ballotbox_poll').val()) {
    	    				throw new Exception("selecione um votacao");
    	    			}
    	    			poll_id = $('#procergs_vpr_corebundle_ballotbox_poll').val();
	    				if (checked1) {
    	    				opening_time = $('#procergs_vpr_corebundle_ballotbox_openingTime_date_year').val()+'-'+
    	    				$('#procergs_vpr_corebundle_ballotbox_openingTime_date_month').val().paddingLeft('00')+'-'+
    	    				$('#procergs_vpr_corebundle_ballotbox_openingTime_date_day').val().paddingLeft('00')+' '+
                        	$('#procergs_vpr_corebundle_ballotbox_openingTime_time_hour').val().paddingLeft('00')+':'+
                        	$('#procergs_vpr_corebundle_ballotbox_openingTime_time_minute').val().paddingLeft('00')+':00';
    	    				closing_time = $('#procergs_vpr_corebundle_ballotbox_closingTime_date_year').val()+'-'+
                        	$('#procergs_vpr_corebundle_ballotbox_closingTime_date_month').val().paddingLeft('00')+'-'+
                        	$('#procergs_vpr_corebundle_ballotbox_closingTime_date_day').val().paddingLeft('00')+' '+
                        	$('#procergs_vpr_corebundle_ballotbox_closingTime_time_hour').val().paddingLeft('00')+':'+
                        	$('#procergs_vpr_corebundle_ballotbox_closingTime_time_minute').val().paddingLeft('00')+':00';
	    				}
	    				lines[0].push('retorno');
	    				var w = lines.length - 1;
	    				$("body").append("<div class='ajax-bg'><div class='ajax-loader'></div><div class='ajax-txt'>Aguarde...</div></div>");
    	    			for (var x = 1; x < (lines.length); x++) {
    	    				var form = {};    	    				
    	    				form.poll_id = poll_id;    	    				
    	    				form.opening_time = opening_time;
    	    				form.closing_time = closing_time;
    	    				form.nome = lines[x][1];
    	    				form.municipio = lines[x][2];
    	    				form.ddd = lines[x][3];
    	    				form.telefone = lines[x][4];
    	    				form.email = lines[x][5];
    	    				
    	    				AjaxPoll.addAjax({
    	                        url: admin_ballotbox_create2,
    	                        type: 'POST',
    	                        async: true,
    	                        data: form,
    	                        chule: x,
    	                        success: function(result) {
    	                        	lines[this.chule].push(result);
    	                        },
    	                        error: function(ex) {
    	                        	lines[this.chule].push(ex.responseText);
    	                        },
    	                        complete: function() {
    	                        	w--;
    	                        	if (w == 0) {
    	                        		$("body div.ajax-bg").remove();
        	        	    			var response = new CSV(lines).encode();
        	        	    			download('resultado.txt', response);   
    	                        	}
    	                        }
    	                    });
    	    			}
        			};
          	    	reader.onerror = function(e) {
          	    		throw new Exception("File could not be read! Code " + e.target.error.code);
          	    	};
          	    	reader.readAsBinaryString(f);
          	    }
    		} catch (ex) {
    			alert(ex.message);
    		}
    	    return false;
    	} else {
    		$("body").append("<div class='ajax-bg'><div class='ajax-loader'></div><div class='ajax-txt'>Aguarde...</div></div>");	
    	}
    });

});
