<?php

/**
 * @author kylekatarnls
 */

class Jade {

    protected $CI;
    protected $jade;
    protected $view_path;

    public function __construct(array $options = NULL) {

        if(is_null($options)) {
            $options = defined('static::SETTINGS') ? ((array) static::SETTINGS) : array();
        }
        if(isset($options['view_path'])) {
            $this->view_path = $options['view_path'];
            unset($options['view_path']);
        } else {
            $this->view_path = APPPATH . 'views';
        }
        if(isset($options['cache'])) {
            if($options['cache'] === TRUE) {
                $options['cache'] = APPPATH . 'cache/jade';
            }
            if(! file_exists($options['cache']) && ! mkdir($options['cache'], 0777, TRUE)) {
                throw new Exception("Cache folder does not exists and cannot be created.", 1);
            }
        }
        $this->CI =& get_instance();
        if(! class_exists('Jade\\Jade')) {
            spl_autoload_register(function ($className) {
                if(strpos($className, 'Jade\\') === 0) {
                    $path = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
                    if(file_exists($path)) {
                        include_once $path;
                    }
                }
            });
        }
        $this->jade = new Jade\Jade($options);
    }

    public function view($view, array $data = array(), $return = false) {

        if(is_array($view) || $view === TRUE) {
            $return = !! $data;
            $data = $view;
            $view = NULL;
        }
        if($data === TRUE) {
            $data = array();
            $return = TRUE;
        }
        if(is_null($view)) {
            $view = $this->router->class . DIRECTORY_SEPARATOR . $this->router->method;
        }
        if(! $this->jade) {
            $this->settings();
        }
        $view = $this->jade_view_path . DIRECTORY_SEPARATOR . $view . '.jade';
        if(! file_exists($view)) {
            $isIndex = (strtr('\\', '/', substr($view, -11)) === '/index.jade');
            $view = $isIndex ? substr($view, 0, -11) . '.jade' : substr($view, 0, -5) . DIRECTORY_SEPARATOR . 'index.jade';
        }
        $data = array_merge($this->CI->load->get_vars(), $data);
        if($return) {
            return $this->jade->render($view, $data);
        } else {
            echo $this->jade->render($view, $data);
        }
    }
}
