# threads-stalker

## how to run

1. Create a bot account on a `$MASTODON_SERVER` with `read:notifications`, `write:notifications`, `write:statuses` permissions.
2. Save the `$ACCESS_TOKEN`
3. Download the `compose.prod.yml` file (the entire repo is not necessary for production)

```shell
touch .env.prod.local
echo "MASTODON_SERVER=$MASTODON_SERVER" >> .env.prod.local
echo "MASTODON_TOKEN=$ACCESS_TOKEN" >> .env.prod.local
docker compose -f compose.prod.yml up -d
docker compose -f compose.prod.yml ps
```