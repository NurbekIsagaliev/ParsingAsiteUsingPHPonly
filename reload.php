<?php

include './functions.php';

include './db.php';

$pdo = getPDO();

$rests = [];

$stmt = $pdo->prepare("SELECT * FROM `categories`");

$stmt -> execute();

$types = $stmt -> fetchAll();

foreach ($types as $type) {

    $maxPage = getMaxPage($type['type'],1);

    for ($i=1; $i <=$maxPage ; $i++) { 

        $rests = array_merge($rests,getRestsFromPage($type['type'],$i)) ;
}

}

foreach ($rests as &$rest) {
    foreach ($types as $type) {
        if( $type['type'] == $rest['category'] ){
            $id = $type['id'];
        }
    }
    $rest['category'] = $id;
}


$cuisines = [];

foreach ($rests as $rest) {
    $cuisines =  array_merge($cuisines,$rest['cuisine'] ?? []);
}

$cuisines = array_unique($cuisines);

print_r($cuisines);

$stmt = $pdo->prepare("TRUNCATE TABLE `cg_rests`.`cuisines`");
$stmt->execute();

$stmt = $pdo -> prepare("
        INSERT INTO
        `cuisines` (
            `name`
        ) VALUES (
            :name
            )
");

$cuisinesMap = [];
foreach ($cuisines as $cuisine) {
    $stmt -> execute([
        ':name' => $cuisine,
    ]);
    $cuisinesMap[$cuisine] = $pdo->lastInsertId();
}

print_r($cuisinesMap);

$stmt = $pdo->prepare("TRUNCATE TABLE `cg_rests`.`rests`");
$stmt->execute();

$stmt = $pdo->prepare("TRUNCATE TABLE `cg_rests`.`rests_cuisines`");
$stmt->execute();

$stmt = $pdo->prepare("
      INSERT INTO
      `rests` (
        `category`,
        `name`,
        `link`,
        
        `price_min`,
        `price_max`,
        `options`
        ) VALUES (
            :category,
            :name,
            :link,
            
            :price_min,
            :price_max,
            :options
            )


");

print_r($rests);



$stmtRC = $pdo->prepare("
          INSERT INTO
          `cg_rests`.`rests_cuisines` (
            `id_rest`,
            `id_cuisine`
          ) VALUES (
            :id_rest,
            :id_cuisine
            )
");

foreach ($rests as $rest) {
    $stmt->execute([
        ':category' => $rest['category'],
        ':name' => $rest['name'],
        ':link' => $rest['link'],
       
        ':price_min' =>$rest['price']['min'],
        ':price_max' =>$rest['price']['max'],
        ':options'=>$rest['options'] ?? '',
    ]);

    $restId = $pdo->lastInsertId();
   
    foreach ($rest['cuisine'] as $cuisine) {
        $stmtRC -> execute([
          ':id_rest' => $restId,
          ':id_cuisine' => $cuisinesMap[$cuisine],
        ]);
    }
}



