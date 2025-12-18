<form method="post" style="mb-2">
    <fieldset>
        <legend>Настройка платежа</legend>

        <label class='form-check-label' for='payment_method'>
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

        <div class='container mt-3'>
            <div class='row'>
                <div class='col-sm-2'>
                    <div class='form-check mb-2'>
                        <input class='form-check-input' type='radio' name='section' id='radio1' value='new_payment_and_tokenization' required>
                        <label class='form-check-label' for='radio1'>Оплата, токенизация</label>
                    </div>
                    <div class='form-check'>
                        <input class='form-check-input' type='radio' name='section' id='radio2' value='pay_with_token' required>
                        <label class='form-check-label' for='radio2'>Оплата токеном</label>
                    </div>
                </div>
                <div class='col-sm-10'>
                    <div id='new_payment_and_tokenization' class='d-none border p-2 mb-2'>
                        <label class='form-check-label' for='tokenization'>
                            <input name='tokenization' value='yes' class='form-check-input' type='checkbox'
                                   id='tokenization'>
                            Токенизация (сохранение платёжных данных для повторных оплат)
                        </label>
                        <span class='text-muted d-block'>
                            Токенизация возможна при оплате методами:
                            <ul>
                                <li>Банковская карта РФ и РБ</li>
                                <li>Банковские карты Visa и Mastercard карта вне РФ и РБ</li>
                                <li>СБП</li>
                                <li>SberPay</li>
                                <li>T-Pay</li>
                            </ul>
                            Токен придёт в вебхуке после совершении оплаты (параметр authorization.storedCredentials.ypmnBindingId)
                        </span>

                        <br>
                        <label class='form-check-label' for='consentType'>
                            <input name='consentType'
                                   value='recurring'
                                   class='form-check-input'
                                   type='checkbox'
                                   id='consentType'>
                            Рекуррентный (повторяющийся) платёж
                        </label>

                        <br>
                        <br>
                        <label class='form-check-label' for='subscriptionPurpose'>
                            Обоснование токенизации
                        </label>
                            <input
                                name='subscriptionPurpose'
                                class='form-control mb-3 w-100'
                                type='text'
                                id='subscriptionPurpose'
                                value="Подписка на сервис X"
                            >
                    </div>

                    <div id='pay_with_token' class='d-none border p-2'>
                        <label class='form-check-label' for='token_string'>
                            Токен
                        </label>
                        <input name='token_string' class='form-control w-100 mb-3' type='text' id='token_string'>
                    </div>
                </div>
            </div>
        </div>
        <script>
            // Это переключение первой оплаты и оплаты токеном
            const radios = document.querySelectorAll('input[name="section"]');
            radios.forEach(radio => {
                radio.addEventListener('change', function () {
                    document.getElementById('new_payment_and_tokenization').classList.add('d-none');
                    document.getElementById('pay_with_token').classList.add('d-none');
                    if (this.value === 'new_payment_and_tokenization') {
                        document.getElementById('new_payment_and_tokenization').classList.remove('d-none');
                    } else if (this.value === 'pay_with_token') {
                        document.getElementById('pay_with_token').classList.remove('d-none');
                    }
                });
            });
        </script>


        <div class='container mt-3'>
            <div class="row">
                <div class="col-12">
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
                </div>
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
            <input name='debug' value='yes' class='form-check-input' type='checkbox' id='debug' checked>
            <label class='form-check-label' for='debug'>
                Режим дебага (вывода сообщений для отладки)
            </label>
        </div>

        <input type="submit" class="btn btn-primary w-100" value="Оплатить">
    </fieldset>

</form>

<hr class="mb-3">
