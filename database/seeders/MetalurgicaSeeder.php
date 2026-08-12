<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;

class MetalurgicaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cargar Marcas Reales del Rubro
        $sinpar = Marca::create(['nombre' => 'Sinpar']); // Discos y corte
        $acindar = Marca::create(['nombre' => 'Acindar']); // Hierros y perfiles
        $conarco = Marca::create(['nombre' => 'Conarco']); // Electrodos y soldadura
        $dowen = Marca::create(['nombre' => 'Dowen Pagio']); // Herramientas/Herrajes
        $generico = Marca::create(['nombre' => 'Genérico']);

        // 2. Cargar Categorías
        $catPerfiles = Categoria::create(['nombre' => 'Perfiles y Hierros']);
        $catCorte = Categoria::create(['nombre' => 'Abrasivos y Corte']);
        $catSoldadura = Categoria::create(['nombre' => 'Soldadura e Insumos']);
        $catEstructuras = Categoria::create(['nombre' => 'Estructuras Estándar']);

        // 3. Cargar Productos Reales
        
        // --- Categoría: Perfiles y Hierros (Venta por barra/metro) ---
        Producto::create([
            'nombre' => 'Hierro Angulo 1 x 1/8 (Barra 6m)',
            'descripcion' => 'Perfil de hierro ángulo laminado en caliente, ideal para herrería y estructuras.',
            'precio' => 24500.00,
            'stock' => 40,
            'unidad_medida' => 'metro',
            'categoria_id' => $catPerfiles->id,
            'marca_id' => $acindar->id,
            'imagen_url' => 'https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?w=600'
        ]);

        Producto::create([
            'nombre' => 'Caño Estructural Cuadrado 40x40 (Espesor 1.6mm)',
            'descripcion' => 'Tubo estructural de acero ideal para marcos de portones y rejas.',
            'precio' => 32000.00,
            'stock' => 25,
            'unidad_medida' => 'metro',
            'categoria_id' => $catPerfiles->id,
            'marca_id' => $acindar->id,
            'imagen_url' => 'https://images.unsplash.com/photo-1535378917042-10a22c95931a?w=600'
        ]);

        // --- Categoría: Abrasivos y Corte (Venta por unidad) ---
        Producto::create([
            'nombre' => 'Disco de Corte Flap 4.5 pulgadas',
            'descripcion' => 'Disco para amoladora angular, ideal para desbaste fino de soldaduras.',
            'precio' => 1800.00,
            'stock' => 150,
            'unidad_medida' => 'unidad',
            'categoria_id' => $catCorte->id,
            'marca_id' => $sinpar->id,
            'imagen_url' => 'https://images.unsplash.com/photo-1504148455328-c376907d081c?w=600'
        ]);

        // --- Categoría: Soldadura ---
        Producto::create([
            'nombre' => 'Electrodos Punta Azul 6013 (Kilo)',
            'descripcion' => 'Electrodos para soldadura eléctrica de acero al carbono, arco suave.',
            'precio' => 6500.00,
            'stock' => 80,
            'unidad_medida' => 'kg',
            'categoria_id' => $catSoldadura->id,
            'marca_id' => $conarco->id,
            'imagen_url' => 'https://images.unsplash.com/photo-1560634951-92f13d3a229c?w=600'
        ]);

        // --- Categoría: Estructuras Terminadas (Lo tradicional del Ecommerce) ---
        Producto::create([
            'nombre' => 'Canasto de Basura Estándar para Vereda',
            'descripcion' => 'Canasto reforzado con metal desplegado y base para cementar.',
            'precio' => 45000.00,
            'stock' => 5,
            'unidad_medida' => 'unidad',
            'categoria_id' => $catEstructuras->id,
            'marca_id' => $generico->id,
            'imagen_url' => 'https://images.unsplash.com/photo-1565428252355-3fdb3b3e3d8d?w=600'
        ]);
    }
}