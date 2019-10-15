<?php
namespace App\Eblskypay\nvp;
class Connection {
  protected $curlObj;

  function __construct($merchantObj) {
    // initialise cURL object/options
    $this->curlObj = curl_init();

    // configure cURL proxy options by calling this function
    $this->ConfigureCurlProxy($merchantObj);

    // configure cURL certificate verification settings by calling this function
    $this->ConfigureCurlCerts($merchantObj);
  }

  function __destruct() {
    // free cURL resources/session
    curl_close($this->curlObj);
  }

  // Send transaction to payment server
  public function SendTransaction($merchantObj, $request) {
    // [Snippet] howToPost - start
    curl_setopt($this->curlObj, CURLOPT_POSTFIELDS, $request);
    // [Snippet] howToPost - end

    // [Snippet] howToSetURL - start
    curl_setopt($this->curlObj, CURLOPT_URL, $merchantObj->GetGatewayUrl());
		// [Snippet] howToSetURL - end

    // [Snippet] howToSetHeaders - start
    // set the content length HTTP header
    curl_setopt($this->curlObj, CURLOPT_HTTPHEADER, array("Content-Length: " . strlen($request)));

    // set the charset to UTF-8 (requirement of payment server)
    curl_setopt($this->curlObj, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded;charset=UTF-8"));
    // [Snippet] howToSetHeaders - end

    curl_setopt($this->curlObj, CURLOPT_FOLLOWLOCATION, false);

    // tells cURL to return the result if successful, of FALSE if the operation failed
    curl_setopt($this->curlObj, CURLOPT_RETURNTRANSFER, TRUE);



    // this is used for debugging only. This would not be used in your integration, as DEBUG should be set to FALSE
    if ($merchantObj->GetDebug()) {
      curl_setopt($this->curlObj, CURLOPT_HEADER, TRUE);
      curl_setopt($this->curlObj, CURLINFO_HEADER_OUT, TRUE);
    }

    // [Snippet] executeSendTransaction - start
    // send the transaction
    $response = curl_exec($this->curlObj);
    // [Snippet] executeSendTransaction - end

    // this is used for debugging only. This would not be used in your integration, as DEBUG should be set to FALSE
    if ($merchantObj->GetDebug()) {
      $requestHeaders = curl_getinfo($this->curlObj);
      $response = $requestHeaders["request_header"] . $response;
    }

    // assigns the cURL error to response if something went wrong so the caller can echo the error
    if (curl_error($this->curlObj))
      $response = "cURL Error: " . curl_errno($this->curlObj) . " - " . curl_error($this->curlObj);

    // respond with the transaction result, or a cURL error message if it failed
    return $response;
  }

  // [Snippet] howToConfigureProxy - start
  // Check if proxy config is defined, if so configure cURL object to tunnel through
  protected function ConfigureCurlProxy($merchantObj) {
    // If proxy server is defined, set cURL option
    if ($merchantObj->GetProxyServer() != "") {
      curl_setopt($this->curlObj, CURLOPT_PROXY, $merchantObj->GetProxyServer());
      curl_setopt($this->curlObj, $merchantObj->GetProxyCurlOption(), $merchantObj->GetProxyCurlValue());
    }

    // If proxy authentication is defined, set cURL option
    if ($merchantObj->GetProxyAuth() != "")
      curl_setopt($this->curlObj, CURLOPT_PROXYUSERPWD, $merchantObj->GetProxyAuth());
  }
  // [Snippet] howToConfigureProxy - end

  // [Snippet] howToConfigureSslCert - start
  // configure the certificate verification related settings on the cURL object
  protected function ConfigureCurlCerts($merchantObj) {
    // if user has given a path to a certificate bundle, set cURL object to check against them
    if ($merchantObj->GetCertificatePath() != "")
      curl_setopt($this->curlObj, CURLOPT_CAINFO, $merchantObj->GetCertificatePath());

    curl_setopt($this->curlObj, CURLOPT_SSL_VERIFYPEER, $merchantObj->GetCertificateVerifyPeer());
    curl_setopt($this->curlObj, CURLOPT_SSL_VERIFYHOST, $merchantObj->GetCertificateVerifyHost());
  }
  // [Snippet] howToConfigureSslCert - end

}



