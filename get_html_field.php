<?php
require 'autoload.php';

function mapped_implode($glue, $array, $symbol = '=') {
    return implode($glue, array_map(
            function($k, $v) use($symbol) {
                return $k . $symbol . $v . '<br>';
            },
            array_keys($array),
            array_values($array)
            )
        );
}

function sendRuqest($url,$data){

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,"sku=$data");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $server_output = curl_exec($ch);
  curl_close ($ch);
  $list = array();
  foreach(json_decode($server_output,true)['storelist'] ?? [] as $storelist){
    foreach($storelist['stores'] as $storeinfo){
      if(!empty($storeinfo['storetitle'])){
        $list[$storeinfo['storetitle']] = $storeinfo['store_inventory_raw'];
      }
    }

  }
  return mapped_implode(', ', $list, ' is ');
}

$prod = array();
$dom = new IvoPetkov\HTML5DOMDocument();
$dom->loadHTML(file_get_contents("https://x"),$dom::ALLOW_DUPLICATE_IDS);

$p = $dom->querySelector('.prodDetailCt');
  $title = $p->querySelector('.title')->innerHTML;
  $stokcode = trim(explode(':',$p->querySelector('.detailRow')->querySelector('.sku')->innerHTML)[1]);

  $prod['title'] = $title;
  $prod['stock'] = $stokcode;

$price_fields = $p->querySelector('.priceBox');
  $old_price =  $price_fields->querySelector('del')->innerHTML;
  $price =  $price_fields->querySelector('span')->innerHTML;

  $prod['oldPrice'] = $old_price;
  $prod['price'] = $price;

$storeList = sendRuqest('https://www.x.com.tr/findinstore',$stokcode);
$prod['stockinfo'] = $storeList;
?>
<table>

  <tr>
         <th>title</th>
         <th>stock</th>
         <th>oldPrice</th>
         <th>price</th>
         <th>stock state</th>

     </tr>

  <tr>
    <?php foreach($prod as $title=>$val){?>
      <td><?=$val;?></td>
    <?php } ?>
  </tr>

</table>
