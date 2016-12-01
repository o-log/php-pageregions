$( document ).ready(function() {
    $('#search_form').on("change keyup input click", 'input', function() {
        var query = $( "input[name=<?=$search_field?>]" ).val();

        if(query.length >=2) {
            $.ajax({
                type: 'post',
                url: "/admin/search_ajax/",
                data: {'<?=$search_field?>': query},
                response: "json",
                success: function (data) {
                    $(".search_result").html(data.html).fadeIn();
                }
            })
        }
    })
});