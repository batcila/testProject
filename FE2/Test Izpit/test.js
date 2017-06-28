var messagesArray = [
	['Васко', 'Купувам трактор', true],
	['Petar', 'Neshto si alabala', true],
	['Васко', 'Iztre]qla rabota e toq izpit', false],
	['Васко asdas', 'asdasdasdas asd asd a', false]
];

function showMsgTable() {
	var table = document.getElementById('table_mgs');
	for (var i = 0; i < messagesArray.length; i++) {
		var row = table.insertRow(i+1);
		var cell1 = row.insertCell();
		var cell2 = row.insertCell();
		var cell3 = row.insertCell();
		var cell4 = row.insertCell();
		var cell5 = row.insertCell();
		var boldMgs = (messagesArray[i][2] ? '<b>' : '');
		var boldMgsClose = (messagesArray[i][2] ? '</b>' : '');
		cell1.innerHTML = '<button type="button">Виж</button>';
		cell2.innerHTML = boldMgs + messagesArray[i][0] + boldMgsClose
		cell3.innerHTML = boldMgs + messagesArray[i][1] + boldMgsClose
		cell4.innerHTML = boldMgs + (messagesArray[i][2] ? 'Не' : 'Да') + boldMgsClose
		cell5.innerHTML = '<input type="checkbox" name="mgsID' + i + '" />';
	};
}

function validateMgsForm() {
	var pass = false;
	var form = document.forms['send_mgs_form'];
	switch (true) {
		case (form['mgs_to'].value == ''):
			pass = 'Избери до кой !';
			break;
		case (form['msg_from'].value == ''):
			pass = 'Избери от кой !';
			break;
		case (form['msg_text'].value == ''):
			pass = 'Няма текст на съобщението !';
			break;
		default:
			pass = true;
			return pass;
	}
	return pass;
}

function sendMgs() {
	var validateMgs = validateMgsForm() ;
	if (validateMgs !== true) {
		alert(validateMgs);
	} else {
		alert('all ok !');
	}

}

showMsgTable();

$(document).ready(function(){

	$('#table_mgs tr:not(:first-child)').hover(function(){
    	$(this).children().addClass('msg_hover');
    }, function(){
    	$(this).children().removeClass('msg_hover');
	});

});