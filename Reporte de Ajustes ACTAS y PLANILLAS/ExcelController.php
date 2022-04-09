<?php

namespace App\Http\Controllers\Medicina;

use App\Http\Controllers\Controller;

use App\Models\Medicina\RespostasPergunta;
use App\Models\Medicina\audit_log;
use App\Models\Medicina\Funcionario;

use App\Models\Medicina\Usuarios;
use Illuminate\Http\Request;
use JWTAuth;

use App\Http\Controllers\Medicina\ProfessorController;
use App\Http\Controllers\Medicina\UsuariosController;
use App\Http\Controllers\Medicina\TentativasController;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;


class ExcelController extends Controller
{

    protected $servico;
    protected $usuarios;
    protected $excel;
    protected $writer;
    protected $audit_log;
    protected $funcionario;
    protected $tentativacontroller;
      protected $professorcontroller;

    public function __construct(UsuariosController $usuariosController,TentativasController $tentativacontroller,ProfessorController $professorcontroller ,RespostasPergunta $servico, Usuarios $usuarios, Spreadsheet $excel, Xlsx $writer, audit_log $audit_log, Funcionario $funcionario)
    {
        $this->servico = $servico;
        $this->usuarios = $usuarios;
        $this->excel = $excel;
        $this->writer = $writer;
        $this->audit_log = $audit_log;
        $this->funcionario = $funcionario;
        $this->tentativacontroller = $tentativacontroller;
        $this->professorcontroller = $professorcontroller;
        $this->usuariosController = $usuariosController;
    }
    public function pegar_id(Request $request)
    {
    }

    public function tirarAcentos($string)
    {
        return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/", "/(Ç)/", "/(ç)/"), explode(" ", "a A e E i I o O u U n N C c"), $string);
    }

