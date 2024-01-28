<?php

$subject = file_get_contents('https://restoran.kz/restaurant');
$pattern = '/<a class="link-inherit-color" href="(.+?)">(.+?)<\/a>/u';
$result = [];

preg_match_all($pattern,$subject,$result);

print_r($result);

$rests = [];

foreach ($result[2] as $i => $name) {

    $rests[] = [
        'name'=> $name,
        'link'=> $result[1][$i],
    ];

}


print_r($rests);

$pattern = '/<li class="d-flex mr-5 mb-3"><svg class="icon icon_md flex-none mr-3" aria-hidden="true"><use xlink:href="(.+?)"><\/use><\/svg>(.+?)<\/li>/u';
$result = [];

preg_match_all($pattern,$subject,$result);

print_r($result[2]);

foreach ($rests as $i => $rest) {

    $rests[$i]['cuisine'] = $result[2][$i*3];

    $rests[$i]['price'] = $result[2][$i*3+1];

    $rests[$i]['options'] = $result[2][$i*3+2];
}



print_r($rests);