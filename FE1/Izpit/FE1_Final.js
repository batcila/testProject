
var form = document.getElementById('form-id');
var tempText;

function sendForm() {
	var bul = confirm('Данните коректни ли са? Да изпратя ли формата?');
		if (bul) {
			form.submit();
			showThanks();
		}
}

function showThanks() {
	var tdText = document.getElementById('tdID');
	tempText = tdText.innerHTML;
	tdText.innerHTML = '<div class="cent"><p>Благодарим ви !</p><input type="button" value="Обратно" onclick="getBackForm()" /></div>';
	
}

function getBackForm() {
	var tdText = document.getElementById('tdID');
	tdText.innerHTML = tempText;
}

function addOption() {
	var optEl = document.getElementById('moreOptions');
	var tempOpt = prompt('Въведете език:');
	optEl.innerHTML += '<input type="checkbox" id="'+ tempOpt +'" name="language[]" value="'+ tempOpt +'" /><label for="'+ tempOpt +'"> '+ tempOpt +' </label>';
}