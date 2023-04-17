<?php
/**
 * SendPulse SMS Gateway
 * Made by Renato
 * https://sms.mxedia.com
 */

define("SENDPULSE_GATEWAY", [
    "siteUrl" => "https://api.sendpulse.com",
    "clientId" => "", // Coloca aquí tu ID de cliente de SendPulse
    "clientSecret" => "", // Coloca aquí tu secreto de cliente de SendPulse
]);

function getAccessToken(&$system)
{
    $response = json_decode($system->guzzle->post(SENDPULSE_GATEWAY["siteUrl"] . "/oauth/access_token", [
        "form_params" => [
            "client_id" => SENDPULSE_GATEWAY["clientId"],
            "client_secret" => SENDPULSE_GATEWAY["clientSecret"],
            "grant_type" => "client_credentials"
        ],
        "http_errors" => false
    ])->getBody()->getContents(), true);

    return isset($response["access_token"]) ? $response["access_token"] : null;
}

function gatewaySend($phone, $message, &$system)
{
    $accessToken = getAccessToken($system);

    if (!$accessToken) {
        return false;
    }

    $params = [
        "sender" => "YourSender", // Reemplaza "YourSender" con el remitente deseado
        "phones" => [$phone],
        "body" => $message
    ];

    $send = json_decode($system->guzzle->post(SENDPULSE_GATEWAY["siteUrl"] . "/sms/send", [
        "headers" => [
            "Authorization" => "Bearer " . $accessToken,
            "Content-Type" => "application/json"
        ],
        "body" => json_encode($params),
        "allow_redirects" => true,
        "http_errors" => false
    ])->getBody()->getContents(), true);

    try {
        return isset($send["result"]) && $send["result"] ? true : false;
    } catch (Exception $e) {
        return false;
    }
}
