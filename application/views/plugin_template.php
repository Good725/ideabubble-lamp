<?php

// Plugin Assets
if(isset($styles) AND is_array($styles))
{
    foreach($styles as $file => $type)
    {
        echo '<link rel="stylesheet" type="text/css" href="'. $file .'" media="'. $type .'">';
    }

}

// Plugin Scripts
if (isset($scripts) AND is_array($scripts))
{
    echo implode(PHP_EOL, $scripts);
}

// Plugin Body
if (isset($body))
{
    echo $body;
}
