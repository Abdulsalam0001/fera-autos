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

$fields = ['car_images_front', 'car_images_back', 'car_images_interior', 'car_images_other'];
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

$mail_sent = mail($admin_email, $subject, $body, $headers);

if ($mail_sent) {
    echo "<p>Thank you! Your car details have been submitted successfully. We will contact you soon.</p>";
} else {
    echo "<p>Sorry, there was an error sending your submission. Please try again later.</p>";
}
?>
