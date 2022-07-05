<?php

namespace App\Common;

class AjaxResponse
{
    public $status;
    public $message;
    public $data;
    public $redirectURL;
    public $reload;

    public function __construct()
    {
        $this->status = true;
        $this->message = '';
        $this->errorCode = 0;
        $this->apiMessage = '';
        $this->data = json_encode([]);
        $this->redirectURL = '';
        $this->reload = false;
        $this->errors = '';
    }
}
