<?php

function flash_message()
{
    // get flash message from CI instance
    $ci =& get_instance();
    $flashmsg = $ci->session->flashdata('message');

    $html = '';
    if (is_array($flashmsg))
    {
            $html = '<div id="flashmessage" class="'.$flashmsg[type].'">                                        
                    <p>'.$flashmsg['content'].'</p>
                    </div>';
    }
    return $html;
}
?>
