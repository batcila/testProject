var progBarID = document.getElementById('progBar');

function progClick(funcParm) {
	
	switch (true) {
		case (funcParm == 'add' && progBarID.value < 100): 		// кликнат "+" и стойност по-малка от 100 > добавя 10
			progBarID.value += 10;
			break;
		case (funcParm == 'sub' && progBarID.value > 0): 		// кликнат "-" и стойност по-голяма от 0 > изважда 10
			progBarID.value -= 10;
			break;
		case (funcParm == 'sub'): 								// ако е кликант "-" и предишните условия не са изпълнени значи е достигната 0
			alert('Достигнахте минимума !');
			break;
		default: 												// ако не е изпълнено нито едно от горните - следователно е достигнато 100 
			alert('Достигнахте максимума !');
			break;
	}

}