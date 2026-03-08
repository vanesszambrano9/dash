<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
   protected $table = 'sessions';
   protected $primaryKey = 'id';
   public $incrementing = false;
   protected $keyType = 'string';
   protected $fillable = [
      'id',
      'user_id',
      'ip_address',
      'user_agent',
      'payload',
      'last_activity',
   ];

   // Relación con el modelo User
   public function user()
   {
      return $this->belongsTo(User::class, 'user_id');
   }

   // Obtener la fecha/hora de la última actividad como Carbon
   public function getLastActivityAtAttribute()
   {
      return \Carbon\Carbon::createFromTimestamp($this->last_activity);
   }

   // Scope para sesiones activas (última actividad en los últimos 30 minutos)
   public function scopeActive($query)
   {
      return $query->where('last_activity', '>=', now()->subMinutes(30)->timestamp);
   }

   // Scope para sesiones de un usuario específico
   public function scopeForUser($query, $userId)
   {
      return $query->where('user_id', $userId);
   }

   // Método para terminar la sesión
   public function terminate()
   {
      return $this->delete();
   }

   // Método estático para contar sesiones activas
   public static function countActiveSessions()
   {
      return self::active()->count();
   }
}
