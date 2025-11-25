<?php
// Simulated Paystack checkout has been removed.
http_response_code(410);
header('Content-Type: text/plain');
echo "Simulated Paystack checkout page has been removed. Use the real Paystack redirect flow instead.";
exit;
?>
