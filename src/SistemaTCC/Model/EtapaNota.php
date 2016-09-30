<?php

namespace SistemaTCC\Model;

/**
 * EtapaNota
 */
class EtapaNota
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $nota;

    /**
     * @var \SistemaTCC\Model\EtapaEntrega
     */
    private $etapaEntrega;

    /**
     * @var \SistemaTCC\Model\Professor
     */
    private $professor;


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
     * Set nota
     *
     * @param string $nota
     *
     * @return EtapaNota
     */
    public function setNota($nota)
    {
        $this->nota = $nota;

        return $this;
    }

    /**
     * Get nota
     *
     * @return string
     */
    public function getNota()
    {
        return $this->nota;
    }

    /**
     * Set etapaEntrega
     *
     * @param \SistemaTCC\Model\EtapaEntrega $etapaEntrega
     *
     * @return EtapaNota
     */
    public function setEtapaEntrega(\SistemaTCC\Model\EtapaEntrega $etapaEntrega = null)
    {
        $this->etapaEntrega = $etapaEntrega;

        return $this;
    }

    /**
     * Get etapaEntrega
     *
     * @return \SistemaTCC\Model\EtapaEntrega
     */
    public function getEtapaEntrega()
    {
        return $this->etapaEntrega;
    }

    /**
     * Set professor
     *
     * @param \SistemaTCC\Model\Professor $professor
     *
     * @return EtapaNota
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
}

