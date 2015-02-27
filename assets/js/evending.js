$( document ).ready(function() {
    var base_url = 'http://localhost/evending/';

    jQuery.validator.addMethod("Person",function(value,element){
        return this.optional(element) || /^[^\`\~\!\@\#\$\%\^\&\*\(\)\_\+\-\=\+\{\}\[\]\;\'\\\:\"\|\<\>\?\,\.\/\s[0-9]]{2,32}$/.test(value);
    },"Wrong!");

    jQuery.validator.addMethod("LogNamPass",function(value,element){
        return this.optional(element) || /^[^\`\~\!\#\$\%\^\(\)\+\-\=\+\{\}\[\]\;\'\\\:\"\|\<\>\?\,\/\s]{4,32}$/.test(value);
    },"Wrong!");

    jQuery.validator.addMethod("Company",function(value,element){
        return this.optional(element) || /^[^\`\~\!\#\$\%\^\(\)\+\-\=\+\{\}\[\]\;\'\\\:\"\|\<\>\?\,\/]{2,64}$/.test(value);
    },"Wrong!");

    jQuery.validator.addMethod("GreaterThan",function(value,element,param){
        return this.optional(element) || value > param;
    },"Wrong!");

    //Register
    $('.register_form button').click(function(e){
        e.preventDefault();

        var self = this;
        
        $(self).closest("form").validate({
            rules:{
                Company: { required: true, Company: true },
                FirstName: { required: true, Person: true },
                LastName: { required: true, Person: true },
                LoginName: { required: true, LogNamPass:true },
                Password: { required: true, LogNamPass:true },
                Password2:{ equalTo: "#pass" },
            },
            messages:{
                Company: 'Въведете име на фирма',
                FirstName: 'Въведете валидно име',
                LastName: 'Въведете валидна фамилия',
                LoginName: 'Въведете валидно потребителско име',
                Password: 'Въведете валидна парола',
                Password2: 'Потвърдете паролата',
            },
        });

        var form_valid = $(self).closest("form").valid();
            
        if(form_valid){
            var data = $(self).closest("form").serialize();

            console.log(data);
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: base_url + 'general/AddUser/',
                data: data,
                success:function(result){
                    console.log(result);
                    if(result.success == true){
                        $('.public').find('.warning').html('<strong>Регистрацията премина успешно!</strong><p>Можете да влезнете от <a href="'+ base_url +'">началната страница</a>.</p>');
                        $('.public').find('.directions').html('');
                    }
                    if(result.success == false){
                        if(result.warning == "username"){
                            $('.public').find('.warning').html('<strong>Потребителското име е заето!</strong>');
                        }
                    }
                }
            });
        }
    });

    //Login
    $('.login_form button').click(function(e){
        e.preventDefault();
        var self = this;
        
        $(self).closest("form").validate({
            rules:{
                LoginName: { required: true, LogNamPass:true },
                Password: { required: true, LogNamPass:true },
            },
            messages:{
                LoginName: 'Въведете валидно потребителско име',
                Password: 'Въведете валидна парола',
            },
        });

        var form_valid = $(self).closest("form").valid();
            
        if(form_valid){
            var data = $(self).closest('form').serialize();

            $.ajax({
                dataType: 'json',
                method: 'post',
                url: base_url + 'general/Authentication/',
                data: data,
                success:function(result){
                    if(result.success == true){
                        $(location).attr('href',base_url);
                    }
                    if(result.success == false){
                        $('.login').find('.wrong_login').html('Грешна информация');
                    }
                }
            });
        }
    });

    //Show add storage form
    $('.content a.add_storage').click(function(e){
        e.preventDefault();

        $('.content').find('.storages_list').hide();
        $('.content').find('.add_storage_form').show();
    })

    //Add storage
    $('.add_storage_form button').click(function(e){
        e.preventDefault();

        $(this).closest('form').validate({
            rules:{
                Name: { required: true, Company: true },
                Address: { Company: true },
                Type: { GreaterThan: 0 },
            },
            messages:{
                Name: 'Името не е валидно',
                Address: 'Некоректно въведен адрес',
                Type: 'Изберете вид',
            },
        })

        var form_valid = $(this).closest('form').valid();

        if(form_valid){
            var data = $(this).closest('form').serialize();

            $.ajax({
                dataType: 'json',
                method: 'post',
                url: base_url + 'admin/AddStorage',
                data: data,
                success:function(result){
                    console.log(result);
                    if(result.success == true){
                        $('.content').find('.warning').html('<p>Складът беше добавен успешно!</p>');
                    }
                    if(result.success == false){
                        $('.content').find('.warning').html('<p>Възникна грешка. Опитайте отново.</p>')
                    }
                }
            });
        }
    });

    //Show add product form
    $('.content a.add_producttype').click(function(e){
        e.preventDefault();

        $('.content').find('.warning').html('');
        $('.content').find('.add_producttype_form').show();
    });
    //$('.nav a.admin').click(function(e){
    //    e.preventDefault();
    //
    //    if(!$(this).hasClass('active')){
    //        $(this).parent('.nav').find('.active').removeClass('active');
    //        var page_name = $(this).attr('page-name');
    //        console.log(page_name);
    //        $(this).addClass('active');
    //    }
    //})
});