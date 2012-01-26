<?php

class ThisToken extends CustomGenericToken
{

    public function getContent()
    {
        return "$" . $this->content;
    }
}
