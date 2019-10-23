<?php
$i = 0;
while ( $i < 300) {
    $customers['add'] = array(
        array(
            'name' => 'Людмила',
            'next_date' => '1508792400',
            'created_at' => '1508619600',
            'responsible_user_id' => 504141,
            'created_by' => 504141,
            'next_price' => 7000,
            'periodicity' => 7,
            'tags' => "Продажи,Карандаши",
            'period_id' => 153795,
            'contacts_id' => array(468986, 468979),
            'company_id' => 356463,
            'custom_fields' => array(
                array(
                    'id' => 4400017,
                    'values' => array(
                        array(
                            'value' => "Важный клиент",
                        ),
                    ),
                ),
                array(
                    'id' => 4400021,
                    'values' => array(
                        "3692469",
                        "3692470",
                        "3692471",
                    ),
                ),
                array(
                    'id' => 458615, #Уникальный индентификатор заполняемого дополнительного поля
                    'values' => array(
                        array(
                            'value' => 'Address line 1',
                            'subtype' => 'address_line_1',
                        ),
                        array(
                            'value' => 'Address line 2',
                            'subtype' => 'address_line_2',
                        ),
                        array(
                            'value' => 'Город',
                            'subtype' => 'city',
                        ),
                        array(
                            'value' => 'Регион',
                            'subtype' => 'state',
                        ),
                        array(
                            'value' => '203',
                            'subtype' => 'zip',
                        ),
                        array(
                            'value' => 'RU',
                            'subtype' => 'country',
                        ),
                    ),
                ),
            ),
        ),
        array(
            'name' => 'Антон',
            'created_at' => 1298904164,
            'next_date' => '1508782500',
            'next_price' => 600200,
            'responsible_user_id' => 215309,
            'custom_fields' => array(
                array(
                    #Нестандартное дополнительное поле типа "мультисписок", которое мы создали
                    'id' => 426106,
                    'values' => array(
                        1237756,
                        1237758,
                    ),
                ),
            ),
        ),
    );
    ++$i;

/* Теперь подготовим данные, необходимые для запроса к серверу */
#Формируем ссылку для запроса
    $link = 'https://granula2000.amocrm.ru/api/v2/customers';
    /* Нам необходимо инициировать запрос к серверу. Воспользуемся библиотекой cURL (поставляется в составе PHP). Подробнее о
    работе с этой
    библиотекой Вы можете
    прочитать в мануале. */
    $curl = curl_init(); #Сохраняем дескриптор сеанса cURL
#Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
    curl_setopt($curl, CURLOPT_URL, $link);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($customers));
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    $out = curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    /* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
    $code = (int) $code;
    $errors = array(
        301 => 'Moved permanently',
        400 => 'Bad request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not found',
        500 => 'Internal server error',
        502 => 'Bad gateway',
        503 => 'Service unavailable',
    );
    try
    {
        #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
        if ($code != 200 && $code != 204) {
            throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
        }

    } catch (Exception $E) {
        die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
    }
}