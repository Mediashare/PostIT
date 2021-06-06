# Bienvenue sur Mediashare

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
### <span class="text-success">Créer</span>
Pour la publication vous aurez besoin de renseignez [votre clef API](https://mediashare.fr/user/edit#inputApikey) dans le header de la requete.

```bash
echo "# LoremIpsum is beautiful" > LoremIpsum.md
curl \
	-H "ApiKey: {YOUR_APIKEY}" \
    -F "title=Lorem Ipsum" \
    -F "content=@./LoremIpsum.md" \
    -F "online=true" \
    https://mediashare.fr/upload
```

### <span class="text-primary">Liste</span>
Récupérer la liste des posts en ligne.

```bash
curl https://mediashare.fr/api/posts
```

### <span class="text-info">Lire</span>
Récupérer un post via son `ID` particulier en ligne.

```bash
curl https://mediashare.fr/api/post/{ID}
```