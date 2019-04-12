<?php

$before = [];
for ($i = 0; $i < rand(1, 50); $i++) {
    $before[] = rand(-100, 100);
}

echo '<h3>Before:</h3>';
echo json_encode($before);

echo '<hr/><h3>Steps:</h3>';
$after = mergeSort($before);

echo '<hr/><h3>After:</h3>';
echo json_encode($after);

function mergeSort($array)
{
    if (count($array) < 2) {
        return $array;
    }

    $mid = count($array) / 2;
    echo '<h5>Splitting '.json_encode($array).' ===> Left: '.json_encode(array_slice($array, 0, $mid)).' Right: '.json_encode(array_slice($array, $mid));

    $right = mergeSort(array_slice($array, 0, $mid));
    $left = mergeSort(array_slice($array, $mid));

    return merge($left, $right);
}
function merge($left, $right)
{
    echo '<h5>Merging '.json_encode($left).' and '.json_encode($right).' ===> Result: ';
    $result = [];
    while (count($left) > 0 || count($right) > 0) {
        if (count($left) > 0 && count($right) > 0) {
            if ($left[0] < $right[0]) {
                array_push($result, array_shift($left));
            } else {
                array_push($result, array_shift($right));
            }
        } elseif (count($left) > 0) {
            array_push($result, array_shift($left));
        } else {
            array_push($result, array_shift($right));
        }
    }
    echo json_encode($result).'</h5>';

    return $result;
}
