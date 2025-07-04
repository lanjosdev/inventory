<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function loginRules()
    {
        return [
            'email' => 'required|email|max:255',
            'password' => 'required|min:8|max:30',
        ];
    }
    public static function loginFeedback()
    {
        return [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser válido.',
            'email.max' => 'O e-mail deve ter no máximo 255 caracteres.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.max' => 'A senha deve ter no máximo 30 caracteres.'
        ];
    }
    public static function rules()
    {
        return [
            'name' => 'required|max:255|min:4',
            'email' => 'required|email|max:255',
            'password' => 'required|min:8|max:30',
        ];
    }
    public static function feedback()
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome deve ter no máximo 255 caracteres.',
            'name.min' => 'O nome deve ter no mínimo 4 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser válido.',
            'email.max' => 'O e-mail deve ter no máximo 255 caracteres.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.max' => 'A senha deve ter no máximo 30 caracteres.'
        ];
    }

    // Regras para alteração de senha pelo próprio usuário
    public static function rulesUpdatePassword()
    {
        return [
            'password' => 'required|min:8|max:30|confirmed',
        ];
    }
    public static function feedbackUpdatePassword()
    {
        return [
            'password.required' => 'A nova senha é obrigatória.',
            'password.min' => 'A nova senha deve ter no mínimo 8 caracteres.',
            'password.max' => 'A nova senha deve ter no máximo 30 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.'
        ];
    }
    // Regras para alteração de senha por admin
    public static function rulesUpdatePasswordAdmin()
    {
        return [
            'password' => 'required|min:8|max:30|confirmed',
        ];
    }
    public static function feedbackUpdatePasswordAdmin()
    {
        return [
            'password.required' => 'A nova senha é obrigatória.',
            'password.min' => 'A nova senha deve ter no mínimo 8 caracteres.',
            'password.max' => 'A nova senha deve ter no máximo 30 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.'
        ];
    }

    /**
     * Relação: retorna os papéis (roles) do usuário.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'fk_user', 'fk_role');
    }
}