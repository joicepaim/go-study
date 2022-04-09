<?php

namespace App\Http\Controllers\Medicina;

use App\Http\Controllers\Controller;



use App\Models\Medicina\Usuarios;
use Illuminate\Http\Request;
use JWTAuth;

use Barryvdh\DomPDF\ServiceProvider;
use Barryvdh\DomPDF\Facade;
use Barryvdh\DomPDF\PDF;





class pdfController extends Controller
{
    

    protected $pdf;
    protected $usuarios;
       protected $tentativacontroller;
    

    public function __construct( Usuarios $usuarios ,PDF $pdf, TentativasController $tentativacontroller)
    {
       $this->pdf = $pdf;
        $this->usuarios = $usuarios;
             $this->tentativacontroller = $tentativacontroller;

    }
    public function pegar_id(Request $request)
    {
    }


    public function tirarAcentos($string)
    {
        return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/", "/(Ç)/", "/(ç)/"), explode(" ", "a A e E i I o O u U n N C c"), $string);
    }

    public function gerarPdf($oferta_disciplina_id,$tipo_examen_id)
    {
   
      $newdados_notas=[];
      $tipo_examen="";
        $dados_notas = (object) $this->tentativacontroller->lista_notas_disciplina2($oferta_disciplina_id)->original;
            //   return response()->json($dados_notas, 200); 
      switch($tipo_examen_id){
          case 7:
                 $fecha = $dados_notas->fecha_inicio->finalexamen;
              $tipo_examen= 'Ordinario';
          break;
            case 8:
                   $fecha =  $dados_notas->fecha_inicio->complementarexamen;
              $tipo_examen= 'Complementario';
          break;
            case 9:
               $fecha = $dados_notas->fecha_inicio->extraordinaria;
              $tipo_examen= 'Extraordinario';
          break;
          
      }
           if(!$fecha){
               return 'nenhum examen feito';
           }
    
    
        foreach($dados_notas->dados_notas as $alunos){
            foreach($alunos->avaliacao as $item){
          
                  if($tipo_examen_id != 7){
                if($item->tipo_examen_id == $tipo_examen_id){
                    $newdados_notas[] = $alunos;
                }
                  }
            }
     
    
                  if($tipo_examen_id == 7){
                   
                    $newdados_notas[] = $alunos;
                    
                }
                       if($alunos->usuarios_id ==10313){
                      $newdados_notas[] = $alunos;
            }
            
        }
        
        
        // return response()->json($newdados_notas,200);
        if($dados_notas->nome_filial =="Filial Pedro Juan Caballero"){
            $nomeFIlial = 'Ciudad de Pedro Juan Caballero';
            $ciudad_filial = $dados_notas->nome_filial;
            $endereco = 'Calle Naciones Unidas Nº450';
        }else{
               $nomeFIlial = 'Ciudad Del Este';
                           $ciudad_filial = $dados_notas->nome_filial;
                      $endereco = 'Avda. Mcal. Estigarribia, Barrio Boquerón';
        }
     
        // return $this->date_entenso($dados_notas->fecha_inicio->finalexamen);
     
        
    //   return $this->date_entenso($fecha);
        
         $pdf=   $this->pdf->loadView('acta', ['dados_notas' => (object)$newdados_notas, 'fecha_inicio' =>$fecha  ,
         'nome_materia' =>$dados_notas->nome_materia,
          'nome_turma' =>$dados_notas->nome_turma,
           'nome_filial' =>$nomeFIlial,
            'ciudad_filial' =>$ciudad_filial,
            'nome_semestre' =>$dados_notas->nome_semestre,
             'nome_periodo_anual' =>$dados_notas->nome_periodo_anual,
             'nome_professor'=>$dados_notas->nome_professor,
              'endereco'=>$endereco,
             'tipo_examen'=>$tipo_examen,
             'data_extenso' =>  $this->date_entenso($fecha),
              'nome_ano' =>$dados_notas->nome_ano,
             
             
             
             
         
         ]);
      
     
          return $pdf->download();
    }
    
