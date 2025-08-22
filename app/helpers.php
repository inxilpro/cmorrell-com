<?php

function md_path(string $filename)
{
    $filename = str($filename)
        ->ltrim('/')
        ->prepend('views/markdown/');

    return resource_path((string) $filename);
}
