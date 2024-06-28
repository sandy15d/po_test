<!DOCTYPE HTML>
<html lang="en_UK">
<head>
    <script>
window.onload = function() {
var canvas = document.getElementById('outerspace');
if (canvas.getContext){
var ctx = canvas.getContext('2d');
// Define the gradient
var mygrad = ctx.createLinearGradient(300,100,640,480);
mygrad.addColorStop(0, '#6c88ba');
mygrad.addColorStop(0.9, 'rgba(0,0,0,0.5)');
mygrad.addColorStop(1, 'rgba(0,0,0,1)');
//Create a drop shadow
ctx.shadowOffsetX = 5;
ctx.shadowOffsetY = 10;
ctx.shadowBlur = 20;
ctx.shadowColor = "black";
// Draw the circle
ctx.fillStyle = mygrad;
ctx.beginPath();
ctx.arc(320,240,200,0,Math.PI*2,true);
ctx.closePath();
ctx.fill();
};
</script>
<meta charset="UTF-8">
<title>Canvas</title>
</head>
<body>
<canvas width="640" height="480" id="outerspace"></canvas>
</body>
</html>