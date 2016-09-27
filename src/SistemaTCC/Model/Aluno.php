<?php

namespace SistemaTCC\Model;

/**
 * Aluno
 */
class Aluno
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $matricula;

    /**
     * @var string
     */
    private $cgu;

    /**
     * @var \SistemaTCC\Model\Pessoa
     */
    private $pessoa;


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
     * Set matricula
     *
     * @param string $matricula
     *
     * @return Aluno
     */
    public function setMatricula($matricula)
    {
        $this->matricula = $matricula;

        return $this;
    }

    /**
     * Get matricula
     *
     * @return string
     */
    public function getMatricula()
    {
        return $this->matricula;
    }

    /**
     * Set cgu
     *
     * @param string $cgu
     *
     * @return Aluno
     */
    public function setCgu($cgu)
    {
        $this->cgu = $cgu;

        return $this;
    }

    /**
     * Get cgu
     *
     * @return string
     */
    public function getCgu()
    {
        return $this->cgu;
    }

    /**
     * Set pessoa
     *
     * @param \SistemaTCC\Model\Pessoa $pessoa
     *
     * @return Aluno
     */
    public function setPessoa(\SistemaTCC\Model\Pessoa $pessoa = null)
    {
        $this->pessoa = $pessoa;

        return $this;
    }

    /**
     * Get pessoa
     *
     * @return \SistemaTCC\Model\Pessoa
     */
    public function getPessoa()
    {
        return $this->pessoa;
    }
}

