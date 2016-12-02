<?php

namespace SistemaTCC\Model;

/**
 * EtapaTipo
 */
class EtapaTipo
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
     * @return EtapaTipo
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
     * @var boolean
     */
    private $avaliado_banca;

    /**
     * @var boolean
     */
    private $avaliado_coordenador;

    /**
     * @var boolean
     */
    private $avaliado_orientador;

    /**
     * @var boolean
     */
    private $entrega_arquivo;


    /**
     * Set avaliadoBanca
     *
     * @param boolean $avaliadoBanca
     *
     * @return EtapaTipo
     */
    public function setAvaliadoBanca($avaliadoBanca)
    {
        $this->avaliado_banca = $avaliadoBanca;

        return $this;
    }

    /**
     * Get avaliadoBanca
     *
     * @return boolean
     */
    public function getAvaliadoBanca()
    {
        return $this->avaliado_banca;
    }

    /**
     * Set avaliadoCoordenador
     *
     * @param boolean $avaliadoCoordenador
     *
     * @return EtapaTipo
     */
    public function setAvaliadoCoordenador($avaliadoCoordenador)
    {
        $this->avaliado_coordenador = $avaliadoCoordenador;

        return $this;
    }

    /**
     * Get avaliadoCoordenador
     *
     * @return boolean
     */
    public function getAvaliadoCoordenador()
    {
        return $this->avaliado_coordenador;
    }

    /**
     * Set avaliadoOrientador
     *
     * @param boolean $avaliadoOrientador
     *
     * @return EtapaTipo
     */
    public function setAvaliadoOrientador($avaliadoOrientador)
    {
        $this->avaliado_orientador = $avaliadoOrientador;

        return $this;
    }

    /**
     * Get avaliadoOrientador
     *
     * @return boolean
     */
    public function getAvaliadoOrientador()
    {
        return $this->avaliado_orientador;
    }

    /**
     * Set entregaArquivo
     *
     * @param boolean $entregaArquivo
     *
     * @return EtapaTipo
     */
    public function setEntregaArquivo($entregaArquivo)
    {
        $this->entrega_arquivo = $entregaArquivo;

        return $this;
    }

    /**
     * Get entregaArquivo
     *
     * @return boolean
     */
    public function getEntregaArquivo()
    {
        return $this->entrega_arquivo;
    }
}
