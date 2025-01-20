<?php declare(strict_types=1);

namespace Ypmn;

class Std
{
    /**
     * Переадресация пользователя
     * @param string $url
     */
    public static function redirect(string $url)
    {
        if(!headers_sent()) {
            header('Location: ' . $url);
        } else {
            echo '
                <script>window.location.replace( \''.$url.'\' )</script>
            ';
        }

        exit();
    }

    /**
     * @param array $params
     * @return string
     */
    public static function alert(array $params)
    {
        return '        
            <div class="alert alert-' . ($params['type'] ?? 'info') . '" role="alert">
              ' .  $params['text'] . '
              <!-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
            </div>
        ';
    }

    /**
     * Удалить null-значения из массива
     * @param array $array
     * @return array
     */
    public static function removeNullValues(array $array) : array
    {
        foreach ($array as $key => $entry) {
            if (is_array($entry)) {
                $array[$key] = self::removeNullValues($entry);
                if ($array[$key] === []) {
                    unset($array[$key]);
                }
            } else {
                if ($entry === null) {
                    unset($array[$key]);
                }
            }
        }

        return $array;
    }

    /**
     * Вывести кнопку оплаты
     * @param array $params
     * @return string
     * @throws PaymentException
     */
    public static function drawYpmnButton(array $params): string
    {
        if (!isset($params['url'])) {
            throw new PaymentException('Передайте в метод drawYpmnButton параметр url');
        }

        if (!isset($params['sum'])) {
            throw new PaymentException('Передайте в метод drawYpmnButton параметр sum');
        }

        $allowedParams = [
            'url',
            'shadow',
            'currency',
            'sum',
            'order_id',
            'newpage',
        ];

        foreach ($params as $key=>$value) {
            if (!in_array($key, $allowedParams)) {
                throw new PaymentException('Недопустимый параметр кнопки оплаты');
            }
        }

        return '
<div class="payment_btn__wrapper">
<a 
    class="payment_btn__link"
    href="' . $params['url'] . '"
    '.( isset($params['newpage']) ? 'target="_blank"' : '' ).'
    title="Оплата через систему YPMN"
>
  <div class="payment_btn__logo">
    <svg xmlns="http://www.w3.org/2000/svg" 
         width="70" 
         height="70" 
         viewBox="0 0 160 162"
         style="
                display: inline;
                padding-right: 16px;
                float: left;
                "
         ><style>
                    @keyframes a1_t { 0% { transform: translate(100px,43.74px) rotate(0deg); animation-timing-function: cubic-bezier(0,0,.58,1); } 16.625% { transform: translate(100px,43.74px) rotate(-15deg); } 100% { transform: translate(100px,43.74px) rotate(0deg); } }
                    @keyframes a0_t { 0% { transform: scale(1,1) translate(-126.64px,-77.99px); animation-timing-function: cubic-bezier(0,0,.58,1); } 16.625% { transform: scale(1.2,1.2) translate(-126.64px,-77.99px); animation-timing-function: cubic-bezier(.42,0,1,1); } 100% { transform: scale(1,1) translate(-126.64px,-77.99px); } }
                    @keyframes a3_t { 0% { transform: translate(100px,43.74px) rotate(0deg); animation-timing-function: cubic-bezier(0,0,.58,1); } 33.3% { transform: translate(100px,43.74px) rotate(-15deg); } 100% { transform: translate(100px,43.74px) rotate(0deg); } }
                    @keyframes a2_t { 0% { transform: scale(1,1) translate(-126.64px,-77.99px); animation-timing-function: cubic-bezier(0,0,.58,1); } 33.3% { transform: scale(1.2,1.2) translate(-126.64px,-77.99px); animation-timing-function: cubic-bezier(.42,0,1,1); } 100% { transform: scale(1,1) translate(-126.64px,-77.99px); } }
                    @keyframes a5_t { 0% { transform: translate(100px,43.74px) rotate(0deg); animation-timing-function: cubic-bezier(0,0,.58,1); } 50% { transform: translate(100px,43.74px) rotate(-15deg); } 100% { transform: translate(100px,43.74px) rotate(0deg); } }
                    @keyframes a4_t { 0% { transform: scale(1,1) translate(-126.64px,-77.99px); animation-timing-function: cubic-bezier(0,0,.58,1); } 50% { transform: scale(1.15,1.15) translate(-126.64px,-77.99px); animation-timing-function: cubic-bezier(.42,0,1,1); } 100% { transform: scale(1,1) translate(-126.64px,-77.99px); } }
                    @keyframes a7_t { 0% { transform: translate(100px,43.74px) rotate(0deg); animation-timing-function: cubic-bezier(0,0,.58,1); } 75% { transform: translate(100px,43.74px) rotate(-12deg); animation-timing-function: cubic-bezier(0,0,.58,1); } 100% { transform: translate(100px,43.74px) rotate(0deg); } }
                    @keyframes a6_t { 0% { transform: scale(1,1) translate(-126.64px,-77.99px); animation-timing-function: cubic-bezier(0,0,.58,1); } 75% { transform: scale(1.1,1.1) translate(-126.64px,-77.99px); animation-timing-function: cubic-bezier(.42,0,1,1); } 100% { transform: scale(1,1) translate(-126.64px,-77.99px); } }
                    </style><g fill="none" transform="translate(7.26,3.76)"><g style="animation: 4s linear infinite both a1_t;"><path d="M131.59 67.09c-9.79-7.14-35.57-2.34-39.37 12.79c-4.63 18.41 17.27 34.97 34.42 25.09c12.29-7.08 14.72-30.73 4.95-37.88Z" fill="#80f3ae" transform="translate(100,43.74) translate(-126.64,-77.99)" style="animation: 4s linear infinite both a0_t;"/></g><g style="animation: 4s linear infinite both a3_t;"><path fill-rule="evenodd" clip-rule="evenodd" d="M141.97 78.4c-1.01-5.78-3.36-10.6-6.98-13.72c-7.43-6.39-20.66-7.64-33.03-4.13c-12.36 3.51-22.48 11.36-24.53 21.57c-2.46 12.22 .86 23.11 8.04 30.52c7.17 7.4 18.43 11.56 32.31 9.78c11.45-1.92 19.75-12.66 23.13-25.4c1.67-6.3 2.06-12.85 1.06-18.62Zm4.08 19.98c-3.65 13.73-13.03 26.9-27.45 29.28l-0.04 .01l-0.05 .01c-15.35 1.98-28.36-2.58-36.85-11.34c-8.49-8.77-12.21-21.48-9.44-35.27c2.63-13.04 15.08-21.88 28.29-25.63c13.22-3.75 28.6-2.83 37.95 5.22c4.81 4.15 7.59 10.23 8.74 16.83c1.15 6.62 .69 13.95-1.15 20.89Z" fill="#65d988" transform="translate(100,43.74) translate(-126.64,-77.99)" style="animation: 4s linear infinite both a2_t;"/></g><g style="animation: 4s linear infinite both a5_t;"><path fill-rule="evenodd" clip-rule="evenodd" d="M149.39 108.93c6.2-19.26 4.28-40.16-8.67-51.67c-9.52-8.46-27.12-10.8-44.45-6.22c-17.21 4.56-33.25 15.73-39.76 33.1c-5.38 14.34-0.85 31.2 10.01 43.98c10.84 12.74 27.65 21.02 46.21 18.44c16.64-2.3 30.46-18.37 36.66-37.63Zm5.06 1.62c-6.5 20.18-21.45 38.56-40.99 41.28c-20.65 2.86-39.18-6.39-50.98-20.27c-11.77-13.84-17.17-32.69-10.95-49.29c7.3-19.43 25.04-31.51 43.38-36.36c18.23-4.82 37.96-2.73 49.33 7.38c15.22 13.53 16.7 37.08 10.21 57.26Z" fill="#4fc694" transform="translate(100,43.74) translate(-126.64,-77.99)" style="animation: 4s linear infinite both a4_t;"/></g><g style="animation: 4s linear infinite both a7_t;"><path fill-rule="evenodd" clip-rule="evenodd" d="M161.81 78.82c-1.86-11.83-6.31-21.63-13.19-27.62c-14.81-12.89-40.55-14.61-64.49-7.42c-23.92 7.18-44.72 22.87-50.15 43.16c-6.14 22.97 2.18 45.73 17.83 61.91c15.68 16.19 38.43 25.49 60.7 21.76c24.19-4.05 40.57-27.22 47.14-53.46c3.26-13.02 4.02-26.54 2.16-38.33Zm2.99 39.62c-6.78 27.08-24.14 52.84-51.41 57.41c-24.28 4.07-48.73-6.09-65.39-23.31c-16.69-17.23-25.86-41.86-19.15-66.97c6.11-22.84 28.99-39.44 53.75-46.88c24.74-7.43 52.71-6.12 69.51 8.5c8.15 7.1 12.97 18.27 14.95 30.8c1.98 12.58 1.16 26.82-2.26 40.45Z" fill="#48a992" transform="translate(100,43.74) translate(-126.64,-77.99)" style="animation: 4s linear infinite both a6_t;"/></g></g>
                </svg>
  </div>
  <div class="payment_btn__sign">
    Оплатить<br>
    <span class="payment_btn__sum">' . number_format($params['sum'], 2, '.', ' ') . ' '. ( isset($params['currency']) ? htmlspecialchars($params['currency']) : '&#8381;' ) . '</span>
  </div>
</a>
<br>
  
  <img class="payment_btn__methods" src="data:image/png;base64,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" alt="" />
</div>

<style>
.payment_btn__wrapper {
  text-align: center;
  display: inline-block;
}

.payment_btn__link {
  display: inline-block;
  width: 200px;
  max-height: 150px;
  line-height: 20px;
  justify-content: center;
  background: #ffffff;
  color: #000000;
  border: 2px solid #4fc694;
  border-radius: 5px;
  padding: 8px;
  cursor: pointer;
  text-decoration: none;
  transition: 1s;
}

.payment_btn__link:hover,
.payment_btn__link:focus,
.payment_btn__link:active
{
  border-color: orange;
}

.payment_btn__logo {
  display: inline-block;
  min-height: 40px;
  float: left;
}

.payment_btn__sign {
  text-align: center;
  display: block;
  padding-top: 12px;
  font-size: 18px;
  font-weight: bolder;
}

.payment_btn__sum {
  display: inlin-block;
  font-size: 13pt;
}

.payment_btn__methods {
  display:inline-block;
  margin-top:4px;
  max-width: 190px;
}
</style>
        ';
    }

    /**
     * Транслитерация
     * @param $string
     * @return string
     */
    public static function rus2translit(string $string): string {
        $converter = [
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        ];

        return strtr($string, $converter);
    }

    /**
     * Преобразование кириллических имён в URI slugs
     * Удобно для продуктов, категорий и т.д.
     * @param string $str Строка на русском или английском
     * @return string URI
     */
    public static function str2uri(string $str) : string {
        // Спецсимволы
        $str = str_replace( '(', '', $str);
        $str = str_replace( '(', '', $str);
        $str = str_replace( "'", '', $str);
        $str = str_replace( ':', '', $str);
        $str = str_replace( "\xc3\x98", '', $str);
        $str = str_replace( '@', '', $str);
        $str = str_replace( '«', '', $str);
        $str = str_replace( '»', '', $str);

        // переводим в транслит
        $str = self::rus2translit($str);

        // в нижний регистр
        $str = strtolower($str);

        // заменям все ненужное нам на "-"
        $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);

        // удаляем начальные и конечные '-'
        $str = trim($str, "-");

        // тут немного частных случаев
        $str = str_replace( '----', '-', $str);
        $str = str_replace( '---', '-', $str);
        $str = str_replace( '--', '-', $str);

        return $str;
    }

