<?php

namespace SistemaTCC\Model;

use Doctrine\Common\Collections\ArrayCollection;
use SistemaTCC\Model\AreaDeInteresse;

/**
 * Professor
 */
class Professor
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $assinatura;

    /**
     * @var \SistemaTCC\Model\Pessoa
     */
    private $pessoa;

	private $areasDeInteresse;

	public function __construct()
	{
		$this->areasDeInteresse = new ArrayCollection();
	}

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
     * Set assinatura
     *
     * @param string $assinatura
     *
     * @return Professor
     */
    public function setAssinatura($assinatura)
    {
        $this->assinatura = $assinatura;

        return $this;
    }

    /**
     * Get assinatura
     *
     * @return string
     */
    public function getAssinatura()
    {
        return $this->assinatura;
    }

    /**
     * Set pessoa
     *
     * @param \SistemaTCC\Model\Pessoa $pessoa
     *
     * @return Professor
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

	public function getAreasDeInteresse()
	{
		return $this->areasDeInteresse;
	}

	public function addAreaDeInteresse(AreaDeInteresse $areaDeInteresse)
	{
		$this->areasDeInteresse[] = $areaDeInteresse;
		$areaDeInteresse->addProfessor($this);
	}

    public function removeAreaDeInteresse(AreaDeInteresse $areaDeInteresse)
    {
        if ($this->areasDeInteresse->contains($areaDeInteresse)) {
            $this->areasDeInteresse->removeElement($areaDeInteresse);
            $areaDeInteresse->removeProfessor($this);
        }
    }
}
