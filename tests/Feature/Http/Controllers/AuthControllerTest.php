<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\{Auth, Hash};
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    private readonly string $baseRoute;

    protected function setUp(): void
    {
        parent::setUp();

        $routeParts = [parent::baseRoute(), 'auth'];
        $this->baseRoute = Str::of(implode('/', $routeParts))->start('/');
    }

    public function testRegister(): void
    {
        $user = User::factory()->make();

        $response = $this->postJson("$this->baseRoute/register", [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
        ]);

        $response->assertCreated()
            ->assertJson(['message' => 'User registered successfully']);

        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function testLogin(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);

        $response = $this->postJson("$this->baseRoute/login", [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
    }

    public function testVerifyToken(): void
    {
        $user = User::factory()->create();
        $token = Auth::login($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("$this->baseRoute/verify-token");

        $response->assertOk()->assertJson(['user' => $user->toArray()]);
    }

    public function testLogout(): void
    {
        $user = User::factory()->create();
        $token = Auth::login($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("$this->baseRoute/logout");

        $response->assertOk()->assertJson(['message' => 'Successfully logged out']);
    }
}
