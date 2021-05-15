<nav class="flex flex-wrap justify-center items-center bg-amber-900 py-4 px-24 mt-0 lg:justify-between">
    <ul class="flex items-center mb-2 mr-4 lg:mb-0">
        <li>
            <a href="{{ route('index') }}" class="block hover:bg-gray-800 hover:text-gray-100 px-3 py-3 flex items-center transition ease-in-out duration-150">
                Home
            </a>
        </li>
        <li>
            <a href="{{ route('recipes.index') }}" class="block hover:bg-gray-800 hover:text-gray-100 px-3 py-3 flex items-center transition ease-in-out duration-150">
                Recipes
            </a>
        </li>
        <li>
            <a href="{{ route('items.index') }}" class="block hover:bg-gray-800 hover:text-gray-100 px-3 py-3 flex items-center transition ease-in-out duration-150">
                Items
            </a>
        </li>
    </ul>
    <!-- search -->
    <livewire:search-dropdown>
</nav>
