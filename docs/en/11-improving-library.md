# Improving the library

[◂ Publishing a stop sign](10-publishing-a-stop-sign.md) | [Documentation Summary ▸](index.md)
-- | --

## 1. Infrastructure

If [Docker](https://www.docker.com/) is installed on your computer, it is not necessary to have Composer or PHP installed.

Para usar o Composer e as bibliotecas de qualidade de código, use o script `./composer`, localizado na raiz deste repositório. Este script é, na verdade, uma ponte para todos os comandos do Composer, executando-os através do Docker.

## 2. Quality control

### 2.1. Tools

For development, tools for unit testing and static analysis were used. All configured to the maximum level of demand.

These are the following tools:

- [PHP Unit](https://phpunit.de)
- [PHP Stan](https://phpstan.org)
- [PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer)
- [PHP MD](https://phpmd.org)

### 2.2. Static analysis

To analyze the implemented code and gather feedback from the tools, use:

```bash
./composer analyse
```

### 2.3. Automated tests

To run the unit tests, use:

```bash
./composer test
```

## 3. Documentation

Good navigation is essential for the documentation to be easy to use. With that in mind, the tool [Freep Docmap](https://github.com/ricardopedias/freep-docmap) was used to generate a pleasant navigation menu on all pages of the documentation.

Editable documents are located in the `assets/docs` directory. After adding or editing any documents contained there, simply run the command below to generate the navigable documentation in the `docs` directory:

```bash
composer docmap
```

[◂ Publishing a stop sign](10-publishing-a-stop-sign.md) | [Documentation Summary ▸](index.md)
-- | --
