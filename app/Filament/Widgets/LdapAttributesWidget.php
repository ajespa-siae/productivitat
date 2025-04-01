<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LdapAttributesWidget extends Widget
{
    protected static string $view = 'filament.widgets.ldap-attributes-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function getLdapAttributes(): array
    {
        $attributes = session('ldap_attributes', []);
        Log::info('Atributos recuperados de la sesión: ' . print_r($attributes, true));
        return $attributes;
    }

    public function hasLdapAttributes(): bool
    {
        return !empty($this->getLdapAttributes());
    }

    public static function canView(): bool
    {
        // Comentado temporalmente - descomentar si se necesita mostrar los atributos LDAP
        return false;
        
        // return true; // Siempre mostrar el widget para verificar que se está renderizando
    }
}
