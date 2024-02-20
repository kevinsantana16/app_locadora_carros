<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ModeloController extends Controller
{
    
    public $modelo;
 
    public function __construct(Modelo $modelo){
      $this->modelo = $modelo;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       $modelo = $this->modelo;
      $modelos = array();
      
    
    
        if($request->has('atributosMarca')) {
            $atributos_marca = $request->get('atributosMarca');
            $modelos = $modelo->with('marca:id,'.$atributos_marca);
            
        } else {
            $modelos = $modelo->with('marca');
        }
      
 
        if($request->has('filtro')){
        
            $filtros = explode(';', $request->filtro);
            foreach($filtros as $key => $condicao){
           
            $c = explode(':', $condicao);
            
            $modelos = $modelo->where($c[0], $c[1], $c[2]);
            

        }
        
    }

      // atributos foi uma variavel  criado na URL usando o sinal de  "?"
     
        if($request->has('atributosModelo')){
        
            $atributos_modelo = $request->atributosModelo;
             $modelos = $modelo->selectRaw($atributos_modelo);
         
      }
        else{
            $modelos = $modelo->with('marca');
         }

        return response()->json($modelos->get(), 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $modelos = $this->modelo;

        $request->validate($this->modelo->rules());
        
       $imagem = $request->file('imagem');
       $imagem_urn = $imagem->store('imagens/modelos', 'public');

       $modelos->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs,
       ]);

        return response()->json($modelos, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         $modelos = $this->modelo->with('marca')->find($id);
       
        if($modelos === null){
            
            return response()->json( ['error' => 'Recurso pesquisado nao existe'], 404) ;
        }

        return response()->json($modelos, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function edit(Modelo $modelos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $modelos = $this->modelo->find($id);
        
        if($modelos === null){
            
            return response()->json(['error' => 'Impossivel fazer atualizacao. Recurso pesquisado nao existe'] , 404);
        }
        

        if($request->method() === 'PATCH'){

            $regrasDinamicas = array();
           
            foreach($modelos->rules() as $input => $regra){
                
                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }

             $request->validate($regrasDinamicas);

        }
        else{
            
            $request->validate($modelos->rules());
            
        }
         
        // Exlui o arquivo antigo caso um novo tenha sido enviado pelo request
        if($request->file('imagem')){
            Storage::disk('public')->delete($modelos->imagem);
        }
            
        
        
         
       $imagem = $request->file('imagem');
       $imagem_urn = $imagem->store('imagens', 'public');

       $modelos->fill($request->all());
       $modelos->imagem = $imagem_urn;

        $modelos->save();
       
       

            return response()->json($modelos, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $modelos = $this->modelo->find($id);
       
        
        
        if($modelos === null){
            
            return response()->json(['error' => 'Impossivel fazer a exclusao. Recurso pesquisado nao existe'] , 404);;
        }
       
       // Exlui o arquivo antigo caso um novo tenha sido enviado pelo request
       Storage::disk('public')->delete($modelos->imagem);
        
       
        $modelos->delete();
        
        return response()->json(['msg' => 'modelo deletado com sucesso'], 200);
    }
}
