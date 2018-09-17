document.addEventListener("DOMContentLoaded", function(){
    $( ".mediasort_category" ).multisortable({
        forcePlaceholderSize :true,
        update: function( evt, ui ) {
            var data = {
                rex_file_category: $('select[name=rex_file_category]').val(),
                sort: [],
            };
            $(".mediasort_category_image").each(function(){
                data.sort.push(this.dataset.id);
            });
            $.post(location.href, data, function(dat){
            });
        }
    });
});
