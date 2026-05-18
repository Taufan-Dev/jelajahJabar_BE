<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Actions\Action;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Login extends BaseLogin
{
    public function registerAction(): Action
    {
        return parent::registerAction()
            ->label('Daftarkan wisata anda disini');
    }

    public function getSubheading(): string | Htmlable | null
    {
        if (! filament()->hasRegistration()) {
            return null;
        }

        return new HtmlString('Pengelola baru? ' . $this->registerAction->toHtml());
    }

    public function getHeading(): string | Htmlable
    {
        return 'Masuk';
    }

    public function authenticateAction(): Action
    {
        return parent::authenticateAction()
            ->label('Masuk');
    }
}
