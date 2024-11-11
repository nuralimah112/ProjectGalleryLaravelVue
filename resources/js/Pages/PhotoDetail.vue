<script setup lang="ts">
import { defineProps, ref, onMounted } from 'vue';
import axios from 'axios';

interface Photo {
    id: number;
    src: string;
    profile: string;
    uploader: string;
    description: string;
    profileLink: string;
    likes_count: number;
    liked_by_user: boolean;
}

interface Comment {
    id: number;
    user_id: number;
    content: string;
    created_at: string;
    user: {
        name: string;
        profile_image: string;

    };
}

const props = defineProps<{
    photo: Photo;
}>();

const comments = ref<Comment[]>([]);
const newComment = ref<string>('');
const commentsLimit = ref(5); // Limit the number of comments displayed initially

// Like functionality
const likePhoto = async (photoId: number) => {
    const photo = props.photo;
    photo.liked_by_user = !photo.liked_by_user;
    photo.likes_count += photo.liked_by_user ? 1 : -1;

    try {
        const response = await axios.post(`/photos/${photoId}/like`);
        const data = response.data;
        photo.likes_count = data.likes_count;
        photo.liked_by_user = data.liked_by_user;
    } catch (error) {
        console.error("Error liking the photo:", error);
        photo.liked_by_user = !photo.liked_by_user;
        photo.likes_count += photo.liked_by_user ? 1 : -1;
        alert("Failed to update the like status. Please try again.");
    }
};

// Fetch comments from the backend
const fetchComments = async () => {
    try {
        const response = await axios.get(`/photos/${props.photo.id}/comments`);
        comments.value = response.data;
    } catch (error) {
        console.error("Failed to fetch comments:", error);
    }
};

// Add a new comment
const addComment = async () => {
    if (!newComment.value) return;

    try {
        const response = await axios.post(`/photos/${props.photo.id}/comments`, {
            content: newComment.value,
        });

        const newCommentData = response.data;
        comments.value.unshift(newCommentData);
        newComment.value = '';
    } catch (error) {
        console.error("Failed to add comment:", error);
    }
};

// Load comments when the component is mounted
onMounted(() => {
    fetchComments();
});

const deleteComment = async (commentId: number) => {
    try {
        const response = await axios.delete(`/comments/${commentId}`);
        
        // Remove the comment from the list if it was successfully deleted
        comments.value = comments.value.filter(comment => comment.id !== commentId);
    } catch (error) {
        console.error("Failed to delete comment:", error);
        alert("Error deleting comment. Please try again.");
    }
};

// Function to go back to the previous page
const goBack = () => {
    window.history.back();
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Photo Detail</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Back Button -->
                <button @click="goBack" class="absolute top-4 left-4 p-2 rounded-full bg-gray-800 text-white hover:bg-gray-700 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>

                <!-- Single Card Section -->
                <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden flex">
                    <!-- Left Section - Photo -->
                    <div class="w-1/2">
                        <img :src="`/storage/photos/${props.photo.src.split('/').pop()}`"
                             class="w-full h-auto object-cover" />
                    </div>

                    <!-- Right Section - Details -->
                    <div class="w-1/2 p-6 flex flex-col justify-between">
                        <!-- Profile and Like Section -->
                        <div>
                            

                            <!-- Like Button and Like Count -->
                            <div class="flex items-center space-x-4 mt-auto">
                                <button @click="likePhoto(props.photo.id)" class="text-2xl text-gray-500 hover:text-red-600 transition-all">
                                    <span v-if="props.photo.liked_by_user">♥</span>
                                    <span v-else>♡</span>
                                </button>
                                <span class="text-lg text-gray-700">{{ props.photo.likes_count }}</span>
                            </div>
                        </div>

                        <!-- Description Section -->
                        <div class="text-gray-600 mt-4">
                        </div>

                        <!-- Comment Section -->
                        <div class="mt-2">
                            <div class="flex items-center space-x-4">
                                <img :src="`/storage/profile_images/${props.photo.profile.split('/').pop()}`"
                                     :alt="`Profile picture of ${props.photo.uploader}`"
                                     class="w-16 h-16 rounded-full object-cover" />
                                <div>
                                    <a :href="`/profile/${props.photo.uploader}`" class="text-xl font-semibold text-gray-800 hover:text-blue-600">
                                        {{ props.photo.uploader }}
                                    </a>
                                </div>
                            </div>
                            <p class="text-xl italic leading-relaxed border-b border-gray-300 pb-2">{{ props.photo.description }}</p>

                            <h3 class="text-lg font-semibold text-gray-800">Comments <span class="text-sm text-gray-500">({{ comments.length }})</span></h3>

                            <!-- List of Comments -->
                            <div class="max-h-64 overflow-y-auto mt-4">
                                <ul class="space-y-4">
                                    <li v-for="comment in comments" :key="comment.id" class="border-b border-gray-200 pb-4">
                                        <div class="flex items-center space-x-3">
                                            <!-- Commenter's Profile Picture with Fallback -->
                                            <img :src="`/storage/profile_images/${comment.user.profile_image ? comment.user.profile_image.split('/').pop() : 'default-avatar.png'}`"
                                                 :alt="`Profile picture of ${comment.user.name}`"
                                                 class="w-12 h-12 rounded-full object-cover" />
                                                 <a :href="`/profile/${comment.user.name}`" class="font-semibold text-gray-800 hover:text-blue-600">
                                                {{ comment.user?.name || 'Anonymous' }}
                                            </a>
                                            <span class="text-sm text-gray-500">{{ new Date(comment.created_at).toLocaleString() }}</span>
                                            
                                        </div>
                                        <p class="text-gray-600">{{ comment.content }}</p>
                                    </li>
                                </ul>
                            </div>

                            <!-- Add New Comment Form -->
                            <div class="mt-6">
                                <input type="text" v-model="newComment" placeholder="Write a comment..."
                                       class="w-full p-3 border rounded-md focus:ring focus:ring-blue-300">
                                <button @click="addComment" class="mt-3 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
