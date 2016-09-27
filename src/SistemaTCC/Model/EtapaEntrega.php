<?php

namespace SistemaTCC\Model;

/**
 * EtapaEntrega
 */
class EtapaEntrega
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $data;

    /**
     * @var \SistemaTCC\Model\Aluno
     */
    private $aluno;

    /**
     * @var \SistemaTCC\Model\Etapa
     */
    private $etapa;

    /**
     * @var \SistemaTCC\Model\EtapaStatus
     */
    private $etapaStatus;


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
     * Set data
     *
     * @param \DateTime $data
     *
     * @return EtapaEntrega
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return \DateTime
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set aluno
     *
     * @param \SistemaTCC\Model\Aluno $aluno
     *
     * @return EtapaEntrega
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
     * Set etapa
     *
     * @param \SistemaTCC\Model\Etapa $etapa
     *
     * @return EtapaEntrega
     */
    public function setEtapa(\SistemaTCC\Model\Etapa $etapa = null)
    {
        $this->etapa = $etapa;

        return $this;
    }

    /**
     * Get etapa
     *
     * @return \SistemaTCC\Model\Etapa
     */
    public function getEtapa()
    {
        return $this->etapa;
    }

    /**
     * Set etapaStatus
     *
     * @param \SistemaTCC\Model\EtapaStatus $etapaStatus
     *
     * @return EtapaEntrega
     */
    public function setEtapaStatus(\SistemaTCC\Model\EtapaStatus $etapaStatus = null)
    {
        $this->etapaStatus = $etapaStatus;

        return $this;
    }

    /**
     * Get etapaStatus
     *
     * @return \SistemaTCC\Model\EtapaStatus
     */
    public function getEtapaStatus()
    {
        return $this->etapaStatus;
    }
}

