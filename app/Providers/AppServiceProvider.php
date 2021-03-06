<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        // 或者直接使用 \DB::
        // 只能接受一个参数

        /*\DB::listen(function($sql) {
                dump($sql);
                // echo $sql->sql;
                // dump($sql->bindings);
        });*/

        // 如果要放入日志文件中
        /*DB::listen(
          function ($sql) {
            // $sql is an object with the properties:
            // sql: The query
            // bindings: the sql query variables
            // time: The execution time for the query
            // connectionName: The name of the connection

            // To save the executed queries to file:
            // Process the sql and the bindings:
            foreach ($sql->bindings as $i => $binding) {
              if ($binding instanceof \DateTime) {
                $sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
              } else {
                if (is_string($binding)) {
                  $sql->bindings[$i] = "'$binding'";
                }
              }
            }

            // Insert bindings into query
            $query = str_replace(array('%', '?'), array('%%', '%s'), $sql->sql);

            $query = vsprintf($query, $sql->bindings);

            // Save the query to file
            $logFile = fopen(
              storage_path('logs' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_query.log'),
              'a+'
            );
            fwrite($logFile, date('Y-m-d H:i:s') . ': ' . $query . PHP_EOL);
            fclose($logFile);
          }
        );*/

        //视图间共享数据
        // view()->share('no_include','0');

        //视图Composer
        view()->composer('layouts.header',function($view){
            // $view->with('user',array('name'=>'test','avatar'=>'/path/to/test.jpg'));
            $view->with('no_include',0);
            $view->with('title','诚车堂-订货管理小助手！');
        });

       /* view()->composer(['hello','home'],function($view){
            $view->with('user',array('name'=>'test','avatar'=>'/path/to/test.jpg'));
        });*/
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
