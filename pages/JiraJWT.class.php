<?php 

  require_once( 'jwt/JWT.php' );
  require_once( 'jwt/SignatureInvalidException.php' );
  require_once( 'jwt/ExpiredException.php' );
  require_once( 'jwt/BeforeValidException.php' );

/**
  * A simple wrapper to validate JIRA JWTs tokens
  */
class JiraJWT extends JWT{
  
  public static function isValid($jwt, $token){
    try{ 
       
      $tks = explode('.', $jwt);
      $header = JWT::jsonDecode(JWT::urlsafeB64Decode($tks[0]));
      //TODO: HEADER CHECKS
      
      $jwt = JWT::decode($jwt, $token->sharedSecret, array('HS256', 'HS384', 'HS512', 'RS256'));
      
      if( $jwt->iss != $token->clientKey){
         //Wrong issuer
         return false;
      }
      
      $hashQuery = JiraJWT::generateStringQueryHash();
      if( $jwt->qsh != $hashQuery ){
        //Wrong hash
        return false;
      }

      return true;
    }catch (Exception $ex){
      return false;
    }finally {
      //Do nothing so far
    }
  }
  
  public static function getJWT(){
    $headers = apache_request_headers();
    $auth = $headers['Authorization'];
    $tks = explode(' ', $auth);
    if( strnatcasecmp ($tks, 'JWT') != 0)
      throw new Exception('Header has not JWT authentication defined');

    return $tks[1];
  }
    
    
  /**
   * Extracted from Symfony\Component\HttpFoundation\Request
   * @param  string $qs
   * @return string
   */
  protected static function normalizeQueryString($qs) {
    if ('' == $qs) {
      return '';
    }
    $parts = array();
    $order = array();
    foreach (explode('&', $qs) as $param) {
      if ('' === $param || '=' === $param[0]) {
        // Ignore useless delimiters, e.g. "x=y&".
        // Also ignore pairs with empty key, even if there was a value, e.g. "=value", as such nameless values cannot be retrieved anyway.
        // PHP also does not include them when building _GET.
        continue;
      }
      $keyValuePair = explode('=', $param, 2);
      // GET parameters, that are submitted from a HTML form, encode spaces as "+" by default (as defined in enctype application/x-www-form-urlencoded).
      // PHP also converts "+" to spaces when filling the global _GET or when using the function parse_str. This is why we use urldecode and then normalize to
      // RFC 3986 with rawurlencode.
      $parts[] = isset($keyValuePair[1]) ? rawurlencode(urldecode($keyValuePair[0])) . '=' . rawurlencode(urldecode($keyValuePair[1])) : rawurlencode(urldecode($keyValuePair[0]));
      $order[] = urldecode($keyValuePair[0]);
    }
    array_multisort($order, SORT_ASC, $parts);
    return implode('&', $parts);
  }
    
  public static function generateStringQueryHash(){
    $method = $_SERVER['REQUEST_METHOD'];
    $URI = $_SERVER['REQUEST_URI'];
    $QUERY = $_SERVER['QUERY_STRING'];

    //Remove BASE URL
    $URI = str_replace("/mantisbt-1.2.19","",$URI);
    //REMOVE QUERY FROM URI 
    $URI =str_replace("?".$QUERY,"",$URI);


    $query =  $method.'&'.$URI.'&'.JiraJWT::normalizeQueryString($QUERY);
   
 
    echo $query.' ('.hash('sha256',  $query).')';
    return hash('sha256',  $query);
  }
}