<?php
class ControllerEpos2Online extends Controller {
    private $error = array();
    public function index() {
       
    }
    public function site(){ 
        $html = "<html>"
                . "<script>window.location.replace(\"".HTTP_SERVER ."\");</script>"
                . "</html>";
        $this->response->setOutput($html);
    }    
    public function admin(){
        $get = json_decode(base64_decode($this->request->get['a']));
        $this->session->data['user_id'] = $get->i;
        $this->load->library('user');
        $this->user = new User($this->registry);
        $this->session->data['token'] = md5(mt_rand());
        $html = "<html>"
                . "<script>window.location.replace(\"".HTTP_ADMIN ."index.php?route=common/home&token=". $this->session->data['token']."\");</script>"
                . "</html>";
        echo $html;die();
        $this->response->setOutput($html);
    }
     public function freetobook(){
        
        $html = "<html>"
                . "<body><div>"
                . "<form name=\"loginForm\" id=\"loginForm\" action=\"https://www.freetobook.com/auth/login.php\" method=\"post\" >
                <input type=\"text\" name=\"username\" id=\"username\" maxlength=\"20\" size=\"22\" value=\"username\" >
                <input type=\"password\" name=\"password\" id=\"password\" size=\"22\" value=\"password\" >
                <input type=\"submit\" name=\"login\" value=\"login\" id=\"login\" >
        </form>"
                . "</div></body>"
                ."<script>document.getElementById(\"loginForm\").submit();</script>"
                . "</html>";
        $this->response->setOutput($html);
    }
    
}