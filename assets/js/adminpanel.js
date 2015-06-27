var base_url = 'http://localhost/evending/';
var errorTimeout = '';

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

$('ul.nav').on('click', 'li a', function(){
    if(!$(this).parents('ul').hasClass('sub-nav')){
        if(!$(this).closest('li').hasClass('active')){
            $('ul.nav-panel li').removeClass('active');
            $(this).closest('li').addClass('active');
            if(!$(this).closest('li').hasClass('slide-trigger')){
                $('ul.sub-nav').slideUp();
            }
        }
    }
});

$('.slide-trigger').on('click', '> a', function(){
    if(!$(this).closest('li').hasClass('active')){
        $('ul.sub-nav').slideUp();
        $(this).siblings('ul.sub-nav').slideDown();
        $(this).siblings('ul.sub-nav').show();
    } else {
        $(this).siblings('ul.sub-nav').find('li.active').removeClass('active');
    }
});

$('.sub-nav').on('click', 'a', function(){
    if(!$(this).closest('li').hasClass('active')){
        $(this).closest('li').siblings('li').removeClass('active');
        $(this).closest('li').addClass('active');
    }
});

$('.users_list select[name="Type"]').on('change',function(e){
    e.preventDefault();

    var iSelectedType = $(this).val();
    var iUserId = $(this).closest('tr').attr('user-id');

    $.ajax({
        method: 'post',
        dataType: 'json',
        url: base_url + 'admin/ChangeUserType',
        data: { "iUserId": iUserId, "iType": iSelectedType },
        success:function (result){
            if(result.success == true){
                $('.alert-success').fadeIn(300).delay(3000).fadeOut(300);
            }
            if(result.success == false){
                $('.alert-danger').fadeIn(300).delay(3000).fadeOut(300);
            }
        }
    });
});

$('a.add_product_category').on('click', function (e) {
    e.preventDefault();

    $('.add_product_category').hide()
    $('.add_product_category_form').show();
});

$('.add_product_category_form a.back').on('click',function(e){
    e.preventDefault();

    $('.add_product_category').show()
    $('.add_product_category_form').hide();
});

$('.add_product_category_form').on('click', 'button', function(e){
    e.preventDefault();

    var this_form = $(this).closest("form");

    this_form.validate({
        rules:{
            Name: { required: true, Address:true },
            Barcode: { required: true, digits:true }
        },
        messages:{
            Name: '',
            Barcode: ''
        }
    });

    var form_valid = this_form.valid();

    if(form_valid){
        var data = this_form.serialize();

        $.ajax({
            dataType: 'json',
            method: 'post',
            url: base_url + 'admin/AddProductCategory/',
            data: data,
            success:function(result){
                if(result.success == true){
                    var CategoryName = $(this_form).find('[name="Name"]').val();
                    var CategoryCode = $(this_form).find('[name="Barcode"]').val();

                    $('.product_categories_list table tbody').append('<tr><td>' + CategoryName + '</td><td>' + CategoryCode + '</td></tr>');

                    $(this_form).find('[name="Name"]').val('');
                    $(this_form).find('[name="Barcode"]').val('');

                    $('.alert-success').fadeIn(300).delay(3000).fadeOut(300);
                }
                if(result.success == false){
                    $('.alert-danger').fadeIn(300).delay(3000).fadeOut(300);
                }
            }
        });
    }
});

$('.main_storage_supply_form [name="ExpirationDate"]').datepicker({
    dateFormat: "dd.mm.yy",
    minDate: "today",
    showOtherMonths: true,
    selectOtherMonths: true
});

$('.main_storage_supply_form').on('click', 'button', function(e){
    e.preventDefault();

    var this_form = $(this).closest("form");

    this_form.validate({
        rules:{
            Category: { required: true, GreaterThan: 0 },
            Quantity: { required: true, digits: true, GreaterThan: 0 },
            SupplyValue: { required: true, Price: true, GreaterThan: 0 },
            ExpirationDate: { required: true, DateValidation: true }
        },
        messages:{
            Category: '',
            Quantity: '',
            SupplyValue: '',
            ExpirationDate: ''
        }
    });

    var form_valid = this_form.valid();

    if(form_valid){
        var data = this_form.serialize();

        $.ajax({
            dataType: 'json',
            method: 'post',
            url: base_url + 'admin/MainStorageSupply/',
            data: data,
            success:function(result){
                if(result.success == true){
                    $(this_form).find('[name="Category"]').val('0');
                    $(this_form).find('[name="Quantity"]').val('');
                    $(this_form).find('[name="SupplyValue"]').val('');
                    $(this_form).find('[name="ExpirationDate"]').val('');

                    $('.alert-success').fadeIn(300).delay(3000).fadeOut(300);
                }
                if(result.success == false){
                    $('.alert-danger').fadeIn(300).delay(3000).fadeOut(300);
                }
            }
        });
    }
});

$('a.add_vending_machine').on('click', function (e) {
    e.preventDefault();

    $('.add_vending_machine').hide()
    $('.add_vending_machine_form').show();
});

$('.add_vending_machine_form a.back').on('click',function(e){
    e.preventDefault();

    $('.add_vending_machine').show()
    $('.add_vending_machine_form').hide();
});

$('.add_vending_machine_form').on('click', 'button', function(e){
    e.preventDefault();

    var this_form = $(this).closest("form");

    this_form.validate({
        rules:{
            Name: { required: true, Address:true },
            Address: { Address:true },
            Cash: { required: true, Price: true }
        },
        messages:{
            Name: '',
            Address: '',
            Cash: ''
        }
    });

    var form_valid = this_form.valid();

    if(form_valid){
        var data = this_form.serialize();

        $.ajax({
            dataType: 'json',
            method: 'post',
            url: base_url + 'admin/AddVendingMachine/',
            data: data,
            success:function(result){
                if(result.success == true){
                    var VendingMachineName = $(this_form).find('[name="Name"]').val();
                    var VendingMachineAddress = $(this_form).find('[name="Address"]').val();
                    var VendingMachineCash = $(this_form).find('[name="Cash"]').val();

                    //$('.product_categories_list table tbody').append('<tr><td>' + CategoryName + '</td><td>' + CategoryCode + '</td></tr>');

                    $(this_form).find('[name="Name"]').val('');
                    $(this_form).find('[name="Address"]').val('');
                    $(this_form).find('[name="Cash"]').val('');

                    $('.alert-success').fadeIn(300).delay(3000).fadeOut(300);
                }
                if(result.success == false){
                    $('.alert-danger').fadeIn(300).delay(3000).fadeOut(300);
                }
            }
        });
    }
});

$('.distributors_content').on( 'click' , 'button' , function (e) {
    e.preventDefault();

    var this_form = $(this).closest("form");
    var data = this_form.serialize();

    console.log(data);
})
