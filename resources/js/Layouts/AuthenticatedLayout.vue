<script setup lang="ts">
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

interface User {
    name: string;
    username: string;
    email: string;
}

interface Auth {
    user: User;
}

const { props } = usePage<{ auth: Auth }>();

const showingNavigationDropdown = ref(false);
const showModal = ref(false); // Modal visibility state
const file = ref<File | null>(null);
const previewUrl = ref<string | null>(null); // Store image preview URL
const description = ref('');
const errorMessage = ref('');

function goToProfile() {
    const auth = props.auth;
    if (auth && auth.user) {
        const name = auth.user.name;
        if (name) {
            Inertia.visit(route('profile.show', { name }));
        }
    }
}

const goToDashboard = () => {
  Inertia.visit(route('dashboard'));
};

// Function to toggle the modal
function toggleModal() {
    showModal.value = !showModal.value;
}

function handleFileUpload(event: Event) {
  // Cast the event target to HTMLInputElement to access the files property
  const inputElement = event.target as HTMLInputElement;
  const selectedFile = inputElement.files ? inputElement.files[0] : null;

  if (!selectedFile) {
    errorMessage.value = 'No file selected.';
    return;
  }

  const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
  if (!validTypes.includes(selectedFile.type)) {
    errorMessage.value = 'Only JPEG, JPG, and PNG files are allowed.';
    file.value = null;
    previewUrl.value = null;
    return;
  }

  if (selectedFile.size > 10 * 2048 * 2048) {
    errorMessage.value = 'File size must be less than 20 MB.';
    file.value = null;
    previewUrl.value = null;
    return;
  }

  errorMessage.value = '';
  file.value = selectedFile;

  // Create a FileReader to display the image preview
  const reader = new FileReader();
  reader.onloadend = () => {
    previewUrl.value = reader.result as string;
  };
  reader.readAsDataURL(selectedFile);
}

function uploadPhoto() {
  if (!file.value) return;

  const formData = new FormData();
  formData.append('src', file.value);
  formData.append('description', description.value);

  Inertia.post(route('photos.store'), formData, {
    onSuccess: () => {
      description.value = '';
      file.value = null;
      previewUrl.value = null; // Clear preview after successful upload
      errorMessage.value = '';
      toggleModal();  // Close modal on success
    },
  });
}
</script>


<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="border-b border-gray-100 bg-white">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <div class="flex shrink-0 items-center">
                                <div class="flex shrink-0 items-center">
    <Link :href="route('dashboard')">
        <!-- Replace the Laravel icon with an image from the public folder -->
        <img src="/images/profiles/logo.jpg" alt="Profile Photo" class="block h-9 w-9 rounded-full object-cover">
    </Link>
</div>

</div>

                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                                                {{ props.auth.user.name }} 

                                                <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main>
                <slot />
            </main>

<!-- Add the conditional class to hide the navbar when modal is visible -->
<div :class="{'hidden': showModal}" class="fixed z-50 w-full h-16 max-w-lg -translate-x-1/2 bg-gray-100 border border-gray-300 rounded-full bottom-4 left-1/2">
                <div class="grid h-full max-w-lg grid-cols-3 mx-auto">
                    <button 
                        @click="goToDashboard" 
                        data-tooltip-target="tooltip-home" 
                        type="button" 
                        class="inline-flex flex-col items-center justify-center px-5 rounded-s-full hover:bg-gray-200 group"
                    >
                        <svg class="w-6 h-6 mb-1 text-gray-700 group-hover:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                        </svg>
                        <span class="sr-only">Home</span>
                    </button>

                    <div class="flex items-center justify-center">
                        <button
                            @click="toggleModal" 
                            type="button"
                            class="inline-flex items-center justify-center w-10 h-10 font-medium bg-blue-500 rounded-full hover:bg-blue-600 group focus:ring-4 focus:ring-blue-200 focus:outline-none"
                        >
                            <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                <path stroke="currentColor" stroke-width="2" d="M9 2v14M2 9h14" />
                            </svg>
                            <span class="sr-only">New</span>
                        </button>
                    </div>

                    <button data-tooltip-target="tooltip-profile" type="button" class="inline-flex flex-col items-center justify-center px-5 rounded-e-full hover:bg-gray-200 group" @click="goToProfile">
                        <svg class="w-6 h-6 mb-1 text-gray-700 group-hover:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a5 5 0 0 0-5 5c0 1.3.5 2.5 1.3 3.4a6.973 6.973 0 0 0-3.1 4.1A2.002 2.002 0 0 0 5 18h10a2.002 2.002 0 0 0 1.8-2.9 6.973 6.973 0 0 0-3.1-4.1A4.992 4.992 0 0 0 15 7a5 5 0 0 0-5-5Zm-3 5a3 3 0 1 1 6 0 3 3 0 0 1-6 0Z" clip-rule="evenodd"/>
                        </svg>
                        <span class="sr-only">Profile</span>
                    </button>
                </div>
            </div>



         <!-- Modal -->
         <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
                <div class="bg-white p-8 rounded-lg shadow-lg max-w-sm mx-auto">
                    <h2 class="text-xl font-semibold mb-4">Upload Photo</h2>
                    <form @submit.prevent="uploadPhoto">
                        <!-- Image Preview -->
                        <div v-if="previewUrl" class="mb-4">
                            <img :src="previewUrl" alt="Preview" class="max-w-full max-h-80 w-auto h-auto rounded-md" />                        </div>
                        <input type="file" @change="handleFileUpload" required class="mb-4" />
                        <textarea v-model="description" placeholder="Enter photo description" class="w-full mb-4 p-2 border border-gray-300 rounded"></textarea>
                        <div class="flex justify-between">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Upload</button>
                        </div>
                        <p v-if="errorMessage" class="text-red-500 mt-2">{{ errorMessage }}</p> <!-- Error message -->
                    </form>
                    <button @click="toggleModal" class="mt-4 text-gray-500">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

