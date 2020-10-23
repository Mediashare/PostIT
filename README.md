# Welcome to Mediashare

<blockquote>
    Mediashare can be used for everything. You can use it to create <strong>documents</strong>, <strong>notes</strong>, <strong>presentations</strong>, <strong>technical documentation</strong>... <br>
    Mediashare is a community sharing platform, but Mediashare does not sell any user data. This project is open-source (<a href="https://github.com/Mediashare/Mediashare">Github</a>). <br>
    Publish your article and share it with your friends and family.
    <em class="float-right">Capt41ne</em>
</blockquote>   

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