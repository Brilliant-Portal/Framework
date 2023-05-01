<script setup>
import { Head } from '@inertiajs/vue3';
import CreateTeamForm from '@/Pages/Teams/Partials/CreateTeamForm.vue';
</script>

<template>
    <Head title="Create Team" />

    <div class="font-sans text-gray-900 antialiased">
        <div class="pt-4 bg-gray-100">
            <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
                <div>
                    <AuthenticationCardLogo />
                </div>

                <div class="w-full sm:max-w-4xl mt-6 prose">
                    <CreateTeamForm />
                </div>
            </div>
        </div>
    </div>
</template>