    public function gerarExcelExtraordinaria($id_turma)
    {
        // $id_oferta_disciplina = 2280;
        
        

        $todosasturmas = (object) $this->tentativacontroller->lista_notas_disciplina($id_turma)->original;


// if($todosasturmas->dados_notas ==""){
//     return response()->json('não ha notas nessa turma',500);
// }



        //  return response()->json($dados_notas, 200); 
    //     if( $dados_notas->dados_notas == ''){
    //   return response()->json('Não há notas nessa unidade', 500); 
    //     }
        
        
        $filename = 'Relatorio notas '.$todosasturmas->nome_turmatotal.'.xlsx';
        
        
        $indeci=0;
             $spreadsheet = new Spreadsheet();

        $spreadsheet->setActiveSheetIndex(0);
        foreach ($todosasturmas->tudonota as $dados_notas) {

// return response()->json($dados_notas, 200); 

            $sheet = $spreadsheet->getActiveSheet();


        $sheet->getRowDimension(1)->setRowHeight(20);
        $sheet->getRowDimension(10)->setRowHeight(100);
        $sheet->getColumnDimension('A')->setWidth(4);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(20);

        $sheet->getColumnDimension('D')->setWidth(11);
        $sheet->getColumnDimension('E')->setWidth(11);
        $sheet->getColumnDimension('F')->setWidth(11);
        $sheet->getColumnDimension('G')->setWidth(11);
        $sheet->getColumnDimension('H')->setWidth(11);
        $sheet->getColumnDimension('I')->setWidth(11);

        $sheet->getColumnDimension('M')->setWidth(13);
        $sheet->getColumnDimension('N')->setWidth(13);
        $sheet->getColumnDimension('O')->setWidth(12);


// for($i=0;$i<10;$i++){




        //////////////////////////ESTILIZAR BORDAS////////////////////////////////




     

        // $styleArray = [
        //     'borders' => [
        //         'bottom' => [
        //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
        //             'color' => ['argb' => '00000000'],
        //         ],
        //     ],
        // ];
        // $sheet->getStyle('C1:L7')->applyFromArray($styleArray);




        //=======================================================================//



        $IMG = 'https://api.ucpvirtual.com.br/storage/documentos/logo.jpeg';
        $row_num = 1;
        if (isset($IMG) && !empty($IMG)) {
            $imageType = "png";

            if (strpos($IMG, ".png") === false) {
                $imageType = "jpg";
            }

            $drawing = new MemoryDrawing();
            // $sheet->getColumnDimension('A')->getRowDimension($row_num)->setWidth(10);
            // $sheet->getRowDimension($row_num)->setRowHeight(50);
            $sheet->mergeCells('A1:B7');
            $gdImage = ($imageType == 'png') ? imagecreatefrompng($IMG) : imagecreatefromjpeg($IMG);

            $drawing->setResizeProportional(false);
            $drawing->setImageResource($gdImage);
            $drawing->setRenderingFunction(\PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::RENDERING_JPEG);
            $drawing->setMimeType(\PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::MIMETYPE_DEFAULT);
            $drawing->setWidth(160);
            $drawing->setHeight(130);
            $drawing->setOffsetX(80);
            $drawing->setOffsetY(10);
            // $drawing->setCoordinates('C'.$row_num);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());
            //$row_num++;
        }



        ////////////////parte superior meio////////////////////////




        $sheet->setCellValue('C1', 'FACULTAD DE CIENCIAS DE LA SALUD');

        $sheet->getStyle("C1:L1")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C1:L1")->getFont()->setSize(18);
        $sheet->mergeCells('C1:L1');


        $sheet->setCellValue('C2', 'CARRERA DE MEDICINA');
        $sheet->getStyle("C2:L2")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C2:L2")->getFont()->setSize(14);
        $sheet->mergeCells('C2:L2');


        $sheet->setCellValue('C3', $dados_notas->nome_filial);

        $sheet->getStyle("C3:L3")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C3:L3")->getFont()->setSize(12);
        $sheet->mergeCells('C3:L3');

        ////////////////parte superior meio////////////////////////


        ///////////// ultima parte do cabecalho superior direiro///////////////

        $sheet->setCellValue('M1', 'Escala');
        $sheet->getStyle("M1:O1")->getAlignment()->setHorizontal('center');
        $sheet->mergeCells('M1:O1');

        $sheet->setCellValue('M2', '100');
        $sheet->getStyle("M2")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("M2")->getFont()->setBold(true);

        //      $sheet->setCellValue('M2','100');
        //   $sheet->getStyle("M2")->getAlignment()->setHorizontal('center');
        $sheet->mergeCells('N2:O2');

        $sheet->setCellValue('M3', '1');
        $sheet->getStyle("M3:M7")->getAlignment()->setHorizontal('center');
        $sheet->setCellValue('M4', '60');
        $sheet->setCellValue('M5', '70');
        $sheet->setCellValue('M6', '81');
        $sheet->setCellValue('M7', '91');
        

        $sheet->setCellValue('N3', '59');
        $sheet->getStyle("N3:N7")->getAlignment()->setHorizontal('center');
        $sheet->setCellValue('N4', '69');
        $sheet->setCellValue('N5', '80');
        $sheet->setCellValue('N6', '90');
        $sheet->setCellValue('N7', '100');
        

        $sheet->setCellValue('O3', '1');
        $sheet->getStyle("O3:O7")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("O3:O7")->getFont()->setBold(true);
        $sheet->setCellValue('O4', '2');
        $sheet->setCellValue('O5', '3');
        $sheet->setCellValue('O6', '4');
        $sheet->setCellValue('O7', '5');
        



        ///////////// ultima parte do cabecalho superior direiro///////////////



        //////////////////////////Meio informacoes /////////////////////////////////////


        $sheet->setCellValue('C4', "Aprobado por Ley Nº 3153/06");
        $sheet->mergeCells('C4:L4');
        $sheet->getStyle("C4:L4")->getFont()->setSize(7);

        $sheet->setCellValue('C5', "ASIGNATURA: ".$dados_notas->nome_materia);
        $sheet->mergeCells('C5:L5');


        $sheet->setCellValue('C6', "Profesor/a: ".$dados_notas->nome_professor);
        $sheet->mergeCells('C6:L6');


        $sheet->setCellValue('C7', "Carrera: Medicina ");
        $sheet->mergeCells('C7:L7');



//------------------------------------------------------comeca aqui------------------------------------------------------------------------------


        $sheet->setCellValue('A8', ''.$dados_notas->nome_semestre.' '."'".$dados_notas->nome_turma."'".$dados_notas->nome_periodo_anual.' '.$dados_notas->nome_ano);
        $sheet->mergeCells('A8:B8');
        $sheet->getStyle("A8:B8")->getFont()->setSize(12);

        $sheet->setCellValue('C8', "Planilla de Proceso de la Valoración de los Aprendizajes-  Primeira Convocatoria 2021  ");
        $sheet->mergeCells('C8:P8');
        $sheet->getStyle("C8:P8")->getFont()->setSize(16);
        $sheet->getStyle("C8")->getAlignment()->setHorizontal('center');



        /////////////////////////////////////////////////////////////////////////////////////


        //////////////////////parte meio inferior em baixo da imagem ////////////////////////


        $sheet->setCellValue('A9', 'Nº');
        $sheet->mergeCells('A9:A13');
        $sheet->getStyle("A9")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("A9")->getAlignment()->setVertical('center');
        $sheet->getStyle("A9:A13")->getFont()->setSize(7);

        $sheet->setCellValue('B9', 'Nombres y Apellidos');
        $sheet->mergeCells('B9:B12');
        $sheet->getStyle("B9")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("B9")->getAlignment()->setVertical('center');
        $sheet->getStyle("B9:B13")->getFont()->setSize(8);




        $sheet->setCellValue('C9', 'Documento de Identidad');
            $sheet->mergeCells('C9:C12');
        $sheet->getStyle("C9")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C9")->getAlignment()->setVertical('center');
        $sheet->getStyle("C9:C13")->getFont()->setSize(8);



        $sheet->setCellValue('D9', 'Reactivos de Evaluación de Proceso');
        $sheet->mergeCells('D9:I9');
        $sheet->getStyle("D9")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("D9")->getFont()->setBold(true);
        $sheet->getStyle("D9:I9")->getFont()->setSize(14);


        ///////////////////////////////////////////////////////////////////////////////

        $sheet->setCellValue('D10', '1 Prueba de Proceso');

        $sheet->setCellValue('E10', '1  Prueba parcial');

        $sheet->setCellValue('F10', '2 Prueba de Proceso');
        $sheet->setCellValue('G10', '2 Prueba Parcial');
        $sheet->setCellValue('H10', 'Trabajo Práctico');
        $sheet->setCellValue('I10', 'Extensión  ');

        $sheet->setCellValue('J10', 'Total de Puntos Logrados');
        $sheet->getStyle('D10:J10')->getAlignment()->setWrapText(true);





        ///////////////// configuracoes 

        $sheet->getStyle('D10:J10')->getAlignment()->setTextRotation(90);
        $sheet->getStyle('D10:I10')->getAlignment()->setWrapText(true);
        $sheet->getStyle("D12:H12")->getFont()->setSize(9);
        $sheet->getStyle("D10:J10")->getFont()->setSize(12);
        $sheet->getStyle("D10:O10")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("D10:O10")->getAlignment()->setVertical('center');
        $sheet->getStyle('O10')->getAlignment()->setTextRotation(90);
        $sheet->getStyle('O10')->getAlignment()->setTextRotation(90);
        $sheet->getStyle('K10:L10')->getAlignment()->setTextRotation(90);



      
 
        $sheet->setCellValue('D11', 'Fecha '.$dados_notas->fecha_inicio->processo1);
       
        $sheet->setCellValue('E11', 'Fecha '.$dados_notas->fecha_inicio->parcial1);
      
        $sheet->setCellValue('F11', 'Fecha '.$dados_notas->fecha_inicio->processo2);
          
        $sheet->setCellValue('G11', 'Fecha '.$dados_notas->fecha_inicio->parcial2);
              $sheet->setCellValue('M11', 'Fecha '.$dados_notas->fecha_inicio->extraordinaria);
     
       if($dados_notas->dataTrabalho){
            $sheet->setCellValue('H11', 'fecha '.$dados_notas->dataTrabalho);
       }
         
        // $sheet->setCellValue('I11', 'Fecha /06/20');
        $sheet->getStyle('D11:I11')->getAlignment()->setWrapText(true);
        $sheet->getStyle('D11:I11')->getAlignment()->setHorizontal('center');


        $sheet->setCellValue('D12', '1 Proceso ');
        $sheet->setCellValue('E12', '1 Parcial ');
        $sheet->setCellValue('F12', '2 Proceso');
        $sheet->setCellValue('G12', '2 Parcial');
        $sheet->setCellValue('H12', 'Trab. Prác.');
        $sheet->setCellValue('I12', 'Extensión');
        $sheet->getStyle('D12:I12')->getAlignment()->setWrapText(true);
        $sheet->setCellValue('J11', 'Fecha Tope 00/00/2020');
        $sheet->mergeCells('J11:J12');
        // $sheet->getStyle('J11:J12')->getAlignment()->setWrapText(true);
        $sheet->getStyle("J11:J12")->getAlignment()->setHorizontal('center');



        ///////////////////////////////////////meio esquedo/////////////////////////////////
        $sheet->setCellValue('K10', '% de la ETAPA');
        $sheet->getStyle("K10")->getFont()->setBold(true);
        $sheet->getStyle('K10')->getAlignment()->setWrapText(true);
        $sheet->getStyle("J11:J12")->getAlignment()->setVertical('center');

        $sheet->mergeCells('K10:K12');
        
        

        $sheet->setCellValue('L10', '% Asist.');
        $sheet->getStyle('L10')->getAlignment()->setWrapText(true);
        $sheet->mergeCells('L10:L12');
               $sheet->getStyle("L10")->getFont()->setBold(true);
   
        $sheet->getStyle("L10:L12")->getAlignment()->setVertical('center');



        $sheet->setCellValue('M10', 'Examen Extraordinaria');
        $sheet->getStyle('M10')->getAlignment()->setWrapText(true);

   



        $sheet->setCellValue('N10', 'Total de Puntos Acumulados en el Parcial + Total de Puntos Logrados en el Examen Fina');
        $sheet->getStyle('N10')->getAlignment()->setWrapText(true);
        $sheet->mergeCells('N10:N12');
        $sheet->getStyle("N10")->getFont()->setSize(9);


        $sheet->setCellValue('O10', 'Calificación Final');
        $sheet->getStyle("O10")->getFont()->setBold(true);
        $sheet->mergeCells('O10:O12');
        $sheet->getStyle("O10")->getFont()->setSize(11);



   
        // $sheet->getStyle("D9")->getFont()->setBold(true);
        // $sheet->getStyle("D9:I15")->getFont()->setSize(10);


        $sheet->getStyle('L11:M11')->getAlignment()->setWrapText(true);

        // $sheet->setCellValue('M11', 'Fecha /07/20')->setCellValue('N11', 'Fecha 04/07/21');
                
                
                if(isset($dados_notas->fecha_inicio->extraorfinaria))
                {$sheet->setCellValue('M11','Fecha '. $dados_notas->fecha_inicio->extraordinaria);
          
                }
          
        $sheet->getStyle("M11")->getAlignment()->setHorizontal('center');


        $sheet->getStyle("L12:M12")->getFont()->setSize(8);
        $sheet
            ->setCellValue('M12', 'Final Escrito');




        $sheet->setCellValue('B13', 'Prueba')
            ->setCellValue('D13', '5')
            ->setCellValue('E13', '15')
            ->setCellValue('F13', '5')
            ->setCellValue('G13', '15')
            ->setCellValue('H13', '10')
            ->setCellValue('I13', '2')
            ->setCellValue('J13', '50')
            ->setCellValue('K13', '%')
            ->setCellValue('L13', '%')
            ->setCellValue('M13', '100')
            ->setCellValue('N13', '100')
            ->setCellValue('O13', '5');


        //          $sheet->getStyle('B13:O1')->applyFromArray(
        //     array(
        //         'fill' => array(
        //             'type' => PHPExcel_Style_Fill::FILL_SOLID,
        //             'color' => array('rgb' => 'FF0000')
        //         )
        //     )
        // );

        $sheet->mergeCells('B13:C13');
        $sheet->getStyle('B13:O13')->getAlignment()->SetHorizontal('center');
        $sheet->getStyle('B13')->getAlignment()->SetHorizontal('center');

        $index=1;
        $i= 14;
        $cont2=0;
        $status=0;

$nota_final_extraordinaria='';
//   return response()->json($dados_notas->dados_notas, 200);  
        ///parte php forech
  foreach($dados_notas->dados_notas as $element){
      
      if($element->extraordinaria >0){
  $soma=0;
             $cont2++;
                $sheet->getRowDimension($i)->setRowHeight(20);
             if($element->extraordinaria >=1 and $element->extraordinaria <=59){
           $calificacion = 1;
           }else if($element->extraordinaria >=60 and $element->extraordinaria <=69){
           $calificacion = 2;
           }else if($element->extraordinaria >=70 and $element->extraordinaria <=80){
           $calificacion = 3;
           }else if($element->extraordinaria >=80 and $element->extraordinaria <=90){
           $calificacion = 4;
           
               
           }else if($element->extraordinaria >=91 and $element->extraordinaria <=100){
                $calificacion = 5;
           }   
           
         $sheet->setCellValue('O'.$i, $calificacion);
         
           
           
           
              $sheet->setCellValue('A'.$i, ''.$index);
              $sheet->setCellValue('B'.$i, $element->nome_usuario);
                 $sheet->setCellValue('C'.$i, $element->doc_oficial);
            
                    //   $sheet->setCellValue('J'.$i, '=D'.$i.'+E'.$i.'+F'.$i.'+G'.$i.'+H'.$i.'+I'.$i.'');

                      $sheet->setCellValue('N'.$i, $element->extraordinaria);
                        $sheet->setCellValue('M'.$i, $element->extraordinaria);
                 
         
              
             
                 if($cont2 == 17 & $status == 0 ){
                     
                     $sheet->getRowDimension(intval($i+1))->setRowHeight(80);
        $sheet->setCellValue('A'.intval($i+1), 'Firma del Docente');
        
        $sheet->setCellValue('A'.intval($i+2), 'Observación. Esta planilla representará la habilitación a la Evaluación Final y la Calificación Final del Semestre. S.A.M.C.');
        $sheet->mergeCells('A'.intval($i+2).':H'.intval($i+2));
            
        $i = $i +2;
        $cont2=0;    
        $status=1;
         $sheet->setBreak('A'.$i, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
              }
                 if($cont2 == 22){
                         $sheet->getRowDimension(intval($i+1))->setRowHeight(80);
        $sheet->setCellValue('A'.intval($i+1), 'Firma del Docente');
        $sheet->setCellValue('A'.intval($i+2), 'Observación. Esta planilla representará la habilitación a la Evaluación Final y la Calificación Final del Semestre. S.A.M.C.');
        $sheet->mergeCells('A'.intval($i+2).':H'.intval($i+2));
        $i = $i +2;
        $cont2=0;
        $sheet->setBreak('A'.$i, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
              }
              $index++;
               $i++;
        }}
          if($cont2 > 0 and  $cont2 < 22){
                  $sheet->getRowDimension(intval($i))->setRowHeight(80);
        $sheet->setCellValue('A'.intval($i), 'Firma del Docente');
        $sheet->setCellValue('A'.intval($i+1), 'Observación. Esta planilla representará la habilitación a la Evaluación Final y la Calificación Final del Semestre. S.A.M.C.');
        $sheet->mergeCells('A'.intval($i+1).':H'.intval($i+1));
          }
    
      
            $footer =  22 - $cont2;
              if($cont2 > 10){
          $footer = 15 + $cont2;
      }
  
                      $sheet->mergeCells('C'.intval($i+$footer+1).':D'.intval($i+$footer+1));
                  $sheet->mergeCells('C'.intval($i+$footer).':D'.intval($i+$footer));
          $sheet->setCellValue('B'.intval($i+$footer), '.................................................');
          $sheet->setCellValue('C'.intval($i+$footer), '.................................................');
          $sheet->setCellValue('G'.intval($i+$footer), '.................................................');
          $sheet->setCellValue('K'.intval($i+$footer), '.................................................'); 
          $sheet->setCellValue('O'.intval($i+$footer), '.................................................');
           
                $sheet->setCellValue('B'.intval($i+$footer+1), 'Docente');
        $sheet->setCellValue('C'.intval($i+$footer+1), 'Secretaria Academica');
          $sheet->setCellValue('G'.intval($i+$footer+1), 'Diretor de carrera');
          $sheet->setCellValue('K'.intval($i+$footer+1), 'Diretor General de filial'); 
             
          $sheet->setCellValue('O'.intval($i+$footer+1), 'Secretaria General Academica');
           
      
               
                      $sheet->getStyle('A'.intval($i+$footer+1).':P'.intval($i+$footer+1))->getFont()->setSize(12);
        
            // $sheet->mergeCells('M'.intval($i+1).':O'.intval($i+1));
            
        $sheet->getStyle('A'.intval($i+$footer+1).':O'.intval($i+$footer+1))->getAlignment()->setHorizontal('center');
          $sheet->getStyle('A'.intval($i+$footer).':O'.intval($i+$footer))->getAlignment()->setHorizontal('center');
            
 

    
$sheet->getStyle('D13:O13')->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('7cd5dc');
  
    $sheet->getStyle('j14:J'.intval($i-1))->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('ffff00');
    
     $sheet->getStyle('O14:O'.$i)->getAlignment()->setHorizontal('center');
    $sheet->getStyle('O14:O'.intval($i-1))->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('ffff00');
    
      $sheet->getStyle('K14:K'.$i)->getAlignment()->setHorizontal('center');
    $sheet->getStyle('K14:K'.$i)->getFont()->setSize(8);
$sheet->getStyle('K14:K'.$i)
    ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        //  $spreadsheet2 = $spreadsheet;
        
    //     $sheet->getStyle('K14:K'.$i)->getNumberFormat()
    // ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
      $sheet->getStyle('C14:C'.$i)->getNumberFormat()
    ->setFormatCode('0');
        //   $sheet->getStyle('C14:C'.$i)->getAlignment()->setHorizontal('center');
          
              $sheet->getStyle('C14:J'.$i)->getAlignment()->setHorizontal('center');
   $sheet
->getPageSetup()
    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
    
    
    
    


 $sheet->getPageSetup()->setPrintArea('A1:P'.intval($i+$footer+1));



    

    $sheet
    ->getHeaderFooter()->setEvenFooter( '&RPage &P of &N');


// }

   $pagina = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];
        if($cont2 == 0){
            $i = $i -2;
        }
        $sheet->getStyle('A1:P'.intval($i+1))->applyFromArray($pagina);
        
           $pagina2 = [
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];
        $sheet->getStyle('A'.intval($i+1).':P'.intval($i+$footer+1))->applyFromArray($pagina2);
        
        

       $sheet->getPageSetup()
        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(8, 13);


           $sheet->setTitle(substr($dados_notas->nome_materia,0,30));
              
                $indeci++;
   if(count($todosasturmas->tudonota) > $indeci){
            $spreadsheet->createSheet();

            // Add some data to the second sheet, resembling some different data types
       
         
            $spreadsheet->setActiveSheetIndex($indeci);
}
            

 
}

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
       
        $writer = new Xlsx($spreadsheet);
        $writer->save('relatorio.xlsx');
        
        $file="relatorio.xlsx";// tive que fazer assim, estava dando erro no retorno
        return response()->download(public_path($file));
        
        
    }

    public function gerarRelatorioAutorizacao(Request $request)
    {
        $filename = 'Relatorio Liberacoes';
        $index = 0;

        $dados_funcionarios = $this->retornar()->original;
        //   return response()->json($dados_funcionarios[0]->nome, 200);

        $spreadsheet = new Spreadsheet();

        $spreadsheet->setActiveSheetIndex(0);
        foreach ($dados_funcionarios as $element) {


            $sheet = $spreadsheet->getActiveSheet();


            $sheet->getColumnDimension('A')->setWidth(30);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(12);
            $sheet->getStyle("A:C")->getAlignment()->setHorizontal('left');

            $sheet->setCellValue('A1', 'RELATORIO LIBERAÇÃO AVALIACAO');
            $sheet->getStyle("A1")->getFont()->setSize(14);
            $sheet->mergeCells('A1:D2');



            $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');


            $dados_funcionario = $this->funcionario

                ->where('funcionario.id', $element->funcionario_id)
                ->join('numero_catraca', 'numero_catraca.usuarios_id', 'funcionario.id')
                ->select('nome', 'sobrenome', 'numero_catraca')
                ->first();

            $sheet->setCellValue('A3', 'Nome Funcionario')->setCellValue('C3', 'Catraca Funcionario');
            $sheet->getStyle("A3:C3")->getFont()->setBold(true);

            $sheet->getStyle("A3:C3")->getFont()->setSize(11);
            $sheet->mergeCells('A3:B3');
            $sheet->mergeCells('C3:D3');
            $sheet->setCellValue('A4', $dados_funcionario->nome . ' ' . $dados_funcionario->sobrenome);

            $sheet->getStyle("A4")->getFont()->setSize(10);
            $sheet->mergeCells('A4:B4');

            $sheet->setCellValue('C4', $dados_funcionario->numero_catraca);

            $sheet->getStyle("C4")->getFont()->setSize(10);
            $sheet->mergeCells('C4:D4');

            $sheet->setCellValue('A6', 'Nome Aluno')->setCellValue('B6', 'Data de Modificacal')->setCellValue('C6', 'Numero Catraca')->setCellValue('D6', 'Status');
            $sheet->getStyle("A6:D6")->getFont()->setBold(true);


            $data_inicio = $request->data_inicio;
            $data_fim = $request->data_fim;

            $data = $this->audit_log
                ->join('funcionario', 'funcionario.id', 'audit_log.modified_by_user_id')
                ->join('usuarios', 'usuarios.id', 'audit_log.id_affected')
                ->join('numero_catraca', 'numero_catraca.usuarios_id', 'usuarios.id')
                ->where('funcionario.id', $element->funcionario_id)
                ->orderBy('usuarios.nome_sobrenome')


                //   ->where('field_affected','["liberacao_avaliacao"]')
                ->where('field_affected', '["liberacao_biometria"]')
                ->where('modified', '>=', date('Y-m-d H:i:s', strtotime($data_inicio . '00:00:00')))
                ->where('modified', '<=', date('Y-m-d H:i', strtotime($data_fim . ' 23:59:00')))
                ->select('usuarios.id as usuario_id','usuarios.nome_sobrenome as nome_aluno', 'field_affected', 'audit_log.modified', 'numero_catraca', 'new_value')
                ->get();


// return $data;





            $i = 6;

            foreach ($data as $item) {
                $i++;
                $sheet->setCellValue('A' . $i, $item->nome_aluno);
                $sheet->setCellValue('B' . $i, $item->modified);
                $sheet->setCellValue('C' . $i, $item->numero_catraca);
                if ($item->new_value == "[\"1\"]") {
                    $sheet->setCellValue('D' . $i, 'Liberado');
                }
                if ($item->new_value == "[\"0\"]") {
                    $sheet->setCellValue('D' . $i, 'Bloqueado');
                }
            }

            $pagina = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '00000000'],
                    ],
                ],
            ];
            $sheet->getStyle('A1:D' . $i)->applyFromArray($pagina);

            $sheet->setTitle(' ' . $dados_funcionario->nome);
            $spreadsheet->createSheet();

            // Add some data to the second sheet, resembling some different data types
            $index++;
            $spreadsheet->setActiveSheetIndex($index);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx' . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);


        $writer->save('php://output');
    }

    public function retornar()
    {

        $data = $this->audit_log
            ->join('funcionario', 'funcionario.id', 'audit_log.modified_by_user_id')
            ->groupBy('audit_log.modified_by_user_id')
            ->where('field_affected', '["liberacao_biometria"]')
            ->where('funcionario.id', '!=', '5')
            ->where('funcionario.id', '!=', '127')
            ->select('funcionario.id as funcionario_id', 'funcionario.nome', 'funcionario.sobrenome')
            ->get();


        return response()->json($data, 200);
    }
    public function gerarExcelOfertaDisciplina($id_oferta_disciplina)
    {
        // $id_oferta_disciplina = 2280;

        $dados_notas = (object) $this->tentativacontroller->lista_notas_disciplina2($id_oferta_disciplina)->original;

        //  return response()->json($dados_notas, 200); 
        if( $dados_notas->dados_notas == ''){
       return response()->json('Não há notas nessa unidade', 500); 
        }
        
        
        $filename = 'Relatorio notas '.$dados_notas->nome_materia.' '.$dados_notas->nome_turma.' .xlsx';
        $spreadsheet = new Spreadsheet();


        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getRowDimension(1)->setRowHeight(20);
        $sheet->getRowDimension(10)->setRowHeight(100);
        $sheet->getColumnDimension('A')->setWidth(4);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(20);

        $sheet->getColumnDimension('D')->setWidth(11);
        $sheet->getColumnDimension('E')->setWidth(11);
        $sheet->getColumnDimension('F')->setWidth(11);
        $sheet->getColumnDimension('G')->setWidth(11);
        $sheet->getColumnDimension('H')->setWidth(11);
        $sheet->getColumnDimension('I')->setWidth(11);

        $sheet->getColumnDimension('M')->setWidth(13);
        $sheet->getColumnDimension('N')->setWidth(13);
        $sheet->getColumnDimension('O')->setWidth(12);


// for($i=0;$i<10;$i++){




        //////////////////////////ESTILIZAR BORDAS////////////////////////////////




     

        // $styleArray = [
        //     'borders' => [
        //         'bottom' => [
        //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
        //             'color' => ['argb' => '00000000'],
        //         ],
        //     ],
        // ];
        // $sheet->getStyle('C1:L7')->applyFromArray($styleArray);




        //=======================================================================//



        $IMG = 'https://api.ucpvirtual.com.br/storage/documentos/logo.jpeg';
        $row_num = 1;
        if (isset($IMG) && !empty($IMG)) {
            $imageType = "png";

            if (strpos($IMG, ".png") === false) {
                $imageType = "jpg";
            }

            $drawing = new MemoryDrawing();
            // $sheet->getColumnDimension('A')->getRowDimension($row_num)->setWidth(10);
            // $sheet->getRowDimension($row_num)->setRowHeight(50);
            $sheet->mergeCells('A1:B7');
            $gdImage = ($imageType == 'png') ? imagecreatefrompng($IMG) : imagecreatefromjpeg($IMG);

            $drawing->setResizeProportional(false);
            $drawing->setImageResource($gdImage);
            $drawing->setRenderingFunction(\PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::RENDERING_JPEG);
            $drawing->setMimeType(\PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::MIMETYPE_DEFAULT);
            $drawing->setWidth(160);
            $drawing->setHeight(130);
            $drawing->setOffsetX(80);
            $drawing->setOffsetY(10);
            // $drawing->setCoordinates('C'.$row_num);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());
            //$row_num++;
        }



        ////////////////parte superior meio////////////////////////




        $sheet->setCellValue('C1', 'FACULTAD DE CIENCIAS DE LA SALUD');

        $sheet->getStyle("C1:M1")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C1:M1")->getFont()->setSize(18);
        $sheet->mergeCells('C1:M1');


        $sheet->setCellValue('C2', 'CARRERA DE MEDICINA');
        $sheet->getStyle("C2:M2")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C2:M2")->getFont()->setSize(14);
        $sheet->mergeCells('C2:M2');


        $sheet->setCellValue('C3', $dados_notas->nome_filial);

        $sheet->getStyle("C3:M3")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C3:M3")->getFont()->setSize(12);
        $sheet->mergeCells('C3:M3');

        ////////////////parte superior meio////////////////////////


        ///////////// ultima parte do cabecalho superior direiro///////////////

        $sheet->setCellValue('N1', 'Escala');
        $sheet->getStyle("N1:P1")->getAlignment()->setHorizontal('center');
        $sheet->mergeCells('N1:P1');

        $sheet->setCellValue('N2', '100');
        $sheet->getStyle("N2")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("N2")->getFont()->setBold(true);

        //      $sheet->setCellValue('M2','100');
        //   $sheet->getStyle("M2")->getAlignment()->setHorizontal('center');
        $sheet->mergeCells('O2:P2');

        $sheet->setCellValue('N3', '1');
        $sheet->getStyle("N3:N7")->getAlignment()->setHorizontal('center');
        $sheet->setCellValue('N4', '60');
        $sheet->setCellValue('N5', '70');
        $sheet->setCellValue('N6', '81');
        $sheet->setCellValue('N7', '91');
        

        $sheet->setCellValue('O3', '59');
        $sheet->getStyle("O3:O7")->getAlignment()->setHorizontal('center');
        $sheet->setCellValue('O4', '69');
        $sheet->setCellValue('O5', '80');
        $sheet->setCellValue('O6', '90');
        $sheet->setCellValue('O7', '100');
        

        $sheet->setCellValue('P3', '1');
        $sheet->getStyle("P3:P7")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("P3:P7")->getFont()->setBold(true);
        $sheet->setCellValue('P4', '2');
        $sheet->setCellValue('P5', '3');
        $sheet->setCellValue('P6', '4');
        $sheet->setCellValue('P7', '5');
        



        ///////////// ultima parte do cabecalho superior direiro///////////////



        //////////////////////////Meio informacoes /////////////////////////////////////


        $sheet->setCellValue('C4', "Aprobado por Ley Nº 3153/06");
        $sheet->mergeCells('C4:M4');
        $sheet->getStyle("C4:M4")->getFont()->setSize(7);

        $sheet->setCellValue('C5', "ASIGNATURA: ".$dados_notas->nome_materia);
        $sheet->mergeCells('C5:M5');


        $sheet->setCellValue('C6', "Profesor/a: ".$dados_notas->nome_professor);
        $sheet->mergeCells('C6:M6');


        $sheet->setCellValue('C7', "Carrera: Medicina ");
        $sheet->mergeCells('C7:M7');



//------------------------------------------------------comeca aqui------------------------------------------------------------------------------


    $sheet->setCellValue('A8', ''.$dados_notas->nome_semestre.' '."'".$dados_notas->nome_turma."'".$dados_notas->nome_periodo_anual.' '.$dados_notas->nome_ano);
       
        $sheet->mergeCells('A8:B8');
        $sheet->getStyle("A8:B8")->getFont()->setSize('12');

        $sheet->setCellValue('C8', "Planilla de Proceso de la Valoración de los Aprendizajes-  Primeira Convocatoria 2021  ");
        $sheet->mergeCells('C8:P8');
        $sheet->getStyle("C8:P8")->getFont()->setSize(16);
        $sheet->getStyle("C8")->getAlignment()->setHorizontal('center');



        /////////////////////////////////////////////////////////////////////////////////////


        //////////////////////parte meio inferior em baixo da imagem ////////////////////////


        $sheet->setCellValue('A9', 'Nº');
        $sheet->mergeCells('A9:A13');
        $sheet->getStyle("A9")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("A9")->getAlignment()->setVertical('center');
        $sheet->getStyle("A9:A13")->getFont()->setSize(7); //"A9:A13"MODIFICADO DE A14 PARA A13

        $sheet->setCellValue('B9', 'Nombres y Apellidos');
        $sheet->mergeCells('B9:B12');
        $sheet->getStyle("B9")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("B9")->getAlignment()->setVertical('center');
        $sheet->getStyle("B9:B13")->getFont()->setSize(8);




        $sheet->setCellValue('C9', 'Documento de Identidad');
            $sheet->mergeCells('C9:C12');
        $sheet->getStyle("C9")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C9")->getAlignment()->setVertical('center');
        $sheet->getStyle("C9:C13")->getFont()->setSize(8);



        $sheet->setCellValue('D9', 'Reactivos de Evaluación de Proceso');
        $sheet->mergeCells('D9:I9');
        $sheet->getStyle("D9")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("D9")->getFont()->setBold(true);
        $sheet->getStyle("D9:I9")->getFont()->setSize(14);


        ///////////////////////////////////////////////////////////////////////////////

        $sheet->setCellValue('D10', '1 Prueba de Proceso');

        $sheet->setCellValue('E10', '1  Prueba parcial');

        $sheet->setCellValue('F10', '2 Prueba de Proceso');
        $sheet->setCellValue('G10', '2 Prueba Parcial');
        $sheet->setCellValue('H10', 'Trabajo Práctico');
        $sheet->setCellValue('I10', 'Extensión  ');

        $sheet->setCellValue('J10', 'Total de Puntos Logrados');
        $sheet->getStyle('D10:J10')->getAlignment()->setWrapText(true);





        ///////////////// configuracoes 

        $sheet->getStyle('D10:J10')->getAlignment()->setTextRotation(90);
        $sheet->getStyle('D10:I10')->getAlignment()->setWrapText(true);
        $sheet->getStyle("D12:H12")->getFont()->setSize(9);
        $sheet->getStyle("D10:J10")->getFont()->setSize(12);
        $sheet->getStyle("D10:P10")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("D10:P10")->getAlignment()->setVertical('center');
        $sheet->getStyle('P10')->getAlignment()->setTextRotation(90);
        $sheet->getStyle('P10')->getAlignment()->setTextRotation(90);
        $sheet->getStyle('K10:L10')->getAlignment()->setTextRotation(90);



      
 
        $sheet->setCellValue('D11', 'Fecha '.$dados_notas->fecha_inicio->processo1);
       
        $sheet->setCellValue('E11', 'Fecha '.$dados_notas->fecha_inicio->parcial1);
      
        $sheet->setCellValue('F11', 'Fecha '.$dados_notas->fecha_inicio->processo2);
          
        $sheet->setCellValue('G11', 'Fecha '.$dados_notas->fecha_inicio->parcial2);
  
          $sheet->setCellValue('H11', 'fecha '.$dados_notas->dataTrabalho);
        // $sheet->setCellValue('I11', 'Fecha /06/20');
        $sheet->getStyle('D11:I11')->getAlignment()->setWrapText(true);
        $sheet->getStyle('D11:I11')->getAlignment()->setHorizontal('center');


        $sheet->setCellValue('D12', '1 Proceso ');
        $sheet->setCellValue('E12', '1 Parcial ');
        $sheet->setCellValue('F12', '2 Proceso');
        $sheet->setCellValue('G12', '2 Parcial');
        $sheet->setCellValue('H12', 'Trab. Prác.');
        $sheet->setCellValue('I12', 'Extensión');
        $sheet->getStyle('D12:I12')->getAlignment()->setWrapText(true);
        $sheet->setCellValue('J11', 'Fecha Tope 00/00/2020');
        $sheet->mergeCells('J11:J12');
        // $sheet->getStyle('J11:J12')->getAlignment()->setWrapText(true);
        $sheet->getStyle("J11:J12")->getAlignment()->setHorizontal('center');



        ///////////////////////////////////////meio esquedo/////////////////////////////////
        $sheet->setCellValue('K10', '% de la ETAPA');
        $sheet->getStyle("K10")->getFont()->setBold(true);
        $sheet->getStyle('K10')->getAlignment()->setWrapText(true);
        $sheet->getStyle("J11:J12")->getAlignment()->setVertical('center');

        $sheet->mergeCells('K10:K12');
        
        

        $sheet->setCellValue('L10', '% Asist.');
        $sheet->getStyle('L10')->getAlignment()->setWrapText(true);
        $sheet->mergeCells('L10:L12');
               $sheet->getStyle("L10")->getFont()->setBold(true);
   
        $sheet->getStyle("L10:L12")->getAlignment()->setVertical('center');



        $sheet->setCellValue('M10', 'Examen final Ordinario 1ra Mesa');
        $sheet->getStyle('M10')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('N10', 'Examen Final Complementário 2da Mesa');
        $sheet->getStyle('N10')->getAlignment()->setWrapText(true);




        $sheet->setCellValue('O10', 'Total de Puntos Acumulados en el Parcial + Total de Puntos Logrados en el Examen Fina');
        $sheet->getStyle('O10')->getAlignment()->setWrapText(true);
        $sheet->mergeCells('O10:O12');
        $sheet->getStyle("O10")->getFont()->setSize(9);



        $sheet->setCellValue('P10', 'Calificación Final');
        $sheet->getStyle("P10")->getFont()->setBold(true);
        $sheet->mergeCells('P10:P13');
        $sheet->getStyle("p10")->getFont()->setSize(11);



   
        // $sheet->getStyle("D9")->getFont()->setBold(true);
        // $sheet->getStyle("D9:I15")->getFont()->setSize(10);


        $sheet->getStyle('L11:N11')->getAlignment()->setWrapText(true);

      
               
        $sheet->getStyle("M11:N11")->getAlignment()->setHorizontal('center');


        $sheet->getStyle("L12:N12")->getFont()->setSize(8);
        $sheet->setCellValue('L12', 'Final Escrito')
            ->setCellValue('M12', 'Final Escrito')
            ->setCellValue('N12', 'Final Escrito');




        $sheet->setCellValue('B13', 'Prueba')
            ->setCellValue('D13', '5')
            ->setCellValue('E13', '15')
            ->setCellValue('F13', '5')
            ->setCellValue('G13', '15')
            ->setCellValue('H13', '10')
            ->setCellValue('I13', '2')
            ->setCellValue('J13', '50')
            ->setCellValue('K13', '%')
            ->setCellValue('L13', '%')
            ->setCellValue('M13', '50')
            ->setCellValue('N13', '50')
            ->setCellValue('O13', '100');


        //          $sheet->getStyle('B13:O1')->applyFromArray(
        //     array(
        //         'fill' => array(
        //             'type' => PHPExcel_Style_Fill::FILL_SOLID,
        //             'color' => array('rgb' => 'FF0000')
        //         )
        //     )
        // );

        $sheet->mergeCells('B13:C13');
        $sheet->getStyle('B13:O13')->getAlignment()->SetHorizontal('center');
        $sheet->getStyle('B13')->getAlignment()->SetHorizontal('center');

        $index=1;
        $i= 14;
        $cont2=0;
        $status=0;


//   return response()->json($dados_notas->dados_notas, 200);  
        ///parte php forech
  foreach($dados_notas->dados_notas as $element){
  $soma=0;
             $cont2++;
                $sheet->getRowDimension($i)->setRowHeight(20);
         if($element->notatotal >=1 and $element->notatotal <=59){
           $calificacion = 1;
           }else if($element->notatotal >=60 and $element->notatotal <=69){
           $calificacion = 2;
           }else if($element->notatotal >=70 and $element->notatotal <=80){
           $calificacion = 3;
           }else if($element->notatotal >=80 and $element->notatotal <=90){
           $calificacion = 4;
           
               
           }else if($element->notatotal >=91 and $element->notatotal <=100){
                $calificacion = 5;
           }   
           
         $sheet->setCellValue('P'.$i, $calificacion);
         
           
           
           
              $sheet->setCellValue('A'.$i, ''.$index);
                $sheet->setCellValue('B'.$i, $element->nome_usuario);
                 $sheet->setCellValue('C'.$i, $element->doc_oficial);
                 $notas=(object)['finalexamen'=>0,'parcial1'=>0,'parcial2'=>0,'processo1'=>0,'processo2'=>0,'complementarexamen'=>0,'parcial1_recuperatoria'=>0,'parcial2_recuperatoria'=>0,'extraordinaria'=>0 ];
                    foreach($element->avaliacao as $nota){
                
                      if(isset($nota->processo1)){
                          $notas->processo1=$nota->processo1;
                   $sheet->setCellValue('D'.$i, ''.$nota->processo1);
                  
                      }
                             
                     if(isset($nota->processo2)){
                              $notas->processo2 = $nota->processo2;
                    $sheet->setCellValue('F'.$i, ''.$nota->processo2);
                         }
                         
                                   if(isset($nota->parcial1)){
                                          $notas->parcial1=$nota->parcial1;
                                   }
                           
                            
                            if(isset($notas->parcial1)){
  
                             if(isset($notas->parcial1_recuperatoria)){
                            if($notas->parcial1_recuperatoria >  $notas->parcial1) {
                           $notas->parcial1 = $notas->parcial1_recuperatoria;
                             
                            }
            
                      }
                     
                                       
                    $sheet->setCellValue('E'.$i, ''. $notas->parcial1);
                    
             
                                   }
                                   
                        if(isset($nota->parcial1_recuperatoria) ){
                            
                     $notas->parcial1_recuperatoria = $nota->parcial1_recuperatoria;
                     
                                
                            }  
                      
                        
                        
                      
                         
                     
                         if(isset($nota->parcial2)){
                          $notas->parcial2=$nota->parcial2;
                         }
                        if(isset($notas->parcial2)){
                         
                
                            if(isset($notas->parcial2_recuperatoria) ){
                             
                            if($notas->parcial2_recuperatoria > $notas->parcial2){
                        $notas->parcial2 = $notas->parcial2_recuperatoria;
                            
                            }
                
                
                      }
                             $sheet->setCellValue('G'.$i, ''. $notas->parcial2);
                             
                              
                
                        }
                              if(isset($nota->parcial2_recuperatoria) ){
                            
                                  $notas->parcial2_recuperatoria = $nota->parcial2_recuperatoria;
                        
                                
                            }
                            
                            
                        
                            
                             if(isset($nota->finalexamen)){
                                 $notas->finalexamen = $nota->finalexamen ;
                    $sheet->setCellValue('M'.$i, $nota->finalexamen);
                             }
                             
                              if(isset($nota->complementarexamen) ){
                          $notas->complementarexamen = $nota->complementarexamen;
                
                          $sheet->setCellValue('N'.$i, $nota->complementarexamen);
     
                              }
                         
                        //       if(isset($nota->extraordinaria)  ){
                        //     if($nota->extraordinaria > $notas->finalexamen) {
                     
                        //   $sheet->setCellValue('N'.$i, $nota->extraordinaria);
                        //     $soma = $nota->extraordinaria ;
                        // }
                        
                        
                        // }
                        if($notas->finalexamen < 1){
                              $sheet->setCellValue('M'.$i, 'A');
                                $sheet->getStyle('M'.$i)->getAlignment()->setHorizontal('right');
                      
                   }
                       
              
                
                   }
                   $soma = $notas->processo1 + $notas->processo1 + $notas->parcial1 + $notas->parcial2 ;
                     $notas=(object)['finalexamen'=>0,'parcial1'=>0,'parcial2'=>0,'processo1'=>0,'processo2'=>0,'complementarexamen'=>0,'parcial1_recuperatoria'=>0,'parcial2_recuperatoria'=>0,'extraordinaria'=>0 ];
               
                       
                             if(isset($element->somaTrabalho)){
                    $sheet->setCellValue('H'.$i, $element->somaTrabalho);
                      $soma = $soma + $element->somaTrabalho;
                          }
                               if(isset($element->ponto_extra)){
                     $sheet->setCellValue('I'.$i, $element->ponto_extra);
                     $soma = $soma + $element->ponto_extra;
                          }
                      
                          
                   
               
             
                   if(($soma/50) <0.6){
                        $texto = 'Sin Derecho';
                  }
              
              
             
        //          if($cont2 == 17 & $status == 0 ){
                     
        //              $sheet->getRowDimension(intval($i+1))->setRowHeight(80);
        // $sheet->setCellValue('A'.intval($i+1), 'Firma del Docente');
        
        // $sheet->setCellValue('A'.intval($i+2), 'Observación. Esta planilla representará la habilitación a la Evaluación Final y la Calificación Final del Semestre. S.A.M.C.');
        // $sheet->mergeCells('A'.intval($i+2).':H'.intval($i+2));
            
        // $i = $i +2;
        // $cont2=0;    
        // $status=1;
        //  $sheet->setBreak('A'.$i, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
        //       }
        //          if($cont2 == 22){
        //                  $sheet->getRowDimension(intval($i+1))->setRowHeight(80);
        // $sheet->setCellValue('A'.intval($i+1), 'Firma del Docente');
        // $sheet->setCellValue('A'.intval($i+2), 'Observación. Esta planilla representará la habilitación a la Evaluación Final y la Calificación Final del Semestre. S.A.M.C.');
        // $sheet->mergeCells('A'.intval($i+2).':H'.intval($i+2));
        // $i = $i +2;
        // $cont2=0;
        // $sheet->setBreak('A'.$i, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
        //       }
              $index++;
               $i++;

        }
        // $sheet->getRowDimension(intval($i))->setRowHeight(80);
        // $sheet->setCellValue('A'.intval($i), 'Firma del Docente');
        // $sheet->setCellValue('A'.intval($i+1), 'Observación. Esta planilla representará la habilitación a la Evaluación Final y la Calificación Final del Semestre. S.A.M.C.');
        // $sheet->mergeCells('A'.intval($i+1).':H'.intval($i+1));
        
              
            $footer =  22 -$cont2;
                       $sheet->mergeCells('C'.intval($i+$footer+1).':D'.intval($i+$footer+1));
                   $sheet->mergeCells('C'.intval($i+$footer).':D'.intval($i+$footer));
           $sheet->setCellValue('B'.intval($i+$footer), '.................................................');
           $sheet->setCellValue('C'.intval($i+$footer), '.................................................');
           $sheet->setCellValue('G'.intval($i+$footer), '.................................................');
           $sheet->setCellValue('K'.intval($i+$footer), '.................................................'); 
           $sheet->setCellValue('O'.intval($i+$footer), '.................................................');
           
                $sheet->setCellValue('B'.intval($i+$footer+1), 'Docente');
        $sheet->setCellValue('C'.intval($i+$footer+1), 'Secretaria Academica');
          $sheet->setCellValue('G'.intval($i+$footer+1), 'Diretor de carrera');
          $sheet->setCellValue('K'.intval($i+$footer+1), 'Diretor General de filial'); 
             
          $sheet->setCellValue('O'.intval($i+$footer+1), 'Secretaria General Academica');
           
      
               
                      $sheet->getStyle('A'.intval($i+$footer+1).':P'.intval($i+$footer+1))->getFont()->setSize(12);
        
            // $sheet->mergeCells('M'.intval($i+1).':O'.intval($i+1));
            
        $sheet->getStyle('A'.intval($i+$footer+1).':P'.intval($i+$footer+1))->getAlignment()->setHorizontal('center');
          $sheet->getStyle('A'.intval($i+$footer).':P'.intval($i+$footer))->getAlignment()->setHorizontal('center');
        
        
        
        

    
$sheet->getStyle('D13:O13')->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('7cd5dc');
  
    $sheet->getStyle('j14:J'.$i)->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('ffff00');
    
        $sheet->getStyle('O14:O'.$i)->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('ffff00');
    
      $sheet->getStyle('K14:K'.$i)->getAlignment()->setHorizontal('center');
    $sheet->getStyle('K14:K'.$i)->getFont()->setSize(8);
$sheet->getStyle('K14:K'.$i)
    ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        //  $spreadsheet2 = $spreadsheet;
        
    //     $sheet->getStyle('K14:K'.$i)->getNumberFormat()
    // ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
      $sheet->getStyle('C14:C'.$i)->getNumberFormat()
    ->setFormatCode('0');
        //   $sheet->getStyle('C14:C'.$i)->getAlignment()->setHorizontal('center');
          
              $sheet->getStyle('C14:J'.$i)->getAlignment()->setHorizontal('center');
   $sheet
->getPageSetup()
    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
    
    
    
    


 $sheet->getPageSetup()->setPrintArea('A1:P'.intval($i+$footer+1));
//   $sheet
//     ->getHeaderFooter()->setOddFooter("&RPage &P of &N");

  $sheet->getHeaderFooter()
            ->setOddFooter('&L&BFirma del Docente: ______________________  '.'&CObservación. Esta planilla representará la habilitación a la Evaluación Final y la Calificación Final del Semestre. S.A.M.C. '. '&RPage &P of &N');
            
    //      $sheet
    // ->getHeaderFooter()->setEvenFooter("&RPage &P of 8");
    
    
    //      $sheet
    // ->getHeaderFooter()->setOddFooter('&L&BFirma del Docente: ______________________  '.'&L&BFirma del Docente: ______________________  '.'&L&BFirma del Docente: ______________________  ');

    
        // $sheet->getHeaderFooter()
        //     ->setOddFooter('&RPage &P de &N');
            

// }

   $pagina = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];
          
        $sheet->getStyle('A1:P'.intval($i+2))->applyFromArray($pagina);
        
                   $sheet->getStyle('A1:P'.intval($i+1))->applyFromArray($pagina);
        
           $pagina2 = [
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];
        $sheet->getStyle('A'.intval($i+1).':P'.intval($i+$footer+1))->applyFromArray($pagina2);
        
        
        

       $sheet->getPageSetup()
        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(8, 13);



        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
       
        $writer = new Xlsx($spreadsheet);
        $writer->save('relatorio.xlsx');
        
        $file="relatorio.xlsx";// tive que fazer assim, estava dando erro no retorno
        return response()->download(public_path($file));
        
        
    }
    
    public function gerarExcelteste2($id_oferta_disciplina)
    {
        // $id_oferta_disciplina = 2280;

        $dados_notas = (object) $this->tentativacontroller->lista_notas_disciplina2($id_oferta_disciplina)->original;

        //  return response()->json($dados_notas, 200); 
        if( $dados_notas->dados_notas == ''){
       return response()->json('Não há notas nessa unidade', 500); 
        }
        
        
        $filename = 'Relatorio notas '.$dados_notas->nome_materia.' '.$dados_notas->nome_turma.' .xlsx';
        $spreadsheet = new Spreadsheet();


        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getRowDimension(1)->setRowHeight(20);
        $sheet->getRowDimension(10)->setRowHeight(100);
        $sheet->getColumnDimension('A')->setWidth(4);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(20);

        $sheet->getColumnDimension('D')->setWidth(11);
        $sheet->getColumnDimension('E')->setWidth(11);
        $sheet->getColumnDimension('F')->setWidth(11);
        $sheet->getColumnDimension('G')->setWidth(11);
        $sheet->getColumnDimension('H')->setWidth(11);
        $sheet->getColumnDimension('I')->setWidth(11);

        $sheet->getColumnDimension('M')->setWidth(13);
        $sheet->getColumnDimension('N')->setWidth(13);
        $sheet->getColumnDimension('O')->setWidth(12);


// for($i=0;$i<10;$i++){




        //////////////////////////ESTILIZAR BORDAS////////////////////////////////




     

        // $styleArray = [
        //     'borders' => [
        //         'bottom' => [
        //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
        //             'color' => ['argb' => '00000000'],
        //         ],
        //     ],
        // ];
        // $sheet->getStyle('C1:L7')->applyFromArray($styleArray);




        //=======================================================================//



        $IMG = 'https://api.ucpvirtual.com.br/storage/documentos/logo.jpeg';
        $row_num = 1;
        if (isset($IMG) && !empty($IMG)) {
            $imageType = "png";

            if (strpos($IMG, ".png") === false) {
                $imageType = "jpg";
            }

            $drawing = new MemoryDrawing();
            // $sheet->getColumnDimension('A')->getRowDimension($row_num)->setWidth(10);
            // $sheet->getRowDimension($row_num)->setRowHeight(50);
            $sheet->mergeCells('A1:B7');
            $gdImage = ($imageType == 'png') ? imagecreatefrompng($IMG) : imagecreatefromjpeg($IMG);

            $drawing->setResizeProportional(false);
            $drawing->setImageResource($gdImage);
            $drawing->setRenderingFunction(\PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::RENDERING_JPEG);
            $drawing->setMimeType(\PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::MIMETYPE_DEFAULT);
            $drawing->setWidth(160);
            $drawing->setHeight(130);
            $drawing->setOffsetX(80);
            $drawing->setOffsetY(10);
            // $drawing->setCoordinates('C'.$row_num);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());
            //$row_num++;
        }



        ////////////////parte superior meio////////////////////////




        $sheet->setCellValue('C1', 'FACULTAD DE CIENCIAS DE LA SALUD');

        $sheet->getStyle("C1:M1")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C1:M1")->getFont()->setSize(18);
        $sheet->mergeCells('C1:M1');


        $sheet->setCellValue('C2', 'CARRERA DE MEDICINA');
        $sheet->getStyle("C2:M2")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C2:M2")->getFont()->setSize(14);
        $sheet->mergeCells('C2:M2');


        $sheet->setCellValue('C3', $dados_notas->nome_filial);

        $sheet->getStyle("C3:M3")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C3:M3")->getFont()->setSize(12);
        $sheet->mergeCells('C3:M3');

        ////////////////parte superior meio////////////////////////


        ///////////// ultima parte do cabecalho superior direiro///////////////

        $sheet->setCellValue('N1', 'Escala');
        $sheet->getStyle("N1:P1")->getAlignment()->setHorizontal('center');
        $sheet->mergeCells('N1:P1');

        $sheet->setCellValue('N2', '100');
        $sheet->getStyle("N2")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("N2")->getFont()->setBold(true);

        //      $sheet->setCellValue('M2','100');
        //   $sheet->getStyle("M2")->getAlignment()->setHorizontal('center');
        $sheet->mergeCells('O2:P2');

        $sheet->setCellValue('N3', '1');
        $sheet->getStyle("N3:N7")->getAlignment()->setHorizontal('center');
        $sheet->setCellValue('N4', '60');
        $sheet->setCellValue('N5', '70');
        $sheet->setCellValue('N6', '81');
        $sheet->setCellValue('N7', '91');
        

        $sheet->setCellValue('O3', '59');
        $sheet->getStyle("O3:O7")->getAlignment()->setHorizontal('center');
        $sheet->setCellValue('O4', '69');
        $sheet->setCellValue('O5', '80');
        $sheet->setCellValue('O6', '90');
        $sheet->setCellValue('O7', '100');
        

        $sheet->setCellValue('P3', '1');
        $sheet->getStyle("P3:P7")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("P3:P7")->getFont()->setBold(true);
        $sheet->setCellValue('P4', '2');
        $sheet->setCellValue('P5', '3');
        $sheet->setCellValue('P6', '4');
        $sheet->setCellValue('P7', '5');
        



        ///////////// ultima parte do cabecalho superior direiro///////////////



        //////////////////////////Meio informacoes /////////////////////////////////////


        $sheet->setCellValue('C4', "Aprobado por Ley Nº 3153/06");
        $sheet->mergeCells('C4:M4');
        $sheet->getStyle("C4:M4")->getFont()->setSize(7);

        $sheet->setCellValue('C5', "ASIGNATURA: ".$dados_notas->nome_materia);
        $sheet->mergeCells('C5:M5');


        $sheet->setCellValue('C6', "Profesor/a: ".$dados_notas->nome_professor);
        $sheet->mergeCells('C6:M6');


        $sheet->setCellValue('C7', "Carrera: Medicina ");
        $sheet->mergeCells('C7:M7');



//------------------------------------------------------comeca aqui------------------------------------------------------------------------------


    $sheet->setCellValue('A8', ''.$dados_notas->nome_semestre.' '."'".$dados_notas->nome_turma."'".$dados_notas->nome_periodo_anual.' '.$dados_notas->nome_ano);
       
        $sheet->mergeCells('A8:B8');
        $sheet->getStyle("A8:B8")->getFont()->setSize('12');

        $sheet->setCellValue('C8', "Planilla de Proceso de la Valoración de los Aprendizajes-  Primeira Convocatoria 2021  ");
        $sheet->mergeCells('C8:P8');
        $sheet->getStyle("C8:P8")->getFont()->setSize(16);
        $sheet->getStyle("C8")->getAlignment()->setHorizontal('center');



        /////////////////////////////////////////////////////////////////////////////////////


        //////////////////////parte meio inferior em baixo da imagem ////////////////////////


        $sheet->setCellValue('A9', 'Nº');
        $sheet->mergeCells('A9:A13');
        $sheet->getStyle("A9")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("A9")->getAlignment()->setVertical('center');
        $sheet->getStyle("A9:A13")->getFont()->setSize(7);

        $sheet->setCellValue('B9', 'Nombres y Apellidos');
        $sheet->mergeCells('B9:B12');
        $sheet->getStyle("B9")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("B9")->getAlignment()->setVertical('center');
        $sheet->getStyle("B9:B13")->getFont()->setSize(8);




        $sheet->setCellValue('C9', 'Documento de Identidad');
            $sheet->mergeCells('C9:C12');
        $sheet->getStyle("C9")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C9")->getAlignment()->setVertical('center');
        $sheet->getStyle("C9:C13")->getFont()->setSize(8);



        $sheet->setCellValue('D9', 'Reactivos de Evaluación de Proceso');
        $sheet->mergeCells('D9:I9');
        $sheet->getStyle("D9")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("D9")->getFont()->setBold(true);
        $sheet->getStyle("D9:I9")->getFont()->setSize(14);


        ///////////////////////////////////////////////////////////////////////////////

        $sheet->setCellValue('D10', '1 Prueba de Proceso');

        $sheet->setCellValue('E10', '1  Prueba parcial');

        $sheet->setCellValue('F10', '2 Prueba de Proceso');
        $sheet->setCellValue('G10', '2 Prueba Parcial');
        $sheet->setCellValue('H10', 'Trabajo Práctico');
        $sheet->setCellValue('I10', 'Extensión  ');

        $sheet->setCellValue('J10', 'Total de Puntos Logrados');
        $sheet->getStyle('D10:J10')->getAlignment()->setWrapText(true);





        ///////////////// configuracoes 

        $sheet->getStyle('D10:J10')->getAlignment()->setTextRotation(90);
        $sheet->getStyle('D10:I10')->getAlignment()->setWrapText(true);
        $sheet->getStyle("D12:H12")->getFont()->setSize(9);
        $sheet->getStyle("D10:J10")->getFont()->setSize(12);
        $sheet->getStyle("D10:P10")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("D10:P10")->getAlignment()->setVertical('center');
        $sheet->getStyle('P10')->getAlignment()->setTextRotation(90);
        $sheet->getStyle('P10')->getAlignment()->setTextRotation(90);
        $sheet->getStyle('K10:L10')->getAlignment()->setTextRotation(90);



      
 
        $sheet->setCellValue('D11', 'Fecha '.$dados_notas->fecha_inicio->processo1);
       
        $sheet->setCellValue('E11', 'Fecha '.$dados_notas->fecha_inicio->parcial1);
      
        $sheet->setCellValue('F11', 'Fecha '.$dados_notas->fecha_inicio->processo2);
          
        $sheet->setCellValue('G11', 'Fecha '.$dados_notas->fecha_inicio->parcial2);
  
          $sheet->setCellValue('H11', 'fecha '.$dados_notas->dataTrabalho);
        // $sheet->setCellValue('I11', 'Fecha /06/20');
        $sheet->getStyle('D11:I11')->getAlignment()->setWrapText(true);
        $sheet->getStyle('D11:I11')->getAlignment()->setHorizontal('center');


        $sheet->setCellValue('D12', '1 Proceso ');
        $sheet->setCellValue('E12', '1 Parcial ');
        $sheet->setCellValue('F12', '2 Proceso');
        $sheet->setCellValue('G12', '2 Parcial');
        $sheet->setCellValue('H12', 'Trab. Prác.');
        $sheet->setCellValue('I12', 'Extensión');
        $sheet->getStyle('D12:I12')->getAlignment()->setWrapText(true);
        $sheet->setCellValue('J11', 'Fecha Tope 00/00/2020');
        $sheet->mergeCells('J11:J12');
        // $sheet->getStyle('J11:J12')->getAlignment()->setWrapText(true);
        $sheet->getStyle("J11:J12")->getAlignment()->setHorizontal('center');



        ///////////////////////////////////////meio esquedo/////////////////////////////////
        $sheet->setCellValue('K10', '% de la ETAPA');
        $sheet->getStyle("K10")->getFont()->setBold(true);
        $sheet->getStyle('K10')->getAlignment()->setWrapText(true);
        $sheet->getStyle("J11:J12")->getAlignment()->setVertical('center');

        $sheet->mergeCells('K10:K12');
        
        

        $sheet->setCellValue('L10', '% Asist.');
        $sheet->getStyle('L10')->getAlignment()->setWrapText(true);
        $sheet->mergeCells('L10:L12');
               $sheet->getStyle("L10")->getFont()->setBold(true);
   
        $sheet->getStyle("L10:L12")->getAlignment()->setVertical('center');



        $sheet->setCellValue('M10', 'Examen final Ordinario 1ra Mesa');
        $sheet->getStyle('M10')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('N10', 'Examen Final Complementário 2da Mesa');
        $sheet->getStyle('N10')->getAlignment()->setWrapText(true);




        $sheet->setCellValue('O10', 'Total de Puntos Acumulados en el Parcial + Total de Puntos Logrados en el Examen Fina');
        $sheet->getStyle('O10')->getAlignment()->setWrapText(true);
        $sheet->mergeCells('O10:O12');
        $sheet->getStyle("O10")->getFont()->setSize(9);



        $sheet->setCellValue('P10', 'Calificación Final');
        $sheet->getStyle("P10")->getFont()->setBold(true);
        $sheet->mergeCells('P10:P13');
        $sheet->getStyle("p10")->getFont()->setSize(11);



   
        // $sheet->getStyle("D9")->getFont()->setBold(true);
        // $sheet->getStyle("D9:I15")->getFont()->setSize(10);


        $sheet->getStyle('L11:N11')->getAlignment()->setWrapText(true);

      
               
        $sheet->getStyle("M11:N11")->getAlignment()->setHorizontal('center');


        $sheet->getStyle("L12:N12")->getFont()->setSize(8);
        $sheet->setCellValue('L12', 'Final Escrito')
            ->setCellValue('M12', 'Final Escrito')
            ->setCellValue('N12', 'Final Escrito');




        $sheet->setCellValue('B13', 'Prueba')
            ->setCellValue('D13', '5')
            ->setCellValue('E13', '15')
            ->setCellValue('F13', '5')
            ->setCellValue('G13', '15')
            ->setCellValue('H13', '10')
            ->setCellValue('I13', '2')
            ->setCellValue('J13', '50')
            ->setCellValue('K13', '%')
            ->setCellValue('L13', '%')
            ->setCellValue('M13', '50')
            ->setCellValue('N13', '50')
            ->setCellValue('O13', '100');


        //          $sheet->getStyle('B13:O1')->applyFromArray(
        //     array(
        //         'fill' => array(
        //             'type' => PHPExcel_Style_Fill::FILL_SOLID,
        //             'color' => array('rgb' => 'FF0000')
        //         )
        //     )
        // );

        $sheet->mergeCells('B13:C13');
        $sheet->getStyle('B13:O13')->getAlignment()->SetHorizontal('center');
        $sheet->getStyle('B13')->getAlignment()->SetHorizontal('center');

        $index=1;
        $i= 14;
        $cont2=0;
        $status=0;


//   return response()->json($dados_notas->dados_notas, 200);  
        ///parte php forech
  foreach($dados_notas->dados_notas as $element){
  $soma=0;
             $cont2++;
                $sheet->getRowDimension($i)->setRowHeight(20);
         if($element->notatotal >=1 and $element->notatotal <=59){
           $calificacion = 1;
           }else if($element->notatotal >=60 and $element->notatotal <=69){
           $calificacion = 2;
           }else if($element->notatotal >=70 and $element->notatotal <=80){
           $calificacion = 3;
           }else if($element->notatotal >=80 and $element->notatotal <=90){
           $calificacion = 4;
           
               
           }else if($element->notatotal >=91 and $element->notatotal <=100){
                $calificacion = 5;
           }   
           
         $sheet->setCellValue('P'.$i, $calificacion);
         
           
           
           
              $sheet->setCellValue('A'.$i, ''.$index);
                $sheet->setCellValue('B'.$i, $element->nome_usuario);
                 $sheet->setCellValue('C'.$i, $element->doc_oficial);
                 $notas=(object)['finalexamen'=>0,'parcial1'=>0,'parcial2'=>0,'processo1'=>0,'processo2'=>0,'complementarexamen'=>0,'parcial1_recuperatoria'=>0,'parcial2_recuperatoria'=>0,'extraordinaria'=>0 ];
                    foreach($element->avaliacao as $nota){
                
                      if(isset($nota->processo1)){
                          $notas->processo1=$nota->processo1;
                   $sheet->setCellValue('D'.$i, ''.$nota->processo1);
                  
                      }
                             
                     if(isset($nota->processo2)){
                              $notas->processo2 = $nota->processo2;
                    $sheet->setCellValue('F'.$i, ''.$nota->processo2);
                         }
                         
                                   if(isset($nota->parcial1)){
                                          $notas->parcial1=$nota->parcial1;
                                   }
                           
                            
                            if(isset($notas->parcial1)){
  
                             if(isset($notas->parcial1_recuperatoria)){
                            if($notas->parcial1_recuperatoria >  $notas->parcial1) {
                           $notas->parcial1 = $notas->parcial1_recuperatoria;
                             
                            }
            
                      }
                     
                                       
                    $sheet->setCellValue('E'.$i, ''. $notas->parcial1);
                    
             
                                   }
                                   
                        if(isset($nota->parcial1_recuperatoria) ){
                            
                     $notas->parcial1_recuperatoria = $nota->parcial1_recuperatoria;
                     
                                
                            }  
                      
                        
                        
                      
                         
                     
                         if(isset($nota->parcial2)){
                          $notas->parcial2=$nota->parcial2;
                         }
                        if(isset($notas->parcial2)){
                         
                
                            if(isset($notas->parcial2_recuperatoria) ){
                             
                            if($notas->parcial2_recuperatoria > $notas->parcial2){
                        $notas->parcial2 = $notas->parcial2_recuperatoria;
                            
                            }
                
                
                      }
                             $sheet->setCellValue('G'.$i, ''. $notas->parcial2);
                             
                              
                
                        }
                              if(isset($nota->parcial2_recuperatoria) ){
                            
                                  $notas->parcial2_recuperatoria = $nota->parcial2_recuperatoria;
                        
                                
                            }
                            
                            
                        
                            
                             if(isset($nota->finalexamen)){
                                 $notas->finalexamen = $nota->finalexamen ;
                    $sheet->setCellValue('M'.$i, $nota->finalexamen);
                             }
                             
                              if(isset($nota->complementarexamen) ){
                          $notas->complementarexamen = $nota->complementarexamen;
                
                          $sheet->setCellValue('N'.$i, $nota->complementarexamen);
     
                              }
                         
                        //       if(isset($nota->extraordinaria)  ){
                        //     if($nota->extraordinaria > $notas->finalexamen) {
                     
                        //   $sheet->setCellValue('N'.$i, $nota->extraordinaria);
                        //     $soma = $nota->extraordinaria ;
                        // }
                        
                        
                        // }
                        if($notas->finalexamen < 1){
                              $sheet->setCellValue('M'.$i, 'A');
                                $sheet->getStyle('M'.$i)->getAlignment()->setHorizontal('right');
                      
                   }
                       
              
                
                   }
                   $soma = $notas->processo1 + $notas->processo1 + $notas->parcial1 + $notas->parcial2 ;
                     $notas=(object)['finalexamen'=>0,'parcial1'=>0,'parcial2'=>0,'processo1'=>0,'processo2'=>0,'complementarexamen'=>0,'parcial1_recuperatoria'=>0,'parcial2_recuperatoria'=>0,'extraordinaria'=>0 ];
               
                       
                             if(isset($element->somaTrabalho)){
                    $sheet->setCellValue('H'.$i, $element->somaTrabalho);
                      $soma = $soma + $element->somaTrabalho;
                          }
                               if(isset($element->ponto_extra)){
                     $sheet->setCellValue('I'.$i, $element->ponto_extra);
                     $soma = $soma + $element->ponto_extra;
                          }
                      
                          
                   
               
             
                   if(($soma/50) <0.6){
                        $texto = 'Sin Derecho';
                  }
              
              
             
        //          if($cont2 == 17 & $status == 0 ){
                     
        //              $sheet->getRowDimension(intval($i+1))->setRowHeight(80);
        // $sheet->setCellValue('A'.intval($i+1), 'Firma del Docente');
        
        // $sheet->setCellValue('A'.intval($i+2), 'Observación. Esta planilla representará la habilitación a la Evaluación Final y la Calificación Final del Semestre. S.A.M.C.');
        // $sheet->mergeCells('A'.intval($i+2).':H'.intval($i+2));
            
        // $i = $i +2;
        // $cont2=0;    
        // $status=1;
        //  $sheet->setBreak('A'.$i, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
        //       }
        //          if($cont2 == 22){
        //                  $sheet->getRowDimension(intval($i+1))->setRowHeight(80);
        // $sheet->setCellValue('A'.intval($i+1), 'Firma del Docente');
        // $sheet->setCellValue('A'.intval($i+2), 'Observación. Esta planilla representará la habilitación a la Evaluación Final y la Calificación Final del Semestre. S.A.M.C.');
        // $sheet->mergeCells('A'.intval($i+2).':H'.intval($i+2));
        // $i = $i +2;
        // $cont2=0;
        // $sheet->setBreak('A'.$i, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
        //       }
              $index++;
               $i++;

        }
        // $sheet->getRowDimension(intval($i))->setRowHeight(80);
        // $sheet->setCellValue('A'.intval($i), 'Firma del Docente');
        // $sheet->setCellValue('A'.intval($i+1), 'Observación. Esta planilla representará la habilitación a la Evaluación Final y la Calificación Final del Semestre. S.A.M.C.');
        // $sheet->mergeCells('A'.intval($i+1).':H'.intval($i+1));
        
              
            $footer =  22 -$cont2;
                       $sheet->mergeCells('C'.intval($i+$footer+1).':D'.intval($i+$footer+1));
                   $sheet->mergeCells('C'.intval($i+$footer).':D'.intval($i+$footer));
           $sheet->setCellValue('B'.intval($i+$footer), '.................................................');
           $sheet->setCellValue('C'.intval($i+$footer), '.................................................');
           $sheet->setCellValue('G'.intval($i+$footer), '.................................................');
           $sheet->setCellValue('K'.intval($i+$footer), '.................................................'); 
           $sheet->setCellValue('O'.intval($i+$footer), '.................................................');
           
                $sheet->setCellValue('B'.intval($i+$footer+1), 'Docente');
        $sheet->setCellValue('C'.intval($i+$footer+1), 'Secretaria Academica');
          $sheet->setCellValue('G'.intval($i+$footer+1), 'Diretor de carrera');
          $sheet->setCellValue('K'.intval($i+$footer+1), 'Diretor General de filial'); 
             
          $sheet->setCellValue('O'.intval($i+$footer+1), 'Secretaria General Academica');
           
      
               
                      $sheet->getStyle('A'.intval($i+$footer+1).':P'.intval($i+$footer+1))->getFont()->setSize(12);
        
            // $sheet->mergeCells('M'.intval($i+1).':O'.intval($i+1));
            
        $sheet->getStyle('A'.intval($i+$footer+1).':P'.intval($i+$footer+1))->getAlignment()->setHorizontal('center');
          $sheet->getStyle('A'.intval($i+$footer).':P'.intval($i+$footer))->getAlignment()->setHorizontal('center');
        
        
        
        

    
$sheet->getStyle('D13:O13')->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('7cd5dc');
  
    $sheet->getStyle('j14:J'.$i)->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('ffff00');
    
        $sheet->getStyle('O14:O'.$i)->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('ffff00');
    
      $sheet->getStyle('K14:K'.$i)->getAlignment()->setHorizontal('center');
    $sheet->getStyle('K14:K'.$i)->getFont()->setSize(8);
$sheet->getStyle('K14:K'.$i)
    ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        //  $spreadsheet2 = $spreadsheet;
        
    //     $sheet->getStyle('K14:K'.$i)->getNumberFormat()
    // ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
      $sheet->getStyle('C14:C'.$i)->getNumberFormat()
    ->setFormatCode('0');
        //   $sheet->getStyle('C14:C'.$i)->getAlignment()->setHorizontal('center');
          
              $sheet->getStyle('C14:J'.$i)->getAlignment()->setHorizontal('center');
   $sheet
->getPageSetup()
    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
    
    
    
    


 $sheet->getPageSetup()->setPrintArea('A1:P'.intval($i+$footer+1));
//   $sheet
//     ->getHeaderFooter()->setOddFooter("&RPage &P of &N");

  $sheet->getHeaderFooter()
            ->setOddFooter('&L&BFirma del Docente: ______________________  '.'&CObservación. Esta planilla representará la habilitación a la Evaluación Final y la Calificación Final del Semestre. S.A.M.C. '. '&RPage &P of &N');
            
    //      $sheet
    // ->getHeaderFooter()->setEvenFooter("&RPage &P of 8");
    
    
    //      $sheet
    // ->getHeaderFooter()->setOddFooter('&L&BFirma del Docente: ______________________  '.'&L&BFirma del Docente: ______________________  '.'&L&BFirma del Docente: ______________________  ');

    
        // $sheet->getHeaderFooter()
        //     ->setOddFooter('&RPage &P de &N');
            

// }

   $pagina = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];
          
        $sheet->getStyle('A1:P'.intval($i+2))->applyFromArray($pagina);
        
                   $sheet->getStyle('A1:P'.intval($i+1))->applyFromArray($pagina);
        
           $pagina2 = [
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];
        $sheet->getStyle('A'.intval($i+1).':P'.intval($i+$footer+1))->applyFromArray($pagina2);
        
        
        

       $sheet->getPageSetup()
        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(8, 13);



        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
       
        $writer = new Xlsx($spreadsheet);
        $writer->save('relatorio.xlsx');
        
        $file="relatorio.xlsx";// tive que fazer assim, estava dando erro no retorno
        return response()->download(public_path($file));
        
        
    }
    
    public function gerarExcelTurma($id_turma)
    {
        // $id_oferta_disciplina = 2280;
        
        

        $todosasturmas = (object) $this->tentativacontroller->lista_notas_disciplina($id_turma)->original;


// if($todosasturmas->dados_notas ==""){
//     return response()->json('não ha notas nessa turma',500);
// }



        //  return response()->json($dados_notas, 200); 
    //     if( $dados_notas->dados_notas == ''){
    //   return response()->json('Não há notas nessa unidade', 500); 
    //     }
        
        
        $filename = 'Relatorio notas '.$todosasturmas->nome_turmatotal.'.xlsx';
        
        
        $indeci=0;
             $spreadsheet = new Spreadsheet();

        $spreadsheet->setActiveSheetIndex(0);
        foreach ($todosasturmas->tudonota as $dados_notas) {

// return response()->json($dados_notas, 200); 

            $sheet = $spreadsheet->getActiveSheet();


        $sheet->getRowDimension(1)->setRowHeight(20);
        $sheet->getRowDimension(10)->setRowHeight(100);
        $sheet->getColumnDimension('A')->setWidth(4);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(20);

        $sheet->getColumnDimension('D')->setWidth(11);
        $sheet->getColumnDimension('E')->setWidth(11);
        $sheet->getColumnDimension('F')->setWidth(11);
        $sheet->getColumnDimension('G')->setWidth(11);
        $sheet->getColumnDimension('H')->setWidth(11);
        $sheet->getColumnDimension('I')->setWidth(11);

        $sheet->getColumnDimension('M')->setWidth(13);
        $sheet->getColumnDimension('N')->setWidth(13);
        $sheet->getColumnDimension('O')->setWidth(12);


// for($i=0;$i<10;$i++){




        //////////////////////////ESTILIZAR BORDAS////////////////////////////////




     

        // $styleArray = [
        //     'borders' => [
        //         'bottom' => [
        //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
        //             'color' => ['argb' => '00000000'],
        //         ],
        //     ],
        // ];
        // $sheet->getStyle('C1:L7')->applyFromArray($styleArray);




        //=======================================================================//



        $IMG = 'https://api.ucpvirtual.com.br/storage/documentos/logo.jpeg';
        $row_num = 1;
        if (isset($IMG) && !empty($IMG)) {
            $imageType = "png";

            if (strpos($IMG, ".png") === false) {
                $imageType = "jpg";
            }

            $drawing = new MemoryDrawing();
            // $sheet->getColumnDimension('A')->getRowDimension($row_num)->setWidth(10);
            // $sheet->getRowDimension($row_num)->setRowHeight(50);
            $sheet->mergeCells('A1:B7');
            $gdImage = ($imageType == 'png') ? imagecreatefrompng($IMG) : imagecreatefromjpeg($IMG);

            $drawing->setResizeProportional(false);
            $drawing->setImageResource($gdImage);
            $drawing->setRenderingFunction(\PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::RENDERING_JPEG);
            $drawing->setMimeType(\PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::MIMETYPE_DEFAULT);
            $drawing->setWidth(160);
            $drawing->setHeight(130);
            $drawing->setOffsetX(80);
            $drawing->setOffsetY(10);
            // $drawing->setCoordinates('C'.$row_num);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());
            //$row_num++;
        }



        ////////////////parte superior meio////////////////////////




        $sheet->setCellValue('C1', 'FACULTAD DE CIENCIAS DE LA SALUD');

        $sheet->getStyle("C1:M1")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C1:M1")->getFont()->setSize(18);
        $sheet->mergeCells('C1:M1');


        $sheet->setCellValue('C2', 'CARRERA DE MEDICINA');
        $sheet->getStyle("C2:M2")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C2:M2")->getFont()->setSize(14);
        $sheet->mergeCells('C2:M2');


        $sheet->setCellValue('C3', $dados_notas->nome_filial);

        $sheet->getStyle("C3:M3")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C3:M3")->getFont()->setSize(12);
        $sheet->mergeCells('C3:M3');

        ////////////////parte superior meio////////////////////////


        ///////////// ultima parte do cabecalho superior direiro///////////////

        $sheet->setCellValue('N1', 'Escala');
        $sheet->getStyle("N1:P1")->getAlignment()->setHorizontal('center');
        $sheet->mergeCells('N1:P1');

        $sheet->setCellValue('N2', '100');
        $sheet->getStyle("N2")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("N2")->getFont()->setBold(true);

        //      $sheet->setCellValue('M2','100');
        //   $sheet->getStyle("M2")->getAlignment()->setHorizontal('center');
        $sheet->mergeCells('O2:P2');

        $sheet->setCellValue('N3', '1');
        $sheet->getStyle("N3:N7")->getAlignment()->setHorizontal('center');
        $sheet->setCellValue('N4', '60');
        $sheet->setCellValue('N5', '70');
        $sheet->setCellValue('N6', '81');
        $sheet->setCellValue('N7', '91');
        

        $sheet->setCellValue('O3', '59');
        $sheet->getStyle("O3:O7")->getAlignment()->setHorizontal('center');
        $sheet->setCellValue('O4', '69');
        $sheet->setCellValue('O5', '80');
        $sheet->setCellValue('O6', '90');
        $sheet->setCellValue('O7', '100');
        

        $sheet->setCellValue('P3', '1');
        $sheet->getStyle("P3:P7")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("P3:P7")->getFont()->setBold(true);
        $sheet->setCellValue('P4', '2');
        $sheet->setCellValue('P5', '3');
        $sheet->setCellValue('P6', '4');
        $sheet->setCellValue('P7', '5');
        



        ///////////// ultima parte do cabecalho superior direiro///////////////



        //////////////////////////Meio informacoes /////////////////////////////////////


        $sheet->setCellValue('C4', "Aprobado por Ley Nº 3153/06");
        $sheet->mergeCells('C4:M4');
        $sheet->getStyle("C4:M4")->getFont()->setSize(7);

        $sheet->setCellValue('C5', "ASIGNATURA: ".$dados_notas->nome_materia);
        $sheet->mergeCells('C5:M5');


        $sheet->setCellValue('C6', "Profesor/a: ".$dados_notas->nome_professor);
        $sheet->mergeCells('C6:M6');


        $sheet->setCellValue('C7', "Carrera: Medicina ");
        $sheet->mergeCells('C7:M7');



//------------------------------------------------------comeca aqui------------------------------------------------------------------------------


        $sheet->setCellValue('A8', ''.$dados_notas->nome_semestre.' '."'".$dados_notas->nome_turma."'".$dados_notas->nome_periodo_anual.' '.$dados_notas->nome_ano);
        $sheet->mergeCells('A8:B8');
        $sheet->getStyle("A8:B8")->getFont()->setSize(12);

        $sheet->setCellValue('C8', "Planilla de Proceso de la Valoración de los Aprendizajes- Primeira Convocatoria 2021 ");
        $sheet->mergeCells('C8:P8');
        $sheet->getStyle("C8:P8")->getFont()->setSize(16);
        $sheet->getStyle("C8")->getAlignment()->setHorizontal('center');



        /////////////////////////////////////////////////////////////////////////////////////


        //////////////////////parte meio inferior em baixo da imagem ////////////////////////


        $sheet->setCellValue('A9', 'Nº');
        $sheet->mergeCells('A9:A13');
        $sheet->getStyle("A9")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("A9")->getAlignment()->setVertical('center');
        $sheet->getStyle("A9:A13")->getFont()->setSize(7);

        $sheet->setCellValue('B9', 'Nombres y Apellidos');
        $sheet->mergeCells('B9:B12');
        $sheet->getStyle("B9")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("B9")->getAlignment()->setVertical('center');
        $sheet->getStyle("B9:B13")->getFont()->setSize(8);




        $sheet->setCellValue('C9', 'Documento de Identidad');
            $sheet->mergeCells('C9:C12');
        $sheet->getStyle("C9")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C9")->getAlignment()->setVertical('center');
        $sheet->getStyle("C9:C13")->getFont()->setSize(8);



        $sheet->setCellValue('D9', 'Reactivos de Evaluación de Proceso');
        $sheet->mergeCells('D9:I9');
        $sheet->getStyle("D9")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("D9")->getFont()->setBold(true);
        $sheet->getStyle("D9:I9")->getFont()->setSize(14);


        ///////////////////////////////////////////////////////////////////////////////

        $sheet->setCellValue('D10', '1 Prueba de Proceso');

        $sheet->setCellValue('E10', '1  Prueba parcial');

        $sheet->setCellValue('F10', '2 Prueba de Proceso');
        $sheet->setCellValue('G10', '2 Prueba Parcial');
        $sheet->setCellValue('H10', 'Trabajo Práctico');
        $sheet->setCellValue('I10', 'Extensión  ');

        $sheet->setCellValue('J10', 'Total de Puntos Logrados');
        $sheet->getStyle('D10:J10')->getAlignment()->setWrapText(true);





        ///////////////// configuracoes 

        $sheet->getStyle('D10:J10')->getAlignment()->setTextRotation(90);
        $sheet->getStyle('D10:I10')->getAlignment()->setWrapText(true);
        $sheet->getStyle("D12:H12")->getFont()->setSize(9);
        $sheet->getStyle("D10:J10")->getFont()->setSize(12);
        $sheet->getStyle("D10:P10")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("D10:P10")->getAlignment()->setVertical('center');
        $sheet->getStyle('P10')->getAlignment()->setTextRotation(90);
        $sheet->getStyle('P10')->getAlignment()->setTextRotation(90);
        $sheet->getStyle('K10:L10')->getAlignment()->setTextRotation(90);



      
 
        $sheet->setCellValue('D11', 'Fecha '.$dados_notas->fecha_inicio->processo1);
       
        $sheet->setCellValue('E11', 'Fecha '.$dados_notas->fecha_inicio->parcial1);
      
        $sheet->setCellValue('F11', 'Fecha '.$dados_notas->fecha_inicio->processo2);
          
        $sheet->setCellValue('G11', 'Fecha '.$dados_notas->fecha_inicio->parcial2);
     
       if($dados_notas->dataTrabalho){
            $sheet->setCellValue('H11', 'fecha '.$dados_notas->dataTrabalho);
       }
         
        // $sheet->setCellValue('I11', 'Fecha /06/20');
        $sheet->getStyle('D11:I11')->getAlignment()->setWrapText(true);
        $sheet->getStyle('D11:I11')->getAlignment()->setHorizontal('center');


        $sheet->setCellValue('D12', '1 Proceso ');
        $sheet->setCellValue('E12', '1 Parcial ');
        $sheet->setCellValue('F12', '2 Proceso');
        $sheet->setCellValue('G12', '2 Parcial');
        $sheet->setCellValue('H12', 'Trab. Prác.');
        $sheet->setCellValue('I12', 'Extensión');
        $sheet->getStyle('D12:I12')->getAlignment()->setWrapText(true);
        $sheet->setCellValue('J11', 'Fecha Tope 00/00/2020');
        $sheet->mergeCells('J11:J12');
        // $sheet->getStyle('J11:J12')->getAlignment()->setWrapText(true);
        $sheet->getStyle("J11:J12")->getAlignment()->setHorizontal('center');



        ///////////////////////////////////////meio esquedo/////////////////////////////////
        $sheet->setCellValue('K10', '% de la ETAPA');
        $sheet->getStyle("K10")->getFont()->setBold(true);
        $sheet->getStyle('K10')->getAlignment()->setWrapText(true);
        $sheet->getStyle("J11:J12")->getAlignment()->setVertical('center');

        $sheet->mergeCells('K10:K12');
        
        

        $sheet->setCellValue('L10', '% Asist.');
        $sheet->getStyle('L10')->getAlignment()->setWrapText(true);
        $sheet->mergeCells('L10:L12');
               $sheet->getStyle("L10")->getFont()->setBold(true);
   
        $sheet->getStyle("L10:L12")->getAlignment()->setVertical('center');



        $sheet->setCellValue('M10', 'Examen final Ordinario 1ra Mesa');
        $sheet->getStyle('M10')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('N10', 'Examen Final Complementário 2da Mesa');
        $sheet->getStyle('N10')->getAlignment()->setWrapText(true);




        $sheet->setCellValue('O10', 'Total de Puntos Acumulados en el Parcial + Total de Puntos Logrados en el Examen Fina');
        $sheet->getStyle('O10')->getAlignment()->setWrapText(true);
        $sheet->mergeCells('O10:O12');
        $sheet->getStyle("O10")->getFont()->setSize(9);



        $sheet->setCellValue('P10', 'Calificación Final');
        $sheet->getStyle("P10")->getFont()->setBold(true);
        $sheet->mergeCells('P10:P13');
        $sheet->getStyle("p10")->getFont()->setSize(11);



   
        // $sheet->getStyle("D9")->getFont()->setBold(true);
        // $sheet->getStyle("D9:I15")->getFont()->setSize(10);


        $sheet->getStyle('L11:N11')->getAlignment()->setWrapText(true);

        // $sheet->setCellValue('M11', 'Fecha /07/20')->setCellValue('N11', 'Fecha 04/07/21');
                
                
                if(isset($dados_notas->fecha_inicio->finalexamen))
                {$sheet->setCellValue('M11','Fecha '. $dados_notas->fecha_inicio->finalexamen);
          
                }
            if(isset($dados_notas->fecha_inicio->complementarexamen)){
            $sheet->setCellValue('N11', 'Fecha '.$dados_notas->fecha_inicio->complementarexamen);
        }
        $sheet->getStyle("M11:N11")->getAlignment()->setHorizontal('center');


        $sheet->getStyle("L12:N12")->getFont()->setSize(8);
        $sheet->setCellValue('L12', 'Final Escrito')
            ->setCellValue('M12', 'Final Escrito')
            ->setCellValue('N12', 'Final Escrito');




        $sheet->setCellValue('B13', 'Prueba')
            ->setCellValue('D13', '5')
            ->setCellValue('E13', '15')
            ->setCellValue('F13', '5')
            ->setCellValue('G13', '15')
            ->setCellValue('H13', '10')
            ->setCellValue('I13', '2')
            ->setCellValue('J13', '50')
            ->setCellValue('K13', '%')
            ->setCellValue('L13', '%')
            ->setCellValue('M13', '50')
            ->setCellValue('N13', '50')
            ->setCellValue('O13', '100');


        //          $sheet->getStyle('B13:O1')->applyFromArray(
        //     array(
        //         'fill' => array(
        //             'type' => PHPExcel_Style_Fill::FILL_SOLID,
        //             'color' => array('rgb' => 'FF0000')
        //         )
        //     )
        // );

        $sheet->mergeCells('B13:C13');
        $sheet->getStyle('B13:O13')->getAlignment()->SetHorizontal('center');
        $sheet->getStyle('B13')->getAlignment()->SetHorizontal('center');

        $index=1;
        $i= 14;
        $cont2=0;
        $status=0;


//   return response()->json($dados_notas->dados_notas, 200);  
        ///parte php forech
  foreach($dados_notas->dados_notas as $element){
  $soma=0;
             $cont2++;
                $sheet->getRowDimension($i)->setRowHeight(20);
             if($element->notatotal >=1 and $element->notatotal <=59){
           $calificacion = 1;
           }else if($element->notatotal >=60 and $element->notatotal <=69){
           $calificacion = 2;
           }else if($element->notatotal >=70 and $element->notatotal <=80){
           $calificacion = 3;
           }else if($element->notatotal >=80 and $element->notatotal <=90){
           $calificacion = 4;
           
               
           }else if($element->notatotal >=91 and $element->notatotal <=100){
                $calificacion = 5;
           }   
           
         $sheet->setCellValue('P'.$i, $calificacion);
         
           
           
           
              $sheet->setCellValue('A'.$i, ''.$index);
              $sheet->setCellValue('B'.$i, $element->nome_usuario);
                 $sheet->setCellValue('C'.$i, $element->doc_oficial);
                 $notas=(object)['finalexamen'=>0,'parcial1'=>0,'parcial2'=>0,'processo1'=>0,'processo2'=>0,'complementarexamen'=>0,'parcial1_recuperatoria'=>0,'parcial2_recuperatoria'=>0,'extraordinaria'=>0 ];
                   foreach($element->avaliacao as $nota){
                
                      if(isset($nota->processo1)){
                          $notas->processo1=$nota->processo1;
                           $sheet->setCellValue('D'.$i, ''.$nota->processo1);
                  
                      }
                             
                        if(isset($nota->processo2)){
                            $notas->processo2 = $nota->processo2;
                            $sheet->setCellValue('F'.$i, ''.$nota->processo2);
                        }
                         
                         
                        if(isset($nota->parcial1)){
                            $notas->parcial1=$nota->parcial1;
                            $sheet->setCellValue('E'.$i, ''. $notas->parcial1);
                        }
                                               
                        if(isset($nota->parcial1_recuperatoria) ){
                            $notas->parcial1_recuperatoria = $nota->parcial1_recuperatoria;
                        }  
        
                        if($notas->parcial1_recuperatoria >  $notas->parcial1) {
                           $notas->parcial1 = $notas->parcial1_recuperatoria;
                           $sheet->setCellValue('E'.$i, ''. $notas->parcial1);
                        }
                                   
                   
                      
                        
                        
                      
                         
                     
                        if(isset($nota->parcial2)){
                            $notas->parcial2=$nota->parcial2;
                            $sheet->setCellValue('G'.$i, ''. $notas->parcial2);
                        }
                    
                        if(isset($nota->parcial2_recuperatoria) ){
                            $notas->parcial2_recuperatoria = $nota->parcial2_recuperatoria;
                        }
       
                        if($notas->parcial2_recuperatoria > $notas->parcial2){
                            $notas->parcial2 = $notas->parcial2_recuperatoria;
                            $sheet->setCellValue('G'.$i, ''. $notas->parcial2);
                        }
                              
                            
                            
                        
                            
                             if(isset($nota->finalexamen)){
                                 $notas->finalexamen = $nota->finalexamen ;
                    $sheet->setCellValue('M'.$i, $nota->finalexamen);
                             }
                             
                              if(isset($nota->complementarexamen) ){
                          $notas->complementarexamen = $nota->complementarexamen;
                
                          $sheet->setCellValue('N'.$i, $nota->complementarexamen);
     
                              }
                         
                        //       if(isset($nota->extraordinaria)  ){
                        //     if($nota->extraordinaria > $notas->finalexamen) {
                     
                        //   $sheet->setCellValue('N'.$i, $nota->extraordinaria);
                        //     $soma = $nota->extraordinaria ;
                        // }
                        
                        
                        // }
                        if($notas->finalexamen < 1){
                              $sheet->setCellValue('M'.$i, 'A');
                                $sheet->getStyle('M'.$i)->getAlignment()->setHorizontal('right');
                      
                   }
                       
              
                
                   }
                   $soma = $notas->processo1 + $notas->processo1 + $notas->parcial1 + $notas->parcial2 ;
                     $notas=(object)['finalexamen'=>0,'parcial1'=>0,'parcial2'=>0,'processo1'=>0,'processo2'=>0,'complementarexamen'=>0,'parcial1_recuperatoria'=>0,'parcial2_recuperatoria'=>0,'extraordinaria'=>0 ];
               
                       
                             if(isset($element->somaTrabalho)){
                    $sheet->setCellValue('H'.$i, $element->somaTrabalho);
                      $soma = $soma + $element->somaTrabalho;
                          }
                               if(isset($element->ponto_extra)){
                     $sheet->setCellValue('I'.$i, $element->ponto_extra);
                     $soma = $soma + $element->ponto_extra;
                          }
                      
                          
                   
               
             
                   if(($soma/50) <0.6){
                        $texto = 'Sin Derecho';
                  }
                 
                  
    
                   
                //   if($soma >100){
                //         $sheet->setCellValue('J'.$i, '100');
                //   }else if($soma == 59){
                //       $soma = 60;
                //   $sheet->setCellValue('J'.$i, '60');
                //   }else{
                       $sheet->setCellValue('J'.$i, '=D'.$i.'+E'.$i.'+F'.$i.'+G'.$i.'+H'.$i.'+I'.$i.'');

                      $sheet->setCellValue('O'.$i, $element->notatotal);
                 
                //   }
                
                // return $element->tope;
                        if(($element->tope/50) >= 0.6){
                        // $texto = '=J'.$i.'/J13';
                          $texto =  ($element->tope/50);
                    
                           
                                 $sheet->getStyle('k', $i)->getNumberFormat()->setFormatCode('0%');
                  
                   $sheet->setCellValue('K'.$i,$texto );
  
                        }
                           if(($element->tope/50) <0.6){
                                 $sheet->setCellValue('K'.$i,'Sin Derecho' );
                 
                  }
                        if($element->tope > 100){
                               $sheet->setCellValue('K'.$i,'100%' );
                          }
              
              
             
                 if($cont2 == 17 & $status == 0 ){
                     
                     $sheet->getRowDimension(intval($i+1))->setRowHeight(80);
        $sheet->setCellValue('A'.intval($i+1), 'Firma del Docente');
        
        $sheet->setCellValue('A'.intval($i+2), 'Observación. Esta planilla representará la habilitación a la Evaluación Final y la Calificación Final del Semestre. S.A.M.C.');
        $sheet->mergeCells('A'.intval($i+2).':H'.intval($i+2));
            
        $i = $i +2;
        $cont2=0;    
        $status=1;
         $sheet->setBreak('A'.$i, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
              }
                 if($cont2 == 22){
                         $sheet->getRowDimension(intval($i+1))->setRowHeight(80);
        $sheet->setCellValue('A'.intval($i+1), 'Firma del Docente');
        $sheet->setCellValue('A'.intval($i+2), 'Observación. Esta planilla representará la habilitación a la Evaluación Final y la Calificación Final del Semestre. S.A.M.C.');
        $sheet->mergeCells('A'.intval($i+2).':H'.intval($i+2));
        $i = $i +2;
        $cont2=0;
        $sheet->setBreak('A'.$i, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
              }
              $index++;
               $i++;
        }
          if($cont2 > 0 and  $cont2 < 22){
                  $sheet->getRowDimension(intval($i))->setRowHeight(80);
        $sheet->setCellValue('A'.intval($i), 'Firma del Docente');
        $sheet->setCellValue('A'.intval($i+1), 'Observación. Esta planilla representará la habilitación a la Evaluación Final y la Calificación Final del Semestre. S.A.M.C.');
        $sheet->mergeCells('A'.intval($i+1).':H'.intval($i+1));
          }
    
      
            $footer =  22 - $cont2;
              if($cont2 > 10){
          $footer = 15 + $cont2;
      }
                      $sheet->mergeCells('C'.intval($i+$footer+1).':D'.intval($i+$footer+1));
                  $sheet->mergeCells('C'.intval($i+$footer).':D'.intval($i+$footer));
          $sheet->setCellValue('B'.intval($i+$footer), '.................................................');
          $sheet->setCellValue('C'.intval($i+$footer), '.................................................');
          $sheet->setCellValue('G'.intval($i+$footer), '.................................................');
          $sheet->setCellValue('K'.intval($i+$footer), '.................................................'); 
          $sheet->setCellValue('O'.intval($i+$footer), '.................................................');
           
                $sheet->setCellValue('B'.intval($i+$footer+1), 'Docente');
        $sheet->setCellValue('C'.intval($i+$footer+1), 'Secretaria Academica');
          $sheet->setCellValue('G'.intval($i+$footer+1), 'Diretor de carrera');
          $sheet->setCellValue('K'.intval($i+$footer+1), 'Diretor General de filial'); 
             
          $sheet->setCellValue('O'.intval($i+$footer+1), 'Secretaria General Academica');
           
      
               
                      $sheet->getStyle('A'.intval($i+$footer+1).':P'.intval($i+$footer+1))->getFont()->setSize(12);
        
            // $sheet->mergeCells('M'.intval($i+1).':O'.intval($i+1));
            
        $sheet->getStyle('A'.intval($i+$footer+1).':O'.intval($i+$footer+1))->getAlignment()->setHorizontal('center');
          $sheet->getStyle('A'.intval($i+$footer).':O'.intval($i+$footer))->getAlignment()->setHorizontal('center');
            
 

    
$sheet->getStyle('D13:O13')->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('7cd5dc');
  
    $sheet->getStyle('j14:J'.intval($i-1))->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('ffff00');
    
    $sheet->getStyle('O14:O'.$i)->getAlignment()->setHorizontal('center');
    $sheet->getStyle('O14:O'.intval($i-1))->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('ffff00');
    
    $sheet->getStyle('K14:K'.$i)->getAlignment()->setHorizontal('center');
    $sheet->getStyle('K14:K'.$i)->getFont()->setSize(8);
    $sheet->getStyle('K14:K'.$i)
    ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        //  $spreadsheet2 = $spreadsheet;
     $sheet->getStyle('N14:N'.$i)->getAlignment()->setHorizontal('center');
     $sheet->getStyle('M14:M'.$i)->getAlignment()->setHorizontal('center');
     $sheet->getStyle('P14:P'.$i)->getAlignment()->setHorizontal('center');
     $sheet->getStyle('A14:A'.$i)->getAlignment()->setHorizontal('center');
      $sheet->getStyle('D12:N12')->getAlignment()->setHorizontal('center');
     $sheet->getStyle('B14:B'.$i)->getAlignment()->setHorizontal('center');
     
    //     $sheet->getStyle('K14:K'.$i)->getNumberFormat()
    // ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
      $sheet->getStyle('C14:C'.$i)->getNumberFormat()
    ->setFormatCode('0');
        //   $sheet->getStyle('C14:C'.$i)->getAlignment()->setHorizontal('center');
          
              $sheet->getStyle('C14:J'.$i)->getAlignment()->setHorizontal('center');
   $sheet
->getPageSetup()
    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
    
    
    
    


 $sheet->getPageSetup()->setPrintArea('A1:P'.intval($i+$footer+1));



    

    $sheet
    ->getHeaderFooter()->setEvenFooter( '&RPage &P of &N');


// }

   $pagina = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];
        if($cont2 == 0){
            $i = $i -2;
        }
        $sheet->getStyle('A1:P'.intval($i+1))->applyFromArray($pagina);
        
           $pagina2 = [
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];
        $sheet->getStyle('A'.intval($i+1).':P'.intval($i+$footer+1))->applyFromArray($pagina2);
        
        

       $sheet->getPageSetup()
        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(8, 13);


           $sheet->setTitle(substr($dados_notas->nome_materia,0,30));
              
                $indeci++;
   if(count($todosasturmas->tudonota) > $indeci){
            $spreadsheet->createSheet();

            // Add some data to the second sheet, resembling some different data types
       
         
            $spreadsheet->setActiveSheetIndex($indeci);
}
            


}

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
       
        $writer = new Xlsx($spreadsheet);
        $writer->save('relatorio.xlsx');
        
        $file="relatorio.xlsx";// tive que fazer assim, estava dando erro no retorno
        return response()->download(public_path($file));
        
        
    }
    
    public function gerarExcelProfessores()
    {
        
              $filename = 'Relatorio Disciplinas Professores.xlsx';
   

        $dados_professores = $this->professorcontroller->listaProfessores()->original;
        //   return response()->json($dados_professores, 200);

        $spreadsheet = new Spreadsheet();

            $sheet = $spreadsheet->getActiveSheet();

            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(40);
              $sheet->getColumnDimension('E')->setWidth(25);
                 $sheet->getColumnDimension('F')->setWidth(25);
            $sheet->getStyle("A:C")->getAlignment()->setHorizontal('left');

            $sheet->setCellValue('A1', 'RELATORIO DISCIPLINAS E SEUS PROFESSORES');
            $sheet->getStyle("A1")->getFont()->setSize(14);
            $sheet->mergeCells('A1:E2');

            $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');

            $sheet->setCellValue('A3', 'Sigla Filial')->setCellValue('B3', 'Nome Semestre')->setCellValue('C3', 'Nome Disciplina')->setCellValue('D3', 'Nome professor')->setCellValue('E3', 'email')
           ->setCellValue('F3', 'Telefone');
            $sheet->getStyle("A3:F3")->getFont()->setBold(true);





            $i = 3;
            foreach ($dados_professores as $element) { 

            $i++;
            
                  $sheet->setCellValue('A' . $i, $element->sigla_filial);
                 $sheet->setCellValue('B' . $i, $element->nome_semestre);
                $sheet->setCellValue('C' . $i,$element->nome_disciplinas );
                $sheet->setCellValue('D' . $i, $element->nome_funcionario);
                $sheet->setCellValue('E' . $i, $element->email);
                    $sheet->setCellValue('F' . $i, $element->telefone_celular1);
             
             
            }

            $pagina = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '00000000'],
                    ],
                ],
            ];
            $sheet->getStyle('A1:D' . $i)->applyFromArray($pagina);

            // $sheet->setTitle('Relatorio');
            // $spreadsheet->createSheet();

            // Add some data to the second sheet, resembling some different data types
            // $index++;
            // $spreadsheet->setActiveSheetIndex($index);
        
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
       
        $writer = new Xlsx($spreadsheet);
        $writer->save('relatorio.xlsx');
        
        $file="relatorio.xlsx";// tive que fazer assim, estava dando erro no retorno
        return response()->download(public_path($file));
        

    }   
    
    public function gerarExcelCaptadores()
    {
        
              $filename = 'Relatorio captadores .xlsx';
   

        $dados_captadores = $this->usuariosController->listaCaptadores()->original;
        //   return response()->json($dados_professores, 200);

        $spreadsheet = new Spreadsheet();

            $sheet = $spreadsheet->getActiveSheet();

            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(20);
            //   $sheet->getColumnDimension('E')->setWidth(25);
            //      $sheet->getColumnDimension('F')->setWidth(25);
            $sheet->getStyle("A:C")->getAlignment()->setHorizontal('left');

            $sheet->setCellValue('A1', 'RELATORIO CAPTADORES');
            $sheet->getStyle("A1")->getFont()->setSize(14);
            $sheet->mergeCells('A1:D2');

            $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');

            $sheet->setCellValue('A3', 'Nome')->setCellValue('B3', 'Email')->setCellValue('C3', 'Telefone')->setCellValue('D3', 'quantidade');
            $sheet->getStyle("A3:F3")->getFont()->setBold(true);





            $i = 3;
            foreach ($dados_captadores as $element) { 

            $i++;
            
                  $sheet->setCellValue('A' . $i, $element->nome.' '.$element->sobrenome );
                 $sheet->setCellValue('B' . $i, $element->email);
                // $sheet->setCellValue('C' . $i,$element->telefone );
                $sheet->setCellValue('D' . $i, $element->total);
                $spreadsheet->getActiveSheet()->setCellValueExplicit('C' . $i,$element->telefone,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                
           
             
             
            }

            $pagina = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '00000000'],
                    ],
                ],
            ];
            $sheet->getStyle('A1:D' . $i)->applyFromArray($pagina);

            // $sheet->setTitle('Relatorio');
            // $spreadsheet->createSheet();

            // Add some data to the second sheet, resembling some different data types
            // $index++;
            // $spreadsheet->setActiveSheetIndex($index);
        
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
       
        $writer = new Xlsx($spreadsheet);
        $writer->save('relatorio.xlsx');
        
        $file="relatorio.xlsx";// tive que fazer assim, estava dando erro no retorno
        return response()->download(public_path($file));
        

    }    
    
    public function gerarRelatorioMovimentacao()
    {
        
                      $filename = 'Relatorio Movimentação .xlsx';
                      $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'http://sistema.hostbrs.com.br/ateste/excel/relatorio_auditoria/123',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'GET',
                  CURLOPT_HTTPHEADER => array(
                    'Cookie: stb_session=hik7j6i1h7o92c7u2bllhj8t7p2nd8ua'
                  ),
                ));

            $response = curl_exec($curl);

            curl_close($curl);
        // echo $response;
