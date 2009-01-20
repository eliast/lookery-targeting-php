<?php
// Copyright 2004-2009 Lookery. All Rights Reserved.

class LookeryTargeting {

  const API_VERSION = '2';
  public $api_key;
  public $secret;

  /*
   * Create a Lookery Targeting client like this:
   *
   * $lt = new LookeryTargeting(API_KEY, SECRET);
   *
   * @param api_key                  your Lookery Targeting API key
   * @param secret                   your Lookery Targeting API secret
   */
  public function __construct($api_key, $secret) {
    $this->api_key                 = $api_key;
    $this->secret                  = $secret;
  }
  
  public function redirect($url, $extra_params=array()) {

    $timestamp = time();
    $signature_enc = self::generate_sig($timestamp, $this->secret);
    $request_url =  "http://services.lookery.com/targeting?"
                      . "v=".                 LookeryTargeting::API_VERSION
                      . "&api_key=".          $this->api_key
                      . "&r_url=".            urlencode($url)
                      . "&timestamp=" .       $timestamp
                      . "&signature=" .       $signature_enc;
                      
    $extra_enc = "";
    foreach($extra_params as $key => $value) {
      $extra_enc = $extra_enc . "&" . urlencode($key) . "=" . urlencode($value);
    }
    
    return $request_url . $extra_enc;
  }
  
  /*
   * Validates that a timestamp was used to create a signature.
   *
   * @param $timestamp     the timestamp used to generate the signature
   * @param $expected_sig  the expected result to check against
   */
  public function verify_signature($timestamp, $expected_sig) {
    return self::generate_sig($timestamp, $this->secret) == $expected_sig;
  }

  /*
   * Generate a signature using the application secret key.
   *
   * The only two entities that know your secret key are you and Lookery,
   * according to the Terms of Service. Since nobody else can generate
   * the signature, you can rely on it to verify that the information
   * was sent by you to the Lookery Targeting Service.
   *
   * @param $timestamp      A timestamp used as data to signt the request.
   * @param $secret         Your API secret key.
   *
   * @return a hash to be checked by Lookery against your request.
   */
  public static function generate_sig($timestamp, $secret) {
    $data = "LookeryTargeting" . LookeryTargeting::API_VERSION . $timestamp;
    return urlencode(self::calculate_RFC2104HMAC($data, $secret));
  }

  // Calculate signature using HMAC: http://www.faqs.org/rfcs/rfc2104.html
  public static function calculate_RFC2104HMAC ($data, $key) {
      return base64_encode (
          pack("H*", sha1((str_pad($key, 64, chr(0x00))
          ^(str_repeat(chr(0x5c), 64))) .
          pack("H*", sha1((str_pad($key, 64, chr(0x00))
          ^(str_repeat(chr(0x36), 64))) . $data))))
       );
  }
  
}

?>
