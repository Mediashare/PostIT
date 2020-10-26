# Welcome to Mediashare

>   Mediashare est une entité promouvant le partage de ressource sur Internet. Sur ce site, vous trouverez principalement des astuces et de la documentation sur différentes outils permettant l'utilisation d'un numérique plus agréable.
L'open-sourcing et le partage des connaissances font partie de l'ADN de Mediashare.

## Installation

```bash
git clone https://github.com/Mediashare/PostIT && cd PostIT
composer install # Install dependencies
composer dump-env prod # Or dev
nano .env.local.php # Edit configuration file
bin/console doctrine:schema:update --force # Create database and tables
```
## Use API

### ``Curl``

```bash
echo "# LoremIpsum is beautiful" > LoremIpsum.md
curl -F "title=Lorem Ipsum" -F "content=@./LoremIpsum.md" {{ url('upload') }}
```
