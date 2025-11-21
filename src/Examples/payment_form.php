<form class="row" method="post" style="mb-2">
<!--    <fieldset>-->
<!--        <legend>Информация о Плательщике</legend>-->
<!--    </fieldset>-->

    <fieldset>
        <legend>Настройка платежа</legend>

        <label class='form-check-label' for='tokenization'>
            Метод оплаты
        </label>
        <select name="payment_method" class='form-select mb-3' aria-label='Метод оплаты' id="payment_method" required>
            <option disabled>Метод оплаты</option>
            <option <?=( @$_REQUEST['method'] == 'CCVISAMC' ? 'selected': '')?> value='CCVISAMC'>Банковская карта РФ и РБ</option>
            <option <?=( @$_REQUEST['method'] == 'INTCARD'  ? 'selected': '')?> value='INTCARD'>Банковские карты Visa и Mastercard карта вне РФ и РБ</option>
            <option <?=( @$_REQUEST['method'] == 'FASTER_PAYMENTS' ? 'selected': '')?> value='FASTER_PAYMENTS'>СБП</option>
            <option <?=( @$_REQUEST['method'] == 'BNPL'     ? 'selected': '')?> value='BNPL'>Рассрочка «ПОДЕЛИ»</option>
            <option <?=( @$_REQUEST['method'] == 'ALFAPAY'  ? 'selected': '')?> value='ALFAPAY'>Alfa Pay</option>
            <option <?=( @$_REQUEST['method'] == 'SBERPAY'  ? 'selected': '')?> value='SBERPAY'>SberPay</option>
            <option <?=( @$_REQUEST['method'] == 'TPAY'     ? 'selected': '')?> value='TPAY'>T-Pay</option>
        </select>

        <div class='form-check mb-3'>
            <input name='tokenization' value='yes' class='form-check-input' type='checkbox' id='tokenization'>
            <label class='form-check-label' for='tokenization'>
                Токенизация (сохранение платёжных данных для повторных оплат)
                <br>
                <br>
                <span class="text-muted">
                    Токенизация возможна при оплате методами:
                    <ul>
                        <li>Банковская карта РФ и РБ</li>
                        <li>Банковские карты Visa и Mastercard карта вне РФ и РБ</li>
                        <li>СБП</li>
                        <li>SberPay</li>
                        <li>T-Pay</li>
                    </ul>
                </span>
            </label>
        </div>

        <div class='form-check mb-3'>
            <input name='receipt' value='yes' class='form-check-input' type='checkbox' id='receipt'>
            <label class='form-check-label' for='receipt'>
                Чек внешней кассы
            </label>
        </div>

        <div class='form-check mb-3'>
            <input name='split' value='yes' class='form-check-input' type='checkbox' id='split'>
            <label class='form-check-label' for='split'>
                Сплитование (разделение платежа между получателями)
            </label>
        </div>
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

        <input type="submit" class="btn btn-primary w-100" value="Оплатить">
    </fieldset>

</form>

<hr class="mb-3">
