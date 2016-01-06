<!DOCTYPE HTML>
<html>
<head>
    <title>Knapsack problem, greedy solution</title>
    <style>
        body>div{
            display: inline-block;
            border: medium solid black;
            text-align:center;
            margin-right:20px;
            margin-bottom:20px;
        }
    </style>
    <script>
        function GenerateRandomValues(){
            var nodes = document.querySelectorAll("input[type=number]");
            for (var i=0; i<nodes.length; i++)
            {
                nodes[i].setAttribute('value',Math.floor(Math.random()*100));
            }
        }
    </script>
</head>
<body>
<form action="" method="post">
    <?PHP
        if(count($_REQUEST)==0)
        {
            echo '<p>How much items do we have in the store? <input type="number" name="count"/></p>';
        }
        if(isset($_REQUEST['count']))
        {
            echo '<p><input type="button" onclick="GenerateRandomValues()" value="Random fill"/></p>';
            echo '<p>Enter capacity of knapsack: <input type="number" name="capacity"/></p>';
            echo '<p>Allow franction? <input type="checkbox" name="fraction"/></p>';
            for($i=1;$i<=$_REQUEST['count'];$i++)
            {
                echo '<p>Item #'.$i.' ==> Weight: <input type="number" name="items['.$i.'][weight]"/> | Price: <input type="number" name="items['.$i.'][price]"/></p>';
            }
        }
    ?>
    <input type="submit"/>
</form>
<hr/>
<?php
if(!isset($_REQUEST['items'])) die();

$st=new store();
$pack=new knapsack($_REQUEST['capacity']);
$allowFraction=isset($_REQUEST['fraction']);

foreach($_REQUEST['items'] as $index=>$item)
{
    $st->addItem(new item($item['weight'],$item['price']));
}
print_knapsack($pack,'Before');
print_store($st,'Before');
echo '<br/>';

$st->sortItemsByValue();
print_store($st,'Sorted');
echo '<br/>';
foreach($st->getAllItems() as $index=>$item) {
    if ($pack->getFreeSpace() >= $item->getWeight())
    {
        $pack->addItem($st->pickItemByIndex($index));
    }else{
        if(!$allowFraction) continue;
        $pack->addItem($st->pickItemFractionByIndex($index,$pack->getFreeSpace()));
    }


    if($pack->getFreeSpace()==0)
    {
        break;
    }
}
print_knapsack($pack,'Final');
print_store($st,'Final');
echo '<br/>';

function print_store(store $store,$title='')
{
    echo '<div><h3>'.$title.' Store</h3><hr/><b>--Info--</b><pre>';
    echo 'count of items:'.$store->getItemsCount().PHP_EOL;
    echo 'total price:'.$store->getTotalPrice().PHP_EOL;
    echo 'total weight:'.$store->getTotalWeight().PHP_EOL;
    echo '</pre><b>--Items--</b><table border="1"><tbody><tr><th>Price</th><th>Weight</th><th>Value</th></tr>';
    foreach($store->getAllItems() as $index=>$item)
    {
        echo '<tr><td>'.$item->getPrice().'</td><td>'.$item->getWeight().'</td><td>'.$item->getValue().'</td></tr>';
    }
    echo '</tbody></table></div>';
}
function print_knapsack(knapsack $knapsack,$title='')
{
    echo '<div><h3>'.$title.' Knapsack</h3><hr/><b>--Info--</b><pre>';
    echo 'Capacity:'.$knapsack->getCapacity().PHP_EOL;
    echo 'used weight:'.$knapsack->getUsedWeight().PHP_EOL;
    echo 'free space:'.$knapsack->getFreeSpace().PHP_EOL;
    echo 'total price:'.$knapsack->getTotalPrice().PHP_EOL;
    echo '</pre><b>--Items--</b><table border="1"><tbody><tr><th>Price</th><th>Weight</th><th>Value</th></tr>';
    foreach($knapsack->getAllItems() as $index=>$item)
    {
        echo '<tr><td>'.$item->getPrice().'</td><td>'.$item->getWeight().'</td><td>'.$item->getValue().'</td></tr>';
    }
    echo '</tbody></table></div>';
}
class store{
    private $items;
        public function sortItemsByValue(){ usort($this->items,itemCompare); }
        public function getAllItems(){ return $this->items; }
        public function getItemsCount(){ return count($this->items); }
        public function getItemByIndex($index){ return $this->items[$index]; }
        public function addItem(item $item){ $this->items[] = $item;}
    public function __construct()
    {
        $this->items=array();
    }
    public function pickItemFractionByIndex($index,$HowMutch)
    {
        $price=$this->items[$index]->getValue()*$HowMutch;
        $item=new item($HowMutch,$price);
        $this->items[$index]->setWeight($this->items[$index]->getWeight()-$HowMutch);
        $this->items[$index]->setPrice($this->items[$index]->getPrice()-$price);
        return $item;
    }
    public function pickItemByIndex($index){
        $item=$this->items[$index];
        unset($this->items[$index]);
        return $item;
    }
    public function getTotalWeight(){
        $sum=0;
        foreach($this->items as $index=>$item)
        {
            $sum+=$item->getWeight();
        }
        return $sum;
    }
    public function getTotalPrice(){
        $sum=0;
        foreach($this->items as $index=>$item)
        {
            $sum+=$item->getPrice();
        }
        return $sum;
    }
}
class knapsack{
    private $capacity;
        public function getCapacity(){ return $this->capacity; }
        public function setCapacity($capacity){ $this->capacity = $capacity; }
    private $items;
        public function getAllItems(){ return $this->items; }
        public function addItem(item $item)
        {
            if ($item->getWeight() + $this->getUsedWeight() > $this->capacity) {
                echo "Error: Knapsack overloaded..." . PHP_EOL;
                return;
            }
            $this->items[] = $item;
        }
    public function __construct($capacity)
    {
        $this->capacity = $capacity;
        $this->items = array();
    }
    public function getTotalPrice(){
        $sum=0;
        foreach($this->items as $index=>$item)
        {
            $sum+=$item->getPrice();
        }
        return $sum;
    }
    public function getUsedWeight(){
        $sum=0;
        foreach($this->items as $index=>$item)
        {
            $sum+=$item->getWeight();
        }
        return $sum;
    }
    public function getFreeSpace(){
        return $this->getCapacity()-$this->getUsedWeight();
    }
}
class item{
    private $weight;
        public function getWeight(){ return $this->weight; }
        public function setWeight($weight){ $this->weight = $weight; }
    private $price;
        public function getPrice(){ return $this->price; }
        public function setPrice($price){ $this->price = $price; }
    public function __construct($weight,$price)
    {
        $this->weight=$weight;
        $this->price=$price;
    }
    public function getValue(){
        return $this->price / $this->weight;
    }
}
function itemCompare($first,$second)
{
    if($first->getValue() == $second->getValue()) return 0;
    return ($first->getValue() < $second->getValue())?1:-1;
}
?>
</body>
</html>
