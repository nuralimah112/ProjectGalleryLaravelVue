<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { ref } from 'vue';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();

// State variables
const profileImage = ref<File | null>(null);
const profileImageUrl = ref<string | null>(null); // For image preview
const isProcessing = ref(false); // Disable the button while processing
const errorMessage = ref<string | null>(null); // Handle errors
const showSuccessPopup = ref(false); // Show success popup

// Handle file change
const onFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        profileImage.value = target.files[0];
        profileImageUrl.value = URL.createObjectURL(target.files[0]); // Show preview
    }
};

// Handle form submission
const uploadProfilePicture = async () => {
    if (!profileImage.value) {
        errorMessage.value = "Please select an image file";
        return;
    }

    const formData = new FormData();
    formData.append('profile_image', profileImage.value);

    isProcessing.value = true; // Start processing
    errorMessage.value = null; // Reset error message

    try {
        const response = await fetch('/profile/upload-photo', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: formData,
        });

        if (response.ok) {
            showSuccessPopup.value = true; // Show the success popup
            profileImageUrl.value = null; // Clear preview after upload
            setTimeout(() => (showSuccessPopup.value = false), 3000); // Hide popup after 3 seconds
        } else {
            const errorData = await response.json();
            errorMessage.value = errorData.message || 'Failed to upload profile picture';
        }
    } catch (error) {
        errorMessage.value = 'An error occurred while uploading';
    } finally {
        isProcessing.value = false; // Stop processing
    }
};
</script>

<template>
    <Head title="Profile" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Profile</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                
                <!-- Profile Photo Upload -->
                <div class="profile-upload">
                    <h3 class="text-lg font-medium text-gray-900">Upload Profile Picture</h3>
                    <form @submit.prevent="uploadProfilePicture" class="mt-4">
                        <!-- Image Preview -->
                        <div v-if="profileImageUrl" class="mb-4">
                            <img :src="profileImageUrl" alt="Profile Preview" class="w-24 h-24 rounded-full" />
                        </div>
                        
                        <!-- File Input -->
                        <div>
                            <input
                                type="file"
                                accept="image/*"
                                @change="onFileChange"
                                class="block w-full text-sm text-gray-500
                                       file:mr-4 file:py-2 file:px-4
                                       file:rounded-md file:border-0
                                       file:text-sm file:font-semibold
                                       file:bg-gray-50 file:text-gray-700
                                       hover:file:bg-gray-100"
                            />
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-4">
                            <button
                                type="submit"
                                :disabled="isProcessing"
                                class="inline-flex items-center px-4 py-2 border border-transparent
                                       rounded-md shadow-sm text-sm font-medium
                                       text-white bg-blue-600 hover:bg-blue-700
                                       focus:outline-none focus:ring-2 focus:ring-offset-2
                                       focus:ring-blue-500 disabled:opacity-25 transition">
                                {{ isProcessing ? 'Uploading...' : 'Save' }}
                            </button>
                        </div>


                        
                        <!-- Error Message -->
                        <div v-if="errorMessage" class="text-red-500 mt-2">
                            {{ errorMessage }}
                        </div>
                    </form>
                </div>

                <!-- Success Popup -->
                <transition name="fade">
                    <div v-if="showSuccessPopup" class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded-md shadow-md">
                        Profile picture updated successfully!
                    </div>
                </transition>

                <!-- Other forms -->
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.profile-upload img {
    border: 2px solid #ccc;
    padding: 4px;
    object-fit: cover;
}
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */ {
    opacity: 0;
}
</style>
