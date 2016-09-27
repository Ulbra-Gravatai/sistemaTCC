<?php

namespace SistemaTCC\Model;

/**
 * Campus
 */
class Campus
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $nome;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nome
     *
     * @param string $nome
     *
     * @return Campus
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome
     *
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }
}

