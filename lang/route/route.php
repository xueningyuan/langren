<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

Route::post('login', 'index/login')
->allowCrossDomain();
Route::post('hall', 'index/hall')
->allowCrossDomain();
Route::post('create_room', 'index/create_room')
->allowCrossDomain();
Route::post('room', 'index/room')
->allowCrossDomain();
Route::post('setlimit', 'index/setlimit')
->allowCrossDomain();
Route::post('gameStart', 'index/gameStart')
->allowCrossDomain();
Route::post('game', 'index/game')
->allowCrossDomain();
Route::post('judge', 'index/judge')
->allowCrossDomain();
Route::post('seat', 'index/seat')
->allowCrossDomain();
Route::post('close', 'index/close')
->allowCrossDomain();
Route::get('over', 'index/over')
->allowCrossDomain();
Route::get('restart', 'index/restart')
->allowCrossDomain();

return [

];
