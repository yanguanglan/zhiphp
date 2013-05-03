$('.J_favs').live('click', function() {
	var $this = $(this);
	var id = $this.attr("data-id");
	$.get('index.php?m=user&a=favs_add', {
		id: id
	}, function(data) {
		$.ZhiPHP._tip({
			content: data.msg,
			status: data.status == 1
		});
	}, 'json');
});