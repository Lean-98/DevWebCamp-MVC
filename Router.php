<?php

namespace MVC;

class Router
{
    public array $getRoutes = [];
    public array $postRoutes = [];

    public function get($url, $fn)
    {
        $this->getRoutes[$url] = $fn;
    }

    public function post($url, $fn)
    {
        $this->postRoutes[$url] = $fn;
    }

    public function comprobarRutas()
    {

       $currentUrl = ($_SERVER['REQUEST_URI'] === '') ? '/' :  $_SERVER['REQUEST_URI'] ;
       $method = $_SERVER['REQUEST_METHOD'];

       //dividimos la URL actual cada vez que exista un '?' eso indica que se están pasando variables por la url
       $splitURL = explode('?', $currentUrl);
  
       if ($method === 'GET') {
           $fn = $this->getRoutes[$splitURL[0]] ?? null; //$splitURL[0] contiene la URL sin variables 
       } else {
       $fn = $this->postRoutes[$splitURL[0]] ?? null;
       }

       if ( $fn ) {
           // Call user fn va a llamar una función cuando no sabemos cual sera
           call_user_func($fn, $this); // This es para pasar argumentos
       } else {
            header('Location: /404');
       }
    }

    public function render($view, $datos = [])
    {
        foreach ($datos as $key => $value) {
            $$key = $value; 
        }

        ob_start(); 

        include_once __DIR__ . "/views/$view.php";

        $contenido = ob_get_clean(); // Limpia el Buffer

        // Utilizar el Layout de acuerdo a la URL
        $url_actual = $_SERVER['PATH_INFO'] ?? '/';
        
        if(str_contains($url_actual, '/admin')) {
            include_once __DIR__ . '/views/admin-layout.php';
        } else {
            include_once __DIR__ . '/views/layout.php';
        }
    }
}
