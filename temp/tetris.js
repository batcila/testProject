var tetrBoard = [];
var canvas = document.getElementById("canvas");
var ctx = canvas.getContext("2d");
var intervalId;


function addElementsToBoard() {
	var tempArray = [];
	for (var x=0; x < 10; x++ ) {
		for (var y=0; y<20; y++ ) {
			tempArray.push(getRandomColor());
		}
		tetrBoard.push(tempArray);
		tempArray = [];
	}
}

function randomizeBoard() {
	for (var x=0; x < 10; x++ ) {
		for (var y=0; y<20; y++ ) {
			tetrBoard[x][y] = getRandomColor();
		}
	}
	drawBoard();
}

function drawSquare(x,y,col) {
	var cordX = x * 40;
	var cordY = y * 40;
	ctx.fillStyle = col; // getRandomColor();
	ctx.fillRect(cordX, cordY, 40, 40);
	ctx.fillStyle = 'rgba(255, 255, 255, .3)';
	ctx.beginPath();
	ctx.moveTo(cordX, cordY);
	ctx.lineTo(cordX + 40, cordY);
	ctx.lineTo(cordX + 32, cordY + 8);
	ctx.lineTo(cordX + 8, cordY + 8);
	ctx.lineTo(cordX + 8, cordY + 32);
	ctx.lineTo(cordX, cordY + 40);
	ctx.closePath();
	ctx.fill();   	
	ctx.fillStyle = 'rgba(0, 0, 0, .3)';
	ctx.beginPath();
	ctx.moveTo(cordX + 40, cordY + 40);
	ctx.lineTo(cordX , cordY + 40);
	ctx.lineTo(cordX + 8, cordY + 32);
	ctx.lineTo(cordX + 32, cordY + 32);
	ctx.lineTo(cordX + 32, cordY + 8);
	ctx.lineTo(cordX + 40, cordY);
	ctx.closePath();
	ctx.fill();
}

function drawBoard() {
	for ( xx = 0; xx < 10; xx++ ) {
		for ( yy = 0; yy < 20; yy++ ) {
			drawSquare(xx,yy,tetrBoard[xx][yy]);
		}
	}
}

function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++ ) {
       color += letters[Math.floor(Math.random() * 16)];
    }    
    return color; 
}

function startColor() {
	intervalId = setInterval(function(){randomizeBoard()}, 50);
	var but = document.getElementById('startButton');
	but.innerHTML = 'Stop';
	but.setAttribute('onclick','stopColor()')
}

function stopColor() {
	clearInterval(intervalId);
	var but = document.getElementById('startButton');
	but.innerHTML = 'Start';
	but.setAttribute('onclick','startColor()')
}

addElementsToBoard();

// window.setTimeout(function(){drawBoard()},50);