<?php
/**
 * Created by IntelliJ IDEA.
 * User: lalittanwar
 * Date: 26/09/15
 * Time: 8:58 PM
 */

namespace app\model;

include_once(RUDRA . "/smarty/Smarty.class.php");

class Email
{

    public $from_email = null;
    public $reply_to_email = null;
    public $to_email = null;
    public $title = null;
    public $message = null;
    public $template = null;
    public $model = null;

    /**
     * Email constructor.
     * @param $title
     */
    public function __construct()
    {
        $this->model = new \Smarty ();
    }

    public function send($message = null)
    {
        if (is_null($message) && !is_null($this->template)) {
            call_user_func(rx_function("rx_set_smarty_paths"), ($this->model));
            $this->message = $this->model->fetch($this->template . ".tpl");
        }
        return $this->sendEmail();
    }

    public function sendEmail()
    {
        if (is_null($this->from_email) || is_null($this->message) || is_null($this->title)) {
            return false;
        }
        if (is_null($this->reply_to_email)) {
            $this->reply_to_email = $this->from_email;
        }
        $headers = 'From: ' . $this->from_email . '' . "\r\n" .
            'Reply-To: ' . $this->reply_to_email . '' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        mail($this->to_email, $this->title, $this->message, $headers);
        return true;
    }

} 