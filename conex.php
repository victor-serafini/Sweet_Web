<?php
$link = mysqli_connect('localhost', 'c2012080_sweet', '09vigadaFO');
if (!$link) {
die('Could not connect: ' . mysqli_error());
}
echo 'Connected successfully';
mysqli_close($link);
?>