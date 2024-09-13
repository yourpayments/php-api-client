<?php declare(strict_types=1);

namespace Ypmn;

use DateTime;
use DateTimeInterface;

/**
 * Класс отправки запроса к API
 */
class CaptureApiRequest
{
    const AUTHORIZE_API = '/api/v4/payments/refund';
    const HOST = 'https://sandbox.payu.ru';

    public function sendRequest(CaptureInterface $capture, MerchantInterface $merchant): array
    {
        $encodedJsonData = $capture->jsonSerialize();
        $encodedJsonDataHash = md5($encodedJsonData);

        $curl = curl_init();
        $date = (new DateTime())->format(DateTimeInterface::ATOM);
        $urlToPostTo = self::HOST . self::AUTHORIZE_API;
        $requestHttpVerb = 'POST';

        curl_setopt_array($curl, [
            CURLOPT_URL => $urlToPostTo,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $requestHttpVerb,
            CURLOPT_POSTFIELDS => $encodedJsonData,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'X-Header-Date: ' . $date,
                'X-Header-Merchant: ' . $merchant->getCode(),
                'X-Header-Signature:' . $this->getSignature(
                    $merchant,
                    $date,
                    $urlToPostTo,
                    $requestHttpVerb,
                    $encodedJsonDataHash)
            ]
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        return ['response' => $response, 'error' => $err];
    }

    private function getSignature(MerchantInterface $merchant, $date, $url, $httpMethod, $bodyHash): string
    {
        $urlParts = parse_url($url);
        $urlHashableParts = $httpMethod . $urlParts['path'] . $urlParts['query'];
        $hashableString = $merchant->getCode() . $date . $urlHashableParts . $bodyHash;

        return hash_hmac('sha256', $hashableString, $merchant->getSecret());
    }
}
