<?php
$before=array();
for($i=0;$i<rand(1,50);$i++)
{
    $before[]=rand(-100,100);
}

echo "<h3>Before:</h3>";
echo json_encode($before);

echo "<hr/><h3>Steps:</h3>";
$after=mergeSort($before);

echo "<hr/><h3>After:</h3>";
echo json_encode($after);

function mergeSort($array)
{
    if(count($array)<2) return $array;
    if(count($array)<3){
        echo "<h5>Splitting ".json_encode($array)." ===>"." Part #1: ".json_encode(array($array[0]))." Part #2: ".json_encode(array($array[1]));
        return merge(array($array[0]),array($array[1]));
    }
    $part=(int)(count($array)/3);
    echo "<h5>Splitting ".json_encode($array)." ===>"." Part #1: ".json_encode(array_slice($array,0,$part))." Part #2: ".json_encode(array_slice($array,$part,$part))." Part #3: ".json_encode(array_slice($array,$part*2));
    return merge(mergeSort(array_slice($array,0,$part)),mergeSort(array_slice($array,$part,$part)),mergeSort(array_slice($array,$part*2)));
}
function merge($part1,$part2,$part3=null)
{
    if($part3==null)
    {
        echo "<h5>Merging ".json_encode($part1)." and ".json_encode($part2)." ===> Result: ";
        $result=array();
        while( count($part1)>0 || count($part2)>0 )
            if(count($part1)>0 && count($part2)>0)
                if($part1[0] < $part2[0]) array_push($result,array_shift($part1));
                else array_push($result,array_shift($part2));
            else if(count($part1)>0) array_push($result,array_shift($part1));
            else array_push($result,array_shift($part2));
        echo json_encode($result)."</h5>";
        return $result;
    }else return merge( merge($part1,$part2),$part3);
}
