<?php
namespace app\controller;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Index
{
    public function indexAction(Request $request, Application $app) {
        $greeting = 'Hello stranger';
        if (null !== ($app['user'])) {
            $greeting = 'Hello ' . $app['user']->getDisplayName();
        }
        return $greeting;
    }
}