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
                        $('.public').find('.warning').html('<strong>Регистрацията премина успешно!</strong><p class="request_success">Можете да влезнете от <a href="'+ base_url +'">началната страница</a>.</p>');
                        $('.public').find('.directions').html('');
                    }
                    if(result.success == false){
                        if(result.warning == "username"){
                            $('.public').find('.warning').html('<p class="request_failure">Потребителското име е заето!</p>');
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
                        $('.content').find('.warning').html('<p class="request_success">Хранилището беше добавено успешно!</p>');
                    }
                    if(result.success == false){
                        $('.content').find('.warning').html('<p class="request_failure">Възникна грешка! Опитайте отново.</p>')
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
                        $('.content').find('.warning').html('<p class="request_success">Операцията е успешна!</p>');
                    }
                    if(result.success == false){
                        $('.content').find('.warning').html('<p class="request_failure">Възникна грешка! Опитайте отново</p>');
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
                Quantity: { required: true, digits: true, GreaterThan: 0 }
            },
            messages:{
                Storage: 'Изберете хранилище',
                Product: 'Изберете продукт',
                Quantity: 'Посочете количество'
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
                        $('.content').find('.warning').html('<p class="request_success">Операцията е успешна!</p>');
                    }
                    if(result.success == false){
                        $('.content').find('.warning').html('<p class="request_failure">Възникна грешка! Опитайте отново</p>');
                    }
                }
            });
        }
    })

    //Storage supply category filter
    $('.supply_form select[name="Category"]').change(function(){
        var selected_category_id = $(this).find('option:selected').val();

        var products_html = '';
        if(selected_category_id > 0){
            $.ajax({
                method: 'post',
                dataType: 'json',
                url: base_url + 'admin/GetProductTypesByCategoryId',
                data: { "iCategoryId" : selected_category_id },
                success:function(result){
                    if(result.success == true){
                        products_html += '<option value="0">Тип изделие</option>';
                        $.each(result.aTypes,function(key,value){
                            products_html += '<option value="'+ value.Id +'">'+ value.Name +'</option>'
                        })

                        $('.supply_form').find('select[name="ProductType"]').html(products_html);
                        $('.content').find('.warning').html('');
                    }
                    if(result.success == false){
                        $('.content').find('.warning').html('<p class="request_failure">'+ result.message +'</p>');
                        $('.supply_form').find('select[name="ProductType"]').html('<option value="0">Тип изделие</option>');
                    }
                }
            });
        } else {
            $('.content').find('.warning').html('');
            $('.supply_form').find('select[name="ProductType"]').html('<option value="0">Тип изделие</option>');
        }
    })

    //storage availability
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
                    var iCounter = 0;
                    var sColor = '';
                    html += '<div class="list" style="display: block">';
                    html += '<div class="container"><div class="column first_column">Продукт (срок на годност)</div><div class="column last_column">Количество</div></div>';
                    $.each(result.aStorageAvailability,function(index,value){
                        if(iCounter % 2 == 0){
                            sColor = 'DDF5B7';
                        } else {
                            sColor = 'FFFF99';
                        }
                        iCounter += 1;
                        html += '<div class="container" style="background-color: #' + sColor + ';"><div class="column first_column" product-id="' + value.oData.Id + '">' + value.oType.Name + ' (' +value.oData.ExpirationDate + ')</div><div class="column last_column">' + value.iQuantity + '</div></div>';
                    })
                    html += '<div class="directions"><a href="">Обратно</a></div></div>';

                    $('.content').html(html);
                }
                if(result.success == false){
                    $('.content').find('.warning').html('<p class="request_failure">'+ result.message +'</p>');
                }
            }
        })
    })

    //distribution select options
    $('.distribution_form select[name="Storage1"]').change(function(){
        var iSelectedStorageId = $(this).val();
        var second_storage_html = '';
        var products_html = '';

        //other storages
        $.ajax({
            method: 'post',
            dataType: 'json',
            url: base_url + 'member/GetRemainingStorages',
            data: { "iSelectedStorageId" : iSelectedStorageId },
            success:function(result){
                if(result.success == true){
                    second_storage_html += '<option value="0">Към</option>';
                    $.each(result.aRemainingStorages,function(index,value){
                        second_storage_html += '<option value="' + value.Id + '">' + value.Name + '</option>';
                    });

                    $('.distribution_form').find('select[name="Storage2"]').html(second_storage_html);
                }
            }
        });

        //products
        $.ajax({
            method: 'post',
            dataType: 'json',
            url: base_url + 'admin/GetStorageAvailability',
            data: { "StorageId" : iSelectedStorageId },
            success:function(result){
                if(result.success == true){
                    products_html += '<option value="0">Изделие</option>';
                    $.each(result.aStorageAvailability,function(index,value){
                        products_html += '<option value="' + value.oData.Id + '" product-quantity="'+ value.iQuantity +'">' + value.oType.Name + ' (' + value.oData.ExpirationDate + ')</option>';
                    });

                    $('.distribution_form').find('select[name="Product"]').html(products_html);
                }
            }
        });
    })

    //distribution quantities
    $('.distribution_form select[name="Product"]').change(function(){
        var iQuantity = $(this).find('option:selected').attr('product-quantity');

        $('.distribution_form').find('input[name="Quantity"]').val(iQuantity);
    })

    //distribution submit
    $('.distribution_form button').click(function(e){
        e.preventDefault();

        var this_form = $(this).closest('form');

        this_form.validate({
            rules:{
                Storage1: { GreaterThan: 0 },
                Storage2: { GreaterThan: 0 },
                Product: { GreaterThan: 0 },
                Quantity: { required: true, digits: true, GreaterThan: 0 }
            },
            messages:{
                Storage1: 'Изберете хранилище',
                Storage2: 'Изберете хранилище',
                Product: 'Изберете продукт',
                Quantity: 'Посочете количество'
            }
        });

        var form_valid = this_form.valid();

        if(form_valid){
            var data = this_form.serialize();

            $.ajax({
                method: 'post',
                dataType: 'json',
                url: base_url + 'member/Distribute',
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('.content').find('.warning').html('<p class="request_success">'+ result.message +'</p>');
                    }
                    if(result.success == false){
                        $('.content').find('.warning').html('<p class="request_failure">'+ result.message +'</p>');
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
    //        $(this).addClass('active');
    //    }
    //})

});