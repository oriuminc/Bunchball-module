<?php

class NitroAPI {

  private $baseURL;
  private $secretKey;
  private $apiKey;
  private $userName;
  private $sessionKey;

  // Constants
  private $CRITERIA_MAX = "MAX";
  private $CRITERIA_CREDITS = "credits";
  private $POINT_CATEGORY_ALL = "all";
  private $TAGS_OPERATOR_OR = "OR";

  // singleton instance
  private static $instance;
  
  /**
   * Constructor
   */
  public function __construct() {
    switch (variable_get('bunchball_environment')) {
      case 'production':
        $this->baseURL = variable_get('bunchball_production_url');

        break;

      case 'sandbox':
        $this->baseURL = variable_get('bunchball_sandbox_url');
        break;

      default:
        break;
    }
    $this->apiKey = variable_get('bunchball_apikey');
    $this->secretKey = variable_get('bunchball_apisecret');
  }
  
  /**
   * Implement singleton pattern.
   * 
   * @return singleton instance of this class
   */
  public static function getInstance() {
    if (!isset(self::$instance)) {
      echo 'Creating new instance.';
      $className = __CLASS__;
      self::$instance = new $className;
    }
    return self::$instance;
  }

  /**
   * Generate a signature from the api key, secret and username.
   * 
   * @return the signature
   */
  private function getSignature() {
    $unencryptedSignature = $this->apiKey . $this->secretKey . time() . $this->userName;

    // get the length
    $length = strlen($unencryptedSignature);

    //append the length to the signature
    $unencryptedSignature = $unencryptedSignature . $length;

    //MD5 on signature
    print("Creating a signature... \n");
    $signature = md5($unencryptedSignature);
    return $signature;
  }

  /**
   *  Parse Nitro XML response as array of attributes and values.
   *
   * @param $url
   *   XML to parse
   * 
   * @return
   *   Array of values 
   */
  private function my_xml2array($url) {
    $xml_values = array();
    $result = drupal_http_request($url);
    $contents = file_get_contents($url);
    $parser = xml_parser_create('');
    if (!$parser)
      return false;

    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, trim($contents), $xml_values);
    xml_parser_free($parser);
    if (!$xml_values)
      return array();

