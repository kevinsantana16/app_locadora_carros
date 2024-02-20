<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'imagem'];

     public function rules(){
        return  [
            'nome' => 'required|unique:marcas,nome,'.$this->id.'|min:3',
            'imagem' => 'required|file|mimes:png',
        ];
     }
    
    public function feedback(){
        return [
            'required' => 'o campo :attribute e obrigatorio',
            'nome.unique' => 'nome da marca ja existe',
            'nome.min' => 'O nome deve ter no minimo tres caracteres'
            ];
    }

    public function modelos(){
       
       // Uma Marca POSSUI varios modelos
        return $this->hasMany(Modelo::class);
    }
       
}
