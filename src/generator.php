<?php
$cwd = getcwd();
while($path = fgets(STDIN)){
    var_dump(file_get_contents(trim($path)));
}