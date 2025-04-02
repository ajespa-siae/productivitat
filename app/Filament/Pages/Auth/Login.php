<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Auth;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;
use Illuminate\Support\Facades\Log;
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
                    ->autofocus()
                    ->extraInputAttributes(['tabindex' => 1]),
                $this->getPasswordFormComponent()
                    ->extraInputAttributes(['tabindex' => 2]),
            ])
            ->statePath('data');
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

        $state = $this->form->getState();

        try {
            // Validar que tenemos un username
            if (empty($state['username'])) {
                throw ValidationException::withMessages([
                    'username' => 'El nombre de usuario es requerido.',
                ]);
            }

            Log::info('Intentando autenticar usuario: ' . $state['username']);

            // Intentar autenticar al usuario
            if (Auth::attempt([
                'samaccountname' => $state['username'],
                'password' => $state['password']
            ])) {
                $user = Auth::user();
                Log::info('Usuario autenticado en Filament: ' . $user->samaccountname);
                
                try {
                    // Usar eager loading para obtener el usuario LDAP con todos sus atributos
                    $ldapUser = LdapUser::select(['*'])
                        ->where('samaccountname', '=', $state['username'])
                        ->first();
                    
                    if ($ldapUser) {
                        $attributes = $ldapUser->getAttributes();
                        Log::info('Atributos LDAP encontrados en Filament: ' . print_r($attributes, true));
                        
                        // Preparar atributos para la sesión
                        $attributesForSession = [];
                        foreach ($attributes as $key => $value) {
                            if (is_array($value) && count($value) === 1) {
                                $attributesForSession[$key] = $value[0];
                            } else {
                                $attributesForSession[$key] = $value;
                            }
                        }
                        
                        // Guardar en la sesión
                        session(['ldap_attributes' => $attributesForSession]);
                        Log::info('Atributos LDAP guardados en sesión desde Filament: ' . print_r($attributesForSession, true));
                        
                        // Intentar obtener el employeeID directamente
                        $employeeId = $ldapUser->getFirstAttribute('employeeid');
                        
                        if (!$employeeId) {
                            foreach ($possibleAttributes as $attr) {
                                $value = $ldapUser->getFirstAttribute($attr);
                                if ($value) {
                                    Log::info("Encontrado valor en atributo {$attr}: {$value}");
                                    $employeeId = $value;
                                    break;
                                }
                            }
                        }
                        
                        if ($employeeId) {
                            $user->nif = $employeeId;
                            $user->save();
                            Log::info('NIF actualizado correctamente a: ' . $employeeId);
                        } else {
                            Log::warning('No se encontró ningún atributo de ID válido');
                        }
                    } else {
                        Log::warning('No se encontró el usuario LDAP: ' . $state['username']);
                    }
                } catch (\Exception $e) {
                    Log::error('Error durante la sincronización en Filament: ' . $e->getMessage());
                    Log::error($e->getTraceAsString());
                }
                
                session()->regenerate();
                
                return app(LoginResponse::class);
            }
            
            Log::warning('Fallo de autenticación para el usuario: ' . $state['username']);
            
        } catch (\Exception $e) {
            Log::error('Error durante la autenticación en Filament: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
        }

        $this->addError('username', __('filament-panels::pages/auth/login.messages.failed'));
        
        return null;
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
