#!/bin/sh
# Normalisasi env var database untuk deployment Render.

fix_render_db_url() {
    url="$1"
    region="${RENDER_REGION:-singapore}"

    [ -z "$url" ] && return 0

    case "$url" in
        *postgres.render.com*)
            printf '%s' "$url"
            return 0
            ;;
    esac

    # Internal URL Render: dpg-xxx-a → dpg-xxx-a.region-postgres.render.com
    printf '%s' "$url" | sed -E "s/(dpg-[a-z0-9]+-a)([:/])/\1.${region}-postgres.render.com\2/"
}

if [ -n "$DATABASE_URL" ]; then
    DATABASE_URL="$(fix_render_db_url "$DATABASE_URL")"
    export DATABASE_URL
fi

if [ -n "$DB_URL" ]; then
    DB_URL="$(fix_render_db_url "$DB_URL")"
    export DB_URL
fi

# Laravel config POS membaca DB_URL; sinkronkan dari DATABASE_URL jika perlu.
if [ -z "$DB_URL" ] && [ -n "$DATABASE_URL" ]; then
    DB_URL="$DATABASE_URL"
    export DB_URL
fi

# Hindari konflik: Laravel parse dari URL, bukan hostname terpisah.
if [ -n "$DATABASE_URL" ] || [ -n "$DB_URL" ]; then
    unset DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD
fi

rm -f public/hot
