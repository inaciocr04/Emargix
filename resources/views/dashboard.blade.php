<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
    @if(Auth::user()->isManager())
        <form action="{{Route('manager.import')}}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="flex flex-col space-y-2">
                <label for="file" class="text-lg font-medium text-gray-700">Sélectionner un fichier</label>
                <input type="file" name="file" id="file" required class="border border-gray-300 rounded-md p-2">
            </div>
            <button type="submit" class="bg-red-600">Importer</button>

        </form>
    @endif
</x-app-layout>
