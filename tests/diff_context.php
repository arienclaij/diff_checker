<?php
$path = "C:/xampp/htdocs/DiffChecker/bin/";
$command = "diff.exe --context a.txt b.txt";
$patch = '';
if ( $handle = popen($path.$command, "r" ) )
        {
            while ( ( $read = fread( $handle, 4096 ) ) != false )
            {
                $patch .= $read;
            }
            pclose( $handle );
        }            
echo $patch;
?>