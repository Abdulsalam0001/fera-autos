<?php
// process-contact.php
$admin_email = "admin@feralautos.org";

// Collect form data safely
function get_post($key) {
    return isset($_POST[$key]) ? htmlspecialchars(trim($_POST[$key])) : '';
}

$name = get_post('name');
$email = get_post('email');
$subject = get_post('subject');
$message = get_post('message');

// Prepare email
$mail_subject = "Contact Form Submission: " . ($subject ?: 'No Subject');
$mail_body = "You have received a new message from the contact form on Feral Autos.\n\n";
$mail_body .= "Name: $name\nEmail: $email\nSubject: $subject\nMessage:\n$message\n";
$headers = "From: $name <$email>\r\n";
$headers .= "Reply-To: $email\r\n";

if (mail($admin_email, $mail_subject, $mail_body, $headers)) {
    header('Location: message-sent.html');
    exit();
} else {
    echo '<p>Sorry, there was an error sending your message. Please try again later.</p>';
}
?>
