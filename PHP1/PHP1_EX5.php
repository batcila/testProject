<?php jQuery uses the browser's .innerHTML property to parse the retrieved document and insert it into the current document. During this process, browsers often filter elements from the document such as <html>, <title>, or <head> elements. As a result, the elements retrieved by .load() may not be exactly the same as if the document were retrieved directly by the browser.



$records = array(
    array(
        'id' => 2135,
        'first_name' => 'John',
        'last_name' => 'Doe',
        ),
    array(
        'id' => 3245,
        'first_name' => 'Sally',
        'last_name' => 'Smith',
        ),
    array(
        'id' => 5342,
        'first_name' => 'Jane',
        'last_name' => 'Jones',
        ),
    array(
        'id' => 5623,
        'first_name' => 'Peter',
        'last_name' => 'Doe',
        )
  );

$dub_array = array(
    0 => 'Yellow', 
    1 => 'Green', 
    2 => 'Yellow', 
    3 => 'Blue', 
    4 => 'Yellow'
    );

function printr($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

function my_array_column ($array, $key_in_array, $key_index = null) {
	$func_exit = [];
	foreach ($array as $keys => $array_part) {
		foreach ($array_part as $key => $value) {
			if (is_null($key_index)) {
				if ($key == $key_in_array) {
					array_push($func_exit,$value);
				}
			} else {
				if ($key == $key_in_array) {
					$func_exit[$array_part[$key_index]] = $value;
				}
			}
		}
	}
	printr($func_exit);
}

function my_dirname ($input_dir, $key_index = 1) {
    $output_string = '';
    switch (true) {
        case (strpos($input_dir, '/') !== false):
            $dir_separator = '/';
            break;
        case (strpos($input_dir, '|') !== false):
            $dir_separator = '|';
            break;
        case (strpos($input_dir, '\\') !== false):
            $dir_separator = '\\';
            break;
        default:
            print_r('something');
        break;
    }
    $temp_array = explode($dir_separator, $input_dir);
    $number_of_dirs = count($temp_array);
    for ($i = $key_index ; $i > 0; $i--) {
        array_pop($temp_array);
    }
    $output_string = implode($dir_separator, $temp_array);
    return $output_string;
}

function my_date_format() {
	//to do @home
}

function my_array_unique($input) {
	return array_flip(array_flip($input));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Exercise 5</title>
</head>
<body>
	<p> <?php my_array_column($records,'something','id') ?> </p>
	<p> <?php print_r(my_array_unique($dub_array)); ?> </p>
    <p> <?php echo(my_dirname('/usr/local/lib/')) ?> </p>
    <p> <?php echo(dirname('/usr/local/lib/')) ?> </p>
    
</body>
</html>