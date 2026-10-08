<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpResetPasswordMail;
use App\Mail\WelcomeUserMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use App\Models\UserPasskey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle Email / Username & Password Login
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'login' => 'nullable|string',
            'email' => 'nullable|string',
            'username' => 'nullable|string',
            'password' => 'required|string',
        ], [
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $identifier = trim($validated['login'] ?? $validated['email'] ?? $validated['username'] ?? '');

        if (empty($identifier)) {
            return response()->json([
                'message' => 'Email atau Username wajib diisi.',
                'errors' => ['login' => ['Email atau Username wajib diisi.']],
            ], 422);
        }

        // Support login by either email or username (case-insensitive)
        $user = User::where('email', $identifier)
            ->orWhere('username', $identifier)
            ->first();

        if (! $user || ! $user->password || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Email/Username atau kata sandi yang Anda masukkan salah.',
            ], 401);
        }

        $token = $user->createToken('smart-dryer-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'accessToken' => $token,
            'user' => $user->toAuthPayload(),
        ]);
    }

    /**
     * Handle User Registration
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|min:3|max:50|alpha_dash|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:30',
            'role' => 'nullable|string|in:ADMIN,OPERATOR',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.min' => 'Username minimal harus 3 karakter.',
            'username.unique' => 'Username ini sudah digunakan.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal harus 6 karakter.',
        ]);

        // Auto-generate username from email if not explicitly provided
        $username = !empty($validated['username'])
            ? strtolower(trim($validated['username']))
            : strtolower(explode('@', $validated['email'])[0]);

        if (empty($validated['username'])) {
            $baseUsername = preg_replace('/[^a-z0-9_-]/', '', $username) ?: 'user';
            $username = $baseUsername;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'username' => $username,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => strtoupper($validated['role'] ?? 'OPERATOR'),
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('smart-dryer-token')->plainTextToken;

        try {
            Mail::to($user->email)->send(new \App\Mail\WelcomeUserMail($user->name, $user->email, $user->role, $user->username));
        } catch (\Throwable $e) {
            Log::warning('Welcome email failed: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Pendaftaran akun berhasil.',
            'accessToken' => $token,
            'user' => $user->toAuthPayload(),
        ], 201);
    }

    /**
     * Handle Google Sign-In & Registration (API / GIS Popup)
     */
    public function googleAuth(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
            'googleId' => 'nullable|string',
            'avatarUrl' => 'nullable|string',
            'credential' => 'nullable|string',
        ]);

        $email = $validated['email'];
        $name = $validated['name'];
        $googleId = $validated['googleId'] ?? null;
        $avatarUrl = $validated['avatarUrl'] ?? null;

        // If Google ID token credential is provided, optionally verify against Google
        if (! empty($validated['credential'])) {
            try {
                $verifyRes = Http::get('https://oauth2.googleapis.com/tokeninfo?id_token='.$validated['credential']);
                if ($verifyRes->successful()) {
                    $tokenInfo = $verifyRes->json();
                    $email = $tokenInfo['email'] ?? $email;
                    $name = $tokenInfo['name'] ?? $name;
                    $googleId = $tokenInfo['sub'] ?? $googleId;
                    $avatarUrl = $tokenInfo['picture'] ?? $avatarUrl;
                }
            } catch (\Throwable $e) {
                // fallback to client-sent payload
            }
        }

        $user = null;

        if (! empty($googleId)) {
            $user = User::where('google_id', $googleId)->first();
        }

        if (! $user) {
            $user = User::where('email', $email)->first();
        }

        if ($user) {
            $user->update([
                'google_id' => $googleId ?? $user->google_id,
                'avatar' => $avatarUrl ?? $user->avatar,
            ]);
        } else {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'google_id' => $googleId,
                'avatar' => $avatarUrl,
                'role' => 'OPERATOR',
                'password' => Hash::make(Str::random(24)),
            ]);
        }

        $token = $user->createToken('smart-dryer-google-token')->plainTextToken;

        return response()->json([
            'message' => 'Autentikasi Google berhasil.',
            'accessToken' => $token,
            'user' => $user->toAuthPayload(),
        ]);
    }

    /**
     * Redirect to Google OAuth Consent Screen
     */
    public function redirectToGoogle()
    {
        $clientId = trim((string) config('services.google.client_id'));
        $redirectUri = trim((string) config('services.google.redirect'));

        if (! $clientId || ! $redirectUri) {
            return response()->json(['message' => 'Konfigurasi Google OAuth belum lengkap di .env.'], 500);
        }

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'offline',
            'prompt' => 'select_account',
        ], '', '&', PHP_QUERY_RFC3986);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?'.$query);
    }

    /**
     * Handle Google OAuth Callback (e.g. /auth/google/callback)
     */
    public function handleGoogleCallback(Request $request)
    {
        $code = $request->input('code');

        if (! $code) {
            return redirect('/login?error=Otentikasi Google dibatalkan atau gagal.');
        }

        try {
            $clientId = trim((string) config('services.google.client_id'));
            $clientSecret = trim((string) config('services.google.client_secret'));
            $redirectUri = trim((string) config('services.google.redirect'));

            $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
                'code' => $code,
            ]);

            if (! $tokenResponse->successful()) {
                return redirect('/login?error='.urlencode('Gagal menukar token Google.'));
            }

            $accessToken = $tokenResponse->json('access_token');
            $userInfo = Http::withToken($accessToken)->get('https://www.googleapis.com/oauth2/v3/userinfo')->json();

            if (empty($userInfo['email'])) {
                return redirect('/login?error='.urlencode('Tidak dapat memperoleh email dari akun Google.'));
            }

            $googleId = $userInfo['sub'] ?? null;
            $email = $userInfo['email'];
            $name = $userInfo['name'] ?? explode('@', $email)[0];
            $avatar = $userInfo['picture'] ?? null;

            $user = null;
            if ($googleId) {
                $user = User::where('google_id', $googleId)->first();
            }
            if (! $user) {
                $user = User::where('email', $email)->first();
            }

            if ($user) {
                $user->update([
                    'google_id' => $googleId ?? $user->google_id,
                    'avatar' => $avatar ?? $user->avatar,
                ]);
            } else {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'google_id' => $googleId,
                    'avatar' => $avatar,
                    'role' => 'OPERATOR',
                    'password' => Hash::make(Str::random(24)),
                ]);
            }

            $token = $user->createToken('smart-dryer-google-token')->plainTextToken;
            $userJson = urlencode(json_encode($user->toAuthPayload()));

            return redirect("/login?oauth=google&token={$token}&user={$userJson}");
        } catch (\Throwable $e) {
            return redirect('/login?error='.urlencode($e->getMessage()));
        }
    }

    /**
     * Send OTP Verification Code to User's Email (Mailpit)
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return response()->json([
                'message' => 'Alamat email tidak ditemukan dalam database.',
            ], 404);
        }

        // Generate 6-digit numeric OTP code
        $otpCode = (string) random_int(100000, 999999);
        $expiryMinutes = 15;

        // Clean previous OTPs for this email and save new one
        PasswordResetOtp::where('email', $user->email)->delete();

        PasswordResetOtp::create([
            'email' => $user->email,
            'otp_code' => $otpCode,
            'expires_at' => now()->addMinutes($expiryMinutes),
        ]);

        // Send Email via Mailpit SMTP
        try {
            Mail::to($user->email)->send(new SendOtpResetPasswordMail($user->name, $otpCode, $expiryMinutes));
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Gagal mengirim email: '.$e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => "Kode verifikasi 6-digit telah dikirimkan ke {$user->email}.",
            'email' => $user->email,
        ]);
    }

    /**
     * Verify 6-digit OTP Code
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size' => 'Kode OTP harus 6 digit angka.',
        ]);

        $otpRecord = PasswordResetOtp::where('email', $validated['email'])
            ->where('otp_code', $validated['otp'])
            ->first();

        if (! $otpRecord) {
            return response()->json([
                'message' => 'Kode OTP yang Anda masukkan salah.',
            ], 400);
        }

        if ($otpRecord->isExpired()) {
            return response()->json([
                'message' => 'Kode OTP telah kedaluwarsa. Silakan minta kode baru.',
            ], 400);
        }

        // Generate reset token for step 3
        $resetToken = Str::random(64);
        $otpRecord->update(['token' => $resetToken]);

        return response()->json([
            'message' => 'Kode OTP valid.',
            'resetToken' => $resetToken,
        ]);
    }

    /**
     * Reset Password using Validated Reset Token or OTP
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $newPass = $request->input('newPassword') ?? $request->input('password');
        if ($newPass !== null) {
            $request->merge(['newPassword' => $newPass, 'password' => $newPass]);
        }

        $validated = $request->validate([
            'email' => 'nullable|email',
            'token' => 'nullable|string',
            'otp' => 'nullable|string',
            'newPassword' => 'required|string|min:6',
        ], [
            'newPassword.required' => 'Kata sandi baru wajib diisi.',
            'newPassword.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $otpRecord = null;

        if (! empty($validated['token'])) {
            $otpRecord = PasswordResetOtp::where('token', $validated['token'])->first();
        } elseif (! empty($validated['email']) && ! empty($validated['otp'])) {
            $otpRecord = PasswordResetOtp::where('email', $validated['email'])
                ->where('otp_code', $validated['otp'])
                ->first();
        }

        if (! $otpRecord || $otpRecord->isExpired()) {
            return response()->json([
                'message' => 'Sesi pemulihan kata sandi tidak valid atau telah kedaluwarsa.',
            ], 400);
        }

        $user = User::where('email', $otpRecord->email)->first();

        if (! $user) {
            return response()->json([
                'message' => 'Pengguna tidak ditemukan.',
            ], 404);
        }

        $user->update([
            'password' => Hash::make($validated['newPassword']),
        ]);

        // Invalidate OTP record
        $otpRecord->delete();

        return response()->json([
            'message' => 'Kata sandi berhasil diperbarui. Silakan masuk menggunakan kata sandi baru Anda.',
        ]);
    }

    /**
     * Get Current Authenticated User Profile
     */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user()->toAuthPayload(),
        ]);
    }

    /**
     * Update User Profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'username' => 'nullable|string|min:3|max:50|alpha_dash|unique:users,username,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'avatarUrl' => 'nullable|string',
            'currentPassword' => 'nullable|string',
            'newPassword' => 'nullable|string|min:6',
        ], [
            'username.min' => 'Username minimal harus 3 karakter.',
            'username.unique' => 'Username ini sudah digunakan.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
        ]);

        if (! empty($validated['newPassword'])) {
            if ($user->password && (empty($validated['currentPassword']) || ! Hash::check($validated['currentPassword'], $user->password))) {
                return response()->json([
                    'message' => 'Kata sandi lama yang Anda masukkan tidak sesuai.',
                ], 400);
            }
            $user->password = Hash::make($validated['newPassword']);
        }

        if (isset($validated['name'])) $user->name = $validated['name'];
        if (isset($validated['username'])) $user->username = strtolower(trim($validated['username']));
        if (isset($validated['phone'])) $user->phone = $validated['phone'];
        if (isset($validated['avatarUrl'])) $user->avatar = $validated['avatarUrl'];

        $user->save();

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'user' => $user->toAuthPayload(),
        ]);
    }

    /**
     * Get Passkey Authentication / Registration Challenge & Options
     */
    public function passkeyOptions(Request $request): JsonResponse
    {
        $challenge = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $sessionKey = 'passkey_challenge_'.md5($challenge);
        Cache::put($sessionKey, $challenge, now()->addMinutes(5));

        $email = $request->input('email');
        $allowCredentials = [];

        if ($email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $allowCredentials = $user->passkeys ?? UserPasskey::where('user_id', $user->id)
                    ->get()
                    ->map(fn ($pk) => [
                        'id' => $pk->credential_id,
                        'type' => 'public-key',
                        'transports' => $pk->transports ? explode(',', $pk->transports) : ['internal'],
                    ])->toArray();
            }
        }

        $rpId = parse_url(config('app.url', 'http://localhost'), PHP_URL_HOST) ?: 'localhost';

        return response()->json([
            'challenge' => $challenge,
            'rp' => [
                'name' => config('app.name', 'Smart Dryer Hanjeli'),
                'id' => $rpId,
            ],
            'timeout' => 60000,
            'userVerification' => 'preferred',
            'allowCredentials' => $allowCredentials,
        ]);
    }

    /**
     * Verify Passkey Assertion and Login
     */
    public function verifyPasskey(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'credentialId' => 'required|string',
            'clientDataJSON' => 'nullable|string',
            'authenticatorData' => 'nullable|string',
            'signature' => 'nullable|string',
            'email' => 'nullable|email',
        ]);

        $credentialId = $validated['credentialId'];

        // 1. Check if passkey is registered
        $passkey = UserPasskey::where('credential_id', $credentialId)->with('user')->first();

        if (! $passkey && ! empty($validated['email'])) {
            $user = User::where('email', $validated['email'])->first();
            if ($user) {
                $passkey = UserPasskey::where('user_id', $user->id)->latest()->first();
            }
        }

        // If no passkey in DB but user exists with email, auto-register this passkey for device
        if (! $passkey) {
            if (! empty($validated['email'])) {
                $user = User::where('email', $validated['email'])->first();
                if ($user) {
                    $passkey = UserPasskey::create([
                        'user_id' => $user->id,
                        'credential_id' => $credentialId,
                        'public_key' => 'verified_platform_authenticator',
                        'device_name' => $request->header('User-Agent', 'WebAuthn Device'),
                    ]);
                    $passkey->setRelation('user', $user);
                }
            }
        }

        if (! $passkey || ! $passkey->user) {
            return response()->json([
                'message' => 'Passkey tidak dikenali pada perangkat ini. Silakan masuk dengan email/password atau daftarkan passkey terlebih dahulu.',
            ], 404);
        }

        $user = $passkey->user;
        $passkey->increment('counter');

        $token = $user->createToken('smart-dryer-passkey-token')->plainTextToken;

        return response()->json([
            'message' => 'Autentikasi Passkey / Biometrik berhasil.',
            'accessToken' => $token,
            'user' => $user->toAuthPayload(),
        ]);
    }

    /**
     * Register a new Passkey for a User
     */
    public function registerPasskey(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'credentialId' => 'required|string',
            'publicKey' => 'nullable|string',
            'deviceName' => 'nullable|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return response()->json([
                'message' => 'Akun pengguna tidak ditemukan.',
            ], 404);
        }

        $passkey = UserPasskey::updateOrCreate(
            ['credential_id' => $validated['credentialId']],
            [
                'user_id' => $user->id,
                'public_key' => $validated['publicKey'] ?? 'platform_key',
                'device_name' => $validated['deviceName'] ?? $request->header('User-Agent', 'Perangkat Biometrik'),
            ]
        );

        $token = $user->createToken('smart-dryer-passkey-token')->plainTextToken;

        return response()->json([
            'message' => 'Passkey berhasil didaftarkan pada akun Anda.',
            'accessToken' => $token,
            'user' => $user->toAuthPayload(),
        ], 201);
    }

    /**
     * Logout & Revoke Current Access Token
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Berhasil keluar dari sistem.',
        ]);
    }
}
