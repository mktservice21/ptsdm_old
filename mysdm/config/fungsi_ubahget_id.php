<?PHP

function encryptForId( $q ) {
    return( $q ); exit;

    $cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
    $qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
    if (!empty($qEncoded)) $qEncoded = str_replace("+", "_a/_", $qEncoded);
    return( $qEncoded );
}

function decryptForId( $q ) {
    return( $q ); exit;

    if (!empty($q)) $q = str_replace("_a/_", "+", $q);
    $cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
    $qDecoded      = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
    return( $qDecoded );
}

function encodeString($str){
    for($i=0; $i<5;$i++) {
        $str=strrev(base64_encode($str)); //apply base64 first and then reverse the string
    }
    return $str;
  }
  
  
  function decodeString($str){
    for($i=0; $i<5;$i++) {
        $str=base64_decode(strrev($str)); //apply base64 first and then reverse the string}
    }
    return $str;
  }

  
?>