     public function gerarPdf2($oferta_disciplina_id,$tipo_examen_id)
    {
    //   $oferta_disciplina_id = 2826  ;
      $newdados_notas=[];
      $tipo_examen="";
        $dados_notas = (object) $this->tentativacontroller->lista_notas_disciplina2($oferta_disciplina_id)->original;
            //   return response()->json($dados_notas, 200); 
      switch($tipo_examen_id){
          case 7:
                 $fecha = $dados_notas->fecha_inicio->finalexamen;
              $tipo_examen= 'Ordinario';
          break;
            case 8:
                   $fecha =  $dados_notas->fecha_inicio->complementarexamen;
              $tipo_examen= 'Complementario';
          break;
            case 9:
               $fecha = $dados_notas->fecha_inicio->extraordinaria;
              $tipo_examen= 'Extraordinario';
          break;
          
      }
           if(!$fecha){
               return 'nenhum examen feito';
           }
    
    
        foreach($dados_notas->dados_notas as $alunos){
            foreach($alunos->avaliacao as $item){
          
                  if($tipo_examen_id != 7){
                if($item->tipo_examen_id == $tipo_examen_id){
                    $newdados_notas[] = $alunos;
                }
                  }
            }
     
    
                  if($tipo_examen_id == 7){
                   
                    $newdados_notas[] = $alunos;
                    
                }
                       if($alunos->usuarios_id ==10313){
                      $newdados_notas[] = $alunos;
            }
            
        }
        
        
        // return response()->json($newdados_notas,200);
        if($dados_notas->nome_filial =="Filial Pedro Juan Caballero"){
            $nomeFIlial = 'Ciudad de Pedro Juan Caballero';
            $ciudad_filial = $dados_notas->nome_filial;
            $endereco = 'Calle Naciones Unidas Nº450';
        }else{
               $nomeFIlial = 'Ciudad Del Este';
                           $ciudad_filial = $dados_notas->nome_filial;
                      $endereco = 'Avda. Mcal. Estigarribia, Barrio Boquerón';
        }
     
        // return $this->date_entenso($dados_notas->fecha_inicio->finalexamen);
     
        
    //   return $this->date_entenso($fecha);
        
         $pdf=   $this->pdf->loadView('acta2', ['dados_notas' => (object)$newdados_notas, 'fecha_inicio' =>$fecha  ,
         'nome_materia' =>$dados_notas->nome_materia,
          'nome_turma' =>$dados_notas->nome_turma,
           'nome_filial' =>$nomeFIlial,
            'ciudad_filial' =>$ciudad_filial,
            'nome_semestre' =>$dados_notas->nome_semestre,
             'nome_periodo_anual' =>$dados_notas->nome_periodo_anual,
             'nome_professor'=>$dados_notas->nome_professor,
              'endereco'=>$endereco,
             'tipo_examen'=>$tipo_examen,
             'data_extenso' =>  $this->date_entenso($fecha),
              'nome_ano' =>$dados_notas->nome_ano,
             
             
             
             
         
         ]);
      
     
          return $pdf->download();
    }
    
    public function date_entenso($value){
         $date = date('Y-m-d', strtotime(str_replace('/', '-', $value)));
        $value = $date;
        // setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
        
        $mes = strftime('%m',  strtotime($value));
  
        $en = ['','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
$mes2=0;
        $pt = [',', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];  
        switch($mes){
            
            case '1':
            $mes2 = 'enero';
            break;
            
            case '2':
            $mes2 ='marzo';
            break;
            
            case '3':
            $mes2 ='marzo';
            break;
            
            case '4':
            $mes2 ='abril';
            break;
            
            case '5':
            $mes2 ='Mayo';
            break; 
            
            case '6':
            $mes2 ='junio';
            break; 
            
            case '7':
            $mes2 ='julio';
            break;
            
            case '8':
            $mes2 ='agosto';
            break;
            
            case '9':
            $mes2 ='septiembre';
            break;
            
            case '10':
            $mes2 ='octubre';
            break;
            
            case '11':
            $mes2 ='noviembre';
            break;
            
            case '12':
            $mes2 ='diciembre';
            break;
            
        }
       
      $dia   = strftime('%d',  strtotime($value));
         if(substr($dia, 0, 1) =='0'){
    $dia = substr($dia, 1, 2);
    // return $dia;
         }
         $ano = strftime('%Y',  strtotime($value));
         $extenso='';
         if($ano == 2020){
             $extenso = 'del año dos mil veinte ante mi';
         }
         if($ano == 2021){
             $extenso = 'del año dos mil veintiuno ante mi';
         }
         
        return ('a los '.$this->valorPorExtenso($dia).' días del mes de  '.$mes2.' '.$extenso);
    }
      public function gerarPdfAviso()
    {
          $pdf=   $this->pdf->loadView('mail/aviso');
           return $pdf->download();
    }
    
    
    
     function valorPorExtenso($number)
    {



    $hyphen      = '-';
    $conjunction = ' y ';
    $separator   = ', ';
    $negative    = 'menos ';
    $decimal     = ' ponto ';
    $dictionary  = array(
        0                   => 'cero',
        1                   => 'uno',
        2                   => 'dos',
        3                   => 'tres',
        4                   => 'cuatro',
        5                   => 'cinco',
        6                   => 'seis',
        7                   => 'siete',
        8                   => 'ocho',
        9                   => 'nueve',
        10                  => 'diez',
        11                  => 'once',
        12                  => 'doce',
        13                  => 'trece',
        14                  => 'catorce',
        15                  => 'quince',
        16                  => 'dieciséis',
        17                  => 'diecisiete',
        18                  => 'dieciocho',
        19                  => 'diecinueve',
        20                  => 'veinte',
        30                  => 'treinta',
        40                  => 'cuarenta',
        50                  => 'cincuenta',
        60                  => 'sesenta',
        70                  => 'setenta',
        80                  => 'ochenta',
        90                  => 'noventa',
        100                 => 'cien',
        200                 => 'docientos',
        300                 => 'trecientos',
        400                 => 'cuatrocientos',
        500                 => 'quinientos',
        600                 => 'seicientos',
        700                 => 'setecientos',
        800                 => 'ochocientos',
        900                 => 'novecientos',
        1000                => 'mil',
        1000000             => array('un', 'millón'),
        1000000000          => array('un', 'billón'),
        1000000000000       => array('un', 'trillón'),
        1000000000000000    => array('un', 'cuatrillón'),
        1000000000000000000 => array('un', 'quintillón')
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words só aceita números entre ' . PHP_INT_MAX . ' à ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $conjunction . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = floor($number / 100)*100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            if ($baseUnit == 1000) {
                $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[1000];
            } elseif ($numBaseUnits == 1) {
                $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit][0];
            } else {
                $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit][1];
            }
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}
    
}