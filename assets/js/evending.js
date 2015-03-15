$( document ).ready(function() {
    var base_url = 'http://localhost/evending/';

    jQuery.validator.addMethod("Person",function(value,element){
        return this.optional(element) || /^[^\`\~\!\@\#\$\%\^\&\*\(\)\_\+\-\=\+\{\}\[\]\;\'\\\:\"\|\<\>\?\,\.\/\s0-9]{2,32}$/.test(value);
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

    jQuery.validator.addMethod("Price",function(value,element){
        return this.optional(element) || /^[0-9]+(\.|,)[0-9]{2}$/.test(value);
    },"Wrong!");

    //Register
    $('.register_form button').click(function(e){
        e.preventDefault();

        var this_form = $(this).closest("form");
        
        this_form.validate({
            rules:{
                Company: { required: true, Company: true },
                FirstName: { required: true, Person: true },
                LastName: { required: true, Person: true },
                LoginName: { required: true, LogNamPass:true },
                Password: { required: true, LogNamPass:true },
                Password2:{ equalTo: "#pass" }
            },
            messages:{
                Company: 'Въведете име на фирма',
                FirstName: 'Въведете валидно име',
                LastName: 'Въведете валидна фамилия',
                LoginName: 'Въведете валидно потребителско име',
                Password: 'Въведете валидна парола',
                Password2: 'Потвърдете паролата'
            },
        });

        var form_valid = this_form.valid();
            
        if(form_valid){
            var data = this_form.serialize();

            $.ajax({
                dataType: 'json',
                method: 'post',
                url: base_url + 'general/AddUser/',
                data: data,
                success:function(result){
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

        var this_form = $(this).closest("form");

        this_form.validate({
            rules:{
                LoginName: { required: true, LogNamPass:true },
                Password: { required: true, LogNamPass:true }
            },
            messages:{
                LoginName: 'Въведете валидно потребителско име',
                Password: 'Въведете валидна парола'
            },
        });

        var form_valid = this_form.valid();
            
        if(form_valid){
            var data = this_form.serialize();

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

        $('.content').find('a.add_storage').hide();
        $('.content').find('.list').hide();
        $('.content').find('.add_storage_form').show();
    })

    //Add storage
    $('.add_storage_form button').click(function(e){
        e.preventDefault();

        var this_form = $(this).closest('form');

        this_form.validate({
            rules:{
                Name: { required: true, Company: true },
                Address: { Company: true },
                Type: { GreaterThan: 0 }
            },
            messages:{
                Name: 'Името не е валидно',
                Address: 'Некоректно въведен адрес',
                Type: 'Изберете вид'
            },
        })

        var form_valid = this_form.valid();

        if(form_valid){
            var data = this_form.serialize();

            $.ajax({
                dataType: 'json',
                method: 'post',
                url: base_url + 'admin/AddStorage',
                data: data,
                success:function(result){
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

    //Show add product type form
    $('.content a.add_product_type').click(function(e){
        e.preventDefault();

        $('.content').find('.add_product_type').hide();
        $('.content').find('.list').hide();
        $('.content').find('.add_product_type_form').show();
    });

    //Add product type
    $('.add_product_type_form button').click(function(e){
        e.preventDefault();

        var this_form = $(this).closest("form");

        this_form.validate({
            rules:{
                Name: { required: true, Company: true },
                Category: { GreaterThan: 0 },
                Price: { required: true, Price: true },
                ExpirationTime: { required: true, digits: true }
            },
            messages:{
                Name: 'Въведеното име е некоректно',
                Category: 'Изберете категория',
                Price: 'Въведете валидна цена',
                ExpirationTime: 'Въведете цяло число (дни)'
            }
        });

        var form_valid = this_form.valid();

        if(form_valid){
            var data = this_form.serialize();

            $.ajax({
                method: 'post',
                dataType: 'json',
                url: base_url + 'admin/AddProductType',
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('.content').find('.warning').html('Операцията е успешна!');
                    }
                    if(result.success == false){
                        $('.content').find('.warning').html('<p>Възникна грешка! Моля опитайте отново</p>');
                    }
                }
            });
        }
    })

    //Storage supply
    $('.supply_form button').click(function(e){
        e.preventDefault();

        var this_form = $(this).closest("form");

        this_form.validate({
            rules:{
                Storage: { GreaterThan: 0 },
                Product: { GreaterThan: 0 },
                Quantity: { required: true, digits: true }
            },
            messages:{
                Storage: 'Изберете склад',
                Product: 'Изберете продукт',
                Quantity: 'Изберете количество'
            }
        });

        var form_valid = this_form.valid();

        if(form_valid){
            var data = this_form.serialize();

            $.ajax({
                method: 'post',
                dataType: 'json',
                url: base_url + 'admin/StorageSupply',
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('.content').find('.warning').html('Операцията е успешна!');
                    }
                    if(result.success == false){
                        $('.content').find('.warning').html('<p>Възникна грешка! Моля опитайте отново</p>');
                    }
                }
            });
        }
    })

    $('.storage_container a.storage_availability').click(function(e){
        e.preventDefault();

        var storage_id = $(this).attr('storage-id');
        var html = '';

        $.ajax({
            method: 'post',
            dataType: 'json',
            url: base_url + 'admin/GetStorageAvailability',
            data: { "StorageId": storage_id },
            success: function(result){
                if(result.success == true){
                    html += '<div class="list" style="display: block">';
                    html += '<div class="container"><div class="column first_column">Продукт (срок на годност)</div><div class="column last_column">Количество</div></div>';
                    $.each(result.aStorageAvailability,function(index,value){
                        html += '<div class="container"><div class="column first_column" product-id="' + index + '">' + value.aProduct.oType.Name + ' (' +value.aProduct.oData.ExpirationDate + ')</div><div class="column last_column">' + value.iQuantity + '</div></div>';
                    })
                    html += '<div class="directions"><a href="">Обратно</a></div></div>';

                    $('.content').html(html);
                }
                if(result.success == false){
                    $('.content').find('.warning').html('<p>Възникна грешка! Моля опитайте отново</p>');
                }
            }
        })
    })

    //$('.nav a.admin').click(function(e){
    //    e.preventDefault();
    //
    //    if(!$(this).hasClass('active')){
    //        $(this).parent('.nav').find('.active').removeClass('active');
    //        var page_name = $(this).attr('page-name');
    //        $(this).addClass('active');
    //    }
    //})

});