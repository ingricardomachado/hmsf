<?php

use App\Models\Sale;
use App\Models\CreditNote;
use App\Models\ProductSale;
use App\Models\CreditNoteProduct;
use App\Models\Discount;
	
    //Helper for active class in side bar menu
    function set_active($path, $active = 'active') {

        return call_user_func_array('Request::is', (array)$path) ? $active : '';

    }

    function money_fmt($value)
	{
    	if (Session::get('money_format') == 'PC2'){
    		return number_format($value,2,',','.');
    	}else if (Session::get('money_format') == 'CP2'){
    		return number_format($value,2,'.',',');    	
    	}else{
    		return number_format($value,2,',','.');
    	}
	}

    function hour_fmt($hora) {
        $arrayHora = explode(":",$hora);
        return $arrayHora[0].":".$arrayHora[1]; 
    }
    
    function date_fmt($date) {
        $arrayDate = explode("-",$date);
        return $arrayDate[2]."/".$arrayDate[1]."/".$arrayDate[0]; 
    }

    function month_letter($month, $format)
    {        
        $month_letter = '';
        switch ($month) 
        {
            case 1:
            ($format=='lg')?$month_letter = 'Enero':$month_letter = 'Ene';
            break;
            case 2:
            ($format=='lg')?$month_letter = 'Febrero':$month_letter = 'Feb';
            break;
            case 3:
            ($format=='lg')?$month_letter = 'Marzo':$month_letter = 'Mar';
            break;
            case 4:
            ($format=='lg')?$month_letter = 'Abril':$month_letter = 'Abr';
            break;
            case 5:
            ($format=='lg')?$month_letter = 'Mayo':$month_letter = 'May';
            break;
            case 6:
            ($format=='lg')?$month_letter = 'Junio':$month_letter = 'Jun';
            break;
            case 7:
            ($format=='lg')?$month_letter = 'Julio':$month_letter = 'Jul';
            break;
            case 8:
            ($format=='lg')?$month_letter = 'Agosto':$month_letter = 'Ago';
            break;
            case 9:
            ($format=='lg')?$month_letter = 'Septiembre':$month_letter = 'Sep';
            break;
            case 10:
            ($format=='lg')?$month_letter = 'Octubre':$month_letter = 'Oct';
            break;
            case 11:
            ($format=='lg')?$month_letter = 'Noviembre':$month_letter = 'Nov';
            break;
            case 12:
            ($format=='lg')?$month_letter = 'Diciembre':$month_letter = 'Dic';
            break;
        }
        return $month_letter;
    }

    function day_letter($day, $format)
    {        
        $day_letter = '';
        switch ($day) 
        {
            case 0:
            ($format=='lg')?$day_letter = 'Domingo':$day_letter = 'Do';
            break;
            case 1:
            ($format=='lg')?$day_letter = 'Lunes':$day_letter = 'Lu';
            break;
            case 2:
            ($format=='lg')?$day_letter = 'Martes':$day_letter = 'Ma';
            break;
            case 3:
            ($format=='lg')?$day_letter = 'Miércoles':$day_letter = 'Mi';
            break;
            case 4:
            ($format=='lg')?$day_letter = 'Jueves':$day_letter = 'Ju';
            break;
            case 5:
            ($format=='lg')?$day_letter = 'Viernes':$day_letter = 'Vi';
            break;
            case 6:
            ($format=='lg')?$day_letter = 'Sábado':$day_letter = 'Sa';
            break;
        }
        return $day_letter;
    }

