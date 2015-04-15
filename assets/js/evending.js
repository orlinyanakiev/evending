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

    jQuery.validator.addMethod("DateValidation",function(value,element){
        return this.optional(element) || /^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\/|-|\.)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/|-|\.)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/|-|\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/.test(value);
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
    $('.users_content').off('click').on('click', '.delete_user i', function ( e ) {
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
                        $('.content').find('.user_container[user-id="' + user_id + '"]').remove();
                    }
                }
            })
        }
    });

    //Edit user
    $('.users_content .list').off('click').on('click','.edit_user i', function (e) {
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

    //Update user
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

    //Users pagination
    $('.users_content').on('click', '.pagination_list a', function(e){
        e.preventDefault();
        var page_id = $(this).attr('page-number');

        $.ajax({
            method: 'post',
            dataType: 'json',
            url: base_url + 'admin/UsersPagination',
            data: { "iPageId": page_id },
            success: function (result) {
                var listing_html = '';
                var colour = '';

                $.each(result.aUsers, function( index , value ){
                    if(index % 2 == 0){
                        colour = 'DDF5B7';
                    } else {
                        colour = 'FFFF99'
                    }

                    listing_html += '<div class="user_container container" user-id="' + value.Id + '" style="background-color: #' + colour + '">';
                    listing_html += '<div class="column first_column">' + value.FirstName + '</div>';
                    listing_html += '<div class="column">' + value.LastName + '</div>';
                    listing_html += '<div class="manage_users last_column">';
                    listing_html += '<a href="#" class="edit_user"><i class="fa fa-pencil"></i></a> ';
                    if(result.oUser.Id == value.Id){
                        listing_html += '<i style="color:grey" class="fa fa-times"></i>';
                    } else {
                        listing_html += '<a href="#" class="delete_user"><i class="fa fa-times"></i></a>';
                    }
                    listing_html += '</div></div>';
                });

                listing_html += result.sPagination;

                $('.users_content').find('.list').html(listing_html);
            }
        });
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

    //admin option navigation
    $( '.admin_options' ).off( 'click' ).on( 'click' , 'a.section' , function( e ) {
        e.preventDefault();

        var section = $(this).attr('section');

        $('.admin_options').hide();
        $("." + section).show();
    });

    //Storages pagination
    $('.storages_content').on('click', '.pagination_list a', function(e){
        e.preventDefault();
        var page_id = $(this).attr('page-number');

        $.ajax({
            method: 'post',
            dataType: 'json',
            url: base_url + 'admin/StoragesPagination',
            data: { "iPageId": page_id },
            success: function (result) {
                var listing_html = '';
                var colour = '';

                $.each(result.aStorages, function( index , value ){
                    if(index % 2 == 0){
                        colour = 'DDF5B7';
                    } else {
                        colour = 'FFFF99'
                    }
                    listing_html += '<div class="storage_container container" style="background-color: #' + colour + '">';
                    listing_html += '<div class="column first_column"><a href="#" class="storage_availability" storage-id="' + value.Id + '">' + value.Name + '</a></div>';
                    listing_html += '<div class="column last_column">' + value.Address + '</div>';
                    listing_html += '</div>';
                });

                listing_html += result.sPagination;

                $('.storages_content').find('.list').html(listing_html);
            }
        });
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
            }
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
                Category: { GreaterThan: 0 }
            },
            messages:{
                Name: 'Въведеното име е некоректно',
                Category: 'Изберете категория'
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
                success: function (result) {
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
    });

    //Edit product type
    $('.manage_product_types .edit_pt').off('click').on('click','i',function(e){
        e.preventDefault();

        var pt_id = $(this).closest('.product_type_container').attr('product-id');

        $.ajax({
            dataType: 'json',
            method: 'post',
            url: base_url + 'admin/GetProductTypeById',
            data: { "iProductTypeId" : pt_id },
            success: function (result) {
                if(result.success == true){
                    $('.add_product_type').hide();
                    $('.list').hide();
                    $('.edit_product_type_form').show();

                    var select_html = '<option value="0">Категория</option>';
                    $.each ( result.aCategories , function ( index , value ) {
                        if ( result.oProductType.Category == index ){
                            select_html += '<option selected="selected" value="' + index + '">' + value + '</option>';
                        } else {
                            select_html += '<option value="' + index + '">' + value + '</option>';
                        }
                    });

                    $('.edit_product_type_form').find('[name="Id"]').val( result.oProductType.Id );
                    $('.edit_product_type_form').find('[name="Category"]').html( select_html );
                    $('.edit_product_type_form').find('[name="Name"]').val( result.oProductType.Name );
                }
            }
        })
    });

    //Update product type
    $('.edit_product_type_form').off('click').on('click','button',function(e){
        e.preventDefault();

        var this_form = $(this).closest("form");

        this_form.validate({
            rules:{
                Id: { required: true, digits: true, GreaterThan: 0 },
                Name: { required: true, Address: true },
                Category: { required: true, digits: true, GreaterThan: 0 }
            },
            messages:{
                Name: 'Въведете валидно име',
                Category: 'Изберете категория'
            }
        });

        var form_valid = this_form.valid();

        if(form_valid){
            var data = this_form.serialize();

            $.ajax({
                dataType: 'json',
                method: 'post',
                url: base_url + 'admin/EditProductType/',
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

    //Delete product type
    $('.manage_product_types .delete_pt').off('click').on('click','i',function(e){
        e.preventDefault();

        if(confirm('Сигурни ли сте, че желаете да изтриете този тип продукти?')){
            var pt_id = $(this).closest('.product_type_container').attr('product-id');

            $.ajax({
                method: 'post',
                dataType: 'json',
                url: base_url + 'admin/DeleteProductType',
                data: { "iProductTypeId" : pt_id },
                success:function(result){
                    if(result.success == true){
                        $('.product_type_container[product-id="' + pt_id + '"]').remove();
                    }
                }
            })
        }
    });

    //Storage supply
    $('.supply_form button').click(function(e){
        e.preventDefault();

        var this_form = $(this).closest("form");

        this_form.validate({
            rules:{
                Storage: { GreaterThan: 0 },
                Product: { GreaterThan: 0 },
                Quantity: { required: true, digits: true, GreaterThan: 0 },
                ExpirationDate: { required: true, DateValidation: true },
                Price: { required: true, Price: true },
                Value: { Price: true }
            },
            messages:{
                Storage: 'Изберете хранилище',
                Product: 'Изберете продукт',
                Quantity: 'Посочете количество',
                ExpirationDate: 'Въведете валидна дата',
                Price: 'Въведете валидна цена',
                Value: 'Въведете валидна цена'
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
                success: function (result) {
                    if(result.success == true){
                        $('.content').find('.warning').html('<p class="request_success">Операцията е успешна!</p>');
                        $('.supply_form').find('[name="Storage"]').val(0);
                        $('.supply_form').find('[name="Category"]').val(0);
                        $('.supply_form').find('[name="ProductType"]').val(0);
                        $('.supply_form').find('[name="Quantity"]').val('');
                        $('.supply_form').find('[name="ExpirationDate"]').val('');
                        $('.supply_form').find('[name="Price"]').val('');
                        $('.supply_form').find('[name="Value"]').val('');

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
                url: base_url + 'member/GetProductTypesByCategoryId',
                data: { "iCategoryId" : selected_category_id },
                success: function (result) {
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
    $('.storages_content .list').on('click', '.storage_availability', function(e){
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
    });

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
                        $('.distribution_form').find('select[name="Storage1"]').val(0);
                        $('.distribution_form').find('[name="Storage2"]').val(0);
                        $('.distribution_form').find('[name="Product"]').val(0);
                        $('.distribution_form').find('[name="Quantity"]').val('');
                        $('.content').find('.warning').html('<p class="request_success">'+ result.message +'</p>');
                        setTimeout(function(){
                            window.location.reload();
                        },500);
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
                    $('.content').find('.warning').html('');
                }
                if(result.success == false ){
                    $('.distribution_form').find('select[name="Product"]').html('<option value="0" selected="selected">Изделие</option>');

                    $('.content').find('.warning').html('<p class="request_failure">'+ result.message +'</p>');
                }
            }
        });
    }

});
