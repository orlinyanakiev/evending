$( document ).ready(function() {
    var base_url = 'http://localhost/evending/';
    var errorTimeout;

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
        return this.optional(element) || /^(\-)?[0-9]+(\.[0-9]{1,2})?$/.test(value);
    },"Wrong!");

    jQuery.validator.addMethod("DateValidation",function(value,element){
        return this.optional(element) || /^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\/|-|\.)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/|-|\.)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/|-|\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/.test(value);
    },"Wrong!");

    //Register user
    $('.register_form button').click(function(e){
        e.preventDefault();

        var this_form = $(this).closest("form");
        var RegisterTimeout;
        
        this_form.validate({
            rules:{
                FirstName: { required: true, Person: true },
                LastName: { required: true, Person: true },
                LoginName: { required: true, LogNamPass:true },
                Password: { required: true, LogNamPass:true },
                Password2:{ equalTo: "#pass" }
            },
            messages:{
                FirstName: '',
                LastName: '',
                LoginName: '',
                Password: '',
                Password2: ''
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
                        $('.register_form [name="FirstName"]').val('');
                        $('.register_form [name="LastName"]').val('');
                        $('.register_form [name="LoginName"]').val('');
                        $('.register_form [name="Password"]').val('');
                        $('.register_form [name="Password2"]').val('');

                        $('.public').find('.warning').html('<p class="request_success"><strong>Регистрацията премина успешно!</strong></p>');
                        $('.public').find('.directions').html('');
                        clearTimeout(RegisterTimeout);
                        RegisterTimeout = setTimeout(function(){
                            $('.public').find('.warning').html('');
                            $('.public').find('.directions').html('<a href="' + base_url + '">Обратно</a>');
                        }, 3000);
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

                    if(result.oUser.Type == 1){
                        GetDistributorVendingMachines(user_id);
                    }

                    $('.edit_user_form').find('[name="Type"]').html(select_html);
                    $('.list').hide();
                    $('.edit_user_form').show();
                }
                if(result.success == false){
                    $('.content').find('.warning').html('<p class="request_failure">Възникна грешка!</p>');
                    clearTimeout(errorTimeout);
                    errorTimeout = setTimeout(function(){
                        $('.content').find('.warning').html('');
                    }, 3000);
                }
            }
        })
    });

    //Distributors storage
    $('.users_content [name="Type"]').change(function(e){
        e.preventDefault();
        var iUserId = $('input[name="UserId"]').val();

        if($(this).val() == 1){
            GetDistributorVendingMachines(iUserId);
        } else {
            $('.vending_machines_list').hide();
        }
    })

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
                FirstName: '',
                LastName: '',
                LoginName: ''
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
                        clearTimeout(errorTimeout);
                        errorTimeout = setTimeout(function(){
                            $('.content').find('.warning').html('');
                        }, 3000);
                    }
                    if(result.success == false){
                        $('.content').find('.warning').html('<p class="request_failure">Опитайте отново!</p>');
                        clearTimeout(errorTimeout);
                        errorTimeout = setTimeout(function(){
                            $('.content').find('.warning').html('');
                        }, 3000);
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
            url: base_url + 'admin/UsersPagination/',
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

                listing_html += result.sPagination + '<div class="directions"><a href="' + base_url + 'admin/manage"">Обратно</a></div>';

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
                LoginName: '',
                Password: ''
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

    //Storages pagination
    $('.storages_content').on('click', '.pagination_list a', function(e){
        e.preventDefault();
        var page_id = $(this).attr('page-number');

        $.ajax({
            method: 'post',
            dataType: 'json',
            url: base_url + 'admin/StoragesPagination/',
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
                    listing_html += '<div class="storage_container container" storage-id="' + value.Id + '" style="background-color: #' + colour + '">' +
                    '<div class="column first_column"><a href="#" class="storage_availability">' + value.Name + '</a></div>' +
                    '<div class="manage_storage last_column">' +
                    '<a href="#" class="edit_storage"><i class="fa fa-pencil"></i></a> ' +
                    '<a href="#" class="delete_storage"><i class="fa fa-times"></i></a>' +
                    '</div><div class="column last_column">' + value.Address + '</div></div>';
                });

                listing_html += result.sPagination + '<div class="directions"><a href="' + base_url + 'admin/manage"">Обратно</a></div>';

                $('.storages_content').find('.list').html(listing_html);
            }
        });
    });

    //Edit storage
    $('.storages_content').on('click', '.edit_storage', function(e){
        e.preventDefault();

        var storage_id = $(this).closest('.storage_container').attr('storage-id');

        $.ajax({
            method: 'post',
            dataType: 'json',
            url: base_url + 'admin/GetStorageById/' + storage_id,
            success:function(result){
                if(result.success == true){
                    $('.edit_storage_form').find('[name="Id"]').val(result.oStorage.Id);
                    $('.edit_storage_form').find('[name="Name"]').val(result.oStorage.Name);
                    $('.edit_storage_form').find('[name="Address"]').val(result.oStorage.Address);

                    $('.list').hide();
                    $('.add_storage').hide();
                    $('.edit_storage_form').show();
                } else {
                    $('.content').find('.warning').html('<p class="request_failure">Възникна грешка!</p>');
                    clearTimeout(errorTimeout);
                    errorTimeout = setTimeout(function(){
                        $('.content').find('.warning').html('');
                    }, 3000);
                }
            }
        });
    });

    //Update storage
    $('.edit_storage_form').on('click','button',function(e){
        e.preventDefault();

        var this_form = $(this).closest('form');

        this_form.validate({
            rules:{
                Name: { required: true, Address: true },
                Address: { Address: true }
            },
            messages:{
                Name: '',
                Address: ''
            }
        })

        var form_valid = this_form.valid();

        if(form_valid){
            var data = this_form.serialize();

            $.ajax({
                dataType: 'json',
                method: 'post',
                url: base_url + 'admin/EditStorage/',
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('.content').find('.warning').html('<p class="request_success">Запазено!</p>');
                        clearTimeout(errorTimeout);
                        errorTimeout = setTimeout(function(){
                            $('.content').find('.warning').html('');
                        }, 3000);
                    }
                    if(result.success == false){
                        $('.content').find('.warning').html('<p class="request_failure">Възникна грешка!</p>');
                        clearTimeout(errorTimeout);
                        errorTimeout = setTimeout(function(){
                            $('.content').find('.warning').html('');
                        }, 3000);
                    }
                }
            });
        }
    });

    //Delete storage
    $('.storages_content').on('click','.delete_storage',function(e){
        e.preventDefault();


        if(confirm('Сигурни ли сте, че желаете да изтриете този склад?')){
            var storage_container = $(this).closest('.storage_container');
            var storage_id = storage_container.attr('storage-id');

            $.ajax({
                method: 'post',
                dataType: 'json',
                url: base_url + 'admin/DeleteStorage/' + storage_id,
                success:function(result){
                    if(result.success == true){
                        storage_container.remove();
                    }
                }
            })
        }
    });

    //Show add storage form
    $('.content a.add_storage').click(function(e){
        e.preventDefault();

        $('.content').find('a.add_storage').hide();
        $('.content').find('.list').hide();
        $('.content').find('.add_storage_form').show();
    });

    //Show cash field
    $('.add_storage_form [name="Type"]').on('change',function (e) {
        e.preventDefault();

        var storage_type = $(this).val();

        if(storage_type == 3){
            $('input[name="Cash"]').show();
        } else {
            $('input[name="Cash"]').hide();
        }
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
                Name: '',
                Address: '',
                Type: ''
            }
        })

        var form_valid = this_form.valid();

        if(form_valid){
            var data = this_form.serialize();

            $.ajax({
                dataType: 'json',
                method: 'post',
                url: base_url + 'admin/AddStorage/',
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('.add_storage_form [name="Name"]').val('');
                        $('.add_storage_form [name="Address"]').val('');
                        $('.add_storage_form [name="Type"]').val(0);
                        $('.add_storage_form [name="Cash"]').val('');
                        $('.add_storage_form [name="Cash"]').hide();

                        $('.content').find('.warning').html('<p class="request_success">Складът беше добавен успешно!</p>');
                        clearTimeout(errorTimeout);
                        errorTimeout = setTimeout(function(){
                            $('.content').find('.warning').html('');
                        }, 3000);
                    }
                    if(result.success == false){
                        $('.content').find('.warning').html('<p class="request_failure">Възникна грешка!</p>');
                        clearTimeout(errorTimeout);
                        errorTimeout = setTimeout(function(){
                            $('.content').find('.warning').html('');
                        }, 3000);
                    }
                }
            });
        }
    });

    //Product types pagination
    $('.product_types_content').on('click', '.pagination_list a', function(e){
        e.preventDefault();
        var page_id = $(this).attr('page-number');

        $.ajax({
            method: 'post',
            dataType: 'json',
            url: base_url + 'admin/ProductTypesPagination/',
            data: { "iPageId": page_id },
            success: function (result) {
                if(result.success == true){
                    var listing_html = '';
                    var colour = '';

                    $.each(result.aProductTypes, function( index , value ){
                        if(index % 2 == 0){
                            colour = 'DDF5B7';
                        } else {
                            colour = 'FFFF99'
                        }

                        listing_html += '<div class="product_type_container container" product-id="' + value.Id + '" style="background-color: #' + colour + '">';
                        listing_html += '<div class="column first_column">' + value.Name + '</div>';
                        listing_html += '<div class="manage_product_types last_column">' +
                        '<a href="#" class="edit_pt"><i class="fa fa-pencil"></i></a> ' +
                        '<a href="#" class="delete_pt"><i class="fa fa-times"></i></a>' +
                        '</div></div>'
                    });

                    listing_html += result.sPagination + '<div class="directions"><a href="' + base_url + 'admin/manage"">Обратно</a></div>';

                    $('.product_types_content').find('.list').html(listing_html);
                }
            }
        });
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
                Name: '',
                Category: ''
            }
        });

        var form_valid = this_form.valid();

        if(form_valid){
            var data = this_form.serialize();

            $.ajax({
                method: 'post',
                dataType: 'json',
                url: base_url + 'admin/AddProductType/',
                data: data,
                success: function (result) {
                    if(result.success == true){
                        this_form.find('[name="Name"]').val('');
                        this_form.find('[name="Category"]').val(0);

                        $('.content').find('.warning').html('<p class="request_success">Операцията е успешна!</p>');
                        clearTimeout(errorTimeout);
                        errorTimeout = setTimeout(function(){
                            $('.content').find('.warning').html('');
                        }, 3000);
                    }
                    if(result.success == false){
                        $('.content').find('.warning').html('<p class="request_failure">Възникна грешка! Опитайте отново</p>');
                        clearTimeout(errorTimeout);
                        errorTimeout = setTimeout(function(){
                            $('.content').find('.warning').html('');
                        }, 3000);
                    }
                }
            });
        }
    });

    //Edit product type
    $('.content .product_types_content .list').off('click').on('click','.edit_pt i',function(e){
        e.preventDefault();

        var pt_id = $(this).closest('.product_type_container').attr('product-id');

        $.ajax({
            dataType: 'json',
            method: 'post',
            url: base_url + 'admin/GetProductTypeById/',
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
                Name: '',
                Category: ''
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
                        clearTimeout(errorTimeout);
                        errorTimeout = setTimeout(function(){
                            $('.content').find('.warning').html('');
                        }, 3000);
                    }
                    if(result.success == false){
                        $('.content').find('.warning').html('<p class="request_failure">Опитайте отново!</p>');
                        clearTimeout(errorTimeout);
                        errorTimeout = setTimeout(function(){
                            $('.content').find('.warning').html('');
                        }, 3000);
                    }
                }
            });
        }
    });

    //Delete product type
    $('.content .product_types_content').on('click','.delete_pt i',function(e){
        e.preventDefault();

        if(confirm('Сигурни ли сте, че желаете да изтриете този тип изделие?')){
            var pt_id = $(this).closest('.product_type_container').attr('product-id');

            $.ajax({
                method: 'post',
                dataType: 'json',
                url: base_url + 'admin/DeleteProductType/',
                data: { "iProductTypeId" : pt_id },
                success:function(result){
                    if(result.success == true){
                        $('.product_type_container[product-id="' + pt_id + '"]').remove();
                    }
                }
            })
        }
    });

    //Products pagination
    $('.products_content .list').on('click', '.pagination_list a', function(e){
        e.preventDefault();
        var page_id = $(this).attr('page-number');

        $.ajax({
            method: 'post',
            dataType: 'json',
            url: base_url + 'admin/ProductsPagination/',
            data: { "iPageId": page_id },
            success: function (result) {
                var listing_html = '';
                var colour = '';

                $.each(result.aProducts, function( index , value ){
                    if(index % 2 == 0){
                        colour = 'DDF5B7';
                    } else {
                        colour = 'FFFF99';
                    }

                    listing_html += '<div class="product_container container" product-id="' + value.Id + '" style="background-color: #' + colour + '">';
                    listing_html += '<div class="column first_column"><a class="show_product_details" href="#">' + value.Type.Name + '</a></div>';
                    listing_html += '<div class="column last_column">' + value.ExpirationDate + '</div>';
                    listing_html += '</div>';
                });

                listing_html += result.sPagination  + '<div class="directions"><a href="' + base_url + 'admin/manage"">Обратно</a></div>';

                $('.products_content').find('.list').html(listing_html);
            }
        });
    });

    //Edit product
    $('.products_content').on('click', '.show_product_details', function(e){
        e.preventDefault();
        var product_id = $(this).closest('.product_container').attr('product-id');

        $.ajax({
            method: 'post',
            dataType: 'json',
            url: base_url + 'admin/GetProductById/',
            data: { "iProductId": product_id },
            success:function( result ){
                if(result.success == true){
                    $('.edit_product_form').find('[name="Id"]').val(result.oProduct.Id);
                    $('.edit_product_form').find('[name="Name"]').text(result.oProduct.Type.Name);
                    $('.edit_product_form').find('[name="Price"]').val(result.oProduct.Price);
                    $('.edit_product_form').find('[name="Value"]').val(result.oProduct.Value);

                    $('.products_content .list').hide();
                    $('.products_content .edit_product_form').show();
                }
            }
        });
    });

    //Update product
    $('.edit_product_form').off('click').on('click', 'button', function(e){
        e.preventDefault();

        var this_form = $(this).closest("form");

        this_form.validate({
            rules:{
                Id: { required: true, digits: true, GreaterThan: 0 },
                Value: { Price: true },
                Price: { Price: true }
            },
            messages:{
                Id: '',
                Price: '',
                Value: ''
            }
        });

        var form_valid = this_form.valid();

        if(form_valid){
            var data = this_form.serialize();

            $.ajax({
                dataType: 'json',
                method: 'post',
                url: base_url + 'admin/EditProduct/',
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('.content').find('.warning').html('<p class="request_success">Оперецията е успешна!</p>');
                        clearTimeout(errorTimeout);
                        errorTimeout = setTimeout(function(){
                            $('.content').find('.warning').html('');
                        }, 3000);
                    }
                    if(result.success == false){
                        $('.content').find('.warning').html('<p class="request_failure">Опитайте отново!</p>');
                        clearTimeout(errorTimeout);
                        errorTimeout = setTimeout(function(){
                            $('.content').find('.warning').html('');
                        }, 3000);
                    }
                }
            });
        }
    });

    $('#datepicker').datepicker({
        dateFormat: "dd.mm.yy",
        minDate: "today",
        showOtherMonths: true,
        selectOtherMonths: true
    });

    //Storage supply
    $('.supply_form button').click(function(e){
        e.preventDefault();

        var this_form = $(this).closest("form");

        this_form.validate({
            rules:{
                Storage: { GreaterThan: 0 },
                Category: { GreaterThan: 0 },
                ProductType: { GreaterThan: 0 },
                Quantity: { required: true, digits: true, GreaterThan: 0 },
                ExpirationDate: { required: true, DateValidation: true },
                Price: { required: true, Price: true },
                Value: { Price: true }
            },
            messages:{
                Storage: '',
                Category: '',
                ProductType: '',
                Quantity: '',
                ExpirationDate: '',
                Price: '',
                Value: ''
            }
        });

        var form_valid = this_form.valid();

        if(form_valid){
            var data = this_form.serialize();

            $.ajax({
                method: 'post',
                dataType: 'json',
                url: base_url + 'member/StorageSupply/',
                data: data,
                success: function (result) {
                    if(result.success == true){
                        $('.supply_form').find('[name="Category"]').val(0);
                        $('.supply_form').find('[name="ProductType"]').val(0);
                        $('.supply_form').find('[name="Quantity"]').val('');
                        $('.supply_form').find('[name="ExpirationDate"]').val('');
                        $('.supply_form').find('[name="Price"]').val('');
                        $('.supply_form').find('[name="Value"]').val('');

                        $('.content').find('.warning').html('<p class="request_success">Операцията е успешна!</p>');
                        clearTimeout(errorTimeout);
                        errorTimeout = setTimeout(function(){
                            $('.content').find('.warning').html('');
                        }, 3000);
                    }
                    if(result.success == false){
                        $('.content').find('.warning').html('<p class="request_failure">Възникна грешка! Опитайте отново</p>');
                        clearTimeout(errorTimeout);
                        errorTimeout = setTimeout(function(){
                            $('.content').find('.warning').html('');
                        }, 3000);
                    }
                }
            });
        }
    });

    //Storage supply category filter
    $('.supply_form select[name="Category"]').change(function(){
        var selected_category_id = $(this).find('option:selected').val();

        var products_html = '';
        if(selected_category_id > 0){
            $.ajax({
                method: 'post',
                dataType: 'json',
                url: base_url + 'member/GetProductTypesByCategoryId/',
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
                        $('.content').find('.warning').html('<p class="request_failure">Няма изделия от тази категория!</p>');
                        $('.supply_form').find('select[name="ProductType"]').html('<option value="0">Тип изделие</option>');
                        clearTimeout(errorTimeout);
                        errorTimeout = setTimeout(function(){
                            $('.content').find('.warning').html('');
                        }, 3000);
                    }
                }
            });
        } else {
            $('.content').find('.warning').html('');
            $('.supply_form').find('select[name="ProductType"]').html('<option value="0">Тип изделие</option>');
        }
    });

    //storage availability
    $('.storages_content .list').on('click', '.storage_availability', function(e){
        e.preventDefault();

        var storage_name = $(this).text();
        var storage_id = $(this).closest('.storage_container').attr('storage-id');
        var html = '';

        $.ajax({
            method: 'post',
            dataType: 'json',
            url: base_url + 'member/AjaxGetStorageAvailability/' + storage_id,
            success: function(result){
                if(result.success == true){
                    var iCounter = 0;
                    var sColor = '';
                    var fValue = 0;
                    var fPrice = 0;

                    html += '<div class="list" style="display: block">' +
                    '<div class="container"><div class="column first_column">' + storage_name + '</div></div>';

                    $.each(result.aStorageAvailability,function(index,value){
                        if(iCounter % 2 == 0){
                            sColor = 'DDF5B7';
                        } else {
                            sColor = 'FFFF99';
                        }
                        iCounter += 1;
                        fPrice += value.fPrice;
                        fValue += value.fValue;
                        html += '<div class="container" style="background-color: #' + sColor + ';"><div class="column first_column" product-id="' + value.oProduct.Id + '">' + value.oProduct.Type.Name + ' (' +value.oProduct.ExpirationDate + ')</div><div class="column last_column">' + value.iQuantity + '</div></div>';
                    });

                    html += '<div class="container"><div class="column first_column">Себестойност на изделията: ' + fValue + ' лв.</div></div>' +
                    '<div class="container"><div class="column first_column">Цена на изделията: ' + fPrice + ' лв.</div></div>';

                    if(result.fCash){
                        html += '<div class=container><div class="column first_column">Парична наличност: ' + result.fCash + ' лв.</div></div>' +
                        '<div class="container"><div class="column first_column">Общо: ' + result.fCurrentValue + ' лв.</div></div>';
                    }

                    html += '<div class="directions"><a href="' + base_url + 'admin/storages">Обратно</a></div>';

                    $('.content').html(html);
                }
                if(result.success == false){
                    $('.content').find('.warning').html('<p class="request_failure">Складът е празен!</p>');
                    clearTimeout(errorTimeout);
                    errorTimeout = setTimeout(function(){
                        $('.content').find('.warning').html('');
                    }, 3000);
                }
            }
        });
    });

    //distribution quantities
    $('.distribution_form select[name="Product"]').change(function(){
        var iQuantity = $(this).find('option:selected').attr('quantity');

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
                Storage1: '',
                Storage2: '',
                Product: '',
                Quantity: ''
            }
        });

        var form_valid = this_form.valid();

        if(form_valid){
            var data = this_form.serialize();

            $.ajax({
                method: 'post',
                dataType: 'json',
                url: base_url + 'member/Distribute/',
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('.distribution_form').find('[name="Product"]').val(0);
                        $('.distribution_form').find('[name="Quantity"]').val('');

                        var iSelectedStorageId = $('.distribution_form').find('[name="Storage1"]').val();
                        var products_html = '';

                        $.ajax({
                            method: 'post',
                            dataType: 'json',
                            url: base_url + 'member/AjaxGetStorageAvailability/' + iSelectedStorageId,
                            success:function(result){
                                if(result.success == true){
                                    products_html += '<option value="0">Изделие</option>';
                                    $.each(result.aStorageAvailability,function(index,value){
                                        products_html += '<option value="' + value.oProduct.Id + '" quantity="'+ value.iQuantity +'">' + value.oProduct.Type.Name + ' (' + value.oProduct.ExpirationDate + ')</option>';
                                    });

                                    $('.distribution_form').find('select[name="Product"]').html(products_html);
                                }
                                if(result.success == false ){
                                    $('.distribution_form').find('select[name="Product"]').html('<option value="0" selected="selected">Изделие</option>');

                                    $('.content').find('.warning').html('<p class="request_success">Операцията е успешна!</p>' +
                                    '<p class="request_failure">Няма повече изделия в склада!</p>');
                                    clearTimeout(errorTimeout);
                                    errorTimeout = setTimeout(function(){
                                        $('.content').find('.warning').html('');
                                    }, 3000);
                                }
                            }
                        });

                        $('.content').find('.warning').html('<p class="request_success">Операцията е успешна!</p>');
                        clearTimeout(errorTimeout);
                        errorTimeout = setTimeout(function(){
                            $('.content').find('.warning').html('');
                        }, 3000);
                    }
                    if(result.success == false){
                        $('.content').find('.warning').html('<p class="request_failure">Възникна грешка!</p>');
                        clearTimeout(errorTimeout);
                        errorTimeout = setTimeout(function(){
                            $('.content').find('.warning').html('');
                        }, 3000);
                    }
                }
            });
        }
    });

    $('.obsolete_form [name="Storage"]').on('change',function(e){
        e.preventDefault();

        var iSelectedStorageId = $(this).find('option:selected').val();
        var products_html = '';

        $.ajax({
            method: 'post',
            dataType: 'json',
            url: base_url + 'member/AjaxGetStorageAvailability/' + iSelectedStorageId,
            success:function(result){
                if(result.success == true){
                    products_html += '<option value="0">Изделие</option>';
                    $.each(result.aStorageAvailability,function(index,value){
                        products_html += '<option value="' + value.oProduct.Id + '" quantity="'+ value.iQuantity +'">' + value.oProduct.Type.Name + ' (' + value.oProduct.ExpirationDate + ')</option>';
                    });

                    $('.obsolete_form').find('select[name="Product"]').html(products_html);
                    $('.content').find('.warning').html('');
                }
                if(result.success == false ){
                    $('.obsolete_form').find('select[name="Product"]').html('<option value="0" selected="selected">Изделие</option>');

                    $('.content').find('.warning').html('<p class="request_failure">Складът е празен!</p>');
                    clearTimeout(errorTimeout);
                    errorTimeout = setTimeout(function(){
                        $('.content').find('.warning').html('');
                    }, 3000);
                }
            }
        });
    });

    $('.obsolete_form [name="Product"]').on('change',function(e){
        e.preventDefault();

        var iQuantity = $(this).find('option:selected').attr('quantity');

        if(iQuantity){
            $('.obsolete_form').find('[name="Quantity"]').val(iQuantity);
        } else {
            $('.obsolete_form').find('[name="Quantity"]').val('');
        }
    });

    $('.obsolete_form button').click(function(e){
        e.preventDefault();

        var this_form = $(this).closest('form');

        this_form.validate({
            rules:{
                Storage: { GreaterThan: 0 },
                Product: { GreaterThan: 0 },
                Quantity: { required: true, digits: true, GreaterThan: 0 }
            },
            messages:{
                Storage: '',
                Product: '',
                Quantity: ''
            }
        });

        var products_html = '';
        var form_valid = this_form.valid();

        if(form_valid){
            var data = this_form.serialize();

            $.ajax({
                method: 'post',
                dataType: 'json',
                url: base_url + 'member/ObsoleteProduct/',
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('.obsolete_form').find('[name="Product"]').val(0);
                        $('.obsolete_form').find('[name="Quantity"]').val('');
                        var iSelectedStorageId = this_form.find('[name="Storage"] option:selected').val();

                        $.ajax({
                            method: 'post',
                            dataType: 'json',
                            url: base_url + 'member/AjaxGetStorageAvailability/' + iSelectedStorageId,
                            success:function(result){
                                if(result.success == true){
                                    products_html += '<option value="0">Изделие</option>';
                                    $.each(result.aStorageAvailability,function(index,value){
                                        products_html += '<option value="' + value.oProduct.Id + '" quantity="'+ value.iQuantity +'">' + value.oProduct.Type.Name + ' (' + value.oProduct.ExpirationDate + ')</option>';
                                    });

                                    $('.obsolete_form').find('select[name="Product"]').html(products_html);
                                    $('.content').find('.warning').html('');
                                }
                                if(result.success == false ){
                                    $('.obsolete_form').find('select[name="Product"]').html('<option value="0" selected="selected">Изделие</option>');

                                    $('.content').find('.warning').html('<p class="request_failure">Складът е празен!</p>');
                                    clearTimeout(errorTimeout);
                                    errorTimeout = setTimeout(function(){
                                        $('.content').find('.warning').html('');
                                    }, 3000);
                                }
                            }
                        });

                        $('.content').find('.warning').html('<p class="request_success">Успешна операция!</p>');
                        clearTimeout(errorTimeout);
                        errorTimeout = setTimeout(function(){
                            $('.content').find('.warning').html('');
                        }, 3000);
                    }
                    if(result.success == false){
                        $('.content').find('.warning').html('<p class="request_failure">Възникна грешка!</p>');
                        clearTimeout(errorTimeout);
                        errorTimeout = setTimeout(function(){
                            $('.content').find('.warning').html('');
                        }, 3000);
                    }
                }
            });
        }
    });


    //Income submit
    $('.revenue_form').on('click','button',function(e){
        e.preventDefault();

        var this_form = $(this).closest('form');

        this_form.validate({
            rules:{
                Storage: { GreaterThan: 0 },
                Value: { required: true, Price: true }
            },
            messages:{
                Storage: '',
                Value: ''
            }
        });

        var form_valid = this_form.valid();

        if(form_valid){
            var data = this_form.serialize();

            $.ajax({
                method: 'post',
                dataType: 'json',
                url: base_url + 'member/RevenueAccounting/',
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('.revenue_form').find('[name="Value"]').val('');

                        $('.content').find('.warning').html('<p class="request_success">Операцията е успешна!</p>');
                        clearTimeout(errorTimeout);
                        errorTimeout = setTimeout(function(){
                            $('.content').find('.warning').html('');
                        }, 3000);
                    }
                    if(result.success == false){
                        $('.content').find('.warning').html('<p class="request_failure">Възникна грешка!</p>');
                        clearTimeout(errorTimeout);
                        errorTimeout = setTimeout(function(){
                            $('.content').find('.warning').html('');
                        }, 3000);
                    }
                }
            });
        }
    });

    //Events pagination
    $('.events_content').on('click', '.pagination_list a', function(e){
        e.preventDefault();
        var page_id = $(this).attr('page-number');

        $.ajax({
            method: 'post',
            dataType: 'json',
            url: base_url + 'admin/EventsPagination/',
            data: { "iPageId": page_id },
            success: function (result) {
                var listing_html = '';
                var colour = '';

                $.each(result.aEvents, function( index , value ){
                    if(index % 2 == 0){
                        colour = 'DDF5B7';
                    } else {
                        colour = 'FFFF99'
                    }

                    listing_html += '<div class="event_container container" style="background-color: #' + colour + '"> ' +
                    '<a href="#" class="show_event_preview" event-id="' + value.Id + '"> ' +
                    '<div class="first_column column">' + value.DateRegistered + '</div> ' +
                    '<div class="first_column column">' + value.UserId.FirstName + ' ' + value.UserId.LastName + '</div> ' +
                    '<div class="first_column column">' + value.Type + '</div>' +
                    '</a></div>';
                });

                listing_html += result.sPagination + '<div class="directions"><a href="' + base_url + 'admin/manage"">Обратно</a></div>';

                $('.events_content').find('.list').html(listing_html);
            }
        });
    });

    //Event preview
    $('.events_content .list').on('click','a.show_event_preview',function(e){
        e.preventDefault();

        var event_id = $(this).attr('event-id');

        $.ajax({
            method: 'post',
            dataType: 'json',
            url: base_url + 'admin/GetEventPreview/' + event_id,
            success:function(result){
                if(result.success == true){
                    $('.events_content .list').html(result.sEventPreview);
                }
                else {
                    $('.content').find('.warning').html('<p class="request_failure">Възникна грешка!</p>');
                    clearTimeout(errorTimeout);
                    errorTimeout = setTimeout(function(){
                        $('.content').find('.warning').html('');
                    }, 3000);
                }
            }
        });
    });

    //sales select options
    $('.sales_form select[name="Storage"]').change(function(){
        var iSelectedStorageId = $(this).val();

        var products_html = '';

        $.ajax({
            method: 'post',
            dataType: 'json',
            url: base_url + 'member/AjaxGetStorageAvailability/' + iSelectedStorageId,
            success:function(result){
                if(result.success == true){
                    products_html += '<option value="0">Изделие</option>';
                    $.each(result.aStorageAvailability,function(index,value){
                        products_html += '<option value="' + value.oProduct.Id + '" quantity="'+ value.iQuantity +'">' + value.oProduct.Type.Name + ' (' + value.oProduct.ExpirationDate + ')</option>';
                    });

                    $('.sales_form').find('select[name="Product"]').html(products_html);
                    $('.content').find('.warning').html('');
                }
                if(result.success == false ){
                    $('.distribution_form').find('select[name="Product"]').html('<option value="0" selected="selected">Изделие</option>');

                    $('.content').find('.warning').html('<p class="request_failure">Складът е празен!</p>');
                    clearTimeout(errorTimeout);
                    errorTimeout = setTimeout(function(){
                        $('.content').find('.warning').html('');
                    }, 3000);
                }
            }
        });
    });

    //sales quantities
    $('.sales_form select[name="Product"]').change(function(){
        var iQuantity = $(this).find('option:selected').attr('quantity');

        $('.sales_form').find('input[name="Quantity"]').val(iQuantity);
    })

    //sales submit
    $('.sales_form button').click(function(e){
        e.preventDefault();

        var this_form = $(this).closest('form');

        this_form.validate({
            rules:{
                Storage: { GreaterThan: 0 },
                Product: { GreaterThan: 0 },
                Quantity: { required: true, digits: true, GreaterThan: 0 }
            },
            messages:{
                Storage: '',
                Product: '',
                Quantity: ''
            }
        });

        var form_valid = this_form.valid();

        if(form_valid){
            var data = this_form.serialize();

            $.ajax({
                method: 'post',
                dataType: 'json',
                url: base_url + 'member/Sale/',
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('.sales_form').find('[name="Product"]').val(0);
                        $('.sales_form').find('[name="Quantity"]').val('');

                        $('.content').find('.warning').html('<p class="request_success">Успешна операция!</p>');
                        clearTimeout(errorTimeout);
                        errorTimeout = setTimeout(function(){
                            $('.content').find('.warning').html('');
                        }, 3000);
                    }
                    if(result.success == false){
                        $('.content').find('.warning').html('<p class="request_failure">Възникна грешка!</p>');
                        clearTimeout(errorTimeout);
                        errorTimeout = setTimeout(function(){
                            $('.content').find('.warning').html('');
                        }, 3000);
                    }
                }
            });
        }
    });

    //sales statistics
    $('.sales_content [name="Storage"], .sales_content [name="Period"]').on('change', function(e){
        e.preventDefault();

        var user_id = $('.sales_content [name="User"]').val();
        var storage_id = $('.sales_content [name="Storage"]').val();
        var period = $('.sales_content [name="Period"]').val();

        GetSales(user_id,storage_id,period);
    });

    //update statistics and storages
    $('.sales_content [name="User"]').on('change', function(e){
        e.preventDefault();

        var user_id = $('.sales_content [name="User"]').val();
        var storage_id = $('.sales_content [name="Storage"]').val();
        var period = $('.sales_content [name="Period"]').val();

        $.ajax({
            method: 'post',
            dataType: 'json',
            url: base_url + 'admin/GetDistributorVendingMachines/' + user_id,
            success:function (result) {
                if(result.success == true){
                    var html = '<option value="0">Всички вендинг автомати</option>';

                    $.each(result.aDistributorStorages, function ( index , value ){
                        html += '<option value="' + value.Id + '">' + value.Name + '</option>';
                    });

                    $('.sales_content [name="Storage"]').html(html);
                } else {
                    $('.sales_content [name="Storage"]').html('<option value=""> --- </option>');
                }
                GetSales (user_id , storage_id , period);
            }
        });
    });

    var GetSales = function (iUserId, iStorageId, sPeriod) {
        $.ajax({
            method: 'post',
            dataType: 'json',
            url: base_url + 'admin/GetSales/',
            data: { "iUserId": iUserId, "iStorageId": iStorageId, "sPeriod": sPeriod },
            success:function(result){
                if(result.success == true){
                    $('#profit').html(result.aSalesData.Profit);
                    $('#expense').html(result.aSalesData.Expense);
                    $('#income').html(result.aSalesData.Income);
                }
            }
        });
    }

    var GetDistributorVendingMachines = function (iUserId){

        $.ajax({
            method: 'post',
            dataType: 'json',
            url: base_url + 'admin/GetDistributorVendingMachines/' + iUserId,
            success:function (result) {
                if(result.success == true){
                    $.each(result.aDistributorStorages, function ( index , value ){
                        $('.vending_machines_list [type="checkbox"]').each(function(){
                            if($(this).val() == value.Id){
                                $(this).attr('checked','checked');
                            }
                        });
                    });

                    $('.vending_machines_list').show();
                }
                if(result.success == false){
                    $('.content').find('.warning').html('<p class="request_failure">Възникна грешка!</p>');
                    clearTimeout(errorTimeout);
                    errorTimeout = setTimeout(function(){
                        $('.content').find('.warning').html('');
                    }, 3000);
                }
            }
        });
    }
});
