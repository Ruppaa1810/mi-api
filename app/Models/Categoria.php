<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Importar
class Categoria extends Model {
use HasFactory;
protected $fillable = ['nombre'];
public function productos(): HasMany {
// Especificamos la FK y la PK local
return $this->hasMany(Producto::class);
}
}