/*! 
  @function num2letras () 
  @abstract Dado un n?mero lo devuelve escrito. 
  @param $num number - N?mero a convertir. 
  @param $fem bool - Forma femenina (true) o no (false). 
  @param $dec bool - Con decimales (true) o no (false). 
  @result string - Devuelve el n?mero escrito en letra. 

*/ 
function num2letras($num, $fem = false, $dec = true) { 
   $matuni[2]  = "dos"; 
   $matuni[3]  = "tres"; 
   $matuni[4]  = "cuatro"; 
   $matuni[5]  = "cinco"; 
   $matuni[6]  = "seis"; 
   $matuni[7]  = "siete"; 
   $matuni[8]  = "ocho"; 
   $matuni[9]  = "nueve"; 
   $matuni[10] = "diez"; 
   $matuni[11] = "once"; 
   $matuni[12] = "doce"; 
   $matuni[13] = "trece"; 
   $matuni[14] = "catorce"; 
   $matuni[15] = "quince"; 
   $matuni[16] = "dieciseis"; 
   $matuni[17] = "diecisiete"; 
   $matuni[18] = "dieciocho"; 
   $matuni[19] = "diecinueve"; 
   $matuni[20] = "veinte"; 
   $matunisub[2] = "dos"; 
   $matunisub[3] = "tres"; 
   $matunisub[4] = "cuatro"; 
   $matunisub[5] = "quin"; 
   $matunisub[6] = "seis"; 
   $matunisub[7] = "sete"; 
   $matunisub[8] = "ocho"; 
   $matunisub[9] = "nove"; 

   $matdec[2] = "veint"; 
   $matdec[3] = "treinta"; 
   $matdec[4] = "cuarenta"; 
   $matdec[5] = "cincuenta"; 
   $matdec[6] = "sesenta"; 
   $matdec[7] = "setenta"; 
   $matdec[8] = "ochenta"; 
   $matdec[9] = "noventa"; 
   $matsub[3]  = 'mill'; 
   $matsub[5]  = 'bill'; 
   $matsub[7]  = 'mill'; 
   $matsub[9]  = 'trill'; 
   $matsub[11] = 'mill'; 
   $matsub[13] = 'bill'; 
   $matsub[15] = 'mill'; 
   $matmil[4]  = 'millones'; 
   $matmil[6]  = 'billones'; 
   $matmil[7]  = 'de billones'; 
   $matmil[8]  = 'millones de billones'; 
   $matmil[10] = 'trillones'; 
   $matmil[11] = 'de trillones'; 
   $matmil[12] = 'millones de trillones'; 
   $matmil[13] = 'de trillones'; 
   $matmil[14] = 'billones de trillones'; 
   $matmil[15] = 'de billones de trillones'; 
   $matmil[16] = 'millones de billones de trillones'; 
   
   //Zi hack
   $float=explode('.',$num);
   $num=$float[0];

   $num = trim((string)@$num); 
   if ($num[0] == '-') { 
      $neg = 'menos '; 
      $num = substr($num, 1); 
   }else 
      $neg = ''; 
   while ($num[0] == '0') $num = substr($num, 1); 
   if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num; 
   $zeros = true; 
   $punt = false; 
   $ent = ''; 
   $fra = ''; 
   for ($c = 0; $c < strlen($num); $c++) { 
      $n = $num[$c]; 
      if (! (strpos(".,'''", $n) === false)) { 
         if ($punt) break; 
         else{ 
            $punt = true; 
            continue; 
         } 

      }elseif (! (strpos('0123456789', $n) === false)) { 
         if ($punt) { 
            if ($n != '0') $zeros = false; 
            $fra .= $n; 
         }else 

            $ent .= $n; 
      }else 

         break; 

   } 
   $ent = '     ' . $ent; 
   if ($dec and $fra and ! $zeros) { 
      $fin = ' coma'; 
      for ($n = 0; $n < strlen($fra); $n++) { 
         if (($s = $fra[$n]) == '0') 
            $fin .= ' cero'; 
         elseif ($s == '1') 
            $fin .= $fem ? ' una' : ' un'; 
         else 
            $fin .= ' ' . $matuni[$s]; 
      } 
   }else 
      $fin = ''; 
   if ((int)$ent === 0) return 'Cero ' . $fin; 
   $tex = ''; 
   $sub = 0; 
   $mils = 0; 
   $neutro = false; 
   while ( ($num = substr($ent, -3)) != '   ') { 
      $ent = substr($ent, 0, -3); 
      if (++$sub < 3 and $fem) { 
         $matuni[1] = 'una'; 
         $subcent = 'as'; 
      }else{ 
         $matuni[1] = $neutro ? 'un' : 'uno'; 
         $subcent = 'os'; 
      } 
      $t = ''; 
      $n2 = substr($num, 1); 
      if ($n2 == '00') { 
      }elseif ($n2 < 21) 
         $t = ' ' . $matuni[(int)$n2]; 
      elseif ($n2 < 30) { 
         $n3 = $num[2]; 
         if ($n3 != 0) $t = 'i' . $matuni[$n3]; 
         $n2 = $num[1]; 
         $t = ' ' . $matdec[$n2] . $t; 
      }else{ 
         $n3 = $num[2]; 
         if ($n3 != 0) $t = ' y ' . $matuni[$n3]; 
         $n2 = $num[1]; 
         $t = ' ' . $matdec[$n2] . $t; 
      } 
      $n = $num[0]; 
      if ($n == 1) { 
         $t = ' ciento' . $t; 
      }elseif ($n == 5){ 
         $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t; 
      }elseif ($n != 0){ 
         $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t; 
      } 
      if ($sub == 1) { 
      }elseif (! isset($matsub[$sub])) { 
         if ($num == 1) { 
            $t = ' mil'; 
         }elseif ($num > 1){ 
            $t .= ' mil'; 
         } 
      }elseif ($num == 1) { 
         $t .= ' ' . $matsub[$sub] . '?n'; 
      }elseif ($num > 1){ 
         $t .= ' ' . $matsub[$sub] . 'ones'; 
      }   
      if ($num == '000') $mils ++; 
      elseif ($mils != 0) { 
         if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub]; 
         $mils = 0; 
      } 
      $neutro = true; 
      $tex = $t . $tex; 
   } 
   $tex = $neg . substr($tex, 1) . $fin; 
   //Zi hack --> return ucfirst($tex);
   $end_num=ucfirst($tex).' con '.$float[1].'/100';
   return $end_num; 
} 


function generateRandomString($length) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}