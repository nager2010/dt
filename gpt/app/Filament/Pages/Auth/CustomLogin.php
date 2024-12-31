<?php
namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;

class CustomLogin extends BaseLogin
{
    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('email')
                ->label('البريد الإلكتروني')
                ->required(),
            TextInput::make('password')
                ->label('كلمة المرور')
                ->password()
                ->required(),
        ]);
    }
}
