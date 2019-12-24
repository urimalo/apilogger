<?php

namespace AWT;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

abstract class AbstractLogger{

    protected $logs = [];

    protected $models = [];

    public function __construct()
    {
        $this->boot();
    }
    /**
     * starting method just for cleaning code
     *
     * @return void
     */
    public function boot(){
    }
    /**
     * logs into associative array
     *
     * @param  $request
     * @param  $response
     * @return array
     */
    public function logData($request,$response){
        $currentRouteAction = Route::currentRouteAction();

        /*
         * Some routes will not contain the `@` symbole (e.g. closures, or routes using a single action controller).
         */
        if ($currentRouteAction) {
            if (strpos($currentRouteAction, '@') !== false) {
                list($controller, $action) = explode('@', $currentRouteAction);
            } else {
                // If we get a string, just use that.
                if (is_string($currentRouteAction)) {
                    list ($controller, $action) = ["", $currentRouteAction];
                } else {
                    // Otherwise force it to be some type of string using `json_encode`.
                    list ($controller, $action) = ["", (string)json_encode($currentRouteAction)];
                }
            }
        }

        $endTime = microtime(true);

        $implode_models = $this->models;

        array_walk($implode_models, function(&$value, $key) {
            $value = "{$key} ({$value})";
        });

        $models = implode(', ',$implode_models);
        $this->logs['created_at'] = Carbon::now();

        $data['userAgent'] = $request->header('User-Agent');
        $data['traceId'] = $request->header('X-Zzt-Trace-Id');
        $data['referer'] = $request->header('Referer');
        $data['method'] = $request->method();
        $data['url'] = $request->path();
        $data['payload'] = $request->all();
        $data['response'] = $response->status();
        $data['duration'] = number_format($endTime - LARAVEL_START, 3);
        $data['controller'] = $controller;
        $data['action'] = $action;
        $data['ip'] = $request->ip();
        $this->logs['data'] = json_encode($data);

        return $this->logs;
    }
}
