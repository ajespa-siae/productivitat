<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Facades\Filament;
use App\Models\User;

class Login extends BaseLogin
{
    use WithRateLimiting;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('username')
                    ->label('Usuari')
                    ->required()
                    ->autocomplete('username')
                    ->autofocus(),
                $this->getPasswordFormComponent(),
            ]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'samaccountname' => $data['username'],
            'password' => $data['password'],
        ];
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            throw ValidationException::withMessages([
                'username' => __('filament-panels::pages/auth/login.messages.throttled', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]),
            ]);
        }

        $data = $this->form->getState();

        if (! auth()->attempt($this->getCredentialsFromFormData($data))) {
            throw ValidationException::withMessages([
                'username' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    public function getHeading(): string 
    {
        return 'Iniciar sessió';
    }

    public function getSubheading(): ?string
    {
        return 'Només els usuaris administradors poden accedir al panell d\'administració.';
    }
}
