<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    use HasFactory;

    protected $fillable = ['marca_id', 'nome', 'imagem', 'numero_portas', 'lugares', 'abs', 'air_bag' ];

     public function rules(){
        return  [
            'marcas_id' => 'exists:marca,id',
            'nome' => 'required|unique:modelos,nome,'.$this->id.'|min:3',
            'imagem' => 'required|file|mimes:png,jpeg,jpg',
            'numero_portas' => 'required|integer|digits_between:1,5',
            'lugares' => 'required|integer|digits_between:1,20',
            'abs' => 'required|boolean',
            'air_bag' => 'required|boolean',
        ];
     }

     public function marca(){
        
        // Um Modelos pertence a varias Marcas
        return $this->belongsTo('App\Models\Marca');
     }
    
}
