<x-layouts.app>
    <x-slot name="title">
        Home
    </x-slot>

    <h1 class="w-full text-4xl mb-4">Valheim Recipes</h1>
    
    <p class="my-2">
        We're under construction, but you can still use the search box or menu to find the Valheim recipe that you need.
    </p>
    <p class="my-2">
        If you find any undocumented problems or have a request, feel free to <x-general.link rel="noopener nofollow" href="https://github.com/jessinthecloud/valheim/issues/new/choose">open an issue</x-general.link> on github
    </p>
    
    <section class="w-full flex flex-wrap mt-8">
        {{--<x-general.button-link class="w-full mt-6 md:w-auto md:mr-6 md:mt-0 lg:max-w-sm" href="{{ route('recipes.index') }}">
            Recipes listing
        </x-general.button-link>--}}
        <x-general.button-link class="w-full flex justify-center items-center mt-6 md:w-auto md:mr-6 md:mt-0 lg:max-w-sm" href="{{ route('items.index') }}">
            All Items Listing
        </x-general.button-link>
        <x-general.button-link class="w-full flex justify-center items-center mt-6 md:w-auto md:mr-6 md:mt-0 lg:max-w-sm" href="{{ route('items.armor.index') }}">
            <span class="mr-2">{!! App\Models\Items\Craftables\Items\Armor::ICON !!}</span> Armor Listing
        </x-general.button-link>
        <x-general.button-link class="w-full flex justify-center items-center mt-6 md:w-auto md:mr-6 md:mt-0 lg:max-w-sm" href="{{ route('items.weapons.index') }}">
            <span class="mr-2">{!! App\Models\Items\Craftables\Items\Weapon::ICON !!}</span> Weapons Listing
        </x-general.button-link>
        <x-general.button-link class="w-full flex justify-center items-center mt-6 md:w-auto md:mt-0 lg:max-w-sm" href="{{ route('items.consumables.index') }}">
            <span class="mr-2">{!! App\Models\Items\Craftables\Items\Consumable::ICON !!}</span> Consumables Listing
        </x-general.button-link>
    </section>

</x-layouts.app>
