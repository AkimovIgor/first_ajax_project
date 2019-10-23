$(document).ready(function(){

    /* ADMIN PANEL */

    // Обновление статуса коммента
    $('.update').on('click', function(e) {
        e.preventDefault();

        that = $(this);
        id = $(that).attr('data-id');

        $.ajax({
            method: 'POST',
            url: '/update.php',
            data: {id: id},
            dataType: 'json',
            success: function(response) {
                if (response['messages']) {
                    $('.alert-danger').html(response['messages']['errors']['status']);
                    $('.alert-danger').css('display', 'block');
                } else {
                    switch(response['status']) {
                        case 0: 
                            $(that).removeClass('btn-warning');
                            $(that).addClass('btn-success');
                            $(that).html('Разрешить');
                            break;
                        case 1: 
                            $(that).removeClass('btn-success');
                            $(that).addClass('btn-warning');
                            $(that).html('Запретить');
                            break;
                    }
                }
            }
        });
    });

    // Удаление коммента
    $('.delete').on('click', function(e) {
        e.preventDefault();

        that = $(this);
        id = $(that).attr('data-id');

        if (!confirm('Вы действительно хотите удалить запись?')) {
            return false;
        }
        
        

        $.ajax({
            method: 'POST',
            url: '/delete.php',
            data: {id: id},
            dataType: 'json',
            success: function(response) {
                if (response['messages']['success']) {
                    $(that).parent().parent().remove();

                    page = $('.page-item.active').find('.page-link').attr('data-href');
                    
                    if (!page) {
                        $('.alert-success').find('.text').html(response['messages']['success']);
                        $('.alert-success').css('display', 'block');
                        
                        if ($('.table').find('tbody').children().length == 0) {
                            $('.card-body').find('span').html('Комментариев пока нет.');
                        }
                        return;
                    }

                    splitPage = page.split("=");
                    number = Number(splitPage[1]) - 1;
                    newpage = splitPage[0] + "=" + String(number);

                    console.log(newpage);
                    console.log($('.table').find('tbody').children().length);
                    
                    
                    if ($('.table').find('tbody').children().length == 0) {
                        $( "#ajax-content" ).load(newpage, function() {
                            $('#app').attr('data-page', newpage);
                            $('.alert-success').find('.text').html(response['messages']['success']);
                            $('.alert-success').css('display', 'block');
                        });
                    } else {
                        $( "#ajax-content" ).load(page, function() {
                            $('#app').attr('data-page', page);
                            $('.alert-success').find('.text').html(response['messages']['success']);
                            $('.alert-success').css('display', 'block');
                        });
                    }
                }
            }
        });
    });



});