<?php
namespace App\Http\Controllers;

use Barryvdh\Elfinder\Connector;
use Barryvdh\Elfinder\Session\LaravelSession;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Auth;

class ElfinderController extends \Barryvdh\Elfinder\ElfinderController
{
    public function showConnector()
    {
        // user private folder
        $privatedir = $this->initfolder(); //janokary
        $roots = $this->app->config->get('elfinder.roots', []);
        if (empty($roots)) {
            $dirs = (array) $this->app['config']->get('elfinder.dir', []);
            foreach ($dirs as $dir) {
                $roots[] = [
                    'driver' => 'LocalFileSystem', // driver for accessing file system (REQUIRED)
                    'path' => public_path($dir), // path to files (REQUIRED)
                    'URL' => url($dir), // URL to files (REQUIRED)
                    'accessControl' => $this->app->config->get('elfinder.access') // filter callback (OPTIONAL)
                ];
            }

            $disks = (array) $this->app['config']->get('elfinder.disks', []);
            foreach ($disks as $key => $root) {
                if (is_string($root)) {
                    $key = $root;
                    $root = [];
                }
                $disk = app('filesystem')->disk($key);
                if ($disk instanceof FilesystemAdapter) {
                    $defaults = [
                        'driver' => 'Flysystem',
                        'filesystem' => $disk->getDriver(),
                        'alias' => $key,
                        'path' => $privatedir, // janokary
                    ];

                    $root['URL'] = url('assets/uploads/'.$privatedir);
                    $roots[] = array_merge($defaults, $root);
                }
            }
        }

        $opts = $this->app->config->get('elfinder.options', array());
        $opts = array_merge(['roots' => $roots], $opts);

        // run elFinder
        $connector = new Connector(new \elFinder($opts));
        $connector->run();
        return $connector->getResponse();
    }

    private function initfolder(){
        if (Auth::check()) {
            $privatedir = md5('te125sg28UH$&$&#@$&^$@^@#^gds23gs'.Auth::user()->id.'jsgzzzzasghHdfhj5454@%326t^$^Gsdgsjsdgjshg88');
        } else {
            $privatedir = '';
        }
        return $privatedir;
    }
}