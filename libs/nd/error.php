<?php

namespace Nd;

class error extends \Exception {
    public function generateResponse() {
        return response::fail($this->message, "Ha ocurrido un error", $this->code);
    }
};

?>