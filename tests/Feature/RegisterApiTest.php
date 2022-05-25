<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;

class RegisterApiTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    /**
     * @test
     */
    public function should_新しいユーザーを作成して返却する()
    {

        $this->withoutExceptionHandling();
        $data = [
            'name' => 'testMan',
            'email' => 'dummy@email.com',
            'password' => 'test1234',
            'password_confirmation' => 'test1234',
            'profile_image' => 'aaaDefault.png'
        ];


        $response = $this->json('POST', route('register'), $data);
        //dd($response);
        $user = User::first();

        $this->assertEquals($data['name'], $user->name);

        dd($user,$data);
        $response
            ->assertStatus(201)
            ->assertJson(['name' => $user->name]);
    }
}
