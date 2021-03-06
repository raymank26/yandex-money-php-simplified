<?php 
namespace YandexMoney;

require_once __DIR__ . "/base.php";

class API extends BaseAPI {

    function __construct($access_token) {
        $this->access_token = $access_token;
    }
    function sendAuthenticatedRequest($url, $options=array()) {
        $this->checkToken();
        return self::sendRequest($url, $options, $this->access_token);
    }
    function checkToken() {
        if($this->access_token == NULL) {
            throw new \Exception("obtain access_token first");
        }
    }
    function accountInfo() {
        return $this->sendAuthenticatedRequest("/api/account-info");
    }
    function operationHistory($options=NULL) {
        return $this->sendAuthenticatedRequest("/api/operation-history", $options);
    }
    function operationDetails($operation_id) {
        return $this->sendAuthenticatedRequest("/api/operation-details",
            array("operation_id" => $operation_id)
        );
    }
    function requestPayment($options) {
        return $this->sendAuthenticatedRequest("/api/request-payment", $options);
    }
    function processPayment($options) {
        return $this->sendAuthenticatedRequest("/api/process-payment", $options);
    }
    function incomingTransferAccept($operation_id, $protection_code=NULL) {
        return $this->sendAuthenticatedRequest("/api/incoming-transfer-accept",
            array(
                "operation_id" => $operation_id,
                "protection_code" => $protection_code
            ));
    }
    function incomingTransferReject($operation_id) {
        return $this->sendAuthenticatedRequest("/api/incoming-transfer-reject",
            array(
                "operation_id" => $operation_id,
            ));
    }
    public static function buildObtainTokenUrl($client_id, $redirect_uri,
        $scope) {
        $params = sprintf(
            "client_id=%s&response_type=%s&redirect_uri=%s&scope=%s",
            $client_id, "code", $redirect_uri, implode(" ", $scope)
            );
        return sprintf("%s/oauth/authorize?%s", self::SP_MONEY_URL, $params);
    }
    public static function getAccessToken($client_id, $code, $redirect_uri,
            $client_secret=NULL) {
        $full_url = self::SP_MONEY_URL . "/oauth/token";
        $result = \Requests::post($full_url, array(), array(
            "code" => $code,
            "client_id" => $client_id,
            "grant_type" => "authorization_code",
            "redirect_uri" => $redirect_uri,
            "client_secret" => $client_secret
        ));
        return self::processResult($result);

    }
}