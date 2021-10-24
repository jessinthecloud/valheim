<x-listings.page
        :title="'Consumables'"
        :entities="$consumables"
        :entityName="'consumable'"
        :routeName="'items.consumables.show'"
        :paginator="$paginator"
></x-listings.page>