<x-layouts.app>
    <x-items.page
        :title="$item->name"
        :item="$item"
        :partial="'partials._item'"
        :index="'item'"
        :item_class_type="'item'"
    ></x-items.page>
</x-layouts.app>
