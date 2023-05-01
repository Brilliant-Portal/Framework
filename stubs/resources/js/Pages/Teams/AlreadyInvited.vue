<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
</script>

<template>
    <Head title="Terms of Service" />

    <div class="font-sans text-gray-900 antialiased">
        <div class="pt-4 bg-gray-100">
            <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
                <div>
                    <AuthenticationCardLogo />
                </div>

                <div class="w-full sm:max-w-2xl mt-6 p-6 bg-white shadow-md overflow-hidden sm:rounded-lg prose">
                    <p class="mt-4">
                        To join an existing team, click the “Accept Invitation” button in the team invitation email.
                    </p>
                    <p class="mt-4">
                        Didn’t get a team invitation? Ask a member of an existing team to invite you to their team.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
