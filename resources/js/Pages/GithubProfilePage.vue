<template>
    <main>
        <!-- Failure Alert -->
        <div class="rounded-md bg-red-50 p-4 mb-5" v-if="errors.length > 0">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-circle-icon class="h-5 w-5 text-red-400" aria-hidden="true" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        There were {{ errors.length }} error{{ errors.length > 1 ? 's' : '' }} with your submission
                    </h3>

                    <div class="mt-2 text-sm text-red-700">
                        <ul role="list" class="list-disc pl-5 space-y-1">
                            <li v-for="error in errors" :key="error">{{ error }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Panel -->
        <div class="container flex justify-between">
            <div>
                <h3>Search GitHub profiles</h3>
                <p>Enter the GitHub username below and press search to load their profile.</p>
                <form class="mt-5 sm:flex sm:items-center" @submit.prevent="search">
                    <div class="w-full sm:max-w-xs">
                        <label for="username" class="sr-only">Username</label>
                        <input type="text" name="username" id="username" v-model="username" placeholder="kingga" />
                    </div>
                    <button class="button mt-3 sm:mt-0 sm:ml-3" type="submit">Search</button>
                </form>
            </div>

            <div class="flex justify-center">
                <strong class="text-sm text-gray-900 dark:text-gray-200">Dark Mode:</strong>
                <Switch v-model="darkModeEnabled" class="ml-2 toggle-switch">
                    <span class="sr-only">Use setting</span>
                    <span aria-hidden="true" class="pointer-events-none absolute bg-white dark:bg-gray-800 w-full h-full rounded-md" />
                    <span aria-hidden="true" :class="[darkModeEnabled ? 'bg-indigo-600' : 'bg-gray-200', 'toggle-switch-left']" />
                    <span aria-hidden="true" :class="[darkModeEnabled ? 'translate-x-5' : 'translate-x-0', 'toggle-switch-right']" />
                </Switch>
            </div>
        </div>

        <!-- User Details -->
        <div class="container my-5" v-if="user">
            <div class="flex">
                <img class="avatar" :src="user.avatar_url" alt="Avatar" />
                <div class="ml-5">
                    <h3>{{ user.name }}</h3>
                    <p class="mb-2"><em>{{ user.login }}</em></p>
                    <p class="my-0"><strong>Location:</strong> {{ user.location }}</p>
                    <p class="my-0"><strong>Profile Created:</strong> {{ user.created_at }}</p>

                    <div class="flex">
                        <p class="my-0"><strong>Followers:</strong> {{ user.followers }}</p>
                        <p class="my-0 ml-2"><strong>Following:</strong> {{ user.following }}</p>
                        <p class="my-0 ml-2"><strong>Repositories:</strong> {{ user.public_repos }}</p>
                    </div>

                    <!-- Repos -->
                    <div class="repo-grid mt-3">
                        <div
                            v-for="(repo, repoIdx) in user.repositories"
                            :key="repo.name"
                            class="repo-item"
                            :class="getRepoClassList(repo, repoIdx)"
                        >
                            <div class="mt-8">
                                <h3>
                                    <a :href="repo.url" target="_blank" class="focus:outline-none">
                                        <!-- Extend touch target to entire panel -->
                                        <span class="absolute inset-0" aria-hidden="true" />
                                        {{ repo.name }}
                                    </a>
                                </h3>

                                <p><strong>Language:</strong> {{ repo.language }}</p>

                                <div class="flex">
                                    <p class="flex" title="Stars">
                                        <star-icon class="h-5 w-5 text-yellow-500" />
                                        <span class="ml-1">{{ repo.stars }}</span>
                                    </p>

                                    <p class="flex ml-2" title="Forks">
                                        <receipt-refund-icon class="h-5 w-5 text-sky-500" />
                                        <span class="ml-1">{{ repo.forks }}</span>
                                    </p>

                                    <p class="flex ml-2" title="Watchers">
                                        <eye-icon class="h-5 w-5 text-indigo-600" />
                                        <span class="ml-1">{{ repo.watchers }}</span>
                                    </p>
                                </div>

                                <p>{{ repo.description }}</p>
                            </div>
                            <span class="pointer-events-none absolute top-6 right-6 text-gray-300 group-hover:text-gray-400" aria-hidden="true">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                  <path d="M20 4h1a1 1 0 00-1-1v1zm-1 12a1 1 0 102 0h-2zM8 3a1 1 0 000 2V3zM3.293 19.293a1 1 0 101.414 1.414l-1.414-1.414zM19 4v12h2V4h-2zm1-1H8v2h12V3zm-.707.293l-16 16 1.414 1.414 16-16-1.414-1.414z" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <a :href="user.html_url" target="_blank">
                        <button class="button mt-3">View Profile</button>
                    </a>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
import { StarIcon, ReceiptRefundIcon, EyeIcon, XCircleIcon } from '@heroicons/vue/solid';
import { Switch } from '@headlessui/vue';

export default {
    name: 'GithubProfilePage',

    components: { StarIcon, ReceiptRefundIcon, EyeIcon, XCircleIcon, Switch },

    data() {
        return {
            username: '',
            user: null,
            errors: [],
            darkModeEnabled: false,
        };
    },

    mounted() {
        this.darkModeEnabled = document.body.classList.contains('dark');
    },

    watch: {
        darkModeEnabled(enabled) {
            localStorage.setItem('darkMode', enabled);

            if (enabled) {
                document.body.classList.add('dark');
            } else {
                document.body.classList.remove('dark');
            }
        },
    },

    methods: {
        async search() {
            this.errors = [];

            if (this.username) {
                try {
                    const response = await axios.post('/github/search', { search: this.username });

                    if (response.status === 200) {
                        const { data } = response;
                        this.user = data;
                    }
                } catch (e) {
                    this.errors = ['The user cannot be found.'];
                }
            } else {
                this.errors = ['No user specified.'];
            }
        },

        getRepoClassList(repo, repoIdx) {
            const clsList = [];

            if (repoIdx === 0) {
                clsList.push('repo-border-tl');
            } else if (repoIdx === 1) {
                clsList.push('repo-border-tr');
            }

            if (repoIdx === this.user.repositories.length - 2) {
                clsList.push('repo-border-bl');
            } else if (repoIdx === this.user.repositories.length - 1) {
                clsList.push('repo-border-br');
            }

            return clsList;
        },
    },
};
</script>

<style>
.container {
    @apply bg-white dark:bg-gray-800 shadow sm:rounded-lg px-4 py-5 sm:p-6;
}

h3 {
    @apply text-lg leading-6 font-medium text-gray-900 dark:text-gray-200;
}

p {
    @apply mt-2 max-w-xl text-sm text-gray-500 dark:text-gray-300;
}

input {
    @apply py-2 px-2 shadow-sm block w-full sm:text-sm dark:bg-gray-800 dark:text-white;
    @apply border border-gray-300 dark:border-gray-500 rounded-md;
    @apply focus:ring-indigo-500 focus:border-indigo-500;
}

button.button {
    @apply w-full inline-flex items-center justify-center px-4 py-2 font-medium text-white bg-indigo-600 sm:w-auto sm:text-sm;
    @apply border border-transparent shadow-sm rounded-md;
    @apply hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500;
}

.avatar {
    @apply inline-block h-14 w-14 rounded-full;
}

.repo-grid {
    @apply rounded-lg bg-gray-200 dark:bg-gray-600 overflow-hidden shadow dark:shadow-gray-600;
    @apply divide-y divide-gray-200 dark:divide-gray-800;
    @apply sm:divide-y-0 sm:grid sm:grid-cols-2 sm:gap-px;
}

.repo-item {
    @apply relative bg-white dark:bg-gray-800 p-6;
    @apply focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500;
}

.repo-border-tl {
    @apply rounded-tl-lg rounded-tr-lg sm:rounded-tr-none;
}

.repo-border-tr {
    @apply sm:rounded-tr-lg;
}

.repo-border-bl {
    @apply sm:rounded-bl-lg;
}

.repo-border-br {
    @apply rounded-bl-lg rounded-br-lg sm:rounded-bl-none;
}

.toggle-switch {
    @apply flex-shrink-0 relative rounded-full inline-flex items-center justify-center h-5 w-10 cursor-pointer;
    @apply focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500;
}

.toggle-switch-left {
    @apply pointer-events-none absolute h-4 w-9 mx-auto rounded-full transition-colors ease-in-out duration-200;
}

.toggle-switch-right {
    @apply pointer-events-none absolute left-0 inline-block h-5 w-5 border border-gray-200 rounded-full bg-white shadow transform ring-0 transition-transform ease-in-out duration-200;
}
</style>
