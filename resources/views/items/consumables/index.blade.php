<x-listings.page
        :title="'Consumables'"
        :entities="$consumables"
        :entityName="'consumable'"
        :routeName="'consumables.show'"
        :paginator="$paginator"
></x-listings.page>