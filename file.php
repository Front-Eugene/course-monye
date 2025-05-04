<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['mail']), FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST['phone']);
    $countryCode = trim($_POST['country_code']);

    if (empty($countryCode) && preg_match('/^\+(\d{1,4})/', $phone, $matches)) {
        $countryCode = $matches[1];
        $phone = preg_replace('/^\+\d{1,4}/', '', $phone);
    }

    $phone = preg_replace('/\D/', '', $phone);

    if (empty($phone) || empty($countryCode)) {
        die("Ошибка: некорректный номер телефона!");
    }

    $formattedPhone = '+' . $countryCode . $phone;

    // === ОТПРАВКА В TELEGRAM ===
    $TOKEN = "7755614311:AAHTuZLaql7O6n6bc6YTlYGVITOzVENeo3M";
    $CHAT_ID = "-1002472798168";
    $message = "<b>Заявка на бесплатный курс</b>\n";
    $message .= "<b>Имя:</b> $name\n";
    $message .= "<b>Почта:</b> $email\n";
    $message .= "<b>Телефон:</b> $formattedPhone\n";

    $URI_API = "https://api.telegram.org/bot$TOKEN/sendMessage";
    $data = [
        'chat_id' => $CHAT_ID,
        'parse_mode' => 'html',
        'text' => $message
    ];

    $ch = curl_init($URI_API);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_exec($ch);
    curl_close($ch);

    // === ОТПРАВКА В GETCOURSE ===
    $api_key = 'eqgnURtinxggHCW0VVuy5uhuKGTuTNCwUEiJo4UYGAQ4yFNkfLYpYNeUEv94XjXKnkYt0x6dloI4Qhx8voMHFa3KPEpgVq5IJbLckd3staqh5mSsfIfAJqgR8Pd3ndXL';
    $getcourse_url = "https://rbcourses.getcourse.ru/pl/api/public/users?key=$api_key";
    
    $formData = [
        'email' => $email,
        'phone' => $formattedPhone,
        'first_name' => $name,
    ];

    $ch = curl_init($getcourse_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($formData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
    ]);

    $response = curl_exec($ch);
    
    if ($response === false) {
        die('Ошибка отправки в GetCourse: ' . curl_error($ch));
    } else {
        file_put_contents('getcourse_log.txt', date('Y-m-d H:i:s') . " - " . $response . PHP_EOL, FILE_APPEND);
        echo "Данные успешно отправлены в GetCourse!";
    }

    curl_close($ch);
    header('Location: thanks.html');
    exit;
}
?>


<?php
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $name = htmlspecialchars(trim($_POST['name']));
//     $email = filter_var(trim($_POST['mail']), FILTER_SANITIZE_EMAIL);
//     $phone = trim($_POST['phone']);
//     $countryCode = trim($_POST['country_code']);

//     // Если код страны пустой, пробуем вытащить из номера телефона
//     if (empty($countryCode) && preg_match('/^\+(\d{1,4})/', $phone, $matches)) {
//         $countryCode = $matches[1];
//         $phone = preg_replace('/^\+\d{1,4}/', '', $phone); // Убираем код страны из номера
//     }

//     // Проверяем, что номер телефона содержит только цифры
//     $phone = preg_replace('/\D/', '', $phone);

//     // Если код страны или номер пустые – ошибка
//     if (empty($phone) || empty($countryCode)) {
//         die("Ошибка: некорректный номер телефона!");
//     }

//     // Форматируем номер
//     $formattedPhone = '+' . $countryCode . $phone;

//     // === ОТПРАВКА В TELEGRAM ===
//     $TOKEN = "7755614311:AAHTuZLaql7O6n6bc6YTlYGVITOzVENeo3M";
//     $CHAT_ID = "-1002472798168";
//     $message = "<b>Заявка на бесплатный курс</b>\n";
//     $message .= "<b>Имя:</b> $name\n";
//     $message .= "<b>Почта:</b> $email\n";
//     $message .= "<b>Телефон:</b> $formattedPhone\n";

//     $URI_API = "https://api.telegram.org/bot$TOKEN/sendMessage";
//     $data = [
//         'chat_id' => $CHAT_ID,
//         'parse_mode' => 'html',
//         'text' => $message
//     ];

//     $ch = curl_init($URI_API);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_POST, true);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//     curl_exec($ch);
//     curl_close($ch);

//     // === ОТПРАВКА В GETCOURSE ===
//     // $getcourse_url = "https://rbcourses.getcourse.ru/pl/lite/block-public/process?id=2141385004";
//       $getcourse_url = "https://rbcourses.getcourse.ru/pl/api/users ";
//     $formData = [
//         'formParams[name]' => $name,
//         'formParams[email]' => $email,
//         'formParams[phone]' => $formattedPhone,
//         'system[refresh]' => '1'
//     ];
    
//     $ch = curl_init($getcourse_url);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_POST, true);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($formData));
    
//     // Добавляем заголовки, если это нужно
//     curl_setopt($ch, CURLOPT_HTTPHEADER, [
//         'Content-Type: application/x-www-form-urlencoded',
//     ]);
    
//     $response = curl_exec($ch);
    
//     // Логируем ответ
//     if($response === false) {
//         die('Ошибка отправки данных в GetCourse: ' . curl_error($ch));
//     } else {
//         echo 'Ответ от GetCourse: ' . $response; // Выводим ответ от сервера
//     }
    
//     curl_close($ch);

//     // Перенаправляем на страницу благодарности
//     header('Location: thanks.html');
//     exit;
// }
?>