<?php

namespace SistemaTCC\Model;

/**
 * Usuario
 */
class Usuario
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $senha;

    /**
     * @var \SistemaTCC\Model\Pessoa
     */
    private $pessoa;

    /**
     * @var \SistemaTCC\Model\UsuarioAcesso
     */
    private $usuarioAcesso;

    /**
     * @var string
     */
    private $nivel;


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
     * Set senha
     *
     * @param string $senha
     *
     * @return Usuario
     */
    public function setSenha($senha)
    {
        $this->senha = $senha;

        return $this;
    }

    /**
     * Get senha
     *
     * @return string
     */
    public function getSenha()
    {
        return $this->senha;
    }

    /**
     * Set pessoa
     *
     * @param \SistemaTCC\Model\Pessoa $pessoa
     *
     * @return Usuario
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

    /**
     * Set usuarioAcesso
     *
     * @param \SistemaTCC\Model\UsuarioAcesso $usuarioAcesso
     *
     * @return Usuario
     */
    public function setUsuarioAcesso(\SistemaTCC\Model\UsuarioAcesso $usuarioAcesso = null)
    {
        $this->usuarioAcesso = $usuarioAcesso;

        return $this;
    }

    /**
     * Get usuarioAcesso
     *
     * @return \SistemaTCC\Model\UsuarioAcesso
     */
    public function getUsuarioAcesso()
    {
        return $this->usuarioAcesso;
    }

    /**
     * Set nivel
     *
     * @param string $nivel
     *
     * @return Usuario
     */
    public function setNivel($nivel)
    {
        $this->nivel = $nivel;

        return $this;
    }

    /**
     * Get nivel
     *
     * @return string
     */
    public function getNivel()
    {
        return $this->nivel;
    }
}
