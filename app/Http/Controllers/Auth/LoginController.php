<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        try {
            if (Auth::attempt([
                'samaccountname' => $credentials['username'],
                'password' => $credentials['password']
            ], $request->boolean('remember'))) {
                
                $user = Auth::user();
                Log::info('Usuario autenticado en Frontend: ' . $user->samaccountname);
                
                try {
                    $ldapUser = LdapUser::select(['*'])
                        ->where('samaccountname', '=', $credentials['username'])
                        ->first();
                    
                    if ($ldapUser) {
                        $attributes = $ldapUser->getAttributes();
                        Log::info('Atributos LDAP encontrados en Frontend: ' . print_r($attributes, true));
                        
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
                        Log::info('Atributos LDAP guardados en sesión desde Frontend: ' . print_r($attributesForSession, true));
                        
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
                        Log::warning('No se encontró el usuario LDAP: ' . $credentials['username']);
                    }
                } catch (\Exception $e) {
                    Log::error('Error durante la sincronización en Frontend: ' . $e->getMessage());
                    Log::error($e->getTraceAsString());
                }

                $request->session()->regenerate();

                return redirect()->intended(route('dashboard'));
            }

            Log::warning('Fallo de autenticación para el usuario: ' . $credentials['username']);
            
            return back()->withErrors([
                'username' => __('auth.failed'),
            ])->onlyInput('username');

        } catch (\Exception $e) {
            Log::error('Error durante la autenticación en Frontend: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return back()->withErrors([
                'username' => __('auth.failed'),
            ])->onlyInput('username');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
