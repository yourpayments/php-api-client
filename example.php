<?php
/**
 * Пример интеграции API многофункциональной платёжной системы Ypmn, версия 4
 * Документация:
 *      https://dev.payu.ru/ru/documents/apiv4/
 *      https://secure.payu.ru/docs/#tag/Payment-API
 * Начните знакомство с кодом с текущего файла
 *  и класса PaymentInterface
 */

use Ypmn\Authorization;
use Ypmn\Delivery;
use Ypmn\IdentityDocument;
use Ypmn\Merchant;
use Ypmn\MerchantToken;
use Ypmn\Payment;
use Ypmn\Client;
use Ypmn\Billing;
use Ypmn\ApiRequest;
use Ypmn\PaymentException;
use Ypmn\Product;
use Ypmn\Capture;
use Ypmn\Refund;
use Ypmn\Std;
use Ypmn\PaymentReference;


if(isset($_GET['function'])){
    try {
        switch ($_GET['function']) {
            case 'start':
                include './src/Examples/'.$_GET['function'] . '.php';
                break;
            case 'simpleGetPaymentLink':
            case 'getPaymentLink':
            case 'getPaymentLinkMarketplace':
            case 'getToken':
            case 'paymentByToken':
            case 'paymentCapture':
            case 'paymentGetStatus':
            case 'payoutCreate':
            case 'paymentWebhook':
            case 'paymentRefund':
            case 'paymentRefundMarketplace':
            case 'getSession':
            case 'oneTimeTokenPayment':
            case 'returnPage':
            case 'secureFields':
            case 'getReportGeneral':
            case 'getReportChart':
            case 'getReportOrder':
            case 'getReportOrderDetails':
            case 'getFasterPayment':
            case 'getBindingFasterPayment':
            case 'paymentByFasterBinding':
            case 'qstCreateOrg':
            case 'qstCreateIp':
            case 'qstStatus':
            case 'qstPrint':
            case 'SOMGetPaymentLink':
            case 'qstList':
                require './src/Examples/start.php';
                @include './src/Examples/'.$_GET['function'] . '__prepend.php';
                require './src/Examples/'.$_GET['function'] . '.php';
                break;

            default:
                throw new PaymentException('Метод не поддерживается');
        }
    } catch (PaymentException $e) {
        //TODO: добавить проверки и выброс исключений
        //TODO: добавить в исключения ссылки на документацию
        echo $e->getHtmlMessage();
    }
}
