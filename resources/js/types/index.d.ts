import { Inertia } from '@inertiajs/inertia';
// User interface as defined
// Define the structure of the page props
interface User {
    name: string;
    username: string;
    email: string;
}

interface Auth {
    user: User;
}

interface PageProps {
    auth: Auth;
}

// Define your props in the component
const props = defineProps<{
    $page: {
        props: PageProps;
    };
}>();

