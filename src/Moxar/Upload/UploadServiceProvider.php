<?php namespace Moxar\Upload;

use Illuminate\Support\ServiceProvider;

class UploadServiceProvider extends ServiceProvider {

    protected $defer = true;

    public function boot() {
            $this->package('moxar/upload');
    }

    public function register()
    {
        $this->app['upload'] = $this->app->share(function($app) {
            return new Upload($app);
        });
        $this->app->booting(function() {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Upload', 'Moxar\Upload\Facades\Upload');
        });
    }

    public function provides() {
            return array('upload');
    }
}