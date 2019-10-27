$(document).ready(function() {

    // Сохраение коммента 
    $('#store').on('click', function(e) {
        e.preventDefault();

        page = $('#app').attr('data-page');

        id = $('#exampleFormControlid').val();
        text = $('#exampleFormControltext').val();
        store = $('#store').attr('name');

        data = {id: id, text: text, store: store, page: page}

        $.ajax({
            method: 'POST',
            url: '/store.php',
            dataType: 'json',
            data: data,
            success: function(response) {
                //console.log(response);
                if (response['messages']['success']) {
                    $('.alert').find('span.text').html(response['messages']['success']);
                    $('.alert').css('display', 'block');

                    $('#exampleFormControltext').parent().removeClass('has-error');
                    $('#exampleFormControltext').next('span').css('display', 'none');
                    $('#comments').find('span#no-comments').remove();

                    if (page == '/?page=1') {
                        comment = "<div class='media'><img src='" + response['image'] 
                        + "' class='mr-3' alt='...' width='64' height='64'><div class='media-body'><h5 class='mt-0'>" 
                        + response['author'] + "</h5><span><small>" 
                        + response['date'] + "</small></span><p>" 
                        + response['text'].replace(/(?:\r\n|\r|\n)/g, '<br>') + "</p></div></div>";
                        //console.log(comment);

                        $('#comments').prepend(comment);
                        //console.log($('#comments').children().length);
                        if ($('#comments').children().length > 4) {
                            $('#comments').children('.media').last().remove('.media');

                            $( "#ajax-content" ).load(page, function() {
                                $('#app').attr('data-page', page);
                                $('.alert-success').find('.text').html(response['messages']['success']);
                                $('.alert-success').css('display', 'block');
                            });
                        }
                        
                    }
                    
                    $('#exampleFormControltext').val('');

                    $("html, body").animate({scrollTop: 0}, 700);
                }
                if (response['messages']['errors']) {
                    
                    $('.alert').css('display', 'none');
                    $('#exampleFormControltext').parent().addClass('has-error');
                    $('#exampleFormControltext').next('span').find('strong').html(response['messages']['errors']['text']);
                    $('#exampleFormControltext').next('span').css('display', 'block');
                    $('#exampleFormControltext').focus();
                    
                    $("html, body").animate({scrollTop: 2000}, 0);
                }
            },
            errors: function(response) {
                //console.log(response);
            }
        });
    });
    

    // Загрузка файла
    $('#edit').on('click', function(e) {
        e.preventDefault();

        name = $('#exampleFormControlInputname').val();
        email = $('#exampleFormControlInputemail').val();
        file = $('#exampleFormControlInputfile').val();
        edit = $('#edit').attr('name');

        arrData = {name: name, email: email, edit: edit, file: file};

        $('.loader').show();

        $.ajax({
            method: "post",
            url: "/profile.php",
            data: arrData,
            enctype: 'multipart/form-data',
            dataType: "json",
            success: function (response) {
                //console.log(response);
                if (response['messages']['success']) {

                    updateProfile(1);
                    $('.alert').find('span.text').html(response['messages']['success']);
                    $('.alert').css('display', 'block');
                    $('.custom-file').next('span').css('display', 'none');


                    hideSuccess($.extend(arrData, arrData, {current: '',password: '',password_confirmation: '',password_equal: 1}));

                    

                    eraseAllFields($.extend(arrData, arrData, {current: '',password: '',password_confirmation: '',password_equal: 1}));
                    
                    $('#navbarDropdownMenuLink').text(response['name'] + ' ');
                    

                    $('#exampleFormControlInputname').val('');
                    $('#exampleFormControlInputemail').val('');
                    $('#exampleFormControlInputname').attr('placeholder', response['name']);
                    $('#exampleFormControlInputemail').attr('placeholder', response['email']);

                    $('#exampleFormControlInputfile').val('');
                    $('.custom-file-label').text('Выберите изображение');

                    if (response['name'] == 'admin') {
                        $('.dropdown-item.admin-control').show();
                    } else {
                        $('.dropdown-item.admin-control').hide();
                    }
                    
                    
                    
                    $("html, body").animate({scrollTop: 0}, 700);
                }
                if (response['messages']['errors']) {
                    updateProfile();
                    $('.loader').hide();
                    
                    $('.alert').css('display', 'none');
                    hideSuccess($.extend(arrData, arrData, {current: '',password: '',password_confirmation: '',password_equal: 1}));
                    eraseAllFields($.extend(arrData, arrData, {current: '',password: '',password_confirmation: '',password_equal: 1}));

                    if (response['messages']['errors']['file']) {
                        $('#exampleFormControlInputfile').addClass('is-invalid');
                        $('.custom-file').next('span').find('strong').html(response['messages']['errors']['file']);
                        $('.custom-file').next('span').css('display', 'block');
                        $('#exampleFormControlInputfile').focus();
                    } else {
                        $('#exampleFormControlInputfile').removeClass('is-invalid');
                        $('.custom-file').next('span').css('display', 'none');
                        printAllErrors(response['messages']['errors']);
                    }
                    
                    $("html, body").animate({scrollTop: 0}, 0);
                }
            }
        });
    });

    // Изменение файла
    $('#exampleFormControlInputfile').on('change', function() {
        
        
        hideSuccess({name: 'name', email: 'email', file: 'file'});
        eraseAllFields({name: 'name', email: 'email', file: 'file'});
        if ($('#exampleFormControlInputfile').val()) {
            val = 'Выбран файл: ' + $(this)[0].files[0]['name'];
            $('.custom-file-label').html(val);
        } else {
            $('.custom-file-label').text('Выберите изображение');
        }
        
        
        
    });

    // Вывод всех мини-ошибок
    function printAllErrors(respData) {
        // respData = Object.assign([], respData).reverse();
        firstError = null;

        for (field in respData) {
            $('#exampleFormControlInput' + field).addClass('is-valid');
        }

        for (error in respData) {
            if (!firstError) firstError = $('#exampleFormControlInput' + error).focus();
            //console.log(firstError);
            if (error) {
                $('#exampleFormControlInput' + error).addClass('is-invalid');
                $('#exampleFormControlInput' + error).next('span').find('strong').html(respData[error]);
                $('#exampleFormControlInput' + error).next('span').css('display', 'block');
            }
        }
    }


    // Редактирование профиля
    function updateProfile(status) {

        var myformData = new FormData();

        if (status == 1) {
            if ($('#exampleFormControlInputfile')[0].files['length']) {
                file = $('#exampleFormControlInputfile')[0].files[0];
                myformData.append('file', file);
            }
        }
        

        $.ajax({
            url: '/upload.php',
            method: 'post',
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: myformData,
            enctype: 'multipart/form-data',
            
            success: function(response){
                //console.log(response);
                //console.log("файл успешно отправлен", response);
                $('#user-image').attr('src', response['userImage']);
                $('.loader').hide();
                
            },
            error: function(response){
                //console.log(response);
                //console.log("нет файла для отправки");
                $('.loader').hide();
            }
        });
    }

    // Очищение ошибок в полях
    function eraseAllFields(data) {
        for (field in data) {
            $('#exampleFormControlInput' + field).next('span').css('display', 'none');
            $('#exampleFormControlInput' + field).removeClass('is-invalid');
        }
    }

    // скрытие рамки у правильных полей
    function hideSuccess(data) {
        for (field in data) {
            $('#exampleFormControlInput' + field).removeClass('is-valid');
        }
    }

    // Кнопка закрытия флеш-сообщения
    $('.close').on('click', function() {
        $('.alert').hide();
    });


    // Изменение пароля
    $('#edit-passw').on('click', function(e) {
        e.preventDefault();

        current = $('#exampleFormControlInputcurrent').val();
        password = $('#exampleFormControlInputpassword').val();
        password_confirm = $('#exampleFormControlInputpassword_confirmation').val();
        edit = $('#edit-passw').attr('name');

        data = {
            current: current, 
            password: password, 
            password_confirmation: password_confirm,
            password_equal: 1,
            edit: edit
        };
        
        $.ajax({
            type: "POST",
            url: "password.php",
            data: data,
            dataType: "json",
            success: function (response) {
                //console.log(response);
                if (response['messages']['success']) {
                    $('.alert').find('span.text').html(response['messages']['success']);
                    $('.alert').css('display', 'block');
                    

                    $('.custom-file-label').text('Выберите изображение');


                    hideSuccess($.extend(data, data, {name: 'name', email: 'email', file: 'file'}));
                    eraseAllFields($.extend(data, data, {name: 'name', email: 'email', file: 'file'}));
                    
                    
                    $('#exampleFormControlInputcurrent').val('');
                    $('#exampleFormControlInputpassword').val('');
                    $('#exampleFormControlInputpassword_confirmation').val('');
                    
                    $("html, body").animate({scrollTop: 0}, 700);

                }
                if (response['messages']['errors']) {
                    
                    $('.alert').css('display', 'none');

                    eraseAllFields($.extend(data, data, {name: 'name', email: 'email', file: 'file'}));

                    printAllErrors(response['messages']['errors']);

                    if (response['messages']['errors']['password_equal']) {
                        $('#exampleFormControlInputpassword').addClass('is-invalid');
                        $('#exampleFormControlInputpassword_confirmation').addClass('is-invalid');
                        $('#exampleFormControlInputpassword').next('span').find('strong').html(response['messages']['errors']['password_equal']);
                        $('#exampleFormControlInputpassword').next('span').css('display', 'block');
                        $('#exampleFormControlInputpassword_confirmation').focus();
                    }
                    $("html, body").animate({scrollTop: 1100}, 0);    
                }
            }
        });
    });


    // Выбор темы
    $('.theme-item').on('click', function(e){
        e.preventDefault();

        if ($('body').attr('data-theme') != "") {
            currentTheme = $('body').attr('data-theme');
        }
        
        theme = $(this).html();

        data = {theme: theme};

        $.ajax({
            method: 'POST',
            url: '/change-theme.php',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (currentTheme) $('head').children('link').last().remove('link');
                $('head').append('<link href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/' + response['themeName'] + '/bootstrap.min.css" rel="stylesheet">');
            }
        });
    });


    // Регистрация
    $('#register').on('click', function(e) {
        e.preventDefault();

        name = $('#exampleFormControlInputname').val();
        email = $('#exampleFormControlInputemail').val();
        password = $('#exampleFormControlInputpassword').val();
        password_confirmation = $('#exampleFormControlInputpassword_confirmation').val();
        submit = $('#register').attr('id');

        arrData = {name: name, email: email, password: password, password_confirmation: password_confirmation, password_equal: 'password_equal', submit: submit};

        $.ajax({
            method: "post",
            url: "/register.php",
            data: arrData,
            dataType: "json",
            success: function (response) {
                //console.log(response);
                if (response['messages']['success']) {
                    
                    eraseAllFields(arrData);
                    
                    $('#navbarDropdownMenuLink').text(response['name'] + ' ');

                    $('#exampleFormControlInputname').val('');
                    $('#exampleFormControlInputemail').val('');

                    window.location.href = '/';
                }
                if (response['messages']['errors']) {
                    
                    eraseAllFields(arrData);

                    printAllErrors(response['messages']['errors']);
                    $('#exampleFormControlInputpassword_confirmation').val('');
                    
                    $("html, body").animate({scrollTop: 0}, 0);
                }
            }
        });
    });


    // Авторизация
    $('#login').on('click', function(e) {
        e.preventDefault();

        email = $('#exampleFormControlInputemail').val();
        password = $('#exampleFormControlInputpassword').val();
        remember = $('#exampleFormControlInputremember').prop("checked");
        submit = $('#login').attr('id');

        arrData = {email: email, password: password, remember: remember, submit: submit};

        $.ajax({
            method: "post",
            url: "/login.php",
            data: arrData,
            dataType: "json",
            success: function (response) {
                //console.log(response);
                if (response['messages']['success']) {

                    $("html, body").animate({scrollTop: 0}, 700);

                    eraseAllFields(arrData);
                    
                    $('#navbarDropdownMenuLink').text(response['name'] + ' ');

                    window.location.href = '/';
                }
                if (response['messages']['errors']) {
                    eraseAllFields(arrData);
                    printAllErrors(response['messages']['errors']);
                    $("html, body").animate({scrollTop: 0}, 0);
                }
            }
        });
    });
});


