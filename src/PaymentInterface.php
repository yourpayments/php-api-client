<?php
namespace Ypmn;

interface PaymentInterface
{
    /**
     * Установить Номер Заказа
     * Используйте удобный вам номер заказа или счёта, уникальный в вашей системе
     * @param string $paymentIdString Номер Заказа
     * @return $this
     */
    public function setMerchantPaymentReference(string $paymentIdString) : self;

    /**
     * Получить Номер платежа
     * @return string Номер платежа
     */
    public function getMerchantPaymentReference() : string;

    /**
     * Установить Код валюты (например, RUB)
     * формат кодов валюты ISO 4217
     * (https://en.wikipedia.org/wiki/ISO_4217)
     * @param string|null $currency Код валюты
     * @return $this
     */
    public function setCurrency(?string $currency) : self;

    /**
     * Получить Код валюты
     * @return string Код валюты
     */
    public function getCurrency() : string;

    /**
     * URL возврата Клиента после оплаты
     * @param string $returnUrl URL возврата Клиента после оплаты
     * @return $this
     */
    public function setReturnUrl(string $returnUrl) : self;

    /**
     * Получить URL возврата Клиента после оплаты
     * Это страница, которую ваш Клиент должен увидеть по завершению платежа
     * На эту страницу будут переданы GET-праметры о статусе платежа
     * Например,
     *    Если не указывать - статус оплаты отображается на платежной странице и редиректа не происходит
     *    если редирект происходит - добавляются несколько полей в виде GET-запроса
     *    GET-запрос удачный:
     *    ?result=0&3dsecure=YES&date=2018-11-01%2018%3A30%3A31&payrefno=51448952&ctrl=b9dd647b1f532c2de00a574a662798f0
     *    GET-запрос неудачный:
     *    ?result=1&3dsecure=YES&date=2018-10-15%2017%3A08%3A16&payrefno=50605885&ctrl=c4ac00d5f30129c9596721f70f4d58f7
     * @return string URL возврата Клиента после оплаты
     */
    public function getReturnUrl() : string;

    /**
     * Установить Авторизацию
     * Должен содерждать paymentMethod и usePaymentPage
     * @param AuthorizationInterface $authorization Авторизация
     * @return $this
     */
    public function setAuthorization(AuthorizationInterface $authorization) : self;

    /**
     * Получить Объект авторизации
     * @return AuthorizationInterface Авторизация
     */
    public function getAuthorization() : AuthorizationInterface;

    /**
     * Получить объект с учетными данными
     *
     * @return StoredCredentialsInterface Учетные данные для подписок
     */
    public function getStoredCredentials(): StoredCredentialsInterface;

    /**
     * Установить объект с учетными данными
     *
     * @param StoredCredentialsInterface $storedCredentials Учетные данные для подписок
     *
     * @return self
     */
    public function setStoredCredentials(StoredCredentialsInterface $storedCredentials): self;

    /**
     * Установить Клиента
     * @param ClientInterface $client Клиент
     * @return $this
     */
    public function setClient(ClientInterface $client) : self;

    /**
     * Получить Клиента
     * @return ClientInterface
     */
    public function getClient() : ClientInterface;

    /**
     * Добавить Продукт
     * @param ProductInterface $product Продукт
     * @return $this
     */
    public function addProduct(ProductInterface $product) : self;

    /**
     * Получить Продукты как массив объектов
     * @return Product[] Продукты
     */
    public function getProducts() : array;

    /**
     * Получить Продукты как ассоциативный массив
     * @return array Продукты
     */
    public function getProductsArray() : array;

    /**
     * Итоговая сумма товарных позиций
     * @return int Итог
     * @throws PaymentException Ошибка оплаты
     */
    public function sumProductsAmount() : float;

    /**
     * @return string|bool
     */
    public function jsonSerialize();
}