//   dd(json_decode($response));

        $dados_movimentacao =   json_decode($response) ;
        // return response()->json($dados_movimentacao,200);
        // return $dados_movimentacao->original;
        // foreach($dados_movimentacao as $item){
        //     return $item->status_antigo;
        // }
        

        $spreadsheet = new Spreadsheet();

            $sheet = $spreadsheet->getActiveSheet();

            $sheet->getColumnDimension('A')->setWidth(35);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(15);
            $sheet->getColumnDimension('G')->setWidth(15);
             
            //   $sheet->getColumnDimension('E')->setWidth(25);
            //      $sheet->getColumnDimension('F')->setWidth(25);
            $sheet->getStyle("A:C")->getAlignment()->setHorizontal('left');

            $sheet->setCellValue('A1', 'RELATORIO MOVIMENTAÇÕES JOABE');
            $sheet->getStyle("A1")->getFont()->setSize(14);
            $sheet->mergeCells('A1:G2');

            $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');

            $sheet->setCellValue('A3', 'Nome')->setCellValue('B3', 'status_antigo')->setCellValue('C3', 'status_novo')->setCellValue('D3', 'atividade')->setCellValue('E3','Data Criacao')
            ->setCellValue('F3','Valor Antigo')->setCellValue('G3','Valor Novo');
            $sheet->getStyle("A3:G3")->getFont()->setBold(true);





            $i = 3;
            foreach ($dados_movimentacao as $element) { 
              $i++;
            
            if(count((array)$element)==11){
                  $sheet->setCellValue('A' . $i, $element->nome_usuario);
                  $sheet->setCellValue('B' . $i, $element->status_antigo );
                 $sheet->setCellValue('C' . $i, $element->status_novo);
                // $sheet->setCellValue('' . $i,$element->telefone );
                $sheet->setCellValue('D' . $i, $element->atividade);
                $sheet->setCellValue('E' . $i, $element->created);
                 $sheet->setCellValue('F' . $i, $element->valor_antigo);
                 $sheet->setCellValue('G' . $i, $element->valor_novo);
                 
                
              
                
                // $spreadsheet->getActiveSheet()->setCellValueExplicit('C' . $i,$element->telefone,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            }
           
             
             
            }

            $pagina = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '00000000'],
                    ],
                ],
            ];
            $sheet->getStyle('A1:G' . $i)->applyFromArray($pagina);

            // $sheet->setTitle('Relatorio');
            // $spreadsheet->createSheet();

            // Add some data to the second sheet, resembling some different data types
            // $index++;
            // $spreadsheet->setActiveSheetIndex($index);
        
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
       
        $writer = new Xlsx($spreadsheet);
        $writer->save('relatorio.xlsx');
        
        $file="relatorio.xlsx";// tive que fazer assim, estava dando erro no retorno
        return response()->download(public_path($file));
    }
    
    public function gerarExcelCalificacion($id_turma)
    {
        // $id_oferta_disciplina = 2280;
        
        

        $todosasturmas = (object) $this->tentativacontroller->lista_notas_disciplina($id_turma)->original;


// if($todosasturmas->dados_notas ==""){
//     return response()->json('não ha notas nessa turma',500);
// }



        //  return response()->json($dados_notas, 200); 
    //     if( $dados_notas->dados_notas == ''){
    //   return response()->json('Não há notas nessa unidade', 500); 
    //     }
        
        
        $filename = 'Relatorio notas '.$todosasturmas->nome_turmatotal.'.xlsx';
        
        
        $indeci=0;
             $spreadsheet = new Spreadsheet();

        $spreadsheet->setActiveSheetIndex(0);
        foreach ($todosasturmas->tudonota as $dados_notas) {

// return response()->json($dados_notas, 200); 

            $sheet = $spreadsheet->getActiveSheet();


        $sheet->getRowDimension(1)->setRowHeight(20);
        $sheet->getRowDimension(10)->setRowHeight(100);
        $sheet->getColumnDimension('A')->setWidth(4);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(20);

        $sheet->getColumnDimension('D')->setWidth(11);
        $sheet->getColumnDimension('E')->setWidth(11);
        $sheet->getColumnDimension('F')->setWidth(11);
        $sheet->getColumnDimension('G')->setWidth(11);
        $sheet->getColumnDimension('H')->setWidth(11);
        $sheet->getColumnDimension('I')->setWidth(11);

        $sheet->getColumnDimension('M')->setWidth(13);
        $sheet->getColumnDimension('N')->setWidth(13);
        $sheet->getColumnDimension('O')->setWidth(20);


// for($i=0;$i<10;$i++){




        //////////////////////////ESTILIZAR BORDAS////////////////////////////////




     

        // $styleArray = [
        //     'borders' => [
        //         'bottom' => [
        //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
        //             'color' => ['argb' => '00000000'],
        //         ],
        //     ],
        // ];
        // $sheet->getStyle('C1:L7')->applyFromArray($styleArray);




        //=======================================================================//



        $IMG = 'https://api.ucpvirtual.com.br/storage/documentos/logo.jpeg';
        $row_num = 1;
        if (isset($IMG) && !empty($IMG)) {
            $imageType = "png";

            if (strpos($IMG, ".png") === false) {
                $imageType = "jpg";
            }

            $drawing = new MemoryDrawing();
            // $sheet->getColumnDimension('A')->getRowDimension($row_num)->setWidth(10);
            // $sheet->getRowDimension($row_num)->setRowHeight(50);
            $sheet->mergeCells('A1:B7');
            $gdImage = ($imageType == 'png') ? imagecreatefrompng($IMG) : imagecreatefromjpeg($IMG);

            $drawing->setResizeProportional(false);
            $drawing->setImageResource($gdImage);
            $drawing->setRenderingFunction(\PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::RENDERING_JPEG);
            $drawing->setMimeType(\PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::MIMETYPE_DEFAULT);
            $drawing->setWidth(160);
            $drawing->setHeight(130);
            $drawing->setOffsetX(80);
            $drawing->setOffsetY(10);
            // $drawing->setCoordinates('C'.$row_num);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());
            //$row_num++;
        }



        ////////////////parte superior meio////////////////////////




        $sheet->setCellValue('C1', 'FACULTAD DE CIENCIAS DE LA SALUD');

        $sheet->getStyle("C1:M1")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C1:M1")->getFont()->setSize(18);
        $sheet->mergeCells('C1:M1');


        $sheet->setCellValue('C2', 'CARRERA DE MEDICINA');
        $sheet->getStyle("C2:M2")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C2:M2")->getFont()->setSize(14);
        $sheet->mergeCells('C2:M2');


        $sheet->setCellValue('C3', $dados_notas->nome_filial);

        $sheet->getStyle("C3:M3")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C3:M3")->getFont()->setSize(12);
        $sheet->mergeCells('C3:M3');

        ////////////////parte superior meio////////////////////////


        ///////////// ultima parte do cabecalho superior direiro///////////////

        $sheet->setCellValue('N1', 'Escala');
        $sheet->getStyle("N1:P1")->getAlignment()->setHorizontal('center');
        $sheet->mergeCells('N1:P1');

        $sheet->setCellValue('N2', '100');
        $sheet->getStyle("N2")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("N2")->getFont()->setBold(true);

        //      $sheet->setCellValue('M2','100');
        //   $sheet->getStyle("M2")->getAlignment()->setHorizontal('center');
        $sheet->mergeCells('O2:P2');

        $sheet->setCellValue('N3', '1');
        $sheet->getStyle("N3:N7")->getAlignment()->setHorizontal('center');
        $sheet->setCellValue('N4', '60');
        $sheet->setCellValue('N5', '70');
        $sheet->setCellValue('N6', '81');
        $sheet->setCellValue('N7', '91');
        

        $sheet->setCellValue('O3', '59');
        $sheet->getStyle("O3:O7")->getAlignment()->setHorizontal('center');
        $sheet->setCellValue('O4', '69');
        $sheet->setCellValue('O5', '80');
        $sheet->setCellValue('O6', '90');
        $sheet->setCellValue('O7', '100');
        

        $sheet->setCellValue('P3', '1');
        $sheet->getStyle("P3:P7")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("P3:P7")->getFont()->setBold(true);
        $sheet->setCellValue('P4', '2');
        $sheet->setCellValue('P5', '3');
        $sheet->setCellValue('P6', '4');
        $sheet->setCellValue('P7', '5');
        



        ///////////// ultima parte do cabecalho superior direiro///////////////



        //////////////////////////Meio informacoes /////////////////////////////////////


        $sheet->setCellValue('C4', "Aprobado por Ley Nº 3153/06");
        $sheet->mergeCells('C4:M4');
        $sheet->getStyle("C4:M4")->getFont()->setSize(7);

        $sheet->setCellValue('C5', "ASIGNATURA: ".$dados_notas->nome_materia);
        $sheet->mergeCells('C5:M5');


        $sheet->setCellValue('C6', "Profesor/a: ".$dados_notas->nome_professor);
        $sheet->mergeCells('C6:M6');


        $sheet->setCellValue('C7', "Carrera: Medicina ");
        $sheet->mergeCells('C7:M7');



//------------------------------------------------------comeca aqui------------------------------------------------------------------------------


        $sheet->setCellValue('A8', ''.$dados_notas->nome_semestre.' '."'".$dados_notas->nome_turma."'".$dados_notas->nome_periodo_anual.' '.$dados_notas->nome_ano);
        $sheet->mergeCells('A8:B8');
        $sheet->getStyle("A8:B8")->getFont()->setSize(12);

        $sheet->setCellValue('C8', "Planilla de Proceso de la Valoración de los Aprendizajes-  Primeira Convocatoria 2021  ");
        $sheet->mergeCells('C8:P8');
        $sheet->getStyle("C8:P8")->getFont()->setSize(16);
        $sheet->getStyle("C8")->getAlignment()->setHorizontal('center');



        /////////////////////////////////////////////////////////////////////////////////////


        //////////////////////parte meio inferior em baixo da imagem ////////////////////////


        $sheet->setCellValue('A9', 'Nº');
        $sheet->mergeCells('A9:A13');
        $sheet->getStyle("A9")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("A9")->getAlignment()->setVertical('center');
        $sheet->getStyle("A9:A13")->getFont()->setSize(7);

        $sheet->setCellValue('B9', 'Nombres y Apellidos');
        $sheet->mergeCells('B9:B12');
        $sheet->getStyle("B9")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("B9")->getAlignment()->setVertical('center');
        $sheet->getStyle("B9:B13")->getFont()->setSize(8);




        $sheet->setCellValue('C9', 'Documento de Identidad');
            $sheet->mergeCells('C9:C12');
        $sheet->getStyle("C9")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C9")->getAlignment()->setVertical('center');
        $sheet->getStyle("C9:C13")->getFont()->setSize(8);



        $sheet->setCellValue('D9', 'Reactivos de Evaluación de Proceso');
        $sheet->mergeCells('D9:I9');
        $sheet->getStyle("D9")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("D9")->getFont()->setBold(true);
        $sheet->getStyle("D9:I9")->getFont()->setSize(14);


        ///////////////////////////////////////////////////////////////////////////////

        $sheet->setCellValue('D10', '1 Prueba de Proceso');

        $sheet->setCellValue('E10', '1  Prueba parcial');

        $sheet->setCellValue('F10', '2 Prueba de Proceso');
        $sheet->setCellValue('G10', '2 Prueba Parcial');
        $sheet->setCellValue('H10', 'Trabajo Práctico');
        $sheet->setCellValue('I10', 'Extensión  ');
        $sheet->setCellValue('J10', 'Total de Puntos Logrados');
        
        $sheet->getStyle('D10:J10')->getAlignment()->setWrapText(true);





        ///////////////// configuracoes 

        $sheet->getStyle('D10:J10')->getAlignment()->setTextRotation(90);
        $sheet->getStyle('D10:I10')->getAlignment()->setWrapText(true);
        $sheet->getStyle("D12:H12")->getFont()->setSize(9);
        $sheet->getStyle("D10:J10")->getFont()->setSize(12);
        $sheet->getStyle("D10:P10")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("D10:P10")->getAlignment()->setVertical('center');
        $sheet->getStyle('P10')->getAlignment()->setTextRotation(90);
        $sheet->getStyle('P10')->getAlignment()->setTextRotation(90);
        $sheet->getStyle('K10:L10')->getAlignment()->setTextRotation(90);



      
 
        $sheet->setCellValue('D11', 'Fecha '.$dados_notas->fecha_inicio->processo1);
       
        $sheet->setCellValue('E11', 'Fecha '.$dados_notas->fecha_inicio->parcial1);
      
        $sheet->setCellValue('F11', 'Fecha '.$dados_notas->fecha_inicio->processo2);
          
        $sheet->setCellValue('G11', 'Fecha '.$dados_notas->fecha_inicio->parcial2);
     
       if($dados_notas->dataTrabalho){
            $sheet->setCellValue('H11', 'fecha '.$dados_notas->dataTrabalho);
       }
         
        // $sheet->setCellValue('I11', 'Fecha /06/20');
        $sheet->getStyle('D11:I11')->getAlignment()->setWrapText(true);
        $sheet->getStyle('D11:I11')->getAlignment()->setHorizontal('center');


        $sheet->setCellValue('D12', '1 Proceso ');
        $sheet->setCellValue('E12', '1 Parcial ');
        $sheet->setCellValue('F12', '2 Proceso');
        $sheet->setCellValue('G12', '2 Parcial');
        $sheet->setCellValue('H12', 'Trab. Prác.');
        $sheet->setCellValue('I12', 'Extensión');
        $sheet->getStyle('D12:I12')->getAlignment()->setWrapText(true);
        $sheet->setCellValue('J11', 'Fecha Tope 00/00/2020');
        $sheet->mergeCells('J11:J12');
        // $sheet->getStyle('J11:J12')->getAlignment()->setWrapText(true);
        $sheet->getStyle("J11:J12")->getAlignment()->setHorizontal('center');



        ///////////////////////////////////////meio esquedo/////////////////////////////////
        $sheet->setCellValue('K10', '% de la ETAPA');
        $sheet->getStyle("K10")->getFont()->setBold(true);
        $sheet->getStyle('K10')->getAlignment()->setWrapText(true);
        $sheet->getStyle("J11:J12")->getAlignment()->setVertical('center');

        $sheet->mergeCells('K10:K12');
        
        

        $sheet->setCellValue('L10', '% Asist.');
        $sheet->getStyle('L10')->getAlignment()->setWrapText(true);
        $sheet->mergeCells('L10:L12');
               $sheet->getStyle("L10")->getFont()->setBold(true);
   
        $sheet->getStyle("L10:L12")->getAlignment()->setVertical('center');



        $sheet->setCellValue('M10', 'Examen final Ordinario 1ra Mesa');
        $sheet->getStyle('M10')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('N10', 'Examen Final Complementário 2da Mesa');
        $sheet->getStyle('N10')->getAlignment()->setWrapText(true);




        $sheet->setCellValue('O10', 'Total de Puntos Acumulados en el Parcial + Total de Puntos Logrados en el Examen Fina');
        $sheet->getStyle('O10')->getAlignment()->setWrapText(true);
        $sheet->mergeCells('O10:O12');
        $sheet->getStyle("O10")->getFont()->setSize(9);



        $sheet->setCellValue('P10', 'Calificación Final');
        $sheet->getStyle("P10")->getFont()->setBold(true);
        $sheet->mergeCells('P10:P13');
        $sheet->getStyle("p10")->getFont()->setSize(11);



   
        // $sheet->getStyle("D9")->getFont()->setBold(true);
        // $sheet->getStyle("D9:I15")->getFont()->setSize(10);


        $sheet->getStyle('L11:N11')->getAlignment()->setWrapText(true);

        // $sheet->setCellValue('M11', 'Fecha /07/20')->setCellValue('N11', 'Fecha 04/07/21');
                
                
                if(isset($dados_notas->fecha_inicio->finalexamen))
                {$sheet->setCellValue('M11','Fecha '. $dados_notas->fecha_inicio->finalexamen);
          
                }
            if(isset($dados_notas->fecha_inicio->complementarexamen)){
            $sheet->setCellValue('N11', 'Fecha '.$dados_notas->fecha_inicio->complementarexamen);
        }
        $sheet->getStyle("M11:N11")->getAlignment()->setHorizontal('center');


        $sheet->getStyle("L12:N12")->getFont()->setSize(8);
        $sheet->setCellValue('L12', 'Final Escrito')
            ->setCellValue('M12', 'Final Escrito')
            ->setCellValue('N12', 'Final Escrito');




        $sheet->setCellValue('B13', 'Prueba')
            ->setCellValue('D13', '5')
            ->setCellValue('E13', '15')
            ->setCellValue('F13', '5')
            ->setCellValue('G13', '15')
            ->setCellValue('H13', '10')
            ->setCellValue('I13', '2')
            ->setCellValue('J13', '50')
            ->setCellValue('K13', '%')
            ->setCellValue('L13', '%')
            ->setCellValue('M13', '50')
            ->setCellValue('N13', '50')
            ->setCellValue('O13', '100');


        //          $sheet->getStyle('B13:O1')->applyFromArray(
        //     array(
        //         'fill' => array(
        //             'type' => PHPExcel_Style_Fill::FILL_SOLID,
        //             'color' => array('rgb' => 'FF0000')
        //         )
        //     )
        // );

        $sheet->mergeCells('B13:C13');
        $sheet->getStyle('B13:O13')->getAlignment()->SetHorizontal('center');
        $sheet->getStyle('B13')->getAlignment()->SetHorizontal('center');

        $index=1;
        $i= 14;
        $cont2=0;
        $status=0;


//   return response()->json($dados_notas->dados_notas, 200);  
        ///parte php forech
  $cont1=0;//contador calificacion 1
  $cont3=0;//contador calificacion 2
  foreach($dados_notas->dados_notas as $element){
  $soma=0;
             $cont2++;
                $sheet->getRowDimension($i)->setRowHeight(20);
             if($element->notatotal >=1 and $element->notatotal <=59){
           $calificacion = 1;
           }else if($element->notatotal >=60 and $element->notatotal <=69){
           $calificacion = 2;
           }else if($element->notatotal >=70 and $element->notatotal <=80){
           $calificacion = 3;
           }else if($element->notatotal >=80 and $element->notatotal <=90){
           $calificacion = 4;
           
               
           }else if($element->notatotal >=91 and $element->notatotal <=100){
                $calificacion = 5;
           }   
           
           
           if($calificacion >0 and $calificacion < 3){
              
         $sheet->setCellValue('P'.$i, $calificacion);
         
         if($calificacion==1){
             $cont1++;
         }
         
         if($calificacion==2){
             $cont3++;
         }
           
           
           
              $sheet->setCellValue('A'.$i, ''.$index);
              $sheet->setCellValue('B'.$i, $element->nome_usuario);
                 $sheet->setCellValue('C'.$i, $element->doc_oficial);
                 $notas=(object)['finalexamen'=>0,'parcial1'=>0,'parcial2'=>0,'processo1'=>0,'processo2'=>0,'complementarexamen'=>0,'parcial1_recuperatoria'=>0,'parcial2_recuperatoria'=>0,'extraordinaria'=>0 ];
                   foreach($element->avaliacao as $nota){
                
                      if(isset($nota->processo1)){
                          $notas->processo1=$nota->processo1;
                   $sheet->setCellValue('D'.$i, ''.$nota->processo1);
                  
                      }
                             
                        if(isset($nota->processo2)){
                            $notas->processo2 = $nota->processo2;
                            $sheet->setCellValue('F'.$i, ''.$nota->processo2);
                        }
                         
                         
                        if(isset($nota->parcial1)){
                            $notas->parcial1=$nota->parcial1;
                            $sheet->setCellValue('E'.$i, ''. $notas->parcial1);
                        }
                                               
                        if(isset($nota->parcial1_recuperatoria) ){
                            $notas->parcial1_recuperatoria = $nota->parcial1_recuperatoria;
                        }  
        
                        if($notas->parcial1_recuperatoria >  $notas->parcial1) {
                           $notas->parcial1 = $notas->parcial1_recuperatoria;
                           $sheet->setCellValue('E'.$i, ''. $notas->parcial1);
                        }
                                   
                   
                      
                        
                        
                      
                         
                     
                        if(isset($nota->parcial2)){
                            $notas->parcial2=$nota->parcial2;
                            $sheet->setCellValue('G'.$i, ''. $notas->parcial2);
                        }
                    
                        if(isset($nota->parcial2_recuperatoria) ){
                            $notas->parcial2_recuperatoria = $nota->parcial2_recuperatoria;
                        }
       
                        if($notas->parcial2_recuperatoria > $notas->parcial2){
                            $notas->parcial2 = $notas->parcial2_recuperatoria;
                            $sheet->setCellValue('G'.$i, ''. $notas->parcial2);
                        }
                              
                            
                            
                        
                            
                             if(isset($nota->finalexamen)){
                                 $notas->finalexamen = $nota->finalexamen ;
                    $sheet->setCellValue('M'.$i, $nota->finalexamen);
                             }
                             
                              if(isset($nota->complementarexamen) ){
                          $notas->complementarexamen = $nota->complementarexamen;
                
                          $sheet->setCellValue('N'.$i, $nota->complementarexamen);
     
                              }
                         
                        //       if(isset($nota->extraordinaria)  ){
                        //     if($nota->extraordinaria > $notas->finalexamen) {
                     
                        //   $sheet->setCellValue('N'.$i, $nota->extraordinaria);
                        //     $soma = $nota->extraordinaria ;
                        // }
                        
                        
                        // }
                        if($notas->finalexamen < 1){
                              $sheet->setCellValue('M'.$i, 'A');
                                $sheet->getStyle('M'.$i)->getAlignment()->setHorizontal('right');
                      
                   }
                       
              
                
                   }
                   $soma = $notas->processo1 + $notas->processo1 + $notas->parcial1 + $notas->parcial2 ;
                     $notas=(object)['finalexamen'=>0,'parcial1'=>0,'parcial2'=>0,'processo1'=>0,'processo2'=>0,'complementarexamen'=>0,'parcial1_recuperatoria'=>0,'parcial2_recuperatoria'=>0,'extraordinaria'=>0 ];
               
                       
                             if(isset($element->somaTrabalho)){
                    $sheet->setCellValue('H'.$i, $element->somaTrabalho);
                      $soma = $soma + $element->somaTrabalho;
                          }
                               if(isset($element->ponto_extra)){
                     $sheet->setCellValue('I'.$i, $element->ponto_extra);
                     $soma = $soma + $element->ponto_extra;
                          }
                      
                          
                   
               
             
                   if(($soma/50) <0.6){
                        $texto = 'Sin Derecho';
                  }
                 
                  
    
                   
                //   if($soma >100){
                //         $sheet->setCellValue('J'.$i, '100');
                //   }else if($soma == 59){
                //       $soma = 60;
                //   $sheet->setCellValue('J'.$i, '60');
                //   }else{
                       $sheet->setCellValue('J'.$i, '=D'.$i.'+E'.$i.'+F'.$i.'+G'.$i.'+H'.$i.'+I'.$i.'');

                      $sheet->setCellValue('O'.$i, $element->notatotal);
                 
                //   }
                
                // return $element->tope;
                        if(($element->tope/50) >= 0.6){
                        // $texto = '=J'.$i.'/J13';
                          $texto =  ($element->tope/50);
                    
                           
                                 $sheet->getStyle('k', $i)->getNumberFormat()->setFormatCode('0%');
                  
                   $sheet->setCellValue('K'.$i,$texto );
  
                        }
                           if(($element->tope/50) <0.6){
                                 $sheet->setCellValue('K'.$i,'Sin Derecho' );
                 
                  }
                        if($element->tope > 100){
                               $sheet->setCellValue('K'.$i,'100%' );
                          }
                          
              
             
             
              
              $index++;
               $i++;
        }
        
  }
       $sheet->setCellValue('O'.intval($i+1), "Total calificaciones 1");
       $sheet->setCellValue('O'.intval($i+2), "Total calificaciones 2");
       $sheet->setCellValue('P'.intval($i+1), $cont1);
            $sheet->setCellValue('P'.intval($i+2), $cont3);
    
      
            $footer =  22 - $cont2;
              if($cont2 > 10){
          $footer = 15 + $cont2;
      }
                      $sheet->mergeCells('C'.intval($i+$footer+1).':D'.intval($i+$footer+1));
                  $sheet->mergeCells('C'.intval($i+$footer).':D'.intval($i+$footer));
          $sheet->setCellValue('B'.intval($i+$footer), '.................................................');
          $sheet->setCellValue('C'.intval($i+$footer), '.................................................');
          $sheet->setCellValue('G'.intval($i+$footer), '.................................................');
          $sheet->setCellValue('K'.intval($i+$footer), '.................................................'); 
          $sheet->setCellValue('O'.intval($i+$footer), '.................................................');
           
                $sheet->setCellValue('B'.intval($i+$footer+1), 'Docente');
        $sheet->setCellValue('C'.intval($i+$footer+1), 'Secretaria Academica');
          $sheet->setCellValue('G'.intval($i+$footer+1), 'Diretor de carrera');
          $sheet->setCellValue('K'.intval($i+$footer+1), 'Diretor General de filial'); 
             
          $sheet->setCellValue('O'.intval($i+$footer+1), 'Secretaria General Academica');
           
      
               
                      $sheet->getStyle('A'.intval($i+$footer+1).':P'.intval($i+$footer+1))->getFont()->setSize(12);
        
            // $sheet->mergeCells('M'.intval($i+1).':O'.intval($i+1));
            
        $sheet->getStyle('A'.intval($i+$footer+1).':O'.intval($i+$footer+1))->getAlignment()->setHorizontal('center');
          $sheet->getStyle('A'.intval($i+$footer).':O'.intval($i+$footer))->getAlignment()->setHorizontal('center');
            
 

    
    $sheet->getStyle('D13:O13')->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('7cd5dc');
  
    $sheet->getStyle('j14:J'.intval($i-1))->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('ffff00');
    
    $sheet->getStyle('O14:O'.$i)->getAlignment()->setHorizontal('center');
    $sheet->getStyle('O14:O'.intval($i-1))->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('ffff00');
   
    
      $sheet->getStyle('K14:K'.$i)->getAlignment()->setHorizontal('center');
      $sheet->getStyle('K14:K'.$i)->getFont()->setSize(8);
      $sheet->getStyle('K14:K'.$i)
     ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        //  $spreadsheet2 = $spreadsheet;
        
    //     $sheet->getStyle('K14:K'.$i)->getNumberFormat()
    // ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
      $sheet->getStyle('C14:C'.$i)->getNumberFormat()
    ->setFormatCode('0');
        //   $sheet->getStyle('C14:C'.$i)->getAlignment()->setHorizontal('center');
          
              $sheet->getStyle('C14:J'.$i)->getAlignment()->setHorizontal('center');
   $sheet
->getPageSetup()
    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
    
    
    
    


 $sheet->getPageSetup()->setPrintArea('A1:P'.intval($i+$footer+1));



    

    $sheet
    ->getHeaderFooter()->setEvenFooter( '&RPage &P of &N');


// }

   $pagina = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];
        if($cont2 == 0){
            $i = $i -2;
        }
        $sheet->getStyle('A1:P'.intval($i+1))->applyFromArray($pagina);
        
           $pagina2 = [
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];
        $sheet->getStyle('A'.intval($i+1).':P'.intval($i+$footer+1))->applyFromArray($pagina2);
        
        

       $sheet->getPageSetup()
        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(8, 13);


           $sheet->setTitle(substr($dados_notas->nome_materia,0,30));
              
                $indeci++;
   if(count($todosasturmas->tudonota) > $indeci){
            $spreadsheet->createSheet();

            // Add some data to the second sheet, resembling some different data types
       
         
            $spreadsheet->setActiveSheetIndex($indeci);
}
            


}

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
       
        $writer = new Xlsx($spreadsheet);
        $writer->save('relatorio.xlsx');
        
        $file="relatorio.xlsx";// tive que fazer assim, estava dando erro no retorno
        return response()->download(public_path($file));
        
        
    }
    
    
}
