<?php

namespace SistemaTCC\Model;

/**
 * EtapaEntregaArquivo
 */
class EtapaEntregaArquivo
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
     * @var string
     */
    private $tipo;

    /**
     * @var string
     */
    private $caminho;

    /**
     * @var \SistemaTCC\Model\EtapaEntrega
     */
    private $etapaEntrega;


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
     * @return EtapaEntregaArquivo
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
     * Set tipo
     *
     * @param string $tipo
     *
     * @return EtapaEntregaArquivo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set caminho
     *
     * @param string $caminho
     *
     * @return EtapaEntregaArquivo
     */
    public function setCaminho($caminho)
    {
        $this->caminho = $caminho;

        return $this;
    }

    /**
     * Get caminho
     *
     * @return string
     */
    public function getCaminho()
    {
        return $this->caminho;
    }

    /**
     * Set etapaEntrega
     *
     * @param \SistemaTCC\Model\EtapaEntrega $etapaEntrega
     *
     * @return EtapaEntregaArquivo
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
}

