<?php

namespace Nd;

class response {
        public $status = false;
        public $message = "";
        public $response = "";
        public $error_no = 0;

        public function toJson(){
                return json_encode($this);
        }

        protected static function generateResponse($message, $response = "", $errno = 0) {
            $res = new response();
            $res->status = true;
            $res->message = $message;
            $res->response = $response;
            $res->error_no = $errno;
            return $res;
        }

        public static function pass($message, $response = "", $errno = 0) {
            $res = response::generateResponse($message, $response, $errno);
            $res->status = true;
            return $res;
        }

        public static function fail($message, $response = "", $errno = 0) {
            $res = response::generateResponse($message, $response, $errno);
            $res->status = false;
            return $res;
        }
};

?>