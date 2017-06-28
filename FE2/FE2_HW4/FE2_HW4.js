$(document).ready(function(){

	$('#hideNav').click(function(){ // бутон за скриване на навигацията
		$('nav').css({'width': '40px', 'border-radius': '0 15px 15px 0'});
		$('#mainNav').css('display', 'none');
		$('#secondNav').css('display', 'initial');
	});

	$('#showNav').click(function(){ // бутон за показване на навигацията
		$('nav').css({'width': 'auto', 'border-radius': '0 0 15px'});
		$('#mainNav').css('display', 'initial');
		$('#secondNav').css('display', 'none');
	});

	$('#sendFile').click(function() { // снимка като бутон за прикачване на фаил
    	$('#myFile').focus().click();
	});

	// Пагинация 
	var pagNumbOfButtons = 15; // брой на бутоните в пагинацията
	var pagActiveButton = 1; // тук ще държим кой е номера на активния бутон ( от 1 - до броя на бутоните)
	
	function drawPagButtons(numberOfBut) { // чертаене на бутоните на пагинацията
		var pag = document.getElementById('pagination');
		var pagination = '<button type="button" id="butBack">&lt;&lt;</button>';
		for (var i = 1 ; i < pagNumbOfButtons + 1 ; i++ ) {
			pagination += '<button type="button" id="pagBut' + i + '"' + (i == 1 ? ' class="activeButton" ' : '' ) + '>' + i + '</button>'
		}
		pagination += '<button type="button" id="butNext">&gt;&gt;</button>';
		pag.innerHTML = pagination;
	}

	drawPagButtons(pagNumbOfButtons); // викаме функцията за да начертаем бутоните при зареждане

	$('#pagination button').not('#butBack').not('#butNext').click(function(){ // Директен клик върху пагинацията
		$('#pagination button').not('#butBack').not('#butNext').removeClass('activeButton');
		$(this).addClass('activeButton');
		pagActiveButton = this.id; 
		pagActiveButton = Number( pagActiveButton.substr(6) ); // вземаме само номера на активния бутон, а не цялото ID
		console.log('active button is:' + pagActiveButton); // само за проверка
	});

	$('#butBack').click(function(){ // бутон "назад"
		if ( pagActiveButton > 1 ) {
			$('#pagBut' + pagActiveButton).removeClass('activeButton');
			$('#pagBut' + ( pagActiveButton - 1) ).addClass('activeButton');
			pagActiveButton--;
			console.log('active button is:' + pagActiveButton); // само за проверка
		} 
	});

	$('#butNext').click(function(){ // бутон "напред"
		if ( pagActiveButton < pagNumbOfButtons ) {
			$('#pagBut' + pagActiveButton).removeClass('activeButton');
			$('#pagBut' + ( pagActiveButton + 1) ).addClass('activeButton');
			pagActiveButton++;
			console.log('active button is:' + pagActiveButton); // само за проверка
		} 
	});
});