<?php

namespace SistemaTCC\Model;

class Atestado
{
	use Serializer\ObjectToJson;


    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $data;

    /**
     * @var string
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
     * Set data
     *
     * @param \DateTime $data
     *
     * @return Atestado
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
     * Set tcc
     *
     * @param \SistemaTCC\Model\Tcc $tcc
     *
     * @return Atestado
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
     * Set professor
     *
     * @param \SistemaTCC\Model\Professor $professor
     *
     * @return Atestado
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
