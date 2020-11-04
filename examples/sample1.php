<?php

require 'vendor/autoload.php';

ptk\db\connect('sqlite:examples/sample.sqlite');

//ptk\db\transaction("INSERT INTO test(name) VALUES('John')");
//ptk\db\transaction("INSERT INTO test(name) VALUES(:name)", [':name' => 'Mary']);
//\ptk\db\commit();

//print_r(\ptk\db\query("SELECT * FROM test"));
print_r(\ptk\db\query("SELECT * FROM test WHERE name LIKE :name", [':name' => 'Mary']));