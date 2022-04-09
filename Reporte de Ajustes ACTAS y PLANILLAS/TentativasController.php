<?php

namespace App\Http\Controllers\Medicina;

use App\Http\Controllers\Controller;
use App\Models\Medicina\Tentativas;
use App\Models\Medicina\Usuarios;
use App\Models\Medicina\TrabalhosAlunos;
use Illuminate\Http\Request;
use JWTAuth;
use Illuminate\Support\Facades\DB;
use App\Models\Medicina\PlataAsuncion;

use App\Models\Medicina\OfertaDisciplina;

class TentativasController extends Controller
{
    protected $alunotrabalho;
    protected $tentativa;
    protected $usuarios;
       protected $servico;
       protected $plata;
    

    public function __construct( PlataAsuncion $plata, TrabalhosAlunos $alunotrabalho ,Tentativas $tentativa, Usuarios $usuarios, OfertaDisciplina $servico)
    {
        $this->tentativa = $tentativa;
  $this->alunotrabalho = $alunotrabalho;
        $this->usuarios = $usuarios;
            $this->servico = $servico;
              $this->plata = $plata;
    }
    
    
    public function concertar(){
        $data = $this->servico
        ->where('created','>','2020-07-01 18:37:43')
        ->where('created','<','2020-10-01 18:37:43')
        
        ->get();
        
        return response()->json($data,200);
        
    }
    public function pegar_id(Request $request)
    {
        $token = JWTAuth::getToken();
        $apy = JWTAuth::getPayload($token)->toArray();
        $token = JWTAuth::decode($token);
        $a = [
            'sub' => $token['sub'],
            'per' => $token['per'],
        ];
        return $a;
    }
    
    public function tirarAcentos($string)
    {
        return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/", "/(Ç)/", "/(ç)/"), explode(" ", "a A e E i I o O u U n N C c"), $string);
    }
    
    // pegarDadosProfessor(){}
    
       public function AllMateriasTurma($id)
    {
       //  $usuario = $this->pegar_id($request);
         
         $resposta = $this->servico
         
                ->where('oferta_disciplina.turma_id', $id)
                ->where('oferta_disciplina.status', 1)
                ->where('turma.status', 1)
                ->where('oferta_disciplina.created','>', '2020-06-01 00:20:08')
                        ->join('disciplinas', 'disciplinas.id','=','oferta_disciplina.disciplinas_id')
                ->join('turma', 'turma.id', '=','oferta_disciplina.turma_id')
                    ->join('semestre', 'semestre.id', 'oferta_disciplina.semestre_id')
       
                ->join('funcionario', 'funcionario.id', '=','oferta_disciplina.funcionario_id')
                ->select('oferta_disciplina.id as oferta_disciplina_id','turma.nome as nome_turma','semestre.nome as nome_semestre')
        
            
         ->get();
         
         if($resposta){
             return $resposta;
             
         }
         
         
         
    }

    public function listar_notas_trabalhosPraticos($id_oferta_disciplina)
    {
        
        //  return $this->AllMateriasTurma(136);  
       
        $notasTrabalhos = $this->alunotrabalho 
         ->where('ex_trabalhos_aluno.oferta_disciplina_id',$id_oferta_disciplina)
        //  ->where('ex_trabalhos_id', 367)
         
      ->join('usuarios','usuarios.id','ex_trabalhos_aluno.usuarios_id')
        ->join('ex_trabalhos', 'ex_trabalhos.id','ex_trabalhos_aluno.ex_trabalhos_id')
         ->select('ex_trabalhos.data_inicio','ex_trabalhos.hora_inicio','ex_trabalhos_aluno.id','ex_trabalhos_id','nota','usuarios.nome as nome_aluno','ex_trabalhos_aluno.usuarios_id')
        //  ->orderBy('ex_trabalhos_aluno.nota','asc')
          ->orderBy('ex_trabalhos_aluno.id','asc')
           ->where('ex_trabalhos_aluno.status',1)
           ->where('ex_trabalhos.status',1)
          ->where('ex_trabalhos_aluno.nota','>=',1)
         ->get();
         
    //   return $notasTrabalhos;
        $usuarios = [];
        $ex_trabalho=[];
        $dataTrabalho= '';
   
        
         
         foreach($notasTrabalhos as $element){
              if(!isset($ex_trabalho[$element->ex_trabalhos_id])){
                 $ex_trabalho[$element->ex_trabalhos_id] = (object)['ex_trabalho_id' => $element->ex_trabalhos_id];
             }
    
             if(!isset($usuarios[$element->usuarios_id])){
                 $usuarios[$element->usuarios_id] = (object)['id' => $element->usuarios_id,'nome_aluno' => $element->nome_aluno];
             }
      
             $dataTrabalho =   date('d/m/Y' ,strtotime($element->data_inicio));
        
             
         }
         
        //   return response()->json($ex_trabalho, 200);   
         
         $somaNotaTrabalhos=[];
           $todosTrabalhosAlunos=[];
        //   $data[]
           $nota=[];
            // $nota2=[];
           
          foreach($usuarios as $usu){
              
             foreach($notasTrabalhos as $item){
          
                if($item->usuarios_id == $usu->id){
      
                $somaNotaTrabalhos[$item->ex_trabalhos_id] = (object) [
                    'usuario_id' => $usu->id,
                    'ex_trabalhos_id' => $item->ex_trabalhos_id,
                    'nota' => $item->nota
               
                    ];
                  }
         
             }
                  $todosTrabalhosAlunos[] = (object)[
                        'usuario_id' => $usu->id,
                        'nome_aluno' => $usu->nome_aluno,
                        'trabalhoaluno' =>  $somaNotaTrabalhos,
                
                 
                  ];
                  $nota=0;
                  $somaNotaTrabalhos=[];
     
         }
                // return $todosTrabalhosAlunos;
         $soma=0;
            
         foreach($todosTrabalhosAlunos as $trabalhos){
         foreach($trabalhos->trabalhoaluno as $dados){
             $soma = $soma + (int)$dados->nota;
         }
         if(is_float($soma/count($ex_trabalho))== true){
               $trabalhos->somatotal =  number_format($soma/count($ex_trabalho),2) ;
         }else{
             $trabalhos->somatotal =  $soma/count($ex_trabalho) ;
         }
             $soma=0;
         }
    
          
        
        // return $todosTrabalhosAlunos;
  return (object) ['todosTrabalhosAlunos'=>$todosTrabalhosAlunos,
    'dataTrabalho'=>$dataTrabalho
    ];   
                
    }
    
    public function listar_notas_trabalhosPraticos2($id_oferta_disciplina,$usuario_id)
    {
        
        $notasTrabalhos = $this->alunotrabalho 
            ->where('ex_trabalhos_aluno.oferta_disciplina_id',$id_oferta_disciplina)
            ->join('usuarios','usuarios.id','ex_trabalhos_aluno.usuarios_id')
            ->join('ex_trabalhos', 'ex_trabalhos.id','ex_trabalhos_aluno.ex_trabalhos_id')
            ->select('ex_trabalhos.data_inicio','ex_trabalhos.hora_inicio','ex_trabalhos_aluno.id','ex_trabalhos_id','nota','usuarios.nome as nome_aluno','ex_trabalhos_aluno.usuarios_id')
            ->where('ex_trabalhos_aluno.usuarios_id',$usuario_id)
            ->where('ex_trabalhos.status',1)
            ->where('ex_trabalhos_aluno.nota', '>=', 1)
            ->orderBy('ex_trabalhos_aluno.id','asc')
        ->get();
         
        $usuarios = [];
        $ex_trabalho=[];
        $dataTrabalho='';
        
         
        foreach($notasTrabalhos as $element){
             
            if(!isset($ex_trabalho[$element->ex_trabalhos_id])){
                 $ex_trabalho[$element->ex_trabalhos_id] = (object)['ex_trabalho_id' => $element->ex_trabalhos_id];
            }
    
            if(!isset($usuarios[$element->usuarios_id])){
                 $usuarios[$element->usuarios_id] = (object)['id' => $element->usuarios_id,'nome_aluno' => $element->nome_aluno];
            }
      
            $dataTrabalho =   date('d/m/Y' ,strtotime($element->data_inicio));

        }
         
        // response()->json($notasTrabalhos, 200);   
         
        $somaNotaTrabalhos=[];
        $todosTrabalhosAlunos=[];
        $nota=[];
        
          foreach($usuarios as $usu){
              
             foreach($notasTrabalhos as $item){
          
                    if($item->usuarios_id == $usu->id){
          
                    $somaNotaTrabalhos[$item->ex_trabalhos_id] = (object) [
                        'usuario_id' => $usu->id,
                         'ex_trabalhos_id' => $item->ex_trabalhos_id,
                         'nota' => $item->nota
                   
                        ];
                      }
         
             }
            
                $todosTrabalhosAlunos[] = (object)[
                        'usuario_id' => $usu->id,
                        'nome_aluno' => $usu->nome_aluno,
                        'trabalhoaluno' =>  $somaNotaTrabalhos,
                
                 
                  ];
                  $nota=0;
                  $somaNotaTrabalhos=[];
     
         }
        //   response()->json($todosTrabalhosAlunos, 200);
         $soma=0;
           
         foreach($todosTrabalhosAlunos as $trabalhos){
             
             foreach($trabalhos->trabalhoaluno as $dados){
                 $soma = $soma + (int)$dados->nota;
             }
             if(is_float($soma/count($ex_trabalho))== true){
                   $trabalhos->somatotal =  number_format($soma/count($ex_trabalho),2) ;
             }else{
                 $trabalhos->somatotal =  $soma/count($ex_trabalho) ;
             }
             $soma=0;
         }
    
    
          
        
        //  return $todosTrabalhosAlunos;
          return  [
                'todosTrabalhosAlunos'=>$todosTrabalhosAlunos,
                'dataTrabalho'=>$dataTrabalho
            ];   
                
                
    }
    
