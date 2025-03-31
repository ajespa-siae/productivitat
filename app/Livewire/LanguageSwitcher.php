<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageSwitcher extends Component
{
    public string $currentLocale;

    public function mount()
    {
        $this->currentLocale = App::getLocale();
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }

    public function switchLocale(string $locale)
    {
        if (!in_array($locale, ['ca', 'es'])) {
            return;
        }

        Session::put('locale', $locale);
        App::setLocale($locale);
        $this->currentLocale = $locale;
        
        return redirect(request()->header('Referer'));
    }
}
