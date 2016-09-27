<?php

namespace SistemaTCC\Model;

/**
 * Tcc
 */
class Tcc
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $titulo;

    /**
     * @var \SistemaTCC\Model\Aluno
     */
    private $aluno;

    /**
     * @var \SistemaTCC\Model\Semestre
     */
    private $semestre;


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
     * Set titulo
     *
     * @param string $titulo
     *
     * @return Tcc
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set aluno
     *
     * @param \SistemaTCC\Model\Aluno $aluno
     *
     * @return Tcc
     */
    public function setAluno(\SistemaTCC\Model\Aluno $aluno = null)
    {
        $this->aluno = $aluno;

        return $this;
    }

    /**
     * Get aluno
     *
     * @return \SistemaTCC\Model\Aluno
     */
    public function getAluno()
    {
        return $this->aluno;
    }

    /**
     * Set semestre
     *
     * @param \SistemaTCC\Model\Semestre $semestre
     *
     * @return Tcc
     */
    public function setSemestre(\SistemaTCC\Model\Semestre $semestre = null)
    {
        $this->semestre = $semestre;

        return $this;
    }

    /**
     * Get semestre
     *
     * @return \SistemaTCC\Model\Semestre
     */
    public function getSemestre()
    {
        return $this->semestre;
    }
}

