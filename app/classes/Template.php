<?php


class Template
{
    private $name = '';
    public function __construct($name)
    {
        $this->name = $name;

        if(false === is_dir($this->getDir()))
            throw new Exception("Template '$name' not found.");


    }
    public function getDir()
    {return TEMPLATE_DIR.$this->name;}

    public function getAuthor()
    {}
    public function getName()
    {return $this->name;}
    public function renderPost($post)
    {}

    public function renderPostList($postList)
    {}

    public function renderNotFound()
    {}
} 