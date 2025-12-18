<form class="row" method="post" style="mb-2">
    <fieldset>
        <legend>Настройка списания</legend>

        <label class='form-check-label' for='payuPaymentReference'>
            Номер транзакции в системе Your Payments
        </label>
        <input name='payuPaymentReference' class='form-control' type='text' id='payuPaymentReference' required="required">

        <label class='form-check-label' for='originalAmount'>
            Сумма оригинальной транзакции
        </label>
        <input name='originalAmount' class='form-control' type='number' min="1" step="0.01" id='originalAmount'
               required='required'>

        <label class='form-check-label' for='amount'>
            Сумма списания (некоторые методы поддерживают частичное списание)
        </label>
        <input name='amount' class='form-control mb-3' type='number' min="1" step="0.01" id='amount'
               required='required'>
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
            <input name='debug' value='yes'  class='form-check-input' type='checkbox' id='debug' checked>
            <label class='form-check-label' for='debug'>
                Режим дебага (вывода сообщений для отладки)
            </label>
        </div>

        <input type="submit" class="btn btn-primary w-100" value="Списать">
    </fieldset>

</form>

<hr class="mb-3">