    public  function lista_notas_disciplina ($id_turma)
    {
     $ofertasdaturma =  $this->AllMateriasTurma($id_turma);  
     
    //  return $ofertasdaturma;
      foreach($ofertasdaturma as $oferta){
           $nome_turmatotal= $oferta->nome_semestre.' '.$oferta->nome_turma;
//   $oferta_disciplina_id = $oferta->oferta_disciplina_id;
   
        $data = $this->tentativa
        
            ->join('usuarios','usuarios.id','ex_tentativa.usuarios_id')
            ->join('ex_examen','ex_examen.id','ex_tentativa.ex_examen_id')
            ->join('ex_tipo_examen','ex_tipo_examen.id','ex_examen.TIPO_EXAMEN_ID')
            ->join('oferta_disciplina','oferta_disciplina.id','=','ex_tentativa.oferta_disciplina_id')
            ->join('turma','turma.id','=','oferta_disciplina.turma_id')
            ->join('disciplinas','disciplinas.id','=','oferta_disciplina.disciplinas_id')
            ->join('config_filial','config_filial.id','oferta_disciplina.config_filial_id')
            ->join('semestre', 'semestre.id', 'oferta_disciplina.semestre_id')
            ->join('periodo_letivo', 'periodo_letivo.id', 'ex_tentativa.periodo_letivo_id')   
            ->leftjoin('periodo_anual','periodo_anual.id','periodo_letivo.periodo_anual_id')
            ->join('ano', 'ano.id', 'periodo_letivo.ano_id')  
            ->join('funcionario', 'funcionario.id','oferta_disciplina.funcionario_id')
            ->where('oferta_disciplina.created','>=','2020-10-12')
          ->select('ex_tipo_examen.nome as nome_tipo_examen', DB::raw('CONCAT(usuarios.sobrenome,\', \', usuarios.nome) as nome_usuarios'),
          DB::raw('CONCAT(funcionario.nome,\' \', funcionario.sobrenome) as nome_funcionario'),
          'ex_tentativa.nota', 'usuarios.id as usuarios_id','ex_tipo_examen.id as tipo_examen_id','usuarios.doc_oficial','ex_examen.FECHA_INICIO AS fecha_inicio',
          'disciplinas.nome as nome_materia','turma.nome as nome_turma','config_filial.id as id_filial','config_filial.nome as nome_filial', 'semestre.nome as nome_semestre' ,
          'ano.nome as nome_ano ','periodo_anual.nome as nome_periodo_anual')
          ->where('ex_tentativa.status',1)
     ->where('ex_examen.oferta_disciplina_id',$oferta->oferta_disciplina_id )
        ->where('ex_tentativa.oferta_disciplina_id',$oferta->oferta_disciplina_id )
   
              
               ->orderBy('usuarios.sobrenome','asc')
               ->orderBy('ex_tentativa.id','desc')
               
               
            //   ->groupBy('ex_examen.id')
          ->get();
          
            

            //   return response()->json($data, 200);  
             $dado_usuarios=[];
                
          foreach($data as $dadosusu){
              $dado_usuarios[]= ['id'=> $dadosusu->usuarios_id,
              'nome' => $dadosusu->nome_usuarios,
              'doc_oficial' => $dadosusu->doc_oficial
              ]
              ;
              
          }
        //   $newarray='';
             if($dado_usuarios==[]) {
             
                }else{
          $dados_usuarios2 = array_unique($dado_usuarios,SORT_REGULAR);
           $notatotal=0;
           $tope=0;
            
               foreach($dados_usuarios2 as $usu){
                   if($usu['id']!= 764 and $usu['id']!= 2550 and $usu['id']!= 658){
                   $newarray=[];
                   $not=[];
                   $not= ['finalexamen'=>0,'parcial1'=>0,'parcial2'=>0,'processo1'=>0,'processo2'=>0,'complementarexamen'=>0,'parcial1_recuperatoria'=>0,'parcial2_recuperatoria'=>0 ];
          foreach($data as $elemen){
              if($elemen->usuarios_id == $usu['id']){
                  if($elemen->tipo_examen_id  ==1 ){
                      $nome ='processo1';
                  }
                     if($elemen->tipo_examen_id  ==2 ){
                      $nome ='parcial1';
                  }
                       if($elemen->tipo_examen_id  ==3 ){
                      $nome ='parcial1_recuperatoria';
                  }
                     if($elemen->tipo_examen_id  ==4 ){
                      $nome ='processo2';
                  }
                      if($elemen->tipo_examen_id  ==5 ){
                      $nome ='parcial2';
                  }
                       if($elemen->tipo_examen_id  ==6 ){
                      $nome ='parcial2_recuperatoria';
                  }
                       if($elemen->tipo_examen_id == 7 ){
                      $nome ='finalexamen';
                  }
                        if($elemen->tipo_examen_id == 8 ){
                      $nome ='complementarexamen';
                  }
                     if($elemen->tipo_examen_id == 9 ){
                      $nome ='extraordinaria';
                      
                  }
                    if(!isset($newarray2[$nome])){
           
                  $not[$nome]=   $elemen->nota;
              $newarray2[$nome] =  (object) [
                  'nome_avaliacao'=> $elemen->nome_tipo_examen,
                  $nome => $elemen->nota ,
                  'tipo_examen_id' => $elemen->tipo_examen_id,
                   "nome"=>$nome
                  ];
           
             
              }
              
          
              }   
              
          
              
          }  
          
        //   return $newarray;
        //   $newarray1=[];
             $newarray3=0;
          foreach($newarray2 as $cr){
              if($cr->tipo_examen_id != 9){
              $nome =$cr->nome;
$newarray[] = $cr;
 $notatotal+=  $cr->$nome;
  $tope +=  $cr->$nome;
 
              }
                if($cr->tipo_examen_id == 9){
                    // $newarray[] = $cr;
                        //   $nome =$cr->nome;
                         $notatotal = $cr->extraordinaria;
$newarray3 = $cr->extraordinaria;
                }
        
          }   
          $newarray2=[];
        
              
          
        //   if(isset($not['complementarexamen'])){
        //   return $not;
        //   }
            
               $tope = $tope - $not['finalexamen'];
                $tope = $tope - $not['complementarexamen'];
            if($not['complementarexamen'] > $not['finalexamen'] ){      
         
                   $notatotal= $notatotal - $not['finalexamen'] ;
                 
              }else{
                   $notatotal= $notatotal - $not['complementarexamen'] ;
                    
              }

          if($not['parcial1_recuperatoria'] > $not['parcial1'] ){        
           
                   $notatotal= $notatotal - $not['parcial1'] ;
                     $tope = $tope - $not['parcial1'];
              }else{
                   $notatotal= $notatotal - $not['parcial1_recuperatoria'] ;
                     $tope = $tope - $not['parcial1_recuperatoria'];
              }

                if($not['parcial2_recuperatoria']> $not['parcial2'] ){        
           
                   $notatotal= $notatotal - $not['parcial2'] ;
                       $tope = $tope - $not['parcial2'];
              }
              else{
                   $notatotal= $notatotal - $not['parcial2_recuperatoria'] ;
                      $tope = $tope - $not['parcial2_recuperatoria'];
              }
              if($notatotal >100){
                  $notatotal = 100;
              }  
            
             if($notatotal != 0){
          
                    $dados_notas[] = (object)[
             "nome_usuario"=>  $usu['nome'],
             "usuarios_id"=>  $usu['id'],
             'doc_oficial'=> $usu['doc_oficial'],
              'avaliacao' => $newarray,
              'extraordinaria'=>$newarray3,
               'notatotal' => $notatotal,
               'tope'=>$tope,
            //  "id_disciplina" => $arquivo['id_disciplina'] 
           ];  
             }
          
           $newarray=[];
           $newarray3=0;
           
             $notatotal=0;
             $tope=0;
               }
               }
                   
               
               $nome='';
                     $fecha_inicio= ['finalexamen'=>null,'parcial1'=>null,'parcial2'=>null,'processo1'=>null,'processo2'=>null,'complementarexamen'=>null,'parcial1_recuperatoria'=>null,'parcial2_recuperatoria'=>null,'extraordinaria'=>null ];
            foreach($data as $element){
           
              $nome_professor = $element->nome_funcionario; 
              $nome_materia = $element->nome_materia;
              $nome_turma = $element->nome_turma;
              if($element->id_filial == 2){
                    $nome_filial = 'Filial Pedro Juan Caballero';
                   
              }else{
                    $nome_filial = 'Filial Ciudad del Este';
              }
           
              $nome_semestre = $element->nome_semestre;
                $nome_ano = $element->nome_ano;
                  $nome_periodo_anual = $element->nome_periodo_anual;
            $usuarios_id = $element->usuarios_id;
              
                    if($element->tipo_examen_id  ==1 ){
                      $nome ='processo1';
                  }
                     if($element->tipo_examen_id  ==2 ){
                      $nome ='parcial1';
                  }
                       if($element->tipo_examen_id  ==3 ){
                      $nome ='parcial1_recuperatoria';
                  }
                     if($element->tipo_examen_id  ==4 ){
                      $nome ='processo2';
                  }
                      if($element->tipo_examen_id  ==5 ){
                      $nome ='parcial2';
                  }
                      if($element->tipo_examen_id  ==6 ){
                      $nome ='parcial2_recuperatoria';
                  }
                        if($element->tipo_examen_id == 7 ){
                      $nome ='finalexamen';
                  }
                       if($element->tipo_examen_id == 8 ){
                      $nome ='complementarexamen';
                  }
                       if($element->tipo_examen_id == 9 ){
                      $nome ='extraordinaria';
                  }
                      $fecha_inicio[$nome] = 
               date('d/m/Y' ,strtotime($element->fecha_inicio));
              
            }
              $notasTrabalhos = $this->listar_notas_trabalhosPraticos($oferta->oferta_disciplina_id);
                // return response()->json($fecha_incio, 200);  
            $data2[$oferta->oferta_disciplina_id] = (object) ['fecha_inicio'=> (object)$fecha_inicio,
            'dados_notas' =>$dados_notas,
            'nome_professor' => $nome_professor,
            'nome_materia' =>$this->tirarAcentos($nome_materia),
            'nome_turma' => $nome_turma,
            'nome_filial' => $nome_filial,
            'nome_semestre' => $nome_semestre,
            'nome_ano' => $nome_ano,
            'nome_periodo_anual' => $nome_periodo_anual,
            'usuario_id' => $usuarios_id,
             'dataTrabalho'=>$notasTrabalhos->dataTrabalho];
          
          
       
       
         foreach($notasTrabalhos->todosTrabalhosAlunos as $notTrab){
            
             foreach($data2[$oferta->oferta_disciplina_id]->dados_notas as $notExamen){
                 
                    
                 if($notTrab->usuario_id == $notExamen->usuarios_id){
                    
                     $notExamen->notatotal=   $notExamen->notatotal + $notTrab->somatotal;
                        $notExamen->tope=   $notExamen->tope + $notTrab->somatotal;
                    //  if($notExamen->notatotal == 59){
                    //         $notExamen->notatotal= 60;
                    //  }
                     $notExamen->somaTrabalho= $notTrab->somatotal;
                 }
             }
         }
            
         
              $pontos_extras = DB::connection('mysql2')->table('ex_ponto_extra')
        ->where('ex_ponto_extra.oferta_disciplina_id',$oferta->oferta_disciplina_id)
       
        ->get();   
        // return $pontos_extras;
        $usu_pont=[];
            foreach($pontos_extras as $pont_ext){
         
            $usu_pont[$pont_ext->usuarios_id]=  ['ponte'=>$pont_ext->usuarios_id];
            
        }
  
        
            foreach($pontos_extras as $pont_exte){
            
             foreach($data2[$oferta->oferta_disciplina_id]->dados_notas as $notExamen){
                 
                    
                 if($pont_exte->usuarios_id == $notExamen->usuarios_id){
                      $notExamen->ponto_extra = count($usu_pont[$pont_exte->usuarios_id]) ;
                     $notExamen->notatotal=   $notExamen->notatotal +   $notExamen->ponto_extra;
                     if($notExamen->notatotal > 100){
                   $notExamen->notatotal = 100;
                     }
                 }
             }
         }
            foreach($data2[$oferta->oferta_disciplina_id]->dados_notas as $notExamen){
                 
                    
                    
                     if($notExamen->notatotal == 59){
                            $notExamen->notatotal= 60;
                     }
                   
                 }
         
         
        
         $notasTrabalhos=[];
         $dados_notas=[];
         $newarray=[];
         $dado_usuarios=[];
         $dados_usuarios2=[];
         
      }
      }
      $tudo = (object)['nome_turmatotal'=>$this->tirarAcentos($nome_turmatotal),'tudonota'=>$data2]; 
      
      
         return response()->json($tudo, 200);     
                
          
    }
    public  function lista_notas_disciplina2($oferta_disciplina_id)
    {
        $nome='';
        
        // $data  = $this->listar_notas_trabalhosPraticos($oferta_disciplina_id);
        // return $data;
    //  $ofertasdaturma =  $this->AllMateriasTurma($id_turma);  
     
    //  return $ofertasdaturma;
  
         
//   $oferta_disciplina_id = $oferta->oferta_disciplina_id;
   
//   $usu_naofez = [];
$fecha_inicio=[];
        $data = $this->tentativa
            ->join('usuarios','usuarios.id','ex_tentativa.usuarios_id')
            ->join('ex_examen','ex_examen.id','ex_tentativa.ex_examen_id')
            ->join('ex_tipo_examen','ex_tipo_examen.id','ex_examen.TIPO_EXAMEN_ID')
            ->join('oferta_disciplina','oferta_disciplina.id','=','ex_tentativa.oferta_disciplina_id')
            ->join('turma','turma.id','=','oferta_disciplina.turma_id')
            ->join('disciplinas','disciplinas.id','=','oferta_disciplina.disciplinas_id')
            ->join('config_filial','config_filial.id','oferta_disciplina.config_filial_id')
            ->join('semestre', 'semestre.id', 'oferta_disciplina.semestre_id')
            ->join('periodo_letivo', 'periodo_letivo.id', 'ex_tentativa.periodo_letivo_id')   
            ->leftjoin('periodo_anual','periodo_anual.id','periodo_letivo.periodo_anual_id')
            ->join('ano', 'ano.id', 'periodo_letivo.ano_id')  
            ->join('funcionario', 'funcionario.id','oferta_disciplina.funcionario_id')
          ->select('ex_tipo_examen.nome as nome_tipo_examen', DB::raw('CONCAT(usuarios.sobrenome,\', \', usuarios.nome) as nome_usuarios'),
          DB::raw('CONCAT(funcionario.nome,\' \', funcionario.sobrenome) as nome_funcionario'),
          'ex_tentativa.nota', 'usuarios.id as usuarios_id','ex_tipo_examen.id as tipo_examen_id','usuarios.doc_oficial','ex_examen.FECHA_INICIO AS fecha_inicio',
          'disciplinas.nome as nome_materia','turma.nome as nome_turma','config_filial.id as id_filial','config_filial.nome as nome_filial', 'semestre.nome as nome_semestre' ,
          'ano.nome as nome_ano ','periodo_anual.nome as nome_periodo_anual')
        //   ->where('ex_examen.STATUS',1)
        // ->where('oferta_disciplina.status',1)
        ->where('oferta_disciplina.status',1)
        ->where('ex_tentativa.status',1)
        ->where('ex_examen.status',1)
        ->where('ex_examen.oferta_disciplina_id',$oferta_disciplina_id)
        ->where('ex_tentativa.oferta_disciplina_id',$oferta_disciplina_id)
            //   ->orderBy('ex_tentativa.id','desc')
               ->orderBy('usuarios.sobrenome','asc')
               
               
            //   ->groupBy('ex_examen.id')
          ->get();
          
            

      

            //   return response()->json($data, 200);  
             $dado_usuarios=[];
                
          foreach($data as $dadosusu){
              $dado_usuarios[]= ['id'=> $dadosusu->usuarios_id,
              'nome' => ucfirst(trim($dadosusu->nome_usuarios)),
              'doc_oficial' => $dadosusu->doc_oficial
              ]
              ;
              
          }
        //   $newarray='';
             if($dado_usuarios==[]) {
             
                }else{
          $dados_usuarios2 = array_unique($dado_usuarios,SORT_REGULAR);
           $notatotal=0;
           
            
               foreach($dados_usuarios2 as $usu){
                       if($usu['id']!= 764 and $usu['id']!= 2550 and $usu['id']!= 658){
                   $newarray=[];
                   $not=[];
                   $not= ['finalexamen'=>0,'parcial1'=>0,'parcial2'=>0,'processo1'=>0,'processo2'=>0,'complementarexamen'=>0,'parcial1_recuperatoria'=>0,'parcial2_recuperatoria'=>0 ];
               foreach($data as $elemen){
              if($elemen->usuarios_id == $usu['id']){
                  if($elemen->tipo_examen_id  ==1 ){
                      $nome ='processo1';
                  }
                     if($elemen->tipo_examen_id  ==2 ){
                      $nome ='parcial1';
                  }
                       if($elemen->tipo_examen_id  ==3 ){
                      $nome ='parcial1_recuperatoria';
                  }
                     if($elemen->tipo_examen_id  ==4 ){
                      $nome ='processo2';
                  }
                      if($elemen->tipo_examen_id  ==5 ){
                      $nome ='parcial2';
                  }
                       if($elemen->tipo_examen_id  ==6 ){
                      $nome ='parcial2_recuperatoria';
                  }
                       if($elemen->tipo_examen_id == 7 ){
                      $nome ='finalexamen';
                  }
                        if($elemen->tipo_examen_id == 8 ){
                      $nome ='complementarexamen';
                  }
                  $not[$nome]=   $elemen->nota;
              $newarray[] =  (object) [
                  'nome_avaliacao'=> $elemen->nome_tipo_examen,
                  $nome => $elemen->nota ,
                  'tipo_examen_id' => $elemen->tipo_examen_id,
        
                  ];
              
           $notatotal+=$elemen->nota;
             
           
              }
              
          
              
          }  
        //   foreach($newarray as $it){
        //       if(isset($it->parcial1)){
        //   if($it->parcial1 == 0){
        //       $usu_naofez[$usu['id']]->parcial1 = true;
        //   }
        //       }
        //       if(isset($it->processo1)){
        //   if($it->processo1 == 0){
        //       $usu_naofez[$usu['id']]->processo1 = true;
        //   }
        //   }
        //   }
          
   
          
        //   if(isset($not['complementarexamen'])){
        //   return $not;
        //   }
            
         if($not['complementarexamen'] > $not['finalexamen'] ){      
         
                   $notatotal= $notatotal - $not['finalexamen'] ;
              }else{
                   $notatotal= $notatotal - $not['complementarexamen'] ;
              }

          if($not['parcial1_recuperatoria'] > $not['parcial1'] ){        
           
                   $notatotal= $notatotal - $not['parcial1'] ;
              }else{
                   $notatotal= $notatotal - $not['parcial1_recuperatoria'] ;
              }

                if($not['parcial2_recuperatoria']> $not['parcial2'] ){        
           
                   $notatotal= $notatotal - $not['parcial2'] ;
              }
              else{
                   $notatotal= $notatotal - $not['parcial2_recuperatoria'] ;
              }
              if($notatotal >100){
                  $notatotal = 100;
              }  
        
               if($notatotal != 0){
          
                    $dados_notas[] = (object)[
             "nome_usuario"=>  $usu['nome'],
             "usuarios_id"=>  $usu['id'],
             'doc_oficial'=> $usu['doc_oficial'],
              'avaliacao' => $newarray,
               'notatotal' => $notatotal
            //  "id_disciplina" => $arquivo['id_disciplina'] 
           ];  
               }
          
           $newarray=[];
           
             $notatotal=0;
              
               }
                   
               }
               $nome='';
                     $fecha_inicio= ['finalexamen'=>null,'parcial1'=>null,'parcial2'=>null,'processo1'=>null,'processo2'=>null,'complementarexamen'=>null,'parcial1_recuperatoria'=>null,'parcial2_recuperatoria'=>null,'extraordinaria'=>null ];
            foreach($data as $element){
           
              $nome_professor = ucfirst(trim($element->nome_funcionario)); 
              $nome_materia = $element->nome_materia;
              $nome_turma = $element->nome_turma;
              if($element->id_filial == 2){
                    $nome_filial = 'Filial Pedro Juan Caballero';
                   
              }else{
                    $nome_filial = 'Filial Ciudad del Este';
              }
           
              $nome_semestre = $element->nome_semestre;
                $nome_ano = $element->nome_ano;
                  $nome_periodo_anual = $element->nome_periodo_anual;
            $usuarios_id = $element->usuarios_id;
              
                    if($element->tipo_examen_id  ==1 ){
                      $nome ='processo1';
                  }
                     if($element->tipo_examen_id  ==2 ){
                      $nome ='parcial1';
                  }
                       if($element->tipo_examen_id  ==3 ){
                      $nome ='parcial1_recuperatoria';
                  }
                     if($element->tipo_examen_id  ==4 ){
                      $nome ='processo2';
                  }
                      if($element->tipo_examen_id  ==5 ){
                      $nome ='parcial2';
                  }
                      if($element->tipo_examen_id  ==6 ){
                      $nome ='parcial2_recuperatoria';
                  }
                        if($element->tipo_examen_id == 7 ){
                      $nome ='finalexamen';
                  }
                       if($element->tipo_examen_id == 8 ){
                      $nome ='complementarexamen';
                  }
                       if($element->tipo_examen_id == 9 ){
                      $nome ='extraordinaria';
                  }
                      $fecha_inicio[$nome] = 
               date('d/m/Y' ,strtotime($element->fecha_inicio));
              
            }
                }
                
                $notasTrabalhos = $this->listar_notas_trabalhosPraticos($oferta_disciplina_id);
                // return response()->json($fecha_incio, 200);  
            $data2 = (object) ['fecha_inicio'=> (object)$fecha_inicio,
            'dados_notas' =>$dados_notas,
            'nome_professor' => $nome_professor,
            'nome_materia' =>$this->tirarAcentos($nome_materia),
            'nome_turma' => $nome_turma,
            'nome_filial' => $nome_filial,
            'nome_semestre' => $nome_semestre,
            'nome_ano' => $nome_ano,
            'nome_periodo_anual' => $nome_periodo_anual,
            'usuario_id' => $usuarios_id,
            'dataTrabalho'=>$notasTrabalhos->dataTrabalho];
          
          
     
       
         foreach($notasTrabalhos->todosTrabalhosAlunos as $notTrab){
            
             foreach($data2->dados_notas as $notExamen){
                 
                    
                 if($notTrab->usuario_id == $notExamen->usuarios_id){
                      $notExamen->notatotal=   $notExamen->notatotal + $notTrab->somatotal;
                     $notExamen->somaTrabalho= $notTrab->somatotal;
                    
                 }
             }
         }
            foreach($data2->dados_notas as $notExamen){
                 
                    
                    
                     if($notExamen->notatotal == 59){
                            $notExamen->notatotal= 60;
                     }
                   
                 }
         
              $pontos_extras = DB::connection('mysql2')->table('ex_ponto_extra')
        ->where('ex_ponto_extra.oferta_disciplina_id',$oferta_disciplina_id )
       
        ->get();   
        // return $pontos_extras;
        $usu_pont=[];
            foreach($pontos_extras as $pont_ext){
         
            $usu_pont[$pont_ext->usuarios_id]=  ['ponte'=>$pont_ext->usuarios_id];
        }
  
        
            foreach($pontos_extras as $pont_exte){
            
             foreach($data2->dados_notas as $notExamen){
                 
                    
                 if($pont_exte->usuarios_id == $notExamen->usuarios_id){
                      $notExamen->ponto_extra = count($usu_pont[$pont_exte->usuarios_id]) ;
                     $notExamen->notatotal=   $notExamen->notatotal +   $notExamen->ponto_extra;
                   
                 }
             }
         }
         
         $notasTrabalhos=[];
         $dados_notas=[];
         $newarray=[];
         $dado_usuarios=[];
         $dados_usuarios2=[];
         
         
         
         
      
    //   $tudo = (object)['nome_turmatotal'=>$this->tirarAcentos($nome_turmatotal),'tudonota'=>$data2]; 
            //  return $usu_naofez;
         return response()->json($data2, 200);     
                
          
    }
    
    
    
