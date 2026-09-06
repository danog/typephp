--TEST--
any
--FILE--
<?php
function main()
{
    $a = std::any(10);
    $b = std::any(4);
    echo var_dump($a/$b);
}
?>
--EXPECT--
float(2.5)
