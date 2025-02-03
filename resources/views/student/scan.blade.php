<x-app-layout>
    <h1>Scanner le QR Code</h1>
    <div class="flex justify-center items-center">
    <div id="reader" class="w-[600px] h-[600px] md:w-[500px] md:h-[400px]"></div> <!-- Conteneur pour la caméra -->
    </div>
    <p>Rafraîchir la page pour pouvoir scanner</p>
    <button onclick="window.location.reload();" class="mt-4 p-2 bg-blue-500 text-white rounded">Rafraîchir la page</button>
</x-app-layout>
