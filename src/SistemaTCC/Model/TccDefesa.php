<?php

namespace SistemaTCC\Model;

class TccDefesa
{
	use Serializer\ObjectToJson;

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
     * Set dataHora
     *
     * @param \DateTime $dataHora
     *
     * @return TccDefesa
     */
    public function setDataHora($dataHora)
    {
        $this->data_hora = $dataHora;

        return $this;
    }

    /**
     * Get dataHora
     *
     * @return \DateTime
     */
    public function getDataHora()
    {
        return $this->data_hora;
    }

    /**
     * Set local
     *
     * @param string $local
     *
     * @return TccDefesa
     */
    public function setLocal($local)
    {
        $this->local = $local;

        return $this;
    }

    /**
     * Get local
     *
     * @return string
     */
    public function getLocal()
    {
        return $this->local;
    }

    /**
     * Set tcc
     *
     * @param \SistemaTCC\Model\Tcc $tcc
     *
     * @return TccDefesa
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
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $data_hora;

    /**
     * @var string
     */
    private $local;

    /**
     * @var \SistemaTCC\Model\Tcc
     */
    private $tcc;


}
