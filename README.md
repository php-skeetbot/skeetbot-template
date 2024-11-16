# php-skeetbot/skeetbot-template

## getting started

### first things first
- ~~clone this repository~~ [*use this template*](https://github.com/new?template_name=skeetbot-template&template_owner=php-skeetbot)
- create a Bluesky account for your bot, e.g under https://bsky.app/settings -> add account
- switch to the bot account and create a new app password https://bsky.app/settings/app-passwords
- copy [`/.config/.env_example`](./.config/.env_example) to `/.config/.env` (for local test, **do not upload the .env to GitHub!**)
	- copy the app password and save it in the `.env` as `BLUESKY_APP_PW`, go to the repository settings on GitHub under `{repo_url}/settings/secrets/actions` and save it there too (not necessary if you plan to run the bot on your own webserver)
    - copy the handle of your bot account (e.g. `mybot.bsky.social`) and save it as `BLUESKY_HANDLE`, save it as GitHub repository secret as well
- fetch a fresh `cacert.pem` from https://curl.se/ca/cacert.pem and save it under `/.config`

### next up: code
- update the [`LICENSE`](./LICENSE)
- change the example namespaces in [`composer.json`](./composer.json), add any libraries you need, add yourself as author
	- commit the `composer.lock` after updating
- change/replace the [`MySkeetBot`](./src/MySkeetBot.php) and [`MySkeetBotTest`](./tests/MySkeetBotTest.php) examples
	- `MySkeetBot` needs to extend the abstract `SkeetBot` class
- update the CLI runner [`run.php`](./cli/run.php) as necessary

### finally: run
- test locally: `php ./cli/run.php`
- create a `run.yml` in [`/.github/workflows`](./.github/workflows) which enables a scheduled GitHub action (see below)
- profit!

```yml
# https://docs.github.com/en/actions/using-workflows/workflow-syntax-for-github-actions

on:
  schedule:
    # POSIX cron syntax (every 12th hour), https://crontab.guru/#0_12_*_*_*
    - cron: "0 12 * * *"

name: "Run"

jobs:

  run-bot:
    name: "Run the bot and post to Bluesky"

    runs-on: ubuntu-latest

    # requiired for stefanzweifel/git-auto-commit-action
    permissions:
      contents: write

    env:
      BLUESKY_APP_PW: ${{ secrets.BLUESKY_APP_PW }}
      BLUESKY_HANDLE: ${{ secrets.BLUESKY_HANDLE }}

    steps:
      - name: "Checkout sources"
        uses: actions/checkout@v4

      - name: "Install PHP"
        uses: shivammathur/setup-php@v2
        with:
          php-version: "8.2"
          coverage: none
          extensions: curl, fileinfo, intl, json, openssl, mbstring, simplexml, sodium, zlib

      - name: "Install dependencies with composer"
        uses: ramsey/composer-install@v3

      # it's probably a good idea to just commit a cacert and update it every now and then
      - name: "Fetch cacert.pem from curl.haxx.se"
        run: wget -O ./.config/cacert.pem https://curl.se/ca/cacert.pem

      - name: "Run bot"
        run: php ./cli/run.php

      # please note that this requires read/write permissions for the actions runner!
      - name: "Commit log"
        uses: stefanzweifel/git-auto-commit-action@v5
        with:
          commit_message: ":octocat: posted skeet"
          file_pattern: "data/posted.json"
          commit_user_name: "github-actions[bot]"
          commit_user_email: "41898282+github-actions[bot]@users.noreply.github.com"
          commit_author: "github-actions[bot] <41898282+github-actions[bot]@users.noreply.github.com>"
```

## related projects
- [php-skeetbot/php-skeetbot](https://github.com/php-skeetbot/php-skeetbot)
	- [php-skeetbot/skeetbot-template](https://github.com/php-skeetbot/skeetbot-template)
- [chillerlan/php-httpinterface](https://github.com/chillerlan/php-httpinterface)
	- [chillerlan/php-http-message-utils](https://github.com/chillerlan/php-http-message-utils)
	- [chillerlan/php-oauth](https://github.com/chillerlan/php-oauth)
- [chillerlan/php-settings-container](https://github.com/chillerlan/php-settings-container)
- [chillerlan/php-dotenv](https://github.com/chillerlan/php-dotenv)

## disclaimer

WE'RE TOTALLY NOT RUNNING A PRODUCTION-LIKE ENVIRONMENT ON GITHUB.<br>
WE'RE RUNNING A TEST AND POST THE RESULT TO AN EXTERNAL WEBSITE.<br>
WE'RE JUST LOOKING IF THE SCRIPT STILL WORKS ON A SCHEDULE N TIMES A DAY.
