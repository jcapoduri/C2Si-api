<?php

class Auth {

    protected $token;

    public function __construct($token = null){
      $this->token = $token;
    }

// Authentication Area

    /**
    * @param: token: access token a validar
    * @return: boolean: so el token es valido o no, de ser valido, renovar la fecha de expiración
    */
    public function authentificate($token = null){
      $_token = $this->token;
      if (!is_null($token)) $_token = $token;
      if (is_null($_token)) return false;
        //var_dump("SELECT usr_id FROM auth_tokens WHERE expiration > CURRENT_TIMESTAMP AND token LIKE '".$token . "'");
      $authtoken = R::findOne('authtoken', 'expiration > now() AND token LIKE ?', array($_token));
      if ($authtoken){
          $authtoken->expiration = date(DateTime::ISO8601, time() + (1 * 60 * 60));
          R::store($authtoken);
          return true;
      }else{
          return false;
      };
    }

    /**
    * @param: token: access token a validar
    * @return: id del usuario del token o -1
    */
    public function getUserId($token = null) {
      $_token = $this->token;
      if (!is_null($token)) $_token = $token;
      //$result = $this->handler->query("SELECT usr_id FROM auth_tokens WHERE expiration > NOW() AND token LIKE '". $_token . "'");
      $authtoken = R::findOne('authtoken', 'expiration > now() AND token LIKE ?', array($_token));
      if ($authtoken){
          return is_null($authtoken->user_id) ? 0 : $authtoken->user_id;
      }else{
          return 0;
      }
    }

    /**
    * @param: userid: id del usuario
    * @return: string, token unico de acceso con 1hora de expiración
    */
    public function generateToken($userid) {
      $token = "";
      if (!$userid) return $token;
      $insertedToekn = false;
      $tries = 5;
      do {
        $randtoken = md5(rand());
        //$insertedToken = $this->handler->query("INSERT INTO auth_tokens (token, expiration, usr_id) VALUES ('".$token."', NOW() + INTERVAL 1 HOUR, " . $userid . ")");
        $token = R::dispense('authtoken');
        $token->token = $randtoken;
        $token->expiration = date(DateTime::ISO8601, time() + (1 * 60 * 60));
        $token->user_id = $userid;
        $insertedToken = R::store($token);
        $tries--;
      } while (!$insertedToken && !$tries);

      return $randtoken;
    }

    public function obsoleteToken($token) {
      $_token = $this->token;
      if (!is_null($token)) $_token = $token;
      if (is_null($_token)) return false;
      $authtoken = R::findOne('authtoken', 'expiration > now() AND token LIKE ?', array($_token));
      if ($authtoken){
          R::trash($authtoken);
          return true;
      }else{
          return false;
      };
    }

// Authorization area

    /**
    *   @param: scopes: string or string array
    *   @return: boolean, if is authorized or not
    */

    public function authorize($scopes) {
        if (!is_array($scopes)) $scopes = array($scopes);

        foreach($scopes as $scope) {

        };

        return true;
    }

};


?>