<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class UsersModuleTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    function it_loads_the_users_list_page()
    {

        factory(User::class)->create([
            'name' => 'Luis',
        ]);

        factory(User::class)->create([
            'name' => 'Dulce',
        ]);

        $this->get('/usuarios')
        ->assertStatus(200)
        ->assertSee('Listado de usuarios')
        ->assertSee('Luis')
        ->assertSee('Dulce');
    }

    /** @test */
    function it_shows_a_default_message_if_the_users_list_is_empty()
    {
        $this->get('/usuarios')
        ->assertStatus(200)
        ->assertSee('No hay usuarios registrados');
    }

    /** @test */
    function it_displays_the_users_details()
    {

        $user = factory(User::class)->create([
            'name' => 'Luis Perez'
        ]);

        $this->get('/usuarios/'.$user->id)
        ->assertStatus(200)
        ->assertSee('Luis Perez');
    }

    /** @test */
    function it_displays_404_error_if_the_user_in_not_found()
    {
        $this->get('/usuarios/999')
        ->assertStatus(404)
        ->assertSee('Página no encontrada');
    }

    /** @test */
    function it_loads_the_new_users_page()
    {
        $this->get('/usuarios/nuevo')
        ->assertStatus(200)
        ->assertSee('Crear usuario');
    }

    /** @test */
    function it_creates_a_new_user()
    {

        $this->withoutExceptionHandling();

        $this->post('/usuarios/crear', [
            'name' => 'Luis',
            'email' => 'luis@correo.com',
            'password' => '123456'
        ])->assertRedirect('usuarios');

        $this->assertCredentials([
            'name' => 'Luis',
            'email' => 'luis@correo.com',
            'password' => '123456',
        ]);

    }

    /** @test */
    function the_name_is_required()
    {

        $this->from('usuarios/nuevo')
        ->post('/usuarios/crear', [
            'name' => '',
            'email' => 'luis@correo.com',
            'password' => '123456'
        ])
        ->assertRedirect('usuarios/nuevo')
        ->assertSessionHasErrors(['name' => 'El campo nombre es obligatorio']);


        $this->assertEquals(0, User::count());
    }

    /** @test */
    function the_email_is_required()
    {

        $this->from('usuarios/nuevo')
        ->post('/usuarios/crear', [
            'name' => 'Luis',
            'email' => '',
            'password' => '123456'
        ])
        ->assertRedirect('usuarios/nuevo')
        ->assertSessionHasErrors(['email' => 'El campo email es obligatorio']);


        $this->assertEquals(0, User::count());
    }

    /** @test */
    function the_email_must_be_valid()
    {

        $this->from('usuarios/nuevo')
        ->post('/usuarios/crear', [
            'name' => 'Luis',
            'email' => 'correo-no-valido',
            'password' => '123456'
        ])
        ->assertRedirect('usuarios/nuevo')
        ->assertSessionHasErrors(['email' => 'Ingrese un correo valido']);


        $this->assertEquals(0, User::count());
    }

    /** @test */
    function the_email_must_be_unique()
    {

        factory(User::class)->create([
            'email' => 'luis@correo.com'
        ]);

        $this->from('usuarios/nuevo')
        ->post('/usuarios/crear', [
            'name' => 'Luis',
            'email' => 'luis@correo.com',
            'password' => '123456'
        ])
        ->assertRedirect('usuarios/nuevo')
        ->assertSessionHasErrors(['email' => 'El correo debe ser unico']);


        $this->assertEquals(1, User::count());
    }

    /** @test */
    function the_password_is_required()
    {

        $this->from('usuarios/nuevo')
        ->post('/usuarios/crear', [
            'name' => 'Luis',
            'email' => 'luis@correo.com',
            'password' => ''
        ])
        ->assertRedirect('usuarios/nuevo')
        ->assertSessionHasErrors(['password' => 'El campo contraseña es obligatorio']);


        $this->assertEquals(0, User::count());
    }

    /** @test */
    function it_loads_the_edit_user_page()
    {
        $user = factory(User::class)->create();

        $this->get("/usuarios/{$user->id}/editar")
        ->assertStatus(200)
        ->assertViewIs('users.edit')
        ->assertSee('Editar usuario')
        ->assertViewHas('user', function ($viewUser) use ($user) {
            return $viewUser->id === $user->id;
        });
    }

    /** @test */
    function it_updates_a_user()
    {

        $user = factory(User::class)->create();

        $this->withoutExceptionHandling();

        $this->put("/usuarios/{$user->id}", [
            'name' => 'Luis',
            'email' => 'luis@correo.com',
            'password' => '123456'
        ])->assertRedirect("/usuarios/{$user->id}");

        $this->assertCredentials([
            'name' => 'Luis',
            'email' => 'luis@correo.com',
            'password' => '123456',
        ]);

    }

    /** @test */
    function the_name_is_required_when_updating_the_user()
    {

        $user = factory(User::class)->create();

        $this->from("/usuarios/{$user->id}/editar")
        ->put("/usuarios/{$user->id}", [
            'name' => '',
            'email' => 'luis@correo.com',
            'password' => '123456'
        ])
        ->assertRedirect("/usuarios/{$user->id}/editar")
        ->assertSessionHasErrors(['name']);


        $this->assertDatabaseMissing('users', ['email' => 'luis@correo.com']);
    }

    /** @test */
    function the_email_must_be_valid_when_updating_the_user()
    {

        $user = factory(User::class)->create();

        $this->from("/usuarios/{$user->id}/editar")
        ->put("/usuarios/{$user->id}", [
            'name' => 'Luis Mendoza',
            'email' => 'correo-no-valido',
            'password' => '123456'
        ])
        ->assertRedirect("/usuarios/{$user->id}/editar")
        ->assertSessionHasErrors(['email']);


        $this->assertDatabaseMissing('users', ['name' => 'Luis Mendoza']);
    }

    
    /** @test */
    function the_email_must_be_unique_when_updating_the_user()
    {

        //$this->withoutExceptionHandling();

        factory(User::class)->create([
            'email' => 'existing-email@example.com',
        ]);

        $user = factory(User::class)->create([
            'email' => 'luis@correo.com'
        ]);

        $this->from("usuarios/{$user->id}/editar")
        ->put("usuarios/{$user->id}", [
            'name' => 'Luis',
            'email' => 'existing-email@example.com',
            'password' => '123456'
        ])
        ->assertRedirect("usuarios/{$user->id}/editar")
        ->assertSessionHasErrors(['email']);

        //
    }

    /** @test */
    function the_users_email_can_stay_the_same_when_updating_the_user()
    {

        $user = factory(User::class)->create([
            'email' => 'luis@correo.com'
        ]);

        $this->from("usuarios/{$user->id}/editar")
        ->put("usuarios/{$user->id}", [
            'name' => 'Luis Perez',
            'email' => 'luis@correo.com',
            'password' => '12345678'
        ])
        ->assertRedirect("usuarios/{$user->id}"); //(users.show)


        $this->assertDatabaseHas('users', [
            'name' => 'Luis Perez',
            'email' => 'luis@correo.com',
        ]);
    }

    /** @test */
    function the_password_is_optional_when_updating_the_user()
    {

        $oldPassword = 'CLAVE_ANTERIOR';

        $user = factory(User::class)->create([
            'password' => bcrypt($oldPassword)
        ]);

        $this->from("usuarios/{$user->id}/editar")
        ->put("usuarios/{$user->id}", [
            'name' => 'Luis',
            'email' => 'luis@correo.com',
            'password' => ''
        ])
        ->assertRedirect("usuarios/{$user->id}"); //(users.show)


        $this->assertCredentials([
            'name' => 'Luis',
            'email' => 'luis@correo.com',
            'password' => $oldPassword //VERY IMPORTANT!
        ]);
    }

    /** @test */
    function it_deletes_a_user()
    {
        $user = factory(User::class)->create();

        $this->delete("usuarios/$user->id")
            ->assertRedirect('usuarios');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id
        ]);

        //$this->assertSame(0, User::count());
    }

}
