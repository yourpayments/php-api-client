<?php

declare(strict_types=1);

namespace Ypmn;

/**
 * Класс для работы с виджетом.
 */
final class Widget
{
    // Код мерчанта.
    public string $merchantCode;
    // Код страны.
    public string $countryCode;
    // Имя.
    public string $firstName;
    // Фамилия.
    public string $lastName;
    // Email.
    public string $email;
    // Телефон.
    public string $phone;
    // Ссылка возврата.
    public string $returnUrl;

    /**
     * @param string $merchantCode Код мерчанта.
     * @param string $countryCode  Код страны.
     * @param string $firstName    Имя.
     * @param string $lastName     Фамилия.
     * @param string $email        Email.
     * @param string $phone        Телефон.
     * @param string $returnUrl    Ссылка возврата.
     */
    public function __construct(
        string $merchantCode,
        string $countryCode = "RU",
        string $firstName = "Not",
        string $lastName = "Set",
        string $email = "email@nomail.ru",
        string $phone = "9990000000",
        string $returnUrl = "https://ya.ru/"
    ) {
        $this->merchantCode = $merchantCode;
        $this->countryCode = $countryCode;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->phone = $phone;
        $this->returnUrl = $returnUrl;
    }

    /**
     * Метод возвращает кнопку покупки.
     *
     * @param float  $amounts  Сумма к оплате.
     * @param string $currency Валюта.
     * @param string $label    Надпись на кнопке.
     * @param string $classes  Классы кнопки.
     *
     * @return string
     */
    public function makeBuyButton(
        float $amount,
        string $currency = "RUB",
        string $label = "Купить",
        string $classes = ""
        ): string {
            $classes = trim("buyBtn " . $classes);
            $params = "data-amount='{$amount}' data-currency='{$currency}'";

            return "<a href='#' class='{$classes}' {$params}>{$label}</a>";
    }

    /**
     * Метод возвращает скрипты для вставки в html.
     *
     * @return string
     */
    public function makeBuyForm(): string
    {
        return "<script src='https://secure.ypmn.ru/pay/widget/v1/js/YPMNFrames.js'></script>
        <script src='https://secure.ypmn.ru/assets/js/crypto-js/4.2.0/crypto-js.min.js'></script>
        <script>
            window.document.body.onload = function() {
                const payButtons = document.getElementsByClassName('buyBtn');
                for (let payButton of payButtons) {
                    payButton.addEventListener('click', async () => {
                        const ypmn = new YPMNFrames(
                            {
                                merchantCode:             '{$this->merchantCode}',
                                countryCode:              '{$this->countryCode}',
                                currency:                 payButton.getAttribute('data-currency'),
                                merchantPaymentReference: '{$this->merchantCode}' + Math.floor(Math.random() * 1000) + Date.now(),
                                firstName:                '{$this->firstName}',
                                lastName:                 '{$this->lastName}',
                                email:                    '{$this->email}',
                                phone:                    '{$this->phone}',
                                paymentSum:               payButton.getAttribute('data-amount'),
                                returnUrl:                '{$this->returnUrl}',
                                paymentMethod:            'CCVISAMC',
                                signature:                '',
                            },
                            document.body.clientWidth,
                            Math.max(document.body.scrollHeight, window.screen.availHeight)
                        );
                        // Подписываем параметры
                        ypmn.data.payment.signature = doSigned(ypmn.data.payment);
                        ypmn.start();
                    });
                }
            };
            if (window.location.hash === '#close') {
                window.top.postMessage('SIGHUP', '*');
            }
            window.addEventListener('hashchange', (event) => {
                if (window.location.hash === '#close') {
                    window.top.postMessage('SIGHUP', '*');
                }
            });
            function doSigned(params) {
                let raw = '';

                Object.keys(params).forEach(k => {
                    if (k !== 'returnUrl' && k !== 'signature') {
                        raw += params[k];
                    }
                });

                return CryptoJS.SHA256(raw).toString();
            }
        </script>";
    }
}
