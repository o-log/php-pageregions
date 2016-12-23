(function () {
	$input = $('#<?= $search_input_id ?>');
	$result = $('<div id="result" class="search_result"/>');

	$input.after($result);

	$input.on('keyup', function () {
		var query = $(this).val();
		var name = $(this).attr('name');
		var data = {};
		data[name] = query;

		if (query.length >= 2) {
			$.ajax({
				type: 'post',
				url: '<?= $search_action ?>',
				data: data,
				response: "json",
				success: function (response) {
					$result.html(response.html);
				}
			});
		} else {
			$result.empty();
		}
	});
})();