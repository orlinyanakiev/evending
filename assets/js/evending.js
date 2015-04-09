$( document ).ready(function() {
    var base_url = 'http://localhost/evending/';

    jQuery.validator.addMethod("Person",function(value,element){
        return this.optional(element) || /^[^\`\~\!\@\#\$\%\^\&\*\(\)\_\+\-\=\+\{\}\[\]\;\'\\\:\"\|\<\>\?\,\.\/\s0-9]{2,32}$/.test(value);
    },"Wrong!");

    jQuery.validator.addMethod("LogNamPass",function(value,element){
        return this.optional(element) || /^[^\`\~\!\#\$\%\^\(\)\+\-\=\+\{\}\[\]\;\'\\\:\"\|\<\>\?\,\/\s]{4,32}$/.test(value);
    },"Wrong!");

    jQuery.validator.addMethod("Address",function(value,element){
        return this.optional(element) || /^[^\`\~\!\#\$\%\^\(\)\+\-\=\+\{\}\[\]\;\'\\\:\"\|\<\>\?\,\/]{2,64}$/.test(value);
    },"Wrong!");

    jQuery.validator.addMethod("GreaterThan",function(value,element,param){
        return this.optional(element) || value > param;
    },"Wrong!");

    jQuery.validator.addMethod("Price",function(value,element){
        return this.optional(element) || /^[0-9]+\.[0-9]{2}$/.test(value);
    },"Wrong!");

    jQuery.validator.addMethod("ExpirationDate",function(value,element){
        return this.optional(element) || /^[01-31]\.[01-31]\.[2015-9999]$/.test(value);
    },"Wrong!");

    //Register user
    $('.register_form button').click(function(e){
        e.preventDefault();

        var this_form = $(this).closest("form");
        
        this_form.validate({
            rules:{
                FirstName: { required: true, Person: true },
                LastName: { required: true, Person: true },
                LoginName: { required: true, LogNamPass:true },
                Password: { required: true, LogNamPass:true },
                Password2:{ equalTo: "#pass" }
            },
            messages:{
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

    //delete user
    $('.manage_users').off('click').on('click','.delete_user',function(e){
        e.preventDefault();

        if(confirm('Сигурни ли сте, че желаете да изтриете този потребител?')){
            var user_id = $(this).closest('.user_container').attr('user-id');

            $.ajax({
                method: 'post',
                dataType: 'json',
                url: base_url + 'admin/DeleteUser',
                data: { "iUserId" : user_id },
                success:function(result){
                    if(result.success == true){
                        $('.content').find('.user_container').attr('user-id',user_id).remove();
                    }
                }
            })
        }
    });

    //Edit user
    $('.manage_users').off('click').on('click','.edit_user', function (e) {
        e.preventDefault();
        var user_id = $(this).closest('.user_container').attr('user-id');

        $.ajax({
            method: 'post',
            dataType: 'json',
            url: base_url + 'admin/GetUser',
            data: { "iUserId" : user_id },
            success:function(result){
                if(result.success == true){
                    var select_html = '';
                    $('.edit_user_form').find('[name="UserId"]').val(result.oUser.Id);
                    $('.edit_user_form').find('[name="FirstName"]').val(result.oUser.FirstName);
                    $('.edit_user_form').find('[name="LastName"]').val(result.oUser.LastName);
                    $('.edit_user_form').find('[name="LoginName"]').val(result.oUser.LoginName);

                    $.each(result.aUserTypes,function(index,value){
                        if(result.oUser.Type == index){
                            select_html += '<option selected value="'+ index +'">'+ value +'</option>';
                        } else {
                            select_html += '<option value="'+ index +'">'+ value +'</option>';
                        }
                    });

                    $('.edit_user_form').find('[name="Type"]').html(select_html);
                    $('.list').hide();
                    $('.edit_user_form').show();
                }
                if(result.success == false){
                    $('.content').find('.warning').html('<p class="request_failure">'+ result.message +'</p>');
                }
            }
        })
    });

    //Save edited user
    $('.edit_user_form').off('click').on('click', 'button', function(e){
        e.preventDefault();

        var this_form = $(this).closest("form");

        this_form.validate({
            rules:{
                UserId: { required: true, digits: true, GreaterThan: 0 },
                FirstName: { required: true, Person: true },
                LastName: { required: true, Person: true },
                LoginName: { required: true, LogNamPass:true },
                Type: { required: true, digits: true }
            },
            messages:{
                FirstName: 'Въведете валидно име',
                LastName: 'Въведете валидна фамилия',
                LoginName: 'Въведете валидно потребителско име'
            }
        });

        var form_valid = this_form.valid();

        if(form_valid){
            var data = this_form.serialize();

            $.ajax({
                dataType: 'json',
                method: 'post',
                url: base_url + 'admin/EditUser/',
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('.content').find('.warning').html('<p class="request_success">Оперецията е успешна!</p>');
                        setTimeout(function(){
                            window.location.reload();
                        },1000);
                    }
                    if(result.success == false){
                        $('.content').find('.warning').html('<p class="request_failure">Опитайте отново!</p>');
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
                Name: { required: true, Address: true },
                Address: { Address: true },
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
                        setTimeout(function(){
                            window.location.reload();
                        },1000);
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
                Name: { required: true, Address: true },
                Category: { GreaterThan: 0 },
                Price: { required: true, Price: true },
                ProductionPrice: { required: true, Price: true }
            },
            messages:{
                Name: 'Въведеното име е некоректно',
                Category: 'Изберете категория',
                Price: 'Въведете валидна цена',
                ProductionPrice: 'Въведете валидна цена'
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
                        setTimeout(function(){
                            window.location.reload();
                        },1000);
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
                Quantity: { required: true, digits: true, GreaterThan: 0 },
                ExpirationDate: { required: true/*, ExpirationDate: true*/ }
            },
            messages:{
                Storage: 'Изберете хранилище',
                Product: 'Изберете продукт',
                Quantity: 'Посочете количество',
                ExpirationDate: 'Въведете валидна дата'
            }
        });

        var form_valid = this_form.valid();

        if(form_valid){
            var data = this_form.serialize();

            $.ajax({
                method: 'post',
                dataType: 'json',
                url: base_url + 'member/StorageSupply',
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('.content').find('.warning').html('<p class="request_success">Операцията е успешна!</p>');
                        setTimeout(function(){
                            window.location.reload();
                        },1000);
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
            url: base_url + 'member/AjaxGetStorageAvailability/' + storage_id,
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

        GetRemainingStorages(iSelectedStorageId);
        GetStorageProducts(iSelectedStorageId);
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
                        $('.distribution_form').find('[name="Storage2"]').val(0);
                        $('.distribution_form').find('[name="Product"]').val(0);
                        $('.distribution_form').find('[name="Quantity"]').val('');
                        $('.content').find('.warning').html('<p class="request_success">'+ result.message +'</p>');
                        window.location.reload();
                    }
                    if(result.success == false){
                        $('.content').find('.warning').html('<p class="request_failure">'+ result.message +'</p>');
                    }
                }
            });
        }
    });

    //other storages
    function GetRemainingStorages(iSelectedStorageId) {
        var second_storage_html = '';

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
    }

    //products
    function GetStorageProducts(iSelectedStorageId){
        var products_html = '';

        $.ajax({
            method: 'post',
            dataType: 'json',
            url: base_url + 'member/AjaxGetStorageAvailability/' + iSelectedStorageId,
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
    }

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