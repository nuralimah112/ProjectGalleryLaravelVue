<script setup lang="ts">
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

interface Photo {
    id: number;
    src: string;
    description: string;
    profile: string;
    uploader: string;
    profileLink: string;
    created_at: string;
}

interface User {
    name: string;
    email: string;
    profile_image: string;
    id: number;
}

const props = defineProps<{
    user: User;
    photos: Photo[];
    authUser: User;
}>();

const photoToEdit = ref<Photo | null>(null);
const newDescription = ref('');
const showDeleteModal = ref(false);
const showEditModal = ref(false);
const selectedPhoto = ref<Photo | null>(null);

const goToSetting = () => {
    Inertia.visit(route('profile.edit'));
};

const openEditModal = (photo: Photo) => {
    photoToEdit.value = photo;
    newDescription.value = photo.description;
    showEditModal.value = true;
};

const openDeleteModal = (photo: Photo) => {
    photoToEdit.value = photo;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    photoToEdit.value = null;
    showDeleteModal.value = false;
};

const closeEditModal = () => {
    photoToEdit.value = null;
    newDescription.value = '';
    showEditModal.value = false;
};

const saveDescription = async () => {
    if (photoToEdit.value) {
        try {
            await Inertia.put(route('photos.update', photoToEdit.value.id), {
                description: newDescription.value,
            });
            closeEditModal();
        } catch (error) {
            console.error('Error updating description:', error);
        }
    }
};

const deletePhoto = async () => {
    if (photoToEdit.value !== null) {
        try {
            await Inertia.delete(route('photos.destroy', photoToEdit.value.id));
            closeDeleteModal();
        } catch (error) {
            console.error('Error deleting photo:', error);
        }
    }
};

const goToPhotoDetails = (photo: Photo) => {
    Inertia.visit(route('photos.show', photo.id)); // Navigate to the photo details page
};

const isOwnProfile = props.user.id === props.authUser.id;
</script>

<template>
    <Head :title="profileTitle" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-semibold text-gray-800">{{ profileTitle }}</h2>
        </template>

        <div class="py-12 bg-gray-100">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex justify-center items-center">
                    <div class="w-full sm:w-96 bg-white shadow-lg rounded-lg p-6">
                        <img :src="`/storage/${props.user.profile_image.replace('public/', '')}`" alt="User Profile Image" class="w-32 h-32 rounded-full object-cover border-4 border-blue-500 shadow-lg mx-auto"/>
                        <h1 class="text-3xl font-semibold text-gray-800 mt-4">{{ props.user.name }}</h1>
                        <p class="text-lg text-gray-600">{{ props.user.email }}</p>
                        <div class="mt-6 flex justify-center gap-4">
                            <button v-if="isOwnProfile" @click="goToSetting" class="px-6 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700">Edit Profile</button>
                        </div>
                    </div>
                </div>

                <div class="mt-12">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Uploaded Photos</h3>
                    <div class="columns-1 sm:columns-2 md:columns-3 lg:columns-4 gap-4 space-y-4">
                        <div v-for="photo in props.photos" :key="photo.id" @click="goToPhotoDetails(photo)" class="break-inside-avoid bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:scale-105 hover:shadow-lg cursor-pointer">
                            <img :src="`/storage/photos/${photo.src.split('/').pop()}`" class="w-full h-auto object-cover rounded-t-lg"/>
                            <div class="p-4">
                                <p class="text-gray-600 text-sm">Uploaded on {{ new Date(photo.created_at).toLocaleDateString() }}</p>
                                <p class="text-gray-600 text-sm">{{ photo.description }}</p>
                                <div class="flex justify-between items-center mt-2">
                                    <button v-if="isOwnProfile" @click.stop="openEditModal(photo)" class="text-sm text-blue-500 hover:text-blue-700">Edit</button>
                                    <button v-if="isOwnProfile" @click.stop="openDeleteModal(photo)" class="text-sm text-red-500 hover:text-red-700">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Description Modal -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 flex justify-center items-center bg-gray-800 bg-opacity-50">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm">
                <h3 class="text-xl font-semibold text-gray-800">Edit Description</h3>
                <textarea v-model="newDescription" class="w-full mt-4 p-2 border border-gray-300 rounded" rows="4" placeholder="Enter new description"></textarea>
                <div class="mt-4 flex justify-between">
                    <button @click="saveDescription" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Save</button>
                    <button @click="closeEditModal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Cancel</button>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex justify-center items-center bg-gray-800 bg-opacity-50">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm">
                <h3 class="text-xl font-semibold text-gray-800">Are you sure you want to delete this photo?</h3>
                <div class="mt-4 flex justify-between">
                    <button @click="deletePhoto" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Yes, Delete</button>
                    <button @click="closeDeleteModal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Cancel</button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script lang="ts">
import { defineComponent, computed } from 'vue';

export default defineComponent({
    props: {
        user: {
            type: Object,
            required: true
        },
        photos: {
            type: Array,
            required: true
        },
        authUser: {
            type: Object,
            required: true
        }
    },
    computed: {
        profileTitle(): string {
            return this.isOwnProfile ? 'My Profile' : this.user.name;
        },
        isOwnProfile(): boolean {
            return this.user.id === this.authUser.id;
        }
    },
    methods: {
        goToSetting() {
            // Logic to navigate to the settings page
        },
        goToPhotoDetails(photo: any) {
            // Logic to navigate to the photo details page
        },
        openEditModal(photo: any) {
            // Logic to open edit modal
        },
        closeEditModal() {
            this.showEditModal = false;
        },
        saveDescription() {
            // Logic to save the new description
        },
        openDeleteModal(photo: any) {
            // Logic to open delete modal
        },
        closeDeleteModal() {
            this.showDeleteModal = false;
        },
        deletePhoto() {
            // Logic to delete the photo
        },
    },
    data() {
        return {
            showEditModal: false,
            showDeleteModal: false,
            newDescription: '',
        };
    },
});
</script>
