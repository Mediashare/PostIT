# Welcome to Mediashare

>   Mediashare is an entity used to **share resources on the internet**.  
On this site you will find mainly **code and documentation** about the different libraries created. **Open-sourcing and knowledge** sharing is in Mediashare's DNA.  
It is obvious that knowledge is empirical, especially in the field of the web, that's why Mediashare exists and why we **promote code sharing** in order to **make more accessible all the information available** on the internet.  


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