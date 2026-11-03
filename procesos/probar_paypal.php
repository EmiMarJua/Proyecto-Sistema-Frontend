
<?php

require_once "../paypal_config.php";

$url = PAYPAL_API . "/v1/oauth2/token";

$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);

curl_setopt($curl, CURLOPT_USERPWD,
    PAYPAL_CLIENT_ID . ":" . PAYPAL_CLIENT_SECRET
);

curl_setopt($curl, CURLOPT_HTTPHEADER, [
    "Accept: application/json",
    "Accept-Language: es_MX"
]);

curl_setopt($curl, CURLOPT_POSTFIELDS,
    "grant_type=client_credentials"
);

$respuesta = curl_exec($curl);

$codigo = curl_getinfo($curl, CURLINFO_HTTP_CODE);

if (curl_errno($curl)) {

    echo "Error de conexión: " . curl_error($curl);

} else {

    $datos = json_decode($respuesta, true);

    if ($codigo == 200 && isset($datos["access_token"])) {

        echo "¡Conexión con PayPal Sandbox exitosa!";

    } else {

        echo "No se pudo conectar con PayPal.";
        echo "<br>Código HTTP: " . $codigo;

        if (isset($datos["error_description"])) {
            echo "<br>Detalle: " .
                htmlspecialchars($datos["error_description"]);
        }
    }
}

curl_close($curl);

?>
