<x-listings.page
    :title="'Items'"
    :entities="$items"
    :entityName="'item'"
    :routeName="'items.show'"
    :paginator="$paginator"
></x-listings.page>