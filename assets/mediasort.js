document.addEventListener("DOMContentLoaded", function(){
    
    function sortByName(dir){
        $('.mediasort_category_image').detach().sort(function(a,b) {
             return dir * a.dataset.originalname.localeCompare(b.dataset.originalname);
        }).appendTo($('#mediasort_category'));
        submitNewSorting();
        $( "#mediasort_category" ).multisortable({
            stop: submitNewSorting
        });
    }
    function submitNewSorting(){
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
    $("#sort_by_name_asc").on('click', function(){
        sortByName(1);
    });
    $("#sort_by_name_desc").on('click', function(){
        sortByName(-1);
    });
    $( "#mediasort_category" ).multisortable({
        stop: submitNewSorting
    });
});
