<?php

namespace SistemaTCC\Model;

use Doctrine\Common\Collections\ArrayCollection;
use SistemaTCC\Model\Professor;

/**
 * AreaDeInteresse
 */
class AreaDeInteresse
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $titulo;

	private $professores;

	public function __construct()
	{
		$this->professores = new ArrayCollection();
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
     * Set titulo
     *
     * @param string $titulo
     *
     * @return AreaDeInteresse
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

	public function getProfessores()
	{
		return $this->professores;
	}

	public function addProfessor(Professor $professor)
	{
		$this->professores[] = $professor;
	}

    public function removeProfessor(Professor $professor)
    {
        if ($this->professores->contains($professor)) {
            $this->professores->removeElement($professor);
            $professor->removeAreaDeInteresse($this);
        }
    }
}
