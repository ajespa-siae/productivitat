<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;
use LdapRecord\Container;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use LdapRecord\Auth\BindException;

class EmployeeIdController extends Controller
{
    public function edit()
    {
        return view('employee-id.edit');
    }

    public function update(Request $request)
    {
        $request->validate([
            'employeeid' => 'required|string|max:255'
        ]);

        $user = auth()->user();
        $loginUsername = Session::get('login_username', $user->name);
        
        try {
            // Registrar el username para depuración
            Log::info('Actualizando employeeId para usuario:', [
                'login_username' => $loginUsername,
                'session_data' => Session::all(),
                'auth_user' => $user->toArray()
            ]);

            // Intentar directamente con MMontes
            $ldapUser = LdapUser::query()
                ->where('samaccountname', '=', 'MMontes')
                ->first();

            if (!$ldapUser) {
                Log::error('Usuario no encontrado en LDAP', [
                    'login_username' => $loginUsername,
                    'ldap_query' => "samaccountname=MMontes"
                ]);
                return back()->withErrors(['error' => 'No se pudo encontrar el usuario en LDAP.']);
            }

            Log::info('Usuario encontrado en LDAP', [
                'ldap_attributes' => $ldapUser->getAttributes(),
                'can_modify' => $ldapUser->getConnection()->isConnected(),
                'connection_config' => $ldapUser->getConnection()->getConfiguration()
            ]);

            try {
                // Intentar actualizar el employeeId en LDAP
                $oldAttributes = $ldapUser->getAttributes();
                $ldapUser->employeeId = $request->employeeid;
                $saveResult = $ldapUser->save();
                
                Log::info('Resultado de la actualización LDAP:', [
                    'save_result' => $saveResult,
                    'old_attributes' => $oldAttributes,
                    'new_attributes' => $ldapUser->getAttributes()
                ]);
            } catch (\Exception $ldapError) {
                Log::error('Error específico al actualizar LDAP:', [
                    'error_message' => $ldapError->getMessage(),
                    'error_code' => $ldapError->getCode(),
                    'error_class' => get_class($ldapError)
                ]);
                throw $ldapError;
            }

            // Actualizar el NIF en la base de datos local
            $user->nif = $request->employeeid;
            $user->save();

            return redirect()->route('dashboard')->with('success', 'ID de empleado actualizado correctamente');
        } catch (BindException $e) {
            Log::error('Error de autenticación LDAP:', [
                'error' => $e->getMessage(),
                'diagnostic_message' => $e->getDiagnosticMessage(),
                'login_username' => $loginUsername
            ]);
            return back()->withErrors(['error' => 'Error de autenticación con LDAP. Por favor, contacte con el administrador.']);
        } catch (\Exception $e) {
            Log::error('Error actualizando employeeId:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'login_username' => $loginUsername
            ]);
            return back()->withErrors(['error' => 'No se pudo actualizar el ID de empleado: ' . $e->getMessage()]);
        }
    }
}
