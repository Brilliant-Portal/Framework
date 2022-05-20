<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('API Documentation') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div id="api-explorer" class="font-sans"></div>

                <script src="https://unpkg.com/swagger-ui-dist@3/swagger-ui-bundle.js"></script>
                <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@3/swagger-ui.css">

                <script>
                    var ui = SwaggerUIBundle({
                        spec: {!! json_encode($spec) !!},
                        dom_id: "#api-explorer",
                        presets: [
                            SwaggerUIBundle.presets.apis,
                            SwaggerUIBundle.SwaggerUIStandalonePreset
                        ],
                    })
                </script>
            </div>
        </div>
    </div>
</x-app-layout>
