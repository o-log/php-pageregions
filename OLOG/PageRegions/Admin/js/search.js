$( document ).ready(function() {
    $('#search_form').on("change keyup input click", 'input', function() {
        var info = $( "input[name=<?=search_field_info?>]" ).val();
        var body = $( "input[name=<?=search_field_body?>]" ).val();

        if(info.length >=2 || body.length >=2)
        {
            $.ajax({
                type: 'post',
                url: "/admin/search_ajax/",
                data: {'<?=$search_field_info?>': info, '<?=$search_field_body?>': body},
                response: 'text',
                success: function (data) {
                    $(".search_result").html(data.html).fadeIn();
                }
            })
        }
    })
});