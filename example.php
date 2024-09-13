<?php
/**
 * Пример интеграции API платёжной системы Ypmn
 * Документация:
 *      https://ypmn.ru/ru/documentation/
 * Начните знакомство с кодом с текущего файла
 *  и класса PaymentInterface
 */

use Ypmn\PaymentException;
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
