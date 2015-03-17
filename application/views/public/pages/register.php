<div class="page_wrapper public">
    <p>Въведете необходимата информация:</p>
    <div class="form">
        <form class="register_form" method="post" action="">
            <input type="hidden" name="Id" value="">
            <input type="text" name="Company" placeholder="Име на фирма" autocomplete="off"/>
            <input type="text" name="FirstName" placeholder="Първо име" autocomplete="off"/>
            <input type="text" name="LastName" placeholder="Фамилия" autocomplete="off"/>
            <input type="text" name="LoginName" placeholder="Потребителско име" autocomplete="off"/>
            <input id="pass" type="password" name="Password" placeholder="Парола"/>
            <input type="password" name="Password2" placeholder="Потвърдете паролата"/>
            <button type="submit">Регистриране</button>
        </form>
    </div>
    <div class="warning">
    </div>
    <div class="directions">
        <a href="<?= base_url();?>">Обратно</a>
    </div>
</div>