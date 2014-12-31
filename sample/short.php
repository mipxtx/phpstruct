<?php
/**
 * @author: mix
 * @date: 30.12.14
 */

$abc = [];
$b = 7;

foreach($abc as $a => $b):
    echo "$a=>$b";
endforeach;

for($i=0;$i<5;$i++):
    echo $i;
endfor;

if($a > $b):
    echo $a;
endif;

while($a):
    echo $a;
endwhile;

switch($a):
    case 1:
        break;
endswitch;


declare(ticks = 1):
    echo "\n";
enddeclare;


?>sdfsdffsdsf<?=$a?>

