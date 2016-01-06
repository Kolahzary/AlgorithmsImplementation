<?PHP
$arrTest=array(5, 4, 3, 2, 1, 0);
var_dump($arrTest);
var_dump(mergeSort($arrTest));

function mergeSort($array)
{
	if(count($array)<2) return $array;
	
	$mid=count($array)/2;
	
	$right=mergeSort(array_slice($array,0,$mid));
	$left=mergeSort(array_slice($array,$mid));
	
	return merge($left,$right);
}
function merge($left,$right)
{
	$result=array();
	while( count($left)>0 && count($right)>0 )
	{
		if($left[0] > $right[0])
		{
			array_push($result,array_shift($left));
		}
		else
		{
			array_push($result,array_shift($right));
		}
	}
	return $result;
}
?>