    public function alunoNotas(Request $request)
    {
        
   
        
          $usuario_id = $this->pegar_id($request);
           $ofertasdaturma = $this->usuarios->listamateriasAluno($usuario_id['sub']);
       
    

             foreach($ofertasdaturma as $oferta){
                 
        $nome_turmatotal= $oferta->nome_semestre.' '.$oferta->nome_turma;
   //   $oferta_disciplina_id = $oferta->oferta_disciplina_id;
        $pontos_extras = DB::connection('mysql2')->table('ex_ponto_extra')
        ->where('ex_ponto_extra.oferta_disciplina_id',$oferta->oferta_disciplina_id )
        ->where('ex_ponto_extra.usuarios_id',$usuario_id['sub'])
        ->first();   
      
        $c = $this->tentativa
            ->join('usuarios','usuarios.id','ex_tentativa.usuarios_id')
            ->join('ex_examen','ex_examen.id','ex_tentativa.ex_examen_id')
            ->join('ex_tipo_examen','ex_tipo_examen.id','ex_examen.TIPO_EXAMEN_ID')
            ->join('oferta_disciplina','oferta_disciplina.id','=','ex_tentativa.oferta_disciplina_id')
            ->join('turma','turma.id','=','oferta_disciplina.turma_id')
       
            ->join('disciplinas','disciplinas.id','=','oferta_disciplina.disciplinas_id')
            ->join('config_filial','config_filial.id','oferta_disciplina.config_filial_id')
            ->join('semestre', 'semestre.id', 'oferta_disciplina.semestre_id')
            ->join('periodo_letivo', 'periodo_letivo.id', 'ex_tentativa.periodo_letivo_id')   
            ->join('periodo_anual','periodo_anual.id','periodo_letivo.periodo_anual_id')
            ->join('ano', 'ano.id', 'periodo_letivo.ano_id')  
            ->join('funcionario', 'funcionario.id','oferta_disciplina.funcionario_id')
          ->select('ex_tipo_examen.nome as nome_tipo_examen', DB::raw('CONCAT(usuarios.sobrenome,\', \', usuarios.nome) as nome_usuarios'),
          DB::raw('CONCAT(funcionario.nome,\' \', funcionario.sobrenome) as nome_funcionario'),
          'ex_tentativa.nota', 'usuarios.id as usuarios_id','ex_tipo_examen.id as tipo_examen_id','usuarios.doc_oficial','ex_examen.FECHA_INICIO AS fecha_inicio',
          'disciplinas.nome as nome_materia','turma.nome as nome_turma','config_filial.id as id_filial','config_filial.nome as nome_filial', 'semestre.nome as nome_semestre' ,
          'ano.nome as nome_ano ','periodo_anual.nome as nome_periodo_anual','ex_tentativa.oferta_disciplina_id')
          ->where('ex_tentativa.usuarios_id',$usuario_id['sub'])
          //->where('ex_examen.oferta_disciplina_id',$oferta->oferta_disciplina_id )
           ->where('ex_tentativa.oferta_disciplina_id',$oferta->oferta_disciplina_id )
            ->orderBy('usuarios.sobrenome','asc')
               
          ->get();
          
          if($c){
              $data = $c;
              
          }
      

        
        $dado_usuarios=[];
                
          foreach($data as $dadosusu){
              $dado_usuarios[]= [
                    'id'=> $dadosusu->usuarios_id,
                    'nome' => $dadosusu->nome_usuarios,
                    'doc_oficial' => $dadosusu->doc_oficial
                ];
              
          }
          
        //   $newarray='';
             if($dado_usuarios==[]) {
              $data2[]= 'não há avaliações';
                }else{
                    
          $dados_usuarios2 = array_unique($dado_usuarios,SORT_REGULAR);
           $notatotal=0;
          
        
            foreach($dados_usuarios2 as $usu){
                   $newarray=[];
                   $not=[];
                    foreach($data as $elemen){
              if($elemen->usuarios_id == $usu['id']){
                  if($elemen->tipo_examen_id  ==1 ){
                      $nome ='processo1';
                  }
                     if($elemen->tipo_examen_id  ==2 ){
                      $nome ='parcial1';
                  }
                       if($elemen->tipo_examen_id  ==3 ){
                      $nome ='parcial1_recuperatoria';
                  }
                     if($elemen->tipo_examen_id  ==4 ){
                      $nome ='processo2';
                  }
                      if($elemen->tipo_examen_id  ==5 ){
                      $nome ='parcial2';
                  }
                       if($elemen->tipo_examen_id  ==6 ){
                      $nome ='parcial2_recuperatoria';
                  }
                       if($elemen->tipo_examen_id == 7 ){
                      $nome ='finalexamen';
                  }
                        if($elemen->tipo_examen_id == 8 ){
                      $nome ='complementarexamen';
                  }
                      if($elemen->tipo_examen_id == 9 ){
                      $nome ='extraordinaria';
                  }
                 
            
                  
                  $not[$nome]=   $elemen->nota;
              $newarray[] =  (object) [
                  'nome_avaliacao'=> $elemen->nome_tipo_examen,
                  'nota' => $elemen->nota ,
                  'nome'=>$nome,
                  'tipo_examen_id' => $elemen->tipo_examen_id,
            
                  ];
              
           $notatotal+=$elemen->nota;
              
          
                      
              }
          
              
          }  
          
        //   if(isset($not['complementarexamen'])){
        //   return $not;
        //   }
       
          
                $dados_notas[] = (object)[
        
                    "usuarios_id"=>  $usu['id'],
                    'avaliacao' => $newarray
              
            //  "id_disciplina" => $arquivo['id_disciplina'] 
                ];  
  
                    $newarray=[];
                    $notatotal=0;
              
               }
                   
               
            $nome='';
               
            foreach($data as $element){
           
              $nome_professor = $element->nome_funcionario; 
              $nome_materia = $element->nome_materia;
              $nome_turma = $element->nome_turma;
              
              if($element->id_filial == 2){
                    $nome_filial = 'Filial Pedro Juan Caballero';
                   
              }else{
                    $nome_filial = 'Filial Ciudad del Este';
              }
           
            $nome_semestre = $element->nome_semestre;
            $nome_ano = $element->nome_ano;
            $nome_periodo_anual = $element->nome_periodo_anual;
            $usuarios_id = $element->usuarios_id;
              
                    if($element->tipo_examen_id  ==1 ){
                      $nome ='processo1';
                  }
                     if($element->tipo_examen_id  ==2 ){
                      $nome ='parcial1';
                  }
                       if($element->tipo_examen_id  ==3 ){
                      $nome ='parcial1_recuperatoria';
                  }
                     if($element->tipo_examen_id  ==4 ){
                      $nome ='processo2';
                  }
                      if($element->tipo_examen_id  ==5 ){
                      $nome ='parcial2';
                  }
                      if($element->tipo_examen_id  ==6 ){
                      $nome ='parcial2_recuperatoria';
                  }
                        if($element->tipo_examen_id == 7 ){
                      $nome ='finalexamen';
                  }
                       if($element->tipo_examen_id == 8 ){
                      $nome ='complementarexamen';
                  }
                  
                      $fecha_inicio[$nome] = 
               date('d/m/Y' ,strtotime($element->fecha_inicio));
              
            }
          if($pontos_extras == ''){
              $pontos_extras =0;
          }else{
              $pontos_extras =1;
          }
          
              $notasTrabalhos = $this->listar_notas_trabalhosPraticos($oferta->oferta_disciplina_id,$usuario_id['sub']);
                // return response()->json($fecha_incio, 200);  
            $data2[$oferta->oferta_disciplina_id] = (object) ['fecha_inicio'=> (object)$fecha_inicio,
            'dados_notas' =>$dados_notas,
            'nome_professor' => $nome_professor,
            'nome_materia' =>$this->tirarAcentos($nome_materia).'- '.$nome_semestre.' '.$nome_turma,
            'nome_filial' => $nome_filial,
            'nome_ano' => $nome_ano,
            'nome_periodo_anual' => $nome_periodo_anual,
            'usuario_id' => $usuarios_id,
            'pontos_extras'=>$pontos_extras,
             'dataTrabalho'=>$notasTrabalhos->dataTrabalho
             ];
       
       
         foreach($notasTrabalhos->todosTrabalhosAlunos as $notTrab){
            
             foreach($data2[$oferta->oferta_disciplina_id]->dados_notas as $notExamen){
                 
                    
                 if($notTrab->usuario_id == $notExamen->usuarios_id){
                    
                    //  $notExamen->notatotal=   $notExamen->notatotal + $notTrab->somatotal;
                     $notExamen->somaTrabalho= $notTrab->somatotal;
                 }
             }
             
         }
         $notasTrabalhos=[];
         $dados_notas=[];
         $newarray=[];
         $dado_usuarios=[];
         $dados_usuarios2=[];
    
                      
                }
      }
      
      //return $data;
      
      
       $final=(object)['finalexamen'=>0,'parcial1'=>0,'parcial2'=>0,'processo1'=>0,'processo2'=>0, 'trabalho'=>0,'extraordinaria'=>0,'pontos_extras'=>0 ];
         foreach($data2 as $end){
             if($end != 'não há avaliações'){
                 
             
      foreach($end->dados_notas as $end2){
          
        foreach($end2->avaliacao as $end3){
           
    
         $nome = $end3->nome;
         
               
                   if(isset($final->complementarexamen) and isset($final->finalexamen) ){        
            if($final->complementarexamen > $final->finalexamen){      
         
                   $final->finalexamen =  $end3->nota;
              }
          }else
          
            if(isset($final->parcial1_recuperatoria) and isset($final->parcial1) ){       
       
          if($final->parcial1_recuperatoria > $final->parcial1 ){        
           
                   $final->parcial1 = $end3->nota;
              }
            }else
          
             if(isset($final->parcial2_recuperatoria) and isset($final->parcial2) ){    
                if($final->parcial2_recuperatoria> $final->parcial2 ){        
           
                   $final->parcial2 = $end3->nota;
              }
             }
          
             if($nome != 'parcial2_recuperatoria' and $nome != 'parcial1_recuperatoria' and $nome != 'complementarexamen'  )
               $final->$nome = $end3->nota;
           }
  
               
                 
            $final->pontos_extras = $end->pontos_extras;
               
                
       
       if(!isset($end2->somaTrabalho)){
       $final->trabalho =0 ;
       }else{
           $final->trabalho =$end2->somaTrabalho ;  
       }
       
        $final->puntoslogrados = $final->parcial1 +$final->processo1 + $final->processo2 + $final->trabalho + $final->parcial2 + $final->finalexamen;
       
               $final2[]= ['materia'=>$end->nome_materia,'notas'=>$final,'fechas'=>$end->fecha_inicio ];
                  $final= (object)['finalexamen'=>0,'parcial1'=>0,'parcial2'=>0,'processo1'=>0,'processo2'=>0, 'trabalho'=>0,'extraordinaria'=>0,'pontos_extra'=>0 ];
      }
        
         }
else{
             $final2= 'não há avaliações';
             
         }
         } 
   
          $tudo = (object)['tudonota'=>$data2]; 
      
        
          
         return response()->json($final2, 200);     
                
          
    
                      
    }
    
    
    public function boletim(Request $request)
    {
         
          $usuario_id = $this->pegar_id($request);
          $ofertasdaturma = $this->usuarios->listamateriasAluno($usuario_id['sub']);
                
        

             foreach($ofertasdaturma as $oferta){
          $nome_turmatotal= $oferta->nome_semestre.' '.$oferta->nome_turma;
//   $oferta_disciplina_id = $oferta->oferta_disciplina_id;
   
        $data = $this->tentativa
            ->join('usuarios','usuarios.id','ex_tentativa.usuarios_id')
            ->join('ex_examen','ex_examen.id','ex_tentativa.ex_examen_id')
            ->join('ex_tipo_examen','ex_tipo_examen.id','ex_examen.TIPO_EXAMEN_ID')
            ->join('oferta_disciplina','oferta_disciplina.id','=','ex_tentativa.oferta_disciplina_id')
            ->join('turma','turma.id','=','oferta_disciplina.turma_id')
            ->join('disciplinas','disciplinas.id','=','oferta_disciplina.disciplinas_id')
            ->join('config_filial','config_filial.id','oferta_disciplina.config_filial_id')
            ->join('semestre', 'semestre.id', 'oferta_disciplina.semestre_id')
            ->join('periodo_letivo', 'periodo_letivo.id', 'ex_tentativa.periodo_letivo_id')   
            ->join('periodo_anual','periodo_anual.id','periodo_letivo.periodo_anual_id')
            ->join('ano', 'ano.id', 'periodo_letivo.ano_id')  
            ->join('funcionario', 'funcionario.id','oferta_disciplina.funcionario_id')
          ->select('disciplinas.carga_horaria','ex_tipo_examen.nome as nome_tipo_examen', DB::raw('CONCAT(usuarios.sobrenome,\', \', usuarios.nome) as nome_usuarios'),
          DB::raw('CONCAT(funcionario.nome,\' \', funcionario.sobrenome) as nome_funcionario'),
          'ex_tentativa.nota', 'usuarios.id as usuarios_id','ex_tipo_examen.id as tipo_examen_id','usuarios.doc_oficial','ex_examen.FECHA_INICIO AS fecha_inicio',
          'disciplinas.nome as nome_materia','turma.nome as nome_turma','config_filial.id as id_filial','config_filial.nome as nome_filial', 'semestre.nome as nome_semestre' ,
          'ano.nome as nome_ano ','periodo_anual.nome as nome_periodo_anual')
          ->where('ex_tentativa.usuarios_id',$usuario_id['sub'])
     ->where('ex_examen.oferta_disciplina_id',$oferta->oferta_disciplina_id )
        ->where('ex_tentativa.oferta_disciplina_id',$oferta->oferta_disciplina_id )
            //   ->orderBy('ex_tentativa.id','desc')
               ->orderBy('usuarios.sobrenome','asc')
               
               
                      //   ->groupBy('ex_examen.id')
          ->get();
          
            

            //   return response()->json($data, 200);  
             $dado_usuarios=[];
                
          foreach($data as $dadosusu){
              $dado_usuarios[]= ['id'=> $dadosusu->usuarios_id,
              'nome' => $dadosusu->nome_usuarios,
              'doc_oficial' => $dadosusu->doc_oficial
              ]
              ;
              
          }
        //   $newarray='';
             if($dado_usuarios==[]) {
             
                }else{
          $dados_usuarios2 = array_unique($dado_usuarios,SORT_REGULAR);
           $notatotal=0;
            
               foreach($dados_usuarios2 as $usu){
                   $newarray=[];
                   $not=[];
                   $not= ['finalexamen'=>0,'parcial1'=>0,'parcial2'=>0,'processo1'=>0,'processo2'=>0,'complementarexamen'=>0,'parcial1_recuperatoria'=>0,'parcial2_recuperatoria'=>0 ];
          foreach($data as $elemen){
              if($elemen->usuarios_id == $usu['id']){
                  if($elemen->tipo_examen_id  ==1 ){
                      $nome ='processo1';
                  }
                     if($elemen->tipo_examen_id  ==2 ){
                      $nome ='parcial1';
                  }
                       if($elemen->tipo_examen_id  ==3 ){
                      $nome ='parcial1_recuperatoria';
                  }
                     if($elemen->tipo_examen_id  ==4 ){
                      $nome ='processo2';
                  }
                      if($elemen->tipo_examen_id  ==5 ){
                      $nome ='parcial2';
                  }
                       if($elemen->tipo_examen_id  ==6 ){
                      $nome ='parcial2_recuperatoria';
                  }
                       if($elemen->tipo_examen_id == 7 ){
                      $nome ='finalexamen';
                  }
                        if($elemen->tipo_examen_id == 8 ){
                      $nome ='complementarexamen';
                  }
                       if($elemen->tipo_examen_id == 9 ){
                      $nome ='extraordinaria';
                  }
                  $not[$nome]=   $elemen->nota;
              $newarray[] =  (object) [
                  'nome_avaliacao'=> $elemen->nome_tipo_examen,
                  $nome => $elemen->nota ,
                  'tipo_examen_id' => $elemen->tipo_examen_id,
            
                  ];
              
           $notatotal+=$elemen->nota;
              
          
                      
              }
          
              
          }  
          
        //   if(isset($not['complementarexamen'])){
        //   return $not;
        //   }
          if(isset($not['complementarexamen']) and isset($not['finalexamen']) ){        
            if($not['complementarexamen'] > $not['finalexamen'] ){      
         
                   $notatotal= $notatotal - $not['finalexamen'] ;
              }
          }
          
            if(isset($not['parcial1_recuperatoria']) and isset($not['parcial1']) ){       
       
          if($not['parcial1_recuperatoria'] > $not['parcial1'] ){        
           
                   $notatotal= $notatotal - $not['parcial1'] ;
              }
            }
          
             if(isset($not['parcial2_recuperatoria']) and isset($not['parcial2']) ){    
                if($not['parcial2_recuperatoria']> $not['parcial2'] ){        
           
                   $notatotal= $notatotal - $not['parcial2'] ;
              }
             }
          
                    $dados_notas[] = (object)[
             "nome_usuario"=>  $usu['nome'],
             "usuarios_id"=>  $usu['id'],
             'doc_oficial'=> $usu['doc_oficial'],
              'avaliacao' => $newarray,
               'notatotal' => $notatotal
            //  "id_disciplina" => $arquivo['id_disciplina'] 
           ];  
          
           $newarray=[];
           
             $notatotal=0;
              
               }
                   
               
               $nome='';
               
            foreach($data as $element){
           
              $nome_professor = $element->nome_funcionario; 
              $nome_materia = $element->nome_materia;
              $nome_turma = $element->nome_turma;
              $carga_horaria = $element->carga_horaria;
              if($element->id_filial == 2){
                    $nome_filial = 'Filial Pedro Juan Caballero';
                   
              }else{
                    $nome_filial = 'Filial Ciudad del Este';
              }
           
              $nome_semestre = $element->nome_semestre;
                $nome_ano = $element->nome_ano;
                  $nome_periodo_anual = $element->nome_periodo_anual;
            $usuarios_id = $element->usuarios_id;
              
                    if($element->tipo_examen_id  ==1 ){
                      $nome ='processo1';
                  }
                     if($element->tipo_examen_id  ==2 ){
                      $nome ='parcial1';
                  }
                       if($element->tipo_examen_id  ==3 ){
                      $nome ='parcial1_recuperatoria';
                  }
                     if($element->tipo_examen_id  ==4 ){
                      $nome ='processo2';
                  }
                      if($element->tipo_examen_id  ==5 ){
                      $nome ='parcial2';
                  }
                      if($element->tipo_examen_id  ==6 ){
                      $nome ='parcial2_recuperatoria';
                  }
                        if($element->tipo_examen_id == 7 ){
                      $nome ='finalexamen';
                  }
                       if($element->tipo_examen_id == 8 ){
                      $nome ='complementarexamen';
                  }
                       if($element->tipo_examen_id == 9 ){
                      $nome ='extraordinaria';
                  }
                      $fecha_inicio[$nome] = 
               date('d/m/Y' ,strtotime($element->fecha_inicio));
              
            }
              $notasTrabalhos = $this->listar_notas_trabalhosPraticos($oferta->oferta_disciplina_id);
                // return response()->json($fecha_incio, 200);  
            $data2[$oferta->oferta_disciplina_id] = (object) ['fecha_inicio'=> (object)$fecha_inicio,
            'dados_notas' =>$dados_notas,
            'nome_professor' => $nome_professor,
            'nome_materia' =>$this->tirarAcentos($nome_materia),
            'nome_turma' => $nome_turma,
            'nome_filial' => $nome_filial,
            'nome_semestre' => $nome_semestre,
            'nome_ano' => $nome_ano,
            'nome_periodo_anual' => $nome_periodo_anual,
            'carga_horaria'=>$carga_horaria,
            'usuario_id' => $usuarios_id,
             'dataTrabalho'=>$notasTrabalhos->dataTrabalho];
          
          
       
       
         foreach($notasTrabalhos->todosTrabalhosAlunos as $notTrab){
            
             foreach($data2[$oferta->oferta_disciplina_id]->dados_notas as $notExamen){
                 
                    
                 if($notTrab->usuario_id == $notExamen->usuarios_id){
                    
                     $notExamen->notatotal=   $notExamen->notatotal + $notTrab->somatotal;
                     $notExamen->somaTrabalho= $notTrab->somatotal;
                 }
             }
         }
         $notasTrabalhos=[];
         $dados_notas=[];
         $newarray=[];
         $dado_usuarios=[];
         $dados_usuarios2=[];
         
      }
      }
      
      
         foreach($data2 as $end){
      foreach($end->dados_notas as $end2){
       
           
    if($end2->notatotal >= 60){
        $status = "aprobado";
    }else{
    $status = "reprobado";
    }
      
         $final[] = ['nome_materia'=>$end->nome_materia,'puntage'=>$end2->notatotal,'horas_academicas'=>$end->carga_horaria,"status"=>$status]; 
               
        
         
          
      }
      $titulo = (object)['titulo'=>$end->nome_semestre.' '.$end->nome_turma ,'dados'=>$final]; 
            
         }
         $fichamateria = ['titulo'=>$titulo->titulo,'dados'=>$titulo->dados ];
          $usu = $this->usuarios->where('usuarios.id',$usuario_id['sub'])
       ->leftJoin('config_filial','config_filial.id','usuarios.config_filial_id')
            //   ->leftJoin('oferta_disciplina','oferta_disciplina.usuario_id','usuarios.id')
            //   ->leftJoin('semestre','semestre.id','oferta_disciplina.semestre_id')
        ->select(DB::raw('CONCAT(usuarios.sobrenome,\', \', usuarios.nome) as nome_usuarios'),'email','telefone_celular1 as contato',
        'doc_oficial as numero_documento','config_filial.nome as nome_filial')
        ->first();
        $aluno=[];
       
         $usu->carrera= 'medicina';
        
         
   $academico= ['dados_usuarios'=>$usu,'notas'=>$fichamateria];

      
         return response()->json($academico, 200);     
                
          
    
                      
    }
    
    
    
