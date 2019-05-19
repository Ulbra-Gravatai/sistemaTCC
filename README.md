# sistemaTCC
Sistema para Controle e Acompanhamento de Trabalhos de Conclusão de Curso

## Documentação adicional
* [Como utilizar o GitHub](https://docs.google.com/document/d/1tkLoMl36GVBOLx65DFY7RL-ss-EARA5kiWxK7eJA0Hs/)
* [Instalação das Dependências](doc/ambiente/DEPENDENCIES.md)
* [Criação de Ambiente com VirtualBox](doc/ambiente/AMBIENTE.md)
* [Vídeo da aula de 30/09](https://www.youtube.com/watch?v=HI7b83x1xB8)

## Pré-requisitos
* [Git](https://git-scm.com)
* [PHP](http://php.net) 5.5
* [Composer](https://getcomposer.org)
* [MySQL](https://www.mysql.com)
* [Bower](http://bower.io)
* [Flyway](https://flywaydb.org)

## Como iniciar (Utilizando linha de comando)
Para colaborar você deve primeiro clonar o projeto do [GitHub](https://github.com/Ulbra-Gravatai/sistemaTCC/):
```
git clone https://github.com/Ulbra-Gravatai/sistemaTCC.git
cd sistemaTCC
```
Entre na branch de desenvolvimento:
```
git checkout dev
```
Então, você deve utilizar o [Composer](https://getcomposer.org) para instalar as dependências:
```
composer install
```
Após os passos acima, atualmente você terá a seguinte estrutura de diretórios:

## Estrutura de diretórios

```
sistemaTCC
|
|-- /config
|   |-- /orm
|   |-- banco-de-dados.sql
|   |-- bootstrap.php
|   |-- cli-config.php
|
|-- /doc
|   |-- /analysis
|
|-- /public
|   |-- /app
|   |-- /lib
|   |-- .htaccess
|   |-- index.php
|
|-- /src
|   |-- /SistemaTCC
|       |-- /Application
|       |   |-- SistemaTCC.php
|       |
|       |-- /Controller
|       |   |-- AlunoController.php
|       |   |-- IndexController.php
|       |   |-- ProfessorController.php
|       |   |-- SemestreController.php
|       |
|       |-- /Model
|       |   |-- Aluno.php
|       |   |-- Pessoa.php
|       |   |-- Professor.php
|       |   |-- Semestre.php
|       |
|       |-- /View
|           |-- /aluno
|           |   |-- formulario.twig
|           |   |-- listar.twig
|           |
|           |-- /index
|           |   |-- creditos.twig
|           |   |-- index.twig
|           |
|           |-- /professor
|           |   |-- formulario.twig
|           |   |-- listar.twig
|           |
|           |-- /semestre
|               |-- formulario.twig
|               |-- listar.twig
|
|-- /vendor
|    |-- /composer
|    |-- /pimple
|    |-- /psr
|    |-- /silex
|    |-- /symfony
|    |-- /twig
|    |-- autoload.php
|
|-- .editorconfig
|-- .bowerrc
|-- .gitignore
|-- bower.json
|-- composer.json
|-- LICENCE
|-- README.md
```

## .editorconfig para não zoar o código
Padrão de código é uma maneira eficiente de organizar as coisas. E isso é o que vamos fazer com o ``.editorconfig``. Ele serve para manter consistente o estilo do código em diferentes editores e IDEs.
Dúvidas dê uma olhada no site [http://editorconfig.org/](http://editorconfig.org/).

## Criando sua branch
Para começar a brincar, crie sua própria branch para não haver problemas na hora de fazer um git push, lembre-se de substituir <seu_nome> por você sabe o que...
```
git checkout -b <seu_nome>
```
## Banco de dados
Para a base de dados está sendo utilizado o flyway.
Você deve: 

  1. Baixar o flyway correspondente a sua plataforma do site
  2. Descompactar o flyway em uma pasta no seu computador
  3. Colocar tudo o que estiver dentro da pasta flyway-4.X.X para dentro da pasta flyway do projeto
  4. Executar flyway migrate 
  5. Sempre que alguém criar uma nova migration na pasta sql, basta executar flyway migrate para o seu banco ficar atualizado.

## Brincadeira!
Agora sim, a brincadeira pode começar!

## Logo mais...
Em seguida colocaremos como utilizar
* [PHP](http://php.net)
* [Composer](https://getcomposer.org)
* [Silex](http://silex.sensiolabs.org)
* [Twig](http://twig.sensiolabs.org)
* [Doctrine ORM](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/)
* [Git](https://github.com)
* [GitHub](https://github.com)
* [MySQL](https://www.mysql.com)
* [Bower](http://bower.io)
