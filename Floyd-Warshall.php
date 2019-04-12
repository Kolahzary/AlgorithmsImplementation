<?php session_start();
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'Regenerate') {
    session_unset();
}
    define('infinity', 99);
//    error_reporting( E_ALL );
//    ini_set('display_errors', 1);
?><!DOCTYPE html>
<html>
<head>
    <title>Floyd-Warshall Algorithm</title>
    <style type="text/css">
        body{
            background: #a6ffa1;
        }
        table{
            empty-cells: hide;
        }
        th{
            background: lawngreen;
        }
        td{
            padding:5px;
        }
    </style>
</head>
<body>
<form method="post"><input type="submit" name="action" value="Regenerate"/></form>
<form method="get">
    From: <input type="text" name="from" value="<?php echo @$_REQUEST['from']; ?>"/>
    To: <input type="text" name="to" value="<?php echo @$_REQUEST['to']; ?>"/>
    <input type="submit"/>
</form>
<?php
if (!isset($_SESSION['map'])) {
    $map = new Map();
    $map->setCount(10);
    // $map->setCitiesFilePath("Data-IranianCities.txt");
    $map->loadRandomCities();
    $map->generateCosts();
    $_SESSION['map'] = clone $map;
} else {
    $map = clone $_SESSION['map'];
}
$htmBefore = $map->getHtml();
$map->floyd_warshall();
$htmAfter = $map->getHtml();

if (isset($_REQUEST['from']) && isset($_REQUEST['to'])) {
    echo '<h3>Result: </h3>'.'Shortest path: '.implode(' ==> ', $map->getShortestPathNodes($_REQUEST['from'], $_REQUEST['to']))
        .'<br/>It costs '.$map->getShortestPathCost($_REQUEST['from'], $_REQUEST['to']);
}

echo '<h2>Before:</h2>'.$htmBefore;
echo '<h2>After:</h2>'.$htmAfter;
?>
<script>
    var tds=document.getElementsByTagName('td');
    for(var i=0;i<tds.length;i++)
    {
        var value=tds[i].innerHTML.split(':')[0];
        if(!isFinite(value)){
            tds[i].style["background-color"]='red';
        }else{
            if(value=='0'){
                tds[i].style["background-color"]='black';
            }else{
                tds[i].style["background-color"]='rgba(0,0,255,'+value/20+')';
            }
        }
    }
</script>
</body>
</html>
<?php
class Map
{
    private $citiesFilePath;

    public function getCitiesFilePath()
    {
        return $this->citiesFilePath;
    }

    public function setCitiesFilePath($citiesFilePath)
    {
        $this->citiesFilePath = $citiesFilePath;
    }

    private $costs;

    public function getCosts()
    {
        return $this->costs;
    }

    public function setCosts($costs)
    {
        $this->costs = $costs;
    }

    public function getCostByIds($from, $to)
    {
        return $this->costs[$from][$to];
    }

    public function generateCosts()
    {
        for ($i = 0; $i < $this->count; $i++) {
            $this->costs[$i] = [];
            for ($j = 0; $j < $this->count; $j++) {
                $this->costs[$i][$j] = infinity;
            }
            $this->costs[$i][$i] = 0;

            $rand = [];
            for ($j = 0; $j < $this->count; $j++) {
                if ($i != $j) {
                    $rand[] = $j;
                }
            }
            shuffle($rand);

            for ($j = 0; $j < 5; $j++) {
                $this->costs[$i][$rand[$j]] = rand(1, 20);
            }
        }
    }

    private $cities;

    public function getCities()
    {
        return $this->cities;
    }

    public function setCities($cities)
    {
        $this->cities = $cities;
    }

    public function getCityById($id)
    {
        return $this->cities[$id];
    }

    public function loadRandomCities()
    {
        $file = file($this->citiesFilePath, FILE_IGNORE_NEW_LINES);
        shuffle($file);
        $this->cities = array_slice($file, 0, $this->count);
    }

    private $count;

    public function getCount()
    {
        return $this->count;
    }

    public function setCount($count)
    {
        $this->count = $count;
    }

    private $paths;

    public function getPaths()
    {
        return $this->paths;
    }

    public function getPathByIds($from, $to)
    {
        return $this->paths[$from][$to];
    }

    public function __construct()
    {
        $this->paths = [];
        $this->cities = [];
        $this->costs = [];
        $this->count = 10;
        $this->citiesFilePath = 'Data-States.txt';
    }

    public function getShortestPathCost($from, $to)
    {
        $_from = array_search($from, $this->cities);
        $_to = array_search($to, $this->cities);
        if (empty($this->paths)) {
            $this->floyd_warshall();
        }

        return $this->getCostByIds($_from, $_to);
    }

    public function getShortestPathNodes($from, $to)
    {
        $_from = array_search($from, $this->cities);
        $_to = array_search($to, $this->cities);
        if (empty($this->paths)) {
            $this->floyd_warshall();
        }

        return $this->getPathByIds($_from, $_to);
    }

    public function getHtml()
    {
        $result = "<table border='1'>";
        $result .= '<tr><th></th>';
        for ($j = 0; $j < $this->count; $j++) {
            $result .= '<th>'.$this->cities[$j].'</th>';
        }
        $result .= '</tr>';
        for ($i = 0; $i < $this->count; $i++) {
            $result .= '<tr><th>'.$this->cities[$i].'</th>';

            for ($j = 0; $j < $this->count; $j++) {
                $result .= '<td>';
                if ($this->costs[$i][$j] == infinity) {
                    $result .= '&infin;';
                } else {
                    $result .= $this->costs[$i][$j];
                    if (!empty($this->paths)) {
                        $result .= ': '.json_encode($this->paths[$i][$j]);
                    }
                }
                $result .= '</td>';
            }
            $result .= '</tr>';
        }
        $result .= '</table>';

        return $result;
    }

    public function floyd_warshall()
    {
        if (!empty($this->paths)) {
            return;
        }
        //add beginning cities
        for ($i = 0; $i < $this->count; $i++) {
            $this->paths[$i] = [];
            for ($j = 0; $j < $this->count; $j++) {
                $this->paths[$i][$j] = [$this->getCityById($i)];
            }
        }

        // find shortest paths and add middle cities
        for ($i = 0; $i < $this->count; $i++) {
            for ($j = 0; $j < $this->count; $j++) {
                for ($k = 0; $k < $this->count; $k++) {
                    if ($this->costs[$i][$j] > $this->costs[$i][$k] + $this->costs[$k][$j]) {
                        $this->costs[$i][$j] = $this->costs[$i][$k] + $this->costs[$k][$j];
                        $middle = $this->getCityById($k);
                    }
                }
                if (isset($middle)) {
                    array_push($this->paths[$i][$j], $middle);
                    unset($middle);
                }
            }
        }

        // add destination cities
        for ($i = 0; $i < $this->count; $i++) {
            for ($j = 0;$j < $this->count;$j++) {
                array_push($this->paths[$i][$j], $this->cities[$j]);
            }
        }
    }
}
?>