    /**
     * Транслитерация JSON
     * @param $json_str
     * @return array|string|string[]
     */
    public static function json_fix_cyr($json_str) {
        $cyr_chars = [
            '\u0430' => 'а', '\u0410' => 'А',
            '\u0431' => 'б', '\u0411' => 'Б',
            '\u0432' => 'в', '\u0412' => 'В',
            '\u0433' => 'г', '\u0413' => 'Г',
            '\u0434' => 'д', '\u0414' => 'Д',
            '\u0435' => 'е', '\u0415' => 'Е',
            '\u0451' => 'ё', '\u0401' => 'Ё',
            '\u0436' => 'ж', '\u0416' => 'Ж',
            '\u0437' => 'з', '\u0417' => 'З',
            '\u0438' => 'и', '\u0418' => 'И',
            '\u0439' => 'й', '\u0419' => 'Й',
            '\u043a' => 'к', '\u041a' => 'К',
            '\u043b' => 'л', '\u041b' => 'Л',
            '\u043c' => 'м', '\u041c' => 'М',
            '\u043d' => 'н', '\u041d' => 'Н',
            '\u043e' => 'о', '\u041e' => 'О',
            '\u043f' => 'п', '\u041f' => 'П',
            '\u0440' => 'р', '\u0420' => 'Р',
            '\u0441' => 'с', '\u0421' => 'С',
            '\u0442' => 'т', '\u0422' => 'Т',
            '\u0443' => 'у', '\u0423' => 'У',
            '\u0444' => 'ф', '\u0424' => 'Ф',
            '\u0445' => 'х', '\u0425' => 'Х',
            '\u0446' => 'ц', '\u0426' => 'Ц',
            '\u0447' => 'ч', '\u0427' => 'Ч',
            '\u0448' => 'ш', '\u0428' => 'Ш',
            '\u0449' => 'щ', '\u0429' => 'Щ',
            '\u044a' => 'ъ', '\u042a' => 'Ъ',
            '\u044b' => 'ы', '\u042b' => 'Ы',
            '\u044c' => 'ь', '\u042c' => 'Ь',
            '\u044d' => 'э', '\u042d' => 'Э',
            '\u044e' => 'ю', '\u042e' => 'Ю',
            '\u044f' => 'я', '\u042f' => 'Я',
        ];

        foreach ($cyr_chars as $cyr_char_key => $cyr_char) {
            $json_str = str_replace($cyr_char_key, $cyr_char, $json_str);
        }
        return $json_str;
    }
}
