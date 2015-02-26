$( document ).ready(function() {
    var base_url = 'http://localhost/evending/';
    
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
        
        jQuery.validator.addMethod("Company",function(value,element){
            return this.optional(element) || /^[^\`]{2,64}$/.test(value);
        },"Wrong!");
        
        jQuery.validator.addMethod("Person",function(value,element){
            return this.optional(element) || /^[^\`\~\!\@\#\$\%\^\&\*\(\)\_\+\-\=\+\{\}\[\]\;\'\\\:\"\|\<\>\?\,\.\/\s]{2,32}$/.test(value);
        },"Wrong!");

        jQuery.validator.addMethod("LogNamPass",function(value,element){
            return this.optional(element) || /^[^\`\~\!\#\$\%\^\(\)\+\-\=\+\{\}\[\]\;\'\\\:\"\|\<\>\?\,\/\s]{4,32}$/.test(value);
        },"Wrong!");
        
        var form_valid = $(self).closest("form").valid();
            
        if(form_valid){
            var data = $(self).closest('form').serialize();

            $.ajax({
                dataType: 'json',
                method: 'post',
                url: base_url + 'general/AddUser/',
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('.homepage').find('.warning').html('<strong>Регистрацията премина успешно!</strong><p>Можете да влезнете от <a href="'+ base_url +'">началната страница</a>.</p>');
                        $('.homepage').find('.directions').html('');
                    }
                    if(result.success == false){
                        if(result.warning == "username"){
                            $('.homepage').find('.warning').html('<strong>Потребителското име е заето!</strong>');
                        }
                    }
                }
            });
        }
    });
    
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
        
        jQuery.validator.addMethod("Name",function(value,element){
            return this.optional(element) || /^[^\`\~\!\@\#\$\%\^\&\*\(\)\_\+\-\=\+\{\}\[\]\;\'\\\:\"\|\<\>\?\,\.\/\s]{2,32}$/.test(value);
        },"Wrong!");

        jQuery.validator.addMethod("LogNamPass",function(value,element){
            return this.optional(element) || /^[^\`\~\!\#\$\%\^\(\)\+\-\=\+\{\}\[\]\;\'\\\:\"\|\<\>\?\,\/\s]{4,32}$/.test(value);
        },"Wrong!");
        
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
                        $('.homepage').find('.wrong_login').html('Грешна информация');
                    }
                }
            });
        }
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