<?php

namespace SistemaTCC\Model;

/**
 * Etapa
 */
class Etapa
{
    use Serializer\ObjectToJson;
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $nome;

    /**
     * @var \DateTime
     */
    private $dataInicio;

    /**
     * @var \DateTime
     */
    private $dataFim;

    /**
     * @var integer
     */
    private $peso;

    /**
     * @var integer
     */
    private $ordem;

    /**
     * @var boolean
     */
    private $enviarEmailAdministrador;

    /**
     * @var boolean
     */
    private $enviarEmailBanca;

    /**
     * @var boolean
     */
    private $enviarEmailOrientador;

    /**
     * @var integer
     */
    private $tcc;

    /**
     * @var \SistemaTCC\Model\EtapaTipo
     */
    private $etapaTipo;

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
     * Set nome
     *
     * @param string $nome
     *
     * @return Etapa
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

    /**
     * Set dataInicio
     *
     * @param \DateTime $dataInicio
     *
     * @return Etapa
     */
    public function setDataInicio($dataInicio)
    {
        $this->dataInicio = $dataInicio;

        return $this;
    }

    /**
     * Get dataInicio
     *
     * @return \DateTime
     */
    public function getDataInicio()
    {
        return $this->dataInicio;
    }

    /**
     * Set dataFim
     *
     * @param \DateTime $dataFim
     *
     * @return Etapa
     */
    public function setDataFim($dataFim)
    {
        $this->dataFim = $dataFim;

        return $this;
    }

    /**
     * Get dataFim
     *
     * @return \DateTime
     */
    public function getDataFim()
    {
        return $this->dataFim;
    }

    /**
     * Set peso
     *
     * @param integer $peso
     *
     * @return Etapa
     */
    public function setPeso($peso)
    {
        $this->peso = $peso;

        return $this;
    }

    /**
     * Get peso
     *
     * @return integer
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * Set ordem
     *
     * @param integer $ordem
     *
     * @return Etapa
     */
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;

        return $this;
    }

    /**
     * Get ordem
     *
     * @return integer
     */
    public function getOrdem()
    {
        return $this->ordem;
    }

    /**
     * Set enviarEmailAdministrador
     *
     * @param boolean $enviarEmailAdministrador
     *
     * @return Etapa
     */
    public function setEnviarEmailAdministrador($enviarEmailAdministrador)
    {
        $this->enviarEmailAdministrador = $enviarEmailAdministrador;

        return $this;
    }

    /**
     * Get enviarEmailAdministrador
     *
     * @return boolean
     */
    public function getEnviarEmailAdministrador()
    {
        return $this->enviarEmailAdministrador;
    }

    /**
     * Set enviarEmailBanca
     *
     * @param boolean $enviarEmailBanca
     *
     * @return Etapa
     */
    public function setEnviarEmailBanca($enviarEmailBanca)
    {
        $this->enviarEmailBanca = $enviarEmailBanca;

        return $this;
    }

    /**
     * Get enviarEmailBanca
     *
     * @return boolean
     */
    public function getEnviarEmailBanca()
    {
        return $this->enviarEmailBanca;
    }

    /**
     * Set enviarEmailOrientador
     *
     * @param boolean $enviarEmailOrientador
     *
     * @return Etapa
     */
    public function setEnviarEmailOrientador($enviarEmailOrientador)
    {
        $this->enviarEmailOrientador = $enviarEmailOrientador;

        return $this;
    }

    /**
     * Get enviarEmailOrientador
     *
     * @return boolean
     */
    public function getEnviarEmailOrientador()
    {
        return $this->enviarEmailOrientador;
    }

    /**
     * Set tcc
     *
     * @param integer $tcc
     *
     * @return Etapa
     */
    public function setTcc($tcc)
    {
        $this->tcc = $tcc;

        return $this;
    }

    /**
     * Get tcc
     *
     * @return integer
     */
    public function getTcc()
    {
        return $this->tcc;
    }

    /**
     * Set etapaTipo
     *
     * @param \SistemaTCC\Model\EtapaTipo $etapaTipo
     *
     * @return Etapa
     */
    public function setEtapaTipo(\SistemaTCC\Model\EtapaTipo $etapaTipo = null)
    {
        $this->etapaTipo = $etapaTipo;

        return $this;
    }

    /**
     * Get etapaTipo
     *
     * @return \SistemaTCC\Model\EtapaTipo
     */
    public function getEtapaTipo()
    {
        return $this->etapaTipo;
    }

    /**
     * Set semestre
     *
     * @param \SistemaTCC\Model\Semestre $semestre
     *
     * @return Etapa
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
