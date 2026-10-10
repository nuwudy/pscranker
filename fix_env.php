<?php
$content = file_get_contents('.env');
$content = str_replace("\0", '', $content);
file_put_contents('.env', $content);
echo "Fixed";
