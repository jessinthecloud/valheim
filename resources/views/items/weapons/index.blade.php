<x-listings.page
        :title="'Weapons'"
        :entities="$weapons"
        :entityName="'weapon'"
        :routeName="'weapons.show'"
        :paginator="$paginator"
></x-listings.page>