    $xml_array = array();
    $last_tag_ar = & $xml_array;
    $parents = array();
    $last_counter_in_tag = array(1 => 0);
    foreach ($xml_values as $data) {
      switch ($data['type']) {
        case 'open':
          $last_counter_in_tag[$data['level'] + 1] = 0;
          $new_tag = array('name' => $data['tag']);
          if (isset($data['attributes']))
            $new_tag['attributes'] = $data['attributes'];
          if (isset($data['value']) && trim($data['value']))
            $new_tag['value'] = trim($data['value']);
          $last_tag_ar[$last_counter_in_tag[$data['level']]] = $new_tag;
          $parents[$data['level']] = & $last_tag_ar;
          $last_tag_ar = & $last_tag_ar[$last_counter_in_tag[$data['level']]++];
          break;
        case 'complete':
          $new_tag = array('name' => $data['tag']);
          if (isset($data['attributes']))
            $new_tag['attributes'] = $data['attributes'];
          if (isset($data['value']) && trim($data['value']))
            $new_tag['value'] = trim($data['value']);

          $last_count = count($last_tag_ar) - 1;
          $last_tag_ar[$last_counter_in_tag[$data['level']]++] = $new_tag;
          break;
        case 'close':
          $last_tag_ar = & $parents[$data['level']];
          break;
        default:
          break;
      };
    }
    return $xml_array;
  }

  /**
   *  Access the attribute values like XPATH
   *
   * @param $xml_tree
   * @param $tag_path
   * @return
   *    values 
   */
  private function get_value_by_path($xml_tree, $tag_path) {
    $tmp_arr = & $xml_tree;
    $tag_path_array = explode('/', $tag_path);
    foreach ($tag_path_array as $tag_name) {
      $res = false;
      foreach ($tmp_arr as $key => $node) {
        if (is_int($key) && $node['name'] == $tag_name) {
          $tmp_arr = $node;
          $res = true;
          break;
        }
      }
      if (!$res)
        return false;
    }
    return $tmp_arr;
  }

  /**
   * Log in to set session.
   * 
   * @param $userName
   *    the user name to record info
   */
  public function login($userName) {
    $this->userName = $userName;
    // construct a signature
    $signature = $this->getSignature();

    // Construct a URL for REST API call user_login to extract Session Key
    $request = $this->baseURL .
            "method=user.login" .
            "&apiKey={$this->apiKey}" .
            "&userId={$this->userName}" .
            "&ts=" . time() .
            "&sig=$signature";

    // Converting XML response attribute and values to array attributes and values
    $arr = $this->my_xml2array($request);

    // Accessing the sessionKey through XPATH
    $sessionKeyArray = $this->get_value_by_path($arr, 'Nitro/Login/sessionKey');
    $this->sessionKey = $sessionKeyArray['value'];
  }

  /**
   * Ensure the API session has been established.
   * 
   * @throws NitroAPI_NoSessionException if session is empty
   */
  private function check_session() {
    if (empty($this->sessionKey)) {
      throw new NitroAPI_NoSessionException(t('Nitro API session not found.'));
    }
  }
  
  
  /**
   * Log an action for the established session.
   * 
   * @param $actionTag
   *    The action tag to log
   * 
   * @param $value
   *    Value associated with the action tag
   * 
   * @throws NitroAPI_NoSessionException
   */
  public function logAction($actionTag, $value) {
    $this->check_session();
    // Construct a URL for user logAction
    $request = "{$this->baseURL}method=user.logAction" .
            "&sessionKey=$sessionKey" .
            "&userId={$this->userName}" .
            "&tags=$actionTag" .
            "&value=$value";

    //Converting XML response attribute and values to array attributes and values
    $arr = $this->my_xml2array($request);

    $responseArray = $this->get_value_by_path($arr, 'Nitro');
    if (! strcmp($responseArray['attributes']['res'], "ok") == 0) {
      throw new NitroAPI_LogActionException(t('Nitro API log action failed'));
    }
  }

  /**
   * Return the user point balance for current session.
   * 
   * @return
   *    the user point balance
   */
  public function getUserPointsBalance() {
    $this->check_session();
    // Construct a URL to get point balance from user
    $request = $this->baseURL .
            "method=user.getPointsBalance" .
            "&sessionKey=" . $this->sessionKey .
            "&start=0" . "&pointCategory=" .
            $this->POINT_CATEGORY_ALL . "&criteria=" .
            $this->CRITERIA_CREDITS . "&userId=Suraj";

    //Converting XML response attribute and values to array attributes and values
    $arr = $this->my_xml2array($request);

    //Accessing the Balance attributes through XPATH and extracting points information
    return $this->get_value_by_path($arr, 'Nitro/Balance');
  }

  /**
   * Retrieve site action leaders.
   * 
   * @param $actionTag
   *    action tag to retrieve
   * @return
   *    array containing leaders
   */
  public function getSiteActionLeaders($actionTag) {
    $this->check_session();
    // Construct a URL to get action leaders
    $request = $this->baseURL . "method=site.getActionLeaders" . "&sessionKey=" . $this->sessionKey . "&tags=" . $actionTag . "&tagsOperator=" . $this->TAGS_OPERATOR_OR . "&criteria=" . $this->CRITERIA_MAX . "&returnCount=" . $this->value;

    //Converting XML response attribute and values to array attributes and values
    $arr = $this->my_xml2array($request);

    //Accessing the Actions attributes through XPATH and extracting action leaders information
    $actionsArray = $this->get_value_by_path($arr, 'Nitro/actions/Action');
    return $actionsArray['attributes'];
  }

}

/**
 * Exception to be thrown when action is attempted but session does not exist.
 */
class NitroAPI_NoSessionException extends Exception {}

/**
 * Exception to be thrown when log action is unsuccessful.
 */
class NitroAPI_LogActionException extends Exception {}

/**
 * Exception to be thrown on HTTP error
 */
class NitroAPI_HttpException extends Exception {}