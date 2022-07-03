<template>
    <div class="flex flex-row divide-x divide-gray-200 mt-5">
        <div class="px-6 w-1/2">
            <label for="name" class="block text-sm font-medium text-gray-700">Nom du nouveau message</label>
            <div class="mt-1">
                <input v-model="name" @change="generateContentDiff()" :disabled="readOnly" type="text" name="name" id="name" class="shadow-sm focus:ring-sky focus:border-sky block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            <p class="mt-2 text-sm text-gray-500" id="name-help">Ce nom sera affiché sur le panel et sur les suggestions de Swan</p>
            <div class="mt-5">
                <div class="block text-sm font-medium text-gray-700">Liste d'alias</div>
                <div class="grid grid-cols-3 gap-4 mt-1">
                    <div class="flex rounded-md shadow-sm" v-for="alias in aliases" :key="alias">
                        <input type="text" :value="alias" disabled class="focus:ring-sky focus:border-sky block rounded-none w-full rounded-l-md sm:text-sm border-gray-300 bg-gray-50">
                        <button :disabled="readOnly" type="button" @click="deleteAlias(alias)" class="-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-sky focus:border-sky">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                    <div class="flex rounded-md shadow-sm" v-if="!readOnly">
                        <input type="text" v-model="newAlias" class="focus:ring-sky focus:border-sky block rounded-none w-full rounded-l-md sm:text-sm border-gray-300">
                        <button type="button" @click="addAlias()" class="-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-sky focus:border-sky">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="mt-5">
                <label for="comment" class="block text-sm font-medium text-gray-700">Contenu du message</label>
                <div class="mt-1">
                    <textarea v-model="content" ref="editor" class="shadow-sm focus:ring-sky-500 focus:border-sky-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                </div>
            </div>
            <div class="flex w-full mt-5 justify-end" v-if="!readOnly">
                <button type="button" @click="submit()" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Envoyer
                    <svg class="ml-3 -mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="px-6 w-1/2">
            <div class="block" ref="aliasesDiff"></div>
            <div class="block" ref="contentDiff"></div>
        </div>
    </div>
</template>

<script>
import easyMDE from 'easymde';
import {html} from 'diff2html';
import {createTwoFilesPatch} from 'diff';
import 'easymde/dist/easymde.min.css';
import 'diff2html/bundles/css/diff2html.min.css';
import axios from "axios";

export default {
    data() {
        return {
            oldName: null,
            oldContent: null,
            oldAliases: [],
            name: null,
            content: null,
            aliases: [],
            newAlias: null,
            editor: null,
            postUrl: null,
            successUrl: null,
            readOnly: null,
        }
    },
    beforeMount() {
        const app = document.getElementById('app');

        ['oldName', 'name', 'oldContent', 'content', 'postUrl', 'successUrl'].forEach(key => {
            if (app.dataset[key])
                this[key] = app.dataset[key];
        });

        ['oldAliases', 'aliases'].forEach(key => {
            if (app.dataset[key])
                this[key] = JSON.parse(app.dataset[key]);
        });

        if (app.dataset['readOnly'])
            this.readOnly = true;

        if (app.dataset['edit']) {
            this.name = this.oldName;
            this.content = this.oldContent;
            this.aliases = this.oldAliases;
        }

        console.log(this);
    },
    methods: {
        generateContentDiff() {
            const contentDiff = createTwoFilesPatch((this.oldName ?? ""), (this.name ?? ""), (this.oldContent ?? ""), (this.content ?? ""), "", "", {});
            this.$refs.contentDiff.innerHTML = html(contentDiff, {
                drawFileList: false,
                matching: 'lines',
                outputFormat: 'line-by-line',
            });
        },
        generateAliasesDiff() {
            const aliasesDiff = createTwoFilesPatch("Liste d'alias", "Liste d'alias", (this.oldAliases ?? []).join('\n'), this.aliases.join('\n'), "", "");
            this.$refs.aliasesDiff.innerHTML = html(aliasesDiff, {
                drawFileList: false,
                matching: 'lines',
                outputFormat: 'line-by-line',
            });
        },
        deleteAlias(name) {
            this.aliases = this.aliases.filter(alias => alias !== name).sort((a, b) => a.localeCompare(b));
            this.generateAliasesDiff()
        },
        hasAlias(name) {
            return this.aliases.includes(name);
        },
        addAlias() {
            let newAlias = this.newAlias;
            if (!newAlias || newAlias === "")
                return;
            newAlias = newAlias.toLowerCase();

            if (this.hasAlias(newAlias)) {
                Swal.fire({
                    title: "Alias dupliqué",
                    text: "Cet alias est déjà présent dans la liste des alias.",
                    icon: "error",
                });
                return;
            }
            this.aliases.push(newAlias);
            this.aliases = this.aliases.sort((a, b) => a.localeCompare(b));
            this.newAlias = "";
            this.generateAliasesDiff()
        },
        submit() {
            const params = new URLSearchParams();
            params.append("name", this.name);
            params.append("content", this.content);
            params.append("aliases", JSON.stringify(this.aliases));
            axios.post(this.postUrl, params)
                .then(r => {
                    const id = r.data.messageEditId;
                    window.location.href = this.successUrl + (id ? `review/${id}` : '');
                })
                .catch(() => Swal.fire({
                    title: "Erreur",
                    text: "Une erreur est survenue lors de l'envoi du formulaire",
                    icon: "error",
                }));
        }
    },
    mounted() {
        let toolbar = [];
        if (!this.readOnly)
            toolbar = toolbar.concat(['bold', 'italic', 'quote', 'link', 'code', '|']);
        toolbar = toolbar.concat(['preview', 'side-by-side', 'fullscreen', '|', 'guide']);
        this.editor = new easyMDE({
            element: this.$refs.editor,
            spellChecker: false,
            autofocus: true,
            toolbar,
        });
        if (this.readOnly)
            this.editor.codemirror.setOption('readOnly', true);
        this.editor.codemirror.on('change', () => {
            this.content = this.editor.value();
            this.generateContentDiff();
        });
        this.generateContentDiff();
        this.generateAliasesDiff();
    }
}
</script>

