<?php


Route::get('/', 'UserController@inicio')
    ->name('users.inicio');

Route::get('/usuarios', 'UserController@index')
    ->name('users');

//usuarios/nuevo != usuarios/[0-9]+ || usuarios/[\d+] (\d+ expresion regular para digitos, mas de un numero)
Route::get('/usuarios/{user}', 'UserController@show')
    ->where('user', '[0-9]+')
    ->name('users.show');

Route::get('/usuarios/nuevo', 'UserController@create')
    ->name('users.create');

Route::post('/usuarios/crear', 'UserController@store');

Route::get('/usuarios/{user}/editar', 'UserController@edit')->name('users.edit');

Route::put('/usuarios/{user}', 'UserController@update');

//Si no queremos que un parametro sea indispensable (parametro opcional) al construir la ruta le ponemos ? como se muestra abajo en {nickname?} 
//pero debe agregarse un valor por defecto por ejemplo $nickname = null
Route::get('/saludo/{name}/{nickname?}', 'WelcomeUserController');

Route::delete('/usuarios/{user}', 'UserController@destroy')->name('users.destroy');