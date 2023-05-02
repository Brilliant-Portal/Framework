<x-app-layout>
    <x-slot name="header">
        <x-brilliant-portal-framework::h2>
            {{ __('API Documentation') }}
        </x-brilliant-portal-framework::h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div id="api-explorer" class="font-sans"></div>

                <script src="https://unpkg.com/swagger-ui-dist@4/swagger-ui-bundle.js"></script>
                <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@4/swagger-ui.css">

                <script>
                    var ui = SwaggerUIBundle({
                        spec: @js($spec),
                        dom_id: "#api-explorer",
                        deepLinking: true,
                        presets: [
                            SwaggerUIBundle.presets.apis,
                            SwaggerUIBundle.SwaggerUIStandalonePreset,
                        ],
                    })
                </script>
            </div>
        </div>
    </div>
</x-app-layout>
