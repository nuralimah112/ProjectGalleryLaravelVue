<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import axios from 'axios'; // Make sure axios is available for fetching

interface Photo {
    id: number;
    src: string;
    profile: string;
    uploader: string;
    profileLink: string;
    likes_count: number;
    liked_by_user: boolean;
}

const props = defineProps<{
    photos: Photo[];
    isAdmin: boolean;
    users: Array<{ id: number; name: string; usertype: string }>;
}>();

// Modal state
const isModalOpen = ref(false);
const itemToDelete = ref<{ id: number; type: 'photo' | 'user' } | null>(null);
const isUserModalOpen = ref(false);

const openDeleteModal = (id: number, type: 'photo' | 'user') => {
    itemToDelete.value = { id, type };
    isModalOpen.value = true;
};

const confirmDelete = () => {
    if (itemToDelete.value) {
        const { id, type } = itemToDelete.value;
        Inertia.delete(type === 'photo' ? `/photos/${id}` : `/account/${id}`);
    }
    closeDeleteModal();
};

const closeDeleteModal = () => {
    isModalOpen.value = false;
    itemToDelete.value = null;
};

const toggleUserModal = () => {
    isUserModalOpen.value = !isUserModalOpen.value;
};

// `localUsers` will store the fetched users list
const localUsers = ref<Array<{ id: number; name: string; usertype: string }>>([]);

// Filtered users to include only those with `usertype` as 'user'
const filteredUsers = computed(() => {
    return localUsers.value.filter(user => user.usertype === 'user');
});

// Fetch users from API when opening the user list modal
const openUserListModal = async () => {
    try {
        const response = await axios.get('/users');  // Ensure the URL matches your backend route
        localUsers.value = response.data;  // Store the response data in localUsers
    } catch (error) {
        alert('Failed to fetch users. Please try again later.');
    }
    isUserModalOpen.value = true;
};

console.log(props.users); // Log initial props for debugging

</script>
<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Photo Gallery</h2>
            <!-- Admin: Button to open user management modal -->
            <button 
                v-if="props.isAdmin" 
                @click="openUserListModal" 
                class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-700"
            >
                Manage Users
            </button>
        </template>

        <!-- Photo Gallery and User Management Modal -->
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="columns-1 sm:columns-2 md:columns-3 lg:columns-4 gap-6 space-y-6">
                            <div 
                                v-for="photo in props.photos" 
                                :key="photo.id" 
                                class="break-inside-avoid bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden"
                            >
                                <a :href="`/photos/${photo.id}`">
                                    <img 
                                        :src="`/storage/photos/${photo.src.split('/').pop()}`"
                                        class="w-full h-auto object-cover rounded-lg"
                                    />
                                </a>
                                <div class="flex items-center p-4">
                                    <a :href="`/profile/${photo.uploader}`" class="flex items-center hover:text-blue-500 group">
                                        <img 
                                            :src="photo.profile" 
                                            :alt="`Profile picture of ${photo.uploader}`" 
                                            class="w-10 h-10 rounded-full object-cover border-2 border-gray-300 group-hover:border-blue-500 transition duration-300 ease-in-out transform group-hover:scale-110"
                                        />
                                        <p class="ml-4 text-sm text-gray-700 group-hover:text-blue-500 group-active:text-blue-700 transition duration-300 ease-in-out">
                                            {{ photo.uploader }}
                                        </p>
                                    </a>
                                    <button 
                                        v-if="props.isAdmin" 
                                        @click="openDeleteModal(photo.id, 'photo')" 
                                        class="ml-4 text-red-500 hover:text-red-700"
                                    >
                                        Delete Photo
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- User Management Modal -->
                        <div v-if="isUserModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                            <div class="bg-white rounded-lg p-6 shadow-lg max-w-3xl mx-auto w-full">
                                <h3 class="text-lg font-semibold mb-4">Manage Users</h3>
                                <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden">
                                    <thead class="bg-gray-100 border-b">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-gray-800 font-medium">ID</th>
                                            <th class="px-4 py-2 text-left text-gray-800 font-medium">Name</th>
                                            <th class="px-4 py-2 text-left text-gray-800 font-medium">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="user in filteredUsers" :key="user.id" class="border-b">
                                            <td class="px-4 py-2">{{ user.id }}</td>
                                            <td class="px-4 py-2">{{ user.name }}</td>
                                            <td class="px-4 py-2">
                                                <button 
                                                    @click="openDeleteModal(user.id, 'user')" 
                                                    class="text-red-500 hover:text-red-700"
                                                >
                                                    Delete User
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="flex justify-end mt-6 space-x-4">
                                    <button 
                                        @click="toggleUserModal" 
                                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                                    >
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg p-6 shadow-lg max-w-md mx-auto">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Confirm Deletion</h2>
                <p class="text-gray-600">Are you sure you want to delete this item? This action cannot be undone.</p>
                
                <div class="flex justify-end mt-6 space-x-4">
                    <button @click="closeDeleteModal" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                    <button @click="confirmDelete" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
