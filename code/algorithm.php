<?php

function fibonacciIterative($n) {
    if ($n <= 1) {
        return $n;
    }

    $a = 0;
    $b = 1;

    for ($i = 2; $i <= $n; $i++) {
        $c = $a + $b;
        $a = $b;
        $b = $c;
    }

    return $b;
}
