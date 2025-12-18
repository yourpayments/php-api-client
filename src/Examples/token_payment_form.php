<form class="row" method="post" style="mb-2">
<!--    <fieldset>-->
<!--        <legend>Информация о Плательщике</legend>-->
<!--    </fieldset>-->

    <fieldset>
        <legend>Оплата токеном</legend>

        <label class='form-check-label' for='payment_method'>
            <label class='form-check-label' for='payment_method'>
                Метод оплаты
            </label>
            <select name='payment_method' class='form-select mb-3' aria-label='Метод оплаты' id='payment_method'
                    required>
                <option disabled>Метод оплаты</option>
                <option <?= (@$_REQUEST['method'] == 'CCVISAMC' ? 'selected' : '') ?> value='CCVISAMC'>Банковская карта
                    РФ и РБ
                </option>
                <option <?= (@$_REQUEST['method'] == 'INTCARD' ? 'selected' : '') ?> value='INTCARD'>Банковские карты
                    Visa и Mastercard карта вне РФ и РБ
                </option>
                <option <?= (@$_REQUEST['method'] == 'FASTER_PAYMENTS' ? 'selected' : '') ?> value='FASTER_PAYMENTS'>
                    СБП
                </option>
                <option <?= (@$_REQUEST['method'] == 'SBERPAY' ? 'selected' : '') ?> value='SBERPAY'>SberPay</option>
                <option <?= (@$_REQUEST['method'] == 'TPAY' ? 'selected' : '') ?> value='TPAY'>T-Pay</option>
            </select>

        <label class='form-check-label' for='merchantPaymentReference'>
            Номер транзакции в системе ТСП
        </label>
        <input name='merchantPaymentReference' class='form-control' type='text' id='merchantPaymentReference' required="required">

        <label class='form-check-label' for='originalAmount'>
            Сумма оригинальной транзакции
        </label>
        <input name='originalAmount' class='form-control mb-3' type='number' min="1" step="0.01" id='originalAmount'
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
