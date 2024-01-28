
<?php

include './db.php';

$pdo = getPDO();

$stmt = $pdo->prepare("
        SELECT
              `rests`.*,
              `categories`.`label`  AS `category`,
              `rests_cuisines`.`id_rest`,
              `rests_cuisines`.`id_cuisine`,
              `cuisines`.`name` AS `cuisine_name`  
        FROM 
              `cg_rests`.`rests` 

        LEFT JOIN
              `categories`
               ON `rests`.`category` = `categories`.`id`

        LEFT JOIN
              `rests_cuisines`
               ON `rests`.`id` = `rests_cuisines`.`id_rest`
        LEFT JOIN
               `cuisines`
                ON `rests_cuisines`.`id_cuisine` = `cuisines`.`id`
  
  ");


$stmt->execute([]);


$rests = $stmt->fetchAll();


print_r($rests);
