<template>
    <div class="flex flex-col py-5 px-2">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nom du module
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <template v-for="store in Object.keys(moduleList)">
                            <tr class="bg-gray-100">
                                <td class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider inline-flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    Catégorie {{ store }}
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr v-for="module in moduleList[store]">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ module.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ module.description }}</td>
                                <td v-if="module.enabled" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600 inline-flex">
                                    Activé
                                    <svg class="ml-3 -mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </td>
                                <td v-if="module.enabled" @click="toggleModule(module, false)" class="px-3 py-2 whitespace-nowrap text-center text-sm font-medium text-red-600">
                                    <button type="button"
                                       class="inline-flex items-center p-1 border border-transparent rounded-full shadow-sm text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
                                        </svg>
                                    </button>
                                </td>
                                <td v-if="!module.enabled" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600 inline-flex">
                                    Désactivé
                                    <svg class="ml-3 -mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </td>
                                <td v-if="!module.enabled" @click="toggleModule(module, true)" class="px-3 py-2 whitespace-nowrap text-center text-sm font-medium text-red-600">
                                    <button type="button"
                                       class="inline-flex items-center p-1 border border-transparent rounded-full shadow-sm text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import axios from "axios";

export default {
    data() {
        return {
            moduleList: {},
            postUrl: null,
        }
    },
    beforeMount() {
        const app = document.getElementById('app');
        if (app.dataset['moduleList'])
            this.moduleList = JSON.parse(app.dataset['moduleList']);
        if (app.dataset['postUrl'])
            this.postUrl = app.dataset['postUrl'];
    },
    methods: {
        updateModuleStatus(module, status) {
            this.moduleList[module.store] = this.moduleList[module.store].map(m => {
                if (m.name === module.name)
                    m.enabled = status;
                return m;
            });
        },
        toggleModule(module, status) {
            const params = new URLSearchParams();
            params.append("moduleId", module.id);
            params.append("status", status);
            axios.post(this.postUrl, params)
                .then(r => this.updateModuleStatus(module, r.data.status))
                .catch(() => Swal.fire({
                    title: "Erreur",
                    text: "Une erreur est survenue lors de l'envoi du formulaire",
                    icon: "error",
                }));
        }
    }
}
</script>

