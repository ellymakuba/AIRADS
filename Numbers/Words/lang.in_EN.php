<?php


require_once("Numbers/Words.php");

class Numbers_Words_in_EN extends Numbers_Words
{
     function toWords($num)
      {
        $number=$num;
       return $this->ChangeToWords($number);
      }

     function changeToWords($num)
     {
         $val="";
         $wholeno=$num;
         $points="";
         $andStr = "";
         $pointStr="";
         $endstr="only";

         $decimalplace=strpos($num,"." );

         if ($decimalplace > 0)
             {
              $wholeno = substr($num,0,$decimalplace);
              $points=substr($num,$decimalplace+1);

                 if($points>0)
                  {
                     $andStr="and";
                     $endstr ="paise"." "."only";
                     $pointStr =$this->translateWholeNumber($points);
                  }
             }

          $val=$this->translateWholeNumber($wholeno)." ".$andStr." ".$pointStr." ".$endstr;

            return $val;
     }

     function translateWholeNumber($number)
     {
         $word="";

         $beginsZero=FALSE;
         $isDone=FALSE;
         $dblAmt=$number;

         if($dblAmt>0)
          {
            $beginsZero=$this->startsWith($number,'0');
            $numDigits=strlen($number);
            $pos=0;
            $place="";

            switch ($numDigits)
            {
              case 1:
                  $word=$this->ones($number);
                  $isDone=TRUE;
                  break;

              case 2:
                  $word=$this->tens($number);
                  $isDone=TRUE;
                  break;

              case 3:
                  $pos=($numDigits%3)+1;
                  $place="Hundred";
                  break;

              case 4:
              case 5:

                  $pos=($numDigits%4)+1;
                  $place="thousand";
                  break;


              case 6:
              case 7:
              case 8:
              case 9:
              case 10:
                 $pos=($numDigits%6)+1;
                   $place="Lac";
                   break;


              default:
                   $isDone=TRUE;
                   break;
            }

            if(!$isDone)
            {
                $word=$this->translateWholeNumber(substr($number,0,$pos))." ".$place." ".$this->translateWholeNumber(substr($number,$pos));

                if($beginsZero)
                 {
                  $word="and"." ".$word;
                 }
            }

               if($word==$place)
               {
                  $word="";
               }
          }
          return $word;
     }

     function startsWith($haystack,$needle,$case=true)
            {
                if($case)
                 {
                    return (strcmp(substr($haystack, 0, strlen($needle)),$needle)===0);
                  }
                  return (strcasecmp(substr($haystack, 0, strlen($needle)),$needle)===0);
              }

     function  tens($digit)
     {
          $digt=$digit;
         $name=NULL;

         switch ($digt)
         {
             case 10:
                 $name="Ten";
                 break;

             case 11:
                 $name="Eleven";
                 break;

             case 12:
                 $name="Twelve";
                 break;

             case 13:
                 $name="Thirteen";
                 break;

             case 14:
                 $name="Fourteen";
                 break;

             case 15:
                 $name="Fifteen";
                 break;

             case 16:
                 $name="Sixteen";
                 break;

             case 17:
                 $name="Seventeen";
                 break;

             case 18:
                 $name="Eighteen";
                 break;

             case 19:
                 $name="Nineteen";
                 break;

             case 20:
                 $name="Twenty";
                 break;

             case 30:
                 $name="Thirty";
                 break;

             case 40:
                 $name="Fourty";
                 break;

             case 50:
                 $name="Fifty";
                 break;

             case 60:
                 $name="Sixty";
                 break;

             case 70:
                 $name="Seventy";
                 break;

             case 80:
                 $name="Eighty";
                 break;

             case 90:
                 $name="Ninety";
                 break;

              default:
                 if($digt>0)
                  {
                    $name=$this->tens(substr($digit,0,1)."0")." ".$this->ones(substr($digit,1));
                  }
                  break;
         }
         return $name;
     }

     function ones($digit)
     {
         $digt=$digit;
         $name="";

         switch ($digt)
         {
             case 1:
                 $name="One";
                 break;

             case 2:
                 $name="Two";
                 break;

             case 3:
                 $name="Three";
                 break;

             case 4:
                 $name="Four";
                 break;

             case 5:
                 $name="Five";
                 break;

             case 6:
                 $name="Six";
                 break;

             case 7:
                 $name="Seven";
                 break;

             case 8:
                 $name="Eight";
                 break;

             case 9:
                 $name="Nine";
                 break;

             default:
                 if($digt>0)
                  {
                    $name=(substr($digit,0,1)."0")." ".$this->ones(substr($digit,1));
                  }
         }
         return  $name;
     }


}



?>