    public function alunoNotas3(Request $request)
    {
        
   
        
          $usuario_id = $this->pegar_id($request);
           $ofertasdaturma = $this->usuarios->listamateriasAluno($usuario_id['sub']);
       
    

             foreach($ofertasdaturma as $oferta){
                 
        $nome_turmatotal= $oferta->nome_semestre.' '.$oferta->nome_turma;
   //   $oferta_disciplina_id = $oferta->oferta_disciplina_id;
        $pontos_extras = DB::connection('mysql2')->table('ex_ponto_extra')
        ->where('ex_ponto_extra.oferta_disciplina_id',$oferta->oferta_disciplina_id )
        ->where('ex_ponto_extra.usuarios_id',$usuario_id['sub'])
        ->first();   
      
        $c = $this->tentativa
            ->join('usuarios','usuarios.id','ex_tentativa.usuarios_id')
            ->join('ex_examen','ex_examen.id','ex_tentativa.ex_examen_id')
            ->join('ex_tipo_examen','ex_tipo_examen.id','ex_examen.TIPO_EXAMEN_ID')
            ->join('oferta_disciplina','oferta_disciplina.id','=','ex_tentativa.oferta_disciplina_id')
            ->join('turma','turma.id','=','oferta_disciplina.turma_id')
       
            ->join('disciplinas','disciplinas.id','=','oferta_disciplina.disciplinas_id')
            ->join('config_filial','config_filial.id','oferta_disciplina.config_filial_id')
            ->join('semestre', 'semestre.id', 'oferta_disciplina.semestre_id')
            ->join('periodo_letivo', 'periodo_letivo.id', 'ex_tentativa.periodo_letivo_id')   
            ->join('periodo_anual','periodo_anual.id','periodo_letivo.periodo_anual_id')
            ->join('ano', 'ano.id', 'periodo_letivo.ano_id')  
            ->join('funcionario', 'funcionario.id','oferta_disciplina.funcionario_id')
          ->select('ex_tipo_examen.nome as nome_tipo_examen',
          'ex_tentativa.nota','ex_tipo_examen.id as tipo_examen_id','ex_tipo_examen.nome as nome_tipo_examen','ex_examen.FECHA_INICIO AS fecha_inicio',
          'ex_tentativa.oferta_disciplina_id','disciplinas.nome as nome_disciplina', 'ex_tentativa.created_at as data_finalizacao')
          ->where('ex_tentativa.usuarios_id',$usuario_id['sub'])
          //->where('ex_examen.oferta_disciplina_id',$oferta->oferta_disciplina_id )
           ->where('ex_tentativa.oferta_disciplina_id',$oferta->oferta_disciplina_id )
            ->orderBy('usuarios.sobrenome','asc')
               
          ->get();
          
          if(count($c) >= 1){
              
              $data[] = $c;
              
          }
      
      }
      
      
      foreach($data as $novo){
          
         $final = [];
          
          foreach($novo as $k => $element){
              
                  $final['disciplina'] = $element->nome_disciplina;
                  $final['fecha'] = date('d-m-Y H:i:s',strtotime($element->data_finalizacao));
                  
                    if($element->tipo_examen_id  == 1 ){
                      $final['processo1'] = $element->nota;
                    }else{
                       $final['processo1'] = '';
                    }
                  
                  
                    if($element->tipo_examen_id  == 2 ){
                      $final['parcial1'] = $element->nota;
                    }else{
                       $final['parcial1'] = '';
                    }
                  
                    if($element->tipo_examen_id  ==3 ){
                      
                     $final['parcial1_recuperatoria'] = $element->nota;
                    }else{
                       $final['parcial1_recuperatoria'] = '';
                    }
                    
                    
                    if($element->tipo_examen_id  ==4 ){
                      $nome ='processo2';
                    }else{
                       $final['processo2'] = '';
                    }
                    
                    if($element->tipo_examen_id  ==5 ){
                      $nome ='parcial2';
                    }else{
                       $final['parcial2'] = '';
                    }
                    
                    if($element->tipo_examen_id  ==6 ){
                      $nome ='parcial2_recuperatoria';
                    }else{
                       $final['parcial2_recuperatoria'] = '';
                    }
                    
                    if($element->tipo_examen_id == 7 ){
                      $nome ='finalexamen';
                    }else{
                       $final['finalexamen'] = '';
                    }
                    
                    if($element->tipo_examen_id == 8 ){
                      $nome ='complementarexamen';
                    }else{
                       $final['complementarexamen'] = '';
                    }
                    
                    if($element->tipo_examen_id == 9 ){
                      $nome ='extraordinaria';
                    }else{
                       $final['extraordinaria'] = '';
                    }
              
              
              
          }
          $cer[]=$final;
          
          
      }
      
         return response()->json($cer, 200);     
                
          
    
                      
    }
    
