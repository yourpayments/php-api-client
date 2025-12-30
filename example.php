<?php
/**
 * Примеры интеграции API платёжной системы Ypmn
 * Документация:
 *      https://ypmn.ru/ru/documentation/
 * Начните знакомство с кодом с текущего файла
 *  и класса PaymentInterface
 */

use Ypmn\PaymentException;
use Ypmn\Std;

if(!empty($_GET['function'])){
    try {
        switch ($_GET['function']) {
            case 'start':
                include './src/Examples/'.$_GET['function'] . '.php';
                break;
            case 'authorize':
            case 'capture':
            case 'token_payment':
            case 'status':
            case 'refund':
            case 'payout':
            case 'simpleGetPaymentLink':
            case 'getPaymentLink':
            case 'getPaymentLinkMarketplace':
            case 'getPaymentLinkMarketplaceWithReceipts':
            case 'getToken':
            case 'paymentByToken':
            case 'paymentCapture':
            case 'paymentGetStatus':
            case 'payoutCreate':
            case 'payoutGetBalance':
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
            case 'getPaymentLinkWithReceipt':
            case 'getBindingFasterPayment':
            case 'paymentByFasterBinding':
            case 'paymentByBindingPays':
            case 'getBindingPays':
            case 'qstCreateOrg':
            case 'qstCreateIp':
            case 'qstStatus':
            case 'qstPrint':
            case 'SOMGetPaymentLink':
            case 'qstList':
            case 'webhookProcessing':
            case 'payQrCode':
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
} else {
    echo Std::alert([
        'type' => 'success',
        'text' => '
            Добро пожаловать на тестовый сервер с примерами для интеграции 
            с помощью SDK от <a href="http://ypmn.ru/?from=SDK_PHP">НКО &laquo;Твои Платежи&raquo;</a>.
            <br>
            <br>
            Выберите пример из меню.
        '
    ]);
}
