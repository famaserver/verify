<?php
$response = '';
$boxClass = 'error'; // پیش‌فرض رنگ قرمز
include('db.php'); // فایل اتصال به دیتابیس

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mobile = $_POST['mobile'];
    $national_code = $_POST['national_code'];

    if (empty($mobile) || empty($national_code)) {
        $response = "شماره موبایل و کد ملی باید وارد شوند.";
    } else {
        // بررسی اینکه آیا این استعلام قبلاً در دیتابیس ذخیره شده است یا خیر
        $stmt = $conn->prepare("SELECT * FROM inquiries WHERE mobile = ? AND national_code = ?");
        $stmt->bind_param("ss", $mobile, $national_code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // اگر استعلام قبلاً انجام شده باشد، پاسخ از دیتابیس می‌آید
            $row = $result->fetch_assoc();
            if ($row['matched'] == 1) {
                $response = "استعلام تکراری است. نتیجه قبلی: موفق";
                $boxClass = 'success';
            } else {
                $response = "استعلام تکراری است. نتیجه قبلی: نتیجه برابر نیست";
                $boxClass = 'error';
            }
        } else {
            // اگر استعلام جدید باشد، درخواست به وب‌سرویس ارسال می‌شود
            $url = "https://api.rokla.ir/v2/api/inquiry/shahkar/";
            $token = "PASTE-TOKEN";

            $headers = [
                "x-api-key: $token",
                "Content-Type: application/json",
            ];

            $data = [
                "mobile" => $mobile,
                "national_code" => $national_code,
            ];

            $jsonData = json_encode($data);

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $jsonData,
                CURLOPT_HTTPHEADER => $headers,
            ]);

            $response = curl_exec($curl);

            if (curl_errno($curl)) {
                $response = "خطا در ارتباط با وب‌سرویس: " . curl_error($curl);
            }

            curl_close($curl);

            // تبدیل پاسخ JSON به آرایه
            $responseData = json_decode($response, true);
            
            if ($responseData && isset($responseData['data']['matched'])) {
                // ذخیره‌سازی در دیتابیس
                $matched = $responseData['data']['matched'] ? 1 : 0;
                $stmt = $conn->prepare("INSERT INTO inquiries (mobile, national_code, matched) VALUES (?, ?, ?)");
                $stmt->bind_param("ssi", $mobile, $national_code, $matched);
                $stmt->execute();
                if ($matched === 1) {
                    $response = "استعلام با موفقیت انجام شد. نتیجه: موفق";
                    $boxClass = 'success';
                } else {
                    $response = "استعلام انجام شد،  نتیجه: نتیجه برابر نیست";
                    $boxClass = 'error';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ارسال اطلاعات به وب‌سرویس</title>
    <style>
        body {
            background-color: #007bff;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #fff;
        }
        .form-container {
            background-color: #fff;
            color: #000;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            font-size: 16px;
            margin-bottom: 5px;
            display: block;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .response {
            margin-top: 20px;
            padding: 10px;
            color: #721c24;
            border-radius: 5px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h1>فرم ارسال اطلاعات</h1>
    <form method="POST" action="">
        <label for="mobile">شماره موبایل:</label>
        <input type="text" id="mobile" name="mobile" required>

        <label for="national_code">کد ملی:</label>
        <input type="text" id="national_code" name="national_code" required>

        <button type="submit">ارسال</button>
    </form>

    <?php if (!empty($response)): ?>
        <div class="response <?php echo $boxClass; ?>">
            <strong>پاسخ وب‌سرویس:</strong>
            <pre><?php echo htmlspecialchars($response); ?></pre>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
