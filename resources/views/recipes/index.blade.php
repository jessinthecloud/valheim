<x-listings.page
    :title="'Recipes'"
    :entities="$recipes"
    :entityName="'recipe'"
    :routeName="'recipes.show'"
    :paginator="$paginator"
></x-listings.page>