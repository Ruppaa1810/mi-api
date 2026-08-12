<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'precio', 'stock', 'categoria_id', 'marca_id', 'unidad_medida', 'imagen_url'];

    // Relación: Un producto pertenece a una categoría
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function marca()
{
    return $this->belongsTo(Marca::class);
}

}