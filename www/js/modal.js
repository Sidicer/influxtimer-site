$(function() {
		$('.inf-modal').on('click', function() {
			$('#inf-img-modal-img').attr('src', $(this).find('img').attr('src'));
			$('#inf-img-modal').modal('show');
		});		
});
