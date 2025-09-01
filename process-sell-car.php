<?php
// process-sell-car.php
$admin_email = "support@feralautos.org"; // Change to your admin email

// Collect form data
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$car_make = $_POST['car_make'] ?? '';
$car_model = $_POST['car_model'] ?? '';
$year = $_POST['year'] ?? '';
$mileage = $_POST['mileage'] ?? '';
$location = $_POST['location'] ?? '';
$price = $_POST['price'] ?? '';
$details = $_POST['details'] ?? '';

// Prepare email body
$subject = "New Car Sale Submission from $name";
$message = "You have received a new car sale submission:\n\n";
$message .= "Name: $name\nEmail: $email\nPhone: $phone\n";
$message .= "Car Make: $car_make\nCar Model: $car_model\nYear: $year\n";
$message .= "Mileage: $mileage\nLocation: $location\nPrice: $price\n";
$message .= "Details: $details\n";

// Handle file uploads and attachments (mail with attachments)
$boundary = md5(uniqid(time()));
$headers = "From: $name <$email>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

$body = "--$boundary\r\n";
$body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
$body .= $message . "\r\n";

$fields = [
    'car_images_back_side',
    'car_images_front_side',
    'car_images_bonnet',
    'car_images_engine',
    'car_images_driver_door',
    'car_images_passenger_door',
    'car_images_dashboard',
    'car_images_interior_roof',
    'car_images_back_seat'
];
foreach ($fields as $field) {
    if (!empty($_FILES[$field]['name'][0])) {
        foreach ($_FILES[$field]['tmp_name'] as $key => $tmp_name) {
            if (is_uploaded_file($tmp_name)) {
                $file_name = $_FILES[$field]['name'][$key];
                $file_type = $_FILES[$field]['type'][$key];
                $file_data = file_get_contents($tmp_name);
                $file_data = chunk_split(base64_encode($file_data));
                $body .= "--$boundary\r\n";
                $body .= "Content-Type: $file_type; name=\"$file_name\"\r\n";
                $body .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n";
                $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
                $body .= $file_data . "\r\n";
            }
        }
    }
}
$body .= "--$boundary--";

if (mail($admin_email, $subject, $body, $headers)) {
    header('Location: https://feralautos.org/thank-you.html');
    exit();
} else {
    echo '<!DOCTYPE html><html lang="en"><head>
<link rel="apple-touch-icon-precomposed" sizes="57x57" href="apple-touch-icon-57x57.png" />
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="apple-touch-icon-114x114.png" />
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="apple-touch-icon-72x72.png" />
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="apple-touch-icon-144x144.png" />
<link rel="apple-touch-icon-precomposed" sizes="60x60" href="apple-touch-icon-60x60.png" />
<link rel="apple-touch-icon-precomposed" sizes="120x120" href="apple-touch-icon-120x120.png" />
<link rel="apple-touch-icon-precomposed" sizes="76x76" href="apple-touch-icon-76x76.png" />
<link rel="apple-touch-icon-precomposed" sizes="152x152" href="apple-touch-icon-152x152.png" />
<link rel="icon" type="image/png" href="favicon-196x196.png" sizes="196x196" />
<link rel="icon" type="image/png" href="favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/png" href="favicon-32x32.png" sizes="32x32" />
<link rel="icon" type="image/png" href="favicon-16x16.png" sizes="16x16" />
<link rel="icon" type="image/png" href="favicon-128.png" sizes="128x128" />
<meta name="application-name" content="&nbsp;"/>
<meta name="msapplication-TileColor" content="#FFFFFF" />
<meta name="msapplication-TileImage" content="mstile-144x144.png" />
<meta name="msapplication-square70x70logo" content="mstile-70x70.png" />
<meta name="msapplication-square150x150logo" content="mstile-150x150.png" />
<meta name="msapplication-wide310x150logo" content="mstile-310x150.png" />
<meta name="msapplication-square310x310logo" content="mstile-310x310.png" />
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Submission Error - Feral Autos</title><link rel="stylesheet" href="/css/bootstrap.min.css"><link rel="stylesheet" href="/css/font-awesome.min.css"><link rel="stylesheet" href="/css/style.css"></head><body><div class="container text-center mt-5"><h2>Sorry, there was an error sending your submission.</h2><p>Please try again later or contact support@feralautos.org.</p><a href="/sell-car.html" class="site-btn mt-3">Back to Form</a></div></body></html>';
    exit();
}
?>
