<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'telefono',
        'avatar', 
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relación con el rol
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    
    // Método auxiliar para verificar si tiene un rol específico
    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }
    
    // Método auxiliar para obtener el nombre del rol
    public function getRoleNameAttribute()
    {
        return $this->role ? $this->role->name : 'Sin rol';
    }

    // ========== MÉTODOS PARA ADMINLTE ==========
    
    /**
     * Obtener la URL de la imagen de perfil para AdminLTE
     */
    public function adminlte_image()
    {
        // Si el usuario tiene un avatar guardado, mostrar su ruta completa
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return Storage::url($this->avatar);
        }
        
        // Si no tiene avatar, generar uno con las iniciales usando UI Avatars
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF&bold=true';
        
        // Alternativa: Usar una imagen por defecto local
        // return asset('vendor/adminlte/dist/img/avatar-default.png');
    }

    /**
     * Obtener la descripción/rol del usuario para AdminLTE
     */
    public function adminlte_desc()
    {
        // Retornar el nombre del rol del usuario
        return $this->role ? $this->role->name : 'Usuario';
    }

    /**
     * Obtener la URL del perfil del usuario para AdminLTE
     */
    public function adminlte_profile_url()
    {
        // Redirigir a la pestaña de perfil en configuración
        return route('configuracion.index') . '?tab=perfil#perfil';
        
        // Alternativas:
        // return route('profile.edit');
        // return route('perfil.show', $this->id);
        // return '/configuracion?tab=perfil';
    }
    
    // ========== MÉTODOS PARA MANEJO DE AVATAR ==========
    
    /**
     * Actualizar el avatar del usuario
     */
    public function updateAvatar($file)
    {
        // Eliminar avatar anterior si existe
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            Storage::disk('public')->delete($this->avatar);
        }
        
        // Guardar nuevo avatar
        $path = $file->store('avatars', 'public');
        $this->avatar = $path;
        $this->save();
        
        return $path;
    }
    
    /**
     * Eliminar el avatar del usuario
     */
    public function deleteAvatar()
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            Storage::disk('public')->delete($this->avatar);
        }
        
        $this->avatar = null;
        $this->save();
    }
    
    /**
     * Obtener la URL completa del avatar
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return Storage::url($this->avatar);
        }
        
        // Retornar avatar por defecto con iniciales
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }
}