    public function alunoNotas4(Request $request){
        
   
        
           $usuario_id = $this->pegar_id($request);
           $ofertasdaturma = $this->usuarios->listamateriasAluno($usuario_id['sub']);
       
    

             foreach($ofertasdaturma as $oferta){
                 
        $nome_turmatotal= $oferta->nome_semestre.' '.$oferta->nome_turma;
   //   $oferta_disciplina_id = $oferta->oferta_disciplina_id;
          
      
        $c = $this->tentativa
            ->join('usuarios','usuarios.id','ex_tentativa.usuarios_id')
            ->join('ex_examen','ex_examen.id','ex_tentativa.ex_examen_id')
            ->join('ex_tipo_examen','ex_tipo_examen.id','ex_examen.TIPO_EXAMEN_ID')
            ->join('oferta_disciplina','oferta_disciplina.id','=','ex_tentativa.oferta_disciplina_id')
            ->join('turma','turma.id','=','oferta_disciplina.turma_id')
       
            ->join('disciplinas','disciplinas.id','=','oferta_disciplina.disciplinas_id')
            ->join('config_filial','config_filial.id','oferta_disciplina.config_filial_id')
            ->join('semestre', 'semestre.id', 'oferta_disciplina.semestre_id')
            ->join('periodo_letivo', 'periodo_letivo.id', 'ex_tentativa.periodo_letivo_id')   
            ->leftJoin('periodo_anual','periodo_anual.id','periodo_letivo.periodo_anual_id')
            ->join('ano', 'ano.id', 'periodo_letivo.ano_id')  
            ->join('funcionario', 'funcionario.id','oferta_disciplina.funcionario_id')
            ->select('periodo_letivo.id as periodo_letivo_id','ex_tipo_examen.nome as nome_tipo_examen',
            'ex_tentativa.nota','ex_tipo_examen.id as tipo_examen_id','ex_tipo_examen.nome as nome_tipo_examen','ex_examen.FECHA_INICIO AS fecha_inicio',
            'ex_tentativa.oferta_disciplina_id','disciplinas.nome as nome_disciplina', 'ex_tentativa.created_at as data_finalizacao')
            ->where('ex_tentativa.usuarios_id',$usuario_id['sub'])
          //->where('ex_examen.oferta_disciplina_id',$oferta->oferta_disciplina_id )
            ->where('ex_tentativa.oferta_disciplina_id',$oferta->oferta_disciplina_id )
            ->orderBy('usuarios.sobrenome','asc')
          ->get();
          
          
          
          if(count($c) >= 1){
              
              
              $data[] = $c;
              
          }
      
      }
 
      
     // response()->json($data, 200);  
      
      
      foreach($data as $novo){
         
         $final = [];
        
          foreach($novo as $k => $element){
              
                    $final['disciplina'] = $element->nome_disciplina;
              
              
                $pontos_extras = DB::connection('mysql2')->table('ex_ponto_extra')
                                ->where('ex_ponto_extra.oferta_disciplina_id',$oferta->oferta_disciplina_id )
                                ->where('ex_ponto_extra.usuarios_id',$usuario_id['sub'])
                                ->first(); 
                                
                    if($pontos_extras){
                        $final['notas']['ponto_extra'] =  ['nota'=>1, 'fecha'=>date('d-m-Y',strtotime($pontos_extras->created_at))];
                    }else{
                       $final['notas']['ponto_extra'] =  ['nota'=>'', 'fecha'=>''];
                    }
                    
                $trabalho_pratico =  $this->notaTrabalhos($element->oferta_disciplina_id,$usuario_id['sub']);
                    if($trabalho_pratico){
                        $final['notas']['trabajo_pratico'] =  $trabalho_pratico;
                    }else{
                       $final['notas']['trabajo_pratico'] =  ['nota'=>'', 'fecha'=>''];
                    }
                    
               
                    if($element->tipo_examen_id  == 1 ){
                        
                        
                      $final['notas']['processo1'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                    }
                  
                  
                    if($element->tipo_examen_id  == 2  and  !isset($final['notas']['parcial1'])){
                        $final['notas']['parcial1'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                    }
                  
                    if($element->tipo_examen_id  ==3 ){
                      $final['notas']['parcial1_recuperatoria'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                   
                    }
                    
                    if($element->tipo_examen_id  ==4 ){
                        $final['notas']['processo2'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                     
                    }
                    
                    if($element->tipo_examen_id  ==5 ){
                        $final['notas']['parcial2'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                      
                    }
                    
                    if($element->tipo_examen_id  ==6 ){
                        $final['notas']['parcial2_recuperatoria'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                     
                    }
                    
                    if($element->tipo_examen_id == 7 ){
                        $final['notas']['finalexamen'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
              
                    }
                    
                    if($element->tipo_examen_id == 8 ){
                        $final['notas']['complementarexamen'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                     
                    }
                    
                    if($element->tipo_examen_id == 9 ){
                        $final['notas']['extraordinaria'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                      
                    }
              
              
              
          }
          $cr[]=$final;
          
          
      }
      foreach($cr as $er){
          if(!isset($er['notas']['processo1'])){
              
              $er['notas']['processo1'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['parcial1'])){
              
              $er['notas']['parcial1'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['parcial1_recuperatoria'])){
              
              $er['notas']['parcial1_recuperatoria'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['processo2'])){
              
              $er['notas']['processo2'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['parcial2'])){
              
              $er['notas']['parcial2'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['parcial2_recuperatoria'])){
              
              $er['notas']['parcial2_recuperatoria'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['finalexamen'])){
              
              $er['notas']['finalexamen'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['complementarexamen'])){
              
              $er['notas']['complementarexamen'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['extraordinaria'])){
              
              $er['notas']['extraordinaria'] =  ['nota'=>'', 'fecha'=>''];
          }
        
         $cer[] = $er;
          
      }
   // return  response()->json($cer, 200); 
      
     
      foreach($cer as $v){
         
           $v['notas']['total_puntos_logrados']['nota'] = (int)$v['notas']['ponto_extra']['nota'] + (int)$v['notas']['trabajo_pratico']['nota']+ (int)$v['notas']['processo1']['nota']+ $this->maxNota((int)$v['notas']['parcial1']['nota'],(int)$v['notas']['parcial1_recuperatoria']['nota']) + (int)$v['notas']['processo2']['nota']+ $this->maxNota((int)$v['notas']['parcial2']['nota'],(int)$v['notas']['parcial2_recuperatoria']['nota']);
           $v['notas']['total_puntos_logrados']['fecha'] = '';
          
          if((int)$v['notas']['extraordinaria']['nota'] >= 1){
              $v['notas']['total_puntos']['nota']= (int)$v['notas']['extraordinaria']['nota'];
              $v['notas']['total_puntos']['fecha'] = $v['notas']['extraordinaria']['fecha'];
              
          }else{
              $v['notas']['total_puntos']['nota'] = $this->maxNota((int)$v['notas']['finalexamen']['nota'],(int)$v['notas']['complementarexamen']['nota']) + (int)$v['notas']['total_puntos_logrados']['nota']; // aqui vai somar o total
              $v['notas']['total_puntos']['fecha'] = '';
          }
            $v['notas']['parcial1']['nota'] = $this->maxNota($v['notas']['parcial1']['nota'],$v['notas']['parcial1_recuperatoria']['nota']);
            $v['notas']['parcial2']['nota'] = $this->maxNota($v['notas']['parcial2']['nota'],$v['notas']['parcial2_recuperatoria']['nota']);
         $r[]=$v;
          
      }
      
         return response()->json($r, 200);     
                
          
    
                      
    }
    public function alunoNotas4_id($id_user,$periodo){
        
   
        
           $usuario_id['sub'] = $id_user;
           $ofertasdaturma = $this->usuarios->listamateriasAluno2($usuario_id['sub'],$periodo);
       
 
          
            //  $pe = $c->periodo_letivo_id;
            //  return $ofertasdaturma;
          
        // $resposta = DB::connection('mysql2')->table('matricula_disciplina')
        // ->join('oferta_disciplina','oferta_disciplina.id','matricula_disciplina.oferta_disciplina_id')
        // ->join('semestre', 'semestre.id', 'oferta_disciplina.semestre_id')
        // ->join('turma','turma.id','=','oferta_disciplina.turma_id')
        // ->join('disciplinas','disciplinas.id','oferta_disciplina.disciplinas_id')
        // // ->where('matricula_disciplina.status',4)
        // ->select('matricula_disciplina.status','oferta_disciplina.id as oferta_disciplina_id','semestre.nome as nome_semestre','turma.nome as nome_turma','disciplinas.nome as nome_materia'
        // ,'disciplinas.carga_horaria')
        // ->where('periodo_letivo_id',$periodo)
        //  ->where('oferta_disciplina.status',1)
       
        // ->get();
        // return response()->json($resposta,200);
          
        //   $ofertasdaturma = $resposta;
        foreach($ofertasdaturma as $oferta){
                 
                $nome_turmatotal= $oferta->nome_semestre.' '.$oferta->nome_turma;
           //   $oferta_disciplina_id = $oferta->oferta_disciplina_id;
                  
              
                $c = $this->tentativa
                    ->Join('usuarios','usuarios.id','ex_tentativa.usuarios_id')
                    ->Join('ex_examen','ex_examen.id','ex_tentativa.ex_examen_id')
                    ->Join('ex_tipo_examen','ex_tipo_examen.id','ex_examen.TIPO_EXAMEN_ID')
                    ->Join('oferta_disciplina','oferta_disciplina.id','=','ex_tentativa.oferta_disciplina_id')
                    ->Join('turma','turma.id','=','oferta_disciplina.turma_id')
               
                    ->Join('disciplinas','disciplinas.id','=','oferta_disciplina.disciplinas_id')
                    ->Join('config_filial','config_filial.id','oferta_disciplina.config_filial_id')
                    ->Join('semestre', 'semestre.id', 'oferta_disciplina.semestre_id')
                    ->Join('periodo_letivo', 'periodo_letivo.id', 'ex_tentativa.periodo_letivo_id')   
                    ->leftJoin('periodo_anual','periodo_anual.id','periodo_letivo.periodo_anual_id')
                    ->Join('ano', 'ano.id', 'periodo_letivo.ano_id')  
                    ->Join('funcionario', 'funcionario.id','oferta_disciplina.funcionario_id')
                    ->select('periodo_letivo.id as periodo_letivo_id','ex_tipo_examen.nome as nome_tipo_examen',
                    'ex_tentativa.nota','ex_tipo_examen.id as tipo_examen_id','ex_tipo_examen.nome as nome_tipo_examen','ex_examen.FECHA_INICIO AS fecha_inicio',
                    'ex_tentativa.oferta_disciplina_id','disciplinas.nome as nome_disciplina', 'ex_tentativa.created_at as data_finalizacao')
                    ->where('ex_tentativa.usuarios_id',$usuario_id['sub'])
                  //->where('ex_examen.oferta_disciplina_id',$oferta->oferta_disciplina_id )
                        ->where('ex_tentativa.status',1)
                        
                    ->where('ex_tentativa.oferta_disciplina_id',$oferta->oferta_disciplina_id )
                    ->orderBy('usuarios.sobrenome','asc')
                  ->get();
                  
      
          if(count($c) >= 1){
              
              
              $data[] = $c;
              
          }else{
            //   $data=[];
          }
       
          
      $cachorro[]=$c;
      }
 
      
//   return response()->json($cachorro, 200);  
      
      if($data !=[]){
      foreach($data as $novo){
         
         $final = [];
        
          foreach($novo as $k => $element){
              
              $final['disciplina'] = $element->nome_disciplina;
              
              if(!isset($cr[$element->nome_disciplina])){
                    
              
              
              
                $pontos_extras = DB::connection('mysql2')->table('ex_ponto_extra')
                                ->where('ex_ponto_extra.oferta_disciplina_id',$oferta->oferta_disciplina_id )
                                ->where('ex_ponto_extra.usuarios_id',$usuario_id['sub'])
                                ->first(); 
                                
                    if($pontos_extras){
                        $final['notas']['ponto_extra'] =  ['nota'=>1, 'fecha'=>date('d-m-Y',strtotime($pontos_extras->created_at))];
                    }else{
                       $final['notas']['ponto_extra'] =  ['nota'=>'', 'fecha'=>''];
                    }
                    
                $trabalho_pratico =  $this->notaTrabalhos($element->oferta_disciplina_id,$usuario_id['sub']);
                
                    if($trabalho_pratico){
                        
                        $final['notas']['trabajo_pratico'] =  $trabalho_pratico;
                        
                    }else{
                        
                       $final['notas']['trabajo_pratico'] =  ['nota'=>'', 'fecha'=>''];
                       
                    }
                    
               
                    if($element->tipo_examen_id  == 1 ){
                        
                        
                      $final['notas']['processo1'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                    }
                  
                  
                    if($element->tipo_examen_id  == 2  and  !isset($final['notas']['parcial1'])){
                        $final['notas']['parcial1'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                    }
                  
                    if($element->tipo_examen_id  ==3 ){
                      $final['notas']['parcial1_recuperatoria'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                   
                    }
                    
                    if($element->tipo_examen_id  ==4 ){
                        $final['notas']['processo2'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                     
                    }
                    
                    if($element->tipo_examen_id  ==5 ){
                        $final['notas']['parcial2'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                      
                    }
                    
                    if($element->tipo_examen_id  ==6 ){
                        $final['notas']['parcial2_recuperatoria'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                     
                    }
                    
                    if($element->tipo_examen_id == 7 ){
                        $final['notas']['finalexamen'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
              
                    }
                    
                    if($element->tipo_examen_id == 8 ){
                        $final['notas']['complementarexamen'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                     
                    }
                    
                    if($element->tipo_examen_id == 9 ){
                        $final['notas']['extraordinaria'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                      
                    }
                    
              }else{
                  
               
                     $cr[$element->nome_disciplina]['disciplina'] = $element->nome_disciplina;
              
              
              
                $pontos_extras = DB::connection('mysql2')->table('ex_ponto_extra')
                                ->where('ex_ponto_extra.oferta_disciplina_id',$oferta->oferta_disciplina_id )
                                ->where('ex_ponto_extra.usuarios_id',$usuario_id['sub'])
                                ->first(); 
                                
                    if($pontos_extras){
                        $cr[$element->nome_disciplina]['notas']['ponto_extra'] =  ['nota'=>1, 'fecha'=>date('d-m-Y',strtotime($pontos_extras->created_at))];
                    }else{
                       $cr[$element->nome_disciplina]['notas']['ponto_extra'] =  ['nota'=>'', 'fecha'=>''];
                    }
                    
                $trabalho_pratico =  $this->notaTrabalhos($element->oferta_disciplina_id,$usuario_id['sub']);
                    if($trabalho_pratico){
                        $cr[$element->nome_disciplina]['notas']['trabajo_pratico'] =  $trabalho_pratico;
                    }else{
                       $cr[$element->nome_disciplina]['notas']['trabajo_pratico'] =  ['nota'=>'', 'fecha'=>''];
                    }
                    
               
                    if($element->tipo_examen_id  == 1 ){
                        
                        
                      $cr[$element->nome_disciplina]['notas']['processo1'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                    }
                  
                  
                    if($element->tipo_examen_id  == 2  and  !isset($cr[$element->nome_disciplina]['notas']['parcial1'])){
                        $cr[$element->nome_disciplina]['notas']['parcial1'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                    }
                  
                    if($element->tipo_examen_id  ==3 ){
                      $cr[$element->nome_disciplina]['notas']['parcial1_recuperatoria'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                   
                    }
                    
                    if($element->tipo_examen_id  ==4 ){
                        $cr[$element->nome_disciplina]['notas']['processo2'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                     
                    }
                    
                    if($element->tipo_examen_id  ==5 ){
                        $cr[$element->nome_disciplina]['notas']['parcial2'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                      
                    }
                    
                    if($element->tipo_examen_id  ==6 ){
                        $cr[$element->nome_disciplina]['notas']['parcial2_recuperatoria'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                     
                    }
                    
                    if($element->tipo_examen_id == 7 ){
                        $cr[$element->nome_disciplina]['notas']['finalexamen'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
              
                    }
                    
                    if($element->tipo_examen_id == 8 ){
                        $cr[$element->nome_disciplina]['notas']['complementarexamen'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                     
                    }
                    
                    if($element->tipo_examen_id == 9 ){
                        $cr[$element->nome_disciplina]['notas']['extraordinaria'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                      
                    }
                    $final2 = $cr[$element->nome_disciplina];
                   $cr[$element->nome_disciplina] = $final2;
                    
                    // return $cr;
              }
              
          } 
         
      if(!isset($cr[$final['disciplina']])){
          $cr[$final['disciplina']] = $final;
      }

      }
      
      
      $novo = $cr;
      $cr=[];
    
          foreach($novo as $er ){
                $cr[] = $er;
          }
    //   return $cr;
       
      foreach($cr as $er){
          if(!isset($er['notas']['processo1'])){
              
              $er['notas']['processo1'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['parcial1'])){
              
              $er['notas']['parcial1'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['parcial1_recuperatoria'])){
              
              $er['notas']['parcial1_recuperatoria'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['processo2'])){
              
              $er['notas']['processo2'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['parcial2'])){
              
              $er['notas']['parcial2'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['parcial2_recuperatoria'])){
              
              $er['notas']['parcial2_recuperatoria'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['finalexamen'])){
              
              $er['notas']['finalexamen'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['complementarexamen'])){
              
              $er['notas']['complementarexamen'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['extraordinaria'])){
              
              $er['notas']['extraordinaria'] =  ['nota'=>'', 'fecha'=>''];
          }
        
         $cer[] = $er;
        //  $juncao[$element->nome_disciplina] = $er;
         
          
      }
         
    // return  response()->json($juncao, 200); 
      
     
      foreach($cer as $v){
         
           $v['notas']['total_puntos_logrados']['nota'] = (int)$v['notas']['ponto_extra']['nota'] + (int)$v['notas']['trabajo_pratico']['nota']+ (int)$v['notas']['processo1']['nota']+ $this->maxNota((int)$v['notas']['parcial1']['nota'],(int)$v['notas']['parcial1_recuperatoria']['nota']) + (int)$v['notas']['processo2']['nota']+ $this->maxNota((int)$v['notas']['parcial2']['nota'],(int)$v['notas']['parcial2_recuperatoria']['nota']);
           $v['notas']['total_puntos_logrados']['fecha'] = '';
         
          
          if((int)$v['notas']['extraordinaria']['nota'] >= 1){
              $v['notas']['total_puntos']['nota']= (int)$v['notas']['extraordinaria']['nota'];
              $v['notas']['total_puntos']['fecha'] = $v['notas']['extraordinaria']['fecha'];
              
          }else{
              $v['notas']['total_puntos']['nota'] = $this->maxNota((int)$v['notas']['finalexamen']['nota'],(int)$v['notas']['complementarexamen']['nota']) + (int)$v['notas']['total_puntos_logrados']['nota']; // aqui vai somar o total
              if( $v['notas']['total_puntos']['nota']  == 59){
                  $v['notas']['total_puntos']['nota'] =60;
              }
                if( $v['notas']['total_puntos']['nota'] > 100){
                $v['notas']['total_puntos']['nota'] = 100;
           }
              $v['notas']['total_puntos']['fecha'] = '';
          }
            $v['notas']['parcial1']['nota'] = $this->maxNota($v['notas']['parcial1']['nota'],$v['notas']['parcial1_recuperatoria']['nota']);
            $v['notas']['parcial2']['nota'] = $this->maxNota($v['notas']['parcial2']['nota'],$v['notas']['parcial2_recuperatoria']['nota']);
         $r[]=$v;
          
      }
      
         return response()->json($r, 200);     
                
     }else{
        return response()->json($data, 200);   
     }}
    
                      
    
    
    public function alunoNotas5(Request $request){
        // 
   
        
           $usuario_id = $this->pegar_id($request);
           $ofertasdaturma = $this->usuarios->listamateriasAluno($usuario_id['sub']);
       
    

             foreach($ofertasdaturma as $oferta){
                 
        $nome_turmatotal= $oferta->nome_semestre.' '.$oferta->nome_turma;
   //   $oferta_disciplina_id = $oferta->oferta_disciplina_id;
        $pontos_extras = DB::connection('mysql2')->table('ex_ponto_extra')
        ->where('ex_ponto_extra.oferta_disciplina_id',$oferta->oferta_disciplina_id )
        ->where('ex_ponto_extra.usuarios_id',$usuario_id['sub'])
        ->first();   
      
        $c = $this->tentativa
            ->join('usuarios','usuarios.id','ex_tentativa.usuarios_id')
            ->join('ex_examen','ex_examen.id','ex_tentativa.ex_examen_id')
            ->join('ex_tipo_examen','ex_tipo_examen.id','ex_examen.TIPO_EXAMEN_ID')
            ->join('oferta_disciplina','oferta_disciplina.id','=','ex_tentativa.oferta_disciplina_id')
            ->join('turma','turma.id','=','oferta_disciplina.turma_id')
       
            ->join('disciplinas','disciplinas.id','=','oferta_disciplina.disciplinas_id')
            ->join('config_filial','config_filial.id','oferta_disciplina.config_filial_id')
            ->join('semestre', 'semestre.id', 'oferta_disciplina.semestre_id')
            ->join('periodo_letivo', 'periodo_letivo.id', 'ex_tentativa.periodo_letivo_id')   
            ->join('periodo_anual','periodo_anual.id','periodo_letivo.periodo_anual_id')
            ->join('ano', 'ano.id', 'periodo_letivo.ano_id')  
            ->join('funcionario', 'funcionario.id','oferta_disciplina.funcionario_id')
          ->select('ex_tipo_examen.nome as nome_tipo_examen',
          'ex_tentativa.nota','ex_tipo_examen.id as tipo_examen_id','ex_tipo_examen.nome as nome_tipo_examen','ex_examen.FECHA_INICIO AS fecha_inicio',
          'ex_tentativa.oferta_disciplina_id','disciplinas.nome as nome_disciplina', 'ex_tentativa.created_at as data_finalizacao')
          ->where('ex_tentativa.usuarios_id',$usuario_id['sub'])
          //->where('ex_examen.oferta_disciplina_id',$oferta->oferta_disciplina_id )
           ->where('ex_tentativa.oferta_disciplina_id',$oferta->oferta_disciplina_id )
            ->orderBy('usuarios.sobrenome','asc')
               
          ->get();
          
          if(count($c) >= 1){
              
              $data[] = $c;
              
          }
      
      }
      
      
      foreach($data as $novo){
         
         $final = [];
          
          foreach($novo as $k => $element){
              
                  $final['disciplina'] = $element->nome_disciplina;
                  
                  
                    if($element->tipo_examen_id  == 1 ){
                        
                        
                      $final['notas'][] =  ['examen'=>$element->nome_tipo_examen,'nota'=>$element->nota, 'fecha'=>date('d-m-Y H:i:s',strtotime($element->data_finalizacao))];
                    }else{
                       $final['notas'][] =  ['examen'=>$element->nome_tipo_examen,'nota'=>'', 'fecha'=>''];
                    }
                    
                   
                    
                  
              
              
              
          }
          $cer[]=$final;
          
          
      }
      
        return response()->json($cer, 200);     
          
    }
    
    public function notaTrabalhos($oferta_disciplina,$usuario_id)
    {
        $trabalho_pratico =  $this->listar_notas_trabalhosPraticos2($oferta_disciplina,$usuario_id);
    
         $n = [];
       foreach($trabalho_pratico['todosTrabalhosAlunos'] as $notTrab){
            $n[] = $notTrab->somatotal;
       }
       
         
         
         if(isset($notTrab->somatotal)){
             $nota = max($n);
             
             $data2 =['fecha'=> date('d-m-Y',strtotime($trabalho_pratico['dataTrabalho'])),'nota'=>$nota];
         }else{
              $data2 =['fecha'=> '','nota'=>''];
         }
       
       
         return $data2;
    }
    
    public function maxNota($p1, $p2){
        $array = [$p1,$p2];
        return max($array);
    }
    
    public function corrirTentativa(Request $request){
    //   $data = $this->tentativa
    //         ->join('usuarios','usuarios.id','ex_tentativa.usuarios_id')
    //         ->join('ex_examen','ex_examen.id','ex_tentativa.ex_examen_id')
    //         ->join('ex_tipo_examen','ex_tipo_examen.id','ex_examen.TIPO_EXAMEN_ID')
    //         ->join('oferta_disciplina','oferta_disciplina.id','=','ex_tentativa.oferta_disciplina_id')
    //         ->join('turma','turma.id','=','oferta_disciplina.turma_id')
    //         ->join('disciplinas','disciplinas.id','=','oferta_disciplina.disciplinas_id')
    //         ->join('config_filial','config_filial.id','oferta_disciplina.config_filial_id')
    //         ->join('semestre', 'semestre.id', 'oferta_disciplina.semestre_id')
    //         ->join('periodo_letivo', 'periodo_letivo.id', 'ex_tentativa.periodo_letivo_id')   
    //         ->leftjoin('periodo_anual','periodo_anual.id','periodo_letivo.periodo_anual_id')
    //         ->join('ano', 'ano.id', 'periodo_letivo.ano_id')  
    //         ->join('funcionario', 'funcionario.id','oferta_disciplina.funcionario_id')
    //       ->select('ex_tentativa.status','ex_tentativa.oferta_disciplina_id','ex_tentativa.created_at','ex_tentativa.id','ex_tipo_examen.nome as nome_tipo_examen', DB::raw('CONCAT(usuarios.sobrenome,\', \', usuarios.nome) as nome_usuarios'),
    //       DB::raw('CONCAT(funcionario.nome,\' \', funcionario.sobrenome) as nome_funcionario'),
    //       'ex_tentativa.nota', 'usuarios.id as usuarios_id','ex_tipo_examen.id as tipo_examen_id','usuarios.doc_oficial','ex_examen.FECHA_INICIO AS fecha_inicio',
    //       'disciplinas.nome as nome_materia','turma.nome as nome_turma','config_filial.id as id_filial','config_filial.nome as nome_filial', 'semestre.nome as nome_semestre' ,
    //       'ano.nome as nome_ano ','periodo_anual.nome as nome_periodo_anual')
    //     //   ->where('ex_tentativa.status',5)
    //   ->where('ex_tipo_examen.id',7)
    //     // ->where('ex_tentativa.id',358498)
    //     //  ->where('ex_examen.id',6247)
    //     ->where('ex_tentativa.usuarios_id',11622)
    //     //  ->where('ex_tentativa.status',5)
    //     ->where('ex_examen.oferta_disciplina_id',2380)
    //     // ->where('ex_tentativa.oferta_disciplina_id',2773) 
    //         //   ->orderBy('ex_tentativa.id','desc')
    //         //   ->orderBy('usuarios.sobrenome','asc')

    //           ->groupBy('ex_examen.id')
    //       ->get();
          
            
    // $array =['nota' => 42];
    // $data =  DB::connection('mysql2')->table('ex_tentativa')->where('id',458697)->update($array); 
     
    // $data->nota = 14;
    // $data->save();
      
      
       
        // $data->faturas_id = 570446;
        // $data->save();
        

            //   $data = $this->tentativa->where('ex_tentativa_id.status',5)
      
     
        //  ->where('ex_examen_id',5777)
        // ->where('ex_tentativa.usuarios_id',10426)
     
        // // ->where('ex_examen.oferta_disciplina_id',3314)
        // ->where('ex_tentativa.oferta_disciplina_id',2908) 
        // ->get();
  
        

    // $data = DB::connection('mysql2')->table('ex_tentativa')
    // ->where('id',429996)->get();
    
  
  
    //  $data = DB::connection('mysql2')->table('finan_pedidos')
    // ->where('id', 83350 )->get();
  
     
//   $data=  $this->tentativa
//   ->join('ex_examen','ex_examen.id','ex_tentativa.ex_examen_id')
//   ->where('ex_tentativa.id',166299)->first();
          
//   $data->nota = 48;
//   $data->save();

//  $data=  DB::connection('mysql2')->table('lista_examenes_materia')
//  ->where('id',8567)
// ->join('ex_t','ex_examen.id','ex_tentativa.ex_examen_id')
//  ->get();


// return $conta;
    // $data = DB::connection('mysql2')->table('ead_tentativa')
    // ->where('oferta_disciplina_id',1835)
    // ->where('usuarios_id',1016)
    // ->get();

    // $data = DB::connection('mysql2')->table('matricula_disciplina')
    // //->join('finan_pedidos','finan_pedidos.id','finan_pedido_faturas.finan_pedidos_id')
    // //->join('faturas','faturas.id','finan_pedido_faturas.faturas_id')
    // ->where('periodo_letivo_id',43282)
    // // ->where('matricula_d',43282)    
    // //->where('USUARIOS_ID',1425 1)
    // //->where('EXAMEN_ID',6226)
    // ->get();
       
    // $array =['status' => 1];
    // $data =  DB::connection('mysql2')->table('matricula_disciplina')->where('id',325380)->update($array);
    
    
return   response()->json($data, 200);     

}
    
    public function enviarNotaTrabalho(Request $request,$id)
    {
        $data = $this->alunotrabalho->find($id);
       
        $data->nota = $request->nota;
        $data->comentario = $request->comentario;
        
       $resposta = $data->save();
        
        if($resposta){
            return response()->json('Avaliado  com sucesso', 200);
        }else{
            return response()->json('Erro ao enviar os dados', 500);
        }



        return response()->json($data,200) ;
  
  
    }
    
    
   
    public function excluirTentativa(Request $request){
  
  $data=  $this->tentativa
  ->join('ex_examen','ex_examen.id','ex_tentativa.ex_examen_id')
  ->where('ex_tentativa.id',$request->tentativa_id)->get();
          
  $data->status = $request->status;
  $data->save();
 
//   return response()->json('examen complementario excluido com sucesso',200);
return $data;
  
  
 
    }
     public function alunoNotas10(Request $request){
          $usuario_id = $this->pegar_id($request);
           
                   $curl = curl_init();
        
                        curl_setopt_array($curl, array(
                          CURLOPT_URL => 'https://permission.wisesistemas.com/solution/permission/'.$usuario_id['sub'],
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_ENCODING => '',
                          CURLOPT_MAXREDIRS => 10,
                          CURLOPT_TIMEOUT => 0,
                          CURLOPT_FOLLOWLOCATION => true,
                          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                          CURLOPT_CUSTOMREQUEST => 'get',
                          
                          CURLOPT_POSTFIELDS =>"",
                          CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json'
                          ),
                        ));
                        
                        $response = curl_exec($curl);
                        
                        curl_close($curl);
                   
                    $noe = json_decode($response);
                        $r = [];
                    foreach($noe as $error){
                        
                        $r[] =$error;
                        
                    }
                    if(count($r) >= 1){
            
                        return response()->json($r, 200);
            
                    }
     
          
           $ofertasdaturma = $this->usuarios->listamateriasAluno($usuario_id['sub']);
        
    
     $c = $this->tentativa
            ->join('usuarios','usuarios.id','ex_tentativa.usuarios_id')
            ->join('ex_examen','ex_examen.id','ex_tentativa.ex_examen_id')
            ->join('ex_tipo_examen','ex_tipo_examen.id','ex_examen.TIPO_EXAMEN_ID')
            ->join('oferta_disciplina','oferta_disciplina.id','=','ex_tentativa.oferta_disciplina_id')
            ->join('turma','turma.id','=','oferta_disciplina.turma_id')
       
            ->join('disciplinas','disciplinas.id','=','oferta_disciplina.disciplinas_id')
            ->join('config_filial','config_filial.id','oferta_disciplina.config_filial_id')
            ->join('semestre', 'semestre.id', 'oferta_disciplina.semestre_id')
            ->leftJoin('periodo_letivo', 'periodo_letivo.id', 'ex_tentativa.periodo_letivo_id')   
            ->leftJoin('periodo_anual','periodo_anual.id','periodo_letivo.periodo_anual_id')
            ->leftjoin('ano', 'ano.id', 'periodo_letivo.ano_id')  
            ->leftjoin('funcionario', 'funcionario.id','oferta_disciplina.funcionario_id')
            ->select('semestre.nome as nome_semestre','turma.nome as nome_turma','periodo_letivo.id as periodo_letivo_id','ex_tipo_examen.nome as nome_tipo_examen',
            'ex_tentativa.nota','ex_tipo_examen.id as tipo_examen_id','ex_tipo_examen.nome as nome_tipo_examen','ex_examen.FECHA_INICIO AS fecha_inicio',
            'ex_tentativa.oferta_disciplina_id','disciplinas.nome as nome_disciplina', 'ex_tentativa.created_at as data_finalizacao')
            ->where('ex_tentativa.usuarios_id',$usuario_id['sub'])
          //->where('ex_examen.oferta_disciplina_id',$oferta->oferta_disciplina_id )
            ->where('ex_tentativa.oferta_disciplina_id',$ofertasdaturma[0]->oferta_disciplina_id )
        
            ->orderBy('usuarios.sobrenome','asc')
          ->first();
          
        //   return $c;
             $pe = $c->periodo_letivo_id;
            //  return $pe;
          
        $resposta = DB::connection('mysql2')->table('matricula_disciplina')
        ->join('oferta_disciplina','oferta_disciplina.id','matricula_disciplina.oferta_disciplina_id')
        ->join('semestre', 'semestre.id', 'oferta_disciplina.semestre_id')
        ->join('turma','turma.id','=','oferta_disciplina.turma_id')
        ->join('disciplinas','disciplinas.id','oferta_disciplina.disciplinas_id')
        // ->where('matricula_disciplina.status',4)
        ->select('matricula_disciplina.status','oferta_disciplina.id as oferta_disciplina_id','semestre.nome as nome_semestre','turma.nome as nome_turma','disciplinas.nome as nome_materia'
        ,'disciplinas.carga_horaria')
        ->where('periodo_letivo_id',$pe)
       
        ->get();
        // return response()->json($resposta,200);
          
          $ofertasdaturma = $resposta;
                 foreach($ofertasdaturma as $oferta){
                 
        $nome_turmatotal= $oferta->nome_semestre.' '.$oferta->nome_turma;
   //   $oferta_disciplina_id = $oferta->oferta_disciplina_id;
          
      
        $c = $this->tentativa
            ->Join('usuarios','usuarios.id','ex_tentativa.usuarios_id')
            ->Join('ex_examen','ex_examen.id','ex_tentativa.ex_examen_id')
            ->Join('ex_tipo_examen','ex_tipo_examen.id','ex_examen.TIPO_EXAMEN_ID')
            ->Join('oferta_disciplina','oferta_disciplina.id','=','ex_tentativa.oferta_disciplina_id')
            ->Join('turma','turma.id','=','oferta_disciplina.turma_id')
       
            ->Join('disciplinas','disciplinas.id','=','oferta_disciplina.disciplinas_id')
            ->Join('config_filial','config_filial.id','oferta_disciplina.config_filial_id')
            ->Join('semestre', 'semestre.id', 'oferta_disciplina.semestre_id')
            ->leftjoin('periodo_letivo', 'periodo_letivo.id', 'ex_tentativa.periodo_letivo_id')   
            ->leftJoin('periodo_anual','periodo_anual.id','periodo_letivo.periodo_anual_id')
            ->leftJoin('ano', 'ano.id', 'periodo_letivo.ano_id')  
            ->leftJoin('funcionario', 'funcionario.id','oferta_disciplina.funcionario_id')
            ->select('periodo_letivo.id as periodo_letivo_id','ex_tipo_examen.nome as nome_tipo_examen',
            'ex_tentativa.nota','ex_tipo_examen.id as tipo_examen_id','ex_tipo_examen.nome as nome_tipo_examen','ex_examen.FECHA_INICIO AS fecha_inicio',
            'ex_tentativa.oferta_disciplina_id','disciplinas.nome as nome_disciplina', 'ex_tentativa.created_at as data_finalizacao')
            ->where('ex_tentativa.usuarios_id',$usuario_id['sub'])
          //->where('ex_examen.oferta_disciplina_id',$oferta->oferta_disciplina_id )
                ->where('ex_tentativa.status',1)->where('oferta_disciplina.created','>=','2020-10-12')
                
            ->where('ex_tentativa.oferta_disciplina_id',$oferta->oferta_disciplina_id )
            ->orderBy('usuarios.sobrenome','asc')
          ->get();
          
        //   return $c;
        
          
          if(count($c) >= 1){
              
              
              $data[] = $c;
              
          }
        //   else{
        //       $data=[];
        //   }
       
          
      
      }
 
      
    //   response()->json($data, 200);  
      
      if($data !=[]){
      foreach($data as $novo){
         
         $final = [];
        
          foreach($novo as $k => $element){
              $final['disciplina'] = $element->nome_disciplina;
              
              if(!isset($cr[$element->nome_disciplina])){
                    
              
              
              
                $pontos_extras = DB::connection('mysql2')->table('ex_ponto_extra')
                                ->where('ex_ponto_extra.oferta_disciplina_id',$oferta->oferta_disciplina_id )
                                ->where('ex_ponto_extra.usuarios_id',$usuario_id['sub'])
                                ->first(); 
                                
                    if($pontos_extras){
                        $final['notas']['ponto_extra'] =  ['nota'=>1, 'fecha'=>date('d-m-Y',strtotime($pontos_extras->created_at))];
                    }else{
                       $final['notas']['ponto_extra'] =  ['nota'=>'', 'fecha'=>''];
                    }
                    
                $trabalho_pratico =  $this->notaTrabalhos($element->oferta_disciplina_id,$usuario_id['sub']);
                    if($trabalho_pratico){
                        $final['notas']['trabajo_pratico'] =  $trabalho_pratico;
                    }else{
                       $final['notas']['trabajo_pratico'] =  ['nota'=>'', 'fecha'=>''];
                    }
                    
               
                    if($element->tipo_examen_id  == 1 ){
                        
                        
                      $final['notas']['processo1'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                    }
                  
                  
                    if($element->tipo_examen_id  == 2  and  !isset($final['notas']['parcial1'])){
                        $final['notas']['parcial1'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                    }
                  
                    if($element->tipo_examen_id  ==3 ){
                      $final['notas']['parcial1_recuperatoria'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                   
                    }
                    
                    if($element->tipo_examen_id  ==4 ){
                        $final['notas']['processo2'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                     
                    }
                    
                    if($element->tipo_examen_id  ==5 ){
                        $final['notas']['parcial2'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                      
                    }
                    
                    if($element->tipo_examen_id  ==6 ){
                        $final['notas']['parcial2_recuperatoria'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                     
                    }
                    
                    if($element->tipo_examen_id == 7 ){
                        $final['notas']['finalexamen'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
              
                    }
                    
                    if($element->tipo_examen_id == 8 ){
                        $final['notas']['complementarexamen'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                     
                    }
                    
                    if($element->tipo_examen_id == 9 ){
                        $final['notas']['extraordinaria'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                      
                    }
                    
              }else{
                  
               
                     $cr[$element->nome_disciplina]['disciplina'] = $element->nome_disciplina;
              
              
              
                $pontos_extras = DB::connection('mysql2')->table('ex_ponto_extra')
                                ->where('ex_ponto_extra.oferta_disciplina_id',$oferta->oferta_disciplina_id )
                                ->where('ex_ponto_extra.usuarios_id',$usuario_id['sub'])
                                ->first(); 
                                
                    if($pontos_extras){
                        $cr[$element->nome_disciplina]['notas']['ponto_extra'] =  ['nota'=>1, 'fecha'=>date('d-m-Y',strtotime($pontos_extras->created_at))];
                    }else{
                       $cr[$element->nome_disciplina]['notas']['ponto_extra'] =  ['nota'=>'', 'fecha'=>''];
                    }
                    
                $trabalho_pratico =  $this->notaTrabalhos($element->oferta_disciplina_id,$usuario_id['sub']);
                    if($trabalho_pratico){
                        $cr[$element->nome_disciplina]['notas']['trabajo_pratico'] =  $trabalho_pratico;
                    }else{
                       $cr[$element->nome_disciplina]['notas']['trabajo_pratico'] =  ['nota'=>'', 'fecha'=>''];
                    }
                    
               
                    if($element->tipo_examen_id  == 1 ){
                        
                        
                      $cr[$element->nome_disciplina]['notas']['processo1'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                    }
                  
                  
                    if($element->tipo_examen_id  == 2  and  !isset($cr[$element->nome_disciplina]['notas']['parcial1'])){
                        $cr[$element->nome_disciplina]['notas']['parcial1'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                    }
                  
                    if($element->tipo_examen_id  ==3 ){
                      $cr[$element->nome_disciplina]['notas']['parcial1_recuperatoria'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                   
                    }
                    
                    if($element->tipo_examen_id  ==4 ){
                        $cr[$element->nome_disciplina]['notas']['processo2'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                     
                    }
                    
                    if($element->tipo_examen_id  ==5 ){
                        $cr[$element->nome_disciplina]['notas']['parcial2'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                      
                    }
                    
                    if($element->tipo_examen_id  ==6 ){
                        $cr[$element->nome_disciplina]['notas']['parcial2_recuperatoria'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                     
                    }
                    
                    if($element->tipo_examen_id == 7 ){
                        $cr[$element->nome_disciplina]['notas']['finalexamen'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
              
                    }
                    
                    if($element->tipo_examen_id == 8 ){
                        $cr[$element->nome_disciplina]['notas']['complementarexamen'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                     
                    }
                    
                    if($element->tipo_examen_id == 9 ){
                        $cr[$element->nome_disciplina]['notas']['extraordinaria'] =  ['nota'=>$element->nota, 'fecha'=>date('d-m-Y',strtotime($element->fecha_inicio))];
                      
                    }
                    $final2 = $cr[$element->nome_disciplina];
                   $cr[$element->nome_disciplina] = $final2;
                    
                    // return $cr;
              }
              
          } 
         
      if(!isset($cr[$final['disciplina']])){
          $cr[$final['disciplina']] = $final;
      }

      }
      
      
      $novo = $cr;
      $cr=[];
    
          foreach($novo as $er ){
                $cr[] = $er;
          }
    //   return $cr;
       
      foreach($cr as $er){
          if(!isset($er['notas']['processo1'])){
              
              $er['notas']['processo1'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['parcial1'])){
              
              $er['notas']['parcial1'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['parcial1_recuperatoria'])){
              
              $er['notas']['parcial1_recuperatoria'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['processo2'])){
              
              $er['notas']['processo2'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['parcial2'])){
              
              $er['notas']['parcial2'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['parcial2_recuperatoria'])){
              
              $er['notas']['parcial2_recuperatoria'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['finalexamen'])){
              
              $er['notas']['finalexamen'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['complementarexamen'])){
              
              $er['notas']['complementarexamen'] =  ['nota'=>'', 'fecha'=>''];
          }
          if(!isset($er['notas']['extraordinaria'])){
              
              $er['notas']['extraordinaria'] =  ['nota'=>'', 'fecha'=>''];
          }
        
         $cer[] = $er;
        //  $juncao[$element->nome_disciplina] = $er;
         
          
      }
         
    // return  response()->json($juncao, 200); 
      
     
      foreach($cer as $v){
         
           $v['notas']['total_puntos_logrados']['nota'] = (int)$v['notas']['ponto_extra']['nota'] + (int)$v['notas']['trabajo_pratico']['nota']+ (int)$v['notas']['processo1']['nota']+ $this->maxNota((int)$v['notas']['parcial1']['nota'],(int)$v['notas']['parcial1_recuperatoria']['nota']) + (int)$v['notas']['processo2']['nota']+ $this->maxNota((int)$v['notas']['parcial2']['nota'],(int)$v['notas']['parcial2_recuperatoria']['nota']);
           $v['notas']['total_puntos_logrados']['fecha'] = '';
          
          if((int)$v['notas']['extraordinaria']['nota'] >= 1){
              $v['notas']['total_puntos']['nota']= (int)$v['notas']['extraordinaria']['nota'];
              $v['notas']['total_puntos']['fecha'] = $v['notas']['extraordinaria']['fecha'];
              
          }else{
              $v['notas']['total_puntos']['nota'] = $this->maxNota((int)$v['notas']['finalexamen']['nota'],(int)$v['notas']['complementarexamen']['nota']) + (int)$v['notas']['total_puntos_logrados']['nota']; // aqui vai somar o total
              if( $v['notas']['total_puntos']['nota']  == 59){
                  $v['notas']['total_puntos']['nota'] =60;
              }
              $v['notas']['total_puntos']['fecha'] = '';
          }
            $v['notas']['parcial1']['nota'] = $this->maxNota($v['notas']['parcial1']['nota'],$v['notas']['parcial1_recuperatoria']['nota']);
            $v['notas']['parcial2']['nota'] = $this->maxNota($v['notas']['parcial2']['nota'],$v['notas']['parcial2_recuperatoria']['nota']);
         $r[]=$v;
          
      }
      
         return response()->json($r, 200);     
                
         }else{
             
             
            return response()->json($data, 200);   
         }
         
     }
    
    
}