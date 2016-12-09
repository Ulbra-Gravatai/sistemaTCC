<?php

namespace SistemaTCC\Model;

/**
 * TccProfessor
 */
class TccProfessor
{
	use Serializer\ObjectToJson;
	
	const BANCA = 0;
	const ORIENTADOR = 1;
    /**
     * @var integer
     */
    private $id;

    /**
     * @var boolean
     */
    private $tipo;

    /**
     * @var \SistemaTCC\Model\Professor
     */
    private $professor;

    /**
     * @var \SistemaTCC\Model\Tcc
     */
    private $tcc;


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
     * Set tipo
     *
     * @param boolean $tipo
     *
     * @return TccProfessor
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return boolean
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set professor
     *
     * @param \SistemaTCC\Model\Professor $professor
     *
     * @return TccProfessor
     */
    public function setProfessor(\SistemaTCC\Model\Professor $professor = null)
    {
        $this->professor = $professor;

        return $this;
    }

    /**
     * Get professor
     *
     * @return \SistemaTCC\Model\Professor
     */
    public function getProfessor()
    {
        return $this->professor;
    }

    /**
     * Set tcc
     *
     * @param \SistemaTCC\Model\Tcc $tcc
     *
     * @return TccProfessor
     */
    public function setTcc(\SistemaTCC\Model\Tcc $tcc = null)
    {
        $this->tcc = $tcc;

        return $this;
    }

    /**
     * Get tcc
     *
     * @return \SistemaTCC\Model\Tcc
     */
    public function getTcc()
    {
        return $this->tcc;
    }
}

