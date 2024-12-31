<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Validation\ValidationException;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Log;

class CustomLogin extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.custom-login';

    public function authenticate(): LoginResponse
    {
        try {
            $data = $this->form->getState();

            $credentials = [
                'email' => $data['email'],
                'password' => $data['password'],
            ];

            if (!Auth::attempt($credentials)) {
                throw ValidationException::withMessages([
                    'data.email' => 'بيانات الدخول غير صحيحة',
                ]);
            }

            session()->regenerate();

            return app(LoginResponse::class);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Login error', ['message' => $e->getMessage()]);
            throw ValidationException::withMessages([
                'data.email' => 'حدث خطأ أثناء تسجيل الدخول',
            ]);
        }
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('email')
                ->label('البريد الإلكتروني')
                ->email()
                ->required()
                ->autocomplete('email'),
            TextInput::make('password')
                ->label('كلمة المرور')
                ->password()
                ->required()
                ->autocomplete('current-password'),
        ])->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('login')
                ->label('تسجيل الدخول')
                ->submit('authenticate')
                ->color('primary')
        ];
    }
}
