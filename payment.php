<?php
if (!isset($_GET['user_id'])) {
    die("معلومات المستخدم مفقودة.");
}
$user_id = $_GET['user_id'];

// توجيه المستخدم إلى PayPal للدفع
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إتمام الدفع</title>
</head>
<body>
    <h2 class="text-center">إتمام الدفع عبر PayPal</h2>

    <script src="https://www.paypal.com/sdk/js?client-id=BAAiyxDV45uB5udtTX9Tma4scGsK_na_pMmDTuv7NyCBaKhTxa239gM_0UlTXKVaim5NVF2vAxhXlQZA_U&components=buttons&currency=USD"></script>
    <div id="paypal-button-container"></div>
    <script>
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '20.00'
                    }
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                // بعد إتمام الدفع بنجاح
                window.location.href = "confirm.php?user_id=" + <?= json_encode($user_id) ?>;
            });
        }
    }).render('#paypal-button-container');
    </script>
</body>
</html>
