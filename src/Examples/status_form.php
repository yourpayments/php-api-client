<form class='row' method='post' style='mb-2'>
    <fieldset>
        <legend>Запрос статуса</legend>

        <label class='form-check-label' for='merchantPaymentReference'>
            Номер заказа в системе ТСП
        </label>
        <input name='merchantPaymentReference' class='form-control mb-2' type='text' id='merchantPaymentReference'
               required='required' autofocus value="<?=@$_REQUEST['merchantPaymentReference']?>">
    </fieldset>

    <fieldset>
        <legend>Настройки интеграции</legend>
        <div class='form-check mb-3'>
            <input name='sandbox' value='yes' class='form-check-input' type='checkbox' id='sandbox' checked>
            <label class='form-check-label' for='sandbox'>
                Режим песочницы (запросы отправляются на тестовый сервер sandbox.ypmn.ru)
            </label>
        </div>

        <div class='form-check mb-3'>
            <input name='debug' value='yes' class='form-check-input' type='checkbox' id='debug' checked>
            <label class='form-check-label' for='debug'>
                Режим дебага (вывода сообщений для отладки)
            </label>
        </div>

        <input type='submit' class='btn btn-primary w-100' value='Списать'>
    </fieldset>
</form>

<hr class='mb-3'>
