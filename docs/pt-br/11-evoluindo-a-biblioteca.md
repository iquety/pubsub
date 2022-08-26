# Evoluindo a biblioteca

[◂ Publicando um sinal de parada](10-publicando-um-sinal-de-parada.md) | [Sumário da Documentação ▸](indice.md)
-- | --

## 1. Infraestrutura

Se o [Docker](https://www.docker.com/) estiver instalado no computador, não será necessário ter o Composer ou PHP instalados.

Para usar o Composer e as bibliotecas de qualidade de código, use o script `./composer`, localizado na raiz deste repositório. Este script é, na verdade, uma ponte para todos os comandos do Composer, executando-os através do Docker.

## 2. Controle de qualidade

### 2.1. Ferramentas

Para o desenvolvimento, foram utilizadas ferramentas para testes de unidade e análise estática. Todas configuradas no nível máximo de exigência.

São as seguintes ferramentas:

- [PHP Unit](https://phpunit.de)
- [PHP Stan](https://phpstan.org)
- [PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer)
- [PHP MD](https://phpmd.org)

### 2.2. Análise estática

Para fazer a análise do código implementado e colher feedback das ferramentas, use:

```bash
./composer analyse
```

### 2.3. Testes automatizados

Para executar os testes de unidade, use:

```bash
./composer test
```

## 3. Documentação

Uma boa navegação é indispensável para que a documentação seja fácil de usar. Pensando nisso, foi utilizada a ferramenta [Freep Docmap](https://github.com/ricardopedias/freep-docmap), para gerar um agradável menu de navegação em todas as páginas da documentação.

Os documentos editáveis se encontram no diretório `assets/docs`. Após acrescentar ou editar quaisquer documentos contidos ali, basta executar o comando abaixo para gerar a documentação navegável no diretório `docs`:

```bash
composer docmap
```

[◂ Publicando um sinal de parada](10-publicando-um-sinal-de-parada.md) | [Sumário da Documentação ▸](indice.md)
-- | --
