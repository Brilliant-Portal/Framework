<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { onMounted } from 'vue';
import SwaggerUI from 'swagger-ui';
import 'swagger-ui/dist/swagger-ui.css';

const props = defineProps({
    spec: {
        type: Object,
        required: true,
    },
});

onMounted(() => {
    SwaggerUI({
        spec: props.spec,
        dom_id: '#api-explorer',
        presets: [
            SwaggerUI.presets.apis,
            SwaggerUI.SwaggerUIStandalonePreset,
        ],
    });
});
</script>

<template>
    <AppLayout title="API Documentation">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                API Documentation
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div id="api-explorer" class="font-sans"></div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
