$(document).ready(function(){
    // Ссылки страниц пагинации
    $('.pagination .page-link').on('click', function(e) {
        e.preventDefault();
        page = $(this).attr('data-href');
    
        $( "#ajax-content" ).load(page, function() {
            $('#app').attr('data-page', page);
        });
    
    });

    $('.pagination .page-link').on('dblclick', function(e){
        e.preventDefault();
        return false;
    });

    // Кнопка закрытия флеш-сообщения
    $('.close').on('click', function() {
        $('.alert').hide();
    });
});