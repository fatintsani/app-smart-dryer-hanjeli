<?php

namespace Tests\Feature;

use App\Models\PasswordResetOtp;
use App\Models\User;
use App\Models\UserPasskey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@hanjeli.id',
            'password' => bcrypt('admin123'),
            'role' => 'ADMIN',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@hanjeli.id',
            'password' => 'admin123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'accessToken',
                'user' => ['id', 'name', 'email', 'role'],
            ]);
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'admin@hanjeli.id',
            'password' => bcrypt('admin123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@hanjeli.id',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
    }

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Petani Baru',
            'email' => 'petanibaru@hanjeli.id',
            'password' => 'secret123',
            'phone' => '08123456789',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'accessToken',
                'user' => ['id', 'name', 'email', 'role'],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'petanibaru@hanjeli.id',
            'role' => 'OPERATOR',
        ]);
    }

    public function test_forgot_password_sends_otp_email(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'petani@hanjeli.id',
            'name' => 'Petani Waluran',
        ]);

        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'petani@hanjeli.id',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('password_reset_otps', [
            'email' => 'petani@hanjeli.id',
        ]);
    }

    public function test_verify_otp_and_reset_password_flow(): void
    {
        $user = User::factory()->create([
            'email' => 'petani@hanjeli.id',
            'password' => bcrypt('oldpassword'),
        ]);

        $otp = PasswordResetOtp::create([
            'email' => 'petani@hanjeli.id',
            'otp_code' => '123456',
            'expires_at' => now()->addMinutes(15),
        ]);

        // Verify OTP
        $verifyResponse = $this->postJson('/api/auth/verify-otp', [
            'email' => 'petani@hanjeli.id',
            'otp' => '123456',
        ]);

        $verifyResponse->assertStatus(200)
            ->assertJsonStructure(['message', 'resetToken']);

        $resetToken = $verifyResponse->json('resetToken');

        // Reset Password
        $resetResponse = $this->postJson('/api/auth/reset-password', [
            'token' => $resetToken,
            'newPassword' => 'newpassword123',
        ]);

        $resetResponse->assertStatus(200);

        // Verify user can login with new password
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'petani@hanjeli.id',
            'password' => 'newpassword123',
        ]);

        $loginResponse->assertStatus(200);
    }

    public function test_passkey_options_generates_challenge(): void
    {
        $response = $this->postJson('/api/auth/passkey/options');

        $response->assertStatus(200)
            ->assertJsonStructure(['challenge', 'rp' => ['name', 'id']]);
    }

    public function test_passkey_register_and_login(): void
    {
        $user = User::factory()->create([
            'email' => 'petani@hanjeli.id',
            'name' => 'Petani Waluran',
        ]);

        // Register passkey
        $regResponse = $this->postJson('/api/auth/passkey/register', [
            'email' => 'petani@hanjeli.id',
            'credentialId' => 'test-credential-id-12345',
            'publicKey' => 'sample-public-key',
            'deviceName' => 'Windows Hello Test',
        ]);

        $regResponse->assertStatus(201)
            ->assertJsonStructure(['message', 'accessToken', 'user']);

        $this->assertDatabaseHas('user_passkeys', [
            'credential_id' => 'test-credential-id-12345',
            'user_id' => $user->id,
        ]);

        // Login with passkey
        $loginResponse = $this->postJson('/api/auth/passkey/verify', [
            'credentialId' => 'test-credential-id-12345',
            'email' => 'petani@hanjeli.id',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJsonStructure(['message', 'accessToken', 'user']);
    }
}
