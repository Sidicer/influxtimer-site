const imgs = ['bg001.jpg', 'bg002.jpg', 'bg003.jpg', 'bg004.jpg'];

$(document).ready(function() {
	console.log('hello');
	const img = imgs[Math.floor(Math.random() * imgs.length)];
	$('#header-imgs').css('background-image', 'url(img/'+img+')');
} );
