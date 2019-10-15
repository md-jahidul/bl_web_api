<?php


namespace App\Eblskypay\nvp;

class Parser extends Connection {
    function __construct($merchantObj) {
        // call parent ctor to init members
        parent::__construct($merchantObj);
    }

    function __destruct() {
        // call parent dtor to free resources
        parent::__destruct();
    }

    // [Snippet] howToConfigureURL - start
    // Modify gateway URL to set the version
    // Assign it to the gatewayUrl member in the merchantObj object
    public function FormRequestUrl($merchantObj) {
        $gatewayUrl = $merchantObj->GetGatewayUrl();
        $gatewayUrl .= "/version/" . $merchantObj->GetVersion();

        $merchantObj->SetGatewayUrl($gatewayUrl);
        return $gatewayUrl;
    }
    // [Snippet] howToConfigureURL - end

    // [Snippet] howToConvertFormData - start
    // Form NVP formatted request and append merchantId, apiPassword & apiUsername
    public function ParseRequest($merchantObj, $formData) {
        $request = "";

        if (count($formData) == 0)
            return "";

        foreach ($formData as $fieldName => $fieldValue) {
            if (strlen($fieldValue) > 0 && $fieldName != "merchant" && $fieldName != "apiPassword" && $fieldName != "apiUsername") {
                // replace underscores in the fieldnames with decimals
                for ($i = 0; $i < strlen($fieldName); $i++) {
                    if ($fieldName[$i] == '_')
                        $fieldName[$i] = '.';
                }
                $request .= $fieldName . "=" . urlencode($fieldValue) . "&";
            }
        }

        // [Snippet] howToSetCredentials - start
        // For NVP, authentication details are passed in the body as Name-Value-Pairs, just like any other data field
        $request .= "merchant=" . urlencode($merchantObj->GetMerchantId()) . "&";
        $request .= "apiPassword=" . urlencode($merchantObj->GetPassword()) . "&";
        $request .= "apiUsername=" . urlencode($merchantObj->GetApiUsername());
        // [Snippet] howToSetCredentials - end

        return $request;
    }
    // [Snippet] howToConvertFormData - end
}
