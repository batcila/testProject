$(document).ready(function(){
	
	var move_step = 5; // Големина на стъпката за бутоните за преместване на снимката

	$('#but_up').click(function(){
		$('.pic_class').css({'top': (parseInt( $('.pic_class').css('top').replace('px', '') ) - move_step) + 'px'});
	});

	$('#but_left').click(function(){
		$('.pic_class').css({'left': (parseInt( $('.pic_class').css('left').replace('px', '') ) - move_step) + 'px'});
	});

	$('#but_down').click(function(){
		$('.pic_class').css({'top': (parseInt( $('.pic_class').css('top').replace('px', '') ) + move_step) + 'px'});
	});

	$('#but_right').click(function(){
		$('.pic_class').css({'left': (parseInt( $('.pic_class').css('left').replace('px', '') ) + move_step) + 'px'});
	});
	
	$.event.special.inputchange = {  // Функция за да може да работи в реално време onchange на input, а не само когато се смени value > http://stackoverflow.com/a/17429056 
	    setup: function() {
	        var self = this, val;
	        $.data(this, 'timer', window.setInterval(function() {
	            val = self.value;
	            if ( $.data( self, 'cache') != val ) {
	                $.data( self, 'cache', val );
	                $( self ).trigger( 'inputchange' );
	            }
	        }, 20));
	    },
	    teardown: function() {
	        window.clearInterval( $.data(this, 'timer') );
	    },
	    add: function() {
	        $.data(this, 'cache', this.value);
	    }
	};

	$('input[data-rounding]').on('inputchange',function(){ // Заобляне на рамката на снимката
		$('.pic_class').css($(this).data('rounding'), $(this).val() + '%');
	});

	$('input[data-borders], select[data-borders]').on('inputchange',function(){ // Стил на 'кантовете' на снимката
		$('.pic_class').css($(this).data('borders'), $(this).val() );
	});

	
});